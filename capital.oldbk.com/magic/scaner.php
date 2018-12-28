<?php
// магия "шаг назад"
if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif (rand(1,100)!=1) {
	//undressall($user['id']);
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	global $rooms;
	$rs = mysql_query("SELECT * FROM `users` WHERE `in_tower` = 1 ORDER by `room` DESC;");
	while($r = mysql_fetch_array($rs)) {
		if($rt != $r['room']) {
			$rt = $r['room'];
			$rr .= "\n".$rooms[$r['room']].": ";
		}
		$rr .= $r['login'].", ";
	}
	echo "<font color=red><b>Отчет о сканировании у вас в рюкзаке<b></font>";

	mysql_query("INSERT INTO oldbk.`inventory` (`bs`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`letter`,`maxdur`,`isrep`,`bs_owner`)
	VALUES
	('1','{$_SESSION['uid']}','Отчет о сканировании','200',1,0,'paper100.gif','{$rr}',1,0,1) ;");

	$bet=1;
	$sbet = 1;
}
?>