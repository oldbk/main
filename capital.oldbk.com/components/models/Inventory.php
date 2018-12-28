<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\Component\VarDumper;
use components\Helper\ItemHelper;
use components\models\_base\BaseModal;

/**
 * Class Inventory
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $name
 * @property int $duration
 * @property int $maxdur
 * @property int $cost
 * @property int $owner
 * @property int $nlevel
 * @property int $nsila
 * @property int $nlovk
 * @property int $ninta
 * @property int $nvinos
 * @property int $nintel
 * @property int $nmudra
 * @property int $nnoj
 * @property int $ntopor
 * @property int $ndubina
 * @property int $nmech
 * @property int $nalign
 * @property int $minu
 * @property int $maxu
 * @property int $gsila
 * @property int $glovk
 * @property int $ginta
 * @property int $gintel
 * @property int $ghp
 * @property int $mfkrit
 * @property int $mfakrit
 * @property int $mfuvorot
 * @property int $mfauvorot
 * @property int $gnoj
 * @property int $gtopor
 * @property int $gdubina
 * @property int $gmech
 * @property string $img
 * @property string $text
 * @property boolean $dressed
 * @property int $bron1
 * @property int $bron2
 * @property int $bron3
 * @property int $bron4
 * @property int $dategoden
 * @property int $magic
 * @property int $type
 * @property string $present
 * @property boolean $sharped
 * @property float $massa
 * @property int $goden
 * @property boolean $needident
 * @property int $nfire
 * @property int $nwater
 * @property int $nair
 * @property int $nearth
 * @property int $nlight
 * @property int $ngray
 * @property int $ndark
 * @property int $gfire
 * @property int $gwater
 * @property int $gair
 * @property int $gearth
 * @property int $glight
 * @property int $ggray
 * @property int $gdark
 * @property string $letter
 * @property boolean $isrep
 * @property int $update
 * @property float $setsale
 * @property int $prototype
 * @property string $otdel
 * @property boolean $bs
 * @property int $gmp
 * @property int $includemagic
 * @property int $includemagicdex
 * @property int $includemagicmax
 * @property string $includemagicname
 * @property int $includemagicuses
 * @property float $includemagiccost
 * @property float $includemagicekrcost
 * @property int $gmeshok
 * @property float $tradesale
 * @property boolean $karman
 * @property int $stbonus
 * @property int $upfree
 * @property int $ups
 * @property int $mfbonus
 * @property int $mffree
 * @property boolean $type3_updated
 * @property int $bs_owner
 * @property int $nsex
 * @property string $present_text
 * @property int $add_time
 * @property boolean $labonly
 * @property boolean $labflag
 * @property int $prokat_idp
 * @property int $prokat_do
 * @property string $arsenal_klan
 * @property int $arsenal_owner
 * @property int $repcost
 * @property int $up_level
 * @property float $ecost
 * @property int $group
 * @property string $ekr_up
 * @property int $unik
 * @property string $add_pick
 * @property int $pick_time
 * @property int $sowner
 * @property int $idcity
 * @property int $battle
 * @property int $t_id
 * @property int $ab_mf
 * @property int $ab_bron
 * @property int $ab_uron
 * @property string $art_param
 * @property string $charka
 */
class Inventory extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'inventory';
	protected $primaryKey = 'id';

    private $mapToUser = array(
        'gsila'     => 'sila',
        'glovk'     => 'lovk',
        'ginta'     => 'inta',
        'gintel'    => 'intel',
        'gnoj'      => 'noj',
        'gtopor'    => 'topor',
        'gdubina'   => 'dubina',
        'gmech'     => 'mec',
        'gfire'     => 'mfire',
        'gwater'    => 'mwater',
        'gair'      => 'mair',
        'gearth'    => 'mearth',
        'glight'    => 'mlight',
        'ggray'     => 'mgray',
        'gdark'     => 'mdark',
        'gmp'       => 'mudra'
    );

    /**
     * @param User $user
     */
    public static function undressAll(&$user)
    {
		$ItemList = static::whereRaw('dressed = ? and owner = ?', [1, $user->id])->get()->toArray();
		$model = new static();

        $undress_stats = array();
        foreach ($ItemList as $item) {
            foreach (array_keys($model->mapToUser) as $stat) {
                if(!isset($undress_stats[$stat])) {
                    $undress_stats[$stat] = 0;
                }

                $undress_stats[$stat] += (int)$item[$stat];
            }

            if($item['prototype'] == 55510351 ) {
                $user->expbonus -= 0.1;
            }
            if($item['prototype'] == 55510352) {
                $user->expbonus -= 0.3;
		$user->rep_bonus -= 0.2;
            }
            if($item['prototype'] == 410027) {
                $user->expbonus -= 0.1;
		$user->rep_bonus -= 0.1;
            }
            if($item['prototype'] == 410028) {
                $user->expbonus -= 0.3;
		$user->rep_bonus -= 0.2;
            }

        }
        foreach ($undress_stats as $stat => $value) {
            $user->takeParam($model->mapToUser[$stat], $value);
        }
        if(count($ItemList) > 0) {
            $user
                ->clearMaxHp()
                ->clearMaxMp()
                ->clearSlots()
                ->save();

            static::whereRaw('dressed = ? and owner = ?', [1, $user->id])
				->update(['dressed' => 0]);
		}

        unset($ItemList);
    }

    /**
     * @param $user_id
     * @return bool
     */
    public static function isDressed($user_id)
    {
    	$count = static::whereRaw('dressed = 1 and owner = ?', [$user_id])->count();

        return $count > 0;
    }
}