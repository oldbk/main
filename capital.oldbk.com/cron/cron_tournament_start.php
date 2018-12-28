<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/init.php';

use components\Component\Db\CapitalDb as DB;
use \components\Helper\Exception\ExitTryException;
use \components\models\clanTournament\ClanTournamentRequest;
use components\models\clanTournament\ClanTournamentRequestUser;
use \components\models\Chat;
use components\models\clanTournament\ClanTournament;
use components\models\clanTournament\ClanTournamentGroup;
use components\Helper\map\HeroMapGenerator;
use components\models\clanTournament\ClanTournamentMapItems;
use components\models\clanTournament\ClanTournamentSmoke;
use components\models\clanTournament\ClanTournamentUser;
use components\Helper\map\items\iMapItem;
use components\Helper\map\items\MapUser;
use components\Helper\map\items\MapBase;

if( !lockCreate("cron_tournament_start") ) {
	exit("Script already running.");
}

$datetime = (new DateTime());
try {
	$Requests = ClanTournamentRequest::where('is_end', '=', 0)
		->where('started_at', '<=', $datetime->getTimestamp())
		->get();

	foreach ($Requests as $Request) {

		$db = DB::connection();
		$db->beginTransaction();
		try {
			//region get user's clan for tournament
			/** @var array $RequestUsers */
			$RequestUsers = ClanTournamentRequestUser::with('user')->whereHas('user', function($query) {
				/** @var ClanTournamentMapItems $query */
				$query->where('room', '=', 401);
			})->select(['clan', DB::raw('count(*) as cnt')])
				->where('request_id', '=', $Request->id)
				->where('is_removed', '=', 0)
				->groupBy(['clan'])
				->having('cnt', '>=', $Request->getNeedUserCount())
				->get(['clan'])->keyBy('clan')->toArray();
			$clanOk = array_keys($RequestUsers);
			//endregion

			$user_temp = ClanTournamentRequestUser::whereIn('clan', $clanOk)
				->where('request_id', '=', $Request->id)
				->get()->toArray();
			$userByClans = prepareTeams($user_temp, $Request->getNeedUserCount());

			//region prepare groups
			$user_ids_in_tournament = [];
			$groupList = [];
			$i = 0;
			while($userByClans) {
				shuffle($userByClans);

				$_team1 = [
					'users' => $userByClans[0]['teams'][0],
					'clan' => $userByClans[0]['clan']
				];
				if(count($userByClans[0]['teams']) > 1) {
					unset($userByClans[0]['teams'][0]);
					$userByClans[0]['teams'] = array_values($userByClans[0]['teams']);
				} else {
					unset($userByClans[0]);
				}

				if(!isset($userByClans[1])) {
					break;
				}

				$_team2 = [
					'users' => $userByClans[1]['teams'][0],
					'clan' => $userByClans[1]['clan']
				];
				if(count($userByClans[1]['teams']) > 1) {
					unset($userByClans[1]['teams'][0]);
					$userByClans[1] = array_values($userByClans[1]['teams']);
				} else {
					unset($userByClans[1]);
				}

				$user_ids_in_tournament = array_merge($user_ids_in_tournament, $_team1['users'], $_team2['users']);

				$groupList[$i][1] = $_team1;
				$groupList[$i][2] = $_team2;
				$i++;
			}
			//endregion

			//region remove all other users from tournament
			/** @var ClanTournamentRequestUser[] $RequestUsers */
			$UsersToExit = ClanTournamentRequestUser::with('user')
				->where('request_id', '=', $Request->id)
				->whereNotIn('user_id', $user_ids_in_tournament)
				->get();
			foreach ($UsersToExit as $RUser) {
				$message = sprintf('<font color="red">Внимание!</font> Не удалось набрать команды для участия в турнире');
				Chat::addToChatSystem($message, $RUser->user);
			}
			unset($UsersToExit);
			ClanTournamentRequestUser::where('request_id', '=', $Request->id)
				->whereNotIn('user_id', $user_ids_in_tournament)
				->update(['is_removed' => 1]);
			//endregion

			if(!$user_ids_in_tournament) {
				$Request->is_end = true;
				if(!$Request->save()) {
					throw new Exception();
				}

				$db->commit();
				continue;
			}

			$Tournament 			= new ClanTournament();
			$Tournament->t_type 	= $Request->t_type;
			$Tournament->height 	= 8;
			$Tournament->width 		= 18;
			$Tournament->team_count = 2;
			$Tournament->ended_at	= (new DateTime())->modify('+30 minutes')->getTimestamp();
			if(!$Tournament->save()) {
				throw new \Exception();
			}

			//region mark request as started
			$Request->is_end = true;
			$Request->tournament_id = $Tournament->id;
			if(!$Request->save()) {
				$app->logger->emergency("Can't start tournament", [
					'request_id' => $Request->id,
				]);
				throw new ExitTryException;
			}
			//endregion

			$user_ids = [];
			foreach ($groupList as $groupTeam) {
				$TournamentGroup 				= new ClanTournamentGroup();
				$TournamentGroup->tournament_id = $Tournament->id;
				$TournamentGroup->team1_clan 	= $groupTeam[1]['clan'];
				$TournamentGroup->team2_clan 	= $groupTeam[2]['clan'];
				if(!$TournamentGroup->save()) {
					throw new \Exception();
				}

				$teams = [
					1 => $groupTeam[1]['users'],
					2 => $groupTeam[2]['users'],
				];
				$user_ids = array_merge($user_ids, $teams[1], $teams[2]);

				$chat_user_ids = [];
				foreach ($teams as $_t_id => $group_users) {
					foreach ($group_users as $_u_id) {
						\components\models\UserLocation::updateOrCreate(['user_id' => $_u_id], [
							'city' 					=> 'capitalcity',
							'in_clan_tournament' 	=> 1,
							'location_special_id' 	=> $TournamentGroup->tournament_id,
							'location_special_id2' 	=> $TournamentGroup->id,
							'location_special_id3' 	=> $_t_id,
						]);

						$chat_user_ids[] = $_u_id;
					}
				}

				$message = sprintf('<font color="red">Внимание!</font> Турнир начался');
				Chat::addToGroupChatSystem($message, $chat_user_ids, 0);

				$Generator = HeroMapGenerator::generate($Tournament->width, $Tournament->height, $teams, $Tournament->t_type);
				$map = $Generator->getMap();

				$smokeOpened = [];
				$mapItemArray = [];
				$groupUsersArray = [];
				foreach ($map as $y => $_t) {
					foreach ($_t as $x => $info) {
						/** @var iMapItem|MapUser|MapBase $item */
						foreach ($Generator->getItems($y, $x) as $item) {
							if($item->getType() == ClanTournamentMapItems::TYPE_USER) {
								$groupUsersArray[] = [
									'tournament_id' => $Tournament->id,
									'group_id' 		=> $TournamentGroup->id,
									'user_id' 		=> $item->getUserId(),
									'team_id' 		=> $item->getTeamId(),
									'location_y' 	=> $y,
									'location_x' 	=> $x,
									'can_moved_at' 	=> (new \DateTime())->modify('+30 sec')->getTimestamp(),
								];

								//region open map for near tiles to user
								$keyField = sprintf('%d_%d_%d', $y, $x, $item->getTeamId());
								if (!array_key_exists($keyField, $smokeOpened)) {
									$smokeOpened[$keyField] = true;
								}
								$around = $Generator->getAround($y, $x);
								foreach ($around as $yx) {
									$key = sprintf('%d_%d_%d', $yx['y'], $yx['x'], $item->getTeamId());
									if (!array_key_exists($key, $smokeOpened)) {
										$smokeOpened[$key] = true;
									}
								}
								//endregion

								continue;
							}

							$mapItem = [
								'tournament_id' => $Tournament->id,
								'group_id' 		=> $TournamentGroup->id,
								'item_type' 	=> $item->getType(),
								'location_y' 	=> $y,
								'location_x' 	=> $x,
								'owner_team_id' => ($item->getType() == ClanTournamentMapItems::TYPE_BASE) ? $item->getTeamId() : 0,
							];
							$mapItemArray[] = $mapItem;
						}
					}
				}

				$smokeArray = [];
				for ($height = 1; $height <= $Generator->getHeight(); $height++) {
					for ($width = 1; $width <= $Generator->getWidth(); $width++) {
						for ($team_num = 1; $team_num <= $Tournament->team_count; $team_num++) {
							$is_removed = 0;
							$keyField = sprintf('%d_%d_%d', $height, $width, $team_num);
							if(isset($smokeOpened[$keyField]) && $smokeOpened[$keyField] === true) {
								$is_removed = 1;
							}

							$smokeArray[] = [
								'tournament_id' => $Tournament->id,
								'group_id' 		=> $TournamentGroup->id,
								'team_id' 		=> $team_num,
								'location_y' 	=> $height,
								'location_x' 	=> $width,
								'is_removed' 	=> $is_removed,
							];
						}
					}
				}

				//insert smoke area
				if(empty($smokeArray) || !ClanTournamentSmoke::insert($smokeArray)) {
					throw new \Exception();
				}
				//insert map items
				if(empty($mapItemArray) || !ClanTournamentMapItems::insert($mapItemArray)) {
					throw new \Exception();
				}
				//insert users
				if(empty($groupUsersArray) || !ClanTournamentUser::insert($groupUsersArray)) {
					throw new \Exception();
				}
			}

			\components\models\User::whereIn('id', $user_ids)->update(['room' => 402]);

			$db->commit();
		} catch (Exception $ex) {
			$db->rollback();

			var_dump($ex->getMessage());
		}
	}

} catch (ExitTryException $ex) {

} catch (Exception $ex) {
	$app->logger->emergency($ex);
}

