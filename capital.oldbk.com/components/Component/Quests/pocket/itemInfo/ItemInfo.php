<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Quests\pocket\itemInfo;

use components\Component\Quests\object\Part;
use components\Component\Quests\object\Reward;
use components\Component\Quests\object\Take;
use components\Component\VarDumper;
use components\Helper\FileHelper;
use components\Helper\item\ItemItem;
use components\Helper\ItemHelper;
use components\Helper\ShopHelper;
use components\models\Inventory;
use components\models\NewDelo;
use components\models\User;

class ItemInfo extends BaseInfo implements iTakeItem
{
    public $is_mf = false;
    public $goden = 0;
    public $ekr_flag = 0;
    public $shop_id;
    public $item_id;
    public $item_ids;
    public $notsell = 0;
    public $is_present = 0;
    public $is_owner = 0;
    public $from_present = null;
    public $unik;
    public $ups;
    public $up_level;
    public $add_time;
    public $nclass;

    public function getItemType()
    {
        return self::ITEM_TYPE_ITEM;
    }

	/**
	 * @param User $owner
	 * @param Part $Part
	 * @param Reward $Reward
	 * @return bool
	 */
    public function give($owner, Part $Part, Reward $Reward)
    {
        try {
            $Items = ShopHelper::getPrototypes(array($this->item_id), $this->shop_id);
            if(!$Items) {
                return false;
            }
            $prototype = $Items[0];

            $item = array(
                'getfrom' => 100000,
            );
            foreach (['ups', 'up_level', 'add_time', 'nclass'] as $field) {
				if($this->{$field} != '' && $this->{$field} !== null) {
					$item[$field] = $this->{$field};
				}
			}
            if($this->is_present) {
                $item['present'] = $this->from_present;
            }

            $options =  array(
                'goden'     => $this->goden,
                'ekr_flag'  => $this->ekr_flag,
                'is_mf'     => $this->is_mf ? true : false,
                'notsell'   => $this->notsell,
                'unik'      => $this->unik,
            );
            $item = ItemHelper::baseFromPrototype($item, $prototype, $options);
            if($prototype['id'] == 222222202) {
                $item['text'] = 'Мое сердце принадлежит только тебе!';
            }
            if($this->is_owner) {
            	$item['sowner'] = $owner->id;
			}

            $GiveItem = new ItemItem($owner, $item);
			$GiveItem->count = $Reward->getCount();

			$item_ids = $GiveItem->give();
			if(!$item_ids) {
				throw new \Exception;
			}

            $_data = array(
                'target_login'          => 'Квест',
                'type'                  => NewDelo::TYPE_QUEST_REWARD_ITEM,
                'add_info'              => $Part->name,
            );

            if(!$GiveItem->newDeloGive($_data)) {
                throw new \Exception;
            }

        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_reward_item');
            return false;
        }

        return true;
    }

    public function take($owner, Part $Part, Take $Take)
    {
        try {
            $item_ids = array();
            $item_id_list = '';
            $item_name      = null;
            $item_type      = null;
            $item_cost      = null;
            $item_duration  = null;
            $item_maxdur    = null;

            $take_item_ids = $this->item_ids ? explode(',', $this->item_ids) : array();
            if($this->item_id) {
                $take_item_ids[] = $this->item_id;
            }

			$Items = Inventory::whereIn('prototype', $take_item_ids)
				->whereRaw('dressed = 0 and setsale = 0')
				->where('owner', '=', $owner->id)
				->limit($Take->getCount())
				->get(['id', 'name', 'type', 'cost', 'duration', 'maxdur'])->toArray();
            foreach ($Items as $Item) {
                $item_ids[] = $Item['id'];

                $item_id_list .= ItemHelper::getItemId($owner->id_city, $Item['id']).',';

                //dirty fix
                $item_name = $Item['name'];
                $item_type = $Item['type'];
                $item_cost = $Item['cost'];
                $item_duration = $Item['duration'];
                $item_maxdur = $Item['maxdur'];
            }
            if(!$item_ids) {
                return true;
            }

            $TakeItem = new ItemItem($owner, array(
                'item_ids' => $item_ids
            ));
			$TakeItem->take();

            $_data = array(
                'target_login'          => 'Квест',
                'type'                  => NewDelo::TYPE_QUEST_TAKE_ITEM,
                'item_id'               => trim($item_id_list, ','),
                'item_name'             => $item_name,
                'item_count'            => count($item_ids),
                'item_type'             => $item_type,
                'item_cost'             => $item_cost,
                'item_dur'              => $item_duration,
                'item_maxdur'           => $item_maxdur,
                'add_info'              => $Part->name,
            );

            if(!$TakeItem->newDeloTake($_data)) {
                throw new \Exception;
            }

        } catch (\Exception $ex) {
            FileHelper::writeException($ex, 'quest_reward_item');
            return false;
        }

        return true;
    }
}