<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 09.12.2015
 */

namespace components\Helper;


class ItemHelper
{
    const WEAPON_DEFAULT    = 0;
    const WEAPON_NOJ        = 1;
    const WEAPON_MECH       = 2;
    const WEAPON_TOPOR      = 3;
    const WEAPON_DUBINA     = 4;
    const WEAPON_KOSTIL     = 5;
    const WEAPON_ELKA       = 6;
    const WEAPON_BUKET      = 7;

    //оружие
    const RAZDEL_ORUJIE_ALL = 0;
    const RAZDEL_NOJ        = 1;
    const RAZDEL_TOPOR      = 11;
    const RAZDEL_DUBINA     = 12;
    const RAZDEL_MECH       = 13;

    //одежда
    const RAZDEL_TAPKI      = 2;
    const RAZDEL_PERCHATKI  = 21;
    const RAZDEL_BRON_L     = 22;
    const RAZDEL_BRON_T     = 23;
    const RAZDEL_SHLEM      = 24;
    const RAZDEL_SHIT       = 3;
    const RAZDEL_PLASH      = 6;

    //ювелирка
    const RAZDEL_SERGI      = 4;
    const RAZDEL_OJERELYA   = 41;
    const RAZDEL_KOLCA      = 42;

    const RAZDEL_RUNA       = 52;

    private static $items = array(
        self::RAZDEL_ORUJIE_ALL,
        self::RAZDEL_NOJ,
        self::RAZDEL_TOPOR,
        self::RAZDEL_DUBINA,
        self::RAZDEL_MECH,

        self::RAZDEL_TAPKI,
        self::RAZDEL_PERCHATKI,
        self::RAZDEL_BRON_L,
        self::RAZDEL_BRON_T,
        self::RAZDEL_SHLEM,
        self::RAZDEL_SHIT,
        self::RAZDEL_PLASH,

        self::RAZDEL_SERGI,
        self::RAZDEL_OJERELYA,
        self::RAZDEL_KOLCA,

        self::RAZDEL_RUNA,
    );

    private static $items_otdel_title = array(
        self::RAZDEL_NOJ        => 'Кастеты и ножи',
        self::RAZDEL_TOPOR      => 'Топоры',
        self::RAZDEL_DUBINA     => 'Дубины',
        self::RAZDEL_MECH       => 'Мечи',

        self::RAZDEL_TAPKI      => 'Обувь',
        self::RAZDEL_PERCHATKI  => 'Перчатки',
        self::RAZDEL_BRON_L     => 'Легкая броня',
        self::RAZDEL_BRON_T     => 'Тяжелая броя',
        self::RAZDEL_SHLEM      => 'Шлем',
        self::RAZDEL_SHIT       => 'Щиты',
        self::RAZDEL_PLASH      => 'Плащи',

        self::RAZDEL_SERGI      => 'Серьги',
        self::RAZDEL_OJERELYA   => 'Кулоны',
        self::RAZDEL_KOLCA      => 'Кольца',

        self::RAZDEL_RUNA       => 'Руины',
    );

    public static function checkCategory($category_id)
    {
        return in_array($category_id, self::$items);
    }

    public static function getDressedCategory()
    {
        $categories = self::$items;

        return $categories;
    }

    public static function getCategoryTitle($category_id)
    {
        return isset(self::$items_otdel_title[$category_id]) ? self::$items_otdel_title[$category_id] : null;
    }

    public static $slots_list = array(
        'sergi', 'kulon', 'perchi', 'boots', 'r1', 'r2', 'r3', 'weap', 'bron',
        'helm', 'shit', 'nakidka', 'rubashka'
    );

    private static $slots_by_category = array(
        self::RAZDEL_NOJ        => 'weap',
        self::RAZDEL_TOPOR      => 'weap',
        self::RAZDEL_DUBINA     => 'weap',
        self::RAZDEL_MECH       => 'weap',

        self::RAZDEL_TAPKI      => 'boots',
        self::RAZDEL_PERCHATKI  => 'perchi',
        self::RAZDEL_BRON_L     => 'rubashka',
        self::RAZDEL_BRON_T     => 'bron',
        self::RAZDEL_SHLEM      => 'helm',
        self::RAZDEL_SHIT       => 'shit',
        self::RAZDEL_PLASH      => 'nakidka',

        self::RAZDEL_SERGI      => 'sergi',
        self::RAZDEL_OJERELYA   => 'kulon',
        self::RAZDEL_KOLCA      => 'r',

        self::RAZDEL_RUNA       => 'runa',
    );

    public static function getSlot($category)
    {
        return self::$slots_by_category[$category];
    }

    public static function prepareOwnForUser($field)
    {
        return str_replace(array(
            'mech','fire','water','air','earth','dark','light','gray',
        ), array(
            'mec','mfire','mwater','mair','mearth','mdark','mlight','mgray'
        ), $field);
    }

    public static function getItemId($city_id, $item_id)
    {
        $prefix = array(
            0 => 'cap',
            1 => 'ava',
            2 => 'ang',
        );

        return sprintf('%s%s', $prefix[$city_id], $item_id);
    }

    public static function makeUnikFromArray(&$item)
    {
        $mfinfo = array(
            'stats' => 0,
            'hp'    => 0,
            'bron'  => 0
        );

        if ($item['gsila'] || $item['glovk'] || $item['ginta'] || $item['gintel'] || $item['gmudra']) {
            $item['stbonus'] += 3;
            $mfinfo['stats'] = 3;
        }
        if ($item['ghp']) {
            $item['ghp'] += 20;
            $mfinfo['hp'] = 20;
        }
        foreach (array('bron1', 'bron2', 'bron3', 'bron4') as $bron) {
            if($item[$bron]) {
                $item[$bron] += 3;
                $mfinfo['bron'] = 3;
            }
        }

        $item['ekr_flag'] = 2;
        $item['unik'] = 1;
        $item['name'].= ' (мф)';
        $item['mfinfo'] = serialize($mfinfo);

        return $item;
    }

