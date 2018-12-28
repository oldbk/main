<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 15.12.2015
 */

namespace components\Helper\user;

use components\Helper\ItemHelper;
use components\Model\Inventory;
use components\Model\User;
use components\Model\user\UserPhantom;

class Phantom extends Dummy
{
    protected $prototype_ids = array();
    /** @var UserPhantom */
    protected $user;

    protected function process()
    {
        parent::process();

        $ItemsOrigin = array();
        if($this->prototype_ids) {
            $ItemsOrigin = $this->getPrototypes($this->prototype_ids);
        }

        foreach ($this->items as $item_id => $item) {
            $bonus_mf = $bonus_stats = $bonus_master = $bonus_minu = $bonus_maxu = 0;

            if(!isset($ItemsOrigin[$item['prototype']])) {
                continue;
            }
            $prototype = $ItemsOrigin[$item['prototype']];

            foreach (array('mfkrit', 'mfakrit', 'mfuvorot', 'mfauvorot') as $field) {
                $bonus_mf += $this->bonus($item, $prototype, $field);;
            }
            foreach (array('gsila', 'glovk', 'ginta', 'gvinos', 'gintel', 'gmudra') as $field) {
                $bonus_stats += $this->bonus($item, $prototype, $field);
            }
            foreach (array('gfire','gwater','gair','gearth','glight','ggray','gdark','gnoj','gmech','gtopor','gdubina') as $field) {
                $bonus_master += $this->bonus($item, $prototype, $field);
            }

            $bonus_list = array(
                'mf'        => $bonus_mf,
                'master'    => $bonus_master,
                'stats'     => $bonus_stats,
                'hp'        => $this->bonus($item, $prototype, 'ghp'),
                'mp'        => $this->bonus($item, $prototype, 'gmp'),
                'extra_needs' => array(
                    'nsila'    => 0,
                    'nlovk'    => 0,
                    'ninta'    => 0,
                    'nvinos'   => 0,

                    'nnoj'     => 0,
                    'nmech'    => 0,
                    'ntopor'   => 0,
                    'ndubina'  => 0,
                )
            );
            foreach (array('bron1','bron2','bron3','bron4','minu','maxu','ab_mf','ab_uron','ab_bron') as $field) {
                $bonus_list[$field] = $this->bonus($item, $prototype, $field);
            }

            $this->items[$item_id]['bonus_list'] = $bonus_list;
        }
    }

    private function bonus($item, $prototype, $field)
    {
        $b = 0;
        if(isset($item[$field])) {
            $b = $item[$field];
            if(isset($prototype[$field])) {
                $b -= $prototype[$field];
            }
        }

        return $b;
    }

    protected function setupOptions($item_id, $item)
    {
        parent::setupOptions($item_id, $item);

        $total_mf = $total_stats = $total_master = 0;
        foreach (array('mfkrit', 'mfakrit', 'mfuvorot', 'mfauvorot') as $field) {
            if(isset($item[$field])) {
                $total_mf += (int)$item[$field];
            }
        }

        foreach (array('gsila', 'glovk', 'ginta', 'gvinos', 'gintel', 'gmudra') as $field) {
            if(isset($item[$field])) {
                $total_stats += (int)$item[$field];
            }
        }

        foreach (array('gfire', 'gwater', 'gair', 'gearth', 'glight', 'ggray', 'gdark') as $field) {
            if(isset($item[$field])) {
                $total_master += (int)$item[$field];
            }
        }
        foreach (array('gnoj', 'gmech', 'gtopor', 'gdubina') as $field) {
            if(isset($item[$field])) {
                $total_master += (int)$item[$field];
            }
        }

        $this->prototype_ids[] = $item['prototype'];

        $this->items[$item_id]['total_mf']      = (int)$total_mf;
        $this->items[$item_id]['total_stats']   = (int)$total_stats;
        $this->items[$item_id]['total_master']  = (int)$total_master;
    }

