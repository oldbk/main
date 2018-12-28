<?php
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	
	if ($ABIL==1)
	{
		$magic = magicinf(5007071);
	}	
	
	$us = mysql_fetch_array(mysql_query("SELECT *  FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
	///$magic = mysql_fetch_array(mysql_query("SELECT * FROM `magic` WHERE `id` = '6060' ;")); в ункциях цепляется
	$exist_trap = mysql_fetch_array(mysql_query("SELECT * FROM `city_trap` WHERE `room` = '{$user['room']}' and `target` = '".$us[id]."' AND `owner` = '".$user[id]."' LIMIT 1;"));
	
		if($user[klan]!='')
		{
			$uklan=mysql_fetch_array(mysql_query("SELECT *  FROM oldbk.`clans` WHERE `short` = '{$user['klan']}' LIMIT 1;"));
			if($uklan[base_klan]>0)
			{
				$uklan[id]=$uklan[base_klan];
			}
		}
		
		if($us[klan]!='')
		{
			$tklan=mysql_fetch_array(mysql_query("SELECT *  FROM oldbk.`clans` WHERE `short` = '{$us['klan']}' LIMIT 1;"));
			if($tklan[base_klan]>0)
			{
				$tklan[id]=$tklan[base_klan];
			}
		}
		
		$chanse = $magic['chanse']+$user['intel'];
		if($chanse>100)
		{
			$chanse=100;
		}


if ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif ($us[bot]==1) { "Не стоит этого делать..."; }
//elseif (($user['lab']>0) or ($user['room']==45) or ($us['room']==45) or ($us['lab']>0) )  {
elseif (($user['lab']>0) or ($user['room']==45) )  {
	echo "Ловушки в этой локации запрещены!";
}
elseif (($user['room'] >=197)AND($user['room'] <=199)) {
     echo "Ловушки в этой локации запрещены!";
}
elseif ($user['room'] ==60) {
     echo "Ловушки в этой локации запрещены!";
}
elseif ($user['room'] == 402) {
	echo "Ловушки в этой локации запрещены!";
}
elseif ($user['room'] ==999) {
     echo "Ловушки в этой локации запрещены!";
}
elseif($us['id'] == $user['id']) {
	echo "Мазохист?..";
}
elseif ($user['room'] == 60 || ($user['room'] >= 49998 && $user['room'] <= 60000)) {
	echo "Тут это не работает...";
}
elseif (($user['room'] >=210)AND($user['room'] <=300)) {
	echo "Тут это не работает...";
}
elseif ($user['room'] == 31 || $user['room'] == 43 || $user['room'] == 200) {
	echo "Ловушки в этой локации запрещены!";
} 
elseif (($us['klan'] == 'radminion' || ($us['align'] > '2' && $us['align'] < '3')) && $user['klan'] != 'radminion' && $user[align]!=5)
{
 	echo "Какой ужас! Ты уверен? Не сейчас...";
}
elseif (($us['klan'] == 'radminion' || ($us['align'] > '2' && $us['align'] < '3')) && $user['klan'] != 'radminion' && $us['id']!=4 && $us['id']!=3 && $us['id']!=2 && $us['id']!=6)
{
	echo "Какой ужас! Ты уверен? Не сейчас...";
}
elseif ($us['level'] < 1) 
{
	echo "Новички находятся под защитой мироздателя!";
}
elseif($uklan[id]==$tklan[id] && $uklan[short]!='' && $tklan[short]!='')
{
	echo "Чтите честь своих соклановцев!";
}
else
if($exist_trap[id]>0)
{
	echo "На этого персонажа в этой локации уже установлена ловушка."; 
}
else
{	
	if (  (rand(1,100) <= $chanse)  OR ($ABIL==1) )
	{
		$komnata = $rooms[$user['room']];
		//$messch="Персонаж &quot;{$user['login']}&quot; $action ловушку в этой комнате...";
		//addch("<img src=i/magic/trap.gif> $messch");		
		echo "<font color=red><b>Вы установили ловушку в локации ''{$komnata}'' на персонажа ".$us[login]."</b></font>";	
		mysql_query("INSERT INTO city_trap VALUES (NULL, '{$user[id]}', '{$us[id]}', '{$user[room]}','".(time()+60*60*24)."')");
		$bet=1;			
		$sbet = 1;
	} 
	else 
	{
		# сгорел
		echo "<font color=red><b>Свиток рассыпался в ваших руках...<b></font>";
		$bet = 1;
	}
}
?>
