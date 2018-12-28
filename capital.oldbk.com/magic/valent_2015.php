<?
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}

if (!(isset($_GET['clearstored'])))
{

$che=(int)($_POST['target']);
/*
echo "TEST";
echo "<br>";
print_r($_GET);
echo "<br>";
print_r($_POST);
*/
$effarray=array(
		9100=>'Получение репутации +30%',
		9102=>'Получение опыта +30%',
		9103=>'Получение рунного опыта +30%',
		9104=>'Таймаут в лабиринт -30%',						
		9105=>'Таймаут в ристалище -30%',
		9106=>'Таймаут в руины -30%');
		
	if (array_key_exists($che,$effarray))
	{
		$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '{$che}' LIMIT 1;")); 
			
		$add_time_eff=time()+($magic['time']*60);
		$updok=0;
		if ($effect['id']>0)
		{

			if ($che==9102) // опыт
			{
			//есть обновляем
			if ($effect['add_info']!='0.3')
				{
				//эффект был другой = велентинка затирает
					mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}' , `name`='{$effarray[$che]}'  , add_info='0.3'  WHERE `id`='{$effect['id']}' ");
					if(mysql_affected_rows()>0)
					{
					$updok=1;
					mysql_query("UPDATE users set expbonus=expbonus-'{$effect['add_info']}'+'0.3' where id='{$user[id]}' ; ");		
					}
				}
				else
				{
				//есть такойже эффект	
					mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}' WHERE `id`='{$effect['id']}' ");
					if(mysql_affected_rows()>0)
					{
					$updok=1;
					}
				}
			}
			elseif ($che==9100) // репа
				{
				$have_bonus=explode(":",$effect['add_info']);
				$have_bonus=$have_bonus[1];

					if ($have_bonus<0.3)				
						{
							mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}' , `name`='{$effarray[$che]}'  ,   add_info='{$rowm['img']}:0.3'   WHERE `id`='{$effect['id']}' ");
							if(mysql_affected_rows()>0)
							{
							$updok=1;
							mysql_query("UPDATE users set rep_bonus=rep_bonus-'{$have_bonus}'+'0.3' where id='{$user[id]}' ; ");		
							}
						}
						else
						{
						//есть такойже эффект	- просто апдейт времени
							mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}' WHERE `id`='{$effect['id']}' ");
							if(mysql_affected_rows()>0)
							{
							$updok=1;
							}
						}

				}
				else //  другие
				{
				
					if ($effect['add_info']!='0.3')
						{
						//эффект был другой = велентинка затирает
							mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}' , `name`='{$effarray[$che]}'  , add_info='0.3'  WHERE `id`='{$effect['id']}' ");
							if(mysql_affected_rows()>0)
							{
							$updok=1;
							}
						}
						else
						{
						//есть такойже эффект	
							mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}' WHERE `id`='{$effect['id']}' ");
							if(mysql_affected_rows()>0)
							{
							$updok=1;
							}
						}
				
				}
				
				
			
		}
		
		if ($updok==0)
		{
		
			if ($che==9102)
				{
				//нету вставляем
				mysql_query("INSERT INTO `effects` SET `type`= '{$che}',`name`='{$effarray[$che]}',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='0.3' ;");
				mysql_query("UPDATE users set expbonus=expbonus+'0.3' where id='{$user[id]}' ; ");		
				}
			elseif ($che==9100)
				{
				mysql_query("INSERT INTO `effects` SET `type`= '{$che}',`name`='{$effarray[$che]}',`time`='{$add_time_eff}',`owner`='{$user[id]}',  add_info='{$rowm['img']}:0.3' ;");
				mysql_query("UPDATE users set rep_bonus=rep_bonus+'0.3' where id='{$user[id]}' ; ");		
				}
				else
				{
				mysql_query("INSERT INTO `effects` SET `type`= '{$che}',`name`='{$effarray[$che]}',`time`='{$add_time_eff}',`owner`='{$user[id]}',  add_info='0.3' ;");
				}
		}
		

		echo "<font color=red>Удачно использована магия <b>\"{$effarray[$che]}\"</b></font>";
/*
		$sbet = 1;
				
		//если не подарок то пропадает
		if ($rowm['present']=='')
		{
		$bet=1;
		}
		else
		{
		
		if (($rowm['maxdur'] <= ($rowm['duration']+1))and ($rowm['magic']) )
			{*/
			$rowm['duration']=0; // заглушка чтоб не удалилась
			$goden=time()+15552000;
			mysql_query("UPDATE oldbk.`inventory` SET `magic` = 0 , `present` = 'От св. Валентина', `dategoden`='{$goden}' ,`goden`=180, `ekr_flag` = 0, sowner = ".$user['id']." WHERE `id` = {$rowm['id']} LIMIT 1;");
/*
			}
		}
*/
	}
	else
	{
			echo "<font color=red><b>:)</b></font>";
	}
}
?>