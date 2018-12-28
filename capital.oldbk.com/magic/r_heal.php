<?php

// снятие штрафа в руинах на кладбище
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}

$tar = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;")); 
if ($tar['id']) {
	if ($tar['ruines'] == $user['ruines']) {
		$effect = mysql_fetch_array(mysql_query("SELECT `id`,`time` FROM `effects` WHERE `owner` = '{$tar['id']}' AND `time` >= ".time()." and `type` = '10' LIMIT 1;")); 
	
		$du = (int)($tar[room]*0.01);
		$du = $du*100;
		$user_room = $tar[room]-$du;
	
	
		if ($effect['time']) {
			if ($user_room == 75) {
				// снимаем эффект
				mysql_query('DELETE FROM `effects` WHERE id = '.$effect['id']);
				echo "<font color=red><b>Персонаж \"$target\" может покинуть кладбище.</b></font>";
				$bet=1;
				$sbet = 1;
	
				$team_colors = array(1 => "blue", 2 => "red");
	
				$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$user['id_grup']].'>'.nick_hist($user).'</font> использовал свиток <b>Реинкарнации</b> и вернул к жизни <font color='.$team_colors[$tar['id_grup']].'>'.nick_hist($tar).'</font><BR>';
				mysql_query('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$user['ruines']);
	
	
				addchp ('<font color=red>Внимание!</font> Вы подверглись реинкарнации с помощью персонажа <B><font color="'.$team_colors[$user['id_grup']].'">'.$user['login'].'</font>. Вы можете покинуть Кладбище','{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
	
			} else {
				echo "<font color=red><b>Персонаж \"$target\" не находится на кладбище.</b></font>";
			}
		} else {
			echo "<font color=red><b>Персонаж \"$target\" не находится на кладбище.</b></font>";
		}
	} else {
		echo "<font color=red><b>Персонаж \"$target\" не находится в вашем турнире.</b></font>";
	}		
} else {
	echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
}
?>