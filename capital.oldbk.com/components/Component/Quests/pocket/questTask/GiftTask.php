<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerGift;
use components\Helper\ShopHelper;
use components\models\User;

class GiftTask extends BaseTask
{
    const OPERATION_TYPE_FSHOP  = 'fshop';
    const OPERATION_TYPE_GIVE   = 'give';

    public $name;
    public $shop_id;
    public $item_id;
    public $item_ids;
    public $user_to;
    public $diff_persons    = 0;
    public $only_male       = 0;
    public $only_female     = 0;
    public $only_level      = 0;
    public $min_level;
    public $max_level;
    public $align           = '';
    public $is_fshop        = 0;
    public $is_give         = 0;

    public function getItemType()
    {
        return self::ITEM_TYPE_GIFT;
    }

    /**
     * @param CheckerGift $Checker
     * @return bool
     */
    public function check($Checker)
    {
        if($this->is_fshop && !$this->is_give && $Checker->operation_type != self::OPERATION_TYPE_FSHOP) {
            return false;
        }

        if($this->is_give && !$this->is_fshop && $Checker->operation_type != self::OPERATION_TYPE_GIVE) {
            return false;
        }

        if($Checker->shop_id != ShopHelper::TYPE_ALL && $Checker->shop_id != $this->shop_id) {
            return false;
        }

        if($this->user_to && !in_array($Checker->user_to->login, explode('|', $this->user_to))) {
            return false;
        }

        if($this->only_male && $Checker->user_to->sex != User::GENDER_MALE) {
            return false;
        }

        if($this->only_female && $Checker->user_to->sex != User::GENDER_FEMALE) {
            return false;
        }

        if($this->only_level && ($Checker->user_to->level < $this->min_level || $Checker->user_to->level > $this->max_level)) {
            return false;
        }

        if($this->diff_persons && in_array($Checker->user_to->id, $this->getProcess())) {
            return false;
        }

        if($this->align) {
            if(!in_array($Checker->user_to->align, explode(',', $this->align))) {
                return false;
            }
        }

        $item_ids = $this->item_ids ? explode(',', $this->item_ids) : array();
        if($this->item_id) {
            $item_ids[] = $this->item_id;
        }
        if(!in_array($Checker->item_id, $item_ids)) {
            return false;
        }

        $this->process[] = $Checker->user_to->id;

        return true;
    }
}