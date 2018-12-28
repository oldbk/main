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
use components\Helper\item\ItemEkr;
use components\models\NewDelo;
use components\models\User;

class EkrInfo extends BaseInfo
{
    public function getItemType()
    {
        return self::ITEM_TYPE_EKR;
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
            $GiveEkr = new ItemEkr($owner, $Reward->count);
            if(!$GiveEkr->give()) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => ' вест',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_EKR,
                'sum_ekr'               => $Reward->count,
                'add_info'              => $Part->name,
            );

            if(!$GiveEkr->newDeloGive($_data)) {
                throw new \Exception;
            }
        } catch (\Exception $ex) {
            if($ex->getMessage() != '') {
                FileHelper::writeException($ex, 'quest_reward_ekr');
            }
            return false;
        }

        return true;
    }
}