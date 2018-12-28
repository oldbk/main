<?php
// magic идентификацыя
	//if (rand(1,2)==1) {
		if (!($_SESSION['uid'] >0)) header("Location: index.php");
		
		$target=$_POST['target'];
		$tar = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;")); 
		$magictime=time()+604800;
		if ($tar['id']) 
		{
			$tar=check_users_city_data($tar[id]);
			
			if($tar[id_city]==$user[id_city] || ADMIN==true)
			{
				if ($tar['klan']!='' ) 
				{
					echo "<font color=red><b>Персонаж \"$target\" состоит в клане!</b></font>";
				}
				else 
				{
					$ok=0;
					if ($user['align'] > '2' && $user['align'] < '3' && $moj[$_POST['use']]==1) {
						$ok=1;
					}
					elseif ($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) {
						$ok=1;
					}
					if ($ok == 1) {
						if($tar[id_city]==$user[id_city])
						{	
							if (mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$tar['id']."','Паладинская проверка','".$magictime."','20');")) {
								$messtel="Помечено, что персонаж чист перед законом";
								$mess="".$user['login']." сделал пометку что ".$_POST['target']." чист перед законом";
								mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
								mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','1');");
								
								tele_check_new($tar,$messtel);
								
								echo "<font color=red><b>Успешно поставлена проверка персонажу \"$target\"</b></font>";			
							} 
							else {
								echo "<font color=red><b>Произошла ошибка!<b></font>";
							}
						}
						else
						if(ADMIN==true)
						{
							
							if (mysql_query("INSERT INTO ".$db_city[$tar[id_city]]."`effects` (`owner`,`name`,`time`,`type`) values ('".$tar['id']."','Паладинская проверка','".$magictime."','20');")) {
								$messtel="Помечено, что персонаж чист перед законом";
								$mess="".$user['login']." сделал пометку что ".$_POST['target']." чист перед законом";
								mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
								mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','1');");
								
								tele_check_new($tar,$messtel);
								
								echo "<font color=red><b>Успешно поставлена проверка персонажу \"$target\"</b></font>";			
							} 
							else {
								echo "<font color=red><b>Произошла ошибка!<b></font>";
							}
						}
						
					}
					else {
						echo "<font color=red><b>Вы не можете поставить проверку!<b></font>";
					}
				}
			}
			else
			{
				echo "<font color=red><b>Персонаж \"$target\" в другом городе!<b></font>";
			}
		}
		else 
		{
			echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
		}
?>
