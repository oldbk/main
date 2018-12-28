<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;


use components\Component\Quests\check\CheckerFight;

class FightTask extends BaseTask
{
    public $min_damage;
    public $need_win;
    public $fight_type = 0;
    public $fight_comment = '';
    public $enemies;
    public $not_enemies;

    public function getItemType()
    {
        return self::ITEM_TYPE_FIGHT;
    }

    /**
     * @param CheckerFight $Checker
     * @return bool
     */
    public function check($Checker)
    {
        if($this->need_win && !$Checker->is_win) {
            return false;
        }

        $fight_types = $this->fight_type != 0 ? explode(',', $this->fight_type) : array();

        if($fight_types && !in_array($Checker->fight_type, $fight_types)) {
            return false;
        }

        if(!empty($this->fight_comment) && $this->fight_comment != $Checker->fight_comment) {
            return false;
        }

        if($this->min_damage > $Checker->damage) {
            return false;
        }

        $isEnemies = true;
        if($this->enemies) {
            $isEnemies = $this->checkEnemies($Checker);
        }
        $isNotEnemies = false;
        if($this->not_enemies) {
            $isNotEnemies = $this->checkNotEnemies($Checker);
        }
        if($isEnemies != true || $isNotEnemies != false) {
            return false;
        }


        return true;
    }

    /**
     * @param CheckerFight $Checker
     * @return bool
     */
    private function checkEnemies($Checker)
    {
        $flag = false;
        foreach (explode(',', $this->enemies) as $item) {
            if(mb_strpos($Checker->battle->t2hist, trim($item)) !== false) {
                $flag = true;
                break;
            }
        }

        if(!$flag) {
            return false;
        }

        return true;
    }

    /**
     * @param CheckerFight $Checker
     * @return bool
     */
    private function checkNotEnemies($Checker)
    {
        $flag = false;
        foreach (explode(',', $this->not_enemies) as $item) {
            if(mb_strpos($Checker->battle->t2hist, trim($item)) !== false) {
                $flag = true;
                break;
            }
        }

        if(!$flag) {
            return false;
        }

        return true;
    }
}