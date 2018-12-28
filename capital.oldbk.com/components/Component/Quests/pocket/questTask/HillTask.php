<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerHill;
use components\Component\VarDumper;
use components\models\Battle;

class HillTask extends BaseTask
{
    public $hills;
    public $any_battle = 0;
    public $enemies;
    public $fight_type;
    public $fight_comment;

    public function getItemType()
    {
        return self::ITEM_TYPE_HILL;
    }

    /**
     * @param CheckerHill $Checker
     * @return bool
     */
    public function check($Checker)
    {
        $hills = explode(',', $this->hills);
        if(!in_array($Checker->value, $hills)) {
            return false;
        }

        if($this->any_battle > 0 && $Checker->battle_id == 0) {
            return false;
        }

        if($this->enemies || $this->fight_type || $this->fight_comment) {
            if($Checker->battle) {
                $Battle = $Checker->battle;
            } else {
                $Battle = Battle::find($Checker->battle_id);
                if($Battle) {
                	$Battle = $Battle->toArray();
				}
            }

            if(!$Battle) {
                return false;
            }

            if($this->enemies) {
                $flag = false;
                foreach (explode(',', $this->enemies) as $item) {
                    if(mb_strpos($Battle['t2hist'], $item.'#') !== false) {
                        $flag = true;
                        break;
                    }
                }

                if(!$flag) {
                    return false;
                }
            }

            if($this->fight_comment && $Battle['coment'] != $this->fight_comment) {
                return false;
            }

            if($this->fight_type && $Battle['type'] != $this->fight_type) {
                return false;
            }
        }

        return true;
    }
}