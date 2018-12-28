<?php
	session_start();
	require_once "dt_functions.php";
	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	function MyDieS() {
		Redirect("dt_start.php");
	}


        require_once "connect.php";
	require_once "functions.php";
	require_once "dt_rooms.php";

	if ($user['room'] != 10000) { header('Location: main.php'); die(); }
	if ($user['in_tower'] == 15) { header('Location: dt.php'); die(); }
	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }



	if (isset($_GET['exit'])) {
		mysql_query('UPDATE `users` SET `users`.`room` = "21" WHERE `users`.`id` = '.$user['id'].' and in_tower = 0') or die();
		Redirect('city.php');
	}

	$art_dt = " (<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'> Артовая БС)";

    	if(ADMIN) {
		if(isset($_GET['makeart'])) {
			mysql_query("UPDATE dt_var SET valint = ".intval($_GET['makeart'])." where var = 'nextdttype'") or die();
		}

		if(isset($_GET['settime'])) {
			$date = $_GET['year'].'-'.$_GET['mon'].'-'.$_GET['day'].' '.$_GET['hour'].':'.$_GET['min'].':'.'00';
			$q = "UPDATE dt_var SET valint =  UNIX_TIMESTAMP('".$date."') where var = 'nextdt'";
			mysql_query($q) or die();
		}
	}



	$dt_active = mysql_query("SELECT * FROM `dt_map` WHERE `active` = 1");
	if (mysql_num_rows($dt_active) > 0) {	
		$dt_active = mysql_fetch_assoc($dt_active) or MyDieS();
	} else {
		$dt_active = false;
	}


	$nextdt = mysql_query('SELECT * FROM dt_var WHERE var = "nextdt"');
	if (mysql_num_rows($nextdt) > 0) {
		$nextdt = mysql_fetch_assoc($nextdt) or MyDieS();
	} else {
		die('Unable to find variable "nextdt"');
	}


    	$nextdttype = mysql_query("SELECT * FROM dt_var WHERE var='nextdttype'");
	if (mysql_num_rows($nextdttype) > 0) {
		$nextdttype = mysql_fetch_assoc($nextdttype) or MyDieS();
	} else {
		die('Unable to find variable "nextdttype"');
	}

	$min_bet = 5;
	$min_up = 3;

	if($nextdttype['valint'] > 0) {
		$min_bet = 10;
		$min_up = 5;
	}

	function get_stavka () {
		global $user;
		$data = mysql_query("SELECT `credit` FROM `dt_rate` WHERE `owner` = ".$user['id']." AND dtid=0");
		if (mysql_num_rows($data) > 0) {
			$data = mysql_fetch_assoc($data) or MyDieS();
			return $data['credit'];
		} else {
			return 0;
		}
	}

	function set_stavka ($credit) {
		global $user, $nextdttype, $min_bet, $min_up;
	        $profile = mysql_query("SELECT * FROM `dt_profile` WHERE `owner` = '{$user[id]}' AND `def`=1;");
	       	if(mysql_num_rows($profile) > 0) {
			$profile = mysql_fetch_assoc($profile) or MyDieS();

			if($profile['sila'] >= 15 && $profile['lovk'] >= 15 && $profile['inta'] >= 15 && $profile['vinos'] >= 15) {
				if ($user['in_tower'] == 0) {
			                if(get_stavka()) {
						$up=1;
			                } else {
						$up=0;
			                }
		
		       			$credit = (float)$credit;
	
					if($credit >= ($up == 1 ? $min_up : $min_bet) && $user['level'] > 5 && $user['money'] >= $credit) {
						$cmoney = $credit;
						$q = mysql_query('SELECT * FROM dt_rate WHERE dtid = 0 and owner = '.$user['id']);
						if (mysql_num_rows($q) > 0) {
							$cmoney = mysql_fetch_assoc($q);
							$cmoney = $cmoney['credit']+$credit;
						}

						if ($cmoney <= 1000) {

						    try {
								$_can = \components\Helper\location\BaseLocation::getLocation(
									\components\Helper\location\BaseLocation::LOCATION_BS,
									$user
								);
								if(!$_can->can(0)) {
									throw new Exception();
								}


								$set_kr_bonus = 1;

								$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=9"));
								if ($get_ivent['stat'] == 1) {
									$set_kr_bonus = 2;
								}

								mysql_query('START TRANSACTION') or MyDieS();
								$sql = 'INSERT `dt_rate` (`owner`,`credit`,`time`,`dtid`, `ip`)
								VALUES
								('.$user['id'].','.($credit*$set_kr_bonus).','.time().',0,"'.$_SERVER['REMOTE_ADDR'].'")
								ON DUPLICATE KEY UPDATE credit = credit + '.($credit*$set_kr_bonus);

								mysql_query($sql) or MyDieS();

								mysql_query("UPDATE `users` set `money` = `money`- '".$credit."' WHERE id = ".$user['id']) or MyDieS();

								$rec['owner']=$user['id'];
								$rec['owner_login']=$user['login'];
								$rec['owner_balans_do'] = $user['money'];
								$user['money'] -= $credit;
								$rec['owner_balans_posle'] = $user['money'];
								$rec['target']=0;
								$rec['target_login']='БС';
								$rec['type'] = 100;//Делаем ставку
								$rec['sum_kr']=$credit;
								add_to_new_delo($rec) or MyDieS();
								mysql_query('COMMIT') or MyDieS();

							} catch (Exception $ex) {
								echo '<B><FONT COLOR=red>С Вашего IP уже подана заявка на участие.</FONT></B>';
							}

						} else {
							echo "<B><FONT COLOR=red>Ваша ставка не должна превышать 1000 кр.</FONT></B>";
						}
					} else {
						echo "<B><FONT COLOR=red>Ваш уровень не подходит для данной БС или у вас нет ".$credit." кр...</FONT></B>";
					}
				}
			} else {
				echo '<B><FONT COLOR=red>Что то не так с профилем...</FONT></B>';
			}
		} else {
			echo "<B><FONT COLOR=red>Прежде чем сделать ставку, создайте профиль &quot;по умолчанию&quot;</FONT></B>";
		}
	}

	function get_fond () {
		$data = mysql_query("SELECT SUM(`credit`)*0.7 as prize, count(`credit`) as usercount FROM `dt_rate` WHERE dtid = 0");
		if (mysql_num_rows($data) > 0) {
			$data = mysql_fetch_assoc($data) or MyDieS();
			return array('prize'=> round($data['prize'],2), 'usercount' => $data['usercount']);
		} else {
			return false;
		}
	}



	if (!$dt_active && $user['in_tower'] == 0 && isset($_POST['docoin'])) {
		if($nextdt['valint'] > time()-1) {
			if(isset($_POST['docoin'])) {
		            	if ($user['level'] > 5) {
					if ($user['align'] == 4) {
						echo "<font color=red><b>Хаосник не может попасть в Башню смерти...</b></font>";
					} else {
						if ($user['hidden'] > 0) {
							echo "<font color=red><b>Невидимка или Перевоплот не может попасть в Башню смерти...</b></font>";
						} else {
							$_POST['coin'] = round($_POST['coin'],2);
							if ($_POST['coin'] > 0) {
								set_stavka($_POST['coin']);
							}
					    	}
					}
				} else {
					echo "<font color=red><b>У вас уровень не тот!</b></font>";
				}
			}	
		} else {
			echo "<font color=red><b>Турнир в Башне Смерти уже начался!</b></font>";
		}
	}

