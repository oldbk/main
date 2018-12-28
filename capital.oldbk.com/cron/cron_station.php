#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";


if( !lockCreate("cron_station") ) {
   exit("Script already running.");
}
echo "Running station ...\n";

$q = mysql_query('UPDATE station SET count = defcount');

echo "Finishing script. Destroy lock.\n";
lockDestroy("cron_station");

?>