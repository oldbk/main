<?
//новые клан вары - функции


function get_voins($war_id,$stor)
{
global $klan;



$voin=array();
$get_voin=mysql_query("SELECT *  from oldbk.clans_war_new_voin where war_id='{$war_id}' order by level ");
/*
if ($user['id']==10638)
{
echo $stor;
echo "<br>";
echo "SELECT *  from oldbk.clans_war_new_voin where war_id='{$war_id}' order by level ";
}
*/

	while($row=mysql_fetch_array($get_voin))
		{
		
			if ($stor==$row['stor'])
				{
				//воинственность моей стороны
				
					if ($row['clan_id']==$klan['id'])
					{
					//моего клана
					$voin['my_clan'][$row['level']]+=$row['voin'];
					}
				$voin['my'][$row['level']]+=$row['voin'];	//общая по уровням				
				$voin['my']['total']+=$row['voin']; //всего
				}
				else
				{
				$voin['en'][$row['level']]+=$row['voin'];				
				$voin['en']['total']+=$row['voin'];				
				}
		
		}
return $voin;
}


function print_mk_war($whoklan,$rulit=0)
{
global $klan_kazna, $user, $klan ,$recrut,$wpers,$wteam;
//форма объявления войны
$war_price[1]=100; //Дуэльная война
$war_price[2]=200; //Альянсовая война

$start_timer[1]=10800; //3 часа на подготовку
$start_timer[2]=86400;//1 сутки на подготовку

$fin_timer[1]=$start_timer[1]+86400;//кланвар длится сутки после подготовки
$fin_timer[2]=$start_timer[2]+172800;//кланвар длится 2 суток после подготовки

$prop_timer[1]=$fin_timer[1]+604800; //любая война как и сейчас 7 дней таймаут
$prop_timer[2]=$fin_timer[2]+604800; //любая война как и сейчас 7 дней таймаут

$protect_attak=86400; // Котька: если клан окончил войну то у него есть сутки пауза от нападений в кроне

/*
//тестовые таймеры
$start_timer[1]=600; //3 часа на подготовку
$start_timer[2]=600;//1 сутки на подготовку

$fin_timer[1]=$start_timer[1]+3600;//кланвар длится сутки после подготовки
$fin_timer[2]=$start_timer[2]+3600;//кланвар длится 2 суток после подготовки

$prop_timer[1]=$fin_timer[1]+600; //любая война как и сейчас 7 дней таймаут
$prop_timer[2]=$fin_timer[2]+600; //любая война как и сейчас 7 дней таймаут
*/
$buff='';
$show_form=true;

	$havewar=chk_war($whoklan);

	if (is_array($havewar))
		{
		//есть война
			//1. проверяем  в каком состоянии война
				
			if ( (strtotime($havewar['ztime'])<=time() ) AND (strtotime($havewar['stime'])>=time() )  )
				{
				//подготовка
					if ($havewar['wtype']==1)
					{
					//Дуэльная война- нет возможности собирать альянсы					
					$buff.=err("У Вас Дуэльная война, и нет возможности собирать альянсы!");
					
					if ((($havewar['defender']==$klan['id'])OR($havewar['agressor']==$klan['id']))  and ($rulit==1))
					  {
						//но можно звать - можно звать наемников в любой момент войны - но после окончания нельзя
						

							 if ($_SERVER['REQUEST_METHOD'] == "POST")
							 {
							 $buff.=do_naims($havewar,$rulit);
							 }
//echo "Рулит1:".$rulit;							 
							$buff.=show_naims();
					   }
					   else
						{
						//смотрят кланы которые в альянсе
						//в этом типе нет альянсов
						}
					
					}
					elseif ($havewar['wtype']==2)
					{
					//echo "подготовка к войне - собираем альянс";
					$buff.=err("У Вас Альянсовая война, во время подготовки можно собирать альянсы!");

					if ((($havewar['defender']==$klan['id'])OR($havewar['agressor']==$klan['id']))  and ($rulit==1))	
					 {	
						      	if ($havewar['defender']==$klan['id'])
						      		{
						      		$stor='defender';
						      		$protiv=$havewar['agressor'];
						      		}
						      		else
						      		{
						      		$stor='agressor';
						      		$protiv=$havewar['defender'];
							      	}
					 
							//считаем сколько уже кланов есть в своем альянсе
							$get_ally_count=mysql_fetch_array(mysql_query("SELECT count(id) as kol  from oldbk.clans_war_new_ally where warid='{$havewar['id']}' and active=1 and {$stor}='{$klan['id']}' ;"));
							if ($get_ally_count['kol']>=2)
							{
							$buff.=err("У Вас уже есть два клана в альянсе!");
							}
							else
							{
							//обработка 
						      	//форма для выбора клана  для альянса
						      		if (strtotime($havewar['stime'])>=(time()+3600) ) 
						      		{
								$buff.=show_mk_ally($klan['id'],$havewar['id'],$rulit,$protiv,$stor);
								}
							}
				
						//форма запроса наемников можно звать наемников в любой момент войны
						 if ($_SERVER['REQUEST_METHOD'] == "POST")
						 {
						 $buff.=do_naims($havewar,$rulit);
						 }
						$buff.=show_naims();
						
					}
					 else
						{
						//смотрят кланы которые в альянсе
							if (($havewar['clanid']==$klan['id']) and ($havewar['allyagr']>0))
							{
							$buff.="<br>Вы уже в альянсе против: ".$havewar['def_txt'];
							}
							elseif (($havewar['clanid']==$klan['id']) and ($havewar['allydef']>0))
							{
							$buff.="<br>Вы уже в альянсе против: ".$havewar['agr_txt'];						
							}
						}	
						
					}
					
					$buff.=err("<br> Война </font> ".$havewar['agr_txt']." против ".$havewar['def_txt']." <br><font color=red> начнется:<b>".$havewar['stime']."</b>");
					
					
					if (($havewar['defender']==$klan['id']) and ($rulit==1)) 
							{
							//если смотрит клан жертва и есть доступ - у него есть возможность отказаться  10 раз бесплатно
								if ($klan['warcancel']<0)
								{
									//отказ бесплатно
									if ($_POST['warcancel'])
									{
									$buff.=mk_warcancel($havewar,$klan);
									}
									else
									{
									$buff.= "<br>Можно отказаться бесплатно от войны еще ".$klan['warcancel']." раз.<form method=POST>";
									$buff.= "<input type=submit name=warcancel value='Отказаться'></form>";
									}
								}
								else
								{
								
								
								//отказ за деньги
									if ($_POST['warcancel'])
									{
									$buff.=mk_warcancel($havewar,$klan);
									}
									else
									{
									$m=$klan['warcancel']*1000+1000;
									$buff.= "<br>Можно отказаться от войны за ".$m." кр.<form method=POST>";
									$buff.= "<input type=submit name=warcancel value='Отказаться'></form>";
									}
								}
							}
					
				}
			else
				{
//				echo  "война в действии";
				$wrtxttype[1]='Дуэльная война';
				$wrtxttype[2]='Альянсовая война';				
				$buff.=err("<br> ".$wrtxttype[$havewar['wtype']]." </font> ".$havewar['agr_txt']." против ".$havewar['def_txt']."<br> <font color=red> Окончание:<b>".$havewar['ftime']."</b><br>");	
				$buff.='<a href=towerlog.php?war='.$havewar['id'].' target=_blank> »» </a>';								

					if (($havewar['defender']==$klan['id']) OR ( $havewar['clanid']==$klan['id'] AND $havewar['allydef']>0) )
						{
						$my_side_is='def';							
						}
					elseif 	(($havewar['agressor']==$klan['id']) OR ( $havewar['clanid']==$klan['id'] AND $havewar['allyagr']>0) )
					 	{
						$my_side_is='agr';	
					 	}
				$buff.="<br>";
				if ($havewar['wtype']==2)
						{
						$voin=get_voins($havewar['id'],$my_side_is);


							
						$buff.=err("Воинственность в войне вашего клана: ".(int)($voin['my_clan'][0])." , общая альянса:<b>".(int)($voin['my']['total'])." против ".(int)($voin['en']['total'])."</b>");
						}
				elseif ($havewar['wtype']==1)
						{
						$voin=get_voins($havewar['id'],$my_side_is);
						$buff.="Воинственность по уровням:
							<table border=1>
							<tr>
								<td>Уровень:</td>
								<td>Ваш клан</td>
								<td>Противник</td>								
								<td>Лидер</td>									
							</tr>";
							$total_my_wins=0;
							$total_en_wins=0;							

							foreach ($voin['my'] as $lvl=>$val)
								{
							if ($lvl!='total')
								{
							$buff.="<tr>
								<td>".(int)($lvl)."</td>
								<td>".(int)($voin['my_clan'][$lvl])."</td>
								<td>".(int)($voin['en'][$lvl])."</td>";

								if ($val>$voin['en'][$lvl])
									{
									$buff.="<td><img src='http://i.oldbk.com/i/flag.gif' title='Ваш клан лидирует' alt='Ваш клан лидирует' border=0></td>";
									$total_my_wins++;
									}
									elseif ($val<$voin['en'][$lvl])
									{
									$buff.="<td>&nbsp;</td>";
									$total_en_wins++;
									}
									else
									{
									$buff.="<td>&nbsp;</td>";
									}
							$buff.="</tr>";
								}
								}
						$buff.="</table><br> Общий зачет: $total_my_wins / $total_en_wins ";
						}
						
				$buff.="<br>";

					 	
				if ((($havewar['defender']==$klan['id'])OR($havewar['agressor']==$klan['id']))  and ($rulit==1))
					{
					//если смотрит клан который напал или клан который защищается
					 
					 if (strtotime($havewar['ftime'])>=time()) //нельзя когада закончилась
					 	{
						//можно звать наемников в любой момент войны
						 if ($_SERVER['REQUEST_METHOD'] == "POST")
						 {
						 $buff.=do_naims($havewar,$rulit);
						 }
						$buff.=show_naims();
						}
					}
					else
					{
					//смотрят кланы которые в альянсе
						if (($havewar['clanid']==$klan['id']) and ($havewar['allyagr']>0))
						{
							$buff.="<br>Вы уже в альянсе против: ".$havewar['def_txt'];
							$my_side_is='agr';							
						}
						elseif (($havewar['clanid']==$klan['id']) and ($havewar['allydef']>0))
						{
							$buff.="<br>Вы уже в альянсе против: ".$havewar['agr_txt'];		
							$my_side_is='def';
						}
					}
					
						if (strtotime($havewar['ftime'])>=time())
							{
							$start_bat=true;
							}
							else
							{
							$start_bat=false;
							}
					$buff.="<br>".print_do_attak($havewar,$my_side_is,$start_bat,$voin);
				}



		}
		else
		{
		//нет воин
		//проверяем запросы на вступление в альянс
		$arr_zay_all=chk_ally_req($whoklan);	

				//обработка вступление или отказ альянска
		if ($rulit>0)
			{
				if ((($_GET['ally_yes']) AND ((int)($_GET['ally_yes'])>0)) and (is_array($arr_zay_all)) )
						{
						//подтверждение о вхождении в альянс
						$ally_id=(int)($_GET['ally_yes']);
						$komu_row=$arr_zay_all[$ally_id];
						$nwrid=$komu_row['id']; //ид войны						
						
						
						// проверить уже вошедших  ПРОТИВ  как наемник из моего клана в этой войне - в которую хотим войти в альянс
							if ($komu_row['allyagr']>0)
							{
							//если входим за агрессоров - будем сматреть кто дефендер
							$look_clan_protiv=$komu_row['defender'];//против кого
							$look_clan_za=$komu_row['allyagr'];//за кого
							$look_clan_za_txt=$komu_row['agr_txt'];//за кого текст
					      		$stor='agressor';
							}
							elseif ($komu_row['allydef']>0)
							{
							//и наоборот
							$look_clan_protiv=$komu_row['agressor'];//против кого
							$look_clan_za=$komu_row['defender']; //закого
							$look_clan_za_txt=$komu_row['def_txt'];//за кого текст							
							$stor='defender';							
							}

						// грузим ВСЕ  АКТИВНЫЕ АЛьянсы -в этой войне
						$get_all_ally=mysql_query("SELECT *  from oldbk.clans_war_new_ally where warid='{$nwrid}' and active=1");		
						$get_ally_count=0;
						$clan_all_protiv=array();
						$clan_all_protiv[0]=$look_clan_protiv;
						while($ally_row=mysql_fetch_array($get_all_ally))
							{
								if ($ally_row[$stor]==$look_clan_za)
									{
									//считаем кол.активных кланов в альянсе за тот клан который пытаемся войти
									$get_ally_count++;
									}
									else
									{
									//запоминаем ид клана против которого пытаемся войти
									$clan_all_protiv[]=$ally_row['clanid'];
									
									}
							}			
//print_r($clan_all_protiv);
//echo "<br>";
						// проверить таймеры клана который входит в альянс - против всех кланов противника
						/*
						foreach ($clan_all_protiv as $i=>$v)
							{
							$test_timers=get_timers_kl($klan['id'],$v);
							if (is_array($test_timers))
							 	{
							 	break;
							 	}
							}
						if (is_array($test_timers)) // убрать
						 {
						  	$buff.=err("<br> Ваш клан не может войти в этот альянс, т.к. у Вашего клана не истекло время нападения на один из кланов альянса противника!<br>");
						 }
						 else	*/
						
					   if($klan_kazna['kr']>=50) // Принять приглашение в Альянс - 50кр. (снимается из клановой казны)
						{	
						$get_myclan_naim=mysql_fetch_array(mysql_query("select count(id) as kol  from oldbk.users where klan='{$klan['short']}' and naim_war='{$nwrid}' and  naim='{$look_clan_protiv}' and id_city=0")); //смотрим по кепу
//						$get_myclan_naim_ava=mysql_fetch_array(mysql_query("select count(id) as kol  from avalon.users where klan='{$klan['short']}' and naim_war='{$nwrid}' and  naim='{$look_clan_protiv}' and id_city=1")); //смотрим по аве

						if ($get_myclan_naim['kol'] >0 )
						{
							$buff.=err("<br> Ваш клан не может войти в этот альянс, т.к. Ваш соклановец воюет наемником на стороне противника!<br>");
						}
						else
						{
					
						//добавить проверку на количество кланов в альянсе - если больше 2х уже то нельзя !!!
							if ($get_ally_count>=2)
							{
								$buff.=err("У ".$look_clan_za_txt." уже есть два клана в альянсе!<br>");
							}
						else
							{
							$coment='Принятие приглашения в альянс.';
					   		if (by_from_kazna($klan['id'],1,50 ,$coment))							
							{
							// подтвердить  выставить флаг
							mysql_query("UPDATE oldbk.clans_war_new_ally  SET active=1  WHERE id='{$ally_id}' AND active=0 AND clanid='{$whoklan}' ;");
							 if (mysql_affected_rows()>0)
							{
							
								//тут проверим если это будет вторая заявка то остальные не активные удаляем - для этой войны
								if ($get_ally_count>=1)
								{
								mysql_query("DELETE FROM  oldbk.clans_war_new_ally  WHERE warid='{$nwrid}'  AND active=0 ");
								}
							
							// удалить остальные заявки
							mysql_query("DELETE FROM oldbk.clans_war_new_ally WHERE id!='{$ally_id}' AND clanid='{$whoklan}' ;");
							
							//добавляем имя клана в кеш войны
							$add_clan_a=getall_inf_clan($klan); //инфа моего клана и рекрутов если есть
							$add_clan=$add_clan_a['html'];
				   		 	$nwt=$komu_row['wtype']; // тип войны  на которую клан вошел в альянс
				   		 	
							$look_clan_protiv_name=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$look_clan_protiv.' LIMIT 1;'));
							$protiv_clan_a=getall_inf_clan($look_clan_protiv_name);

				   		 				
				   		 				mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$klan['short']}','{$protiv_clan_a['txt']}','{$nwrid}', '{$komu_row['stime']}' ) ON DUPLICATE KEY UPDATE war_with=CONCAT(war_with,',{$protiv_clan_a['txt']}') ");
							   		 	if ($klan['rekrut_klan']>0)
								   			{
											$myclan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$klan['rekrut_klan'].' LIMIT 1;'));
								   		 	mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$myclan2['short']}','{$protiv_clan_a['txt']}','{$nwrid}', '{$komu_row['stime']}' ) ON DUPLICATE KEY UPDATE war_with=CONCAT(war_with,',{$protiv_clan_a['txt']}') ");
											}
										mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$look_clan_protiv_name['short']}','{$add_clan_a['txt']}','{$nwrid}', '{$komu_row['stime']}' ) ON DUPLICATE KEY UPDATE war_with=CONCAT(war_with,',{$add_clan_a['txt']}') ");
										if ($look_clan_protiv_name['rekrut_klan']>0)
											{
											$clan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$look_clan_protiv_name['rekrut_klan'].' LIMIT 1;'));
											mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$clan2['short']}','{$add_clan_a['txt']}','{$nwrid}', '{$komu_row['stime']}' ) ON DUPLICATE KEY UPDATE war_with=CONCAT(war_with,',{$add_clan_a['txt']}') ");
											}
											

				   		 	
							if ($komu_row['allyagr']>0)
									{
										$help_to=$komu_row['agr_txt']; 
										mysql_query("UPDATE oldbk.clans_war_new SET agr_txt=CONCAT(agr_txt,',".$add_clan."') where id='{$nwrid}' ");
										//добавить в таймеры
										// и надо добавить таймеры против всех участников противоположного альянса!!!
										/* - уже не надо "в таймауте - распространяется только на тех кто объявлял и принимал, а не на альянсы. т.е. так как было ранее. "
										foreach ($clan_all_protiv as $i=>$v)
										{
							   		 	mysql_query("INSERT INTO `oldbk`.`clans_war_new_times` SET `kl1`='{$klan['id']}',`kl2`='{$v}',`fintime`=NOW() + INTERVAL ".$prop_timer[$nwt]." SECOND "); 										
							   		 	}
							   		 	*/
									}
								elseif ($komu_row['allydef']>0)
									{
										$help_to=$komu_row['def_txt'];
										mysql_query("UPDATE oldbk.clans_war_new SET def_txt=CONCAT(def_txt,',".$add_clan."') where id='{$nwrid}' ");										
										//добавить в таймеры
										// и надо добавить таймеры против всех участников противоположного альянса!!!	
										/* - уже не надо "в таймауте - распространяется только на тех кто объявлял и принимал, а не на альянсы. т.е. так как было ранее. "
										foreach ($clan_all_protiv as $i=>$v)									
										{
							   		 	mysql_query("INSERT INTO `oldbk`.`clans_war_new_times` SET `kl2`='{$klan['id']}',`kl1`='{$v}',`fintime`=NOW() + INTERVAL ".$prop_timer[$nwt]." SECOND "); 																				
							   		 	}
							   		 	*/
									}

							$buff.=err("<br> Ваш клан вошел  в альянс  к  ".$help_to."!<br>");
						 	unset($arr_zay_all);//затираем  весь масив
						 	$show_form=false;
							}
							
						     }
						     }	
							
						   }
						  }
						  else
						  	{
						  	$buff.=err("<br> В казне не хватает средств!<br>");
						  	}
						
						}
				elseif ((($_GET['ally_no']) AND ((int)($_GET['ally_no'])>0)) AND (is_array($arr_zay_all)) )
						{
						//отказ о вступление в альянс
						$ally_id=(int)($_GET['ally_no']);
						$komu_row=$arr_zay_all[$ally_id];
						mysql_query("DELETE FROM oldbk.clans_war_new_ally WHERE id='{$ally_id}' AND clanid='{$whoklan}' ;");
						 if (mysql_affected_rows()>0)
						 	{
						 		if ($komu_row['allyagr']>0)
									{
										$help_to=$komu_row['agr_txt']; 
									}
								elseif ($komu_row['allydef']>0)
									{
										$help_to=$komu_row['def_txt'];
									}
						 	$buff.=err("<br> Отказанно в альянсе ".$help_to."!<br>");
						 	unset($arr_zay_all[$ally_id]); // затираем только удаленную запись
						 	}
						 	
						 //отправить телеграмму клану о том что им отказали
						}
				 }
				else
				{
				 	//$buff.=err("<br> У Вас недостаточно прав доступа!<br>");
				}


		if ((is_array($arr_zay_all)) AND (count($arr_zay_all)>0))
		{
			$buff.="<b>Заявки на вступление в альянс:</b><br>";
				//есть заявки на встпление а альянс
				
					foreach ($arr_zay_all as $zid=>$ro)
					{
						if ($ro['allyagr']>0)
							{
							$help_to=$ro['agr_txt'];
							}
						elseif ($ro['allydef']>0)
							{
							$help_to=$ro['def_txt'];
							}
					
					if ($rulit>0)
					{
					$buff.="<br> Война </font> ".$ro['agr_txt']." против ".$ro['def_txt']."  начнется:<b>".$ro['stime']."</b> <br> Войти в альянс с ".$help_to."  <a href=?razdel=wars&ally_yes=".$zid.">Да</a> / <a href=?razdel=wars&ally_no=".$zid.">Нет</a>  ";
					}
					else
					{
					$buff.="<br> Война </font> ".$ro['agr_txt']." против ".$ro['def_txt']."  начнется:<b>".$ro['stime']."</b> <br> Приглашение в альянс с ".$help_to."  - ожидается. ";
					}

					}
				
		}
		else
		{

				if (time()<mktime(23,59,59,5,31,2016) )
				{
				$show_form=false;
				unset($_POST['mkwarto']);
				}
			
//			$show_form=false;
//			unset($_POST['mkwarto']);
		
		//нет заявок на вступления в альянсы 
		// рисуем старт для воин + обработка
			
			$mkwarto=(int)($_POST['mkwarto']);
			$wt=(int)($_POST['wt']);
			if (($_POST['addwar']) AND ($rulit>0) AND ($wt==1||$wt==2) AND ($mkwarto>0) )
			{
			if (file_exists("/www/locks/clanwarclock.txt")) 
			{
			//есть лок файл
			    	$buff.=err('Попробуйте еще раз!');
			} else 
			{
			// файла нет ставим лок файл
			$fp = fopen ("/www/locks/clanwarclock.txt","a"); //открытие
			flock ($fp,LOCK_EX); 
			fputs($fp , time()); 
			fflush ($fp); 
			flock ($fp,LOCK_UN); 
			fclose ($fp);
			$war_cost=$war_price[$wt]; //ставим стоимость нужно го типа

			if(\components\Helper\Captcha::validate()) {
			     if($klan_kazna)
			     {
			     //проверяем деньги в казне
			     if($klan_kazna['kr']>=$war_cost)
			      {
				//пробуем объявить войну
				//1. проверяем клан кандидат -на все возможные моменты
				$get_clan_targ=mysql_fetch_array(mysql_query("SELECT * from oldbk.clans where id='{$mkwarto}' and id!=81 and id!=34 and id!=78 and base_klan=0 and time_to_del=0 "));
				
				if (($get_clan_targ['id']==34) or ($get_clan_targ['id']==78) or  ($get_clan_targ['id']==81)or  ($get_clan_targ['id']==458)  )
				{
			   	$buff.=err("Для объявления войны, в казне не хватает средст :) ");
				}
				else
				if (($get_clan_targ['id']>0) and ($klan['id']!=$get_clan_targ['id']) )
				{
				//клан найден
				//1.1 - проверка на таймер войны
				$get_clan_protect=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_new_protect where clanid='{$get_clan_targ['id']}' "));
				 if (!($get_clan_protect['id']>0))
				 	{
				 	//1.2 - проверка своего таймера
					$get_clan_protect_my=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_new_protect where clanid='{$klan['id']}' "));
				 	if (!($get_clan_protect_my['id']>0))
				 	{
				 	//проверка людей в клане 
				 	$get_clan_pip=mysql_fetch_array(mysql_query("select count(id) as kolpip  from users  where klan='{$get_clan_targ['short']}' "));
				 	
				 	if ($get_clan_pip['kolpip']>0)
				 	{
					//2. проверяем главу
					if ($get_clan_targ['glava']>0)
						{
						 $targ_glava =check_users_city_data($get_clan_targ['glava']);
						 	if ($targ_glava['id']>0)
						 	{
						 	//3. проверим не воюет ли клан
						 		$test_targ_war=chk_war($get_clan_targ['id']);
						 		if (!(is_array($test_targ_war)))
						 		{
						 			$gtimers=get_timers_kl($klan['id'],$get_clan_targ['id']);
							 		if (!(is_array($gtimers)))
							 		//проверяем таймаут на клан
						 			{
							 		//все хорошо можем  снять деньги  $war_cost из казны
							 		$coment='Объявление войны клану <b>'.$get_clan_targ['short'].'</b>';
							   		if (by_from_kazna($klan['id'],1,$war_cost ,$coment))
							   		{
							   		//создаем
							   		$agr_text_a=getall_inf_clan($klan);
							   		$agr_text=$agr_text_a['html'];
							   		$def_text_a=getall_inf_clan($get_clan_targ);				   		
							   		$def_text=$def_text_a['html'];
							   		mysql_query("INSERT INTO `oldbk`.`clans_war_new` SET `agressor`='{$klan['id']}',`defender`='{$get_clan_targ['id']}', `agr_txt`='{$agr_text}',`def_txt`='{$def_text}', `wtype`='{$wt}',`ztime`=NOW(),`stime`=NOW() + INTERVAL ".$start_timer[$wt]." SECOND ,`ftime`=NOW() + INTERVAL ".$fin_timer[$wt]." SECOND ,`winner`=0;");
							   		 if (mysql_affected_rows()>0)
							   		 	{
							   		 	$new_war_id = mysql_insert_id();
							   		 	//создаем запись о том когда выйдет таймер
							   		 	mysql_query("INSERT INTO `oldbk`.`clans_war_new_times` SET `kl1`='{$klan['id']}',`kl2`='{$get_clan_targ['id']}',`fintime`=NOW() + INTERVAL ".$prop_timer[$wt]." SECOND "); 
							   		 	
								   		$buff.=err('Вы объявили войну клану:'.$def_text);
								   		$wrtxttype[1]='Дуэльная';
										$wrtxttype[2]='Альянсовая';
								   		//отправляем телеграмму  всем сокланам о том что объявлена война
								   		send_tele_to_clan($get_clan_targ['short'],"Вашему клану объявлена ".$wrtxttype[$wt]." война от клана: ".$agr_text." ");
								   		
							   		 	mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$get_clan_targ['short']}','{$agr_text_a['txt']}','{$new_war_id}', (NOW() + INTERVAL ".$start_timer[$wt]." SECOND) ) ");
								   		
								   		 if ($get_clan_targ['rekrut_klan']>0)
								   		 	{
								   		 	$clan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$get_clan_targ['rekrut_klan'].' LIMIT 1;'));
									   		send_tele_to_clan($clan2['short'],"Вашему клану объявлена ".$wrtxttype[$wt]." война от клана: ".$agr_text." ");								   		 	
							   		 		mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$clan2['short']}','{$agr_text_a['txt']}','{$new_war_id}' , (NOW() + INTERVAL ".$start_timer[$wt]." SECOND) ) ");
								   		 	}
								   		//отправляем телегу своему клану
								   		$wrtxttype[1]='Дуэльную';
										$wrtxttype[2]='Альянсовую';
										
								   		send_tele_to_clan($klan['short'],"Персонаж \"".$user['login']."\" объявил ".$wrtxttype[$wt]." войну клану: ".$def_text." ");
								   		
							   		 	mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$klan['short']}','{$def_text_a['txt']}','{$new_war_id}' , (NOW() + INTERVAL ".$start_timer[$wt]." SECOND) ) ");
								   		
								   		if ($klan['rekrut_klan']>0)
								   			{
											$myclan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$klan['rekrut_klan'].' LIMIT 1;'));
									   		send_tele_to_clan($myclan2['short'],"Персонаж \"".$user['login']."\" объявил ".$wrtxttype[$wt]." войну клану: ".$def_text." ");			
								   		 	mysql_query("INSERT INTO oldbk.clans_war_city_sync (name,war_with,war_id,stime) VALUES ('{$myclan2['short']}','{$def_text_a['txt']}','{$new_war_id}', (NOW() + INTERVAL ".$start_timer[$wt]." SECOND) ) ");
								   			}

								   		
								   		
								   		$show_form=false;
								   		}
								   		else
								   		{
								   		$buff.=err('Системная ошибка!');
								   		}
							   		}
							   	    }
							   	    else
							   	    {
							   	   $buff.=err('Вы не можете напасть на этот клан до <b>'.$gtimers['fintime'].'</b>');
							   	 
							   	    }
						 		}
						 		else
						 		{
								$buff.=err("Этот клан уже воюет!");						 		
						 		}
						 	}
						 	else
							{
							$buff.=err("У этого клана нет главы!");
							}
						}
						else
						{
						$buff.=err("У этого клана нет главы!");
						}
						
					   }
					   else
					    {
					    $buff.=err("У этого клана нет людей!");
					    }
					 
					   }
					   else
					   {
						$buff.=err("Ваш клан не может нападать до:".$get_clan_protect_my['fintime']);					   
					   }	
						
					}	
					else
					{
					//$buff.=err("У этого клана защита от нападения до:".$get_clan_protect['fintime']);
					 $buff.=err('Вы не можете напасть на клан, который недавно закончил предыдущую войну. Попробуйте позже еще раз.');							   	   
					}
				}
				else
				{
				$buff.=err("Такой клан не найден");
				}
			     }
			     else
			     {
			   	$buff.=err("Для объявления войны, в казне не хватает средст!");			     
			     }
			   }
			   else
			   {
			   	$buff.=err("Для объявления войны, вам необходима казна клана!");
			   }
                        }
			else
                          {
			   	$buff.=err("Не верная каптча!");
			  }
			
			unlink("/www/locks/clanwarclock.txt"); //удаляем блокировку
			}
			}

		//нет войны - рисуем -форму для вызова

			if (time()<mktime(23,59,59,5,31,2016) )
				{
				$show_form=false;
		  		$buff.='Клановое перемирие до: 31/05/2016 23:59:59';				
				}

