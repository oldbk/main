<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\models\NewDelo;
use components\models\User;
use components\models\UserAbils;

class ItemAbility extends BaseItem
{
	public $count = 1;

	protected $magic_id;

	/**
	 * ItemAbility constructor.
	 * @param User $owner
	 * @param $magic_id
	 */
	public function __construct($owner, $magic_id)
	{
		parent::__construct($owner);

		$this->magic_id = $magic_id;
	}

	/**
	 * @return bool
	 */
	public function give()
	{
		if($this->count == 0) {
			$this->count = 1;
		}

		/** @var UserAbils $Abils */
		$Abils = UserAbils::firstOrNew(['owner' => $this->owner->id, 'magic_id' => $this->magic_id], [
			'allcount' 	=> 0,
			'findata' 	=> 0,
			'dailyc' 	=> 0,
			'daily' 	=> 0
		])->first();
		$Abils->allcount = $Abils->allcount + $this->count;

		if(!$Abils->save()) {
			return false;
		}

		return true;
	}

	/**
	 * @param array $data
	 * @return bool
	 */
	public function newDeloGive(array $data = array())
	{
		$_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'owner_balans_do'       => $this->owner->money,
			'owner_balans_posle'    => $this->owner->money,
			'item_count'            => $this->count,
			'add_info'              => $this->magic_id,
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