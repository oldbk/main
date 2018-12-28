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
 * @property int $type
 * @property int $item_id
 * @property string $name
 * @property string $img
 * @property int $room
 * @property string $present
 * @property int $extra
 * @property int $durability
 *
 */
class RuinesItems extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'ruines_items';
	protected $primaryKey = 'id';
}