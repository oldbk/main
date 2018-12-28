<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 16.05.17
 * Time: 17:53
 */

require_once __DIR__ . '/../../components/bootstrap_cli.php';
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../../functions.php';
use \Monolog\Logger;

ini_set("mysqli.reconnect",1);

\components\Component\RabbitMQ\Builder::setApp($app);
try {
	\components\Component\RabbitMQ\Builder::queue('mslots', 'mslots-logs')
		->receive(function ($msg, $name) use ($app) {
			$fileToExit = realpath((__DIR__).'/../tmp/mslots_logs.exit');
			if(file_exists($fileToExit)) {
				echo sprintf('FILE: %s. EXIT', $fileToExit).PHP_EOL;
				@unlink($fileToExit);
				exit;
			}

			$data = \components\Helper\Json::decode($msg->body, true);

			if (!isset($data['user_id']) || $data['user_id'] == 0) return true;


			try {
				$data['event'] = 'mslots-logs';
				$app->logger->addRecord($data['err_level'] ? $data['err_level'] : Logger::INFO, $data['message'], $data);
			} catch (Exception $ex) {

			}

			// logic
			if (!mysql_ping()) {
				$_db = $app->config('db.capital');
				$q = mysql_connect($_db['host'], $_db['username'], $_db['password']);
				if ($q === false) {
					throw new Exception(mysql_error());
				}
				$q = mysql_select_db ("oldbk");
				if ($q === false) {
					throw new Exception(mysql_error());
				}
				$q = mysql_query("SET NAMES CP1251");
				if ($q === false) {
					throw new Exception(mysql_error());
				}
				$q = mysql_query("SET time_zone = '+3:00';");
				if ($q === false) {
					throw new Exception(mysql_error());
				}
			}

			$q = mysql_query('SELECT * FROM users_complect_scrolls WHERE owner = '.$data['user_id']);
			if ($q === false) {
				throw new Exception(mysql_error());
			}

			if (mysql_num_rows($q) > 0) {
				$compl = mysql_fetch_assoc($q);
				$q = mysql_query('SELECT * FROM users WHERE id = '.$data['user_id']);
				if ($q === false) {
					throw new Exception(mysql_error());
				}
				if (mysql_num_rows($q) > 0) {
					$telo = mysql_fetch_assoc($q);
					$res = dressscrollkomplekt($telo,$compl);
				}
			}

			return true;
		});

} catch (Exception $ex) {
	$app->logger->addEmergency((string)$ex);
}