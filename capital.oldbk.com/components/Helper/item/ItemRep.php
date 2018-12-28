<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\Component\VarDumper;
use components\models\NewDelo;
use components\models\User;

class ItemRep extends BaseItem
{
    public $count = 0;

	/**
	 * ItemRep constructor.
	 * @param User $owner
	 * @param $count
	 */
    public function __construct($owner, $count)
    {
        parent::__construct($owner);
        $this->count = $count;
    }

    public function give()
    {
        $_data = [
			'repmoney' => $this->owner->repmoney + $this->count,
			'rep' => $this->owner->rep + $this->count,
		];
		if(!User::where('id', '=', $this->owner->id)->limit(1)->update($_data)) {
			throw new \Exception;
		}

        $this->owner->repmoney += $this->count;
        $this->owner->rep += $this->count;
        return true;
    }

    public function newDeloGive(array $data = array())
    {
        $_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'owner_balans_do'       => $this->owner->money,
			'owner_balans_posle'    => $this->owner->money,
			'owner_rep_do'          => $this->owner->repmoney - $this->count,
			'owner_rep_posle'       => $this->owner->repmoney,
			'sum_rep'               => $this->count,
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