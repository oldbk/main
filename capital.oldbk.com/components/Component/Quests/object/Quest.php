<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests\object;


class Quest extends Base
{
    public $id;
    public $quest_type;
    public $name;

    public $user_quest_id;
    public $is_canceled;

    public $cancel = false;

    /** @var Part[] */
    public $part = array();

    public function addPart(Part $Part)
    {
        $this->part[$Part->id] = $Part;
        return $this;
    }

    public function isFinished()
    {
        foreach ($this->part as $Part) {
            if($Part->is_finished == false) {
                return false;
            }
        }

        return true;
    }

	/**
	 * @param $number
	 * @return Part|null
	 */
    public function getPartByNumber($number)
	{
		if(count($this->part) < $number) {
			return null;
		}

		foreach ($this->part as $Part) {
			if($Part->part_number != $number) {
				continue;
			}

			return $Part;
		}

		return null;
	}

    /**
     * @return mixed
     */
    public function isCanceled()
    {
        return $this->is_canceled;
    }
}