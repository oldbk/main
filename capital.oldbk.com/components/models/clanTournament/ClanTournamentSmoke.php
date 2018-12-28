<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\clanTournament;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;

/**
 * Class Bank
 * @package components\models
 *
 *
 * @property int $tournament_id
 * @property int $group_id
 * @property int $team_id
 * @property int $location_x
 * @property int $location_y
 * @property int $is_removed
 * @property int $opened_at
 *
 */
class ClanTournamentSmoke extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'clan_tournament_smoke';

	protected $primaryKey = ['tournament_id', 'group_id', 'team_id', 'location_x', 'location_y'];
	public $incrementing = false;
}