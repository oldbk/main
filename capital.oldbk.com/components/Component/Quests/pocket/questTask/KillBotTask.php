<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.06.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerKillBot;

class KillBotTask extends BaseTask
{
    public $bot_names;
    public $diff_bot = 0;

    public function getItemType()
    {
        return self::ITEM_TYPE_KILL_BOT;
    }

    /**
     * @param CheckerKillBot $Checker
     * @return bool
     */
    public function check($Checker)
    {
        foreach (explode(',', $this->bot_names) as $item) {
            $flag = false;
            if(mb_strpos($Checker->t2hist, $item.'#') !== false) {
                if(!$this->diff_bot) {
                    $flag = true;
                }
                if(!in_array($item, $Checker->process)) {
                    $flag = true;
                }
            }

            if($flag) {
                $this->process[] = $item;
                return true;
            }
        }

        return false;
    }
}