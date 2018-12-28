<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;

use components\models\Battle;

class CheckerEvent extends BaseChecker
{
    public $event_type;

    public $count = 1;

    /** @var Battle */
    public $battle = null;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_EVENT;
    }
}