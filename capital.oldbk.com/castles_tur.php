<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	include('connect.php');
	include('functions.php');
	require_once('castles_config.php');
	require_once('castles_functions.php');

	if ($user['room'] != 72001) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	if ($user['in_tower'] == 0) {
		mysql_query("DELETE from oldbk.inventory where owner='{$user[id]}' and bs_owner = 3");
		if (mysql_affected_rows()>0) {
			addchp('<font color=red>Внимание оружейка замки FIX!</font> FIX Отработал на ID:'.$user[id].'  ','{[]}Десятый{[]}',-1,0);
		}
	}

	function nick_castle($telo) {
		$mm .= "<img src=\"http://i.oldbk.com/i/align_".($telo['align']>0 ? $telo['align']:"0").".gif\">";
		if ($telo['klan'] <> '') {
			$mm .= '<img title="'.$telo['klan'].'" src="http://i.oldbk.com/i/klan/'.$telo['klan'].'.gif">';
		}
		$mm .= "<B>";
		if ($telo['room'] != 72001) {
			$mm .= '<font color=gray>'.$telo['login'].'</font>';
		} else {
			$mm .= $telo['login'];
		}
		$mm .= "</B> [{$telo['level']}]<a href=inf.php?{$telo['id']} target=_blank><IMG SRC=http://i.oldbk.com/i/inf.gif WIDTH=12 HEIGHT=11 ALT=\"Инф. о {$telo['login']}\"></a>";
		return $mm;
	}



	if (isset($_GET['armory']) && $user['battle'] == 0 && $user['hidden'] == 0 && strlen($user['klan']) && $user['level'] >= 9 && $user['level'] <= 14 && $user['in_tower'] == 0) {
		$eff = mysql_num_rows(mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND (`type` >=11 AND `type` <= 14);"));
		if ($eff == 0) {
			$sk_row=" `sila`='{$user[sila]}',`lovk`='{$user[lovk]}',`inta`='{$user[inta]}',`vinos`='{$user[vinos]}',`intel`='{$user[intel]}',
			`mudra`='{$user[mudra]}',`duh`='{$user[duh]}',`bojes`='{$user[bojes]}',`noj`='{$user[noj]}',`mec`='{$user[mec]}',`topor`='{$user[topor]}',`dubina`='{$user[dubina]}',
			`maxhp`='{$user[maxhp]}',`hp`='{$user[hp]}',`maxmana`='{$user[maxmana]}',`mana`='{$user[mana]}',`sergi`='{$user[sergi]}',`kulon`='{$user[kulon]}',`perchi`='{$user[perchi]}',
			`weap`='{$user[weap]}',`bron`='{$user[bron]}',`r1`='{$user[r1]}',`r2`='{$user[r2]}',`r3`='{$user[r3]}',`runa1`='{$user[runa1]}',`runa2`='{$user[runa2]}',`runa3`='{$user[runa3]}',`helm`='{$user[helm]}',`shit`='{$user[shit]}',`boots`='{$user[boots]}',
			`stats`='{$user[stats]}',`master`='{$user[master]}',`nakidka`='{$user[nakidka]}',`rubashka`='{$user[rubashka]}',`mfire`='{$user[mfire]}',`mwater`='{$user[mwater]}',`mair`='{$user[mair]}',`mearth`='{$user[mearth]}',
			`mlight`='{$user[mlight]}',`mgray`='{$user[mgray]}',`mdark`='{$user[mdark]}', `bpbonushp`='{$user[bpbonushp]}'";

			$asql="INSERT INTO oldbk.`castles_profile` SET `owner`='{$user[id]}',`prof`=0, ".$sk_row." ON DUPLICATE KEY UPDATE  ".$sk_row;
			mysql_query($asql);

		 	mysql_query("update oldbk.inventory set dressed=0 where id IN (".GetDressedItems($user,DRESSED_BOTH).")");

			// расчёты по статам
			if ($user['level'] == 9) {
				$arr['stats'] = 120;
				$arr['vinos'] = 13;
				$arr['master'] = 10;
			} elseif ($user['level'] == 10) {
				$arr['stats'] = 140;
				$arr['vinos'] = 16;
				$arr['master'] = 11;
			} elseif ($user['level'] == 11) {
				$arr['stats'] = 200;
				$arr['vinos'] = 19;
				$arr['master'] = 12;
			} elseif ($user['level'] == 12) {
				$arr['stats'] = 230;
				$arr['vinos'] = 23;
				$arr['master'] = 14;
			} elseif ($user['level'] == 13) {
				$arr['stats'] = 250;
				$arr['vinos'] = 24;
				$arr['master'] = 14;
			} elseif ($user['level'] == 14) {
				$arr['stats'] = 300;
				$arr['vinos'] = 25;
				$arr['master'] = 14;
			}

                        $arr['hp']=$arr['vinos']*6;


			mysql_query("UPDATE `users` SET
				`users`.`sila`=3,`users`.`lovk`=3,`users`.`inta`=3,`users`.`vinos`='{$arr[vinos]}',`users`.`intel`=0,`users`.`mudra`=0,
				`users`.`duh`=0,`users`.`bojes`=0,`users`.`noj`=0,`users`.`mec`=0,`users`.`topor`=0,`users`.`dubina`=0,
				`users`.`maxhp`='{$arr[hp]}',`users`.`hp`='{$arr[hp]}',`users`.`maxmana`=0,`users`.`mana`=0,`users`.`sergi`=0,`users`.`kulon`=0,
				`users`.`perchi`=0,`users`.`weap`=0,`users`.`bron`=0,`users`.`r1`=0,`users`.`r2`=0,`users`.`r3`=0,`users`.`helm`=0,`users`.`runa1`=0,`users`.`runa2`=0,`users`.`runa3`=0,
				`users`.`shit`=0,`users`.`boots`=0,`users`.`stats`='{$arr[stats]}',`users`.`master`='{$arr[master]}',`users`.`nakidka`=0,`users`.`rubashka`=0,`users`.`mfire`=0,
				`users`.`mwater`=0,`users`.`mair`=0,`users`.`mearth`=0,`users`.`mlight`=0,`users`.`mgray`=0,`users`.`mdark`=0,`users`.`bpbonushp`=0,
				`users`.`room` = '198' WHERE `users`.`id`  = '{$user[id]}'
			");
			Redirect("castles_armory.php");
		}
		
	}


	if (isset($_GET['exit']) && $user['in_tower'] == 0) {
		mysql_query('UPDATE `users` SET room = 70000 WHERE id = '.$_SESSION['uid']) or die();		
		Redirect("castles.php?level=".intval($_GET['exit']));
		
	}

	if (isset($_GET['exit3']) && $user['in_tower'] == 0) {
		mysql_query('UPDATE `users` SET room = 70000 WHERE id = '.$_SESSION['uid']) or die();		
		Redirect("castles.php?level=999");
		
	}

	if (isset($_GET['exit2']) && $user['in_tower'] == 0) {
		$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "cfromcity"') or die();
		$var = mysql_fetch_assoc($q) or die();

		if ($var['val'] == 0) {
			mysql_query('UPDATE `users` SET room = 49999 WHERE id = '.$_SESSION['uid']) or die();		
			Redirect("outcity.php");
		} else {
			mysql_query('UPDATE `users` SET room = 49998 WHERE id = '.$_SESSION['uid']) or die();		
			Redirect("aoutcity.php");
		}
		
	}

	

	$castles = array();
	$q = mysql_query('SELECT * FROM castles WHERE id != 155');

	while($c = mysql_fetch_assoc($q)) {
		$castles[$c['nlevel']][$c['num']] = $c;
	}

	if ($user['id'] == 102904) {
		$q = mysql_query('SELECT * FROM castles WHERE id = 155');
	
		while($c = mysql_fetch_assoc($q)) {
			$castles[$c['nlevel']][$c['num']] = $c;
		}	
	}
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
<script>
	function SwitchTab(newt) {
		document.getElementById("m"+newt).style.backgroundImage='url(http://i.oldbk.com/i/map/active_bg.jpg)';
		document.getElementById("t"+newt).style.color="#464646";
		document.getElementById("m"+newt).style.fontWeight="bold";
	}

</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="http://i.oldbk.com/i/castles/jquery.qtip.min.js" type="text/javascript"></script>
<link href="http://i.oldbk.com/i/castles/jquery.qtip.css" type="text/css" rel="stylesheet">
	<?php if (!isset($_GET['logs'])) { ?>
	<script>
			var timerID;
			function refreshPeriodic() {
				location.href='castles_tur.php?'+Math.random();
			}
			timerID = setTimeout("refreshPeriodic()",30000);
	</script>
	<?php } ?>
</head>
<body bgcolor=#D7D7D7 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">
<br><br>
<center>
<table border=0 style="text-align:center; padding:0px; margin:0px;border-collapse:collapse;">
<tr>
<?php
if ($user['in_tower'] == 0) {
	reset($castles);
	while(list($k,$v) = each($castles)) {
		echo '<td id="m'.$k.'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?exit='.$k.'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t'.$k.'" color="#a4a4a4">Уровень '.$k.'</td>';
	}

	echo '<td id="m'.($k+1).'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/active_bg.jpg);"><font id="t'.($k+1).'" color="#464646"><b>Турниры</b> <img src="http://i.oldbk.com/i/castles/castle_icon.png" align="middle"></td>';
	echo '<td id="m'.($k+2).'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?exit3='.$k.'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t'.($k+1).'" color="#a4a4a4">Осада замка</td>';
	echo '<td id="m'.($k+3).'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?exit2='.$k.'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t'.($k+2).'" color="#a4a4a4" onmouseout="document.getElementById(\'backi\').src=\'http://i.oldbk.com/i/castles/back_button.png\';" onmouseover="document.getElementById(\'backi\').src=\'http://i.oldbk.com/i/castles/back_button_hover2.png\';">Вернуться <img src="http://i.oldbk.com/i/castles/back_button.png" align="middle" id="backi" onmouseover="this.src=\'http://i.oldbk.com/i/castles/back_button_hover2.png\';" onmouseout="this.src=\'http://i.oldbk.com/i/castles/back_button.png\';"></td>';
}
?>
</tr>
</table>
<div id="d1">
	<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0">
		<TR><TD align=center valign=top>
		<table width=1 border=0 cellspacing="0" cellpadding="0"><tr><td valign=top>
		<div style="width:868px; height:400px; position:relative;background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/castles/tmain_bg.jpg);">
		<?php
			if (isset($_GET['logs'])) {
				echo '<br>';
				$q = mysql_query('SELECT *, castles_tur.id AS ct, castles_tur.status AS ctstatus FROM castles_tur LEFT JOIN castles ON castles.id = castles_tur.castle_id ORDER BY castles_tur.id DESC LIMIT 50');
				while($l = mysql_fetch_assoc($q)) {
					// выводим логи
					if ($l['ctstatus'] == 0) {
						echo '<span style="margin-left:120px;"><FONT class=date>'.date("d.m.Y H:i",$l['starttime']).'</font> Турнир за право владения замком <b>'.$castles_config[$l['num']]['name'].' ['.$l['nlevel'].']</b>. Турнир идёт. <a target="_blank" href="castles_log.php?id='.$l['ct'].'">Лог</A></span><BR>';
					} elseif ($l['ctstatus'] == 1) {
						echo '<span style="margin-left:120px;"><FONT class=date>'.date("d.m.Y H:i",$l['starttime']).'</font> Турнир за право владения замком <b>'.$castles_config[$l['num']]['name'].' ['.$l['nlevel'].']</b>. Турнир закончен. <a target="_blank" href="castles_log.php?id='.$l['ct'].'">Лог</A></span><BR>';
					} elseif ($l['ctstatus'] == 3) {
						echo '<span style="margin-left:120px;"><FONT class=date>'.date("d.m.Y H:i",$l['starttime']).'</font> Турнир за право владения замком <b>'.$castles_config[$l['num']]['name'].' ['.$l['nlevel'].']</b>. Турнир закончен. <a target="_blank" href="castles_log.php?id='.$l['ct'].'">Лог</A></span><BR>';
					} elseif ($l['ctstatus'] == 4) {
						echo '<span style="margin-left:120px;"><FONT class=date>'.date("d.m.Y H:i",$l['starttime']).'</font> Турнир за право владения замком <b>'.$castles_config[$l['num']]['name'].' ['.$l['nlevel'].']</b>. Турнир не состоялся.</span><BR>';
					}
				}
			} else {
				$eff = mysql_num_rows(mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND (`type` >=11 AND `type` <= 14);"));
				if ($eff == 0 && $user['in_tower'] == 0 && strlen($user['klan']) && $user['level'] >= 9 && $user['level'] <=  14 && $user['battle'] == 0 && $user['hidden'] == 0) { ?><img OnClick="location.href='castles_tur.php?armory=1';" style="cursor:pointer; z-index:3; position: absolute; left: 560px; top: 20px;" src="http://i.oldbk.com/i/castles/b1.png" alt="Оружейная комната" title="Оружейная комната" onmouseover="this.src='http://i.oldbk.com/i/castles/b1_h.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/b1.png';" /><?php } ?>
				<?php if ($user['in_tower'] == 0) { ?><img OnClick="location.href='castles_tur.php?logs=1';" style="cursor:pointer; z-index:3; position: absolute; left: 720px; top: 20px;" src="http://i.oldbk.com/i/castles/b2.png" alt="История турниров" title="История турниров" onmouseover="this.src='http://i.oldbk.com/i/castles/b2_h.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/b2.png';" /> <?php } ?>
		<?php

				$showtext = true;
				if ($user['in_tower'] == 0) {
					// не в турнире, показываем заявки
					$q = mysql_query('SELECT * FROM castles_start LEFT JOIN castles ON castles_start.castle_id = castles.id');
					$starts = array();
					$u = array();
					while($c = mysql_fetch_assoc($q)) {
						$starts[$c['castle_id']][] = $c;
						$t = explode(",",$c['users']);
						while(list($k,$v) = each($t)) {
							$u[$v] = array();
						}
					}
					
/*					$q = mysql_query('
						SELECT * FROM oldbk.users WHERE id IN ('.implode(",",array_keys($u)).') and id_city = 0
						UNION
						SELECT * FROM avalon.users WHERE id IN ('.implode(",",array_keys($u)).') and id_city = 1
					');
*/
					if (count($u)>0)
					{
					$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.implode(",",array_keys($u)).') ');
					

					while($c = mysql_fetch_assoc($q)) {
						$u[$c['id']] = $c;
					}
					}
					echo '<br>';
					while(list($k,$v) = each($starts)) {
						$showtext = false;
						echo '<table style="width: 402px; height: 46px; margin-left: 45px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/castles/zagolovok_bg.png);"><tr><td><div style="margin-left:60px;margin-top:15px;"><b>'.$castles_config[$v[0]['num']]['name'].' ['.$v[0]['nlevel'].']</b> - Начало турнира: <font color=green><b>'.($v[0]['hourofday']+1).':00</b></font></span></td></table>';
						$myklan = "";
						while(list($ka,$va) = each($v)) {
							if ($va['klan'] == $user['klan'] || $va['ownerklan'] == $user['klan']) {
								$t = explode(",",$va['users']);
								$myklan = "(Ваш клан подал заявку в составе: ".nick_castle($u[$t[0]]).", ".nick_castle($u[$t[1]]).", ".nick_castle($u[$t[2]]).")";
							}
						}
						echo '<span style="margin-left: 100px;">';
						if (count($v) == 1) {
							echo 'В заявке: <b>1</b> клан';
						} elseif(count($v) >= 5) {
							echo 'В заявке: <b>'.count($v).'</b> кланов';
						} else {
							echo 'В заявке: <b>'.count($v).'</b> клана';
						}
						echo '</span><br>';
						if (strlen($myklan)) {
							echo '<span style="margin-left: 100px;display: block;">';
							echo $myklan;
							echo '</span>';
						}
						echo '<br><br>';
					}
					$q = mysql_query('SELECT *,castles_tur.id AS ct FROM castles_tur LEFT JOIN castles ON castles_tur.castle_id = castles.id WHERE castles_tur.status = 0 ORDER BY castles_tur.id DESC');
					while($v = mysql_fetch_assoc($q)) {
						$showtext = false;
						echo '<table style="width: 402px; height: 46px; margin-left: 45px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/castles/zagolovok_bg.png);"><tr><td><div style="margin-left:60px;margin-top:15px;"><b>'.$castles_config[$v['num']]['name'].' ['.$v['nlevel'].']</b> - Турнир идёт <a href="castles_log.php?id='.$v['ct'].'" target="_blank">&gt;&gt;</a></span></td></table>';
						echo '<span style="margin-left: 100px;">';
						echo '</span><br><br>';
					}

					$q = mysql_query('SELECT * FROM castles WHERE status = 2');
					while($v = mysql_fetch_assoc($q)) {
						$showtext = false;
						echo '<table style="width: 402px; height: 46px; margin-left: 45px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/castles/zagolovok_bg.png);"><tr><td><div style="margin-left:60px;margin-top:15px;"><b>'.$castles_config[$v['num']]['name'].' ['.$v['nlevel'].']</b> - Турнир окончен.</span></td></table>';
						echo '<span style="margin-left: 100px;">';
						echo 'Победителями стали команды <b>'.CGetClan2($v['clanashort1']).'</b> и <b>'.CGetClan2($v['clanashort2']).'</b>.</span><br><span style="margin-left: 100px;">Начало битвы между ними за право владения замком начнется в '.date("H:i",$v['timeouta']);
						echo '</span><br><br>';
					}
					if ($showtext) {
						echo '<br><br><span style="margin-left: 100px;">';
						echo 'В данный момент нет ни одного турнира.<br><br></span>';
						echo '<span style="margin-left: 100px;"> Ближайшие освобождающиеся замки:';

						$castles = array();
						$q = mysql_query('SELECT * FROM castles WHERE id != 155 ORDER BY dayofweek ASC, hourofday ASC');
						while($c = mysql_fetch_assoc($q)) {
							$castles[] = $c;
						}
						$l1 = array();
						reset($castles);
						while(list($k,$v) = each($castles)) {
							if ($v['nlevel'] == 9 || $v['nlevel'] == 10) continue;
							if ($v['status'] == 0) {
								if ($v['dayofweek'] == date("N") && $v['hourofday'] >= date("G")) {
									$l1[] = $v;
								}
								if ($v['dayofweek'] == date("N")+1) {
									$l1[] = $v;
								}
	
							}
						}

						$i = 1;
						echo '<table width="100%" style="margin-left: 100px;">';
						if (count($l1)) {
							while(list($k,$v) = each($l1)) {
								echo '<tr><td><B>'.$i.'.</b></td><td nowrap>'.$castles_config[$v['num']]['name'].' замок ['.$v['nlevel'].']</td><td></td><td nowrap>'.NextFree($v).'</td></tr>';
								$i++;
							}
							echo '<tr><td colspan=2>&nbsp;</td></tr>';
						} else {
							echo '<tr><td><center>Нет замков, которые скоро освобождаются.</center></td></tr>';
						}
						echo '</table>';

						echo '</span>';
					}
				} else {
					// выводим лог текущего турнира
					echo "<center><input type=button value='Обновить' onClick=\"location.href='castles_tur.php?'+Math.random();\"></center><br><br>";

					$q = mysql_query('SELECT * FROM castles_tur WHERE id = '.$user['id_grup']);
					$t = mysql_fetch_assoc($q);
					echo $t['log'];
				}
			}
		?>
		</div>
	</td></tr></TABLE></td></tr></table>               
</div>
</body>
</html>
