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
 * @property int $razmen_to
 * @property int $to_t
 * @property int $razmen_from
 * @property int $from_t
 * @property int $attack
 * @property int $attack2
 * @property int $block
 * @property int $time_blow
 * @property int $lab
 *
 */
class BattleFd extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'battle_fd';
	protected $primaryKey = 'id';
}