<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 16.05.17
 * Time: 17:53
 */

require_once __DIR__ . '/../../components/bootstrap_cli.php';

use \components\Helper\rating\AbstractRating;
use components\models\EventRating;
use components\models\UserEventRating;
use components\Component\Db\CapitalDb as DB;


\components\Component\RabbitMQ\Builder::setApp($app);
try {
	\components\Component\RabbitMQ\Builder::queue('event', 'event-rating-operation')
		->receive(function ($msg, $name)  use ($app) {
			$fileToExit = realpath((__DIR__).'/../tmp/event_rating_operation.exit');
			if(file_exists($fileToExit)) {
				echo sprintf('FILE: %s. EXIT', $fileToExit).PHP_EOL;
				@unlink($fileToExit);
				exit;
			}

			$data = \components\Helper\Json::decode($msg->body, true);
			if(!isset($data['operation']) || !isset($data['rating_id'])) {
				return true;
			}
			$data['event'] = $name;

			$db = DB::connection();
			$db->beginTransaction();
			try {
				/** @var EventRating $Rating */
				$Rating = EventRating::where('id', '=', $data['rating_id'])->first();
				if (!$Rating) {
					return true;
				}

				if (in_array($data['operation'], [AbstractRating::OPERATION_END, AbstractRating::OPERATION_END_START])) {
					$reward_till = (new \DateTime())->modify('+' . $Rating->reward_till_days . ' days')->setTime(0, 0);

					UserEventRating::where('iteration_num', '=', $Rating->iteration_num)
						->where('is_end', '=', 0)
						->where('rating_id', '=', $Rating->id)
						->update(['is_end' => 1, 'reward_till' => $reward_till->getTimestamp()]);

					$Rating->iteration_num += 1;
					$Rating->is_enabled = false;
				}

				if ($data['operation'] != AbstractRating::OPERATION_END) {
					$Rating->is_enabled = true;
				}

				if (!$Rating->save()) {
					$data['message'] = "Ќе удалось сохранить запись рейтинга";
					throw new Exception();
				}

				$db->commit();
			} catch (Exception $ex) {
				$db->rollBack();
				$app->logger->error($ex, $data);

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