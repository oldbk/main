<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 15.11.17
 * Time: 16:00
 */

namespace components\Helper\location;


use components\models\DtRate;
use components\models\User;

class LocationBs extends BaseLocation
{
	public function can($id)
	{
		$other_ips = [];
		/** @var DtRate[] $DtRate */
		$DtRate = DtRate::whereRaw('dtid = ? and owner != ?', [$id, $this->_user['id']])->get(['ip']);
		foreach ($DtRate as $_item) {
			$other_ips[] = $_item->ip;
		}
		if(empty($other_ips)) {
			return true;
		}

		$ips = User::getIps($this->_user['id']);
		if(empty($ips)) {
			return true;
		}
		$diff = array_intersect($ips, $other_ips);
		if($diff) {
			throw new \Exception();
		}

		return true;
	}
}