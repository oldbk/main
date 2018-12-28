<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 16.05.17
 * Time: 17:53
 */

require_once __DIR__ . '/../../components/bootstrap_cli.php';

\components\Component\RabbitMQ\Builder::setApp($app);
try {
	\components\Component\RabbitMQ\Builder::queue('quest', 'logs-quest')
		->receive(function ($msg, $name)  use ($app) {
			$fileToExit = realpath((__DIR__).'/../tmp/quest_logs.exit');
			if(file_exists($fileToExit)) {
				echo sprintf('FILE: %s. EXIT', $fileToExit).PHP_EOL;
				@unlink($fileToExit);
				exit;
			}

			$data = \components\Helper\Json::decode($msg->body, true);

			try {
				$data['event'] = 'logs-quest';
				$app->logger->addInfo( isset($data['message']) ? $data['message'] : '', $data);
			} catch (Exception $ex) {

			}

			return true;
		});

} catch (Exception $ex) {
	$app->logger->addEmergency((string)$ex);
}