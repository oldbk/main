<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerEmpty;
use components\Component\VarDumper;
use components\models\Inventory;

class ItemTask extends BaseTask
{
    public $shop_id;
    public $item_id;
    public $name;

    private $item_count = null;

    public function getItemType()
    {
        return self::ITEM_TYPE_EMPTY;
    }

    /**
     * @param CheckerEmpty $Checker
     * @return bool
     */
    public function check($Checker)
    {
    	if($this->item_count === null) {
			$this->item_count = Inventory::whereRaw('prototype = ? and setsale = 0 and owner = ?', [$this->item_id, $this->getUser()->id])->count();
		}

        if($this->item_count < $Checker->count) {
            return false;
        }

        return true;
    }

    public function getCount()
    {
    	if($this->item_count === null) {
			$this->item_count = Inventory::whereRaw('prototype = ? and setsale = 0 and owner = ?', [$this->item_id, $this->getUser()->id])->count();
		}

		return $this->item_count;
    }
}