<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.10.2018
 * Time: 22:39
 */

namespace components\Helper\map\items;

use components\models\clanTournament\ClanTournamentMapItems;

class MapBase extends BaseMapItem
{
	protected $team_id;

	public function __construct($image, $team_id, $item_id = 0)
	{
		parent::__construct($image, 0, $item_id);
		$this->team_id = $team_id;
	}

	public function getType()
	{
		return ClanTournamentMapItems::TYPE_BASE;
	}

	/**
	 * @return mixed
	 */
	public function getTeamId()
	{
		return $this->team_id;
	}
}