<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class UserAbils
 * @package components\models
 *
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $public_key
 * @property string $secret_key
 */
class SnSettings extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'sn_settings';
	protected $primaryKey = 'id';
}