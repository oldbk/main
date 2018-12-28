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
 * @package components\models
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $updated_at
 * @property int $created_at
 * @property int $is_group
 * @property int $is_enabled
 *
 */
class ConfigKoMain extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'config_ko_main';
	protected $primaryKey = 'id';
}