<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Quests\pocket\itemInfo;

use components\Component\Quests\object\Part;
use components\Component\Quests\object\Reward;
use components\Helper\item\ItemAbility;
use components\models\NewDelo;
use components\models\User;

class AbilityInfo extends BaseInfo
{
    public $magic_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_ABILITY_OWN;
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
            $GiveAbility = new ItemAbility($owner, $this->magic_id);
            $GiveAbility->count = $Reward->count;
            if(!$GiveAbility->give()) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => 'Квест',
                'type'                  => NewDelo::TYPE_LOTO_ABILITY,
                'item_name'             => $this->getName(),
                'item_count'            => $Reward->getCount(),
                'add_info'              => $Part->name,
            );

            if(!$GiveAbility->newDeloGive($_data)) {
                throw new \Exception;
            }
        } catch (\Exception $ex) {
            return false;
        }

        unset($owner);
        return true;
    }
}