<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 15.12.2015
 */

namespace components\Helper\user;

use components\Helper\ItemHelper;
use components\Model\phantom\PhantomCShop;
use components\Model\phantom\PhantomEShop;
use components\Model\phantom\PhantomShop;
use components\Model\User;

class Dummy
{
    public $errors = array();

    public $minu         = 0;
    public $maxu         = 0;
    public $mfkrit       = 0;
    public $mfakrit      = 0;
    public $mfuvorot     = 0;
    public $mfauvorot    = 0;
    public $bron1        = 0;
    public $bron2        = 0;
    public $bron3        = 0;
    public $bron4        = 0;
    public $ab_mf        = 0;
    public $ab_bron      = 0;
    public $ab_uron      = 0;
    public $unik         = 0;
    public $supunik      = 0;
    public $hp           = 0;
    public $mana         = 0;

    //region общие статы
    public $sila         = 0;
    public $lovk         = 0;
    public $inta         = 0;
    public $vinos        = 0;
    public $intel        = 0;
    public $mudra        = 0;
    //endregion

    //region родные статы
    public $own_sila     = 0;
    public $own_lovk     = 0;
    public $own_inta     = 0;
    public $own_vinos    = 0;
    public $own_intel    = 0;
    public $own_mudra    = 0;
    //endregion

    //region общие владения магией
    public $fire         = 0;
    public $water        = 0;
    public $air          = 0;
    public $earth        = 0;
    public $light        = 0;
    public $gray         = 0;
    public $dark         = 0;
    //endregion

    //region родные владения магией
    public $own_fire    = 0;
    public $own_water   = 0;
    public $own_air     = 0;
    public $own_earth   = 0;
    public $own_light   = 0;
    public $own_gray    = 0;
    public $own_dark    = 0;
    //endregion

    //region общие владения оружием
    public $noj         = 0;
    public $mech        = 0;
    public $topor       = 0;
    public $dubina      = 0;
    //endregion

    //region родные владения оружием
    public $own_noj     = 0;
    public $own_mech    = 0;
    public $own_topor   = 0;
    public $own_dubina  = 0;
    //endregion

    public $weapon_type = ItemHelper::WEAPON_DEFAULT;
    public $main_master = 0;

    public $min_udar = 0;
    public $max_udar = 0;

    /** @var User */
    protected $user;
    protected $items = array();
    protected $items_income = array();
    protected $effects = array();

    public $medal1 = false;
    public $medal2 = false;

    protected $sergi       = 0;
    protected $kulon       = 0;
    protected $perchi      = 0;
    protected $weap        = 0;
    protected $bron        = 0;
    protected $r1          = 0;
    protected $r2          = 0;
    protected $r3          = 0;
    protected $helm        = 0;
    protected $shit        = 0;
    protected $boots       = 0;
    protected $nakidka     = 0;
    protected $rubashka    = 0;
    protected $runa1       = 0;
    protected $runa2       = 0;
    protected $runa3       = 0;

    public function __construct(&$user, $items, $effects = array())
    {
        $this->user     = $user;
        $this->items    = $this->items_income = $items;
        $this->effects  = $effects;

        $this->defaultValues();
        $this->process();
        $this->calculate();
    }

    protected function defaultValues()
    {
        foreach (array('sergi','kulon','perchi','weap','bron','r1','r2','r3','runa1','runa2','runa3','helm',
                     'shit','boots','nakidka','rubashka') as $slot) {
            $this->{$slot} = $this->user->{$slot};
        }

        foreach (array('sila','lovk','inta','vinos','intel','mudra','noj','topor','dubina') as $field) {
            $this->{'own_' . $field}  = $this->{$field}  = $this->user->{$field};
        }
        $this->own_mech     = $this->mech   = $this->user->mec;
        $this->own_dark     = $this->dark   = $this->user->mdark;
        $this->own_light    = $this->light  = $this->user->mlight;
        $this->own_gray     = $this->gray   = $this->user->mgray;
        $this->own_fire     = $this->fire   = $this->user->mfire;
        $this->own_water    = $this->water  = $this->user->mwater;
        $this->own_air      = $this->air    = $this->user->mair;
        $this->own_earth    = $this->earth  = $this->user->mearth;
    }

