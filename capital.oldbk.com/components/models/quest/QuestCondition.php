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
 * @property int $group
 * @property int $item_id
 * @property string $item_type
 * @property string $condition_type
 * @property string $field
 * @property string $value
 *
 */
class QuestCondition extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_condition';
	protected $primaryKey = 'id';

    const ITEM_TYPE_QUEST = 'quest';
    const ITEM_TYPE_PART = 'part';
}