<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\models\Bank;
use components\models\BankHistory;
use components\models\NewDelo;
use components\models\User;

class ItemEkr extends BaseItem
{
    public $count = 0;
    protected $balance_do = 0;
    protected $balance_posle = 0;

	/**
	 * ItemEkr constructor.
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
        $Bank = Bank::findBank($this->owner->getId());
        if(!$Bank) {
            throw new \Exception('Нет счета. ID:'.$this->owner->getId());
        }

        $this->balance_do = $Bank['ekr'];
        $this->balance_posle = $Bank['ekr'] + $this->count;
        $_data = [
			'ekr' => $Bank['ekr'] + $this->count
		];
        if(!Bank::where('id', '=', $Bank['id'])->update($_data)) {
            throw new \Exception;
        }

        $_data = [
			'date' => time(),
			'bankid' => $Bank['id'],
			'text' => sprintf('Вы получили на счет <b>%d</b> екр. <i>(Итого: %s кр., %s екр.)</i>',
				$this->count, $Bank['cr'], $this->balance_posle)
		];
        if(!BankHistory::insert($_data)) {
            throw new \Exception;
        }
        
        return true;
    }

    public function newDeloGive(array $data = array())
    {
        $_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'owner_balans_do'       => $this->balance_do,
			'owner_balans_posle'    => $this->balance_posle,
			'sum_ekr'               => $this->count,
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