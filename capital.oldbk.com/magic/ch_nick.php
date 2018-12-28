<?php
// magic идентификацыя
	//if (rand(1,2)==1) {


		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$tar = mysql_fetch_array(mysql_query("SELECT `id`,`login` FROM oldbk.`users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		$target=$_POST['target'];
		$target1=$_POST['target1'];
		if ($tar['id']) {
			$ok=0;
			if ($user['align'] > '2' && $user['align'] < '3') {
				$ok=1;
			}
			elseif ($user['align'] == '1.99') {
					$ok=1;
			}
			if ($ok == 1) {
echo "UPDATE oldbk.`users` set `login`='{$target1}' WHERE `login`='{$target}' LIMIT 1 ;";
				if (mysql_query("UPDATE oldbk.`users` set `login`='{$target1}' WHERE `login`='{$target}' LIMIT 1 ;")) 
				{

					//и в авалоне
					mysql_query("UPDATE avalon.`users` set `login`='{$target1}' WHERE `login`='{$target}' LIMIT 1 ;");
					$mess="Сообщение от ".$user['login'].": Смена ника с ".$target." на ".$target1." ";
					mysql_query("UPDATE oldbk.`users_pas_ch` set `login`='{$target1}' WHERE `login`='{$target}' LIMIT 1 ;");

					mysql_query("INSERT INTO oldbk.users_nick_hist SET uid='".$tar[id]."' , old_login='".$tar[login]."';");
					
					mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
					if ($user['align'] == '1.99') 
					{
						mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."');");
					}
					echo "<font color=red><b>Успешно сменен ник персонажа \"$target\" на \"$target1\"</b></font>";
				}
				else {
					echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
			}
			else {
				echo "<font color=red><b>Вы не можете сменить ник этому персонажу!<b></font>";
			}
		}
		else {
			echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
		}
?>
