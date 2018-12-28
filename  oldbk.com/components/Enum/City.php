<?php

namespace components\Enum;


/**
 * Class City
 * @package components\Enum
 */
class City
{
    /**
     * @var array
     */
    private static $cities = [
        1 =>'Capital City',
        2 =>'Avalon City',
    ];

    /**
     * @var int
     */
    public static $defaultRegistrationCity = 1;

    /**
     * @param $index
     * @return mixed
     */
    public static function getCityName($index)
    {
        return static::$cities[$index];
    }
}