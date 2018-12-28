<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php");die();}
if ($user[level] < 4) 
{
   err("Вы слишком малы для этого боя! ");
}
else
{
$get_battle=mysql_fetch_array(mysql_query("select * from users_clons where (id_user=89) and battle>0 LIMIT 1;"));
 if ($get_battle[battle] > 0)
 {
 	$batt=$get_battle[battle];
 	$bd = mysql_fetch_array(mysql_query ('SELECT * FROM `battle` WHERE `id` = '.$batt.' LIMIT 1;'));

	$myeff = getalleff($user['id']);

	$check_bexit = mysql_fetch_array(mysql_query("SELECT bexit_count,bexit_team FROM `battle_vars`WHERE `owner` = '{$user['id']}' and battle = '{$batt}' LIMIT 1;"));
	if (($bd[win]!=3) OR ($bd[status]!=0)) { err("Бой уже окончен!");  }
	elseif (isset($myeff['owntravma'])) { err("С Вашей травмой, нельзя напасть!"); }
	elseif (isset($myeff[830])) { err("Вы находитесь под медитацией!"); }
	elseif ($user['hp'] < $user['maxhp']*0.33) {  err("Вы слишком ослаблены для нападения!");} 
	elseif ($check_bexit['bexit_count']>1) { err("Вы достигли лимита выхода-входа в этот бой...");}
	else
	{
	// ставим эфект СПЕЦ невидимости - затирая если есть что то - потом если надо восстановим
	{
		$idiluz=mt_rand(10,99).date("H").mt_rand(1,9).date("i").mt_rand(1,9).date("s");
		$duration=86400;
		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`idiluz`) values ('".$user['id']."','Иллюзия невидимости',".(time()+$duration).",202,'".$idiluz."');");
		mysql_query("UPDATE `users` SET `hidden`='{$idiluz}' , hiddenlog=''  where `id`='{$user['id']}';");
		$user[hidden]=$idiluz;
		$user[hiddenlog]='';
		
	}
	
	
	
	//вмешиваемся
	if ($user[level] >= 10) 	
		{ 
		$result=mysql_query("select battle_t, count(id) as kol from users where battle='{$batt}' and hp>0 and level >='{$user[level]}' group by battle_t");
		if (mysql_num_rows($result)>0)
			{
				while($cc=mysql_fetch_assoc($result))
					{
					if ($cc[battle_t]==1)
							{
							$inb1=$cc[kol];
							}
							else
							{
							$inb2=$cc[kol];
							}
					}
					
				if ($inb1>$inb2)
					{
					$za=2;	
					}
					else
					{
					$za=1;						
					}
			}
			else
			{
			$za=mt_rand(1,2); 			
			}
		} 
		else 
		{
		$za=mt_rand(1,2); 
		}
	
	$time = time();
	$sstatus=''; 
	$sexi[0]='вмешалась';
	$sexi[1]='вмешался';
	mysql_query('UPDATE `battle` SET '.$sstatus.'  to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$user['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($user).'\') WHERE `id` = '.$batt.' and win=3 and status=0;');
	  if (mysql_affected_rows()>0)
	   {
	    $bet=1;
		if ( ($user[hidden]>0) and ($user[hiddenlog]=='') )
		{ $usrlogin='<i>Невидимка</i>'; $user[sex]=1; }
		 else { 
		 $fuser = load_perevopl($user); 
		 $usrlogin=$fuser['login']; 
		 $user[sex]=$fuser['sex']; 
		 }
		addch ("<b>".$usrlogin."</b> ".$sexi[$user[sex]]." в <a href=logs.php?log=".$batt." target=_blank>поединок »»</a>.  ",$user['room'],$user['id_city']);
//		addlog($batt,'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$za).'  '.$sexi[$user[sex]].'  в поединок!<BR>');
		
		$user[battle_t]=$za;
		$ac=($user[sex]*100)+mt_rand(1,2);
//		addlog($batt,"!:V:".time().":".nick_new_in_battle($user).":".$ac."\n");					
		addlog($batt,"!:W:".time().":".BNewHist($user).":".$user[battle_t].":".$ac."\n");	
		
		
		mysql_query("UPDATE users SET `battle` =".$batt.",`zayavka`=0 , `battle_t`='{$za}' WHERE `id`= ".$user['id']);
		mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$batt}','{$user['id']}')");
		/// вот это еще надо  добавить уник индекс в базу
		mysql_query("INSERT `battle_vars` (battle,owner,update_time,type)  VALUES ('{$batt}','{$user['id']}','{$time}','1') ON DUPLICATE KEY UPDATE `update_time` = '{$time}' ;");
		header("Location:fbattle.php");
	   }
	   else
	   {
	   err("Бой окончен!");
	   }
	}
  }
  else
  {
  err("Нет такого боя!");
  }
}
?>
