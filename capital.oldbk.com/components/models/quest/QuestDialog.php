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
 * @property int $global_parent_id
 * @property int $bot_id
 * @property int $item_id
 * @property string $item_type
 * @property string $action_type
 * @property string $message
 * @property int $is_saved
 * @property int $order_position
 * @property int $next_save_dialog
 * @property int $updated_at
 * @property int $created_at
 * @property string $quest_dialog
 *
 */
class QuestDialog extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_dialog';
	protected $primaryKey = 'id';

	const ACTION_QUEST_START        = 'quest_start';
	const ACTION_QUEST_END          = 'quest_end';
	const ACTION_PART_START         = 'part_start';
	const ACTION_PART_CHECK_FINISH  = 'part_check_finish';
	const ACTION_PART_END           = 'part_end';
	const ACTION_PART_NEXT_START    = 'part_next_start';
	const ACTION_TASK_CHECK         = 'task_check';
	const ACTION_DIALOG             = 'dialog';

	const TYPE_QUEST    = 'quest';
	const TYPE_PART     = 'part';
}