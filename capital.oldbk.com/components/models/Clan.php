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
 * @property string $short
 * @property string $name
 * @property string $descr
 * @property int $glava
 * @property string $vozm
 * @property string $align
 * @property string $mshadow
 * @property string $wshadow
 * @property string $homepage
 * @property string $chat
 * @property string $rekrut1
 * @property string $rekrut2
 * @property int $rekrut_klan
 * @property int $base_klan
 * @property int $voinst
 * @property int $messages
 * @property int $defch
 * @property int $tax_date
 * @property int $tax_timer
 * @property int $msg
 * @property int $time_to_del
 * @property int $warcancel
 *
 */
class Clan extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'clans';
	protected $primaryKey = 'id';
}