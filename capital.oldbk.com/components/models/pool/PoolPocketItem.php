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
 * @property int $pocket_id
 * @property string $item_type
 * @property int $give_count
 * @property int $updated_at
 * @property int $created_at
 *
 *
 * @property PoolPocket $pocket
 * @property PoolPocketItemInfo[] $infos
 */
class PoolPocketItem extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool_pocket_item';
	protected $primaryKey = 'id';

	/** @var  */
	protected $info = null;

	public function pocket()
	{
		return $this->belongsTo(PoolPocket::class, 'pocket_id', 'id');
	}

	public function infos()
	{
		return $this->hasMany(PoolPocketItemInfo::class, 'pocket_item_id', 'id');
	}
}