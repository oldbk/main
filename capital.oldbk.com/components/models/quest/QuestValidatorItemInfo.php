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
 * @property string $validator_item_type
 * @property int $validator_parent_id
 * @property string $validator_parent_type
 *
 */
class QuestValidatorItemInfo extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'quest_validator_item_info';
	protected $primaryKey = ['item_id', 'field'];
	public $incrementing = false;
}