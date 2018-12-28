<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\models\NewDelo;
use components\models\quest\UserQuestEvent;
use components\models\User;

class ItemWeight extends BaseItem
{
    public $count = 0;
    protected $event_id;

	/**
	 * ItemWeight constructor.
	 * @param User $owner
	 * @param $count
	 * @param $event_id
	 */
    public function __construct($owner, $count, $event_id)
    {
        parent::__construct($owner);
        $this->count = $count;
        $this->event_id = $event_id;
    }

    public function give()
    {
    	/** @var UserQuestEvent $UserQuestEvent */
		$UserQuestEvent = UserQuestEvent::firstOrNew([
			'user_id' => $this->owner->id,
			'event_id' => $this->event_id,
			'date_event' => date('Y'),
		], ['count' => 0]);
		$UserQuestEvent->count += $this->count;
		if(!$UserQuestEvent->save()) {
			throw new \Exception();
		}
        
        return true;
    }

    public function newDeloGive(array $data = array())
    {
        $_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'item_count'            => $this->count,
			'sdate'                 => time(),
		];

        $_data = array_merge($_data, $data);

        if(!NewDelo::addNew($_data)) {
            return false;
        }

        return true;
    }

    public function take()
    {
        return false;
    }

    public function newDeloTake(array $data = array())
    {
        return false;
    }
}