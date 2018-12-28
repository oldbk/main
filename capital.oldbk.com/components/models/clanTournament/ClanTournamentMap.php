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
 * @property int $updated_at
 * @property int $created_at
 *
 *
 * @property ClanTournamentUser $user
 */
class ClanTournamentMap extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'clan_tournament_map';
	protected $primaryKey = 'id';
}