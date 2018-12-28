<?php



		if (!($_SESSION['uid'] >0)) header("Location: index.php");
		$target=$_POST['target'];
		$tar = mysql_fetch_array(mysql_query("SELECT `id`,`room` FROM `users` WHERE `login` = '{$target}' LIMIT 1;"));

	if ($tar['id']) {
			mysql_query("UPDATE `users` SET `room`='{$tar['room']}' where `id`='{$user[id]}' LIMIT 1;");
			
			echo "<font color=red><b>Успешно телепортировались к \"$target\" </b></font>";		
			}
		else {
			echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
		}
?>
