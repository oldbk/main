<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Inventory
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $owner
 * @property int $sila
 * @property int $sila_count
 * @property int $lovk
 * @property int $lovk_count
 * @property int $inta
 * @property int $inta_count
 * @property int $intel
 * @property int $intel_count
 * @property int $mudra
 * @property int $mudra_count
 * @property int $maxhp
 * @property int $maxhp_count
 * @property float $expbonus
 * @property int $expbonus_count
 * @property int $refresh
 * @property int $battle
 * @property int $usec
 */
class UserBonus extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_bonus';
	protected $primaryKey = 'id';

	/**
	 * @param $user_id
	 * @return bool
	 */
	public static function isHave($user_id)
	{
		$count = static::whereRaw('owner = ?', [$user_id])->count();

		return $count > 0;
	}
}