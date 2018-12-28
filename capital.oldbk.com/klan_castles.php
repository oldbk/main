<?php

	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	require_once "connect.php";
	require_once "functions.php";
	require_once "castles_config.php";	
	require_once "castles_functions.php";	

	if ($user['klan'] == '') {
	    	die();
	}

?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<style>
	.row {
		cursor:pointer;
	}
.m {background: #99CCCC;text-align: center;}
.s {background: #BBDDDD;text-align: center;}
.s2 {background: #C0D6D4;text-align: center;}

A.menu {
	FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #959595; TEXT-DECORATION: none

}
A.menu2 {
	FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #8F0000; TEXT-DECORATION: none

}
A.menu:hover {
	FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #8F0000; TEXT-DECORATION: none

}
.menu22{
  FONT-WEIGHT: bold; FONT-SIZE: 8pt; COLOR: #003388; TEXT-DECORATION: none
}
.menu221{
background-color: #A5A5A5;
text-align: center;
}
</style>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#d7d7d7>

<table width=100% border=0>
	<tr>
		<td colspan=2 valign=top align=right><input type="button" title="Вернуться" value="Вернуться" onclick="location.href='main.php';"></td></tr>
		<tr><td width=50% rowspan=2 valign=top>
			<center>
			<table border=0 width=956>
			<tr>
				<td width=956 style="background-image: url(http://i.oldbk.com/i/frames/menu_bg33.jpg); background-repeat: no-repeat" >
					<table border=0 cellpadding=4 cellspacing=3>
						<tr height=38>
							<td width="15">&nbsp;</td>
							<td align="center" width=127 valign=top><a class=<?=($_GET['razdel']=='main'?'"menu2"':'"menu"')?> href=klan.php?razdel=main>Главная</a></td>
							<td align="center" width=127 valign=top><a class=<?=($_GET['razdel']=='kazna'?'"menu2"':'"menu"')?> href=klan.php?razdel=kazna>Казна</a></td>
							<td align="center" width=140 valign=top><a class=menu href=klan_arsenal.php>Арсенал</a></td>
							<td align="center" width=143 valign=top><a class=<?=($_GET['razdel']=='wars'?'"menu2"':'"menu"')?> href=klan.php?razdel=wars>Войны и враги</a></td>
							<td align="center" width=140 valign=top><a class=<?=($_GET['razdel']=='message'?'"menu2"':'"menu"')?> href=klan.php?razdel=message>Сообщения</a></td>
							<td align="center" width=132 valign=top><a class=menu2 href="klan_castles.php">Замки</a></td>
							<td align="center" width=122 valign=top><a class=<?=($_GET['razdel']=='maintains'?'"menu2"':'"menu"')?> href=klan.php?razdel=maintains>Управление</a></td>
							<td width="5">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>

		</td></tr>
</table>

<?php
	$selfclan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$user['klan'].'"');
	$selfclan = mysql_fetch_assoc($selfclan);
	$second = FALSE;
	if ($selfclan !== FALSE) {
		$second = CGetSecondClan($selfclan);
	}

	$qq = 'SELECT * FROM oldbk.castles WHERE (clanshort = "'.$user['klan'].'"';
	if ($second !== FALSE) {
		$qq .= ' or clanshort = "'.$second.'")';
	} else {
		$qq .= ')';
	}

	$qq .= ' or (clanashort1 = "'.$user['klan'].'" or clanashort2 = "'.$user['klan'].'"';
	if ($second !== FALSE) {
		$qq .= ' or clanashort1 = "'.$second.'" or clanashort2 = "'.$second.'")';
	} else {
		$qq .= ')';
	}

	$q = mysql_query($qq);
	$castles = array();
	while($c = mysql_fetch_assoc($q)) {
		$castles[] = $c;
	}
?>
<script>
if (/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())) {
document.write('<br><br><br>');
}
</script>
<table style="" width=90% border=0 align=center>
<tr>
<td width=33% nowrap valign=top>
<fieldset><legend align="center" style="margin:0 auto;"><b>Наши владения</b></legend>
<center><font style="color:#959595;">Наши владения до:</font></center><br>
<?php
	$l1 = array();
	reset($castles);
	while(list($k,$v) = each($castles)) {
		if (($v['clanshort'] == $user['klan'] || ($second !== FALSE && $v['clanshort'] == $second))) {
			$l1[] = $v;
		}
	}

	$i = 1;
	echo '<table width="100%">';
	if (count($l1)) {
		while(list($k,$v) = each($l1)) {
			echo '<tr><td><b>'.$i.'.</b></td><td nowrap><img title='.($v['clanshort']=='pal'?'Орден паладинов':$v['clanshort']).' src=http://i.oldbk.com/i/klan/'.$v['clanshort'].'.gif> '.$castles_config[$v['num']]['name'].' замок ['.$v['nlevel'].']</td><td nowrap><b>'.NextFree($v).'</b></td></tr>';
			$i++;
		}
	}

	if (!count($l1)) {
		echo '<tr><td><center>Вашему клану не принадлежит ни один из замков.</center></td></tr>';
	}
	echo '</table>';
?>
</fieldset>
</td>

<td width=33% nowrap valign=top>
<fieldset><legend align="center" style="margin:0 auto;"><b>Наши атаки</b></legend>
<center><font style="color:#959595;">Во сколько начало атаки</font></center><br>
<?php
	$l1 = array();

	reset($castles);
	while(list($k,$v) = each($castles)) {
		if (($v['clanashort1'] == $user['klan'] || ($second !== FALSE && $v['clanashort1'] == $second)) || ($v['clanashort2'] == $user['klan'] || ($second !== FALSE && $v['clanashort2'] == $second))) {
			if ($v['status'] == 2) {
				$l1[] = $v;
			}
		}
	}

	$i = 1;
	echo '<table width="100%">';
	if (count($l1)) {
		while(list($k,$v) = each($l1)) {
			echo '<tr><td><B>'.$i.'.</b></td><td><a target="_blank" href="http://oldbk.com/encicl/klani/clans.php?clan='.$v['clanashort1'].'"><img title='.($v['clanashort1']=='pal'?'Орден паладинов':$v['clanashort1']).' src=http://i.oldbk.com/i/klan/'.$v['clanashort1'].'.gif></a> против <a target="_blank" href="http://oldbk.com/encicl/klani/clans.php?clan='.$v['clanashort2'].'"><img title='.($v['clanashort2']=='pal'?'Орден паладинов':$v['clanashort2']).' src=http://i.oldbk.com/i/klan/'.$v['clanashort2'].'.gif></a></td><td> '.$castles_config[$v['num']]['name'].' замок ['.$v['nlevel'].']</td><td nowrap><b style="color:red;">'.date("d.m H:i",$v['timeouta']).'</b></td></tr>';
			$i++;
		}
		echo '<tr><td colspan=2>&nbsp;</td></tr>';
	} else {
		echo '<tr><td><center>Ваш клан не претендует ни на один из замков.</center></td></tr>';
	}
	echo '</table>';

?>
</fieldset>
</td>
<td width=33% nowrap valign=top>
<fieldset><legend align="center" style="margin:0 auto;"><b>Освобождающиеся замки</b></legend>
<center><font style="color:#959595;">Во сколько освобождаются ближайшие замки</font></center><br>
<?php
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
	echo '<table width="100%">';
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

?>
</fieldset>
</td>
</tr>
</table>

</body>
</html>