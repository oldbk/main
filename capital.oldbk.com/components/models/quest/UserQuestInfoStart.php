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
 * @property int $user_quest_id
 * @property int $quest_id
 * @property int $level
 * @property string $align
 * @property string $craft_levels
 *
 */
class UserQuestInfoStart extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest_info_start';
	protected $primaryKey = 'user_quest_id';
}