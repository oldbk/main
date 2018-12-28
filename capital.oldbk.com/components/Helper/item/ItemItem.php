<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\Component\VarDumper;
use components\Helper\ItemHelper;
use components\models\Inventory;
use components\models\NewDelo;
use components\models\User;

class ItemItem extends BaseItem
{
    public $count = 1;
    protected $item;
    protected $item_ids = [];

	/**
	 * ItemItem constructor.
	 * @param User $owner
	 * @param $item
	 */
    public function __construct($owner, $item)
    {
        parent::__construct($owner);

        $this->item = $item;
    }

	/**
	 * @return bool|array
	 */
    public function give()
    {
    	if(!$this->count) {
    		$this->count = 1;
		}

		$item_ids = [];

		$_data = array_merge($this->item, [
			'owner'    => $this->owner->id,
			'idcity'   => $this->owner->id_city,
		]);
		if(!isset($_data['add_time'])) {
			$_data['add_time'] = time();
		}
		for($i = 1; $i <= $this->count; $i++) {
			$item_id = Inventory::insertGetId($_data);
			if(!$item_id) {
				return false;
			}
			$item_ids[] = $item_id;
		}
		$this->item_ids = $item_ids;

		return $item_ids;
    }

	/**
	 * @param array $data
	 * @return bool
	 */
    public function newDeloGive(array $data = array())
    {
    	$item_ids = [];
    	foreach ($this->item_ids as $item_id) {
			$item_ids[] = ItemHelper::getItemId($this->owner->id_city, $item_id);
		}

        $_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'owner_balans_do'       => $this->owner->money,
			'owner_balans_posle'    => $this->owner->money,
			'item_count'            => $this->count,
			'sdate'                 => time(),

			'item_name' 			=> $this->item['name'],
			'item_type'     		=> $this->item['type'],
			'item_cost'     		=> $this->item['cost'],
			'item_ecost' 			=> $this->item['ecost'],
			'item_dur'      		=> $this->item['duration'],
			'item_maxdur'   		=> $this->item['maxdur'],
			'item_mfinfo'   		=> $this->item['mfinfo'] ? $this->item['mfinfo'] : '',
			'item_level'    		=> $this->item['nlevel'],
			'item_proto'    		=> $this->item['prototype'],
			'item_id'               => implode(',', $item_ids),
		];

        $_data = array_merge($_data, $data);

        if(!NewDelo::addNew($_data)) {
            return false;
        }

        return true;
    }

	/**
	 * @return bool|int
	 */
    public function take()
    {
        if(!is_array($this->item) || !isset($this->item['item_ids'])) {
            return false;
        }
        $item_ids = $this->item['item_ids'];

		Inventory::whereIn('id', $item_ids)
			->where('owner', '=', $this->owner->id)
			->delete();

		return true;
    }

	/**
	 * @param array $data
	 * @return bool
	 */
    public function newDeloTake(array $data = array())
    {
        $_data = [
			'owner'                 => $this->owner->id,
			'owner_login'           => $this->owner->login,
			'owner_balans_do'       => $this->owner->money,
			'owner_balans_posle'    => $this->owner->money,
			'item_count'            => 1,
			'sdate'                 => time(),
		];
        $_data = array_merge($_data, $data);

        if(!NewDelo::addNew($_data)) {
            return false;
        }

        return true;
    }
}