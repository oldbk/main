<?php

$us = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '".mysql_real_escape_string($_POST['target'])."' LIMIT 1;"));
if ($us['id']>0)
{
$owntravmadb = mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$us['id']." AND (`type`=12 OR `type`=11 OR `type`=13) ;");
$ownt = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$us['id']." AND (`type`=12 OR `type`=11 OR `type`=13) LIMIT 1 ;"));
$magic = magicinf(21);
 if($user['room']==45 &&  $us['lab']>0)
{
	$us['room']=$user['room'];
}

if ($user['intel'] >= 8) {
		$int=$magic['chanse'] + ($user['intel'] - 8)*3;
		$int = $int * 0.95; //8/08/2012 - Ден+Алина правка процентов		
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
}
else
if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif ($us['battle'] > 0) {
	echo "Персонаж в бою...";
} elseif (!$ownt['type']) {
	echo "У персонажа нет тяжелых, средних или легких травм...";
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


			$travm="легких";
			$tt = 11;
			$bet=1;
			$sbet = 1;
			while ($owntravma = mysql_fetch_array($owntravmadb)) {
				if ($owntravma['type'] > $tt) {
					$tt = $owntravma['type'];
				}
				deltravma($owntravma['id']);
			}

			if ($tt == 11) $travm = "легких";
			if ($tt == 12) $travm = "средних";
			if ($tt == 13) $travm = "тяжёлых";

			echo "Персонаж &quot;{$_POST['target']}&quot; исцелен!";
		// hidden by fred
			if($user[hidden]>0)
			 {
				if($us[hidden]>0) {
				addch("<img src=i/magic/cure3.gif> ".$angel." &quot;<i>Невидимка</i>&quot; ".$action." от {$travm} травм &quot;<i>Невидимка</i>&quot;",$user['room'],$user['id_city']);
				addchp ("<font color=red>Внимание!</font> Вас ".$action." ".$angel." &quot;<i>Невидимка</i>&quot;  от {$travm} травм","{[]}".$us['login']."{[]}",$user['room'],$user['id_city']); 

				} else {
				addch("<img src=i/magic/cure3.gif> ".$angel." &quot;<i>Невидимка</i>&quot; ".$action." от {$travm} травм &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
				addchp ("<font color=red>Внимание!</font> Вас ".$action." ".$angel." &quot;<i>Невидимка</i>&quot;  от {$travm} травм ","{[]}".$us['login']."{[]}",$user['room'],$user['id_city']); 
			
				}
			}
			else
			{
				if($us[hidden]>0) {
				addch("<img src=i/magic/cure3.gif> ".$angel." &quot;{$user['login']}&quot; ".$action." от {$travm} травм &quot;<i>Невидимка</i>&quot;",$user['room'],$user['id_city']);
				} else {
				addch("<img src=i/magic/cure3.gif> ".$angel." &quot;{$user['login']}&quot; ".$action." от {$travm} травм &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
				}
				addchp ("<font color=red>Внимание!</font> Вас ".$action." ".$angel." &quot;{$user['login']}&quot;  от {$travm} травм ","{[]}".$us['login']."{[]}",$user['room'],$user['id_city']); 
			}
		
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
 }
 else
 	{
 	echo "Персонаж не найден...";
 	}
?>
