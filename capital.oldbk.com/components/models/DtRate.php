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
 *
 * @property int $id
 * @property int $owner
 * @property float $credit
 * @property int $time
 * @property int $dtid
 * @property string $ip
 *
 */
class DtRate extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'dt_rate';
	protected $primaryKey = 'id';
}