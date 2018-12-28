<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


class CheckerDrop extends BaseChecker
{
    public $item_id;
    public $shop_id;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_DROP;
    }
}