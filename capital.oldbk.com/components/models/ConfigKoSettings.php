<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class Bank
 * @package components\models
 *
 *
 * @property int $main_id
 * @property int $group_id
 * @property string $field_name
 * @property string $field_value
 * @property string $field_type
 *
 */
class ConfigKoSettings extends BaseModal
{
	use tHasCompositePrimaryKey;

	const TYPE_DATETIMEPICKER 	= 'datetimepicker';
	const TYPE_STRING 			= 'string';
	const TYPE_ARRAY 			= 'array';

	protected $connection = 'capital';
	protected $table = 'config_ko_settings';
	protected $primaryKey = ['main_id', 'group_id', 'field_name'];
	public $incrementing = false;
}