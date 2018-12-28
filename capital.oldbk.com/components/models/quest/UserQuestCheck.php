<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\quest;
use components\models\_base\BaseModal;


/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $user_id
 * @property string $check_type
 * @property int $check_count
 * @property string $params
 * @property int $created_at
 * @property int $finished_at
 *
 *
 */
class UserQuestCheck extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'user_quest_check';
	protected $primaryKey = 'id';

    const TYPE_WEIGHT 	= 'weight';
    const TYPE_FORTUNA 	= 'fortuna';

    public static function addWeight($user_id, $count, $params = [])
    {
        $_data = [
			'user_id' => $user_id,
			'check_type' => self::TYPE_WEIGHT,
			'check_count' => $count,
			'params' => serialize($params),
			'created_at' => time(),
		];

		return static::insertGetId($_data);
	}

    public static function getWeight()
    {
    	return static::whereRaw('finished_at = 0 and check_type = ?', [static::TYPE_WEIGHT])->get()->toArray();
    }

	public static function getFortuna()
	{
		return static::whereRaw('finished_at = 0 and check_type = ?', [static::TYPE_FORTUNA])->get()->toArray();
	}
}