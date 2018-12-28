<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/init.php';

if( !lockCreate("cron_quest_daily") ) {
    exit("Script already running.");
}

use \components\models\quest\UserQuest;

echo sprintf('Start Quest Daily Finish cron [%s]', date('d.m.Y H:i:s')).PHP_EOL;
$db = \components\Component\Db\CapitalDb::connection();
$db->beginTransaction();
try {
    $quest_date = new \DateTime();
	$quest_date->setTime(0,0);

    $quest_ids = array();

    $UserQuest = UserQuest::from('user_quest as uq')
		->join('quest_list as ql', 'ql.id', '=', 'uq.quest_id')
		->whereRaw('ql.is_deleted = 0 and ql.quest_type = "weekly"')
		->whereRaw('uq.is_finished = 0 and uq.is_end = 0 and uq.is_cancel = 0 and uq.created_at < ?', [$quest_date->getTimestamp()])
		->groupBy(['uq.quest_id'])
		->get()->toArray();
    foreach ($UserQuest as $item) {
        $quest_ids[] = $item['quest_id'];
    }

    echo sprintf('Incomplete quest count: %d', count($quest_ids)).PHP_EOL;
    if(!$quest_ids) {
        throw new \Exception();
    }

	echo 'End quests: '.implode(', ', $quest_ids).PHP_EOL;
	UserQuest::whereRaw('is_finished = 0 and is_end = 0 and is_cancel = 0 and created_at < ?', [$quest_date->getTimestamp()])
		->whereIn('quest_id', $quest_ids)
		->update(['is_end' => 1, 'ended_at' => time()]);

    $db->commit();
    echo 'Finish'.PHP_EOL;
} catch (\Exception $ex) {
    $db->rollBack();
    if($ex->getMessage() != '') {
        echo 'Error. See log file cron_quest_weekly.txt'.PHP_EOL;
        \components\Helper\FileHelper::writeException($ex, 'cron_quest_weekly');
    }
}

lockDestroy('cron_quest_daily');