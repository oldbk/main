<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 15.11.17
 * Time: 16:00
 */

namespace components\Helper\location;

use components\models\RuinesStart;
use components\models\User;

class LocationRuine extends BaseLocation
{
	public function can($id)
	{
		/** @var RuinesStart $RuineStart */
		$RuineStart = RuinesStart::find($id);
		if(!$RuineStart) {
			throw new \Exception('', 12);
		}
		$_login_ids = explode(';', $RuineStart->t1_logins);
		if(empty($_login_ids)){
			return true;
		}

		$other_ips = $this->getOtherIps($_login_ids);
		if(empty($other_ips)) {
			return true;
		}

		$ips = User::getIps($this->_user['id']);
		if(empty($ips)) {
			return true;
		}
		$diff = array_intersect($ips, $other_ips);
		if($diff) {
			throw new \Exception('', 11);
		}

		return true;
	}
}