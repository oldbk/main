<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemEkr;
use components\models\User;

class PoolEkr extends BaseItem
{
    public function getItemType()
    {
        return self::ITEM_TYPE_EKR;
    }

	/**
	 * @param User $owner
	 * @param \Closure $CallbackDelo
	 * @param \Closure|null $CallbackItem
	 * @return bool
	 */
	public function give(User $owner, \Closure $CallbackDelo, $CallbackItem = null) : bool
    {
        try {
            $GiveEkr = new ItemEkr($owner, $this->give_count);
            if(!$GiveEkr->give()) {
                throw new ExitTryException('ѕыталс€ выдать екр');
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_EKR);
            if(!$GiveEkr->newDeloGive($_data)) {
                throw new ExitTryException('ѕыталс€ добавить запись в дело');
            }

            return true;
        } catch (ExitTryException $ex) {
			Slim::getInstance()->logger->alert($ex->getMessage(), [
				'pool_id' 				=> $this->pool_id,
				'pool_pocket_id' 		=> $this->pool_pocket_id,
				'pool_pocket_item_id' 	=> $this->pool_pocket_item_id,
				'owner_id' 				=> $owner->id,
				'call'					=> 'PoolEkr::give',
			]);
        } catch (\Exception $ex) {
			Slim::getInstance()->logger->error($ex);
		}

        return false;
    }

	public function getChatString()
	{
		return sprintf('≈кр: %d. ', $this->give_count);
	}

	public function getViewArray()
	{
		return [
			'link' 	=> 'https://oldbk.com/encicl/ekr.html',
			'img' 	=> 'http://i.oldbk.com/i/newd/icon_reputation.gif',
			'name'  => '≈кр',
			'count' => $this->give_count,
		];
	}
}