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
 * @property int $event_id
 * @property string $name
 * @property int $user_id
 * @property string $reward
 * @property int $created_at
 * @property int $date_event
 *
 */
class QuestEventReward extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_event_reward';
	protected $primaryKey = 'id';
}