<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Stol
 * @package components\models
 *
 *
 * @property int $user_id
 * @property string $login
 * @property string $city
 * @property int $in_clan_tournament
 * @property int $location_special_id
 * @property int $location_special_id2
 * @property int $location_special_id3
 */
class UserLocation extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_location';
	protected $primaryKey = 'user_id';

	public $incrementing = false;
}