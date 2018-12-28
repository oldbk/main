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
 * @method $this asModel()
 *
 * @property int $Id
 * @property int $owner
 * @property int $stat
 * @property int $masters
 * @property string $dropstat
 * @property string $dropmast
 * @property int $klass
 * @property int $dropklass
 */
class UserZnahar extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_znahar';
	protected $primaryKey = 'Id';
}