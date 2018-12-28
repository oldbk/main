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
 * @property int $user_quest_id
 * @property int $user_quest_part_id
 * @property int $quest_id
 * @property int $quest_part_id
 * @property int $user_id
 * @property int $item_id
 * @property int $count
 * @property int $need_count
 * @property int $ended_at
 * @property int $is_finished
 * @property string $process
 * @property int $is_deleted
 *
 */
class UserQuestPartItem extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest_part_item';
	protected $primaryKey = 'id';
}