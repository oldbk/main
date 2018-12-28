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
 * @property string $t_type
 * @property int $height
 * @property int $width
 * @property int $team_count
 * @property string $clan_team1
 * @property string $clan_team2
 * @property int $updated_at
 * @property int $created_at
 * @property int $is_end
 * @property int $ended_at
 *
 *
 * @property ClanTournamentUser[] $users
 * @property ClanTournamentGroup[] $groups
 * @property ClanTournamentMapItems[] $mapItems
 * @property ClanTournamentSmoke[] $mapSmoke
 */
class ClanTournament extends BaseModal
{
	const TYPE_1x1 = '1x1';
	const TYPE_3x3 = '3x3';
	const TYPE_5x5 = '5x5';

	protected $connection = 'capital';
	protected $table = 'clan_tournament';
	protected $primaryKey = 'id';
	public $timestamps = true;
	public $dateFormat = 'U';

	/**
	 * @return int
	 * @throws \Exception
	 */
	public function getNeedUserCount()
	{
		switch ($this->t_type) {
			case ClanTournament::TYPE_1x1:
				return 1;
				break;
			case ClanTournament::TYPE_3x3:
				return 3;
				break;
			case ClanTournament::TYPE_5x5:
				return 5;
				break;
		}

		throw new \Exception("Can't find tournament type: ".$this->t_type);
	}

	public function mapItems()
	{
		return $this->hasMany(ClanTournamentMapItems::class, 'tournament_id', 'id');
	}

	public function users()
	{
		return $this->hasMany(ClanTournamentUser::class, 'tournament_id', 'id');
	}

	public function groups()
	{
		return $this->hasMany(ClanTournamentGroup::class, 'tournament_id', 'id');
	}

	public function mapSmoke()
	{
		return $this->hasMany(ClanTournamentSmoke::class, 'tournament_id', 'id');
	}
}