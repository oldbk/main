<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/init.php';

if( !lockCreate("cron_znahar_daily") ) {
    exit("Script already running.");
}

echo sprintf('Start Znahar Daily Finish cron [%s]', date('d.m.Y H:i:s')).PHP_EOL;

try {
	\components\Component\Db\CapitalDb::table(\components\models\UserZnahar::tableName())->update(['klass' => 1]);
    echo 'Finish'.PHP_EOL;
} catch (\Exception $ex) {
	echo 'Error. See log file cron_znahar_daily.txt'.PHP_EOL;
	\components\Helper\FileHelper::writeException($ex, 'cron_znahar_daily');
}

lockDestroy('cron_znahar_daily');