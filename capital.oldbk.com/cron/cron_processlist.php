<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";

$exclude = array(
	"localhost",
	"93.125.108.207",
	"31.154.88.236",
	"195-138-84-12.broadband.tenet.odessa.ua",

);


function SaveLOG($ip,$data) {
	$fp = fopen('/www/other/baseip.log','a+b');
	if ($fp) {
		if (flock($fp, LOCK_EX)) {
			fwrite($fp,date("d/m/Y H:i:s").":".$ip.":".serialize($data)."\r\n");
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
}


$q = mysql_query('SHOW FULL PROCESSLIST');
while($i = mysql_fetch_assoc($q)) {
	$host = $i['Host'];
	$host = explode(":",$host);
	$host = $host[0];
	if (!in_array($host,$exclude) && strpos($host,".internal") === false) {
		SaveLog($host,$i);
	}
}

?>
