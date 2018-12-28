<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Chat
 * @package components\Model
 *
 * @property int $id
 * @property int $owner
 * @property int $date
 * @property string $text
 * @property string $login
 * @property float $align
 * @property string $klan
 * @property int $level
 * @property int $del_id
 * @property string $del_login
 * @property int $del_level
 * @property float $del_align
 * @property string $del_klan
 *
 */
class Fontan extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'fontan';
	protected $primaryKey = 'id';
}