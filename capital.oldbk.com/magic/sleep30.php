<?php

$us = mysql_fetch_array(mysql_query("SELECT *  FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
$magic = mysql_fetch_array(mysql_query("SELECT `chanse` FROM `magic` WHERE `id` = '15' ;"));
$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$us['id']}' and `type` = '2' LIMIT 1;"));

if ($user['intel'] >= 1 && $klan_abil != 1) {
		$int=$magic['chanse'] + ($user['intel'] - 1)*3;
		if ($int>98){$int=99;}
	}
	elseif($klan_abil==1)    //если юзается как абилка (наследие от клана, шанс 100%)
	{
		$int=101;
	}
else {$int=0;}
if ($CHAOS==1) { $int=101; }

if (($us['room']==23) OR ($user['room']==23))
	{
	//ремонтка
	 if ((test_lic_mag($us)) AND ($us['room']==23))
	 	{
	 	$candoit=false;
		 }
		 else
		 {
		 $candoit=true;
		 }

	 if ((test_lic_mag($user)) AND ($user['room']==23))
	 	{
	 	$candoit2=false;
		 }
		 else
		 {
		 $candoit2=true;
		 }		 
		 
	}
	else
	{
	$candoit=true;
	$candoit2=true;	
	}


if ($candoit2==false) { echo "В ремотной мастерской маг неможет наложить заклятие молчания!"; }
elseif ($candoit==false) { echo "В ремотной мастерской на мага нельзя наложить заклятие молчания!"; }
elseif (($user['battle'] > 0) and  ($user['id'] !=12) and ($user['id'] !=190672)) {echo "Не в бою...";}
elseif ($effect['time']) {echo "На персонаже уже есть заклятие молчания"; }
elseif ($user['room'] != $us['room']) { echo "Персонаж в другой комнате!"; }
elseif ($us['odate'] < (time()-60) ) {echo "Персонаж не в игре!";}
elseif ($us['hidden'] >0 ) {echo "Персонаж не в игре!";}
elseif ($us['deal'] >= 1) { echo "Вы не можете наложить заклятие молчания на этого персонажа"; }
elseif ($us['klan'] =='pal') { echo "Вы не можете наложить заклятие молчания на этого персонажа"; }
elseif ($user['klan'] =='pal') { echo "Вы не можете наложить заклятие молчания этим свитком"; }
elseif ($us['align'] > 2 && $us['align'] < 3) { echo "Решились поднять руку на Ангела?.."; }
elseif ($us['id'] ==12) { echo "Решились поднять руку на Ангела?.."; }
elseif (rand(1,100) < $int) {

			$nick = nick7($user['id']);
			addch("<img src=i/magic/sleep.gif>Персонаж &quot;{$nick}&quot; наложил заклятие молчания на &quot;{$_POST['target']}&quot;, сроком 30 мин.",$user['room'],$user['id_city']);

			$juser = mysql_fetch_array(mysql_query("SELECT `id` FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
			mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$juser['id']."','Заклятие молчания',".(time()+1800).",2);");
			mysql_query("UPDATE users set slp=1 where id={$juser['id']} ;");
				echo "<font color=red><b>На персонажа \"{$_POST['target']}\" наложено заклятие молчания </b></font>";
				$bet=1;
				$sbet = 1;

} else {
				echo "Свиток рассыпался в ваших руках...";
				$bet=1;
			}
?>