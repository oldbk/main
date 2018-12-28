<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\Helper;


class StatsHelper
{
    const STAT_SILA     = 1;
    const STAT_LOVK     = 2;
    const STAT_INTA     = 3;
    const STAT_VINOS    = 4;
    const STAT_INTEL    = 5;
    const STAT_MUDRA    = 6;

    public static $stats = array(
        'sila'  => 'Сила',
        'lovk'  => 'Ловкость',
        'inta'  => 'Интуиция',
        'vinos' => 'Выносливость',
        'intel' => 'Интеллект',
        'mudra' => 'Мудрость ',
    );

    public static $stats_id = array(
        self::STAT_SILA     => 'sila',
        self::STAT_LOVK     => 'lovk',
        self::STAT_INTA     => 'inta',
        self::STAT_VINOS    => 'vinos',
        self::STAT_INTEL    => 'intel',
        self::STAT_MUDRA    => 'mudra',
    );

    public static function getStatsIdName()
    {
        $arr = array();
        foreach (self::$stats_id as $id => $key) {
            $arr[$id] = self::$stats[$key];
        }

        return $arr;
    }

    public static function getTitle($stat_id)
    {
        $stat = static::getKeyById($stat_id);
        return isset(static::$stats[$stat]) ? static::$stats[$stat] : null;
    }

    public static function getKeyById($id)
    {
        return isset(self::$stats_id[$id]) ? self::$stats_id[$id] : null;
    }

    public static function checkStat($num)
    {
        return array_key_exists($num, self::$stats_id);
    }
}