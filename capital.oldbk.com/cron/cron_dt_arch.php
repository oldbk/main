<?php
ob_start();
include "/www/capitalcity.oldbk.com/cron/init.php";
ob_end_clean();
require_once('/www/capitalcity.oldbk.com/memcache.php');
require_once('/www/capitalcity.oldbk.com/dt_config.php');
require_once('/www/capitalcity.oldbk.com/dt_functions.php');
require_once('/www/capitalcity.oldbk.com/fsystem.php');
$src = '/www/capitalcity.oldbk.com/';

if(!lockCreate("cron_dt_arch")) {
	die();
}

unlink('/www/capitalcity.oldbk.com/cron/cron_dtarch_stop');

addchp ('<font color=red>Внимание!</font> start arch','{[]}Десятый{[]}',-1,-1);
addchp ('<font color=red>Внимание!</font> start arch','{[]}Bred{[]}',-1,-1);

function EchoLog($txt) {
	echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
}

function bot_attack($bot,$usr,$map,$kulak = 0, $force = 0) {
	EchoLog("Attack start");
	$is_bot = false;
	if ($usr['id'] > _BOTSEPARATOR_) {
		EchoLog("Attacking bot");
		$is_bot = true;
	}

	$q = mysql_query('START TRANSACTION');
	if ($q === false) return false;

	if ($is_bot) {
		// ищем бота по логину и по 
		$q = mysql_query('SELECT * FROM `users_clons` WHERE (`id` = '.$usr['id'].' or id = '.$bot['id'].') AND `bot_online` = 5 AND `bot_room` = '.$bot['bot_room'].' FOR UPDATE');
		if ($q === false) return false;
	} else {
		$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$usr['id'].' AND room = '.$bot['bot_room'].' FOR UPDATE');
		if ($q === false) return false;

		$jert = mysql_fetch_assoc($q);
		$jert['bot_room'] = $jert['room'];

		$q = mysql_query('SELECT * FROM `users_clons` WHERE id = '.$bot['id'].' FOR UPDATE');
		if ($q === false) return false;

		$bot = mysql_fetch_assoc($q);
		if (!$bot) return false;
	}

	if ((mysql_num_rows($q) == 2 && $is_bot) || (!$is_bot && count($jert))) {
		// получили инфу
		if ($is_bot) {
			$p1 = mysql_fetch_assoc($q);
			$p2 = mysql_fetch_assoc($q);
			if (!$p1 || !$p2) return false;

			if ($p1['id'] == $bot['id']) {
				$bot = $p1;
				$jert = $p2;
			} else {
				$bot = $p2;
				$jert = $p1;
			}
		}

			
		// проверяем комнату, айди чтобы не сам на себя и чтобы была противоположная команда и чтобы хп были больше 0 и что мы сами не в бою
		if ($bot['bot_room'] == $jert['bot_room'] && $jert['id'] != $bot['id'] && $bot['hp'] > 0 && $jert['hp'] > 0 && $bot['battle'] == 0) {
			if($jert['battle'] == 0) {
				if ($kulak) {
					EchoLog("Kulak attack, undressing self");
					$bot = undressallbot($bot);
					if ($bot === false) return false;
					if ($is_bot) {
						EchoLog("Undressing bot enemy");
						$jert = undressallbot($jert);
						if ($jert === false) return false;
					} else {
						EchoLog("Undressing user enemy");
						$q = undressalltrz($jert['id']);
						if ($q === false) return false;
					}
				}

				// фиксим HP у противника, фиксим у себя
				if ($is_bot) {
					$q = mysql_query('UPDATE `users_clons` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE (`id` = '.$bot['id'].' or id = '.$jert['id'].') AND hp > maxhp');
					if ($q === false) return false;
				} else {
					$q = mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$jert['id']);
					if ($q === false) return false;
					$q = mysql_query('UPDATE `users_clons` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE `id` = '.$bot['id'].' AND hp > maxhp');
					if ($q === false) return false;
				}
				if ($q === false) return false;

				$q = mysql_query('INSERT INTO `battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
						VALUES
						(
							"Бой в Башне Смерти",
							"",
							"'.mt_rand(1,5).'",
							"1010",
							"0",
							"'.$bot['id'].'",
							"'.$jert['id'].'",
							"'.time().'",
							"'.time().'",
							3,
							"'.mysql_real_escape_string(BNewHist($bot)).'",
							"'.mysql_real_escape_string(BNewHist($jert)).'",
							"1","0"
						)
				');
				if ($q === false) return false;

				$id = mysql_insert_id();

				// теперь обновляем себя и противника что мы в бою
				if ($is_bot) {
					$q = mysql_query('UPDATE `users_clons` SET `battle` = '.$id.', `battle_t` = 2 WHERE `id`= '.$jert['id']);
				} else {
					$q = mysql_query('UPDATE `users` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id`= '.$jert['id']);
				}
				if ($q === false) return false;

				$q = mysql_query('UPDATE `users_clons` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$bot['id']);
				if ($q === false) return false;

				if ($kulak) {
					$q = addch('<img src=i/magic/attackk.gif> <b>'.$bot['login'].'</b>, применив кулачное нападение, внезапно напал на <b>'.$jert['login'].'</b>.',$bot['bot_room']);
				} else {
					if ($force) {
						$q = addch('<img src=i/magic/attack.gif> <b>'.$bot['login'].'</b>, применив магию нападения, в ярости напал на <b>'.$jert['login'].'</b>.',$bot['bot_room']);
					} else {
						$q = addch('<img src=i/magic/attack.gif> <b>'.$bot['login'].'</b>, применив магию нападения, внезапно напал на <b>'.$jert['login'].'</b>.',$bot['bot_room']);
					}
				}
				if ($q === false) return false;

				$p2 = '<b>'.nick_align_klan($bot).'</b> и <b>'.nick_align_klan($jert).'</b>';
				addlog($id,"!:S:".time().":".BNewHist($bot).":".BNewHist($jert)."\n");

				// лог
				if ($kulak) {
					$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($bot).'</b> напал кулачным нападением на <b>'.nick_hist($jert).'</b> завязался <a href="logs.php?log='.$id.'" target="_blank">бой »»</a><BR>';
				} else {
					$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($bot).'</b> напал на <b>'.nick_hist($jert).'</b> завязался <a href="logs.php?log='.$id.'" target="_blank">бой »»</a><BR>';
				}


				if ($force) {
					$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($bot).'</b> в ярости напал на <b>'.nick_hist($jert).'</b> завязался <a href="logs.php?log='.$id.'" target="_blank">бой »»</a><BR>';
				}

				$q = mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']);
				if ($q === false) return false;
			} else {
				// уже есть бой, вмешиваемся
				// фиксим хп
				$q = mysql_query('UPDATE `users_clons` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE `id` = '.$bot['id'].' AND hp > maxhp');
				if ($q === false) return false;

				// находим бой жертвы
				$q = mysql_query('SELECT * FROM `battle` WHERE `id` = '.$jert['battle']);
				if ($q === false) return false;
				$bd = mysql_fetch_assoc($q);

				if ($bd['id']) {
					$t1 = explode(";",$bd['t1']);
	
					// проставляем кто-где
					if ($jert['battle_t'] == 1) {
						$meteam = 2;
						$enemyteam = 1;
					} else {
						$meteam = 1;
						$enemyteam = 2;
					}
	
					// добавляем себя в массив боя
					$q = mysql_query('UPDATE `battle` SET `t'.$meteam.'` = CONCAT(`t'.$meteam.'`,";'.$bot['id'].'"),  `t'.$meteam.'hist`= CONCAT(`t'.$meteam.'hist`,"'.mysql_real_escape_string(BNewHist($bot)).'")  ,`to'.$meteam.'` = "'.time().'", `to'.$enemyteam.'` = "'.(time()-1).'" WHERE `id` = '.$jert['battle'].' and status=0 and win=3 and t1_dead=""');
					if ($q === false) return false;
	
					if (mysql_affected_rows() > 0) {
						$q = addch('<img src=i/magic/attack.gif> <b>'.$bot['login'].'</b> вмешался в <a href=logs.php?log='.$jert['battle'].' target=_blank>поединок »»</a>.',$bot['bot_room']);
						if ($q === false) return false;

						$bot['battle_t'] = $meteam;
						$ac = ($bot['sex']*100)+mt_rand(1,2);
						addlog($jert['battle'],"!:W:".time().":".BNewHist($bot).":".$bot['battle_t'].":".$ac."\n");
						// выставляем себе номер боя
						$q = mysql_query('UPDATE users_clons SET `battle` = '.$jert['battle'].', `battle_t`= '.$meteam.' WHERE `id` = '.$bot['id']);
						if ($q === false) return false;
	
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($bot).'</b> вмешался в поединок против <b>'.nick_hist($jert).'</b> <a href="logs.php?log='.$jert['battle'].'" target="_blank">бой »»</a><BR>';

						if ($force) {
							$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($bot).'</b> в ярости вмешался в поединок против <b>'.nick_hist($jert).'</b> <a href="logs.php?log='.$jert['battle'].'" target="_blank">бой »»</a><BR>';
						}

						$q = mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']);
						if ($q === false) return false;
					}
				}
			}
		}
	}

	$q = mysql_query('COMMIT');
	if ($q === false) return false;

	EchoLog("Attack end");	
	return true;
}