//		$show_form=false;
//		$buff.='Клановые войны временно отключены.';	
			
			if (($rulit>0) and ($show_form))
			{
			//доступ к кнопке подачи вызова
			//+ надо учесть приглашения в альянсы
		 		$buff.='<form method=post><table border=0><tr><td>Война:</td></tr><tr>
  				<td>Объявить войну клану  <select size="1" name="mkwarto">
  				<option value=""></option>';
			$sql=mysql_query("select co.id,co.short,cr.id as rid,cr.short as rshort  from oldbk.`clans` co  left join oldbk.`clans` cr  on co.rekrut_klan=cr.id where co.base_klan=0 AND co.id !='".$whoklan."' AND (co.short not in ('Adminion','radminion','pal','ytesters','ztesters','xtesters','3testers','4testers','3testers1','4testers1','5testers','6testers','6testers1','testTest','rаdminion')) AND co.time_to_del=0   order by short");
			while($data=mysql_fetch_array($sql))
			{
					$buff.= '<option  value="'.$data['id'].'">'.$data['short'].($data['rid']>0?' - '.$data['rshort']:'').'</option>';
			}
					$buff.= '</select><br>';

	  		$buff.= '</td></tr>
	  		<tr>
	  			<td><input type=radio name=wt value=1>Дуэльная война (стоимость '.$war_price[1].' кр.) <br>
	  			<input type=radio name=wt value=2>Альянсовая война (стоимость '.$war_price[2].' кр.) 
	  			</td>
	  		</tr>';

	  		$buff.= '<tr><td align=center>';
			if($klan_kazna)
					{

					$buff.= \components\Helper\Captcha::render();
					$buff.= '<input type="submit" name="addwar" value="Объявить"> <br> <small>(в казне '.$klan_kazna['kr'].'кр.)</small>';
					}
					else
					{
					$buff.= 'Для оплаты необходимо наличие казны.';
					}
	  		$buff.= '</td></tr>';
	  		$buff.= '</table></form>';
  			}
			 
			
		
		
		  }
		
		
			if  ( ((int)($_GET['post_attack'])>0) and ($user['naim']>0) )
				{
				die('<script>location.href = "main.php?edit=1&effects=1&post_attack='.$_GET['post_attack'].')";</script>');			
				}
		
		
		}
		
		
		
		
