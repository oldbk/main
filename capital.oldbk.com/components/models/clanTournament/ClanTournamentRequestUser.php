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
 * @property int $request_id
 * @property int $user_id
 * @property string $clan
 * @property int $is_ended
 * @property int $is_removed
 * @property int $joined_at
 *
 *
 * @property User $user
 * @property ClanTournamentRequest $request
 */
class ClanTournamentRequestUser extends BaseModal
{
	use tHasCompositePrimaryKey;

	protected $connection = 'capital';
	protected $table = 'clan_tournament_request_user';

	protected $primaryKey = ['request_id', 'user_id'];
	public $incrementing = false;

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function request()
	{
		return $this->belongsTo(ClanTournamentRequest::class, 'request_id', 'id');
	}
}