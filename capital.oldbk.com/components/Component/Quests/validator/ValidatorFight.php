<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 27.08.16
 * Time: 23:18
 */

namespace components\Component\Quests\validator;


use components\Component\Quests\check\BaseChecker;
use components\Component\Quests\check\CheckerEvent;
use components\Component\Quests\check\iChecker;
use components\models\Battle;

class ValidatorFight extends BaseValidator
{
    public $type = 0;
    public $comment;
    public $enemies; //противники
    public $helpers; //помощники
    public $need_win = 0;
    public $min_damage = 0;

    public function getCheckerTypes()
    {
        return array(
            BaseChecker::ITEM_TYPE_EVENT,
        );
    }

    /**
     * @param iChecker|CheckerEvent $Checker
     * @return bool
     */
    public function check($Checker)
    {
        /** @var Battle $Battle */
        $Battle = $Checker->getBattle();
        $user = $Checker->getUser();

        if(!$Battle && $user->battle) {
            $Battle = Battle::find($user->battle);
            $Checker->setBattle($Battle);
        }
        if(!$Battle) {
            return false;
        }

        if($this->need_win && !$Battle->is_win) {
            return false;
        }

        if($this->min_damage > $Battle->damage) {
            return false;
        }

        $fight_types = $this->type != 0 ? explode(',', $this->type) : array();
        if($fight_types && !in_array($Battle->type, $fight_types)) {
            return false;
        }

        if($this->comment && $this->comment != $Battle->coment) {
            return false;
        }

        if($this->enemies) {
            $flag = false;
            foreach (explode(',', $this->enemies) as $item) {
                if(mb_strpos($Battle->t2hist, $item.'#') !== false) {
                    $flag = true;
                    break;
                }
            }

            if(!$flag) {
                return false;
            }
        }

        if($this->helpers) {
            $flag = false;
            foreach (explode(',', $this->helpers) as $item) {
                if(mb_strpos($Battle->t1hist, $item.'#') !== false) {
                    $flag = true;
                    break;
                }
            }

            if(!$flag) {
                return false;
            }
        }

        return true;
    }
}