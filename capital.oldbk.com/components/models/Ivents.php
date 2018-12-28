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
 *
 * @property int $id
 * @property string $nazv
 * @property string $info
 * @property int $stat
 * @property string $last_finish
 * @property int $cc
 * @property int $off
 *
 */
class Ivents extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'ivents';
	protected $primaryKey = 'id';
}