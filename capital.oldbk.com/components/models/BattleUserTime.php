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
 * @property int $owner
 * @property int $timer1
 * @property int $timer2
 * @property int $timer3
 *
 */
class BattleUserTime extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'battle_user_time';
	protected $primaryKey = 'id';
}