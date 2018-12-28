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
 * @property int $event_id
 * @property int $count
 * @property int $updated_at
 * @property int $created_at
 * @property int $date_event
 *
 *
 */
class UserQuestEvent extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest_event';
	protected $primaryKey = 'id';
	public $timestamps = true;
	public $dateFormat = 'U';
}