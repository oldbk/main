<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Delo
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $author
 * @property int $pers
 * @property string $text
 * @property string $text_ext
 * @property int $type
 * @property int $date
 * @property int $battle
 *
 */
class Delo extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'delo';
	protected $primaryKey = 'id';
}