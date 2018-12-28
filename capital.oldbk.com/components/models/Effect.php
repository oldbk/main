<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;

use components\models\_base\BaseModal;

/**
 * Class Effect
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $type
 * @property string $name
 * @property int $time
 * @property int $sila
 * @property int $lovk
 * @property int $inta
 * @property int $vinos
 * @property int $intel
 * @property int $owner
 * @property int $lastup
 * @property int $idiluz
 * @property int $pal
 * @property string $add_info
 * @property int $battle
 * @property int $eff_bonus
 *
 */
class Effect extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'effects';
	protected static $table_name = 'effects';
	protected $primaryKey = 'id';

	protected static $type_ids = [];

	/**
	 * @param array $types
	 * @param $user_id
	 * @return int
	 */
	public static function deleteByTypeList(array $types, $user_id)
	{
		return static::where('owner', '=', $user_id)
			->whereIn('type', $types)
			->delete();
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	public static function isHave($user_id)
	{
		$count = static::whereIn('type', static::$type_ids)
			->whereRaw('owner = ? and time > ?', [$user_id, (new \DateTime())->getTimestamp()])
			->count();

		return $count > 0;
	}

	/**
	 * @param $user_id
	 * @return array
	 */
	public static function getEffects($user_id)
	{
		return static::whereRaw('owner = ? and time > ?', [$user_id, (new \DateTime())->getTimestamp()])
			->whereIn('type', static::$type_ids)
			->get()->toArray();
	}

	/**
	 * @param $user_id
	 * @return boolean
	 */
	public static function isHaveStatsEffects($user_id)
	{
		$count = static::whereRaw('owner = ? and time > ? and (sila > 0 or lovk > 0 or inta > 0 or vinos > 0 or intel > 0)', [$user_id, (new \DateTime())->getTimestamp()])
			->whereIn('type', static::$type_ids)
			->count();

		return $count > 0;
	}
}