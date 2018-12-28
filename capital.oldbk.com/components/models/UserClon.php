<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class UserClon
 *
 * @property int $id
 * @property string $login
 * @property int $sex
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
 * @property int $noj
 * @property int $mec
 * @property int $topor
 * @property int $dubina
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
 * @property int $nakidka
 * @property string $shadow
 * @property int $battle
 * @property int $bot
 * @property int $id_user
 * @property int $at_cost
 * @property int $kulak1
 * @property int $sum_minu
 * @property int $sum_maxu
 * @property int $sum_mfkrit
 * @property int $sum_mfakrit
 * @property int $sum_mfuvorot
 * @property int $sum_mfauvorot
 * @property int $sum_bron1
 * @property int $sum_bron2
 * @property int $sum_bron3
 * @property int $sum_bron4
 * @property int $ups
 * @property int $injury_possible
 * @property int $battle_t
 * @property int $hidden
 * @property int $hil
 * @property int $bot_online
 * @property int $bot_room
 * @property int $bot_count
 * @property int $rubashka
 * @property int $hiddenlog
 * @property int $mklevel
 * @property int $runa1
 * @property int $runa2
 * @property int $runa3
 * @property int $fullhptime
 * @property int $uclass
 * @property int $owner
 * @property int $exp
 * @property int $nextup
 * @property int $stats
 * @property int $master
 * @property int $expbonus
 * @property int $stbat
 * @property int $winstbat
 * @property int $win
 * @property int $lose
 * @property int $last_battle
 * @property string $skills
 * @property int $skills_point
 * @property string $passkills
 * @property int $passkills_points
 * @property int $naem_status
 * @property int $naem_id
 * @property int $mfire
 * @property int $mwater
 * @property int $mair
 * @property int $mearth
 * @property int $mlight
 * @property int $mgray
 * @property int $mdark
 * @property string $borndate
 * @property string $borncity
 * @property string $citizen
 * @property string $borntime
 * @property string $skulls
 * @property string $rep_bonus
 * @property string $energy
 * @property string $fullentime
 *
 */
class UserClon extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_clons';
	protected $primaryKey = 'id';
}