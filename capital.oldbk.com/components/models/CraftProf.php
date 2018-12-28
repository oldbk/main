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
 * @property string $name
 * @property string $rname
 * @property integer $type
 * @property string $desc
 *
 */
class CraftProf extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'craft_prof';
	protected $primaryKey = 'id';
}