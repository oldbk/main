<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 07.04.2016
 */

namespace components\Helper;


class LocationHelper
{
	const ROOM_ALL          = 'ALL';

	//входы
	const ROOM_ENTER_LAB    = 45;
	const ROOM_ENTER_RUINE  = 999;

	//улица
	const ROOM_CP           = 20;

	//здания
	const ROOM_SHOP         = 22;
	const ROOM_REMONT       = 23;
	const ROOM_ELKA         = 24;
	const ROOM_COMIS        = 25;
	const ROOM_BANK         = 29;
	const ROOM_FLOWER       = 34;
	const ROOM_KLAN         = 28;
	const ROOM_BEREZA       = 35;
	const ROOM_LOTERY       = 42;
	const ROOM_LORD         = 90;
	const ROOM_DEATH_TOWER  = 10000;
	const ROOM_ZNAHAR       = 43;

	const ROOM_RUINE        = 'ruine';
	const ROOM_RUINE_ENTER  = 999;

	const ROOM_LAB_ENTER    = 45;
	const ROOM_LAB_PROSTOJ  = 'lab_1';
	const ROOM_LAB_GEROIK   = 'lab_2';
	const ROOM_LAB_NOVICHKI = 'lab_3';
	const ROOM_LAB_3D       = 'lab_4';

	const ROOM_BS_ENTER     = 10000;
	const ROOM_BS           = 'bs';

	const ROOM_ZAGA         = 'zaga';

	//комнаты
	const ROOM_TEST         = 44;

    private static $location_list = array(
        self::ROOM_CP,
        self::ROOM_SHOP,
        self::ROOM_REMONT,
        self::ROOM_ELKA,
        self::ROOM_COMIS,
        self::ROOM_BANK,
        self::ROOM_FLOWER,
        self::ROOM_KLAN,
        self::ROOM_BEREZA,
        self::ROOM_LOTERY,
        self::ROOM_LORD,
        self::ROOM_DEATH_TOWER,
        self::ROOM_ZNAHAR,
        self::ROOM_RUINE,
        self::ROOM_RUINE_ENTER,
        self::ROOM_LAB_ENTER,
        self::ROOM_LAB_PROSTOJ,
        self::ROOM_LAB_GEROIK,
        self::ROOM_LAB_NOVICHKI,
        self::ROOM_LAB_3D,
        self::ROOM_BS_ENTER,
        self::ROOM_BS,
        self::ROOM_TEST,
    );
}