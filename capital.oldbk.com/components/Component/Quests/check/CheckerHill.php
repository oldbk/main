<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


class CheckerHill extends BaseChecker
{
    public $value;
    public $battle_id = 0;
    public $battle;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_HILL;
    }
}