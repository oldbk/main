<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


class CheckerItem extends BaseChecker
{
    public $shop_id;
    public $item_id;
    public $name;
    public $category_id;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_ITEM;
    }
}