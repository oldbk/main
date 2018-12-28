<?php
		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$tar = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		$target=$_POST['target'];
		if ($tar['id'])
		{
		
			$wmz=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users_money`  WHERE `owner` = '{$tar['id']}' LIMIT 1;"));
			if ($wmz)
				{
				
					if ($wmz['wmid']!='')
					{
					//есть актуальные данные просто делаем разбан
						mysql_query("UPDATE `oldbk`.`users_money` SET `ban`=0, bantime = 0 WHERE `owner`='{$tar['id']}' ; ");
						if (mysql_affected_rows()>0)
						{
						$mess = "<font color=red>".$user['login']." снял запрет на вывод денег</font>";
						mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
						echo "<font color=red><b>Успешно снят запрет вывода денег для персонажа: \"$target\"</b></font>";
						}
						else 
						{
						echo "<font color=red><b>Произошла ошибка! У персонажа  \"$target\"  уже нет запрета!<b></font>";
						}	
					}
					else
					{
					//нет актуальных данных - просто удаляем
					mysql_query("DELETE FROM oldbk.`users_money`  WHERE `owner` = '{$tar['id']}' LIMIT 1;");
					$mess = "<font color=red>".$user['login']." снял запрет на вывод денег</font>";
					mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
					echo "<font color=red><b>Успешно снят запрет вывода денег для персонажа: \"$target\"</b></font>";
					}
						

				}
				else
				{
				
						echo "<font color=red><b> У персонажа  \"$target\"  нет запрета!<b></font>";
				}
				
		}
		else 
		{
			echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
		}
?>
