<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\models;
use components\Component\Config;
use components\Component\Slim\Slim;
use components\models\_base\BaseModal;

/**
 * Class Settings
 * @package components\Model
 *
 * @property string $key
 * @property string $value
 */
class Settings extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'settings';
	protected $primaryKey = 'key';

	public $incrementing = false;

	/**
	 * @param Slim $app
	 * @return Config
	 */
	public static function getAll($app)
	{
		$model = Config::init();

		$config_cache = $app->cache->get('dbConfig');
		if(!$config_cache || !is_array($config_cache)) {
			$config_cache = [];
			foreach (static::get() as $item) {
				$config_cache[] = [
					'key' => $item->key,
					'value' => $item->value,
				];
			}

			$app->cache->set('dbConfig', $config_cache, 3600 * 24);
		}

		foreach ($config_cache as $item) {
			if(property_exists($model, $item['key'])) {
				$model->{$item['key']} = $item['value'];
			}
		}

		return $model;
	}
}