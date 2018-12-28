<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.02.2018
 * Time: 00:54
 */

//dirty fix for including in the function
if(!isset($app)) {
	global $app;
}

require_once ROOT_DIR.'/components/config/routes.php';
if($app->bk_security->isNeedVerify()) {
	$app->bk_security->setRef(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/main.php');
	header('Location: /action/security/twofa');
}