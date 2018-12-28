#!/usr/bin/php
<?php
//ini_set('display_errors','On');
require_once('/www/capitalcity.oldbk.com/cron/init.php');
require_once('/www/capitalcity.oldbk.com/fsystem.php');
require_once('/www/capitalcity.oldbk.com/functions.php');
require_once('/www/capitalcity.oldbk.com/ruines_config.php');
require_once('/www/capitalcity.oldbk.com/memcache.php');
//include "/www/avaloncity.oldbk.com/cron/init.php";
//include "/www/avaloncity.oldbk.com/fsystem.php";
if( !lockCreate("cron_ghost") ) {
    exit("Script already running.");
}

//крон бота
//

function make_attack_by_bot($bot,$telo) // масивы
{
//
//столбим - по новому по уму!
	mysql_query("UPDATE `users` SET `battle` = 1 WHERE `id`= ".$bot[id]." and battle=0 and room=".$telo[room]." ; ");
		if (mysql_affected_rows()>0)
		{
		//бот успешно застолбился - был не в бою и в нужной комнате
			//столбим чара
			mysql_query("UPDATE `users` SET `battle` = 1 WHERE `id`= ".$telo[id]." and battle=0 and room=".$bot[room]." ; ");
			if (mysql_affected_rows()>0)
			{
			//чар успешно застолбился - т.е. не в бою и находится в комнате бота
			// начинаем бой

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//1.готовим бота-клона
							$BOT=$bot;
							$jert=$telo;
							$BOT[protid]=$BOT[id];
							$BNAME=nick_hist($BOT);
							$BOT_items=load_mass_items_by_id($BOT);
							mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
							`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
							`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
							`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
							`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
							`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
							`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
							`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=1,`bot`=1,
							`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',`bot_room`='{$BOT['room']}',
							`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
							`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
							`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
							`injury_possible`=0, `battle_t`=1;");
							echo mysql_error();
							$BOT['id'] = mysql_insert_id();
$sv = array(1,2,3,4,5);  //делаем тайм
//2. создаем бой
							mysql_query("INSERT INTO `battle` (`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`,`status_flag`)
							VALUES
							(NULL,'Бой с Исчадием Хаоса','','".$sv[rand(0,4)]."','6','0','".$BOT['id']."','".$jert['id']."','".time()."','".time()."',3,'".BNewHist($BOT)."','".BNewHist($jert)."','1','1','1')");
					//echo mysql_error();
							$battleid = mysql_insert_id();
					//обновляем бота
							mysql_query("UPDATE `users_clons` SET `battle` = {$battleid} WHERE `id`= {$BOT['id']}");
					//echo mysql_error();
					//обновление жертвы
							if($jert['hp'] > $jert['maxhp'])
							{
							   mysql_query("UPDATE `users` SET `hp` = `maxhp`, `battle_t`=2, `battle`={$battleid}  WHERE `id` = {$jert['id']} ;");
							}
							 else
							 {
							   mysql_query("UPDATE `users` SET  `battle_t`=2, `battle`={$battleid}   WHERE `id` = {$jert['id']} ;");
							 }
//3. создаем лог
							$rr = "<b>".nick_align_klan($BOT)."</b> и <b>".nick_align_klan($jert)."</b>";
//добавить чат
					$attack_txt=array('напал','набросился', 'накинулся');
					//addlog($battleid,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
					addlog($battleid,"!:S:".time().":".BNewHist($BOT).":".BNewHist($jert)."\n");
					
					
					addchp ('<font color=red>Внимание!</font> <B>'.$bot[login].'</B> '.($attack_txt[mt_rand(0,(count($attack_txt)-1))]).' на вас.  ','{[]}'.$jert[login].'{[]}');
//все удачно
			return true;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
			else
			{
			//чар не застолбился т.е. убежал или дето в бою уже....делаем откат боту
			mysql_query("UPDATE `users` SET `battle` = 0 WHERE `id`= ".$bot[id]." ; ");
			// неудачно выходим
			return false;
			}

		}
		else
		{
		//незастолбился выходим
		return false;
		}



}



/* Исчадие 

$bots[102]='Исчадие Хаоса'; //
//доступные комнаты
$broom[0]=20;
$broom[1]=21;
$broom[2]=26;
$broom[3]=50;

//Главный цикл по ботам
foreach($bots as $bk=>$bn)
                       {
                       echo "bk:$bk / bv:$bn <br> ";
			//1. запрашиваем  бота текущего из времени
			$sqlget="select * from variables where var='ghost_".$bk."_time' ; ";
			$q_get=mysql_query($sqlget);
			        if (mysql_affected_rows() > 0)
				{
				// есть данные работаем
				echo "есть данные...работаем<br>";
				$t=mysql_fetch_array($q_get);
				$bot_time=$t[value];
				 if ($bot_time <= time())
				 	{
				 	// время на пора
					echo "Нужное время<br>";

					//запросим статус бота из онлайна
					// может он уже в онлайне
					$bbot=mysql_query("select * from users where id=".$bk." and odate > 1;");
					        if (mysql_affected_rows() > 0)
						{
						echo " есть запись значит он онлайн<br>";
						//проверяем в бою или нет?
						$bbb_bot=mysql_query("select * from users_clons where id_user=".$bk.";");
	       					        if (mysql_affected_rows() > 0)
	       					        {
	       					        $bbb_bot=mysql_fetch_array($bbb_bot);
	       					        // да в бою
	       					        echo "Бот:$bn в бою!<br>";

	       					         // проверяем HP
	       					          if ($bbb_bot[hp] < ($bbb_bot[maxhp]-10)  )
	       					          	{
	       					          	//echo "test1";
	       					          	//если меньше половины выпускаем пати ботов
	       					          	$bots_team= array(221,224,225,226,227,228);
	       					          	foreach($bots_team as $keyto=>$valto)
	       					           	   	{
	       					           	   	 if ($keyto>$bbb_bot[bot_count])
	       					           	   	 		{
	       					           	   	 		//выпускаем бота - $valto
             	   	 								$BOT= mysql_fetch_array(mysql_query("select * from users where id=".$valto." ;"));
											$BOT[protid]=$BOT[id];
											$BOT_items=load_mass_items_by_id($BOT);
											$BOT['login']=$BOT['login']." (клон ".($keyto+1).")";
											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
											`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
											`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
											`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
											`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
											`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
											`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
											`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`='{$bbb_bot[battle]}',`bot`=2,
											`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
											`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
											`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
											`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
											`injury_possible`=0, `battle_t`='{$bbb_bot[battle_t]}' , bot_online = 1, bot_room='{$bbb_bot[bot_room]}'   ;"); //онлайн =1 т.к. это не мастер боты

											$BOT['id'] = mysql_insert_id();
	       					           	   	 		// конкат в бой новый ид и хистори
	       					           	   	 		$time=time();
	       					           	   	 		$za=$bbb_bot[battle_t];
 	      					           	   	 		mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$BOT['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\','.nick_align_klan($BOT).'\') WHERE `id` = '.$bbb_bot[battle].' ;');

	       					           	   	 		// отправляем системку и в лог
	       					           	   	 		if ($BOT['sex'] == 1) {$action="заступился";}	else {$action="заступилась";}
	       					           	   	 		$sexi[0]='вмешалась';
											$sexi[1]='вмешался';

											addch("<img src=i/magic/helpbatl.gif> <B>{$BOT['login']}</B>, применив магию помощь союзнику, ".$action." за &quot;{$bbb_bot['login']}&quot;");
        	   	 								addlog($bbb_bot[battle],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($BOT,$bbot[battle_t]).'  '.$sexi[$BOT[sex]].'  в поединок!<BR>');

	       					           	   	 		// Добавляем счетчик
	       					           	   	 		mysql_query("UPDATE `users_clons` SET `bot_count`=`bot_count`+1 WHERE `id`='{$bbb_bot['id']}';");

	       					           	   	 		}
	       					           	   	 }



	       					          	}



	       					        }
	       					        else
	       					        {
	       					        echo "Бот:$bn НЕ бою! - надо нападать<br>";
	       					        ///
	       					        // начало кода нападения
	       					        // получаем полные данные по боту
	       					        $monstro=mysql_fetch_array(mysql_query("select * from users where id=".$bk.";"));
	       					        //запрашиваем кто онлайн в комнате с ботом берем по максимальному уровню и не калеку
	       					        //1. надо понять если кто онлайн для нападения в комнате бота не в бою и не в травме
							$kandid = "select * FROM  `users` as u WHERE  (ldate >= ".(time()-90)." ) AND `room` = ".$monstro['room']." AND hp > 100 AND .battle=0 AND id!=".$bk." AND klan!='radminion' AND klan!='Adminion' AND id not in (SELECT `id` FROM `effects` WHERE (`type` = 11 OR `type` = 12 OR `type` = 13 OR `type` = 14) ) ORDER by level DESC;";
							$kand=mysql_query($kandid);
					        	if (mysql_affected_rows() > 0)
	       					        		{
	       					        		$telo=mysql_fetch_array($kand);
	       					        		echo "есть кандидат...пытаюсь напасть на ".$telo[id]." <br>";
     					        		     	if (make_attack_by_bot($monstro,$telo))
     					        		     	     {
     					        		     	     echo "Получилось напасть <br>";
     					        		     	     }
     					        		     	     else
     					        		     	     {
     					        		     	     echo "Напасть не вышло...идим тут ждем минуту<br>";
     					        		     	     }

	       					        		}
	       					        		else
	       					        		{
									//в комнате нет подходящей цели
									//
									$new_rand_room=mt_rand(0,(count($broom)-1) );
									$new_rand_room=$broom[$new_rand_room];
									mysql_query("UPDATE `users` SET `users`.`room` = '".$new_rand_room."' WHERE  `users`.`id` = '{$bk}' ;");
									echo "Нет подходящей цели... перемещаемся в комнату $new_rand_room <br>";
	       					        		}


	       					        ///
	       					        }


						}
						else
						{
						echo " нету бота в онлайне значит пора его завести в онлайн<br>";
						$new_rand_room=mt_rand(0,(count($broom)-1) );
						$new_rand_room=$broom[$new_rand_room];

						mysql_query("UPDATE users set ldate=1999999999 , odate=1999999999, room=".$new_rand_room." where id=".$bk." LIMIT 1; ");

					        addch ("Вас приветствует <a href=javascript:top.AddTo(\"".$bn."\")><span oncontextmenu=\"return OpenMenu(event,8)\">".$bn."</span></a>!   ",$new_rand_room);

						}

				 	}
				 	else
				 	{
					echo "Еще не время <br>";
				 	}


				}
				else
				{
				// нет данных - создаем для бота время время +2 минуты
				echo "нет данных...инициализируем<br>";
				mysql_query("INSERT INTO `variables` SET `var`='ghost_".$bk."_time',`value`='".(time()+120)."';");
				}




                       }
*/
//крон бота
//
function mk_bot($proto,$botlogin,$botonlie,$botroom,$team)
{
// 
$telo=mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$proto}' LIMIT 1;"));

					$telo_items=load_mass_items_by_id($telo);
					mysql_query("INSERT INTO `users_clons` SET `login`='".$botlogin."',`sex`='{$telo['sex']}',
					`level`='{$telo['level']}',`align`='{$telo['align']}',`klan`='{$telo['klan']}',`sila`='{$telo['sila']}',
					`lovk`='{$telo['lovk']}',`inta`='{$telo['inta']}',`vinos`='{$telo['vinos']}',
					`intel`='{$telo['intel']}',`mudra`='{$telo['mudra']}',`duh`='{$telo['duh']}',`bojes`='{$telo['bojes']}',`noj`='{$telo['noj']}',
					`mec`='{$telo['mec']}',`topor`='{$telo['topor']}',`dubina`='{$telo['dubina']}',`maxhp`='{$telo['maxhp']}',`hp`='{$telo['maxhp']}',
					`maxmana`='{$telo['maxmana']}',`mana`='{$telo['mana']}',`sergi`='{$telo['sergi']}',`kulon`='{$telo['kulon']}',`perchi`='{$telo['perchi']}',
					`weap`='{$telo['weap']}',`bron`='{$telo['bron']}',`r1`='{$telo['r1']}',`r2`='{$telo['r2']}',`r3`='{$telo['r3']}',`helm`='{$telo['helm']}',
					`shit`='{$telo['shit']}',`boots`='{$telo['boots']}',`nakidka`='{$telo['nakidka']}',`rubashka`='{$telo['rubashka']}',`shadow`='{$telo['shadow']}',`battle`=0,
					`id_user`='{$telo['id']}',`at_cost`='{$telo_items['allsumm']}',`kulak1`=0,`sum_minu`='{$telo_items['min_u']}',
					`sum_maxu`='{$telo_items['max_u']}',`sum_mfkrit`='{$telo_items['krit_mf']}',`sum_mfakrit`='{$telo_items['akrit_mf']}',
					`sum_mfuvorot`='{$telo_items['uvor_mf']}',`sum_mfauvorot`='{$telo_items['auvor_mf']}',`sum_bron1`='{$telo_items['bron1']}',
					`sum_bron2`='{$telo_items['bron2']}',`sum_bron3`='{$telo_items['bron3']}',`sum_bron4`='{$telo_items['bron4']}',`ups`='{$telo_items['ups']}',
					`injury_possible`=0, `battle_t`='{$team}', bot='{$botonlie}' , bot_online='{$botonlie}', bot_room='{$botroom}' ;");
					$bot = mysql_insert_id();
					//делаем масив для бота шоб не перечитывать из базы
					$bot_data=$telo;
					$bot_data[id]=$bot;
					$bot_data[login]=$botlogin;
return $bot_data;
}
	



//доступные комнаты

$broom[0]=20;
$broom[1]=21;
$broom[2]=26;
$broom[3]=50;

if ((time()>=1325361601) and (time()<=1325448001))
{
/// проверяем образ
$test_img=mysql_fetch_array(mysql_query("select id, shadow from users where id=102"));
if ($test_img['shadow']=='i_obraz.gif')
	{
	mysql_query("UPDATE `users` SET `shadow`='i_obraz.gif' WHERE `id` in (101,102,103,104,105,106,107,108,109,110);");
	}

$boname[103]='Исчадие Дракона (7)';
$boname[104]='Исчадие Дракона (8)';
$boname[105]='Исчадие Дракона (9)';
$boname[106]='Исчадие Дракона (10)';
$boname[107]='Исчадие Дракона (11)';
$boname[108]='Исчадие Дракона (12)';
$boname[109]='Исчадие Дракона (13)';
$boname[110]='Исчадие Дракона (14)';
$boname[110]='Исчадие Дракона (15)';
}
else
{
/// проверяем образ
$test_img=mysql_fetch_array(mysql_query("select id, shadow from users where id=102"));
if ($test_img['shadow']!='i_obraz.gif')
	{
	mysql_query("UPDATE `users` SET `shadow`='i_obraz.gif' WHERE `id` in (101,102,103,104,105,106,107,108,109,110);");
	}

// названия ботов
$boname[101]='Исчадие Хаоса (15)';
$boname[103]='Исчадие Хаоса (7)';
$boname[104]='Исчадие Хаоса (8)';
$boname[105]='Исчадие Хаоса (9)';
$boname[106]='Исчадие Хаоса (10)';
$boname[107]='Исчадие Хаоса (11)';
$boname[108]='Исчадие Хаоса (12)';
$boname[109]='Исчадие Хаоса (13)';
$boname[110]='Исчадие Хаоса (14)';
}

			//1. запрашиваем  бота текущего из времени
			$sqlget="select * from variables where var='ghost_all_time' ; ";
			$q_get=mysql_query($sqlget);
				if (mysql_num_rows($q_get)>0)
				{
				// есть данные работаем
				$t=mysql_fetch_array($q_get);
				$bot_time=$t[value];
				 if ($bot_time <= time())
				 	{
				 	// время на пора
					echo "Нужное время<br>";
					//запрашиваем нужного бота
					$get_bot_next=mysql_fetch_array(mysql_query("select * from variables where var='ghost_next_id';"));
					$bk=$get_bot_next[value];
					if ($bk>0)
					{
					// может он уже в онлайне
					        $bbot=mysql_fetch_array(mysql_query("select * from users_clons where id_user=".$bk." and  bot_online='2' ;"));
					        if ($bbot[id]> 0)
						{
						echo " есть запись значит он онлайн<br>";

	       					        if ($bbot[battle] > 0)
	       					        {
	       					        // да в бою
	       					        echo "Бот:$bk в бою!<br>";
	       					        }
			       				else
	       					        {
	       					        //проверяем сколько бродит оно
	       					        if ($bot_time+900<=time())
	       					        	{
	       					        	//бродит больше 15 минут
	       					        	echo "удаляем бота";
	       					        	mysql_query("delete from users_clons where `id`= {$bbot[id]} and battle=0 ;");
	       					        	}
	       					        	else
	       					        	{
	       						        echo "нет не в бою<br>";
	      	 					        echo "делаем переход";
	       						        $new_rand_room=mt_rand(0,(count($broom)-1) );
								$new_rand_room=$broom[$new_rand_room];
	       						        mysql_query("UPDATE `users_clons` SET `bot_room` = {$new_rand_room} WHERE `id`= {$bbot[id]}");	       					        	
	       					               	}
	       					        
	       					        
	       					        }
						}
						else
						{
						echo " нету бота в онлайне значит пора его завести в онлайн<br>";
						$new_rand_room=mt_rand(0,(count($broom)-1) );
						$new_rand_room=$broom[$new_rand_room];
						$bot1=mk_bot($bk,$boname[$bk],2,$new_rand_room,0);
						
						
						}
					   }
					   else
					   {
					   //ошибка
					   }
				 	}
				 	else if ($bot_time-300 <= time())
				 	{
			 			$get_smg=mysql_fetch_array(mysql_query("select * from variables where var='ghost_sysmsg_5m'  "));
		  				if ($get_smg['value']==0)
		  				{
							//запрашиваем нужного бота
							$get_bot_next=mysql_fetch_array(mysql_query("select * from variables where var='ghost_next_id';"));
							$gbk=$get_bot_next['value'];
						
							$sml_min[103]=6;$sml_max[103]=6;
							$sml_min[104]=7;$sml_max[104]=7;						
							$sml_min[105]=8;$sml_max[105]=8;						
							$sml_min[106]=9;$sml_max[106]=9;
							$sml_min[107]=10;$sml_max[107]=10;
							$sml_min[108]=11;$sml_max[108]=11;																		
							$sml_min[109]=12;$sml_max[109]=12;
							$sml_min[110]=13;$sml_max[110]=13;									
							$sml_min[101]=14;$sml_max[101]=14;		
													
							$TEXT='<img src=http://i.oldbk.com/i/align_4.9.gif><b>'.$boname[$gbk].'</b><a href=http://capitalcity.oldbk.com/inf.php?'.$gbk.' target=_blank><img src=http://i.oldbk.com/i/inf.gif></a></font><font color=black>: Пробил час моего освобождения! Всего <b>пять минут</b> отделяют меня от исхода, призывайте своих сильнейших воинов - настало время великой битвы';
							addch2levels($TEXT,$sml_min[$gbk],$sml_max[$gbk],0);
					           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='ghost_sysmsg_5m' ;");	   	 	 
					         }
				 	}	
				 	else
				 	{
					echo "Еще не время <br>";
				 	}


				}
				else
				{
				// нет данных
				
				}


///////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////Руины
	function DrawNicks($str) {
		if (empty($str)) return "";
		$str = unserialize($str);
		$ret = '';
		if (is_array($str)) {
			while(list($k,$v) = each($str)) {
				$ret .= $v.', ';
			}
		}
		if (strlen($ret)) $ret = substr($ret,0,strlen($ret)-2);
		return $ret;
	}


	function CronRuinesStart() {
		global $ritems, $keyexcluderooms, $team_colors,$bot_id, $exptable;
		$needrestart = false; // флаг отвечающий за рекурсивный запуск этой функции если сформированы несколько команд

		// вначале стартуем собранные хаот команды
		$q = mysql_query('SELECT t1.type AS type, t1.id AS id1, t1.ownerlvl AS ownerlvl, t1.lvl AS lvl, t1.t1_logins AS t1_logins, t1.t1_loginscache AS t1cache  FROM `ruines_start` AS t1 WHERE substrCount(t1.t1_logins,";") = t1.num AND type = 1 LIMIT 1') or mydie(mysql_error().":".__LINE__);

		$data = array();
		if (mysql_num_rows($q) > 0) {
			$data = mysql_fetch_assoc($q) or mydie(mysql_error().":".__LINE__);
			$needrestart = true;
		} else {
			$q = mysql_query('SELECT t1.id AS id1,  t2.id AS id2, t1.ownerlvl AS ownerlvl, t1.lvl AS lvl, t1.t1_logins AS t1_logins, t2.t1_logins AS t2_logins, t1.t1_loginscache AS t1cache, t2.t1_loginscache AS t2cache, t1.type AS type FROM `ruines_start` AS t1 LEFT JOIN `ruines_start` AS t2 ON (t1.ownerlvl = t2.ownerlvl AND t1.lvl = t2.lvl AND t1.id != t2.id AND t1.num = t2.num AND substrCount(t1.t1_logins,";") = t1.num AND substrCount(t2.t1_logins,";") = t2.num) WHERE t1.type = 0 AND t2.type = 0 AND t2.id IS NOT NULL LIMIT 1') or mydie(mysql_error().":".__LINE__);
			if (mysql_num_rows($q) > 0) {
				$data = mysql_fetch_assoc($q) or mydie(mysql_error().":".__LINE__);
				$needrestart = true;
			}
		}

		$allids = array();
		if (count($data)) {
			// нашли команды для старта

			// обозначаем номер карты и её стартовый румид
			$roomid = 1000;
			$q = mysql_query('SELECT * FROM `ruines_map` ORDER BY `rooms` ASC') or mydie(mysql_error().":".__LINE__);
			if (mysql_num_rows($q) > 0) {
				while($res = mysql_fetch_assoc($q)) {
					if ($roomid == $res['rooms']) {
						$roomid += 100;
					} else {
						break;
					}
				}
			}
			if ($roomid > 10000) die(mysql_error().":".__LINE__);

			if ($data['type'] == 1) {
				$data['id2'] = $data['id1'];
				$t = explode(";",$data['t1_logins']);
				shuffle($t);
				$logins = array();
				while(list($k,$v) = each($t)) {
					if (!empty($v)) $logins[$v] = 1;
				}
				$q = mysql_query('SELECT * FROM users WHERE id IN ('.implode(",",array_keys($logins)).')') or mydie(mysql_error().":".__LINE__);
				while($u = mysql_fetch_assoc($q)) {
					$logins[$u['id']] = nick_hist($u);
				}

				$data['t1_logins'] = "";
				$data['t1cache'] = "";
				$data['t2_logins'] = "";
				$data['t2cache'] = "";
				reset($logins);
				$i = 0;
				while(count($logins)/2 > $i && list($k,$v) = each($logins)) {
					$data['t1_logins'] .= $k.";";
					$data['t1cache'][$k] = $v;
					$i++;
				}

				while(count($logins) > $i && list($k,$v) = each($logins)) {
					$data['t2_logins'] .= $k.";";
					$data['t2cache'][$k] = $v;
					$i++;
				}
				$data['t1cache'] = serialize($data['t1cache']);
				$data['t2cache'] = serialize($data['t2cache']);
			}

			$lvlmin = $lvlmax = 9;

			// переносим народ внутрь
			$t1ids = str_replace(';',',',$data['t1_logins']);
			$t1ids = substr($t1ids,0,strlen($t1ids)-1);
			$t2ids = str_replace(';',',',$data['t2_logins']);
			$t2ids = substr($t2ids,0,strlen($t2ids)-1);

			$allids = array_merge(explode(',',$t1ids),explode(',',$t2ids));

			reset($allids);
			$actsql = 'INSERT INTO ruines_activity_log (mapid,owner,var,val) VALUES ';
			while(list($k,$v) = each($allids)) {
				$actsql .= '("'.$data[id1].'","'.$v.'","start","1"),';
			}
			$actsql = substr($actsql,0,strlen($actsql)-1);
			mysql_query($actsql);

			reset($allids);
			while(list($k,$v) = each($allids)) {
				mysql_query('DELETE FROM users_bonus where owner = '.$v) or mydie(mysql_error().":".__LINE__);

				// выставляем in_tower = 2
				mysql_query('UPDATE users SET in_tower = 2 WHERE id = '.$v) or mydie(mysql_error().":".__LINE__);

				// раздеваем
				undressall($v);
			}


			// удаляем молчи кроме паловских
			mysql_query('DELETE FROM `effects` WHERE `owner` IN ('.$t1ids.','.$t2ids.') AND type = 2 AND pal <> 1') or mydie(mysql_error().":".__LINE__);
			mysql_query('UPDATE users set slp = 0 WHERE `id` IN ('.$t1ids.','.$t2ids.') AND `id` NOT IN (SELECT `owner` FROM effects where `owner` IN ('.$t1ids.','.$t2ids.') AND type = 2)') or mydie(mysql_error().":".__LINE__);

			// удаляем путы
			mysql_query('DELETE FROM `effects` WHERE `owner` IN ('.$t1ids.','.$t2ids.') AND type IN (10,791,792,793,794)') or mydie(mysql_error().":".__LINE__);


			// создаём карту, выставляем там вещи
			mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);

			mysql_query("INSERT INTO `ruines_map` (id,rooms,starttime,lowlvl,type) VALUES ('{$data[id1]}','{$roomid}',".time().", '{$lvlmin}' ,'{$data[type]}' )  ") or  mydie(mysql_error().":".__LINE__);
			 
			$mapid = $data['id1'];


			// пишем сколько было стартов
			mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`)
						VALUES(
							"0",
							"start",
							"1"
						)
						ON DUPLICATE KEY UPDATE
							`val` = val + 1
			') or mydie(mysql_error().":".__LINE__);



			// на всякий случай чистим комнаты от возможного старого шмотья
			mysql_query('DELETE FROM `ruines_items` WHERE room BETWEEN '.$roomid.' AND '.($roomid+100)) or mydie(mysql_error().":".__LINE__);

			// выкидываем рандомно первый ключ
			$keyroom = 0;
			do {
				$keyroom = mt_rand(1,77);
			} while(in_array($keyroom,$keyexcluderooms));

			mysql_query('INSERT INTO `ruines_items` (type,name,img,room,extra) VALUES ("4","Ключ","",'.($roomid+$keyroom).',0)') or mydie(mysql_error().":".__LINE__);

			// проходимся по конфигу
			$ri = array();
			reset($ritems);
			while(list($k,$v) = each($ritems)) {
				while(list($ka,$va) = each($v)) {
					// формат ПРОТО-СУБТИП-КОЛВО-ТИП
					$type = 0;
					if (isset($va['scroll'])) $type = 3;
					$ri[] = $k.'-'.$ka.'-'.$va['count'].'-'.$type;
				}
			}

			// получаем все прото из магаза
			$shop = array();
			$q = mysql_query('SELECT `id`,`name`,`img` FROM oldbk.`shop`') or mydie(mysql_error().":".__LINE__);
			while($i = mysql_fetch_assoc($q)) {
				$shop[$i['id']] = $i;
			}

			$chests = array();
			while($sh = array_shift($ri)) {
				$t = explode("-",$sh);
				for ($i = 0; $i < $t[2]; $i++) {
					$iroom = mt_rand(1,74)+$roomid;
					// тип
					if ($t[3] == 3) {
						// свиток, к свитку нужен сундук
						$chests[$iroom] = 1;
					}

					mysql_query('INSERT `ruines_items` (`type`,`iteam_id`, `name`, `img`, `room`, `extra`)
							VALUES (
								"'.$t[3].'",
								"'.$t[0].'",
								"'.mysql_real_escape_string($shop[$t[0]]['name']).'",
								"'.mysql_real_escape_string($shop[$t[0]]['img']).'",
								"'.$iroom.'",
								"'.$t[1].'"
							)
					') or mydie(mysql_error().":".__LINE__);

					$id = mysql_insert_id();
					$sql = 'SELECT img FROM ruines_items WHERE id = '.$id;
					$cache = array();
					$cache[0] = array(
						'img' => mysql_real_escape_string($shop[$t[0]]['img']),
					);
					setCache(md5("mysql_query".$sql),$cache,3*3600);

				}
			}

			// расставляем сундуки
			// добавляем 5-7 пустых сундуков
			$fchest = mt_rand(5,7);
			for ($i = 0; $i < $fchest; $i++) {
				do {
					$chestroom = mt_rand(1,74)+$roomid;
				} while(isset($chests[$chestroom]));
				$chests[$chestroom] = 1;
			}

			while(list($k,$v) = each($chests)) {
					mysql_query('INSERT `ruines_items` (`type`,`iteam_id`, `name`, `img`, `room`, `extra`)
							VALUES (
								"2",
								"0",
								"Сундук",
								"",
								"'.$k.'",
								"0"
							)
					') or mydie(mysql_error().":".__LINE__);
			}

			// раскидываем ботов
			if (isset($bot_id[$lvlmin])) {
				$z = 0;
				reset($bot_id[$lvlmin]["start"]);
				while(list($k,$v) = each($bot_id[$lvlmin]["start"])) {
					$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$k) or mydie(mysql_error().":".__LINE__);
					$BOT = mysql_fetch_array($q) or mydie(mysql_error().":".__LINE__);
					$BOT['protid'] = $BOT['id'];

					$BOT_items = load_mass_items_by_id($BOT);

					for ($i = 0; $i < $v; $i++) {
						$botroom = mt_rand(1,74)+$roomid; // без тронов и кладбища и остального
						$z++;
						mysql_query('INSERT INTO `users_clons` SET
								`login` = "'.$BOT['login'].' (иллюзия '.($z).')",
								`sex` = "'.$BOT['sex'].'",
								`level` = "'.$BOT['level'].'",
								`align` = "'.$BOT['align'].'",
								`klan` = "'.$BOT['klan'].'",
								`sila` = "'.$BOT['sila'].'",
								`lovk` = "'.$BOT['lovk'].'",
								`inta` = "'.$BOT['inta'].'",
								`vinos` = "'.$BOT['vinos'].'",
								`intel` = "'.$BOT['intel'].'",
								`mudra` = "'.$BOT['mudra'].'",
								`duh` = "'.$BOT['duh'].'",
								`bojes` = "'.$BOT['bojes'].'",
								`noj` = "'.$BOT['noj'].'",
								`mec` = "'.$BOT['mec'].'",
								`topor` = "'.$BOT['topor'].'",
								`dubina` = "'.$BOT['dubina'].'",
								`maxhp` = "'.$BOT['maxhp'].'",
								`hp` = "'.$BOT['maxhp'].'",
								`maxmana` = "'.$BOT['maxmana'].'",
								`mana` = "'.$BOT['mana'].'",
								`sergi` = "'.$BOT['sergi'].'",
								`kulon` = "'.$BOT['kulon'].'",
								`perchi` = "'.$BOT['perchi'].'",
								`weap` = "'.$BOT['weap'].'",
								`bron` = "'.$BOT['bron'].'",
								`r1` = "'.$BOT['r1'].'",
								`r2` = "'.$BOT['r2'].'",
								`r3` = "'.$BOT['r3'].'",
								`helm` = "'.$BOT['helm'].'",
								`shit` = "'.$BOT['shit'].'",
								`boots` = "'.$BOT['boots'].'",
								`nakidka` = "'.$BOT['nakidka'].'",
								`rubashka` = "'.$BOT['rubashka'].'",
								`shadow` = "'.$BOT['shadow'].'",
								`battle` = 0,
								`bot` = 2,
								`id_user` = "'.$BOT['id'].'",
								`at_cost` = "'.$BOT_items['allsumm'].'",
								`kulak1` = 0,
								`sum_minu` = "'.$BOT_items['min_u'].'",
								`sum_maxu` = "'.$BOT_items['max_u'].'",
								`sum_mfkrit` = "'.$BOT_items['krit_mf'].'",
								`sum_mfakrit` = "'.$BOT_items['akrit_mf'].'",
								`sum_mfuvorot` = "'.$BOT_items['uvor_mf'].'",
								`sum_mfauvorot` = "'.$BOT_items['auvor_mf'].'",
								`sum_bron1` = "'.$BOT_items['bron1'].'",
								`sum_bron2` = "'.$BOT_items['bron2'].'",
								`sum_bron3` = "'.$BOT_items['bron3'].'",
								`sum_bron4` = "'.$BOT_items['bron4'].'",
								`ups` = "'.$BOT_items['ups'].'",
								`injury_possible` = 0,
								`battle_t` = 0,
								`bot_online` = 5,
								`bot_room` = "'.$botroom.'"
						') or mydie(mysql_error().":".__LINE__);
					}
				}
			}


			// удаляем старые реальные характеристики
			mysql_query('DELETE FROM `ruines_realchars` WHERE `owner` IN ('.$t1ids.','.$t2ids.')') or mydie(mysql_error().":".__LINE__);

			// раздеваем всех и выставляем статы по профилю

			$val_i = "";

			reset($allids);
			while(list($k,$v) = each($allids)) {
				// для индекса - инстерты
				$val_i .= '("'.time().'","'.$v.'","'.$data['id1'].'"),';

				// кол-во умений (сумма, выдадим при старте руин)
				$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$v) or mydie(mysql_error().":".__LINE__);
				$u = mysql_fetch_array($q) or mydie(mysql_error().":".__LINE__);

				$master = ($u['noj']+$u['mec']+$u['topor']+$u['dubina']+$u['mfire']+$u['mwater']+$u['mair']+$u['mearth']+$u['mlight']+$u['mgray']+$u['mdark']+$u['master']);

				// сила без бонуса
				$srt = $u['sila'] - $u['bpbonussila'];

				echo serialize($u)."\r\n";

				// Сохраняем реальные статы в голом виде + все умения.
				mysql_query('INSERT INTO `ruines_realchars`
						(`owner`,`sila`,`lovk`,`inta`,`vinos`,`intel`,`mudra`,`stats`,`master`,`bpbonussila`,`bpbonushp`,`noj`,`mec`,`topor`,`dubina`,`mfire`,`mwater`,`mair`,`mearth`,`mlight`,`mgray`,`mdark`,`mana`)
						VALUES
						(
							"'.$u['id'].'",
							"'.$srt.'",
							"'.$u['lovk'].'",
							"'.$u['inta'].'",
							"'.$u['vinos'].'",
							"'.$u['intel'].'",
							"'.$u['mudra'].'",
							"'.$u['stats'].'",
							"'.$u['master'].'",
							"'.$u['bpbonussila'].'",
							"'.$u['bpbonushp'].'",
							"'.$u['noj'].'",
							"'.$u['mec'].'",
							"'.$u['topor'].'",
							"'.$u['dubina'].'",
							"'.$u['mfire'].'",
							"'.$u['mwater'].'",
							"'.$u['mair'].'",
							"'.$u['mearth'].'",
							"'.$u['mlight'].'",
							"'.$u['mgray'].'",
							"'.$u['mdark'].'",
							"'.$u['maxmana'].'"
						)
				') or mydie(mysql_error().":".__LINE__);

				// применяем профили всегда
				$q  = mysql_query('SELECT * FROM `ruines_profile` WHERE `owner` = '.$v.' AND `def` = 1') or mydie(mysql_error().":".__LINE__);
				if (mysql_num_rows($q) > 0) {
					$tec = mysql_fetch_array($q) or mydie(mysql_error().":".__LINE__);

					$master = 10;

					// теперь проверяем статы
        				// если разница имеющихся и профильных статов = 0, то все ок... если это не так, то тело идет с родными статами минус бонус.
                               		$hp = $tec['vinos']*6;

					mysql_query('UPDATE `users` SET
							`sila` = "'.$tec['sila'].'",
							`lovk` = "'.$tec['lovk'].'",
							`inta` = "'.$tec['inta'].'",
							`vinos` = "'.$tec['vinos'].'",
							`intel` = "'.$tec['intel'].'",
							`mudra` = "'.$tec['mudra'].'",
							`stats` = 0,
							`noj` = 0,
							`mec` = 0,
							`topor` = 0,
							`dubina` = 0,
							`mfire` = 0,
							`mwater` = 0,
							`mair` = 0,
							`mearth` = 0,
							`mlight` = 0,
							`mgray` = 0,
							`mdark` = 0,
							`master` = "'.$master.'",
							`maxhp` = "'.$hp.'",
							`hp` = "'.$hp.'",
							`bpbonussila` = 0,
							`mana` = 0,
							`maxmana` = 0,
							`bpbonushp` = 0
						WHERE `id` = '.$u['id']
					) or mydie(mysql_error().":".__LINE__);
				} else {
					mydie("die: no user profile: ".$u['id']);
				}

				// выставляем юзеру номер комнаты, взасимости куда разнесло
				$room = mt_rand(1,74);
				mysql_query('UPDATE `users` SET `room` = '.($roomid+$room).' WHERE `id` = '.$v) or mydie(mysql_error().":".__LINE__);
			}

			$val_i = substr($val_i,0,strlen($val_i)-1);

			// пишем индекс для поиска
			mysql_query('INSERT INTO ruines_log_index (`starttime`, `user`, `mapid`) VALUES '.$val_i) or mydie(mysql_error().":".__LINE__);

			// выставляем всем юзером что они в башне = 2 и выставляем юзерам номер карты руин и номер заявки в 0
			mysql_query('UPDATE `users` SET `in_tower` = 2, `ruines` = '.$data['id1'].', `zayavka` = 0 WHERE `id` IN ('.$t1ids.','.$t2ids.')') or mydie(mysql_error().":".__LINE__);

			// выставляем командам номера
			mysql_query('UPDATE `users` SET `id_grup` = 1 WHERE `id` IN ('.$t1ids.')') or mydie(mysql_error().":".__LINE__);
			mysql_query('UPDATE `users` SET `id_grup` = 2 WHERE `id` IN ('.$t2ids.')') or mydie(mysql_error().":".__LINE__);

			// чистим смерти
			mysql_query('DELETE FROM `ruines_var` WHERE `var` = "rdeath" AND owner IN ('.$t1ids.','.$t2ids.')') or mydie(mysql_error().":".__LINE__);

			// чистим если вдруг шмотки остались
			mysql_query('DELETE FROM oldbk.`inventory` WHERE bs_owner = 2 AND `owner` IN ('.$t1ids.','.$t2ids.')') or mydie(mysql_error().":".__LINE__);

			// добавляем лог
			$t1 = DrawNicks($data['t1cache']);
			$t2 = DrawNicks($data['t2cache']);

			$log = '<span class=date>'.date("d.m.y H:i").'</span> <b>Начало турнира.</b> <font color='.$team_colors[1].'>'.$t1.'</font> против <font color='.$team_colors[2].'>'.$t2.'</font><BR>';

			mysql_query('INSERT INTO `ruines_log` (`id`,`log`,`starttime`,`t1`,`t2`,`active`)
					VALUES (
						'.$mapid.',
						"'.mysql_real_escape_string($log).'",
						'.time().',
						"'.mysql_real_escape_string($t1).'",
						"'.mysql_real_escape_string($t2).'",
						"1"
				)
			') or mydie(mysql_error().":".__LINE__);

			// удаляем заявку
			mysql_query('DELETE FROM `ruines_start` WHERE id IN ('.$data['id1'].','.$data['id2'].')') or mydie(mysql_error().":".__LINE__);
		}

		mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);

		global $app;
		if($allids) {
			try {
				$user_list = \components\models\User::whereIn('id', $allids)->get()->toArray();
				foreach ($user_list as $_user_) {
					$UserObj = new components\models\User($_user_);
					/** @var \components\Component\Quests\Quest $QuestComponent */
					$QuestComponent = $app->quest->setUser($UserObj)->get();

					$Checker = new \components\Component\Quests\check\CheckerEvent();
					$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RUIN_DO;
					if (($Item = $QuestComponent->isNeed($Checker)) !== false) {
						$QuestComponent->taskUp($Item);
						unset($Item);
					}

					unset($UserObj);
					unset($QuestComponent);
					unset($Checker);
				}
			} catch (Exception $ex) {
				\components\Helper\FileHelper::writeException($ex, 'cron_ghost_ruine_start');
			}
		}

		if ($needrestart) {
			//CronRuinesStart();
		}
	}

	CronRuinesStart();


	function MyDie($txt) {
		echo time().":".$txt."\n";
		lockDestroy("cron_ghost");
		die();
	}


	function CronRuinesClear() {
		global $keyexcluderooms, $team_colors, $gomoney;

		mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);
		// выбираем все карты и прибиваем чистые
		$q = mysql_query('SELECT * FROM `ruines_map` WHERE starttime+600 < '.time().' FOR UPDATE') or mydie(mysql_error().":".__LINE__);
		while($m = mysql_fetch_assoc($q)) {
			// ищем юзеров на карте
			$qa = mysql_query('SELECT * FROM `users` WHERE `room` BETWEEN '.$m['rooms'].' AND '.($m['rooms']+100).' AND id_city = 0') or mydie(mysql_error().":".__LINE__);
			if (mysql_num_rows($qa) == 0) {
				// юзеров нет, чистим карту

				// чистим шмотки
				mysql_query('DELETE FROM `ruines_items` WHERE `room` BETWEEN '.$m['rooms'].' AND '.($m['rooms']+100)) or mydie(mysql_error().":".__LINE__);

				// удаляем карту
				mysql_query('DELETE FROM `ruines_map` WHERE id = '.$m['id']) or mydie(mysql_error().":".__LINE__);

				// удаляем клонов
				mysql_query('DELETE FROM `users_clons` WHERE bot_online = 5 AND `bot_room` BETWEEN '.$m['rooms'].' AND '.($m['rooms']+100)) or mydie(mysql_error().":".__LINE__);

				// пишем что турнир заверешен
				$log = '<span class=date>'.date("d.m.y H:i").'</span> <b>Турнир завершен.</b><BR>';
				mysql_query('UPDATE `ruines_log` SET `active` = 0, `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE `id` = '.$m['id']) or mydie(mysql_error().":".__LINE__);

				RuinesCheckPenalty($m['id']);
			}
		}
		mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);

		// тут режим все заявки которым больше получаса
		$q = mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);
			$q = mysql_query('SELECT * FROM `ruines_start` WHERE ('.time().' - starttime) > '.(60*30).' AND substrCount(t1_logins,";") = num FOR UPDATE') or mydie(mysql_error().__LINE__);
			while($data = mysql_fetch_assoc($q)) {
				$tids = str_replace(';',',',$data['t1_logins']);
				$tids = substr($tids,0,strlen($tids)-1);

				mysql_query('DELETE FROM `ruines_start` WHERE id = '.$data['id']) or mydie(mysql_error().":".__LINE__);
				mysql_query('UPDATE `users` SET zayavka = 0 WHERE id IN ('.$tids.')') or mydie(mysql_error().":".__LINE__);

				// возвращаем бабки
				$t = explode(',',$tids);
				while(list($k,$v) = each($t)) {
					// возвращаем деньги
					mysql_query('UPDATE `users` set money = money + '.$gomoney.' WHERE id = '.$v) or mydie(mysql_error().":".__LINE__);

					// находим логин
					$qa = mysql_query('SELECT `login` FROM `users` WHERE id = '.$v) or mydie(mysql_error().":".__LINE__);
					$qa = mysql_fetch_assoc($qa) or mydie(mysql_error().":".__LINE__);

					$rec = array();
		    			$rec['owner']=$qa[id];
					$rec['owner_login']=$qa[login];
					$rec['owner_balans_do']=$qa['money'];
					$rec['owner_balans_posle']=$qa['money']+$gomoney;
					$rec['target_login'] = "Руины";
					$rec['type'] = 201; // вернул за руины
					$rec['sum_kr']= $gomoney;
					add_to_new_delo($rec); //юзеру
				}
			}
		$q = mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);


		// ищем всех с ключом больше 10 минут в офе
		$q = mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);

		$q = mysql_query('SELECT * FROM ruines_map') or mydie(mysql_error().":".__LINE__);
		$ruines = array();
		while($r = mysql_fetch_assoc($q)) {
			if ($r['k1owner'] > 0) $ruines[] = $r['k1owner'];
			if ($r['k2owner'] > 0) $ruines[] = $r['k2owner'];
		}

		$q = mysql_query('SELECT * FROM users WHERE battle = 0 AND in_tower = 2 AND id IN ("'.implode(",",$ruines).'") AND ( (odate + 600) <= '.time().'  OR block=1 ) ' ) or mydie(mysql_error().":".__LINE__);
		while($u = mysql_fetch_assoc($q)) {
			$m = mysql_query('SELECT * FROM ruines_map WHERE id = '.$u['ruines']) or mydie(mysql_error().":".__LINE__);
			$m = mysql_fetch_assoc($m);

			// забираем ключ
			mysql_query('UPDATE `ruines_map` SET k1owner = 0, k2owner = 0 WHERE id = '.$m['id']) or mydie(mysql_error().":".__LINE__);

			// пишем в лог
			$log = '<span class=date>'.date("d.m.y H:i").'</span> Ключ утерян по бездействию <font color="'.$team_colors[$u['id_grup']].'">'.nick_hist($u).'</font>.</b><BR>';
			mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE `id` = '.$m['id']) or mydie(mysql_error().":".__LINE__);

			// выкидываем рандомно ключ
			$keyroom = 0;
			do {
				$keyroom = mt_rand(1,77);
			} while(in_array($keyroom,$keyexcluderooms));
			mysql_query('INSERT INTO `ruines_items` (type,name,img,room,extra) VALUES ("4","Ключ","",'.($m['rooms']+$keyroom).',0)') or mydie(mysql_error().":".__LINE__);

			// пускаем мессагу
			$q2 = mysql_query('SELECT * FROM `users` WHERE `room` BETWEEN '.$m['rooms'].' AND '.($m['rooms']+100).' AND `in_tower` = 2') or mydie(mysql_error().":".__LINE__);
			while($u2 = mysql_fetch_assoc($q2)) {
				addchp ('<font color=red>Внимание!</font> <B><font color="'.$team_colors[$u['id_grup']].'">'.$u['login'].'</font></B> потерял ключ по бездействию.','{[]}'.$u2['login'].'{[]}',$u2['room'],$u2['id_city']);
			}
		}
		$q = mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);

		// ищем всех с ключом больше 60 минут
		$q = mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);

		$q = mysql_query('SELECT * FROM ruines_map WHERE type = 1') or mydie(mysql_error().":".__LINE__);
		$ruines = array();
		while($r = mysql_fetch_assoc($q)) {
			if ($r['k1owner'] > 0 && $r['k1timeout'] > 0 && $r['k1timeout']+3600 <= time()) $ruines[] = $r['k1owner'];
			if ($r['k2owner'] > 0 && $r['k2timeout'] > 0 && $r['k2timeout']+3600 <= time()) $ruines[] = $r['k2owner'];
		}

		$q = mysql_query('SELECT * FROM users WHERE battle = 0 AND in_tower = 2 AND id IN ("'.implode(",",$ruines).'")' ) or mydie(mysql_error().":".__LINE__);
		while($u = mysql_fetch_assoc($q)) {
			$m = mysql_query('SELECT * FROM ruines_map WHERE id = '.$u['ruines']) or mydie(mysql_error().":".__LINE__);
			$m = mysql_fetch_assoc($m);

			// забираем ключ
			mysql_query('UPDATE `ruines_map` SET k1owner = 0, k2owner = 0 WHERE id = '.$m['id']) or mydie(mysql_error().":".__LINE__);

			// пишем в лог
			$log = '<span class=date>'.date("d.m.y H:i").'</span> Ключ утерян по таймауту <font color="'.$team_colors[$u['id_grup']].'">'.nick_hist($u).'</font>.</b><BR>';
			mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE `id` = '.$m['id']) or mydie(mysql_error().":".__LINE__);

			// выкидываем рандомно ключ
			$keyroom = 0;
			do {
				$keyroom = mt_rand(1,77);
			} while(in_array($keyroom,$keyexcluderooms));
			mysql_query('INSERT INTO `ruines_items` (type,name,img,room,extra) VALUES ("4","Ключ","",'.($m['rooms']+$keyroom).',0)') or mydie(mysql_error().":".__LINE__);

			// пускаем мессагу
			$q2 = mysql_query('SELECT * FROM `users` WHERE `room` BETWEEN '.$m['rooms'].' AND '.($m['rooms']+100).' AND `in_tower` = 2') or mydie(mysql_error().":".__LINE__);
			while($u2 = mysql_fetch_assoc($q2)) {
				addchp ('<font color=red>Внимание!</font> <B><font color="'.$team_colors[$u['id_grup']].'">'.$u['login'].'</font></B> потерял ключ по таймауту.','{[]}'.$u2['login'].'{[]}',$u2['room'],$u2['id_city']);
			}
		}
		$q = mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);


		// тут ищем всех кто в заявке больше 10 минут в офе
		$qa = mysql_query('SELECT rs.id AS rsid, u.* FROM `users` AS u LEFT JOIN ruines_start AS rs ON u.zayavka = rs.id WHERE u.room = 999 AND rs.id is NOT NULL AND odate + 300 < '.time()) or mydie(mysql_error().":".__LINE__);
		while($u = mysql_fetch_assoc($qa)) {
			if (($u['ldate'] + 300) <= time()) {
				$q = mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);
				$q = mysql_query('SELECT * FROM `ruines_start` WHERE id = '.$u['rsid'].' FOR UPDATE') or mydie(mysql_error().":".__LINE__);
				if (mysql_num_rows($q) > 0) {
					$data = mysql_fetch_assoc($q) or mydie(mysql_error().":".__LINE__);
					$t1 = explode(';',$data['t1_logins']);
	
					// еще можем выйти из заявки
					$newt1 = "";
					while(list($k,$v) = each($t1)) {
						if (!empty($v) && $v !== $u['id']) {
							$newt1 .= $v.';';
						}
					}
	
					// возвращаем деньги
					mysql_query('UPDATE `users` set money = money + '.$gomoney.' WHERE id = '.$u['id']) or mydie(mysql_error().":".__LINE__);
	
					$rec = array();
		    			$rec['owner']=$u[id];
					$rec['owner_login']=$u[login];
					$rec['owner_balans_do']=$u['money'];
					$rec['owner_balans_posle']=$u['money']+$gomoney;
					$rec['target_login'] = "Руины";
					$rec['type'] = 201; // вернул за руины
					$rec['sum_kr']= $gomoney;
					add_to_new_delo($rec); //юзеру
	

	
					if (empty($newt1)) {
						// заявка полностью пустая - удаляем
						mysql_query('DELETE FROM `ruines_start` WHERE id = '.$data['id']) or mydie(mysql_error().":".__LINE__);
					} else {
						// обновляем заявку и перерисовываем отображение ников
						$t1cache = unserialize($data['t1_loginscache']);
						unset($t1cache[$u['id']]);
						$t1cache = serialize($t1cache);
	
						mysql_query('UPDATE `ruines_start` SET
							starttime = "'.time().'",
							t1_logins = "'.$newt1.'",
							t1_loginscache = "'.mysql_real_escape_string($t1cache).'"
							WHERE id = '.$data['id']
						) or mydie(mysql_error().":".__LINE__);
					}
					mysql_query('UPDATE `users` SET zayavka = 0 WHERE id = '.$u['id']) or mydie(mysql_error().":".__LINE__);
				}
				$q = mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);
			}
		}
	}

	CronRuinesClear();


lockDestroy("cron_ghost");
?>