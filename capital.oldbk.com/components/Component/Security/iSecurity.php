<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.02.2018
 * Time: 00:47
 */

namespace components\Component\Security;


interface iSecurity
{
	public function isNeedVerify();
	public function verify($user_id, $code);
	public function setRef($link);
	public function getRef();
}