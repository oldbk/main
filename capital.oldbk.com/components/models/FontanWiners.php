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
 * @property int $id
 * @property int $winner
 * @property string $winner_info
 * @property string $winner_count
 * @property string $win_type
 * @property string $created_at
 *
 */
class FontanWiners extends BaseModal
{
	const TYPE_KR 	= 1;
	const TYPE_EKR 	= 2;

	protected $connection = 'capital';
	protected $table = 'fontan_winers';
	protected $primaryKey = 'id';
}