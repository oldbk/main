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
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $pers
 * @property string $text
 * @property int $date
 *
 */
class Lichka extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'lichka';
	protected $primaryKey = 'id';
}