<?php


// нападалка от новой боевки !!!
$jert = mysql_fetch_array(mysql_query("SELECT *  FROM `users` WHERE `id` = '{$_GET[post_attack]}' LIMIT 1;"));

$myeff = getalleff($user['id']);
$jeff = getalleff($jert['id']);

//комнаты для аркана из которых он вытягивает
$rooms_jert_arkan=array (15,17,18,36,56,54,55); 

	if ($USE_ARKAN)
	{
		echo "Номер войны:".$mystatus[war_id];
	 	//addchp ('<font color=red>ARKAN</font>'.$user[login].' vrag: '.$jert[login].' /wid'.$mystatus[war_id],'{[]}Bred{[]}');
		//загружаем кол.юзов аркана для проверки
		$get_arkans=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans_war_log where war_id='{$mystatus[war_id]}' and winner=0 and type=2; "));
		if ($myteam==1)
		{
			$my_side_is='agrr';
		}
		elseif ($myteam==2)
		{
			$my_side_is='def';
		}
		else {
			die('error');
		}
	
		$ccamax=$my_side_is."_arkan_maxcount";
		$ccacount=$my_side_is."_arkan_count";
	}

//шансы сработки
$int=101;
$time_out=3;
$battle_type=100;
$attack_to = 0;


// если жертва в бою готовим данные
if ($jert['battle']>0)
{
	$check_bexit = mysql_fetch_array(mysql_query("SELECT bexit_count,bexit_team FROM `battle_vars` WHERE `owner` = '{$user['id']}' and battle = '{$jert['battle']}' LIMIT 1;"));
	$bd = mysql_fetch_array(mysql_query ('SELECT * FROM `battle` WHERE `id` = '.$jert['battle'].' LIMIT 1;'));
}

$grant_continue = false;
if($user[klan]!='')
{
	
	$klan = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user['klan']}' LIMIT 1;"));
	if($klan[rekrut_klan]>0)
	{
		$recrut=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$klan[rekrut_klan]."' LIMIT 1;"));
		if($jert[klan]==$recrut[short])
		{
			$jert[klan]=$user[klan];	
		}
	}
	else
	if($klan[base_klan]>0)
	{

		$base_klan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$klan[base_klan]."' LIMIT 1;"));
		if($jert[klan]==$base_klan[short])
		{
			$jert[klan]=$user[klan];
		}
	}

}

if ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif($jert[id_city]!=$user[id_city])
	{
	err('Персонаж в другом городе!');
} elseif ($jert['zayavka'] > 100000000) {
	echo "Персонаж находится в очереди на бой склонностей...";
}
elseif (isset($myeff['owntravma'])) {
	echo "С Вашей травмой, нелья напасть!";
} elseif (isset($myeff[830])) {
	echo "Вы находитесь под медитацией!";
}elseif (($user['lab']>0) or ($user['room']==45) or ($jert['room']==45) or ($jert['lab']>0) || ($user['room'] >= 70000 && $user['room'] <= 72001) || ($user['room'] >= 91 && $user['room'] <= 97))  {
	echo "Нападения в этой локации запрещены!";
}elseif($user['in_tower']>0 || ($user['room'] >= 49998 && $user['room'] <= 60000)){
	echo "Тут это не работает...";
}elseif (($user['room'] >=197)AND($user['room'] <=199)) {
     echo "Нападения в этой локации запрещены!";
}
elseif ($user['room'] ==999) {
     echo "Нападения в этой локации запрещены!";
}
elseif ($user['room'] ==72001) {
     echo "Нападения в этой локации запрещены!";
}
elseif (($user['room'] >=210)AND($user['room'] <=300)) {
	echo "Тут это не работает...";
}
elseif ($jert['ldate'] < (time()-60) && $jert['battle']==0) {
	echo "Персонаж не в игре!";
} elseif($jert['id'] == $user['id']) {
	echo "Мазохист?..";
} elseif($bd['teams']!=''){
	echo "Бой закрыт от вмешательства...".$bd['teams'];
} elseif(($bd['type']!=100) and ($bd['type']!=101)  and ($jert['battle']>0) ){
	echo "Это не клановый бой...";
} /*elseif($jert['battle']==0 && $can_start==0){
	echo "Вы не можете начать клановый бой...";
}*/
elseif ($check_bexit['bexit_count']>0 and $check_bexit['bexit_team']==$jert[battle_t] and $jert[battle] > 0) {
	echo "Вы уже были в бою за противоположную сторону... нельзя вмешаться теперь за другую...";
} elseif ($user['zayavka'] > 0) {
	echo "Вы ожидаете поединка...";
} elseif (isset($jeff[830])) {
	echo "Персонаж находится под медитацией...";
} elseif (isset($jeff['owntravma']) && !$jert['battle']) {
	echo "Персонаж тяжело травмирован...";
} elseif ($user['klan'] != '' && ($user['klan'] == $jert['klan'] && $jert['klan'] != 'radminion')) {
//} elseif ($user['klan'] != '' && ($user['klan'] == $jert['klan'] && $jert['klan'] != 'rrrrr')) {
	echo "Чтите честь ваших сокланов.";
} /*elseif ($user['align'] >=1 && $user['align'] <2 && $jert['align'] >1 && $jert['align'] <2) {
	echo "Чтите честь братьев.";
} elseif ($user['align']  == 6 && $jert['align'] >=1 && $jert['align'] < 2) {
	echo "Чтите честь братьев.";
} */
 elseif ($user['room'] == 60 ) {
	echo "Тут это не работает...";
}
elseif (($user['room'] != 1) and ($USE_ARKAN)) {
	echo "Вы можете использовать аркан только находясь в Комнате для новичков!";
}
elseif (($USE_ARKAN) and (!($get_arkans)) ) {
	echo "Нет арканов!";
}
elseif ( ($USE_ARKAN)  and ($get_arkans[$ccacount]>=$get_arkans[$ccamax])) {
	echo "Нет арканов!!";
}
 elseif (($USE_ARKAN) and (!(in_array($jert['room'],$rooms_jert_arkan))) ) {
	echo "Персонаж не находится в залах склонностей!";
}
elseif ( ($USE_ARKAN) and ($jert[battle]>0)) {
	echo "Нельзя использовать на персонажа, который уже в бою!";
}
 elseif (($user['room'] != $jert['room']) and (!($USE_ARKAN))) {
	echo "Персонаж в другой комнате!";
} elseif ($jert['room'] == 31 || $jert['room'] == 43 || $jert['room'] == 200 || $jert['room'] == 10000 || $jert['room'] == 72) {
	echo "Нападения в этой локации запрещены!";
}
/*elseif (($jert['klan'] == 'radminion' || ($jert['align'] > '2' && $jert['align'] < '3')) && $user['klan'] != 'radminion' && $user[align]!=5)
	{
	 	echo "Какой ужас! Ты уверен? Не сейчас...";
		settravmazol($_SESSION['uid']);
		addch("<img src=i/magic/attack.gif> <B>{$user['login']}</B>, попытался напасть на &quot;{$_POST['target']}&quot;, но внезапно почувствовал слабость...");
		mysql_query("UPDATE `users` SET `hp`=0 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
	}
elseif (($jert['klan'] == 'radminion' || ($jert['align'] > '2' && $jert['align'] < '3')) && $user['klan'] != 'radminion' && $jert['id']!=4 && $jert['id']!=3 && $jert['id']!=2 && $jert['id']!=6)
	{
	 	echo "Какой ужас! Ты уверен? Не сейчас...";
		settravmazol($_SESSION['uid']);
		addch("<img src=i/magic/attack.gif> <B>{$user['login']}</B>, попытался напасть на &quot;{$_POST['target']}&quot;, но внезапно почувствовал слабость...");
		mysql_query("UPDATE `users` SET `hp`=0 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
	}*/
