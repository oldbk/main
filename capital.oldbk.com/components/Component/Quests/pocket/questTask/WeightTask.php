<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerWeight;

class WeightTask extends BaseTask
{
    public $event_id;
    public $event_name;

    public function getItemType()
    {
        return self::ITEM_TYPE_WEIGHT;
    }

    /**
     * @param CheckerWeight $Checker
     * @return bool
     */
    public function check($Checker)
    {
        if($Checker->event_id != $this->event_id) {
            return false;
        }

        $this->process[] = $Checker->event_id;

        $this->setUpCount($Checker->count);

        return true;
    }
}