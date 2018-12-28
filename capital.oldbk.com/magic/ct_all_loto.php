<?php
$us = mysql_fetch_array(mysql_query("SELECT *  FROM `users` WHERE `login` = '".mysql_real_escape_string($_POST['target'])."' LIMIT 1;"));
if ($us['id']>0)
{
if (ADMIN) {
	$travma = mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$us['id']."' AND (`type`='11' OR `type`='12' OR `type`='13' OR `type`='14');");
} else {
	$travma = mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$us['id']."' AND (`type`='11' OR `type`='12' OR `type`='13');");
}
if($user['room']==45 &&  $us['lab']>0)
{
	$us['room']=$user['room'];
}
if($user['klan']== 'radminion' || $user['klan']== 'Adminion')
{
	$us['room']=$user['room'];
	$us['ldate']=time();
}

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
} elseif ($user['in_tower'] > 0) {
	echo "Не работает в БС...";
}
 elseif ($us['battle'] > 0) {
	echo "Персонаж в бою...";
} elseif ($user['room'] != $us['room']) {
	echo "Персонаж в другой комнате!";
} elseif ($us['ldate'] < (time()-60) ) {
	echo "Персонаж не в игре!";
} else
 {

			if ($user['sex'] == 1) {$action="исцелил";}
			else {$action="исцелила";}
			if ($user['align'] > '2' && $user['align'] < '3')  {
				$angel="Ангел";
			}
			elseif ($user['align'] > '1' && $user['align'] < '2') {
				$angel="Персонаж";
			}


				while ($owntravma=mysql_fetch_array($travma)) {
					deltravma($owntravma['id']);
				$c++;
				}
				if ($c ==0)
					{
					echo "У персонажа нет травм...";
					}
				else {
				
				if ($user[hidden]>0)
				{
				$nickk='<i>Невидимка</i>';
				$angel='';
				}
				else
				{
				$nickk=$user['login'];				
				}


				if ($us['hidden'] > 0) {
					$_POST['target'] = '<i>Невидимка</i>';
				}

				addch("<img src=i/magic/cure3.gif> $angel &quot;{$nickk}&quot; ".$action." от травм &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
				echo "<b>Все прошло удачно!</b>";
				$bet=1;
				$sbet = 1;
					}

}
 }
 else
 	{
 	echo "Персонаж не найден...";
 	}
?>