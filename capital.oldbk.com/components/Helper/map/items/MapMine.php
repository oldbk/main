<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.10.2018
 * Time: 22:39
 */

namespace components\Helper\map\items;


use components\models\clanTournament\ClanTournamentMapItems;

class MapMine extends BaseMapItem
{
	public function getType()
	{
		return ClanTournamentMapItems::TYPE_MINE;
	}

	public function isHidden()
	{
		if($this->isTaken()) {
			return false;
		}

		return true;
	}
}