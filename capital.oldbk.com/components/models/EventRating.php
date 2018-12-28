<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.09.2018
 * Time: 14:13
 */

namespace components\models;

use components\models\_base\BaseModal;

/**
 * Class UserEventRating
 * @package components\models\_base
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property string $link
 * @property string $link_encicl
 * @property integer $is_enabled
 * @property string $enable_type
 * @property integer $iteration_num
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $reward_till_days
 *
 * @property EventRatingCondition[] $condition
 */
class EventRating extends BaseModal
{
	const ITERATION_WEEK 		= 'weekly';
	const ITERATION_CONDITION 	= 'condition';

	protected $connection = 'capital';
	protected $table = 'event_rating';
	protected $primaryKey = 'id';

	/** @var bool */
	public $isActive = false;
	/** @var \DateTime */
	public $datestart;
	/** @var \DateTime */
	public $dateend;

	public function condition()
	{
		return $this->hasMany(EventRatingCondition::class, 'rate_id', 'id');
	}

	/**
	 * @return array
	 */
	protected function getConditions()
	{
		$condition = [];
		foreach ($this->condition as $Condition) {
			$condition[$Condition->condition_type][$Condition->group][$Condition->field] = $Condition->value;
		}

		return $condition;
	}

	/**
	 * @return bool
	 */
	public function isActive()
	{
		switch (true) {
			case ($this->enable_type == self::ITERATION_WEEK):
				return true;

				break;
			case ($this->enable_type == self::ITERATION_CONDITION):
				$condition = $this->getConditions();

				foreach ($condition as $condition_type => $conditions) {
					foreach ($conditions as $group => $data) {
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
								break;
							default:
								break;
						}
					}
				}
				break;
		}

		return false;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getStartDatetime()
	{
		switch (true) {
			case ($this->enable_type == self::ITERATION_WEEK):
				$datenumber =  date('N') - 1;
				return (new \DateTime())->modify('-'.$datenumber.' days')->setTime(0,0);
			case ($this->enable_type == self::ITERATION_CONDITION):
				$condition = $this->getConditions();

				foreach ($condition as $condition_type => $conditions) {
					foreach ($conditions as $group => $data) {
						switch ($condition_type) {
							case EventRatingCondition::CONDITION_DATE:
								$datestart = new \DateTime($data['date']);
								$datestart->setTime(0,0);

								$dateend = new \DateTime($data['date']);
								$dateend->setTime(23,59, 59);
								if($dateend->getTimestamp() < time()) {
									continue;
								}

								return $datestart;
								break;
							case EventRatingCondition::CONDITION_RANGE:
								$dateend = new \DateTime($data['date_end']);
								$dateend->setTime(0,0);
								if($dateend->getTimestamp() < time()) {
									continue;
								}

								$datestart = new \DateTime($data['date_start']);
								$datestart->setTime(0,0);

								return $datestart;
								break;
							case EventRatingCondition::CONDITION_WEEK:
								break;
							default:
								break;
						}
					}
				}
				break;
		}

		return null;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getEndDatetime()
	{
		switch (true) {
			case ($this->enable_type == self::ITERATION_WEEK):
				$datenumber =  7 - date('N');
				return (new \DateTime())->modify('+'.$datenumber.' days')->setTime(23,59, 59);
			case ($this->enable_type == self::ITERATION_CONDITION):
				$condition = $this->getConditions();

				foreach ($condition as $condition_type => $conditions) {
					foreach ($conditions as $group => $data) {
						switch ($condition_type) {
							case EventRatingCondition::CONDITION_DATE:
								$dateend = new \DateTime($data['date']);
								$dateend->setTime(23,59, 59);
								if($dateend->getTimestamp() < time()) {
									continue;
								}

								return $dateend;
								break;
							case EventRatingCondition::CONDITION_RANGE:
								$dateend = new \DateTime($data['date_end']);
								$dateend->setTime(23,59, 59);
								if($dateend->getTimestamp() < time()) {
									continue;
								}

								return $dateend;
								break;
							case EventRatingCondition::CONDITION_WEEK:
								break;
							default:
								break;
						}
					}
				}
				break;
		}

		return null;
	}
}