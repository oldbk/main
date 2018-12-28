<?php
	include "/www/capitalcity.oldbk.com/cron/init.php";
	require_once('/www/capitalcity.oldbk.com/functions.php');
	require_once('/www/capitalcity.oldbk.com/castles_functions.php');
	require_once('/www/capitalcity.oldbk.com/castles_config.php');
	require_once('/www/capitalcity.oldbk.com/clan_kazna.php');
	require_once('/www/capitalcity.oldbk.com/memcache.php');

	if(!lockCreate("cron_castles") ) {
	    	exit("Script already running.");
	}

	function MyDie($txt) {
		echo $txt."\n";
		return FALSE;
	}

	function EchoLog($txt) {
		echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
	}

	function ProcessCastleBattle($c) {
		// процессим старты финального боя
		EchoLog("found castle to start battle for own: ".$c['id']." : ".serialize($c));
		$alist = array();
		$dlist = array();
		$preroom = 70000+$c['id'];

		$asql = "";
		$asecond = CGetSecondClan2($c['clanashort1']);
		if ($asecond == 3) return false;
		if ($asecond !== FALSE) $asql = ' OR klan = "'.$asecond.'"';

		$dsql = "";
		$dsecond = CGetSecondClan2($c['clanashort2']);
		if ($dsecond == 3) return false;
		if ($dsecond !== FALSE) $dsql = ' OR klan = "'.$dsecond.'"';

		// ищем команду 1
		$q = mysql_query('SELECT * FROM oldbk.users WHERE id_city = 0 AND (room = '.$preroom.') AND (klan = "'.$c['clanashort1'].'" '.$asql.') AND level = '.$c['nlevel'].' AND battle = 0 AND hp > 1 AND in_tower = 16 LIMIT 5');
		if ($q === FALSE) return FALSE;

		// ищем команду 2
		$q2 = mysql_query('SELECT * FROM oldbk.users WHERE id_city = 0 AND (room = '.$preroom.') AND (klan = "'.$c['clanashort2'].'" '.$dsql.') AND level = '.$c['nlevel'].' AND battle = 0 AND hp > 1 AND in_tower = 16 LIMIT 5');
		if ($q2 === FALSE) return FALSE;

		if (mysql_num_rows($q) == 0 && mysql_num_rows($q2) == 0) {
			// нет ниодной команды
			EchoLog("No attackers founds in 2 teams, reset castle to normal state: ".$c['id']);
			$q = mysql_query('UPDATE oldbk.castles SET status = 0, timeouta = 0, clanashort1 = "", clanashort2 = "" WHERE id = '.$c['id']);
			if ($q === FALSE) return FALSE;
			return true;
		} elseif (mysql_num_rows($q) > 0 && mysql_num_rows($q2) == 0) {
			EchoLog("No attackers founds in team 2 castle go to team 1: ".$c['id']);			
			// нет первой команды

			// собираем людей которых надо выкинуть из одевалки
			$uall = array();
			while($uu = mysql_fetch_assoc($q)) {
				$uall[] = $uu['id'];
			}
			$res = UndressCastlesAllTrz($uall);
			if ($res === false) return false;

			$q = mysql_query('UPDATE oldbk.castles SET status = 0, lastpagegen = '.(time()+(24*3600)).', lastcoingen = '.(time()+(24*3600)).', battle = 0, clanashort1 = "", clanashort2 = "", clanshort = "'.$c['clanashort1'].'", timeouta = 0 WHERE id = '.$c['id']);
			if ($q === FALSE) return FALSE;
			$wtklan = CGetClan3($c['clanashort1']);
			if ($wtklan === FALSE) return FALSE;
			$q = WriteToCastle($c['id'],'Замок был взят без боя кланом '.$wtklan.'.');
			if ($q === FALSE) return FALSE;
			$q = mysql_query('DELETE FROM castles_inventory WHERE cid = '.$c['id']);
			if ($q === FALSE) return FALSE;
		} elseif (mysql_num_rows($q2) > 0 && mysql_num_rows($q) == 0) {
			EchoLog("No attackers founds in team 1 castle go to team 2: ".$c['id']);			
			// нет второй команды

			// собираем людей которых надо выкинуть из одевалки
			$uall = array();
			while($uu = mysql_fetch_assoc($q2)) {
				$uall[] = $uu['id'];
			}
			$res = UndressCastlesAllTrz($uall);
			if ($res === false) return false;

			$q = mysql_query('UPDATE oldbk.castles SET status = 0, lastpagegen = '.(time()+(24*3600)).', lastcoingen = '.(time()+(24*3600)).', battle = 0, clanashort1 = "", clanashort2 = "", clanshort = "'.$c['clanashort2'].'", timeouta = 0 WHERE id = '.$c['id']);
			if ($q === FALSE) return FALSE;
			$wtklan = CGetClan3($c['clanashort2']);
			if ($wtklan === FALSE) return FALSE;
			$q = WriteToCastle($c['id'],'Замок был взят без боя кланом '.$wtklan.'.');
			if ($q === FALSE) return FALSE;
			$q = mysql_query('DELETE FROM castles_inventory WHERE cid = '.$c['id']);
			if ($q === FALSE) return FALSE;
		} else {
			EchoLog("found users for 2 teams, starting battle: ".$c['id']);

			$dstr = "";
			while($u = mysql_fetch_assoc($q)) {
				$alist[] = $u;
				$dstr .= $u['id'].",";

				// снимаем слоты больше 5
				$qsnim = mysql_query('UPDATE oldbk.inventory SET dressed=0 WHERE owner='.$u['id'].' and id IN ('.$u['m11'].','.$u['m12'].','.$u['m13'].','.$u['m14'].','.$u['m15'].','.$u['m16'].','.$u['m17'].','.$u['m18'].','.$u['m19'].','.$u['m20'].')');
				if ($qsnim === false)  return false;
				$qsnim = mysql_query('UPDATE users SET m6 = 0, m7 = 0, m8 = 0, m9 = 0, m10 = 0, m11 = 0, m12 = 0, m13 = 0, m14 = 0, m15 = 0, m16 = 0, m17 = 0, m18 = 0, m19 = 0, m20 = 0 WHERE id = '.$u['id']);
				if ($qsnim === false)  return false;
			}
			EchoLog("Found team1 for ".$c['id'].":".substr($dstr,0,-1));

			$dstr = "";
			while($u = mysql_fetch_assoc($q2)) {
				$dlist[] = $u;
				$dstr .= $u['id'].",";

				// снимаем слоты больше 5
				$qsnim = mysql_query('UPDATE oldbk.inventory SET dressed=0 WHERE owner='.$u['id'].' and id IN ('.$u['m11'].','.$u['m12'].','.$u['m13'].','.$u['m14'].','.$u['m15'].','.$u['m16'].','.$u['m17'].','.$u['m18'].','.$u['m19'].','.$u['m20'].')');
				if ($qsnim === false)  return false;
				$qsnim = mysql_query('UPDATE users SET m6 = 0, m7 = 0, m8 = 0, m9 = 0, m10 = 0, m11 = 0, m12 = 0, m13 = 0, m14 = 0, m15 = 0, m16 = 0, m17 = 0, m18 = 0, m19 = 0, m20 = 0 WHERE id = '.$u['id']);
				if ($qsnim === false)  return false;

			}
			EchoLog("Found team2 for ".$c['id'].":".substr($dstr,0,-1));

			// есть обе команды
			$r = StartCastleBattleVSpl($alist,$dlist,$c['id']);				
			return $r;
		}
	}

	function ProcessStartTur($c) {
		// процессим старт одного турнира
		global $castles_config;
		EchoLog("starting tur for ".$c['castle_id'].":".serialize($c));

		$q = mysql_query('SELECT * FROM castles WHERE id = '.$c['castle_id']);
		$cid = mysql_fetch_assoc($q);

		$q = mysql_query('SELECT * FROM castles_start WHERE castle_id = '.$c['castle_id']);
		$teams = array();
		$allids = array();

		if ($c['nlevel'] == 9) {
			$arr['stats'] = 120;
			$arr['vinos'] = 13;
			$arr['master'] = 10;
		} elseif ($c['nlevel'] == 10) {
			$arr['stats'] = 140;
			$arr['vinos'] = 16;
			$arr['master'] = 11;
		} elseif ($c['nlevel'] == 11) {
			$arr['stats'] = 200;
			$arr['vinos'] = 19;
			$arr['master'] = 12;
		} elseif ($c['nlevel'] == 12) {
			$arr['stats'] = 230;
			$arr['vinos'] = 23;
			$arr['master'] = 14;
		} elseif ($c['nlevel'] == 13) {
			$arr['stats'] = 250;
			$arr['vinos'] = 24;
			$arr['master'] = 14;
		} elseif ($c['nlevel'] == 14) {
			$arr['stats'] = 300;
			$arr['vinos'] = 25;
			$arr['master'] = 14;
		}

		$arr['hp'] = $arr['vinos']*6;


		// проходимся по всем командам и проверяем сколько команд и сколько из них стартует
		while($t = mysql_fetch_assoc($q)) {
			EchoLog("checking team: ".$t['users']);
			$second = CGetSecondClan2($t['klan']);
			$ssql = "";	
			if ($second !== FALSE && $second !== 3) {
				$ssql = ' or klan = "'.$second.'"';
			}
			$q2 = mysql_query('SELECT * FROM oldbk.users WHERE id_city = 0 AND hidden = 0 AND id IN ('.$t['users'].') AND room = 72001 AND (klan = "'.$t['klan'].'" '.$ssql.') AND level = '.$c['nlevel'].' AND battle = 0 AND hp > 1 AND in_tower = 0');
			if (mysql_num_rows($q2) == 3) {
				// здесь надо сделать проверку профиля турнирного, возможно для этого придётся их уже одевать
				$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` IN ('".$t['users']."') AND (`type` >=11 AND `type` <= 14);"));
				if (!$eff[id]>0) {
					$pr = mysql_query("SELECT * from oldbk.castles_profile where def=1 and prof>0 and owner IN (".$t['users'].") GROUP BY owner");
					if (mysql_num_rows($pr) == 3) {
						// проверяем нализают ли шмотки

						$ALLOK = true;

						while($proff = mysql_fetch_assoc($pr)) {
							$arrneed = $proff; //распаковали нужный заранее загруженый проф
							$slots = array('sergi'=>0, 'kulon'=>0,'weap'=>0,'bron'=>0,'r1'=>0,'r2'=>0,'r3'=>0,'helm'=>0,'perchi'=>0,'shit'=>0,'boots'=>0,'nakidka'=>0,'rubashka'=>0);
							$rco = 0; $slqitem='';
							$arr_shop_id=array(); $coun_shop_id=0;


	 						$isila=3;
							$ilovk=3;
							$iinta=3;

							$TEST_OK=1;

							$usr = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.users WHERE id = '.$proff['owner']));

							if ($usr[klan]=='pal') { 
								$need_align=6;
							} else {
								$need_align=(int)($usr[align]);
							}		
		
							foreach($arrneed as $kn=>$kv) {
								if ($kn=='sergi'  OR  $kn=='kulon' OR $kn=='weap' OR  $kn=='bron'  OR $kn=='r1' OR $kn=='r2' OR $kn=='r3' OR	$kn=='helm' OR $kn=='perchi' OR	$kn=='shit' OR  $kn=='boots' OR $kn=='nakidka' OR $kn=='rubashka') {
									if (($kv>0) AND ($kn!='0')) {
										$get_test_ids=mysql_fetch_array(mysql_query("select * from oldbk.shop where id={$kv} and  `wopen` > 0 AND (nalign={$need_align} OR nalign=0)  AND type!=12 "));
										if ($get_test_ids>0) {
											$isila+=$get_test_ids[gsila];
											$ilovk+=$get_test_ids[glovk];
											$iinta+=$get_test_ids[ginta];
											$iintel+=$get_test_ids[gintel];
											$ihp+=$get_test_ids[ghp];
										} else {
											$TEST_OK=0;					
											break;
										}
									}
			
								}

							}

							if (!$TEST_OK) {
								EchoLog("profile problem 1: ".$usr['id']);
								$ALLOK = false;
								break;
							}
						}

						if ($ALLOK) {
							$t1 = explode(",",$t['users']);
							while(list($k,$v) = each($t1)) {
								$allids[] = $v;
							}
		
							EchoLog("team good: ".$t['users']);
							$teams[] = array('users' => $t['users'], 'klan' => $t['klan'], 'ownerklan' => $t['ownerklan'], 'lastbround' => 0);
						} else {
							EchoLog("profile problem in user (".$usr['id']."): ".$t['users']);
							$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Команда клана <b>".$t['klan']."</b> не допущена на турнир за замок ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."]. ";
							$str .= "У персонажа <b>".$usr['login']."</b> проблемы с профилем по умолчанию. ";
							addchp($str,"Дворецкий",72001);
						}
					} else {
						EchoLog("not found profile for team: ".$t['users']);
						$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Команда клана <b>".$t['klan']."</b> не допущена на турнир за замок ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."]. ";

						$tlist1 = explode(",",$t['users']);
						$badlist = array();
						while(list($ka,$va) = each($tlist1)) {
							$badlist[$va] = 1;
						}

						$pr = mysql_query("SELECT * from oldbk.castles_profile where def=1 and prof>0 and owner IN (".$t['users'].") GROUP BY owner");
						while($u = mysql_fetch_assoc($pr)) {
							unset($badlist[$u['owner']]);
						}

						$qz = mysql_query('SELECT * FROM oldbk.users WHERE id_city = 0 AND id IN ('.implode(",",array_keys($badlist)).')');
						while($u = mysql_fetch_assoc($qz)) {
							$str .= "У персонажа <b>".$u['login']."</b> нет профиля по умолчанию. ";
						}

						addchp($str,"Дворецкий",72001);
					}
				} else {
					EchoLog("found trv for team: ".$t['users']);
					$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Команда клана <b>".$t['klan']."</b> не допущена на турнир за замок ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."]. ";

					$eff = mysql_query("SELECT * FROM `effects` WHERE `owner` IN ('".$t['users']."') AND (`type` >=11 AND `type` <= 14) GROUP BY owner");
					$ulist = array();
					while($tr = mysql_fetch_assoc($eff)) {
						$ulist[] = $tr['owner'];
					}

					$qz = mysql_query('SELECT * FROM oldbk.users WHERE id_city = 0 AND id IN ('.implode(",",$ulist).')');
					while($u = mysql_fetch_assoc($qz)) {
						$str .= "Персонаж <b>".$u['login']."</b> с травмой. ";
					}
					addchp($str,"Дворецкий",72001);
				}
			} else {
				$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Команда клана <b>".$t['klan']."</b> не допущена на турнир за замок ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."]. ";

				$q2 = mysql_query('SELECT * FROM oldbk.users WHERE id_city = 0 AND id IN ('.$t['users'].')');
				while($u = mysql_fetch_assoc($q2)) {
					if ($u['room'] != 72001) $str .= "Персонаж <b>".$u['login']."</b> не в локации. ";
					if ($u['hidden'] > 0) $str .= "Персонаж <b>".$u['login']."</b> под магией невидимость или перевоплощение. ";
					if ($u['level'] != $c['nlevel']) $str .= "Персонаж <b>".$u['login']."</b> изменил уровень. ";
					if ($u['klan'] !== $t['klan'] && $u['klan'] !== $second) $str .= "Персонаж <b>".$u['login']."</b> не соотвествует клану. ";
				}

				addchp($str,"Дворецкий",72001);
				EchoLog("3 players not found: ".$t['users']);
			}
		}

		$ua = array();
		if (count($allids)) {
			reset($allids);
			$q = mysql_query('SELECT * FROM users WHERE id IN ('.implode(",",$allids).')') or mydie(mysql_error().":".__LINE__);;
			while($u = mysql_fetch_assoc($q)) {
				$ua[$u['id']] = $u;
			}
		}

		if (count($teams) == 2) {
			// всего две команды
			EchoLog("only 2 teams, set castle to final fight");
			$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Турнир за замок ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."] не состоялся. Победили без боя команды <b>".$teams[0]["klan"]."</b> и <b>".$teams[1]["klan"]."</b>. Финальный бой начнется в <b>".date("H:i",time()+600)."</b> у ворот замка ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."].</b>";
			addchp($str,"Дворецкий",72001);
			mysql_query('UPDATE castles SET status = 2, timeouta = '.(time()+600).', clanashort1 = "'.$teams[0]['klan'].'", clanashort2 = "'.$teams[1]['klan'].'" WHERE id = '.$c['castle_id']);
			mysql_query('DELETE FROM castles_inventory WHERE cid = '.$c['castle_id']);
			mysql_query('INSERT INTO castles_tur (status,data,castle_id,starttime) VALUES (3,"'.mysql_real_escape_string(serialize($teams)).'",'.$c['castle_id'].',"'.time().'")') or mydie(mysql_error().":".__LINE__);
			$id = mysql_insert_id();

			$cc = "";
			reset($teams);
			while(list($k,$v) = each($teams)) {
				$t = explode(",",$v['users']);
				$cc .= "(";
				$tmp = "";
				while(list($ka,$va) = each($t)) {
					$tmp .= nick_align_klan($ua[$va]).", ";
				}
				$tmp = substr($tmp,0,strlen($tmp)-2);			
				$cc .= $tmp;
				$cc .= "), ";
			}
	
			$cc = substr($cc,0,strlen($cc)-2);			
			// пишем в лог турнира о его начале
			$txt = '<span class=date>'.date("d.m.y H:i").'</span> Начало турнира между командами: '.$cc.'.<BR>';
			mysql_query('UPDATE castles_tur SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$id) or mydie(mysql_error().":".__LINE__);

			$txt = '<span class=date>'.date("d.m.y H:i").'</span> В турнире без боя победили команды: '.$cc.'. Финальный бой начнется в '.date("H:i",time()+600).' у ворот замка '.$castles_config[$cid['num']]['name'].' ['.$c['nlevel'].'].<BR>';
			mysql_query('UPDATE castles_tur SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$id) or mydie(mysql_error().":".__LINE__);

			mysql_query('DELETE FROM castles_start WHERE castle_id = '.$c['castle_id']);
			return true;
		} elseif (count($teams) < 2) {
			// одна команда или 0
			EchoLog("< 2 teams for fights, return money if need");
			$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Турнир за замок ".$castles_config[$cid['num']]['name']." [".$c['nlevel']."] не состоялся. При старте турнира оказалось менее 2х команд.";
			addchp($str,"Дворецкий",72001);

			reset($teams);
			while(list($k,$v) = each($teams)) {
				$clan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$v['ownerklan'].'"');
				$clan = mysql_fetch_assoc($clan);
	    			$clan_kazna=clan_kazna_have($clan['id']);
				if ($clan_kazna) {
					sell_to_kazna($clan['id'],50,"","Возврат 50 кр. в казну за несостоявшийся турнир");
				}
			}
			mysql_query('INSERT INTO castles_tur (status,data,castle_id,starttime) VALUES (4,"'.mysql_real_escape_string(serialize($teams)).'",'.$c['castle_id'].',"'.time().'")') or mydie(mysql_error().":".__LINE__);
			mysql_query('DELETE FROM castles_start WHERE castle_id = '.$c['castle_id']);
			return true;
		} else {
			// команд больше 2х всё ок, продолжаем

		}
		EchoLog("found teams: ".count($teams));

		// добавляем в таблицу запись о турнире
		$q = mysql_query('INSERT INTO castles_tur (status,data,castle_id,starttime) VALUES (0,"'.mysql_real_escape_string(serialize($teams)).'",'.$c['castle_id'].',"'.time().'")') or mydie(mysql_error().":".__LINE__);
		// раздеваем всех и выставляем параметры
		$id = mysql_insert_id();


		mysql_query('DELETE FROM castles_start WHERE castle_id = '.$c['castle_id']);

		while(list($k,$v) = each($allids)) {
			EchoLog("Processing char: ".$v);
			$rco = 0;
			mysql_query('DELETE FROM users_bonus where owner = '.$v) or mydie(mysql_error().":".__LINE__);

			// раздеваем
			undressalltrz($v) or mydie(mysql_error().":".__LINE__);

			// одеваем
			$usr = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.users WHERE id = '.$v));

			// сохраяем дефолт профиль
			$sk_row=" `sila`='{$usr[sila]}',`lovk`='{$usr[lovk]}',`inta`='{$usr[inta]}',`vinos`='{$usr[vinos]}',`intel`='{$usr[intel]}',
			`mudra`='{$usr[mudra]}',`duh`='{$usr[duh]}',`bojes`='{$usr[bojes]}',`noj`='{$usr[noj]}',`mec`='{$usr[mec]}',`topor`='{$usr[topor]}',`dubina`='{$usr[dubina]}',
			`maxhp`='{$usr[maxhp]}',`hp`='{$usr[hp]}',`maxmana`='{$usr[maxmana]}',`mana`='{$usr[mana]}',`sergi`='{$usr[sergi]}',`kulon`='{$usr[kulon]}',`perchi`='{$usr[perchi]}',
			`weap`='{$usr[weap]}',`bron`='{$usr[bron]}',`r1`='{$usr[r1]}',`r2`='{$usr[r2]}',`r3`='{$usr[r3]}',`helm`='{$usr[helm]}',`shit`='{$usr[shit]}',`boots`='{$usr[boots]}',`runa1`='{$usr[runa1]}',`runa2`='{$usr[runa2]}',`runa3`='{$usr[runa3]}',
			`stats`='{$usr[stats]}',`master`='{$usr[master]}',`nakidka`='{$usr[nakidka]}',`rubashka`='{$usr[rubashka]}',`mfire`='{$usr[mfire]}',`mwater`='{$usr[mwater]}',`mair`='{$usr[mair]}',`mearth`='{$usr[mearth]}',
			`mlight`='{$usr[mlight]}',`mgray`='{$usr[mgray]}',`mdark`='{$usr[mdark]}', `bpbonushp`='{$usr[bpbonushp]}'  ";
	
			$asql="INSERT INTO oldbk.`castles_profile` SET `owner`='{$usr[id]}',`prof`=0, ".$sk_row." ON DUPLICATE KEY UPDATE  ".$sk_row."  ; ";
			mysql_query($asql);
			echo $asql;


			$pr = mysql_query("SELECT * from oldbk.castles_profile where def=1 and prof>0 and owner = ".$v);
			$proff = mysql_fetch_assoc($pr);
			$arrneed=$proff;

			if ($usr[klan]=='pal') { 
				$need_align=6;
			} else {
				$need_align=(int)($usr[align]);
			}		

			EchoLog("Start dressing: ".$v);
			$slots = array('sergi'=>0, 'kulon'=>0,'weap'=>0,'bron'=>0,'r1'=>0,'r2'=>0,'r3'=>0,'helm'=>0,'perchi'=>0,'shit'=>0,'boots'=>0,'nakidka'=>0,'rubashka'=>0);

			reset($arrneed);
			foreach($arrneed as $kn=>$kv) {
				EchoLog("process dressing: ".$kv.":".$kn);
				if ( ($kn=='sergi' OR  $kn=='kulon' OR $kn=='weap' OR  $kn=='bron' OR
					$kn=='r1' OR $kn=='r2' OR $kn=='r3' OR	$kn=='helm' OR $kn=='perchi' OR
					$kn=='shit' OR  $kn=='boots' OR $kn=='nakidka' OR $kn=='rubashka' ) AND $kv >0) {

					$gsql="select * from oldbk.shop where id={$kv} and  `wopen` > 0 AND (nalign={$need_align} OR nalign=0) AND type!=12 ; ";
					$get_items=mysql_query($gsql);
					$it_kol=mysql_num_rows($get_items);
			 		if ($it_kol>0) {
						EchoLog("process dressing: ".$kv.":".$kn.":insert");
			 			$arrdress=mysql_fetch_array($get_items);
						//инсертим-с флагом одето
						$str=''; 
						$sql=''; 
						if($arrdress[nlevel]>6) {
							$str = ",`up_level` "; 
							$sql = ",'".$arrdress[nlevel]."' ";
						}

						$arrdress['nalign'] = 0;

						if(mysql_query("INSERT INTO oldbk.`inventory`
							( `bs_owner`, `present`, `dressed` , `prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
								`nclass`,`otdel`,`gmp`,`gmeshok`, `group`,`letter` ".$str."
							)
							VALUES
							( '10', 'Armory' , 1,'{$arrdress['id']}','{$usr[id]}','{$arrdress['name']}','{$arrdress['type']}',{$arrdress['massa']},{$arrdress['cost']},'{$arrdress['img']}',{$arrdress['maxdur']},{$arrdress['isrep']},'{$arrdress['gsila']}','{$arrdress['glovk']}','{$arrdress['ginta']}','{$arrdress['gintel']}','{$arrdress['ghp']}','{$arrdress['gnoj']}','{$arrdress['gtopor']}','{$arrdress['gdubina']}','{$arrdress['gmech']}','{$arrdress['gfire']}','{$arrdress['gwater']}','{$arrdress['gair']}','{$arrdress['gearth']}','{$arrdress['glight']}','{$arrdress['ggray']}','{$arrdress['gdark']}','0','{$arrdress['nsila']}','{$arrdress['nlovk']}','{$arrdress['ninta']}','{$arrdress['nintel']}','{$arrdress['nmudra']}','{$arrdress['nvinos']}','{$arrdress['nnoj']}','{$arrdress['ntopor']}','{$arrdress['ndubina']}','{$arrdress['nmech']}','{$arrdress['nfire']}','{$arrdress['nwater']}','{$arrdress['nair']}','{$arrdress['nearth']}','{$arrdress['nlight']}','{$arrdress['ngray']}','{$arrdress['ndark']}',
							'{$arrdress['mfkrit']}','{$arrdress['mfakrit']}','{$arrdress['mfuvorot']}','{$arrdress['mfauvorot']}','{$arrdress['bron1']}','{$arrdress['bron2']}','{$arrdress['bron3']}','{$arrdress['bron4']}','{$arrdress['maxu']}','{$arrdress['minu']}','{$arrdress['magic']}','{$arrdress['nlevel']}',
							'{$arrdress['nalign']}','".(($arrdress['goden'])?($arrdress['goden']*24*60*60+time()):"")."','{$arrdress['goden']}'
							,4,'{$arrdress['razdel']}','{$arrdress['gmp']}','{$arrdress['gmeshok']}','{$arrdress['group']}','{$arrdress['letter']}' ".$sql."
							) ;"))
						{
							$nid = mysql_insert_id();
							EchoLog("process dressing: ".$kv.":".$kn.":insert:".$nid);
							/////////////////////////////////////////////////////////////////////////////////////////////
							//потом определяем что за поле и прописываем чару
							switch($arrdress['type']) {
								case 1: $slots['sergi']=$nid; break;
								case 2: $slots['kulon']=$nid; break;
								case 3: $slots['weap']=$nid; break;
								case 4: $slots['bron']=$nid; break;
								case 5:
								{
									$rco++;	$tmp='r'.$rco;
									$slots[$tmp]=$nid; break;
								}
								case 8: $slots['helm']=$nid; break;
								case 9: $slots['perchi']=$nid; break;
								case 10: $slots['shit']=$nid; break;
								case 11: $slots['boots']=$nid; break;
								case 27: $slots['nakidka']=$nid; break;
								case 28: $slots['rubashka']=$nid; break;				
							}
						}
			 		}
		  		}
			}
			EchoLog("end dressing: ".$v);
			reset($slots);
			$slqitem = "";
			foreach($slots as $kn=>$kv) {
				$slqitem.=" ".$kn."=".$kv." , ";
			}

			///апдейтим чара
			$asql="UPDATE `users` SET
				`users`.`sila`={$arrneed[sila]},`users`.`lovk`={$arrneed[lovk]},`users`.`inta`={$arrneed[inta]},`users`.`vinos`={$arrneed[vinos]},`users`.`bpbonushp`=0,
				`users`.`intel`={$arrneed[intel]},`users`.`mudra`={$arrneed[mudra]},`users`.`duh`={$arrneed[duh]},`users`.`bojes`={$arrneed[bojes]},
				`users`.`noj`={$arrneed[noj]},`users`.`mec`={$arrneed[mec]},`users`.`topor`={$arrneed[topor]},`users`.`dubina`={$arrneed[dubina]},
				`users`.`maxhp`={$arrneed[maxhp]},`users`.`hp`={$arrneed[maxhp]},`users`.`maxmana`={$arrneed[maxmana]},`users`.`mana`={$arrneed[mana]},
				 ".$slqitem."
				`users`.`stats`='{$arrneed[stats]}',`users`.`master`='{$arrneed[master]}',`users`.`mfire`={$arrneed[mfire]},`users`.`mwater`={$arrneed[mwater]},
				`users`.`mair`={$arrneed[mair]},`users`.`mearth`={$arrneed[mearth]},`users`.`mlight`={$arrneed[mlight]},`users`.`mgray`={$arrneed[mgray]},`users`.`mdark`={$arrneed[mdark]}
				 WHERE  `users`.`id`  = '{$usr[id]}' ;";
			EchoLog($asql);
			mysql_query($asql) or mydie(mysql_error().":".__LINE__);


		}
		mysql_query('UPDATE `users` SET `in_tower` = 10, `id_grup` = '.$id.' WHERE `id` IN ('.implode(",",$allids).')') or mydie(mysql_error().":".__LINE__);

		// выставляем статус замку
		mysql_query('UPDATE `castles` SET `status` = 1, `tur_log` = '.$id.' WHERE `id` = '.$c['castle_id']) or mydie(mysql_error().":".__LINE__);

		$cc = "";
		reset($teams);
		while(list($k,$v) = each($teams)) {
			$t = explode(",",$v['users']);
			$cc .= "(";
			$tmp = "";
			while(list($ka,$va) = each($t)) {
				$tmp .= nick_align_klan($ua[$va]).", ";
			}
			$tmp = substr($tmp,0,strlen($tmp)-2);			
			$cc .= $tmp;
			$cc .= "), ";
		}

		$cc = substr($cc,0,strlen($cc)-2);			
		// пишем в лог турнира о его начале
		$txt = '<span class=date>'.date("d.m.y H:i").'</span> Начало турнира между командами: '.$cc.'.<BR>';
		mysql_query('UPDATE castles_tur SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$id) or mydie(mysql_error().":".__LINE__);
			
		EchoLog("tur started");
		return true;
	}

	function cmpcround($a, $b) {
		if ($a['lastbround'] == $b['lastbround']) {
			return 0;
		}
		return ($a['lastbround'] < $b['lastbround']) ? -1 : 1;
	}


	function CronProcessCastlesTurs() {
		// выбираем все активные турниры
		global $castles_config;
		$q = mysql_query('SELECT * FROM castles_tur WHERE status = 0') or mydie(mysql_error().":".__LINE__);;
		while($t = mysql_fetch_assoc($q)) {
			EchoLog("Processing active tur: ".$t['id'].":".serialize($t));
			// проверяем команды чтобы небыли в бою, если не в бою - запускаем новый круг боёв
			$teams = unserialize($t['data']);
			$allids = array();
			while(list($k,$v) = each($teams)) {
				$t1 = explode(",",$v['users']);
				while(list($ka,$va) = each($t1)) {
					$allids[] = $va;
				}
			}

			$q2 = mysql_query('SELECT * FROM users WHERE id IN ('.implode(",",$allids).') and battle > 0') or mydie(mysql_error().":".__LINE__);;

			if (mysql_num_rows($q2) == 0) {
				$q2 = mysql_query('SELECT * FROM users WHERE id IN ('.implode(",",$allids).')') or mydie(mysql_error().":".__LINE__);;

				$ua = array();
				while($u = mysql_fetch_assoc($q2)) {
					$ua[$u['id']] = $u;
				}

				// если осталось две конмады, то заканчиваем турнир, проигравших выкидываем в fsystem
				EchoLog("tur check teams");
				if (count($teams) == 2) {
					EchoLog("2 teams left, setting finish battle");
					// выкидываем тела из турнира и проставяем финиш
					mysql_query('UPDATE castles SET status = 2, timeouta = '.(time()+600).', clanashort1 = "'.$teams[0]['klan'].'", clanashort2 = "'.$teams[1]['klan'].'" WHERE id = '.$t['castle_id']) or mydie(mysql_error().":".__LINE__);;
					mysql_query('DELETE FROM castles_inventory WHERE cid = '.$t['castle_id']);
					mysql_query('UPDATE castles_tur SET status = 1 WHERE id = '.$t['id']) or mydie(mysql_error().":".__LINE__);;

					$cid = mysql_query('SELECT * FROM castles WHERE id = '.$t['castle_id']);
					$cid = mysql_fetch_assoc($cid);

					$str = "<img src=/i/magic/castle_icon.png> <font color=red>Внимание!</font> Турнир за замок ".$castles_config[$cid['num']]['name']." [".$cid['nlevel']."] окончен. Победили команды <B>".$teams[0]['klan']."</B> и <B>".$teams[1]['klan']."</B>. Финальный бой начнется в ".date("H:i",time()+600)." у ворот замка ".$castles_config[$cid['num']]['name']." [".$cid['nlevel']."].";
					addchp($str,"Дворецкий",72001);

					// пишем в лог победителей и в поле winners тоже
					$txt = '<span class=date>'.date("d.m.y H:i").'</span> В турнире победили команды: '.CGetClan2($teams[0]['klan']).' и '.CGetClan2($teams[1]['klan']).'. Финальный бой начнется в '.date("H:i",time()+600).' у ворот замка '.$castles_config[$cid['num']]['name'].' ['.$cid['nlevel'].'].<BR>';
					mysql_query('UPDATE castles_tur SET status = 1, winners = "'.mysql_real_escape_string(CGetClan2($teams[0]['klan']).', '.CGetClan2($teams[1]['klan'])).'", `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$t['id']) or mydie(mysql_error().":".__LINE__);

					// снимаем шмот
					reset($teams);
					while(list($k,$v) = each($teams)) {
						$t1u = explode(",",$v['users']);
						while(list($ka,$va) = each($t1u)) {
							CastleExitDress($ua[$va]);
						}
					}

					continue;
				}

				EchoLog("tur random start");
				shuffle($teams);
				usort($teams, "cmpcround");

				EchoLog("tur battle start");
				for ($i = 0; $i < count($teams); $i+=2) {
					if (isset($teams[$i],$teams[$i+1])) {
						EchoLog("tur battle go");
						$t1 = explode(",",$teams[$i]['users']);
						$t2 = explode(",",$teams[$i+1]['users']);

						$tf1 = array();
						while(list($k,$v) = each($t1)) {
							$tf1[] = $ua[$v];
						}

						$tf2 = array();
						while(list($k,$v) = each($t2)) {
							$tf2[] = $ua[$v];
						}

						// стартуем бой
						$id = StartCastleBattleVSpl2($tf1,$tf2);

						// пишем лог
						$cc = "(".nick_align_klan($ua[$t1[0]]).", ".nick_align_klan($ua[$t1[1]]).", ".nick_align_klan($ua[$t1[2]]).") и (".nick_align_klan($ua[$t2[0]]).", ".nick_align_klan($ua[$t2[1]]).", ".nick_align_klan($ua[$t2[2]]).")";

						$txt = '<span class=date>'.date("d.m.y H:i").'</span> Начало боя между командами: '.$cc.'. <a target="_blank" href="logs.php?log='.$id.'">&gt;&gt;</a><BR>';
						mysql_query('UPDATE castles_tur SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$t['id']) or mydie(mysql_error().":".__LINE__);
						$teams[$i]['lastbround'] = $t['lastround']+1;
						$teams[$i+1]['lastbround'] = $t['lastround']+1;
					} else {
						EchoLog("tur battle no team");
						if (isset($teams[$i])) {
							$t1 = explode(",",$teams[$i]['users']);

							// пишем лог
							$cc = "(".nick_align_klan($ua[$t1[0]]).", ".nick_align_klan($ua[$t1[1]]).", ".nick_align_klan($ua[$t1[2]]).")";

							$txt = '<span class=date>'.date("d.m.y H:i").'</span> Команда '.$cc.' ожидает следующий раунд.<BR>';
							mysql_query('UPDATE castles_tur SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$t['id']) or mydie(mysql_error().":".__LINE__);
						}
						// не делаем ничего
						// одна команда не при делах ,не делаем ничего
					}
				}

				// обновляем data в турнире
				EchoLog("tur data update");
				$q2 = mysql_query('START TRANSACTION');
				$q2 = mysql_query('SELECT * FROM castles_tur WHERE id = '.$t['id'].' FOR UPDATE');
				if ($q2 !== FALSE) {
					mysql_query('UPDATE castles_tur SET data = "'.mysql_real_escape_string(serialize($teams)).'", lastround = lastround + 1 WHERE id = '.$t['id']);
				}
				$q2 = mysql_query('COMMIT');	                                                 

				EchoLog("new tur round starting");
			} else {
				EchoLog("tur in process, battle > 0 in users list");
			}
		}
	}

	function CronCastlesTur() {
		//  обрабатываем старты турниров
		$q = mysql_query('SELECT * FROM castles_start LEFT JOIN castles ON castles_start.castle_id = castles.id GROUP BY castle_id');
		while ($c = mysql_fetch_assoc($q)) {
			if (($c['dayofweek'] == date("N") && ($c['hourofday']+1) == date("G")) || ($c['dayofweek']+1 == date("N") && ($c['hourofday']+1) == 24)) {
				EchoLog("Try to start: ".serialize($c));
				if(ProcessStartTur($c) === FALSE) break;
				break;
			}
		}
	}

	function CronCastles() {
		// выбираем старты боёв за финальный бой замков
		while(true) {
			$q = mysql_query('START TRANSACTION');
			if ($q === FALSE) return FALSE;
			$q = mysql_query('SELECT * FROM oldbk.castles WHERE status = 2 AND timeouta <= '.time().' AND battle = 0 LIMIT 1 FOR UPDATE');
			if (mysql_num_rows($q) > 0) {
				$c = mysql_fetch_assoc($q);        
				if($c === FALSE || ProcessCastleBattle($c) === FALSE) {
					$q = mysql_query('COMMIT');
					if ($q === FALSE) return FALSE;
					break;
				}
			} else {
				$q = mysql_query('COMMIT');
				if ($q === FALSE) return FALSE;
				break;
			}
			$q = mysql_query('COMMIT');
		}
		return true;
	}


	CronCastles();
	CronCastlesTur();
	CronProcessCastlesTurs();

	lockDestroy("cron_castles");
?>