?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<link rel="stylesheet" href="/i/btn.css" type="text/css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>
function refr() {
	window.location.href='dt_start.php';
	return;
}
</script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0"><tr>
	<td align=right>
	    <div class="btn-control">
            <INPUT class="button-big btn" TYPE="button" value="Профили характеристик" style="background-color:#A9AFC0" onclick="window.open('dt_profile.php', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
            <INPUT class="button-mid btn" TYPE="button" onclick="refr(); return false;" value="Обновить">
            <INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/tower.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
            <INPUT class="button-mid btn" TYPE="button" onclick = "location.href = 'dt_start.php?exit=1';" value="Вернуться" name="strah">
        </div>
	</td></tr></table>

	<table width="100%"><tr><td>
<?
	
	if(!$dt_active) {
		?>
		<h3>Башня смерти.</h3>
		<center><h4>Внимание! Персонаж с травмой не может более зайти в БС!</h4></center>
		<font color=red>Внимание! В турнирах Башни Смерти не работают бонусы от еды, книг магии и прочих эффектов.</font><br>
		Начало турнира: <span class=date><?=date("d.m.Y H:i",$nextdt['valint'])?></span><?php if($nextdttype['valint'] > 0) echo $art_dt; ?><BR>
		<?php
		if(ADMIN) {
			$dd[date('d')] = ' selected';
			$mm[date('m')] = ' selected';
			$hh[date('H')] = ' selected';
			$nn[date('i')] = ' selected';

			?><br/>Установить время:
			<FORM method=GET>
			<input type='hidden' name='settime' value='1'>
			<select name='day'>
			<?
					for($i = 1; $i <= date("t"); $i++) {
						$l = strlen($i);
						$i = ($l==1 ? '0'.$i:$i);
						echo '<option value="'.$i.'" '.$dd[$i].'>'.$i.'</option>';
					}
			?>
			</select>
			<select name='mon'>
			<?
					for($i=1;$i<=12;$i++) {
						$l=strlen($i);
						$i=($l==1?'0'.$i:$i);
						echo '<option value="'.$i.'" '.$mm[$i].'>'.$i.'</option>';
					}
			?>
			</select>

			<select name='year'>
				<?php
				for($i=2014;$i<=2020;$i++) {
					if (date("Y") == $i) {
						echo '<option selected value="'.$i.'">'.$i.'</option>';
					} else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				?>
			</select>

			<select name='hour'>
			<?
					for($i=0; $i <= 23; $i++) {
						$l=strlen($i);
						$i=($l==1?'0'.$i:$i);
						echo '<option value="'.$i.'" '.$hh[$i].'>'.$i.'</option>';
					}
			?>
			</select>
			<select name='min'>
			<?
					for($i=0;$i<=55;) {
						$l=strlen($i);
						$i=($l==1?'0'.$i:$i);
						echo '<option value="'.$i.'">'.$i.'</option>';
						$i+=5;
					}
			?>
			</select>
			<div class="btn-control" style="display: inline-block">
                <input class="button-big btn" type='submit' value='Установить время'>
            </div>
			</FORM><br/>

			<?php
			if($nextdttype['valint'] > 0) {
			 	echo "<a href='dt_start.php?makeart=0' onclick=\"return(confirm('уверены?'));\">Сделать эту БС обычной...</a><br/><br/>";
			} else {
				echo "<a href='dt_start.php?makeart=1' onclick=\"return(confirm('уверены?'));\">Сделать эту БС артовой...</a><br/><br/>";
			}
		}

		$turnir_info = get_fond();
		?>
		Призовой фонд на текущий момент: <B><?=$turnir_info['prize'];?></B> кр.<BR>
		Всего подано заявок: <B><?=$turnir_info['usercount'];?></B><BR>
		<FORM method=POST>
		<h4>Подать заявку на участие в турнире:</h4>
		Чем выше ваша ставка, тем больше шансов принять участие в турнире. <br>
		<?
		        $rate = get_stavka();
			
			if($rate) {
				echo "Вы уже поставили <B><FONT COLOR=red>".round($rate,2)." кр.</B></FONT> хотите увеличить ставку? У вас в наличии <b>".round($user['money'],2)." кр.</b><BR>";
				?>
                <div class="btn-control">
                    <input type="text" name="coin" value="<?=$min_up?>.00" size="8"> <input class="button-mid btn" type="submit" value="увеличить ставку" name="docoin">
                </div>
                <BR><?
			} else {
				echo "У вас в наличии <b>".round($user['money'],2)."</b> кр., сколько ставите кредитов? <br>";
				?><input type="text" name="coin" value="<?=$min_bet?>.00" size="8">
                    <div class="btn-control" style="display: inline-block">
                        <input class="button-mid btn" type="submit" value="Подать заявку" name="docoin">
                    </div>
                <BR><? echo " (мин. <b>{$min_bet}.00 кр.</b>, макс. <b>1000кр.</b>) <font color=red>ВНИМАНИЕ! <a target=\"_blank\" href=\"http://oldbk.com/forum.php?konftop=11&topic=230364723&konftop=11\">Обсуждение.</a></font><BR>";
			}
		?>
		</form>
		<br>Подробнее о башне смерти читайте в разделе "Подсказка".
		<?
	} else {
		$lss = mysql_query("select * from `users` WHERE `in_tower` = 15 and hp > 0");
		$ls = mysql_num_rows($lss);
		while($in = mysql_fetch_array($lss)) {
			$lors .= nick_hist($in).", ";
		}

		$lss = mysql_query("select * from `users_clons` WHERE `id_user` = 84 and hp > 0");
		$ls1 = mysql_num_rows($lss);
		while($in = mysql_fetch_array($lss)) {
			$lors .= nick_hist($in).", ";
		}

		$lors = substr($lors,0,-2);

		?>

		<H4>Турнир начался. <? 
		if($dt_active['arttype']) {
			echo "<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'> Артовая БС.";
		} ?></H4>
		Призовой фонд: <B><?=$dt_active['prize']?> кр.</B><BR>
		<?
		// тут показываем лог
		$q = mysql_query('SELECT * FROM dt_log WHERE dt_id = '.$dt_active['id']);
		$log = mysql_fetch_assoc($q);
		if ($dt_active['darktype']) {
			echo substr($log['log'],0,strpos($log['log'],'<BR>')+4);
			echo '<br><br>Тьма окутывает Башню Смерти<BR>';
		} else {
			echo $log['log'];
		}
		?><BR>
		<?php
			if ($dt_active['darktype']) {
				?>
				Всего живых участников на данный момент: <B><?=$ls?><? if ($ls1) echo ' + '.$ls1?></B>
				<?
			} else {

				if ($dt_active['halftype']) {
				$ll = mysql_fetch_assoc(mysql_query('SELECT count(*) as ll FROM users WHERE room IN ('.implode(",",$dt_halfleft).')'));
				$ll2 = mysql_fetch_assoc(mysql_query('SELECT count(*) as ll2 FROM users WHERE room IN ('.implode(",",$dt_halfright).')'));
				?>
				Всего живых участников на данный момент: <B><? echo $ls.' ('.$ll['ll'].'+'.$ll2['ll2'].') ';?><? if ($ls1) echo ' + '.$ls1?></B> (<?=$lors?>)

				<?php
				} else {
			?>
				Всего живых участников на данный момент: <B><?=$ls?><? if ($ls1) echo ' + '.$ls1?></B> (<?=$lors?>)
			<?php
				}
			}
			?>
		<BR>
		<P align=right><INPUT TYPE="button" onclick="refr(); return false;" value="Обновить"></P>
		<?
	}
	?><BR><BR>

	</td><td width=510>
	<div id="maindiv" style="position:relative;z-index:1;"><img src="http://i.oldbk.com/i/map/npc_archiv_fon2.png" id="mainbg">
	<a href="?quest=1"><img style="z-index:3; position: absolute; left: 226px; top: 12px;" src="http://i.oldbk.com/i/map/npc_archiv.png" alt="Архивариус" title="Архивариус" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/npc_archiv_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/map/npc_archiv.png'"/></a>
<?php

$mldiag = array();
$mlquest = "-40/30";
if(isset($_GET['qaction']) && isset($_GET['d'])) {
	$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_ARCHIVARIUS);
	//зашли в движок квестов
	$dialog_id = isset($_GET['d']) ? (int)$_GET['d'] : null;
	$action_id = isset($_GET['a']) ? (int)$_GET['a'] : null;
	$dialog = $BotDialog->dialog($dialog_id, $action_id);
	if($dialog !== false) {
		$mldiag[0] = $dialog['message'];
		foreach ($dialog['actions'] as $action) {
			$key = '&a='.$action['action'];
			if(isset($action['dialog'])) {
				$key .= '&d='.$action['dialog'];
			}
			$mldiag[$key] = $action['message'];
		}
	}
}

