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
 * @property int $loto_num
 * @property int $category_id
 * @property int $stock
 * @property int $created_at
 * @property int $updated_at
 * @property int $count
 * @property int $use_count
 * @property int $cost_kr
 * @property int $cost_ekr
 * @property string $item_name
 *
 */
class LotoItem extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'loto_item';
	protected $primaryKey = 'id';
}