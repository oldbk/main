<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("castles_pre.php");
	}


	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	require_once('connect.php');
	require_once('functions.php');
	require_once('castles_config.php');
	require_once('castles_functions.php');

	if (!($user['room'] > 70000 && $user['room'] < 71000)) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }
	if ($user['in_tower'] == 16) Redirect('castles_o.php');

	$cid = $user['room']-70000;
	$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid) or die();
	$c = mysql_fetch_assoc($q) or die("no castle");

	$second = false;
	$selfclan = false;
	$mainclan = false;

	if (strlen($user['klan'])) {
		$selfclan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$user['klan'].'"');
		$selfclan = mysql_fetch_assoc($selfclan);
		if ($selfclan !== FALSE) {
			$second = CGetSecondClan($selfclan);
		}
	}

	require_once "clan_kazna.php"; 

	if ($selfclan !== FALSE) {
		if ($selfclan['base_klan'] > 0) {
			$mainclan = $second;
		} else {
			$mainclan = $selfclan['short'];
		}
	}


	if (isset($_GET['clanlist']) && $selfclan) {
		echo '<table cellpadding=0 cellspacing=0 border=0>
		<tr><td><img src="http://i.oldbk.com/i/castles/mod_1.jpg"></td></tr>
		<tr><td style="background-repeat: repeat; background-image: url(http://i.oldbk.com/i/castles/mod_2.jpg);">
		<script>
		function CheckSForm() {
			var p1 = 0;
			var p2 = 0;
			var cbs = document.forms["users"].elements["uattack[]"];
			var allchk = 0;
			for(var i=0,cbLen=cbs.length;i<cbLen;i++) {
				if(cbs[i].checked) {
					allchk++;
					if (p1 == 0) {
						p1 = cbs[i].value;
						continue;
					}
					if (p2 == 0) p2 = cbs[i].value;
				} 
			}
			if (allchk == 2) {
				location.href = "castles_pre.php?ua1="+p1+"&ua2="+p2;
				return false;
			} else {
				alert("Вы должны выбрать двух участников турнира");
				return false;
			}
		}
		function OnCheckU() {
			var cbs = document.forms["users"].elements["uattack[]"];
			var allchk = 0;
			for(var i=0,cbLen=cbs.length;i<cbLen;i++) {
				if(cbs[i].checked) {
					allchk++;
				} 
			}
			if (allchk == 2) {
				for(var i=0,cbLen=cbs.length;i<cbLen;i++) {
					if(!cbs[i].checked) {
						cbs[i].disabled = "disabled";
					} 
				}
			} else {
				for(var i=0,cbLen=cbs.length;i<cbLen;i++) {
					if(!cbs[i].checked) {
						cbs[i].disabled = "";
					} 
				}
			}
		}
		</script>
		<div style="margin-left:20px;text-align:left;">
		<form name="users" action="castles_pre.php" method="POST">
		Выберите еще 2-х участников турнира:
		<table>';

		if ($selfclan) {
    			$clan_kazna=clan_kazna_have($selfclan['id']);
			if ($clan_kazna) {
				if ($clan_kazna['kr'] >= 50) {
					$q = mysql_query('SELECT * FROM castles_start');
					$allulist = array();
					while($u = mysql_fetch_assoc($q)) {
						$t = explode(",",$u['users']);
						while(list($k,$v) = each($t)) {
							$allulist[$v] = 1;
						}
					}

					if (!isset($allulist[$user['id']])) {
						$q = mysql_query('SELECT * FROM oldbk.users WHERE klan = "'.$selfclan["short"].'" and id_city = 0 and level = '.$c['nlevel']);
						while($u = mysql_fetch_assoc($q)) {
							if ($u['id'] == $user['id']) continue;
							if (isset($allulist[$u['id']])) continue;
							if ($u['hidden'] > 0) continue;
							echo '<tr><td><input OnChange="OnCheckU();" type="checkbox" name="uattack[]" value="'.$u['id'].'"></td><td>'.nick_hist($u).'</td></tr>';
						}
						if ($second) {
							$q = mysql_query('SELECT * FROM oldbk.users WHERE klan = "'.$second.'" and id_city = 0 and level = '.$c['nlevel']);
							while($u = mysql_fetch_assoc($q)) {
								if ($u['hidden'] > 0) continue;
								if (isset($allulist[$u['id']])) continue;
								echo '<tr><td><input OnChange="OnCheckU();" type="checkbox" name="uattack[]" value="'.$u['id'].'"></td><td>'.nick_hist($u).'</td></tr>';
							}
						}
					} else {
						echo '<tr><td><font color="red">Вы уже участвуете в другом замковом турнире.</font></td></tr>';
					}
				} else {
					echo '<tr><td><font color="red">Для подачи заявки в казне клана должно быть 50кр.</font></td></tr>';
				}
			} else {
				echo '<tr><td><font color="red">У вашего клана нет казны</font></td></tr>';
			}
		}
		echo '</table>
		<input OnClick="return CheckSForm();" type="submit" value="Выбрать"> <input type="button" OnClick="javascript:closehistory2();" value="Закрыть">
		</form>
		</div>
		</td></tr>
		<tr><td><img src="http://i.oldbk.com/i/castles/mod_3.jpg"></td></tr>
		</table>';
		die();
	}


	if (isset($_GET['history'])) {
		$q = mysql_query('SELECT * FROM oldbk.castles_history WHERE castle_id = '.$cid.' ORDER BY `time` DESC LIMIT 50');
		echo '<table border=0 width=100%><tr><td><center><font style="COLOR:#8f0000;FONT-SIZE:10pt"><B>История замка '.$castles_config[$c['num']]['name'].' ['.$c['nlevel'].']</B></font></center></td><td align="right"><a onClick="closehistory();" style="cursor: pointer;">Х</a></td></tr><tr><td colspan="2">';
		while($x = mysql_fetch_assoc($q)) {
			echo '<b>'.date("d/m/Y H:i:s",$x['time'])."</b> ".$x['text'].'<br>';
		}
		if (mysql_num_rows($q) == 50) {
			echo '<center><br><br><b>Более старые записи затерялись в пыльных архивах...</b></center><br>';
		}
		echo '</td></tr></table>';
		die();
	}


	if (isset($_GET['exit'])) {
		mysql_query('UPDATE `users` SET room = 70000 WHERE id = '.$_SESSION['uid']) or die();		
		Redirect("castles.php?level=".$c['nlevel']);
	}

	if (isset($_GET['enter'])) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid.' FOR UPDATE') or mydie();
		$c = mysql_fetch_assoc($q);
		if (!empty($user['klan']) && ($c['clanshort'] == $user['klan'] || ($second !== FALSE && $c['clanshort'] == $second)) && $c['status'] == 0) {
			mysql_query('UPDATE `users` SET room = '.(71000+$cid).' WHERE id = '.$_SESSION['uid']) or mydie();
			$q = mysql_query('COMMIT') or mydie();
			Redirect("castles_inside.php");		
		}
		$q = mysql_query('COMMIT') or mydie();
		Redirect("castles_pre.php");		
	}

	if (isset($_GET['ua1'],$_GET['ua2'])) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid.' FOR UPDATE') or mydie();
		$c = mysql_fetch_assoc($q);
		if (!empty($user['klan']) && CanAttackCastle($c) && $c['status'] == 0 && $user['level'] == $c['nlevel']) {
			$polno = unserialize($selfclan['vozm']);
			if ($polno[$user['id']][9] || $selfclan['glava'] == $user['id']) {
				$in = intval($_GET['ua1']).",".intval($_GET['ua2']);

				$q = mysql_query('SELECT * FROM castles_start');
				$allulist = array();
				while($u = mysql_fetch_assoc($q)) {
					$t = explode(",",$u['users']);
					while(list($k,$v) = each($t)) {
						$allulist[$v] = 1;
					}
				}

				if (!isset($allulist[intval($_GET['ua1'])]) && !isset($allulist[intval($_GET['ua2'])]) && !isset($allulist[$user['id']])) {
					$q = mysql_query('
						SELECT * FROM oldbk.users WHERE id IN ('.$in.') and id_city = 0
						UNION
						SELECT * FROM avalon.users WHERE id IN ('.$in.') and id_city = 1
					') or mydie();
					if (mysql_num_rows($q) == 2) {
						$allok = true;
						while($u = mysql_fetch_assoc($q)) {
							if ($u['klan'] != $user['klan'] && $u['klan'] != $second) {
								$allok = false;
								break;
							}
						}
						if ($allok && CanAttackCastle($c)) {
							// снимаем бабки из казны и добавляем заявку
							$q = mysql_query('SELECT * FROM castles_start WHERE klan = "'.$mainclan.'" and castle_id = '.$cid) or mydie();
							if (mysql_num_rows($q) == 0 && pay_from_kazna($selfclan['id'],1,50,"за участие в замковом турнире")) {
								$usr = $user['id'].",".$in;
								$q = mysql_query('
									INSERT INTO castles_start (castle_id,klan,ownerklan,users) 
									VALUES("'.$cid.'","'.$mainclan.'","'.$user['klan'].'","'.$usr.'");
								') or die();
							}
						}
					}
				}
			}
		}

		$q = mysql_query('COMMIT') or mydie();
		Redirect("castles_pre.php?okattack");
	}

	$center = false;
	$cattack = false;

	// можем ли войти
	if (!empty($user['klan']) && !empty($c['clanshort']) && ($c['clanshort'] == $user['klan'] || $c['clanshort'] === $second) && $c['status'] == 0) {
		$center = true;
	}

	// можем ли нападать
	if (!empty($user['klan']) && CanAttackCastle($c) && $c['status'] == 0 && $user['level'] == $c['nlevel']) {
		$polno = unserialize($selfclan['vozm']);
		if ($polno[$user['id']][9]) {
			$q = mysql_query('SELECT * FROM castles_start WHERE klan = "'.$mainclan.'" and castle_id = '.$cid);
			if (mysql_num_rows($q) == 0) {
				$cattack = 1;
			}
		}
	}

	if (isset($_GET['entero']) && $user['hidden'] == 0 && !empty($user['klan']) && $c['nlevel'] == $user['level'] && $c['status'] == 2 && $c['timeouta']-60 > time() && !empty($user['klan']) && ($c['clanashort1'] == $user['klan'] || $c['clanashort1'] === $second || $c['clanashort2'] == $user['klan'] || $c['clanashort2'] === $second)) {
		$q = mysql_query('SELECT * FROM users WHERE room = '.$user['room'].' AND in_tower = 16 AND (klan = "'.$user['klan'].'" or klan="'.$second.'")');
		if (mysql_num_rows($q) >= 3) die(); 

		$q = mysql_query_cache('SELECT * FROM `effects` where owner = '.$user['id'].' AND (`type`= 11 OR `type`= 12 OR `type`= 13 OR `type`= 14 )',false,10);

		if (count($q) > 0) {
			echo '<font color=red>Вы не можете участвовать в турнире с травмой.</font>';
		} else {
			mysql_query('DELETE FROM users_bonus where owner = '.$user['id']);
			undressall($user['id']);
	
			mysql_query('DELETE FROM `castles_realchars` WHERE `owner` = '.$user['id']);
	
			// кол-во умений
			$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$user['id']);
			$u = mysql_fetch_array($q);
	
			// сила без бонуса
			$srt = $u['sila'] - $u['bpbonussila'];

			// Сохраняем реальные статы в голом виде + все умения.
			mysql_query('INSERT INTO `castles_realchars`
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
			');
	

			if ($user['level'] == 9) {
				$stats = 120;
				$vinos = 13;
			}
			if ($user['level'] == 10) {
				$stats = 140;		
				$vinos = 16;
			}
			if ($user['level'] == 11) {
				$stats = 200;
				$vinos = 19;
			}
			if ($user['level'] == 12) {
				$stats = 230;	
				$vinos = 23;
			}
			if ($user['level'] == 13) {
				$stats = 250;	
				$vinos = 24;
			}
			if ($user['level'] == 14) {
				$stats = 300;	
				$vinos = 25;
			}
	
			$hp = $vinos*3;
			$master = 15;
	
	
			mysql_query('UPDATE `users` SET
				`sila` = "3",
				`lovk` = "3",
				`inta` = "3",
				`vinos` = "'.$vinos.'",
				`intel` = "3",
				`mudra` = "3",
				`stats` = "'.$stats.'",
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
				`in_tower` = 16,
				`bpbonushp` = 0
				WHERE `id` = '.$user['id']
			);
	
			mysql_query('DELETE FROM effects WHERE owner = '.$user['id'].' AND type IN (791,792,793,794,795) ');
			mysql_query('UPDATE effects SET time = 1 WHERE type = 826 and owner = '.$user['id']);

			Redirect('castles_o.php');
		}
	}
?>

<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>

jQuery.fn.center = function () {
	this.css("position","absolute");
	this.css("top", Math.max(0, (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop()) + "px");
	this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	return this;
}

function showhistory() {
       $.get('castles_pre.php?history=1', function(data) {
		$('#pl_list').html(data);
		$('#pl_list').center();
		$('#pl_list').show(200, function() {
		});	


	});
}

function ShowClanUsers() {
       $.get('castles_pre.php?clanlist=1&b='+Math.random(), function(data) {
		$('#pl_list2').html(data);
		$('#pl_list2').center();
		$('#pl_list2').show(200, function() {
		});	
	});
}


function closehistory() {
  	$('#pl_list').hide(200);
}

function closehistory2() {
  	$('#pl_list2').hide(200);
}

</script>
	<script>
			var timerID;
			function refreshPeriodic() {
				location.href='castles_pre.php?'+Math.random();
			}
			timerID = setTimeout("refreshPeriodic()",30000);
	</script>
</head>
<body onResize="$('#pl_list').center();$('#pl_list2').center();" bgcolor=#e2e0e0 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">
<table width="100%" border=0><tr><td width=55% align="center"><h3 style="text-align:right;"><?php echo $castles_config[$c['num']]['name'].' замок ['.$c['nlevel'].']' ?> <?php if (isset($_GET['okattack'])) echo 'Вы подали заявку. В '.($c['hourofday']+1).':00 все заявленные участники должны находиться в замковой локации "Турниры" и иметь профиль по умолчанию.';?></td><td align=right><input type=button value='Обновить' onClick="location.href='castles_pre.php?'+Math.random();"> <INPUT TYPE=button value="Вернуться" onClick="location.href='castles_pre.php?exit=1';"></td></tr></table>
<div id="d1">
	<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=1 border=0 cellspacing="0" cellpadding="0"><tr><td valign=top>
		<div style="position:relative;">
		<?php
			if (!empty($user['klan']) && !empty($c['clanshort']) && ($c['clanshort'] == $user['klan'] || $c['clanshort'] === $second) && ($c['status'] == 0)) {
				$bg = "http://i.oldbk.com/i/castles/bg_open.jpg";
			} else {
				$bg = "http://i.oldbk.com/i/castles/bg_close.jpg";
			}

		?>
		<img OnClick="showhistory();" style="cursor:pointer; z-index:3; position: absolute; left: 698px; top: 275px;" src="http://i.oldbk.com/i/castles/btn3.png" alt="История" title="История" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/btn3_h.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/btn3.png';" />
		<img <?php if ($center) { ?> OnClick="location.href='castles_pre.php?enter=1';" <?php } ?> id="btnenter" style="cursor:pointer; z-index:3; position: absolute; left: 112px; top: 119px;" src="http://i.oldbk.com/i/castles/btn4<?php if ($center) echo '_a'; ?>.png" alt="Войти в замок" title="Войти в замок" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/btn4<?php if ($center) echo 'h'; ?>.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/btn4<?php if ($center) echo '_a'; ?>.png';" />

		<?php 
			$q = mysql_query('SELECT * FROM users WHERE room = '.$user['room'].' AND in_tower = 16 AND (klan = "'.$user['klan'].'" or klan="'.$second.'")');
			if (mysql_num_rows($q) < 3 && $user['hidden'] == 0 && !empty($user['klan']) && $c['nlevel'] == $user['level'] && $c['status'] == 2 && $c['timeouta']-60 > time() && !empty($user['klan']) && ($c['clanashort1'] == $user['klan'] || $c['clanashort1'] === $second || $c['clanashort2'] == $user['klan'] || $c['clanashort2'] === $second)) {
				echo '<img OnClick="location.href=\'castles_pre.php?entero=1;\'" id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2_a.png" alt="Вступить в бой" title="Вступить в бой" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/castles/btn2h.png\';" onmouseout="this.src=\'http://i.oldbk.com/i/castles/btn2_a.png\';" />';
			} else {
                ?>
			<img <?php if ($cattack) { ?>OnClick="ShowClanUsers();" <?php } ?> id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2<?php if ($cattack) echo '_a'; ?>.png" alt="Заявить о нападении" title="Заявить о нападении" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/btn2<?php if ($cattack) echo 'h'; ?>.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/btn2<?php if ($cattack) echo '_a'; ?>.png';" />
		<?php
			}
		?>

		<?php
			// hstatus
			if (empty($c['clanshort'])) {
				$hstatus = 'Замок никому не принадлежит.';
			} else {
				$clan = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$c['clanshort'].'"'));
				$hstatus = CGetClan($clan);
			}

			// bstatus
			$bstatus = "";
			if ($c['status'] == 0) {
				if (CanAttackCastle($c)) {
					$bstatus = 'Открыт для заявок на турнир (до '.($c['hourofday']+1).':00 '.date("d.m.Y").').';
				} else {
					$bstatus = 'Защищен от нападения до '.NextFree($c);
				}
			}
			if ($c['status'] == 1) {
				$bstatus = 'Идёт турнир за замок <a target="_blank" href="castles_log.php?id='.$c['tur_log'].'">&gt;&gt;</a>.';
			}
			if ($c['status'] == 2) {
				$bstatus = 'Ожидается начало битвы между '.CGetClan2($c['clanashort1']).' и '.CGetClan2($c['clanashort2']).' за право владения замком ('.date("H:i",$c['timeouta']).').';
			}
			if ($c['status'] == 3) {
				$bstatus = 'Идёт битва между кланами '.CGetClan2($c['clanashort1']).' и '.CGetClan2($c['clanashort2']).' за право владения замком <a target="_blank" href="logs.php?log='.$c['battle'].'">&gt;&gt;</a>.';
			}
			       
		?>
		<div id="headstatus" style="z-index:3; position: absolute; left: 325px; top: 74px; width: 215px; font-family: Tahoma; color: #653d0a; font-weight: bold; text-align:center;"><?php echo $hstatus; ?></div>
		<div id="bottomstatus" style="z-index:3; position: absolute; left: 35px; top: 484px; width: 800px; font-family: Tahoma; color: #653d0a; font-weight: bold; text-align:center;"><?php echo $bstatus; ?></div>

		<img id="imgareamap" src="<?php echo $bg; ?>" border="0" style="z-index:1;">
	</td></tr></TABLE></td></tr></table>
</div>
<div id="pl_list" style="z-index: 300; position: absolute; left: 50px; top: 30px;
	width: 800px; background-color: #eeeeee; height: 400px;
	border: 1px solid black; display: none; overflow-y: auto;">
</div>

<div id="pl_list2" style="z-index: 350; position: absolute; left: 20px; top: 10px;width: 321px; background-color: #eeeeee;border: 1px solid black; display: none; overflow-y: auto;">
</div>


</body>
</html>