<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemWeight;
use components\models\quest\QuestEvent;
use components\models\quest\UserQuestCheck;
use components\models\User;

class PoolWeight extends BaseItem
{
    public $event_id;

    public function getItemType()
    {
        return self::ITEM_TYPE_WEIGHT;
    }

	/**
	 * @param User $owner
	 * @param \Closure $CallbackDelo
	 * @param null $CallbackItem
	 * @return bool
	 */
	public function give(User $owner, \Closure $CallbackDelo, $CallbackItem = null) : bool
    {
        $QuestEvent = QuestEvent::find($this->event_id)->toArray();
        if(!$QuestEvent) {
            return false;
        }

        try {
            $GiveWeight = new ItemWeight($owner, $this->give_count, $this->event_id);
            if(!$GiveWeight->give()) {
                throw new ExitTryException('ѕыталс€ выдать вес');
            }

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_WEIGHT);

            if(!$GiveWeight->newDeloGive($_data)) {
                throw new ExitTryException('ѕыталс€ добавить запись в дело');
            }

            $params = [
				'event_id' => $this->event_id
			];
            if(!UserQuestCheck::addWeight($owner->id, $this->give_count, $params)) {
                throw new ExitTryException('ѕыталс€ добавить вес в чекер');
            }

            return true;
		} catch (ExitTryException $ex) {
			Slim::getInstance()->logger->alert($ex->getMessage(), [
				'pool_id' 				=> $this->pool_id,
				'pool_pocket_id' 		=> $this->pool_pocket_id,
				'pool_pocket_item_id' 	=> $this->pool_pocket_item_id,
				'owner_id' 				=> $owner->id,
				'call'					=> 'PoolWeight::give',
			]);
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->alert($ex);
		}

        return false;
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