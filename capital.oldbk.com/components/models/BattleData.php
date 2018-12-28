<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class BattleData
 *
 * @property int $battle
 * @property int $baff_717
 * @property int $t1_baff_804
 * @property int $t2_baff_804
 * @property int $t3_baff_804
 * @property int $baff_863
 * @property int $t2_castle
 *
 */
class BattleData extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'battle_data';
	protected $primaryKey = 'battle';
	public $incrementing = false;
}