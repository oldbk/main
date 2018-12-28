<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $loto_num
 * @property int $item_id
 * @property string $field
 * @property string $value
 *
 */
class LotoItemInfo extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'loto_item_info';
	protected $primaryKey = ['loto_num', 'item_id', 'field'];
	public $incrementing = false;
}