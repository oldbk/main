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
 * @property int $stavka
 *
 */
class FontanSt extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'fontan_st';
	protected $primaryKey = 'id';
}