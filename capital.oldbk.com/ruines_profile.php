<?php
$head = <<<HEAD
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<link rel="stylesheet" href="/i/btn.css" type="text/css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	</HEAD>
	<body bgcolor=e2e0e0>
	<h3>Профили характеристик</h3>
	Статы не позволяют сходить в руины? Раскиньте ваши статы так, как вы хотите, и участвуйте в турнире! Выбранный по умолчанию профиль, загрузится сам. Вы можете создавать неограниченное число профилей, и менять их за секунды до турнира!
	<BR><BR>
	<font color=red><b>%ERROR%</b></font>
HEAD;


$main = <<<MAIN
	<table width=100% bordercolor=silver border=1 cellpadding=0 cellspacing=0>
		<tr bgcolor=silver>
			<td>Название</td><td width=25%>По умолчанию</td><td>Удалить</td>
		</tr>
		%PROFILES%
	</table><BR>
	<div class="btn-control">
		<INPUT class="button-mid btn" TYPE=button value="Обновить" onclick="window.location.href='ruines_profile.php?'+Math.random();">
	</div>

	<script>
		function countall() {
			document.getElementById('stats').value = %USERALL%-Math.abs(document.getElementById('sila').value)-Math.abs(document.getElementById('lovk').value)-Math.abs(document.getElementById('inta').value)-Math.abs(document.getElementById('vinos').value)-Math.abs(document.getElementById('intel').value)-Math.abs(document.getElementById('mudra').value);
		}
	</script>

	<form method="POST">
		Название: <input type="text" name="name" value="%CURRENT%">
		<table cellpadding=0 cellspacing=0 >
			<tr bgcolor=silver>
				<td>Характеристика &nbsp;</td><td>Знач.</td>
			</tr>
			<tr>
				<td>Бонус Сила</td><td>%BONUSSILA% не будет учтен</td>
			</tr>
	
			<tr>
				<td>Сила</td><td><input type="text" id="sila" size=4 onblur="countall();" value="%SILA%" name="sila"></td>
			</tr>
			<tr>
				<td>Ловкость</td><td><input type="text" id="lovk" size=4 onblur="countall();" value="%LOVK%" name="lovk"></td>
			</tr>
			<tr>
				<td>Интуиция</td><td><input type="text" id="inta" size=4 onblur="countall();" value="%INTA%" name="inta"></td>
			</tr>
			<tr>
				<td>Выносливость</td><td><input type="text" id="vinos" size=4 onblur="countall();" value="%VINOS%" name="vinos"></td>
			</tr>
			<tr>
				<td>Интеллект</td><td><input type="text" id="intel" size=4 onblur="countall();" value="%INTEL%" name="intel"></td>
			</tr>
			<tr>
				<td>Мудрость</td><td><input type="text" id="mudra" size=4 onblur="countall();" value="%MUDRA%" name="mudra"></td>
			</tr>
			<tr>
				<td>Свободных</td><td><input type="text" id="stats" name="stats" size=4 disabled value="%USERALLLEFT%"></td>
			</tr>
		</table>
		<div class="btn-control">
			<input class="button-big btn" type="submit" OnClick="if (document.getElementById('stats').value != 0) { alert('Ошибка распределения статов! '); return false; }" value="Сохранить/изменить">
		</div>
MAIN;

$bottom = <<<BOTTOM
	</form>
	</body>
	</html>