    protected function process()
    {
        foreach ($this->items as $item_id => $item) {
            $this->setupOptions($item_id, $item);
        }

        if($this->weap > 0) {
            $this->prepareWeaponMasters($this->weap);
        }
    }

    /**
     * @param $weapon_id
     *
     * по типу оружия выставляем кол-во владений для подсчета урона
     */
    protected function prepareWeaponMasters($weapon_id)
    {
        $w = $this->getItem($weapon_id);
        switch (true) {
            //нож
            case ($w['otdel'] == ItemHelper::RAZDEL_NOJ):
                $this->weapon_type = ItemHelper::WEAPON_NOJ;
                $this->main_master = $this->noj;
                break;
            //меч
            case ($w['otdel'] == ItemHelper::RAZDEL_MECH):
                $this->weapon_type = ItemHelper::WEAPON_MECH;
                $this->main_master = $this->mech;
                break;
            //топор
            case ($w['otdel'] == ItemHelper::RAZDEL_TOPOR):
                $this->weapon_type = ItemHelper::WEAPON_TOPOR;
                $this->main_master = $this->topor;
                break;
            //дубина
            case ($w['otdel'] == ItemHelper::RAZDEL_DUBINA):
                $this->weapon_type = ItemHelper::WEAPON_DUBINA;
                $this->main_master = $this->dubina;
                break;
            //костыль
            case (in_array($w['prototype'], array(501, 502))):
                $this->weapon_type = ItemHelper::WEAPON_KOSTIL;
                break;
            //елка
            case ($w['otdel'] == ItemHelper::RAZDEL_PLASH && $w['prototype'] >= 55510301 && $w['prototype'] <= 55510401):
                $this->weapon_type = ItemHelper::WEAPON_ELKA;

                $ma = $this->noj;
                if ($ma < $this->topor) { $ma = $this->topor;}
                if ($ma < $this->dubina) { $ma = $this->dubina;}
                if ($ma < $this->mech) { $ma = $this->mech;}

                $this->main_master = $ma;
                break;
            //букет
            case (($w['otdel'] == ItemHelper::RAZDEL_PLASH && $w['prototype'] >= 410001 && $w['prototype'] <= 410030) || $w['minu'] > 0):
                $this->weapon_type = ItemHelper::WEAPON_BUKET;

                $ma = $this->noj;
                if ($ma < $this->topor) { $ma = $this->topor;}
                if ($ma < $this->dubina) { $ma = $this->dubina;}
                if ($ma < $this->mech) { $ma = $this->mech;}

                $this->main_master = $ma;
                break;
        }
    }

    /**
     * @param $item_id
     * @param $item
     *
     * Проходимя по каждому надетому предмету и отнимает то, что он дает, дабы получить исходное состояние
     */
    protected function setupOptions($item_id, $item)
    {
        if(in_array($item['prototype'], array(501, 502))) {
            if($this->shit == $item['id']) {
                $item['minu'] = 0;
                $item['maxu'] = 0;
            } elseif($this->weap == $item['id']) {
                $item['bron1'] = 0;
                $item['bron2'] = 0;
                $item['bron3'] = 0;
                $item['bron4'] = 0;
            }
        }

        $this->own_sila  -= $item['gsila'];
        $this->own_lovk  -= $item['glovk'];
        $this->own_inta  -= $item['ginta'];
        if(isset($item['gvinos'])) {
            $this->own_vinos -= $item['gvinos'];
        }
        $this->own_intel -= $item['gintel'];
        if(isset($item['gmp'])) {
            $this->own_mudra -= $item['gmp'];
        }

        $this->own_fire  -= $item['gfire'];
        $this->own_water -= $item['gwater'];
        $this->own_air   -= $item['gair'];
        $this->own_earth -= $item['gearth'];
        $this->own_light -= $item['glight'];
        $this->own_gray  -= $item['ggray'];
        $this->own_dark  -= $item['gdark'];

        $this->own_noj    -= $item['gnoj'];
        $this->own_mech   -= $item['gmech'];
        $this->own_topor  -= $item['gtopor'];
        $this->own_dubina -= $item['gdubina'];

        if($item['unik'] == 1) {
            $this->unik++;
        } elseif($item['unik'] == 2) {
            $this->supunik++;
        }

        $this->minu         += $item['minu'];
        $this->maxu         += $item['maxu'];
        $this->mfkrit       += $item['mfkrit'];
        $this->mfakrit      += $item['mfakrit'];
        $this->mfuvorot     += $item['mfuvorot'];
        $this->mfauvorot    += $item['mfauvorot'];
        $this->bron1        += $item['bron1'];
        $this->bron2        += $item['bron2'];
        $this->bron3        += $item['bron3'];
        $this->bron4        += $item['bron4'];
        $this->ab_mf        += $item['ab_mf'];
        $this->ab_bron      += $item['ab_bron'];
        $this->ab_uron      += $item['ab_uron'];
        $this->hp           += $item['ghp'];
    }

