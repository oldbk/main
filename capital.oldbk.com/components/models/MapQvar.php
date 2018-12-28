<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 *
 * @property int $owner
 * @property int $var
 * @property int $val
 *
 */
class MapQvar extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'map_qvar';
	protected $primaryKey = 'id';
}