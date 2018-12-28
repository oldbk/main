<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Chat
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $owner
 * @property string $ip
 * @property int $date
 *
 */
class Iplog extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'iplog';
	protected $primaryKey = 'id';
}