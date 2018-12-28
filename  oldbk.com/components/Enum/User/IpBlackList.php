<?php

namespace components\Enum\User;

/**
 * Class IpBlackList
 */
class IpBlackList
{
    public static $ips = [
        '77.120.192.136',
        '188.19.171.169',
        '84.52.34.215',
    ];

    public static function pushIp($ip)
    {
        static::$ips[] = $ip;
    }


}
