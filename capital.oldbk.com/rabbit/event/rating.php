<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 16.05.17
 * Time: 17:53
 */

require_once __DIR__ . '/../../components/bootstrap_cli.php';
use \components\Component\Rating\Rating;
use \components\models\User;
use \components\Helper\Json;
use components\Component\Db\CapitalDb as DB;

\components\Component\RabbitMQ\Builder::setApp($app);
try {
	\components\Component\RabbitMQ\Builder::queue('event', 'event-rating')
		->receive(function ($msg, $name)  use ($app) {
			$fileToExit = realpath((__DIR__).'/../tmp/event_rating.exit');
			if(file_exists($fileToExit)) {
				echo sprintf('FILE: %s. EXIT', $fileToExit).PHP_EOL;
				@unlink($fileToExit);
				exit;
			}

			$data = Json::decode($msg->body, true);
			if(!isset($data['rating_key']) || !isset($data['user_id']) || !isset($data['value_add'])) {
				return true;
			}
			$data['event'] = $name;

			DB::beginTransaction();
			try {
				$User = User::find($data['user_id']);
				$Rating = (new Rating($User, $app))->setUser($User);

				$ratings = $Rating->getAvailableRatingByKey($data['rating_key']);

				$data['ratingIds'] = [];
				$data['check_done'] = null;
				if($ratings) {
					foreach ($ratings as $ratingId => $rating) {
						$data['ratingIds'][] = $ratingId;
					}

					$data['check_done'] = true;
					if($Rating->check($ratings, $data['value_add']) === false) {
						$data['check_done'] = false;
					}
				}

				DB::commit();
			} catch (Exception $ex) {
				DB::rollback();
				$app->logger->emergency($ex, $data);

				return false;
			}

			try {
				$app->logger->addInfo( isset($data['message']) ? $data['message'] : '', $data);
			} catch (Exception $ex) {

			}

			return true;
		});

} catch (Exception $ex) {
	$app->logger->emergency((string)$ex);
}