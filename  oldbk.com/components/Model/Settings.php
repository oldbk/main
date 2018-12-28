<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\Model;
use components\Component\Config;

/**
 * Class Settings
 * @package components\Model
 *
 * @property string $key
 * @property string $value
 */
class Settings extends AbstractCapitalModel
{
    /**
     * @param string $className
     * @return Settings
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function fieldMap()
    {
        return array(
            'key', 'value'
        );
    }

    public static function tableName()
    {
        return 'settings';
    }

    public static function pkField()
    {
        return 'key';
    }

    public function getPk()
    {
        return $this->key;
    }

    /**
     * @return Config
     */
    public static function getAll()
    {
        $model = Config::init();

        foreach (static::findAll()->asArray() as $item) {
            if(property_exists($model, $item['key']))
                $model->{$item['key']} = $item['value'];
        }

        return $model;
    }
}