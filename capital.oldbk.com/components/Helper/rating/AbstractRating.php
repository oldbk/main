<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.09.2018
 * Time: 14:27
 */

namespace components\Helper\rating;


abstract class AbstractRating implements iRating
{
	const KEY_FONTAN 	= 'fontan';
	const KEY_FORTUNA 	= 'fortuna';
	const KEY_ARENA 	= 'arena';
	const KEY_RUINE 	= 'ruine';
	const KEY_RISTA_ONE = 'rista_one';
	const KEY_DRAGON 	= 'dragon';
	const KEY_HAOS 		= 'haos';
	const KEY_OSADA 	= 'osada';
	const KEY_HELLOWEEN = 'helloween';

	const OPERATION_START 		= 'start';
	const OPERATION_END 		= 'end';
	const OPERATION_END_START 	= 'end_start';


	public $value_add = 0;
	public $operation;

	public $rating_id;

	public function __construct($rating_id = null)
	{
		$this->rating_id = $rating_id;
	}


	public function getAddValue()
	{
		return $this->value_add;
	}

	public function setStart()
	{
		$this->operation = self::OPERATION_START;
		return $this;
	}

	public function setEnd()
	{
		$this->operation = self::OPERATION_END;
		return $this;
	}

	public function setEndStart()
	{
		$this->operation = self::OPERATION_END_START;
		return $this;
	}

	public function getOperation()
	{
		return $this->operation;
	}

	public function getRatingId()
	{
		return $this->rating_id;
	}
}