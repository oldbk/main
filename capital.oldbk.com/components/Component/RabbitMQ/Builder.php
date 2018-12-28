<?php
namespace components\Component\RabbitMQ;

use components\Component\Slim\Slim;

class Builder
{
	/*
	private static $defaults = [
		'rpc'      => [
			'queue'    => [
				'passive'     => false,
				'durable'     => true,
				'exclusive'   => false,
				'auto_delete' => true,
				'nowait'      => false,
			],
			'consumer' => [
				'no_local'  => false,
				'no_ack'    => false,
				'exclusive' => false,
				'nowait'    => false,
			],
		],
		'exchange' => [
			'exchange' => [
				'passive'     => false,
				'durable'     => true,
				'auto_delete' => false,
				'internal'    => false,
				'nowait'      => false,
			],
			'queue'    => [
				'passive'     => false,
				'durable'     => true,
				'exclusive'   => false,
				'auto_delete' => true,
				'nowait'      => false,
			],
			'consumer' => [
				'no_local'  => false,
				'no_ack'    => false,
				'exclusive' => false,
				'nowait'    => false,
			],
		],
		'queue'    => [
			'queue'    => [
				'passive'     => false,
				'durable'     => true,
				'exclusive'   => false,
				'auto_delete' => false,
				'nowait'      => false,
			],
			'consumer' => [
				'no_local'  => false,
				'no_ack'    => false,
				'exclusive' => false,
				'nowait'    => false,
			],
		],
	];
	*/

	/** @var Slim */
	private static $app;

	public static function setApp(Slim $app)
	{
		self::$app = $app;
	}

	private static function getConfig($confName)
	{
		$conf = self::$app->config('rabbitmq');
		if(!isset($conf['config'][$confName])) {
			throw new \Exception(sprintf('RabbitMQ-rpc config key: %s not found', $confName));
		}

		return [
			'config' => $conf['config'][$confName],
			'server' => $conf['server']
		];
	}

	public static function rpc($confName, $name)
	{
		$conf = self::getConfig($confName);

		$config = $conf['config']['rpc'];
		$config['server'] = $conf['server'];
		return new RPC($name, $config, self::$app->rabbitmq);
	}
	public static function exchange($confName, $name)
	{
		$conf = self::getConfig($confName);

		$config = $conf['config']['exchange'];
		$config['server'] = $conf['server'];
		return new Exchange($name, $config, self::$app->rabbitmq);
	}

	/**
	 * @param $confName |  Config ame
	 * @param $name  |  Queue name
	 * @return Queue
	 * @throws \Exception
	 */
	public static function queue($confName, $name)
	{
		$conf = self::getConfig($confName);

		$config = $conf['config']['queue'];
		$config['server'] = $conf['server'];
		return new Queue($name, $config, self::$app->rabbitmq);
	}
}