    public static $vaucher = array(200001,200002,200005,200010,200025,200050,200100,200250,200500);

    public static function baseFromPrototype($item, $prototype, $options = array())
    {
        $fields = array(
/* общие */         'name','type','massa','img','ekr_flag','rareitem','img_big', 'notsell',
/* цена */          'cost','ecost', 'repcost',
/* долговечность */ 'maxdur','duration',
/* ремонт */        'isrep',
            //что дает предмет
/* общее */         'ghp','gmp','magic','group',
/* статы */         'gsila','glovk','ginta','gintel',
/* владения ор. */  'gnoj','gtopor','gdubina','gmech',
/* владения маг. */ 'gfire','gwater','gair','gearth','glight','ggray','gdark',
/* МФ */            'mfkrit','mfakrit','mfuvorot','mfauvorot',
/* бронь */         'bron1','bron2','bron3','bron4',
/* урон */          'maxu','minu',
/* бонусы */        'stbonus','mfbonus', //ВНИМАНИЕ!!! боунсы прототипа, без учета Модификации предмета
/* усил */          'ab_mf','ab_bron','ab_uron',
            //требования
/* общее */         'nlevel','nalign','nsex',
/* идент */         'needident',
/* статы */         'nsila','nlovk','ninta','nintel','nmudra','nvinos',
/* владения ор. */  'nnoj','ntopor','ndubina','nmech',
/* владения маг. */ 'nfire','nwater','nair','nearth','nlight','ngray','ndark',
        );
        foreach ($fields as $field) {
            $item[$field] = $prototype[$field];
        }

        $item = array_merge($item, array(
            'prototype'     => $prototype['id'],
            'otdel'         => $prototype['razdel'],
            'labonly'       => 0,
            'labflag'       => 0,
        ));

        foreach ($options as $field => $value) {
            if($value == null) {
                continue;
            }
            switch ($field) {
                case 'goden':
                    if($value > 0) {
                        $DateTime = new \DateTime();
                        $DateTime->modify(sprintf('+%d days', $value));
                        $item['goden'] = $value;
                        $item['dategoden'] = $DateTime->getTimestamp();
                    }
                    break;
                case 'ekr_flag':
                    $item['ekr_flag'] = $value;
                    break;
                case 'is_mf':
                    if($value === true) {
                        $item = self::makeUnikFromArray($item);
                    }
                    break;
                case 'unik':
                    if(is_numeric($value) && $value >= 0) {
                        $item['unik'] = $value;
                    }
                    break;
                case 'notsell':
                    if($value == 1) {
                        $item['notsell'] = $value;
                    }
                    break;
                default:
                    $item[$field] = $value;
                    break;
            }
        }

        if(!isset($item['goden']) && isset($prototype['goden']) && $prototype['goden'] > 0) {
            $DateTime = new \DateTime();
            $DateTime->modify(sprintf('+%d days', $prototype['goden']));
            $item['goden'] = $prototype['goden'];
            $item['dategoden'] = $DateTime->getTimestamp();
        }

        return $item;
    }

    public static function getOptions()
	{
		return ['goden', 'ekr_flag', 'is_mf', 'unik', 'notsell'];
	}

	public static function buildLink($item)
	{
		$ehtml = str_replace('.gif','', $item['img']);
		$item['otdel'] == '' ? $xx = $item['razdel'] : $xx = $item['otdel'];

		$razdel = [
			1 => "kasteti",
			11 => "axe",
			12 => "dubini",
			13 => "swords",
			14 => "bow",
			2 => "boots",
			21 => "naruchi",
			22 => "robi",
			23 => "armors",
			24 => "helmet",
			3 => "shields",
			4 => "clips",
			41 => "amulets",
			42 => "rings",
			5 => "mag1",
			51 => "mag2",
			6 => "amun",
			61 => 'eda' ,
			62 => 'res',
			72 =>''
		];
		$vau4 = [100005,100015,100020,100025,100040,100100,100200,100300];

		if ($item['type'] == 30) {
			$razdel[$xx]="runs/".$ehtml;
		} elseif($razdel[$xx] == '') {
			$dola = [5001,5002,5003,5005,5010,5015,5020,5025];
			if (in_array($item['prototype'], $vau4)) {
				$razdel[$xx] = 'vaucher';
			} elseif (in_array($item['prototype'], $dola)) {
				$razdel[$xx] = 'earning';
			} else {
				$oskol = [15551,15552,15553,15554,15555,15556,15557,15558,15561,15562,15568,15563,15564,15565,15566,15567];
				if (in_array($item['prototype'], $oskol)) {
					$razdel[$xx] = "amun/".$ehtml;
				} else {
					$razdel[$xx] = 'predmeti/'.$ehtml;
				}
			}
		} else {
			$razdel[$xx] = $razdel[$xx]."/".$ehtml;

		}

		if (($item['art_param'] != '') && ($item['type'] != 30)) {
			if ($item['arsenal_klan'] != '') {
				$razdel[$xx] = 'art_clan';
			} elseif ($item['sowner'] != 0) {
				//личный
				$razdel[$xx] = 'art_pers';
			}
		}

		return "http://oldbk.com/encicl/".$razdel[$xx].'.html';
	}

	public static function buildImg($item, $big = false)
	{
		if($big === false) {
			return 'http://i.oldbk.com/i/sh/'.$item['img'];
		}

		return 'http://i.oldbk.com/i/sh/'.($item['img_big']?$item['img_big']:$item['img']);
	}
}