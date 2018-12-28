<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\Helper\StatsHelper;
use components\models\_base\BaseModal;

/**
 * Class User
 * @package components\models
 *
 * @property int $id
 * @property string $login
 * @property string $email
 * @property string $pass
 * @property string $second_password
 * @property string $realname
 * @property string $borndate
 * @property int $sex
 * @property string $city
 * @property int $icq
 * @property string $http
 * @property string $info
 * @property string $lozung
 * @property string $color
 * @property int $level
 * @property string $align
 * @property string $klan
 * @property int $sila
 * @property int $lovk
 * @property int $inta
 * @property int $vinos
 * @property int $intel
 * @property int $mudra
 * @property int $duh
 * @property int $bojes
 * @property float $money
 * @property int $noj
 * @property int $mec
 * @property int $topor
 * @property int $dubina
 * @property int $win
 * @property int $lose
 * @property string $status
 * @property string $borncity
 * @property int $borntime
 * @property int $room
 * @property int $maxhp
 * @property int $hp
 * @property int $maxmana
 * @property int $mana
 * @property int $sergi
 * @property int $kulon
 * @property int $perchi
 * @property int $weap
 * @property int $bron
 * @property int $r1
 * @property int $r2
 * @property int $r3
 * @property int $helm
 * @property int $shit
 * @property int $boots
 * @property int $stats
 * @property int $exp
 * @property int $master
 * @property string $shadow
 * @property int $nextup
 * @property int $m1
 * @property int $m2
 * @property int $m3
 * @property int $m4
 * @property int $m5
 * @property int $m6
 * @property int $m7
 * @property int $m8
 * @property int $m9
 * @property int $m10
 * @property int $m11
 * @property int $m12
 * @property int $m13
 * @property int $m14
 * @property int $m15
 * @property int $m16
 * @property int $m17
 * @property int $m18
 * @property int $m19
 * @property int $m20
 * @property int $nakidka
 * @property int $mfire
 * @property int $mwater
 * @property int $mair
 * @property int $mearth
 * @property int $mlight
 * @property int $mgray
 * @property int $mdark
 * @property int $fullhptime
 * @property int $zayavka
 * @property int $battle
 * @property int $battle_t
 * @property int $block
 * @property int $palcom
 * @property int $medals
 * @property int $ip
 * @property int $podarokAD
 * @property int $lab
 * @property int $bot
 * @property int $in_tower
 * @property float $ekr
 * @property int $chattime
 * @property string $sid
 * @property int $fullmptime
 * @property int $deal
 * @property string $married
 * @property int $injury_possible
 * @property int $labzay
 * @property int $fcount
 * @property int $rep
 * @property int $repmoney
 * @property int $last_battle
 * @property int $vk_user_id
 * @property int $bpzay
 * @property int $bpalign
 * @property int $bpstor
 * @property int $bpbonussila
 * @property int $bpbonushp
 * @property string $show_advises
 * @property int $hidden
 * @property int $battle_fin
 * @property string $gruppovuha
 * @property int $autofight
 * @property float $expbonus
 * @property int $wcount
 * @property int $victorina
 * @property int $id_grup
 * @property int $prem
 * @property int $hiller
 * @property int $khiller
 * @property int $slp
 * @property int $trv
 * @property int $ldate
 * @property int $stamina
 * @property int $odate
 * @property int $id_city
 * @property int $ruines
 * @property int $voinst
 * @property int $rubashka
 * @property int $stbat
 * @property int $winstbat
 * @property int $citizen
 * @property int $skulls
 * @property string $hiddenlog
 * @property int $naim
 * @property int $naim_war
 * @property int $pasbaf
 * @property int $runa1
 * @property int $runa2
 * @property int $runa3
 * @property int $is_sn
 * @property int $elkbat
 * @property int $smagic
 * @property int $unikstatus
 * @property int $change
 * @property int $rep_bonus
 * @property int $gold
 * @property int $znak
 * @property int $buketbat
 * @property int $uclass
 * @property string $oldpass
 * @property string $salt
 */
