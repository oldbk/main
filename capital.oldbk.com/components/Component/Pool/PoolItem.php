<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 09.10.2018
 * Time: 22:46
 */

namespace components\Component\Pool;


use components\Component\Pool\item\BaseItem;
use components\Component\Pool\item\iItem;

class PoolItem
{
	public $id;
	public $pool_id;
	public $pocket_id;

	/** @var iItem */
	public $info;

	public function __construct($item_type, $give_count)
	{
		$this->info = BaseItem::getItemInfo($item_type);
		$this->info->setGiveCount($give_count);
	}

	public function populate(array $attributes)
	{
		$this->info->populate($attributes);
	}

	/**
	 * @return iItem
	 */
	public function getInfo()
	{
		return $this->info;
	}
}