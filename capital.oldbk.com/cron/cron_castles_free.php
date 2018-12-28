<?php
	require_once('/www/capitalcity.oldbk.com/cron/init.php');
	require_once('/www/capitalcity.oldbk.com/functions.php');
	require_once('/www/capitalcity.oldbk.com/castles_functions.php');
	require_once('/www/capitalcity.oldbk.com/castles_config.php');
	require_once('/www/capitalcity.oldbk.com/memcache.php');

	if(!lockCreate("cron_castles_free") ) {
	    	exit("Script already running.");
	}

	function MyDie($txt) {
		echo $txt."\n";
		return FALSE;
	}

	function EchoLog($txt) {
		echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
	}

	$q = mysql_query('SELECT * FROM oldbk.castles WHERE status = 0 AND dayofweek = '.date("N").' AND hourofday = '.date("G"));
	$txt = "";
	while($c = mysql_fetch_assoc($q)) {
		EchoLog("Freeing castle id: ".$c['id'].":".serialize($c));
		mysql_query('UPDATE oldbk.castles SET clanshort = "" WHERE id = '.$c['id']);
		mysql_query('UPDATE oldbk.users SET room = '.($c['id']+70000).' WHERE room = '.($c['id']+71000).' and id_city = 0');
		$txt .= $castles_config[$c['num']]['name'].' ['.$c['nlevel'].'], ';
	}

	if (strlen($txt)) {
		$txt = substr($txt,0,strlen($txt)-2);
		// системка про освобождения замков
		$alltxt = 'Внимание! <b>Освободились <a href="http://oldbk.com/encicl/?/zamki.html" target="_blank"><b>замки</b></a></b>: '.$txt.'. <a href="http://oldbk.com/encicl/?/zamkibattle.html" target="_blank">Подача заявок</a> на турнир возможна в течение часа.';
		EchoLog("system message: ".$alltxt);
		addch2all($alltxt,0);
	}

	lockDestroy("cron_castles_free");
?>