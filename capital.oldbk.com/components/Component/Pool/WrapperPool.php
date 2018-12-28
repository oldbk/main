<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 11.06.2018
 * Time: 23:53
 */

namespace components\Component\Pool;

use components\Component\Pool\item\iItem;
use components\models\pool\PoolAssign;

class WrapperPool
{
	/** @var PoolPocket[] */
	protected $pockets = [];

	/**
	 * WrapperPool constructor.
	 * @param $target_id
	 * @param $target_type
	 * @param \Closure $validatorCallback
	 */
	public function __construct($target_id, $target_type, $validatorCallback)
	{
		/** @var PoolAssign[] $Assigns */
		$Assigns = PoolAssign::with('pool')->where('target_id', '=', $target_id)
			->where('target_type', '=', $target_type)
			->get();
		foreach ($Assigns as $Assign) {
			if(is_callable($validatorCallback) && $validatorCallback($Assign) === false) {
				continue;
			}

			$Assign->pool->load('pockets', 'pockets.items', 'pockets.items.infos');
			foreach ($Assign->pool->pockets as $Pocket) {
				$PoolPocket = new PoolPocket();
				$PoolPocket->pocket_id = $Pocket->id;
				$PoolPocket->condition = $Pocket->condition;

				foreach ($Pocket->items as $Item) {
					$PoolItem = new PoolItem($Item->item_type, $Item->give_count);

					$attributes = [];
					foreach ($Item->infos as $Info) {
						$attributes[$Info->field] = $Info->value;
					}
					$PoolItem->populate($attributes);

					$PoolPocket->addItem($PoolItem);
				}

				$this->pockets[] = $PoolPocket;
			}
		}
	}

	/**
	 * @return iItem[]
	 */
	public function getGiveItems()
	{
		$items = [];
		foreach ($this->pockets as $Pocket) {
			$items = array_merge($items, $Pocket->getValidItems());
		}

		return $items;
	}
}