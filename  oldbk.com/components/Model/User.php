<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;
use components\Component\VarDumper;
use components\Helper\StatsHelper;

/**
 * Class User
 * @package components\Model
 *
 * @method $this|$this[] asModel()
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
 */
class User extends AbstractCapitalModel
{
    const ZNAHAR_FREE_LEVEL         = 3;
    const ZNAHAR_RATIO_STATS        = 1;
    const ZNAHAR_RATIO_STATS_NEWBIE = 0;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 0;

    public $bank = array();

    public static $slots = array(
        'sergi', 'kulon', 'perchi', 'weap', 'bron', 'r1', 'r2', 'r3', 'helm',
        'shit', 'boots', 'nakidka', 'rubashka', 'runa1', 'runa2', 'runa3'
    );

    const STIH_FIRE     = 1;
    const STIH_EARTH    = 2;
    const STIH_AIR      = 3;
    const STIH_WATER    = 4;

    /**
     * @param string $className
     * @return User
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function fieldMap()
    {
        return array(
            'id', 'login', 'email', 'pass', 'second_password', 'realname', 'borndate', 'sex', 'city', 'icq', 'http',
            'info', 'lozung', 'color', 'level', 'align', 'klan', 'sila', 'lovk', 'inta', 'vinos', 'intel', 'mudra', 'duh',
            'bojes', 'money', 'noj', 'mec', 'topor', 'dubina', 'win', 'lose', 'status', 'borncity', 'borntime', 'room',
            'maxhp', 'hp', 'maxmana', 'mana', 'sergi', 'kulon', 'perchi', 'weap', 'bron', 'r1', 'r2', 'r3', 'helm',
            'shit', 'boots', 'stats', 'exp', 'master', 'shadow', 'nextup', 'm1', 'm2', 'm3', 'm4', 'm5', 'm6', 'm7',
            'm8', 'm9', 'm10', 'm11', 'm12', 'm13', 'm14', 'm15', 'nakidka', 'mfire', 'mwater', 'mair', 'mearth',
            'mlight', 'mgray', 'mdark', 'fullhptime', 'zayavka', 'battle', 'battle_t', 'block', 'palcom', 'medals',
            'ip', 'podarokAD', 'lab', 'bot', 'in_tower', 'ekr', 'chattime', 'sid', 'fullmptime', 'deal', 'married',
            'injury_possible', 'labzay', 'fcount', 'rep', 'repmoney', 'last_battle', 'vk_user_id', 'bpzay', 'bpalign',
            'bpstor', 'bpbonussila', 'bpbonushp', 'show_advises', 'hidden', 'battle_fin', 'gruppovuha', 'autofight',
            'expbonus', 'wcount', 'victorina', 'id_grup', 'prem', 'hiller', 'khiller', 'slp', 'trv', 'ldate', 'stamina',
            'odate', 'id_city', 'ruines', 'voinst', 'rubashka', 'stbat', 'winstbat', 'citizen', 'skulls', 'hiddenlog',
            'naim', 'naim_war', 'naim_war', 'pasbaf', 'runa1', 'runa2', 'runa3', 'is_sn', 'elkbat', 'smagic', 'unikstatus',
            'change', 'rep_bonus', 'gold', 'znak', 'buketbat'
        );
    }

    public static function tableName()
    {
        return 'users';
    }

    public static function pkField()
    {
        return 'id';
    }

    public function getPk()
    {
        return $this->id;
    }

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
    public function clearSlots()
    {
        foreach (static::$slots as $slot) {
            $this->{$slot} = 0;
        }
        for ($i = 1; $i < 16; $i++) {
            $field = 'm'.$i;
            $this->{$field} = 0;
        }
        return $this;
    }

    public function getMagStih()
    {
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

    public function enter($room)
    {
        return static::update(array('room' => $room), 'id = ?', $this->id);
    }

    public function htmlLogin()
    {
        $align = sprintf('<img src="https://i.oldbk.com/i/align_%s.gif">', $this->align);
        $klan = '';
        if($this->klan)
            $klan = sprintf('<img src="https://i.oldbk.com/i/klan/%s.gif">', $this->klan);
        $login = sprintf('<b>%s</b>', $this->login);
        $level = sprintf('[%d]', $this->level);
        $inf = sprintf('<a href="/inf.php?%d" target="_blank"><img src="https://i.oldbk.com/i/inf.gif" width="12" height="11" alt="Инф. о %s"></a>', $this->id, $this->login);

        return sprintf('%s%s%s%s%s', $align, $klan, $login, $level, $inf);
    }

    public function fullHtmlLogin()
    {
        $private = sprintf('<img onclick="top.AddToPrivate(\'%s\', top.CtrlPress,event); return false;" src="https://i.oldbk.com/i/lock.gif" style="cursor:pointer;" title="Приват" width="20" height="15">', $this->login);

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

    public function getZnaharRatioStats()
    {
        return $this->level > static::ZNAHAR_FREE_LEVEL ? static::ZNAHAR_RATIO_STATS : static::ZNAHAR_RATIO_STATS_NEWBIE;
    }

    public function getAlignForAbility()
    {
        return $this->klan == 'pal' ? 6 : $this->align;
    }

    public function getGlobalAbility()
    {
        return $this->getAlignForAbility();
    }

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

    public function getCost($num)
    {
        $key = StatsHelper::getKeyById($num);

        return $this->{$key} <= 10 ? 5 : $this->{$key} / 2 + 0.5;
    }

    public function takeStat($num, $count = 1)
    {
        $stat = StatsHelper::getKeyById($num);
        $this->takeParam($stat, $count);

        return $this;
    }

    public function addStat($num, $count = 1)
    {
        $stat = StatsHelper::getKeyById($num);
        $value = (int)$this->{$stat};

        $this->{$stat} = $value + (int)$count;

        return $this;
    }
}