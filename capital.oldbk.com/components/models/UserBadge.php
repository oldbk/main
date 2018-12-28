<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $user_id
 * @property string $img
 * @property string $description
 * @property string $alt
 * @property int $is_enabled
 * @property int $show_time
 * @property int $show_started_at
 * @property int $show_ended_at
 * @property int $rate_unique
 * @property int $stage
 * @property string $link
 * @property int $created_at
 * @property int $updated_at
 *
 */
class UserBadge extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_badge';
	protected $primaryKey = 'id';
	public $timestamps = true;
	public $dateFormat = 'U';

	const TYPE_MARCH8       = 'march8';
	const TYPE_MASLO        = 'maslo';
	const TYPE_MASLO_2017   = 'maslo2017';
	const TYPE_EASTER_EKR   = 'pasha_ekr';
	const TYPE_EASTER_KR    = 'pasha_kr';
	const TYPE_LOTO         = 'loto';
	const TYPE_MAY          = 'may';
	const TYPE_BUKET	    = 'buket';
	const TYPE_BOULING	    = 'bouling';
	const TYPE_SVET_TMA_08	= 'svet_tma_08';
	const TYPE_NTUR_RISTA	= 'medal_ntur_rista';
	const TYPE_HALLOWEEN	= 'medal_halloween_2017';

	public static function findByUserId($user_id)
	{
		$time = time();
		$items = static::whereRaw('user_id = ? and is_enabled = 1 and (show_time = 0 or (show_started_at <= ? and show_ended_at >= ?))',
			[$user_id,$time, $time])->get()->toArray();

		return $items;
	}

	public static function findByUserIdAll($user_id)
	{
		$time = time();
		$items = static::whereRaw('user_id = ? and (show_time = 0 or (show_started_at <= ? and show_ended_at >= ?))',
			[$user_id,$time, $time])->get()->toArray();

		return $items;
	}

	/**
	 * @param $user_id
	 * @param $type
	 * @return array|bool
	 */
	public static function hasExpire($user_id, $type)
	{
		$badge = static::whereRaw('user_id = ? and rate_unique = ? and show_time = 1', [$user_id, $type])->first();
		if($badge) {
			return $badge->toArray();
		}

		return false;
	}

	public static function addOrUpdateExpire($user_id, $img, $alt, $ended_timestamp, $type, $link = null)
	{
		$data = [
			'user_id'           => $user_id,
			'img'               => $img,
			'description'       => null,
			'alt'               => $alt,
			'created_at'        => time(),
			'is_enabled'        => 1,
			'show_time'         => 1,
			'show_started_at'   => time(),
			'show_ended_at'     => $ended_timestamp,
			'rate_unique'       => $type,
			'link'              => $link,
		];

		if(($badge = self::hasExpire($user_id, $type)) !== false) {
			unset($data['created_at']);
			unset($data['is_enabled']);

			return static::where('id', '=', $badge['id'])->update($data);
		}

		return static::insertGetId($data);
	}
}