    public function calculate()
    {
        if($this->weapon_type == ItemHelper::WEAPON_DEFAULT && $this->user->align == 2) {
            $this->min_udar += $this->user->level;
            $this->max_udar += $this->user->level;
        }

        $this->min_udar += round((floor($this->sila / 3) + 1) + $this->user->level + $this->minu * (1 + 0.07 * $this->main_master));
        $this->max_udar += round((floor($this->sila / 3) + 4) + $this->user->level + $this->maxu * (1 + 0.07 * $this->main_master));

        $uvorota    = 0;
        $auvorota   = 0;
        $krita      = 0;
        $akrita     = 0;

        //region валентинки дающие МФ
        if(isset($this->effects[900])) {
            $uvorota    += (int)$this->effects[900]['add_info'];
        }
        if(isset($this->effects[901])) {
            $auvorota   += (int)$this->effects[901]['add_info'];
        }
        if(isset($this->effects[902])) {
            $krita      += (int)$this->effects[902]['add_info'];
        }
        if(isset($this->effects[903])) {
            $akrita     += (int)$this->effects[903]['add_info'];
        }

        //макс МФ
        if(isset($this->effects[904])) {
            $this->ab_mf    += (int)$this->effects[904]['add_info'];
        }
        //макс бронь
        if(isset($this->effects[905])) {
            $this->ab_bron  += (int)$this->effects[905]['add_info'];
        }
        //макс урон
        if(isset($this->effects[906])) {
            $this->ab_uron  += (int)$this->effects[906]['add_info'];
        }

        $this->mfuvorot     += $uvorota + $this->lovk * 5;
        $this->mfauvorot    += $auvorota + $this->lovk * 5 + $this->inta * 2;
        $this->mfkrit       += $krita + $this->inta * 5;
        $this->mfakrit      += $akrita + $this->inta * 5 + $this->lovk * 2;

        $mf_uvorota     = $this->mfuvorot;
        $mf_auvorota    = $this->mfauvorot;
        $mf_krita       = $this->mfkrit;
        $mf_akrita      = $this->mfakrit;

        if($this->ab_mf > 0) {
            $this->getMaxMF($this->mfuvorot, $this->mfauvorot, $this->mfkrit , $this->mfakrit);
        }

        if($this->ab_bron > 0) {
            $this->bron1 += (int)($this->bron1 * ($this->ab_bron / 100));
            $this->bron2 += (int)($this->bron2 * ($this->ab_bron / 100));
            $this->bron3 += (int)($this->bron3 * ($this->ab_bron / 100));
            $this->bron4 += (int)($this->bron4 * ($this->ab_bron / 100));
        }

        if(isset($this->effects[791])) {
            $this->bron1 += (int)($this->bron1 * (15 / 100));
            $this->bron2 += (int)($this->bron2 * (15 / 100));
            $this->bron3 += (int)($this->bron3 * (15 / 100));
            $this->bron4 += (int)($this->bron4 * (15 / 100));
        }

        if($this->ab_uron > 0) {
            $this->min_udar += (int)($this->min_udar * ($this->ab_uron / 100));
            $this->max_udar += (int)($this->max_udar * ($this->ab_uron / 100));
        }

        if(isset($this->effects[792])) {
            $this->min_udar += (int)($this->min_udar * (5 / 100));
            $this->max_udar += (int)($this->max_udar * (5 / 100));
        }

        if(strpos($this->user->medals, 'k202;') !== false) {
            $this->unik++;
            $this->medal1 = true;
        }

        if(strpos($this->user->medals, 'k203;') !== false) {
            $this->supunik++;
            $this->medal2 = true;
        }

        $ratio_list = array();
        if($this->unik >= 13) {
            $ratio_list[] = 0.04;
        } elseif($this->unik >= 12) {
            $ratio_list[] = 0.03;
        } elseif($this->unik >= 9) {
            $ratio_list[] = 0.02;
        } elseif($this->unik >= 6) {
            $ratio_list[] = 0.01;
        }

        if($this->supunik >= 13) {
            $ratio_list[] = 0.08;
        } elseif($this->supunik >= 12) {
            $ratio_list[] = 0.06;
        } elseif($this->supunik >= 9) {
            $ratio_list[] = 0.04;
        } elseif($this->supunik >= 6) {
            $ratio_list[] = 0.02;
        }

        if(isset($this->effects[793])) {
            $ratio_list[] = 0.01;
        }

        foreach ($ratio_list as $ratio) {
            $this->mfuvorot     += round($mf_uvorota * $ratio);
            $this->mfauvorot    += round($mf_auvorota * $ratio);
            $this->mfkrit       += round($mf_krita * $ratio);
            $this->mfakrit      += round($mf_akrita * $ratio);

            $this->ab_mf += $ratio * 100;
        }
    }

