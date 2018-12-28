<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 10.06.2016
 */

namespace components\Helper\item;


interface iItemGiveTake
{
	/**
	 * @return bool|int
	 */
    public function give();
	/**
	 * @return bool
	 */
    public function take();
	/**
	 * @param array $data
	 * @return bool
	 */
    public function newDeloGive(array $data = array());
	/**
	 * @param array $data
	 * @return bool
	 */
    public function newDeloTake(array $data = array());
}