/**
 * @param $users
 * @param $group_count
 * @return array
 */
function prepareTeams($users, $group_count)
{
	$usersByClan = [];
	foreach ($users as $_u) {
		$usersByClan[$_u['clan']][$_u['user_id']] = $_u;
	}

	$groupsByClan = [];
	foreach (array_keys($usersByClan) as $_clan) {
		$_users = $usersByClan[$_clan];
		$diff = (count($_users) % $group_count);
		if($diff != 0) { //если кол-во игроков не кратно, удаляем лишних
			for ($_i = 0; $_i < $diff; $_i++) {
				shuffle($_users);
				array_shift($_users);
			}
		}

		$_team = 0;
		foreach ($_users as $_uId => $_u) {
			if(!isset($groupsByClan[$_clan])) {$groupsByClan[$_clan] = ['clan' => $_clan, 'teams' => []];}
			if(!isset($groupsByClan[$_clan]['teams'][$_team])) {$groupsByClan[$_clan]['teams'][$_team] = [];}

			if(count($groupsByClan[$_clan]['teams'][$_team]) == $group_count) {
				$_team++;
			}

			$groupsByClan[$_clan]['clan'] = $_clan;
			$groupsByClan[$_clan]['teams'][$_team][] = $_u['user_id'];
		}
	}

	return $groupsByClan;
}


lockDestroy('cron_tournament_start');