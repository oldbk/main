<?
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}


$effarray=array(
	9101=>'Увеличение получаемой репутации 20%',
	9102=>'Увеличение получаемого опыта на 20%',
	9103=>'Увеличение получаемого рунного опыта на 20%',
	9104=>'Уменьшение таймаута на посещение Лабиринта Хаоса на 20%',						
	9105=>'Уменьшение таймаута на посещение Ристалища на 20%',
	9106=>'Уменьшение таймаута на посещение Руин Старого Замка на 20%',
	9107=>'Уменьшение таймаута на квест в загороде на 20%',
);


if ($che == 9108) {
	$effarray[9103] = 'Увеличение получаемого рунного опыта на 30%';
	$che = 9103;
	$addinfo = 0.3;
} 
elseif ($che == 20104) {
	$effarray[9104] = 'Уменьшение таймаута на посещение Лабиринта Хаоса на 50%';
	$che = 9104;
	$addinfo = 0.5;
} 
elseif ($che == 20105) {
	$effarray[9105] = 'Уменьшение таймаута на посещение Ристалища на 50%';
	$che = 9105;
	$addinfo = 0.5;
} 
elseif ($che == 20106) {
	$effarray[9106] = 'Уменьшение таймаута на посещение Руин Старого Замка на 50%';
	$che = 9106;
	$addinfo = 0.5;
}
elseif ($che == 20107) {
	$effarray[9107] = 'Уменьшение таймаута на квест в загороде на 50%';
	$che = 9107;
	$addinfo = 0.5;
}
else {
	$addinfo = 0.2;
}


if ($che>0)
{
		
	if (array_key_exists($che,$effarray))
	{
		$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '{$che}' LIMIT 1;")); 
			
		$add_time_eff=time()+($magic['time']*60);
		$updok=0;
		if (($effect['id']>0) and ($effect['add_info'] >= $addinfo) )
		{
				echo "<font color=red>Нельзя использовать пока есть эффект более высокого действия!</b></font>";
				$updok=1;
				$aerror=1;
		}
		else
		if ($effect['id']>0)
		{
		//есть обновляем
			mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}',`name`='{$effarray[$che]}', add_info='{$addinfo}' WHERE `id`='{$effect['id']}' ");
			if(mysql_affected_rows()>0)
			{
			$updok=1;
			}
		}
		if ($updok==0)
		{
			//нету вставляем

			mysql_query("INSERT INTO `effects` SET `type`= '{$che}',`name`='{$effarray[$che]}',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='{$addinfo}' ;");
		
			if ($che==9102)
				{
				mysql_query("UPDATE users set expbonus=expbonus+'0.2' where id='{$user[id]}' ; ");		
				}
			else
			if ($che==9106)
				{
				
					$get_tim=mysql_fetch_array(mysql_query("select * from ruines_var where owner='{$user['id']}' and var='cango'"));
					if ($get_tim['val']>time()) {
						if ($get_tim['val']-time() > 8*3600) {
							echo "<font color=red><b>Что-то не сработало...</b></font>";
							return;
						} else {
							$updt=(int)(($get_tim['val']-time())*$addinfo);
							mysql_query("UPDATE `oldbk`.`ruines_var` SET `val`=`val`-'{$updt}'   WHERE `owner`='{$user['id']}' AND `var`='cango';");
						}
					}
				
				}
			else
			if ($che==9107)
				{
					$get_tim=mysql_fetch_array(mysql_query("select * from map_var where owner='{$user['id']}' and var='cango'"));
					if ($get_tim['val']>time())
						{
						$updt=(int)(($get_tim['val']-time())*$addinfo);
						mysql_query("UPDATE `oldbk`.`map_var` SET `val`=`val`-'{$updt}'   WHERE `owner`='{$user['id']}' AND `var`='cango';");
						}
				}
			else
			if ($che==9105)
				{
				// снимаем время если есть для ристалища Одиночных Сражений
					$get_tim=mysql_fetch_array(mysql_query("SELECT * from effects where `owner`='{$user['id']}' and type=8270"));
					if ($get_tim['time']>time())
						{
						$updt=(int)(($get_tim['time']-time())*$addinfo);
						mysql_query("UPDATE effects SET `time`=`time`-'{$updt}'   WHERE `owner`='{$user['id']}' AND `type`='8270' ");
						}
				}				
			
				
		}
		
		if ($aerror!=1)
			{
			echo "<font color=red>Удачно использована магия <b>\"{$effarray[$che]}\"</b></font>";
			$sbet = 1;
			$bet=1;
			}
	}
	else
	{
			echo "<font color=red><b>:)</b></font>";
	}
}
else
	{
	echo "";
	}
?>