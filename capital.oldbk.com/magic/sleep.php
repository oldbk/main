<?php

$coma[] = "А вот раньше просто кляпом рот затыкали.";
$coma[] = "А еще раз можешь? ;)";
$coma[] = "А раньше все не так было ";
$coma[] = "Безобразие куда цензура смотрит?";
$coma[] = "Бог сотворил землю, а паладин молчание!!!";
$coma[] = "Вечность? Это тоже единица измерения времени.";
$coma[] = "Вот и мне жена так же рот затыкает";
$coma[] = "В Клубе жесткие законы... Только не надо тосковать по беззаконью! ";
$coma[] = "Гнетущую тишину нарушает всеобщее молчание... ";
$coma[] = "Давно бы так ";
$coma[] = "Еще одним немым стало больше ";
$coma[] = "Жестоко, но справедливо ";
$coma[] = "Закон. И против него не попрешь.";
$coma[] = "Значит, есть еще порядок в этом мире ";
$coma[] = "И тишина...";
$coma[] = "Молчание - золото. Ощути себя богатым. ";
$coma[] = "Молчание не ценят, потому что оно достается на халяву... (с), но ему подарю с удовольствием!";
$coma[] = "Молчание - это своего рода инвалидность для болтунов.";
$coma[] = "Не надо злить нас!";
$coma[] = "Нет крика громче тишины... ";
$coma[] = "Ни ругнуться, ни ответить теперь.";
$coma[] = "Ну, как, дошло?";
$coma[] = "Ну, наконец-то!";
$coma[] = "О чем с этим человеком можно говорить, когда с ним и помолчать то не о чем! ";
$coma[] = "Он сказал лишнего.";
$coma[] = "Одна из ступеней развития слова - молчание.";
$coma[] = "Придется помолчать, чтобы тебя выслушали.";
$coma[] = "Прям как рыбка теперь, только рот открывается.";
$coma[] = "Семь раз подумай, один раз промолчи. ";
$coma[] = "Сначала было слово. Потом появилось молчание... ";
$coma[] = "Тебе повезло, что не навсегда. ";
$coma[] = "У вас есть право хранить молчание ";
$coma[] = "Цените слово потому, что каждое может стать последним. ";
$coma[] = "Это безмолвие становится все громче и громче… ";
$coma[] = "Это надо обдумать.";
$coma[] = "Это урок нам всем ";
$coma[] = "Я вас долго слушал, теперь у вас есть время подумать.";
$coma[] = "Я конечно не садист, но мне все это нравится ";
$coma[] = "Помолчи, за умного сойдешь. ";


if (!($_SESSION['uid'] >0)) header("Location: index.php");
$_POST['timer']=isset($_POST['timer'])?(int)$_POST['timer']:'';