return $buff;		
}

function print_do_attak($havewar,$my_side_is,$startbattle,$voin)
{
global $klan,$user;
//print_r($_GET);
//print_r($_POST);
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
					//картинка нападалки + арканы
					
		        		$buff.= '<br><a href="#" onclick="javascript:runmagic1(\'Нападение\',\'post_attack\',\'target\') "><img title="Клановое Нападение" src="http://i.oldbk.com/i/klan_attak_p.gif"></a>';

						//получаем кол.использованых - для сторон 			 		
			 			//вычисляем сколько должно быть у меня арканов
		 			 	if ($my_side_is=='agr')
		 			 	{
 				 			$get_count_arkan=(int)$havewar['agr_ark'];
		 			 		$my_need_ark =(int)($voin['my']['total']/500)+3; // 3 поумолчанию дается 
		 			 		
		 			 	}
		 				 elseif ($my_side_is=='def')
		 			 	{
 				 			$get_count_arkan=(int)$havewar['def_ark'];		 			 	
		 			 		$my_need_ark =(int)($voin['my']['total']/500)+3; // 3 поумолчанию дается 
		 			 	}
						
						$can_use_arkan=false;
						/*
		 				if ($get_count_arkan<$my_need_ark)
		 				{
			        			$buff.='<a href="#" onclick="javascript:runmagic1(\'Аркан\',\'post_attack2\',\'target\') "><img title="Аркан '.$get_count_arkan.'/'.$my_need_ark.'"  src="http://i.oldbk.com/i/klan_arkan_p.gif"></a>';        		
			        			$can_use_arkan=true;
			        		}
			        		else
			        		{
		        				$buff.='<img title="Аркан '.$get_count_arkan.'/'.$my_need_ark.'" src="http://i.oldbk.com/i/klan_arkan_p.gif">';
			        			$can_use_arkan=false;		        				
		        			}
		        			*/

//////////////////////////////////////////////////нападалка///////////////////////////////////////////////////////////////////////////////////////////////////////////
						if ( (isset($_POST['target'])) OR ((int)$_GET['post_attack']>0))
						{
						$stop=false;
						$_GET['post_attack']=(int)$_GET['post_attack'];
						
						if ($_GET['post_attack']>0)
						{
						//нападаем из чата - по дефалту нападаем напой а не арканом
						$_POST['use']='post_attack';							
						}
						else
					        if (($_POST['use']=='post_attack2') AND ($can_use_arkan==false))
					        {
					        	$buff.=err('<br>Арканы закончились :( <br>');
							$stop=true;
					        }
						elseif ($_POST['use']=='post_attack2')
						{
						$_POST['use']='post_attack';	
						$USE_ARKAN=true;
						}
	
						if ($user['ruines']>0)
						{
							$buff.=err('<br>Тут это не работет...<br>');
							$stop=true;								
						}
						elseif($_POST['use']=='post_attack') 
							{
								if ($_GET['post_attack']>0)
								{
							        $telo=mysql_fetch_array(mysql_query('SELECT * from users where id = "'.$_GET['post_attack'].'" LIMIT 1'));
							        }
							        else
							        {
							        $telo=mysql_fetch_array(mysql_query('SELECT * from users where login = "'. strip_tags($_POST['target']).'" LIMIT 1'));
							        $_GET['post_attack']=$telo['id'];
							        }
							
							
							if  (  ($telo['id']>0) AND (($telo['klan']!='') OR ($telo['naim_war'] == $havewar['id']) )  )
							{
							   $test_naim=false;
							//есть тело и в клане или тело наемник в войне этой
								//проверяем клан
								if ($telo['klan']!='')
										{
										//проверяем сразу клан если есть
									   	    $target_clan=mysql_fetch_array(mysql_query('SELECT * from oldbk.clans where short ="'.$telo['klan'].'" LIMIT 1'));
										    $target_clan['id']=($target_clan['base_klan']>0?$target_clan['base_klan']:$target_clan['id']); // если клан цель рекрут = берем ид клана основы									   	    
										   
										   if ($telo['naim_war'] == $havewar['id'])
										   	{
											    $test_naim=true; //  проверить на найм
											 }
									   	 }
									   	else
									   	{
									   	//если нету - то проверяем клан наемника
									   	    $target_clan=mysql_fetch_array(mysql_query('SELECT * from oldbk.clans where id ="'.$telo['naim'].'" LIMIT 1'));
									   	}
							   	    

								 //теперь проверяем можем ли мы на него напасть
								    if ($my_side_is=='agr')
								    	{
									 		if ( ($havewar['defender']==$telo['naim']) and ($telo['naim_war'] == $havewar['id']))
									 		{
									 		//все ок это найм - можно нападать
									 		
									 		}
									 		elseif ($havewar['defender']!=$target_clan['id']) // если клан на который пытаемся напасть не прямой враг
									 		{
											// если дефендер не клан на ккоторый пытаемся напасть - то ищем  в альянсах дефендера
											$target_clan_ally=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_new_ally where warid='{$havewar['id']}' and defender='{$havewar['defender']}' and clanid='{$target_clan['id']}'"));
												if (!($target_clan_ally['id']>0))
													{
													$buff.=err('<br>У Вас нет войны с этим кланом!<br>');
													$stop=true;		
													}
									 		}
								    	}
								    else
								    	{
								    			if ( ($havewar['agressor']==$telo['naim']) and ($telo['naim_war'] == $havewar['id']))
									 		{
									 		//все ок это найм - можно нападать
									 		
									 		}
									 		elseif ($havewar['agressor']!=$target_clan['id']) // если клан на который пытаемся напасть не прямой враг
									 		{
											// если агрессор не клан на который пытаемся напасть - то ищем  в альянсах агрессора
											$target_clan_ally=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_new_ally where warid='{$havewar['id']}' and agressor='{$havewar['agressor']}' and clanid='{$target_clan['id']}'"));
												if (!($target_clan_ally['id']>0))
													{
													$buff.=err('<br>У Вас нет войны с этим кланом!<br>');
													$stop=true;		
													}
									 		}
								    	}
						
							}
							elseif (($telo['id']>0) AND ($telo['klan']==''))
							{
							$buff.=err('<br>Этот персонаж не в клане!<br>');
							$stop=true;						
							}
							else
							{
							$buff.=err('<br>Такой персонаж не найден!<br>');
							$stop=true;						
							}
						     }
						
					        	if($stop==false)
							{
							//$buff.="ТИПА ОК";
							
								//если все гуд то инклюдим свиток
								$klan_war=true;
								//$startbattle - можно ли начинать бой
								//$USE_ARKAN - тут если использован аркан
								$buff.="<br>";
								include "magic/klanattack_new.php";
								//инклюдим свиток нападения
								//если в бою - то в каком? и можно ли вмешаться?
								//если не в бою - то нападаем.
								if($napal==1)
								{
								header("Location: fbattle.php");
								die('<script>location.href = "fbattle.php";</script>');
								}
							
							}
							elseif ($user['naim'] >0 )
							{
							$buff.=err('<br>Пробуем напасть как наемник...<br>');
							die('<script>location.href = "main.php?edit=1&effects=1&post_attack='.$_GET['post_attack'].')";</script>');
							}
							

						}
						else
						{
						//print_r($_GET);
						//echo "<br>";
						//print_r($_POST);
						}
return $buff;
}