if (isset($_GET['quest']) && empty($mldiag)) {
	$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_ARCHIVARIUS);

	$mldiag[0] = 'Рад встрече, чем я могу помочь?';

	foreach ($BotDialog->getMainDialog() as $dialog) {
		$key = '&d='.$dialog['dialog'];
		$mldiag[$key] = $dialog['title'];
	}

	$mldiag[4] = 'Ничего не нужно, пока!';

}
if(!empty($mldiag)) {
	require_once('mlquest.php');
}

?>
	</div>
	</td></tr></table>

<?php /*
	$qq = mysql_fetch_assoc(mysql_query('SELECT * FROM dt_usersvar WHERE var = "wins" AND owner = "'.$user['id'].'"'));
	if (!$qq['val']) $qq['val'] = 0;
	echo 'Всего одержано побед: '.$qq['val']; */
?>
<BR>
<?
	$row = mysql_query("SELECT * FROM `dt_map` WHERE `active` = 0 ORDER by `id` DESC LIMIT 20;");
?>
<P>&nbsp;<H4>Победители 20-ти предыдущих турниров.</H4>
<OL>
<?
	$hh = array();
	while($data = mysql_fetch_array($row)) {
		$hh[] = $data;
	}
	for($i = 0; $i< count($hh); $i++) {
       	?>
		<LI>
		<? 
			if($hh[$i]['arttype'] == 1) {
				echo "<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'>";
			} 
		?> Победитель: <?=$hh[$i]['winnerlog']?> Начало турнира <FONT class=date><?=date("d.m.y H:i",$hh[$i]['starttime'])?></FONT> продолжительность <FONT class=date><?=floor(($hh[$i]['endtime']-$hh[$i]['starttime'])/60/60)?> ч. <?=floor(($hh[$i]['endtime']-$hh[$i]['starttime'])/60-floor(($hh[$i]['endtime']-$hh[$i]['starttime'])/60/60)*60)?> мин.</FONT> приз: <B><?=$hh[$i]['prize']?> кр.</B> <A HREF="/dt_log.php?id=<?=$hh[$i]['id']?>" target="_blank">история турнира »»</A><BR>
		</LI>
		<?
	}
