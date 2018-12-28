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
 * @property int $lotodate
 * @property int $status
 * @property string $log
 * @property int $msg
 * @property int $msg24
 * @property int $msg6
 * @property int $in_process
 *
 */
class ItemLotoRas extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'item_loto_ras';
	protected $primaryKey = 'id';
}