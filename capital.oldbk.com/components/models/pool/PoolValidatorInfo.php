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
 * @property int $validator_id
 * @property string $field
 * @property string $value
 * @property string $target_type
 * @property int $target_id
 * @property int $pool_id
 * @property int $pocket_id
 * @property string $validator_type
 *
 */
class PoolValidatorInfo extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool_validator_info';
	protected $primaryKey = 'id';
}