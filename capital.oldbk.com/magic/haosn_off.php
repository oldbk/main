<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$tar = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
$target=$_POST['target'];
if ($tar['id']) {
	$tar=check_users_city_data($tar[id]);
	if($tar[id_city]==$user[id_city] || ADMIN) {
		$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '4' LIMIT 1;"));
		if (($effect['time']) OR ($tar[align]==4)) {
			$ok=0;
			if ($user['align'] > '2' && $user['align'] < '3') {
				$ok=1;
			} elseif (($user['align'] > '1' && $user['align']<2) && ($tar['align'] > '1' && $tar['align'] < '2') && ($user['align'] > $tar['align'])) {
				$ok=1;
			} elseif (($user['align'] > '1' && $user['align']<2) && !($tar['align'] > '2' && $tar['align'] < '3') && !($tar['align'] > '1' && $tar['align'] < '2')) {
				$ok=1;
			}


			if ($ok == 1) {
				mysql_query("DELETE FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '4'");
				mysql_query("UPDATE ".$db_city[$tar[id_city]]."`users` SET `palcom` = '',`align`='0' WHERE `id` = {$tar['id']}");

				if ($user['sex'] == 1) {
					$action="выпустил";
				} else {
					$action="выпустила";
				}

				if ($user['align'] > '2' && $user['align'] < '3')  {
					$angel="Ангел";
				} elseif ($user['align'] > '1' && $user['align'] < '2') {
					$angel="Паладин";
				}

				$mess="$angel &quot;{$user['login']}&quot; $action из хаоса &quot;$target&quot;..";
				mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
				mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','9');");

				addch("<img src=i/magic/haos_off.gif> $mess",$user['room'],$user['id_city']);
				if($tar[room]!=$user[room]) {
					addchp (' <img src=i/magic/haos_off.gif> '.$mess,'{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
				}
				echo "<font color=red><b>Успешно снято заклятие хаоса с персонажа \"$target\"</b></font>";
				@file_get_contents('http://blog.oldbk.com/api/refresh.html?game_id='.$tar['id']);
			} else {
				echo "<font color=red><b>Вы не можете снять заклятие хаоса с этого персонажа!<b></font>";
			}
		} else {
			echo "<font color=red><b>На персонаже \"$target\" нет заклятия хаоса </b></font>";
		}
	} else {
		echo "<font color=red><b>Персонаж \"$target\" в другом городе!<b></font>";
	}	
} else {
	echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
}
?>