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
 * @property int $user_id
 * @property string $login
 * @property int $count
 * @property int $updated_at
 * @property int $finish_interval
 * @property string $data
 * @property int $change_host
 * @property int $change_host_count
 * @property int $total_check_host
 *
 */
class PluginUserWarning extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'plugin_user_warning';
	protected $primaryKey = 'id';
}