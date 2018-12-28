<?php
// magic идентификацыя
	//if (rand(1,2)==1) {
		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$m = mysql_fetch_array(mysql_query("SELECT `id`,`align`,`married`,`sex`,`login` FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		$w = mysql_fetch_array(mysql_query("SELECT `id`,`align`,`married`,`sex`,`login` FROM `users` WHERE `login` = '{$_POST['target1']}' LIMIT 1;"));
	//	print_r($m);
		$muzh=$_POST['target'];
		$zhena=$_POST['target1'];
		if ($m['id'] and $w['id']) 
		{
			$m=check_users_city_data($m[id]);
			$w=check_users_city_data($w[id]);
			if($m[id_city]==$user[id_city] && $w[id_city]==$user[id_city])
			{
				if ($w['login'] != $_POST['target1']) {
					echo "<font color=red><b>Персонаж ".$_POST['target']." не состоит в браке с ".$_POST['target1']."!<b></font>";
				}
				elseif ($m['login'] != $_POST['target']) {
					echo "<font color=red><b>Персонаж ".$_POST['target1']." не состоит в браке с ".$_POST['target']."!<b></font>";
				}
				elseif ($m['sex'] != 1) {
					echo "<font color=red><b>Неправильный пол жениха!<b></font>";
				}
				elseif ($w['sex'] != 0) {
					echo "<font color=red><b>Неправильный пол невесты!<b></font>";
				}
				elseif($m['id']!=$w['married'] || $w['id']!=$m['married'])
				{
					echo "<font color=red><b>Они женаты, но не друг на друге!<b></font>";
				}
				else {
					if ((($user['align'] > '2' && $user['align'] < '3') || ($user['align'] > '1' && $user['align'] < '2')) && $moj[$_POST['use']]==1 ) {
						if (mysql_query("UPDATE `users` SET `married`='' WHERE `id` = '{$m['id']}' LIMIT 1;") && mysql_query("UPDATE `users` SET `married`='' WHERE `id` = '{$w['id']}' LIMIT 1;")) {
							$mess="Расторжение брака между &quot;$muzh&quot; и &quot;$zhena&quot;, регистратор &quot;{$user['login']}&quot;.";
							mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$m['id']."','$mess','".time()."');");
							mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$w['id']."','$mess','".time()."');");
							mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','4');");
							echo "<font color=red><b>Успешно расторгнут брак между \"$muzh\" и \"$zhena\"!</b></font>";
						}
						else {
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
					}
					else {
						echo "<font color=red><b>Вы не можете расторгнуть брак!<b></font>";
					}
				}
			}
			else
			{
				echo "<font color=red><b>Персонаж(и) \"$target\" в другом городе!<b></font>";
			}
		}
		else {
			echo "<font color=red><b>Персонаж \"$muzh\" или \"$zhena\" не существует!<b></font>";
		}
?>