BOTTOM;
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');

	if ($user['room'] != 999) {
		$head = str_replace('%ERROR%','Вы не успели',$head);
		echo $head.$bottom;
		die();
	}
	if ($user['battle'] != 0 || $user['battle_fin'] != 0) { 
		$head = str_replace('%ERROR%','Вы не успели',$head);
		echo $head.$bottom;
		die();
	}


	if (isset($_GET['delsn'])) {
		if ($user['zayavka'] == 0) {
			$_GET['delsn'] = intval($_GET['delsn']);
			mysql_query('DELETE FROM `ruines_profile` WHERE `id` = '.$_GET['delsn'].' AND `owner` = '.$user['id']) or die();
			Redirect('ruines_profile.php');		
		} else {
			$head = str_replace('%ERROR%','Нельзя удалить в заявке',$head);
		}
	}

	if (isset($_GET['setdef'])) {
		$_GET['setdef'] = intval($_GET['setdef']);
		$q = mysql_query('SELECT * FROM ruines_profile WHERE `owner` = '.$user['id'].' AND id = '.$_GET['setdef']) or die();
		if (mysql_num_rows($q) > 0) {
			mysql_query('UPDATE `ruines_profile` SET `def` = 1 WHERE `owner` = '.$user['id'].' AND  `id` = '.$_GET['setdef']) or die();
			mysql_query('UPDATE `ruines_profile` SET `def` = 0 WHERE `owner` = '.$user['id'].' AND  `id` <> '.$_GET['setdef']) or die();
		}
		$head = str_replace('%ERROR%','Сохранено',$head);
	}


	// получаем инфу перса
	$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$_SESSION["uid"]) or die();
	$user = mysql_fetch_assoc($q) or die();

	$stats = 125;

	// профили по умолчанию
	$q = mysql_query('SELECT * FROM ruines_profile WHERE `owner` = '.$user['id']);
	if (mysql_num_rows($q) == 0) {
		$q = mysql_query('SELECT * FROM ruines_var WHERE `owner` = '.$user['id'].' and var = "defprofile"');
		if (mysql_num_rows($q) == 0) {
			// выставляем профили
			mysql_query('INSERT INTO ruines_var (owner,var,val) VALUES ('.$user['id'].',"defprofile","1")');
			mysql_query('INSERT INTO ruines_profile (owner,name,sila,lovk,inta,vinos,intel) VALUES ('.$user['id'].',"Критовик (ознакомительный профиль)",23,20,46,28,6) ');
			mysql_query('INSERT INTO ruines_profile (owner,name,sila,lovk,inta,vinos,intel) VALUES ('.$user['id'].',"Уворотчик (ознакомительный профиль)",23,48,20,28,6) ');
		}
	}

	if(isset($_POST['name'])){
		// проверяем сумму статов
		$postedstats = abs($_POST['sila'])+abs($_POST['lovk'])+abs($_POST['inta'])+abs($_POST['vinos'])+abs($_POST['intel'])+abs($_POST['mudra']);
		if ($stats == $postedstats && abs($_POST['vinos']) > 0) {
			$q = mysql_query('
				INSERT `ruines_profile` 
				(`owner`,`name`,`sila`,`lovk`,`inta`,`vinos`,`intel`,`mudra`, `def`)
				VALUES (
					"'.$user['id'].'",
					"'.$_POST['name'].'",
					"'.abs($_POST['sila']).'",
					"'.abs($_POST['lovk']).'",
					"'.abs($_POST['inta']).'",
					"'.abs($_POST['vinos']).'",
					"'.abs($_POST['intel']).'",
					"'.abs($_POST['mudra']).'",
					"0"
				) ON DUPLICATE KEY UPDATE
					`sila` = "'.abs($_POST['sila']).'",
					`lovk` = "'.abs($_POST['lovk']).'",
					`inta` = "'.abs($_POST['inta']).'",
					`vinos` = "'.abs($_POST['vinos']).'",
					`intel` = "'.abs($_POST['intel']).'",
					`mudra` = "'.abs($_POST['mudra']).'"
			') or die();
			$head = str_replace('%ERROR%','Сохранено',$head);
		} else {
			$head = str_replace('%ERROR%','Что-то не то со статами... сумму проверьте. Необходимо использовать все статы!',$head);
		}
	}

	$main = str_replace("%USERALL%",$stats,$main);
	$main = str_replace("%BONUSSILA%",$user['bpbonussila'],$main);

	if (isset($_GET['id'])) {
		$q = mysql_query('SELECT * FROM ruines_profile WHERE `owner` = '.$user['id'].' AND `id` = '.intval($_GET['id'])) or die();
		if (mysql_num_rows($q) == 0) Redirect('ruines_profile.php');
		$current = mysql_fetch_assoc($q) or die();

		$main = str_replace("%USERALLLEFT%",$stats-$current['sila']-$current['lovk']-$current['inta']-$current['vinos']-$current['intel']-$current['mudra'],$main);
		$main = str_replace("%CURRENT%",$current['name'],$main);
		$main = str_replace("%SILA%",$current['sila'],$main);
		$main = str_replace("%LOVK%",$current['lovk'],$main);
		$main = str_replace("%INTA%",$current['inta'],$main);
		$main = str_replace("%VINOS%",$current['vinos'],$main);
		$main = str_replace("%INTEL%",$current['intel'],$main);
		$main = str_replace("%MUDRA%",$current['mudra'],$main);
	} else {
		$main = str_replace("%USERALLLEFT%",$stats,$main);
		$main = str_replace("%CURRENT%",'',$main);
		$main = str_replace("%SILA%",'',$main);
		$main = str_replace("%LOVK%",'',$main);
		$main = str_replace("%INTA%",'',$main);
		$main = str_replace("%VINOS%",'',$main);
		$main = str_replace("%INTEL%",'',$main);
		$main = str_replace("%MUDRA%",'',$main);
	}

	$prof = "";
	$data = mysql_query('SELECT * FROM `ruines_profile` WHERE `owner` = '.$user['id']) or die();
	while($row = mysql_fetch_array($data)) {
		$prof .= '<tr onclick=\'location.href="ruines_profile.php?id='.$row['id'].'";\' style="cursor:pointer;"><td><B>'.$row['name'].'</B></td><td><a href="?setdef='.($row['def'] ? "" : $row['id']).'">'.($row['def'] ? '<font color=red>По умолчанию</font>' : 'Установить').'</a></td><td><a href="?delsn='.$row['id'].'">X</a></td></tr>';
	}
	
	$head = str_replace('%ERROR%','',$head);
	$main = str_replace('%PROFILES%',$prof,$main);
	echo $head.$main.$bottom;
?>