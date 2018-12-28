<?php
	function InsertBot($BOT,$BOT_items,$name) {
		mysql_query('INSERT INTO oldbk.`users_clons` SET 
			`login` = "'.$name.'",
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
			`bot` = 1,
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
			`bot_online` = 0, 
			`bot_room` = 0
		') or die();

		return mysql_insert_id();
		
	}

	if (!($_SESSION['uid'] >0)) {
		header("Location: index.php"); die();
	}

	if ($user['room'] < 50000 || $user['room'] > 53600) {
		echo "Тут это не работает...";
		return;
	}

	if ($user['battle'] > 0) {
		echo "Не в бою...";
		return;
	}

	$target = $_POST['target'];
	if (empty($target)) {
		echo "Введите имя персонажа для нападения.";
		return;
	}

	$q = mysql_query('SELECT * FROM oldbk.users WHERE login = "'.mysql_real_escape_string($target).'" AND id != '.$user['id']) or die();
	if (mysql_num_rows($q) != 1) {
		echo "Персонаж не найден.";
		return;
	}

	// жертва	
	$jert = mysql_fetch_assoc($q) or die();

	// травмы
	$owntravma = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$jert['id']." AND (type=13 OR type=12 OR  type=14);"));
	$owntravma_my = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$user['id']." AND (type=13 OR type=14);"));

	if ($owntravma_my) {
		echo "С Вашей травмой, нелья напасть!";  
		return;
	}

	if ($jert['level'] <= 4) { 
		echo "Грех обижать маленьких!";
		return;
	} elseif ($owntravma['id'] && !$jert['battle']) {
		echo "Персонаж тяжело травмирован...";
		return;
	} elseif ($jert['ldate'] < (time()-60)) {
		echo "Персонаж не в игре!";
		return;
	} elseif ($jert['hidden'] > 0) {
		echo "Персонаж не в игре!";
		return;
	} elseif ($user['room'] != $jert['room']) {
		echo "Персонаж далеко от вас!";
		return;
	} elseif ($jert['hp'] < $jert['maxhp']*0.33  && !$jert['battle']) {
		echo "Жертва слишком слаба!";
		return;
	} elseif ($jert['klan'] == "radminion" || $jert['klan'] == "Adminion" || $jert['id'] == 546433) {
		echo "Ой! На них нельзя!";
		return;
	} elseif ($user['id_grup'] == $jert['id_grup']) {
		echo "На своих нападать нельзя!";
		return;
	} elseif ($jert['hp'] < 1  && $jert['battle']>0) {
		echo "Вы не можете напасть на погибшего!";
		return;
	} elseif ($user['hp'] < $user['maxhp']*0.33) {
		echo "Вы слишком ослаблены для нападения!";
		return;
	}


	// всё ок, нападаем
	$q = mysql_query('START TRANSACTION') or die();
	$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$user['id_grup'].' OR id = '.$jert['id_grup'].' FOR UPDATE') or die();
	if (mysql_num_rows($q) != 2) {
		echo "Нападение не удалось!";
		$q = mysql_query('COMMIT') or die();
		return;
	} else {
		$p1 = mysql_fetch_assoc($q) or die();
		$p2 = mysql_fetch_assoc($q) or die();
		if ($p1['id'] == $user['id_grup']) {
			$megroup = $p1;
			$enemygroup = $p2;
		} else {
			$megroup = $p2;
			$enemygroup = $p1;
		}
	}

	if ($megroup['room'] != $enemygroup['room']) {
		echo "Эта группа далеко от вас!";
		$q = mysql_query('COMMIT') or die();
		return;
	}


	$enemyteam = $enemygroup['leader'].",".$enemygroup['team'];
	$meteam = $megroup['leader'].",".$megroup['team'];

	require_once('fsystem.php');

	if (strlen($enemyteam) && strlen($meteam)) {
		$enemyteam = substr($enemyteam,0,strlen($enemyteam)-1);
		$meteam = substr($meteam,0,strlen($meteam)-1);
		$allteam = $meteam.','.$enemyteam;

		if ($enemygroup['status'] == 2 && $jert['battle'] > 0) {
			// есть бой, присоединяемся
			// фиксим хп        
			mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` IN ('.$meteam.')') or die();
	
			// если у когото нет хелов - добавляем
			mysql_query('UPDATE oldbk.`users` SET `hp` = 2 WHERE `hp` = 0 AND `id` IN ('.$meteam.')') or die();
			
			// находим бой жертвы
			$q = mysql_query('SELECT * FROM oldbk.`battle` WHERE `id` = '.$jert['battle']) or die();
			$bd = mysql_fetch_assoc($q);
			if ($bd === FALSE) {
				echo "Нападение не удалось!";
				$q = mysql_query('COMMIT') or die();
				return;			
			}

			if ($bd['type'] != 13 && $bd['type'] != 14) {
				echo "В этот бой нельзя вмешаться!";
				$q = mysql_query('COMMIT') or die();
				return;			
			}

			$t1 = explode(";",$bd['t1']);

			// проставляем кто-где
			if ($jert['battle_t'] == 1) {
				$meteamb = 2;
				$enemyteamb = 1;
			} else {
				$meteamb = 1;
				$enemyteamb = 2;
			}
					
								$add_auto="";
								if (!(($bd['status_flag'] >0) OR ($bd['CHAOS']==2) OR ($bd['CHAOS']==-1) ) )
								{
								//если нет авто удара
									//проверяем кол. людей
								    		if  (users_in_battle($jert['battle']) >= 9) //входящий уже 10й
								    		{
								    		//в загороде все бои начинаются - с нап
											$add_auto=" CHAOS=-1 ,  ";
								    		}
								}

			// кешируем инфу
			$cache = array();
			$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.$meteam.')') or die();
			while($u = mysql_fetch_assoc($q)) {
				$cache[$u['id']] = $u;
			}

			// кешируем инфу для боя
			$t1 = explode(",",$meteam);
			$t1c = "";
			$t1b = "";

			while(list($k,$v) = each($t1)) {
				$t1c .= BNewHist($cache[$v]);
				$t2b .= nick_in_battle_hist($cache[$v],$meteamb);
			}

			$t1b = substr($t1b,0,strlen($t1b)-1);

			// добавляем себя в массив боя
			mysql_query('UPDATE oldbk.`battle` SET '.$add_auto.'  `t'.$meteamb.'` = CONCAT(`t'.$meteamb.'`,";'.str_replace(",",";",$meteam).'"), `t'.$meteamb.'hist`= CONCAT(`t'.$meteamb.'hist`,"'.mysql_real_escape_string($t1c).'")  ,`to'.$meteamb.'` = "'.time().'", `to'.$enemyteamb.'` = "'.(time()-1).'"  WHERE `id` = '.$jert['battle'].' and status=0 and win=3 and t1_dead=""') or die();

			if (mysql_affected_rows() > 0) {
				// выставляем себе номер боя
				mysql_query('UPDATE oldbk.users SET `battle` = '.$jert['battle'].', `zayavka` = 0, `battle_t`= '.$meteamb.' WHERE `id` IN ('.$meteam.')') or die();
	
				//addlog($jert['battle'],'<span class=date>'.date("H:i").'</span> Группа составом '.$t2b.' вмешалась в поединок!<BR>');
				$t2b=str_replace(':','^',$t2b);
				addlog($jert['battle'],"!:X:".time().':::Группа составом '.$t2b." вмешалась в поединок!\n");
	
				mysql_query('UPDATE oldbk.map_groups SET status = 2 WHERE id = '.$megroup['id'].' OR id = '.$enemygroup['id']) or die();
	
				if (($user[hidden] >0) and ($user[hiddenlog]==''))
				 {
					if ($jert[bot]==0)  { addchp ('<font color=red>Внимание!</font> На вас '.$action.' <B><i>Невидимка</i></B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$user['room'],$user['id_city']); }

					if (($get_abil['magic_id']>0) or ($tabil['magic']>0) )
						{
							if ($tabil['magic']>0)
								{
								$magic = magicinf($tabil['magic']);						
								}
								else
								{
								$magic = magicinf($get_abil['magic_id']);						
								}
						if ($magic['id']==2525) { $magic['img']='attackbv.gif';}											
						$rowm['name']=$magic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';			
						$rowm['img']=$magic['img'];
						}
					else
					if ($incmagic['name']!='')
						{
						$rowm['name']=$incmagic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';			
						$rowm['img']=$magic['img'];
						}
						else
						{
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
						}
						
					$fuser['login']='<i>Невидимка</i>';
					$fuser['id']=$fuser['hidden'];
					
					if ($rowm['img']=='') { $rowm['img']='attac0.gif'; $mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';						 }
					if ($rowm['name']=='') { $rowm['name']='Нападение';}

					addch($mag_gif." ".link_for_user($fuser)." использовал магию &quot;".link_for_magic($rowm['img'],$rowm['name'])."&quot;, внезапно ".$action." на персонажа ".link_for_user($jert).".",$jert['room'],$jert['id_city']);
					
				}
				else
				{
				        $fuser=load_perevopl($user); //проверка и загрузка перевопла если надо
		        		if ($fuser['sex'] == 1) {$action="напал";}	else {$action="напала";}
					if ($jert[bot]==0)  { addchp ('<font color=red>Внимание!</font> На вас '.$action.' <B>'.$fuser['login'].'</B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']); }

						if (($get_abil['magic_id']>0) or ($tabil['magic']>0) )
						{
							if ($tabil['magic']>0)
								{
								$magic = magicinf($tabil['magic']);						
								}
								else
								{
								$magic = magicinf($get_abil['magic_id']);						
								}
						if ($magic['id']==2525) { $magic['img']='attackbv.gif';}																
						$rowm['name']=$magic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';			
						$rowm['img']=$magic['img'];
						}
					else
					if ($incmagic['name']!='')
						{
						$rowm['name']=$incmagic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';						
						$rowm['img']=$magic['img'];												
						}
						else
						{
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
						}
					
					if ($rowm['img']=='') { $rowm['img']='attac0.gif'; $mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';						 }
					if ($rowm['name']=='') { $rowm['name']='Нападение';}
					
					addch($mag_gif." ".link_for_user($fuser)." использовал магию &quot;".link_for_magic($rowm['img'],$rowm['name'])."&quot;, внезапно ".$action." на персонажа ".link_for_user($jert).".",$jert['room'],$jert['id_city']);


				}
			}

			$q = mysql_query('COMMIT') or die();

			//header("Location: fbattle.php");
			js_goto_fbattle();
		} elseif ($jert['battle'] == 0) {
			// нету боя, создаём

			// фиксим хп
			mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` IN ('.$allteam.')') or die();
	
			// если у когото нет хелов - добавляем
			mysql_query('UPDATE oldbk.`users` SET `hp` = 2 WHERE `hp` = 0 AND `id` IN ('.$allteam.')') or die();

			// кешируем инфу
			$cache = array();
			$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.$allteam.')') or die();
			while($u = mysql_fetch_assoc($q)) {
				$cache[$u['id']] = $u;
			}

			// кешируем инфу для боя
			$t1 = explode(",",$meteam);
			$t1c = "";
			$t1b = "";
			$t2 = explode(",",$enemyteam);
			$t2c = "";
			$t2b = "";

			$mids = array();

			while(list($k,$v) = each($t1)) {
				$t1b .= BNewHist($cache[$v]);
				$t1c .= nick_align_klan($cache[$v]).",";
			}
			while(list($k,$v) = each($t2)) {
				$mids[] = $v;
				$t2b .= BNewHist($cache[$v]);
				$t2c .= nick_align_klan($cache[$v]).",";
			}

			$botids1 = "";
			$botids2 = "";

			// добавляем лошадей в бой
			if ($megroup['horse'] == 1 || $enemygroup['horse'] == 1) {
				$q = mysql_query('SELECT * FROM oldbk.`users` WHERE `id` = 88') or die();
				$BOT = mysql_fetch_array($q) or die();
				$BOT['protid'] = $BOT['id'];
				$BOT_items = load_mass_items_by_id($BOT);


				if ($megroup['horse']) {
					$q = mysqL_query('SELECT login,hidden FROM oldbk.users WHERE id IN('.$meteam.')');
					while($u = mysql_fetch_assoc($q)) {
						if ($u['hidden']) {
							$horsename = 'Лошадь (<i>Невидимка</i>) ';
						} else {
							$horsename = 'Лошадь ('.$u['login'].') ';
						}
						$BOT['login'] = $horsename;
						$i = InsertBot($BOT,$BOT_items,$horsename);
						$t1c .= nick_align_klan($BOT).",";
						$t1b .= BNewHist($BOT);
						$botids1 .= $i.",";
					}
					if (strlen($botids1)) $botids1 = substr($botids1,0,strlen($botids1)-1);
				}				

				if ($enemygroup['horse']) {
					$q = mysqL_query('SELECT login,hidden FROM oldbk.users WHERE id IN('.$enemyteam.')');
					while($u = mysql_fetch_assoc($q)) {
						if ($u['hidden']) {
							$horsename = 'Лошадь (<i>Невидимка</i>) ';
						} else {
							$horsename = 'Лошадь ('.$u['login'].') ';
						}
						$BOT['login'] = $horsename;
						$i = InsertBot($BOT,$BOT_items,$horsename);
						$t2c .= nick_align_klan($BOT).",";
						$t2b .= BNewHist($BOT);
						$botids2 .= $i.",";
					}
					if (strlen($botids2)) $botids2 = substr($botids2,0,strlen($botids2)-1);
				}				
			}
			

			$t1c = substr($t1c,0,strlen($t1c)-1);
			$t2c = substr($t2c,0,strlen($t2c)-1);

			$t1a = (strlen($botids1) ? str_replace(",",";",$meteam).";".str_replace(",",";",$botids1) : str_replace(",",";",$meteam));
			$t2a = (strlen($botids2) ? str_replace(",",";",$enemyteam).";".str_replace(",",";",$botids2) : str_replace(",",";",$enemyteam));


			mysql_query('INSERT INTO oldbk.`battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
					VALUES
					(
					"Загородный бой",
					"",
					"3",
					"13",
					"0",
					"'.$t1a.'",
					"'.$t2a.'",
					"'.time().'",
					"'.time().'",
					3,
					"'.mysql_real_escape_string($t1b).'",
					"'.mysql_real_escape_string($t2b).'",
					"0","0"
					)
			') or die();

			$id = mysql_insert_id();

			// закидываем всех в бой			
			mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$enemyteam.')') or die();
			mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id` IN ('.$meteam.')') or die();

			if (strlen($botids1)) {
				mysql_query('UPDATE oldbk.`users_clons` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id` IN ('.$botids1.')') or die();
			}

			if (strlen($botids2)) {
				mysql_query('UPDATE oldbk.`users_clons` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$botids2.')') or die();
			}

			$p2 = '<b>'.$t1c.'</b> и <b>'.$t2c.'</b>';
