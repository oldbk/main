<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models\clanTournament;
use components\models\_base\BaseModal;
use components\models\_trait\tHasCompositePrimaryKey;
use components\models\User;

/**
 * Class Bank
 * @package components\models
 *
 *
 * @property int $id
 * @property int $tournament_id
 * @property int $group_id
 * @property int $user_id
 * @property int $team_id
 * @property int $location_x
 * @property int $location_y
 * @property int $is_died
 * @property int $moved_at
 * @property int $can_moved_at
 * @property int $hospital_count
 *
 *
 * @property ClanTournament $tournament
 * @property ClanTournamentUserItems[] $tookItems
 * @property ClanTournamentGroup $group
 * @property User $user
 */
class ClanTournamentUser extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'clan_tournament_user';
	protected $primaryKey = 'id';

	public function tournament()
	{
		return $this->belongsTo(ClanTournament::class, 'tournament_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function tookItems()
	{
		return $this->hasMany(ClanTournamentUserItems::class, 'tournament_user_id', 'id');
	}

	public function group()
	{
		return $this->belongsTo(ClanTournamentGroup::class, 'group_id', 'id');
	}

	/**
	 * @return bool|int
	 */
	public function inFight()
	{
		if($this->user->battle > 0) {
			return $this->user->battle;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function inPit()
	{
		return $this->can_moved_at > $this->tournament->ended_at;
	}

	/**
	 * @return bool
	 */
	public function haveFlag()
	{
		foreach ($this->tookItems as $TookItem) {
			if($TookItem->mapItem->item_type == ClanTournamentMapItems::TYPE_FLAG) {
				return true;
			}
		}

		return false;
	}
}