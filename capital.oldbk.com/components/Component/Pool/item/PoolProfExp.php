<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemProfExp;
use components\models\User;

class PoolProfExp extends BaseItem
{
    public $profession_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_PROF_EXP;
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
            $GiveExp = new ItemProfExp($owner, $this->give_count, $this->profession_id);
            if(!$GiveExp->give()) {
                throw new ExitTryException('Пытался добавить опыт крафта');
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_PROF_EXP);

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
				'call'					=> 'PoolCraftexp::give',
			]);
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->alert($ex);
		}

        return false;
    }

	public function getChatString()
	{
		return sprintf('Опыт профессии: %d. ', $this->give_count);
	}

	public function getViewArray()
	{
		return [
			'link' 	=> 'https://oldbk.com/encicl/kr.html',
			'img' 	=> 'http://i.oldbk.com/i/newd/icon_reputation.gif',
			'name'  => 'Опыт профессии',
			'count' => $this->give_count,
		];
	}
}