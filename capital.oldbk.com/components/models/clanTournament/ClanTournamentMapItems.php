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
 * @property int $owner_team_id
 * @property int $user_id
 * @property string $item_type
 * @property int $location_x
 * @property int $location_y
 * @property int $is_removed
 * @property int $is_taken
 *
 */
class ClanTournamentMapItems extends BaseModal
{
	const TYPE_FLAG 	= 'flag';
	const TYPE_WALL 	= 'wall';
	const TYPE_BASE 	= 'base';
	const TYPE_MINE 	= 'mine';
	const TYPE_HOSPITAL = 'hospital';
	const TYPE_POWER 	= 'power';
	const TYPE_PIT 		= 'pit';
	const TYPE_USER 	= 'user';

	const IMAGE_FLAG 		= '/assets/tournament/tournament_flag.png';
	const IMAGE_WALL 		= '/assets/tournament/obstacle.png';
	const IMAGE_BASE 		= '/assets/tournament/tournament_baza2.png';
	const IMAGE_BASE2 		= '/assets/tournament/tournament_baza1.png';
	const IMAGE_MINE 		= '/assets/tournament/tournament_flower2.png';
	const IMAGE_PIT 		= '/assets/tournament/tournament_pit.png';
	const IMAGE_HOSPITAL 	= '/assets/tournament/hospital.png';
	const IMAGE_USER 		= '/assets/tournament/tournament_pers1.png';
	const IMAGE_USER_FLAG 	= '/assets/tournament/tournament_pers1_flag.png';
	const IMAGE_USER2 		= '/assets/tournament/tournament_pers2.png';
	const IMAGE_USER2_FLAG 	= '/assets/tournament/tournament_pers2_flag.png';
	const IMAGE_POWER 		= '/assets/tournament/checkpoint.png';
	const IMAGE_POWER2 		= '/assets/tournament/checkpoint_blue.png';
	const IMAGE_POWER3 		= '/assets/tournament/checkpoint_red.png';

	protected $connection = 'capital';
	protected $table = 'clan_tournament_map_items';
	protected $primaryKey = 'id';

	public static function title($item_type)
	{
		$titles = [
			self::TYPE_FLAG 	=> 'Флаг',
			self::TYPE_WALL 	=> 'Преграда',
			self::TYPE_BASE 	=> 'База',
			self::TYPE_MINE 	=> 'Ядовитый цветок',
			self::TYPE_HOSPITAL => 'Фонтан',
			self::TYPE_POWER 	=> 'Место захвата',
			self::TYPE_PIT 		=> 'Яма',
			self::TYPE_USER 	=> 'Персонаж',
		];

		return isset($titles[$item_type]) ? $titles[$item_type] : null;
	}
}