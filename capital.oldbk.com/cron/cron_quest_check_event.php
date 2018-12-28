<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/init.php';

if( !lockCreate("cron_quest_check") ) {
    exit("Script already running.");
}

echo sprintf('Start Quest Daily Finish cron [%s]', date('d.m.Y H:i:s')).PHP_EOL;

$WeightList = \components\models\quest\UserQuestCheck::getWeight();
foreach ($WeightList as $Weight) {
    try {
        $params = unserialize($Weight['params']);
        $event_id = $params['event_id'];

        $User = \components\models\User::find($Weight['user_id']);

        //$Quest = $app->quest->setUser($User)->get();
        $QuestObj = new \components\Component\Quests\Quest();
        $Quest = $QuestObj->setUser($User)->get();
        $Checker = new \components\Component\Quests\check\CheckerWeight();
        $Checker->event_id = $event_id;
        $Checker->count = $Weight['check_count'];
        if(($Item = $Quest->isNeed($Checker)) !== false) {
            $Quest->taskUp($Item);
        }

        $_data = [
			'finished_at' => time(),
		];
		\components\models\quest\UserQuestCheck::find($Weight['id'])->update($_data);

    } catch (Exception $ex) {
        echo 'Error. See log file cron_quest_check.txt'.PHP_EOL;
		$app->logger->addEmergency((string)$ex);
    }
}


$FortunaList = \components\models\quest\UserQuestCheck::getFortuna();
foreach ($FortunaList as $Fortuna) {
	try {
		$params = unserialize($Fortuna['params']);
		$User = \components\models\User::find($Fortuna['user_id']);

		//$Quest = $app->quest->setUser($User)->get();
		$QuestObj = new \components\Component\Quests\Quest();
		$Quest = $QuestObj->setUser($User)->get();

		$Checker = new \components\Component\Quests\check\CheckerEvent();
		$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_FORTUNA;
		if(($Item = $Quest->isNeed($Checker)) !== false) {
			$Quest->taskUp($Item);
		}
		unset($Checker);

		try {
			$FortuanRating = new \components\Helper\rating\FortunaRating();
			switch ($params['game']) {
				case 0:
					$FortuanRating->value_add = 1;
					break;
				case 1:
					$FortuanRating->value_add = 3;
					break;
				case 2:
					$FortuanRating->value_add = 5;
					break;
				case 3:
					$FortuanRating->value_add = 7;
					break;
				case 4:
					$FortuanRating->value_add = 10;
					break;
				case 5:
					$FortuanRating->value_add = 15;
					break;
			}

			$app->applyHook('event.rating', $user, $FortuanRating);
		} catch (Exception $ex) {
			$app->logger->addEmergency((string)$ex);
		}


		$_data = [
			'finished_at' => time(),
		];
		\components\models\quest\UserQuestCheck::find($Fortuna['id'])->update($_data);

	} catch (Exception $ex) {
		echo 'Error. See log file cron_quest_check.txt'.PHP_EOL;
		$app->logger->addEmergency((string)$ex);
	}
}

lockDestroy('cron_quest_check');