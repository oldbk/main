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
 * @property int $dialog_id
 * @property int $item_id
 * @property string $item_type
 * @property int $next_dialog_id
 * @property string $message
 * @property int $updated_at
 * @property int $created_at
 *
 */
class QuestDialogAction extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'quest_dialog_action';
	protected $primaryKey = 'id';
}