    /**
     * @param $id
     * @return null
     */
    public function getItem($id)
    {
        return isset($this->items[$id]) ? $this->items[$id] : null;
    }

    /**
     * @param $uv
     * @param $auv
     * @param $kr
     * @param $akr
     *
     * Изменяем максимальый МФ
     */
    protected function getMaxMF(&$uv, &$auv, &$kr, &$akr)
    {
        if($uv >= $auv && $uv >= $kr && $uv >= $akr) {
            $uv += (int)($uv * ($this->ab_mf / 100));
            return;
        }

        if($auv >= $uv && $auv >= $kr && $auv >= $akr ) {
            $auv += (int)($auv * ($this->ab_mf / 100));
            return;
        }

        if($kr >= $uv && $kr >= $auv && $kr >= $akr ) {
            $kr += (int)($kr * ($this->ab_mf / 100));
            return;
        }

        if($akr >= $uv && $akr >= $auv && $akr >= $kr) {
            $akr += (int)($akr * ($this->ab_mf / 100));
            return;
        }
    }

    /**
     * @return array
     *
     * Родные статы
     */
    public function getAllOwn()
    {
        return array(
            'sila'      => (int)$this->own_sila,
            'lovk'      => (int)$this->own_lovk,
            'inta'      => (int)$this->own_inta,
            'vinos'     => (int)$this->own_vinos,
            'intel'     => (int)$this->own_intel,
            'mudra'     => (int)$this->own_mudra,
            'noj'       => (int)$this->own_noj,
            'mech'      => (int)$this->own_mech,
            'topor'     => (int)$this->own_topor,
            'dubina'    => (int)$this->own_dubina,
            'fire'      => (int)$this->own_fire,
            'air'       => (int)$this->own_air,
            'earth'     => (int)$this->own_earth,
            'water'     => (int)$this->own_water,
            'light'     => (int)$this->own_light,
            'gray'      => (int)$this->own_gray,
            'dark'      => (int)$this->own_dark,
        );
    }

    /**
     * @return array
     *
     * Итоговые статы
     */
    public function getAllTotal()
    {
        return array(
            'sila'      => (int)$this->sila,
            'lovk'      => (int)$this->lovk,
            'inta'      => (int)$this->inta,
            'vinos'     => (int)$this->vinos,
            'intel'     => (int)$this->intel,
            'mudra'     => (int)$this->mudra,
            'noj'       => (int)$this->noj,
            'mech'      => (int)$this->mech,
            'topor'     => (int)$this->topor,
            'dubina'    => (int)$this->dubina,
            'fire'      => (int)$this->fire,
            'air'       => (int)$this->air,
            'earth'     => (int)$this->earth,
            'water'     => (int)$this->water,
            'light'     => (int)$this->light,
            'gray'      => (int)$this->gray,
            'dark'      => (int)$this->dark,
        );
    }

