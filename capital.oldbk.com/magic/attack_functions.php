<?
//функции для нападалок

 function attak_to_battle($bd,$telo,$jert,$check_bexit) //масивбоя, масив -нападающего, масив-жертвы, масив -выхода избоя
{
global $CP_ATTACK, $CP_ATTACK2;


 $need_update_user=0;
//механика вмешательства в бой
	// проставляем кто-где
	if ($jert['battle_t']==1) {$za=2;} else {$za=1;}
	$time = time();
	
if ($bd['coment']=="<b>Бой на Центральной площади</b>") 
	{
	//входим в бой на ЦП
						$telo['battle_t']=$za;
						$TEST_CAN_I_GO=can_i_go_battle($telo,$bd,$za,true); // и говорим что вызвали из ЦП
						if ($TEST_CAN_I_GO)
						{
						// тогда сбиваем неведов
						//если юзер вевидимка или перевоплот
								if ($telo['hidden']>0)
								{
								mysql_query("UPDATE users set hidden=0 , hiddenlog='' where id='{$telo['id']}' ");
								mysql_query("DELETE from effects where (type=1111 OR type=200)  and owner='{$telo['id']}' and idiluz!=0;");
								$telo['hidden']=0;
								$telo['hiddenlog']='';								
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
						return false;
						}
	}	
	
	
	
						if ($check_bexit['bexit_count']>0)
						{
						//вернулся вбой 
							$sexi[0]='вернулась';
							$sexi[1]='вернулся';
							$t1b=explode(";",$bd[t1]);
							$t2b=explode(";",$bd[t2]);
							 if  (in_array ($telo[id], $t1b) AND ($za==2) )
							 	{
							 	//был в первой команде стал во второй - надо добавить в масив
							 	$asdf='`t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$telo['id'].'\') , ';
							 	}
							 elseif  (in_array ($telo[id], $t2b) AND ($za==1) )
							 	{
							 	//был в 2 команде стал в 1 - надо добавить в масив
							 	$asdf='`t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$telo['id'].'\') , ';
							 	}
							 	
							mysql_query('UPDATE `battle`  SET '.$asdf.'  to1='.$time.', to2='.$time.', `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($telo).'\') WHERE `id` = '.$jert['battle'].'  and  status=0 and win=3 and t1_dead="";');
							if (mysql_affected_rows()>0)
								 {
								  //апдейт прошел
								  $need_update_user=1;
								 }
						}
						else
						{
						//первый вход в бой

						//ставим первый статус если надо
						// не учитываем кулачки тип 4 и 5
						  	if ( ($bd[status_flag]==0)  AND ($bd['type']!=4) AND ($bd['type']!=5) )
						   	{
						   		/*
						   		if ( ($bd['coment']=="<b>Бой на Центральной площади</b>") AND (time()>mktime(0,0,0,3,7,2018)) AND (time()<mktime(23,59,59,3,16,2018))  )
						   		{
							   		if  (users_in_battle($jert['battle']) >= 9)
							   			{
							   			 $sstatus=" status_flag=1 ,";
							   			}
							   			else
								    		{
									    		$sstatus='';
									    	}
						   		}
						   		else
						   		*/
						   		
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

								$add_auto="";
								$testbat=86039954;
								//http://capitalcity.oldbk.com/news.php?topic=5626
								if (!(($bd['status_flag'] >0) OR ($bd['CHAOS']==2) OR ($bd['CHAOS']==-1) ) )
								{
								//если нет авто удара
								
								if (($bd['type']==13) OR ($bd['type']==6  AND  (($bd['blood']==0) OR ($bd['blood']==1))))
									{
									//нужные типы боев
									
										//проверяем кол. людей
								    		if  (users_in_battle($jert['battle']) >= 9) //входящий уже 10й
								    		{
								    		// если больше 10
										//проверяем как начался бой заявкой или нет
										$get_nap=mysql_fetch_array(mysql_query("select id from battle_vars where battle='{$bd['id']}' and napal=0 limit 1"));
											if (!($get_nap['id'] >0))
											{
											//если нет ид-знач бой начался нападением
											$add_auto=" CHAOS=-1 ,  ";
											if ($bd[id]==$testbat) { addchp ('<font color=red>Внимание!</font> D0 ','{[]}Bred{[]}',-1,-1); }
											}
											else
											{
											 if ($bd[id]==$testbat) { addchp ('<font color=red>Внимание!</font> D1 ','{[]}Bred{[]}',-1,-1); }
											}											
								    		}
										else
										{
										 if ($bd[id]==$testbat) { addchp ('<font color=red>Внимание!</font> D2 ','{[]}Bred{[]}',-1,-1); }
										}
									}
									else
									{
									 if ($bd[id]==$testbat) { addchp ('<font color=red>Внимание!</font> D3 ','{[]}Bred{[]}',-1,-1); }
									}
								}
								else
								{
								 if ($bd[id]==$testbat) { addchp ('<font color=red>Внимание!</font> D4 ','{[]}Bred{[]}',-1,-1); }
								}
								

								
								

							$sexi[0]='вмешалась';
							$sexi[1]='вмешался';
							mysql_query('UPDATE `battle` SET '.$add_auto.' '.$sstatus.'  to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$telo['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($telo).'\') WHERE `id` = '.$jert['battle'].'   and  status=0 and win=3 and t1_dead="" ;');
							if (mysql_affected_rows()>0)
								 {
								  //апдейт прошел
								  $need_update_user=1;
								 }
						}
					///////////////////////////////////////////	
					if ($need_update_user==1)	
					{
						
							if ( ($telo[hidden]>0) and ($telo[hiddenlog]=='') )
							{ 
							$usrlogin='<i>Невидимка</i>'; 
							$doit_txt=$sexi[1];							
							$telo[sex]=1;
							} else
							{
							$fuser = load_perevopl($telo); 
							$usrlogin=$fuser['login']; 
							$telo[sex]=$fuser[sex];
							$doit_txt=$sexi[$telo[sex]];
							}
						
						
						addch ("<b>".$usrlogin."</b> ".$doit_txt." в <a href=logs.php?log=".$jert['battle']." target=_blank>поединок »»</a>.  ",$telo['room'],$telo['id_city']);
//						addlog($jert['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($telo,$za).'  '.$doit_txt.'  в поединок!<BR>');
						$telo[battle_t]=$za;
						$ac=($telo[sex]*100)+mt_rand(1,2);
	//					addlog($jert['battle'],"!:V:".time().":".nick_new_in_battle($telo).":".$ac."\n");			
						addlog($jert['battle'],"!:W:".time().":".BNewHist($telo).":".$telo[battle_t].":".$ac."\n");	



				        	///////////////////////////////////////////////////////////
						/// бои с волнами проверказа за влез за бота
						if ( ( $bd[coment]=='<b>Бой с Волнами Драконов</b>' ) or ( $bd[coment]=='<b>Бой с порождением Хаоса</b>' ) or ( $bd[coment]=='Бой с Исчадием Хаоса' ) or ( $bd[coment]=='<b>Бой с Духом Мерлина</b>' )  )
						{
							$test_za=mysql_fetch_array(mysql_query("select * from users_clons where battle={$bd[id]} and battle_t={$za} and bot_online > 0 and hp>0 LIMIT 1"));
							 if ($test_za[id]>0)
						 	{
							// Влез за ботов
							//echo "Za bota";
							$test_za[hp]=$test_za[hp]+$telo[hp];
							if ($test_za[hp]>$test_za[maxhp]) {$test_za[hp]=$test_za[maxhp]; }
							if ($test_za['sex'] == 1) {$action="";} else {$action="а";}
							$sexi[0]='ла';$sexi[1]=''; $uaction[0]='умер';$uaction[1]='погиб';$rda=mt_rand(0,1);
							//mysql_query_100("UPDATE `users` SET `hp`=0 WHERE `id`='{$telo[id]}';");
							$deltelohp="  , `hp`=0  ";
							//addlog($bd[id],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($test_za,$za).' выкрикнул'.$action.': <b>Опа! Свежая человечья кровушка! Пришло время подкрепиться!</b><BR>
							//<span class=date>'.date("H:i").'</span> '.nick_in_battle($test_za,$za).' напал на '.nick_in_battle($telo,$za).' и выпил всю его энергию, уровень жизни <B>+'.$telo[hp].'</B> ['.$test_za[hp].'/'.$test_za[maxhp].']<BR>
							//<span class=date>'.date("H:i").'</span> '.nick_in_battle($telo,$za).' <b>'.$uaction[$rda].'</b>!<BR>');
							$telo[battle_t]=$za;
					       	       addlog($bd[id],"!:X:".time().':'.nick_new_in_battle($test_za).':'.($test_za[sex]+400).":\n"."!:X:".time().':'.nick_new_in_battle($test_za).':'.($test_za[sex]+410).":".nick_in_battle($telo,$za)."\n"."!:D:".time().":".nick_new_in_battle($telo).":".(($telo[sex]*100)+1)."\n");
					 		}
					 		else
					 		{
					 		$deltelohp="";
					 		}
						}
						
						mysql_query("UPDATE users SET `battle` =".$bd['id'].",`zayavka`=0 ".$deltelohp." , `battle_t`='{$za}' WHERE `id`= ".$telo['id']);
						mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$jert['battle']}','{$telo['id']}')");
						/// вот это еще надо  добавить уник индекс в базу
						mysql_query("INSERT `battle_vars` (battle,owner,update_time,type)  VALUES ('{$jert['battle']}','{$telo['id']}','{$time}','1') ON DUPLICATE KEY UPDATE `update_time` = '{$time}' ;");
						
						
					return true;	
					}
					else
					{
					return false;
					}

}


 function attak_start_battle($bd,$telo,$jert,$blood_var,$status_var=0,$kulak=0,$icom='',$time_out=0,$batl_type=6,$KRARKAN = 0) //0-простой бой) //масивбоя, масив -нападающего, масив-жертвы, масив -выхода избоя
 {
	global $CP_ATTACK, $CP_ATTACK2, $test_room;
	 $BYLEVEL=true;

 						// начинаем бой
						if (($jert['bot']==2) OR ($jert['bot']==3))
							  {
							 	 $sql="UPDATE `users_clons` SET `battle` = 1 WHERE `id` = {$jert['id']} AND `bot_room` = {$telo['room']} AND `battle` = 0 LIMIT 1;";
							 $BYLEVEL=false;
							  }
							else
						 	{
								if ($KRARKAN) {
									$sql="UPDATE `users` SET `battle` = 1 WHERE `id` = {$jert['id']} AND `battle` = 0 and id_city='{$telo[id_city]}'  LIMIT 1;";
								} else {
									$sql="UPDATE `users` SET `battle` = 1 WHERE `id` = {$jert['id']} AND `room` = {$telo['room']} AND `battle` = 0 and id_city='{$telo[id_city]}'  LIMIT 1;";
								}
						 	}

						mysql_query($sql);
						if (mysql_affected_rows()>0)
						{
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
								mysql_query("UPDATE  `zayavka` SET zcount=zcount-1, team{$needup} = replace (team{$needup},'{$jert['id']};','') ,  t{$needup}hist = replace (t{$needup}hist,',".BNewHist($jert)."','') ".$fond_sql."  WHERE	id = {$jert['zayavka']};");
								}
							} // zay
			    //бой не кровавый
                            $bot_sql1="";
			    $bot_sql2="";
					if (($jert[bot]==2) OR ($jert[bot]==3))
					{
					 $BYLEVEL=false;
		   			//none
			   		//Если исчадье
					   if (($jert['id_user']>=101) and ($jert['id_user']<=110))
					   {
					    $blood_var=1;//переводим в кровь!
					    $status_var=3;//переводив 3 эпик
					    $icom='Бой с Исчадием Хаоса';
					   }
					   elseif (($jert['id_user']>=303) and ($jert['id_user']<=309))
					   {
					    $blood_var=1;//переводим в кровь!
					    $status_var=3;//переводив 3 эпик
					    $icom='<b>Бой с Духом Мерлина</b>';
					   }					   
					   else  if (($jert['id_user']>=42) and ($jert['id_user']<=65))
					   {
					    $icom='<b>Бой с Волнами Драконов</b>';
					    
						    if ($jert['level']>=10)
						    	{
							    $blood_var=1;//переводим в кровь!					    
							 }
					   }
					     else  if ($jert['id_user']==190672) 
					   {
					    $icom='<b>Бой с Пятницо</b>';
					   }
					     else  if ($jert['id_user']==9) 
					   {
					    $icom='<b>Бой с Тыквой</b>';
					   }					   
					    else
					    {
					    $icom='<b>Бой с порождением Хаоса</b>';
					    }
					}
					else
		                        if($jert[bot]==1) {
                       							 $BYLEVEL=false;
									$BOT=mysql_fetch_array(mysql_query("SELECT * from `users` where `id`='".$jert[id]."' ;"));
									$jert=$BOT;
									$BNAME=BNewHist($BOT);
									$BOT_items=load_mass_items_by_id($BOT);
									mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
									`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
									`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
									`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
									`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['hp']}',
									`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
									`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
									`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=1,`bot`=1,
									`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',`bot_room`='{$BOT['room']}',
									`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
									`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
									`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
									`injury_possible`=0, `battle_t`=2;");
									$jert['id'] = mysql_insert_id();
												//Если исчадье
												if ($BOT['id']==102)
												{
												$blood_var=1;//переводим в кровь!
												$status_var=3;//переводив 3 эпик
												$icom='Бой с Исчадием Хаоса';
												}
									$bot_sql1=",`CHAOS`";
									$bot_sql2=",'1'";
							}
							
				 		if ($kulak==1) // если задана кулачка то раздеваем
				 		{
							undressall($telo['id']);
				 			if ($jert['id_user'] == 84) {
								 $BYLEVEL=false;
								// раздеваем бота
								include "./dt_functions.php";
								undressallbot($jert);
							} else {
								undressall($jert['id']);
							}
							
				 		}

				 		if($telo[in_tower]==15)
				 		{
							 $BYLEVEL=false;
				 			$batl_type=1010;
				 			$icom='Бой в Башне Смерти';
				 			$blood_var=1;
							$time_out = mt_rand(1,5);
				 		}
				 			
							
						 mysql_query("UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = ".time()." WHERE  `hp` > `maxhp` AND `id` = '".$jert['id']."' LIMIT 1;");	// FIX HP
						// Fix HP
						mysql_query("UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = ".time()." WHERE  `hp` > `maxhp` AND `id` = '".$telo['id']."' LIMIT 1;");
						//Fix CP attak
						if ($CP_ATTACK==true) {$bot_sql1=",`CHAOS`"; $bot_sql2=",'-1'";  $BYLEVEL=false; }
						
						if ($CP_ATTACK2==true) {
											 $BYLEVEL=false;
											$icom="<b>Бой на Центральной площади</b>";
											$bot_sql1=",`CHAOS`"; $bot_sql2=",'-1'"; 
											
											//убираем неведов если есть
											//если юзер вевидимка или перевоплот
											if ($telo['hidden']>0)
											{
											mysql_query("UPDATE users set hidden=0 , hiddenlog='' where id='{$telo['id']}' ");
											mysql_query("DELETE from effects where (type=1111 OR type=200)  and owner='{$telo['id']}' and idiluz!=0;");
											$telo['hidden']=0;
											$telo['hiddenlog']='';								
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
						

				if ($time_out==0) //если тайм не был указан	                       
					{
			                        //рандом тайм - в минутах
			                     //   $sv = array(3,4,5);
			                     //   $time_out=$sv[mt_rand(0,2)];
			                     $time_out=3;
		                        }
	                        			$addlevels='';
	                        			$BYLEVEL=false;  //http://tickets.oldbk.com/issue/oldbk-2583
	                        			if ($BYLEVEL==true)
	                        			{
	                        			$minlevel=($jert['level']>$telo['level']?$telo['level']:$jert['level']);
	                        			$maxlevel=($jert['level']<$telo['level']?$telo['level']:$jert['level']);
	                        			$addlevels=$minlevel."|".$maxlevel;
	                        			}
	                        			
							mysql_query("INSERT INTO `battle`
								(
									`id`,`damage`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`blood`, `status_flag` ".$bot_sql1."
								)
								VALUES
								(
									NULL, '".$addlevels."' ,'".$icom."','','".$time_out."','".$batl_type."','1','".$telo['id']."','".$jert['id']."','".time()."','".time()."','".$blood_var."' , '".$status_var."' ".$bot_sql2."
								)");
							$battle_id = mysql_insert_id();

							if ($CP_ATTACK2==true) 
							{
							//инициализация линейки
							$telo['battle_t']=1;
							start_line_battle($battle_id,$telo,$jert);
							}

							if ($telo['in_tower'] == 15) {
								$qq = mysql_fetch_assoc(mysql_query('SELECT * FROM dt_map WHERE active = 1'));
								if ($qq) {
									$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($telo).'</b> напал кулачным нападением на <b>'.nick_hist($jert).'</b> завязался <a href="logs.php?log='.$battle_id.'" target="_blank">бой »»</a><BR>';
									mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$qq['id']);
								}
							}

							$time = time();
						 	$user_nick=BNewHist($telo);
							// создаем лог
							$rr = "<b>".nick_align_klan($telo)."</b> и <b>".nick_align_klan($jert)."</b>";
							addch ("<a href=logs.php?log=".$battle_id." target=_blank>Бой</a> между <B><b>".nick_align_klan($telo)."</b> и <b>".nick_align_klan($jert)."</b> начался.   ",$telo['room'],$telo['id_city']);
							//addlog($battle_id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
							addlog($battle_id,"!:S:".time().":".$user_nick.":".BNewHist($jert)."\n");
							$time = time();
							//вставка данных
							if (($jert[bot]==2) OR ($jert[bot]==3))
							{
							mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$battle_id}','{$telo['id']}')");
							mysql_query("INSERT INTO battle_vars (battle,owner,update_time,type) VALUES ('{$battle_id}','{$telo['id']}','{$time}','1') ");
							}
							else
							{
							mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$battle_id}','{$telo['id']}') , ('{$battle_id}','{$jert['id']}')");
							mysql_query("INSERT INTO battle_vars (battle,owner,update_time,type) VALUES ('{$battle_id}','{$telo['id']}','{$time}','1'), ('{$battle_id}','{$jert['id']}','{$time}','1')");
							}
							// апдейтим врага и чара
							if($jert[bot]==1)  {
								mysql_query("UPDATE `users_clons` SET `battle` = {$battle_id} WHERE `id` = '{$jert['id']}' ;");
							}
							else if (($jert[bot]==2) OR ($jert[bot]==3))   {
								mysql_query("UPDATE `users_clons` SET `battle` = {$battle_id} , `battle_t`=2  WHERE `id` = '{$jert['id']}' ;");
							}
							else
							{
								$krarkansql = "";
								if ($KRARKAN) {
									$krarkansql = ", room = 1 ";
								}
								mysql_query("UPDATE `users` SET `battle` ={$battle_id},`zayavka`=0, `battle_t`=2".$krarkansql."  WHERE `id`= {$jert['id']} ;");
							}
							mysql_query("UPDATE `users` SET `battle` = {$battle_id} , `zayavka`=0 , `battle_t`=1 WHERE `id` = {$telo['id']} ;");
							mysql_query_100("UPDATE battle set `status`=0,`t1hist`='".$user_nick."' , `t2hist`='".BNewHist($jert)."' where id={$battle_id};");
							
							if ($test_room==true)
							{
							//mysql_query("UPDATE battle set teams='Тренеровочный бой', `coment`='<b>Test</b>', type=22 ,`CHAOS`=1 WHERE `id` = '{$battle_id}'  ");
							//mysql_query("UPDATE battle set teams='AFB', type=7, `CHAOS`=2  WHERE `id` = '{$battle_id}'  ");
							//mysql_query("UPDATE `battle_vars` SET `napal`=0 WHERE `battle`='{$battle_id}'  ");
							}
							
							return $battle_id ;
	                	}
return false;	                	
}


function test_travm($telo)
 {
    //умная проверка травмы жертвы
	$get_travm = mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$telo['id']." AND (type=11 OR type=12 OR type=13 OR  type=14 );");
	$trav_count=0;
		while($travm_row = mysql_fetch_array($get_travm)) 
			{
			if ($travm_row[type]==11)
					{
				 	$trav_count++;
				 	}
			 else 	     {
				 	$trav_count+=3;			 
			 		}
			}
			
if ($trav_count>2)  
		{ 
		return true; 
		} 
	else 
	{
	return false;
	} 
 }


function test_attak_bots($ibot,$user)
{
			 if (($ibot==103) or ($ibot==303))
			     	{
			        if ($user[level]>6)     { $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<6)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }
			     elseif (($ibot==104) or ($ibot==304))
			     	{
			        if ($user[level]>7)     { $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<7)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }
			     elseif (($ibot==105) or ($ibot==305))
			     	{
			        if ($user[level]>8)     { $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<8)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }   
			     elseif (($ibot==106) or ($ibot==306))
			     	{
			        if ($user[level]>9)     { $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<9)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }			        
			     elseif (($ibot==107) or ($ibot==307))
			     	{
			        if ($user[level]>10)     { $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<10)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }			        
			     elseif (($ibot==108) or ($ibot==308))
			     	{
			        if ($user[level]>11)     	{ $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<11)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }
			     elseif (($ibot==109) or ($ibot==309))
			     	{
			        if ($user[level]>12)     	{ $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<12)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }			        
			     elseif (($ibot==110) or ($ibot==310))
			     	{
			        if ($user[level]>13)     	{ $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<13)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }			        
			     elseif (($ibot==101) or ($ibot==301))
			     	{
			        if ($user[level]>30)     	{ $bot_error="Этот бой для вас слишком легкий..."; }
			        elseif ($user[level]<14)	{ $bot_error="Этот бой для вас слишком тяжелый..."; }	
			        else  { $bot_error=""; }
			        }			        
			     else
			     {
			     $bot_error="";
			     }               
return$bot_error;
}



?>