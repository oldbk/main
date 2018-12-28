<?php
	session_start();
	$rscriptstart = time();
	require_once('dt_functions.php');
	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');
	require_once('fsystem.php');
	require_once('dt_config.php');


	$giveitempenalty = 3; // задержка на поднятие вещи
	$attacktime = 0; // задержка на нападение
	$attacktimerage = 60*3; // задержка на нападение ярости

	if (!($user['room'] > 10000 && $user['room'] < 11000)) {
		sleep(2);
		Redirect("main.php");
	}
	if ($user['in_tower'] != 15) Redirect('main.php');
	if ($user['battle'] != 0 || $user['battle_fin'] != 0) Redirect("fbattle.php");
	if ($user['hp'] <= 0) {
		Redirect("dt_start.php");
	}

	// получаем инфу о карте
	$q = mysql_query('SELECT * FROM `dt_map` WHERE `active` = 1') or die();
	$map = mysql_fetch_assoc($q) or die("Unable to find DT map");

	$error = "";
	$trap = "";

	$ses = GetSerFile($frpath);
	if (!isset($ses['id']) || $ses['id'] != $map['id']) {
		// новая бс, старые данные надо снести
		$ses['time'] = time();
		$ses['timei'] = time();
		$ses['id'] = $map['id'];
		SaveSerFile($frpath,$ses);

		if ($_SESSION['tsound'] == 1) { 
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

	if ($map['halftype']) {
		$dt_rooms[$dt_relmap+517][2] = 0;
		$dt_rooms[$dt_relmap+518][4] = 0;

		$dt_rooms[$dt_relmap+524][2] = 0;
		$dt_rooms[$dt_relmap+525][4] = 0;

		$dt_rooms[$dt_relmap+539][3] = 0;
		$dt_rooms[$dt_relmap+545][1] = 0;

		$dt_rooms[$dt_relmap+520][2] = 0;
	}

	if (isset($_GET['give'])) {
		$id = intval($_GET['give']);
		if ($ses['timei'] <= time()) {
			$q = mysql_query('START TRANSACTION') or mydie();
			$q = mysql_query('SELECT * FROM `dt_items` WHERE `id` = '.$id.' AND type = 0 AND room = '.$user['room'].' FOR UPDATE') or mydie();
			if (mysql_num_rows($q) > 0) {
				$ses['timei'] = time() + $giveitempenalty;
				$item = mysql_fetch_assoc($q) or mydie();

				// удаляем вещь
				mysql_query('DELETE FROM `dt_items` WHERE `id` = '.$id.' AND room = '.$user['room']) or mydie();

				// получилаем прототип
				$proto = $item['iteam_id'];
				$subtype = $item['extra'];

				$q = mysql_query_cache('SELECT  * FROM oldbk.`shop` WHERE `id` = '.$item['iteam_id'],false,60);
				if ($q === FALSE || !count($q)) mydie();
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


				mysql_query('INSERT INTO oldbk.`inventory`
						(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`duration`,`maxdur`,`isrep`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
							`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
							,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
							`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`img_big`,`nclass`
						)
						VALUES	(
							"'.mysql_real_escape_string($present).'",
							'.$dress['id'].',
							'.$user['id'].',
							"'.mysql_real_escape_string($item['name']).'",
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
							'.$dress['group'].',"",0,0,0,0,"",0,0,0,"'.$subtype.'","'.$dress['img_big'].'",4
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


	// передвижение
	if (isset($_GET['path'])) {
		$path = intval($_GET['path']);
		if ($path < 1 || $path > 4) Redirect('dt.php');

		// проверяем есть ли путы
		$q = mysql_query('SELECT * FROM `effects` WHERE `type` = 10 AND `time` >= '.time().' AND `owner` = '.$user['id']) or mydie();
		if (mysql_num_rows($q) > 0) $trap = 'Вы парализованы и не можете двигаться...';
		
		if (empty($trap) && $ses['time'] <= time()) {
			if ($dt_rooms[$user['room']][$path]) {
				$newroom = $dt_rooms[$user['room']][$path];

				// Сначала пробуем перейти. В запросе есть условие на battle = 0, что перс не в бою.
				// Если перейти удастся, то будем делать дальнейшие действия.
		   		mysql_query('UPDATE `users` SET `room` = '.($newroom).' WHERE `users`.`id` = '.$user['id'].' AND `battle` = 0 AND in_tower = 15') or mydie();
				if (mysql_affected_rows() > 0) {					

					$ses['time'] = time() + $dt_rooms[$dt_rooms[$user['room']][$path]][5]; // штраф на хождение той комнату КУДА мы перешли

					// ушел из комнаты, в $user['room'] еще старая комната
					$mids = array();
					$list = mysql_query('SELECT * FROM `users` WHERE `room` = '.$user['room'].' AND `in_tower` = 15') or mydie();
					while($u = mysql_fetch_assoc($list)) {
						if($u['id'] != $user['id']) {
							$mids[] = $u['id'];
						}
					}
					if (count($mids)) addch_group('<font color=red>Внимание!</font> <B>'.$user['login'].'</B> отправился в <B>'.$dt_rooms[$newroom][0].'</B>.',$mids);

					// пришел в комнату
					$mids = array();
					$list = mysql_query('SELECT * FROM `users` WHERE `room` = '.($newroom).' AND `in_tower` = 15') or mydie();
					while($u = mysql_fetch_assoc($list)) {
						if($u['id'] != $user['id']) {
							$mids[] = $u['id'];
						}
					}
					if (count($mids)) addch_group('<font color=red>Внимание!</font> <B>'.$user['login'].'</B> вошел в комнату.',$mids);

					$user['room'] = $newroom;

					SaveSerFile($frpath,$ses);
				
					// обработка ловушек, type - 1 ловушка
					$q = mysql_query('START TRANSACTION') or mydie();
					$q = mysql_query('SELECT * FROM dt_items WHERE type = 1 AND room = '.$user['room'].' AND extra <> '.$user['id'].' FOR UPDATE') or mydie();

					if (mysql_num_rows($q) > 0) {
						$trap = mysql_fetch_assoc($q) or mydie();

						// снимаем 50% хп
						if($user['maxhp']/$user['hp'] < 3) {
							$newhp = round($user['hp']/2); 
						} else {
							$newhp = $user['hp']; 
						}

						$nomove = mt_rand(1,5);
						$nomovetime = time()+$nomove*60;

						mysql_query('UPDATE users SET `hp` = '.$newhp.', `fullhptime` = '.time().' WHERE `id` = '.$user['id']) or mydie();

						$user['hp'] = $newhp;
						$user['fullhptime'] = time();

						if ($trap['extra'] > _BOTSEPARATOR_) {
							$q = mysql_query('SELECT * FROM `users_clons` WHERE `id` = "'.$trap['extra'].'"') or mydie();
							$trap_owner = mysql_fetch_assoc($q) or mydie();
						} else {
							$q = mysql_query('SELECT * FROM `users` WHERE `id` = "'.$trap['extra'].'"') or mydie();
							$trap_owner = mysql_fetch_assoc($q) or mydie();
						}

						addch("<img src=i/magic/trap.gif> {$user[login]} угодил в ловушку.. Парализован на {$nomove} минут...") or mydie();
						addchp('<font color=red>Внимание!</font> <B>'.$user['login'].'</B> попал в вашу ловушку в '.$dt_rooms[$newroom][0].'. Парализован на '.$nomove.' минут...','{[]}'.$trap_owner['login'].'{[]}',-1,$trap_owner['id_city']) or mydie();

						// напоролись на ловушку
						$log = '<span class=date2>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($user).'</b> напоролся на ловушку, установленную <b>'.nick_hist($trap_owner).'</b><BR>';
						mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']) or mydie();


						mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ('.$user['id'].',"Путы",'.$nomovetime.',10)') or mydie();
						mysql_query('DELETE from dt_items where id = '.$trap['id']) or mydie();

						// пишем текст
						$trap = '<b>Вы попали в ловушку, поставленную персонажем <B>'.$trap_owner['login'].'</b> не можете двигаться '.$nomove.' минут';
					}
					$q = mysql_query('COMMIT') or mydie();

					// ЯРОСТНАЯ БС!
					if ($map['ragetype'] > 0) {
						// рандомно нападаем
						$sh = mt_rand(1,10);
						if(($sh < 9) and (!(($map['starttime'] + $attacktimerage) > time()))) {
							$trg = array();
	                        			$jert = mysql_query("SELECT * FROM `users` WHERE `room` = '".$user['room']."' AND `in_tower` = 15 AND `id` <> ".$user['id']);

					        	while($jr = mysql_fetch_array($jert)) {
					            		$trg[] = $jr['id'];
					         	}


	                        			$jert = mysql_query("SELECT * FROM `users_clons` WHERE `bot_room` = ".$user['room']);

					        	while($jr = mysql_fetch_array($jert)) {
					            		$trg[] = $jr['id'];
					         	}


						     	if(count($trg) > 0) {   
								// если тел хотя бы одно (акромя самого перса)
						     		$trg_id = mt_rand(0,count($trg)-1);

								if ($trg[$trg_id] > _BOTSEPARATOR_) {
	                             					$jert = mysql_fetch_array(mysql_query("SELECT login,hp FROM `users_clons` WHERE `bot_room` = '".$user['room']."' AND `id` = ".$trg[$trg_id]));
								} else {                                                                   
	                             					$jert = mysql_fetch_array(mysql_query("SELECT login,hp FROM `users` WHERE `room` = '".$user['room']."' AND `in_tower` = 15 AND `id` = ".$trg[$trg_id]));
								}

							    	if($jert['hp'] > 0) {
							       		$_POST['attack'] = $jert['login'];
								}
						     	}
						}
					}
				} else {
					Redirect('dt.php');
				}
			}
		}
	}


	// нападение
	if(isset($_POST['attack'])) {
		if (($map['starttime'] + $attacktime) > time()) {
			$error = 'Нельзя нападать первые 3 минуты после начала.';
		}
		if (empty($error)) {
			$is_bot = FALSE;
			$jert = array();

			if (strpos($_POST['attack'],'pxива') !== FALSE) {
				$is_bot = TRUE;
			}

			$q = mysql_query('START TRANSACTION') or mydie();
			if ($is_bot) {
				// ищем бота по логину и по 
				$q = mysql_query('SELECT * FROM `users_clons` WHERE `login` = "'.$_POST['attack'].'" AND `bot_online` = 5 AND `bot_room` = '.$user['room'].' FOR UPDATE') or mydie();
				if (mysql_num_rows($q) > 0) {
					// нашли бота
					$jert = mysql_fetch_assoc($q) or mydie();
					$jert['room'] = $jert['bot_room'];
					$q2 = mysql_query('SELECT * FROM users WHERE id = '.$user['id'].' FOR UPDATE') or mydie();
					$user = mysql_fetch_assoc($q2) or mydie();
				}
			} else {
				$q = mysql_query('SELECT * FROM `users` WHERE `login` = "'.$_POST['attack'].'" OR `id` = '.$user['id'] .' FOR UPDATE') or mydie();
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
				if (isset($_SESSION['dtlastattack'])) {
					if ($rscriptstart - $_SESSION['dtlastattack'] <= 2) {
						$error = "Не так быстро...";
						$chkattack = false;
					}
				}
				
				// проверяем комнату, айди чтобы не сам на себя и чтобы была противоположная команда и чтобы хп были больше 0 и что мы сами не в бою
				if ($chkattack && $jert['room'] == $user['room'] && $jert['id'] != $user['id'] && $user['hp'] > 0 && $jert['hp'] > 0 && $user['battle'] == 0 && $user['battle_fin'] == 0) {
					if($jert['battle'] == 0) {
						// фиксим HP у противника, фиксим у себя
						if ($is_bot) {
							mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']) or mydie();
						} else {
							mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND (`id` = '.$jert['id'].' OR `id` = '.$user['id'].')') or mydie();
						}

						// проверяем hp противника
						if ($jert['hp'] == 0) {
							// его должно уже выкидывать из бс
							mydie();
						}

						mysql_query('INSERT INTO `battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
								VALUES
								(
									"Бой в Башне Смерти",
									"",
									"'.mt_rand(1,5).'",
									"1010",
									"0",
									"'.$user['id'].'",
									"'.$jert['id'].'",
									"'.time().'",
									"'.time().'",
									3,
									"'.mysql_real_escape_string(BNewHist($user)).'",
									"'.mysql_real_escape_string(BNewHist($jert)).'",
									"1","0"
								)
						') or mydie();

						$_SESSION['dtlastattack'] = time();

						$id = mysql_insert_id();

						// теперь обновляем себя и противника что мы в бою
						if ($is_bot) {
							mysql_query('UPDATE `users_clons` SET `battle` = '.$id.', `battle_t` = 2 WHERE `id`= '.$jert['id']) or mydie();
						} else {
							mysql_query('UPDATE `users` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id`= '.$jert['id']) or mydie();
						}
						mysql_query('UPDATE `users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$user['id']) or mydie();

						addch('<img src=i/magic/attack.gif> <b>'.$user['login'].'</b>, применив магию нападения, внезапно напал на <b>'.$jert['login'].'</b>.',$user['room']) or mydie();
						$p2 = '<b>'.nick_align_klan($user).'</b> и <b>'.nick_align_klan($jert).'</b>';
						addlog($id,"!:S:".time().":".BNewHist($user).":".BNewHist($jert)."\n");

						// лог
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($user).'</b> напал на <b>'.nick_hist($jert).'</b> завязался <a href="logs.php?log='.$id.'" target="_blank">бой »»</a><BR>';
						mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']) or mydie();

						$q = mysql_query('COMMIT') or mydie();
						Redirect('fbattle.php');
					} else {
						// уже есть бой, вмешиваемся
	
						// фиксим хп
						mysql_query('UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']) or mydie();

						// находим бой жертвы
						$q = mysql_query('SELECT * FROM `battle` WHERE `id` = '.$jert['battle']) or mydie();
						$bd = mysql_fetch_assoc($q) or Redirect('dt.php'); // если не нашли бой - редиректимся
	
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
							$user[battle_t]=$meteam;
							$ac=($user[sex]*100)+mt_rand(1,2);
							addlog($jert['battle'],"!:W:".time().":".BNewHist($user).":".$user[battle_t].":".$ac."\n");	
							// выставляем себе номер боя
							mysql_query('UPDATE users SET `battle` = '.$jert['battle'].', `zayavka` = 0, `battle_t`= '.$meteam.' WHERE `id`= '.$user['id']) or mydie();
	
							$log = '<span class=date>'.date("d.m.y H:i").'</span>  <b>'.nick_hist($user).'</b> вмешался в поединок против <b>'.nick_hist($jert).'</b> <a href="logs.php?log='.$jert['battle'].'" target="_blank">бой »»</a><BR>';
							mysql_query('UPDATE `dt_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE dt_id = '.$map['id']) or mydie();
						}


						$q = mysql_query('COMMIT') or mydie();						
						Redirect('fbattle.php');
					}
				} else {
					if ($chkattack) {
						$error = 'Жертва ускользнула из комнаты...';
					}
				}
			} else {
				$error = 'Жертва ускользнула из комнаты...';
			}
			$q = mysql_query('COMMIT') or mydie();
		}
	}

	$diffwalk = $ses['time'] - time();
	$diffitem = $ses['timei'] - time();

?>
<HTML><HEAD>
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
// Заголовок, название скрипта, имя поля с логином
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


function returned2(s){
	if (top.oldlocation != '') { top.frames['main'].navigate(top.oldlocation+'?'+s+'tmp='+Math.random()); top.oldlocation=''; }
	else { top.frames['main'].navigate('main.php?'+s+'tmp='+Math.random()) }
}
<?
$step=1;
if ($step==1) $idkomu=0;
?>
function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}
</script>
</HEAD>
<body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor=e2e0e0 onload="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>,1); ;">
<div id=hint4 class=ahint></div>
<?
 	if ($do_sound!='') {echo $do_sound;}
?>


<TABLE width=100% cellspacing=0 cellpadding=0>

<TR><TD><?=nick($user);?></TD>
<TD class='H3' align=right><?=$dt_rooms[$user['room']][0];?>&nbsp; &nbsp;
<?php
if (($map['starttime'] + $attacktime) <= time()) {
	?>
	<IMG SRC="http://i.oldbk.com/i/tower/attack.gif" WIDTH=66 HEIGHT=24 ALT="Напасть на..." style="cursor:pointer" onclick="findlogin('Напасть на','dt.php','attack')">
	<?php
}
?>
</TD>
<TR>
<TD valign=top>
<FONT COLOR=red><? if(!empty($error)) echo $error; ?></FONT>

<?

	// type:
	// 0 - просто вещь, в экстра - субтип из конфига
	// 1 - ловушка - в extra - owner

	$its = mysql_query("SELECT * FROM `dt_items` WHERE `room` = '".$user['room']."' and type != 1");
	if(mysql_num_rows($its)>0) {
		echo '<H4>В комнате разбросаны вещи:</H4>';
		while($it = mysql_fetch_assoc($its)) {
			echo '<A HREF="dt.php?give=',$it['id'],'"><IMG SRC="dt_show.php?id='.$it['id'].'"></A> ';
		}
	}


?>
</TD>
<TD colspan=3 valign=top align=right nowrap>
<script language="javascript" type="text/javascript">
function fastshow2 (content) {
	var el = document.getElementById("mmoves");
	var o = window.event.srcElement;
	if (content == '') { el.innerHTML =  '';}
	if (content!='' && el.style.visibility != "visible") {el.innerHTML = '<small>'+content+'</small>';}
	var x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft - el.offsetWidth + 5;
	var y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop+20;
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

<script language="javascript" type="text/javascript">
var solo_store;
function solo(n, name) {
	if (check_access()==true) {
		window.location.href = '?path='+n+'&rnd='+Math.random();
	} else if (name && n) {
		solo_store = n;
		var add_text = (document.getElementById('add_text') || document.createElement('div'));
		add_text.id = 'add_text';
		add_text.innerHTML = 'Вы перейдете в: <strong>' + name +'</strong> (<a href="#" onclick="return clear_solo();">отмена</a>)';
		document.getElementById('ione').parentNode.parentNode.nextSibling.firstChild.appendChild(add_text);
		ch_counter_color('red');
	}
	return false;
}
function clear_solo () {
	document.getElementById('add_text').removeNode(true);
	solo_store = false;
	ch_counter_color('#00CC00');
	return false;
}
var from_map = false;
function imover(im) {
	im.filters.Glow.Enabled=true;
	if ( from_map == false && im.id.match(/mo_(\d)/) && document.getElementById('b' + im.id)) {
		from_map = true;
		document.getElementById('b' + im.id).runtimeStyle.color = '#666666';
		from_map = false;
	}

}
function imout(im) {
	im.filters.Glow.Enabled=false;
	if ( from_map == false && im.id.match(/mo_(\d)/) && document.getElementById('b' + im.id)) {
		from_map = true;
		document.getElementById('b' + im.id).runtimeStyle.color = document.getElementById('b' + im.id).style.color;
		from_map = false;
	}
}
function bimover (im) {
	if ( from_map==false && document.getElementById(im.id.substr(1)) ) {
		from_map = true;
		imover(document.getElementById(im.id.substr(1)));
		from_map = false;
	}
}
function bimout (im) {
	if ( from_map==false && document.getElementById(im.id.substr(1)) ) {
		from_map = true;
		imout(document.getElementById(im.id.substr(1)));
		from_map = false;
	}
}
function bsolo (im) {
	if (document.getElementById(im.id.substr(1))) {
		document.getElementById(im.id.substr(1)).click();
	}
	return false;
}
function Down() {top.CtrlPress = window.event.ctrlKey}
document.onmousedown = Down;

</script>
<style type="text/css">
    img.aFilter { filter:Glow(color=,Strength=,Enabled=0); cursor:pointer }
	hr { height: 1px; }
</style>

<table  border="0" cellpadding="0" cellspacing="0">
	<tr align="right" valign="top">
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="1"><tr><td>
			<div style="position:relative; cursor: pointer;" id="ione"><img src="http://i.oldbk.com/i/tower/<?=($user['room']-$dt_relmap+500)?>.jpg" alt="" border="1"/>

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

					<td><table width="80"  border="0" cellspacing="0" cellpadding="0">
                    	<tr>
                    		<td colspan="3" align="center"><img src="i/move/navigatin_46.gif" width="80" height="4" /></td>
                    		</tr>
                    	<tr>
                    		<td colspan="3" align="center"><table width="80"  border="0" cellspacing="0" cellpadding="0">
                    				<tr>
                    					<td><img src="i/move/navigatin_48.gif" width="9" height="8" /></td>
                    					<td width="100%" height="100%" align=center>
							<table border="0" height=100% width=100% cellspacing="0" cellpadding="0">

                    							<tr>

                    								<td nowrap="nowrap" align="center">
											<div style="height:8px;background-color:red;padding:0px;margin:0px;border:solid black 0px;font-size:1px; text-align:left" id="prcont">
												<div style="width:0%;height:100%;padding:0px;margin:0px;background-color:green;" id="barl"></div>
											</div>
                   								</td>
                    							</tr>
                    					</table></td>

                    					<td><img src="i/move/navigatin_50.gif" width="7" height="8" /></td>
                    					</tr>
                    				</table></td>
                    		</tr>

	<tr>
		<td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="i/move/navigatin_51.gif" width="31" height="8" /></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_54.gif" width="9" height="20" /><img src="i/move/navigatin_55i.gif" width="22" height="20" border="0" /></td>
				</tr>
				<tr>
					<td><a onclick="return check('m7');" <?if($dt_rooms[$user['room']][4]) { echo 'id="m7"';}?> href="?rnd=0.817371946556865&path=4"><img src="i/move/navigatin_59<?if(!$dt_rooms[$user['room']][4]) { echo 'i';}?>.gif" width="21" height="20" border="0" o<?if(!$dt_rooms[$user['room']][4]) { echo 'i';}?>nmousemove="fastshow2('<?=$dt_rooms[$dt_rooms[$user['room']][4]][0]?>');" onmouseout="hideshow();" /></a><img src="i/move/navigatin_60.gif" width="10" height="20" border="0" /></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_63.gif" width="11" height="21" /><img src="i/move/navigatin_64i.gif" width="20" height="21" border="0" /></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_68.gif" width="31" height="8" /></td>
				</tr>
		</table></td>
		<td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><a onclick="return check('m1');" <?if($dt_rooms[$user['room']][1]) { echo 'id="m1"';}?> href="?rnd=0.817371946556865&path=1"><img src="i/move/navigatin_52<?if(!$dt_rooms[$user['room']][1]) { echo 'i';}?>.gif" width="19" height="22" border="0" <?if(!$dt_rooms[$user['room']][1]) { echo 'i';}?>onmousemove="fastshow2('<?=$dt_rooms[$dt_rooms[$user['room']][1]][0]?>');" onmouseout="hideshow();" /></a></td>
				</tr>
				<tr>
					<td><a href="?rnd=0.817371946556865"><img src="i/move/navigatin_58.gif" width="19" height="33" border="0" o nmousemove="fastshow2('<strong>Обновить</strong><br />Переходы:<br />Картинная галерея 1<br />Зал ораторов<br />Картинная галерея 3');" onmouseout="hideshow();" /></a></td>
				</tr>
				<tr>
					<td><a onclick="return check('m5');" <?if($dt_rooms[$user['room']][3]) { echo 'id="m5"';}?> href="?rnd=0.817371946556865&path=3"><img src="i/move/navigatin_67<?if(!$dt_rooms[$user['room']][3]) { echo 'i';}?>.gif" width="19" height="22" border="0" <?if(!$dt_rooms[$user['room']][3]) { echo 'i';}?>onmousemove="fastshow2('<?=$dt_rooms[$dt_rooms[$user['room']][3]][0]?>');" onmouseout="hideshow();" /></a></td>
				</tr>
		</table></td>
		<td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="i/move/navigatin_53.gif" width="30" height="8" /></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_56i.gif" width="21" height="20" border="0" /><img src="i/move/navigatin_57.gif" width="9" height="20" /></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_61.gif" width="8" height="21" /><a onclick="return check('m3');" <?if($dt_rooms[$user['room']][2]) { echo 'id="m3"';}?> href="?rnd=0.817371946556865&path=2"><img src="i/move/navigatin_62<?if(!$dt_rooms[$user['room']][2]) { echo 'i';}?>.gif" width="22" height="21" border="0" <?if(!$dt_rooms[$user['room']][2]) { echo 'i';}?>onmousemove="fastshow2('<?=$dt_rooms[$dt_rooms[$user['room']][2]][0]?>');" onmouseout="hideshow();" /></a></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_65i.gif" width="21" height="20" border="0" /><img src="i/move/navigatin_66.gif" width="9" height="20" /></td>
				</tr>
				<tr>
					<td><img src="i/move/navigatin_69.gif" width="30" height="8" /></td>
				</tr>
		</table></td>
	</tr>

                   	</table></td>
           		</tr>
          	</table>
			<br><input type=button value='Обновить' onClick="location.href='dt.php?'+Math.random();">
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
			if (tempname.match(/b\.gif$/)) {
					document.getElementById('m'+t).children[0].id = 'backend';
			}
			var newname;
			newname = tempname.replace(/(b)?\.gif$/,'i.gif');
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
				newname = tempname.replace(/i\.gif$/,'.gif');
				if (document.getElementById('m'+t).children[0].id == 'backend') {
					tempname = newname.replace(/\.gif$/,'b.gif');
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
<BR>Всего живых участников на данный момент: <?
	$q = mysql_query('SELECT count(*) as allcount FROM users WHERE in_tower = 15');
	$ls = mysql_fetch_assoc($q);

	$q = mysql_query('SELECT count(*) as allcount FROM users_clons WHERE id_user = 84');
	$ls1 = mysql_fetch_assoc($q);

	if ($map['halftype']) {
		$ll = mysql_fetch_assoc(mysql_query('SELECT count(*) as ll FROM users WHERE room IN ('.implode(",",$dt_halfleft).')'));
		$ll2 = mysql_fetch_assoc(mysql_query('SELECT count(*) as ll2 FROM users WHERE room IN ('.implode(",",$dt_halfright).')'));
		echo "<B>".($ls['allcount'])."</B> (".$ll['ll']." + ".$ll2['ll2'].") + <B>".$ls1['allcount']."</B><br>";
	} else {
		echo "<B>".($ls['allcount'])."</B> + <B>".$ls1['allcount']."</B><br>";
	}

	if($map['ragetype']) {
		echo "Ярость ослепляет вас...<br>";
	}
	if($map['darktype']) {
		echo "Башня Смерти окутана мраком...<br>";
	}
	if($map['greedtype']) {
		echo "Жадность охватила вас...<br>";
	}
	if($map['halftype']) {
		echo "Мир разделен напополам...<br>";
	}
	/*
	if($map['whitetype']) {
		echo "Белая БС<br>";
	}
	if($map['hptype'] == 0) {
		echo "Нормальные ХП<br>";
	}
	if($map['hptype'] == 1) {
		echo "Быстрые ХП<br>";
	}
	if($map['hptype'] == 2) {
		echo "Медленные ХП<br>";
	}*/
?><BR>
<div id=hint3 class=ahint></div>
<script>top.onlineReload(true)</script>
</BODY>
</HTML>
