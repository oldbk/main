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
 * @property int $pocket_id
 * @property int $item_id
 * @property string $field
 * @property string $value
 */
class WcEventItemDetails extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'wc_event_item_details';
	protected $primaryKey = ['item_id', 'field'];
	public $incrementing = false;
}