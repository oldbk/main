<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class UserZnaharSfree
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $owner
 * @property int $free
 * @property int $last_use
 * @property int $used
 * @property int $free_count
 */
class UserZnaharSfree extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_znahar_sfree';
	protected $primaryKey = 'id';
}