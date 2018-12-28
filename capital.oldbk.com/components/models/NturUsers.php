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
 *
 *
 * @property int $id
 * @property int $ntype
 * @property int $stat
 * @property int $mktime
 * @property int $stat_time
 * @property string $nazva
 * @property string $pas
 * @property string $koment
 * @property int $o1
 * @property int $o2
 * @property int $o3
 * @property int $o4
 * @property int $o5
 * @property int $o6
 * @property int $o7
 * @property int $o8
 * @property int $o9
 * @property int $o10
 * @property int $o11
 * @property int $o12
 * @property int $o13
 * @property int $o14
 * @property int $o15
 * @property int $o16
 * @property int $o17
 * @property int $o18
 * @property int $o19
 * @property int $o20
 * @property int $o21
 * @property int $o22
 * @property int $o23
 * @property int $o24
 * @property int $o25
 * @property int $o26
 * @property int $o27
 * @property int $o28
 * @property int $o29
 * @property int $o30
 * @property int $o31
 * @property int $o32
 * @property int $battle
 * @property int $faza
 * @property int $sysmcount
 *
 */
class NturUsers extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'ntur_users';
	protected $primaryKey = 'id';
}