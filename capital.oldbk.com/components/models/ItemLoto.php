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
 * @property int $loto
 * @property int $owner
 * @property int $saletime
 * @property int $dil
 * @property string $lotodate
 * @property int $win
 * @property int $item_name
 * @property int $item_id
 * @property int $shop_id
 * @property int $cost_kr
 * @property int $cost_ekr
 *
 */
class ItemLoto extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'item_loto';
	protected $primaryKey = 'id';
}