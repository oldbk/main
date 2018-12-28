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
 * @property int $user_id
 * @property string $password
 * @property string $second_password
 * @property string $secret_2fa
 * @property int $security_cooldown
 * @property int $transfer_cooldown
 * @property string $reset_token
 */
class UserRewardList extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'user_reward_list';
	protected $primaryKey = ['user_id', 'reward_type'];
	public $incrementing = false;
	public $timestamps = true;
	public $dateFormat = 'U';

	const UPDATED_AT = null;

	const REWARD_2FA = '2fa_first';

	public static function isGotReward($user_id, $reward_type)
	{
		$count = static::whereRaw('user_id = ? and reward_type > ?', [$user_id, $reward_type])
			->count();

		return $count > 0;
	}
}