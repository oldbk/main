<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\models\effect;
use components\models\Effect;
use components\models\User;


class AddZnaharStatEffect extends Effect
{
	protected static $type_ids = [101050];

	/**
	 * @param User $User
	 * @param integer $time
	 * @return boolean
	 * @throws \Exception
	 */
	public static function add(&$User, $time = null)
	{
		if($time === null) {
			$time = (new \DateTime())->modify('+5 minute')->getTimestamp();
		}

		$_data = [
			'type' 	=> static::$type_ids[0],
			'name' 	=> 'Дополнительные статы для комплекта',
			'time' 	=> $time,
			'sila' 	=> 75,
			'lovk' 	=> 75,
			'inta' 	=> 75,
			'owner' => $User->id,
		];

		if(!static::insertGetId($_data)) {
			throw new \Exception();
		}
		
		$User->sila += 75;
		$User->lovk += 75;
		$User->inta += 75;

		return true;
	}

	/**
	 * @param User $User
	 * @return boolean
	 * @throws \Exception
	 */
	public static function drop(&$User)
	{
		$del = static::where('owner', '=', $User->id)
			->where('type', '=', static::$type_ids[0])
			->limit(1)
			->delete();
		if(!$del) {
			throw new \Exception();
		}

		$User->sila -= 75;
		$User->lovk -= 75;
		$User->inta -= 75;

		return true;
	}
}