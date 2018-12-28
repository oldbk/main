<?php
// magic идентификацыя
	//if (rand(1,2)==1) {

		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$tar = mysql_fetch_array(mysql_query("SELECT `id`,`align`,`block` FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;")); 
		$target=$_POST['target'];
		if ($tar['id']) 
		{
			$tar=check_users_city_data($tar[id]);
			
			if($tar[id_city]==$user[id_city] || ADMIN)
			{
				if ($tar['block'] == 1) 
				{
					$ok=0;
					if ($user['align'] > '2' && $user['align'] < '3') {
						$ok=1;
					}
					elseif (($user['align'] > '1' && $user['align'] < '2') && ($tar['align'] > '1' && $tar['align'] < '2') && ($user['align'] > $tar['align']) && $moj[$_POST['use']]==1) {
						$ok=1;
					}
					elseif (($user['align'] > '1' && $user['align'] < '2') && !($tar['align'] > '2' && $tar['align'] < '3') && !($tar['align'] > '1' && $tar['align'] < '2') && $moj[$_POST['use']]==1) {
						$ok=1;
					}
					if ($ok == 1) {
						if (mysql_query("UPDATE ".$db_city[$tar[id_city]]."`users` SET `palcom` = '',`block`='0' WHERE `id` = {$tar['id']} LIMIT 1;")) {
							if ($user['sex'] == 1) {$action="снял";}
							else {$action="сняла";}
							if ($user['align'] > '2' && $user['align'] < '3')  {
								$angel="Ангел";
							}
							elseif ($user['align'] > '1' && $user['align'] < '2') {
								$angel="Паладин";
							}
							if($tar['klan']!='')
							{	
								$klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE short='".$tar['klan']."'"));
								$data=mysql_query('select * from oldbk.gellery_prot where klan_owner = '.$klan[id].';');
					                         if(mysql_num_rows($data)>0)
					                         {
						                         while($row=mysql_fetch_array($data))
						                         {
						                            	 $sql='insert into oldbk.gellery set owner='.$tar[id].',img="'.$row[img].'", exp_date='.$row[exp_date].',otdel='.$row[otdel].';';
										 mysql_query($sql);
						                         }
					                         }							
							}
							
							$mess="$angel &quot;{$user['login']}&quot; $action заклятие смерти с &quot;$target&quot;.";
							mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
							mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','15');");
							addch("<img src=i/magic/death_off.gif> $mess",$user['room'],$user['id_city']);
							echo "<font color=red><b>Успешно снято заклятие смерти с персонажа \"$target\"</b></font>";			
							@file_get_contents('http://blog.oldbk.com/api/refresh.html?game_id='.$tar['id']);
						}
						else {
							echo "<font color=red><b>Произошла ошибка!<b></font>";
						}
					}
					else {
						echo "<font color=red><b>Вы не можете снять заклятие смерти с этого персонажа!<b></font>";
					}
				}
				else {
					echo "<font color=red><b>На персонаже \"$target\" нет заклятия смерти </b></font>";
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
