<?php

$us = mysql_fetch_array(mysql_query("SELECT *  FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
$owntravma = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$us['id']." AND `type`=11;"));
$magic = magicinf(19);



if($user['room']==45 &&  $us['lab']>0)
{
	$us['room']=$user['room'];
}

if ($user['room'] >= 49998 && $user['room'] <= 60000) {
	// тело в загороде
	if ($us['room'] >= 49998 && $us['room'] <= 60000) {
		// кого лечим тоже в загороде
		$us['room'] = $user['room'];
	}
}

if ($user['intel'] >= 4) {
		$int=$magic['chanse'] + ($user['intel'] - 4)*3;
		if ($int>98){$int=99;}
	}
else {$int=0;}

if($user['id'] == '188' || $user['klan'] == 'radminion') {$int = 101;}

if ($us['id']!=$user['id'])
	{
	//если лечин не себя - нужна лицензия
	if (test_lic_med($user))
		{
		$lic_lek=true;
		}
		else
		{
		$lic_lek=false;
		}
	}
	else
	{
	$lic_lek=true;
	}

if ($lic_lek==false)
{
	echo "Для лечения других персонажей необходима лицензия лекаря...";
}else
if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif ($us['battle'] > 0) {
	echo "Персонаж в бою...";
} elseif (!$owntravma['id']) {
	echo "У персонажа нет легких травм...";
} elseif ($user['room'] != $us['room']) {
	echo "Персонаж в другой комнате!";
} elseif ($us['ldate'] < (time()-60) ) {
	echo "Персонаж не в игре!";
} elseif (rand(1,100) < $int) {

			if ($user['sex'] == 1) {$action="исцелил";}
			else {$action="исцелила";}
			if (($user['align'] > '2' && $user['align'] < '3') && $user['align'] != '2.4')  {
				$angel="Ангел";
			}
			elseif ($user['align'] > '1' && $user['align'] < '2') {
				$angel="Персонаж";
			}
				$bet=1;
				$sbet = 1;			
				echo "Персонаж &quot;{$_POST['target']}&quot; исцелен!";
// hidden by fred
$hidden = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE type=200 and `owner` = '{$user[id]}' LIMIT 1;"));
$hidden2 = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE type=200 and `owner` = '{$us[id]}' LIMIT 1;"));
		if($hidden[0])
		 {
				if($hidden2[0]) {
				addch("<img src=i/magic/zagorod_cure1.gif> ".$angel." &quot;<i>Невидимка</i>&quot; ".$action." от легких травм &quot;<i>Невидимка</i>&quot;",$user['room'],$user['id_city']);
				} else {
				addch("<img src=i/magic/zagorod_cure1.gif> ".$angel." &quot;<i>Невидимка</i>&quot; ".$action." от легких травм &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
				}
}
else
{
				if($hidden2[0]) {
				addch("<img src=i/magic/zagorod_cure1.gif> ".$angel." &quot;{$user['login']}&quot; ".$action." от легких травм &quot;<i>Невидимка</i>&quot;",$user['room'],$user['id_city']);
				} else {
				addch("<img src=i/magic/zagorod_cure1.gif> ".$angel." &quot;{$user['login']}&quot; ".$action." от легких травм &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
				}
}

				deltravma($owntravma['id']);

		if($us['id'] != $user['id'] && !$_SESSION['beginer_quest'][none]) 
		{				
			// квест
		        $last_q=check_last_quest(30);
		        if($last_q) 
			{
				quest_check_type_30($last_q,$user[id],3,1);
			}
		      
		}


} else {

				echo "Свиток рассыпался в ваших руках...";
				$bet=1;
	}
?>
