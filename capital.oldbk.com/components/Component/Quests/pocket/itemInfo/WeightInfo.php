<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Quests\pocket\itemInfo;

use components\Component\Quests\object\Part;
use components\Component\Quests\object\Reward;
use components\Helper\FileHelper;
use components\Helper\item\ItemWeight;
use components\models\NewDelo;
use components\models\quest\QuestEvent;
use components\models\quest\UserQuestCheck;
use components\models\User;
use components\models\UserBadge;

class WeightInfo extends BaseInfo
{
    public $event_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_WEIGHT;
    }

	/**
	 * @param User $owner
	 * @param Part $Part
	 * @param Reward $Reward
	 * @return bool
	 */
    public function give($owner, Part $Part, Reward $Reward)
    {
        $QuestEvent = QuestEvent::find($this->event_id)->toArray();
        if(!$QuestEvent) {
            return false;
        }

        try {
            $GiveExp = new ItemWeight($owner, $Reward->count, $this->event_id);
            if(!$GiveExp->give()) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => ' вест',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_WEIGHT,
                'sum_kr'                => $Reward->count,
                'add_info'              => $QuestEvent['name'],
            );

            if(!$GiveExp->newDeloGive($_data)) {
                throw new \Exception;
            }

            $params = array(
                'event_id' => $this->event_id
            );
            if(!UserQuestCheck::addWeight($owner->id, $Reward->count, $params)) {
                throw new \Exception;
            }
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_reward_weight');
            return false;
        }

        return true;
    }
}