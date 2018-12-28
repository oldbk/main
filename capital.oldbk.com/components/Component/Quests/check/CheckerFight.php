<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;

use components\models\Battle;

class CheckerFight extends BaseChecker
{
    public $damage;
    public $is_win;
    public $fight_type;
    public $fight_comment;
    /** @var Battle */
    public $battle;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_FIGHT;
    }
}