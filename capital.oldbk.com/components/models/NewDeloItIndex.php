<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Delo
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $item_id
 * @property int $delo_id
 *
 */
class NewDeloItIndex extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'new_delo_it_index';
	protected $primaryKey = 'id';
}