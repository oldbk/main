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
 *
 * @property int $id
 * @property int $owner
 * @property int $type
 * @property int $ownerlvl
 * @property int $num
 * @property int $lvl
 * @property string $t1_logins
 * @property string $t1_loginscache
 * @property string $t1_pass
 * @property string $comment
 * @property int $starttime
 *
 */
class RuinesStart extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'ruines_start';
	protected $primaryKey = 'id';
}