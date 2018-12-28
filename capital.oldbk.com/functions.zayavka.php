<?
//echo "//функции заявок V.5.1 - 22/12/2012 + NEW_LOG";

		// функция парсинга тимы
		function fteam ( $team ) {
			$team = explode(";",$team);
			unset($team[count($team)-1]);
			return $team;
		}


		// функция получения списка заявок - переделана
		function getlist ($razdel = 1, $level = null, $id = null )
		{

			if($level == 0) {$level = null;}

			if ($razdel==3) { $dgv="  (am1>0 or am2>0 or ae1>0 or ae2>0)  "; } else { $dgv=" (am1=0 and am2=0 and ae1=0 and ae2=0) "; }

			$fict = mysql_query("SELECT * FROM `zayavka` WHERE  ".$dgv." AND   ".
				(( $level != null )? " ((`t1min` <= '{$level}' OR `t1min` = '99') AND (`t1max` >= '{$level}' OR `t1max` = '99') ".(($razdel==4)?"OR ((`t2min` <= '{$level}' OR `t2min` = '99') AND (`t2max` >= '{$level}' OR `t2max` = '99'))":"").") AND " : "" ).
				" `level` = {$razdel} ".
				(( $id != null )? " AND `id` = {$id} " : "")
				." ORDER by `id` DESC;" );



		if (mysql_num_rows($fict))
		  {
			while ( $row = mysql_fetch_array($fict) )
			{
			$zay[$row['id']] = $row;
			}
			return $zay;
		  }
		   else return false;
		}



		// добавление в тиму перса - переписанна
		function addteam ( $team = 1, $telo, $telo_eff, $zay , $zid)
			{
			global $OKADD,$user; //для статуса в физах
			//$zay -масив
			if ($zay[start]<=time())
					{
						return "Вы не успели...";
					}

			if ($zay['subtype']==1)
			{
				if ($telo['weap']>0)
				{
				$chek_elka=mysql_fetch_array(mysql_query("select id,name from oldbk.inventory where  id='{$telo['weap']}'   ; "));

					if (strpos($chek_elka['name'], 'овогодняя ёлка') === false)
					{
						return "У Вас не одета елка....";
					}
				}
				else
				{
				return "У Вас не одета елка....";
				}
			}
			elseif ($zay['subtype']==2)
				{
					if ($telo['weap']>0)
					{
					$chek_elka=mysql_fetch_array(mysql_query("select id,name,prototype from oldbk.inventory where  id='{$telo['weap']}'   ; "));

						if (!( (($chek_elka['prototype'] >=410130 ) and ($chek_elka['prototype'] <= 410136 )) || (($chek_elka['prototype'] >=410001 ) and ($chek_elka['prototype'] <= 410008 )) || (($chek_elka['prototype'] >=410021 ) and ($chek_elka['prototype'] <= 410028 ))  ))
						{
							return "У Вас не одет Букет...";
						}
					}
					else
					{
							return "У Вас не одет Букет...";
					}
				}
			elseif ($zay['subtype']==3)
			{

				if ($telo['uclass']==0)
				{
					return "Бои доступны для персонажей установивших класс персонажа, сходите к знахарю...";
				}
				elseif (!(	($telo['sergi'] >0) AND
						    ($telo['kulon'] >0) AND
						    ($telo['weap'] >0) AND
						    ($telo['bron']  >0) AND
						    ($telo['r1'] >0) AND
						    ($telo['r2'] >0) AND
						    ($telo['r3']>0) AND
						    ($telo['helm']>0) AND
						    ($telo['perchi']>0)  AND
						    ($telo['shit'] > 0) AND
						    ($telo['boots']>0) AND
						    ($telo['nakidka']>0) AND
						    ($telo['rubashka']>0) ) )
				{
					return "Бои доступны в полном комплекте вещей...";
				}

			}
			elseif ($zay['coment'] =='<b>#zlevels</b>' )
			{


				if ($telo['weap']>0)
				{
					if ($telo['level']>7)
					{
					$chek_elka=mysql_fetch_array(mysql_query("select id,name,prototype, nlevel, otdel from oldbk.inventory where  id='{$telo['weap']}'   ; "));

						if ( (($chek_elka['prototype'] >=55510301 ) AND ($chek_elka['prototype'] <=55510352) ) OR ($chek_elka['prototype'] ==1006233 ) OR ($chek_elka['prototype'] ==1006232 ) OR ($chek_elka['prototype'] ==1006234 ) OR (($chek_elka['prototype'] >=410130  ) AND ($chek_elka['prototype'] <=410136) ) OR (($chek_elka['prototype'] >=410001  ) AND ($chek_elka['prototype'] <=410008) ) OR (($chek_elka['prototype'] >=410021  ) AND ($chek_elka['prototype'] <=410028) ) )
						{
						//елкам и оружию хаоса разрешаем

						}
						else
						if  ( (($chek_elka['nlevel']<$telo['level']) and ($telo['level']<14)) OR  (($chek_elka['nlevel']<($telo['level']-1)) and ($telo['level']>13)) )
						{
							return "Нельзя зайти с оружием ниже своего уровня!";
						}
					}
				}
				else
				{
						return "У Вас нет оружия!";
				}
			}


			if (($telo_eff['owntravma'])>=1) //больше легкой травмы
			{
					if (($telo_eff[12]>0)  && ($zay[type]!=4 AND $zay[type]!=5))
						{
						return "У вас средняя травма, поединки с оружием слишком тяжелы для вас...";
						}
					else if ($telo_eff[13]>0 )
						{
						return "У вас тяжелая травма, вы не сможете драться...";
						}
					else if ($telo_eff[14]>0 )
						{
						return "У вас тяжелая травма, вы не сможете драться...";
						}
					else if ( ($telo_eff['owntravma'])>1)
						{
						return "У вас тяжелая травма, вы не сможете драться...";
						}
			}



			if  ($zay[nomagic]>0)
				{
				//проверка кастов
				$booksa=mysql_fetch_assoc(mysql_query("select * from effects where owner='{$telo[id]}' AND type IN (791,792,793,794,795) LIMIT 1;"));
					 if ($booksa[id]>0)
					 {
					 return "Эта заявка не может быть принята вами. Бой без магии - нельзя зайти с использованными книгами.";
					 }

				if (($telo['hidden']>0)AND($telo['hiddenlog']==''))
					 {
					 return "Эта заявка не может быть принята под заклятием невидимости. Бой без магии!";
					 }
				elseif (($telo['hidden']>0)AND($telo['hiddenlog']!=''))
					 {
					 return "Эта заявка не может быть принята под заклятием перевоплощение. Бой без магии!";
					 }
				}


			if ($telo['hp'] < $telo['maxhp']*0.33)
			{
				return "Вы слишком ослаблены для боя, восстановитесь.";
			}

			if ( ustatus($telo) != 0) { return "Эта заявка не может быть принята вами.(2)"; }

			if ($zay['level']==7)
			{
			//нет капчи для боев классов

			}
			else
			// Captcha
			if (($zay['type'] == 3 || $zay['type'] == 5) AND ($telo[prem]!=3) )
			{

			$load_captime=mysql_fetch_array(mysql_query("select UNIX_TIMESTAMP(captime) as captime from users_capcha_time where owner='{$telo['id']}' "));
			if (time()-$load_captime['captime'] < 3600) //1 ч.
			{
				unset($_SESSION['securityCode']);
			}
			else
				{
				if ((!isset($_POST['securityCode1']) && !isset($_POST['securityCode2'])) || !isset($_SESSION['securityCode']) || (!strlen($_POST['securityCode1']) && !strlen($_POST['securityCode2']))) return "Вы не ввели защитный код!";
				$code = (isset($_POST['securityCode1']) && strlen($_POST['securityCode1'])) ? $_POST['securityCode1'] : $_POST['securityCode2'];
				if ($code !== $_SESSION['securityCode']) {
					unset($_SESSION['securityCode']);
					return "Неверный защитный код!";
				}
				unset($_SESSION['securityCode']);
				//после ввода капчи фиксируем время ввода
				mysql_query("INSERT INTO `oldbk`.`users_capcha_time` SET `owner`='{$telo['id']}', captime=NOW() ON DUPLICATE KEY UPDATE captime=NOW();");
				}

			}


			//футбол
		if ($zay['type'] == 20)
				{
		//check dressed items
					$check1=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where owner='{$telo[id]}' and dressed=1 and magic!=51; "));
					$check2=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where owner='{$telo[id]}' and dressed=1 and prototype>1000 and prototype<1045 and includemagic=0;"));
					if (($check1[0]!= $check2[0]) or ($check2[0]!=4))
						{
							return "Вы одеты не по форме...(1)";
						}
				}

			$rr='';
			if ($team == 1) { $teamz = 2; } else { $teamz = 1; }

			if (($zay['type'] == 2 ) and ($zay['level'] == 4 ))
			{

			//проверки тренировочных боев

				//1. проверка по склонкам если она есть
				 if ($zay['alig'.$team] >1)
						{

						//если стоит склонка
						$ual=(int)($telo['align']);
						if ($ual==1) $ual=4; // свет
						if ($ual==6) $ual=4; // свет
						//2=нейт,3-тьма 4- свет
						if ($zay['alig'.$team]!=$ual) 	return "Эта заявка не может быть принята вами. Неподходящая склонность.";
						}
				//2. проверка по кланам если она есть
						$my_klan_id=0;
						if ($telo[klan]!='')
						{
						$my_klan=mysql_fetch_array(mysql_query('select * from oldbk.clans where short = "'.$telo[klan].'";'));
						$my_klan_id=$my_klan['id'];
						}

					 if  (($zay['klan'.$team] >0) OR ($zay['reklan'.$team] >0) )
					 	{

					 	 if  ( ($zay['klan'.$team] !=$my_klan_id) and ($zay['reklan'.$team] !=$my_klan_id and ($my_klan_id >0)  ) ) return "Эта заявка не может быть принята вами. Не тот клан.";
					 	 if  ( ($my_klan_id==0) and ($zay['klan'.$team] >0)) return "Эта заявка не может быть принята вами. У Вас нет клана!";

					 	}

				//3. проверка по уровням
				//проверяется ниже


			}
			else
			if ($zay['type'] == 3 OR $zay['type'] == 5)
			{

			}
			else
			{


				if ($telo[klan]!='')
				{
				$my_klan=mysql_fetch_array(mysql_query('select * from oldbk.clans where short = "'.$telo[klan].'";'));
				if ($my_klan !== FALSE)
				{
					$my_klan_array=array('"'.$telo[klan].'"');

					if ($my_klan[base_klan]>0)
					{
					//у клана есть основа
						$my_klan_add=mysql_fetch_array(mysql_query('select * from oldbk.clans where id = "'.$my_klan[base_klan].'";'));
						if ($my_klan_add[short]!='')
						 {
							$my_klan_array[]='"'.$my_klan_add[short].'"';
						}
					}
					else
					if ($my_klan[rekrut_klan]>0)
					{
					//у клана есть рекруты
						$my_klan_add=mysql_fetch_array(mysql_query('select * from oldbk.clans where short = "'.$my_klan[rekrut_klan].'";'));
						if ($my_klan_add[short]!='')
						{
						$my_klan_array[]='"'.$my_klan_add[short].'"';
						}
					}


					$get_test_klns = mysql_fetch_array(mysql_query("select * from users where zayavka='{$zay[id]}' and battle_t='{$teamz}' and klan in (".implode(",",$my_klan_array).")  LIMIT 1;"));

					if($get_test_klns)
					{
					//если есть хоть один из клана или основы или рекрута - против
					return "Чтите честь ваших сокланов.";
					}
				}
				}


			 if ( (($zay['am1']!=0) OR ($zay['am2']!=0) or ($zay['ae1']!=0) or ($zay['ae1']!=0)) AND ((int)($telo[align])>0)  )
			    {
			    //бои склонностей и у тела есть склонка
			    $telo_align=(int)($telo[align]);
			    if  ($telo_align==1) { $telo_align=6;  }; //палы = свет

			    if ($team==1)
			    	{
			    	   if (($telo_align!=$zay['am1']) and ($zay['am1']!=0)) { return "Эта заявка не может быть принята вами."; }
			    	   if (($telo_align!=$zay['am2']) and ($zay['am2']!=0)) { return "Эта заявка не может быть принята вами."; }
			    	}
			    	elseif ($team==2)
			    	{
			    	   if (($telo_align!=$zay['ae1']) and ($zay['ae1']!=0)) { return "Эта заявка не может быть принята вами."; }
			    	   if (($telo_align!=$zay['ae2']) and ($zay['ae2']!=0)) { return "Эта заявка не может быть принята вами."; }
			    	}
			   }


			} // fin by type

		$t_arr=fteam($zay['team'.$team]);

			if($zay['t'.$team.'min'] == 99)
				{
				// клановые заявки
				if ($telo[klan]!='')
					{
					if ($t_arr[0]!='') //первый чел есть
					  {
						//получаем название клана - тела которое первое в команде - куда тело хочет зайти
						$toper = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id`='{$t_arr[0]}' LIMIT 1;"));
						if($toper['klan']!='')
						{
						if($user['klan']!=$toper['klan']) { return "Эта заявка не может быть принята вами!"; }
						}
						else
						{
						return "Эта заявка не может быть принята вами!";
						}
					  }

					}
					else
					{
					return "Эта заявка не может быть принята вами. Вы не в клане!3";
					}
				}
				else
				{
					if ($telo['level'] > 0  &&!($zay['t'.$team.'min'] <= $telo['level'] && $zay['t'.$team.'max'] >= $telo['level'])) { return "Эта заявка не может быть принята вами. Уровень не тот!"; }
				}

			if (($zay['type']==3) and ($zay['level']!=7))
				{
				//для хаостов
				if ( $zay['zcount'] >= $zay['t1c'] ) { return "Группа уже набрана."; }
				}
				else
				{
				if ( count($t_arr) >= $zay['t'.$team.'c'] ) { return "Группа уже набрана."; }
				}

			// money
			if($zay['price']>0)
			{

			   if($telo[hidden] > 0)
			   		{
					return "В магии Иллюзии нельзя принять заявку на деньги...";
					}
					else
					{
						if($zay['price']>$telo[money])
						{
						return "У вас недостаточно кредитов для принятия этой заявки.";
						}
					}

				$sql_money=" `users`.money=`users`.money-{$zay['price']} ,   ";
				$sql_money_a=" and  `users`.money >={$zay['price']}  ";

			}
			else
			{
				$sql_money="";
				$sql_money_a="";
			}

			mysql_query("UPDATE `users`, `zayavka` SET
							`users`.battle_t={$team},
							`users`.zayavka = {$zid}, ".$sql_money."
							`zayavka`.team{$team}= CONCAT(`zayavka`.team{$team},'".$telo[id].";'),
							`zayavka`.t{$team}hist=CONCAT(`zayavka`.t{$team}hist,'".BNewHist($telo)."'),
							`zayavka`.zcount=`zayavka`.zcount+1,
							`zayavka`.fond=`zayavka`.fond+".$zay['price']."
						WHERE `users`.id = {$telo[id]}  AND `zayavka`.id = {$zay[id]} AND `users`.battle=0 AND  `users`.zayavka=0 ".$sql_money_a." ");
			if (mysql_affected_rows()>0)
					{
						$ret = "Вы приняли заявку на бой";
						if($zay['price']>0)
						{
						  $ret .= ", и заплатили {$zay['price']} кр.";

		  		    			$rec['owner']=$telo[id];
							$rec['owner_login']=$telo['login'];
							$rec['owner_balans_do']=$telo['money'];
							$rec['owner_balans_posle']=$telo['money']-$zay['price'];
							$rec['target']=0; $rec['target_login']='';
							$rec['type']=6;
							$rec['sum_kr']=$zay['price'];
							$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
							$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0; $rec['item_dur']=0; $rec['item_maxdur']=0;
							$rec['item_ups']=0; $rec['item_unic']=0; $rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
							add_to_new_delo($rec); //юзеру
						}
					$OKADD=true;
					$user[zayavka]=$zid;
					$user[battle_t]=$team;
					return $ret;
					}
					else
					{
						return "Эта заявка не может быть принята вами!";
					}
		}

		// отозвать запрос ;) - переделаная
		function delteam ( $team = 2,$telo)
		{
		global $OKBB;

			mysql_query("UPDATE  `zayavka` SET team{$team} ='' ,  t{$team}hist =''   WHERE	id = {$telo[zayavka]} and team{$team}='{$telo[id]};' LIMIT 1;");
			if(mysql_affected_rows()>0)
				{
				mysql_query("UPDATE `users` SET zayavka = '0' , battle_t=0  WHERE  id = {$telo[id]} and  `zayavka` = {$telo[zayavka]} and battle_t={$team} ;");
				if(mysql_affected_rows()>0)
					{
					$OKBB=true;

					return "Вы отозвали запрос";
					}
					else
					{
					return "Ошибка 18!";
					}
				}
				else
				{
				return "Ошибка 19!";
				}

		}

		// подача заявки - переделанная
		function addzayavka ( $start = 10, $timeout = 3, $t1c, $t2c, $type, $t1min, $t2min, $t1max, $t2max, $coment, $telo, $telo_eff , $level = 1, $stavka, $blood=0, $price=0, $nomagic=0 ,$autob=0, $am1=0, $am2=0, $ae1=0, $ae2=0,$in_att=0,$hrandom=0,$klan1=0,$reklan1=0,$klan2=0,$reklan2=0,$alig1=0,$alig2=0)
		{
		global $user;
		$timeout = 3; //всегда и везде
		 $subtype=0;


			if ( $level ==10)
				{
				//бои на елках
				 $level=5; // возвращаем  закладку хаота;
				 $subtype=1; //  бои на елках
				 $type=3;// не кулак
				}
			elseif ( $level ==11)
				{
				//бои на елках
				 $level=5; // возвращаем  закладку хаота;
				 $subtype=2; //  бои на букетах
				 $type=3;// не кулак
				}
			elseif ( $level ==12)
				{
				//бои  классов
				 $level=5; // возвращаем  закладку хаота;
				 $subtype=3; //  бои  классов
				 $type=3;// не кулак
				}


			if ( ($level==7)  ) //and (($user['klan']=='radminion') OR ($user['klan']=='testTest') )
			 {
			 //разрешаем пока только тестам
			 }
			elseif ((int)$level<1 || (int)$level>5) return "Ошибка...";

			if ($level==7)
			{
			//нет капчи для боев классов

			}
			elseif (($type == 3 || $type == 5) and ($telo[prem]!=3))
			{
			// Captcha
			$load_captime=mysql_fetch_array(mysql_query("select UNIX_TIMESTAMP(captime) as captime from users_capcha_time where owner='{$telo['id']}' "));
			if (time()-$load_captime['captime'] < 3600) //6 ч.
			{
				unset($_SESSION['securityCode']);
			}
			else
				{
				if (!isset($_POST['securityCode']) || !isset($_SESSION['securityCode']) || !strlen($_POST['securityCode'])) return "Вы не ввели защитный код!";
				if ($_POST['securityCode'] != $_SESSION['securityCode']) {
					unset($_SESSION['securityCode']);
					return "Неверный защитный код!";
				}
				unset($_SESSION['securityCode']);
				//после ввода капчи фиксируем время ввода
				mysql_query("INSERT INTO `oldbk`.`users_capcha_time` SET `owner`='{$telo['id']}', captime=NOW() ON DUPLICATE KEY UPDATE captime=NOW();");
				}
			}

		if ($subtype==1)
		{
			if ($telo['weap']>0)
			{
			$chek_elka=mysql_fetch_array(mysql_query("select id,name from oldbk.inventory where  id='{$telo['weap']}'   ; "));

				if (strpos($chek_elka['name'], 'овогодняя ёлка') === false)
				{
					return "У Вас не одета елка....3";
				}
			}
			else
			{
			return "У Вас не одета елка....4";
			}
		}
		elseif ($subtype==2)
		{
			if ($telo['weap']>0)
			{
			$chek_elka=mysql_fetch_array(mysql_query("select id,name,prototype from oldbk.inventory where  id='{$telo['weap']}'   ; "));

				if (!( (($chek_elka['prototype'] >=410130 ) and ($chek_elka['prototype'] <= 410136 )) || (($chek_elka['prototype'] >=410001 ) and ($chek_elka['prototype'] <= 410008 )) || (($chek_elka['prototype'] >=410021 ) and ($chek_elka['prototype'] <= 410028 )) ))
				{
					return "У Вас не одет Букет...";
				}
			}
			else
			{
					return "У Вас не одет Букет...";
			}
		}
		elseif( $subtype==3)
		{

				if ($telo['uclass']==0)
				{
					return "Бои доступны для персонажей установивших класс персонажа, сходите к знахарю...";
				}
				elseif (!(	($telo['sergi'] >0) AND
						    ($telo['kulon'] >0) AND
						    ($telo['weap'] >0) AND
						    ($telo['bron']  >0) AND
						    ($telo['r1'] >0) AND
						    ($telo['r2'] >0) AND
						    ($telo['r3']>0) AND
						    ($telo['helm']>0) AND
						    ($telo['perchi']>0)  AND
						    ($telo['shit'] > 0) AND
						    ($telo['boots']>0) AND
						    ($telo['nakidka']>0) AND
						    ($telo['rubashka']>0) ) )
				{
					return "Бои доступны в полном комплекте вещей...";
				}
		}
		elseif ($type!=20)
		{
			if ($level==1 && ($type!=1 && $type!=4)) $type=1;
			if ($level==2 && ($type!=1 && $type!=4 && $type!=6)) $type=1;
			if ($level==4 && ($type!=2 && $type!=4)) $type=2;
			if ($level==5 && ($type!=3 && $type!=5)) $type=3;
			if ($level==3 && ($type==2)) $type=2; //тип боя!!
		}
		else
		{
		//check dressed items
		$check1=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where owner='{$telo[id]}' and `dressed`='1' and `magic`!='51'; "));
		$check2=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where owner='{$telo[id]}' and `dressed`='1' and `prototype`>1000 and `prototype`<1045 and `includemagic`='0';"));
		if (($check1[0]!= $check2[0]) or ($check2[0]!=4))
				{
				return "Вы одеты не по форме....";
				}
		}

		if ( $start == 5 OR $start == 10 OR $start == 15 ) //OR $start == 30 OR $start == 45 OR $start == 60
		{
		} else { $start = 10; }

		if( $timeout == 3 OR  $timeout == 4 OR $timeout == 5 OR  $timeout == 7 OR  $timeout == 10)
		{
		} else { $timeout = 3; }


			if (($telo_eff['owntravma'])>=1) //больше легкой травмы
			{
					if (($telo_eff[12]>0) && ($type!=4 AND $type!=5))
						{
						return "У вас средняя травма, поединки с оружием слишком тяжелы для вас...";
						}
					else if ($telo_eff[13]>0 )
						{
						return "У вас тяжелая травма, вы не сможете драться...";
						}
					else if ($telo_eff[14]>0 )
						{
						return "У вас тяжелая травма, вы не сможете драться...";
						}
					else if ( ($telo_eff['owntravma'])>1)
						{
						return "У вас тяжелая травма, вы не сможете драться...";
						}
			}

			if  ($nomagic>0)
				{
				//проверка кастов
				$booksa=mysql_fetch_assoc(mysql_query("select * from effects where owner='{$telo[id]}' AND type IN (791,792,793,794,795) LIMIT 1;"));
					 if ($booksa[id]>0)
					 {
					 return "Бой без магии - нельзя зайти с использованными книгами.";
					 }

				if (($telo['hidden']>0)AND($telo['hiddenlog']==''))
					 {
					 return "Вы не можете создать эту заявку под заклятием невидимости. Бой без магии!";
					 }
				elseif (($telo['hidden']>0)AND($telo['hiddenlog']!=''))
					 {
					 return "Вы не можете создать эту заявку под заклятием перевоплощение. Бой без магии!";
					 }

				}



			if ($level!=3)
			{
				if (!$telo['klan'] && $t1min == 99)
				{
				return "Вы не состоите в клане.";
				}
			}
			// хп
			if ($telo['hp'] < $telo['maxhp']*0.33)
			{
				return "Вы слишком ослаблены для боя, восстановитесь.";
			}


			$price = round($price,0);
			if ($price>0)
				{
				if($price>$telo[money]) {return "У вас нехватает денег для подачи заявки!";}
				$fond = $price;
				$stavka = round($stavka,2);
				$price_out = ", money=money-{$price}";
				$price_text=", и заплатили {$price} кр.";
				}
				else
				{
				$fond = 0;
				$price=0;
				}

				$rsql='';
				$rsqlv='';
				//тренеровочные
				if ($type==2)
					{
					$rsql=" , `klan1`, `klan2` , `reklan1` , `reklan2` , `alig1` , `alig2` ";
					$rsqlv=" , '{$klan1}', '{$klan2}' , '{$reklan1}' , '{$reklan2}' , '{$alig1}' ,  '{$alig2}' ";
					}


			$start = time()+$start*60;
			mysql_query("INSERT INTO `zayavka`
				(`bcl` , `nomagic`,`price`,`fond`,`start`, `timeout`, `t1c`, `t2c`, `type`, `level`, `coment`, `team1`, `stavka`, `t1min`, `t2min`, `t1max`, `t2max`,`podan`,`blood`,`autoblow`, `am1` , `am2` , `ae1` , `ae2`, `t1hist`,`subtype`, `zcount` , `hz` ".$rsql." ) values
				('{$in_att}','{$nomagic}','{$price}','{$fond}','{$start}','{$timeout}','{$t1c}','{$t2c}','{$type}','{$level}','".mysql_real_escape_string($coment)."','{$telo[id]};','{$stavka}','{$t1min}', '{$t2min}', '{$t1max}', '{$t2max}', '".date("H:i")."', '{$blood}','{$autob}', '{$am1}', '{$am2}' , '{$ae1}' , '{$ae2}' , '".BNewHist($telo)."' , '{$subtype}' , '1', '{$hrandom}' ".$rsqlv." );");

			$NEW_ID_Z=mysql_insert_id();
			if (mysql_affected_rows()>0)
				{
					mysql_query("UPDATE `users` SET  battle_t=1 , `zayavka` = ".$NEW_ID_Z." ".$price_out." WHERE `id` = {$telo[id]};");

					$user[zayavka]=$NEW_ID_Z;
					$user[battle_t]=1;

					if($price>0)
						{
							$rec['owner']=$telo[id];
							$rec['owner_login']=$telo['login'];
							$rec['owner_balans_do']=$telo['money'];
							$rec['owner_balans_posle']=$telo['money']-$price;
							$rec['target']=0; $rec['target_login']='';
							$rec['type']=6;
							$rec['sum_kr']=$price;
							$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
							$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0; $rec['item_dur']=0; $rec['item_maxdur']=0;
							$rec['item_ups']=0; $rec['item_unic']=0; $rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
							add_to_new_delo($rec); //юзеру

						}
				return "Вы подали заявку на бой".$price_text;
				}
				else
				{
				return "Ошибка создания заявки!";
				}
		}

		// отзыв заявки - переписанная
		function delzayavka ()
		{
		global $OKD, $user;
		//проверить чтоб заявке отзывающий был только один был со стороны создающего
		 if (($user[zayavka]>0)	and ($user[battle_t]==1))
		 	{
		 	$get_test_zay=mysql_fetch_array(mysql_query("SELECT * from zayavka where id='{$user[zayavka]}' and team1='{$user[id]};' and team2='' LIMIT 1;"));
				if ($get_test_zay[id]>0)
				{
					mysql_query("DELETE FROM `zayavka` WHERE id='{$user[zayavka]}' and team1='{$user[id]};' and team2='' ");
					if (mysql_affected_rows()>0)
						{
						mysql_query("UPDATE `users` SET `zayavka` = 0, battle_t=0  WHERE `id` = {$user[id]} LIMIT 1;");
						$user[zayavka]=0;
						$user[battle_t]=0;
						$OKD=ture;
						return 'Вы отозвали заявку.';
						}
						else
						{
						return 'Вы не можете отозвать эту заявку.';
						}
				}
				else
				{
				return 'Вы не можете отозвать эту заявку.';
				}
			}
			else
			{
				return 'Вы не можете отозвать эту заявку.';
			}
		}

		//показываем физы аля от этих ботов
		function showsbots($botsids)
		{

					global $user, $time_to_bot;

		$out='';

			foreach($botsids as $k=>$v)
			{
			if (time()-$_SESSION['bottout'][$k]>$time_to_bot)
				{
				$kk+=120;
				$out.="<INPUT TYPE=radio ".($user[zayavka]>0?"disabled ":"")." NAME='gocombat' value={$k}><font class=date>".date("H:i",time()-(200+$kk))."</font> ";
				$out.= BNewRender($v);
				$out.= "&nbsp; тип боя: ";
				$out.= "<IMG SRC=\"http://i.oldbk.com/i/fighttype1.gif\" WIDTH=20 HEIGHT=20 ALT=\"физический бой\"> ";
				$out.= " (таймаут 3 мин.) <BR>";
				}
			}

		return $out;
		}

		// показать физическую заявку -переделано
		function showfiz ( $row ) {
			global $user;
			$rr = "<INPUT TYPE=radio ".($user[zayavka]>0?"disabled ":"")." NAME='gocombat' value={$row['id']}><font class=date>{$row['podan']}</font> ";

			$rr .= BNewRender($row[t1hist]);

			if($row['team2'])
				{
				$rr .= " <i>против</i> ";
				$rr .= BNewRender($row[t2hist]);
				}

			$rr .= "&nbsp; тип боя: ";
			if ($row['type'] == 4) {
				$rr .= "<IMG SRC=\"http://i.oldbk.com/i/fighttype4.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"> ";
			}
			elseif ($row['type'] == 6) {
				$rr .= "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"кровавый бой\"> ";
			}
			elseif ($row['type'] == 1) {
				$rr .= "<IMG SRC=\"http://i.oldbk.com/i/fighttype1.gif\" WIDTH=20 HEIGHT=20 ALT=\"физический бой\"> ";
			}
			$rr .= " (таймаут {$row['timeout']} мин.) <BR>";
			return $rr;
		}

		// показать групповую заявку -переделано
		function showgroup ( $row )
		{
	            global $user;
			if ($row[level]==3)
				{
				$alish1="";$alish2="";
					if ($row[am1]>0) {$alish1.="<img src='http://i.oldbk.com/i/align_{$row[am1]}.gif'>";}
					if ($row[am2]>0) {$alish1.="<img src='http://i.oldbk.com/i/align_{$row[am2]}.gif'>";}
					if ($row[ae1]>0) {$alish2.="<img src='http://i.oldbk.com/i/align_{$row[ae1]}.gif'>";}
					if ($row[ae2]>0) {$alish2.="<img src='http://i.oldbk.com/i/align_{$row[ae2]}.gif'>";}
				}
				else
				{
				$alish1="";$alish2="";
				}

			if($row['t1min']==99)
			{
				$range1 = "<i>клан</i>";
			}
			else
			{
				$range1 = "{$row['t1min']}-{$row['t1max']}";
			}
			if($row['t2min']==99)
			{
				$range2 = "<i>клан</i>";
			}
			else
			{
				$range2 = "{$row['t2min']}-{$row['t2max']}";
			}
			$rr = "<INPUT TYPE=radio ".(($user[zayavka]>0)?"disabled ":"")." NAME=gocombat value={$row['id']}><font class=date>{$row['podan']}</font> <b>{$row['t1c']}</b>({$range1} {$alish1} ) (";

			if (count($row['team1']) ==0) { $rr.= "<i>группа не набрана</i>"; }
								else
								{
								$rr .= BNewRender($row[t1hist]);
								}
			$rr .= ") <i>против</i> <b>{$row['t2c']}</b>({$range2} {$alish2} )(";
			if (count($row['team2']) ==0) { $rr.= "<i>группа не набрана</i>"; }
									else
									{
									$rr .= BNewRender($row[t2hist],1);
									}

			if ($row['blood'] && $row['type'] == 5)
			{
				$rr .= "<IMG SRC=\"i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"\">";
			}

			$rr .= ")&nbsp; тип боя: ";
			if ($row['blood'] && $row['type'] == 4)
			{
				$rr .= "<IMG SRC=\"i/fighttype4.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"><IMG SRC=\"i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\">";
			}
			elseif ($row['blood'] && $row['type'] == 2) {
				$rr .= "<IMG SRC=\"i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\">";
			}
			elseif ($row['type'] == 2 ) {
				$rr .= "<IMG SRC=\"i/fighttype2.gif\" WIDTH=20 HEIGHT=20 ALT=\"групповой бой\">";
			}
			elseif ($row['type'] == 20 ) {
				$rr .= "<IMG SRC=\"i/fighttype20.gif\" WIDTH=20 HEIGHT=20 ALT=\"футбольный поединок\" title=\"футбольный поединок\">";
			}
			elseif ($row['type'] == 4) {
				$rr .= "<IMG SRC=\"i/fighttype4.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный групповой бой\">";
			}
			$rr .= "(таймаут {$row['timeout']} мин.) <span style='color:gray;'><i >бой начнется через ".round((($row['start'])-time())/60,1)." мин. ".(($row['coment'])?"(".$row['coment'].")":"")."</i></span>";

			if ($row['price'] >0 ) { $rr .= " (Бой на деньги:".$row['price']."кр.<u>текущий фонд:".$row['fond']."кр. </u>)"; }

		if ($user['klan']=='radminion')
			{
			$rr .= "<a href='?zid={$row['id']}&do=del'><img src='i/clear.gif' title='Отменить заявку'></a>";
			}

			if (($user['align']>1.4 && $user['align']<2) || ($user['align']>2 && $user['align']<3))
			{
				$rr .= "<a href='?zid={$row['id']}&do=clear'><img src='i/clear.gif' title='Удалить комментарий'></a><BR>";
			}
			else {
				$rr .= "<BR>";
			}

			return $rr;
		}

		// показать хаотическую заявку - переделано
	function showhaos ( $row ) {
            global $user;

          /*  if (($user['klan']!='radminion') and ($row['team1']=='') )
            	{
            	return ;
            	}
          */



			$rr = "<input type=hidden name='price{$row['id']}' id='price{$row['id']}' value='{$row['price']}'>
			<INPUT TYPE=radio ".(($user[zayavka]>0)?"disabled ":"")." NAME=gocombat id=gocombat value={$row['id']}><font class=date>{$row['podan']}</font> (";
			$all_in=0;
			$T1_array=fteam($row['team1']);
//			$all_in=count($T1_array);
			$all_in=$row['zcount'];

			if ($row['hide']==1)
				{
				$rr.='<i>Участники скрыты</i>';
				$zp=', ';
				}
				else
					if ($row['t1hist']!='')
							{
							$rr.=BNewRender($row['t1hist']);
							$zp='';
							}

			$rr .= "";
			if ($all_in ==0) { $rr.= $zp."<i>группа не набрана</i>"; }


			$rr .= ") ({$row['t1min']}-{$row['t1max']}) &nbsp; тип боя: ";
			if ($row['autoblow']==1)
					{
					$rr .= "<IMG SRC=\"i/achaos.gif\" WIDTH=20 HEIGHT=20 ALT=\"бой с автоударом\">";
					}

			if ($row['subtype']==1)
					{
					$rr .= "<IMG SRC=\"http://i.oldbk.com/i/fighttype7.gif\" WIDTH=20 HEIGHT=20 ALT=\"Бой на елках\" TITLE=\"Бой на елках\">";
					}
			elseif ($row['subtype']==2)
					{
					$rr .= "<IMG SRC=\"http://i.oldbk.com/i/fight_flowers.png\" WIDTH=20 HEIGHT=20 ALT=\"Бой на елках\" TITLE=\"Бой на букетах\">";
					}

			if ($row['blood'] && $row['type'] == 5) {
				$rr .= "<IMG SRC=\"i/fighttype5.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"><IMG SRC=\"i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\">";
			}
			elseif ($row['blood'] && $row['type'] == 3) {
				$rr .= "<IMG SRC=\"i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\">";
			}
			elseif ($row['type'] == 3) {
				$rr .= "<IMG SRC=\"i/fighttype3.gif\" WIDTH=20 HEIGHT=20 ALT=\"групповой бой\">";
			}
			elseif ($row['type'] == 5) {
				$rr .= "<IMG SRC=\"i/fighttype5.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный групповой бой\">";
			}


			if( ($all_in==1) && ($T1_array[0]==$user['id']) && ($row['coment'] !='<b>Великий Хаотический Бой!</b>') && ($row['coment'] !='<b>#zlevels</b>' ) && ($row['coment'] !='<b>#zbuket</b>' ) && ($row['coment'] !='<b>#zelka</b>' ) )
			{
				$rr .=' <a href="?haos_del='.$row['id'].'&level=haos">Отозвать заявку</a> ';
			}

			if ($row['coment'] =='<b>#zlevels</b>' ) { $row['coment'] ='<b>Автозаявка</b>'; $dd=true; }
			if ($row['coment'] =='<b>#zbuket</b>' ) { $row['coment'] ='<b>Автозаявка</b>'; $dd=true; }
			if ($row['coment'] =='<b>#zelka</b>' ) { $row['coment'] ='<b>Автозаявка</b>'; $dd=true; }
			if ($row['coment'] =='#zlevels' ) { $row['coment'] ='<b>Автозаявка</b>'; $dd=true; }
			if ($row['coment'] =='<b>Великий Хаотический Бой!</b>') {  $dd=true; }

			if ($row['subtype'] == 3) { $rr.=' <b>Бой классов</b> '; }

			$rr .= "(таймаут {$row['timeout']} мин.) <span style='color:gray;'><i>бой начнется через ".round((($row['start']+10)-time())/60,1)." мин. ".(($row['coment'])?"(".$row['coment'].")":"")."</i></span>";
			if (($user['align']>1.4 && $user['align']<2) || ($user['align']>2 && $user['align']<3))
			{
				if ($dd!=true)
				{
				$rr .= "<a href='?zid={$row['id']}&do=clear'><img src='i/clear.gif'></a>";
				}
			}
			if($row['price']>0) {
				if($row['nomagic'] == 1) {$nomagic = ", <u>без магии</u>";}
				$row['fond'] = round($row['fond']*0.9,2);
				$rr .= "<BR>Бой на деньги{$nomagic}: <b>{$row['price']}кр.</b> (текущий фонд: {$row['fond']}кр.)";
			}

			if($row['hz']>0)
			{
			$rr .=" <u>случайно</u> ";
			}




			$rr.=' (в заявке '.$row['zcount'].'/'.$row['t1c'].' чел.)';
			$rr .= "<BR><BR>";


			return $rr;
		}

		// user status - переделанная
		function ustatus ($telo)
		{
		if(($telo[zayavka]>0) and ($telo[battle_t]==1))	{ return 1; }
		elseif(($telo[zayavka]>0) and ($telo[battle_t]==2))	{ return 2; }
		elseif($telo[zayavka]==0) { return 0; }
		else { return -1; }
		}

		// стартовать бой!
		function battlestart ( $telo, $zay, $r)
		{
		$lvl_info='';


		if ($zay[id]>0)
		{
		//$telo - масив -запускающего бой - если это физы(начальные бои)- если в этой переменной не масив а CHAOS - товызов идет из файта для старта групповых заявой по времени
		//$zay - масив заявки одной которую надо запустить
		//$r - указатель раздела заявки
              	//if ($zay['coment'] =='#zlevels' ) { $row['coment'] ==''; }

		$T1_array=fteam($zay[team1]);
		$T2_array=fteam($zay[team2]);
		$time = time();

		$mk_satus=0;//поумолчанию

		if ($r==5) {$chaos_flag=1;} else {$chaos_flag=0;} //chos_flag

		if ($r==3)
				{
				// данные по боям склонок - записываем туда масив со склонками 1;2;3;4
				  $aligns_battle=$z['am1'].";".$z['am2'].";".$z['ae1'].";".$z['ae2'] ;
				} else { $aligns_battle=''; }

		if (($chaos_flag==1) and ($zay['autoblow']==1))
				{
				$chaos_flag=2;//CHAOS =2-хоат с авто ударами
				}

			// снимаем шмот, если кулачка
		if ($zay['type'] == 4 OR $zay['type'] == 5)
			{
				foreach($T1_array as $k=>$v)
				{
					undressall($v);
				}
				foreach($T2_array as $k=>$v)
				{
					undressall($v);
				}
			}

		if ($zay['subtype']==1)
			{
			$zay['type']=7; // ставим тип боя на елках 7
			//бой на елках
			//ищем укого нет елок в этой заявке
			$get_noelk=mysql_query("select id , login from users u where zayavka='{$zay['id']}'  and weap not in (select id from inventory where id=u.weap and name like '%овогодняя ёлка%'  ) ");
			if (mysql_num_rows($get_noelk))
				  {
					while ( $row = mysql_fetch_array($get_noelk) )
					{
					//разщдеваем у кого нет елки
					undressall($row['id']);
					}
				  }

			}
		elseif ($zay['subtype']==2)
			{
			$zay['type']=8; // ставим тип боя на букетах 8
			//ищем укого нет букета в этой заявке
			$get_noelk=mysql_query("select id , login from users u where zayavka='{$zay['id']}'  and weap not in (select id from inventory where id=u.weap and prototype in (410130,410131,410132,410133,410134,410135,410136,410001,410002,410003,410004,410005,410006,410007,410008,410021,410022,410023,410024,410025,410026,410027,410028)) ");
			if (mysql_num_rows($get_noelk))
				  {
					while ( $row = mysql_fetch_array($get_noelk) )
					{
					//разщдеваем у кого нет елки
					undressall($row['id']);
					}
				  }

			}
			elseif ($zay['subtype']==3)
			{
				$lvl_info=$zay['t1min'].'|'.$zay['t1max'];//уровни для записи в бой


					$get_noelk=mysql_query("select u.id, u.login, u.level, u.align,u.klan,u.room,u.id_city from users u where u.zayavka='{$zay['id']}' and  (`sergi`=0 OR `kulon`=0 OR `weap`=0 OR `bron`=0 OR `r1`= 0  OR `r2`=0 OR `r3`=0 OR `helm`=0 OR `perchi`=0 OR `shit` = 0 OR `boots`=0 OR `nakidka`=0 OR `rubashka`=0 ) ");

				if (mysql_num_rows($get_noelk))
					  {

						while ( $row = mysql_fetch_array($get_noelk) )
						{


							//раздеваем остальных
							undressall($row['id']);
						         //отправляем системку
						         addchp ('<font color=red>Внимание!</font> С вас было снято все обмундирование перед боем, потому что у вас был не полный комплет вещей!','{[]}'.$row['login'].'{[]}',$row['room'],$row['id_city']);

						}
					  }
			}
			elseif ($zay['coment'] =='<b>#zlevels</b>' )
			{
			$lvl_info=$zay['t1min'].'|'.$zay['t1max'];//уровни для записи в бой
			//ищем  кого раздеть
		 	//addchp ('<font color=red>Z1</font> , id: '.$zay['id'].'  ','{[]}Bred{[]}');
				$get_noelk=mysql_query("select u.id, u.login, u.level, u.align,u.klan,u.room,u.id_city, i.prototype from users u LEFT JOIN oldbk.inventory i ON  i.id=u.weap where u.zayavka='{$zay['id']}' and u.level>7 and (u.weap=0 or (((i.nlevel<u.level and u.level<14) or (i.nlevel<u.level-1 and u.level>13)) and (i.prototype not in (1006233,1006232,1006234,410130,410131,410132,410133,410134,410135,410136,410001,410002,410003,410004,410005,410006,410007,410008,410021,410022,410023,410024,410025,410026,410027,410028) and (i.prototype<55510301 or i.prototype>55510352)) ) )");

				if (mysql_num_rows($get_noelk))
					  {

						while ( $row = mysql_fetch_array($get_noelk) )
						{


							//раздеваем остальных
							undressall($row['id']);
						         //отправляем системку
						         addchp ('<font color=red>Внимание!</font> С вас было снято все обмундирование перед уровневым боем, потому что ваше оружие не соответствует вашему уровню!','{[]}'.$row['login'].'{[]}',$row['room'],$row['id_city']);
				  		 	//addchp ('<font color=red>undress in </font> zid: '.$zay['id'].' telo id '.$row['id'].'  ','{[]}Bred{[]}');

						}
					  }
					  else
					  {
		  		 	//addchp ('<font color=red>Z3 none</font> , id: '.$zay['id'].'  ','{[]}Bred{[]}');
					  }
			}
			elseif ($r==7) //бои классов
			{
			$lvl_info=$zay['t1min'].'|'.$zay['t1max'];//уровни для записи в бой
			$zay['type']=22; //  отключаем  23 - с опытом и репой делаем тип боя  бой классов
				//ищем овнеров  которые одели запрещенные шмотки
				/*
				$get_no_class_items=mysql_query("select * from inventory where owner in (select id from users where zayavka='{$zay['id']}') and dressed=1 and type not in (30,28,27,12) and nclass=0  group by owner");

				if (mysql_num_rows($get_no_class_items))
					  {
					  $unr_array=array(); // масив персов на раздевание

						while ( $its = mysql_fetch_array($get_no_class_items) )
						{
						//запоминаем предметы для снятия
						$unr_array[]=$its['owner'];
						}

						foreach($unr_array as $k=>$ownerid)
							{
								$ttelo=check_users_city_data($ownerid);
								if ($ttelo['id']>0)
									{
									undressall($ttelo['id']);
									 //отправляем системку
									addchp ('<font color=red>Внимание!</font> С вас было снято все обмундирование, потому что на вас были надеты предметы которые не имееют класса!','{[]}'.$ttelo['login'].'{[]}',$ttelo['room'],$ttelo['id_city']);
								        }


							}


					  }
				*/

			}



		if($zay['timeout'] != 3 AND $zay['timeout'] != 4 AND $zay['timeout'] != 5 AND $zay['timeout'] != 7 AND $zay['timeout'] != 10)  {  $zay['timeout'] = 3;	}



				if ($zay['subtype']==3)
				{
				$battle_in_deny='<b>Бой классов</b>';
				}
				else
				if ($zay['coment']=='<b>Великий Хаотический Бой!</b>')
				{
				$battle_in_deny='Хаотичный бой';
				}
				elseif (($zay['t1max']==$zay['t2max']) AND ($zay['t1max']<=4))
				{
				$battle_in_deny='Бой новичков';
				}
				elseif ($zay['bcl']>0)
				{
				$battle_in_deny='Хаотичный бой';
				}
				elseif ($zay['level']==6) //новый бой склонностей
				{
				$battle_in_deny=$zay['coment']; //закрываем вмешивание
				$chaos_flag = -1;
				}
				elseif (($zay['type'] == 2 ) and ($zay['level'] == 4 ))
				{
				$battle_in_deny='Тренировочный бой'; //закрываем вмешивание
				$chaos_flag = -1; // авто включаем
				$zay['type']=22; // делаем тип боя тренировочный
				}
				else
				{
				$battle_in_deny='';
				}

			include "config_ko.php";
			if (((time()>$KO_start_time46) and (time()<$KO_fin_time46)) )  // 'Неделя беспредела';
				{
				if ($battle_in_deny=='Хаотичный бой')
					{
					$battle_in_deny='';
					}
				}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ($r== 3 OR $r == 4 OR $r==5 OR $r==7 )
			{
				$haot_start_count=4;

					/*if  ( ( $zay['coment']='<b>#zbuket</b>') and ($zay['t1min']==14) and ($zay['t1max']==14) )
						{
						//http://tickets.oldbk.com/issue/oldbk-2586
						$haot_start_count=8;
						}
					*/


					 //отмена заявки групппы не набрались
					if  (
					 (((count($T2_array) !=$zay['t1c'] )or(count($T1_array) !=$zay['t1c'])) AND ($zay['type']==20)) //футбольная - заявка
					 OR ( (count($T1_array) < $haot_start_count) and ($r==5)  )    //заявка не набравшая 4х тел и хаот
					 OR ( (count($T2_array) ==0 ) and  $r!=5   ) ) // заявки у которых время вышло но нет воторой команды и заявка не (5) хаот

					{
					 mysql_query("DELETE FROM `zayavka` WHERE `id`= '".$zay[id]."';"); // удаляем заявку

					 if ((count($T1_array)>0) or (count($T2_array)>0 ))
					    {
						$price = $zay['price'];
						if($price>0) { $return_text = "Возвращено {$price} кр.";  $sql_money=" ,  money=money+{$price}  ";  } else { $sql_money=""; }


						foreach(array_merge($T1_array,$T2_array) as $v)
					   	{
						if($price > 0)
							{
						        $current_money = mysql_fetch_array(mysql_query("select * from users where id={$v} and zayavka={$zay[id]}"));
						        if ($current_money[id]>0)
							{
							$rec['owner']=$current_money[id];
							$rec['owner_login']=$current_money['login'];
							$rec['owner_balans_do']=$current_money['money'];
							$rec['owner_balans_posle']=$current_money['money']+$price;
							$rec['target']=0; $rec['target_login']='';
							$rec['type']=7;
							$rec['sum_kr']=$price;
							$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
							$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0; $rec['item_dur']=0; $rec['item_maxdur']=0;
							$rec['item_ups']=0; $rec['item_unic']=0; $rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
							add_to_new_delo($rec); //юзеру
							}
							}
						$mess_ids[]=$v;
					  	}

						 mysql_query("UPDATE `users` SET `zayavka`=0, battle_t=0 ".$sql_money."   WHERE `zayavka` = '".$zay[id]."';");

						 if ($zay['type']==20)
						 	{
							addch_group('<font color=red>Внимание!</font> Ваш бой не может начаться по причине "Группа не набрала '.$zay['t1c'].' человек". '.$return_text.'  ',$mess_ids);
							}
							else
							{
							addch_group('<font color=red>Внимание!</font> Ваш бой не может начаться по причине "Группа не набрана". '.$return_text.'  ',$mess_ids);
							}
					}

	      				return;
					}
			}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//готовим ид боя
			    $zay['price'] = round($zay['price'],0);
			    $zay['fond'] = round($zay['fond'],0);
				$zay['coment'] = mysql_real_escape_string($zay['coment']);
mysql_query("INSERT INTO `battle` ( `id`,`teams`,`damage`,`status_flag`,`nomagic`,`price`,`fond`,`coment`,`timeout`,`type`,`status`,`to1`,`to2`,`blood`,`CHAOS`,`exp` ) 	VALUES	( NULL,'{$battle_in_deny}','{$lvl_info}','{$mk_satus}','{$zay['nomagic']}','{$zay['price']}','{$zay['fond']}','{$zay['coment']}','{$zay['timeout']}','{$zay['type']}','1','".$time."','".$time."','".$zay['blood']."','".$chaos_flag."' , '".$aligns_battle."' )");
if (mysql_affected_rows()>0)
{
//бой создаля
$battle_id = mysql_insert_id();
	 mysql_query("DELETE FROM `zayavka` WHERE `id`= '".$zay[id]."';"); // удаляем заявку из базы!
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if (($r==5) and ( $zay['hz']==1))
		{
		//рандом распределение
		//addchp ('<font color=red>Внимание!</font>RANDOM HAOS start Zid:'.$zay['id'],'{[]}Bred{[]}',-1,-1);

				//новый механизм
				$get_all_gamer=mysql_query("select *  FROM users  WHERE zayavka='{$zay[id]}' and battle=0 ORDER by rand() DESC");

				//make masss
				$all_gamers_data=array();
				$cc=0;
				$co=0;
				$tt=1;

				while($gamer=mysql_fetch_array($get_all_gamer))
					{
						$cc++;
						$all_gamers_data[$gamer[id]]=$gamer;
					}

				$to_battle_id['team1'] = array();
				$to_battle_id['team2'] = array();
				$to_battle_login['team1'] = array();
				$to_battle_login['team2'] = array();
				$to_battle_data['team1'] = array();
				$to_battle_data['team2'] = array();
				$to_battle_hist['team1'] = array();
				$to_battle_hist['team2'] = array();
				$to_battle_var=array();

				foreach ($all_gamers_data as $id => $gamer)
				{
				$co++;

				             if ($tt==1)
				                {
       				                $tt=2;
				                $to_battle_id['team1'][]=$id; // ид чаров за команду
				                $to_battle_data['team1'][]=make_html_login_battle($all_gamers_data[$id]);  // html дата для лога
                				$to_battle_hist['team1'][]=BNewHist($all_gamers_data[$id]); // koд - для хистори в battle
						$to_battle_login['team1'][]=make_login_battle($all_gamers_data[$id]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$id}','{$time}','1','0')";	// данные для вставки в battle_vars
				                }
				                else
				                {
				                $tt=1;
						//ставим в команду два
						$to_battle_id['team2'][] =$id;
						$to_battle_data['team2'][]=make_html_login_battle($all_gamers_data[$id]);  // html дата для лога
                				$to_battle_hist['team2'][]=BNewHist($all_gamers_data[$id]); // koд - для хистори в battle
						$to_battle_login['team2'][]=make_login_battle($all_gamers_data[$id]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$id}','{$time}','1','0')";	// данные для вставки в battle_vars
				                }
				  }

				  if ($zay['coment']=='<b>Великий Хаотический Бой!</b>')
				  {
				  $mk_satus=10;
				  }
				  elseif ($zay['coment']=='<b>#zbuket</b>')
				  {
				  $mk_satus=10;
				  }
					elseif ($zay['coment']=='<b>#zelka</b>')
					{
					$mk_satus=10;
					}
				  else
				 if ( ($zay['type']==5) OR ($zay['type']==4))
				  {
				  //кулачки не могут быть статусными 12 /12 /2013
				  $mk_satus=0;
				  }
				  else  if (($co >=50) and (($zay['subtype']==1)OR($zay['subtype']==2)))
				  	{
				  	$mk_satus=10; // делаем великий хаот если бой на елках/букетах
				  	//$mk_satus=0; // не делаем
				  	}
				  	else  if ($co >=100)
				  	{
				  	$mk_satus=10; //делаем велик если больше 100 тел
				  	}
		}
		else
		// генерим ТИМС	 - если хаот - обычный-старый
		if ($r==5)
			{
				//новый механизм
				$get_all_gamer=mysql_query("select ((level*10000) + sila + lovk + inta + vinos + intel + mudra + stats + IFNULL((select (sum(mfkrit)) + sum(mfakrit) + sum(mfuvorot) + sum(mfauvorot) + (sum(bron1)*10) + (sum(bron2)*10) + (sum(bron3)*10) + (sum(bron4)*10) + (sum(cost)) + (sum(maxu)*10) + (sum(ecost)) FROM oldbk.inventory WHERE owner = users.id AND dressed=1 AND type!=12),0) ) as glsum , users.*  FROM users
				WHERE zayavka='{$zay[id]}' and battle=0 ORDER by glsum DESC;");

				//make masss
				$all_gamers=array();
				$all_gamers_data=array();
				$cc=0;
				while($gamer=mysql_fetch_array($get_all_gamer))
					{
						$cc++;
						$all_gamers[$gamer[id]]=$gamer[glsum];
						$all_gamers_data[$gamer[id]]=$gamer;
					}

				if (!(($cc/2) == round($cc/2))) { $cc--;} //если нечетное то от общего кол. -1;

				// haos grup sex ))
				$co=0; $tt=1; $lr=1; $all_sum1=0;$all_sum2=0;

				$to_battle_id['team1'] = array();
				$to_battle_id['team2'] = array();
				$to_battle_login['team1'] = array();
				$to_battle_login['team2'] = array();
				$to_battle_data['team1'] = array();
				$to_battle_data['team2'] = array();
				$to_battle_hist['team1'] = array();
				$to_battle_hist['team2'] = array();
				$to_battle_var=array();

				foreach ($all_gamers as $id => $glsum)
				{
				$co++;
				      if ($co<=$cc)
				          {
				          //не конец
				             if ($tt==1)
				                {
				                $to_battle_id['team1'][]=$id; // ид чаров за команду
				                $to_battle_data['team1'][]=make_html_login_battle($all_gamers_data[$id]);  // html дата для лога
                				$to_battle_hist['team1'][]=BNewHist($all_gamers_data[$id]); // koд - для хистори в battle
						$to_battle_login['team1'][]=make_login_battle($all_gamers_data[$id]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$id}','{$time}','1','0')";	// данные для вставки в battle_vars
     			                            $all_sum1+=$glsum;
			                         // ставим в первую тиму
			                          if ($lr==1)
			                            {
			                            //следуюущий
    				                    $tt=2;$lr=2;
    				                    }
    				                    else
    				                    {
			                            //следуюущий
    				                    $tt=1;$lr=1;
    				                    }
				                }
				                else
				                {
						//ставим в команду два
						$to_battle_id['team2'][] =$id;
						$to_battle_data['team2'][]=make_html_login_battle($all_gamers_data[$id]);  // html дата для лога
                				$to_battle_hist['team2'][]=BNewHist($all_gamers_data[$id]); // koд - для хистори в battle
						$to_battle_login['team2'][]=make_login_battle($all_gamers_data[$id]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$id}','{$time}','1','0')";	// данные для вставки в battle_vars

		      		                $all_sum2+=$glsum;
			                          if ($lr==1)
			                            {
			                            //следуюущий
    				                    $tt=1;$lr=2;
    				                    }
    				                    else
    				                    {
			                            //следуюущий
    				                    $tt=2;$lr=1;
    				                    }
				                }
				          }
				          else
				          {
				          // значит нечетное количество и последнего кидаем в слабую команду
				           if ($all_sum1 > $all_sum2)
				             {
						$to_battle_id['team2'][] =$id;
						$to_battle_data['team2'][]=make_html_login_battle($all_gamers_data[$id]);  // html дата для лога
                				$to_battle_hist['team2'][]=BNewHist($all_gamers_data[$id]); // koд - для хистори в battle
						$to_battle_login['team2'][]=make_login_battle($all_gamers_data[$id]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$id}','{$time}','1','0')";	// данные для вставки в battle_vars
				             }
				             else
				             {
						$to_battle_id['team1'][]=$id; // ид чаров за команду
				                $to_battle_data['team1'][]=make_html_login_battle($all_gamers_data[$id]);  // html дата для лога
                				$to_battle_hist['team1'][]=BNewHist($all_gamers_data[$id]); // koд - для хистори в battle
						$to_battle_login['team1'][]=make_login_battle($all_gamers_data[$id]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$id}','{$time}','1','0')";	// данные для вставки в battle_vars
				             }
				          }
				  }
				  // ставим статус для хаота если он больше 100 людей

				 if ( ($zay['type']==5) OR ($zay['type']==4))
				  {
				  //кулачки не могут быть статусными 12 /12 /2013
				  $mk_satus=0;
				  }
				  else  if (($co >=50) and (($zay['subtype']==1)OR($zay['subtype']==2)))
				  	{
				  	$mk_satus=10; // делаем великий хаот если бой на елках
				  	//$mk_satus=0; // не делаем великий хаот если бой на елках
				  	}
				  	else  if ($co >=100)
				  	{
				  	$mk_satus=10; //делаем велик если больше 100 тел
				  	}


			}
		else
				{
				///Подготовка данных для других типов боев
				// загрузка данных
				$to_battle_id['team1'] = array();
				$to_battle_id['team2'] = array();
				$to_battle_login['team1'] = array();
				$to_battle_login['team2'] = array();
				$to_battle_data['team1'] = array();
				$to_battle_data['team2'] = array();
				$to_battle_hist['team1'] = array();
				$to_battle_hist['team2'] = array();
				$to_battle_var=array();
				$cc=0;

				if ($zay['level']==6)
				{
				//новые бои склонок ДП
				$get_all_can=mysql_query("select *  FROM users	WHERE zayavka in (select id from zayavka_turn where zayid={$zay[id]}) and battle=0 "); //выбираем из очереди по ид реальной заявки
				}
				else
				{
				$get_all_can=mysql_query("select *  FROM users	WHERE zayavka={$zay[id]} and battle=0 ");
				}

				while($gamer=mysql_fetch_array($get_all_can))
					{
						$cc++;
						$all_gamers_data[$gamer[id]]=$gamer;
					}

				//Команда т1 - в порядке захода заявок
					foreach ($T1_array as $cid)
					{
					if (is_array($all_gamers_data[$cid]))
						{
						$to_battle_id['team1'][] = $cid;
						$to_battle_data['team1'][]=make_html_login_battle($all_gamers_data[$cid]);  // html дата для лога
               					$to_battle_hist['team1'][]=BNewHist($all_gamers_data[$cid]); // koд - для хистори в battle
						$to_battle_login['team1'][]=make_login_battle($all_gamers_data[$cid]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$cid}','{$time}','1','0')"; // данные для вставки в battle_vars
						}
					}

				//Команда т2 - в порядке захода заявок
					foreach ($T2_array as $cid)
					{
					if (is_array($all_gamers_data[$cid]))
						{
						$to_battle_id['team2'][] = $cid;
						$to_battle_data['team2'][]=make_html_login_battle($all_gamers_data[$cid]);  // html дата для лога
               					$to_battle_hist['team2'][]=BNewHist($all_gamers_data[$cid]); // koд - для хистори в battle
						$to_battle_login['team2'][]=make_login_battle($all_gamers_data[$cid]); // просто логины чистые
						$to_battle_var[]="('{$battle_id}','{$cid}','{$time}','1','0')";	// данные для вставки в battle_vars
						}
					}
				}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////// создаем логи и бой из имеющихся уже данных
		//////////вставляем сразу данные в battle_vars
		mysql_query("INSERT INTO battle_vars (battle,owner,update_time,type,napal) VALUES  ".implode(",",$to_battle_var)."  ");

///////////mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$id}','{$v}')");--устарело можно не кидать!?

		// создаем лог
		$rr = "<b>".implode(",",$to_battle_data['team1'])."</b> и <b>".implode(",",$to_battle_data['team2'])."</b>"; //для текста в лог боя
		$rrc="<b>".implode(",",$to_battle_login['team1'])."</b> и <b>".implode(",",$to_battle_login['team2'])."</b>"; //для текста в чат
		$hist1=implode("",$to_battle_hist['team1']);//собираем хистори для T1 для battle
		$hist2=implode("",$to_battle_hist['team2']);//собираем хистори для T2 для battle



		if ($zay['level']==6)
		{
		//новые бои склонок ДП
			//обновляем людей для команд
			if ((is_array($to_battle_id['team1'])) and (count($to_battle_id['team1'])>0 )  )
			{
			mysql_query("UPDATE users SET `battle` ={$battle_id},`zayavka`=0, `battle_t`=1,`last_battle`=0, `battle_fin`=0  WHERE `id` in (".implode(",",$to_battle_id['team1']).")");
			}

			if ((is_array($to_battle_id['team2'])) and (count($to_battle_id['team2'])>0 )  )
			{
			mysql_query("UPDATE users SET `battle` ={$battle_id},`zayavka`=0, `battle_t`=2,`last_battle`=0, `battle_fin`=0  WHERE `id` in (".implode(",",$to_battle_id['team2']).")");
			}

			//удаляем из очереди
			mysql_query("DELETE FROM `zayavka_turn` WHERE zayid='{$zay[id]}' ");

		}
		else
		{
			//обновляем людей для команд
			if ((is_array($to_battle_id['team1'])) and (count($to_battle_id['team1'])>0 )  )
			{
			mysql_query("UPDATE users SET `battle` ={$battle_id},`zayavka`=0, `battle_t`=1,`last_battle`=0, `battle_fin`=0  WHERE zayavka={$zay[id]} and `id` in (".implode(",",$to_battle_id['team1']).")");
			}

			if ((is_array($to_battle_id['team2'])) and (count($to_battle_id['team2'])>0 )  )
			{
			mysql_query("UPDATE users SET `battle` ={$battle_id},`zayavka`=0, `battle_t`=2,`last_battle`=0, `battle_fin`=0  WHERE zayavka={$zay[id]} and `id` in (".implode(",",$to_battle_id['team2']).")");
			}
		}

		//лог боя
		addlog($battle_id,"!:S:".time().":".$hist1.":".$hist2."\n");


		//обновление данных в бое и запуск - добавляем ид команд и их хистори, и делаем разлочку
		mysql_query("UPDATE battle SET `status_flag`={$mk_satus} , `status`=0, `t1`='".implode(";",$to_battle_id['team1'])."' , `t2`='".implode(";",$to_battle_id['team2'])."' , `t1hist` ='{$hist1}',`t2hist`='{$hist2}' where id={$battle_id} ;");

		//отправляем групповую системку
		addch_group('<font color=red>Внимание!</font> Ваш бой начался! <BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ', array_merge($to_battle_id['team1'],$to_battle_id['team2']));

		//системка
		if ($telo[room]) addch ("<a href=logs.php?log=".$battle_id." target=_blank>Поединок</a> между <B>".$rrc."</B> начался.   ",$telo['room'],CITY_ID);

	}
///=======================================================================================
 }
}

	function make_html_login_battle($telo)
	{
		if ( ($telo[hidden] > 0) and ($telo[hiddenlog] == '' ))
		{
		return  nick_hist($telo);
		}
		elseif ( ($telo[hidden] > 0) and ($telo[hiddenlog] != '' ))
		{
		$flogin=load_perevopl($telo);
		return nick_hist($flogin);
		}
		else
		{
		return  nick_hist($telo);
		}
	}

	function make_login_battle($telo)
	{
		if ( ($telo[hidden] > 0) and ($telo[hiddenlog] == '' ))
		{
		return  "<i>Невидимка</i>";
		}
		elseif ( ($telo[hidden] > 0) and ($telo[hiddenlog] != '' ))
		{
		$flogin=load_perevopl($telo);
		return $flogin[login];
		}
		else
		{
		return  $telo[login];
		}
	}

?>
