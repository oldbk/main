<?php
	include "/www/capitalcity.oldbk.com/cron/init.php";
	require_once('/www/capitalcity.oldbk.com/functions.php');
	require_once('/www/capitalcity.oldbk.com/castles_functions.php');
	require_once('/www/capitalcity.oldbk.com/castles_config.php');
	require_once('/www/capitalcity.oldbk.com/memcache.php');

	if(!lockCreate("cron_castles_osada") ) {
	    	exit("Script already running.");
	}

	function MyDie($txt) {
		echo $txt."\n";
		return FALSE;
	}

	function EchoLog($txt) {
		echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
	}


	$q = mysql_query('SELECT * FROM castles_osada WHERE osadaend < '.time().' or score >= '.$osada_end);
	while($c = mysql_fetch_assoc($q)) {
	        EchoLog("Processing: ".$c['id']);

		$h = date("H",$c['nextosada']);
		$dur = date("H",$c['osadaend'])-date("H",$c['nextosada']);

		$key = -1;

		foreach($osada[$c['id']] as $k => $v) {
			if ($v['start'] == $h && $v['duration'] == $dur) {
				$key = $k;
				break;
			}
		}

		$newosada = [];

		EchoLog("Osada key: ".$key);

		$nextday = false;

		if ($key != -1) {
			$key++;
			if (isset($osada[$c['id']][$key])) {
				$newosada = $osada[$c['id']][$key];
			} else {
				$newosada = $osada[$c['id']][0];
				$nextday = true;
			}
		} else {
			$newosada = $osada[$c['id']][0];
		}


		// выставляем новое время осады
		if ($nextday) {
			$next = mktime($newosada['start'],0,0,date("n"),date("j")+1);
			$end = mktime($newosada['start']+$newosada['duration'],0,0,date("n"),date("j")+1);
		} else {
			$next = mktime($newosada['start'],0,0);
			$end = mktime($newosada['start']+$newosada['duration'],0,0);
		}

		EchoLog("Osada next: ".$next);
		EchoLog("Osada end: ".$end);

		mysql_query('UPDATE castles_osada SET nextosada = '.$next.', osadaend = '.$end.' WHERE id = '.$c['id']) or MyDie(mysql_error());


		if ($c['score'] >= $osada_end) {
			mysql_query('UPDATE castles_osada SET score = 0 WHERE id = '.$c['id']);
		}
	}

	//системки
	$msg_h=date("H");
	$msg_i=date("i");

		if (($msg_h=="12") and ($msg_i=='00'))
		{
		//12:00
		$TEXT='<font color=black><b>Открыта общая <a href=https://oldbk.com/encicl/osadazamka.html target=_blank>осада замка</a>!</b> Прими участие в осаде, займи свое место в рейтинге и получи ценные призы! Осада закончится через <b>1 час 59 минут</b>.</font>';
		addch2levels($TEXT,8,21,0);
		}
		elseif (($msg_h=="13") and ($msg_i=='00'))
		{
		//13:00
		$TEXT='<font color=black><b>Открыта общая <a href=https://oldbk.com/encicl/osadazamka.html target=_blank>осада замка</a>!</b> Прими участие в осаде, займи свое место в рейтинге и получи ценные призы! Осада закончится через <b>59 минут</b>.</font>';
		addch2levels($TEXT,8,21,0);
		}
		elseif (($msg_h=="20") and ($msg_i=='00'))
		{
		//20:00
		$TEXT='<font color=black><b>Открыта общая <a href=https://oldbk.com/encicl/osadazamka.html target=_blank>осада замка</a>!</b> Прими участие в осаде, займи свое место в рейтинге и получи ценные призы! Осада закончится через <b>59 минут</b>.</font>';
		addch2levels($TEXT,8,21,0);
		}
		elseif (($msg_h=="20") and ($msg_i=='30'))
		{
		//20:30
		$TEXT='<font color=black><b>Открыта общая <a href=https://oldbk.com/encicl/osadazamka.html target=_blank>осада замка</a>!</b> Прими участие в осаде, займи свое место в рейтинге и получи ценные призы! Осада закончится через <b>29 минут</b>.</font>';
		addch2levels($TEXT,8,21,0);
		}


	lockDestroy("cron_castles_osada");
?>
