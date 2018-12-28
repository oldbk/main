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
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $message
 * @property int $is_send
 * @property int $must_send
 * @property int $day_interval
 * @property string $message_type
 * @property int $is_fixed
 * @property int $updated_at
 * @property int $created_at
 *
 */
class StaticMessage extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'static_message';
	protected $primaryKey = 'id';

	const MESSAGE_LOTO = 'loto';
}