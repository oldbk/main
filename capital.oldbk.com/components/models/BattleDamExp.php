<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use Yadakhov\InsertOnDuplicateKey;

/**
 * Class BankHistory
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $battle
 * @property int $owner
 * @property int $damage
 * @property int $exp
 * @property int $fwin
 * @property int $dcount
 * @property int $mag_damage
 * @property int $dflag
 *
 */
class BattleDamExp extends BaseModal
{
	use InsertOnDuplicateKey;

	protected $connection = 'capital';
	protected $table = 'battle_dam_exp';
	protected $primaryKey = 'id';
}