    /**
     * @param $ids
     * @return array
     *
     * Получаем протатипы предметов
     */
    protected function getPrototypes($ids)
    {
        $Items = PhantomShop::getByIds($ids);

        $ids = array_diff($ids, array_keys($Items));
        if(!empty($ids)) {
            $Items += PhantomEShop::getByIds($ids);
        }

        $ids = array_diff($ids, array_keys($Items));
        if(!empty($ids)) {
            $Items += PhantomCShop::getByIds($ids);
        }

        return $Items;
    }

    /**
     * @param $item_id
     *
     * Снимаем предмет
     */
    protected function takeOff($item_id)
    {
        $item = $this->getItem($item_id);
        $slot = $this->getSlot($item['otdel']);

        $this->{$slot} = 0;
        unset($this->items[$item_id]);

        if(in_array($item['prototype'], array(501, 502))) {
            if($this->user->shit == $item['id']) {
                $item['minu'] = 0;
                $item['maxu'] = 0;
            } elseif($this->user->weap == $item['id']) {
                $item['bron1'] = 0;
                $item['bron2'] = 0;
                $item['bron3'] = 0;
                $item['bron4'] = 0;
            }
        }

        $this->sila  -= $item['gsila'];
        $this->lovk  -= $item['glovk'];
        $this->inta  -= $item['ginta'];
        if(isset($item['gvinos'])) {
            $this->vinos -= $item['gvinos'];
        }
        $this->intel -= $item['gintel'];
        if(isset($item['gmp'])) {
            $this->mudra -= $item['gmp'];
        }

        $this->fire  -= $item['gfire'];
        $this->water -= $item['gwater'];
        $this->air   -= $item['gair'];
        $this->earth -= $item['gearth'];
        $this->light -= $item['glight'];
        $this->gray  -= $item['ggray'];
        $this->dark  -= $item['gdark'];

        $this->noj    -= $item['gnoj'];
        $this->mech   -= $item['gmech'];
        $this->topor  -= $item['gtopor'];
        $this->dubina -= $item['gdubina'];

        if($item['unik'] == 1) {
            $this->unik--;
        } elseif($item['unik'] == 2) {
            $this->supunik--;
        }

        $this->minu         -= $item['minu'];
        $this->maxu         -= $item['maxu'];
        $this->mfkrit       -= $item['mfkrit'];
        $this->mfakrit      -= $item['mfakrit'];
        $this->mfuvorot     -= $item['mfuvorot'];
        $this->mfauvorot    -= $item['mfauvorot'];
        $this->bron1        -= $item['bron1'];
        $this->bron2        -= $item['bron2'];
        $this->bron3        -= $item['bron3'];
        $this->bron4        -= $item['bron4'];
        $this->ab_mf        -= $item['ab_mf'];
        $this->ab_bron      -= $item['ab_bron'];
        $this->ab_uron      -= $item['ab_uron'];
        $this->hp           -= $item['ghp'];
    }

