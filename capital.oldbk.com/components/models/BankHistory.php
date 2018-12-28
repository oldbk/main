<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class BankHistory
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $date
 * @property string $text
 * @property int $bankid
 *
 */
class BankHistory extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'bankhistory';
	protected $primaryKey = 'id';
}