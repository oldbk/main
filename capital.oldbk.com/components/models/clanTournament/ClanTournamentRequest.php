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
 * @property string $liga_type
 * @property int $tournament_id
 * @property string $comment
 * @property int $created_at
 * @property int $started_at
 * @property int $is_end
 *
 *
 * @property ClanTournamentRequestUser $user
 * @property ClanTournamentRequestUser[] $users
 */
class ClanTournamentRequest extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'clan_tournament_request';
	protected $primaryKey = 'id';

	public function getLigaName()
	{
		$liga = [
			1 => 'Обычная'
		];

		return $liga[$this->liga_type];
	}

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

	public function user()
	{
		return $this->hasOne(ClanTournamentRequestUser::class, 'request_id', 'id');
	}

	public function users()
	{
		return $this->hasMany(ClanTournamentRequestUser::class, 'request_id', 'id');
	}
}