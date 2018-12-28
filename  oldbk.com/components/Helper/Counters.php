<?php

namespace components\Helper;

/**
 * Class Counters
 * @package components\Helper
 */
class Counters
{
    /**
     * @param bool $isUTF8
     * @return mixed|string
     */
    public static function getCounters($isUTF8 = false)
    {
        $counters = include $_SERVER['DOCUMENT_ROOT'] . "/counters/all.php";

        return $isUTF8
            ? iconv("windows-1251", "UTF-8", $counters)
            : $counters;
    }
}