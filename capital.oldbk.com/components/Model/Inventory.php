<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;
use components\Component\VarDumper;
use components\Helper\ItemHelper;

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
class Inventory extends AbstractCapitalModel
{
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
     * @param string $className
     * @return Inventory
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function fieldMap()
    {
        return array(
            'id', 'name', 'duration', 'maxdur', 'cost', 'owner', 'nlevel', 'nsila', 'nlovk', 'ninta', 'nvinos',
            'nintel', 'nmudra', 'nnoj','ntopor', 'ndubina', 'nmech', 'nalign', 'minu', 'maxu', 'gsila', 'glovk',
            'ginta', 'gintel', 'ghp', 'mfkrit', 'mfakrit', 'mfuvorot', 'mfauvorot', 'gnoj', 'gtopor', 'gdubina',
            'gmech', 'img', 'text', 'dressed', 'bron1', 'bron2', 'bron3', 'bron4', 'dategoden', 'magic', 'type',
            'present', 'sharped', 'massa', 'goden', 'needident', 'nfire', 'nwater', 'nair', 'nearth', 'nlight',
            'ngray', 'ndark', 'gfire', 'gwater', 'gair', 'gearth', 'glight', 'ggray', 'gdark', 'letter', 'isrep',
            'update', 'setsale', 'prototype', 'otdel', 'bs', 'gmp', 'includemagic', 'includemagicdex',
            'includemagicmax', 'includemagicname', 'includemagicuses', 'includemagiccost', 'includemagicekrcost',
            'gmeshok', 'tradesale', 'karman', 'stbonus', 'upfree', 'ups', 'mfbonus', 'mffree', 'type3_updated',
            'bs_owner', 'nsex', 'present_text', 'add_time', 'labonly', 'labflag', 'prokat_idp', 'prokat_do',
            'arsenal_klan', 'arsenal_owner', 'repcost', 'up_level', 'ecost', 'group', 'ekr_up', 'unik', 'add_pick',
            'pick_time', 'sowner', 'idcity', 'battle', 't_id', 'ab_mf', 'ab_bron', 'ab_uron', 'art_param', 'charka',
        );
    }

    public static function tableName()
    {
        return 'inventory';
    }

    public static function pkField()
    {
        return 'id';
    }

    public function getPk()
    {
        return $this->id;
    }

    /**
     * @param User $user
     * @return Inventory
     */
    public static function undressAll(&$user)
    {
        $ItemList = static::findAll('`t`.dressed = ? and `t`.owner = ?', array(1, $user->id))->asArray();
        $model = static::model();

        $undress_stats = array();
        foreach ($ItemList as $item) {
            foreach (array_keys($model->mapToUser) as $stat) {
                if(!isset($undress_stats[$stat])) {
                    $undress_stats[$stat] = 0;
                }

                $undress_stats[$stat] += (int)$item[$stat];
            }

            if($item['prototype'] == 55510351) {
                $user->expbonus -= 0.1;
            }
            if($item['prototype'] == 55510352) {
                $user->expbonus -= 0.3;
		$user->rep_bobus -= 0.2;
            }
            if($item['prototype'] == 410027) {
                $user->expbonus -= 0.1;
		$user->rep_bobus -= 0.1;

            }
            if($item['prototype'] == 410028) {
                $user->expbonus -= 0.3;
		$user->rep_bobus -= 0.2;
            }
        }
        foreach ($undress_stats as $stat => $value) {
            $user->takeParam($model->mapToUser[$stat], $value);
        }
        if(count($ItemList) > 0) {
            $user
                ->clearMaxHp()
                ->clearSlots()
                ->save();
           // static::update(array('dressed' => 0), 'owner = ?', array($user->id));
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
        $item = static::find('`t`.dressed = 1 and `t`.owner = ?', array($user_id), array('id'))->asArray();

        return !empty($item);
    }
}