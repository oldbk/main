<?php


namespace components\Helper;

/**
 * Class Geo
 * @package components\Helper
 */
class Geo
{

    public static $gi;
    public static $gi6;


    public static function init()
    {
        include(ROOT_DIR . "/GeoIP/geoip.inc");
        include(ROOT_DIR . "/GeoIP/geoipregionvars.php");

        static::$gi = geoip_open(ROOT_DIR . "/GeoIP/GeoIP.dat", GEOIP_STANDARD);
        static::$gi6 = geoip_open(ROOT_DIR . "/GeoIP/GeoIPv6.dat", GEOIP_STANDARD);
    }
}