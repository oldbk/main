<?php
	if (!isset($mlglobal)) die();

	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');
	require_once('map_config.php');
	require_once('mlfunctions.php');

	// руны
	$mlrunes = 0;
	if (ADMIN) $mlrunes = 1;

	// есть-нет лошади
	$user['horse'] = $user['podarokAD'];

	$self = basename($_SERVER['PHP_SELF']);
	$bfound = false;
	reset($map_locations);
	while(list($k,$v) = each($map_locations)) {
		if ($v['redirect'] == $self && $v['room'] == $user['room']) {
			$bfound = true;
			break;
		}
	}

	if ($bfound === FALSE) Redirect("main.php");
	if ($user['battle'] != 0 || $user['battle_fin'] != 0) { Redirect("fbattle.php"); }

	// конюшня в городе
	if (isset($_GET['exit']) && $user['room'] == $maprel+$maprelall+999) {
		mysql_query("UPDATE oldbk.`users` SET `room` = '".($maprel-1)."' WHERE `id` = '{$_SESSION['uid']}' ;") or die();
		Redirect('outcity.php');		
	}

	if (isset($_GET['exit']) && $user['room'] == $maprel+$maprelall+1000) {
		mysql_query("UPDATE oldbk.`users` SET `room` = '".($maprel-2)."' WHERE `id` = '{$_SESSION['uid']}' ;") or die();
		Redirect('aoutcity.php');		           
	}

	if (isset($_GET['exit']) && $user['room'] != 49998 && $user['room'] != 49999) {
		$_SESSION['mappath'] = array();
		$_SESSION['mapcost'] = 0;

		$q = mysql_query('START TRANSACTION') or die();

		reset($map_locations);
		$roomtoexit = 0;
		while(list($k,$v) = each($map_locations)) {
			if ($v['room'] == $user['room']) {
				$roomtoexit = $v['dots'][0] + $maprel;
			}
		}		
		
		if (!$roomtoexit) die();

		$teamcache[$user['id']] = nick_hist($user);

		$q = mysql_query('INSERT INTO oldbk.map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$user['id'].','.$user['horse'].','.$roomtoexit.',"","","","'.mysql_real_escape_string(serialize($teamcache)).'",0)') or die();
		$id = mysql_insert_id();

		mysql_query('UPDATE oldbk.`users` SET room = '.$roomtoexit.', id_grup = '.$id.' WHERE id = '.$_SESSION['uid']) or die();

		$q = mysql_query('COMMIT') or die();
		Redirect('map.php');	
	}
?>