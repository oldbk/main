<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Helper\item\ItemAbility;
use components\models\User;

class PoolAbility extends BaseItem
{
    public $magic_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_ABILITY_OWN;
    }

	/**
	 * @param User $owner
	 * @param \Closure $CallbackDelo
	 * @param null $CallbackItem
	 * @return bool
	 */
	public function give(User $owner, \Closure $CallbackDelo, $CallbackItem = null) : bool
    {
        try {
            $GiveAbility = new ItemAbility($owner, $this->magic_id);
            $GiveAbility->count = $this->give_count;
            if(!$GiveAbility->give()) {
                throw new \Exception;
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_ABILITY_OWN);

            if(!$GiveAbility->newDeloGive($_data)) {
                throw new \Exception;
            }
        } catch (\Exception $ex) {
            return false;
        }

        unset($owner);
        return true;
    }

	public function getChatString()
	{
		return null;
	}

	public function getViewArray()
	{
		return [];
	}
}