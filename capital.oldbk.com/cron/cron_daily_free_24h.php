<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 26.09.2018
 * Time: 11:18
 */

require_once __DIR__ . '/init.php';

if( !lockCreate("cron_daily_free_24h") ) {
	exit("Script already running.");
}

lockDestroy('cron_daily_free_24h');