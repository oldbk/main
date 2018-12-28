<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.09.2018
 * Time: 14:13
 */

namespace components\models;

use components\models\_base\BaseModal;

/**
 * Class UserEventRating
 * @package components\models\_base
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $rating_id
 * @property integer $value
 * @property integer $is_end
 * @property integer $is_reward
 * @property integer $iteration_num
 * @property integer $is_closed
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property EventRating $rating
 */
class UserEventRating extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_event_rating';
	protected $primaryKey = 'id';
	public $timestamps = true;
	public $dateFormat = 'U';

	/**
	 * @param $user_ratingId
	 * @return bool|int
	 */
	public static function getNumberByUserRatingId($user_ratingId)
	{
		/** @var UserEventRating $UserRating */
		$UserRating = UserEventRating::find($user_ratingId);
		if(!$UserRating) {
			return false;
		}

		return $UserRating->getPosition();
	}

	public function getPosition()
	{
		$UserAdmin = User::whereRaw('klan = "radminion" or klan = "Adminion"')->get()->keyBy('id')->toArray();
		$admin_ids = array_keys($UserAdmin);

		$position = UserEventRating::where('iteration_num', '=', $this->iteration_num)
			->where('rating_id', '=', $this->rating_id)
			->where('value', '>', $this->value)
			->whereNotIn('user_id', $admin_ids)
			->count();

		$position2 = UserEventRating::where('iteration_num', '=', $this->iteration_num)
			->where('rating_id', '=', $this->rating_id)
			->where('value', '=', $this->value)
			->where('id', '<', $this->id)
			->whereNotIn('user_id', $admin_ids)
			->count();

		return $position + $position2 + 1;
	}

	public function getUpdatedAt()
	{
		if($this->updated_at instanceof \DateTime) {
			return $this->updated_at->getTimestamp();
		}

		return $this->updated_at;
	}

	public function rating()
	{
		return $this->belongsTo(EventRating::class, 'rating_id', 'id');
	}
}