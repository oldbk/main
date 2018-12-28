<?php
// magic идентификацыя
if ($user['battle'] > 0) {
	echo "Не в бою...";
}
else
{
	$magic = magicinf(3);
	if ($user['intel'] >= 2) {
		$int=$magic['chanse'] + ($user['intel'] - 2)*3;
		if ($int>98){$int=99;}
	}
	else {$int=0;}

	if (rand(1,100) < $int) {

		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '{$target}' AND `needident` = 1 AND `owner` = '{$user['id']}' and bs_owner='".$user[in_tower]."'  LIMIT 1;"));

		if(!((int)$dress['id'] > 0))
		{
			header("Location: main.php?edit=1");
			die();
		}

		if (mysql_query("UPDATE oldbk.`inventory` SET `needident` = 0 WHERE `id` = {$dress['id']} AND bs_owner='".$user[in_tower]."' LIMIT 1;"))
		{
			echo "<font color=red><b>Предмет \"{$dress['name']}\" удачно идентифицирован <b></font>";
			$bet=1;
			$sbet = 1;
		}
		else
		{
			echo "<font color=red><b>Неправильное имя предмета<b></font>";
		}
	} else
	{
		echo "<font color=red><b>Неудачно...<b></font>";
		$bet = 1;
	}
}
?>