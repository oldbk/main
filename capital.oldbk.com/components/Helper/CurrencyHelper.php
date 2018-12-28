<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 27.11.2015
 */

namespace components\Helper;


class CurrencyHelper
{
    const CURRENCY_KR   = 1;
    const CURRENCY_EKR  = 2;
    const CURRENCY_GOLD = 3;

    private static $currency_type = array(
        self::CURRENCY_KR   => 'кр',
        self::CURRENCY_EKR  => 'екр',
        self::CURRENCY_GOLD => 'монет',
    );

    public static function getTitle($currency)
    {
        return isset(self::$currency_type[$currency]) ? self::$currency_type[$currency] : null;
    }
}