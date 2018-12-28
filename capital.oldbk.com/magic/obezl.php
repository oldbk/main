<?php
// magic идентификацыя
	//if (rand(1,2)==1) {

$coma[] = "Я и не вспомню как его зовут... ";


		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$magictime=time()+($_POST['timer']*60*1440);
		$tar = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		$target=$_POST['target'];
		if ($tar['id']) 
		{
		
			$tar=check_users_city_data($tar[id]);
			if($tar[id_city]==$user[id_city] || ADMIN)
			{
				$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '5' LIMIT 1;"));
				if ($effect['time']) 
				{
					echo "<font color=red><b>На персонаже \"$target\" уже есть заклятие обезличивания </b></font>";
				}
				else 
				{
					$ok=0;
					if ($user['align'] > '2' && $user['align'] < '3' && $moj[$_POST['use']]==1) {
						$ok=1;
					}
					elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && ($tar['align'] > '1' && $tar['align'] < '2') && ($user['align'] > $tar['align'])) {
						$ok=1;
					}
					elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && !($tar['align'] > '2' && $tar['align'] < '3') && !($tar['align'] > '1' && $tar['align'] < '2')) {
						$ok=1;
					}
					if ($ok == 1) {
						if (mysql_query("INSERT INTO  ".$db_city[$tar[id_city]]."`effects` (`owner`,`name`,`time`,`type`) values ('".$tar['id']."','Заклятие обезличивания','$magictime',5);")) {
							$ldtarget=$target;
							switch($_POST['timer']) {
								case "2": $magictime="два дня."; break;
								case "3": $magictime="три дня."; break;
								case "14": $magictime="две недели."; break;
								case "30": $magictime="месяц."; break;
								case "60": $magictime="два месяца."; break;
								case "365": $magictime="бессрочно."; break;
							}
							if ($user['sex'] == 1) {$action="наложил";}
							else {$action="наложила";}
							if ($user['align'] > '2' && $user['align'] < '3')  {
								$angel="Ангел";
							}
							elseif ($user['align'] > '1' && $user['align'] < '2') {
								$angel="Паладин";
							}
							$mess="$angel &quot;{$user['login']}&quot; $action заклятие обезличивания на &quot;$target&quot; сроком $magictime";
							$messch="$angel &quot;{$user['login']}&quot; $action заклятие обезличивания на &quot;$target&quot; сроком $magictime.";
	
							mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");

							mysql_query("UPDATE `users` SET `unikstatus`='' WHERE `id`='{$tar['id']}' "); // снимаем статус
							
							mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','6');");
							addch("<img src=i/magic/obezl.gif> $messch",$user['room'],$user['id_city']);
							if($tar[room]!=$user[room])
				                        {
				                        	addchp (' <img src=i/magic/obezl.gif> '.$messch,'{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
				                        }
							addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
							echo "<font color=red><b>Успешно наложено заклятие обезличивания на персонажа \"$target\"</b></font>";

							//deny_money_out($tar,"На Вас наложено заклятие обезличивания");
							
						}
						else {
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
					}
					else {
						echo "<font color=red><b>Вы не можете наложить заклятие обезличивания на этого персонажа!<b></font>";
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
