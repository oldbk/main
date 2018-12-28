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
 * Class UserBabil
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $owner
 * @property int $magic
 * @property int $btype
 * @property int $dur
 * @property int $maxdur
 */
class UserBabil extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'users_babil';
	protected $primaryKey = ['owner', 'magic'];
	public $incrementing = false;
}