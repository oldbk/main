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
 * @property int $id_dj
 * @property int $r1_access
 * @property int $r2_access
 * @property int $top_dj
 * @property int $icq
 * @property string $skype
 *
 */
class Rdjsn extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'r_djsn';
	protected $primaryKey = 'id';
}