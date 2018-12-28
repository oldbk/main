<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\models\magic;
use components\Component\VarDumper;
use components\models\Magic;


/**
 * Class Ability
 * @package components\models\magic
 */
class Ability extends Magic
{
    const ABILITY_PASSIVE       = 1;
    const ABILITY_NO_FIGHT      = 2;
    const ABILITY_FIGHT         = 3;
    const ABILITY_COLLECTIVE    = 4;

    private static $abilityTitle = [
		self::ABILITY_PASSIVE       => 'Пассивные абилити',
		self::ABILITY_NO_FIGHT      => 'Небоевые абилити',
		self::ABILITY_FIGHT         => 'Боевые абилити',
		self::ABILITY_COLLECTIVE    => 'Коллективные боевые абилити',
	];

    private static $abilityLimits = [
		self::ABILITY_PASSIVE       => 1,
		self::ABILITY_NO_FIGHT      => 2,
		self::ABILITY_FIGHT         => 2,
		self::ABILITY_COLLECTIVE    => 2,
	];

    private static $abilityKey = [
		self::ABILITY_PASSIVE       => 'passive',
		self::ABILITY_NO_FIGHT      => 'nofight',
		self::ABILITY_FIGHT         => 'fight',
		self::ABILITY_COLLECTIVE    => 'collective',
	];

    private static $pasivAbility        = [
		3 => [840, 841, 842],
		6 => [850, 851, 852],
		2 => [860, 861, 862],
	];
    private static $noFightAbility      = [
		3 => [825, 1730, 723, 830, 813],
		6 => [846, 1731, 844, 724, 831],
		2 => [826, 1732, 848, 725, 832],
		0 => [838, 834],
	];
    private static $fightAbility        = [
		3 => [704, 701, 702, 728],
		6 => [805, 705, 708, 726],
		2 => [711, 703, 706, 727],
		4 => [716, 712, 713],
		0 => [714, 715],
	];
    private static $collectiveAbility   = [
		3 => [707, 864, 808],
		6 => [709, 863, 720],
		2 => [721, 722, 804],
		0 => [717],
	];

    private static $maxudar = [
		//Боевые
		716 => 2,
		704 => 5, 726 => 5, 727 => 5, 728 => 5, 805 => 5, 711 => 5, 714 => 5,
		701 => 10, 702 => 10, 705 => 10, 708 => 10, 703 => 10, 706 => 10, 712 => 10, 713 => 10, 715 => 10,
		//Коллективые
		707 => 3, 864 => 3, 808 => 3, 709 => 3, 863 => 3, 720 => 3, 721 => 3, 722 => 3, 804 => 3, 717 => 3,
	];

    public static function getAllAbilityByAlign($align)
    {
        $returned = array();

        $align = (int)$align;
        $ability_ids = array();

        foreach ([self::$pasivAbility, self::$noFightAbility, self::$fightAbility, self::$collectiveAbility] as $ability) {
            if(isset($ability[$align])) {
                $ability_ids = array_merge($ability_ids, $ability[$align]);
            }
        }
		if(!$ability_ids) {
			return $returned;
		}

		$_data = static::whereIn('id', $ability_ids)
			->get()->toArray();

		foreach ($_data as $item) {
			$returned[$item['id']] = $item;
		}

        return $returned;
    }

    public static function getPassiveAbilityByAlign($align)
    {
        $align = (int)$align;

        return isset(self::$pasivAbility[$align]) ? self::$pasivAbility[$align] : array();
    }

    public static function getNoFightAbilityByAlign($align)
    {
        $align = (int)$align;

        return isset(self::$noFightAbility[$align]) ? self::$noFightAbility[$align] : array();
    }

    public static function getFightAbilityByAlign($align)
    {
        $align = (int)$align;

        return isset(self::$fightAbility[$align]) ? self::$fightAbility[$align] : array();
    }

    public static function getCollectiveAbilityByAlign($align)
    {
        $align = (int)$align;

        return isset(self::$collectiveAbility[$align]) ? self::$collectiveAbility[$align] : array();
    }

    public static function getAbilityViewList($align)
    {
        $align = (int)$align;

        return array(
            self::ABILITY_PASSIVE       => self::getPassiveAbilityByAlign($align),
            self::ABILITY_NO_FIGHT      => self::getNoFightAbilityByAlign($align),
            self::ABILITY_FIGHT         => self::getFightAbilityByAlign($align),
            self::ABILITY_COLLECTIVE    => self::getCollectiveAbilityByAlign($align),
        );
    }

    /**
     * @param $type
     * @return null
     */
    public static function getAbilityTitle($type)
    {
        return isset(self::$abilityTitle[$type]) ? self::$abilityTitle[$type] : null;
    }

    public static function getAbilityKey($type)
    {
        return isset(self::$abilityKey[$type]) ? self::$abilityKey[$type] : null;
    }

    public static function hasAbility($align, $type, $ability)
    {
        $abilityViewList = self::getAbilityViewList($align);
        return isset($abilityViewList[$type]) && in_array($ability,$abilityViewList[$type]);
    }

    public static function checkLimit($type, $count)
    {
        return self::$abilityLimits[$type] > $count;
    }

    public static function getAbilityLimit($type)
    {
        return self::$abilityLimits[$type];
    }

    public static function getMaxUdar($ability_id)
    {
        return isset(self::$maxudar[$ability_id]) ? self::$maxudar[$ability_id] : 0;
    }
}