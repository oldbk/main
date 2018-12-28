<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 07.04.2016
 */

namespace components\Helper;


use components\Component\VarDumper;
use components\models\User;

class FightHelper
{
    const TYPE_NEWBIE   = 'newbie';
    const TYPE_FIZ      = 'fiz';
    const TYPE_ALIGN    = 'align';
    const TYPE_GROUP    = 'group';
    const TYPE_CHAOT    = 'chaot';
    const TYPE_QUEST    = 'quest';

    private static $list = array(
        self::TYPE_NEWBIE,
        self::TYPE_FIZ,
        self::TYPE_ALIGN,
        self::TYPE_GROUP,
        self::TYPE_CHAOT,
    );

    public static function getList()
    {
        return self::$list;
    }

    public static function parseLog($battle_id, $get_user_info = false)
	{
		$dir = (int)($battle_id/1000);

		$filename = '/www/data/combat_logs/'.$dir.'000/battle'.$battle_id.'.txt';
		/** @var array|bool $statfile */
		/*$statfile = file($filename);
		if(!$statfile || !is_array($statfile)) {
			return false;
		}*/
		$user_placeholder = [
			'team' 			=> null,
			'login' 		=> null,
			'hidden_login' 	=> null,
			'hidden' 		=> false,
			'damage' 		=> [
				'count' 	=> [
					'basic' => 0,
					'krit' 	=> 0,
					'all' 	=> 0,
				],
				'total' 	=> [
					'basic' => 0,
					'krit' 	=> 0,
					'all' 	=> 0,
				],
				'magic'		=> [
					'count' => 0,
					'total' => 0,
				],
			],
			'kill'			=> 0,
		];


		$user_logins = [];
		$teams = [
			1 => [],
			2 => []
		];
		$f = fopen($filename, "r");
		$line_count = 0;
		while(!feof($f)) {
			$line_count++;

			$line = fgets($f);
			if(trim($line) == '') {
				continue;
			}

			$line_info = explode(':', $line);
			$user_info = [];
			$magic = false;
			switch (true) {
				case ($line_info[0] == '!' && in_array($line_info[1], ['U', 'B', 'K', 'P', 'R'])): //физический удар
					$user_info = explode("|", $line_info[3]);
					break;
				case ($line_info[0] == '!' && in_array($line_info[1], ['Y', 'Z', 'J', 'G', 'L', 'O', '1'])): //магический удар
					$user_info = explode("|", $line_info[6]);
					$magic = true;
					break;
			}
			if(!$user_info) {
				continue;
			}

			$team = (int)$user_info[1];
			$login = $user_info[0];
			if(!in_array($login, $user_logins)) {
				$user_logins[] = $login;
			}

			if(!isset($teams[$team][$login])) {
				$teams[$team][$login] = $user_placeholder;
			}
			$user =& $teams[$team][$login];

			$user = array_merge($user, [
				'login'			=> $login,
				'hidden_login'	=> !empty($user_info[2]) ? $user_info[2] : null,
				'team' 			=> $team,
				'hidden' 		=> $user_info[2] != '' ? true : false,
			]);

			$damage = explode("|", $line_info[11])[0];
			$damage_type = in_array($line_info[1], ['K', 'P']) ? 'krit' : 'basic';
			if($damage == 0) {
				continue;
			}

			if($magic === false) {
				$user['damage']['count'][$damage_type]++;
				$user['damage']['count']['all']++;

				$user['damage']['total'][$damage_type] += $damage;
				$user['damage']['total']['all'] += $damage;
			} else {
				$user['damage']['magic']['count']++;
				$user['damage']['magic']['total'] += $damage;
			}


			$kill = explode("/", $line_info[12])[0];
			if($kill == '[0') {
				$user['kill']++;
			} elseif($kill == '[??') {

			}

			unset($user, $team, $login, $damage, $damage_type, $line_info, $user_info, $magic);
		}

		fclose($f);

		if($get_user_info && $user_logins) {
			$Users = User::whereIn('login', $user_logins)->get(['login', 'id', 'level', 'align'])->toArray();
			foreach ($Users as $_user) {
				for ($i = 1; $i < 3; $i++) {
					if(isset($teams[$i][$_user['login']])) {
						$teams[$i][$_user['login']]['info'] = $_user;
					}
				}
			}
			unset($Users, $user_logins);
		}

		return $teams;
	}
}