<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 09.06.2016
 */

namespace components\Component\Quests\object;


use components\Component\Quests\check\iChecker;
use components\Component\Quests\pocket\itemInfo\iRewardItem;
use components\Component\Quests\validator\iValidator;

class Reward extends Base
{
    public $pocket_item_id;
    public $pocket_id;
    public $quest_id;
    public $item_type;

    public $item_id;

    public $count = 0;

    /** @var iRewardItem */
    public $info;

    /**
     * @var iValidator[]
     */
    public $validators = array();

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
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return iRewardItem
     */
    public function getInfo()
    {
        return $this->info;
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

    /**
     * @param iChecker $Checker
     * @return mixed
     */
    public function check($Checker)
    {
        foreach ($this->validators as $validator) {
            if($validator->check($Checker) === false) {
                return false;
            }
        }

        return true;
    }
}