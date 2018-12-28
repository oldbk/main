<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 19.10.2018
 * Time: 03:55
 */

namespace components\Helper\map;


use components\Helper\map\items\iMapItem;
use components\Helper\map\items\MapBase;
use components\Helper\map\items\MapFlag;
use components\Helper\map\items\MapHospital;
use components\Helper\map\items\MapMine;
use components\Helper\map\items\MapPit;
use components\Helper\map\items\MapPower;
use components\Helper\map\items\MapUser;
use components\Helper\map\items\MapWall;
use components\models\clanTournament\ClanTournament;
use components\models\clanTournament\ClanTournamentMapItems;

class HeroMapGenerator
{
	const DIRECTION_ANY 	= -1;
	const DIRECTION_TOP 	= 0;
	const DIRECTION_RIGHT 	= 1;
	const DIRECTION_BOTTOM 	= 2;
	const DIRECTION_LEFT 	= 3;

	private $_width;
	private $_height;
	private $_teams;
	private $_flag1;
	private $_mine;
	private $_wall_line;
	private $_power;
	private $_pit;

	protected $tournament_type;
	protected $viewer_id;
	protected $viewer_team_id;

	private $directions = [
		self::DIRECTION_TOP => [
			0 => ['y' => 0, 'x' => -1],
			1 => ['y' => -1, 'x' => -1],
		],
		self::DIRECTION_RIGHT => [
			0 => ['y' => 0, 'x' => 1],
			1 => ['y' => -1, 'x' => 1],
		],
		self::DIRECTION_BOTTOM => [
			0 => ['y' => 1, 'x' => 1],
			1 => ['y' => 0, 'x' => 1],
		],
		self::DIRECTION_LEFT => [
			0 => ['y' => 1, 'x' => -1],
			1 => ['y' => 0, 'x' => -1],
		],
	];

	private $map = [];
	private $corner = null;

	/**
	 * HeroMapGenerator constructor.
	 * @param $viewer_id
	 * @param $viewer_team_id
	 * @param $width
	 * @param $height
	 * @param $teams
	 * @param $tournament_type
	 */
	public function __construct($viewer_id, $viewer_team_id, $width, $height, $teams, $tournament_type)
	{
		$this->tournament_type = $tournament_type;
		$this->viewer_id = $viewer_id;
		$this->viewer_team_id = $viewer_team_id;

		$this->_width 		= $width;
		$this->_height 		= $height;
		$this->_teams 		= $teams;
		$this->_flag1 		= 5;
		$this->_mine 		= mt_rand(2, 5);
		$this->_wall_line 	= 3;
		$this->_power		= mt_rand(1, 3);
		$this->_pit			= mt_rand(1, 3);
	}

	/**
	 * @return mixed
	 */
	public function getWidth()
	{
		return $this->_width;
	}

	/**
	 * @return mixed
	 */
	public function getHeight()
	{
		return $this->_height;
	}

	/**
	 * @param $width
	 * @param $height
	 * @param $teams
	 * @param $tournament_type
	 * @return HeroMapGenerator
	 */
	public static function generate($width, $height, $teams, $tournament_type)
	{
		$obj = new self(null, null, $width, $height, $teams, $tournament_type);

		$obj->buildMap();
		$obj->buildItems();

		return $obj;
	}

	/**
	 * @param $viewer_id
	 * @param $viewer_team_id
	 * @param $width
	 * @param $height
	 * @param $map_items
	 * @param $opened
	 * @param $tournament_type
	 * @return HeroMapGenerator
	 */
	public static function populate($viewer_id, $viewer_team_id, $width, $height, $map_items, $opened, $tournament_type)
	{
		$obj = new self($viewer_id, $viewer_team_id, $width, $height, [], $tournament_type);
		$obj->buildMap();

		foreach ($opened as $yx) {
			$obj->show($yx['y'], $yx['x']);
		}

		foreach ($map_items as $y => $_t) {
			foreach ($_t as $x => $info) {
				foreach ($info['items'] as $item) {
					switch ($item['type']) {
						case ClanTournamentMapItems::TYPE_BASE:
							$obj->putBase($y, $x, $item['team_id'], $item['id']);
							break;
						case ClanTournamentMapItems::TYPE_FLAG:
							if(!$item['is_taken']) {
								$obj->putFlag($y, $x, $item['id']);
							}
							break;
						case ClanTournamentMapItems::TYPE_WALL:
							$obj->putWall($y, $x, $item['id']);
							break;
						case ClanTournamentMapItems::TYPE_POWER:
							$obj->putPower($y, $x, $item['team_id'], $item['id']);
							break;
						case ClanTournamentMapItems::TYPE_PIT:
							$obj->putPit($y, $x, $item['is_taken'], $item['id']);
							break;
						case ClanTournamentMapItems::TYPE_MINE:
							$obj->putMine($y, $x, $item['is_taken'], $item['id']);
							break;
						case ClanTournamentMapItems::TYPE_HOSPITAL:
							$obj->putHospital($y, $x, $item['id']);
							break;
						case ClanTournamentMapItems::TYPE_USER:
							$obj->putUser($y, $x, $item['user_id'], $item['team_id'], $item['withFlag']);
							break;
					}
				}
			}
		}

		return $obj;
	}

