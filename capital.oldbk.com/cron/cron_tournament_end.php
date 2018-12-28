<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/init.php';

use components\Component\Db\CapitalDb as DB;
use \components\Helper\Exception\ExitTryException;
use \components\models\clanTournament\ClanTournament;
use \components\models\Battle;
use components\models\User;
use components\models\UserLocation;
use \components\models\clanTournament\ClanTournamentGroup;
use \components\models\clanTournament\ClanTournamentMapItems;
use \components\models\clanTournament\ClanTournamentUser;
use \components\models\Chat;
use \components\models\clanTournament\ClanTournamentUserItems;

if( !lockCreate("cron_tournament_end") ) {
    exit("Script already running.");
}

$datetime = (new DateTime());
try {
	$builder = ClanTournamentGroup::with(['tournament', 'users', 'users.user'])
		->where('is_end', '=', 0);

	$builder->where(function($query) use ($datetime) {
		$query
			->where('need_finish', '=', 1)
			->orWhereHas('tournament', function($query) use ($datetime) {
				/** @var ClanTournament $query */
				$query->where('is_end', '=', 0)
					->where('ended_at', '<=', $datetime->getTimestamp());
			});
	});

	$tournament_ids = [];
	/** @var ClanTournamentGroup[] $Groups */
	$Groups = $builder->get();
	foreach ($Groups as $Group) {
		$db = DB::connection();
		$db->beginTransaction();
		try {
			//очки за знаки силы
			$powers = ClanTournamentMapItems::where('tournament_id', '=', $Group->tournament_id)
				->where('group_id', '=', $Group->id)
				->where('owner_team_id', '>', 0)
				->where('item_type', '=', 'power')
				->groupBy('owner_team_id')
				->select(['owner_team_id', DB::raw('count(id) as count')])
				->get()->keyBy('owner_team_id')
				->toArray();
			$Group->team1_value += isset($powers[1]) ? ($powers[1]['count'] * 2) : 0;
			$Group->team2_value += isset($powers[2]) ? ($powers[2]['count'] * 2) : 0;

			//очки за убийства
			$died = ClanTournamentUser::where('tournament_id', '=', $Group->tournament_id)
				->where('group_id', '=', $Group->id)
				->where('is_died', '=', 1)
				->groupBy('team_id')
				->select(['team_id', DB::raw('count(id) as count')])
				->get()->keyBy('team_id')
				->toArray();
			if(!isset($died[2])) {$died[2] = ['count' => 0];}
			if(!isset($died[1])) {$died[1] = ['count' => 0];}

			$Group->team1_value += $died[2]['count'] * 1;
			$Group->team2_value += $died[1]['count'] * 1;

			if($Group->tournament->getNeedUserCount() == $died[1]['count'] || $Group->tournament->getNeedUserCount() == $died[2]['count']) {
				$freeFlagCount = ClanTournamentMapItems::where('tournament_id', '=', $Group->tournament_id)
					->where('group_id', '=', $Group->id)
					->where('item_type', '=', ClanTournamentMapItems::TYPE_FLAG)
					->where('is_removed', '=', 0)
					->count();
				if($Group->tournament->getNeedUserCount() == $died[1]['count']) {
					$Group->team2_value += $freeFlagCount * 2;
				} else {
					$Group->team1_value += $freeFlagCount * 2;
				}
			}

			$win_point = 0;
			$lose_point = 0;
			if($Group->team1_value > $Group->team2_value) {
				$Group->team1_value = (int)($Group->team1_value * 1.2);
				$Group->win = 1;
				$win_point = $Group->team1_value;
				$lose_point = $Group->team2_value;
			} elseif($Group->team2_value > $Group->team1_value) {
				$Group->team2_value = (int)($Group->team2_value * 1.2);
				$Group->win = 2;
				$lose_point = $Group->team1_value;
				$win_point = $Group->team2_value;
			}

			$Group->is_end = true;
			if(!$Group->save()) {
				throw new Exception();
			}

			$battle_ids = [];
			$winners_ids = [];
			$losers_ids = [];
			foreach ($Group->users as $TUser) {
				if($TUser->team_id == $Group->win) {
					$winners_ids[] = $TUser->user_id;
				} else {
					$losers_ids[] = $TUser->user_id;
				}

				if($TUser->user->battle && !in_array($TUser->user->battle, $battle_ids)) {
					$battle_ids[] = $TUser->user->battle;
				}
			}

			if($battle_ids) {
				$Battles = Battle::whereIn('id', $battle_ids)->get();
				foreach ($Battles as $Battle) {
					if($Battle->finishTotally() === false) {
						throw new Exception();
					}
				}
			}

			$win_message = sprintf('<font color="red">Внимание!</font> Ваша команда победила соперников и получает очки в кол-ве %d', $win_point).' (включая бонус за победу)';
			Chat::addToGroupChatSystem($win_message, $winners_ids, 0);
			$lose_message = sprintf('<font color="red">Внимание!</font> Ваша команда проиграла у соперников и получает очки в кол-ве %d', $lose_point);
			Chat::addToGroupChatSystem($lose_message, $losers_ids, 0);

			//remove users from tournament. Set room "Tournament enter"
			User::whereIn('id', array_merge($winners_ids, $losers_ids))
				->where('room', '=', 402)
				->update(['room' => 401]);

			UserLocation::whereIn('user_id', array_merge($winners_ids, $losers_ids))
				->update([
					'in_clan_tournament' => 0,
					'location_special_id' => 0,
					'location_special_id2' => 0,
					'location_special_id3' => 0
				]);

			$db->commit();
			if(!in_array($Group->tournament_id, $tournament_ids)) {
				$tournament_ids[] = $Group->tournament_id;
			}

		} catch (Exception $ex) {
			$db->rollBack();
			$app->logger->emergency($ex);
		}
	}

	ClanTournament::whereIn('id', $tournament_ids)
		->where('is_end', '=', 0)
		->update(['is_end' => 1]);

	ClanTournamentUserItems::whereIn('tournament_id', $tournament_ids)->delete();

} catch (ExitTryException $ex) {

} catch (Exception $ex) {
	$app->logger->emergency($ex);
}


lockDestroy('cron_tournament_end');