function chk_war($clanid)
{
//проверка на существует ли текущая война у клана
$get_wars=mysql_fetch_array(mysql_query("select cw.* , cy.clanid , cy.clan_txt, cy.agressor as allyagr, cy.defender as allydef  from oldbk.clans_war_new  cw LEFT join oldbk.clans_war_new_ally cy on cy.warid=cw.id and cy.active=1 where cw.winner=0 and (cw.agressor='{$clanid}' or cw.defender='{$clanid}' or cy.clanid='{$clanid}' )")); 
	if ($get_wars['id']>0)
		{
		return $get_wars;	//есть война возвращаем ее масив
		}
return false;
}

function chk_ally_req($clanid)
{
//проверка на  приглашения в альянс и войны со статусом 0
//надо масив со списком всех приглашений
	$get_req=mysql_query("select cy.id as zid, cy.clanid, cy.clan_txt as allytxt, cy.agressor as allyagr, cy.defender as allydef, cw.*   from oldbk.clans_war_new_ally cy left join oldbk.clans_war_new cw on cy.warid=cw.id where clanid='{$clanid}' "); /// and stime>NOW()+ INTERVAL 1 HOUR
	if (mysql_num_rows($get_req) >0)
			{
					while($row=mysql_fetch_assoc($get_req))
					{

					// проверка времени войны - вступление а альянс можно только в момент приготовления за 1 час
					
					if ((strtotime($row['ztime'])<=time()) AND ( strtotime($row['stime'])>=(time()+3600) ) )
						{
						$out[$row['zid']]=$row;
						}
						else
						{
						//просроченные запросы об альянсайх
						//удаляем запросы на альянсы
						mysql_query("DELETE FROM oldbk.clans_war_new_ally WHERE id='{$row['zid']}';");
						}
					}
			}
			else
			{
			return false;
			}

return $out;
}

