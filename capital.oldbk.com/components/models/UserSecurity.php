<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\Component\Db\CapitalDb;
use components\Component\Slim\Slim;
use components\Helper\FileHelper;
use components\models\_base\BaseModal;
use \PragmaRX\Google2FA\Google2FA;

/**
 * Class Stol
 * @package components\models
 *
 *
 * @property int $user_id
 * @property string $password
 * @property string $second_password
 * @property int $status_2fa
 * @property string $secret_2fa
 * @property int $security_cooldown
 * @property int $transfer_cooldown
 * @property string $reset_token
 */
class UserSecurity extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_security';
	protected $primaryKey = 'user_id';
	public $incrementing = false;

	const STATUS_2FA_DISABLED 	= 0;
	const STATUS_2FA_PREPARE 	= 1;
	const STATUS_2FA_ENABLED 	= 2;

	private $_errors = [];

	public function errors()
	{
		$_errors = $this->_errors;
		$this->_errors = [];

		return $_errors;
	}

	protected function addError($error)
	{
		$this->_errors[] = $error;
		return $this;
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	public static function isTransferCooldown($user_id)
	{
		$count = static::whereRaw('user_id = ? and transfer_cooldown > ?', [$user_id, (new \DateTime())->getTimestamp()])
			->count();

		return $count > 0;
	}

	public function isEnabled2Fa()
	{
		return $this->status_2fa == self::STATUS_2FA_ENABLED;
	}

	public function isPrepared2Fa()
	{
		return $this->status_2fa == self::STATUS_2FA_PREPARE;
	}

	public function isDisabled2Fa()
	{
		return $this->status_2fa == self::STATUS_2FA_DISABLED;
	}

	/**
	 * @param Google2FA $google2fa
	 * @return bool
	 */
	public function prepare2Fa($google2fa)
	{
		if($this->security_cooldown > time()) {
			$this->addError('Кулдаун 3 дня');
			return false;
		}

		try {
			$this->secret_2fa = $google2fa->generateSecretKey();
			$this->status_2fa = self::STATUS_2FA_PREPARE;
			if(!$this->save()) {
				return false;
			}

			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>2FA включена</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			if(!$Lichka->save()) {
				return false;
			}

			return true;
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->emergency((string)$ex);
		}

		return false;
	}

	/**
	 * @param Google2FA $google2fa
	 * @param $code
	 * @return bool
	 */
	public function enable2Fa($google2fa, $code)
	{
		if($this->security_cooldown > time()) {
			$this->addError('Кулдаун 3 дня');
			return false;
		}

		if(!$google2fa->verifyKey($this->secret_2fa, $code)) {
			$this->addError('Цифры не верны');
			return false;
		}

		try {
			$this->status_2fa = self::STATUS_2FA_ENABLED;
			$this->security_cooldown = (new \DateTime())->modify('+3 days')->getTimestamp();
			if(!$this->save()) {
				return false;
			}

			User::where('id', '=', $this->user_id)
				->limit(1)
				->update(['second_password' => '']);

			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>2FA активирована</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			if(!$Lichka->save()) {
				return false;
			}

			return true;
		} catch (\Exception $ex) {

			Slim::getInstance()->logger->emergency((string)$ex);
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function disable2Fa()
	{
		if($this->security_cooldown > time()) {
			$this->addError('Кулдаун 3 дня');
			return false;
		}

		try {
			$this->status_2fa = self::STATUS_2FA_DISABLED;
			$this->security_cooldown = (new \DateTime())->modify('+3 days')->getTimestamp();
			if(!$this->save()) {
				return false;
			}

			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>2FA выключена</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			if(!$Lichka->save()) {
				return false;
			}

			return true;
		} catch (\Exception $ex) {

			Slim::getInstance()->logger->emergency((string)$ex);
		}

		return false;
	}

	/**
	 * @param $num
	 * @return string
	 */
	public function generateSecondPassword($num)
	{
		switch (intval($num)) {
			case 1:
				$len = 0;
				break;
			case 2:
				$len = 4;
				break;
			case 3:
				$len = 6;
				break;
			case 4:
				$len = 8;
				break;
			default:
				$len = 4;
				break;
		}
		$second_password = "";
		for ($i = 0; $i < $len; $i++) {
			$second_password .= rand(0, 9);
		}

		return $second_password;
	}

	public function enableSecondPassword($second_password)
	{
		if($this->security_cooldown > time()) {
			$this->addError('Кулдаун 3 дня');
			return false;
		}

		try {
			$this->security_cooldown = (new \DateTime())->modify('+3 days')->getTimestamp();
			if(!$this->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			User::where('id', '=', $this->user_id)
				->limit(1)
				->update(['second_password' => md5($second_password)]);

			//write to lichka
			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>Второй пароль установлен</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			if(!$Lichka->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			if($this->isEnabled2Fa() || $this->isPrepared2Fa()) {
				if(!$this->disable2Fa()) {
					$this->addError('Произошла ошибка попробуйте позже');
					return false;
				}
			}

			return true;
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->emergency((string)$ex);
		}

		return false;
	}

	public function disableSecondPassword()
	{
		if($this->security_cooldown > time()) {
			$this->addError('Кулдаун 3 дня');
			return false;
		}

		try {
			$this->security_cooldown = (new \DateTime())->modify('+3 days')->getTimestamp();
			if (!$this->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			User::where('id', '=', $this->user_id)
				->limit(1)
				->update(['second_password' => '']);

			//write to lichka
			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<<font color=green>Второй пароль снят</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			if(!$Lichka->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

		} catch (\Exception $ex) {
			Slim::getInstance()->logger->emergency((string)$ex);
		}

		return false;
	}

	/**
	 * @param $old_password
	 * @param $new_password
	 * @param $new_password2
	 * @return bool
	 * @throws \Exception
	 */
	public function changePassword($old_password, $new_password, $new_password2)
	{
		if($this->security_cooldown > time()) {
			$this->addError('Кулдаун 3 дня');
			return false;
		}

		if($new_password != $new_password2) {
			$this->addError('Новый пароль не совпадает');
			return false;
		}

		try {
			/** @var User $User */
			$User = User::find($this->user_id);
			if(!$User) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			if(!$User->validatePassword($old_password)) {
				$this->addError('Старый пароль не верен');
				return false;
			}

			//add cooldown
			$this->transfer_cooldown = (new \DateTime())->modify('+1 day')->getTimestamp();
			$this->security_cooldown = (new \DateTime())->modify('+3 days')->getTimestamp();
			if(!$this->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			//write to lichka
			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>Сменен пароль</font>. Ip с которого произведена смена: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			if(!$Lichka->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			//change user password
			$User->pass = User::generatePassword($User->login, $new_password, $User->salt);
			if(!$User->save()) {
				$this->addError('Произошла ошибка попробуйте позже');
				return false;
			}

			return true;
		} catch (\Exception $ex) {
			Slim::getInstance()->logger->emergency((string)$ex);
		}

		return false;
	}
}
