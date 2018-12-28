<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 21.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Zayavka
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $coment
 * @property int $type
 * @property string $team1
 * @property string $team2
 * @property int $start
 * @property int $timeout
 * @property int $t1min
 * @property int $t1max
 * @property int $t2min
 * @property int $t2max
 * @property int $level
 * @property string $podan
 * @property int $t1c
 * @property int $t2c
 * @property float $stavka
 * @property int $blood
 * @property int $fond
 * @property int $price
 * @property int $nomagic
 * @property int $autoblow
 * @property int $am1
 * @property int $am2
 * @property int $ae1
 * @property int $ae2
 * @property string $t1hist
 * @property string $t2hist
 * @property int $bcl
 * @property int $subtype
 * @property int $zcount
 * @property int $hz
 */
class Zayavka extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'zayavka';
	protected $primaryKey = 'id';
}