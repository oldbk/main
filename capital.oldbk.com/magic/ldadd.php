<?php
// magic идентификацыя
	//if (rand(1,2)==1) {


		if (!($_SESSION['uid'] >0)) header("Location: index.php");
		$target=$_POST['ldnick'];
//for hiddens sleep
if ( strpos($target,"Невидимка:" ) !== FALSE )
	{
		$hitarget=explode(":",$target);
		$target=$hitarget[0];
		$realtar=mysql_fetch_array(mysql_query("SELECT * FROM effects WHERE `idiluz` = '{$hitarget[1]}' LIMIT 1;"));
		$tar = mysql_fetch_array(mysql_query("SELECT `id`,`align`,`login` FROM oldbk.`users` WHERE `id` = '{$realtar[owner]}' LIMIT 1;"));
		$realnametarget=$tar[login];
	}
	else
	{
		$realnametarget=$target;
		$tar = mysql_fetch_array(mysql_query("SELECT `id`,`align` FROM oldbk.`users` WHERE `login` = '{$_POST['ldnick']}' LIMIT 1;"));
	}
//

if($_POST['dec']=='on')
	{
	 $_POST['ldtext']=iconv('utf-8', 'cp1251', $_POST['ldtext']);
	}


		if ($tar['id']) 
		{
			$tar=check_users_city_data($tar[id]);
			
			if($tar[id_city]==$user[id_city] || ADMIN)
			{
				if ($user['id'] ==5) { $user['align']=1.1;}
				$ok=0;
				if ($user['align'] > '2' && $user['align'] < '3' && $moj[$_POST['use']]==1) {
					$ok=1;
				}
				elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && ($tar['align'] > '1' && $tar['align'] < '2') && ($user['align'] >= $tar['align'])) {
					$ok=1;
				}
				elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && !($tar['align'] > '2' && $tar['align'] < '3') && !($tar['align'] > '1' && $tar['align'] < '2')) {
					$ok=1;
				}
				if ($ok == 1) {
					if ($_POST['red']) {
						if (!$_POST['ldtext']) {$pal="";}
						else {
	
							$date_today = date("m.d.y H:i");
							$pal=$date_today." ".$_POST['ldtext'];
						}
						
						if (($tar['deal']==1) and ($user['klan']!='radminion'))
						{
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
						else
						{
						if (mysql_query("UPDATE ".$db_city[$tar[id_city]]."`users` SET `palcom` = '$pal' WHERE `id` = {$tar['id']} LIMIT 1;")) {
							$mess="Сообщение от ".$user['login'].": ".$_POST['ldtext'];
							mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
							$mess="Изменена причина отправки в хаос/блокировки &quot;{$_POST['ldnick']}&quot;: $mess";
							mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','2');");
							echo "<font color=red><b>Успешно изменена причина отправки в хаос/блокировки персонажа \"$target\"</b></font>";
						}
						else {
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
						}
					}
					else {
						$mess="Сообщение от ".$user['login'].": ".$_POST['ldtext'];
						if (mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');")) {
							$mess="Добавлена запись в дело &quot;{$_POST['ldnick']}&quot;: $mess";
							mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','2');");
							echo "<font color=red><b>Успешно добавлена запись в дело игрока \"$target\"</b></font>";
						}
						else {
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
					}
				}
				else 
				{
					echo "<font color=red><b>Вы не можете добавить запись в дело этого персонажа!<b></font>";
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
