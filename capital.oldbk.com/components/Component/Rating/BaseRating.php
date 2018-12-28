<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.06.2016
 */

namespace components\Component\Rating;


use components\Component\AbstractComponent;
use components\Component\Config;
use components\Helper\FileHelper;
use components\models\EventRating;
use components\models\EventRatingCondition;
use components\models\User;
use components\models\UserEventRating;
use Monolog\Logger;

abstract class BaseRating extends AbstractComponent
{
    abstract protected function init();

    /** @var User */
    protected $user;

    public $available_ratings = [];

    public function run()
    {

    }

	/**
	 * BaseRating constructor.
	 * @param User $User
	 * @param null $app
	 */
    public function __construct($User, $app = null)
	{
		parent::__construct($app);
		$this->setUser($User);
	}

	public function getAvailableRatingByKey($key)
	{
		$isAdmin = (int)Config::admins($this->user->id);

		$RatingData = EventRating::where('key', '=', $key)
			->when(!$isAdmin, function($query) {
				/** @var mixed $query */
				$query->where('is_enabled', '=', 1);
			})->get()->toArray();

		$ratings = [];
		foreach ($RatingData as $rating) {
			if($this->checkAvailableCondition($rating) === false && !$isAdmin) {
				continue;
			}

			$ratings[$rating['id']] = $rating;
		}
		$RatingConditionList = $this->getConditions(array_keys($ratings));
		foreach ($ratings as $ratingId => $rating) {
			if(isset($RatingConditionList[$ratingId]) && $this->checkCondition($RatingConditionList[$ratingId]) === false && !$isAdmin) {
				continue;
			}

			$this->available_ratings[$ratingId] = $rating;
		}

		return $this->available_ratings;
	}

    /**
     * @param $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    protected function checkAvailableCondition($rating)
	{
		switch (true) {
			case ($rating['enable_type'] == EventRating::ITERATION_WEEK):
				/*$_day = date('w');
				if($_day == 0 || $_day == 6) {
					return false;
				}*/
				return true;
			case ($rating['enable_type'] == EventRating::ITERATION_CONDITION):
				return true;
		}

		return true;
	}

	/**
	 * @param $item_ids
	 * @param $item_type
	 * @return array
	 */
    protected function getConditions($item_ids)
    {
		$condition_list = array();
		$QuestConditionList = EventRatingCondition::whereIn('rate_id', $item_ids)
			->get()->toArray();
		foreach ($QuestConditionList as $Condition) {
			$rate_id = $Condition['rate_id'];

			$condition_list[$rate_id][$Condition['condition_type']][$Condition['group']][$Condition['field']] = $Condition['value'];
		}
		unset($QuestConditionList);

		return $condition_list;
    }

    /**
     * @param $condition_list
     * @return bool
     */
    protected function checkCondition($condition_list)
    {
		foreach ($condition_list as $condition_type => $array) {
			foreach ($array as $group => $data) {
				switch ($condition_type) {
					case EventRatingCondition::CONDITION_DATE:
						$current = time();
						$datestart = new \DateTime($data['date']);
						$datestart->setTime(0,0);

						$dateend = new \DateTime($data['date']);
						$dateend->setTime(23,59,59);

						if($datestart->getTimestamp() < $current && $dateend->getTimestamp() > $current) {
							return true;
						}
						break;
					case EventRatingCondition::CONDITION_RANGE:
						$current = time();
						$datestart = new \DateTime($data['date_start']);
						$datestart->setTime(0,0);

						$dateend = new \DateTime($data['date_end']);
						$dateend->setTime(23,59,59);

						if($datestart->getTimestamp() < $current && $dateend->getTimestamp() > $current) {
							return true;
						}
						break;
					case EventRatingCondition::CONDITION_WEEK:
						return false;
						break;
				}
			}
		}

		return false;
    }

    public function check($ratings, $value)
	{
		try {
			foreach ($ratings as $rating) {
				$Rating = UserEventRating::where('is_end', '=', 0)
					->where('rating_id', '=', $rating['id'])
					->where('is_reward', '=', 0)
					->where('user_id', '=', $this->user->id)
					->where('iteration_num', '=', $rating['iteration_num'])
					->first();
				if(!$Rating) {
					$Rating 				= new UserEventRating();
					$Rating->user_id 		= $this->user->id;
					$Rating->rating_id 		= $rating['id'];
					$Rating->iteration_num 	= $rating['iteration_num'];
				}

				$Rating->value += $value;
				if(!$Rating->save()) {
					$data = [
						'message' 	=> "Can't save rating",
						'userId' 	=> $this->user->id,
						'ratingId' 	=> $rating['id'],
						'value'		=> $Rating->value,
						'add_value' => $value,
					];
					$this->app()->logger->crit("Can't save rating", $data);
				}
			}
		} catch (\Exception $ex) {
			$this->app()->logger->crit($ex);

			return false;
		}

		return true;
	}
}