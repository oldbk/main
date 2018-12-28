<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 16.05.17
 * Time: 17:53
 */

require_once __DIR__ . '/../../components/bootstrap_cli.php';

use \components\models\User;
use \components\models\UserLocation;
use \components\models\Battle;
use \components\models\clanTournament\ClanTournament;
use \components\models\clanTournament\ClanTournamentGroup;
use \components\models\clanTournament\ClanTournamentUser;
use \components\models\clanTournament\ClanTournamentMapItems;
use \components\models\clanTournament\ClanTournamentUserItems;
use \components\Helper\Exception\ExitTryException;
use components\Component\Db\CapitalDb as DB;


\components\Component\RabbitMQ\Builder::setApp($app);
try {
	\components\Component\RabbitMQ\Builder::queue('tournament', 'tournament-fight')
		->receive(function ($msg, $name)  use ($app) {
			$fileToExit = realpath((__DIR__).'/../tmp/tournament_fight.exit');
			if(file_exists($fileToExit)) {
				echo sprintf('FILE: %s. EXIT', $fileToExit).PHP_EOL;
				@unlink($fileToExit);
				exit;
			}

			$data = \components\Helper\Json::decode($msg->body, true);
			if(!isset($data['userId']) || !isset($data['winnerIds'])) {
				$app->logger->emergency('Fail message in tournament-fight', $data);
				return true;
			}
			$data['event'] = 'tournament-fight';

			$db = DB::connection();
			$db->beginTransaction();
			try {
				$UserLocation = UserLocation::where('user_id', '=', $data['userId'])
					->where('in_clan_tournament', '>', 0)
					->first();
				if(!$UserLocation) {
					throw new Exception("User not in the tournament");
				}
				$User = User::where('id', '=', $UserLocation->user_id)->first();

				$tournament_id 	= $UserLocation->location_special_id;
				$group_id 		= $UserLocation->location_special_id2;
				$team_id 		= $UserLocation->location_special_id3;

				/** @var ClanTournamentUser $TUser */
				$TUser = ClanTournamentUser::with(['tookItems', 'tookItems.mapItem'])->where('user_id', '=', $data['userId'])
					->where('tournament_id', '=', $tournament_id)
					->where('group_id', '=', $group_id)
					->first();
				if(!$TUser) {
					throw new Exception("Can't find TUser");
				}

				$User->room = 401;
				$TUser->is_died = true;

				if(!$TUser->save() || !$User->save()) {
					throw new Exception("Can't save TUser and User");
				}

				$UserLocation->in_clan_tournament 	= 0;
				$UserLocation->location_special_id 	= 0;
				$UserLocation->location_special_id2 = 0;
				$UserLocation->location_special_id3 = 0;
				$UserLocation->save();

				sendMessage($tournament_id, $group_id, $User);
				foreach ($TUser->tookItems as $TookItem) {
					if($TookItem->mapItem->item_type != ClanTournamentMapItems::TYPE_FLAG) {
						continue;
					}
					$Flag = $TookItem->mapItem;

					/** @var ClanTournamentUser $TUserNewOwner */
					$TUserNewOwner = ClanTournamentUser::whereIn('user_id', $data['winnerIds'])
						->where('is_died', '=', 0)
						->where('tournament_id', '=', $tournament_id)
						->where('group_id', '=', $group_id)
						->first();
					if(!$TUserNewOwner) {
						throw new Exception("Can't find flag new owner");
					}
					ClanTournamentUserItems::where('map_item_id', '=', $Flag->id)->delete();

					$Flag->owner_team_id = ($TUser->team_id == 1) ? 2 : 1;
					$Flag->user_id = $TUserNewOwner->id;
					if(!$Flag->save()) {
						throw new Exception("Can't move flag");
					}

					$UserItem = new ClanTournamentUserItems();
					$UserItem->tournament_user_id = $TUserNewOwner->id;
					$UserItem->map_item_id = $Flag->id;
					$UserItem->tournament_id = $TUser->tournament_id;
					if(!$UserItem->save()) {
						throw new Exception("Can't save user item");
					}
					break;
				}

				$alive = ClanTournamentUser::where('tournament_id', '=', $tournament_id)
					->where('group_id', '=', $group_id)
					->where('team_id', '=', $team_id)
					->where('is_died', '=', 0)
					->count();
				if(!$alive) {
					ClanTournamentGroup::where('id', '=', $group_id)->update(['need_finish' => 1]);

					$_user_ids = [];
					$GUsers = ClanTournamentUser::where('is_died', '=', 0)
						->where('tournament_id', '=', $TUser->tournament_id)
						->where('group_id', '=', $TUser->group_id)->get();
					foreach ($GUsers as $GUser) {
						$_user_ids[] = $GUser->user_id;
					}
					if($_user_ids) {
						User::whereIn('id', $_user_ids)
							->where('room', '=', 402)
							->update(['room' => 401]);

						UserLocation::whereIn('user_id', $_user_ids)
							->where('in_clan_tournament', '>', 0)
							->update([
								'in_clan_tournament' => 0,
								'location_special_id' => 0,
								'location_special_id2' => 0,
								'location_special_id3' => 0
							]);
					}
				}

				$db->commit();
			} catch (Exception $ex) {
				$db->rollBack();
				$app->logger->emergency( $ex, $data);

				return false;
			}

			try {
				$app->logger->addInfo( isset($data['message']) ? $data['message'] : '', $data);
			} catch (Exception $ex) {

			}

			return true;
		});

} catch (Exception $ex) {
	$app->logger->addEmergency((string)$ex);
}

function sendMessage($tournament_id, $group_id, User $User)
{
	$message = sprintf('<font color="red">Внимание!</font> Персонаж %s был повержен и выбыл из турнира', $User->htmlLogin());

	$GroupUsers = ClanTournamentUser::where('tournament_id', '=', $tournament_id)
		->where('group_id', '=', $group_id)
		->where('is_died', '=', 0)
		->where('user_id', '!=', $User->id)
		->get()->toArray();
	$chat_user_ids = [];
	foreach ($GroupUsers as $GUser) {
		$chat_user_ids[] = $GUser['user_id'];
	}

	\components\models\Chat::addToGroupChatSystem($message, $chat_user_ids, 0);
}