<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 19.04.17
 * Time: 14:49
 */

namespace components\models\_base;

/**
 * Class BaseModal
 * @package components\models
 *

 */
abstract class BaseModal extends \Eloquent
{
	protected $table;
	public $timestamps = false;
	protected $guarded = [];
	protected $connection = 'capital';

	public static function tableName()
	{
		$model = new static();
		$name = $model->getTable();
		unset($model);

		return $name;
	}
}