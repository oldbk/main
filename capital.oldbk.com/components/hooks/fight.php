<?php

use \components\models\Battle;
use \components\models\User;
use components\Helper\RandomHelper;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 07.04.17
 * Time: 13:54
 *
 * @var \components\Component\Slim\Slim $app
 */

$app->hook('fight.user.finished', function($User, $Battle) use ($app) {

	/**
	 * @var \components\models\Battle $Battle
	 */
	if(is_array($User)) {
		$User = new User($User);
	}
	$rabbit_log = [];

	try {
		$rabbit_log = [
			'is_checked' 	=> false,
			'is_need' 		=> false,
			'fight_info'	=> [
				'is_win' 			=> ($Battle->win == $User->battle_t),
				'fight_type' 		=> $Battle->type,
				'fight_comment' 	=> $Battle->coment,
				'battle_id'			=> $Battle->id,
				'battle_type'		=> $Battle->getFightKey(),
				'damage'			=> $Battle->user_damage,
			],
			'quest'			=> [],
			'event'			=> 'fight_end',
			'message'		=> null,
			"user_id" 		=> $User->id,
			"login" 		=> $User->login,
			'err_level'		=> \Monolog\Logger::INFO,
		];

		$types = Battle::getTypes();
		if(!in_array($Battle->getFightKey(), $types)) {
			return;
		}

		$Quest = $app->quest->setUser($User)->get();
		$rabbit_log['quest'] = [
			'user_quest_ids' 	=> $Quest->getUserQuestIds(),
			'quest_ids' 		=> $Quest->getQuestIds(),
		];

		$Checker = new \components\Component\Quests\check\CheckerFight();
		$Checker->damage = $Battle->user_damage;
		$Checker->is_win = ($Battle->win == $User->battle_t);
		$Checker->fight_type = $Battle->type;
		$Checker->fight_comment = $Battle->coment;

		$Checker->battle = $Battle;
		$Items = $Quest->isNeed($Checker, true);
		if ($Items !== false) {
			$rabbit_log['is_need'] = true;
			$rabbit_log['is_checked'] = $Quest->taskUpMultiple($Items);
			unset($Items);
		}

		unset($Checker);
	} catch (Exception $ex) {
		$rabbit_log['err_level'] = \Monolog\Logger::ERROR;
		$rabbit_log['message'] = $ex->getMessage();
		$app->logger->error($ex, $rabbit_log);
	}

	if(empty($rabbit_log)) {
		return;
	}

	try {
		\components\Component\RabbitMQ\Builder::setApp($app);

		$queue = \components\Component\RabbitMQ\Builder::queue('quest','logs-quest');
		$queue->emit($rabbit_log);
	} catch (Exception $ex) {
		$app->logger->error($ex, $rabbit_log);
	}
});

//check for clan tournament
$app->hook('fight.user.finished', function($User, $Battle) use ($app) {
	/** @var \components\models\Battle $Battle */
	/** @var \components\models\User $User */
	if(is_array($User)) {
		$User = new User($User);
	}

	if($Battle->getFightKey() != Battle::FIGHT_CLAN_TOURNAMENT || $User->battle_t == $Battle->win) {
		return;
	}

	switch ($Battle->win) {
		case 1:
			$winnerIds = explode(';', $Battle->t1);
			break;
		case 2:
			$winnerIds = explode(';', $Battle->t2);
			break;
		default:
			return;
	}

	$rabbit_log = [
		'userId' 	=> $User->id,
		'winnerIds' => $winnerIds,
	];
	try {
		\components\Component\RabbitMQ\Builder::setApp($app);

		$queue = \components\Component\RabbitMQ\Builder::queue('tournament','tournament-fight');
		$queue->emit($rabbit_log);
	} catch (Exception $ex) {
		$app->logger->error($ex, $rabbit_log);
	}
});

$app->hook('fight.user.finished', function($User, $Battle) use ($app) {
	if(is_array($User)) {
		$User = new User($User);
	}

	$rabbit_log = [];

	try {
		$data = false;
		if (strlen($User->gruppovuha)) $data = unserialize($User->gruppovuha);

		if ($data !== false) {
			if (isset($data[10]) && $data[10] > 0) {
				$rabbit_log = [
					"user_id" 	=> $User->id,
					'err_level'	=> \Monolog\Logger::INFO,
				];
			}
		}
	} catch (Exception $ex) {
		$rabbit_log['err_level'] = \Monolog\Logger::ERROR;
		$rabbit_log['message'] = $ex->getMessage();
		$app->logger->error($ex, $rabbit_log);
	}

	if(empty($rabbit_log)) {
		return;
	}

	try {
		\components\Component\RabbitMQ\Builder::setApp($app);

		$queue = \components\Component\RabbitMQ\Builder::queue('mslots','mslots-logs');
		$queue->emit($rabbit_log);
	} catch (Exception $ex) {
		$app->logger->error($ex, $rabbit_log);
	}
});

//check for arena rating
$app->hook('fight.user.finished', function($User, $Battle) use ($app) {
	/** @var \components\models\Battle $Battle */
	if($Battle->getFightKey() != Battle::FIGHT_ARENA) {
		return;
	}

	$ArenaRating = new \components\Helper\rating\ArenaRating();
	$ArenaRating->value_add = (int)($Battle->user_damage / 100);

	$app->applyHook('event.rating', $User, $ArenaRating);
});