	/**
	 * @param $direction
	 * @return int
	 */
	protected function getRevers($direction)
	{
		if($direction == self::DIRECTION_TOP) {
			return self::DIRECTION_BOTTOM;
		}

		if($direction == self::DIRECTION_BOTTOM) {
			return self::DIRECTION_TOP;
		}

		if($direction == self::DIRECTION_RIGHT) {
			return self::DIRECTION_LEFT;
		}

		if($direction == self::DIRECTION_LEFT) {
			return self::DIRECTION_RIGHT;
		}

		return self::DIRECTION_ANY;
	}

	/**
	 * @param $y
	 * @param $x
	 * @param $exclude_direction
	 * @return array
	 */
	public function getAround($y, $x, $exclude_direction = null)
	{
		$around = [];
		foreach ($this->directions as $direction => $info) {
			if($exclude_direction === $direction || $exclude_direction === self::DIRECTION_ANY) {
				continue;
			}

			$yx = $this->getCoordsTo($y, $x, $direction);
			if($this->isOnMap($yx['y'], $yx['x']) !== false) {
				$around[] = $yx;
			}
		}

		return $around;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return bool
	 */
	protected function isOnMap($y, $x)
	{
		return isset($this->map[$y][$x]) ? true : false;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return bool
	 */
	protected function hasItems($y, $x)
	{
		return (isset($this->map[$y][$x]['items']) && !empty($this->map[$y][$x]['items']));
	}

	/**
	 * @param $y
	 * @param $x
	 * @return bool
	 */
	public function isHide($y, $x)
	{
		if($this->isOnMap($y, $x) === false) {
			return true;
		}

		return !$this->map[$y][$x]['show'];
	}

	/**
	 * @param $y
	 * @param $x
	 * @param array $exclude_types
	 * @return bool
	 */
	protected function isAroundEmpty($y, $x, $exclude_types = [])
	{
		$around = $this->getAround($y, $x);
		$around[] = ['y' => $y, 'x' => $x];

		foreach ($around as $yx) {
			if(!$this->isOnMap($yx['y'], $yx['x'])) {
				continue;
			}

			foreach ($this->getItems($yx['y'], $yx['x']) as $item) {
				if(!in_array($item->getType(), $exclude_types)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @param $y
	 * @param $x
	 * @param null $exclude_direction
	 * @return bool
	 */
	protected function canPut($y, $x, $exclude_direction = null)
	{
		if(!$this->isOnMap($y, $x) || $this->hasItems($y, $x)) {
			return false;
		}

		$aroundYX = $this->getAround($y, $x, $exclude_direction);
		foreach ($aroundYX as $yx) {
			if(!$this->isOnMap($yx['y'], $yx['x']) || $this->hasItems($yx['y'], $yx['x'])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param $currentY
	 * @param $currentX
	 * @param $newY
	 * @param $newX
	 * @return bool
	 */
	public function canMove($currentY, $currentX, $newY, $newX)
	{
		if(!isset($this->map[$newY][$newX]['show'])) {
			return false;
		}

		$near = false;
		foreach ($this->getAround($currentY, $currentX) as $yx) {
			if($newY == $yx['y'] && $newX == $yx['x']) {
				$near = true;
				break;
			}
		}
		if(!$near) {
			return false;
		}

		foreach ($this->getItems($newY, $newX) as $item) {
			if($item->getType() == ClanTournamentMapItems::TYPE_WALL) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return iMapItem[]|null
	 */
	public function getItems($y, $x)
	{
		return isset($this->map[$y][$x]['items']) ? $this->map[$y][$x]['items'] : null;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return MapUser[]
	 */
	public function getTeamUsers($y, $x)
	{
		$returned = [];

		/** @var MapUser[] $items */
		$items = $this->getItems($y, $x);
		foreach ($items as $item) {
			if($item->getType() == ClanTournamentMapItems::TYPE_USER && $item->getTeamId() == $this->viewer_team_id && $item->getUserId() != $this->viewer_id) {
				$returned[] = $item;
			}
		}

		return $returned;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return MapUser[]
	 */
	public function getEnemyUsers($y, $x)
	{
		$returned = [];

		/** @var MapUser[] $items */
		$items = $this->getItems($y, $x);
		foreach ($items as $item) {
			if($item->getType() == ClanTournamentMapItems::TYPE_USER && $item->getTeamId() != $this->viewer_team_id) {
				$returned[] = $item;
			}
		}

		return $returned;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return bool
	 */
	public function isFlag($y, $x)
	{
		$items = $this->getItems($y, $x);
		foreach ($items as $item) {
			if($item->getType() == ClanTournamentMapItems::TYPE_FLAG) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return bool|MapFlag
	 */
	public function getFlag($y, $x)
	{
		$items = $this->getItems($y, $x);
		foreach ($items as $item) {
			if($item->getType() == ClanTournamentMapItems::TYPE_FLAG) {
				return $item;
			}
		}

		return false;
	}

	/**
	 * @param $y
	 * @param $x
	 * @return bool
	 */
	public function isEnemy($y, $x)
	{
		/** @var iMapItem[]|MapUser[] $items */
		$items = $this->getItems($y, $x);
		foreach ($items as $item) {
			if($item->getType() == ClanTournamentMapItems::TYPE_USER && $item->getTeamId() != $this->viewer_team_id) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $y
	 * @param $x
	 * @param $direction
	 * @return array
	 */
	public function getCoordsTo($y, $x, $direction)
	{
		$revers = $x % 2;
		$direction = $this->directions[$direction][$revers];

		return [
			'y' => $y + $direction['y'],
			'x' => $x + $direction['x'],
		];
	}

	public function show($y, $x)
	{
		if($this->isOnMap($y, $x)) {
			$this->map[$y][$x]['show'] = true;
		}
	}

	public function showArea($y, $x)
	{
		$this->show($y, $x);
		$around = $this->getAround($y, $x);
		foreach ($around as $yx) {
			if(!$this->isHide($yx['y'], $yx['x'])) {
				continue;
			}

			$this->show($yx['y'], $yx['x']);
		}
	}

	protected function buildMap()
	{
		$this->corner = mt_rand(0,1);

		for ($location_y = 1; $location_y <= $this->_height; $location_y++) {
			for($location_x = 1; $location_x <= $this->_width ; $location_x++) {
				$this->map[$location_y][$location_x] = [
					'show' => false,
					'items' => [],
				];
			}
		}
	}

	protected function buildItems()
	{
		for($wall = 0; $wall < $this->_wall_line; $wall++) {
			$length = mt_rand(2, 4);
			$location_y = mt_rand(2, $this->_height - 2);
			$location_x = mt_rand(0, $this->_width);
			if(!$this->canPut($location_y, $location_x)) {
				continue;
			}
			$this->putWall($location_y, $location_x);

			$rand_direction = mt_rand(0, 3);
			$previous_direction = $this->getRevers($rand_direction);
			for($i = 0; $i < $length-1; $i++) {

				$yx = $this->getCoordsTo($location_y, $location_x, $rand_direction);
				if(!$this->canPut($yx['y'], $yx['x'], $previous_direction)) {
					continue;
				}

				$location_y = $yx['y'];
				$location_x = $yx['x'];
				$previous_direction = $this->getRevers($rand_direction);

				$this->putWall($yx['y'], $yx['x']);
			}

		}

		$flagCount = $this->_flag1;
		while($flagCount > 0) {
			$location_y = mt_rand(2, $this->_height - 2);
			$location_x = mt_rand(2, $this->_width - 2);

			if(!$this->canPut($location_y, $location_x, self::DIRECTION_ANY) || !$this->isAroundEmpty($location_y, $location_x, [
					ClanTournamentMapItems::TYPE_WALL,
					ClanTournamentMapItems::TYPE_MINE,
					ClanTournamentMapItems::TYPE_HOSPITAL,
				])) {
				continue;
			}

			$this->putFlag($location_y, $location_x);
			$flagCount--;
		}

		$power = $this->_power;
		while ($power > 0) {
			$location_y = mt_rand(2, $this->_height - 2);
			$location_x = mt_rand(2, $this->_width - 2);

			if(!$this->canPut($location_y, $location_x, self::DIRECTION_ANY) || !$this->isAroundEmpty($location_y, $location_x, [
					ClanTournamentMapItems::TYPE_WALL,
					ClanTournamentMapItems::TYPE_MINE,
					ClanTournamentMapItems::TYPE_HOSPITAL,
				])) {
				continue;
			}

			$this->putPower($location_y, $location_x);
			$power--;
		}

		$mine = $this->_mine;
		while ($mine > 0) {
			$location_y = mt_rand(3, $this->_height - 3);
			$location_x = mt_rand(3, $this->_width - 3);

			if(!$this->canPut($location_y, $location_x, self::DIRECTION_ANY)) {
				continue;
			}

			$this->putMine($location_y, $location_x);
			$mine--;
		}

		$i = 20;
		while($i > 0) {
			$i--;
			$location_y = mt_rand(3, $this->_height - 3);
			$location_x = mt_rand(3, $this->_width - 3);

			if(!$this->canPut($location_y, $location_x, self::DIRECTION_ANY)) {
				continue;
			}

			$this->putHospital($location_y, $location_x);
			break;
		}

		if($this->tournament_type != ClanTournament::TYPE_1x1) {
			$pit = $this->_pit;
			while ($pit > 0) {
				$location_y = mt_rand(3, $this->_height - 3);
				$location_x = mt_rand(3, $this->_width - 3);

				if(!$this->canPut($location_y, $location_x, self::DIRECTION_ANY)) {
					continue;
				}

				$this->putPit($location_y, $location_x);
				$pit--;
			}
		}

		if($this->corner) {
			$this->putBase(1,1, 1);
			foreach ($this->_teams[1] as $user_id) {
				$this->putUser(1, 1, $user_id, 1);
			}

			$this->putBase($this->_height, $this->_width, 2);
			foreach ($this->_teams[2] as $user_id) {
				$this->putUser($this->_height, $this->_width, $user_id, 2);
			}

		} else {
			$this->putBase($this->_height,2, 1);
			foreach ($this->_teams[1] as $user_id) {
				$this->putUser($this->_height, 2, $user_id, 1);
			}

			$this->putBase(1, $this->_width - 1, 2);
			foreach ($this->_teams[2] as $user_id) {
				$this->putUser(1, $this->_width - 1, $user_id, 2);
			}
		}
	}

	protected function putWall($y, $x, $item_id = 0)
	{
		$this->map[$y][$x]['items'][] = new MapWall(ClanTournamentMapItems::IMAGE_WALL, $item_id);
	}

	protected function putBase($y, $x, $team_id, $item_id = 0)
	{
		$image = ($team_id == $this->viewer_team_id) ? ClanTournamentMapItems::IMAGE_BASE2 : ClanTournamentMapItems::IMAGE_BASE;

		$this->map[$y][$x]['items'][] = new MapBase($image, $team_id, $item_id);
	}

	protected function putFlag($y, $x, $item_id = 0)
	{
		$this->map[$y][$x]['items'][] = new MapFlag(ClanTournamentMapItems::IMAGE_FLAG, $item_id);
	}

	protected function putPower($y, $x, $team_id = 0, $item_id = 0)
	{
		$image = ClanTournamentMapItems::IMAGE_POWER;
		if($team_id && $team_id == $this->viewer_team_id) {
			$image = ClanTournamentMapItems::IMAGE_POWER2;
		} elseif($team_id) {
			$image = ClanTournamentMapItems::IMAGE_POWER3;
		}

		$this->map[$y][$x]['items'][] = new MapPower($image, $item_id);
	}

	protected function putMine($y, $x, $is_taken = 0, $item_id = 0)
	{
		$this->map[$y][$x]['items'][] = new MapMine(ClanTournamentMapItems::IMAGE_MINE, $is_taken, $item_id);
	}

	protected function putPit($y, $x, $is_taken = 0, $item_id = 0)
	{
		$this->map[$y][$x]['items'][] = new MapPit(ClanTournamentMapItems::IMAGE_PIT, $is_taken, $item_id);
	}

	protected function putHospital($y, $x, $item_id = 0)
	{
		$this->map[$y][$x]['items'][] = new MapHospital(ClanTournamentMapItems::IMAGE_HOSPITAL, $item_id);
	}

	protected function putUser($y, $x, $user_id, $team_id, $withFlag = false)
	{
		$image = ClanTournamentMapItems::IMAGE_USER;
		if($withFlag) {
			$image = ClanTournamentMapItems::IMAGE_USER_FLAG;
		}

		if($team_id && $team_id != $this->viewer_team_id) {
			$image = ClanTournamentMapItems::IMAGE_USER2;
			if($withFlag) {
				$image = ClanTournamentMapItems::IMAGE_USER2_FLAG;
			}
		}

		$this->map[$y][$x]['items'][] = new MapUser($image, $user_id, $team_id);
	}

	public function getMap()
	{
		return $this->map;
	}
}