elseif ($jert['level'] < 1) {
	echo "Новички находятся под защитой мироздателя!";
} elseif ($jert['hp'] < $jert['maxhp']*0.33  && !$jert['battle']) {
	echo "Жертва слишком слаба!";
} elseif ($user['hp'] < $user['maxhp']*0.33) {
	echo "Вы слишком ослаблены для нападения!";
} elseif ($bd[type] == 15) {
	echo "Нельзя вмешаться в квестовый бой!";
} elseif ($bd[type] == 20) {
	echo "Нельзя вмешаться в футбольный бой!";
} elseif ($bd[fond]>0) {
	echo "Нельзя вмешаться в бой на деньги!";
} elseif ($jert['hp'] < 1  && $jert['battle']>0) {
	echo "Вы не можете напасть на погибшего!";
} elseif ($jert['battle']>1 && $check_bexit['bexit_count']>1) {     //в клановых нет смысла проверять выходы
  echo '<font color=red>Вы достигли лимита выхода-входа в этот бой...</font>';
  if($user['klan'] == 'radminion') {echo 'attackk ['.$check_bexit['bexit_count'].'/1]';}
}
elseif(!($_POST['dropability']) && $jert['battle'])
{
	//echo "0001";
	// заход против соклана
	// только если чар в клане! нахер лишние запросы
	if ($user['klan']!='')
	{
		$is_clan = false;
		$clans_query=mysql_fetch_array(mysql_query("SELECT * from `users` where `klan`='{$user['klan']}' and `battle_t`='{$jert['battle_t']}' and `battle`='{$jert['battle']}' LIMIT 1;")); //достаточно одной таблетки
		if ($clans_query[0]>0)
		{
		$is_clan = true;
		}

		if($is_clan)
		{
		$grant_continue = false;
		echo "<form id='formability' action='".$_SERVER['PHP_SELF']."' method='POST'><input type='hidden' name='sd4' value='".$user[id]."'><input type='hidden' name='use' value='".(int)($_POST['use'])."'><input id='target' name='target' type='hidden' value='".$jert['login']."' /><input id='dropability' name='dropability' type='hidden' value='1'/></form><script type='text/javascript'>var cat = confirm('С вac будет снят значек и склонность, если вы продолжите. Напасть?');if(cat) { document.getElementById('formability').submit(); }</script>";
		}
		else
		{
		$grant_continue = true;
		}
	} // конец проверки если чар клановый
	 else
	  {
	  $grant_continue = true;
	  }
	// если бой НЕ ХАОТ
	if ($user[align]!=0)
	{
	// если есть алигн то проверяем типы боя


	  	if  ((int)($bd['CHAOS'])==0)
		{
			// светлый  за темного
			if (($user['align'] >= 1 && $user['align'] < 2) || $user['align'] == 6)
			{
				$is_dark = false;
				if ($jert['battle_t']==1) {$za=2;} else {$za=1;}
				$ali_query = mysql_fetch_array(mysql_query("SELECT * FROM `users` where `align`=3 and `battle_t`='{$za}' and `battle`='{$jert['battle']}' LIMIT 1;")); //достаточно одной таблетки
				if($ali_query[0]>0)
					{
					$is_dark = true;
					}


				if($is_dark)
				{
					$grant_continue = false;
					echo "<form id='formability' action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'><input id='target' name='target' type='hidden' value='".$jert['login']."' /><input id='dropability' name='dropability' type='hidden' value='1'/></form><script type='text/javascript'>var cat = confirm('С вac будет снята склонность, если вы продолжите. Напасть?');if(cat) { document.getElementById('formability').submit(); }</script>";
				}
				else
				{
					$grant_continue = true;
				}
			}
			else
			// темный за светлого
			if ($user['align'] == 3)
			{
				$is_light = false;
				if ($jert['battle_t']==1) {$za=2;} else {$za=1;}
				$ali_query = mysql_fetch_array(mysql_query("SELECT * FROM `users` where ( (`align` >= 1 and `align` < 2) OR `align`=6 ) and `battle_t`='{$za}' and `battle`='{$jert['battle']}' LIMIT 1;")); //достаточно одной таблетки
				if($ali_query[0]>0)
				{
					$is_light = true;
				}

				if($is_light)
				{
					$grant_continue = false;
					echo "<form id='formability' action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'><input id='target' name='target' type='hidden' value='".$jert['login']."' /><input id='dropability' name='dropability' type='hidden' value='1'/></form><script type='text/javascript'>var cat = confirm('С вac будет снята склонность, если вы продолжите. Напасть?');if(cat) { document.getElementById('formability').submit(); }</script>";
				}
				else
				{
					$grant_continue = true;
				}
			}
		}//chaos
  	}
  	else // если юзер без алигна
  	 {
		$grant_continue = true;
  	 }
}
elseif(!(rand(1,100) < $int))
{
	echo "Свиток рассыпался в ваших руках...";
	$bet=1;
}
else
{
	$grant_continue = true;

}

