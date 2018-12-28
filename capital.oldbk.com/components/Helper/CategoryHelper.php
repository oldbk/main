<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 06.04.2016
 */

namespace components\Helper;

/**
 * Class CategoryHelper
 * @package common\helper
 *
 *
 * this file duplicate to game
 */
class CategoryHelper
{
    const CATEGORY_ITEM     = 1;
    const CATEGORY_MAGIC    = 2;
    const CATEGORY_OTHER    = 3;

    private static $labels = array(
        self::CATEGORY_ITEM     => 'Обмундирование',
        self::CATEGORY_MAGIC    => 'Заклятия',
        self::CATEGORY_OTHER    => 'Прочее',
    );

    public static function getLabel($category_id)
    {
        return self::$labels[$category_id];
    }

    public static function getLabels()
    {
        return self::$labels;
    }
}