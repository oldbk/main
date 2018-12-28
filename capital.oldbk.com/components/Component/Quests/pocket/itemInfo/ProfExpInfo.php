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
use components\Helper\item\ItemProfExp;
use components\models\NewDelo;
use components\models\User;

class ProfExpInfo extends BaseInfo
{
    public $profession_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_PROF_EXP;
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
            $GiveExp = new ItemProfExp($owner, $Reward->count, $this->profession_id);
            if(!$GiveExp->give()) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => ' вест',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_PROFEXP,
                'add_info'              => $Part->name,
            );

            if(!$GiveExp->newDeloGive($_data)) {
                throw new \Exception;
            }

        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_reward_prof_exp');
            return false;
        }

        return true;
    }
}