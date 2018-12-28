<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 15.11.17
 * Time: 16:00
 */

namespace components\Helper\location;

use components\models\NturUsers;
use components\models\User;

class LocationNtur extends BaseLocation
{
	public function can($id)
	{
		/** @var NturUsers $NturUsers */
		$NturUsers = NturUsers::find($id);
		if(!$NturUsers || $NturUsers->stat != 0) {
			return false;
		}
		$user_ids = [];
		for ($i = 1; $i < 33; $i++) {
			$filed = 'o'.$i;
			if($NturUsers->{$filed} > 0) {
				$user_ids[] = $NturUsers->{$filed};
			}
		}
		if(empty($user_ids)){
			return true;
		}

		$other_ips = $this->getOtherIps($user_ids);
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