    public function getUser()
    {
        return array(
            'sila'      => (int)$this->user->sila,
            'lovk'      => (int)$this->user->lovk,
            'inta'      => (int)$this->user->inta,
            'vinos'     => (int)$this->user->vinos,
            'intel'     => (int)$this->user->intel,
            'mudra'     => (int)$this->user->mudra,

            'fire'      => (int)$this->user->mfire,
            'water'     => (int)$this->user->mwater,
            'air'       => (int)$this->user->mair,
            'earth'     => (int)$this->user->mearth,
            'light'     => (int)$this->user->mlight,
            'gray'      => (int)$this->user->mgray,
            'dark'      => (int)$this->user->mdark,

            'noj'       => (int)$this->user->noj,
            'mech'      => (int)$this->user->mec,
            'topor'     => (int)$this->user->topor,
            'dubina'    => (int)$this->user->dubina,

            'weap_id'   => (int)$this->user->weap,
            'shit_id'   => (int)$this->user->shit,

            'align'     => (int)$this->user->align,

            'level'     => (int)$this->user->level,
            'bpbonushp' => (int)$this->user->bpbonushp,
        );
    }

    public function getEffects()
    {
        $returned = array();

        foreach ($this->effects as $effect_id => $effect) {
            $returned[$effect_id] = $effect['add_info'];
        }

        return $returned;
    }

    public function getOwn()
    {
        return array(
            'sila'  => (int)$this->own_sila,
            'lovk'  => (int)$this->own_lovk,
            'inta'  => (int)$this->own_inta,
            'vinos' => (int)$this->own_vinos,
            'intel' => (int)$this->own_intel,
            'mudra' => (int)$this->own_mudra,

            'noj'   => (int)$this->own_noj,
            'mech'  => (int)$this->own_mech,
            'topor' => (int)$this->own_topor,
            'dubina'=> (int)$this->own_dubina,

            'fire'  => (int)$this->own_fire,
            'water' => (int)$this->own_water,
            'air'   => (int)$this->own_air,
            'earth' => (int)$this->own_earth,
            'light' => (int)$this->own_light,
            'dark'  => (int)$this->own_dark,
            'gray'  => (int)$this->own_gray,
        );
    }

    private $_new_prototypes = array();
    public function checkNewItems($ids)
    {
        $this->_new_prototypes = $this->getPrototypes($ids);
        if(count($this->_new_prototypes) != count($ids)) {
            return false;
        }

        return true;
    }

    public function getPrice()
    {
        $price = 0;
        foreach ($this->_new_prototypes as $item_id => $item) {
            $price += round($item['cost'] / 100 * 10, 2);
        }

        return $price;
    }

