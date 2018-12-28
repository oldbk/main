<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 21.05.2018
 * Time: 23:15
 */

namespace components\Component\Email;


use components\Component\AbstractComponent;
use components\Component\Email\providers\iProvider;
use components\Helper\FileHelper;

class Notification extends AbstractComponent
{
	protected $_config;
	protected $_default = 'gmail';
	/** @var iProvider */
	protected $_client;

	public function __construct($config, $app)
	{
		$this->_config = $config;
		if(isset($this->_config['default']) && isset($this->_config['providers'][$this->_config['default']])) {
			$this->_default = $this->_config['default'];
		}

		$provider_config = $this->_config['providers'][$this->_default];
		$className = $provider_config['class'];
		unset($provider_config['class']);

		try {
			$this->_client = new $className($provider_config);
		} catch (\Exception $ex) {
			FileHelper::writeException($ex, 'notification', 'log');
		}

		parent::__construct($app);
	}

	public function run()
	{
		// TODO: Implement run() method.
	}

	public function changeFirstPassword($email)
	{
		if(!$email) {
			return;
		}

		try {
			$template = $this->app()->view()->renderPartial('common/email/password/first');
			$this->_client->sendMessage($email, 'Изменение в безопасности', $template);
		} catch (\Exception $ex) {
			FileHelper::writeException($ex, 'notification', 'log');
		}
	}

	public function changeSecondPassword($email)
	{
		if(!$email) {
			return;
		}

		try {
			$template = $this->app()->view()->renderPartial('common/email/password/second');
			$this->_client->sendMessage($email, 'Изменение в безопасности', $template);
		} catch (\Exception $ex) {
			FileHelper::writeException($ex, 'notification', 'log');
		}
	}

	public function change2FA($email)
	{
		if(!$email) {
			return;
		}

		try {
			$template = $this->app()->view()->renderPartial('common/email/password/2fa');
			$this->_client->sendMessage($email, 'Изменение в безопасности', $template);
		} catch (\Exception $ex) {
			FileHelper::writeException($ex, 'notification', 'log');
		}
	}
}