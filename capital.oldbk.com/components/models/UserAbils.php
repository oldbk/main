<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class UserAbils
 * @package components\models
 *
 *
 * @property int $owner
 * @property int $magic_id
 * @property int $allcount
 * @property int $findata
 * @property int $dailyc
 * @property int $daily
 */
class UserAbils extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'users_abils';
	protected $primaryKey = ['owner', 'magic_id'];
	public $incrementing = false;
}