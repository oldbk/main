<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerItem;
use components\Helper\ShopHelper;

class BuyTask extends BaseTask
{
    public $shop_id;
    public $category_id;
    public $item_id;
    public $name;

    public function getItemType()
    {
        return self::ITEM_TYPE_BUY;
    }

    /**
     * @param CheckerItem $Checker
     * @return bool
     */
    public function check($Checker)
    {
        if($this->shop_id && $this->shop_id != $Checker->shop_id && $Checker->shop_id != ShopHelper::TYPE_ALL) {
            return false;
        }

        if($this->category_id && $this->category_id != $Checker->category_id) {
            return false;
        }

        if($this->item_id && $this->item_id != $Checker->item_id) {
            return false;
        }

        return true;
    }
}