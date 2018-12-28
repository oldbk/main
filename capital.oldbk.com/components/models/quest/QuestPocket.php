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
 * @property int $item_id
 * @property string $item_type
 * @property string $condition
 * @property int $updated_at
 * @property int $created_at
 * @property int $dialog_finish_id
 *
 */
class QuestPocket extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_pocket';
	protected $primaryKey = 'id';

    const TYPE_AND  = 'and';
    const TYPE_OR   = 'or';

    const TYPE_PART_REWARD      = 'part_reward';
    const TYPE_PART_VALIDATE    = 'part_validate';
    const TYPE_PART_TASK        = 'part_task';
    const TYPE_PART_TAKE        = 'part_take';
}