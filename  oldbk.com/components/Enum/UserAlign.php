<?php

namespace components\Enum;

/**
 * Class UserAlign
 * @package Combats\Enum
 */
class UserAlign extends Enum
{
	const GRAY					= 0;    //Серый

	const PALADIN_INQUISITOR	= 1.2;  //Инк
	const PALADIN_BLUE			= 1.3;  //Синий паладин =) Паладин поднебеcья
	const PALADIN_SMILE			= 1.5;  //Желтый ( Паладин cолнечной улыбки)
	const PALADIN_FIRE			= 1.7;  //Красный (Паладин огненной заpи)
	const PALADIN_KEEPER		= 1.75; //Хранитель знаний
	const PALADIN_SKY			= 1.9;  //Паладин неба
	const PALADIN_COORDINATOR	= 1.91; //Старший паладин неба
	const PALADIN_COMMANDER		= 1.92; //Кавалер ордена
	const PALADIN_BUG_LORD		= 1.93; //Повелитель багов
	const PALADIN_HIGH			= 1.99; //Верховный

    const NEUTRAL				= 2;    //Нейтрал
    const DARK	    			= 3;    //Тьма
	const CHAOS					= 4;    //Хаос
	const ACHAOS				= 5;    //Абсолютный Хаос
	const ACHAOS2				= 2.9;  //Абсолютный Хаос
    const LIGHT					= 6;    //Светлый

    const CREATOR					= 2.1;//Мироздатель
    const GARBAGE					= 2.2;//Мусорщик

    const PROGERS					= 2.4;//progers

	/**
	 * @param $value
	 * @return bool
	 */
	public static function isValuePaladinRang($value)
	{
		return ($value > 1 && $value < 2);
	}

    /**
     * @param $value
     * @return bool
     */
    public static function isValueAdmin($value)
    {
        return ($value > 2 && $value < 3);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isValueAdminClan($value)
    {
        return ($value == 'Adminion' || $value == 'radminion');
    }

}
