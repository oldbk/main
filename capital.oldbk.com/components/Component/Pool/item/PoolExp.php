<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemExp;
use components\models\User;

class PoolExp extends BaseItem
{
    public function getItemType()
    {
        return self::ITEM_TYPE_EXP;
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
            $GiveExp = new ItemExp($owner, $this->give_count);
            if(!$GiveExp->give()) {
                throw new ExitTryException('ѕыталс€ выдать опыт');
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_EXP);

            if(!$GiveExp->newDeloGive($_data)) {
                throw new ExitTryException('ѕыталс€ добавить запись в дело');
            }

            return true;
        } catch (ExitTryException $ex) {
			Slim::getInstance()->logger->alert($ex->getMessage(), [
				'pool_id' 				=> $this->pool_id,
				'pool_pocket_id' 		=> $this->pool_pocket_id,
				'pool_pocket_item_id' 	=> $this->pool_pocket_item_id,
				'owner_id' 				=> $owner->id,
				'call'					=> 'PoolExp::give',
			]);
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->alert($ex);
        }
        
        return false;
    }

	public function getChatString()
	{
		return sprintf('ќпыт: %d. ', $this->give_count);
	}

	public function getViewArray()
	{
		return [
			'link' 	=> 'https://oldbk.com/encicl/exp.html',
			'img' 	=> 'http://i.oldbk.com/i/newd/icon_reputation.gif',
			'name'  => 'ќпыт',
			'count' => $this->give_count,
		];
	}
}