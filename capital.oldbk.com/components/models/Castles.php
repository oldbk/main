<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;


/**
 * Class Castles
 * @package components\models
 *
 * @property int $id
 * @property int $num
 * @property int $nlevel
 * @property int $status
 * @property int $dayofweek
 * @property int $hourofday
 * @property int $timeouta
 * @property string $clanshort
 * @property string $clanashort1
 * @property string $clanashort2
 * @property int $lastpagegen
 * @property int $lastcoingen
 * @property int $pagenum
 * @property int $pagecolor
 * @property int $battle
 * @property int $tur_log
 *
 */
class Castles extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'castles';
	protected $primaryKey = 'id';
}