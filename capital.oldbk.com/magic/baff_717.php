<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Круговая оборона';
$baff_type=717;//817-начало 818-задержка использования

if ($user['battle'] == 0) 
{	
	echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
else 
{
	
	
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and battle='{$user[battle]}' and type=817 ; "));
	if (($get_test_baff[id] > 0) )
	{
		err('Вы уже использовали это заклятие, ожидайте его окончания!');
	}
	else
	 {
	$get_imun_baff=mysql_fetch_array(mysql_query("select * from battle_data where battle='{$user[battle]}' ; "));
	if (($get_imun_baff[baff_717]+60*60) > time() )
		{
		$ltm=$get_imun_baff[baff_717]+60*60;
		err("Вы сможете использовать это заклятие через ".floor(($ltm-time())/60/60)." ч. ".round((($ltm-time())/60)-(floor(($ltm-time())/3600)*60))." мин.");
		}
		else
		{
		//проверяем данные о бое
		$get_battle_data= mysql_fetch_array(mysql_query("select * from battle where id='{$user[battle]}' and win=3 and `status`=0 and t1_dead=''; "));
		if ($get_battle_data[id]>0)
		{
		$start_battle=explode(' ',$get_battle_data['date']);
		$start_date=$start_battle[0];
		$start_time=$start_battle[1];
		$start_date=explode('-',$start_date);
		$start_time=explode(':',$start_time);
		$start_battle_time=mktime($start_time[0], $start_time[1], $start_time[2], $start_date[1], $start_date[2], $start_date[0] )+ 5*60; //+5 минут
		if ($start_battle_time > time())
			{
			err('Можно использовать только через 5 минут после начала боя!');
			}
		 	elseif ($get_battle_data[teams]!='')
			{
			err("Этот бой уже закрыт от вмешательства!");
			}
			elseif ($get_battle_data['type'] != 3 AND $get_battle_data['type'] != 2)
			{
			err('Можно использовать только в хаотических или групповых боях!');
			}
			else
			{
			// проверяем  сколько есть уже в этом бою закастовавших это заклинание 
			$get_count_baff=mysql_query("select  * from effects where battle='{$user[battle]}' and type=817  and owner in (select id from users where battle='{$user[battle]}' and battle_t='{$user[battle_t]}' and hp >0); ");
			$kow=0;
			while ($baff_owners = mysql_fetch_array($get_count_baff))
			   	{
				$kow++;
				$remem_own[$kow]=$baff_owners[owner];
				$remem_time[$kow]=$baff_owners[time];
				$remem_baff_id[$kow]=$baff_owners[id];
			   	}
		
			if ($kow==0)
			{
			//никого я начинаю
				mysql_query("INSERT INTO `effects` SET `type`='817',`name`='Начало Круговая оборона', `time`='".(time()+60)."', `owner`='{$user[id]}',`battle`='{$user[battle]}';");//1 минута
				if (mysql_affected_rows()>0)
				{
				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>(Начал цепь...)</i> <BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(Начал цепь...)</i>\n");


				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			}
			else if ($kow==1)
			{
			//есть 1 я продолжаю
				mysql_query("INSERT INTO `effects` SET `type`='817',`name`='Продолжение Круговая оборона',`time`='".$remem_time[1]."',`owner`='{$user[id]}',`battle`='{$user[battle]}';");//время дублируем с эфекта начала
				if (mysql_affected_rows()>0)
				{
				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>(Продолжил цепь...)</i> <BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(Продолжил цепь...)</i>\n");
				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			}
			else
			{
			//есть двое я замыкаю и включаю баф
			//удаляем начало бафа
			mysql_query("UPDATE battle set teams='{$baff_name}' WHERE `id` = '{$user[battle]}' and status=0 and win=3  LIMIT 1;");
			if (mysql_affected_rows()>0)
				{
				    foreach($remem_own as $ic=>$owner)
					{
					mysql_query("DELETE from effects where `type`='817' and owner='{$owner}' and battle='{$user[battle]}'  ;"); 	
					 }	
				//ставим эффект закрытия боя и закрываем бой
				//открытие боя произходит в кроне в обработке эффектов по типу  717 
				mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=".(time()+600).",`owner`='{$user[id]}', `battle`='{$user[battle]}';");//  вешаем эфект на 10 минут
				
				//ставим иммунитет на 
				mysql_query("INSERT INTO `battle_data` SET battle='{$user[battle]}' , baff_717=".time()."  ON DUPLICATE KEY UPDATE  baff_717=".time()." ; ");
				
				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

				
//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>(Закрыл цепь...)</i> <BR>');									
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(Закрыл цепь...)</i>\n");

				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
				else
				{
				 err('Этот бой уже окончен!');
				}
				
			 }
			   
			} 
		}
		else
		{
		 err('Этот бой уже окончен!');
		}   
			   
			   
			   
		}
	  }	
		
}	
	


?>
