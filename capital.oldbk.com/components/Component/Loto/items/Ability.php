<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 14.04.2016
 */

namespace components\Component\Loto\items;

use components\Helper\item\ItemAbility;
use components\models\NewDelo;
use components\models\User;

class Ability extends BaseItem
{
	protected $prototype;

	protected $images = array(
		2525 => 'attackbv.gif'
	);

	public function __construct($app, $loto_id, $loto_item, $prototype)
	{
		$this->prototype = $prototype;

		parent::__construct($app, $loto_id, $loto_item);
	}

	protected function prepareItem($loto_item)
	{
		$magic_id = $loto_item['info']['magic_id'];

		$item = parent::prepareItem($loto_item);
		$item = array_merge($item, array(
			'count'     => (isset($loto_item['info']['count']) && $loto_item['info']['count'] > 0) ? $loto_item['info']['count'] : 1,
			'magic_id'  => $magic_id,
			'name'      => $loto_item['info']['name'],
			'img'       => isset($this->images[$magic_id]) ? $this->images[$magic_id] : $this->prototype['img'],
			'item_id'   => $this->prototype['magic_id'],
			'nalign'    => 0,
			'duration'  => 0,
			'maxdur'    => isset($loto_item['info']['count']) ? $loto_item['info']['count'] : 1,
			'massa'     => 0
		));

		return $item;
	}

	/**
	 * @param $owner
	 * @return bool
	 */
	public function give($owner)
	{
		if(is_array($owner)) {
			$owner = new User($owner);
		}
		try {
			$GiveAbility = new ItemAbility($owner, $this->item['magic_id']);
			$GiveAbility->count = $this->count;
			if($item_id = $GiveAbility->give() === false) {
				throw new \Exception;
			}

			$_data = array(
				'target_login'          => 'Лоттерея',
				'type'                  => NewDelo::TYPE_LOTO_ABILITY,
				'item_name'             => $this->item['name'],
				'item_dur'              => $this->item['duration'],
				'item_maxdur'           => $this->item['maxdur'],
			);

			if(!$GiveAbility->newDeloGive($_data)) {
				throw new \Exception;
			}

			if(!$this->sendMessage($owner)) {
				throw new \Exception;
			}
		} catch (\Exception $ex) {
			return false;
		}

		unset($owner);
		return true;
	}
}