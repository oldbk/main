<?php
	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include 'functions.php';
	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($user['room'] != '10000') { header('location: main.php'); die(); }

	$min_sila=15;
	$min_lovka=15;
	$min_inta=15;
	$min_vinos=15;
	
	$stats=107;

	// профили по умолчанию
	$q = mysql_query('SELECT * FROM dt_profile WHERE `owner` = '.$user['id']);
	if (mysql_num_rows($q) == 0) {
		$q = mysql_query('SELECT * FROM dt_usersvar WHERE `owner` = '.$user['id'].' and var = "defprofile"');
		if (mysql_num_rows($q) == 0) {
			// выставляем профили
			mysql_query('INSERT INTO dt_usersvar (owner,var,val) VALUES ('.$user['id'].',"defprofile","1")');
			mysql_query('INSERT INTO dt_profile (owner,name,sila,lovk,inta,vinos,intel) VALUES ('.$user['id'].',"Обычный турнир (ознакомительный профиль)",20,20,41,20,6) ');
			mysql_query('INSERT INTO dt_profile (owner,name,sila,lovk,inta,vinos,intel) VALUES ('.$user['id'].',"Артовый турнир (ознакомительный профиль)",25,25,26,25,6) ');
		}
	}

	
	undressall($_SESSION['uid']);

	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

	if ((int)$_GET['delsn']>0) {
		mysql_query("DELETE FROM `dt_profile` WHERE `id`='".(int)$_GET['delsn']."' AND `owner` = ".$user['id']);
	}

	if(isset($_POST['name']) && !empty($_POST['name'])) {
		$_POST['sila'] = intval($_POST['sila']);
		$_POST['lovk'] = intval($_POST['lovk']);
		$_POST['inta'] = intval($_POST['inta']);
		$_POST['intel'] = intval($_POST['intel']);
		$_POST['vinos'] = intval($_POST['vinos']);
		$_POST['mudra'] = intval($_POST['mudra']);

		if($_POST['sila'] < $min_sila) {
			$error=1;
		} elseif($_POST['lovk'] < $min_lovka) {
			$error=1;
		} elseif($_POST['inta'] < $min_inta) {
			$error=1;
		} elseif($_POST['vinos'] < $min_vinos) {
			$error=1;
		} elseif($_POST['mudra'] < 0) {
			$error=1;
		} elseif ($stats == ($_POST['sila']+$_POST['lovk']+$_POST['inta']+$_POST['vinos']+$_POST['intel']+$_POST['mudra'])) { 
			$prize = '0';
			if($_POST['prize']==1) {
				$prize = '0';
			} elseif($_POST['prize'] == 2) {
				$prize = '1';
			}
			
			mysql_query('
				INSERT `dt_profile` (`owner`,`name`,`sila`,`lovk`,`inta`,`vinos`,`intel`,`mudra`,`prize`)
				VALUES (
					'.$user['id'].',
					"'.$_POST['name'].'",
					'.$_POST['sila'].',
					'.$_POST['lovk'].',
					'.$_POST['inta'].',
					'.$_POST['vinos'].',
					'.$_POST['intel'].',
					'.$_POST['mudra'].',
					'.$prize.'
				)  ON DUPLICATE KEY UPDATE
					`sila` = '.$_POST['sila'].',
					`lovk` = '.$_POST['lovk'].',
					`inta` = '.$_POST['inta'].',
					`vinos` = '.$_POST['vinos'].',
					`intel` = '.$_POST['intel'].',
					`mudra` = '.$_POST['mudra'].',
					`prize` = '.$prize
				) or die(mysql_error());
			echo "<font color=red><b>Сохранено.</b></font>";
		} else {
			$error=1;

		}

		if($error==1) {
			echo "<font color=red><b>Что-то не то со статами... проверьте сумму или распределение. <br>Необходимо использовать все статы!</b></font>";
		}
	}


	$tec = array();
	if (isset($_GET['id'])) {
		$tec = mysql_fetch_array(mysql_query("SELECT * FROM `dt_profile` WHERE `owner` = ".$user['id']." AND `id` = ".intval($_GET['id'])));
	}

	if(isset($_GET['setdef'])) {
		mysql_query("UPDATE `dt_profile` SET `def` = 1 WHERE `owner` = ".$user['id']." AND `id` = ".(int)$_GET['setdef']);
		mysql_query("UPDATE `dt_profile` SET `def` = 0 WHERE `owner` = ".$user['id']." AND `id` <> ".(int)$_GET['setdef']);
		echo "<font color=red><b>Сохранено.</b></font>";
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
</HEAD>
<body bgcolor=e2e0e0>
<h3>Профили характеристик</h3>
Статы не позволяют сходить в БС? Раскиньте ваши статы так, как вы хотите, и участвуйте в турнире! Выбранный по умолчанию профиль, загрузится сам. Вы можете создавать неограниченное число профилей, и менять их за секунды до турнира!
<BR><BR>
<table width=100% bordercolor=silver border=1 cellpadding=0 cellspacing=0>
	<tr bgcolor=silver>
		<td>Название</td><td width=25%>По ум.</td><td>Удалить</td>
	</tr>
	<?php
	 $data = mysql_query("SELECT * FROM `dt_profile` WHERE `owner` = ".$user['id']);
	 while($row = mysql_fetch_array($data)) 
	 {
		echo "<tr onclick='location.href=\"?id={$row['id']}\";' style='cursor:pointer;'><td><B>{$row['name']}</B></td><td><a href='?setdef=".($row['def']?"":$row['id'])."'>".($row['def']?"<font color=red>По умолчанию</font>":"Установить")."</a></td>
		<td><a href='?delsn=".$row['id']."&ddname=".$row['name']."'>X</a></td></tr>\n";
	 }
	?>
</table><BR>
<div class="btn-control">
    <INPUT class="button-mid btn" TYPE=button value="Обновить" onclick="window.location.href='dt_profile.php';">
</div>
<script>
	function countall() {
		document.getElementById('stats').value = <?=$stats ?>-Math.abs(document.getElementById('sila').value)-Math.abs(document.getElementById('lovk').value)-Math.abs(document.getElementById('inta').value)-Math.abs(document.getElementById('vinos').value)-Math.abs(document.getElementById('intel').value)-Math.abs(document.getElementById('mudra').value);
	}
</script>
<form method="POST">
	Назв.: <input type="text" name="name" value="<?=$tec['name']?>"><br>
	Приз: Опыт<input type="radio" name="prize" value=1 <?=($tec['prize']==0?'checked':'')?> > Репутация <input type="radio" name="prize" value=2 <?=($tec['prize']==1?'checked':'')?>>
	<table cellpadding=0 cellspacing=0 >
		<tr bgcolor=silver>
			<td>Характеристика &nbsp;</td><td>Знач.</td>
		</tr>
		<tr>
			<td>Бонус Сила</td><td><?=$user[bpbonussila]?> не будет учтен</td>
		</tr>

		<tr>
			<td>Сила</td><td><input type="text" id="sila" size=4 onblur="countall();" value="<?=$tec['sila']?>" name="sila"><мин. <?=$min_sila?>></td>
		</tr>
		<tr>
			<td>Ловкость</td><td><input type="text" id="lovk" size=4 onblur="countall();" value="<?=$tec['lovk']?>" name="lovk"><мин. <?=$min_lovka?>></td>
		</tr>
		<tr>
			<td>Интуиция</td><td><input type="text" id="inta" size=4 onblur="countall();" value="<?=$tec['inta']?>" name="inta"><мин. <?=$min_inta?>></td>
		</tr>
		<tr>
			<td>Выносливость</td><td><input type="text" id="vinos" size=4 onblur="countall();" value="<?=$tec['vinos']?>" name="vinos"><мин. <?=$min_vinos?>></td>
		</tr>
		<tr>
			<td>Интеллект</td><td><input type="text" id="intel" size=4 onblur="countall();" value="<?=$tec['intel']?>" name="intel"></td>
		</tr>
		<tr>
			<td>Мудрость</td><td><input type="text" id="mudra" size=4 onblur="countall();" value="<?=$tec['mudra']?>" name="mudra"></td>
		</tr>
		<tr>
			<td>Свободных</td><td><input type="text" id="stats" name="stats" size=4 disabled value="<?=$stats?>"></td>
		</tr>
	</table>
	<div class="btn-control">
        <input class="button-big btn" type="submit" OnClick="if (document.getElementById('stats').value!=0) { alert('Ошибка распределения статов! '); return false; }" value="Сохранить/изменить">
    </div>
</form>
</body>
</html>
