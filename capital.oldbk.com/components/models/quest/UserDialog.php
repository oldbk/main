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
 * @property int $user_id
 * @property int $quest_id
 * @property int $dialog_id
 * @property int $state
 *
 *
 */
class UserDialog extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'user_dialog';
	protected $primaryKey = ['user_id', 'quest_id', 'dialog_id'];
	public $incrementing = false;
}