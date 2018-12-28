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
 * @property string $target_type
 * @property int $target_id
 * @property string $validator_type
 * @property int $pool_id
 * @property int $pocket_id
 * @property int $created_at
 *
 */
class PoolValidator extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool_validator';
	protected $primaryKey = 'id';
}