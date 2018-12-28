<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.05.2016
 */

namespace components\Component\Loto;


use components\models\Chat;
use components\models\Inventory;
use components\models\ItemLoto;
use components\models\ItemLotoRas;
use components\models\LotoItem;
use components\models\StaticMessage;

class Loto extends BaseLoto
{
    public function run()
    {
        $messages = StaticMessage::where('message_type', '=', StaticMessage::MESSAGE_LOTO)
			->whereIn('id', [4, 5])
			->get()->toArray();
        foreach ($messages as $message) {
            switch ($message['id']) {
                case 4:
                    $this->message_finish = $message['message'];
                    break;
                case 5:
                    $this->message_private = $message['message'];
                    break;
            }
        }

        $this->buildItemList();
    }

    /**
     * @param $owner //пользователь, который получает выигрышь
     * @param $ticket_id //номер билета
     * @param null $loto_item_id
     * @return bool
     */
    public function give($owner, $ticket_id, $loto_item_id = null)
    {
        $this->debug('¬ыдаем награду');
        if($loto_item_id === null) {
            $loto_item_id = $this->getItemLotoId();
        }

        $itemObject = $this->item_list[$loto_item_id];
        if(!$itemObject->give($owner)) {
            return false;
        }
        $item = $itemObject->getItem();
        $item_loto = $itemObject->getItemLoto();

        $this->debug(sprintf('¬ыдали награду %s персонажу %s (%d)', $item['name'], $owner['login'], $owner['id']));

        if($this->_debug === true) {
            return true;
        }

        $_data = [
			'item_name' => $item['name'],
			'item_id'   => $item_loto['info']['item_id'],
			'shop_id'   => $item_loto['info']['shop_id'],
			'cost_kr'   => $item_loto['cost_kr'],
			'cost_ekr'  => $item_loto['cost_ekr'],
		];

		if(!ItemLoto::where('id', '=', $ticket_id)->update($_data)) {
			return false;
		}

        $this->item_use_count[$loto_item_id]++;
        $_data = [
			'use_count' => $this->item_use_count[$loto_item_id]
		];
		if(!LotoItem::whereRaw('id = ? and loto_num = ?', [$item_loto['id'], $item_loto['loto_num']])->update($_data)) {
			$this->item_use_count[$loto_item_id]--;
			return false;
		}

        return true;
    }

    public function finish()
    {
        if($this->_debug === true) {
            return true;
        }

        //отправл€ем сообщение о завершении
        $message = str_replace(array('%loto_num%'), array($this->loto_id), $this->message_finish);
        if(!Chat::addToAll($message)) {
            return false;
        }

        $_data = [
			'status' => 0,
		];
        //помечаем как завершенный текущий тираж
        if(!ItemLotoRas::where('id', '=', $this->loto_id)->update($_data)) {
            return false;
        }

        $DateTime = new \DateTime();
        $DateTime->modify('+1 week')
            ->setTime(20,0);
        $_data = [
			'lotodate' => $DateTime->getTimestamp(),
			'status' => 1
		];
        //выставл€ем следующий тираж
        if(!ItemLotoRas::insert($_data)) {
            return false;
        }

		//удал€ем все билеты
		Inventory::whereRaw('prototype = ? and upfree = ?', [33333, $this->loto_id])->delete();

        //запускаем вновь сообщени€
        $_data = [
			'is_send' => 0,
		];
		StaticMessage::where('message_type', '=', StaticMessage::MESSAGE_LOTO)
			->whereRaw('is_fixed = 0')->update($_data);

        return true;
    }
}