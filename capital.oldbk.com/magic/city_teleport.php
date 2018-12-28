<?

if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); }
echo "<font color=red>Магия временно не работает!</font>";
/*
if ($user['klan'] != "radminion") {
	echo "<font color=red>Магия временно не работает!</font>";
	return;
}

elseif (($user[battle]!=0) OR ($user[battle_fin]!=0))
{
echo "<font color=red>Не в бою!</font>";
}
elseif ($user[zayavka]>0)
{
echo "<font color=red>Вы в заявке!</font>";
}
elseif ( 2==1)
{
echo "<font color=red>Нелетная погода, попробуйте позже...</font>";
}
elseif ((( ($user[room]!=20) AND ($user[room]!=21) AND ($user[room]!=66) AND ($user[room]!=26) AND ($user[room]!=50))) and ($user[klan]!='radminion') and ($user[klan]!='Adminion') )
{
echo "<font color=red>Использовать телепорт можно только на улице или площади!</font>"; 
}
else
//id_city=0-кеп / 1-авалон/2-angels
if ( (strtolower($_POST['target'])=='capital') OR ( strtolower($_POST['target'])=='capitalcity') OR ( strtolower($_POST['target'])=='cap') ) 
	{
	$rowm=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE id = ".$rowm[id]." limit 1 "));
	
		if ($user[id_city]!=0) {

		// собираем бонусы
		$bonus = array();
		$q = mysql_query('SELECT * FROM users_bonus WHERE owner = '.$user['id']);
		while($b = mysql_fetch_assoc($q)) {
			$bonus[] = $b;
		}
		mysql_query('DELETE from users_bonus where owner='.$user['id'].';');

		//портируемся
		$sexi[0]='а';$sexi[1]='';
		//echo "Полетели...";

		mysql_query("UPDATE users set odate=0 , id_city=0 where id={$user[id]} and id_city!=0 and battle = 0;");

		if (mysql_affected_rows()>0)
		{

		// переехали
		while(list($k,$v) = each($bonus)) {
			mysql_query('
			INSERT INTO oldbk.users_bonus (owner,sila,sila_count,lovk,lovk_count,inta,inta_count,intel,intel_count,mudra,mudra_count,maxhp,maxhp_count,expbonus,expbonus_count,refresh,battle,usec) 
			VALUES ("'.$v['owner'].'","'.$v['sila'].'","'.$v['sila_count'].'","'.$v['lovk'].'","'.$v['lovk_count'].'","'.$v['inta'].'","'.$v['inta_count'].'","'.$v['intel'].'","'.$v['intel_count'].'","'.$v['mudra'].'","'.$v['mudra_count'].'","'.$v['maxhp'].'","'.$v['maxhp_count'].'","'.$v['expbonus'].'","'.$v['expbonus_count'].'","'.$v['refresh'].'","'.$v['battle'].'","'.$v['usec'].'")
			');
		}

		if ($NOCHANGECITY != 1) {
			// меняем штраф на город
			$q = mysql_query('SELECT * FROM oldbk.map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"');
			if (mysql_num_rows($q) > 0) {
				// если есть штраф
				$lastcity = mysql_fetch_assoc($q) or die();
				$t = explode(":",$lastcity['val']);
				$t[1] = 0;
				mysql_query('UPDATE oldbk.map_qvar SET val = "'.implode(":",$t).'" WHERE owner = '.$user['id'].' AND var = "lastcity"');
			}
		}

		//fix bag!
		mysql_query("DELETE from oldbk.effects where owner={$user[id]}");
		//сразу перетащим эфекты
		mysql_query("INSERT INTO oldbk.effects SELECT NULL,`type`,`name`,`time`,`sila`,`lovk`,`inta`,`vinos`, `intel`, `owner`, `lastup`, `idiluz`,`pal`, `add_info`, `battle` ,`eff_bonus`  from {$db_city[$user[id_city]]}effects where owner={$user[id]}");
		//удаляем копии
		mysql_query("DELETE from {$db_city[$user[id_city]]}effects where owner={$user[id]}");
		
		$bet=1;
		$sbet = 1;
		//обновить инвентарь c подменой ИД тут остаются вещи которые в комке и остается отюзаный свиток!
		$TELEPORT_GOOD='to_capital';
		
  		// готовим данные для чара 
		// готовимся обновить уже в кепе чара на новые одетые ИД
		
		//надо найти и заюзать свиток/встройку
		if ($ABIL!=1)			
		   {

			addch("<img src=i/magic/teleport.gif> <B>{$user['login']}</B> использовал".$sexi[$user[sex]]." <i>Телепорт</i>...",$user['room'],$user['id_city']);

			// НЕТ ЗАПИСИ В ДЕЛО

		     if (($rowm['maxdur'] <= ($rowm['duration']+1)) and ($rowm['magic']) )
			{
			//echo "Заюзали свиток и он закончился надо удалить свиток  $id ";
			//работаем по т_ид т.к. он уже в другом городе
			mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` = '{$id}' and owner={$user[id]} LIMIT 1;");
			}
			else
			{
			//не свиток или не закончился
				if(!$rowm['magic']) 
				{
				//echo "Это была встройка делаем ей -1 $id ";
				
					mysql_query("UPDATE oldbk.`inventory` SET `includemagicdex` =includemagicdex-{$bet} WHERE owner={$user[id]}  AND `id` = {$id} LIMIT 1;");
				} 
				else 
				{
				//echo " Был свиток но не закончился делаем -1 $id ";
					mysql_query("UPDATE oldbk.`inventory` SET `duration` =`duration`+{$bet} WHERE `id` = {$id} and owner={$user[id]} LIMIT 1;");
				}
			}
		  }
		  else
		  {
		  //была абилка ниче не делаем пока
		    if ($PERSON==1)
		    	{
		    	//персональная абилка
		    	
					    	if ($get_abil[dailyc]>0) // если есть суточные то -1 к соточным
						{
						mysql_query("update oldbk.users_abils set dailyc=dailyc-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");						
						}
						else
						{
			    			mysql_query("update oldbk.users_abils set allcount=allcount-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");
			    			}
		    			
		    			
		    	}
		    else if ($klan_abil==1)
		    	{
					//удаление 1 юза
					if (($tabil[maxcount]-$tabil[count])>0)
					{
					// -1 юз из бесплатных					
					$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+1;
					mysql_query("update clans_abil set `count`=`count`+1, `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and maxcount!=count and maxcount!=0 and klan='".$user['klan']."'  ; ");
					mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '(из бесплатных)' ) ; ");
					}
					else
					{
					//-1 из платных
					$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+1;
					//считаем юзерский счет
					mysql_query("update clans_abil set `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and klan='".$user['klan']."'  ; ");
					//считаем из таблицы платных -1
					mysql_query("UPDATE `oldbk`.`abil_buy_clans` SET `all_count`=`all_count`-1 WHERE `magic_id`='".(int)($_POST['use'])."' AND `klan_name`='".$user['klan']."' ;");
					//пишем лог
					mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '(из платных)' ) ; ");					
					}		    		
		    	}
		  }

		
		//редирект и остановка
		if ($TELEPORT_GOOD=='to_capital')
		{


		$_SESSION['TELEPORT_GOOD']='capitalcity.oldbk.com';		
		echo "<script>".(!is_array($_SESSION['vk'])?"top.":"parent.")."window.location='/teleport_redir.php'</script>";		
		die();
		}

		
		
		}
		 else
		  {
		  echo "Ошибка телепорта!";
		  }
		
		}
		else
		{
		echo "<font color=red>Вы уже тут :)</font>";
		}
	}
 else if ( ( strtolower($_POST['target'])=='avalon') OR ( strtolower($_POST['target'])=='avaloncity')  OR ( strtolower($_POST['target'])=='ava') ) 
       {
       $rowm=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE id = ".$rowm[id]." limit 1 "));


       if ($user[id_city]!=1)
		{
		// собираем бонусы
		$bonus = array();
		$q = mysql_query('SELECT * FROM users_bonus WHERE owner = '.$user['id']);
		while($b = mysql_fetch_assoc($q)) {
			$bonus[] = $b;
		}

		mysql_query('DELETE from users_bonus where owner='.$user['id'].';');
		$sexi[0]='а';$sexi[1]='';
		//echo "Полетели...";

		mysql_query("UPDATE users set odate=0 , id_city=1 where id={$user[id]} and id_city!=1 and battle = 0;");
		if (mysql_affected_rows()>0)
		{

		// переехали
		while(list($k,$v) = each($bonus)) {
			mysql_query('
			INSERT INTO avalon.users_bonus (owner,sila,sila_count,lovk,lovk_count,inta,inta_count,intel,intel_count,mudra,mudra_count,maxhp,maxhp_count,expbonus,expbonus_count,refresh,battle,usec) 
			VALUES ("'.$v['owner'].'","'.$v['sila'].'","'.$v['sila_count'].'","'.$v['lovk'].'","'.$v['lovk_count'].'","'.$v['inta'].'","'.$v['inta_count'].'","'.$v['intel'].'","'.$v['intel_count'].'","'.$v['mudra'].'","'.$v['mudra_count'].'","'.$v['maxhp'].'","'.$v['maxhp_count'].'","'.$v['expbonus'].'","'.$v['expbonus_count'].'","'.$v['refresh'].'","'.$v['battle'].'","'.$v['usec'].'")
			');
		}


		if ($NOCHANGECITY != 1) {
			// меняем штраф на город
			$q = mysql_query('SELECT * FROM oldbk.map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"');
			if (mysql_num_rows($q) > 0) {
				// если есть штраф
				$lastcity = mysql_fetch_assoc($q) or die();
				$t = explode(":",$lastcity['val']);
				$t[1] = 1;
				mysql_query('UPDATE oldbk.map_qvar SET val = "'.implode(":",$t).'" WHERE owner = '.$user['id'].' AND var = "lastcity"');
			}
		}

		//bug fix
		mysql_query("DELETE from avalon.effects where owner={$user[id]}");
		
		mysql_query("INSERT INTO avalon.effects SELECT NULL,`type`,`name`,`time`,`sila`,`lovk`,`inta`,`vinos`, `intel` , `owner`, `lastup`, `idiluz`,`pal`, `add_info`, `battle` ,`eff_bonus`  from {$db_city[$user[id_city]]}effects where owner={$user[id]}");
		//удаляем копии
		mysql_query("DELETE from {$db_city[$user[id_city]]}effects where owner={$user[id]}");
				
		$bet=1;
		$sbet = 1;
		$TELEPORT_GOOD='to_avalon';
		
 		
 		// готовим данные для чара 
		// готовимся обновить уже в avalon чара на новые одетые ИД
		
		
		//надо найти и заюзать свиток/встройку
		if ($ABIL!=1)			
		   {

			addch("<img src=i/magic/teleport.gif> <B>{$user['login']}</B> использовал".$sexi[$user[sex]]." <i>Телепорт</i>...",$user['room'],$user['id_city']);
			// НЕТ ЗАПИСИ В ДЕЛО
		     if (($rowm['maxdur'] <= ($rowm['duration']+1))and ($rowm['magic']) )
			{
			//echo "Заюзали свиток и он закончился надо удалить свиток  $id ";
			//работаем по т_ид т.к. он уже в другом городе
			mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` = '{$id}' and owner={$user[id]} LIMIT 1;");
			}
			else
			{
			//не свиток или не закончился
				if(!$rowm['magic']) 
				{
				//echo "Это была встройка делаем ей -1 $id ";
				mysql_query("UPDATE oldbk.`inventory` SET `includemagicdex` =`includemagicdex`-{$bet} WHERE `id` = {$id} and owner={$user[id]} LIMIT 1;");
				} 
				else 
				{
				//echo " Был свиток но не закончился делаем -1 $id ";
					mysql_query("UPDATE oldbk.`inventory` SET `duration` =`duration`+{$bet} WHERE `id` = {$id} and owner={$user[id]} LIMIT 1;");
				}
			}
		  }
		  else
		  {
		  //была абилка ниче не делаем пока
		  	if ($PERSON==1)
		    	{
		    	//персональная абилка
		    		if ($get_abil[dailyc]>0) // если есть суточные то -1 к соточным
						{
						mysql_query("update oldbk.users_abils set dailyc=dailyc-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");						
						}
						else
						{
		    		    		mysql_query("update oldbk.users_abils set allcount=allcount-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");
		    		    		}
		    	}
		    	else if ($klan_abil==1)
		    	{
					//удаление 1 юза
					if (($tabil[maxcount]-$tabil[count])>0)
					{
					// -1 юз из бесплатных					
					$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+1;
					mysql_query("update clans_abil set `count`=`count`+1, `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and maxcount!=count and maxcount!=0 and klan='".$user['klan']."'  ; ");
					mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '(из бесплатных)' ) ; ");
					}
					else
					{
					//-1 из платных
					$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+1;
					//считаем юзерский счет
					mysql_query("update clans_abil set `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and klan='".$user['klan']."'  ; ");
					//считаем из таблицы платных -1
					mysql_query("UPDATE `oldbk`.`abil_buy_clans` SET `all_count`=`all_count`-1 WHERE `magic_id`='".(int)($_POST['use'])."' AND `klan_name`='".$user['klan']."' ;");
					//пишем лог
					mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '(из платных)' ) ; ");					
					}		    		
		    	}
		  	
		  }

		
		//редирект и стоп
		if ($TELEPORT_GOOD=='to_avalon')
		{

		$_SESSION['TELEPORT_GOOD']='avaloncity.oldbk.com';
		echo "<script>".(!is_array($_SESSION['vk'])?"top.":"parent.")."window.location='/teleport_redir.php'</script>";		
		die();
		}
	
		
		
		}
		 else
		  {
		  echo "Ошибка телепорта!";
		  }
		
		}
		else
		{
		echo "<font color=red>Вы уже тут :)</font>";
		}
       }
 else if (( ( strtolower($_POST['target'])=='angels') OR ( strtolower($_POST['target'])=='angelscity')  OR ( strtolower($_POST['target'])=='ang') )  and ($user[id]==14897 || $user[id]==248454) )
       {
       $rowm=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE id = ".$rowm[id]." limit 1 "));


       if ($user[id_city]!=2)
		{
		// собираем бонусы
		$bonus = array();
		$q = mysql_query('SELECT * FROM users_bonus WHERE owner = '.$user['id']);
		while($b = mysql_fetch_assoc($q)) {
			$bonus[] = $b;
		}

		mysql_query('DELETE from users_bonus where owner='.$user['id'].';');
		$sexi[0]='а';$sexi[1]='';
		//echo "Полетели...";

		mysql_query("UPDATE users set odate=0 , id_city=2 where id={$user[id]} and id_city!=2 and battle = 0;");
		if (mysql_affected_rows()>0)
		{

		// переехали
		while(list($k,$v) = each($bonus)) {
			mysql_query('
			INSERT INTO angels.users_bonus (owner,sila,sila_count,lovk,lovk_count,inta,inta_count,intel,intel_count,mudra,mudra_count,maxhp,maxhp_count,expbonus,expbonus_count,refresh,battle,usec) 
			VALUES ("'.$v['owner'].'","'.$v['sila'].'","'.$v['sila_count'].'","'.$v['lovk'].'","'.$v['lovk_count'].'","'.$v['inta'].'","'.$v['inta_count'].'","'.$v['intel'].'","'.$v['intel_count'].'","'.$v['mudra'].'","'.$v['mudra_count'].'","'.$v['maxhp'].'","'.$v['maxhp_count'].'","'.$v['expbonus'].'","'.$v['expbonus_count'].'","'.$v['refresh'].'","'.$v['battle'].'","'.$v['usec'].'")
			');
		}


		if ($NOCHANGECITY != 1) {
			// меняем штраф на город
			$q = mysql_query('SELECT * FROM oldbk.map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"');
			if (mysql_num_rows($q) > 0) {
				// если есть штраф
				$lastcity = mysql_fetch_assoc($q) or die();
				$t = explode(":",$lastcity['val']);
				$t[1] = 2;
				mysql_query('UPDATE oldbk.map_qvar SET val = "'.implode(":",$t).'" WHERE owner = '.$user['id'].' AND var = "lastcity"');
			}
		}

		//чистим перед вставкой
		mysql_query("DELETE from angels.effects where owner={$user[id]}");
		
		//вставка в новый город - из текущего

		
		mysql_query("INSERT INTO angels.effects SELECT NULL,`type`,`name`,`time`,`sila`,`lovk`,`inta`,`vinos`, `intel` , `owner`, `lastup`, `idiluz`,`pal`, `add_info`, `battle` ,`eff_bonus`  from {$db_city[$user[id_city]]}effects where owner={$user[id]}");
		//удаляем копии
		mysql_query("DELETE from {$db_city[$user[id_city]]}effects where owner={$user[id]}");
				
		$bet=1;
		$sbet = 1;
		$TELEPORT_GOOD='to_angels';
		
 		
 		// готовим данные для чара 

		
		//надо найти и заюзать свиток/встройку
		if ($ABIL!=1)			
		   {

			addch("<img src=i/magic/teleport.gif> <B>{$user['login']}</B> использовал".$sexi[$user[sex]]." <i>Телепорт</i>...",$user['room'],$user['id_city']);
			// НЕТ ЗАПИСИ В ДЕЛО
		     if (($rowm['maxdur'] <= ($rowm['duration']+1))and ($rowm['magic']) )
			{
			//echo "Заюзали свиток и он закончился надо удалить свиток  $id ";
			//работаем по т_ид т.к. он уже в другом городе
			mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` = '{$id}' and owner={$user[id]} LIMIT 1;");
			}
			else
			{
			//не свиток или не закончился
				if(!$rowm['magic']) 
				{
				//echo "Это была встройка делаем ей -1 $id ";
				mysql_query("UPDATE oldbk.`inventory` SET `includemagicdex` =`includemagicdex`-{$bet} WHERE `id` = {$id} and owner={$user[id]} LIMIT 1;");
				} 
				else 
				{
				//echo " Был свиток но не закончился делаем -1 $id ";
					mysql_query("UPDATE oldbk.`inventory` SET `duration` =`duration`+{$bet} WHERE `id` = {$id} and owner={$user[id]} LIMIT 1;");
				}
			}
		  }
		  else
		  {
		  //была абилка ниче не делаем пока
		  	if ($PERSON==1)
		    	{
		    	//персональная абилка
		    		if ($get_abil[dailyc]>0) // если есть суточные то -1 к соточным
						{
						mysql_query("update oldbk.users_abils set dailyc=dailyc-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");						
						}
						else
						{
		    		    		mysql_query("update oldbk.users_abils set allcount=allcount-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");
		    		    		}
		    	}
		    	else if ($klan_abil==1)
		    	{
					//удаление 1 юза
					if (($tabil[maxcount]-$tabil[count])>0)
					{
					// -1 юз из бесплатных					
					$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+1;
					mysql_query("update clans_abil set `count`=`count`+1, `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and maxcount!=count and maxcount!=0 and klan='".$user['klan']."'  ; ");
					mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '(из бесплатных)' ) ; ");
					}
					else
					{
					//-1 из платных
					$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+1;
					//считаем юзерский счет
					mysql_query("update clans_abil set `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and klan='".$user['klan']."'  ; ");
					//считаем из таблицы платных -1
					mysql_query("UPDATE `oldbk`.`abil_buy_clans` SET `all_count`=`all_count`-1 WHERE `magic_id`='".(int)($_POST['use'])."' AND `klan_name`='".$user['klan']."' ;");
					//пишем лог
					mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '(из платных)' ) ; ");					
					}		    		
		    	}
		  	
		  }

		
		//редирект и стоп
		if ($TELEPORT_GOOD=='to_angels')
		{

		$_SESSION['TELEPORT_GOOD']='angelscity.oldbk.com';
		echo "<script>".(!is_array($_SESSION['vk'])?"top.":"parent.")."window.location='/teleport_redir.php'</script>";		
		die();
		}
	
		
		
		}
		 else
		  {
		  echo "Ошибка телепорта!";
		  }
		
		}
		else
		{
		echo "<font color=red>Вы уже тут :)</font>";
		}
       }       
  else
    {
    echo "<font color=red>Такого города нет!</font>";
    }
  */
?>