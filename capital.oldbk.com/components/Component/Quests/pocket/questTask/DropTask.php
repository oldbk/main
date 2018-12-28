<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerDrop;
use components\Helper\ShopHelper;
use components\models\Inventory;

class DropTask extends BaseTask
{
    public $shop_id;
    public $item_id;
    public $item_ids;
    public $is_all;
    public $name;

    public function getItemType()
    {
        return self::ITEM_TYPE_DROP;
    }

    /**
     * @param CheckerDrop $Checker
     * @return bool
     */
    public function check($Checker)
    {
        if($Checker->shop_id != ShopHelper::TYPE_ALL && $Checker->shop_id != $this->shop_id) {
            return false;
        }

        $item_ids = $this->item_ids ? explode(',', $this->item_ids) : array();
        if($this->item_id) {
            $item_ids[] = $this->item_id;
        }

        if(!in_array($Checker->item_id, $item_ids)) {
            return false;
        }

        if($this->is_all && in_array($Checker->item_id, $this->process)) {
            return false;
        }
        $this->process[] = $Checker->item_id;

        return true;
    }

    public function getCount()
    {
    	return Inventory::whereRaw('prototype = ? and setsale = 0 and owner = ?', [$this->item_id, $this->getUser()->id])->count();
    }
}