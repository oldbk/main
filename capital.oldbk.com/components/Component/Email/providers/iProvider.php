<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 21.05.2018
 * Time: 23:18
 */

namespace components\Component\Email\providers;


interface iProvider
{
	/**
	 * iProvider constructor.
	 * @param array $config
	 */
	public function __construct($config);

	/**
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @return mixed
	 */
	public function sendMessage($to, $subject, $message);
}