    public function createItem($item)
    {
        $prototype = $this->_new_prototypes[$item['id']];

        $slot = ItemHelper::getSlot($prototype['otdel']);
        $item_id = $this->user->{$slot};

        $dressed = $this->getItem($item_id);

        if(!$this->validateNewItem($item, $prototype, $dressed)) {
            return false;
        }

        foreach (array('sila','lovk','inta','intel','mp','hp') as $field) {
            $dressed['g' . $field] = $item['g' . $field];
        }
        foreach (array('sila','lovk','inta','vinos','intel','mudra','level','align') as $field) {
            $dressed['n' . $field] = $item['n' . $field];
        }
        foreach (array('fire','water','earth','air','dark','light','gray','noj','mech','topor','dubina') as $field) {
            $dressed['n' . $field] = $item['n' . $field];
            $dressed['g' . $field] = $item['g' . $field];
        }
        foreach (array('bron1','bron2','bron3','bron4','mfkrit','mfakrit','mfuvorot','mfauvorot','img','massa',
                     'minu','maxu','ab_mf','ab_bron','ab_uron') as $field) {
            $dressed[$field] = $item[$field];
        }
        $dressed['prototype'] = $prototype['id'];
        $dressed['name'] = $prototype['name'];
        $dressed['owner'] = $this->user->id;
        $dressed['charka'] = null;
        $dressed['art_param'] = null;
        $dressed['goden'] = 1;
        $dressed['maxdur'] = 1000;

        $DateTime = new \DateTime();
        $DateTime->modify('+2 hour');
        $dressed['dategoden'] = $DateTime->getTimestamp();

        unset($dressed['id']);
        unset($dressed['bonus_list']);
        unset($dressed['total_mf']);
        unset($dressed['total_stats']);
        unset($dressed['total_master']);
        try {
            if($id = Inventory::insert($dressed)) {
                Inventory::update(array('dressed' => 0), 'id = ? and owner = ?', array($this->user->{$slot}, $this->user->id));
                $dressed['id'] = $id;
                $this->putOn($dressed);
                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
        }

        return false;
    }

    protected function validateNewItem($item, $prototype, $dressed_item)
    {
        if($prototype['otdel'] != $item['otdel']) {
            return false;
        }

        $total_new_stats = 0;
        $total_new_master = 0;
        $total_proto_stats = 0;
        $total_proto_master = 0;

        foreach (array('sila','lovk','inta','mp','intel','mp') as $field) {
            if($item['g' . $field] > 0 && $prototype['g' . $field] == 0) {
                $this->errors[] = 'Вы не можете повысить параметр, который не дает предмет';
                return false;
            }

            $total_new_stats += (int)$item['g' . $field];
            $total_proto_stats += (int)$prototype['g' . $field];
        }

        foreach (array('noj','mech','topor','dubina','fire','water','air','earth','dark','light','gray') as $field) {
            if($item['g' . $field] > 0 && $prototype['g' . $field] == 0) {
                $this->errors[] = 'Вы не можете повысить владение, которое не дает предмет';
                return false;
            }

            $total_new_master += (int)$item['g' . $field];
            $total_proto_master += (int)$prototype['g' . $field];
        }

        if($dressed_item['bonus_list']['stats'] + $total_proto_stats != $total_new_stats && $total_proto_stats > 0) {
            $this->errors[] = 'Кол-во статов не сходится';
            return false;
        }

        if($dressed_item['bonus_list']['master'] + $total_proto_master != $total_new_master && $total_proto_master > 0) {
            $this->errors[] = 'Кол-во владений не сходится';
            return false;
        }

        if($dressed_item['bonus_list']['hp'] + $prototype['ghp'] != $item['ghp']) {
            $this->errors[] = 'Кол-во жизни не сходится';
            return false;
        }

        foreach (array('bron1','bron2','bron3','bron4','minu','maxu','ab_mf','ab_uron','ab_bron') as $field) {
            if($dressed_item['bonus_list'][$field] + $prototype[$field] != $item[$field]) {
                $this->errors[] = sprintf('Задано некорректное значение: %s - %s. Бонус лист: %d. Прото: %d',
                    $field, $item[$field], $dressed_item['bonus_list'][$field], $prototype[$field]);
                return false;
            }
        }

        $mf_new = 0; $mf_old = 0;
        foreach (array('mfkrit', 'mfakrit', 'mfuvorot', 'mfauvorot') as $field) {
            $mf_new += (int)$item[$field];
            $mf_old += (int)$prototype[$field];
        }

        if($dressed_item['bonus_list']['mf'] + $mf_old != $mf_new && $mf_old > 0) {
            $this->errors[] = sprintf('Кол-во МФ не сходится. Бонус %d. Прото %d. Новые %d',
                $dressed_item['bonus_list']['mf'], $mf_old, $mf_new);
            return false;
        }

        if($prototype['nlevel'] > $this->user->level) {
            $this->errors[] = 'Уровень маловат';
            return false;
        }

        if($prototype['nalign'] > 0 && $prototype['nalign'] != $this->user->getAlignForItems()) {
            $this->errors[] = 'Неподходящая склонность';
            return false;
        }

        if($prototype['nsex'] > 0 && $prototype['nsex'] != $this->user->sex) {
            $this->errors[] = 'Неподходящий пол';
            return false;
        }

        return true;
    }

    public function getDressedView()
    {
        $fields = array('name', 'massa', 'prototype', 'id', 'minu', 'maxu', 'bron1', 'bron2', 'bron3', 'bron4', 'gsila', 'glovk',
            'ginta', 'gintel', 'gfire', 'gwater', 'gair', 'gearth', 'glight', 'ggray', 'gdark', 'gnoj', 'gmech',
            'gtopor' ,'gdubina', 'unik', 'mfkrit', 'mfakrit', 'mfuvorot', 'mfauvorot', 'ab_mf', 'ab_uron', 'ab_bron',
            'img', 'otdel', 'nlevel', 'nsila', 'nlovk', 'ninta', 'nvinos', 'nintel', 'nmudra', 'nnoj', 'ntopor',
            'ndubina', 'nmech', 'nalign', 'nfire', 'nwater', 'nair', 'nearth', 'nlight', 'ngray', 'ndark', 'ghp', 'gmp',
            'bonus_list', 'total_stats', 'total_mf', 'total_master');

        $returned = array();
        foreach ($this->items as $item) {
            $returned[$item['id']] = array_intersect_key($item, array_combine($fields, range(0, count($fields) - 1)));
        }

        return $returned;
    }
}