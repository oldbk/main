<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 09.06.2016
 */

namespace components\Component\Quests\object;


use components\Component\Quests\check\iChecker;
use components\Component\Quests\pocket\iPocketItemInfo;
use components\Component\Quests\pocket\questTask\iQuestTask;
use components\Component\Quests\validator\iValidator;
use components\Component\VarDumper;
use components\Helper\FileHelper;

class Task extends Base implements iQuestTask
{
    public $pocket_item_id;
    public $pocket_id;
    public $quest_id;
    public $item_type;

    /** @var int part_id */
    public $item_id;
    public $user_quest_id;
    public $user_part_id;
    public $user_task_id;

    public $count = 0;
    public $start_count = 0;
    public $count_done = 0;

    public $is_finished = 0;

	public $can_be_multiple = 0;

    public $process = array();

    /** @var iQuestTask */
    public $info;

    /**
     * @var iValidator[]
     */
    public $validators = array();

	/**
	 * @return bool
	 */
	public function canBeMultiple()
	{
		return $this->can_be_multiple;
	}

    /**
     * @return mixed
     */
    public function getPocketItemId()
    {
        return $this->pocket_item_id;
    }
    
    /**
     * @return mixed
     */
    public function getQuestId()
    {
        return $this->quest_id;
    }

    /**
     * @return mixed
     */
    public function getItemType()
    {
        return $this->item_type;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * @return mixed
     */
    public function getUserQuestId()
    {
        return $this->user_quest_id;
    }

    /**
     * @return mixed
     */
    public function getUserPartId()
    {
        return $this->user_part_id;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param iChecker $Checker
     * @return mixed
     */
    public function check($Checker)
    {
        $this->info->setProcess($this->process);
        if(!$this->info->check($Checker)) {
            return false;
        }

        $Checker->setProcess($this->info->getProcess());
        foreach ($this->validators as $validator) {
            if($validator->check($Checker) === false) {
                return false;
            }
        }

        $this->process = $Checker->getProcess();

        return true;
    }

    public function getUpCount()
    {
        $upCount = $this->info->getUpCount();
        return $upCount <= 0 ? 1 : $upCount;
    }

    public function isFinished()
    {
        return $this->count <= $this->count_done || $this->is_finished;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

	/**
	 * @return int
	 */
    public function getStartCount()
	{
		return $this->start_count;
	}

    /**
     * @return int
     */
    public function getCountDone()
    {
        return $this->count_done;
    }

    /**
     * @return mixed
     */
    public function getUserTaskId()
    {
        return $this->user_task_id;
    }

    public function addCountDone($count = 1)
    {
        $this->count_done += $count;
        return $this;
    }

    public function takeCountDone($count = 1)
    {
        $this->count_done -= $count;
        return $this;
    }

    public function getProcess()
    {
        return $this->process;
    }

    public function setProcess($process)
    {
        $this->process = $process;
        return $this;
    }

    public function addValidator($validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function getValidators()
    {
        return $this->validators;
    }
}