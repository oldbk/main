<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\quest;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $item_id
 * @property string $field
 * @property string $value
 * @property int $global_parent_id
 * @property int $pocket_id
 * @property int $pocket_item_id
 * @property string $pocket_item_type
 *
 */
class QuestPocketItemInfo extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'quest_pocket_item_info';
	protected $primaryKey = ['item_id', 'field'];
	public $incrementing = false;
}