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
 * @property int $id
 * @property int $user_id
 * @property int $sn_type
 * @property string $sn_id
 * @property int $updated_at
 * @property int $created_at
 * @property int $deleted_at
 * @property int $is_deleted
 */
class UserSn extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_sn';
	protected $primaryKey = 'id';
}