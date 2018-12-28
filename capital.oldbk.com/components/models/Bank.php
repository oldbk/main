<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $pass
 * @property float $cr
 * @property float $ekr
 * @property int $owner
 * @property int $haos
 * @property int $def
 *
 */
class Bank extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'bank';
	protected $primaryKey = 'id';

	/**
	 * @param $user_id
	 * @param $id
	 * @param $password
	 * @return array|mixed
	 */
	public static function login($user_id, $id, $password)
	{
		$password = iconv('utf-8', 'windows-1251', $password);
		$bank = static::whereRaw('owner = ? and id = ? and pass = ?', [$user_id, $id, md5($password)])->first();
		if($bank) {
			return $bank->toArray();
		}

		return null;
	}

	/**
	 * @param $user_id
	 * @return array|mixed
	 */
	public static function findBank($user_id)
	{
		$bank = static::whereRaw('owner = ?', [$user_id])
			->orderBy('def', 'desc')
			->orderBy('id', 'asc')
			->first();
		if($bank) {
			return $bank->toArray();
		}

		return null;
	}
}