<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.02.2018
 * Time: 00:22
 */

namespace components\Component\Security;


use components\models\User2fa;

class TwoFA implements iSecurity
{
	const CHECK_TIME = 1800;

	public function __construct()
	{
		if(!isset($_SESSION['bk_security'])) {
			$_SESSION['bk_security'] = [
				'access' => false,
				'time' => time() - self::CHECK_TIME,
				'ref' => null,
			];
		}
	}

	public function isNeedVerify()
	{
		if(time() - $_SESSION['bk_security']['time'] >= self::CHECK_TIME || $_SESSION['bk_security']['access'] == false) {
			return true;
		}

		return false;
	}

	public function verify($user_id, $code)
	{
		$user2fa = User2fa::find($user_id);
		if(!$user2fa || !$user2fa->isEnabled()) {
			$this->setup(true, true);
			return true;
		}

		$google2fa = new \PragmaRX\Google2FA\Google2FA();
		$valid = $google2fa->verifyKey($user2fa->secret, $code);
		if($valid) {
			$this->setup(true, true);
		} else {
			$this->setup(false);
		}

		return $valid;
	}

	public function setRef($link)
	{
		$_SESSION['bk_security']['ref'] = $link;
	}

	public function getRef()
	{
		return $_SESSION['bk_security']['ref'];
	}

	private function setup($access, $update_time = false)
	{
		$_SESSION['bk_security']['access'] = $access;
		if($update_time) {
			$_SESSION['bk_security']['time'] = time();
		}
	}
}