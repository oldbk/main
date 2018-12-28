<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 09.10.2018
 * Time: 22:43
 */

namespace components\Component\Pool;


class PoolPocket
{
	public $pocket_id;
	public $condition;

	/** @var PoolItem[] */
	public $items = [];

	public function addItem(PoolItem $Item)
	{
		$this->items[] = $Item;
	}

	/**
	 * @return PoolItem[]
	 */
	public function getValidItems()
	{
		$items = [];

		switch ($this->condition) {
			case \components\models\pool\PoolPocket::CONDITION_AND:
				foreach ($this->items as $Item) {
					$items[] = $Item->getInfo();
				}
				break;
			case \components\models\pool\PoolPocket::CONDITION_OR:
				$items = $this->items;
				shuffle($items);
				foreach ($items as $Item) {
					//@TODO maybe some validator
					$items[] = $Item->getInfo();
					break;
				}
				break;
		}

		return $items;
	}
}