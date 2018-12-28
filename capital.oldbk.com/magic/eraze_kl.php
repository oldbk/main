<?php
// magic идентификацыя
	//if (rand(1,2)==1) {

		if (!($_SESSION['uid'] >0))
		{
			header("Location: index.php");
			die();
		}

			$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$user['id']}' and `type` = '5001' LIMIT 1;"));
			if ($effect['time']) {
					if (mysql_query("DELETE FROM`effects` WHERE `owner` = '{$user['id']}' and `type` = '5001' LIMIT 1 ;")) {

						$mess="Снят штраф на переход между склонностями";
						mysql_query("INSERT INTO `lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$user['id']."','$mess','".time()."');");
						echo "<font color=red><b>Успешно снят штраф склонности </b></font>";
						$bet=1;
						$sbet = 1;
					}
					else {
						echo "<font color=red><b>Произошла ошибка!<b></font>";
					}

			}
			else {
				echo "<font color=red><b>На персонаже нет штрафа на склонность.</b></font>";
			}

?>
