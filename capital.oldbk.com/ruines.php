<?php
	$rscriptstart = time();

	function Redirect($path) {
		header("Location: ".$path);
		die();
	}

	function MyDie() {
		Redirect("ruines.php");
	}

	function GetSerFile($path) {
		clearstatcache();
		if (file_exists($path)) {
			$data = file_get_contents($path);
			if ($data !== FALSE && strlen($data) > 0) {
				$data = unserialize($data);
				if ($data !== null && $data !== FALSE) {
					return $data;
				}
			}
		}
		return array();
	}

	function SaveSerFile($path,$arr) {
		$fp = fopen($path,'w+');
		if ($fp) {
			if (flock($fp, LOCK_EX)) {
				fwrite($fp,serialize($arr));
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
	}

	function getrepwinner($level) {
		if ($level == 8) return  3000;
		if ($level == 9) return  5000;
		if ($level == 10) return 10000;
		if ($level == 11) return 20000;
		if ($level == 12) return 40000;
		if ($level == 13) return 80000;
		if ($level > 13) return  80000;
	}

	function getreplooser($level) {
		if ($level == 8) return  300;
		if ($level == 9) return  500;
		if ($level == 10) return 1000;
		if ($level == 11) return 2000;
		if ($level == 12) return 4000;
		if ($level == 13) return 8000;
		if ($level > 13) return  8000;
	}

	function RuinesMakeRep($user,$is_win) {
		$step = array(
			0   => 500,
			500 => 1000,
			1000 => 1500,
			1500 => 2000,
			2000 => 2000,
		);


		$q = mysql_query('SELECT * FROM effects WHERE owner ='.$user['id'].' and type = 420420 LIMIT 1') or mydie();
		if (mysql_num_rows($q) > 0) {
			$eff = mysql_fetch_assoc($q) or mydie();
			if ($is_win > 0) {
				// выдаём репу и двигаем следующий шаг
				$rec = array();
	    			$rec['owner']=$user['id'];
				$rec['owner_login']=$user['login'];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user['repmoney'];
				$rec['target_login'] = "Руины";
				$rec['owner_rep_posle']=$user['repmoney']+$eff['add_info'];
				$rec['sum_rep'] = $eff['add_info'];
				$rec['type'] = 203;
				add_to_new_delo($rec) or mydie();

				addchp ('<font color=red>Внимание!</font> Вы выиграли турнир и получаете <b>'.$eff['add_info'].'</b> репутации.','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or mydie();

				mysql_query('UPDATE users SET repmoney = repmoney + '.$eff['add_info'].', rep = rep + '.$eff['add_info'].' WHERE id = '.$user['id'].' LIMIT 1') or mydie();

				addchp ('<font color=red>Внимание!</font> При следующей победе в турнире Руин Старого Замка вам будет начислена награда <b>'.$step[$eff['add_info']].' репутации</b>.','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or mydie();
				mysql_query('UPDATE effects SET add_info = '.$step[$eff['add_info']].' WHERE id = '.$eff['id'].' LIMIT 1') or mydie();

			} else {
				// проиграли - удаляем эффект
				mysql_query('DELETE FROM effects WHERE type = 420420 and owner = '.$user['id'].' LIMIT 1') or mydie();
			}
		} else {
			// нет эффекта
			if ($is_win > 0) {
				// победили, выставляем первый шаг и репу
				reset($step);
				list($k,$v) = each($step);

				if (date("N") == 7) {
					$t = mktime(23,59,59);
				} else {
					$t = strtotime("Next Sunday")+((24*3600)-1);
				}

				mysql_query("INSERT INTO `effects` SET `type` = 420420, `name` = 'Бонус репутации в руинах' , `time`= '".$t."' , `owner` = '".$user['id']."', add_info = '".$step[$k]."'") or mydie();

				addchp ('<font color=red>Внимание!</font> При следующей победе в турнире Руин Старого Замка вам будет начислена награда <b>'.$v.' репутации</b>.','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or mydie();
			} else {
				// за проигрышь ничего
			}
		}
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	require_once('connect.php');
	require_once('functions.php');
	require_once('fsystem.php');
	require_once('ruines_config.php');


	$chestpenalty = 3; // задержка на хотьбу после открытия сундука
	$giveitempenalty = 3; // задержка на поднятие вещи
	$givekeypenalty = 3; // задержка на поднятие ключа
	$keypenalty = 5; // задержка на каждый ход ключеносца
	$attacktime = 60*3; // задержка на нападение
	$keytime = 60*3; // задержка на появление ключей
	$repwinner = 1000; // получает победитель
	$replooser = 150; // получает проигравший
	$travmatime = 60*60*3; // время травмы
	$winnerpenalty = 60*60*8; // пенальти на время посещения руин для победивших
	$looserpenalty = 60*60*3; // пенальти на время посещения руин для проигравших

	if (!($user['room'] > 1000 && $user['room'] < 10000)) Redirect("main.php");
	if ($user['in_tower'] != 2) Redirect('main.php');
	if ($user['battle'] != 0 || $user['battle_fin'] != 0) { Redirect("fbattle.php"); }

	// получаем инфу о карте
	$q = mysql_query('SELECT * FROM `ruines_map` WHERE `id` = '.$user['ruines']) or die();
	$map = mysql_fetch_assoc($q) or die();


	$error = "";
	$trap = "";

	$croom = $user['room'] - $map['rooms'];
	$ses = GetSerFile($frpath);
	if (!isset($ses['id']) || $ses['id'] != $map['id']) {
		// новые руины, старые данные надо снести
		$ses['time'] = time();
		$ses['timei'] = time();
		$ses['id'] = $map['id'];
		SaveSerFile($frpath,$ses);

		if ($_SESSION['tsound']==1) {
			$do_sound="<object width=\"1\" height=\"1\"
				codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\">
				<param name=\"quality\" value=\"high\" /><param name=\"src\" value=\"/sound/startbat.swf\" />
				<embed type=\"application/x-shockwave-flash\" width=\"1\" height=\"1\" src=\"/sound/startbat.swf\" quality=\"high\">
				</embed>
				</object>";
		} else {
			$do_sound='';
		}


	}


	// открываем сокровищницу если есть победа
	if ($map['t'.$user['id_grup'].'score'] >= 2) {
		// создаём комнату
		$rooms[78] = array("Сокровищница",67,68,69,66,10);

		// делаем проходы
		$rooms[66][2] = 78;
		$rooms[67][4] = 78;
		$rooms[68][4] = 78;
		$rooms[69][2] = 78;
	}

	if (isset($_GET['opensanct']) && $croom == 78 && $map['sanct'] == -1) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM `ruines_map` WHERE id = '.$map['id'].' FOR UPDATE') or mydie();
		$chkpnl = 0;
		if (mysql_num_rows($q) > 0) {
			$map = mysql_fetch_assoc($q);
			if ($map['sanct'] != -1) mydie();

			// даём всем плюшки и выкидываем из сокровищницы
			$q = mysql_query('SELECT * FROM `users` WHERE `ruines` = '.$map['id']) or mydie();

			// лог
			$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> открыл <b>Сокровищницу</b>.<BR>';
			mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

			$uarr = array();
			$uinfo = array();
			$sl = 0;
			$mids = array();
			while($u = mysql_fetch_assoc($q)) {

				// лог
				$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> выбывает из турнира.<BR>';
				mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

				$uarr[] = $u['id'];
				$uinfo[$u['id']] = $u;

				mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`,`battle`,`getfrom`) VALUES ('Золотой слиток 1 екр',0,1,1,'{$u[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'ruin_gold1.gif','',0,0,0,0,0,0,0,50,'Руины Старого замка',0,0.1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,777771,'6',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,".$user[id_city].",0,45);") or mydie();
				$sl_id = mysql_insert_id();

				$rec = array();
	    			$rec['owner']=$u[id];
				$rec['owner_login']=$u[login];
				$rec['owner_balans_do']=$u['money'];
				$rec['owner_balans_posle']=$u['money'];
				$rec['type']=202;
				$rec['target_login'] = "Руины";
				$rec['item_id']=get_item_fid(array("idcity" => $user['id_city'], "id" => $sl_id));
				$rec['item_name']='Золотой слиток 1 екр';
				$rec['item_count']=1;
				$rec['item_type']=50;
				$rec['item_cost']=1;
				$rec['item_dur']=0;
				$rec['item_maxdur']=1;
				if (add_to_new_delo($rec) === FALSE) mydie();

				mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`,`battle`,`getfrom`) VALUES ('Зелье Старого Мага',0,1,1,'{$u[id]}',4,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'bott4.gif','',0,0,0,0,0,0,667,50,'',0,0.1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,667667,'61',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,".$user[id_city].",0,45);") or mydie();
				$zel_id = mysql_insert_id();

				$rec = array();
	    			$rec['owner']=$u[id];
				$rec['owner_login']=$u[login];
				$rec['owner_balans_do']=$u['money'];
				$rec['owner_balans_posle']=$u['money'];
				$rec['type']=202;
				$rec['target_login'] = "Руины";
				$rec['item_id']=get_item_fid(array("idcity" => $user['id_city'], "id" => $zel_id));
				$rec['item_name']='Зелье Старого Мага';
				$rec['item_count']=1;
				$rec['item_type']=50;
				$rec['item_cost']=1;
				$rec['item_dur']=0;
				$rec['item_maxdur']=1;
				if (add_to_new_delo($rec) === FALSE) mydie();

				$mids[] = $u['id'];
				$sl++;

				// пишем сколько вынес 1 екр
				mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								'.$u['id'].',
								"1ekr",
								"1"
							) 
							ON DUPLICATE KEY UPDATE
								`val` = val + 1
				') or mydie();
			}

			if (count($mids)) {
				addch_group('<font color=red>Внимание!</font> Вы открыли Сокровища и получили слиток на <b>1</b> екр и <b>Зелье Старого Мага</b>!</b>',$mids) or mydie();
			}

			// сколько екр вынесено
			mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
						VALUES(
							"0",
							"1ekr",
							"1"
						) 
						ON DUPLICATE KEY UPDATE
							`val` = val + '.$sl
			) or mydie();


			// пишем колва открытий сундуков
			mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
						VALUES(
							"0",
							"opensanct",
							"1"
						) 
						ON DUPLICATE KEY UPDATE
							`val` = val + 1
			') or mydie();


			if (get_chanse(5)) {
				shuffle($uarr);
				$u = $uarr[0];
				$u = $uinfo[$u];
				mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`,`battle`,`getfrom`) VALUES ('Золотой слиток 10 екр',0,1,1,'{$u[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'ruin_gold2.gif','',0,0,0,0,0,0,0,50,'Руины Старого замка',0,0.1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,777772,'6',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,0,0,45);") or mydie();
				$sl_id = mysql_insert_id();
				addchp ('<font color=red>Внимание!</font> Вы открыли Сокровища и получили слиток на <b>10</b> екр','{[]}'.$u['login'].'{[]}',-1,$u['id_city']) or mydie();

				$rec = array();
	    			$rec['owner']=$u[id];
				$rec['owner_login']=$u[login];
				$rec['owner_balans_do']=$u['money'];
				$rec['owner_balans_posle']=$u['money'];
				$rec['target_login'] = "Руины";
				$rec['type']=202;
				$rec['item_id']=get_item_fid(array("idcity" => $user['id_city'], "id" => $sl_id));
				$rec['item_name']='Золотой слиток 10 екр';
				$rec['item_count']=1;
				$rec['item_type']=50;
				$rec['item_cost']=1;
				$rec['item_dur']=0;
				$rec['item_maxdur']=1;
				if (add_to_new_delo($rec) === FALSE) mydie();

				mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								"0",
								"10ekr",
								"1"
							) 
							ON DUPLICATE KEY UPDATE
								`val` = val + 1
				') or mydie();

				// пишем сколько вынес 10 екр
				mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								'.$u['id'].',
								"10ekr",
								"1"
							) 
							ON DUPLICATE KEY UPDATE
								`val` = val + 1
				') or mydie();

				$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> повезло и он нашел в <b>Сокровищнице</b> слиток на 10 екр.<BR>';
				mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

			}


			if (get_chanse(1)) {
				shuffle($uarr);
				$u = $uarr[0];
				$u = $uinfo[$u];
				mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`,`battle`,`getfrom`) VALUES ('Золотой слиток 20 екр',0,1,1,'{$u[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'ruin_gold3.gif','',0,0,0,0,0,0,0,50,'Руины Старого замка',0,0.1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,777773,'6',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,0,0,45);") or mydie();
				addchp ('<font color=red>Внимание!</font> Вы открыли Сокровища и получили слиток на <b>20</b> екр','{[]}'.$u['login'].'{[]}',-1,$u['id_city']) or mydie();

				mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								"0",
								"20ekr",
								"1"
							) 
							ON DUPLICATE KEY UPDATE
								`val` = val + 1
				') or mydie();

				$rec = array();
	    			$rec['owner']=$u[id];
				$rec['owner_login']=$u[login];
				$rec['owner_balans_do']=$u['money'];
				$rec['owner_balans_posle']=$u['money'];
				$rec['type']=202;
				$rec['item_id']=get_item_fid(array("idcity" => $user['id_city'], "id" => $sl_id));
				$rec['item_name']='Золотой слиток 20 екр';
				$rec['target_login'] = "Руины";
				$rec['item_count']=1;
				$rec['item_type']=50;
				$rec['item_cost']=1;
				$rec['item_dur']=0;
				$rec['item_maxdur']=1;
				if (add_to_new_delo($rec) === FALSE) mydie();

				// пишем сколько вынес 20 екр
				mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								'.$u['id'].',
								"20ekr",
								"1"
							) 
							ON DUPLICATE KEY UPDATE
								`val` = val + 1
				') or mydie();

				$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> повезло и он нашел в <b>Сокровищнице</b> слиток на 20 екр.<BR>';
				mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();
			}


			// теперь выкидываем всех нах
			$ruine_week = false;
			$ivent = mysql_query("select * from oldbk.ivents where id=3") or mydie();
			$get_ivent = mysql_fetch_array($ivent);
			if ($get_ivent['stat'] == 1) {
				$ruine_week = true;
			}


			$eids = "";

			reset($uinfo);
			while(list($k,$u) = each($uinfo)) {
				$eids .= $u['id'].',';

				// раздеваем
				undressalltrz($u['id']) or mydie();

				if ($ruine_week) RuinesMakeRep($u,1);

				// выставляем статы
				$q = mysql_query('SELECT * FROM `ruines_realchars` WHERE `owner` = '.$u['id']) or mydie();
				$tec = mysql_fetch_assoc($q) or mydie();

				$q2 = mysql_query("select * from effects where owner='{$u[id]}' and type>=1001 and type<=1003") or mydie();
				$hp_bonus = mysql_fetch_array($q2);
				if ($hp_bonus['id']>0) {
					// эффект еще есть
				} else {
					//эфекта такого уже нет!

					$tec['maxhp'] = $tec['maxhp']-$tec['bpbonushp'];
					$tec['bpbonushp'] = 0;

					if ($tec['hp'] > $tec['maxhp']) {
						$tec['hp'] = $tec['maxhp'];
					}
				}


				$hp = $tec['vinos']*6 + ($tec['bpbonushp']);

				// удаляем эффекты пут и травм
				mysql_query('DELETE FROM `effects` WHERE type IN (10,11,12,13) AND `owner` = '.$u['id']) or mydie();

				mysql_query('UPDATE `users` SET 
						`sila` = "'.($tec['sila']+$tec['bpbonussila']).'",
						`lovk` = "'.$tec['lovk'].'",
						`inta` = "'.$tec['inta'].'",
						`vinos` = "'.$tec['vinos'].'",
						`intel` = "'.$tec['intel'].'",
						`mudra` = "'.$tec['mudra'].'",
						`stats` = "'.$tec['stats'].'",
						`noj` = "'.$tec['noj'].'",
						`mec` = "'.$tec['mec'].'",
						`topor` = "'.$tec['topor'].'",
						`dubina` = "'.$tec['dubina'].'",
						`mfire` = "'.$tec['mfire'].'",
						`mwater` = "'.$tec['mwater'].'",
						`mair` = "'.$tec['mair'].'",
						`mearth` = "'.$tec['mearth'].'",
						`mlight` = "'.$tec['mlight'].'",
						`mgray` = "'.$tec['mgray'].'",
						`mdark` = "'.$tec['mdark'].'",
						`master` = "'.$tec['master'].'",
						`mana` = "'.$tec['mana'].'",
						`maxmana` = "'.$tec['mana'].'",
						`maxhp` = "'.$hp.'",
						`hp` = "'.$hp.'", 
						`bpbonussila` = '.$tec['bpbonussila'].',
						`bpbonushp` = '.$tec['bpbonushp'].' WHERE `id` = '.$u['id']
				) or mydie();

				// выставляем время до след посещения руин

				//смотрим бонус
				$qeffb = mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$u['id']}'  and `type` = '9106' LIMIT 1;") or mydie();
				$bonuseffect = mysql_fetch_array($qeffb);
				if ($bonuseffect['id']>0) {
					$topen = (int)($winnerpenalty-($winnerpenalty*$bonuseffect['add_info']));
				} else {
					$topen = $winnerpenalty;
				}

				mysql_query('INSERT INTO oldbk.`ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								'.$u['id'].',
								"cango",
								'.(time()+$topen).'
							) 
							ON DUPLICATE KEY UPDATE
								`val` = '.(time()+$topen)
				) or mydie();

				// выставляем время до след посещения руин
				mysql_query('INSERT INTO avalon.`ruines_var` (`owner`,`var`,`val`) 
							VALUES(
								'.$u['id'].',
								"cango",
								'.(time()+$topen).'
							) 
							ON DUPLICATE KEY UPDATE
								`val` = '.(time()+$topen)
				) or mydie();

			}

			$eids = substr($eids,0,strlen($eids)-1);

			// удаляем молчи кроме паловских
			mysql_query('DELETE FROM `effects` WHERE `owner` IN ('.$eids.') AND type = 2 AND pal <> 1') or mydie();
			mysql_query('UPDATE users set slp = 0 WHERE `id` IN ('.$eids.') AND `id` NOT IN (SELECT `owner` FROM effects where `owner` IN ('.$eids.') AND type = 2)') or mydie();

			// ставим всех на старт, выставляем что они уже не в башне и удаляем номер инстанса руин, номер команды в 0
			mysql_query('UPDATE `users` SET `in_tower` = 0, `id_grup` = 0, `ruines` = 0, `room` = 999 WHERE `id` IN ('.$eids.')') or mydie();

			// чистим смерти
			mysql_query('DELETE FROM `ruines_var` WHERE `var` = "rdeath" AND owner IN ('.$eids.')') or mydie();

			// чистим реальные статы
			mysql_query('DELETE FROM `ruines_realchars` WHERE `owner` IN ('.$eids.')') or mydie();

			// удаляем шмот в инвентаре с руин
			mysql_query('DELETE FROM oldbk.`inventory` WHERE bs_owner = 2 AND `owner` IN ('.$eids.')') or mydie();

			// чистим шмотки
			mysql_query('DELETE FROM `ruines_items` WHERE `room` BETWEEN '.$map['rooms'].' AND '.($map['rooms']+100)) or mydie();

			// удаляем карту
			mysql_query('DELETE FROM `ruines_map` WHERE id = '.$map['id']) or mydie();

			// удаляем клонов
			mysql_query('DELETE FROM `users_clons` WHERE bot_online = 5 AND `bot_room` BETWEEN '.$map['rooms'].' AND '.($map['rooms']+100)) or mydie();

			// пишем что турнир заверешен
			$log = '<span class=date>'.date("d.m.y H:i").'</span> <b>Турнир завершен.</b><BR>';
			$chkpnl = 1;
			mysql_query('UPDATE `ruines_log` SET `active` = 0, `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE `id` = '.$map['id']) or mydie();

			$q = mysql_query('COMMIT') or mydie();

			if(is_array($uinfo) && $uinfo) {
				reset($uinfo);
				foreach ($uinfo as $_user_) {
					try {
						$User = new \components\models\User($_user_);
						$Quest = $app->quest
							->setUser($User)
							->get();
						$Checker = new \components\Component\Quests\check\CheckerEvent();
						$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RUIN_REWARD;

						if(($Item = $Quest->isNeed($Checker)) !== false) {
							$Quest->taskUp($Item);
						}

					} catch (Exception $ex) {

					}
				}
			}
		} else {
			$q = mysql_query('COMMIT') or mydie();
		}
		if ($chkpnl) {
			RuinesCheckPenalty($map['id']);
		}
		Redirect("ruines_start.php");
	}

	if (isset($_GET['forceexit']) && ($croom == 75 || $map['t'.$user['id_grup'].'score'] >= 2)) {
		$q = mysql_query('START TRANSACTION') or mydie();
		// выход на кладбище или же на сокровищнице

		// удаляем эффекты пут и травм
		mysql_query('DELETE FROM `effects` WHERE type IN (10,11,12,13) AND `owner` = '.$user['id']) or mydie();

		// раздеваем
		undressalltrz($user['id']) or mydie();

		// выставляем статы
		$q = mysql_query('SELECT * FROM `ruines_realchars` WHERE `owner` = '.$user['id']) or mydie();
		$tec = mysql_fetch_assoc($q) or mydie();

		if ($tec['bpbonushp'] > 0) {
			// если был боныс хп - проверяем незакончился ли он

			$q2 = mysql_query("select * from effects where owner='{$user[id]}' and type>=1001 and type<=1003") or mydie();
			$hp_bonus = mysql_fetch_array($q2);
			if ($hp_bonus['id']>0) {
				// эффект еще есть
			} else {
				//эфекта такого уже нет!

				$tec['maxhp'] = $tec['maxhp']-$tec['bpbonushp'];
				$tec['bpbonushp'] = 0;

				if ($tec['hp'] > $tec['maxhp']) {
					$tec['hp'] = $tec['maxhp'];
				}
			}
		}

		$hp = $tec['vinos']*6 + ($tec['bpbonushp']);

		mysql_query('UPDATE `users` SET 
				`sila` = "'.($tec['sila']+$tec['bpbonussila']).'",
				`lovk` = "'.$tec['lovk'].'",
				`inta` = "'.$tec['inta'].'",
				`vinos` = "'.$tec['vinos'].'",
				`intel` = "'.$tec['intel'].'",
				`mudra` = "'.$tec['mudra'].'",
				`stats` = "'.$tec['stats'].'",
				`noj` = "'.$tec['noj'].'",
				`mec` = "'.$tec['mec'].'",
				`topor` = "'.$tec['topor'].'",
				`dubina` = "'.$tec['dubina'].'",
				`mfire` = "'.$tec['mfire'].'",
				`mwater` = "'.$tec['mwater'].'",
				`mair` = "'.$tec['mair'].'",
				`mearth` = "'.$tec['mearth'].'",
				`mlight` = "'.$tec['mlight'].'",
				`mgray` = "'.$tec['mgray'].'",
				`mdark` = "'.$tec['mdark'].'",
				`master` = "'.$tec['master'].'",
				`mana` = "'.$tec['mana'].'",
				`maxmana` = "'.$tec['mana'].'",
				`maxhp` = "'.$hp.'",
				`hp` = "'.$hp.'", 
				`bpbonussila` = '.$tec['bpbonussila'].',
				`bpbonushp` = '.$tec['bpbonushp'].' WHERE `id` = '.$user['id']
		) or mydie();

		mysql_query('DELETE FROM `ruines_realchars` WHERE `owner` = '.$user['id']) or mydie();

		addchp ('<font color=red>Внимание!</font> Вы покинули турнир.','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or mydie();

		$ruine_week = false;
		$ivent = mysql_query("select * from oldbk.ivents where id=3") or mydie();
		$get_ivent = mysql_fetch_array($ivent);
		if ($get_ivent['stat'] == 1) {
			$ruine_week = true;
		}


		if ($map['t'.$user['id_grup'].'score'] < 2) {
			if (true) {
				$rr = mt_rand(0,100);
				$ttext  = "";
				if ($rr < 60)
					settravmabyinfotrz($user,11,$travmatime);
				if ($rr >= 60 && $rr < 85)
					settravmabyinfotrz($user,12,$travmatime);
				if ($rr >= 85)
					settravmabyinfotrz($user,13,$travmatime);
			}
		}


		// если наша команда выиграла - то winnerpenalty
		$pen = 0;
		if ($map['t'.$user['id_grup'].'score'] >= 2) {
			$pen = $winnerpenalty;
			if ($ruine_week) RuinesMakeRep($user,1);
		} else {
			$pen = $looserpenalty;
			if ($ruine_week) RuinesMakeRep($user,0);
		}


		//смотрим бонус
		$qeffb = mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9106' LIMIT 1;") or mydie();
		$bonuseffect = mysql_fetch_array($qeffb);
		if ($bonuseffect['id'] > 0) {
			$topen=(int)($pen-($pen*$bonuseffect['add_info']));
		} else {
			$topen=$pen;
		}

		// выставляем время до след посещения руин
		mysql_query('INSERT INTO oldbk.`ruines_var` (`owner`,`var`,`val`) 
					VALUES(
						'.$user['id'].',
						"cango",
						'.(time()+$topen).'
					) 
					ON DUPLICATE KEY UPDATE
						`val` = '.(time()+$topen)
		) or mydie();

		// выставляем время до след посещения руин
		mysql_query('INSERT INTO avalon.`ruines_var` (`owner`,`var`,`val`) 
					VALUES(
						'.$user['id'].',
						"cango",
						'.(time()+$topen).'
					) 
					ON DUPLICATE KEY UPDATE
						`val` = '.(time()+$topen)
		) or mydie();


		// чистим смерти
		mysql_query('DELETE FROM `ruines_var` WHERE `var` = "rdeath" AND owner = '.$user['id']) or mydie();


		// удаляем молчи кроме паловских
		mysql_query('DELETE FROM `effects` WHERE `owner` = '.$user['id'].' AND type = 2 AND pal <> 1') or mydie();
		mysql_query('UPDATE users set slp = 0 WHERE `id` = '.$user['id'].' AND `id` NOT IN (SELECT `owner` FROM effects where `owner` = '.$user['id'].' AND type = 2)') or mydie();

		// ставим всех на старт, выставляем что они уже не в башне и удаляем номер инстанса руин, номер команды в 0
		mysql_query('UPDATE `users` SET `in_tower` = 0, `id_grup` = 0, `ruines` = 0, `room` = 999 WHERE `id` = '.$user['id']) or mydie();

		// удаляем шмот в инвентаре с руин
		mysql_query('DELETE FROM oldbk.`inventory` WHERE bs_owner = 2 AND `owner` = '.$user['id']) or mydie();

		// лог
		$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> добровольно покинул турнир<BR>';
		mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

		$q = mysql_query('COMMIT') or mydie();

		Redirect('ruines_start.php');
	}

	// нападение
	if(isset($_POST['attack']) && $croom != 75) {
		if (($map['starttime'] + $attacktime) > time()) {
			$error = 'Нельзя нападать первые 3 минуты после начала турнира.';
		}
		if (empty($error)) {
			$is_bot = FALSE;
			$jert = array();

			if (strpos($_POST['attack'],'(иллюзия') !== FALSE) {
				$is_bot = TRUE;
			}

			$q = mysql_query('START TRANSACTION') or mydie();
			if ($is_bot) {
				// ищем бота по логину и по
				$q = mysql_query('SELECT * FROM `users_clons` WHERE `login` = "'.mysql_real_escape_string($_POST['attack']).'" AND `bot_online` = 5 AND `bot_room` = '.$user['room'].' FOR UPDATE') or mydie();
				if (mysql_num_rows($q) > 0) {
					// нашли бота
					$jert = mysql_fetch_assoc($q) or mydie();
					$jert['id_grup'] = 3;
					$jert['room'] = $jert['bot_room'];
				}
			} else {
				$q = mysql_query('SELECT * FROM `users` WHERE `login` = "'.mysql_real_escape_string($_POST['attack']).'" OR `id` = '.$user['id'] .' FOR UPDATE') or mydie();
			}
			if ( (mysql_num_rows($q) == 2 && !$is_bot) || ($is_bot && count($jert)) ) {
				// получили инфу
				if (!$is_bot) {
					$p1 = mysql_fetch_assoc($q) or mydie();
					$p2 = mysql_fetch_assoc($q) or mydie();
					if ($p1['id'] == $user['id']) {
						$user = $p1;
						$jert = $p2;
					} else {
						$user = $p2;
						$jert = $p1;
					}
				}

				// сохраняем время нападения
				$chkattack = true;
				if (isset($_SESSION['ruineslastattack'])) {
					if ($rscriptstart - $_SESSION['ruineslastattack'] <= 2) {
						$error = "Не так быстро...";
						$chkattack = false;
					}
				}
				$_SESSION['ruineslastattack'] = time();

				// проверяем комнату, айди чтобы не сам на себя и чтобы была противоположная команда и чтобы хп были больше 0 и что мы сами не в бою
				if ($chkattack && $jert['room'] == $user['room'] && $jert['id'] != $user['id'] && $user['id_grup'] != $jert['id_grup'] && $user['hp'] > 0 && $jert['hp'] > 0 && $user['battle'] == 0 && $user['battle_fin'] == 0) {
					if($jert['battle'] == 0) {
						// поймали
						//if ($jert['battle_t']) mydie();

						// фиксим HP у противника, фиксим у себя
						if ($is_bot) {
							mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']) or mydie();
						} else {
							mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND (`id` = '.$jert['id'].' OR `id` = '.$user['id'].')') or mydie();
						}

						// проверяем hp противника
						if ($jert['hp'] == 0 && !$is_bot) {
							mysql_query('UPDATE `users` SET `hp` = 2 WHERE `id` = '.$jert['id']) or mydie();
						}

						mysql_query('INSERT INTO `battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
								VALUES
								(
									"Бой в руинах",
									"",
									"'.mt_rand(1,3).'",
									"11",
									"0",
									"'.$user['id'].'",
									"'.$jert['id'].'",
									"'.time().'",
									"'.time().'",
									3,
									"'.mysql_real_escape_string(BNewHist($user)).'",
									"'.mysql_real_escape_string(BNewHist($jert)).'",
									"0","0"
								)
						') or mydie();

						$id = mysql_insert_id();

						// теперь обновляем себя и противника что мы в бою
						if ($is_bot) {
							mysql_query('UPDATE `users_clons` SET `battle` = '.$id.', `battle_t` = 2 , hp=maxhp  WHERE `id`= '.$jert['id']) or mydie();
						} else {
							mysql_query('UPDATE `users` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id`= '.$jert['id']) or mydie();
						}
						mysql_query('UPDATE `users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$user['id']) or mydie();

						addch('<img src=i/magic/attack.gif> <b>'.$user['login'].'</b>, применив магию нападения, внезапно напал на <b>'.$jert['login'].'</b>.',$user['room']) or mydie();
						$p2 = '<b>'.nick_align_klan($user).'</b> и <b>'.nick_align_klan($jert).'</b>';
						//addlog($id,'Часы показывали <span class=date>'.date("Y.m.d H.i").'</span>, когда '.$p2.' бросили вызов друг другу. <BR>');
						addlog($id,"!:S:".time().":".BNewHist($user).":".BNewHist($jert)."\n");

						// лог
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> напал на <font color='.$team_colors[$jert['id_grup']].'>'.nick_hist($jert).'</font> завязался <a href="logs.php?log='.$id.'" target="_blank">бой »»</a><BR>';
						mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

						$q = mysql_query('COMMIT') or mydie();
						Redirect('fbattle.php');
					} else {
						// уже есть бой, вмешиваемся

						// фиксим хп
						mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']) or mydie();

						// находим бой жертвы
						//fix by Bred  and status=0 and win=3 and t1_dead=""
						$q = mysql_query('SELECT * FROM `battle` WHERE `id` = '.$jert['battle'].' and status=0 and win=3 and t1_dead="" ') or mydie();
						$bd = mysql_fetch_assoc($q) or Redirect('ruines.php'); // если не нашли бой - редиректимся

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
						mysql_query('UPDATE `battle` SET `t'.$meteam.'` = CONCAT(`t'.$meteam.'`,";'.$user['id'].'"),  `t'.$meteam.'hist`= CONCAT(`t'.$meteam.'hist`,"'.mysql_real_escape_string(BNewHist($user)).'")  ,`to'.$meteam.'` = "'.time().'", `to'.$enemyteam.'` = "'.(time()-1).'"  WHERE `id` = '.$jert['battle'].' and status=0 and win=3 and t1_dead=""') or mydie();

						if (mysql_affected_rows() > 0) {
							addch('<img src=i/magic/attack.gif> <b>'.$user['login'].'</b> вмешался в <a href=logs.php?log='.$jert['battle'].' target=_blank>поединок »»</a>.',$user['room']) or mydie();
							//addlog($jert['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$meteam).' вмешался в поединок!<BR>');
							$user[battle_t]=$meteam;
							$ac=($user[sex]*100)+mt_rand(1,2);
//							addlog($jert['battle'],"!:V:".time().":".nick_new_in_battle($user).":".$ac."\n");
							addlog($jert['battle'],"!:W:".time().":".BNewHist($user).":".$user[battle_t].":".$ac."\n");
							// выставляем себе номер боя
							mysql_query('UPDATE users SET `battle` = '.$jert['battle'].', `zayavka` = 0, `battle_t`= '.$meteam.' WHERE `id`= '.$user['id']) or mydie();

							$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> вмешался в поединок против <font color='.$team_colors[$jert['id_grup']].'>'.nick_hist($jert).'</font> <a href="logs.php?log='.$jert['battle'].'" target="_blank">бой »»</a><BR>';
							mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();
						}


						$q = mysql_query('COMMIT') or mydie();
						Redirect('fbattle.php');
					}
				} else {
					if ($chkattack) {
						if ($user['id_grup'] == $jert['id_grup']) {
							$error = 'Нельзя нападать на своих...';
						} else {
							$error = 'Жертва ускользнула из комнаты...';
						}
					}
				}
			} else {
				$error = 'Жертва ускользнула из комнаты...';
			}
			$q = mysql_query('COMMIT') or mydie();
		}
	}

	if (isset($_GET['give'])) {
		if (!isset($_SESSION['ruinesactivity']['give'])) {
			$_SESSION['ruinesactivity']['give'] = 1;
			$q = mysql_query('SELECT * FROM ruines_activity_log WHERE mapid = '.$user['ruines'].' and owner = '.$user['id'].' and var = "give"');
			if (mysql_num_rows($q) == 0) {
				mysql_query('INSERT INTO ruines_activity_log (mapid,owner,var,val) VALUES("'.$user['ruines'].'","'.$user['id'].'","give","1")');
			}
		}

		$id = intval($_GET['give']);
		if ($ses['timei'] <= time()) {
			$q = mysql_query('START TRANSACTION') or mydie();
			$q = mysql_query('SELECT * FROM `ruines_items` WHERE `id` = '.$id.' AND type = 0 AND room = '.$user['room'].' FOR UPDATE') or mydie();
			if (mysql_num_rows($q) > 0) {
				$ses['timei'] = time() + $giveitempenalty;
				$item = mysql_fetch_assoc($q) or mydie();

				// удаляем вещь
				mysql_query('DELETE FROM `ruines_items` WHERE `id` = '.$id.' AND room = '.$user['room']) or mydie();

				// получилаем прототип
				$proto = $item['iteam_id'];
				$subtype = $item['extra'];

				$q = mysql_query_cache('SELECT * FROM oldbk.`shop` WHERE `id` = '.$item['iteam_id'],false,24*3600);
				if ($q === FALSE || !count($q)) mydie();
				$dress = $q[0];

				// temp log
				// mysql_query('INSERT INTO ruines_log_temp (`map_id`,`owner`,`atime`,`action`) VALUES ('.$map['id'].','.$user['id'].','.time().',"give:'.$id.':'.$dress['name'].':'.$user['room'].':'.$item['room'].'")');

				$mfinfo = array('stats' => 0, 'hp' => 0, 'bron' => 0);

				if ($dress['type'] < 12) {
					// подсчитываем апы
					$ups = array();
					$level = 0;
					$hp = 0;
					$bron = 0;
					$stat = 0;
					$mf = 0;
					$udar = 0;
					$nparam = 0;
					$duration = 0;

					for ($i = 7; $i <= $map['lowlvl']; $i++) {
						if ($dress['nlevel'] < $i) {
							$level = $iupgrade[$i]['level'];
							$hp += $iupgrade[$i]['hp'];
							$bron += $iupgrade[$i]['bron'];
							$stat += $iupgrade[$i]['stat'];
							$mf += $iupgrade[$i]['mf'];
							$udar += $iupgrade[$i]['udar'];
							$nparam += $iupgrade[$i]['nparam'];
							$duration = $iupgrade[$i]['duration'];
						}
					}

					if (!$level) $level = $map['lowlvl'];

					// апаем
					$dress['name'] = $dress['name'].' ['.$level.'] '.$ritems[$proto][$subtype]['addname'];


					$dress['nlevel'] = $level;
					if ($dress['ghp'] > 0) $dress['ghp'] += $hp;
					if ($dress['bron1'] > 0) $dress['bron1'] += $bron;
					if ($dress['bron2'] > 0) $dress['bron2'] += $bron;
					if ($dress['bron3'] > 0) $dress['bron3'] += $bron;
					if ($dress['bron4'] > 0) $dress['bron4'] += $bron;
					if ($dress['type'] == 3) {
						// шмотка - оружие наносящие дамак
						$dress['minu'] += $udar;
						$dress['maxu'] += $udar;
					} else {
						// просто шмотка
						$dress[$ritems[$proto][$subtype]['directionmf']] += $mf;
						$dress[$ritems[$proto][$subtype]['directionstats']] += $stat;
					}

					if ($dress['maxdur'] < $duration) $dress['maxdur'] = $duration;

					if ($dress['nsila'] > 0) $dress['nsila'] += $nparam;
					if ($dress['nlovk'] > 0) $dress['nlovk'] += $nparam;
					if ($dress['ninta'] > 0) $dress['ninta'] += $nparam;
					if ($dress['nvinos'] > 0) $dress['nvinos'] += $nparam;
					if ($dress['nnoj'] > 0) $dress['nnoj'] += $nparam;
					if ($dress['ntopor'] > 0) $dress['ntopor'] += $nparam;
					if ($dress['ndubina'] > 0) $dress['ndubina'] += $nparam;
					if ($dress['nmech'] > 0) $dress['nmech'] += $nparam;

					// теперь добавляем к вещи статы из конфига
					$cmf = $ritems[$proto][$subtype];

					// статы
					if (isset($cmf['gsila'])) {
						$dress['gsila'] += $cmf['gsila'];
						$mfinfo['stats'] = $cmf['gsila'];
					}
					if (isset($cmf['glovk'])) {
						$dress['glovk'] += $cmf['glovk'];
						$mfinfo['stats'] = $cmf['glovk'];
					}
					if (isset($cmf['ginta'])) {
						$dress['ginta'] += $cmf['ginta'];
						$mfinfo['stats'] = $cmf['ginta'];
					}
					if (isset($cmf['gintel'])) {
						$dress['gintel'] += $cmf['gintel'];
						$mfinfo['stats'] = $cmf['gintel'];
					}
					if (isset($cmf['gmp'])) {
						$dress['gmp'] += $cmf['gmp'];
						$mfinfo['stats'] = $cmf['gmp'];
					}

					// мфы
					if (isset($cmf['mfuvorot'])) $dress['mfuvorot'] += $cmf['mfuvorot'];
					if (isset($cmf['mfauvorot'])) $dress['mfauvorot'] += $cmf['mfauvorot'];
					if (isset($cmf['mfkrit'])) $dress['mfkrit'] += $cmf['mfkrit'];
					if (isset($cmf['mfakrit'])) $dress['mfakrit'] += $cmf['mfakrit'];

					// броня
					if (isset($cmf['bron1'])) {
						$dress['bron1'] += $cmf['bron1'];
						$mfinfo['bron'] = $cmf['bron1'];
					}
					if (isset($cmf['bron2'])) {
						$dress['bron2'] += $cmf['bron2'];
						$mfinfo['bron'] = $cmf['bron2'];
					}
					if (isset($cmf['bron3'])) {
						$dress['bron3'] += $cmf['bron3'];
						$mfinfo['bron'] = $cmf['bron3'];
					}
					if (isset($cmf['bron4'])) {
						$dress['bron4'] += $cmf['bron4'];
						$mfinfo['bron'] = $cmf['bron4'];
					}


					// хелы
					/*
					if (isset($cmf['ghp'])) {
						$dress['ghp'] += $cmf['ghp'];
						$mfinfo['hp'] = $cmf['ghp'];
					}
					*/

					// урон
					if ($dress['type'] == 3) {
						if (isset($cmf['minu'])) $dress['minu'] += $cmf['minu'];
						if (isset($cmf['maxu'])) $dress['maxu'] += $cmf['maxu'];
					}
				} else {
					// вещь не можем апать, скорее всего это свиток. ничего с ним не делаем
				}

				$dur = $item['durability'];
				$present = $item['present'];

				// убираем требование склонности
				$dress['nalign'] = 0;
				$dress['nlevel'] = 0;

				// все что выше 3 серой магии - делаем на 3
				if ($dress['ngray'] > 3) $dress['ngray'] = 3;

				// правим на свитках требования
				if ($dress['id'] == 246) $dress['nintel'] = 3;
				if ($dress['id'] == 247) $dress['nintel'] = 6;
				if ($dress['id'] == 248) $dress['nintel'] = 8;
				if ($dress['id'] == 249) $dress['nintel'] = 10;

				if (count($mfinfo) && strpos($ritems[$proto][$subtype]['addname'],'(мф)') !== false) {
					$mfinfo = mysql_real_escape_string(serialize($mfinfo));
				} else {
					$mfinfo = "";
				}

				mysql_query('INSERT INTO oldbk.`inventory`
						(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`duration`,`maxdur`,`isrep`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
							`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
							,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
							`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`mfinfo`,`img_big`,`getfrom`,`nclass`
						)
						VALUES	(
							"'.mysql_real_escape_string($present).'",
							'.$dress['id'].',
							'.$user['id'].',
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
							"2",
							'.$dress['group'].',"",0,0,0,0,"",0,0,0,"'.$subtype.'","'.$mfinfo.'","'.$dress['img_big'].'",45,4
					)
				') or mydie();

				SaveSerFile($frpath,$ses);
			} else {
				$error = 'Кто-то быстрее...';
			}
			$q = mysql_query('COMMIT') or mydie();
		} else {
			$error = 'Вы сможете поднять вещь через '.($ses['timei']-time()).' секунд...';
		}
	}

	if (isset($_GET['dropkey'])) {
		// ложим ключ на альтарь
		if ($croom == 77 && $user['id_grup'] == 1 && $map['k1owner'] == $user['id'] || $croom == 76 && $user['id_grup'] == 2 && $map['k2owner'] == $user['id']) {
			$q = mysql_query('START TRANSACTION') or mydie();
			$q = mysql_query('SELECT * FROM `ruines_map` WHERE id = '.$map['id'].' FOR UPDATE') or mydie();
			$map = mysql_fetch_assoc($q) or mydie();

			if ($map['t'.$user['id_grup'].'score'] == 1) {
				// ложим ключ и выигрываем. противополжную команду выкидывем из руин

				// ставим 2 очка в команду и отбираем ключ
				mysql_query('UPDATE `ruines_map` SET t'.$user['id_grup'].'score = t'.$user['id_grup'].'score + 1, k'.$user['id_grup'].'owner = 0 WHERE id = '.$map['id']) or mydie();

				// пускаем системку по всем игрокам
				$mids = array();
				$q = mysql_query('SELECT * FROM `users` WHERE `room` BETWEEN '.$map['rooms'].' AND '.($map['rooms']+100).' AND `in_tower` = 2') or mydie();
				while($u = mysql_fetch_assoc($q)) {
					$mids[] = $u['id'];
				}
				if (count($mids)) {
					addch_group('<font color=red>Внимание!</font> <B><font color="'.$team_colors[$user['id_grup']].'">'.$user['login'].'</font></B> положил ключ на свой трон и его команда выиграла турнир',$mids) or mydie();
				}

				$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> положил ключ на свой трон и его команда выиграла турнир.<BR>';
				mysql_query('UPDATE `ruines_log` SET `win` = '.$user['id_grup'].', `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

				// выкидываем противоположную команду из руин
				if ($user['id_grup'] == 1) {
					$meteam = 1;
					$enemyteam = 2;
				} else {
					$meteam = 2;
					$enemyteam = 1;
				}

				$abattles = array();
				$awins = array();

				// для начала прибиваем всех тех кто в бою
				$q = mysql_query('SELECT * FROM `users` WHERE battle > 0 and id_grup = '.$enemyteam.' and ruines = '.$map['id']) or mydie();
				if (mysql_num_rows($q) > 0) {
					$inbattle = array();
					$battles = array();
					$tbattles = array();
					$uids = "";
					$bids = "";
					$botsids = "";
					while($u = mysql_fetch_assoc($q)) {
						if ($u['hp'] > 0) {
							$uids .= $u['id'].',';
							$inbattle[$u['id']] = $u;
							$battles[$u['battle']][] = $u['id'];
						}
						$tbattles[$u['battle']][] = $u['id'];
						if ($u['battle_t'] == 1) {
							$awins[$u['battle']] = 2;
						} else {
							$awins[$u['battle']] = 1;
						}
					}

					// теперь находим клонов в боях
					reset($tbattles);
					while(list($k,$v) = each($tbattles)) {
						$bids .= $k.",";
					}
					$bids = substr($bids,0,strlen($bids)-1);

					$qz = mysql_query('SELECT * FROM `users_clons` WHERE battle IN ('.$bids.') and hp > 0 and bot_online != 5') or mydie();
					while($u = mysql_fetch_assoc($qz)) {
						$u['sex'] = 1;
						$inbattle[$u['id']] = $u;
						$battles[$u['battle']][] = $u['id'];
						$botsids .= $u['id'].',';
					}

					if (strlen($uids) || strlen($botsids)) {
						// если есть кого убивать
						$qbb = mysql_query('SELECT * FROM battle WHERE id IN ('.implode(",",array_keys($tbattles)).')');
						if ($qbb === FALSE) mydie();
						while($qbb2 = mysql_fetch_assoc($qbb)) {
							$abattles[$qbb2['id']] = $qbb2;
						}

						// блокируем бои
						mysql_query('UPDATE battle SET status=1 where id IN ('.implode(",",array_keys($abattles)).')') or mydie();

						// пишем в лог игроков
						$action[0] = 'умер';
						$action[1] = 'погиб';
						$sexi[0] = 'ла';
						$sexi[1] = '';
						$rda = mt_rand(0,1);
						reset($battles);
						while(list($k,$v) = each($battles)) {
							$logtext = "";
							while(list($ka,$va) = each($v)) {
								//$logtext .= '<span class=date>'.date("H:i").'</span> '.nick_in_battle($inbattle[$va],$inbattle[$va]['battle_t']).' <b>'.$action[$rda].$sexi[$inbattle[$va]['sex']].'</b>!<BR>';
								$logtext .= "!:D:".time().":".nick_new_in_battle($inbattle[$va]).":".(($inbattle[$va]['sex']*100)+mt_rand(1,4))."\n";
							}
							addlog($k,$logtext);
						}


						// сносим хелы
						if (strlen($uids)) {
							// выставляем HP в 0
							$uids = substr($uids,0,strlen($uids)-1);
							mysql_query('UPDATE `users` SET hp = 0 WHERE id IN ('.$uids.')') or mydie();
						}

						if (strlen($botsids)) {
							// выставляем HP в 0
							$botsids = substr($botsids,0,strlen($botsids)-1);
							mysql_query('UPDATE `users_clons` SET hp = 0 WHERE id IN ('.$botsids.')') or mydie();
						}

						// вторая блокировка
						mysql_query('UPDATE battle SET t1_dead="finlog" where id IN ('.implode(",",array_keys($abattles)).')') or mydie();
					}
				}

				$gor = GetOpRs();

				$ruine_week = false;
				$ivent = mysql_query("select * from oldbk.ivents where id=3") or mydie();
				$get_ivent = mysql_fetch_array($ivent);
				if ($get_ivent['stat'] == 1) {
					$ruine_week = true;
				}


				// находим проигравшую команду
				$q = mysql_query('SELECT * FROM `users` WHERE id_grup = '.$enemyteam.' and ruines = '.$map['id']) or mydie();
				$eids = "";

				$_all_ruine_users = [];
				while($u = mysql_fetch_assoc($q)) {
					$eids .= $u['id'].',';
					$_all_ruine_users[$u['id']] = $u;

					// лог
					$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> выбывает из турнира.<BR>';
					mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();


					// раздеваем
					undressalltrz($u['id']) or mydie();

					if ($ruine_week) RuinesMakeRep($u,0);

					// выставляем статы
					$qa = mysql_query('SELECT * FROM `ruines_realchars` WHERE `owner` = '.$u['id']) or mydie();
					$tec = mysql_fetch_assoc($qa) or mydie();

					$q2 = mysql_query("select * from effects where owner='{$u[id]}' and type>=1001 and type<=1003") or mydie();

					$hp_bonus = mysql_fetch_array($q2);
					if ($hp_bonus['id']>0) {
						// эффект еще есть
					} else {
						//эфекта такого уже нет!

						$tec['maxhp'] = $tec['maxhp']-$tec['bpbonushp'];
						$tec['bpbonushp'] = 0;

						if ($tec['hp'] > $tec['maxhp']) {
							$tec['hp'] = $tec['maxhp'];
						}
					}

					$hp = $tec['vinos']*6 + ($tec['bpbonushp']);


					// удаляем эффекты пут и травм
					mysql_query('DELETE FROM `effects` WHERE type IN (10,11,12,13) AND `owner` = '.$u['id']) or mydie();


					mysql_query('UPDATE `users` SET 
							`sila` = "'.($tec['sila']+$tec['bpbonussila']).'",
							`lovk` = "'.$tec['lovk'].'",
							`inta` = "'.$tec['inta'].'",
							`vinos` = "'.$tec['vinos'].'",
							`intel` = "'.$tec['intel'].'",
							`mudra` = "'.$tec['mudra'].'",
							`stats` = "'.$tec['stats'].'",
							`noj` = "'.$tec['noj'].'",
							`mec` = "'.$tec['mec'].'",
							`topor` = "'.$tec['topor'].'",
							`dubina` = "'.$tec['dubina'].'",
							`mfire` = "'.$tec['mfire'].'",
							`mwater` = "'.$tec['mwater'].'",
							`mair` = "'.$tec['mair'].'",
							`mearth` = "'.$tec['mearth'].'",
							`mlight` = "'.$tec['mlight'].'",
							`mgray` = "'.$tec['mgray'].'",
							`mdark` = "'.$tec['mdark'].'",
							`master` = "'.$tec['master'].'",
							`mana` = "'.$tec['mana'].'",
							`maxmana` = "'.$tec['mana'].'",
							`maxhp` = "'.$hp.'",
							`bpbonussila` = '.$tec['bpbonussila'].',
							`bpbonushp` = '.$tec['bpbonushp'].' WHERE `id` = '.$u['id']
					) or mydie();

					// проигравшя команда получает по 200 репы, и легкую или среднюю травму на 3 часа и задержку на посещение на 2 часа
					$add_rep = getreplooser($u['level']);
					$ar = 0;
					if ($u['prem']) $ar += 0.1;
					$ua = intval($u['align']);
					if ($ua == 1) $ua = 6;
					if ($gor == $ua) $ar += 0.05;

					$add_rep = intval($add_rep * (1+$ar));

					addchp ('<font color=red>Внимание!</font> Вы проиграли турнир и получаете <b>'.$add_rep.'</b> опыта и травму.','{[]}'.$u['login'].'{[]}',-1,$u['id_city']) or mydie();
                                        mysql_query('UPDATE `users` SET `exp` = `exp` + '.$add_rep.'  WHERE `id` = '.$u['id']) or mydie();

					/*
					$rec = array();
		    			$rec['owner']=$u[id];
					$rec['owner_login']=$u[login];
					$rec['owner_balans_do']=$u['money'];
					$rec['owner_balans_posle']=$u['money'];
					$rec['owner_rep_do']=$u['repmoney'];
					$rec['target_login'] = "Руины";
					$rec['owner_rep_posle']=$u['repmoney']+$add_rep;
					$rec['sum_rep'] = $add_rep;
					$rec['type'] = 204;
					if (add_to_new_delo($rec) === FALSE) mydie();
					*/

					if (true) {
						$rr = mt_rand(0,100);
						$ttext = "";
						if ($rr < 60)
							settravmabyinfotrz($u,11,$travmatime) or mydie();
						if ($rr >= 60 && $rr < 85)
							settravmabyinfotrz($u,12,$travmatime) or mydie();
						if ($rr >= 85)
							settravmabyinfotrz($u,13,$travmatime) or mydie();
					}


					//смотрим бонус
					$qeffb = mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$u['id']}'  and `type` = '9106' LIMIT 1;") or mydie();
					$bonuseffect = mysql_fetch_array($qeffb);
					if ($bonuseffect['id'] > 0) {
						$topen=(int)($looserpenalty-($looserpenalty*$bonuseffect['add_info']));
					} else {
						$topen=$looserpenalty;
					}

					// выставляем время до след посещения руин
					mysql_query('INSERT INTO oldbk.`ruines_var` (`owner`,`var`,`val`) 
								VALUES(
									'.$u['id'].',
									"cango",
									'.(time()+$topen).'
								) 
								ON DUPLICATE KEY UPDATE
									`val` = '.(time()+$topen)
					) or mydie();

					mysql_query('INSERT INTO avalon.`ruines_var` (`owner`,`var`,`val`) 
								VALUES(
									'.$u['id'].',
									"cango",
									'.(time()+$topen).'
								) 
								ON DUPLICATE KEY UPDATE
									`val` = '.(time()+$topen)
					) or mydie();

				}
				if (strlen($eids)) {
					$eids = substr($eids,0,strlen($eids)-1);

					// удаляем молчи кроме паловских
					mysql_query('DELETE FROM `effects` WHERE `owner` IN ('.$eids.') AND type = 2 AND pal <> 1') or mydie();
					mysql_query('UPDATE users set slp = 0 WHERE `id` IN ('.$eids.') AND `id` NOT IN (SELECT `owner` FROM effects where `owner` IN ('.$eids.') AND type = 2)') or mydie();

					// ставим всех на старт, выставляем что они уже не в башне и удаляем номер инстанса руин, номер команды в 0
					mysql_query('UPDATE `users` SET `in_tower` = 0, `id_grup` = 0, `ruines` = 0, `room` = 999 WHERE `id` IN ('.$eids.')') or mydie();

					// чистим смерти
					mysql_query('DELETE FROM `ruines_var` WHERE `var` = "rdeath" AND owner IN ('.$eids.')') or mydie();

					// чистим реальные статы
					mysql_query('DELETE FROM `ruines_realchars` WHERE `owner` IN ('.$eids.')') or mydie();

					// удаляем шмот в инвентаре с руин
					mysql_query('DELETE FROM oldbk.`inventory` WHERE bs_owner = 2 AND `owner` IN ('.$eids.')') or mydie();

					// вроде всё ) выкинули и обработали, теперь свою команду обрабатываем
				}

				$error = 'Вы положили ключ на трон и выиграли турнир.';

				// победившая команда вне зависимости от сокровищ получает по 1000 репы и задержку на посещение руин на 6 часов.

				$gor = GetOpRs();

				// находим победившую команду
				$q = mysql_query('SELECT * FROM `users` WHERE id_grup = '.$meteam.' and ruines = '.$map['id']) or mydie();
				$_user_win_list = array();
				while($u = mysql_fetch_assoc($q)) {
					$_user_win_list[$u['id']] = $u;

					$add_rep = getrepwinner($u['level']);
					$ar = 0;
					if ($u['prem']) $ar += 0.1;
					$ua = intval($u['align']);
					if ($ua == 1) $ua = 6;
					if ($gor == $ua) $ar += 0.05;

					$add_rep = intval($add_rep * (1+$ar));

					addchp ('<font color=red>Внимание!</font> Вы выиграли турнир и получаете <b>'.$add_rep.'</b> опыта. Сокровищница открыта, ищите её рядом с кладбищем.','{[]}'.$u['login'].'{[]}',-1,$u['id_city']) or mydie();
                                        mysql_query('UPDATE `users` SET `exp` = `exp`+ '.$add_rep.' WHERE `id` = '.$u['id']) or mydie();


					if (mt_rand(0,100)<15)
					{
					DropBonusItem(112004,$u,"Удача","Коллекция №2: Ангельская поступь",0,1,20,true); // Карта Комментатора выдается при победе в Руинах - шанс 15%
					}





						mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) VALUES('.$u['id'].',"colln4_wins",	"1") ON DUPLICATE KEY UPDATE `val` = val + 1') or mydie(); // коллекция руинты №4
						$cw = mysql_query('SELECT * FROM ruines_var WHERE var = "colln4_wins" AND owner = '.$u['id']) or mydie();
						$cw = mysql_fetch_assoc($cw) ;
						if ($cw !== FALSE)
							{
							$cw = $cw['val'];
							if ($cw % 10 == 0)
								{

								//проверка наличия футляра или готовой
								//$count_fut = mysql_query('select * from oldbk.inventory where owner='.$u['id'].' and (prototype=114010 OR prototype=114000) limit 1') or mydie();
								//$count_fut = mysql_fetch_assoc($count_fut) ;
								//if (!($count_fut['id']>0))
								if (true)
									{
										DropBonusItem(114010,$u,"Удача","Коллекция №4: «Руины Старого Замка»",0,1,20,true);
									}
								}
							}



					// легендарный квест
					mysql_query('UPDATE oldbk.map_var SET val = val + 1 WHERE owner = "'.$u['id'].'" AND var = "q32s2"') or mydie();

					/*
					$rec = array();
		    			$rec['owner']=$u[id];
					$rec['owner_login']=$u[login];
					$rec['owner_balans_do']=$u['money'];
					$rec['owner_balans_posle']=$u['money'];
					$rec['owner_rep_do']=$u['repmoney'];
					$rec['target_login'] = "Руины";
					$rec['owner_rep_posle']=$u['repmoney']+$add_rep;
					$rec['sum_rep'] = $add_rep;
					$rec['type'] = 203;
					if (add_to_new_delo($rec) === FALSE) mydie();
					*/

					// пишем колва подбед

					//лог прогресса + победы в руинах
					mysql_query("INSERT INTO oldbk.users_progress set owner='{$u['id']}', awinruins=1 ON DUPLICATE KEY UPDATE awinruins=awinruins+1") or mydie();

					mysql_query('INSERT INTO `ruines_var` (`owner`,`var`,`val`) 
								VALUES(
									'.$u['id'].',
									"wins",
									"1"
								) 
								ON DUPLICATE KEY UPDATE
									`val` = val + 1
					') or mydie();

					$qw = mysql_query('SELECT * FROM ruines_var WHERE var = "wins" AND owner = '.$u['id']) or mydie();
					$ww = mysql_fetch_assoc($qw);
					if ($ww !== FALSE) {
						$ww = $ww['val'];
						$wexist = array();
						$in = str_ireplace("|","",$u['medals']);
						$t = explode(";",$in);
						$addm = "";
						$msg = "";
						$addrep = 0;

						while(list($k,$v) = each($t)) {
							$v = trim($v);
							if ($v == "100" || $v == "101" || $v == "102" || $v == "103") {
								$wexist[$v] = 1;
							}
						}
						if ($ww >= 100 && !isset($wexist[100])) {
							$addm = "100;";
							$addrep = "3000";
							$msg = "<font color=red>Внимание!</font> Вы получили <b>".$addrep."</b> репутации и значок в информацию о персонаже за 100 побед в Руинах Старого Замка!";
						}
						if ($ww >= 300 && !isset($wexist[101])) {
							$addm = "101;";
							$addrep = "5000";
							$msg = "<font color=red>Внимание!</font> Вы получили <b>".$addrep."</b> репутации и значок в информацию о персонаже за 300 побед в Руинах Старого Замка!";

						}
						if ($ww >= 500 && !isset($wexist[102])) {
							$addm = "102;";
							$addrep = "10000";
							$msg = "<font color=red>Внимание!</font> Вы получили <b>".$addrep."</b> репутации и значок в информацию о персонаже за 500 побед в Руинах Старого Замка!";
						}
						if ($ww >= 1000 && !isset($wexist[103])) {
							$addm = "103;";
							$addrep = "15000";
							$msg = "<font color=red>Внимание!</font> Вы получили <b>".$addrep."</b> репутации и значок в информацию о персонаже за 1000 побед в Руинах Старого Замка!";
						}
						if (strlen($addm)) {
							mysql_query('UPDATE users SET medals = "'.$u['medals'].$addm.'", rep = rep + '.$addrep.', `repmoney` = `repmoney` + '.$addrep.' WHERE id = '.$u['id']) or mydie();
							addchp ($msg,'{[]}'.$u['login'].'{[]}',-1,$u['id_city']) or mydie();

							$rec = array();
				    			$rec['owner']=$u[id];
							$rec['owner_login']=$u[login];
							$rec['owner_balans_do']=$u['money'];
							$rec['owner_balans_posle']=$u['money'];
							$rec['owner_rep_do']=$u['repmoney'];
							$rec['target_login'] = "Руины";
							$rec['owner_rep_posle']=$u['repmoney']+$addrep;
							$rec['sum_rep'] = $addrep;
							$rec['type'] = 250;
							if (add_to_new_delo($rec) === FALSE) mydie();

						}
					}
				}

				$map['t'.$user['id_grup'].'score']++;
				// всё )

				$map['k'.$user['id_grup'].'owner'] = 0 ;
				$q = mysql_query('COMMIT') or mydie();

				//win team $_user_win_list
				if($_user_win_list && is_array($_user_win_list)) {
					foreach ($_user_win_list as $_user_) {
						try {
							$User = new \components\models\User($_user_);
							$Quest = $app->quest
								->setUser($User)
								->get();
							$Checker = new \components\Component\Quests\check\CheckerEvent();
							$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RUIN_WIN;

							if(($Item = $Quest->isNeed($Checker)) !== false) {
								$Quest->taskUp($Item);
							}

						} catch (Exception $ex) {
							\components\Helper\FileHelper::writeException($ex, 'ruine');
						}
					}
				}
				//region rating
				try {
                    $AllRuineUsers = \components\models\User::where('ruines', '=', $map['id'])->get()->toArray();
                    if($AllRuineUsers) {
                        foreach ($AllRuineUsers as $_user_) {
                            $_user_id_ = $_user_['id'];
                            try {
                                $RuineRating = new \components\Helper\rating\RuineRating();
                                $RuineRating->value_add = isset($_user_win_list[$_user_id_]) ? 5 : 1;

                                $app->applyHook('event.rating', $_user_, $RuineRating);
                            } catch (Exception $ex) {
                                $app->logger->addEmergency((string)$ex);
                            }
                        }
                    }
                } catch (Exception $ex) {
                    $app->logger->addEmergency((string)$ex);
                }
                //endregion

				// после коммита вызываем финиш
				reset($abattles);
				while(list($k,$v) = each($abattles)) {
					finish_battle($awins[$k],$v,0,$v['type'],0,0);
					if ($user['id_grup'] == 1) {
						addlog($v['id'],"<span class=date>".date("H:i")."</span> Бой окончен, турнир выиграла синяя команда.\n");
					} else {
						addlog($v['id'],"<span class=date>".date("H:i")."</span> Бой окончен, турнир выиграла красная команда.\n");
					}
				}


			} else {
				// добавляем очко команде, забираем ключ у игрока
				mysql_query('UPDATE `ruines_map` SET t'.$user['id_grup'].'score = t'.$user['id_grup'].'score + 1, k'.$user['id_grup'].'owner = 0 WHERE id = '.$map['id']) or mydie();

				// выкидываем рандомно ключ
				$keyroom = 0;
				do {
					$keyroom = mt_rand(1,77);
				} while(in_array($keyroom,$keyexcluderooms));
				mysql_query('INSERT INTO `ruines_items` (type,name,img,room,extra) VALUES ("4","Ключ","",'.($map['rooms']+$keyroom).',0)') or mydie();

				// пускаем системку по всем игрокам
				$mids = array();
				$q = mysql_query('SELECT * FROM `users` WHERE `room` BETWEEN '.$map['rooms'].' AND '.($map['rooms']+100).' AND `in_tower` = 2') or mydie();
				while($u = mysql_fetch_assoc($q)) {
					$mids[] = $u['id'];
				}
				if (count($mids)) {
					addch_group('<font color=red>Внимание!</font> <B><font color="'.$team_colors[$user['id_grup']].'">'.$user['login'].'</font></B> положил ключ на свой трон. Новый ключ появился в руинах.',$mids) or mydie();
				}

				$error = 'Вы положили ключ на трон';
				$map['t'.$user['id_grup'].'score']++;

				$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> положил ключ на свой трон.<BR>';
				mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

				$map['k'.$user['id_grup'].'owner'] = 0;
				$q = mysql_query('COMMIT') or mydie();
			}

			try {
				$User = new \components\models\User($user);
				$Quest = $app->quest
					->setUser($User)
					->get();
				$Checker = new \components\Component\Quests\check\CheckerEvent();
				$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RUIN_KEY;

				if(($Item = $Quest->isNeed($Checker)) !== false) {
					$Quest->taskUp($Item);
				}

			} catch (Exception $ex) {

			}
		}
	}

	if (isset($_GET['heal'])) {
		if ($croom == 75 || ($map['t'.$user['id_grup'].'score'] >= 2 && ($croom == 76 || $croom == 77))) {
			// колодец жизни на кладбище
			mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE `id` = '.$user['id']) or mydie();
			$error = 'Вы сделали пару глотков и полностью восстановили свои силы...';

			// для отображения полных хп
			$user['hp'] = $user['maxhp'];
		} else {
			// колодец жизни из монстра

			$id = intval($_GET['heal']);

			$q = mysql_query('START TRANSACTION') or mydie();
			// проверяем
			$q = mysql_query('SELECT * FROM `ruines_items` WHERE id = '.$id.' AND type = 5 AND `room` = '.$user['room'].' FOR UPDATE') or mydie();

			if (mysql_num_rows($q) > 0) {
				// восстанавливаем хп
				mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE `id` = '.$user['id']) or mydie();
				$error = 'Вы сделали пару глотков и полностью восстановили свои силы. Обернувшись вы не увидели колодец...';

				// удаляем колодец
				mysql_query('DELETE FROM `ruines_items` WHERE id = '.$id) or mydie();

				// для отображения полных хп
				$user['hp'] = $user['maxhp'];
			}
			$q = mysql_query('COMMIT') or mydie();
		}
	}

	if (isset($_GET['givekey'])) {
		$id = intval($_GET['givekey']);
		// проверяем время
		if (!(($map['starttime'] + $keytime) > time())) {
			if ($ses['timei'] <= time()) {
				$q = mysql_query('START TRANSACTION') or mydie();
				// проверяем тип - ключ, комнату
				$q = mysql_query('SELECT * FROM `ruines_items` WHERE id = '.$id.' AND type = 4 AND `room` = '.$user['room'].' FOR UPDATE') or mydie();
				if (mysql_num_rows($q) > 0) {
					$key = mysql_fetch_assoc($q) or mydie();
					// выставляем в таблицу карты кто поднял ключ
					mysql_query('UPDATE `ruines_map` SET k'.$user['id_grup'].'owner = '.$user['id'].', k'.$user['id_grup'].'timeout = '.time().' WHERE id = '.$map['id']) or mydie();

					// удаляем ключ с пола
					mysql_query('DELETE FROM `ruines_items` WHERE id = '.$id) or mydie();

					// ставим таймаут на поднятие
					$ses['timei'] = time() + $givekeypenalty;

					$error = 'Вы подняли ключ, теперь вам необходимо донести его до трона своей команды.';

					// пускаем системку по всем игрокам
					$mids = array();
					$q = mysql_query('SELECT * FROM `users` WHERE `room` BETWEEN '.$map['rooms'].' AND '.($map['rooms']+100).' AND `in_tower` = 2') or mydie();
					while($u = mysql_fetch_assoc($q)) {
						$mids[] = $u['id'];
					}
					if (count($mids)) {
						addch_group('<font color=red>Внимание!</font> <B><font color="'.$team_colors[$user['id_grup']].'">'.$user['login'].'</font></B> поднял ключ в локации <B>'.$rooms[$croom][0].'</B>.',$mids) or mydie();
					}

					$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> поднял ключ в локации <B>'.$rooms[$croom][0].'</B>.<BR>';
					mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

					SaveSerFile($frpath,$ses);
				}
				$q = mysql_query('COMMIT') or mydie();
			} else {
				$error = 'Вы сможете поднять вещь через '.($ses['timei']-time()).' секунд...';
			}
		}
	}


	if (isset($_GET['openchest'])) {
		// открываем сундук
		$id = intval($_GET['openchest']);
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM `ruines_items` WHERE id = '.$id.' AND type = 2 AND `room` = '.$user['room'].' AND extra = 0 FOR UPDATE') or mydie();
		if (mysql_num_rows($q) > 0) {
			$chest = mysql_fetch_assoc($q) or mydie();

			// достаём все вещи из сундука
			mysql_query('UPDATE `ruines_items` SET type = 0 WHERE type = 3 AND room = '.$user['room']) or mydie();

			// открывем сундук
			mysql_query('UPDATE `ruines_items` SET extra = 1 WHERE id = '.$id) or mydie();

			// добавляем таймаут на хотьбу
			if ($ses['time'] <= time()) {
				$ses['time'] = time() + $chestpenalty;
			} else {
				$ses['time'] += $chestpenalty;
			}
			SaveSerFile($frpath,$ses);
		}
		$q = mysql_query('COMMIT') or mydie();
	}



	// передвижение
	if (isset($_GET['path'])) {
		$path = intval($_GET['path']);
		if ($path < 1 || $path > 4) Redirect('ruines.php');

		// проверяем есть ли путы
		$q = mysql_query('SELECT * FROM `effects` WHERE `type` = 10 AND `time` >= '.time().' AND `owner` = '.$user['id']) or mydie();
		if (mysql_num_rows($q) > 0) $trap = 'Вы парализованы и не можете двигаться...';

		if (empty($trap) && $ses['time'] <= time()) {
			if ($rooms[$croom][$path]) {
				$newroom = $rooms[$croom][$path];

				// temp log
				// mysql_query('INSERT INTO ruines_log_temp (`map_id`,`owner`,`atime`,`action`) VALUES ('.$map['id'].','.$user['id'].','.time().',"path")');
				// id, map_id, owner, atime, action

				$q = mysql_query('SELECT * FROM ruines_activity_log WHERE mapid = '.$user['ruines'].' and owner = '.$user['id'].' and var = "path"');
				if (mysql_num_rows($q) == 0) {
					mysql_query('INSERT INTO ruines_activity_log (mapid,owner,var,val) VALUES("'.$user['ruines'].'","'.$user['id'].'","path","'.time().'")');
				} else {
					$chk22 = mysql_fetch_assoc($q);
					if (time() - $chk22['val'] > 22*60) {
						// не двигался больше 22 минут
						mysql_query('INSERT INTO ruines_activity_log (mapid,owner,var,val) VALUES("'.$user['ruines'].'","'.$user['id'].'","22path","'.time().'")');
					}
					mysql_query('UPDATE ruines_activity_log SET val = '.time().' WHERE mapid = '.$user['ruines'].' and owner = '.$user['id'].' and var = "path"');
				}



				// Сначала пробуем перейти. В запросе есть условие на battle = 0, что перс не в бою.
				// Если перейти удастся, то будем делать дальнейшие действия.
		   		mysql_query('UPDATE `users` SET `room` = '.($map['rooms']+$newroom).' WHERE `users`.`id` = '.$user['id'].' AND `battle` = 0 AND in_tower = 2') or mydie();
				if (mysql_affected_rows() > 0) {
					// проверяем, что если мы ключеносец - то добавляем пенальти
					$addtime = 0;
					if ($map['k'.$user['id_grup'].'owner'] == $user['id']) {
						$addtime = $keypenalty;
        				}

					if ($croom == 75) {
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> покинул <b>Кладбище</b> и вернулся в турнир.<BR>';
						mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();
					}

					// зелье гермеса ускоряет движение
					$germes = 0;
					$q = mysql_query('SELECT * FROM `effects` WHERE `type` = 605 AND `time` >= '.time().' AND `owner` = '.$user['id']) or mydie();
					if (mysql_num_rows($q) > 0) {
						$germes = -3;
					}


					$ses['time'] = time() + $rooms[$rooms[$croom][$path]][5]+$addtime + $germes; // штраф на хождение той комнату КУДА мы перешли
					if ($user['klan'] == "radminion") {
						$ses['time'] = time();
					}

					// ушел из комнаты, в $user['room'] еще старая комната
					$mids = array();
					$list = mysql_query('SELECT * FROM `users` WHERE `room` = '.$user['room'].' AND `in_tower` = 2') or mydie();
					while($u = mysql_fetch_assoc($list)) {
						if($u['id'] != $user['id']) {
							$mids[] = $u['id'];
						}
					}
					if (count($mids)) addch_group('<font color=red>Внимание!</font> <B>'.$user['login'].'</B> отправился в <B>'.$rooms[$newroom][0].'</B>.',$mids);

					// пришел в комнату
					$mids = array();
					$list = mysql_query('SELECT * FROM `users` WHERE `room` = '.($map['rooms']+$newroom).' AND `in_tower` = 2') or mydie();
					while($u = mysql_fetch_assoc($list)) {
						if($u['id'] != $user['id']) {
							$mids[] = $u['id'];
						}
					}
					if (count($mids)) addch_group('<font color=red>Внимание!</font> <B>'.$user['login'].'</B> вошел в комнату.',$mids);

					$croom = $newroom; // новая комната
					$user['room'] = ($map['rooms']+$newroom);

					SaveSerFile($frpath,$ses);

					// обработка ловушек, type - 1 ловушка
					$q = mysql_query('START TRANSACTION') or mydie();
					$q = mysql_query('SELECT * FROM ruines_items WHERE type = 1 AND room = '.$user['room'].' AND extra <> '.$user['id'].' FOR UPDATE') or mydie();

					if (mysql_num_rows($q) > 0) {
						$trap = mysql_fetch_assoc($q) or mydie();


						// снимаем 50% хп
						if($user['maxhp']/$user['hp'] < 3) {
							$newhp = round($user['hp']/2);
							$percent = 50;
						} else {
							$newhp = $user['hp'];
							$percent = 0;
						}

						$nomove = mt_rand(1,5);
						$nomovetime = time()+$nomove*60;

						mysql_query('UPDATE users SET `hp` = '.$newhp.', `fullhptime` = '.time().' WHERE `id` = '.$user['id']) or mydie();

						$user['hp'] = $newhp;
						$user['fullhptime'] = time();

						$q = mysql_query('SELECT * FROM `users` WHERE `id` = "'.$trap['extra'].'"') or mydie();
						$trap_owner = mysql_fetch_assoc($q) or mydie();

						addch("<img src=i/magic/trap.gif> {$user[login]} угодил в ловушку.. Парализован на {$nomove} минут...") or mydie();
						addchp('<font color=red>Внимание!</font> <B>'.$user['login'].'</B> попал в вашу ловушку в '.$rooms[$newroom][0].'. Парализован на '.$nomove.' минут...','{[]}'.$trap_owner['login'].'{[]}',-1,$trap_owner['id_city']) or mydie();

						// напоролись на ловушку
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> напоролся на ловушку, установленную <font color='.$team_colors[$trap_owner['id_grup']].'>'.nick_hist($trap_owner).'</font><BR>';
						mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();


						mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ('.$user['id'].',"Путы",'.$nomovetime.',10)') or mydie();
						mysql_query('DELETE from ruines_items where id = '.$trap['id']) or mydie();

						// пишем текст
						$trap = '<b>Вы попали в ловушку, поставленную персонажем <B><font color="'.$team_colors[$trap_owner['id_grup']].'">'.$trap_owner['login'].'</font></b> не можете двигаться '.$nomove.' минут';
					}
					$q = mysql_query('COMMIT') or mydie();

				        // проверяем на бой в сокровищнице
					if ($newroom == 78) {
						mysql_query('START TRANSACTION') or mydie();
						$q = mysql_query('SELECT * FROM `ruines_map` WHERE id = '.$map['id'].' FOR UPDATE') or mydie();
						$san = mysql_fetch_assoc($q) or mydie();
						if ($san['sanct'] >= 0) {
							// надо начинать бой с ботами в сокровищнице
							if ($san['sanct']) {
								// бой уже идёт, присоединяемся
								$q = mysql_query('SELECT * FROM `battle` WHERE `id` = '.$san['sanct']) or mydie();
								if (mysql_num_rows($q)) {
									$bd = mysql_fetch_assoc($q);

									$t1 = explode(";",$bd['t1']);

									// проставляем кто-где
									$meteam = 1;
									$enemyteam = 2;

									addch('<img src=i/magic/attack.gif> <b>'.$user['login'].'</b> вмешался в <a href=logs.php?log='.$bd['id'].' target=_blank>поединок »»</a>.',$map['rooms']+$newroom) or mydie();
//									addlog($bd['id'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$meteam).' вмешался в поединок!<BR>');
									$user[battle_t]=$meteam;
									$ac=($user[sex]*100)+mt_rand(1,2);
									addlog($bd['id'],"!:V:".time().":".nick_new_in_battle($user).":".$ac."\n");

									// добавляем себя в массив боя
									mysql_query('UPDATE `battle` SET `t'.$meteam.'` = CONCAT(`t'.$meteam.'`,";'.$user['id'].'"),  `t'.$meteam.'hist`= CONCAT(`t'.$meteam.'hist`,"'.mysql_real_escape_string(BNewHist($user)).'")  ,`to'.$meteam.'` = "'.time().'", `to'.$enemyteam.'` = "'.(time()-1).'"  WHERE `id` = '.$bd['id']) or mydie();

									// выставляем себе номер боя
									mysql_query('UPDATE `users` SET `battle` = '.$bd['id'].', `battle_t`= '.$meteam.' WHERE `id`= '.$user['id']) or mydie();

									// обновляем телу фрейм
									addchp ('<font color=red>Внимание!</font> Вы вмешались в бой.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or mydie();

									$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> вмешался в бой за сокровища <a href="logs.php?log='.$bd['id'].'" target="_blank">бой »»</a>.<BR>';
									mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();

								}
							} else {
								// боя нет, создаём новый

								// для начала создаём клонов
								$botids = "";
								$bothist = "";
								$bothista = "";
								if (isset($bot_id[$map['lowlvl']])) {
									while(list($k,$v) = each($bot_id[$map['lowlvl']]["end"])) {
										$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$k) or mydie();
										$BOT = mysql_fetch_array($q) or mydie();
										$BOT['protid'] = $BOT['id'];

										$BOT_items = load_mass_items_by_id($BOT);


										for ($i = 0; $i < $v; $i++) {
											mysql_query('INSERT INTO `users_clons` SET 
													`login` = "'.$BOT['login'].' (иллюзия '.($i+1).')",
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
											') or mydie();
											$botids .= mysql_insert_id().",";
											$bothist .= BNewHist($BOT);
											$bothista .= nick_align_klan($BOT).", ";
										}
									}
								}

								$botids = substr($botids,0,strlen($botids)-1);

								// клонов создали, делаем бой
								mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']) or mydie();

								mysql_query('INSERT INTO `battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
										VALUES
										(
											"Бой в руинах за сокровища",
											"",
											"'.mt_rand(1,3).'",
											"12",
											"0",
											"'.$user['id'].'",
											"'.str_replace(",",";",$botids).'",
											"'.time().'",
											"'.time().'",
											3,
											"'.mysql_real_escape_string(BNewHist($user)).'",
											"'.mysql_real_escape_string($bothist).'",
											"0","0"
										)
								') or mydie();

								$id = mysql_insert_id();

								// теперь обновляем себя и противника что мы в бою
								mysql_query('UPDATE `users_clons` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$botids.')') or mydie();
								mysql_query('UPDATE `users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$user['id']) or mydie();


								$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> зашёл в <b>Сокровищницу</b> и завязался <a href="logs.php?log='.$id.'" target="_blank">бой »»</a>.<BR>';
								mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) or mydie();


								// пишем номер боя для остальных
								mysql_query('UPDATE `ruines_map` SET `sanct` = '.$id.' WHERE `id` = '.$map['id']) or mydie();


								$p2 = '<b>'.nick_align_klan($user).'</b> и <b>'.($bothista).'</b>';
//								addlog($id,'Часы показывали <span class=date>'.date("Y.m.d H.i").'</span>, когда '.$p2.' бросили вызов друг другу. <BR>');
								addlog($id,"!:S:".time().":".BNewHist($user).":".$bothist."\n");

								// обновляем телу фрейм
								addchp ('<font color=red>Внимание!</font> На вас напали Призраки Старого замка!<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or mydie();
							}
							mysql_query('COMMIT') or mydie();
							Redirect('fbattle.php');
						}
						mysql_query('COMMIT') or mydie();
					}
				} else {
					Redirect('ruines.php');
				}
			}
		}
	}

	$diffwalk = $ses['time'] - time();
	$diffitem = $ses['timei'] - time();
?>

<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
<STYLE>
	.H3			{ COLOR: #8f0000;  FONT-FAMILY: Arial;  FONT-SIZE: 12pt;  FONT-WEIGHT: bold;}
</STYLE>

<SCRIPT src='i/commoninf.js'></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" >

var Hint3Name = '';

function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT id="'+name+'" TYPE=text NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}

function Prv(logins)
{
	top.frames['bottom'].window.document.F1.text.focus();
	top.frames['bottom'].document.forms[0].text.value = logins + top.frames['bottom'].document.forms[0].text.value;
}

function returned2(s){
	if (top.oldlocation != '') { top.frames['main'].navigate(top.oldlocation+'?'+s+'tmp='+Math.random()); top.oldlocation=''; }
	else { top.frames['main'].navigate('main.php?'+s+'tmp='+Math.random()) }
}

function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
	Hint3Name='';
}

function fastshow2 (content,event) {
	var el = document.getElementById("mmoves");
	event = event || window.event;
	var o = event.srcElement;
	if (content == '') { el.innerHTML =  '';}
	if (content!='' && el.style.visibility != "visible") {el.innerHTML = '<small>'+content+'</small>';}
	var x = event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft - el.offsetWidth + 5;
	var y = event.clientY + document.documentElement.scrollTop + document.body.scrollTop+20;
	if (x + el.offsetWidth + 3 > document.body.clientWidth + document.body.scrollLeft) { x=(document.body.clientWidth + document.body.scrollLeft - el.offsetWidth - 5); if (x < 0) {x=0}; }
	if (y + el.offsetHeight + 3 > document.body.clientHeight  + document.body.scrollTop) { y=(document.body.clientHeight + document.body.scrollTop - el.offsetHeight - 3); if (y < 0) {y=0}; }
	if (x<0) {x=0;}
	if (y<0) {y=0;}
	el.style.left = x + "px";
	el.style.top  = y + "px";
	if (el.style.visibility != "visible") {
		el.style.visibility = "visible";
	}
}
function hideshow () {
	document.getElementById("mmoves").style.visibility = 'hidden';
}

</script>
</HEAD>

<body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor=#D7D7D7 onload="top.setHP(<?php echo $user['hp'] ?>,<?php echo $user['maxhp'] ?>,1);">
<div id=hint4 class=ahint></div>
<?
 if ($do_sound!='') {echo $do_sound;}
?>
<TABLE bgcolor="#D7D7D7" width=100% border=0 cellspacing=0 cellpadding=0>

<TR valign=top><TD><?php echo nick($user);?></TD>
<TD class='H3' align=right><?php echo $rooms[$croom][0] ?>&nbsp; &nbsp;

<?php

	if (($map['starttime'] + $attacktime) <= time() && $croom != 75) {
?>
		<IMG align=top SRC=http://i.oldbk.com/i/tower/attack.gif WIDTH=66 HEIGHT=24 ALT="Напасть на..." style="cursor:pointer" onclick="findlogin('Напасть на','ruines.php','attack')">
<?php
	}
?>

</TD>
<TR>
<TD valign=top>
<a href="ruines_log.php?id=
<?php
	echo $map['id'];
?>
" target="_blank">Лог турнира</a><br>
<?php
	echo 'Счёт: <font color='.$team_colors[1].'>'.$map['t1score'].'</font> - <font color='.$team_colors[2].'>'.$map['t2score'].'</font><br>';
?>
<br>

<B>
<?php
	if ($map['k'.$user['id_grup'].'owner'] == $user['id']) {
		echo 'У вас находится древний ключ, быстрее принесите его к своему трону.<br>';
	}
?>
</b>

<FONT COLOR=red>
<?php
	if (!empty($error)) {
		echo $error;
		echo '<br>';
	}
?>
</FONT>

<FONT COLOR=red>
<?php
	if (!empty($trap)) {
		echo $trap;
		echo '<br>';
	}
?>
</FONT>


<?php
	// пишем колво в руинах
	$r = 0;
	$b = 0;
	$rd = 0;
	$bd = 0;
	$q = mysql_query('SELECT * FROM `users` WHERE ruines = '.$map['id']) or mydie();
	while($u = mysql_fetch_assoc($q)) {
		$du = (int)($u['room']*0.01);
		$du = $du * 100;
		$uroom = $u['room'] - $du;
		if ($uroom == 75) {
			if ($u['id_grup'] == 1) {
				$bd++;
			} else {
				$rd++;
			}
		} else {
			if ($u['id_grup'] == 1) {
				$b++;
			} else {
				$r++;
			}
		}
	}

	echo '<font color=red><IMG SRC=http://i.oldbk.com/i/lock.gif WIDTH=20 HEIGHT=15 BORDER=0 ALT="приват команде красных" style="cursor:pointer" onClick="Prv(\'private [team-red] \')"> красных '.$r.' (+'.$rd.' на кладбище)  </font><br>';
	echo '<font color=blue><IMG SRC=http://i.oldbk.com/i/lock.gif WIDTH=20 HEIGHT=15 BORDER=0 ALT="приват команде синих" style="cursor:pointer" onClick="Prv(\'private [team-blue] \')"> синих '.$b.' (+'.$bd.' на кладбище) </font><br>';



	// списки боёв моей команды
	$q = mysql_query('SELECT * FROM `users` WHERE battle > 0 AND ruines = '.$map['id'].' AND id_grup = '.$user['id_grup']) or mydie();
	if (mysql_num_rows($q) > 0) {
		echo '<br>';
		while($b = mysql_fetch_assoc($q)) {
			echo '<font color='.$team_colors[$b['id_grup']].'>'.nick_hist($b).'</font> в <a href="logs.php?log='.$b[battle].'" target="_blank">бою »»</a><br>';
		}
	}

	if ($croom == 75 || $map['t'.$user['id_grup'].'score'] >= 2) {

		echo "<script LANGUAGE=\"JavaScript\">
		function confirmSubmit()
		{
		var agree=confirm('Действительно хотите Выйти и ничего не получить?');
		if (agree)
			return true ;
			else
			return false ;
		}
		</script>";
		echo '<br><br><form method=get ><input style="font-weight:bold; background-color:red;" type=Submit name=forceexit value=\'Выйти и ничего не получить!\' onClick="return confirmSubmit()" ></form>';

	}


	// type:
	// 0 - просто вещь, в экстра - субтип из конфига
	// 1 - ловушка - в extra - owner
	// 2 - сундук - в extra 0 - closed/ 1 - open
	// 3 - вещи в сундуке
	// 4 - ключ
	// 5 - колодецжизни


	$its = mysql_query('SELECT * FROM `ruines_items` WHERE `room` = '.$user['room'].' AND (type = 0 OR type = 2 OR type = 4 OR type = 5)');
	if(mysql_num_rows($its) > 0) {
		echo '<H4>В комнате находится:</H4>';
		while($it = mysql_fetch_assoc($its)) {
			if ($it['type'] == 2) {
				if ($it['extra']) {
					// открыт уже
					echo '<IMG SRC="http://i.oldbk.com/llabb/use_sunduk_off.gif"> ';
				} else {
					// закрыт
					echo '<A HREF="ruines.php?openchest='.$it['id'].'"><IMG SRC="http://i.oldbk.com/llabb/use_sunduk_on.gif" ALT="Открыть сундук"></A> ';
				}
			} elseif ($it['type'] == 4) {
				// проверяем время появления ключей
				if (!(($map['starttime'] + $keytime) > time())) {
					// ключ
					echo '<A HREF="ruines.php?givekey=',$it['id'],'"><IMG SRC="http://i.oldbk.com/i/sh/runin_key.gif" ALT="Подобрать ключ"></A>';
				}
			} elseif ($it['type'] == 5 && $croom != 75) {
				// колодец жизни, не показываем если на кладбище. тут свой есть
				echo '<br><br><h4>Колодец жизни: </h4><br>';
				echo '<A HREF="ruines.php?heal='.$it['id'].'"><IMG SRC="http://i.oldbk.com/i/city/sub/ruin_kolodec.png" ALT="Выпить воды"></A><br>';
			} else {
				echo '<A HREF="ruines.php?give=',$it['id'],'"><IMG SRC="ruines_show.php?id='.$it['id'].'"></A> ';
			}
		}
	}

	if ($croom == 75 || ($map['t'.$user['id_grup'].'score'] >= 2 && ($croom == 76 || $croom == 77))) {
		if (empty($trap) && $croom == 75) {
			$q = mysql_query('SELECT * FROM `effects` WHERE `type` = 10 AND `time` >= '.time().' AND `owner` = '.$user['id']) or mydie();
			if (mysql_num_rows($q) > 0) {
				$nomove = mysql_fetch_assoc($q) or mydie();
				$t = round(($nomove['time']-time()) / 60);
				if ($t <= 0) $t = 1;
				if ($t < 100)
					echo 'Вы парализованы и не можете двигаться еще '.$t.' мин.<br>';
			}
		}

		echo '<br><br><h4>Колодец жизни: </h4><br>';
		echo '<A HREF="ruines.php?heal=0"><IMG SRC="http://i.oldbk.com/i/city/sub/ruin_kolodec.png" ALT="Выпить воды"></A><br>';
	}

	// показываем ключи
	if ($croom == 76) {
		for ($i = 0; $i < $map['t2score']; $i++) {
			echo '<IMG SRC="http://i.oldbk.com/i/sh/runin_key.gif"> ';
		}
	}
	if ($croom == 77) {
		for ($i = 0; $i < $map['t1score']; $i++) {
			echo '<IMG SRC="http://i.oldbk.com/i/sh/runin_key.gif"> ';
		}
	}

	// показываем сокровища
	if ($croom == 78) {
		echo '<br><a href="ruines.php?opensanct"><IMG SRC="http://i.oldbk.com/i/sh/ruin_box_op.gif"></a><br>';
	}

	if ($croom == 77 && $user['id_grup'] == 1 && $map['k1owner'] == $user['id'] || $croom == 76 && $user['id_grup'] >= 2 && $map['k2owner'] == $user['id']) {
		// кнопка - положить ключ на трон
		echo '<br><br><input type=button name="dropkey" value="Положить ключ" OnClick="location.href=\'ruines.php?dropkey=1\'">';
	}

?>
</TD>
<TD colspan=3 valign=top align=right nowrap>
<script language="javascript" type="text/javascript">
</script>

<table border="0" cellpadding="0" cellspacing="0">
	<tr align="right" valign="top">
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="1"><tr><td>
			<div style="position:relative;" id="ione"><img src="http://i.oldbk.com/i/ruines/<?php echo $croom ?>.jpg" alt="" border="1"/>

			</div></td></tr>

				<tr><td align="right"><div align="right" id="btransfers"><table cellpadding="0" cellspacing="0" border="0" id="bmoveto">
				<tr><td bgcolor="#D3D3D3">

				</td>
				</tr>
				</table></div></td></tr>

			</table>

			</td>
		<td>

			<table width="80" border="0" cellspacing="0" cellpadding="0">
            	<tr>

					<td>

			<table width="80"  border="0" cellspacing="0" cellpadding="0">
	                    	<tr>
	                    		<td colspan="3" align="center">&nbsp;</td>
	                    		</tr>
	                    	<tr>
	                    		<td colspan="3" align="center">
							<table border=0 width="220" border="0" cellspacing="0" cellpadding="0">
	                    				<tr>
	                    					<td></td>
	                    					<td width="100%" height="100%" align=center>
									<table border="0" height=100% width=90% cellspacing="0" cellpadding="0">
	                    							<tr>
	                    								<td nowrap="nowrap" align="center">
												<div style="height:8px;background-color:red;padding:0px;margin:0px;border:solid black 0px;font-size:1px; text-align:left" id="prcont">
													<div style="width:0%;height:100%;padding:0px;margin:0px;background-color:green;" id="barl"></div>
												</div>
                   									</td>
	                    							</tr>
                    							</table>

									</td>
                    					<td></td>
                    					</tr>
			</table>


				</td>
                    		</tr>
	</table>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td colspan=6><img src="http://i.oldbk.com/i/nav/top.jpg"></td></tr>
<tr><td><img src="http://i.oldbk.com/i/nav/mid1.jpg"></td><td>

<table width=100% cellpadding=0 cellspacing=0>
	<tr><td><img src="http://i.oldbk.com/i/nav/a1.jpg"></td><td><td><a onclick="return check('m7');" <?php if($rooms[$croom][2]) { echo 'id="m7"';}?> href="?rnd=0.817371946556865&path=2"><img src="http://i.oldbk.com/i/nav/up_<?php if(!$rooms[$croom][2]) echo '0'; else echo '1';?>.jpg" o<?php if(!$rooms[$croom][2]) { echo 'i';}?>nmousemove="fastshow2('<?php echo $rooms[$rooms[$croom][2]][0] ?>',event);" onmouseout="hideshow();" /></a></td><td><img src="http://i.oldbk.com/i/nav/a2.jpg"></td></tr>
	<tr><td><a onclick="return check('m3');" <?php if($rooms[$croom][1]) { echo 'id="m3"';}?> href="?rnd=0.817371946556865&path=1"><img src="http://i.oldbk.com/i/nav/left_<?php if(!$rooms[$croom][1]) echo '0'; else echo '1';?>.jpg" <?php if(!$rooms[$croom][1]) { echo 'i';} ?>onmousemove="fastshow2('<?php echo $rooms[$rooms[$croom][1]][0] ?>',event);" onmouseout="hideshow();" /></a></td><td><td><a href="?rnd=0.817371946556865"><img src="http://i.oldbk.com/i/nav/center.jpg"></a></td><td><a onclick="return check('m1');" <?php if($rooms[$croom][3]) { echo 'id="m1"';}?> href="?rnd=0.817371946556865&path=3"><img src="http://i.oldbk.com/i/nav/right_<?php if(!$rooms[$croom][3]) echo '0'; else echo '1';?>.jpg" <?php if(!$rooms[$croom][3]) { echo 'i';}?>onmousemove="fastshow2('<?php echo $rooms[$rooms[$croom][3]][0] ?>',event);" onmouseout="hideshow();" /></a></td></tr>
	<tr><td><img src="http://i.oldbk.com/i/nav/a3.jpg"></td><td><td><a onclick="return check('m5');" <?php if($rooms[$croom][4]) { echo 'id="m5"';}?> href="?rnd=0.817371946556865&path=4"><img src="http://i.oldbk.com/i/nav/down_<?php if(!$rooms[$croom][4]) echo '0'; else echo '1';?>.jpg" <?php if(!$rooms[$croom][4]) { echo 'i';}?>onmousemove="fastshow2('<?php echo $rooms[$rooms[$croom][4]][0] ?>',event);" onmouseout="hideshow();" /></a></td><td><img src="http://i.oldbk.com/i/nav/a4.jpg"></td></tr>
</table>

</td>
<td><img src="http://i.oldbk.com/i/nav/mid2.jpg"></td></tr>
<tr><td colspan=6><img src="http://i.oldbk.com/i/nav/bottom.jpg"></td></tr>
</table>



</td>
	</tr>

                   	</table></td>
           		</tr>
          	</table>

			<table  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td nowrap="nowrap" id="moveto">
						<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#DEDEDE">

						</table>
					</td>
				</tr>
			</table>

			</td>
	</tr>
</table>

<div id="mmoves" style="background-color:#FFFFCC; visibility:hidden; overflow:visible; position:absolute; border-color:#666666; border-style:solid; border-width: 1px; padding: 2px; white-space: nowrap;"></div>

<script language="javascript" type="text/javascript">
// прогресс бар
var progressEnd = <?php echo ($diffwalk*2)+1 ?>;
var progressColor = '#00CC00';
var mtime = parseInt('<?php echo $diffwalk ?>');
if (!mtime || mtime<=0) {mtime=0;}
var progressInterval = Math.round(mtime*1000/progressEnd);

var is_accessible = true;
var progressAt = progressEnd;
var progressTimer;


function progress_clear() {
	DoBar(0);
	progressAt = 0;

	for (var t = 1; t <= 8; t++) {
		if (document.getElementById('m'+t) ) {
			var tempname = document.getElementById('m'+t).children[0].src;
			if (tempname.match(/\_[0-9]+\.jpg$/)) {
					document.getElementById('m'+t).children[0].id = 'backend';
			}
			var newname;
			newname = tempname.replace(/\_[0-9]+\.jpg$/,'_0.jpg');
			document.getElementById('m'+t).children[0].src = newname;
		}
	}

	is_accessible = false;
	set_moveto(true);
}

function progress_update() {
	progressAt++;
	if (progressAt > progressEnd) {
		for (var t = 1; t <= 8; t++) {
			if (document.getElementById('m'+t) ) {
				var tempname = document.getElementById('m'+t).children[0].src;
				var newname;
				newname = tempname.replace(/_1.jpg$/,'_0.jpg');
				if (document.getElementById('m'+t).children[0].id == 'backend') {
					tempname = newname.replace(/_0.jpg$/,'_1.jpg');
					newname = tempname;
				}
				document.getElementById('m'+t).children[0].src = newname;
			}
		}
		is_accessible = true;
		set_moveto(false);
	} else {
		DoBar(progressAt);
		progressTimer = setTimeout('progress_update()',progressInterval);
	}
}


function set_moveto (val) {
	document.getElementById('moveto').disabled = val;
	if (document.getElementById('bmoveto')) {
		document.getElementById('bmoveto').disabled = val;
	}
}
function progress_stop() {
	clearTimeout(progressTimer);
	progress_clear();
}
function check(it) {
	return is_accessible;
}

function DoBar(number) {
	procent = Math.round(number * 100 / progressEnd);
	s(procent);
}

function s(st){
	document.getElementById('barl').style.width = st ? st + '%' : '1';
}

if (mtime > 0) {
	progress_clear();
	progress_update();
} else {
	DoBar(progressEnd);
}
</script>

</TD>
</TR>
</TABLE>
<div id=hint3 class=ahint></div>
<script>top.onlineReload(true)</script>
</BODY>
</HTML>