function show_mk_ally($clanid,$warid,$rulit,$look_clan_protiv,$stor)
{
global $klan_kazna;
//создание запроса на альянс + форма
$show_form=true;	
$ally_cost=50;
		
			if (($_SERVER['REQUEST_METHOD'] == "POST") AND ($_POST['addally']) AND ($rulit>0) )
				{
				$mkally=(int)($_POST['mkally']);
					if ($mkally>0)
					{
					$get_clan_targ=mysql_fetch_array(mysql_query("SELECT * from oldbk.clans where id='{$mkally}' and id!=81 and id!=34 and id!=78  and id!=458 and base_klan=0 and time_to_del=0 "));					
					if ($get_clan_targ['id']>0)
					{
					$get_clan_mess=mysql_fetch_array(mysql_query("SELECT * from oldbk.clans_war_new_ally  where  `warid`='{$warid}' and  `clanid`='{$get_clan_targ['id']}' "));					
						if ($get_clan_mess['id']>0)
						{
						$buff.=err('<br>Этот клан уже имеет приглашение в альянс в этой войне!<br>');
						}
					else
					{
					$targ_glava =check_users_city_data($get_clan_targ['glava']);
					if ($targ_glava['id']>0)
					{
//					print_r($_POST);
					//1 проверить не воюет ли клан
					$clan_havewar=chk_war($mkally);
					if (is_array($clan_havewar))
						{
						//воюет или готовится к войне
						$buff.=err('<br>Этот клан уже воюет или готовится к войне, его нельзя позвать в альянс!<br>');
						}
						else
						{
						//2. проверить наемников - этого клана(и его рекрутов) - не против ли они - в этой войне
						$add_sql=" klan='{$get_clan_targ['short']}' ";
	
								if ($get_clan_targ['rekrut_klan']>0)
								{
								$get_rekr=mysql_fetch_array(mysql_query("SELECT * from oldbk.clans where id='{$get_clan_targ['rekrut_klan']}' "));					
								if ($get_rekr['id']>0)
									{
									$add_sql=" ( klan='{$get_clan_targ['short']}' OR klan='{$get_rekr['short']}'  )";
									}
								}
																
						 $get_aclan_naim=mysql_fetch_array(mysql_query("select count(id) as kol  from oldbk.users where ".$add_sql." and naim_war='{$warid}' and  naim='{$look_clan_protiv}' and id_city=0")); //смотрим по кепу
						 
							if ($get_aclan_naim['kol']>0)
								{
								$buff.=err('<br>У этого клана есть наемник который воюет против Вас, этот клан нельзя позвать в альянс!<br>');
								}
								else
								{
								  if($klan_kazna['kr']>=$ally_cost)
								  	{
								  	//2. 
								  	//проверка своего таймера
									$get_clan_protect_my=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_new_protect where clanid='{$get_clan_targ['id']}' "));
								 	if (!($get_clan_protect_my['id']>0))
									{								  	
									//3. если все гуд
									// 4. списать деньки  и создать приглашение								
									$coment='Приглашение в альянс клану <b>'.$get_clan_targ['short'].'</b>';
							   		if (by_from_kazna($clanid,1,$ally_cost ,$coment))
							   			{

							   			$kl_txt=getall_inf_clan($get_clan_targ);
										$kl_txt=$kl_txt['html'];							   			
							   			mysql_query("INSERT INTO `oldbk`.`clans_war_new_ally` SET `warid`='{$warid}',`clanid`='{$get_clan_targ['id']}',`clan_txt`='{$kl_txt}', ".$stor."='{$clanid}'  ,`active`=0;");
							   				 if (mysql_affected_rows()>0)
							   				{
							   				$buff.=err('<br>Приглашение в альянс удачно отправлено:'.$kl_txt.'<br>');		

							   			//телеграмма клану о приглашении							   				
				   							send_tele_to_clan($get_clan_targ['short'],"Вашему клану пришло приглашение о вступлении в альянс!");
											if ($get_clan_targ['rekrut_klan']>0)
								   		 	{
								   		 	$clan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$get_clan_targ['rekrut_klan'].' LIMIT 1;'));
									   		send_tele_to_clan($clan2['short'],"Вашему клану пришло приглашение о вступлении в альянс!");								   		 	
								   		 	}				   							
							   				
							   											
							   				}
							   			
							   			}
							   		   }
							   		   else
							   		   {
										$buff.=err('<br>Этот клан не может войти в альянс до:'.$get_clan_protect_my['fintime'].'<br>');																   		   
							   		   }
									}
									else
									{
									$buff.=err('<br>Нехватает кредитов на оплату!<br>');									
									}
								}
						}

					}
					else
						{
						$buff.=err("<br>У этого клана нет главы!<br>");
						}
						
					 }
					}
					else
						{
						$buff.=err('<br>Этот клан нельзя позвать в альянс!<br>');						
						}
					}				
				}
			
			
		
			if (($rulit>0) and ($show_form))
			{
			$buff.='<form method=post>
  				Позвать в Альянс ('.$ally_cost.' кр) <select size="1" name="mkally">
  				<option value="">Список доступных к кланов</option>';
				$sql=mysql_query("select co.id,co.short,cr.id as rid,cr.short as rshort  from oldbk.`clans` co  left join oldbk.`clans` cr  on co.rekrut_klan=cr.id where co.base_klan=0 AND co.id !='".$clanid."' AND (co.short not in ('Adminion','radminion','pal','ytesters','ztesters','xtesters','3testers','4testers','3testers1','4testers1','5testers','6testers','6testers1','testTest','rаdminion')) AND co.time_to_del=0   order by short");
				while($data=mysql_fetch_array($sql))
				{
					$buff.= '<option  value="'.$data['id'].'">'.$data['short'].($data['rid']>0?' - '.$data['rshort']:'').'</option>';
				}
				$buff.= '</select>';
			$buff.= '<input type="submit" name="addally" value="Отправить запрос"> ';
	  		$buff.= '</form>';
			}

