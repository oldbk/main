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
 * @property int $id
 * @property int $tournament_id
 * @property string $team1_clan
 * @property string $team2_clan
 * @property int $team1_value
 * @property int $team2_value
 * @property int $win
 * @property int $need_finish
 * @property int $is_end
 *
 * @property ClanTournament $tournament
 * @property ClanTournamentUser[] $users
 */
class ClanTournamentGroup extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'clan_tournament_group';
	public $incrementing = true;

	public function users()
	{
		return $this->hasMany(ClanTournamentUser::class, 'group_id', 'id');
	}

	public function tournament()
	{
		return $this->belongsTo(ClanTournament::class, 'tournament_id', 'id');
	}
}