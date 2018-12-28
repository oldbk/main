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
 * @property int $user_id
 * @property string $action_type
 * @property int $rate_type
 * @property int $value
 * @property int $created_at
 * @property int $updated_at
 *
 */
class TopRate extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'top_rate';
	protected $primaryKey = 'id';
}