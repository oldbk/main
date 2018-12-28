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
 * @property int $owner
 * @property int $chaos
 * @property int $labp
 *
 */
class Ristalka extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'ristalka';
	protected $primaryKey = 'id';
}