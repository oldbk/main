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
 * @property int $owner
 * @property string $date
 * @property string $text
 * @property int $addinfo
 * @property int $deltime
 *
 */
class Telegraph extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'telegraph';
	protected $primaryKey = 'id';

	/**
	 * @param $user_id
	 * @param $text
	 * @return int
	 */
	public static function add($user_id, $text)
	{
		$_data = array(
			'owner' => $user_id,
			'text' => sprintf('[%s] %s ', date('d.m.Y H:i'), $text)
		);

		return static::insertGetId($_data);
	}
}