<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\models\CraftProf;
use components\models\NewDelo;
use components\models\User;
use components\models\UsersCraft;

class ItemProfExp extends BaseItem
{
    public $count = 0;
    public $profession_id;

	/**
	 * ItemProfExp constructor.
	 * @param User $owner
	 * @param $count
	 * @param $profession_id
	 */
    public function __construct($owner, $count, $profession_id)
    {
        parent::__construct($owner);
        $this->count = $count;
        $this->profession_id = $profession_id;
    }

    public function give()
    {
    	/** @var CraftProf $Profession */
        $Profession = CraftProf::find($this->profession_id);
        if(!$Profession) {
            throw new \Exception();
        }

        $expField = $Profession->name.'exp';
        /** @var UsersCraft $UsersCraft */
        $UsersCraft = UsersCraft::find($this->owner->id)->toArray();
        if(!$UsersCraft || !isset($UsersCraft[$expField])) {
            throw new \Exception();
        }

        $_data = [
			$expField => $UsersCraft[$expField] + $this->count
		];
        if(!UsersCraft::where('owner', '=', $this->owner->id)->update($_data)) {
            throw new \Exception;
        }

        return true;
    }

    public function newDeloGive(array $data = array())
    {
        $_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'owner_balans_do'       => $this->owner->money,
			'owner_balans_posle'    => $this->owner->money,
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