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
 * @property int $status
 * @property int $nextosada
 * @property int $osadaend
 * @property int $score
 *
 */
class CastlesOsada extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'castles_osada';
	protected $primaryKey = 'id';
}