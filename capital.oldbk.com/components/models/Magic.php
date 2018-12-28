<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Magic
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $name
 * @property int $chanse
 * @property int $time
 * @property string $file
 * @property int $targeted
 * @property string $img
 * @property int $battle_use
 * @property int $need_block
 * @property int $nlevel
 *
 */
class Magic extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'magic';
	protected $primaryKey = 'id';
}