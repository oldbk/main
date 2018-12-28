<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;


/**
 * Class Chat
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property string $cdate
 * @property string $text
 * @property int $city
 * @property int $room
 * @property int $owner
 *
 */
class Chat extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'chat';
	protected $primaryKey = 'id';

	/**
	 * @param $text
	 * @param $who
	 * @param int $room
	 * @param int $city
	 * @return int
	 */
	public static function addToChat($text, $who, $room=0, $city=-1)
	{
		$txt_to_file = ":[".time()."]:[{$who}]:[".($text)."]:[".$room."]";
		$room = -1;

		$_data = [
			'text' => $txt_to_file,
			'city' => $city,
			'room' => $room
		];

		return static::insertGetId($_data);
	}

	/**
	 * @param $text
	 * @param User|array $user
	 * @return int
	 */
	public static function addToChatSystem($text, $user)
	{
		$login      = ($user instanceof User) ? $user->login : $user['login'];
		$room       = ($user instanceof User) ? $user->room : $user['room'];
		$city       = ($user instanceof User) ? $user->id_city : $user['id_city'];

		$who = '{[]}' . $login . '{[]}';

		$txt_to_file = ":[".time()."]:[{$who}]:[".($text)."]:[".$room."]";
		$room = -1;

		$_data = [
			'text' => $txt_to_file,
			'city' => $city,
			'room' => $room
		];

		return static::insertGetId($_data);
	}

	/**
	 * @param $text
	 * @param $user_ids
	 * @param $city_id
	 * @return int
	 */
	public static function addToGroupChatSystem($text, $user_ids, $city_id)
	{
		if(!is_array($user_ids)) {
			$user_ids = [$user_ids];
		}
		$user_ids = implode(":|:", $user_ids);

		$txt_to_file = ":[".time()."]:[!group!:|:".$user_ids."]:[".($text)."]:[]";
		$_data = [
			'text' => $txt_to_file,
			'city' => $city_id,
		];

		return static::insertGetId($_data);
	}

	/**
	 * @param $text
	 * @param int $city_id
	 * @return int
	 */
	public static function addToAll($text, $city_id = 0)
	{
		$txt_to_file=":[".time()."]:[!sys2all!!]:[<font color=\"#CB0000\">".($text)."</font>]:[1]";

		$_data = [
			'text' => $txt_to_file,
			'city' => $city_id,
		];
		return static::insertGetId($_data);
	}

	/**
	 * @param $text
	 * @param int $city_id
	 * @return int
	 */
	public static function addToAllSystem($text, $city_id = 0)
	{
		$txt_to_file=":[".time()."]:[!sys2all!!]:[".($text)."]:[1]";

		$_data = [
			'text' => $txt_to_file,
			'city' => $city_id,
		];

		return static::insertGetId($_data);
	}
}