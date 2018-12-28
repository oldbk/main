<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.10.2018
 * Time: 22:37
 */

namespace components\Helper\map\items;


abstract class BaseMapItem implements iMapItem
{
	protected $id;
	protected $image;
	protected $is_taken = 0;
	protected $hidden = false;

	public function __construct($image, $is_taken = 0, $item_id = 0)
	{
		$this->id = $item_id;
		$this->image = $image;
		$this->is_taken = $is_taken;
	}

	/**
	 * @return mixed
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @return int
	 */
	public function isTaken()
	{
		return $this->is_taken;
	}

	/**
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->hidden;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}
}