<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\pool;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @property int $pocket_item_id
 * @property string $field
 * @property string $value
 * @property int $pool_id
 * @property int $pocket_id
 *
 *
 * @property PoolPocketItem $item
 */
class PoolPocketItemInfo extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool_pocket_item_info';
	protected $primaryKey = 'id';

	public function item()
	{
		return $this->belongsTo(PoolPocketItem::class, 'pocket_item_id', 'id');
	}
}