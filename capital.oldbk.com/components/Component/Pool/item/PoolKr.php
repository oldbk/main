<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemKr;
use components\models\User;

class PoolKr extends BaseItem
{
    public function getItemType()
    {
        return self::ITEM_TYPE_KR;
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
            $GiveExp = new ItemKr($owner, $this->give_count);
            if(!$GiveExp->give()) {
                throw new ExitTryException('	Пытался выдать кр');
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_KR);

            if(!$GiveExp->newDeloGive($_data)) {
                throw new ExitTryException('Пытался добавить запись в дело');
            }

            return true;
        } catch (ExitTryException $ex) {
			Slim::getInstance()->logger->alert($ex->getMessage(), [
				'pool_id' 				=> $this->pool_id,
				'pool_pocket_id' 		=> $this->pool_pocket_id,
				'pool_pocket_item_id' 	=> $this->pool_pocket_item_id,
				'owner_id' 				=> $owner->id,
				'call'					=> 'PoolKr::give',
			]);
		}
        catch (\Exception $ex) {
			Slim::getInstance()->logger->alert($ex);
        }

        return false;
    }

	public function getChatString()
	{
		return sprintf('Кр: %d. ', $this->give_count);
	}

	public function getViewArray()
	{
		return [
			'link' 	=> 'https://oldbk.com/encicl/kr.html',
			'img' 	=> 'http://i.oldbk.com/i/newd/icon_reputation.gif',
			'name'  => 'КР',
			'count' => $this->give_count,
		];
	}
}