<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\pool;
use components\models\_base\BaseModal;

/**
 * Class PoolAssign
 * @package components\models\pool
 *
 * @property int $id
 * @property int $pool_id
 * @property string $target_type
 * @property int $target_id
 * @property string $target_name
 * @property int $updated_at
 * @property int $created_at
 *
 *
 * @property Pool $pool
 * @property PoolAssignRating $rating
 */
class PoolAssign extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool_assign';
	protected $primaryKey = 'id';

	public function pool()
	{
		return $this->belongsTo(Pool::class, 'pool_id', 'id');
	}

	public function rating()
	{
		return $this->hasOne(PoolAssignRating::class, 'pool_assign_id', 'id');
	}
}