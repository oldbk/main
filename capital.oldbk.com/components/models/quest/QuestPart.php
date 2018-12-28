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
 * @property int $quest_id
 * @property string $name
 * @property string $img
 * @property string $description_type
 * @property string $description_data
 * @property string $chat_start
 * @property string $chat_end
 * @property int $is_auto_finish
 * @property int $is_auto_start
 * @property int $part_number
 * @property int $updated_at
 * @property int $created_at
 * @property int $is_deleted
 * @property int $weight
 * @property string $complete_condition_message
 *
 */
class QuestPart extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_part';
	protected $primaryKey = 'id';

    const DESCRIPTION_TYPE_TASK         = 'task';
    const DESCRIPTION_TYPE_INVENTORY    = 'inventory';
}