<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 26.09.2018
 * Time: 11:18
 */

require_once __DIR__ . '/init.php';

$User = \components\models\User::where('id', '=', 546433)->first();

for($i = 0; $i < 1000; $i++) {
	$OsadaRating = new \components\Helper\rating\OsadaRating();
	$OsadaRating->value_add = 1;


	$app->applyHook('event.rating', $User, $OsadaRating);
}