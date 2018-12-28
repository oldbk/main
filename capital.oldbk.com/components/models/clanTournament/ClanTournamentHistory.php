<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\clanTournament;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\models
 *
 *
 * @property int $id
 * @property int $tournament_id
 * @property int $group_id
 * @property string $event_type
 * @property int $user_id
 * @property int $target_id
 * @property int $battle_id
 * @property int $location_y
 * @property int $location_x
 * @property int $created_at
 *
 */
class ClanTournamentHistory extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'clan_tournament_history';
	protected $primaryKey = 'id';
	public $timestamps = true;
	public $dateFormat = 'U';

	const UPDATED_AT = null;
}