<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 07.04.17
 * Time: 13:54
 *
 * @var \components\Component\Slim\Slim $app
 */

$app->hook('event.rating', function($User, $EventRating) use ($app) {
	/** @var \components\Helper\rating\iRating $EventRating $EventRating */

	try {
		if(is_array($User)) {
			$User = new \components\models\User($User);
		}
		$rabbit_data = [
			'user_id' 		=> $User->id,
			'rating_key' 	=> $EventRating->getKey(),
			'value_add' 	=> $EventRating->getAddValue()
		];

		unset($User);
	} catch (Exception $ex) {
		$app->logger->err($ex);
		return;
	}

	try {
		\components\Component\RabbitMQ\Builder::setApp($app);

		$queue = \components\Component\RabbitMQ\Builder::queue('event','event-rating');
		$queue->emit($rabbit_data);
	} catch (Exception $ex) {
		$app->logger->err($ex, $rabbit_data);
	}
});


$app->hook('event.operation', function($EventRating) use ($app) {
	/** @var \components\Helper\rating\iRating $EventRating */

	try {
		$rabbit_data = [
			'rating_id' => $EventRating->getRatingId(),
			'operation' => $EventRating->getOperation()
		];
	} catch (Exception $ex) {
		$app->logger->err($ex);
		return;
	}

	try {
		\components\Component\RabbitMQ\Builder::setApp($app);

		$queue = \components\Component\RabbitMQ\Builder::queue('event','event-rating-operation');
		$queue->emit($rabbit_data);
	} catch (Exception $ex) {
		$app->logger->err($ex, $rabbit_data);
	}
});