//			addlog($id,'Часы показывали <span class=date>'.date("Y.m.d H.i").'</span>, когда '.$p2.' бросили вызов друг другу. <BR>');
			addlog($id,"!:S:".time().":".$t1b.":".$t2b."\n");

			// выставляем статус на группы
			mysql_query('UPDATE oldbk.map_groups SET status = 2 WHERE id = '.$megroup['id'].' OR id = '.$enemygroup['id']) or die();

			$q = mysql_query('COMMIT') or die();

			if (count($mids)) addch_group('<font color=red>Внимание!</font> На вас напали! <BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ',$mids);
			
			if ($user['hidden'] > 0) {
				addch('<img src=i/magic/attac0.gif> <b><i>Невидимка</i></b>, применив магию нападения, внезапно напал на <b>'.$jert['login'].'</b>.',$jert['room']);
			} else {
				addch('<img src=i/magic/attac0.gif> <b>'.$user['login'].'</b>, применив магию нападения, внезапно напал на <b>'.$jert['login'].'</b>.',$jert['room']);
			}
			                 
			//header("Location: fbattle.php");
			js_goto_fbattle();
		} else {
			echo "Нападение не удалось!";
			$q = mysql_query('COMMIT') or die();
			return;
		}
	}

	$q = mysql_query('COMMIT') or die();
	$bet = 1;
	$sbet = 1;
?>