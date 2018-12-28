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
 * @property int $name
 * @property int $description
 * @property int $quest_ids
 * @property int $is_enabled
 *
 *
 */
class QuestEvent extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_event';
	protected $primaryKey = 'id';
}