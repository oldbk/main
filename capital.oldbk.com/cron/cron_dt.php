<?php
require_once("/www/capitalcity.oldbk.com/cron/init.php");
require_once('/www/capitalcity.oldbk.com/dt_config.php');
require_once('/www/capitalcity.oldbk.com/fsystem.php');
require_once('/www/capitalcity.oldbk.com/memcache.php');

if(!lockCreate("cron_dt")) {
	exit("Script already running.");
}

function EchoLog($txt) {
	echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
}

function MyDie($txt,$ugoodlist = array()) {
	echo time().":".$txt."\n";
	if (count($ugoodlist)) {
		EchoLog("Reset goodlist to in_tower = 0");
		mysql_query('COMMIT');
		mysql_query_100('UPDATE users SET in_tower = 0 WHERE id IN ('.implode(",",$ugoodlist).')');
	}
	lockDestroy("cron_dt");
	die();
}


$q = mysql_query('SELECT * FROM `dt_map` WHERE `active` = 1');
if ($q === false) {
	EchoLog("SQL select map error");
	lockDestroy("cron_dt");
	die();
}
if (mysql_num_rows($q)) {
	$map = mysql_fetch_assoc($q);

	//if ($map['prize'] > 10000) $map['prize'] = 10000;

	if ($map === false) {
		EchoLog("Map fetch error");
		lockDestroy("cron_dt");
		die();
	}

	// active DT exists

	// HP regeneration
	// быстрые
	EchoLog("Healing...");
	if($map['hptype'] == 1) {
		// быстрые хп
		mysql_query("UPDATE users SET hp = hp+((".time()."-fullhptime)/60)*(maxhp/10), fullhptime = ".time()." WHERE  hp < maxhp AND battle = 0 AND in_tower = 15");
		mysql_query("UPDATE users_clons SET hp = hp+((".time()."-fullhptime)/60)*(maxhp/10), fullhptime = ".time()." WHERE  hp < maxhp AND battle = 0 AND id_user = 84");
	} elseif($map['hptype'] == 2) { 
		// медленные
		mysql_query("UPDATE users SET hp = hp+((".time()."-fullhptime)/60)*(maxhp/3000), fullhptime = ".time()." WHERE  hp < maxhp AND battle = 0 AND in_tower = 15");
		mysql_query("UPDATE users_clons SET hp = hp+((".time()."-fullhptime)/60)*(maxhp/3000), fullhptime = ".time()." WHERE  hp < maxhp AND battle = 0 AND id_user = 84");
	} elseif ($map['hptype'] == 0) {
		// стандартные
		mysql_query("UPDATE users SET hp = hp+((".time()."-fullhptime)/60)*(maxhp/20), fullhptime = ".time()." WHERE  hp < maxhp AND battle = 0 AND in_tower = 15");
		mysql_query("UPDATE users_clons SET hp = hp+((".time()."-fullhptime)/60)*(maxhp/20), fullhptime = ".time()." WHERE  hp < maxhp AND battle = 0 AND id_user = 84");
	}

	mysql_query("UPDATE `users_clons` SET `hp` = `maxhp`, `fullhptime` = ".time()." WHERE  `hp` > `maxhp` AND `battle` = 0");

	// проверяем если карта пололам
	if ($map['halftype']) {
		EchoLog("Check halftype");
		//$q = mysql_query('SELECT (SELECT count(*) FROM users WHERE room IN ('.implode(",",$dt_halfleft).')) + (SELECT count(*) FROM users_clons WHERE bot_room IN ('.implode(",",$dt_halfleft).')) as allcount');
		$sql = 'SELECT count(*) as allcount FROM users WHERE room IN ('.implode(",",$dt_halfleft).')';
		EchoLog($sql);
		$q = mysql_query($sql);
		
		$r1 = mysql_fetch_assoc($q);

		//$q = mysql_query('SELECT (SELECT count(*) FROM users WHERE room IN ('.implode(",",$dt_halfright).')) + (SELECT count(*) FROM users_clons WHERE bot_room IN ('.implode(",",$dt_halfright).')) as allcount');
		$sql = 'SELECT count(*) as allcount FROM users WHERE room IN ('.implode(",",$dt_halfright).')';
		EchoLog($sql);
		$q = mysql_query($sql);
		$r2 = mysql_fetch_assoc($q);

		if ($r1 !== false && $r2 !== false) {
			if (($r1['allcount'] == 0 || $r2['allcount'] == 0) || ($r1['allcount'] == 1 && $r2['allcount'] == 1)) {
				EchoLog("Disabling halftype");
				$log = '<span class=date>'.date("d.m.y H:i").'</span> Стена разрушилась<BR>';
				mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']);
				mysql_query('UPDATE dt_map SET halftype = 0 WHERE id = '.$map['id']);
			}
		}
	}

	// проверяем окончание турнира
	EchoLog("Check for end...");


	$alive_man = mysql_fetch_assoc(mysql_query('SELECT count(*) as aliveman FROM users WHERE in_tower = 15'));
	$alive_man = $alive_man['aliveman'];
	$alive_bot = mysql_fetch_assoc(mysql_query('SELECT count(*) as alivebot FROM users_clons WHERE id_user = 84'));
	$alive_bot = $alive_bot['alivebot'];

	$newdt = 0;

	if(($map['starttime']+60*60*5) < time()) {
		EchoLog("Technical timeout in BS");
		// техтайм по боям
		$data = mysql_query('SELECT * FROM battle WHERE type = 1010 and win = 3');
		while($data_battle = mysql_fetch_array($data)) {
			EchoLog("Finishing battle: ".$data['id']);
			finish_battle(4,$data_battle,$data_battle['blood'],$data_battle['type'],$data_battle['fond'],0);
		}

		// бои закончили если были, теперь выкидываем нах из бс весь сброд
		$q = mysql_query('SELECT * FROM users WHERE in_tower = 15');
		$ulist = array();
		while($u = mysql_fetch_assoc($q)) {
			$ulist[$u['id']] = $u;
		}
		
		$realprofile = array();
		$q = mysql_query('SELECT * FROM dt_realchars WHERE owner IN ('.implode(",",array_keys($ulist)).')');
		while($pr = mysql_fetch_assoc($q)) {
			$realprofile[$pr['owner']] = $pr;
		}

		undressallfast(array_keys($ulist));

		foreach ($ulist as $v => $k) {
			mysql_query('DELETE FROM effects WHERE type = 10 AND owner = '.$v);

			// статы возвращаем и выставяем рум
			$q2 = mysql_query("select * from effects where owner='{$v}' and type>=1001 and type<=1003");
			$hp_bonus = mysql_fetch_array($q2);
			if ($hp_bonus['id'] > 0) {
				// эффект еще есть		
			} else {
				//эфекта такого уже нет!
			
				$realprofile[$v]['maxhp'] = $realprofile[$v]['maxhp']-$realprofile[$v]['bpbonushp'];
				$realprofile[$v]['bpbonushp'] = 0;

				if ($realprofile[$v]['hp'] > $realprofile[$v]['maxhp']) {
					$realprofile[$v]['hp'] = $realprofile[$v]['maxhp'];
				}
			}

			$hp = $realprofile[$v]['vinos']*6 + ($realprofile[$v]['bpbonushp']);

			mysql_query('UPDATE `users` SET 
						`sila` = "'.($realprofile[$v]['sila']+$realprofile[$v]['bpbonussila']).'",
						`lovk` = "'.$realprofile[$v]['lovk'].'",
						`inta` = "'.$realprofile[$v]['inta'].'",
						`vinos` = "'.$realprofile[$v]['vinos'].'",
						`intel` = "'.$realprofile[$v]['intel'].'",
						`mudra` = "'.$realprofile[$v]['mudra'].'",
						`stats` = "'.$realprofile[$v]['stats'].'",
						`noj` = "'.$realprofile[$v]['noj'].'",
						`mec` = "'.$realprofile[$v]['mec'].'",
						`topor` = "'.$realprofile[$v]['topor'].'",
						`dubina` = "'.$realprofile[$v]['dubina'].'",
						`mfire` = "'.$realprofile[$v]['mfire'].'",
						`mwater` = "'.$realprofile[$v]['mwater'].'",
						`mair` = "'.$realprofile[$v]['mair'].'",
						`mearth` = "'.$realprofile[$v]['mearth'].'",
						`mlight` = "'.$realprofile[$v]['mlight'].'",
						`mgray` = "'.$realprofile[$v]['mgray'].'",
						`mdark` = "'.$realprofile[$v]['mdark'].'",
						`master` = "'.$realprofile[$v]['master'].'",
						`mana` = "'.$realprofile[$v]['mana'].'",
						`maxmana` = "'.$realprofile[$v]['mana'].'",
						`maxhp` = "'.$hp.'",
						`hp` = "'.$hp.'", 
						`bpbonussila` = '.$realprofile[$v]['bpbonussila'].',
						`bpbonushp` = '.$realprofile[$v]['bpbonushp'].', `room` = 10000, in_tower = 0 WHERE `id` = '.$v
			);

			$sql = "UPDATE `dt_log` SET `log` = CONCAT(`log`,'<span class=date>".date("d.m.y H:i")."</span> ".mysql_real_escape_string(nick_align_klan($ulist[$v]))." выбывает из турнира<BR>') WHERE `dt_id` = ".$map['id'];
			mysql_query($sql);
			addchp ('<font color=red>Внимание!</font> Вы выбыли из турнира Башни Смерти по технической ничьей.', '{[]}'.$ulist[$v]['login'].'{[]}');
		}

		// выпиливаем ботов
		mysql_query('DELETE FROM users_clons WHERE id_user = 84');

		// выпиливаем весь шмот ботов и юзеров
		mysql_query('DELETE FROM inventory WHERE bs_owner = 15');

		$alive_man = 0;
		$alive_bot = 0;
		EchoLog("Processing timeout end");
	}

	if($alive_man == 1 && $alive_bot == 0) {
		// остался один джедай - выпускаем
		$usr = mysql_fetch_array(mysql_query('SELECT * FROM users WHERE `in_tower` = 15 and battle = 0'));
		if ($usr) {
			EchoLog("Last user alive: ".$usr['id']);
	
			mysql_query('INSERT INTO `dt_usersvar` (`owner`,`var`,`val`) 
						VALUES(
							'.$usr['id'].',
							"wins",
							"1"
						) 
						ON DUPLICATE KEY UPDATE
							`val` = val + 1
			');

			$exp = round($map['prize']*mt_rand(50,70));
		
			$rep = 0;
			if($exp > 10000) {
				$exp = 10000;
			}

			$realprofile = array();
			$q = mysql_query('SELECT * FROM dt_realchars WHERE owner = '.$usr['id']);
			while($pr = mysql_fetch_assoc($q)) {
				$realprofile[$usr['id']] = $pr;
			}

			undressallfast($usr['id']);

			if (mt_rand(0,100)<15)
			{
				DropBonusItem(112003,$usr,"Удача","Коллекция №2: Ангельская поступь",0,1,20,true); //Карта Архивариуса выдается за участие в БС - шанс 15% за участие (в зачет идет ТОЛЬКО участие, победа не важна)
			}

			// медальки
			$qw = mysql_query('SELECT * FROM dt_usersvar WHERE var = "wins" AND owner = '.$usr['id']);
			$ww = mysql_fetch_assoc($qw);
			if ($ww !== FALSE) {
				$ww = $ww['val'];
				$wexist = array();
				$in = str_ireplace("|","",$usr['medals']);
				$t = explode(";",$in);
				$addm = "";
				
				while(list($k,$v) = each($t)) {
					$v = trim($v);
					if ($v == "dt1" || $v == "dt2" || $v == "dt3" || $v == "dt4" || $v == "dt5") {
						$wexist[$v] = 1;
					}
				}

				if ($ww >= 25 && !isset($wexist["dt1"])) {
					$addm .= "dt1;";
				}
				if ($ww >= 50 && !isset($wexist["dt2"])) {
					$addm .= "dt2;";
				}
				if ($ww >= 100 && !isset($wexist["dt3"])) {
					$addm .= "dt3;";
				}
				if ($ww >= 150 && !isset($wexist["dt4"])) {
					$addm .= "dt4;";
				}
				if ($ww >= 250 && !isset($wexist["dt5"])) {
					$addm .= "dt5;";
				}

				if (strlen($addm)) {
					mysql_query('UPDATE users SET medals = "'.$usr['medals'].$addm.'" WHERE id = '.$usr['id']);
				}
			}

			$v = $usr['id'];


			// статы возвращаем и выставяем рум
			mysql_query('DELETE FROM effects WHERE type = 10 AND owner = '.$v);

			$q2 = mysql_query("select * from effects where owner='{$v}' and type>=1001 and type<=1003");
			$hp_bonus = mysql_fetch_array($q2);
			if ($hp_bonus['id'] > 0) {
				// эффект еще есть		
			} else {
				//эфекта такого уже нет!
		
				$realprofile[$v]['maxhp'] = $realprofile[$v]['maxhp']-$realprofile[$v]['bpbonushp'];
				$realprofile[$v]['bpbonushp'] = 0;
	
				if ($realprofile[$v]['hp'] > $realprofile[$v]['maxhp']) {
					$realprofile[$v]['hp'] = $realprofile[$v]['maxhp'];
				}
			}

			$hp = $realprofile[$v]['vinos']*6 + ($realprofile[$v]['bpbonushp']);

			mysql_query('UPDATE `users` SET 
					`sila` = "'.($realprofile[$v]['sila']+$realprofile[$v]['bpbonussila']).'",
					`lovk` = "'.$realprofile[$v]['lovk'].'",
					`inta` = "'.$realprofile[$v]['inta'].'",
					`vinos` = "'.$realprofile[$v]['vinos'].'",
					`intel` = "'.$realprofile[$v]['intel'].'",
					`mudra` = "'.$realprofile[$v]['mudra'].'",
					`stats` = "'.$realprofile[$v]['stats'].'",
					`noj` = "'.$realprofile[$v]['noj'].'",
					`mec` = "'.$realprofile[$v]['mec'].'",
					`topor` = "'.$realprofile[$v]['topor'].'",
					`dubina` = "'.$realprofile[$v]['dubina'].'",
					`mfire` = "'.$realprofile[$v]['mfire'].'",
					`mwater` = "'.$realprofile[$v]['mwater'].'",
					`mair` = "'.$realprofile[$v]['mair'].'",
					`mearth` = "'.$realprofile[$v]['mearth'].'",
					`mlight` = "'.$realprofile[$v]['mlight'].'",
					`mgray` = "'.$realprofile[$v]['mgray'].'",
					`mdark` = "'.$realprofile[$v]['mdark'].'",
					`master` = "'.$realprofile[$v]['master'].'",
					`mana` = "'.$realprofile[$v]['mana'].'",
					`maxmana` = "'.$realprofile[$v]['mana'].'",
					`maxhp` = "'.$hp.'",
					`hp` = "'.$hp.'", 
					`bpbonussila` = '.$realprofile[$v]['bpbonussila'].',
					`bpbonushp` = '.$realprofile[$v]['bpbonushp'].', `room` = 10000, in_tower = 0 WHERE `id` = '.$v
			);
	
			// выпиливаем весь шмот джедая
			mysql_query('DELETE FROM inventory WHERE bs_owner = 15');


			$prize_data = mysql_fetch_assoc(mysql_query("SELECT * FROM dt_profile WHERE `owner`='".$usr['id']."' AND  def = 1"));

			if($prize_data['prize'] == 1) {
				$exp = round($exp*0.6);
				$rep = 1;
			}

			$txt = '<font color=red>Поздравляем!</font> Вы победитель турнира Башни смерти! Получаете <b>'.$map['prize'].'</b> кр. и <b>'.($rep == 1 ? $exp. '</b> репутации' : $exp. '</b> опыта').'.';
			addchp($txt,'{[]}'.$usr['login'].'{[]}');

			// беггинер квесты, код умыча
			$luquest = mysql_fetch_assoc(mysql_query('select * from oldbk.beginers_quests_step where owner = '.$usr[id].' AND status=0 AND quest_id=107 limit 1;'));
			$last_q[$luquest[quest_id]] = $luquest;
			////////


			$rec = array();
			$rec['owner'] = $usr['id'];
			$rec['owner_login'] = $usr['login'];
			$rec['owner_balans_do'] = $usr['money'];
			$usr['money'] += $map['prize'];
			$rec['owner_balans_posle'] = $usr[money];
			if($rep == 1) {
				$rec['sum_kr'] = $map['prize'];
				$rec['sum_rep']=$exp;
				$sql_fields = 'rep = rep+"'.$exp.'", repmoney=repmoney+"'.$exp.'", ';
			} else {
				$rec['sum_kr'] = $map['prize'];
				$sql_fields = 'exp = exp+"'.$exp.'",';
	
			}
			$rec['target']=0;
			$rec['target_login']='БС';
			$rec['type']=101;
			add_to_new_delo($rec);

			$sql = "UPDATE `users` SET ".$sql_fields." money = money + ".$map['prize']." WHERE id = ".$usr['id'];
	
			if(mysql_query($sql)) {
				// код умыча
				quest_check_type_20($last_q,$usr['id'],'OFF',100,1,2);
				//
			}
		
			mysql_query('UPDATE `dt_map` SET `winner` = '.$usr['id'].', `winnerlog` = "'.mysql_real_escape_string(nick_align_klan($usr)).'", `endtime` = '.time().' , `active` = 0  WHERE `active` = 1');
			mysql_query("UPDATE `dt_log` SET `log` = CONCAT(`log`,'<span class=date>".date("d.m.y H:i")."</span> Турнир завершен. Победитель: ".mysql_real_escape_string(nick_align_klan($usr))." Приз: <B>".$map['prize']."</B> кр. и <B>".$exp."</B> опыта.<BR>') WHERE `dt_id` = ".$map['id']);
			
			mysql_query("INSERT INTO oldbk.users_progress set owner='{$usr['id']}', awinbs=1 ON DUPLICATE KEY UPDATE awinbs=awinbs+1");
			
			$newdt = 1;

			try {
				$User = new \components\models\User($usr);
				$Quest = $app->quest
					->setUser($User)
					->get();
				$Checker = new \components\Component\Quests\check\CheckerEvent();
				$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_BS_WIN;

				if(($Item = $Quest->isNeed($Checker)) !== false) {
					$Quest->taskUp($Item);
				}

			} catch (Exception $ex) {
				\components\Helper\FileHelper::writeException($ex, 'cron_BS');
			}
		}
	} elseif($alive_man == 0) {
		if($alive_bot >= 1) {
			EchoLog("Bots alive");
			// боты живы, арха в победители

			$q = mysql_query('SELECT * FROM users_clons WHERE battle > 0 and id_user = 84');
			
			if (mysql_num_rows($q) == 0) {
				$lastbot = mysql_fetch_array(mysql_query("SELECT * FROM users_clons WHERE id_user = 84 ORDER BY id LIMIT 1"));
	
				mysql_query('DELETE FROM users_clons WHERE id_user = 84');
				mysql_query('DELETE FROM inventory WHERE bs_owner = 15');
	
				$sql = 'UPDATE `dt_map` SET 
						`winner` = 84, 
						`winnerlog`= "'.mysql_real_escape_string(nick_align_klan($lastbot)).'",
						`endtime` = '.time().',
						`active` = 0 
					WHERE active = 1
				';
				mysql_query($sql);
				mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,\''."<span class=date>".date("d.m.y H:i")."</span> Турнир завершен. Победитель: ".mysql_real_escape_string(nick_align_klan($lastbot))." Приз: <B>".$map['prize']."</B> кр.<BR>".'\') WHERE `dt_id` = '.$map['id']);
				$newdt = 1;
			} else {
				EchoLog("Some bots in battle");
			}
		} else {
			EchoLog("All died");
			// никого не осталось, ничья
			mysql_query('UPDATE `dt_map` SET `winner` = 0, `winnerlog`= "<i>нет победителя</i>", `endtime` = '.time().', `active` = 0 WHERE active = 1');
			mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,\''."<span class=date>".date("d.m.y H:i")."</span> Турнир завершен. <b>Ничья.</b>".'\') WHERE `dt_id` = '.$map['id']);

			// чистим шмот на всякие
			mysql_query('DELETE FROM inventory WHERE bs_owner = 15');
			$newdt = 1;
		}
	}

	if($newdt == 1) {
		$newtime = (time()+60*60*4);
		$newtime = mktime(date("H",$newtime),date("i",$newtime),0,date("n",$newtime),date("j",$newtime),date("Y",$newtime));
		EchoLog("Set new DT start: ".$newtime);

	    	$nextdttype = mysql_query("SELECT * FROM dt_var WHERE var = 'nextdttype'");
		$nextdttype = mysql_fetch_assoc($nextdttype);
		$nextdttype['valint']++;

		if ($nextdttype['valint'] >= 3) $nextdttype['valint'] = 0;

		mysql_query('UPDATE dt_var SET valint = '.$nextdttype['valint'].' WHERE var = "nextdttype"');
		mysql_query('UPDATE dt_var SET valint = '.$newtime.' WHERE var = "nextdt"');

		mysql_query("UPDATE users SET ldate = 1999999999, odate = 1999999999 WHERE id = 84");

	}
	EchoLog("End check complete");
} else {
	// check for new DT
	$nextdt = mysql_query('SELECT * FROM dt_var WHERE var = "nextdt"');
	if (mysql_num_rows($nextdt) > 0) {
		$nextdt = mysql_fetch_assoc($nextdt) or mydie(mysql_error().":".__LINE__);
		if ($nextdt['valint'] <= time()) {
			// стартуем турнир
			$q = mysql_query('SELECT * FROM dt_rate WHERE dtid = 0');
			if ($q === false) {
				EchoLog("DT rate select error");
				lockDestroy("cron_dt");
				die();
			}

			EchoLog("New tournir starting...");

			if (mysql_num_rows($q) < 2) {
				EchoLog("Users less that 2, set new DT +6 hours from now");
				$newtime = time()+(6*3600);
				$newtime = mktime(date("H",$newtime),date("i",$newtime),0,date("n",$newtime),date("j",$newtime),date("Y",$newtime));
				mysql_query('UPDATE dt_var SET valint = '.$newtime.' WHERE var = "nextdt"') or mydie(mysql_error().":".__LINE__);
				EchoLog("Flushing rates");
				mysql_query('DELETE FROM dt_rate WHERE dtid = 0') or mydie(mysql_error().":".__LINE__);
			} else {
				// process starting
				$ulist = array();
				$fond = 0;
				while($r = mysql_fetch_assoc($q)) {
					$ulist[] = $r['owner'];
					$fond += $r['credit'];
				}

				$fond = round($fond*0.7,2);
				EchoLog("Ulist: ".implode(",",$ulist)." Fond: ".$fond);
				
				// тип бс
			    	$nextdttype = mysql_query("SELECT * FROM dt_var WHERE var='nextdttype'");
				$nextdttype = mysql_fetch_assoc($nextdttype) or MyDieS();

				// чистим старый шмот на всякие
				mysql_query("DELETE FROM dt_items");
				mysql_query("DELETE FROM oldbk.inventory WHERE bs_owner = 15");

				// создаём карту, выставляем там вещи
				$isart = ($nextdttype['valint'] > 0 ? 1 : 0);

				// генерим шмот
				$dtitems = DTGetItemList($isart);
				EchoLog("DTItemsList: ".implode(",",$dtitems));
				
				// собираем прото шмотья
				$protolist = array();
				$duniq = array_unique($dtitems);
				$q = mysql_query('SELECT * FROM shop WHERE id IN ('.implode(",",$duniq).')');
				while($ii = mysql_fetch_assoc($q)) {
					$protolist[$ii['id']] = $ii;
				}


				// отбираем тела которые идут в бс
				$ugoodlist = array();
				$ugoodtxt = "";
				$data = mysql_query("
					SELECT dt.owner, dt.credit, u.id, u.slp, u.align, u.klan, u.login, u.level
					FROM dt_rate as dt, users as u 
					left join dt_profile dc	on dc.owner = u.id
					WHERE 
						dt.dtid = 0 AND 
						dc.def=1 AND
						(SELECT count(id) FROM effects WHERE effects.owner = dt.owner AND (type=11 OR type=12 OR type=13 OR type=14)) = 0 AND u.id = dt.owner
						AND room = 10000 AND 
						u.hidden = 0 AND
						u.ldate >= ".(time()-180)."
					ORDER by credit DESC, dt.time ASC LIMIT 40") or mydie(mysql_error().":".__LINE__);


				if (mysql_num_rows($data) < 2) {
					EchoLog("Start aborted, < 2 good users, exiting");
					mysql_query('UPDATE dt_var SET valint = '.(time()+6*(3600)).' WHERE var = "nextdt"') or mydie(mysql_error().":".__LINE__);
					EchoLog("Flushing rates");
					mysql_query('DELETE FROM dt_rate WHERE dtid = 0') or mydie(mysql_error().":".__LINE__);
					lockDestroy("cron_dt");					
					return;
				}

				//mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);

				while($row = mysql_fetch_array($data)) {
					mysql_query('UPDATE users SET in_tower = 15 WHERE id = '.$row['id']) or mydie(mysql_error().":".__LINE__);
					//undressalltrz($row['id']) or mydie(mysql_error().":".__LINE__);;
					undressall($row['id']);
					$ugoodlist[] = $row['id'];
					$ugoodtxt .= s_nick($row['id'],$row['align'],$row['klan'],$row['login'],$row['level']).', ';
				}

				//mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);

				EchoLog('UGoodlist: '.implode(",",$ugoodlist));

				// удалили бонусы
				mysql_query('DELETE from users_bonus where owner in ('.implode(",",$ugoodlist).')') or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// загнали все статы в бекап
				mysql_query("DELETE FROM dt_realchars") or mydie(mysql_error().":".__LINE__,$ugoodlist);
				$sql = "INSERT INTO dt_realchars 
					(owner,name,sila,lovk,inta,vinos,intel,mudra,stats,nextup,level,master,bpbonussila,bpbonushp,noj,mec,topor,dubina,mfire, mwater,mair,mearth,mlight,mgray,mdark)

					SELECT 
					u.id,u.login,(u.sila - u.bpbonussila) as sila ,u.lovk,u.inta,u.vinos,u.intel,u.mudra,u.stats,u.nextup,u.level, 
					u.master as master,
					u.bpbonussila,u.bpbonushp,u.noj,u.mec,u.topor, u.dubina,u.mfire,u.mwater,u.mair,u.mearth,u.mlight, u.mgray,u.mdark 
					FROM users u
					WHERE u.id in (".implode(",",$ugoodlist).")
				";
				EchoLog($sql);
				mysql_query($sql) or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// удаляем молчи кроме паловских
				mysql_query('DELETE FROM `effects` WHERE `owner` IN ('.implode(",",$ugoodlist).') AND type = 2 AND pal <> 1') or mydie(mysql_error().":".__LINE__,$ugoodlist);
				mysql_query('UPDATE users set slp = 0 WHERE `id` IN ('.implode(",",$ugoodlist).') AND `id` NOT IN (SELECT `owner` FROM effects where `owner` IN ('.implode(",",$ugoodlist).') AND type = 2)') or mydie(mysql_error().":".__LINE__,$ugoodlist);
	
				// удаляем путы и эффекты книг
				mysql_query('DELETE FROM `effects` WHERE `owner` IN ('.implode(",",$ugoodlist).') AND type IN (10,791,792,793,794)') or mydie(mysql_error().":".__LINE__,$ugoodlist);



				// стартуем потиху
				mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// закидываем тела
				mysql_query("UPDATE users SET in_tower = 15, fullhptime=(UNIX_TIMESTAMP()) WHERE id in (".implode(",",$ugoodlist).")") or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// генерим начальную карту
				mysql_query('INSERT INTO dt_map (active,starttime, prize, arttype) VALUES (1,"'.time().'","'.$fond.'","'.$isart.'") ') or mydie(mysql_error().":".__LINE__,$ugoodlist);
				$mapid = mysql_insert_id();
				mysql_query('INSERT INTO dt_log (dt_id) VALUES ('.$mapid.')') or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// типы бс генерим
				$dt_ragetype = 0;
				$dt_greedtype = 0;
				$dt_darktype = 0;
				$dt_whitetype = 0;
				$dt_halftype = 0;
				$dt_hptype = mt_rand(0,2);
				$alloptions = 0;
				
				if (mt_rand(0,9) < 3 && $alloptions < 2) {
					$dt_ragetype = 1;
					$alloptions++;
				}
				if (mt_rand(0,9) <= 3 && $alloptions < 2) {
					$dt_greedtype = 1;
					$alloptions++;
				}                  
				if (mt_rand(0,9) < 3 && $alloptions < 2) {
					$dt_darktype = 1;
					$alloptions++;
				}
				if (mt_rand(0,9) < 3 && $alloptions < 2 && !$dt_darktype) {
					$dt_whitetype = 1;
					$alloptions++;
				}
				if (mt_rand(0,9) < 3 && $alloptions < 2) {
					$dt_halftype = 1;
					$alloptions++;
				}
		
				// обновляем запись в базе
				$sql = 'UPDATE dt_map SET arttype = '.$isart.', ragetype = '.$dt_ragetype.', greedtype = '.$dt_greedtype.', darktype = '.$dt_darktype.', whitetype = '.$dt_whitetype.', halftype = '.$dt_halftype.',  hptype = '.$dt_hptype.' WHERE id = '.$mapid;
				EchoLog($sql);
				mysql_query($sql) or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// раскидываем шмот
				$zwh = 0;
				shuffle($dtitems);
				while($sh = array_shift($dtitems)) {
					if (!isset($protolist[$sh])) {
						EchoLog("Not found proto: ".$sh);
						continue;
					}

					$shopid = $protolist[$sh];
			
					if ($dt_halftype && in_array($sh,$dt_topweapon)) {
						if (!($zwh % 2)) {
							$irrm = $dt_halfleft[mt_rand(0,count($dt_halfleft)-1)];
						} else {
							$irrm = $dt_halfright[mt_rand(0,count($dt_halfright)-1)];
						}
						$zwh++;
					} else {
						$irrm = rand($dt_relmap+501,$dt_relmap+560);
					}
					mysql_query('
						INSERT dt_items (iteam_id, name, img, room)
						VALUES
						('.$shopid['id'].', "'.$shopid['name'].'", "'.$shopid['img'].'", '.$irrm.')
					') or mydie(mysql_error().":".__LINE__,$ugoodlist);
				}


				// считаем чеки и ботов
				$bcount = 1;
				$check_name = '400';

				if (count($ugoodlist) >= 20) {
					$bcount = 2;
				}

				if (count($ugoodlist) >= 35) {
					$bcount = 3;
				}

				$sql = 'UPDATE dt_items SET name = "Чек на предьявителя '.$check_name.'" WHERE iteam_id = 114';
				EchoLog($sql);
				mysql_query($sql) or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// накидываем профиля
				$sql = "
					UPDATE users as u
					INNER JOIN (SELECT * FROM dt_profile dc WHERE owner in (".implode(",",$ugoodlist).") AND def='1') as dcs

					SET u.room = (floor(RAND()*60+".(501+$dt_relmap).")), 
						u.master=u.noj+u.mec+u.topor+u.dubina+u.mfire+u.mwater+u.mair+u.mearth+u.mlight+u.mgray+u.mdark+u.master,
						u.sila=dcs.sila, u.lovk=dcs.lovk, u.inta=dcs.inta, u.vinos=dcs.vinos,
						u.intel=dcs.intel, u.mudra=dcs.mudra, 
						u.stats=0, u.noj=0, u.mec=0, u.topor=0, 
						u.dubina=0, u.mfire=0, u.mwater=0, u.mair=0, u.mearth=0, 
						u.mlight=0, u.mgray=0, u.mdark=0, 
						u.bpbonussila=0, u.bpbonushp = 0,
						u.maxhp=(dcs.vinos*6), u.hp=(dcs.vinos*6) 
					WHERE u.id = dcs.owner
				";			
				EchoLog($sql);
				mysql_query($sql) or mydie(mysql_error().":".__LINE__,$ugoodlist);

				if ($dt_halftype) {
					// полубс - перераскидываем чаров
					$zi = 0;
					reset($ugoodlist);
					while(list($k,$v) = each($ugoodlist)) {
						if (!($zi % 2)) {
							$rr = $dt_halfleft[mt_rand(0,count($dt_halfleft)-1)];
						} else {
							$rr = $dt_halfright[mt_rand(0,count($dt_halfright)-1)];
						}
						$zi++;
						mysql_query('UPDATE users SET room = '.$rr.' WHERE id = '.$v) or mydie(mysql_error().":".__LINE__,$ugoodlist);
					}
				}

				// впиливаем ботов
				$q = mysql_query('SELECT * FROM `users` WHERE `id` = 84') or mydie(mysql_error().":".__LINE__,$ugoodlist);
				$BOT = mysql_fetch_array($q) or mydie(mysql_error().":".__LINE__);
				$BOT['protid'] = $BOT['id'];

				/*
				if(!$isart) {
					$BOT['lovk'] += 10;
				}
				*/

				$BOT['login'] = 'Аpxивариус';
				$BOT_items = load_mass_items_by_id($BOT);
				$z = 0;
				$zi = 0;
				for ($i = 0; $i < $bcount; $i++) {
					if ($dt_halftype) {
						if (!($zi % 2)) {
							$botroom = $dt_halfleft[mt_rand(0,count($dt_halfleft)-1)];
						} else {
							$botroom = $dt_halfright[mt_rand(0,count($dt_halfright)-1)];
						}
						$zi++;
					} else {
						$botroom = mt_rand(501,560)+$dt_relmap;
					}


					$z++;
					if ($z == 1) $kl = "";
					if ($z >= 2) {
						$BOT['login'] = "Помощник аpxивариуса";
						if ($z == 2) {
							$kl = 1;
						} else {
							$kl++;
						}
					}

					if ($isart) {
						$BOT_items['uvor_mf'] += $BOT['lovk'] * 5;
						$BOT_items['auvor_mf'] += $BOT['lovk'] * 5 + $BOT['inta'] * 2;
						$BOT_items['krit_mf'] += $BOT['inta'] * 5;
						$BOT_items['akrit_mf'] += $BOT['inta'] * 5 + $BOT['lovk'] * 2;
					}

					/*
					if ($isart) {
						$BOT_items['uvor_mf'] += $BOT['lovk'] * 2;
						$BOT_items['auvor_mf'] += $BOT['lovk'] * 2 + $BOT['inta'] * 1;
						$BOT_items['krit_mf'] += $BOT['inta'] * 2;
						$BOT_items['akrit_mf'] += $BOT['inta'] * 2 + $BOT['lovk'] * 1;
					}*/

					mysql_query('INSERT INTO `users_clons` SET
							`login` = "'.trim($BOT['login'].' '.$kl).'",
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
							`fullhptime` = "'.time().'",
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
					') or mydie(mysql_error().":".__LINE__,$ugoodlist);
					$ugoodtxt .= s_nick(mysql_insert_id(),$BOT['align'],$BOT['klan'],trim($BOT['login'].' '.$kl),$BOT['level']).', ';
				}


				// впиливаем логи
				$log = '<span class=date>'.date("d.m.y H:i").'</span> Начало турнира. Участники: '.substr($ugoodtxt,0,-2).'<BR>';
				mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$mapid) or mydie(mysql_error().":".__LINE__,$ugoodlist);

				// обновляем ставки
				mysql_query("UPDATE dt_rate SET dtid = ".$mapid." WHERE dtid = 0") or mydie(mysql_error().":".__LINE__,$ugoodlist);
				EchoLog("COMMIT");				
				mysql_query("UPDATE users SET ldate = 0, odate = 0 WHERE id = 84") or mydie(mysql_error().":".__LINE__,$ugoodlist);

				mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__,$ugoodlist);


				if($ugoodlist) {
					reset($ugoodlist);
					try {
						$UserList = \components\models\User::whereIn('id', $ugoodlist)->get()->toArray();
						foreach ($UserList as $_user_) {
							$UserObj = new \components\models\User($_user_);
							/** @var \components\Component\Quests\Quest $QuestComponent */
							$QuestComponent = $app->quest->setUser($UserObj)->get();

							$Checker = new \components\Component\Quests\check\CheckerEvent();
							$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_BS_DO;
							if (($Item = $QuestComponent->isNeed($Checker)) !== false) {
								$QuestComponent->taskUp($Item);
								unset($Item);
							}

							unset($UserObj);
							unset($QuestComponent);
							unset($Checker);
						}
					} catch (Exception $ex) {
						\components\Helper\FileHelper::writeException($ex, 'cron_bs_start');
					}
				}
				//$ugoodlist
			}
		} else {
			$t = $nextdt['valint']-time();
			$t = ceil($t / 60);
			if ($t == 15) {
				$counts = mysql_fetch_array(mysql_query("SELECT count(*), sum(credit) FROM dt_rate where dtid = 0"));
				$fond = round($counts[1]*0.7,2);
				$zay = $counts[0];
				$start_date = date("d.m.Y H:i",$nextdt['valint']);

			    	$nextdttype = mysql_query("SELECT * FROM dt_var WHERE var='nextdttype'");
				$nextdttype = mysql_fetch_assoc($nextdttype);

				// создаём карту, выставляем там вещи
				$isart = ($nextdttype['valint'] > 0 ? 1 : 0);

				$type = 'Тип турнира: <b>обычный</b>.';
				if ($isart) $type = 'Тип турнира: <b>артовый</b>.';
				$text = 'Начало турнира в <a href="http://oldbk.com/encicl/?/bashnya_smerty.html" target="_blank">Башне Смерти</a> через 15 минут. '.$type.' Текущий призовой фонд: <b>'.$fond.' кр.</b>, заявок: '.$zay;
				addch2all($text);
			}
		}
	}
}


function DTGetItemList($isart) {
	if(!$isart) {
		$shmots = array(
			"1","1","92","92","93","93","19","19","20","20","20","23","23","24","14","87","87","6","6",
			"17","17","17","17","11","11","12","12","12","28","28","43","43","36","36","36","37","37","37",
			"38","38","38","50","50","57","52","52","51","51","48","48","47","47","49","49","59","59","60",
			"60","61","61","63","64","64","65","65","66","66","68","68","69","69","70","70","4","5","79","79",
			"80","76","75","75","94","94","95","95","82","91","91","34","34","9","9","101101","101101",
			"101101","101101","650","650","650","650","651","651","651","651","652","652","652","652","653","653","653","653",
			"102","102","102","103","103","103","104","106","106","107","107","108","108","109",
			"110","111","112","112","113","113","114","121",
			// new from type
			"62","62","280","280",
			"222222236","222222236","222222236","222222236","222222236","222222236",
			"222222237","222222237","222222237","222222237","222222237","222222237",
			"657","657","657","657","657",
		);
 	} else if($isart == 1) {
		$shmots = array(
			"202","114",
			"207","207","207","207","207","207","207","207","207","207",
			"32","32","38","38","38","38","38","147","147","147","147","147","174","174","35","35","35","172","172","33","33","26",
			"26","28","28","28","28","31","31","31","27","27","24","24","24","22","22","22","23","23","23","16","16","16",
			"16","16","16","16","16","16","16","17","17","17","17","17","17","17","87","87","87","87","87","14","14","14",
			"14","14","15","15","15","15","15","173","173","74","74","73","73","70","70",
			"70","62","62","62","57","57","57","57","57",
			"82","82","111","111","83","83","131","131","96","96","5","5","5","78","78",
			"78","109","109","71","71","170","170","170","170","170","9","9","9","9","9",
			"654","654","654","654","654",
			"653","653","653","653","653","653","653","653","653","653",
			"653","653","653","653","653","653","653","653","653","653",
			"653","653","653","653","653","653","653","653","653","653",
			"231", "228", "45", "177", "178"
		);
				
		$artcount = 3;
		for($i = 0; $i < $artcount; $i++)	{
			$m = mt_rand(0,5);  // новые и старые простые мечи.
			if($m == 0) {
				$shmots[] = "240";
			} elseif($m == 1) {
				$shmots[] = "239";
			} elseif($m == 2) {
				$shmots[] = "234";
			} elseif($m == 3) {
				$shmots[] = "190";
			} elseif($m == 4) {
				$shmots[] = "5";
			} elseif($m == 5) {
				$shmots[]="78";
			}
		}

		$artcount = 3;
		for($i = 0;$i < $artcount; $i++) {
			$m = mt_rand(0,3);  //перчатки  пал тьмы свет нейтр.
			if($m == 0) {
				$shmots[] = "177";
				$shmots[] = "45";
				$shmots[] = "40";
				$shmots[] = "263";
				$shmots[] = "1120327606";
				$shmots[] = "1121104234";
				$shmots[] = "1490402627";
			} elseif($m == 1) {
				$shmots[] = "178";
				$shmots[] = "43";
				$shmots[] = "10";
				$shmots[] = "1121104234";
				$shmots[] = "1220902600";
			} elseif($m == 2) {
				$shmots[] = "228";
				$shmots[] = "263";
				$shmots[] = "231";
				$shmots[] = "1120327606";
				$shmots[] = "1561008724";
				$shmots[] = "1510721104";
				$shmots[] = "40";
				$shmots[] = "10";
			} elseif($m == 3) {
				$shmots[] = "231";
				//$shmots[] = "620";
				$shmots[] = "1160716230";
				$shmots[] = "1121104234";
				$shmots[] = "1300728624";
				$shmots[] = "1510721104";
			}
		}

		$artcount = 2;
		$m = mt_rand(0,1);
		for($i = 0; $i < $artcount; $i++) {
			if($m == 0) {
				$shmots[] = "202"; //Лучшие ботинки
			}
		}
						
		$artcount = 5;
		for($i = 0; $i < $artcount; $i++) {
			$m = mt_rand(0,1);
			if($m == 0) {
				$shmots[] = "203"; //Кромус
			} elseif($m == 1) {
				$shmots[] = "204";  //Герои
			}
		}

		$artcount = 3;
		for($i = 0; $i < $artcount; $i++) {
			$m = mt_rand(0,1);
			if($m == 0) {
				$shmots[] = "200"; //Закрытый шлем Развития
			} elseif($m == 1) {
				$shmots[] = "208";  //Шлем Ангела
			}
		}

		$artcount = 4;
		for($i = 0; $i < $artcount; $i++) {
			$m=mt_rand(0,1);
			if($m == 0) {
				$shmots[] = "209"; //Щит Откровения
			} elseif ($m == 1) {
				$shmots[] = "210";  //Щит Пророчества
			}
		}

		$artcount = 8;
		for($i = 0; $i < $artcount;$i++) {
			$m = mt_rand(0,5);
			if($m == 0) {
				$shmots[] = "196"; //Броня Ангела
			} elseif($m == 1 || $m == 5) {
				$shmots[] = "197";  //Доспех -Броня Титанов-
			} elseif($m == 2) {
				$shmots[] = "198";   //досп. Хаоса
			} elseif($m == 3 || $m == 4) {
				$shmots[] = "205";  //Панцирь Злости
			}
		}

		$artcount = 5;
		for($i = 0; $i < $artcount; $i++) {
			$m = mt_rand(0,3);
			if($m == 0) {
				$shmots[] = "195"; //великое кольцо жизни
			} elseif($m == 1) {
				$shmots[] = "201";  //кольцо жизни
			} elseif($m == 2) {
				$shmots[] = "201";   //кольцо жизни
			} elseif($m == 3) {
				$shmots[] = "201";  //кольцо жизни
			}
		}

		$artcount=3;
		for($i = 0;$i < $artcount;$i++)	{
			$m = mt_rand(0,3); //топор и меч хаоса
			if($m == 0) {
				$shmots[] = "1006241";
			} elseif($m == 1) {
				$shmots[] = "1006242";
			} elseif($m == 2) {
				   //вихрь и радость
				$shmots[] = "206";
			} elseif($m == 3) {
				$shmots[] = "199";
			}
		}
	}

	// trap 4-6 count
	$shmots[] = "194194"; $shmots[] = "194194"; $shmots[] = "194194"; $shmots[] = "194194";
	if(mt_rand(1,5) > 2) {
		$shmots[] = "194194";
	}
	if(mt_rand(1,5) > 2) {
		$shmots[] = "194194";
	}

	if(!$isart) {
		// not art bs
		if(mt_rand(1,5) > 2) { //Клонирование
			$shmots[] = "119";
		}
		if(mt_rand(1,5) > 2) { // Переманить клона
			$shmots[] = "120";
		}
	}

	$shcount = mt_rand(1,5);

	for($i = 0;$i < $shcount; $i++)  { // путы
		$shmots[] = "121";
	}

	if(!$isart == 0) {
		// not art
		$shcount = mt_rand(1,2);
		for($i = 0;$i < $shcount;$i++) { // Кулачное нападение
			$shmots[] = "171";
		}
	} else {
		//art
		$shcount = mt_rand(1,3);
		for($i = 0;$i < $shcount; $i++) { // Кулачное нападение
			$shmots[] = "171";
		}
	}
	return $shmots;
}

lockDestroy("cron_dt");
?>