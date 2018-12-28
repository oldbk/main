<?php
// магия "шаг назад"
if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif (rand(1,100)!=1) {
	//undressall($user['id']);
	if (!($_SESSION['uid'] >0)) header("Location: index.php");

	require_once('./dt_rooms.php');	

	$rs = mysql_query("SELECT * FROM `users` WHERE `in_tower` = 15 ORDER by `room` DESC;");
	while($r = mysql_fetch_array($rs)) {
		if($rt != $r['room']) {
			$rt = $r['room'];
			$rr .= "\n".$dt_rooms[$r['room']][0].": ";
		}
		$rr .= $r['login'].", ";
	}

	$rs = mysql_query("SELECT * FROM `users_clons` WHERE id_user = 84 ORDER by `bot_room` DESC;");
	while($r = mysql_fetch_array($rs)) {
		if($rt != $r['bot_room']) {
			$rt = $r['bot_room'];
			$rr .= "\n".$dt_rooms[$r['bot_room']][0].": ";
		}
		$rr .= $r['login'].", ";
	}

	echo "<font color=red><b>Отчет о сканировании у вас в рюкзаке<b></font>";

	mysql_query("INSERT INTO oldbk.`inventory` (`prototype`,`bs`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`letter`,`maxdur`,`isrep`,`bs_owner`)
	VALUES
	(104,'15','{$_SESSION['uid']}','Отчет о сканировании','200',1,0,'paper100.gif','{$rr}',1,0,15) ;");

	$bet=1;
	$sbet = 1;
}
?>