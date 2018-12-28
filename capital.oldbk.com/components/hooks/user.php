<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 07.04.17
 * Time: 13:54
 *
 * @var \components\Component\Slim\Slim $app
 */

$app->hook('user.enter', function($User) use ($app) {
	try {
		if(is_array($User)) {
			$User = new \components\models\User($User);
		}

		if($User->level == 0) {
			/** @var \components\models\UserAdvert $UserAdvert */
			$UserAdvert = \components\models\UserAdvert::whereRaw('user_id = ? and status = "hold"', [$User->id])->first(['id', 'cpa']);
			if($UserAdvert) {
				$_data = array(
					'status' 				=> 'approved',
					'updated_at' 			=> time(),
					'need_send_postback' 	=> 1
				);
				$UserAdvert->status = 'approved';
				$UserAdvert->updated_at = time();
				$UserAdvert->need_send_postback = 1;
				$UserAdvert->save();

				$client = new \Guzzle\Http\Client();
				$link = sprintf('http://postback.kadam.net/ru/postback/?data=%s&status=%s', $UserAdvert['cpa'], 'approved');
				$request = $client->get($link);
				$request->send();
			}
		}

		unset($User);
	} catch (Exception $ex) {
		$app->logger->error($ex, $User->getAttributes());
	}
});

$app->hook('user.enter', function($User) use ($app) {
	try {
		if(is_array($User)) {
			$User = new \components\models\User($User);
		}

		$Quest = $app->quest->setUser($User)->get();
		$Checker = new \components\Component\Quests\check\CheckerEvent();
		$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_GAME_ENTER;

		if(($Item = $Quest->isNeed($Checker)) !== false) {
			$Quest->taskUp($Item);
		}

		unset($User);
		unset($Checker);
	} catch (Exception $ex) {
		$app->logger->error($ex, $User->getAttributes());
	}
});

$app->hook('user.enter', function($User) use ($app) {
	try {
		\components\Component\RabbitMQ\Builder::setApp($app);
		if(is_array($User)) {
			$User = new \components\models\User($User);
		}


		$queue = \components\Component\RabbitMQ\Builder::queue('quest','logs-user');
		$queue->emit([
			"user_id" 		=> $User->id,
			"login" 		=> $User->login,
			'event' 		=> 'game_enter',
			'message'		=> null,
			'err_level'		=> \Monolog\Logger::INFO,
		]);
	} catch (Exception $ex) {
		\components\Helper\FileHelper::writeException($ex, 'hook_user');
	}
});

$app->hook('user.move', function($User) use ($app) {
	try {
		if(is_array($User)) {
			$User = new \components\models\User($User);
		}

		$Quest = $app->quest->setUser($User)->get();
		$Checker = new \components\Component\Quests\check\CheckerEvent();
		$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_LOCATION_ENTER;

		if(($Item = $Quest->isNeed($Checker)) !== false) {
			\components\Helper\FileHelper::writeArray([
				'action' => 'enter',
				'user' => $User->getId(),
				'room' => $User->room,
			], 'move');

			$Quest->taskUp($Item);
		}

		unset($User);
		unset($Checker);
	} catch (Exception $ex) {
		\components\Helper\FileHelper::writeException($ex, 'hook_user');
	}
});