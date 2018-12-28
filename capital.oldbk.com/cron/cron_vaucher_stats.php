<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
if( !lockCreate("cron_vauch_job") ) {
    exit("Script already running.");
}
echo date("d.m.y H:i:s").'\r\n';

$fpath = '/www/capitalcity.oldbk.com/ecobalans/vauchbalday';

$q = mysql_query('
	SELECT prototype-100000 as nom,count(*) as cc FROM inventory LEFT JOIN users ON users.id = inventory.owner WHERE prototype IN (100005,100015,100020,100025,100040,100100,100200,100300) 
	and klan != "radminion" and klan != "Adminion" and (users.bot = 0 or users.id=488  ) and users.id != 8325 GROUP BY prototype
');

$all = 0;

while($v = mysql_fetch_assoc($q)) {
	$all += $v['nom']*$v['cc'];
}


$fp = fopen ($fpath,"a");
if(flock ($fp,LOCK_EX)) {
	fputs($fp , date("d/m/Y").":".$all."\n");
	flock ($fp,LOCK_UN);
}

fclose ($fp);


lockDestroy("cron_vauch_job");
?>