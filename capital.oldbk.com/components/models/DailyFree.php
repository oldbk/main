<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;


/**
 * Class Chat
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $user_id
 * @property string $essence
 * @property int $uses
 * @property int $limit_uses
 * @property int $used_total
 * @property int $limit_used_total
 * @property int $used_at
 * @property int $added_at
 * @property int $is_finished
 *
 */
class DailyFree extends BaseModal
{
	use tHasCompositePrimaryKey;

	const ESSENCE_FONTAN = 'fontan';

	protected $connection = 'capital';
	protected $table = 'daily_free';
	protected $primaryKey = ['user_id', 'essence'];
	public $incrementing = false;
}