<?php

if (!isset($_GET['key'])) die();
if ($_GET['key'] != 'q5tyv28tui245ti4ju5thn5tn4k5tj') die();

session_start();

if (!isset($_SESSION['uid'])) die();

require_once "connect.php";

if (isset($_SESSION['uid'])) {
	$query = mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
	$user = mysql_fetch_assoc($query);
}


if (!isset($user['id'])) die();

$palrights = array();

$data=mysql_query("SELECT * FROM oldbk.`pal_rights` WHERE pal_id='".$user['id']."' LIMIT 1");
if (mysql_num_rows($data) > 0) {
	$palrights = mysql_fetch_assoc($data);
}

$tmp = $user;
unset($tmp['info']);

echo serialize(array_merge($_SESSION,['__user' => $tmp,['__palrights' => $palrights]]));