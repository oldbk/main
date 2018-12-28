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
 * @property int $date
 * @property int $status
 * @property int $itemproto
 * @property int $itemcount
 *
 */
class UserFortuneStats extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_fortune_stats';
	protected $primaryKey = false;
}