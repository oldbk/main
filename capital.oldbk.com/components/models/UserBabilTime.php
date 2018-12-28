<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class UserBabilTime
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $owner
 * @property int $stime
 */
class UserBabilTime extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'users_babil_time';
	protected $primaryKey = 'owner';

    /**
     * @return bool
     */
    public function checkFree()
    {
        $last_plus_month = new \DateTime();
        $last_plus_month->setTimestamp($this->stime)
            ->modify('+1 month');

        $current_time = new \DateTime();

        return $last_plus_month < $current_time;
    }
}