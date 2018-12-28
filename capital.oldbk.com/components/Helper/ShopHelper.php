<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.01.2016
 */

namespace components\Helper;

use components\models\Cshop;
use components\models\Eshop;
use components\models\Shop;

class ShopHelper
{
    const TYPE_ALL      = 0;
    const TYPE_SHOP     = 1;
    const TYPE_ESHOP    = 2;
    const TYPE_CSHOP    = 3;
    const TYPE_FSHOP    = 5;

    private static $string_name = array(
        self::TYPE_SHOP     => 'shop',
        self::TYPE_ESHOP    => 'eshop',
        self::TYPE_CSHOP    => 'cshop',
    );

    private static $razdel = array(
		1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",
		24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun", 61=>'eda' , 62=>'res', 72 =>''
	);

    public static function getPrototypes($ids, $shop_id)
    {
        switch ($shop_id) {
            case static::TYPE_SHOP:
                return Shop::whereIn('id', $ids)->get()->toArray();
                break;
            case static::TYPE_ESHOP:
                return Eshop::whereIn('id', $ids)->get()->toArray();
                break;
            case static::TYPE_CSHOP:
                return Cshop::whereIn('id', $ids)->get()->toArray();
                break;
        }

        return array();
    }

    public static function getFromString($name)
    {
        if(($key = array_search($name, self::$string_name)) !== false) {
            return $key;
        }

        return 0;
    }

    public static function buildLink($item)
	{
		$xx = $item['otdel'] == '' ? $item['razdel'] : $item['otdel'];
		$temp = explode('.', $item['img']);

		return sprintf('<a href="http://oldbk.com/encicl/%s/%s.html" target="_blank">%s</a>', static::$razdel[$xx], $temp[0], $item['name']);
	}
}