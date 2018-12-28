<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.10.2018
 * Time: 22:39
 */

namespace components\Helper\map\items;


use components\models\clanTournament\ClanTournamentMapItems;

class MapWall extends BaseMapItem
{
	public function __construct($image, $item_id = 0)
	{
		parent::__construct($image, 0, $item_id);
	}

	public function getType()
	{
		return ClanTournamentMapItems::TYPE_WALL;
	}
}