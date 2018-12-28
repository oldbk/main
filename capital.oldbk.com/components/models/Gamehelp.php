<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;

use components\models\_base\BaseModal;

class Gamehelp extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'gamehelp';
	protected $primaryKey = 'id';
}