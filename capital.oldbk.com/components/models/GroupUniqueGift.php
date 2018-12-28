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
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property string $group_name
 * @property int $gift_id
 * @property int $shop_id
 *
 */
class GroupUniqueGift extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'group_unique_gift';
	protected $primaryKey = ['group_name', 'gift_id', 'shop_id'];
	public $incrementing = false;

    const GROUP_DJ = 'dj';

    public static function getUserGroup(array $user)
    {
        $group = array();
        if(Rdjsn::where('id_dj', '=', $user['id'])->count()) {
            $group[] = self::GROUP_DJ;
        }
        if(in_array($user['klan'], array('radminion', 'Adminion'))) {
            $group[] = self::GROUP_DJ;
        }

        return $group;
    }

    public static function getGroupGiftIds($group_list)
    {
        $ids = array();
        if(empty($group_list)) {
            return $ids;
        }

		$gifts = static::whereIn('group_name', $group_list)->get(['gift_id'])->toArray();
		foreach ($gifts as $gift) {
            $ids[] = $gift['gift_id'];
        }

        return $ids;
    }
}