<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/init.php';

use components\models\clanTournament\ClanTournamentRequest;
use components\models\clanTournament\ClanTournament;
use components\Component\Db\CapitalDb as DB;
use \components\Helper\Exception\ExitTryException;


if( !lockCreate("cron_tournament_new") ) {
	exit("Script already running.");
}


try {
	$current_hour = (int)date('G');
	$tournament_hours = [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22];
	if(!in_array($current_hour, $tournament_hours)) {
		//throw new ExitTryException;
	}

	$tournaments = [ClanTournament::TYPE_1x1, ClanTournament::TYPE_3x3, ClanTournament::TYPE_5x5];

	$db = DB::connection();
	$db->beginTransaction();
	try {
		$time = (new DateTime())->setTime($current_hour, 0, 0);

		foreach ($tournaments as $tournament_type) {
			$isCreated = ClanTournamentRequest::where('t_type', '=', $tournament_type)
				->where('created_at', '=', $time->getTimestamp())
				->count();
			if($isCreated == 0) {
				$Request = new ClanTournamentRequest();
				$Request->t_type = $tournament_type;
				$Request->liga_type = 1;
				$Request->comment = 'Турнир '.$tournament_type;
				$Request->created_at = $time->getTimestamp();
				$Request->started_at = (new DateTime())->modify('+30 minutes')->getTimestamp();
				if(!$Request->save()) {
					$app->logger->emergency(sprintf("Can't create tournament request. Type: %s", $tournament_type));
					throw new Exception();
				}
			}
		}

		$db->commit();
	} catch (Exception $ex) {
		$db->rollBack();
	}
} catch (ExitTryException $ex) {

} catch (Exception $ex) {
	$app->logger->emergency($ex);
}


lockDestroy('cron_tournament_new');