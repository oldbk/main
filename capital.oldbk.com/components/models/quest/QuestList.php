<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\quest;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $quest_type
 * @property string $name
 * @property int $started_at
 * @property int $ended_at
 * @property int $min_level
 * @property int $max_level
 * @property int $limit_count
 * @property int $limit_interval
 * @property int $is_enabled
 * @property int $updated_at
 * @property int $created_at
 * @property int $is_deleted
 * @property int $is_canceled
 *
 */
class QuestList extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_list';
	protected $primaryKey = 'id';

    const TYPE_DATERANGE    = 'daterange';
    const TYPE_LIMITED      = 'limited';
    const TYPE_INTERVAL     = 'interval';
    const TYPE_DAILY        = 'daily';
    const TYPE_WEEKLY       = 'weekly';
}