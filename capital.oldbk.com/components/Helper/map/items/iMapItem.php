<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.10.2018
 * Time: 23:04
 */

namespace components\Helper\map\items;


interface iMapItem
{
	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return string
	 */
	public function getImage();

	/**
	 * @return boolean
	 */
	public function isHidden();

	/**
	 * @return boolean
	 */
	public function isTaken();

	/**
	 * @return int
	 */
	public function getId();
}