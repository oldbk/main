<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 17.12.2015
 */

namespace components\Helper;


class Json
{
    public static function encode($data)
    {
        $data = self::array_encode($data);
        return json_encode($data);
    }

    public static function decode($data, $assoc = false, $iconv = false)
    {
    	$data = json_decode($data, $assoc);
		if($iconv) {
			$data = self::array_decode($data);
		}

        return $data;
    }

    protected static function array_encode(&$arr){
        array_walk_recursive($arr, function(&$val, &$key){
            if(is_string($val))
                $val = iconv('windows-1251', 'utf-8', $val);
            if(is_string($key))
                $key = iconv('windows-1251', 'utf-8', $key);
        });
        return $arr;
    }

	protected static function array_decode(&$arr){
		array_walk_recursive($arr, function(&$val, &$key){
			if(is_string($val) && mb_detect_encoding($val, 'UTF-8', true) !== false)
				$val = iconv('utf-8','windows-1251', $val);
			if(is_string($key) && mb_detect_encoding($val, 'UTF-8', true) !== false)
				$key = iconv('utf-8','windows-1251', $key);
		});
		return $arr;
	}
}