if ($grant_continue) {

		if(isset($_POST['dropability']))
		{
			Test_Arsenal_Items($user);
			mysql_query("UPDATE users set klan='', status='', align=0 where id='{$user['id']}'");
			mysql_query("INSERT INTO `lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$user['id']."','Вмешался в поединок, и потерял склонность ".$user[klan].", напал на {$_POST['target']} ','".time()."');");
			ref_drop($user['id']);
		}

			if ($user['sex'] == 1) {$action="напал";}	else {$action="напала";}
			if ($user['align'] > '2' && $user['align'] < '3')  {
				$angel="Ангел";
			} elseif ($user['align'] >= '1' && $user['align'] < '2') {
				$angel="Персонаж";
			}



			if($jert['id']!=$user['id']) {
				// hidden by fred
				// new hiddent

				$bet=1;
				$sbet = 1;
				if($jert['battle'] > 0)
				{
					$battle_war =  mysql_fetch_array(mysql_query("SELECT * FROM `battle` WHERE id=".$jert['battle'].";"));
                    if($mystatus['war_id']==$battle_war['war_id'])
                    {
                        if($myteam==1 && $jert['battle_t']==2)
                        {
                        	$access=1;
                        }
                        else
                        if($myteam==2 && $jert['battle_t']==1)
                        {
                        	$access=1;
                        }
                        else
                        if($myteam==1 && $jert['battle_t']==1 || $myteam==2 && $jert['battle_t']==2)
                        {
                        	echo '<font color=red>Вмешиваться за своих?..</font>';
                        }
					    // проставляем кто-где
						if($access==1)
	                    {


							if ($jert['battle_t']==1) {$za=2;} else {$za=1;}
							$time = time();
							if ($check_bexit['bexit_count']>0)
							{
								$sexi[0]='вернулась';
								$sexi[1]='вернулся';
								$t1b=explode(";",$bd[t1]);
								$t2b=explode(";",$bd[t2]);
								 if  (in_array ($user[id], $t1b) AND ($za==2) )
								 	{
								 	//был в первой команде стал во второй - надо добавить в масив
								 		$asdf='`t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$user['id'].'\') , ';
								 	}
								 elseif  (in_array ($user[id], $t2b) AND ($za==1) )
								 	{
								 	//был в 2 команде стал в 1 - надо добавить в масив
								 		$asdf='`t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$user['id'].'\') , ';
								 	}


								mysql_query('UPDATE `battle`  SET '.$asdf.'  to1='.$time.', to2='.$time.', `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($user).'\') WHERE `id` = '.$jert['battle'].' ;');
								$err=mysql_error();
								//addchp ('<font color=red>Внимание!</font> attak1 error'.$err.' ','{[]}Bred{[]}'); 
							}
							else
							{
								$sexi[0]='вмешалась';
								$sexi[1]='вмешался';

								//ставим первый статус если надо
						  if ($bd[status_flag]==0)
						   {
						   $usr_in_b=users_in_battle($jert['battle']);
						   if (($bd[type]==100) and ($usr_in_b >= 29) )
						   	{
						   	   $sstatus=" type=101 ,";
						   	}
						   /*
						   else	
						   if  ($usr_in_b >= 99)
						    	{
						    	   //не хаот
   						    	    $sstatus=" status_flag=1 ,";
						    	}
						    */
						    	else
						    	{
						    	$sstatus='';
						    	}
						   }
						   else { $sstatus=''; }

								mysql_query('UPDATE `battle` SET '.$sstatus.'  to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$user['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($user).'\')   WHERE `id` = '.$jert['battle'].' ;');
								$err=mysql_error();
								//addchp ('<font color=red>Внимание!</font> attak2 error'.$err.' ','{[]}Bred{[]}'); 								
							}

							if ( ($user[hidden]>0) and ($user[hiddenlog]=='') )
							{ 
							$usrlogin='<i>Невидимка</i>'; 
							$doit_txt=$sexi[1];							
							$user[sex]=1;
							} else
							{
							$fuser = load_perevopl($user); 
							$usrlogin=$fuser['login']; 
							$user[sex]=$fuser[sex];
							$doit_txt=$sexi[$user[sex]];
							}


							addch ("<b>".$usrlogin."</b> ".$sexi[$user[sex]]." в <a href=logs.php?log=".$jert['battle']." target=_blank>поединок »»</a>.  ",$user['room'],$user['id_city']);

							//addlog($jert['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$za).'  '.$sexi[$user[sex]].'  в поединок!<BR>');
							$user[battle_t]=$za;
							$ac=($user[sex]*100)+mt_rand(1,2);
//							addlog($jert['battle'],"!:V:".time().":".nick_new_in_battle($user).":".$ac."\n");			
							addlog($jert['battle'],"!:W:".time().":".BNewHist($user).":".$user[battle_t].":".$ac."\n");	

							//пока остается так

							mysql_query("UPDATE users SET `battle` =".$jert['battle'].",`zayavka`=0 , `battle_t`='{$za}' WHERE `id`= ".$user['id']);

							mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$jert['battle']}','{$user['id']}')");

							/// вот это еще надо  добавить уник индекс в базу
							mysql_query("INSERT `battle_vars` (battle,owner,update_time,type)  VALUES ('{$jert['battle']}','{$user['id']}','{$time}','1') ON DUPLICATE KEY UPDATE `update_time` = '{$time}' ;");

					        ///////////////////////////////////////////////////////////

							
	                        $napal=1;
						}
                    }
                    else
                    {
                       echo '<font color=red>Это не ваша битва</font>';
                    }
				}
				elseif($can_start==1)  //можем начанать бой
				{
					// начинаем бой
					if($jert[deal]==1)
					{
						echo '<font color=red>С дилера нельзя начать клановую битву...</font>';
					}
					else
					if($jert[align]==1.3 || $jert[align]==1.5 || $jert[align]==1.7)
					{
						echo '<font color=red>С паладина этого ранга нельзя начать клановую битву...</font>';
					}
					else
					if($user[deal]==1)
					{
						echo '<font color=red>Вам нельзя начать клановую битву...</font>';
					}
					else
					if($user[align]==1.3 || $user[align]==1.5 || $user[align]==1.7)
					{
						echo '<font color=red>Вам нельзя начать клановую битву...</font>';
					}
					else
					{
						$bet=1;
						$sbet = 1;
						// если чел в заявке, выбиваем его
						if($jert['zayavka'] > 0 )
						{
							//грузив всю заявку один раз
							$zay = mysql_fetch_array(mysql_query("SELECT * FROM `zayavka` WHERE `id`=".$jert['zayavka'].";"));
							// делаем масив жертвы
						       $jertv_team = explode(";",$zay['team1']);
							if (in_array ($jert['id'],$jertv_team))
								{
								// да он тут
								$new_team = str_replace($jert['id'].";","",$zay['team1']);
								$needup=1;
								$other_team=$zay['team2'];
								}
								else
								{
								//значит тут
								$new_team = str_replace($jert['id'].";","",$zay['team2']);
								$needup=2;
								$other_team=$zay['team1'];
								}

						// если заявка была на бабки
							if ($zay[price]>0)
							{
						  		$current_money=$jert[money];

								if (mysql_query("UPDATE users SET money=money+".$zay[price]." WHERE id='".$jert['id']."'")) // вернем
								{
													//new_delo
									  		    		$rec['owner']=$jert[id];
													$rec['owner_login']=$jert[login];
													$rec['owner_balans_do']=$jert['money'];
													$jert[money]=$jert[money]+$zay[price];
													$rec['owner_balans_posle']=$jert['money'];
													$rec['target']=0;
													$rec['target_login']='';
													$rec['type']=69;
													$rec['sum_kr']=$zay[price];
													$rec['sum_ekr']=0;
													$rec['sum_kom']=0;
													$rec['item_id']='';
													$rec['item_name']='';
													$rec['item_count']=0;
													$rec['item_type']=0;
													$rec['item_cost']=0;
													$rec['item_dur']=0;
													$rec['item_maxdur']=0;
													$rec['item_ups']=0;
													$rec['item_unic']=0;
													$rec['item_incmagic']='';
													$rec['item_incmagic_count']='';
													$rec['item_arsenal']='';
													add_to_new_delo($rec); //юзеру
										if (olddelo==1)
										{
										mysql_query("INSERT INTO `delo` (`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$jert['id']}','\"".$jert['login']."\" получил возврат ставки (напали в заявке на деньги) ".$zay[price]." кр. (Баланс до: ".$current_money." кр. После: ".$jert[money]." кр.)',1,'".time()."');");
										}
								addchp ('<font color=red>Внимание!</font> Вам возвращено '.$zay[price].' кр. ставки. ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);
								$fond_sql="  ,`fond`=`fond`-{$zay[price]} ";
								}


							} /// заявка бои на деньги
							else
							{
								$fond_sql='';
							}

						//если обе команды в заявке пустые то грохаем заявку
							if ( ($new_team=='') AND ($other_team==''))
							{
							//грохаем нафиг
								mysql_query("DELETE FROM `zayavka` WHERE id = {$jert['zayavka']};");
							}
							else
							{
							// если не то тогда апдейтим
								mysql_query("UPDATE  `zayavka` SET  zcount=zcount-1,  team{$needup} = '{$new_team}' , t{$needup}hist = replace (t{$needup}hist,',".BNewHist($jert)."','') ".$fond_sql."  WHERE	id = {$jert['zayavka']};");
							}

						} // zay

						//рандом тайм - в минутах
						$sv = array(3,4,5);
						//бой кровавый


						mysql_query("INSERT INTO `battle`
							(
								`id`,`coment`,`teams`,`timeout`,`type`,`status`,".($myteam==1?"`t1`,`t2`":"`t2`,`t1`").",`to1`,`to2`,`blood`,`CHAOS`
							)
							VALUES
							(
								NULL,'','','".$time_out."','100','1','".$user['id']."','".$jert['id']."','".time()."','".time()."','1','2'
							)");

						$battle_id = mysql_insert_id();
						$time = time();

						//вносим записи в таблизу урона и опыта для нападающего и жертвы
						/*
						mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner)
						 VALUES
						 ('{$battle_id}','{$user['id']}') , ('{$battle_id}','{$jert['id']}')");
                       */
						// создаем лог

						if ($myteam==1)
							{
							$t1h =BNewHist($user);
							$t2h =BNewHist($jert);
							}
							else
							{
							$t1h =BNewHist($jert);							
							$t2h =BNewHist($user);
							}

						addch ("<a href=logs.php?log=".$battle_id." target=_blank>Бой</a> между <B>".($myteam==1?"<b>".$user_nick."</b> и <b>".nick_align_klan($jert)."</b>":"<b>".nick_align_klan($jert)."</b> и <b>".$user_nick."</b>")." начался.   ",$user['room'],$user['id_city']);
						//addlog($battle_id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
						addlog($battle_id,"!:S:".time().":".$t1h.":".$t2h."\n");
						
						$time = time();

						//вставка данных
						mysql_query("INSERT INTO battle_vars (battle,owner,update_time,type)
						VALUES ('{$battle_id}','{$user['id']}','{$time}','1'), ('{$battle_id}','{$jert['id']}','{$time}','1')");
						// апдейтим врага и чара

						if ($USE_ARKAN) 
						{ 
						//-1 в юз арканов
						mysql_query("UPDATE oldbk.clans_war_log SET {$ccacount}={$ccacount}+1 where war_id='{$mystatus[war_id]}'  ; ");					
						$add_sql_arkan=' , room=1 '; 
						} else { $add_sql_arkan='';}
						
						mysql_query("UPDATE `users` SET `battle` = {$battle_id} , `zayavka`=0 ".$add_sql_arkan." , `battle_t`=".($myteam==1?"2":"1")." WHERE `id` = {$jert['id']} ;");
						mysql_query("UPDATE `users` SET `battle` = {$battle_id} , `zayavka`=0 , `battle_t`=".($myteam==1?"1":"2")." WHERE `id` = {$user['id']} ;");

						mysql_query_100("UPDATE battle set `war_id`='{$mystatus[war_id]}',`status`=0,`t1hist`='".($myteam==1?BNewHist($user):BNewHist($jert))."' , `t2hist`='".($myteam==1?BNewHist($jert):BNewHist($user))."' where id={$battle_id};");

						
						$napal=1;
						
					}
				}
                else
                {
                	$stop=1;
                	echo '<font color=red>Вы не можете начать клановый бой...</font>';
                }

				$link_battle_id=$battle_id;
				if($user[hidden] >0 && $napal==1 && $stop!=1)
				{
					if($USE_ARKAN)
					{
					addchp ('<font color=red>Внимание!</font> Вас поймал персонаж <B><i>Невидимка</i></B>.','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);					
					addchp ('<font color=red>Внимание!</font> Вы поймали персонажа <B>'.$jert['login'].'</B>.','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);										
					}				
					addch("<img src=i/magic/attack.gif> <B><i>Невидимка</i></B>, применив магию нападения, внезапно <a href=http://capitalcity.oldbk.com/logs.php?log=".$link_battle_id." target=_blank>".$action."</a> на &quot;{$jert[login]}&quot;",$user['room'],$user['id_city']);
					addchp ('<font color=red>Внимание!</font> На вас '.$action.' <B><i>Невидимка</i></B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);
					$user_nick="<B><i>Невидимка</i></B><a href=inf.php?{$user[hidden]} target=_blank><IMG SRC=http://i.oldbk.com/i/inf.gif WIDTH=12 HEIGHT=11 ALT=\"Инф. о Невидимка}\"></a> ";
				}
				elseif($napal==1 && $stop!=1)
				{
					if($USE_ARKAN)
					{
					addchp ('<font color=red>Внимание!</font> Вас поймал персонаж <B>'.$user['login'].'</B>.','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);					
					addchp ('<font color=red>Внимание!</font> Вы поймали персонажа <B>'.$jert['login'].'</B>.','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);										
					}				
					addch("<img src=i/magic/attack.gif> <B>{$user['login']}</B>, применив магию нападения, внезапно <a href=http://capitalcity.oldbk.com/logs.php?log=".$link_battle_id." target=_blank>".$action."</a> на &quot;{$jert[login]}&quot;",$user['room'],$user['id_city']);
					addchp ('<font color=red>Внимание!</font> На вас '.$action.' <B>'.$user['login'].'</B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);
					$user_nick=nick_align_klan($user);
				}


			} else {
				echo '<font color=red>Мазохист?..</font>';
			}
}
?>
