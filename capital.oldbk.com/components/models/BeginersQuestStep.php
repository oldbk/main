<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $owner
 * @property int $quest_id
 * @property int $qtype
 * @property int $qftype
 * @property int $status
 * @property int $step
 * @property int $step_f
 * @property string $city
 * @property string $get_date
 * @property int $count
 * @property int $quest_count
 *
 */
class BeginersQuestStep extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'beginers_quests_step';
	protected $primaryKey = 'id';
}