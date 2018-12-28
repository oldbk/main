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
use components\Helper\item\ItemRep;
use components\models\NewDelo;
use components\models\User;

class RepaInfo extends BaseInfo
{
    public function getItemType()
    {
        return self::ITEM_TYPE_REPA;
    }

    /**
     * @param User $owner
     * @param Part $Part
     * @param Reward $Reward
     * @return bool
     */
    public function give($owner, Part $Part, Reward $Reward)
    {
        try {
            $GiveRep = new ItemRep($owner, $Reward->count);
            if(!$GiveRep->give()) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => ' вест',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_REP,
                'add_info'              => $Part->name,
            );

            if(!$GiveRep->newDeloGive($_data)) {
                throw new \Exception;
            }
        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_reward_repa');
            return false;
        }

        return true;
    }
}