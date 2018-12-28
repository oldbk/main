<?php
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}

if ($user['battle'] > 0) {
	echo "Не в бою...";
	return;
}

$eff = mysql_query('SELECT * FROM effects WHERE type = 814 AND owner = '.$_SESSION['uid']);
if (mysql_num_rows($eff) > 0) {
	$eff = mysql_fetch_assoc($eff);
	echo "Вы не можете использовать Жажду Наживы еще ".floor(($eff['time']-time())/60/60)." ч. ".round((($eff['time']-time())/60)-(floor(($eff['time']-time())/3600)*60))." мин.";
	return;
}

$duration = 60*60;

mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$user['id']."','Жажда наживы',".(time()+$duration).",813);");
mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$user['id']."','Задержка на использование Жажды наживы',".(time()+(24*60*60)).",814);");

$bet = 1;
$sbet = 1;
$MAGIC_OK=1;
echo "<font color=red><b>Вы использовали Жажду Наживы...</b></font>";
?>
