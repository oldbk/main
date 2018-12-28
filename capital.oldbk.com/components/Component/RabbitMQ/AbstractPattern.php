<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 17.05.17
 * Time: 17:12
 */

namespace components\Component\RabbitMQ;


use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class AbstractPattern
{
	/** @var AMQPStreamConnection */
	private $connection;
	protected $conf;
	protected $name;

	public function __construct($name, $conf, $connection)
	{
		$this->name = $name;
		$this->conf = $conf;
		$this->connection = $connection;
	}
	/**
	 * @return AMQPStreamConnection
	 */
	protected function createConnection()
	{
		if($this->connection instanceof AMQPStreamConnection && $this->connection->isConnected()) {
			return $this->connection;
		}

		$server = $this->conf['server'];
		$this->connection = new AMQPStreamConnection(
			$server['host'],
			$server['port'],
			$server['user'],
			$server['password'],
			$server['vhost'],
			false,
			'AMQPLAIN',
			null,
			'en_US',
			30.0,
			15.0
		);

		return $this->connection;
	}
}