if($_POST['timer']>'0' && $_POST['timer'] <= '1'){$_POST['timer']='0.5';}
if($_POST['timer']>'1' && $_POST['timer'] <= '15'){$_POST['timer']='15';}
if($_POST['timer']>'15' && $_POST['timer'] <= '30'){$_POST['timer']='30';}
if($_POST['timer']>'30' && $_POST['timer'] <= '60'){$_POST['timer']='60';}
if($_POST['timer']>'60' && $_POST['timer'] <= '180'){$_POST['timer']='180';}
if($_POST['timer']>'180' && $_POST['timer'] <= '360'){$_POST['timer']='360';}
if($_POST['timer']>'360' && $_POST['timer'] <= '720'){$_POST['timer']='720';}
if($_POST['timer']>'720' && $_POST['timer'] <= '1440'){$_POST['timer']='1440';}

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
			$effect = mysql_fetch_array(mysql_query("SELECT * FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '2' and pal = 1 LIMIT 1;"));
			if ($effect['time'] && isset($_POST['updatetime'])) {
				$updatetime = 1;
			} elseif ($effect['time']) {
				echo "<font color=red><b>На этом персонаже уже есть заклятие молчания!<b></font>";
				return;
			}

			$ok=0;
			if ((($user['align'] > '2' && $user['align'] < '3') || $user['align']==7) && $moj[$_POST['use']]==1) 
			{
				$ok=1;
			}
			elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && ($tar['align'] > '1' && $tar['align'] < '2') && ($user['align'] > $tar['align'])) 
			{
				$ok=1;
			}
			elseif (($user['align'] > '1' && $user['align'] < '2' && $moj[$_POST['use']]==1) && !($tar['align'] > '2' && $tar['align'] < '3') && !($tar['align'] > '1' && $tar['align'] < '2')) 
			{
				$ok=1;
			}


			if ($tar['id'] ==12) { $ok=0; }
			if ($tar['id'] ==190672) { $ok=0; }
				
			if ($ok == 1) 
			{
				if ($updatetime) {
					$q = mysql_query('UPDATE effects SET time = time + '.($_POST['timer']*60).' WHERE id = '.$effect['id'].' LIMIT 1');
				} else {
					$q = mysql_query("INSERT INTO ".$db_city[$tar[id_city]]."`effects`
					(`owner`,`name`,`time`,`type`,`pal`)
					values
					('".$tar['id']."','Заклятие молчания','$magictime',2,1);");
				}

				if ($q) {
					$ldtarget=$target;
					switch($_POST['timer']) 
					{
						case "0.5": $magictime="ВЕЧНОСТЬ"; break;
						case "15": $magictime="15 мин."; break;
						case "30": $magictime="30 мин."; break;
						case "60": $magictime="1 час."; break;
						case "180": $magictime="3 часа."; break;
						case "360": $magictime="6 часов."; break;
						case "720": $magictime="12 часов."; break;
						case "1440": $magictime="1 сутки."; break;
						case "2880": $magictime="2-е суток."; break;
						case "4320": $magictime="3-е суток."; break;	
						case "10080": $magictime="1 неделя."; break;								
						
					}

					if ($admindays) {
						$magictime = $_REQUEST['admindays']." дней.";
					}


					if ($user['align'] > '2' && $user['align'] < '3')  
					{
						$angel="Ангел";
					}
					elseif ($user['align'] > '1' && $user['align'] < '2') 
					{
						$angel="Паладин";
					}

					if ($updatetime == 1) {
						if ($user['sex'] == 1) {$action="увеличил";} else {$action="увеличила";}
						$mess="$angel &quot;{$user['login']}&quot; $action заклятие молчания персонажу &quot;$realnametarget&quot;, добавочный срок $magictime";
						$messch="$angel &quot;{$user['login']}&quot; $action заклятие молчания персонажу &quot;$target&quot;, добавочный срок $magictime";
	
						echo "<font color=red><b>Успешно продлено заклятие молчания на персонажа \"$target\"</b></font>";
					} else {
						if ($user['sex'] == 1) {$action="наложил";} else {$action="наложила";}
						$mess="$angel &quot;{$user['login']}&quot; $action заклятие молчания на &quot;$realnametarget&quot; сроком $magictime";
						$messch="$angel &quot;{$user['login']}&quot; $action заклятие молчания на &quot;$target&quot; сроком $magictime";
		
						echo "<font color=red><b>Успешно наложено заклятие молчания на персонажа \"$target\"</b></font>";
					}

					mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
					mysql_query("INSERT INTO oldbk.`paldelo`(`id`,`author`,`text`,`date`,`m_type`) VALUES ('','".$_SESSION['uid']."','$mess','".time()."','10');");
	
					mysql_query("UPDATE ".$db_city[$tar[id_city]]."users set slp=1 where id={$tar['id']} ;");

					addch("<img src=i/magic/sleep.gif> $messch",$user['room'],$user['id_city']);
					addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);

			                if($tar[room]!=$user[room]) {
			                	addchp (' <img src=i/magic/sleep.gif> '.$messch,'{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
			                }

				}
				else 
				{
					echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
			}
			else 
			{
				echo "<font color=red><b>Вы не можете наложить заклятие молчания на этого персонажа!<b></font>";
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
