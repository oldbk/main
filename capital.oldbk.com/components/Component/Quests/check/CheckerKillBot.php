<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 05.06.2016
 */

namespace components\Component\Quests\check;


class CheckerKillBot extends BaseChecker
{
    public $bot_id;
    public $t1hist;
    public $t2hist;

    public function getCheckerType()
    {
        return self::ITEM_TYPE_KILL_BOT;
    }
}