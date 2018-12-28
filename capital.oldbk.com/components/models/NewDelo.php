<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\Helper\ItemHelper;
use components\models\_base\BaseModal;

/**
 * Class NewDelo
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $owner
 * @property string $owner_login
 * @property float $owner_balans_do
 * @property float $owner_balans_posle
 * @property int $owner_rep_do
 * @property int $owner_rep_posle
 * @property int $target
 * @property string $target_login
 * @property int $type
 * @property int $sdate
 * @property float $sum_kr
 * @property float $sum_ekr
 * @property int $sum_rep
 * @property float $sum_kom
 * @property string $item_id
 * @property string $aitem_id
 * @property string $item_name
 * @property int $item_count
 * @property int $item_type
 * @property int $item_proto
 * @property float $item_cost
 * @property float $item_ecost
 * @property int $item_dur
 * @property int $item_maxdur
 * @property int $item_ups
 * @property int $item_unic
 * @property string $item_incmagic
 * @property int $item_incmagic_id
 * @property string $item_incmagic_count
 * @property string $item_arsenal
 * @property int $item_sowner
 * @property int $battle
 * @property int $bank_id
 * @property string $add_info
 *
 */
class NewDelo extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'new_delo';
	protected $primaryKey = 'id';

	const TYPE_QUEST_REWARD_ITEM    = 274;
	const TYPE_QUEST_REWARD_KR      = 279;
	const TYPE_QUEST_REWARD_EKR     = 291;
	const TYPE_QUEST_REWARD_EXP     = 292;
	const TYPE_QUEST_REWARD_REP     = 182;
	const TYPE_QUEST_REWARD_WEIGHT  = 293;
	const TYPE_QUEST_REWARD_PROFEXP = 292;

	const TYPE_QUEST_ITEM           = 275;
	const TYPE_QUEST_TAKE_ITEM      = 276;

	const TYPE_QUEST_ADD            = 277;
	const TYPE_QUEST_FINISH         = 278;

	const TYPE_LOTO_ABILITY         = 185;
	const TYPE_LOTO_ITEM            = 185;
	const TYPE_WC_ITEM            	= 1340;

	const TYPE_PLUGIN_VIOLATION     = 600;

	const TYPE_RATING_REWARD_ITEM   = 1380;
	const TYPE_RATING_REWARD_REP    = 1381;

	public static function placeholder()
	{
		return array(
			'target'                => 0,
			'target_login'          => null,
			'sum_kr'                => 0,
			'sum_ekr'               => 0,
			'sum_rep'               => 0,
			'sum_kom'               => 0,
			'item_id'               => '',
			'item_proto'            => 0,
			'item_name'             => '',
			'item_count'            => 0,
			'item_type'             => 0,
			'item_cost'             => 0,
			'item_ecost'            => 0,
			'item_dur'              => 0,
			'item_maxdur'           => 0,
			'item_ups'              => 0,
			'item_unic'             => 0,
			'item_incmagic'         => '',
			'item_incmagic_count'   => '',
			'item_arsenal'          => '',
		);
	}

	public static function addDelo($owner, $target, $item_ids, $type, $item, $prototype, $info)
	{
		$item_id_string = '';
		foreach ($item_ids as $item_id) {
			$item_id_string .= ItemHelper::getItemId($owner['id_city'], $item_id).',';
		}
		$item_id_string = trim($item_id_string, ',');

		$_data = [
			'owner'                 => $owner['id'],
			'owner_login'           => $owner['login'],
			'owner_balans_do'       => $owner['money'],
			'owner_balans_posle'    => $owner['money'],
			'target'                => isset($target['target']) ? $target['target'] : 0,
			'target_login'          => isset($target['target_login']) ? $target['target_login'] : null,
			'type'                  => $type,
			'sum_kr'                => 0,
			'sum_ekr'               => 0,
			'sum_kom'               => 0,
			'item_id'               => $item_id_string,
			'item_proto'            => $prototype['id'],
			'item_name'             => $item['name'],
			'item_count'            => count($item_ids),
			'item_type'             => $item['type'],
			'item_cost'             => $item['cost'],
			'item_ecost'            => $item['ecost'],
			'item_dur'              => $item['duration'],
			'item_maxdur'           => $item['maxdur'],
			'item_ups'              => 0,
			'item_unic'             => 0,
			'item_incmagic'         => '',
			'item_incmagic_count'   => '',
			'item_arsenal'          => '',
			'sdate'                 => time(),
			'add_info'              => $info['add_info']
		];

		return self::addNew($_data);
	}

	/**
	 * @param $data
	 * @return bool|int
	 */
	public static function addNew($data)
	{
		$data = array_merge(self::placeholder(), $data);
		$delo_id = static::insertGetId($data);
		if(!$delo_id) {
			return false;
		}

		$sert = array(200001, 200002, 200005, 200010, 200025, 200050, 200100, 200250, 200500);
		if(!isset($data['item_type'])) {
			$data['item_type'] = 0;
		}
		if(!isset($data['item_proto'])) {
			$data['item_proto'] = 0;
		}
		if(!isset($data['type'])) {
			$data['type'] = 0;
		}


		if(($data['item_type'] > 0 && $data['item_type'] < 12 || $data['item_type'] == 28 || $data['item_type'] == 555 || $data['item_type'] == 27 || in_array($data['item_proto'], $sert)) && $data['type'] != 32 && $data['type'] != 33)
		{
			foreach (explode(',',$data['item_id']) as $item_id) {
				$_data = array(
					'item_id' => $item_id,
					'delo_id' => $delo_id
				);
				NewDeloItIndex::insert($_data);
			}
		}

		return $delo_id;
	}
}