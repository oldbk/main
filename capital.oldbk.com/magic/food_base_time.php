<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	$prof_data=GetUserProfLevels($user); 
	// Повар      Дополнительный бонус от еды: +...НР      20НР за уровень ремесла
	if (($prof_data['cooklevel']>0)	and ($addstat['maxhp']>0) )
		{
		$addstat['maxhp']+=(20*(int)($prof_data['cooklevel']));
		}


$get_food=mysql_fetch_array(mysql_query("select * from users_bonus where owner='{$user[id]}' ;"));

if ($user['battle'] > 0) {echo "Не в бою...";}
else
	if ($get_food['id']>0)
	{
	 echo "<font color=red>У вас есть похожий эффект!</font>";	
	}
else
	{
	
	if (is_array($addstat)) 
		{
		$sqlprm=array();
				foreach($addstat as $k => $v) 
				{
				$sqlprm[]=" `{$k}`=`{$k}` + ".(int)$v;		
				}
	 		
	 		if (count($sqlprm)>0)
	 			{
			  	$magictime="NOW() + INTERVAL {$magic['time']} MINUTE";
				mysql_query("INSERT into users_bonus SET ".implode(",",$sqlprm)." , owner='{$user[id]}' , finish_time=".$magictime."  ON DUPLICATE KEY UPDATE  ".implode(",",$sqlprm)." , finish_time=".$magictime." , refresh=refresh+1 ; ");
				 if (mysql_affected_rows()>0)
						 	{
						 	echo "<font color=red>Вы подкрепились</font></br>";
							$bet=1;
							$sbet = 1;
			  				}
	 			}
	 			else
	 			{
	 			echo "Ошибка параметров";
	 			}
	 	}
	 	else
	 	{
	 	echo "Ошибка конфигурации";
	 	}
	 
	}

?>
