#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";

if( !lockCreate("cron_deltele") ) {
    exit("Script already running.");
}

$q = mysql_query('DELETE FROM oldbk.telegraph WHERE deltime > 0 and deltime < '.time());

lockDestroy("cron_deltele");
?>
