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
 * @property int $sn_type
 * @property string $sn_id
 * @property int $gender
 * @property int $birthday
 * @property string $email
 * @property int $created_at
 * @property string $sid
 */
class SnTemp extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'sn_temp';
	protected $primaryKey = 'id';
}