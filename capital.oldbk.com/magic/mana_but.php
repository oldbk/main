<?php 
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}
if ($user['maxmana']==$user['mana'])	
	{
	echo "<font color=red><b>У персонажа и так полный запас  магической энергии!<b></font>";
	}
elseif ($user['battle']>0)	
	{
	echo "<font color=red><b>Не в бою...<b></font>";
	}
else
	{
	mysql_query("UPDATE `users` SET `mana`=`maxmana` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
	echo "<font color=red><b>Вы пополнили магическую энергию!<b></font>";
	$bet=1;
	$sbet = 1;
	}
?>