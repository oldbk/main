#!/usr/bin/php
<?php
die();
include "/www/capitalcity.oldbk.com/cron/init.php";
if( !lockCreate("cron_forum") ) {
    exit("Script already running.");
}
mysql_query("UPDATE oldbk.forum SET close_info='Архивариус,,2,9,0', closepal=83, `close`=1  WHERE type=2 AND parent<100 AND updated < DATE_ADD(NOW(), INTERVAL -120 DAY)");

lockDestroy("cron_forum");
?>