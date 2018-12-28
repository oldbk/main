<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\models\effect;
use components\models\Effect;

/**
 * Class Ability
 * @package components\models\magic
 */
class Element extends Effect
{
    private static $titles = [
		1 => 'Огонь',
		2 => 'Земля',
		3 => 'Воздух',
		4 => 'Вода',
	];

    private static $titles2 = [
		1 => 'Стихия Огня',
		2 => 'Стихия Земли',
		3 => 'Стихия Воздуха',
		4 => 'Стихия Воды',
	];

    private static $types = [
		1 => 10901,
		2 => 10902,
		3 => 10903,
		4 => 10904,
	];

    private static $images = [
		1 => 'http://i.oldbk.com/i/sh/wrath_ares.gif',
		2 => 'http://i.oldbk.com/i/magic/wrath_ground_status.gif',
		3 => 'http://i.oldbk.com/i/magic/wrath_air_status.gif',
		4 => 'http://i.oldbk.com/i/magic/wrath_water_status.gif',
	];

    private static $prices = [
		1   => 0.74,
		3   => 1.99,
		7   => 4,
		30  => 14.44
	];

    public static function getTitle($num)
    {
        return isset(self::$titles[$num]) ? self::$titles[$num] : null;
    }

    public static function getTitle2($num)
    {
        return isset(self::$titles2[$num]) ? self::$titles2[$num] : null;
    }

    public static function getPrices()
    {
        return static::$prices;
    }

    public static function getTitles()
    {
        return static::$titles;
    }

    public static function getImage($num)
    {
        return isset(self::$images[$num]) ? self::$images[$num] : null;
    }

    public static function checkDay($day)
    {
        return array_key_exists($day, static::$prices);
    }

    public static function checkTypes($type)
    {
        return array_key_exists($type, static::$types);
    }

    public static function getPrice($num)
    {
        return static::$prices[$num];
    }

    public static function getTypes()
    {
        return static::$types;
    }
}