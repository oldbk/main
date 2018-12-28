<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\models\effect;
use components\models\Effect;
use components\models\User;


class Travma extends Effect
{
	const INFO_ZNAHAR = 'znahar';

	protected static $type_ids = [12, 13, 11, 14, 200, 1111, 826];

	public static function dontmove($user_id, $time = null)
	{
		if($time === null) {
			$time = (new \DateTime())->modify('+5 minute')->getTimestamp();
		}

		$_data = array(
			'owner' 	=> $user_id,
			'name' 		=> '',
			'time' 		=> $time,
			'type' 		=> 14,
			'sila' 		=> 0,
			'lovk' 		=> 0,
			'inta' 		=> 0,
			'vinos' 	=> 0,
			'lastup' 	=> 0,
			'add_info' 	=> static::INFO_ZNAHAR,
		);

		return static::insertGetId($_data);
	}

	public static function removeDontmove($user_id)
	{
		return static::where('add_info', '=', Travma::INFO_ZNAHAR)
			->where('owner', '=', $user_id)
			->where('type', '=', 14)
			->limit(1)
			->delete();
	}

	/**
	 * @param int $user_id
	 * @param \DateTime $datetime
	 * @param array $stats
	 * @return int
	 */
	public static function nelech($user_id, $datetime, $stats = array())
	{
		$_s = array_merge(array(
			'sila' 	=> 0,
			'lovk' 	=> 0,
			'inta' 	=> 0,
			'vinos' => 0,
		), $stats);

		$User = User::find($user_id)->toArray();
		if(!$User) {
			return false;
		}
		$_data = [
			'sila' 	=> $User['sila'] - $_s['sila'],
			'lovk' 	=> $User['lovk'] - $_s['lovk'],
			'inta' 	=> $User['inta'] - $_s['inta'],
			'vinos' => $User['vinos'] - $_s['vinos'],
		];
		static::where('id', '=', $user_id)
			->update($_data);

		$_data = array_merge([
			'owner' => $user_id,
			'name' => '',
			'time' => $datetime->getTimestamp(),
			'type' => 14,
			'lastup' => 0
		], $_s);


		return static::insertGetId($_data);
	}

}