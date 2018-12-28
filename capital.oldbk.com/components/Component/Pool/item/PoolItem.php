<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;


use components\Component\Slim\Slim;
use components\Helper\Exception\ExitTryException;
use components\Helper\item\ItemItem;
use components\Helper\ItemHelper;
use components\Helper\ShopHelper;
use components\models\Shop;
use components\models\User;

class PoolItem extends BaseItem
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

    private $prototype = [];

    public function getItemType()
    {
        return self::ITEM_TYPE_ITEM;
    }

	/**
	 * @param User $owner
	 * @param \Closure $CallbackDelo
	 * @param \Closure|null $CallbackItem
	 * @return bool
	 */
    public function give(User $owner, \Closure $CallbackDelo, $CallbackItem = null) : bool
    {
        try {
            $Items = ShopHelper::getPrototypes([$this->item_id], $this->shop_id);
            if(!$Items) {
                return false;
            }
            $this->prototype = $prototype = $Items[0];

            $item = [];
            if($this->is_present) {
                $item['present'] = $this->from_present;
            }

            $options =  [
				'goden'     => $this->goden,
				'ekr_flag'  => $this->ekr_flag,
				'is_mf'     => $this->is_mf ? true : false,
				'notsell'   => $this->notsell,
				'unik'      => $this->unik,
			];
            $item = ItemHelper::baseFromPrototype($item, $prototype, $options);
            if($this->is_owner) {
            	$item['sowner'] = $owner->id;
			}
			if($CallbackItem && $CallbackItem instanceof \Closure) {
				$item = $CallbackItem($item);
			}

            $GiveItem = new ItemItem($owner, $item);
			$GiveItem->count = $this->give_count;

			$item_ids = $GiveItem->give();
			if(!$item_ids) {
				throw new ExitTryException('ѕыталс€ выдать предмет пула.');
			}

			$_data = [];
			$_data = $CallbackDelo($_data, self::ITEM_TYPE_ITEM);

            if(!$GiveItem->newDeloGive($_data)) {
                throw new ExitTryException('ѕыталс€ добавить запись в дело.');
            }

            return true;
        } catch (ExitTryException $ex) {
			Slim::getInstance()->logger->alert($ex->getMessage(), [
				'pool_id' 				=> $this->pool_id,
				'pool_pocket_id' 		=> $this->pool_pocket_id,
				'pool_pocket_item_id' 	=> $this->pool_pocket_item_id,
				'owner_id' 				=> $owner->id,
				'call'					=> 'PoolItem::give',
			]);
		} catch (\Exception $ex) {
        	Slim::getInstance()->logger->error($ex);

        }

		return false;
    }

    public function getChatString()
	{
		$link = ItemHelper::buildLink($this->prototype);

		return sprintf('<a href="%s" target="_blank">%s</a> %dшт. ', $link, $this->prototype['name'], $this->give_count);
	}

	public function getViewArray()
	{
		return [
			'link' 	=> ItemHelper::buildLink($this->prototype),
			'img' 	=> ItemHelper::buildImg($this->prototype, true),
			'name' 	=> $this->prototype['name'],
			'count' => $this->give_count,
		];
	}
}