<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 15.11.17
 * Time: 15:53
 */

namespace components\Helper\location;


use components\models\Iplog;

abstract class BaseLocation
{
	const LOCATION_RUINE 	= 'ruine';
	const LOCATION_BS 		= 'bs';
	const LOCATION_NTUR 	= 'ntur';

	/** @var [] */
	protected $_user = [];

	public function __construct($user)
	{
		$this->_user = $user;
	}

	public static function getList()
	{
		return [
			self::LOCATION_BS,
			self::LOCATION_NTUR,
			self::LOCATION_RUINE,
		];
	}

	/**
	 * @param $location_type
	 * @param $user
	 * @return iCanLocation
	 * @throws \Exception
	 */
	public static function getLocation($location_type, $user)
	{
		if(!in_array($location_type, self::getList())) {
			throw new \Exception('Location not found');
		}

		$className = sprintf('components\Helper\location\Location%s', ucfirst($location_type));

		return new $className($user);
	}

	protected function getOtherIps($user_ids)
	{
		$other_ips = [];
		foreach ($user_ids as $_id) {
			if(!$_id) {
				continue;
			}

			/** @var Iplog $Iplog */
			$Iplog = Iplog::where('owner', '=', $_id)->orderBy('date', 'desc')->first(['ip']);
			if(!$Iplog) {
				continue;
			}
			$other_ips = array_merge($other_ips, explode('|', $Iplog->ip));
		}

		return $other_ips;
	}
}