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
 * Class Chat
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property string $login
 * @property int $owner
 * @property string $passwd
 * @property string $active_key
 * @property string $date
 * @property string $ip
 * @property int $active
 * @property string $salt
 *
 */
class ConfirmpasswdNew extends BaseModal
{
    use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'confirmpasswd_new';
    protected $primaryKey = ['login', 'date'];
    public $incrementing = false;
}
