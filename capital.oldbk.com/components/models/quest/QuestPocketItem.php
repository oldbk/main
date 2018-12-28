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
 * @property int $pocket_id
 * @property int $pocket_item_id
 * @property string $pocket_item_type
 * @property string $item_type
 * @property int $count
 * @property int $updated_at
 * @property int $created_at
 *
 */
class QuestPocketItem extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_pocket_item';
	protected $primaryKey = 'id';
}