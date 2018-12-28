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
 * @property int $id
 * @property int $pool_id
 * @property string $description
 * @property string $condition
 * @property int $updated_at
 * @property int $created_at
 *
 *
 * @property Pool $pool
 * @property PoolPocketItem[] $items
 */
class PoolPocket extends BaseModal
{
	const CONDITION_OR 	= 'or';
	const CONDITION_AND = 'and';

	protected $connection = 'capital';
	protected $table = 'pool_pocket';
	protected $primaryKey = 'id';

	public function pool()
	{
		return $this->belongsTo(Pool::class, 'pool_id', 'id');
	}

	public function items()
	{
		return $this->hasMany(PoolPocketItem::class, 'pocket_id', 'id');
	}
}