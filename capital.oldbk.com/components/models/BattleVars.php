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
 * @property int $update_time
 * @property int $type
 * @property int $bexit_count
 * @property int $bexit_team
 * @property int $istok_use
 * @property int $colns_use
 * @property int $napal
 * @property int $unclons_use
 * @property int $help_use
 * @property int $help_proto
 * @property int $bots_use
 * @property int $baf_701_use
 * @property int $baf_702_use
 * @property int $baf_703_use
 * @property int $baf_705_use
 * @property int $baf_706_use
 * @property int $baf_708_use
 * @property int $baf_709_use
 * @property int $baf_711_use
 * @property int $baf_712_use
 * @property int $baf_713_use
 * @property int $baf_714_use
 * @property int $baf_715_use
 * @property int $baf_720_use
 * @property int $baf_721_use
 * @property int $baf_722_use
 * @property int $baf_723_use
 * @property int $baf_805_use
 * @property int $baf_808_use
 * @property int $baf_823_use
 * @property int $baf_795_use
 *
 */
class BattleVars extends BaseModal
{
	use InsertOnDuplicateKey;

	protected $connection = 'capital';
	protected $table = 'battle_vars';
	protected $primaryKey = 'id';
}