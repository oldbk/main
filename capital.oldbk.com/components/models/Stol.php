<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class Stol
 * @package components\models
 *
 *
 * @property int $owner
 * @property int $stol
 * @property int $count
 */
class Stol extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'stol';
	protected $primaryKey = ['owner', 'stol'];
	public $incrementing = false;
}