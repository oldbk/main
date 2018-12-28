<?php
$head = <<<HEADHEAD
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<link rel="stylesheet" href="/i/btn.css" type="text/css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<style>
		div {display: inline;}
	</style>
	</HEAD>

	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0>
 	<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
	<TR valign=top>
	<TD width=3% align=center>&nbsp;</TD>
	<TD width=100%><h3>Руины Старого замка - %NOW% логи турниров</h3></div></TD><TD align=right nowrap>
	
	<div class="btn-control">
		%BUTTON% 
		<input class="button-mid btn" type=button value='Обновить' onClick="location.href=location.href"> 
		<INPUT class="button-mid btn" TYPE=button value="Вернуться" onClick="location.href='ruines_start.php';">
	</div>
	
	</form>
	</div></TD>
	</TD></TR>
	<TR height=100%><td>&nbsp;</td><TD valign=top colspan=2><br><br>
HEADHEAD;

$bottom = <<<BOTTOM
	</TD></TR>
	</table>
BOTTOM;

	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_GET['id'])) {
		if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	
	}

	require_once('connect.php');

	if (!isset($_GET['id'])) {
		require_once('functions.php');
		require_once('ruines_config.php');
	}

	if (isset($_GET['old'])) {
		$head = str_replace('%BUTTON%','<input class="button-big btn" type=button value="Просмотреть текущие" onClick="location.href=\'ruines_log.php\'">',$head);
		$head = str_replace('%NOW%','завершенные',$head);
	} else {
		$head = str_replace('%BUTTON%','<input class="button-big btn" type=button value="Просмотреть завершенные" onClick="location.href=\'ruines_log.php?old&date='.date("d.m.y").'\'">',$head);
		$head = str_replace('%NOW%','текущие',$head);
	}

	echo $head;

	if (!isset($_GET['id'])) {
		if (isset($_GET['old'])) {
			if ($_REQUEST['date'] != null) {
				if (!isset($_REQUEST['filter'])) $_REQUEST['filter'] = "";
				echo '<form method="GET" action="ruines_log.php"><TABLE width=100% cellspacing=0 cellpadding=0><TR>
				<TD valign=top>&nbsp;<A HREF="ruines_log.php?old&date='.
				date("d.m.y",mktime(0, 0, 0, substr($_REQUEST['date'],3,2), substr($_REQUEST['date'],0,2)-1, "20".substr($_REQUEST['date'],6,2)))
				.'&filter='.(($_REQUEST['filter'])?$_REQUEST['filter']:"").'">« Предыдущий день</A></TD>
				<TD valign=top align=center><H3>Записи о завершенных турнирах за '.(($_REQUEST['date']!=1)?"{$_REQUEST['date']}":"".date("d.m.y")).'</H3></TD>
				<TD  valign=top align=right><A HREF="ruines_log.php?old&date='.
				date("d.m.y",mktime(0, 0, 0, substr($_REQUEST['date'],3,2), substr($_REQUEST['date'],0,2)+1, "20".substr($_REQUEST['date'],6,2)))
				.'&filter='.(($_REQUEST['filter'])?$_REQUEST['filter']:"").'">Следующий день »</A>&nbsp;</TD>
				</TR><TR><TD colspan=3 align=center>Показать только бои персонажа: <INPUT TYPE=text NAME=filter value="'.(($_REQUEST['filter'])?$_REQUEST['filter']:"").'"> за <INPUT TYPE=hidden NAME=old value="1"> <INPUT TYPE=text NAME=date size=12 value="'.(($_REQUEST['date']!=1)?"{$_REQUEST['date']}":"".date("d.m.y")).'"> <INPUT TYPE=submit value="фильтр!"></TD>
				</TR></TABLE></form>';

				//переменная для проверки границы
				$DHIST1=mktime('00','00','00',substr($_REQUEST['date'],3,2),substr($_REQUEST['date'],0,2),substr($_REQUEST['date'],6,2));
				$DHIST2=mktime('23','59','59',substr($_REQUEST['date'],3,2),substr($_REQUEST['date'],0,2),substr($_REQUEST['date'],6,2));


				if (empty($_REQUEST['filter'])) 
				{
					$data = mysql_query('SELECT * FROM ruines_log_index WHERE starttime >= '.$DHIST1.' AND starttime <='.$DHIST2.' GROUP BY `mapid`');
					$maps = array();
					while($row = mysql_fetch_array($data)) {
						$maps[$row['mapid']] = 1;
					}
				} else 
				{
					$u = mysql_fetch_array(mysql_query("SELECT `id` FROM `users` WHERE `login` = '".(($_REQUEST['filter'])?"{$_REQUEST['filter']}":"{$user['login']}")."' LIMIT 1;"));
					
					if ($u['id']>0)
					{
					$data = mysql_query('SELECT * FROM ruines_log_index WHERE user = '.$u['id'].' AND starttime >= '.$DHIST1.' AND starttime <='.$DHIST2);
					$maps = array();
					while($row = mysql_fetch_array($data)) {
							$maps[$row['mapid']] = 1;
						}
					}
				}
	
		if (count($maps)>0)
		{
				$mapids = "";
				while(list($k,$v) = each($maps)) {
					$mapids .= $k.",";
				}
		}		
				if (strlen($mapids)) {
					$mapids = substr($mapids,0,strlen($mapids)-1);
					$q = mysql_query('SELECT * FROM ruines_log WHERE id IN ('.$mapids.') AND active = 0') or die();
					if (mysql_num_rows($q) > 0) {
						while($l = mysql_fetch_assoc($q)) {
							if ($l['win']) {
								// ставим флаг победы
								$l['t'.$l['win']] .= ' <img src="http://i.oldbk.com/i/flag.gif">';
							}
							echo '<span class=date>'.date("d.m.y H:i",$l['starttime']).'</span> '.$l['t1'].' против '.$l['t2'].' <a target="_blank" href="?id='.$l['id'].'">просмотреть лог</a><br><br>';
						}
					} else {
						echo '<CENTER><BR><BR><B>В этот день не было турниров, или же, летописец опять потерял свитки...</B><BR><BR><BR></CENTER>';
					}
				} else {
					echo '<CENTER><BR><BR><B>В этот день не было турниров, или же, летописец опять потерял свитки...</B><BR><BR><BR></CENTER>';
				}
			}
		} else {
			$q = mysql_query('SELECT * FROM ruines_log WHERE active = 1 ORDER BY `id` DESC');
			while($l = mysql_fetch_assoc($q)) {
				echo '<span class=date>'.date("d.m.y H:i",$l['starttime']).'</span> '.$l['t1'].' против '.$l['t2'].' <a target="_blank" href="?id='.$l['id'].'">просмотреть лог</a><br><br>';
			}
		}
	} else {
		$q = mysql_query('SELECT * FROM ruines_log WHERE id = '.intval($_GET['id']));
		if (mysql_num_rows($q) > 0) {
			$l = mysql_fetch_assoc($q);
			echo $l['log'];
		}
	}

	echo $bottom;	
?>