?>
</OL>
<?
$data = mysql_query_cache("SELECT * FROM `dt_map` WHERE active = 0 ORDER by `prize` DESC LIMIT 1 ",false,60*5);
if (count($data)) {
	$data=$data[0];
	?>
	<H4>Максимальный выигрыш</H4>
	<?
	if($data['arttype'] > 0) {echo "<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'>";} ?> Победитель: <?=$data['winnerlog']?> Начало турнира <FONT class=date><?=date("d.m.y H:i",$data['starttime'])?></FONT> продолжительность <FONT class=date><?=floor(($data['endtime']-$data['starttime'])/60/60)?> ч. <?=floor(($data['endtime']-$data['starttime'])/60-floor(($data['endtime']-$data['starttime'])/60/60)*60)?> мин.</FONT> приз: <B><?=$data['prize']?> кр.</B> <A HREF="/dt_log.php?id=<?=$data['id']?>" target="_blank">история турнира »»</A><BR>
<?
}
/*
$data = mysql_query_cache("SELECT * FROM `dt_map` WHERE active = 0  ORDER by (`endtime`-`starttime`) DESC LIMIT 1 ",false,60*5);
if (count($data)) {
	$data=$data[0];
	?>
	<H4>Самый продолжительный турнир</H4>
	<? if($data['arttype'] > 0) {echo "<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'>";} ?> Победитель: <?=$data['winnerlog']?> Начало турнира <FONT class=date><?=date("d.m.y H:i",$data['starttime'])?></FONT> продолжительность <FONT class=date><?=floor(($data['endtime']-$data['starttime'])/60/60)?> ч. <?=floor(($data['endtime']-$data['starttime'])/60-floor(($data['endtime']-$data['starttime'])/60/60)*60)?> мин.</FONT> приз: <B><?=$data['prize']?> кр.</B> <A HREF="/dt_log.php?id=<?=$data['id']?>" target="_blank">история турнира »»</A><BR>
	<?
}

*/
?>
</BODY>
</HTML>