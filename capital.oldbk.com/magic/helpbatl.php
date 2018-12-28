<?php
// by fred 20 07 2012
// v.4 - нужна переделака если будет использоваться для 3-х стороннего боя

if (!($zastup) && !($magic['id'])) {
	$magic = magicinf(53);
}

$do_not_help=array(10000,9,190672,101,102,103,104,105,106,107,108,109,110,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187);


$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));

if ($jert [klan]==$user[klan]) {
	$zastup_za_svoego=true;

}


if($jert['battle'] > 0) {
	$check_bexit = mysql_fetch_array(mysql_query("SELECT * FROM `battle_vars` WHERE `owner` = '{$user['id']}' and battle = '{$jert['battle']}' LIMIT 1;"));

	$bd = mysql_fetch_array(mysql_query ('SELECT * FROM `battle` WHERE `id` = '.$jert['battle'].' LIMIT 1;'));

	if  ($bd['damage']!='')
		{
		$batslvls=explode('|',$bd['damage']);
		}

	if( (($bd['type']==100) or ($bd['type']==101)) && $zastup_za_svoego==true )
	{

		//проверяем, можем ли мы вмешаться в клановый бой..
		//можем если у нас есть безответная война, и тело из клана с кем война напал на нашего соклана.
		$klan=mysql_fetch_array(mysql_query('SELECT * from oldbk.`clans` where short = "'.$user['klan'].'" LIMIT 1'));

		//времеено берем ID основы для нападения, нужно для четкой статистики в войнах.
		$bcp_id=$klan[id];
		$klan[id]=($klan[base_klan]>0?$klan[base_klan]:$klan[id]);

		//проверяем, есть ли у меня война, где я защитник или нападающий
		$data=mysql_query('SELECT * from oldbk.`clans_war_2`
		WHERE (
			(agressor='.$klan[id].' OR defender = '.$klan[id].') AND `date`>'.time().' AND war_id='.$bd['war_id'].'
		)
		ORDER BY id DESC LIMIT 1');
		if(mysql_num_rows($data)>0)
		{
			$zastup_za_svoego=true;
		}
		else
		{
			$zastup_za_svoego=false;
		}

		$klan[id]=$bcp_id;
	}

	$p190672=mysql_fetch_array(mysql_query ("SELECT * FROM `users` WHERE `id` in (190672,10000) and battle='{$jert['battle']}'  LIMIT 1;")); //загружаем ботов из нужного боя если они есть

	if (  ($p190672['battle'] >0 )  and ($p190672['battle_t'] !=$jert['battle_t']) )
	{
		if ($user['weap']>0)
				{
				//test weap
				$test_user_weap=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id ='{$user['weap']}' limit 1;"));

				if ((($test_user_weap['prototype'] >=55510301 ) AND ($test_user_weap['prototype'] <=55510352) ) OR ($test_user_weap['prototype'] ==1006233 ) OR ($test_user_weap['prototype'] ==1006232 ) OR ($test_user_weap['prototype'] ==1006234 ) OR ($test_user_weap['otdel'] ==6 ) )
					{
						//елкам и оружию хаоса разрешаем

					}
					else
					if ($test_user_weap['nlevel']<$user['level'] )
						{
						$low_level='Вы не можете биться на стороне этого персонажа с оружием ниже своего уровня';
						}
				}
				else
				{
				$low_level='Вы не можете биться на стороне этого персонажа без оружия';
				}
	}


	if (($bd[type]==2)AND($bd[exp]!='')) {
		$user_align=(int)($user[align]);
		if ($user_align==1) {$user_align=6;}

		//decode
		$aaligns=explode(";",$bd[exp]);
		//переменные для боев склонок
		if ($jert['battle_t']==2) {
			$my_aligns1=$aaligns[2];
			$my_aligns2=$aaligns[3];
			$targ_aligns1=$aaligns[0];
			$targ_aligns2=$aaligns[1];
		} else 	{
			$my_aligns1=$aaligns[0];
			$my_aligns2=$aaligns[1];
			$targ_aligns1=$aaligns[2];
			$targ_aligns2=$aaligns[3];
		}
	}

	if($jert['battle_t']==1) { $attack_to = 2; } else { $attack_to = 1; }
}


if ($zastup) {
	$int=101;
}
else
{

	if($magic['chanse']==100)
	{
		$int=101;
	}
	else
	if ($user['intel'] >= 0) {
		$int=$magic['chanse'] + ($user['intel'] - 4)*3;
		if ($int>98){
			$int=101;
		}
	}
	else
	{
		$int=0;
	}
}



$grant_continue = false;

$myeff = getalleff($user['id']);

if ($user['battle'] > 0) {
		echo "Не в бою...";
} elseif ( ( $bd[coment]=='<b>Бой с Волнами Драконов</b>' ) or ( $bd[coment]=='<b>Бой с порождением Хаоса</b>' ) or ( $bd[coment]=='Бой с Исчадием Хаоса' ) or ( $bd[coment]=='<b>Бой с Духом Мерлина</b>' ) or ( $bd[coment]=='<b>Бой с Пятницо</b>' )  )
{
	echo "Тут это не работает...";
} elseif ($bd['type'] == 23 && !$user['uclass']) {
	echo 'Для вмешательства в классовый бой у вас должен быть установлен класс';
} elseif($jert['battle']==0) {
	echo '<font color=red>Союзник не в бою...</font>';
} elseif ($jert['ldate'] < (time()-60)) {
	echo "Персонаж не в игре!";
} elseif ($jert['hidden'] > 0) {
	echo "Персонаж не в игре!";
} elseif (isset($myeff['owntravma'])) {
	echo "С Вашей травмой, нелья напасть!";
} elseif (isset($myeff[830])) {
	echo "Вы находитесь под медитацией!";
} elseif ($low_level!='') {
	echo $low_level;
} elseif (in_array($jert['id'],$do_not_help))
{
 	echo "Нельзя быть на одной стороне с <b>{$jert[login]}</b>!";

} elseif($bd['teams']!='' || ($user['room'] >= 49998 && $user['room'] <= 53600))
				{
				     $h=explode(":||:",$bd['teams']);
				      if ($h[0]==20000)
				      	{
						echo "Бой изолирован...";
				      	}
				      	else
				      	{
					echo "Бой закрыт от вмешательства...".$bd['teams'];
					}
				}
			 	elseif( ($bd['type']==100) and (!($zastup_za_svoego)))
			 	{
				 echo "Это клановый бой...";
				}
				elseif( ($bd['type']==140) OR ($bd['type']==141)  OR ($bd['type']==150) OR ($bd['type']==151)  )
			 	{
				 echo "Это клановый бой...";
				}
				elseif( ($bd['type']==101) and (!($zastup_za_svoego)))
			 	{
				 echo "Это клановый бой...";
				}
				elseif (($user['room'] >=210)AND($user['room'] <=300) || ($user['room'] >= 50000 && $user['room'] <= 53600) || ($user['room'] >= 70000 && $user['room'] <= 72001)) {
				echo "Тут это не работает...";
				}
				elseif($jert['id']==$user['id'])
				{
				 echo '<font color=red>Мазохист?...</font>';
				}
				elseif ($user['zayavka'] > 0)
				{
				echo "Вы ожидаете поединка...";
				}
				elseif($jert[bot]==1)
				{
				echo "Тут это не поможет...";
				}
				elseif ($user[align]==3 and ($jert[align]==6 or $jert[align]==1 or $jert[klan]=='pal') and ($bd['CHAOS']==0) )
				{
				// бой не хаот темный на может заступиться за светлого
				echo "Вы не можете заступиться за персонажа этой склонности!";
				}
				elseif ($jert[align]==3 and ($user[align]==6 or $user[align]==1 or $user[klan]=='pal') and ($bd['CHAOS']==0) )
				{
				// бой не хаот свет не может заступиться за темного
					echo "Вы не можете заступиться за персонажа этой склонности!";
				} elseif ( (($bd[type]==2)AND($bd[exp]!='')) AND ($user_align==0 OR $user_align==4) ) {
					echo "Вы неможете вмешаться в этот бой, у вас не та склонность!";
				} elseif ($bd[type]== 40 || $bd[type] == 41) {
					echo "Не работает для боёв противостояния!";
				} elseif (  (($bd[type]==2)AND($bd[exp]!='')) AND ($user_align!=$my_aligns1 AND $user_align!=$my_aligns2) ) {
				echo "Вы неможете вмешаться за эту сторону, у вас не та склонность!";
				} elseif (  (($bd[type]==2)AND($bd[exp]!='')) AND (($user_align==$targ_aligns1 OR $user_align==$targ_aligns1)AND($user_align!=2)) ) {
				echo "Вы неможете вмешаться против своей склонности!";
				}
				 elseif ($user['room'] != $jert['room'])
				{
					echo "Персонаж в другой комнате!";
				}
				elseif ($jert['room'] == 31 || $jert['room'] == 43 || $jert['room'] == 200 || $jert['room']==60)
				{
					echo "Нападения в этой локации запрещены!";
				}
				elseif (($jert['klan'] == 'radminion' || $jert['align'] == '2.7') && ($user['klan'] != 'radminion'))
				{
					echo "Разберуться как-то без вас! Не сейчас...";
				}
				 elseif ($user['hp'] < $user['maxhp']*0.33)
				{
						echo "Вы слишком ослаблены для боя!";
				}
				elseif ($bd[fond]>0)
				{
						echo "Нельзя вмешаться в бой на деньги!";
				}
				elseif (($jert['hp'] < 1)  AND  ($jert['battle'] > 1))
				{
						echo "Вы не можете заступиться за погибшего!";
				}
						 elseif ($jert['battle']>1 && $check_bexit['bexit_count']>0)
				{
							  echo '<font color=red>Вы достигли лимита выхода-входа в этот бой...</font>';
							  if($user['klan'] == 'radminion') {echo 'attackk ['.$check_bexit['bexit_count'].'/1]';}
				}
				elseif ( ($jert['battle']>0) && ($bd['damage']!='') && ($batslvls[0]!=$user['level'] && $batslvls[1]!=$user['level'])   )
				{
						echo '<font color=red>Зайти в этот бой могут только те уровни, с которых он начат..</font>';
				}


				elseif(!(rand(1,100) < $int))
				{
					echo "Свиток рассыпался в ваших руках...";
					$bet=1;
				}
				elseif(!isset($_POST['dropability']) && $jert['battle']>0)
				{
				// заход против соклана
	                    		$is_clan = false;
					if ($user['klan']!='')
					{

	                        		$uklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '".$user['klan']."' LIMIT 1;"));
					        if($uklan['base_klan']>0)
					        {
					        	$baseklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$uklan['base_klan']."' LIMIT 1;"));
					        	$sql="'".$user['klan']."', '".$baseklan['short']."'";
					        }
					        else
					        if($uklan['rekrut_klan']>0)
					        {
					        	$rekrutklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$uklan['rekrut_klan']."' LIMIT 1;"));
					        	$sql="'".$user['klan']."', '".$rekrutklan['short']."'";
					        }
					        else
					        {
					        	$sql="'".$user['klan']."'";
					        }

						$clans_query = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE  battle={$jert['battle']} and battle_t={$attack_to} and `klan` in (".$sql.") LIMIT 1 ; "));
						if ($clans_query[id] >0)
						{
							$is_clan = true;
						}
					}
				////
						if($is_clan)
						{
							$grant_continue = false;
							echo "<form id='formability' action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'><input id='target' name='target' type='hidden' value='".$jert['login']."' /><input id='dropability' name='dropability' type='hidden' value='1'/></form><script type='text/javascript'>var cat = confirm('С вac будет снят значек и склонность, если вы продолжите. Напасть?');if(cat) { document.getElementById('formability').submit(); }</script>";
						}
						else
						{
						$grant_continue = true;
						}

				}
				else
				{
					$grant_continue = true;
				}


	if ($bd['coment']=="<b>Бой на Центральной площади</b>")
	{
						$za=$jert['battle_t'];
						$user['battle_t']=$za;
						$TEST_CAN_I_GO=can_i_go_battle($user,$bd,$za,true); // и говорим что вызвали из ЦП
						if ($TEST_CAN_I_GO)
						{
						// тогда сбиваем неведов
						//если юзер вевидимка или перевоплот
								if ($user['hidden']>0)
								{
								mysql_query("UPDATE users set hidden=0 , hiddenlog='' where id='{$user['id']}' ");
								mysql_query("DELETE from effects where (type=1111 OR type=200)  and owner='{$user['id']}' and idiluz!=0;");
								$user['hidden']=0;
								$user['hiddenlog']='';
								}
								//если жертва в невидимости
							if ($jert['hidden']>0)
								{
								mysql_query("UPDATE users set hidden=0 , hiddenlog='' where id='{$jert['id']}' ");
								mysql_query("DELETE from effects where (type=1111 OR type=200)  and owner='{$jert['id']}' and idiluz!=0;");
								$jert['hidden']=0;
								$jert['hiddenlog']='';
								}

						}
						else
						{
						err('<br>Вы пока не можете вмешаться, силы будут не равные...');
						$grant_continue=false;
						}
	}


