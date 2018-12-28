<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 09.10.2018
 * Time: 16:35
 */

namespace components\Component\Pool\item;


use components\models\User;

interface iItem
{
	/**
	 * @param User $owner
	 * @param \Closure $CallbackDelo
	 * @param null $CallbackItem
	 * @return bool
	 */
	public function give(User $owner, \Closure $CallbackDelo, $CallbackItem = null) : bool;

	public function populate(array $attributes);

	/**
	 * @param $count
	 * @return self
	 */
	public function setGiveCount($count);

	/**
	 * @return string
	 */
	public function getChatString();

	/**
	 * @return array
	 */
	public function getViewArray();
}