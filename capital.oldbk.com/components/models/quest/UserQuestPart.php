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
 * @property int $quest_id
 * @property int $quest_part_id
 * @property int $is_finished
 * @property int $is_started
 * @property int $started_at
 * @property int $ended_at
 * @property int $ready_to_finish
 * @property int $part_number
 *
 */
class UserQuestPart extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest_part';
	protected $primaryKey = 'id';
}