<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\Component\Db\CapitalDb;
use components\Helper\FileHelper;
use components\models\_base\BaseModal;
use \PragmaRX\Google2FA\Google2FA;

/**
 * Class Chat
 * @package components\Model
 *
 *
 * @property int $user_id
 * @property string $secret
 * @property int $is_enabled
 * @property int $updated_at
 * @property int $created_at
 * @property int $deleted_at
 * @property int $status
 *
 */
class User2fa extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_2fa';
	protected $primaryKey = 'user_id';

	const STATUS_DISABLED 	= 0;
	const STATUS_PREPARE 	= 1;
	const STATUS_ENABLED 	= 2;

	public function isEnabled()
	{
		return $this->status == self::STATUS_ENABLED;
	}

	public function isPrepared()
	{
		return $this->status == self::STATUS_PREPARE;
	}

	public function isDisabled()
	{
		return $this->status == self::STATUS_DISABLED;
	}

	/**
	 * @param Google2FA $google2fa
	 */
	public function prepare($google2fa)
	{
		$db = CapitalDb::connection();
		$db->beginTransaction();
		try {
			$this->secret = $google2fa->generateSecretKey();
			$this->updated_at = time();
			$this->status = self::STATUS_PREPARE;
			$this->save();

			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>2FA включена</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			$Lichka->save();

			$db->commit();
		} catch (\Exception $ex) {
			$db->rollBack();

			FileHelper::writeException($ex, 'model_user2fa_prepare', 'log');
		}
	}

	public function enable()
	{
		$db = CapitalDb::connection();
		$db->beginTransaction();
		try {
			$this->updated_at = time();
			$this->status = self::STATUS_ENABLED;
			$this->save();

			User::where('id', '=', $this->user_id)
				->limit(1)
				->update(['second_password' => '']);

			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>2FA активирована</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			$Lichka->save();

			$db->commit();
		} catch (\Exception $ex) {
			$db->rollBack();

			FileHelper::writeException($ex, 'model_user2fa_enable', 'log');
		}
	}

	public function disable()
	{
		$db = CapitalDb::connection();
		$db->beginTransaction();
		try {
			$this->status = self::STATUS_DISABLED;
			$this->updated_at = time();
			$this->save();

			$Lichka = new Lichka();
			$Lichka->pers = $this->user_id;
			$Lichka->text = sprintf('<font color=green>2FA выключена</font>. Ip с которого произведена операция: %s', $_SERVER['REMOTE_ADDR']);
			$Lichka->date = time();
			$Lichka->save();

			$db->commit();
		} catch (\Exception $ex) {
			$db->rollBack();

			FileHelper::writeException($ex, 'model_user2fa_disable', 'log');
		}
	}
}