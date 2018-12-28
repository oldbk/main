<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class Stol
 * @package components\models
 *
 *
 * @property int $wc_event_id
 * @property int $user_id
 * @property int $res
 * @property int $is_rewarded
 * @property int $item_id
 * @property int $item_proto_id
 * @property int $is_win
 * @property int $created_at
 */
class WcEventBet extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'wc_event_bet';
	protected $primaryKey = ['wc_event_id', 'user_id'];
	public $incrementing = false;
	public $timestamps = true;
	public $dateFormat = 'U';

	const UPDATED_AT = null;

	const BET_WIN_1 	= 1;
	const BET_WIN_2 	= 2;
	const BET_NO_WIN 	= 3;

	public static function types()
	{
		return [
			self::BET_WIN_1,
			self::BET_NO_WIN,
			self::BET_WIN_2,
		];
	}

	public function getBetType()
	{
		$bets = [
			self::BET_WIN_1 	=> 'Ï1',
			self::BET_WIN_2 	=> 'Ï2',
			self::BET_NO_WIN 	=> 'X',
		];

		return $bets[$this->res];
	}
}