<?php

namespace components\Eloquent;

use Illuminate\Database\Eloquent\Model;


/**
 * Class User2fa
 * @package components\Eloquent
 */
class User2fa extends Model
{
	protected $table = 'user_2fa';
	protected $primaryKey = 'user_id';

	const STATUS_DISABLED 	= 0;
	const STATUS_PREPARE 	= 1;
	const STATUS_ENABLED 	= 2;

    /**
     * @return bool
     */
	public function isEnabled()
	{
		return $this->status == self::STATUS_ENABLED;
	}

    /**
     * @return bool
     */
	public function isPrepared()
	{
		return $this->status == self::STATUS_PREPARE;
	}

    /**
     * @return bool
     */
	public function isDisabled()
	{
		return $this->status == self::STATUS_DISABLED;
	}
}