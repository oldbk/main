<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class UserComplect
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $owner
 * @property string $name
 * @property string $data
 * @property int $id_city
 *
 */
class UserComplect extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_complect2';
	protected $primaryKey = 'id';
}