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
 * @property string $item_type
 * @property int $parent_id
 * @property int $parent_type
 *
 */
class QuestValidatorItem extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_validator_item';
	protected $primaryKey = 'id';
}