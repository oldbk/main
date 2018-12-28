<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Quests\pocket\itemInfo;

class CustomItemInfo extends BaseInfo
{
    public $get_method;

    public $goden = 0;
    public $ekr_flag = 0;
    public $item_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_CUSTOM_ITEM;
    }
}