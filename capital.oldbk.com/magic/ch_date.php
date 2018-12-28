<?php
// magic идентификацыя
	//if (rand(1,2)==1) {


		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$tar = mysql_fetch_array(mysql_query("SELECT `id`,`login` FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		$target=$_POST['target'];
		$bdate=$_POST['target1'];
		if ($tar['id']) {
			$ok=0;
			if ($user['align'] > '2' && $user['align'] < '3') {
				$ok=1;
			}
			elseif ($user['align'] == '1.99') {
					$ok=1;
			}
			if ($ok == 1) {
				if (mysql_query("UPDATE `users` set `borndate`='{$bdate}' WHERE `login`='{$target}' LIMIT 1 ;")) {
				
					$mess="Сообщение от ".$user['login'].": Смена даты рождения на ".$bdate." ";

					mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
					if ($user['align'] == '1.99') {
						mysql_query("INSERT INTO `paldelo`(`id`,`author`,`text`,`date`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."');");
					}
					echo "<font color=red><b>Успешно сменена дата рождения персонажа на \"$bdate\"</b></font>";
				}
				else {
					echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
			}
			else {
				echo "<font color=red><b>Вы не можете сменить дату рождения этому персонажу!<b></font>";
			}
		}
		else {
			echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
		}
?>
