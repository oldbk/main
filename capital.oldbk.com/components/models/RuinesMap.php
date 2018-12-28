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
 * @property int $rooms
 * @property int $starttime
 * @property int $t1score
 * @property int $t2score
 * @property int $k1owner
 * @property int $k2owner
 * @property int $lowlvl
 * @property int $sanct
 * @property int $k1timeout
 * @property int $k2timeout
 * @property int $type
 *
 */
class RuinesMap extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'ruines_map';
	protected $primaryKey = 'id';
}