<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 14.04.2016
 */

namespace components\Component\Loto\items;

use components\Helper\item\ItemItem;
use components\Helper\ItemHelper;
use components\models\NewDelo;
use components\models\User;

class Item extends BaseItem
{
    protected $vaucher = array(200001,200002,200005,200010,200025,200050,200100,200250,200500);

    protected $prototype;

    public function __construct($app, $loto_id, array $loto_item, array $prototype)
    {
        $this->prototype = $prototype;

        parent::__construct($app, $loto_id, $loto_item);
    }

    protected function prepareItem($loto_item)
    {
        $item = ItemHelper::baseFromPrototype(array(), $this->prototype);

        $other_settings = array();
        if(isset($loto_item['info']['other_settings']) && $loto_item['info']['other_settings']) {
            $other_settings = $this->prepareOtherSettings($loto_item['info']);
        }
        $item = array_merge($item, $other_settings);

        if($loto_item['info']['is_mf']) {
            $item = ItemHelper::makeUnikFromArray($item);
        }

       return array_merge($item, parent::prepareItem($loto_item));
    }

    protected function prepareOtherSettings($item_info = array())
    {
        $other = array();

        $fields = array('goden', 'ekr_flag');
        foreach ($fields as $field) {
            if(!isset($item_info[$field])) {
                continue;
            }

            switch ($field) {
                case 'goden':
                    $DateTime = new \DateTime();
                    $DateTime->modify(sprintf('+%d days', $item_info['goden']));
                    $other['goden'] = $item_info['goden'];
                    $other['dategoden'] = $DateTime->getTimestamp();
                    break;
                case 'ekr_flag':
                    if($item_info['ekr_flag'] == 2 && $this->prototype['unikflag'] > 0 && $this->prototype['unikflag'] > $this->item['ecost']) {
                        $other['ecost'] = $this->prototype['unikflag'];
                    }
                    $other[$field] = $item_info[$field];
                    break;
                default:
                    $other[$field] = $item_info[$field];
                    break;
            }
        }
        if(in_array($this->prototype['id'], $this->vaucher)) {
            $other['unik'] = 2;
            $other['present'] = 'Лотерея';
        }

        return $other;
    }

    /**
     * @param array|User $owner
     * @return bool
     */
    public function give($owner)
    {
        if(is_array($owner)) {
            $owner = new User($owner);
        }

        try {
            $GiveItem = new ItemItem($owner, $this->item);
			$item_ids = $GiveItem->give();
            if(!$item_ids) {
                throw new \Exception;
            }

            $_data = array(
                'target_login'          => 'Лоттерея',
                'type'                  => NewDelo::TYPE_LOTO_ITEM,
            );

            if(!$GiveItem->newDeloGive($_data)) {
                throw new \Exception;
            }

            if(!$this->sendMessage($owner)) {
                throw new \Exception;
            }
        } catch (\Exception $ex) {
            return false;
        }

        unset($owner);
        return true;
    }

    public function getViewItems()
    {
        $response = parent::getViewItems();
        foreach ($response as $key => $_item) {
            $response[$key]['item_id'] = $_item['prototype'];
        }

        return $response;
    }
}