<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemRep;
use components\models\User;

class PoolRepa extends BaseItem
{
    public function getItemType()
    {
        return self::ITEM_TYPE_REPA;
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
            $GiveRep = new ItemRep($owner, $this->give_count);
            if(!$GiveRep->give()) {
                throw new ExitTryException('Пытался выдать репу');
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_REPA);

			if(!$GiveRep->newDeloGive($_data)) {
				throw new ExitTryException('Пытался добавить запись в дело');
			}

			return true;
		} catch (ExitTryException $ex) {
			Slim::getInstance()->logger->alert($ex->getMessage(), [
				'pool_id' 				=> $this->pool_id,
				'pool_pocket_id' 		=> $this->pool_pocket_id,
				'pool_pocket_item_id' 	=> $this->pool_pocket_item_id,
				'owner_id' 				=> $owner->id,
				'call'					=> 'PoolRep::give',
			]);
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->alert($ex);
		}

        return false;
    }

	public function getChatString()
	{
		return sprintf('Репутация: %d. ', $this->give_count);
	}

	public function getViewArray()
	{
		return [
			'link' 	=> 'https://oldbk.com/encicl/repa.html',
			'img' 	=> 'http://i.oldbk.com/i/newd/icon_reputation.gif',
			'name'  => 'Репутация',
			'count' => $this->give_count,
		];
	}
}