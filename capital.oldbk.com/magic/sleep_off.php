<?php
// magic идентификацыя
	//if (rand(1,2)==1) {

		if (!($_SESSION['uid'] >0)) header("Location: index.php");
		$target=$_POST['target'];
//for hiddens sleep
if ( strpos($target,"Невидимка:" ) !== FALSE )
	{
	$hitarget=explode(":",$target);
	$target=$hitarget[0];
	$realtar=mysql_fetch_array(mysql_query("SELECT * FROM effects WHERE `idiluz` = '{$hitarget[1]}' LIMIT 1;"));
	$tar = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$realtar[owner]}' LIMIT 1;"));
	$realnametarget=$tar[login];
	}
	else
	{
$realnametarget=$target;
	$tar = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
	}
//

		if ($tar['id']) 
		{
			$tar=check_users_city_data($tar[id]);
				
			if($tar[id_city]==$user[id_city] || ADMIN)
			{
				if ($user['id'] ==5) { $user['align']=1.1;}
				$effect = mysql_fetch_array(mysql_query("SELECT * FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '2' ORDER BY pal DESC LIMIT 1;"));
				if ($effect['time']) {
					$ok=0;
					if ((($user['align'] > '2' && $user['align'] < '3') || $user['align']==7) && $moj[$_POST['use']]==1) {
						$ok=1;
					}
					elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && ($tar['align'] > '1' && $tar['align'] < '2') && ($user['align'] > $tar['align'])) {
						$ok=1;
					}
					elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && !($tar['align'] > '2' && $tar['align'] < '3') && !($tar['align'] > '1' && $tar['align'] < '2')) {
						$ok=1;
					}
					if ($ok == 1) {
						if (mysql_query("DELETE FROM  ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '2' and id = ".$effect['id'])) {
							if ($user['sex'] == 1) {$action="снял";}
							else {$action="сняла";}
							if ($user['align'] > '2' && $user['align'] < '3')  {
								$angel="Ангел";
							}
							elseif ($user['align'] > '1' && $user['align'] < '2') {
								$angel="Паладин";
							}
							$mess="$angel &quot;{$user['login']}&quot; $action заклятие молчания с &quot;$realnametarget&quot;.";
							$messch="$angel &quot;{$user['login']}&quot; $action заклятие молчания с &quot;$target&quot;.";
							mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
							mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','11');");

							$qtype2 = mysql_query('SELECT * FROM effects WHERE type = 2 and owner = '.$effect['owner']);
							if (mysql_num_rows($qtype2) == 0) {
								mysql_query("UPDATE ".$db_city[$tar[id_city]]."users set slp=0 where id={$tar['id']} ;");
							}
							addch("<img src=i/magic/sleep_off.gif> $messch",$user['room'],$user['id_city']);

	                        if($tar[room]!=$user[room])
	                        {
	                        	addchp (' <img src=i/magic/sleep_off.gif> '.$messch,'{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
	                        }
							echo "<font color=red><b>Успешно снято заклятие молчания с персонажа \"$target\"</b></font>";
						}
						else {
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
					}
					else {
						echo "<font color=red><b>Вы не можете снять заклятие молчания с этого персонажа!<b></font>";
					}
				}
				else {
					echo "<font color=red><b>На персонаже \"$target\" нет заклятия молчания </b></font>";
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
