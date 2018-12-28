<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php");die();}
	if ($user['level'] <$magic['nlevel']) 
	{
	err('Вы не можете использовать эту лицензию, неподходящий уровень!');
	}
	else
	{
	//проверяем на другие лицензии
	$get_lic=mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}' and type in (50000,2000,30000)  ;"));
	if ($get_lic['id']>0)
	{
	err('Вы не можете использовать эту лицензию, у Вас уже есть лицензия другого типа!');
	}
	else
	{
	//1. проверка эффекта
	$get_eff=mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}' and type=40000;"));
	$duration=$magic['time']*60;
	 if ($get_eff[id]>0)
	 	{
	 	//есть
		mysql_query("INSERT INTO `effects` (`id`,`owner`,`name`,`time`,`type`) values ('{$get_eff[id]}','".$user['id']."','Лицензия лекаря',".(time()+$duration).",40000) ON DUPLICATE KEY UPDATE `time`=`time`+{$duration};");
		err('Удачно продлена лицензия лекаря!');
		$bet=1;
		$sbet=1;
	 	}
	 	else
	 	{
		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$user['id']."','Лицензия лекаря',".(time()+$duration).",40000);");
		err('Удачно открыта лицензия лекаря!');
		$bet=1;
		$sbet=1;		
	 	}
	 }	
	 	
	}
?>