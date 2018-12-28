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
 * @property string $src
 * @property string $code
 * @property int $is_correct
 * @property string $check_param
 * @property int $created_at
 * @property string $data
 *
 */
class PluginAnalyze extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'plugin_analyze';
	protected $primaryKey = 'id';
}