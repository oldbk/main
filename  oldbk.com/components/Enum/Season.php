<?php

namespace components\Enum;


/**
 * Class Season
 * @package components\Enum
 */
class Season
{
    /**
     * @var array
     */
    private static $season = [
        1 => 'winter',
        2 => 'spring',
        3 => 'summer',
        4 => 'autumn',
    ];


    /**
     * @param $quarter
     * @return string
     */
    public static function getSeason($quarter) :string
    {
        return static::$season[$quarter] ?? '';
    }
}