if ($grant_continue)
{
	if(isset($_POST['dropability']) )
	{
		Test_Arsenal_Items($user);
		mysql_query("UPDATE users set klan='', status='', align=0 where id='{$user['id']}'");
		mysql_query("INSERT INTO `lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$user['id']."','Вмешался в поединок против соклана, и потерял склонность ".$user[klan].", заступился за {$_POST['target']} ','".time()."');");
		ref_drop($user['id']);
	}
$sbet = 1;
$bet=1;


						if ($zastup )
						{
							 $mag_name=$baff_name;
							 $mag_gif='<img src=i/magic/'.$get_align.'n4.jpg>';
						}
						else
						{
							$mag_name='помощь союзнику';
							$mag_gif='<img src=i/magic/helpbatl.gif>';
						}





						if(($user['hidden'] > 0) and ($user['hiddenlog'] ==''))
						 {
							addch($mag_gif." <B><i>Невидимка</i></B>, применив магию ".$mag_name.",  заступился за &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
							addchp ('<font color=red>Внимание!</font> За вас заступился <B><i>Невидимка</i></B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);
							$sexi='вмешался';
						}
						else
						{
							$fuser=load_perevopl($user); //проверка и загрузка перевопла если надо
							if ($fuser['sex'] == 1) {$action="заступился"; 	$sexi='вмешался';  }	else {$action="заступилась"; $sexi='вмешалась';}
							addch($mag_gif." <B>{$fuser['login']}</B>, применив магию ".$mag_name.", ".$action." за &quot;{$_POST['target']}&quot;",$user['room'],$user['id_city']);
							addchp ('<font color=red>Внимание!</font> За вас '.$action.' <B>'.$fuser['login'].'</B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);
						}



					if ($jert['battle_t']==1) {$za=1;} else {$za=2;}
					$time = time();
					if ($check_bexit['bexit_count']>0)
					{
						mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($user).'\')   WHERE `id` = '.$jert['battle'].' ;');
					}
					else
					{
					//ставим первый статус если надо

					if (($bd['type']!=100) and ($bd['type']!=101) and ($bd['type']!=4) and ($bd['type']!=5) )
					{
						  if ($bd[status_flag]==0)
						   {
						    if  (users_in_battle($jert['battle']) >= 99)
						    	{
						    	if ($bd['CHAOS']>0)
						    	   {
						    	   //если хаот
						    	    $sstatus=" status_flag=10 ,";
						    	   }
						    	   else
						    	   {
						    	   //не хаот
   						    	    $sstatus=" status_flag=1 ,";
						    	   }
						    	}
						    	else
						    	{
						    	$sstatus='';
						    	}
						   }
						   else { $sstatus=''; }
					}
					else { $sstatus=''; }


					mysql_query('UPDATE `battle` SET '.$sstatus.' to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$user['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($user).'\')   WHERE `id` = '.$jert['battle'].' ;');
					}


				//	addlog($jert['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$za).'  '.$sexi.'  в поединок!<BR>');
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
					$user[battle_t]=$za;
					$ac=($user[sex]*100)+mt_rand(1,2);
	//				addlog($jert['battle'],"!:V:".time().":".nick_new_in_battle($user).":".$ac."\n");
					addlog($jert['battle'],"!:W:".time().":".BNewHist($user).":".$user[battle_t].":".$ac."\n");


					//пока остается так

					mysql_query("UPDATE users SET `battle` =".$jert['battle'].",`zayavka`=0 , `battle_t`='{$za}' WHERE `id`= ".$user['id']);

					/// вот это еще надо  добавить уник индекс в базу
					mysql_query("INSERT `battle_vars` (battle,owner,update_time,type)  VALUES ('{$jert['battle']}','{$user['id']}','{$time}','1') ON DUPLICATE KEY UPDATE `update_time` = '{$time}' ;");
				        ///////////////////////////////////////////////////////////





					header("Location:fbattle.php");
					//die("<script>location.href='fbattle.php';</script>");
}


?>
