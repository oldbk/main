<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php");die();}

function record_goto($logid,$text)
{
	$logf="/www_logs7/goto_log/".$logid.".txt";
	$fp = fopen ($logf,"a");
	flock ($fp,LOCK_EX); 
	fputs($fp ,time()."|".$text."\n");
	fflush ($fp); 
	flock ($fp,LOCK_UN); 
	fclose ($fp); 
}

	$VR_GODA=date("n");
	$ZIMA_array=array(12,1,2);
	$VESNA_array=array(3,4,5);
	$LETO_array=array(6,7,8);
	$OSEN_array=array(9,10,11);
		
	if (in_array($VR_GODA,$ZIMA_array)) 
	{
		$ZIMA=true;
	} elseif (in_array($VR_GODA,$VESNA_array)) {
		$VESNA=true;	
	} elseif (in_array($VR_GODA,$OSEN_array)) {
		$OSEN=true;	
	} else {
		$LETO=true;
	}


function test_to_go($battle,$telo)
{
//только люди
$get_data=mysql_query("select sum(GetLevelPoint(if(`exp`>8000000000,14,`level`))) as tpoints, battle_t from users where battle='{$battle['id']}' and hp>0 group by battle_t");

if (mysql_num_rows($get_data) > 0) 
	{
		while($row=mysql_fetch_array($get_data))
		{
			$teams[$row['battle_t']]=$row['tpoints'];
		}
		
		if ($teams[1]>$teams[2]) 
			{
			record_goto($battle['id'],$teams[1]."|".$teams[2]."|2|".$telo['id']."|".$telo['login']);
			return 2;
			}
		elseif ($teams[1]<$teams[2]) 
			{
			return 1;
			record_goto($battle['id'],$teams[1]."|".$teams[2]."|1|".$telo['id']."|".$telo['login']);			
			}
		else
			{
			return false;			
			record_goto($battle['id'],$teams[1]."|".$teams[2]."|0|".$telo['id']."|".$telo['login']);						
			}
	}
return false;
}

if ($user[level] < 7) 
{
   err("Вы слишком малы для этого боя! ");
}
else
{

				if ($ZIMA)
	 				{
		 			$BOTS_conf=array(89);
		 			}
		 			else
		 			{
		 			$BOTS_conf=array(86,87);		 			
		 			}
			
$get_battle=mysql_fetch_array(mysql_query("select * from users_clons where id_user in (".implode(",",$BOTS_conf).")  and battle>0 LIMIT 1;"));

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

	$ok_battle_in=false;
	$za=test_to_go($bd,$user); // новый алгоритм весов
	//	$za=2; 		
	if ($za==false)
		{
		//нет данных или вес равный = рандом
		$za=mt_rand(1,2); 
		$user['battle_t']=$za;
		$ok_battle_in=true;						
		record_goto($battle['id'],"false|false|".$za."|".$user['id']."|".$user['login']);						
		}
		else
		{
		$user['battle_t']=$za;
		$ok_battle_in=true;								
		}
	
			if ($ok_battle_in)
				{
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
  }
  else
  {
  err("Нет такого боя!");
  }
}
?>