class User extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users';
	protected static $table_name = 'users';
	protected $primaryKey = 'id';
	public $timestamps = false;

    const ZNAHAR_FREE_LEVEL         = 3;
    const ZNAHAR_RATIO_STATS        = 1;
    const ZNAHAR_RATIO_STATS_NEWBIE = 0;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 0;

    public $bank = [];
    public $realsex;

    public static $slots = [
		'sergi', 'kulon', 'perchi', 'weap', 'bron', 'r1', 'r2', 'r3', 'helm',
		'shit', 'boots', 'nakidka', 'rubashka', 'runa1', 'runa2', 'runa3'
	];

	private $_salt = 'N5v{xL/M(=-iEh<s%eaM';

    const STIH_FIRE     = 1;
    const STIH_EARTH    = 2;
    const STIH_AIR      = 3;
    const STIH_WATER    = 4;

	/**
	 * @return int
	 */
    public function getId()
    {
        return $this->id ? $this->id : 0;
    }

	/**
     * @param $field
     * @param $val
     * @return $this
     */
    public function takeParam($field, $val)
    {
        $value = (int)$this->{$field};
        $this->{$field} = $value - (int)$val;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearMaxHp()
    {
        $this->maxhp = $this->vinos * 6 + $this->bpbonushp;
		if($this->hp > $this->maxhp) {
            $this->hp = $this->maxhp;
        }

        return $this;
    }

	/**
	 * @return $this
	 */
	public function clearMaxMp()
	{
		$this->maxmana = $this->mudra * 10;
		if($this->mana > $this->maxmana) {
			$this->mana = $this->maxmana;
		}

		return $this;
	}

    /**
     * @return $this
     */
    public function clearSlots()
    {
        foreach (static::$slots as $slot) {
            $this->{$slot} = 0;
        }
        for ($i = 1; $i < 21; $i++) {
            $field = 'm'.$i;
            $this->{$field} = 0;
        }
        return $this;
    }

	/**
	 * @return int
	 */
    public function getMagStih()
    {
        if($this->smagic > 0) {
            return $this->smagic;
        }

        $dt = $this->borndate;
        $month = substr($dt,3,2);
        $day = substr($dt,0,2);

        $zodiak = 0;
        if ($month == "01") {
            $zodiak = $day >= 21 ? 11 : 10;
        } elseif ($month == "02") {
            $zodiak = $day >= 21 ? 12 : 11;
        } elseif ($month == "03") {
            $zodiak = $day >= 21 ? 1 : 12;
        } elseif ($month == "04") {
            $zodiak = $day >= 21 ? 2 : 1;
        } elseif ($month == "05") {
            $zodiak = $day >= 21 ? 3 : 2;
        } elseif ($month == "06") {
            $zodiak = $day >= 22 ? 4 : 3;
        } elseif ($month == "07") {
            $zodiak = $day >= 23 ? 5 : 4;
        } elseif ($month == "08") {
            $zodiak = $day >= 24 ? 6 : 5;
        } elseif ($month == "09") {
            $zodiak = $day >= 24 ? 7 : 6;
        } elseif ($month == "10") {
            $zodiak = $day >= 24 ? 8 : 7;
        } elseif ($month == "11") {
            $zodiak = $day >= 23 ? 9 : 8;
        } elseif ($month == "12") {
            $zodiak = $day >= 22 ? 10 : 9;
        }


        if ($zodiak == 1 || $zodiak == 5 || $zodiak == 9) {
            return static::STIH_FIRE; // Огонь (Овен, Лев, Стрелец)
        } elseif ($zodiak == 2 || $zodiak == 6 || $zodiak == 10) {
            return static::STIH_EARTH; // Земля (Козерог. Телец, Дева)
        } elseif ($zodiak == 3 || $zodiak == 7 || $zodiak == 11) {
            return static::STIH_AIR; //Воздух (Весы, Водолей, Близнецы)
        } elseif ($zodiak == 4 || $zodiak == 8 || $zodiak == 12) {
            return static::STIH_WATER; //Вода (Рак, Скорпион, Рыбы)
        }

        return static::STIH_FIRE;
    }

	/**
	 * @param array $exptable
	 * @return $this
	 */
    public function getEmptyStats(array $exptable)
    {
        $_stats = $this->getStatByAP($exptable);

        $this->sila = $this->bpbonussila + 3;
        $this->lovk = 3;
        $this->inta = 3;
        $this->vinos = $_stats['vinos'];
        $this->intel = 0;
        $this->mudra = 0;
        $this->stats = $_stats['stats'];
        $this->clearMaxHp();

        return $this;
    }

	/**
	 * @param array $exptable
	 * @return $this
	 */
    public function getEmptyMasters(array $exptable)
    {
        $_stats = $this->getStatByAP($exptable);
        $this->master = $_stats['master'];
        $this->noj = 0;
        $this->mec = 0;
        $this->topor = 0;
        $this->dubina = 0;
        $this->mfire = 0;
        $this->mwater = 0;
        $this->mair = 0;
        $this->mearth = 0;
        $this->mlight = 0;
        $this->mdark = 0;
        $this->mgray = 0;

        return $this;
    }

	/**
	 * @param array $exptable
	 * @return array
	 */
    public function getStatByAP(array $exptable)
    {
        $cl = 0; $money = 0; $stats = 3; $vinos = 3; $master = 1;
        foreach ($exptable as $current_exp => $item) {
            if($this->exp < $current_exp)
                break;

            /* 0stat  1umen  2vinos 3kred, 4level, 5up*/
            $cl = $item[5];
            $stats += $item[0];
            $master += $item[1];
            $vinos += $item[2];
        }

        return array('stats' => $stats, 'master' => $master, 'vinos' => $vinos, 'up' => $cl);
    }

	/**
	 * @param $room
	 * @return int
	 */
    public function enter($room)
    {
		$this->room = $room;
        return static::where('id', '=', $this->id)->update(['room' => $room]);
    }

	/**
	 * @return string
	 */
    public function htmlLogin()
    {
    	return static::renderNick($this->id, $this->align, $this->klan, $this->login, $this->level);
    }

	/**
	 * @return string
	 */
    public function fullHtmlLogin()
    {
        $private = sprintf('<img onclick="top.AddToPrivate(\'%s\', top.CtrlPress,event); return false;" src="http://i.oldbk.com/i/lock.gif" style="cursor:pointer;" title="Приват" width="20" height="15">', $this->login);

        return sprintf('%s%s', $private, $this->htmlLogin());
    }

    /**
     * @return int
     */
    public function getMoneyStatsForZnahar()
    {
        return (int)(($this->sila + $this->lovk + $this->inta + $this->vinos + $this->intel + $this->mudra - 9 - $this->level + 3) * 4);
    }

    /**
     * @return int
     */
    public function getMoneyMasterForZnahar()
    {
        return (int)(($this->noj + $this->mec + $this->topor + $this->dubina + $this->mfire + $this->mwater + $this->mair + $this->mearth + $this->mlight + $this->mgray + $this->mdark) * 4);
    }

	/**
	 * @return int
	 */
    public function getZnaharRatioStats()
    {
        return $this->level > static::ZNAHAR_FREE_LEVEL ? static::ZNAHAR_RATIO_STATS : static::ZNAHAR_RATIO_STATS_NEWBIE;
    }

	/**
	 * @return int|string
	 */
    public function getAlignForAbility()
    {
        return ($this->klan == 'pal' || $this->align == '1.99') ? 6 : $this->align;
    }

	/**
	 * @return int|string
	 */
    public function getGlobalAbility()
    {
        return $this->getAlignForAbility();
    }

	/**
	 * @param $num
	 * @return bool
	 */
    public function checkMinStat($num)
    {
        $add = 0;
        if($this->level == 9) {
            $add = 1;
        } elseif($this->level == 10) {
            $add = 3;
        }

        switch($num) {
            case StatsHelper::STAT_SILA:
                return $this->sila < (4 + $this->bpbonussila);
                break;
            case StatsHelper::STAT_LOVK:
                return $this->lovk < 4;
                break;
            case StatsHelper::STAT_INTA:
                return $this->inta < 4;
                break;
            case StatsHelper::STAT_VINOS:
                return $this->vinos < (4 + $this->level + $add);
                break;
            case StatsHelper::STAT_INTEL:
                return $this->intel < 1;
                break;
            case StatsHelper::STAT_MUDRA:
                return $this->mudra < 1;
                break;
        }

        return false;
    }

	/**
	 * @param $num
	 * @return bool
	 */
    public function canMoveTo($num)
    {
        switch($num) {
            case StatsHelper::STAT_INTEL:
                return $this->level >= 4;
                break;
            case StatsHelper::STAT_MUDRA:
                return $this->level >= 7;
                break;
        }

        return true;
    }

	/**
	 * @param $num
	 * @return float|int
	 */
    public function getCost($num)
    {
        $key = StatsHelper::getKeyById($num);

        return $this->{$key} <= 10 ? 5 : $this->{$key} / 2 + 0.5;
    }

	/**
	 * @param $num
	 * @param int $count
	 * @return $this
	 */
    public function takeStat($num, $count = 1)
    {
        $stat = StatsHelper::getKeyById($num);
        $this->takeParam($stat, $count);

        return $this;
    }

	/**
	 * @param $num
	 * @param int $count
	 * @return $this
	 */
    public function addStat($num, $count = 1)
    {
        $stat = StatsHelper::getKeyById($num);
        $value = (int)$this->{$stat};

        $this->{$stat} = $value + (int)$count;

        return $this;
    }

	/**
	 * @return array
	 */
	public static function getAllMagicStih()
	{
		return [
			self::STIH_FIRE,
			self::STIH_EARTH,
			self::STIH_AIR,
			self::STIH_WATER,
		];
	}

	/**
	 * @return bool|int
	 */
	public function isHaveUnikMedal()
	{
		return strpos($this->medals, 'k202;') !== false ? true : false;
	}

	/**
	 * @return bool|int
	 */
	public function isHaveSunikMedal()
	{
		return strpos($this->medals, 'k203;') !== false ? true : false;
	}

	/**
	 * @return array
	 */
	public function getUandUU()
	{
		$item_ids = [];
		$DressedItems = Inventory::where('dressed', '=', 1)
			->where('owner', '=', $this->id)
			->get(['id'])->toArray();
		foreach ($DressedItems as $Item) {
			$item_ids[] = $Item['id'];
		}
		$bonuses = [
			'ab_mf' 	=> 0,
			'ab_bron' 	=> 0,
			'ab_uron' 	=> 0,
			'u' 		=> 0,
			'uu' 		=> 0,
		];
		if($item_ids) {
			$bonuses = Inventory::from('inventory as i')
				->whereIn('id', $item_ids)
				->selectRaw('sum(ab_mf) as ab_mf, sum(ab_bron) as ab_bron, sum(ab_uron) as ab_uron')
				->selectRaw('count(if(unik=1,1,null)) as u, count(if(unik=2,1,null)) as uu')
				->first()->toArray();
		}
		if($this->isHaveUnikMedal()) {
			$bonuses['u']++;
		}
		if($this->isHaveSunikMedal()) {
			$bonuses['uu']++;
		}

		return $bonuses;
	}

	/**
	 * @param $u_count
	 * @return int
	 */
	public static function getTypeU($u_count)
	{
		switch (true) {
			case($u_count >= 6 && $u_count < 9):
				return 1;
			case($u_count >= 9 && $u_count < 12):
				return 2;
			case($u_count >= 12 && $u_count < 13):
				return 3;
			case($u_count >= 13):
				return 4;
				break;
		}

		return 0;
	}

	/**
	 * @param $uu_count
	 * @return int
	 */
	public static function getTypeUU($uu_count)
	{
		switch (true) {
			case($uu_count >= 6 && $uu_count < 9):
				return 1;
			case($uu_count >= 9 && $uu_count < 12):
				return 2;
			case($uu_count >= 12 && $uu_count < 13):
				return 3;
			case($uu_count >= 13):
				return 4;
				break;
		}

		return 0;
	}

	/**
	 * @param $user_id
	 * @return array
	 */
	public static function getIps($user_id)
	{
		$ips = [];
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$ips = [$_SERVER['REMOTE_ADDR']];
		} else {
			$Iplog = Iplog::where('owner', '=', $user_id)->orderBy('date', 'desc')->first(['ip']);
			if($Iplog) {
				$ips = explode('|', $Iplog->ip);
			}
		}

		return $ips;
	}

	/**
	 * @param $id
	 * @param $align
	 * @param $klan
	 * @param $login
	 * @param $level
	 * @return string
	 */
	public static function renderNick($id, $align, $klan, $login, $level)
	{
		$align = sprintf('<img src="http://i.oldbk.com/i/align_%s.gif">', $align);
		$klan_string = '';
		if($klan) {
			$klan_string = sprintf('<img src="http://i.oldbk.com/i/klan/%s.gif">', $klan);
		}
		$login = sprintf('<b>%s</b>', $login);
		$level = sprintf('[%d]', $level);
		$inf = sprintf('<a href="/inf.php?%d" target="_blank"><img src="http://i.oldbk.com/i/inf.gif" width="12" height="11" alt="Инф. о %s"></a>', $id, $login);

		return sprintf('%s%s%s%s%s', $align, $klan_string, $login, $level, $inf);
	}

	public function logRaw()
	{
		$hidden = isset($this->attributes['hidden']) ? $this->attributes['hidden'] : 0;

		$arr = [
			'id' 	=> $this->id,
			'level' => $this->level,
			'align' => $this->align,
			'klan' 	=> $this->klan,
			'login' => $this->login,
		];

		if (strpos($this->login,'Невидимка (клон') !== false) {
			$arr = array_merge($arr, [
				'login' => '<i>'.$this->login.'</i>',
				'level' => '??',
			]);
		} elseif ($hidden > 0 && $this->hiddenlog != '') {
			$voplot = $this->voplotInfo();
			$arr = array_merge($arr, [
				'id' 	=> $voplot['id'],
				'level' => $voplot['level'],
				'align' => $voplot['align'],
				'klan' 	=> $voplot['klan'],
				'login' => $voplot['login'],
			]);
		} elseif($hidden > 0) {
			$arr = array_merge($arr, [
				'id' => $hidden,
				'login' => '<i>Невидимка</i>',
				'level' => '??'
			]);
		}

		return $arr['id'].'|'.$arr['level'].'|'.$arr['align'].'|'.$arr['klan'].'|'.$arr['login'].'#';
	}

	public function voplotInfo()
	{
		$hidden = isset($this->attributes['hidden']) ? $this->attributes['hidden'] : 0;

		$arr = [
			'id' 	=> $this->id,
			'login' => $this->login,
			'level' => $this->level,
			'align' => $this->align,
			'sex' 	=> $this->sex,
			'klan' 	=> $this->klan
		];
		if($hidden > 0 && $this->hiddenlog != '') {
			$fake = explode(",", $this->hiddenlog);
			$arr = array_merge($arr, [
				'id' 	=> $fake[0],
				'login' => $fake[1],
				'level' => $fake[2],
				'align' => $fake[3],
				'sex' 	=> $fake[4],
				'klan' 	=> $fake[5],
			]);
		}

		return $arr;
	}

    /**
     * @return string
     */
    public static function generateSalt()
    {
        return md5(md5(time()). time());
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return $this->pass == self::generatePassword($password, $this->salt);
    }

    /**
     * @param $login
     * @param $password
     * @param $salt
     * @return string
     */
    public static function generatePassword($password, $salt)
    {
        return md5(md5($password) . $salt);
    }
}
