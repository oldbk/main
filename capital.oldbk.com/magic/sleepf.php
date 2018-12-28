<?php
$coma[] = "А может того... сразу в хаос? ";
$coma[] = "А будешь еще флудить на форуме - несчастный случай приключится... или авария какая...";
$coma[] = "Неграмотные могут поставить крестик вместо подписи";
$coma[] = "Отмодерили? Расслабься и получай удовольствие";
$coma[] = "Позор флудерастам!";
$coma[] = "Согласные с приговором - могут опустить руки и отойти от стенки";
$coma[] = "Флуд есть зло!";
$coma[] = "Тебе повезло, что не навсегда";
$coma[] = "Повышаем, повышаем уровень грамотности";


if (!($_SESSION['uid'] >0)) header("Location: index.php");

$admindays = false;

if (isset($_REQUEST['admindays']) && ADMIN) {
	$_REQUEST['admindays'] = intval($_REQUEST['admindays']);
	if ($_REQUEST['admindays'] > 0) {
		$_POST['timer'] = $_REQUEST['admindays']*60*24;
		$admindays = true;
	}
}

$magictime=time()+($_POST['timer']*60);

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
				$updatetime = 0;
				$effect = mysql_fetch_array(mysql_query("SELECT * FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '3' and pal = 1 LIMIT 1;"));
				if ($effect['time'] && isset($_POST['updatetime'])) {
					$updatetime = 1;
				} elseif ($effect['time']) {
					echo "<font color=red><b>На этом персонаже уже есть заклятие молчания!<b></font>";
					return;
				}


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
					if ($updatetime) {
						$q = mysql_query('UPDATE effects SET time = time + '.($_POST['timer']*60).' WHERE id = '.$effect['id'].' LIMIT 1');
					} else {
						$q = mysql_query("INSERT INTO ".$db_city[$tar[id_city]]."`effects`
						(`owner`,`name`,`time`,`type`,`pal`)
						values
						('".$tar['id']."','Заклятие форумного молчания','$magictime',3,1);");
					}

					if ($q) {
						$ldtarget=$target;
	
						switch($_POST['timer']) {
							case "15": $magictime="15 мин."; break;
							case "30": $magictime="30 мин."; break;
							case "60": $magictime="1 час."; break;
							case "180": $magictime="3 часа."; break;
							case "360": $magictime="6 часов."; break;
							case "720": $magictime="12 часов."; break;
							case "1440": $magictime="1 сутки."; break;
							case "4320": $magictime="3 суток."; break;
							case "10080": $magictime="1 неделя."; break;
							case "525600": $magictime="бессрочно."; break;
						}
						if ($admindays) {
							$magictime = $_REQUEST['admindays']." дней.";
						}

						if ($user['align'] > '2' && $user['align'] < '3')  {
							$angel="Ангел";
						}
						elseif ($user['align'] > '1' && $user['align'] < '2') {
							$angel="Паладин";
						}

						if ($updatetime) {
							if ($user['sex'] == 1) {$action="увеличил";} else {$action="увеличила";}
							$mess="$angel &quot;{$user['login']}&quot; $action заклятие форумного молчания персонажу &quot;$realnametarget&quot;, добавочный срок $magictime";
							$messch="$angel &quot;{$user['login']}&quot; $action заклятие форумного молчания персонажу &quot;$target&quot;, добавочный срок $magictime";
							echo "<font color=red><b>Успешно продлено заклятие форумного молчания на персонажа \"$target\"</b></font>";
						} else {
							if ($user['sex'] == 1) {$action="наложил";} else {$action="наложила";}
							$mess="$angel &quot;{$user['login']}&quot; $action заклятие форумного молчания на &quot;$realnametarget&quot; сроком $magictime";
							$messch="$angel &quot;{$user['login']}&quot; $action заклятие форумного молчания на &quot;$target&quot; сроком $magictime";
							echo "<font color=red><b>Успешно наложено заклятие форумного молчания на персонажа \"$target\"</b></font>";
						}

						mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
						mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','12');");
						addch("<img src=i/magic/sleepf.gif> $messch",$user['room'],$user['id_city']);
						if($tar[room]!=$user[room])
			                        {
			                        	addchp (' <img src=i/magic/sleepf.gif> '.$messch,'{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
		                        	}
						addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
					}
					else {
						echo "<font color=red><b>Произошла ошибка!<b></font>";
					}
				}
				else {
					echo "<font color=red><b>Вы не можете наложить заклятие форумного молчания на этого персонажа!<b></font>";
				}
			}
			else
			{
				echo "<font color=red><b>Персонаж \"$target\" в другом городе!<b></font>";
			}
			
		}
		else {
			echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
		}
?>
