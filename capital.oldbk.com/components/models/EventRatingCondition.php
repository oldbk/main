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
 * Class EventRatingCondition
 * @package components\models\_base
 *
 * @property integer $group
 * @property integer $rate_id
 * @property string $condition_type
 * @property string $field
 * @property string $value
 */
class EventRatingCondition extends BaseModal
{
	const ITEM_TYPE_RATING = 'rating';

	const CONDITION_RANGE 	= 'date_rang';
	const CONDITION_WEEK 	= 'week';
	const CONDITION_DATE 	= 'date';

	protected $connection = 'capital';
	protected $table = 'event_rating_condition';
	protected $primaryKey = 'id';
}