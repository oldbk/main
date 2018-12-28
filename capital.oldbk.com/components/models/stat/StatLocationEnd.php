<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\stat;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $location_type
 * @property int $user_id
 * @property int $user_level
 * @property int $created_at
 *
 */
class StatLocationEnd extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'stat_location_end';
	protected $primaryKey = 'id';
}