<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.09.2018
 * Time: 14:27
 */

namespace components\Helper\rating;


class ArenaRating extends AbstractRating
{
	public function getKey()
	{
		return static::KEY_ARENA;
	}
}