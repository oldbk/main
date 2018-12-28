<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 17.12.2015
 */

namespace components\Helper;


class Json
{
    public static function encode($data, $in = 'windows-1251', $out = 'utf-8')
    {
        $data = self::array_encode($data, $in, $out);
        return json_encode($data);
    }

    public static function decode($data, $convert = false)
    {
    	if($convert) {
			$data = iconv('windows-1251', 'utf-8', $data);
		}

        return json_decode($data);
    }


	public static function decode2($data)
	{
		$data = iconv('cp1251', 'utf8', $data);
		$data = json_decode($data, true);

		return self::array_encode($data, 'utf8', 'cp1251');
	}

    public static function array_encode(&$arr, $in = 'windows-1251', $out = 'utf-8'){
        array_walk_recursive($arr, function(&$val, &$key) use ($in, $out) {
            if(is_string($val))
                $val = iconv($in, $out, $val);
            if(is_string($key))
                $key = iconv($in, $out, $key);
        });
        return $arr;
    }
}