return $buff;
}

function getall_inf_clan($clan)
{
//текст для кеша клана - входной масив клана основы

$out['html']=show_klan_name($clan['short'],$clan['align']);
$out['txt']=$clan['short'];

 if ($clan['rekrut_klan']>0)
 	{
 	$clan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$clan['rekrut_klan'].' LIMIT 1;'));
 	$out['html'].=" и рекруты ".show_klan_name($clan2['short'],$clan2['align']);
 	$out['txt'].=",".$clan2['short'];
 	}
return $out;
}

function get_timers_kl($k1,$k2)
{
//проверка таймаута между кланами
$get_test=mysql_fetch_assoc(mysql_query("SELECT * FROM clans_war_new_times WHERE ((kl1='{$k1}' and kl2='{$k2}' ) OR (kl2='{$k1}' and kl1='{$k2}' ) ) and fintime>NOW()  LIMIT 1"));
	if (is_array($get_test))
	{
	return $get_test;
	}
	
return false;	
}

function mk_warcancel($havewar,$klan)
{
global $klan_kazna, $user ;
$war_price[1]=100; //Дуэльная война
$war_price[2]=200; //Альянсовая война

$OK=false;
//отказ войны
	if ($klan['warcancel']>=0)
	{
	//снимаем деньги за отказ
		if($klan_kazna)
		{
		$m=$klan['warcancel']*1000+1000;
			 if($klan_kazna['kr']>=$m)
			      {
					$coment='Отказ в войне клану '.$havewar['agr_txt'];
			   		if (by_from_kazna($klan['id'],1,$m ,$coment))
			   		{
			   		//оплачено
		   			$OK=true;
			   		}
					else
					{
					$buff.= err('<br>Для отказа войны в казне не хватает средст! ');				
					}			   		
				}
				else
				{
				$buff.= err('<br>Для отказа войны в казне не хватает средст! ');				
				}
		}
		else
		{
		$buff.= err('<br>Для оплаты необходимо наличие казны.');
		}
		
	}
	else
	{
	$OK=true;
	}

	if ($OK==true)
		{
		//
		mysql_query("UPDATE oldbk.clans_war_new set winner=4 where id='{$havewar['id']}' and winner=0;");
		 if (mysql_affected_rows()>0)
		 	{
		 	//увеличиваем счетчик отказов
		 	mysql_query("UPDATE oldbk.clans set warcancel=warcancel+1 where id='{$klan['id']}' ");
		 	
		 	//удаляем все альянсы для этой войны активные и нет
		 	mysql_query("DELETE from oldbk.clans_war_new_ally where warid='{$havewar['id']}';");
		 	
		 	//очищаем приглашения наемников
		 	mysql_query("DELETE from oldbk.naim_message where war_id='{$havewar['id']}';");
		 	//очищаем тех наемников которые уже зашли - чистится автоматом из триггера oldbk.naim_message
		 	
			//чистим кеш нап чата
			mysql_query("delete from `oldbk`.`clans_war_city_sync` where war_id='{$havewar['id']}';");
		 	
		 	//возвращаем бабки клану который их вызывал на вонйну
			// 	если клану отказано от войны то ему 100-200 кр за объявление возвращаются в казну
			//$war_price
			$back_money=$war_price[$havewar['wtype']];
			mysql_query("UPDATE oldbk.clans_kazna set kr=kr+{$back_money}   WHERE `clan_id` = '{$havewar['agressor']}' ;");
//			echo "UPDATE oldbk.clans_kazna set kr=kr+{$back_money}   WHERE `clan_id` = '{$havewar['agressor']}' ;" ;
			 if (mysql_affected_rows()>0)
			 	{
			 	//пишем в лог в казну  агрессора
			 	$txt='Возврат '.$back_money.' кр объявление войны клану:'. $havewar['def_txt'];
			 	txt_to_kazna_log(1,1,$havewar['agressor'],$txt,$user);
			 	}
			 	else
			 	{
			 	//echo "ERROR KAZNA";
			 	}

		 	//отправляем телеграммы напавшим
			$clan1=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$havewar['agressor'].' LIMIT 1;'));
			send_tele_to_clan($clan1['short'],"Клан : ".$havewar['def_txt']." отказался от войны!");
			if ($clan1['rekrut_klan']>0)
			{
			$clan2=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$clan1['rekrut_klan'].' LIMIT 1;'));
			send_tele_to_clan($clan2['short'],"Клан : ".$havewar['def_txt']." отказался от войны!");
			}
		 	
		 	//при отмене - что делать с таймерами защиты? агрессора и тех кто в его альянсе - пока остаются
		 	
		 	$buff.= err('<br>Удачно отказанно в войне!');
		 	}
		 	else
		 	{
		 	$buff.= err('<br>Системная ошибка -отказа в войне!');
		 	}
		}

	
