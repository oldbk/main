<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.05.2016
 */

namespace components\Component\Loto;

use components\Component\Db\CapitalDb;
use components\models\Chat;
use components\models\StaticMessage;

class LotoMessage extends BaseLoto
{
    public function run()
    {
		$messages = StaticMessage::where('message_type', '=', StaticMessage::MESSAGE_LOTO)
			->whereIn('id', [4, 5])
			->get()->toArray();
        foreach ($messages as $message) {
            $this->message_other[$message['id']] = $message;
        }
    }

    public function sendMessages()
    {
        $Time = new \DateTime();
        foreach ($this->message_other as $message_id => $message) {
            if($message['is_send'] || $message['must_send'] > $Time->getTimestamp()) {
                continue;
            }
            $db = CapitalDb::connection();
            $db->beginTransaction();
            try {
                $loto_time = new \DateTime();
                $loto_time->setTimestamp($this->loto['lotodate']);

                $text = str_replace(array('%loto_num%', '%time%'), array($this->loto_id, $loto_time->format('H:i')), $message['message']);
                if(!Chat::addToAll($text)) {
                    throw new \Exception();
                }

                $DataTime = new \DateTime();
                $DataTime->setTimestamp($message['must_send'])
                    ->modify(sprintf('+%d days', $message['day_interval']));
                $_data = array(
                    'is_send'   => 1,
                    'must_send' => $DataTime->getTimestamp(),
                );
				StaticMessage::where('id', '=', $message['id'])->update($_data);

                $db->commit();
            } catch (\Exception $ex) {
                $db->rollBack();

                return false;
            }
        }

        return true;
    }
}