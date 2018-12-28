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
 * @property int $id
 * @property int $battle
 * @property int $owner
 * @property int $point
 * @property int $runs
 * @property int $battle_flag
 * @property int $rkf_bonus
 * @property int $useabil
 * @property int $usehill
 * @property int $cure_value_hp
 * @property int $rkm_bonus
 *
 */
class BattleRunsExp extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'battle_runs_exp';
	protected $primaryKey = 'id';
}