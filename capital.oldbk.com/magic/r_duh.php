<?php

// снятие  зелье +10% опыта на 360 мин
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}
$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '667' LIMIT 1;")); 
$wtime= magicinf(667);
if (!($effect['id']))
	{
	$add_time_eff=time()+($magic['time']*60);
	mysql_query("INSERT INTO `effects` SET `type`=667,`name`='Зелье Старого Мага',`time`='{$add_time_eff}',`owner`='{$user[id]}';");
	echo mysql_error();
	mysql_query("UPDATE users set expbonus=expbonus+0.1 where id='{$user[id]}' ; ");
	echo "<font color=red>Вы выпили <b>\"Зелье Старого Мага\", опыт + 10% </b></font>";
	$bet=1;
	$sbet = 1;
	} else {
		echo "<font color=red><b>Вы уже выпили такое зелье!</b></font>";
	}
		

?>