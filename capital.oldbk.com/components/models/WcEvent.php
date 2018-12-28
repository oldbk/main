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
 * @property int $year
 * @property string $team1
 * @property string $team2
 * @property int $datetime
 * @property int $team1_res
 * @property int $team2_res
 * @property int $who_win
 * @property int $updated_at
 */
class WcEvent extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'wc_event';
	protected $primaryKey = 'id';
	public $incrementing = false;

	public function getDescription()
	{
		return sprintf('%s - %s', $this->team1, $this->team2);
	}
}