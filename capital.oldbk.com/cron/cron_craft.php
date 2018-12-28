<?php
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = microtime_float();

$fromcron = true;

require_once "/www/capitalcity.oldbk.com/cron/init.php";
require_once "/www/capitalcity.oldbk.com/craft_config.php";
require_once "/www/capitalcity.oldbk.com/craft_functions.php";



function EchoLog($txt) {
	echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
}

$q = mysql_query('START TRANSACTION');
$q = mysql_query('SELECT craft_job.*,users.ldate FROM craft_job LEFT JOIN users ON users.id = craft_job.owner WHERE craft_job.status = 1 and lastupdate > 0 and lastupdate < '.time().' and ldate >= '.(time()-120).' FOR UPDATE') or Redirect();
EchoLog("Found ".mysql_num_rows($q)." to process");
while($cs = mysql_fetch_assoc($q)) {
	// если больше 180 сек назад был последний апдейт, значит тело в офе было
	$forceupdate = false;
	if (time() - $cs['lastupdate'] > 180) {
		EchoLog("Lastupdate more that 180 seconds ".time().":".$cs['lastupdate']);
		$cs['lastupdate'] = time()-1;
		$forceupdate = true;
	}

	$difftime = time() - $cs['lastupdate']; // получили разницу по времени
	if ($difftime >= 60 || $cs['craftlefttime'] <= 60 || $forceupdate) {
		if ($cs['craftlefttime'] < $difftime) $difftime = $cs['craftlefttime'];
		EchoLog("Proccesing: ".$cs['owner'].":".$difftime);

		mysql_query('UPDATE craft_job SET craftlefttime = craftlefttime - '.$difftime.', lastupdate = '.time().' WHERE id = '.$cs['id']) or Redirect();
		$cs['craftlefttime'] -= $difftime;

		if ($cs['craftlefttime'] <= 0) {
			EchoLog(" craftlefttime = 0");
			// если получаем craftlefttime = 0 то проверяем
			$usr = mysql_query('SELECT * FROM users WHERE id = '.$cs['owner']) or Redirect();
			if (mysql_num_rows($usr)) {
				$usr = mysql_fetch_assoc($usr);
				$loc = $craftrooms[$cs['loc']];
				CraftCheckComplete($usr,$cs,$loc,'',true);
			} else {
				EchoLog("User not found: ".$cs['owner']);	
			}
		}
	}
}
$q = mysql_query('COMMIT');
EchoLog("End: ".(microtime_float() - $time_start));