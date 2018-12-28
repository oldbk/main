<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.10.2018
 * Time: 22:39
 */

namespace components\Helper\map\items;


use components\models\clanTournament\ClanTournamentMapItems;

class MapUser extends BaseMapItem
{
	protected $user_id;
	protected $team_id;

	public function __construct($image, $user_id, $team_id)
	{
		parent::__construct($image);

		$this->user_id = $user_id;
		$this->team_id = $team_id;
	}

	public function getType()
	{
		return ClanTournamentMapItems::TYPE_USER;
	}

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @return mixed
	 */
	public function getTeamId()
	{
		return $this->team_id;
	}
}