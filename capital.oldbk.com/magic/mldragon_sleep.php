<?php
	// магия "усыпить дракона"
	if (!isset($maprel)) {
		include('map_config.php');
	}

	if ($user['room'] != ($maprel+$maprelall+15)) {
		echo 'Это работает только в пещере с драконом...';
	} else {
		$questexist = false;
		$q = mysql_query('SELECT * FROM map_quests WHERE owner = '.$user['id']) or die();
		if (mysql_num_rows($q) > 0) {
			$questexist = mysql_fetch_assoc($q) or die();
		}
	
		// 8ой квест
		if ($questexist !== FALSE && $questexist['q_id'] == 8 && $questexist['step'] == 4) {
			require_once('mlfunctions.php');
			mysql_query('START TRANSACTION');
			if (!SetQuestStep($user,8,5)) QuestDie();
			mysql_query('COMMIT');
			$bet = 1;
			$sbet = 1;
			echo 'Дракон усыплён...';
		} else {
			echo 'Зелье использовано впустую...';
			$bet = 1;
		}
	}
?>