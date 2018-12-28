<?php
if ($user['battle'] > 0) {
	echo "Не в бою...";
}
else
if ($user['hp']==$user['maxhp'])
	{
	echo "Вы и так полны сил...";
	}
elseif ($user['in_tower'] == 1) {echo "Не в башне смерти!";}
elseif ($user['in_tower'] == 2) {echo "Не в руинах!";}
else
{
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	mysql_query("UPDATE `users` SET `hp`=`maxhp` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");

	echo "<font color=red><b>Вы пополнили здоровье из колодца...</b></font>";
	$bet=1;
	$sbet = 1;
}
?>