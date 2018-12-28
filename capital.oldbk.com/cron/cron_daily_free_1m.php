<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 26.09.2018
 * Time: 11:18
 */

require_once __DIR__ . '/init.php';
use components\Component\Db\CapitalDb as DB;
use \components\models\DailyFree;

if( !lockCreate("cron_daily_free_1m") ) {
	exit("Script already running.");
}

try {
	DB::table('daily_free')
		->where('essence', '=', DailyFree::ESSENCE_FONTAN)
		->whereRaw('uses < limit_uses') //меньше "порционного лимита"
		->where('added_at', '<=', (new \DateTime())->modify('-20 minute')->getTimestamp()) //добавлялось более 6мин назад
		->increment('uses', 1, ['added_at' => (new \DateTime())->getTimestamp()]); //добавляем 1 юз

} catch (Exception $ex) {
	$app->logger->addEmergency((string)$ex);
}

lockDestroy('cron_daily_free_1m');
