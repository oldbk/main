<?php
namespace components\Component\RabbitMQ;

use components\Helper\Json;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
class Queue extends AbstractPattern
{
	/**
	 * @param AMQPChannel $channel
	 */
	private function declareQueue($channel)
	{
		$conf = $this->conf['queue'];
		$channel->queue_declare($this->name, $conf['passive'], $conf['durable'], $conf['exclusive'],
			$conf['auto_delete'], $conf['nowait']);
	}
	public function emit($data = null)
	{
		$connection = $this->createConnection();
		$channel = $connection->channel();
		$this->declareQueue($channel);
		$msg = new AMQPMessage(Json::encode($data),
			['delivery_mode' => 2] # make message persistent
		);
		$channel->basic_publish($msg, '', $this->name);
		$channel->close();
		$connection->close();
	}
	public function receive(callable $callback)
	{
		$connection = $this->createConnection();
		$channel = $connection->channel();
		$this->declareQueue($channel);
		$consumer = $this->conf['consumer'];
		if ($consumer['no_ack'] === false) {
			$channel->basic_qos(null, 1, null);
		}

		$channel->basic_consume($this->name, '', $consumer['no_local'], $consumer['no_ack'], $consumer['exclusive'],
			$consumer['nowait'],
			function ($msg) use ($callback, $consumer) {
				$return = call_user_func($callback, $msg, $this->name);
				if($consumer['no_ack'] === false) {
					if($return) {
						$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
					} else {
						$msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
					}
				}
			});


		$now = new \DateTime();
		echo '['.$now->format('d/m/Y H:i:s')."] Queue '{$this->name}' initialized \n";
		while (count($channel->callbacks)) {
			$channel->wait();
		}
		$channel->close();
		$connection->close();
	}
}