    /**
     * @param $item
     * @return string
     *
     * Надеваем предмет
     */
    public function putOn($item)
    {
        $slot = $this->getSlot($item['otdel']);
        if($this->{$slot} > 0) {
            $this->takeOff($this->{$slot});
            $this->{$slot} = $item['id'];
        }
        $this->items[$item['id']] = $item;

        if(in_array($item['prototype'], array(501, 502))) {
            if($this->shit == $item['id']) {
                $item['minu'] = 0;
                $item['maxu'] = 0;
            } elseif($this->weap == $item['id']) {
                $item['bron1'] = 0;
                $item['bron2'] = 0;
                $item['bron3'] = 0;
                $item['bron4'] = 0;
            }
        }

        $this->sila  += $item['gsila'];
        $this->lovk  += $item['glovk'];
        $this->inta  += $item['ginta'];
        if(isset($item['gvinos'])) {
            $this->vinos += $item['gvinos'];
        }
        $this->intel += $item['gintel'];
        if(isset($item['gmp'])) {
            $this->mudra += $item['gmp'];
        }

        $this->fire  += $item['gfire'];
        $this->water += $item['gwater'];
        $this->air   += $item['gair'];
        $this->earth += $item['gearth'];
        $this->light += $item['glight'];
        $this->gray  += $item['ggray'];
        $this->dark  += $item['gdark'];

        $this->noj    += $item['gnoj'];
        $this->mech   += $item['gmech'];
        $this->topor  += $item['gtopor'];
        $this->dubina += $item['gdubina'];

        if($item['unik'] == 1) {
            $this->unik++;
        } elseif($item['unik'] == 2) {
            $this->supunik++;
        }

        $this->minu         += $item['minu'];
        $this->maxu         += $item['maxu'];
        $this->mfkrit       += $item['mfkrit'];
        $this->mfakrit      += $item['mfakrit'];
        $this->mfuvorot     += $item['mfuvorot'];
        $this->mfauvorot    += $item['mfauvorot'];
        $this->bron1        += $item['bron1'];
        $this->bron2        += $item['bron2'];
        $this->bron3        += $item['bron3'];
        $this->bron4        += $item['bron4'];
        $this->ab_mf        += $item['ab_mf'];
        $this->ab_bron      += $item['ab_bron'];
        $this->ab_uron      += $item['ab_uron'];
        $this->hp           += $item['ghp'];
    }

    protected function getSlot($category_id)
    {
        $slot = ItemHelper::getSlot($category_id);
        if($slot == 'r') {
            switch (true) {
                case empty($this->r1):
                    $slot = 'r1';
                    break;
                case empty($this->r2):
                    $slot = 'r2';
                    break;
                case empty($this->r3):
                    $slot = 'r3';
                    break;
                default:
                    $slot = 'r1';
                    break;
            }
        }
        if($slot == 'runa') {
            switch (true) {
                case empty($this->runa1):
                    $slot = 'runa1';
                    break;
                case empty($this->runa2):
                    $slot = 'runa2';
                    break;
                case empty($this->runa3):
                    $slot = 'runa3';
                    break;
                default:
                    $slot = 'runa1';
                    break;
            }
        }

        return $slot;
    }

    /**
     * @param $field
     * @param $value
     *
     * Изменяем родной стат
     */
    public function changeOwn($field, $value)
    {
        $current = $this->{'own_' . $field};
        $diff = $current - $value;
        if($diff > 0) {
            $this->{'own_' . $field} -= $diff;
            $this->{$field} -= $field;
        } else {
            $this->{'own_' . $field} += $diff * (-1);
            $this->{$field} += $diff * (-1);
        }
    }

    public function writeToUser()
    {
        foreach (array('sergi','kulon','perchi','weap','bron','r1','r2','r3','runa1','runa2','runa3','helm',
                     'shit','boots','nakidka','rubashka') as $slot) {
            $this->user->{$slot} = $this->{$slot};
        }

        foreach (array('sila','lovk','inta','vinos','intel','mudra','noj','topor','dubina') as $field) {
            $this->user->{$field} = $this->{$field};
        }
        $this->user->mec    = $this->mech;
        $this->user->mdark  = $this->dark;
        $this->user->mlight = $this->light;
        $this->user->mgray  = $this->gray;
        $this->user->mfire  = $this->fire;
        $this->user->mwater = $this->water;
        $this->user->mair   = $this->air;
        $this->user->mearth = $this->earth;

        $this->user->maxhp = $this->hp + $this->vinos * 6 + $this->user->bpbonushp;

        return true;
    }

    public function canKeepItems()
    {
        $min_need = array();
        foreach ($this->items as $item_id => $item) {
            foreach (array('sila','lovk','inta','vinos','intel','mudra','noj','topor','dubina','mech','fire','water','air','earth','dark','light','gray') as $field) {
                if(!isset($min_need[$field]) || $min_need[$field] < $item['n' . $field]) {
                    $min_need[$field] = $item['n' . $field];
                }
            }
        }

        foreach ($min_need as $field => $value) {
            if($this->{$field} < $value) {
                $this->errors[] = 'У вас недостаточно статов для поддержки комплекта. Если сохранить, все рухнет :(';
                return false;
            }
        }

        return true;
    }
}