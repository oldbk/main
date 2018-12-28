<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


class CheckerWeight extends BaseChecker
{
    public $event_id;

    public $count = 1;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_WEIGHT;
    }
}