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
 * @property int $get_money
 * @property int $give_money
 *
 */
class FontanLog extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'fontan_log';
	protected $primaryKey = 'id';
}