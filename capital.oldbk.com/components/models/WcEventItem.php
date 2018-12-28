<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Stol
 * @package components\models
 *
 *
 * @property int $id
 * @property int $pocket_id
 * @property int $item_count
 * @property int $updated_at
 */
class WcEventItem extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'wc_event_item';
	protected $primaryKey = 'id';
	public $incrementing = false;
}