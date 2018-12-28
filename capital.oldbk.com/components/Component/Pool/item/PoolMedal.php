<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\models\User;
use components\models\UserBadge;

class PoolMedal extends BaseItem
{
    public $medal_stage_1;
    public $medal_stage_1_title;
    public $medal_stage_2;
    public $medal_stage_2_title;
    public $medal_stage_3;
    public $medal_stage_3_title;
    public $medal_stage_4;
    public $medal_stage_4_title;
    public $medal_stage_5;
    public $medal_stage_5_title;
    public $medal_key;
    public $day = 0;

    public function getItemType()
    {
        return self::ITEM_TYPE_MEDAL;
    }

	/**
	 * @param User $owner
	 * @param \Closure $CallbackDelo
	 * @param null $CallbackItem
	 * @return bool
	 */
	public function give(User $owner, \Closure $CallbackDelo, $CallbackItem = null) : bool
    {
        $info = $this->getInfo();

        $stage = 1;
        $types = [];
        for($i = 1; $i <=5; $i++) {
            if($info[$i]['img']) {
                $types[$i] = $this->medal_key.'_'.$i;
            }
        }

        $t = array_values($types);
        //fix for old functionality (before stage added)
        $t[] = $this->medal_key;

        /** @var UserBadge $Badge */
        $Badge = UserBadge::whereIn('rate_unique', $t)
			->where('user_id', '=', $owner->id)
			->whereRaw('(show_time = 0 or (show_time = 1 and show_ended_at >= ?))', [time()])
			->first();
        if($Badge) {
            //fix for old functionality (before stage added)
            $stage = $Badge['rate_unique'] == $this->medal_key ? $Badge['stage'] : (int)array_search($Badge['rate_unique'], $types);
            if($stage >= count($types)) {
                $stage = count($types) - 1;
            }
            $stage++;

            return $this->update($Badge->id, $stage);
        }

        return $this->add($owner, $stage);
    }

	/**
	 * @param $owner
	 * @param $stage
	 * @return bool
	 */
    protected function add($owner, $stage)
    {
        $info = $this->getInfo();

        $_data = [
			'user_id'           => $owner->id,
			'img'               => $info[$stage]['img'],
			'description'       => null,
			'alt'               => $info[$stage]['title'],
			'created_at'        => time(),
			'is_enabled'        => 1,
			'show_time'         => 0,
			'show_started_at'   => 0,
			'show_ended_at'     => 0,
			'rate_unique'       => $this->medal_key,
			'stage'             => $stage,
		];

        if($this->day > 0) {
            $_data = array_merge($_data, [
				'show_time'         => 1,
				'show_started_at'   => time(),
				'show_ended_at'     => (new \DateTime())->modify('+'.$this->day.' day')->getTimestamp(),
				'rate_unique'       => $this->medal_key,
			]);
        }

        return UserBadge::insert($_data) ? true : false;
    }

    /**
     * @param $id
     * @param $stage
     * @return boolean
     */
    protected function update($id, $stage)
    {
        $info = $this->getInfo();

        $_data = array(
            'img'               => $info[$stage]['img'],
            'alt'               => $info[$stage]['title'],
            'rate_unique'       => $this->medal_key,
            'stage'             => $stage,
        );
        if($this->day > 0) {

            $_data = array_merge($_data, array(
                'show_time'         => 1,
                'show_ended_at'     => (new \DateTime())->modify('+'.$this->day.' day')->getTimestamp(),
            ));
        }

        UserBadge::where('id', '=', $id)->update($_data);
        return true;
    }

    /**
     * @return array
     */
    private function getInfo()
    {
        return [
			1 => [
				'img' => $this->medal_stage_1,
				'title' => $this->medal_stage_1_title,
			],
			2 => [
				'img' => $this->medal_stage_2,
				'title' => $this->medal_stage_2_title,
			],
			3 => [
				'img' => $this->medal_stage_3,
				'title' => $this->medal_stage_3_title,
			],
			4 => [
				'img' => $this->medal_stage_4,
				'title' => $this->medal_stage_4_title,
			],
			5 => [
				'img' => $this->medal_stage_5,
				'title' => $this->medal_stage_5_title,
			],
		];
    }

	public function getChatString()
	{
		return null;
	}

	public function getViewArray()
	{
		return [];
	}
}