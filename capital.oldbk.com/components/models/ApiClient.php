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
class ApiClient extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'api_client';
	protected $primaryKey = 'id';

	private static $_salt = 'jPLycYjfqKloWbgvo8zT';

	public static function generateToken()
	{
		return md5(time().time().self::$_salt);
	}
}