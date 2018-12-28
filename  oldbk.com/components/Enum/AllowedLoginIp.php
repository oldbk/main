<?php

namespace components\Enum;


/**
 * Class AllowedLoginIp
 * @package components\Enum
 */
class AllowedLoginIp
{
    /**
     * @var array
     */
    private static $allowedIp = [
        14897 => '195.138.84.12', // 14897:Bred
    ];


    /**
     * @param $id
     * @return bool|mixed
     */
    public static function getIpById($id)
    {
        return static::$allowedIp[$id] ?? false;
    }
}