function CronArchRun() {
	global $dt_rooms;
	global $art_items_up;
	global $noart_items_up;
	global $frrelpath;
	global $dt_relmap;
	global $dt_topweapon;

	$attacktime = 60*3; // задержка на нападение

	//EchoLog("CronArchRun");
	$qbot = mysql_query('SELECT * FROM users_clons WHERE id_user = 84');
	if ($qbot === false) return false;
	if (mysql_num_rows($qbot) == 0) return 4; // long wait

	$map = mysql_query('SELECT * FROM dt_map WHERE active = 1');
	if ($map === false) return false;
	if (mysql_num_rows($map) == 0) return 4; // long wait
	$map = mysql_fetch_assoc($map);

	/*
	if ($map['halftype']) {
		$dt_rooms[$dt_relmap+517][2] = 0;
		$dt_rooms[$dt_relmap+518][4] = 0;

		$dt_rooms[$dt_relmap+524][2] = 0;
		$dt_rooms[$dt_relmap+525][4] = 0;

		$dt_rooms[$dt_relmap+544][2] = 0;
		$dt_rooms[$dt_relmap+545][4] = 0;

		$dt_rooms[$dt_relmap+550][2] = 0;
		$dt_rooms[$dt_relmap+551][4] = 0;

		$dt_rooms[$dt_relmap+554][2] = 0;
		$dt_rooms[$dt_relmap+555][4] = 0;

		$dt_rooms[$dt_relmap+520][2] = 0;
	}
	*/

	$z = 0;

	while($bot = mysql_fetch_assoc($qbot)) {
		EchoLog("Processing bot: ".$bot['id']);

		// обновляем инфу
		if ($z != 0) {
			$q = mysql_query('SELECT * FROM users_clons WHERE id = '.$bot['id']);
			if ($q === false) return false;
			$bot = mysql_fetch_assoc($q);
		} else {
			$z++;
		}

		if ($bot['battle'] > 0) {
			EchoLog("Bot in battle");
			// если в бою, то можем юзать хилки, клон, переман
			//EchoLog("Getting all scrolls from bot");
			$archilist = array();
			$scrolls = array();
			$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$bot['id'].' AND bs_owner = 15 and type = 12');
			if ($q === false) return false;
			$isclone = false;
			$unclone = false;
			while($i = mysql_fetch_assoc($q)) {
				$scrolls[] = $i;
				if ($i['prototype'] == 119) $isclone = $i;
				if ($i['prototype'] == 120) $unclone = $i;
			}

			// хилимся в бою
			if ($bot['maxhp'] > $bot['hp'] * 2) {
				// хелов меньше 50% - хилимся если есть чем
				//EchoLog("Bot need healing");
				$healsproto = array(650,651,652,653,654,656,657);
				reset($scrolls);
				while(list($k,$v) = each($scrolls)) {
					if (in_array($v['prototype'],$healsproto)) {
						$toadd = 0;
						switch($v['prototype']) {
							case 650: $toadd = 15; break;
							case 651: $toadd = 30; break;
							case 652: $toadd = 45; break;
							case 653: $toadd = 60; break;
							case 654: $toadd = 300; break;
							case 656: $toadd = 90; break;
							case 657: $toadd = 120; break;
						}
						if ($toadd) {
							if ($toadd+$bot['hp'] > $bot['maxhp']) {
								$toadd = $bot['maxhp']-$bot['hp'];						
							}
							EchoLog("Healing +".$toadd);
							$q = mysql_query('UPDATE users_clons SET hp = hp + '.$toadd.' WHERE hp > 0 and battle > 0 and id = '.$bot['id']);
							if ($q === false) return false;
							if (mysql_affected_rows()) {
								$bot['hp'] += $toadd;
								addlog($bot['battle'],"!:H:".time().':'.nick_new_in_battle($bot).":".(($bot['sex']*100)+1).":".(($bot['sex']*100)+1)."::::::".$toadd.":[".($bot['hp'])."/".$bot['maxhp']."]\n");

							       	if($v['duration']+1 >= $v['maxdur']) {
							     		$q = mysql_query('DELETE FROM oldbk.inventory where id = '.$v['id'].' AND owner = '.$bot['id']);
									if ($q === false) return false;
									unset($scrolls[$k]);
							     	} else {
									$q = mysql_query('update oldbk.inventory set duration = duration+1 WHERE id = '.$v['id'].' AND owner = '.$bot['id']);
									if ($q === false) return false;
									$scrolls[$k]['duration']++;
								}
							} else {
								break; // похилится не удалось, прерываем цикл
							}
						}
						if ($bot['maxhp'] < $bot['hp'] * 2) break;
						reset($scrolls); // проходимся по циклу опять
						sleep(1);
					}
				}
			}

			// переман клона - 120
			$q = mysql_query('SELECT * FROM users_clons WHERE battle = '.$bot['battle'].' AND bot_online = 0 and battle_t != '.$bot['battle_t']);
			if ($q === false) return false;
			if (mysql_num_rows($q) > 0 && is_array($unclone)) {
				EchoLog("Bot unclone");
				// есть клон в противоположной команде, делаем переман
				$get_life_users = mysql_query("select id from users where battle='{$bot[battle]}' and  battle_t != '{$bot[battle_t]}' and hp > 0 LIMIT 1;");
				if ($get_life_users === false) return false;

				if (mysql_num_rows($get_life_users)) {
					// есть люди - можем переманивать
					$clon = mysql_fetch_assoc($q);

					$q = mysql_query("UPDATE `users_clons` SET `battle_t` = ".$bot['battle_t']."  WHERE `id` = ".$clon['id']);
					if ($q === false) return false;

					$my_team_n = $bot['battle_t'];
					$en_team_n = $clon['battle_t'];

					$time = time();
					$q = mysql_query("UPDATE battle SET to1=".$time.", to2=".$time.", t".$my_team_n."=CONCAT(t".$my_team_n.",';".$clon[id]."') , t".$my_team_n."hist=CONCAT(t".$my_team_n."hist,'".BNewHist($clon)."') ,  t".$en_team_n."=REPLACE(t".$en_team_n.",';".$clon[id]."',''), t".$en_team_n."hist=REPLACE(t".$en_team_n."hist,'".BNewHist($clon)."','') WHERE id = ".$bot['battle']);
					if ($q === false) return false;

					$btext=str_replace(':','^',nick_in_battle($clon,$clon['battle_t'])).' на свою сторону.';
			       	       	addlog($bot['battle'],"!:X:".time().':'.nick_new_in_battle($bot).':'.($bot['sex']+1000).":".$btext."\n");

					$v = $unclone;
				       	if($v['duration']+1 >= $v['maxdur']) {
				     		$q = mysql_query('DELETE FROM oldbk.inventory where id = '.$v['id'].' AND owner = '.$bot['id']);
						if ($q === false) return false;
				     	} else {
						$q = mysql_query('update oldbk.inventory set duration = duration+1 WHERE id = '.$v['id'].' AND owner = '.$bot['id']);
						if ($q === false) return false;
					}

				}

			}

			// клонирование - 119
			if ($bot['maxhp'] < $bot['hp'] * 2 && mt_rand(1,100) >= 50 && is_array($isclone)) {
				// если хп > 50%

				// проверяем есть ли живые клоны за команду арха
				$q = mysql_query('SELECT * FROM users_clons WHERE battle = '.$bot['battle'].' AND bot_online = 0 and battle_t = '.$bot['battle_t']);
				if ($q === false) return false;
				if (mysql_num_rows($q) == 0) {
					EchoLog("Bot clone");
					// выпускаем клона
					$bot_data = mysql_query('SELECT * FROM users_clons WHERE id = '.$bot['id']);
					if ($bot_data === false) return false;
					if (mysql_num_rows($bot_data)) {
					        $bot_data = mysql_fetch_assoc($bot_data);

						$bot_data['login'] .= ' (клон)';

						$q = mysql_query("INSERT INTO `users_clons` SET `login`='".$bot_data['login']."',`sex`='{$bot_data['sex']}',
							`level`='{$bot_data['level']}',`align`='{$bot_data['align']}',`klan`='{$bot_data['klan']}',`sila`='{$bot_data['sila']}',
							`lovk`='{$bot_data['lovk']}',`inta`='{$bot_data['inta']}',`vinos`='{$bot_data['vinos']}',
							`intel`='{$bot_data['intel']}',`mudra`='{$bot_data['mudra']}',`duh`='{$bot_data['duh']}',`bojes`='{$bot_data['bojes']}',`noj`='{$bot_data['noj']}',
							`mec`='{$bot_data['mec']}',`topor`='{$bot_data['topor']}',`dubina`='{$bot_data['dubina']}',`maxhp`='{$bot_data['maxhp']}',`hp`='{$bot_data['hp']}',
							`maxmana`='{$bot_data['maxmana']}',`mana`='{$bot_data['mana']}',`sergi`='{$bot_data['sergi']}',`kulon`='{$bot_data['kulon']}',`perchi`='{$bot_data['perchi']}',
							`weap`='{$bot_data['weap']}',`bron`='{$bot_data['bron']}',`r1`='{$bot_data['r1']}',`r2`='{$bot_data['r2']}',`r3`='{$bot_data['r3']}',`helm`='{$bot_data['helm']}',
							`shit`='{$bot_data['shit']}',`boots`='{$bot_data['boots']}',`nakidka`='{$bot_data['nakidka']}',`rubashka`='{$bot_data['rubashka']}',`shadow`='{$bot_data['shadow']}',`battle`='{$bot_data['battle']}',`bot`=1,
							`id_user`='{$bot['id']}',`at_cost`='{$bot_data['allsumm']}',`kulak1`=0,`sum_minu`='{$bot_data['min_u']}',
							`sum_maxu`='{$bot_data['max_u']}',`sum_mfkrit`='{$bot_data['krit_mf']}',`sum_mfakrit`='{$bot_data['akrit_mf']}',
							`sum_mfuvorot`='{$bot_data['uvor_mf']}',`sum_mfauvorot`='{$bot_data['auvor_mf']}',`sum_bron1`='{$bot_data['bron1']}',
							`sum_bron2`='{$bot_data['bron2']}',`sum_bron3`='{$bot_data['bron3']}',`sum_bron4`='{$bot_data['bron4']}',`ups`='{$bot_data['ups']}',  `hiddenlog`='{$bot_data[hiddenlog]}',
							`injury_possible`=0, `battle_t`='{$bot_data['battle_t']}';");

						if ($q === false) return false;

						$bot_data['id'] = mysql_insert_id();

						$time = time();
						$ttt = $bot_data['battle_t'];

						$btext = str_replace(':','^',nick_in_battle($bot_data,$ttt));
       	       					addlog($bot_data['battle'],"!:X:".time().':'.nick_new_in_battle($bot_data).':'.($bot_data['sex']+300).":".$btext."\n");

						$q = mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data['id'].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.BNewHist($bot_data).'\') WHERE id = '.$bot_data['battle'].' ;');
						if ($q === false) return false;

						$v = $isclone;		
					       	if($v['duration']+1 >= $v['maxdur']) {
					     		$q = mysql_query('DELETE FROM oldbk.inventory where id = '.$v['id'].' AND owner = '.$bot['id']);
							if ($q === false) return false;
					     	} else {
							$q = mysql_query('update oldbk.inventory set duration = duration+1 WHERE id = '.$v['id'].' AND owner = '.$bot['id']);
							if ($q === false) return false;
						}

					}
				}
			}

			// список людей-ботов в комнате - юзаем магию также в бою типа пут, ловушек, молчей
			$usrlist = array();
			$q = mysql_query('SELECT * FROM users WHERE room = '.$bot['bot_room'].' and battle = 0');
			if ($q === false) return false;
			while($u = mysql_fetch_assoc($q)) {
				$u['bot_room'] = $u['room'];
				$usrlist[] = $u;
			}
			$q = mysql_query('SELECT * FROM users_clons WHERE bot_room = '.$bot['bot_room'].' and id <> '.$bot['id']);
			if ($q === false) return false;
			while($u = mysql_fetch_assoc($q)) {
				$u['room'] = $u['bot_room'];
				$usrlist[] = $u;
			}
			shuffle($usrlist);


			reset($scrolls);
			while(list($k,$v) = each($scrolls)) {
				$used = 0;

				switch($v['prototype']) {
					case 194194:
						// ловушка
						$q = mysql_query('SELECT * FROM dt_items WHERE type = 1 AND room = '.$bot['bot_room']);
						if ($q === false) return false;
						if (mysql_num_rows($q) == 0) {
							EchoLog("Bot trap from battle");
							$q = mysql_query('INSERT INTO `dt_items` (type,name,img,room,extra) VALUES ("1","Ловушка","",'.$bot['bot_room'].','.$bot['id'].')');
							if ($q === false) return false;
							$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
							if ($q === false) return false;
							unset($scrolls[$k]);
							$used = 1;
						}
					break;
					case 121:
						// путы
						if (!(($map['starttime'] + $attacktime) > time()) && count($usrlist)) {
							if (mt_rand(1,100) <= 25) {
								$usr = $usrlist[mt_rand(0,count($usrlist)-1)];
								$q = mysql_query('SELECT * FROM `effects` WHERE `owner` = '.$usr['id'].' and `type` = 10'); 
								if ($q === false) return false;	

								if (mysql_num_rows($q) == 0) {
									EchoLog("Bot stop from battle");
									$q = mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('.$usr['id'].',"Путы",'.(time()+600).',10)');
									if ($q === false) return false;
									$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
									if ($q === false) return false;

									$messch="Персонаж &quot;{$bot[login]}&quot; наложил путы на &quot;{$usr[login]}&quot;";
									addch("<img src=i/magic/chains.gif> $messch",$usr['bot_room'],$usr['id_city']);						

									unset($scrolls[$k]);
									$used = 1;
								}
							}
						}
					break;
					case 102: case 103:
						// молчи
						if (count($usrlist)) {
							$usr = $usrlist[mt_rand(0,count($usrlist)-1)];
							if ($usr['id'] < _BOTSEPARATOR_) {
								$q = mysql_query('SELECT * FROM `effects` WHERE type = 2 AND `owner` = '.$usr['id']);
								if ($q === false) return false;
								if (mysql_num_rows($q) == 0) {
									EchoLog("Bot sleep from battle");
									$tt = 900;
									if ($v['prototype'] == 103) $tt = 1800;

									addch("<img src=i/magic/sleep.gif>Персонаж &quot;{$bot[login]}&quot; наложил заклятие молчания на &quot;{$usr['login']}&quot;, сроком ".($tt/60)." мин.",$usr['bot_room'],$usr['id_city']);
		
									$q = mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$usr['id']."','Заклятие молчания',".(time()+$tt).",2)");
									if ($q === false) return false;
									$q = mysql_query("UPDATE users set slp = 1 where id = ".$usr['id']);
									if ($q === false) return false;

									$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
									if ($q === false) return false;
									unset($scrolls[$k]);
									$used = 1;
								}
							}
						}
					break;
				}

				if ($used) break; // не более одного свитка за проход
			}

		} else {
			// если не в бою
			EchoLog("Bot not in battle");


			$ses = GetSerFile($frrelpath.$bot['id']);
			if (!isset($ses['id']) || $ses['id'] != $map['id']) {
				// новая бс, старые данные надо снести
				$ses['time'] = time();
				$ses['timei'] = time();
				$ses['id'] = $map['id'];
				$ses['ihash'] = 0;
				SaveSerFile($frrelpath.$bot['id'],$ses);
			}



			$q = mysql_query('SELECT * FROM users WHERE in_tower = 15');
			if (mysql_num_rows($q) == 0) {
				EchoLog("No users in tower, do nothing...");
				return 3;
			}

			$getnewitems = false;
			// вначале проверяем вещи в комнате
			$q = mysql_query('START TRANSACTION');
			if ($q === false) return false;
			$q = mysql_query('SELECT * FROM `dt_items` WHERE type = 0 AND iteam_id != 114 AND room = '.$bot['bot_room'].' FOR UPDATE');
			if ($q === false) return false;

			if (mysql_num_rows($q) > 0) {
				//EchoLog("Getting ".mysql_num_rows($q)." items in room ".$bot['bot_room']);
				$items = array();
				while($i = mysql_fetch_assoc($q)) {
					$items[] = $i;
				}

				// удаляем вещи
				$q = mysql_query('DELETE FROM `dt_items` WHERE room = '.$bot['bot_room']);
				if ($q === false) return false;				

				while(list($k,$item) = each($items)) {
					// получилаем прототип
					$proto = $item['iteam_id'];
					$subtype = $item['extra'];

					$q = mysql_query_cache('SELECT * FROM oldbk.`shop` WHERE `id` = '.$item['iteam_id'],false,60);
					if ($q === FALSE || !count($q)) return false;
					$dress = $q[0];
	
					$dur = $item['durability'];
					$present = $item['present'];

					// убираем требование левела
					$dress['nlevel'] = 0;


					if ($map['arttype']) {
						$tmp = $art_items_up;
					} else {
						$tmp = $noart_items_up;
					}

					reset($tmp[$dress['id']]);
					while(list($k,$v) = each($tmp[$dress['id']])) {
						$dress[$k] = $v;
					}
	
                                        $getnewitems = true;
					$q = mysql_query('INSERT INTO oldbk.`inventory`
							(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`duration`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
								`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
								,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
								`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`
							)
							VALUES	(
								"'.mysql_real_escape_string($present).'",
								'.$dress['id'].',
								'.$bot['id'].',
								"'.mysql_real_escape_string($dress['name']).'",
								'.$dress['type'].',
								'.$dress['massa'].',
								'.$dress['cost'].',
								"'.mysql_real_escape_string($dress['img']).'",
								'.$dur.',
								'.$dress['maxdur'].',
								'.$dress['isrep'].',
								'.$dress['gsila'].',
								'.$dress['glovk'].',
								'.$dress['ginta'].',
								'.$dress['gintel'].',
								'.$dress['ghp'].',
								'.$dress['gnoj'].',
								'.$dress['gtopor'].',
								'.$dress['gdubina'].',
								'.$dress['gmech'].',
								'.$dress['gfire'].',
								'.$dress['gwater'].',
								'.$dress['gair'].',
								'.$dress['gearth'].',
								'.$dress['glight'].',
								'.$dress['ggray'].',
								'.$dress['gdark'].',
								'.$dress['needident'].',
								'.$dress['nsila'].',
								'.$dress['nlovk'].',
								'.$dress['ninta'].',
								'.$dress['nintel'].',
								'.$dress['nmudra'].',
								'.$dress['nvinos'].',
								'.$dress['nnoj'].',
								'.$dress['ntopor'].',
								'.$dress['ndubina'].',
								'.$dress['nmech'].',
								'.$dress['nfire'].',
								'.$dress['nwater'].',
								'.$dress['nair'].',
								'.$dress['nearth'].',
								'.$dress['nlight'].',
								'.$dress['ngray'].',
								'.$dress['ndark'].',
								'.$dress['mfkrit'].',
								'.$dress['mfakrit'].',
								'.$dress['mfuvorot'].',
								'.$dress['mfauvorot'].',
								'.$dress['bron1'].',
								'.$dress['bron2'].',
								'.$dress['bron3'].',
								'.$dress['bron4'].',
								'.$dress['maxu'].',
								'.$dress['minu'].',
								'.$dress['magic'].',
								'.$dress['nlevel'].',
								'.$dress['nalign'].',
								"'.( ($dress['goden']) ? ($dress['goden']*24*60*60+time()) : 0).'",
								'.$dress['goden'].',
								'.$dress['razdel'].',
								"15",
								'.$dress['group'].',"",0,0,0,0,"",0,0,0,"'.$subtype.'"
							)
					');
					if ($q === false) return false;
				}
			}
			$q = mysql_query('COMMIT');
			if ($q === false) return false;

			// получаем список шмотья, используется для переодевания если надо и для юзания магии ниже
			//EchoLog("Getting all items from bot");
			$archilist = array();
			$scrolls = array();
			$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$bot['id'].' AND bs_owner = 15 ORDER BY dressed DESC');
			if ($q === false) return false;
			//EchoLog("Found: ".mysql_num_rows($q));
			$id = 0;
			$up = 0;

			$arch_topweapon = false;
			$arch_fullitems = 0; // 13 шмоток можно одеть
			$arch_fullitemscost = 0;

			while($i = mysql_fetch_assoc($q)) {
				if ($i['dressed'] && in_array($i['prototype'],$dt_topweapon)) {
					$arch_topweapon = true;
				}

				if ($i['dressed']) {
					$arch_fullitems++;
					$arch_fullitemscost += $i['cost'];
				}


				$archilist[] = $i;
				$up += DateTime::createFromFormat('!Y-m-d H:i:s',$i['update'])->getTimestamp(); 
				$id += $i['id'];
				if ($i['type'] == 12) {
					$scrolls[] = $i;
				}
			}

			$ihash = $id.$up;

			if (!isset($ses['ihash']) || $ses['ihash'] != $ihash) {
				$ses['ihash'] = $ihash;
				$getnewitems = true;
				SaveSerFile($frrelpath.$bot['id'],$ses);
			}


			if ($getnewitems) {
				//EchoLog(serialize($bot));
				// одеваемся в лучшее шмотьё
				$typelist = array(
					'sergi' => array(),
					'kulon' => array(),
					'weap' => array(),
					'bron' => array(),
					'helm' => array(),
					'perchi' => array(),
					'shit' => array(),
					'boots' => array(),
					'nakidka' => array(),
					'rubashka' => array(),
					'r1' => array(),
					'r2' => array(),
					'r3' => array(),
				);
				$drsl = array();
				$udrsl = array();

				reset($archilist);
				while(list($k,$v) = each($archilist)) {
					$slot1 = "";
					switch($v['type']) {
						case 1: $slot1 = 'sergi'; break;
						case 2: $slot1 = 'kulon'; break;
						case 3: $slot1 = 'weap'; break;
						case 4: $slot1 = 'bron'; break;
						case 5: $slot1 = 'r1'; break;
						case 6: $slot1 = 'r2'; break;
						case 7: $slot1 = 'r3'; break;
						case 8: $slot1 = 'helm'; break;
						case 9: $slot1 = 'perchi'; break;
						case 10: $slot1 = 'shit'; break;
						case 11: $slot1 = 'boots'; break;
						case 27: $slot1 = 'nakidka'; break;
						case 28: $slot1 = 'rubashka'; break;
					}
					if (empty($slot1)) continue;

						
					if ($map['hptype'] == 2) {
						// бс без хп
						if ($v['type'] == 5 || $v['type'] == 6 || $v['type'] == 7) {
							// это кольцо
							if ($v['prototype'] == 195 || $v['prototype'] == 201) {
								// кольцо жизни или великое кольцо, ставим ценник в 1
								$v['cost'] = 1;
							}
						}
					}


					if ($v['dressed']) {
						if ($v['type'] == 5) {
							// исключения на кольца
							for ($i = 1; $i <= 3; $i++) {
								if ($bot['r'.$i] == $v['id']) {
									$typelist['r'.$i]["dressed"] = $v;
									if (isset($typelist['r'.$i]["best"])) {
										if ($typelist['r'.$i]["best"]['cost'] <= $v['cost']) {
											$typelist['r'.$i]["best"] = $v;
										}
									} else {
										$typelist['r'.$i]["best"] = $v;
									}
								}
							}
						} else {
							$typelist[$slot1]["dressed"] = $v;
							$typelist[$slot1]["best"] = $v;
						}
					} else {
						if ($v['type'] == 5) {
							// исключения на кольцах
							
							// вешаем кольца на пустые слоты
							for ($i = 1; $i <= 3; $i++) {
								if (!isset($typelist['r'.$i]["best"])) {
									$typelist['r'.$i]["best"] = $v;
									continue 2;
								}
							}

							// все слоты заняты, находим слот с минимальной ценой
							$slotr = 0;
							$slotrmin = PHP_INT_MAX;
							for ($i = 1; $i <= 3; $i++) {
								if (isset($typelist['r'.$i]["best"])) {
									if ($slotrmin > $typelist['r'.$i]["best"]['cost']) {
										$slotr = $i;
										$slotrmin = $typelist['r'.$i]["best"]['cost'];
									}
								}
							}

							if ($slotr > 0) {
								if($typelist['r'.$slotr]["best"]['cost'] < $v['cost']) {
									$typelist['r'.$slotr]["best"] = $v;
								}
							}
						} else {
							if (!isset($typelist[$slot1]["best"]) || $typelist[$slot1]["best"]['cost'] < $v['cost']) {
								$typelist[$slot1]["best"] = $v;
							}
						}
					}
				}

				// собрали список, проверяем
				while(list($k,$v) = each($typelist)) {
					if (count($v)) {
						if (isset($v['dressed']) && isset($v['best']) && $v['dressed']['cost'] < $v['best']['cost']) {
							// снимаем шмотку
							$bot[$k] = 0;
							$item = $v['dressed'];
							EchoLog("UnDressing: ".$item['prototype'].":".$item['name']);
							$udrsl[] = $item['id'];

							$bot['maxhp'] -= $item['ghp'];
							$bot['sum_minu'] -= $item['minu'];
							$bot['sum_maxu'] -= $item['maxu'];

							$bot['sila'] -= $item['gsila'];
							$bot['lovk'] -= $item['glovk'];
							$bot['inta'] -= $item['ginta'];
							$bot['intel'] -= $item['gintel'];
							$bot['mudra'] -= $item['gmp'];

							$bot['sum_mfuvorot'] -= $item['glovk']*5;
							$bot['sum_mfauvorot'] -= $item['glovk']*5;
							$bot['sum_mfauvorot'] -= $item['ginta']*2;

							$bot['sum_mfkrit'] -= $item['ginta']*5;
							$bot['sum_mfakrit'] -= $item['ginta']*5;
							$bot['sum_mfakrit'] -= $item['glovk']*2;

							$bot['noj'] -= $item['gnoj'];
							$bot['topor'] -= $item['gtopor'];
							$bot['dubina'] -= $item['gdubina'];
							$bot['mec'] -= $item['gmech'];							

							$bot['sum_mfkrit'] -= $item['mfkrit'];
							$bot['sum_mfakrit'] -= $item['mfakrit'];
							$bot['sum_mfuvorot'] -= $item['mfuvorot'];
							$bot['sum_mfauvorot'] -= $item['mfauvorot'];

							$bot['sum_bron1'] -= $item['bron1'];
							$bot['sum_bron2'] -= $item['bron2'];
							$bot['sum_bron3'] -= $item['bron3'];
							$bot['sum_bron4'] -= $item['bron4'];
							$bot['at_cost'] -= $item['cost'];
							unset($v['dressed']);
						}

						if (!isset($v['dressed']) && isset($v['best']) && $bot[$k] == 0) {
							//EchoLog("Dressing best");
							// содеваем шмотку
							$bot[$k] = $v['best']['id'];
							$item = $v['best'];
							$drsl[] = $item['id'];
							EchoLog("Dressing: ".$item['prototype'].":".$item['name']);
							                            
							$bot['maxhp'] += $item['ghp'];
							$bot['sum_minu'] += $item['minu'];
							$bot['sum_maxu'] += $item['maxu'];

							$bot['sila'] += $item['gsila'];
							$bot['lovk'] += $item['glovk'];
							$bot['inta'] += $item['ginta'];
							$bot['intel'] += $item['gintel'];
							$bot['mudra'] += $item['gmp'];

							$bot['sum_mfuvorot'] += $item['glovk']*5;
							$bot['sum_mfauvorot'] += $item['glovk']*5;
							$bot['sum_mfauvorot'] += $item['ginta']*2;

							$bot['sum_mfkrit'] += $item['ginta']*5;
							$bot['sum_mfakrit'] += $item['ginta']*5;
							$bot['sum_mfakrit'] += $item['glovk']*2;

							$bot['noj'] += $item['gnoj'];
							$bot['topor'] += $item['gtopor'];
							$bot['dubina'] += $item['gdubina'];
							$bot['mec'] += $item['gmech'];							

							$bot['sum_mfkrit'] += $item['mfkrit'];
							$bot['sum_mfakrit'] += $item['mfakrit'];
							$bot['sum_mfuvorot'] += $item['mfuvorot'];
							$bot['sum_mfauvorot'] += $item['mfauvorot'];

							$bot['sum_bron1'] += $item['bron1'];
							$bot['sum_bron2'] += $item['bron2'];
							$bot['sum_bron3'] += $item['bron3'];
							$bot['sum_bron4'] += $item['bron4'];

							$bot['at_cost'] += $item['cost'];
						}
					}
				}
				if ($bot['hp'] > $bot['maxhp']) $bot['hp'] = $bot['maxhp'];

				// обновляем бота:
				if (count($udrsl) || count($drsl)) {
					$q = mysql_query('START TRANSACTION');
					if ($q === false) return false;

					// EchoLog(print_r($bot,true));
	
					EchoLog('Updating items...');
					$q = mysql_query('UPDATE users_clons 
						SET
							sila = '.$bot['sila'].',
							lovk = '.$bot['lovk'].',
							inta = '.$bot['inta'].',
							intel = '.$bot['intel'].',
							mudra = '.$bot['mudra'].',
							noj = '.$bot['noj'].',
							mec = '.$bot['mec'].',
							topor = '.$bot['topor'].',
							dubina = '.$bot['dubina'].',
							maxhp = '.$bot['maxhp'].',
							hp = '.$bot['hp'].',
							sergi = '.$bot['sergi'].',
							kulon = '.$bot['kulon'].',
							perchi = '.$bot['perchi'].',
							weap = '.$bot['weap'].',
							bron = '.$bot['bron'].',
							r1 = '.$bot['r1'].',
							r2 = '.$bot['r2'].',
							r3 = '.$bot['r3'].',
							helm = '.$bot['helm'].',
							shit = '.$bot['shit'].',
							boots = '.$bot['boots'].',
							nakidka = '.$bot['nakidka'].',
							at_cost = '.$bot['at_cost'].',
							sum_minu = '.$bot['sum_minu'].',
							sum_maxu = '.$bot['sum_maxu'].',
							sum_mfkrit = '.$bot['sum_mfkrit'].',
							sum_mfakrit = '.$bot['sum_mfakrit'].',
							sum_mfuvorot = '.$bot['sum_mfuvorot'].',
							sum_mfauvorot = '.$bot['sum_mfauvorot'].',
							sum_bron1 = '.$bot['sum_bron1'].',
							sum_bron2 = '.$bot['sum_bron2'].',
							sum_bron3 = '.$bot['sum_bron3'].',
							sum_bron4 = '.$bot['sum_bron4'].',
							rubashka = '.$bot['rubashka'].'
						WHERE id = '.$bot['id']
					);
					if ($q === false) return false;
	
					//EchoLog('Updating items ok...');
	
					// инвентарь фиксим
					if (count($udrsl)) {
						$q = mysql_query('UPDATE oldbk.inventory SET dressed = 0 WHERE id IN ('.implode(",",$udrsl).') and owner = '.$bot['id']);
						if ($q === false) return false;	
					}
					if (count($drsl)) {
						$q = mysql_query('UPDATE oldbk.inventory SET dressed = 1 WHERE id IN ('.implode(",",$drsl).') and owner = '.$bot['id']);
						if ($q === false) return false;	
					}
					$q = mysql_query('COMMIT');
					if ($q === false) return false;	
				}
				//EchoLog(serialize($bot));
			}

			//EchoLog("Moving?");

			// двигаемся ботом если таймаута нет
			// проверяем есть ли путы
			$q = mysql_query('SELECT * FROM `effects` WHERE `type` = 10 AND `time` >= '.time().' AND `owner` = '.$bot['id']);
			if ($q === false) return false;

			if (mysql_num_rows($q) == 0 && $ses['time'] <= time()) {
				//EchoLog("Moving");
				// пут нету
				$paths = array();
				for ($i = 1; $i <=4; $i++) {
					if ($dt_rooms[$bot['bot_room']][$i]) {
						$paths[] = $dt_rooms[$bot['bot_room']][$i];
					}
				}
				shuffle($paths);
				$newroom = array_shift($paths);
				EchoLog("Moving, new room: ".$newroom);

		   		$q = mysql_query('UPDATE `users_clons` SET `bot_room` = '.($newroom).' WHERE `id` = '.$bot['id'].' AND `battle` = 0');
				if ($q === false) return false;
				if (mysql_affected_rows() > 0) {
					$ses['time'] = time() + $dt_rooms[$newroom][5]; // штраф на хождение той комнату КУДА мы перешли
					SaveSerFile($frrelpath.$bot['id'],$ses);

					// ушел из комнаты, в $bot['bot_room'] еще старая комната
					$mids = array();
					$list = mysql_query('SELECT * FROM `users` WHERE `room` = '.$bot['bot_room'].' AND `in_tower` = 15');
					if ($list === false) return false;
					while($u = mysql_fetch_assoc($list)) {
						$mids[] = $u['id'];
					}
					if (count($mids)) addch_group('<font color=red>Внимание!</font> <B>'.$bot['login'].'</B> отправился в <B>'.$dt_rooms[$newroom][0].'</B>.',$mids);

					// пришел в комнату
					$mids = array();
					$list = mysql_query('SELECT * FROM `users` WHERE `room` = '.($newroom).' AND `in_tower` = 15');
					if ($list === false) return false;
					while($u = mysql_fetch_assoc($list)) {
						$mids[] = $u['id'];
					}
					if (count($mids)) addch_group('<font color=red>Внимание!</font> <B>'.$bot['login'].'</B> вошел в комнату.',$mids);

					$bot['bot_room'] = $newroom;

			
					// обработка ловушек, type - 1 ловушка
					$q = mysql_query('START TRANSACTION');
					if ($q === false) return false;
					$q = mysql_query('SELECT * FROM dt_items WHERE type = 1 AND room = '.$bot['bot_room'].' AND extra <> '.$bot['id'].' FOR UPDATE');
					if ($q === false) return false;

					if (mysql_num_rows($q) > 0) {
						$trap = mysql_fetch_assoc($q);

						// снимаем 50% хп
						if($bot['maxhp']/$bot['hp'] < 3) {
							$newhp = round($bot['hp']/2); 
						} else {
							$newhp = $bot['hp']; 
						}


						$q = mysql_query('UPDATE users_clons SET `hp` = '.$newhp.', `fullhptime` = '.time().' WHERE `id` = '.$bot['id']);
						if ($q === false) return false;

						$nomove = mt_rand(1,5);
						$nomovetime = time()+$nomove*60;

						if ($trap['extra'] > _BOTSEPARATOR_) {
							$q = mysql_query('SELECT * FROM `users_clons` WHERE `id` = "'.$trap['extra'].'"');
							if ($q === false) return false;
							$trap_owner = mysql_fetch_assoc($q);
						} else {
							$q = mysql_query('SELECT * FROM `users` WHERE `id` = "'.$trap['extra'].'"');
							if ($q === false) return false;
							$trap_owner = mysql_fetch_assoc($q);
						}
						EchoLog("Getting trap: ".$nomove);
						$q = addch("<img src=i/magic/trap.gif> {$bot[login]} угодил в ловушку.. Парализован на {$nomove} минут...");
						if ($q === false) return false;
						$q = addchp('<font color=red>Внимание!</font> <B>'.$bot['login'].'</B> попал в вашу ловушку в '.$dt_rooms[$newroom][0].'. Парализован на '.$nomove.' минут...','{[]}'.$trap_owner['login'].'{[]}',-1,$trap_owner['id_city']);
						if ($q === false) return false;

						// напоролись на ловушку
						$log = '<span class=date2>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($bot).'</b> напоролся на ловушку, установленную <b>'.nick_hist($trap_owner).'</b><BR>';
						$q = mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']);
						if ($q === false) return false;

						$q = mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ('.$bot['id'].',"Путы",'.$nomovetime.',10)');
						if ($q === false) return false;
						$q = mysql_query('DELETE FROM dt_items where id = '.$trap['id']);
						if ($q === false) return false;
					}
					$q = mysql_query('COMMIT');
					if ($q === false) return false;
				} else {
					EchoLog("Moving fail, battle ?");
					continue; //procceing next
				}
			}

			// список людей-ботов в комнате
			$usrlist = array();
			$q = mysql_query('SELECT * FROM users WHERE room = '.$bot['bot_room']);
			if ($q === false) return false;
			while($u = mysql_fetch_assoc($q)) {
				$u['bot_room'] = $u['room'];
				$usrlist[] = $u;
			}
			$q = mysql_query('SELECT * FROM users_clons WHERE bot_room = '.$bot['bot_room'].' and id <> '.$bot['id']);
			if ($q === false) return false;
			while($u = mysql_fetch_assoc($q)) {
				$u['room'] = $u['bot_room'];
				$usrlist[] = $u;
			}
			shuffle($usrlist);

			if (!(($map['starttime'] + $attacktime) > time()) && count($usrlist)) {
				// можем напасть
				$usr = $usrlist[mt_rand(0,count($usrlist)-1)];

				if (mt_rand(1,100) >= 80) {
					EchoLog("Bot attack from button");
					if (bot_attack($bot,$usr,$map)) {
						return 3;
					} else {
						return false;
					}
				}
			}	

			// юзаем свитки вне боя

			// молчанки 15,30
			// путы
			// ловушки
			// кулачка

			reset($scrolls);
			while(list($k,$v) = each($scrolls)) {
				$used = 0;

				switch($v['prototype']) {
					case 194194:
						// ловушка
						$q = mysql_query('SELECT * FROM dt_items WHERE type = 1 AND room = '.$bot['bot_room']);
						if ($q === false) return false;
						if (mysql_num_rows($q) == 0) {
							EchoLog("Bot trap");
							$q = mysql_query('INSERT INTO `dt_items` (type,name,img,room,extra) VALUES ("1","Ловушка","",'.$bot['bot_room'].','.$bot['id'].')');
							if ($q === false) return false;
							$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
							if ($q === false) return false;
							unset($scrolls[$k]);
							$used = 1;
						}
					break;
					case 171:
						// кулачка
						if (!(($map['starttime'] + $attacktime) > time()) && count($usrlist) && $arch_topweapon == false && $arch_fullitems != 13) {

							$usr = $usrlist[mt_rand(0,count($usrlist)-1)];
							$usr_fullitemscost = 0;

							$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$usr['id'].' AND dressed = 1 and type IN (1,2,3,4,5,6,7,8,9,10,11,27,28)');

							while($i = mysql_fetch_assoc($q)) {
								$usr_fullitemscost += $i['cost'];
							}

							$arch_fullitemscost = $arch_fullitemscost * 1.6; // на 60 % дороже

							if ($usr_fullitemscost > $arch_fullitemscost && mt_rand(1,100) >= 60) {
								EchoLog("Bot kulak");
								if (bot_attack($bot,$usr,$map,1)) {
									$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
									if ($q === false) return false;
									return 3;
								} else {
									return false;
								}
							}
						}
					break;
					case 121:
						// путы
						if (!(($map['starttime'] + $attacktime) > time()) && count($usrlist)) {
							if (mt_rand(1,100) >= 50) {
								$usr = $usrlist[mt_rand(0,count($usrlist)-1)];
								$q = mysql_query('SELECT * FROM `effects` WHERE `owner` = '.$usr['id'].' and `type` = 10'); 
								if ($q === false) return false;	

								if (mysql_num_rows($q) == 0) {
									EchoLog("Bot stop");
									$q = mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('.$usr['id'].',"Путы",'.(time()+600).',10)');
									if ($q === false) return false;
									$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
									if ($q === false) return false;

									$messch="Персонаж &quot;{$bot[login]}&quot; наложил путы на &quot;{$usr[login]}&quot;";
									addch("<img src=i/magic/chains.gif> $messch",$usr['bot_room'],$usr['id_city']);						

									unset($scrolls[$k]);
									$used = 1;
								}
							}
						}
					break;
					case 102: case 103:
						// молчи
						if (count($usrlist)) {
							$usr = $usrlist[mt_rand(0,count($usrlist)-1)];
							if ($usr['id'] < _BOTSEPARATOR_) {
								$q = mysql_query('SELECT * FROM `effects` WHERE type = 2 AND `owner` = '.$usr['id']);
								if ($q === false) return false;
								if (mysql_num_rows($q) == 0) {
									EchoLog("Bot sleep");
									$tt = 900;
									if ($v['prototype'] == 103) $tt = 1800;

									addch("<img src=i/magic/sleep.gif>Персонаж &quot;{$bot[login]}&quot; наложил заклятие молчания на &quot;{$usr['login']}&quot;, сроком ".($tt/60)." мин.",$usr['bot_room'],$usr['id_city']);
		
									$q = mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$usr['id']."','Заклятие молчания',".(time()+$tt).",2)");
									if ($q === false) return false;
									$q = mysql_query("UPDATE users set slp = 1 where id = ".$usr['id']);
									if ($q === false) return false;

									$q = mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$v['id'].' and owner = '.$bot['id']);
									if ($q === false) return false;
									unset($scrolls[$k]);
									$used = 1;
								}
							}
						}
					break;
				}

				if ($used) break; // не более одного свитка за проход
			}


			// проверка передачи чека
			// только у главного арха
			if (true) {
			/*if (strpos($bot['login'],'Архивaриус') !== false) {*/
				// делаем транзой
				$q = mysql_query('START TRANSACTION');
				if ($q === false) return false;
				$q = mysql_query('SELECT * FROM users_clons WHERE id = '.$bot['id'].' AND battle = 0 AND hp > 0 FOR UPDATE');
				if ($q === false) return false;
				if (mysql_num_rows($q) > 0) {
					reset($archilist);
					while(list($k,$v) = each($archilist)) {
						if ($v['prototype'] == 114 && !empty($v['present'])) {
							EchoLog("Found check for cashing");
							$q = mysql_query('SELECT * FROM users WHERE login = "'.$v['present'].'"');
							if ($q === false) return false;
							if (mysql_num_rows($q) > 0) {
								$usr = mysql_fetch_assoc($q);
								EchoLog("Found user: ".$usr['login']);
								// попытка нападения
								if ($usr['room'] == $bot['bot_room']) {
									EchoLog('Presenter in room');
									if (mt_rand(1,100) >= 65) {
										EchoLog("Bot attack presenter");
	
										$q = mysql_query('COMMIT');
										if ($q === false) return false;
	
										if (bot_attack($bot,$usr,$map,0,1)) {
											return 3;
										} else {
											return false;
										}
									} else {
										EchoLog("Good cashing");
										$pr_count = explode(' ',$v['name']);
										$pr_count[3] = (int)$pr_count[3];
			
										$q = mysql_query("UPDATE `users` SET `money` = `money`+ ".$pr_count[3]." WHERE `id` = ".$usr['id']);
										if ($q === false) return false;
										$q = addchp ('<font color=red>Внимание!</font> <B>"'.$bot['login'].'"</B> передал вам <B>'.$pr_count[3].'.00 кр</B>.  ','{[]}'.$usr['login'].'{[]}',$usr['room'],$usr['id_city']);
										if ($q === false) return false;
	
										$rec = array();
										$rec['owner'] = $usr['id'];
										$rec['owner_login']=$usr['login'];
										$rec['owner_balans_do'] = $usr['money'];
										$usr['money'] += $pr_count[3];
										$rec['owner_balans_posle'] = $usr['money'];
										$rec['sum_kr'] = $pr_count[3];
										$rec['target'] = 0;
										$rec['target_login'] = 'БС';
										$rec['type'] = 102;//Получение выйгрыша
										$q = add_to_new_delo($rec);
										if ($q === false) return false;
	
										$q = mysql_query('UPDATE dt_log SET `log` = CONCAT(`log`,\''."<span class=date>".date("d.m.y H:i")."</span>  ".mysql_real_escape_string(nick_align_klan($usr))." обналичил чек на <B>".$pr_count[3]." кр.</B><BR>".'\') WHERE `dt_id` = '.$map['id']);
										if ($q === false) return false;
										mysql_query('DELETE FROM oldbk.`inventory` WHERE id = '.$v['id'].' and owner = '.$bot['id']);
										if ($q === false) return false;
									}
								} else {
									EchoLog("User not in bot room: ".$usr['room'].":".$bot['bot_room']);
								}
							}
						}
					}
				}
				$q = mysql_query('COMMIT');
				if ($q === false) return false;
			}
		}
	}
	return 3;
}

while(TRUE) {
	$r = CronArchRun();
	if ($r === FALSE) {
		echo date("d/m/Y H:i:s").": Base error signal ".mysql_error()."\n";
		break;
	} elseif ($r === 3) {
		Sleep(3);
	} elseif ($r === 4) {
		Sleep(3);
	} elseif ($r === TRUE) {
		continue;
	}
	if (file_exists('/www/capitalcity.oldbk.com/cron/cron_dtarch_stop')) {
		echo "Stop signal\n";
		break;
	}
}

addchp ('<font color=red>Внимание!</font> arch stop','{[]}Десятый{[]}',-1,-1);
addchp ('<font color=red>Внимание!</font> arch stop','{[]}Bred{[]}',-1,-1);


lockDestroy("cron_dt_arch");
?>