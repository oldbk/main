<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.02.2016
 */

namespace components\Helper;


class BotHelper
{
    const BOT_CHARODEJKA        = 1;
    const BOT_NASTAVNICK        = 2;
    const BOT_ALHIMIK           = 3;
    const BOT_ORAKUL            = 4;
    const BOT_STRAZH            = 5;
    const BOT_KOMENDANT         = 6;
    const BOT_MASTER_KALVIS     = 7;
    const BOT_BARMEN_PYATNICO   = 8;
    const BOT_JULIA             = 10;
    const BOT_DU_RANDIR         = 11;
    const BOT_KLER              = 12;
    const BOT_FLOWER            = 13;
    const BOT_ARCHIVARIUS       = 14;
    const BOT_MAG               = 15;
    const BOT_ALISA             = 16;
    const BOT_ALEXANDRO         = 17;
    const BOT_GALIAS         	= 18;
    const BOT_JAMES         	= 19;

    public static $links_main = array(
        self::BOT_STRAZH => '/outcity.php',
    );

    public static $bots = array(
        self::BOT_STRAZH            => 'Страж',
        self::BOT_CHARODEJKA        => 'Чародейка',
        self::BOT_NASTAVNICK        => 'Наставкник',
        self::BOT_ALHIMIK           => 'Алхимик Агниус',
        self::BOT_ORAKUL            => 'Оракул',
        self::BOT_KOMENDANT         => 'Комендант',
        self::BOT_MASTER_KALVIS     => 'Мастер Кальвис',
        self::BOT_BARMEN_PYATNICO   => 'бармен Пятницо',
        self::BOT_JULIA             => 'Джулия',
        self::BOT_DU_RANDIR         => 'Ду Рандир',
        self::BOT_KLER              => 'Клэр',
        self::BOT_FLOWER            => 'Цветочница',
        self::BOT_ARCHIVARIUS       => 'Архивариус',
        self::BOT_MAG               => 'Маг',
        self::BOT_ALISA             => 'Алиса',
        self::BOT_ALEXANDRO         => 'Алехандро',
        self::BOT_GALIAS            => 'Торговец Галиас',
        self::BOT_JAMES            	=> 'Стражник Джеймс',
    );

    public static function getBotLogin($bot_id)
    {
        return self::$bots[$bot_id];
    }
}