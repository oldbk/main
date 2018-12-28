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
 * @property int $user_id
 * @property int $ts_id
 * @property string $cpa
 * @property string $status
 * @property int $need_send_postback
 * @property int $updated_at
 * @property int $created_at
 *
 */
class UserAdvert extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_advert';
	protected $primaryKey = 'id';
}