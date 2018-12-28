<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 31.05.2016
 */

namespace components\Component\Quests\pocket\questTask;

use components\Component\Quests\check\CheckerEvent;

class EventTask extends BaseTask
{
    const EVENT_RUIN_KEY        			= 'ruin_key';
    const EVENT_RUIN_WIN        			= 'ruin_win';
    const EVENT_RUIN_DO         			= 'ruin_do';
    const EVENT_RIST_WIN        			= 'rist_win';
    const EVENT_RIST_DO         			= 'rist_do';
    const EVENT_FONTAN          			= 'fontan';
    const EVENT_RUIN_REWARD     			= 'ruin_reward';
    const EVENT_BS_WIN          			= 'bs_win';
    const EVENT_BS_DO           			= 'bs_do';
    const EVENT_BS_CHECk        			= 'bs_check';
    const EVENT_FIGHT_HIT       			= 'fight_hit';
    const EVENT_MAKE_FLOWER     			= 'make_flower';
    const EVENT_FLOWER_BOX      			= 'flower_box';
    const EVENT_LOCATION_ENTER  			= 'location_enter';
    const EVENT_REGISTRATION    			= 'registration';
    const EVENT_GIVE_SNOWBALL   			= 'give_snowball';
    const EVENT_COMMENT_ELKA    			= 'comment_elka';
    const EVENT_GAME_ENTER      			= 'game_enter';
    const EVENT_FONTAN_WIN      			= 'fontan_win';
    const EVENT_TOWN_OUT_QUEST_ANY_FINISH 	= 'town_out_quest_any_finish';
	const EVENT_LAB_QUEST_ANY_FINISH 		= 'lab_quest_any_finish';
	const EVENT_FORTUNA 					= 'fortuna_any';

    public $event_type;

    public function getItemType()
    {
        return self::ITEM_TYPE_EVENT;
    }

    /**
     * @param CheckerEvent $Checker
     * @return bool
     */
    public function check($Checker)
    {
         if($Checker->event_type != $this->event_type) {
             return false;
         }

        $methodName = $Checker->event_type.'Check';
         if(method_exists($this, $methodName)) {
             $this->$methodName($Checker);
         }

         return true;
    }

    public function fight_hitCheck($Checker)
    {
        $this->setUpCount($Checker->count);
    }
}