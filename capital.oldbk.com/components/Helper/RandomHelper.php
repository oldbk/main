<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.05.17
 * Time: 14:30
 */

namespace components\Helper;


class RandomHelper
{
	public static function getChance($persent)
	{
		$mm = 1000000;
		return (mt_rand($mm, 100 * $mm) <= $persent*$mm);
	}
}