return $buff;
}


function show_naims()
{
global $klan, $rulit;
//echo "Рулит".$rulit;
			$out='<form method=post>';
  			$out.="Позвать наемника (30 кр):	<select size=\"1\" name=\"naimid\">";
  			$out.= '<option value="">Список доступных к найму</option>';
  			$snaim=mysql_query("select e.*, u.login, u.level , u.id as uid from oldbk.effects e LEFT JOIN oldbk.users u ON e.owner=u.id where e.type=2000 and u.id_city=0 and naim=0 and u.klan!='{$klan[short]}' and u.klan!='radminion'
  							UNION select e.*, u.login, u.level , u.id as uid from avalon.effects e LEFT JOIN avalon.users u ON e.owner=u.id where e.type=2000 and u.id_city=1 and naim=0 and u.klan!='{$klan[short]}' and u.klan!='radminion' ");
  			while($ndata=mysql_fetch_array($snaim))
			{
			$out.= '<option   value="'.$ndata[uid].'">'.$ndata[login].'['.$ndata[level].'] - окончание лиц. '.date("d-m-Y H:i",$ndata[time]).' </option>';
			}
  			$out.='</select>';
			$out.='<input type="submit" name="addnaim" value="Пригласить"> <br> ';  			
			$out.='</form>';
return $out;
}

function do_naims($wararr,$rulit=0)
{
global $klan, $user;
$buf='';
	if ($_POST[addnaim] && $rulit==1 && $_POST[naimid] > 0)
        {
		if(is_array($wararr))
		{
			if($wararr['defender']==$klan['id'])
	        	{
	        		$myside='defender';
	        		$enside='agressor';
	        		
	        	}
	        	else
	        	if($wararr['agressor']==$klan['id'])
	        	{
	        		$myside='agressor';
	        		$enside='defender';	        		
	        	}
		$mwar_id=$wararr['id']; // ид войны 
		}
///////////////////////////////////////////////////				
        if ($mwar_id>0)
        {
        
        	$naim=(int)($_POST[naimid]);
        	$get_test_naim=check_users_city_data($naim);
        	if ($get_test_naim[id]>0)
        		{
	        	$ucit[0]='oldbk.';
	        	$ucit[1]='avalon.';
			// прповерка эфекта у наема
        		$get_test_eff=mysql_fetch_array(mysql_query("select * from ".$ucit[$get_test_naim[id_city]]."effects where owner='".$get_test_naim[id]."' AND type= 2000  limit 1;"));
        		//проверить нету ли уже у меня запроса
        		if ($get_test_eff[id]>0)
        			{
         		$get_test_message=mysql_fetch_array(mysql_query("select * from oldbk.naim_message  where owner='".$get_test_naim[id]."' AND in_klan_id='{$klan['id']}' ;"));	
        		 if (!($get_test_message))	
        			{
        			 if ($get_test_naim['naim']==0)
        			 	{
        			 	$good_to_add=0;
        			 	 if ($get_test_naim['klan']!='')
        			 	 	{
	 	 	
        			 	 	//дополнительные проверки если найм в клане
        			 	 	 $get_naim_clan=mysql_fetch_array(mysql_query("SELECT * from oldbk.clans where short='{$get_test_naim['klan']}' ; "));
        			 	 	 if ($get_naim_clan['id']==$klan['id'])
        			 	 	 {
	        			 	 $buf.=err('<br>Наемник из вашего клана, он и так за Вас :)<br>');
        			 	 	 }
        			 	 	 elseif ($get_naim_clan['base_klan']==$klan['id'])
        			 	 	 {
	        			 	 $buf.=err('<br>Наемник из вашего рекрут клана, он и так за Вас :)<br>'); 
        			 	 	 }
        			 	 	 elseif ($get_naim_clan['id']==$wararr[$enside])
        			 	 	 {
	        			 	 $buf.=err('<br>Наемник из клана Вашего врага...его нельзя позвать!<br>'); 
        			 	 	 }        			 	 	 
        			 	 	 elseif (($get_naim_clan['base_klan']==$wararr[$enside]) and ($get_naim_clan['base_klan']>0))
        			 	 	 {
	        			 	 $buf.=err('<br>Наемник из рекрут клана Вашего врага...его нельзя позвать!<br>'); 
        			 	 	 }         			 	 	 
						else
        			 	 	 {
        			 	 	 $good_to_add=1; //  ставим по умолчанию что все нормально
        			 	 	 	if($get_naim_clan['base_klan']>0)
        			 	 	 	{
        			 	 	 		$get_naim_clan['id']=$get_naim_clan['base_klan']; //сровняли рекрутов и основу найма
        			 	 	 	}
        			 	 	 	//теперь надо проверить чтобы наемник не был в кланах и их рекрутов против
        			 	 	 	$get_test_inally=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_new_ally where clanid='{$get_naim_clan['id']}' and warid='{$mwar_id}' ; "));
        			 	 	 	
        			 	 	 	if ($get_test_inally['id']>0)
        			 	 	 	{
        			 	 	 	   if  ($get_test_inally['active']==1)
        			 	 	 	     {	
        			 	 	 		if ($get_test_inally[$myside]==$klan['id']) 
        			 	 	 		{
	        			 	 	 		$buf.=err('<br>Наемник из клана Вашего альянса, вы неможете его позвать...<br>');
       			 	 	 			 	$good_to_add=0;
        			 	 	 		}
        			 	 	 		else
        			 	 	 		{
        			 	 	 			$buf.=err('<br>Наемник из клана альянса Вашего врага, вы неможете его позвать...<br>');
       			 	 	 			 	$good_to_add=0;
        			 	 	 		}
        			 	 	 	    }
        			 	 	 	    else
        			 	 	 	    {
	   			 	 	 		if ($get_test_inally[$myside]==$klan['id']) 
        			 	 	 		{
	        			 	 	 		$buf.=err('<br>Клан наемника еще не ответил на Ваш запрос альянса, вы неможете его позвать...<br>');
       			 	 	 			 	$good_to_add=0;
        			 	 	 		}
        			 	 	 		else
        			 	 	 		{
        			 	 	 			$buf.=err('<br>Клан наемника еще не ответил на запрос альянса Вашего врага, вы неможете его позвать...<br>');
       			 	 	 			 	$good_to_add=0;
        			 	 	 		}
        			 	 	 	    }
        				 	 	}
        			 	 	 }
				        	}
				        	else
				        	{
			        		$good_to_add=1;
				        	}

						if ($good_to_add==1)
						{
		        			$my_clan_name=$user['klan']; // название моего клана
		        			$my_war_name=''; // текст войны кто против кого получаем название войны из $mwar_id
        					// создаем запрос найму
       						// списываем логируем деньги - из казны
       						if (by_from_kazna($klan['id'],1,30,'Вызов наемника:'.$get_test_naim['login']))
       						   {
	        					mysql_query("INSERT INTO `oldbk`.`naim_message` SET `owner`='{$get_test_naim['id']}',`in_klan_id`='{$klan['id']}',`stat`=0,`sender`='{$user['id']}',`war_id`=$mwar_id;");
							if (mysql_affected_rows() >0 )
							{
	        					//+отправляем ему  сообщение - проверить отправления сообщения
        						 if($get_test_naim[odate] >= (time()-60))
								                        {
							                        	addchp ('<font color=red>Внимание!</font> Клан  '.$my_clan_name.', в лице '.$user['login'].', просит Вас помочь в войне '.$my_war_name.'. Принять или отказать Вы можете во вкладке "Состояние" !','{[]}'.$get_test_naim['login'].'{[]}');
								                        }
								                        else
								                        {
								                         mysql_query("INSERT INTO oldbk.`telegraph`   (`owner`,`date`,`text`) values ('".$get_test_naim['id']."','','<font color=red>Внимание!</font> Клан ".$my_clan_name.", в лице ".$user['login'].", просит Вас помочь в войне ".$my_war_name.". Принять или отказать Вы можете во вкладке \"Состояние\" !');");
								                        }
							 $buf.=err("<br>Приглашение наемнику удачно отправлено!<br>");
							}
        					   }
        					   else
        					   {
        					   	 $buf.=err("<br>Приглашение наемнику не отправлено!<br>");
        					   }
        					}
        				}
        				else
        				{
        				$buf.=err('<br>Этот наемник уже занят!<br>');
        				}
        			 }
        			 else
        			 {
        			 $buf.=err('<br>Этот наемник уже получил от Вас приглашение!<br>');
        			 }
        			}
	        		else
        			{
        			$buf.=err('<br>У наемника нет лицензии!<br>');
        			}
        		}
        		else
        		{
        		$buf.=err('<br>Наемник не найден<br>');
        		}
        	
        	}
        	else
        	{
        		$buf.=err('У Вас нет войны!<br>');
        	}
        	
        }
return $buf;
}

function send_tele_to_clan($klan_name,$msg)
{
						 		$data=mysql_query("select * from oldbk.users where klan='".$klan_name."';");
		 						while($sok=mysql_fetch_array($data))
							 	{
	 							telegraph_new($sok,$msg,'2',time()+(2*24*3600));
							 	}
}

//не забыть добавлять в кеш данные  наемников
//не забыть у наемников проверять какие кланы в альянсе против при подтверждении


?>