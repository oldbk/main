<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.01.2016
 */

namespace components\Helper;


use components\Model\Cshop;
use components\Model\Eshop;
use components\Model\Shop;

class ShopHelper
{
    const TYPE_ALL      = 0;
    const TYPE_SHOP     = 1;
    const TYPE_ESHOP    = 2;
    const TYPE_CSHOP    = 3;

    private static $string_name = array(
        self::TYPE_SHOP     => 'shop',
        self::TYPE_ESHOP    => 'eshop',
        self::TYPE_CSHOP    => 'cshop',
    );

    public static function getPrototypes($ids, $shop_id)
    {
        switch ($shop_id) {
            case static::TYPE_SHOP:
                return Shop::findAll('id in ('.implode(',', array_map(function($v){ return (int)$v; }, $ids)).')')->asArray();
                break;
            case static::TYPE_ESHOP:
                return Eshop::findAll('id in ('.implode(',', array_map(function($v){ return (int)$v; }, $ids)).')')->asArray();
                break;
            case static::TYPE_CSHOP:
                return Cshop::findAll('id in ('.implode(',', array_map(function($v){ return (int)$v; }, $ids)).')')->asArray();
                break;
        }

        return array();
    }

    public static function getFromType($type) {
        if (isset(self::$string_name[$type])) return self::$string_name[$type];
	return false;		
    }

    public static function getFromString($name)
    {
        if(($key = array_search($name, self::$string_name)) !== false) {
            return $key;
        }

        return 0;
    }
}