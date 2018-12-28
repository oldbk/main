<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.09.2018
 * Time: 18:01
 */

namespace components\Helper\rating;


interface iRating
{
	public function getKey();

	public function getAddValue();

	public function getOperation();

	public function getRatingId();
}