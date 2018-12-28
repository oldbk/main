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
 * @property int $tournament_user_id
 * @property int $map_item_id
 * @property int $tournament_id
 *
 *
 * @property ClanTournamentUser $tournamentUser
 * @property ClanTournamentMapItems $mapItem
 */
class ClanTournamentUserItems extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'clan_tournament_user_items';

	protected $primaryKey = ['tournament_user_id', 'map_item_id'];
	public $incrementing = false;

	public function tournamentUser()
	{
		return $this->belongsTo(ClanTournamentUser::class, 'tournament_user_id', 'id');
	}

	public function mapItem()
	{
		return $this->belongsTo(ClanTournamentMapItems::class, 'map_item_id', 'id');
	}
}