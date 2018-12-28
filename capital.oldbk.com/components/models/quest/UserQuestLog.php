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
 * @property int $user_id
 * @property int $quest_id
 * @property int $part_id
 * @property int $pocket_item_id
 * @property int $user_quest_id
 * @property int $user_part_id
 * @property int $user_task_id
 * @property int $check_count
 * @property int $created_at
 *
 */
class UserQuestLog extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest_log';
	protected $primaryKey = 'id';
}