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
 * @property int $agressor
 * @property string $agr_txt
 * @property int $defender
 * @property string $def_txt
 * @property int $wtype
 * @property string $ztime
 * @property string $stime
 * @property string $ftime
 * @property int $winner
 * @property int $agr_ark
 * @property int $def_ark
 *
 */
class ClanWarNew extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'clans_war_new';
	protected $primaryKey = 'id';
}