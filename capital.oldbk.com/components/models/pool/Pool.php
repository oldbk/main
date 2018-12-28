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
 * @property string $name
 * @property int $updated_at
 * @property int $created_at
 *
 *
 * @property PoolPocket[] $pockets
 * @property PoolAssign[] $assigns
 */
class Pool extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool';
	protected $primaryKey = 'id';

	public $timestamps = false;
	protected $guarded = [];

	public static function tableName()
	{
		$model = new static();
		$name = $model->getTable();
		unset($model);

		return $name;
	}

	public function pockets()
	{
		return $this->hasMany(PoolPocket::class, 'pool_id', 'id');
	}

	public function assigns()
	{
		return $this->hasMany(PoolAssign::class, 'pool_id', 'id');
	}
}