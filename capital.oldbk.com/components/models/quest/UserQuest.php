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
 * @property int $is_finished
 * @property int $is_send_gift
 * @property int $is_cancel
 * @property int $is_end
 * @property int $dialog_id_save
 * @property int $custom_dialog_id
 * @property int $created_at
 * @property int $ended_at
 *
 *
 */
class UserQuest extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest';
	protected $primaryKey = 'id';

    public static function getEnabled($user_id)
    {
		$QuestList = static::whereRaw('user_id = ? and is_cancel = 0 and is_end = 0 and is_send_gift = 0 and is_finished = 0',
			[$user_id])->get(['quest_id', 'id', 'current_part'])->toArray();
		$user_quests = [];
		foreach ($QuestList as $Quest) {
			$user_quests[$Quest['id']] = $Quest;
		}

		return $user_quests;
    }
}