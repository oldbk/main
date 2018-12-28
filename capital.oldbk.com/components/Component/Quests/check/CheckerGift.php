<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;

use components\models\User;

class CheckerGift extends BaseChecker
{
    public $name;
    public $shop_id;
    public $item_id;
    /** @deprecated */
    public $user_id;
    /** @deprecated */
    public $user_gender;
    /** @deprecated */
    public $user_login;
    /** @deprecated */
    public $user_level;
    public $operation_type;
    /** @var User */
    public $user_to;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_GIFT;
    }
}