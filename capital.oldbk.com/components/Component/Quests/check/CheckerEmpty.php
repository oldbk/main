<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


class CheckerEmpty extends BaseChecker
{
    public $count;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_EMPTY;
    }
}