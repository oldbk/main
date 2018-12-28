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
 * @property int $pool_assign_id
 * @property int $rating_id
 * @property int $min_position
 * @property int $max_position
 *
 */
class PoolAssignRating extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pool_assign_rating';
	protected $primaryKey = 'id';
}