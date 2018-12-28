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

	$magictime=0;
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
			
		if(($tar[id_city]==$user[id_city]) || ADMIN)
		{	
			
			$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM ".$db_city[$tar[id_city]]."`effects` WHERE `owner` = '{$tar['id']}' and `type` = '2' LIMIT 1;"));
			if ($effect['time']) 
			{
				echo "<font color=red><b>На персонаже \"$target\" уже есть заклятие молчания </b></font>";
			}
			else 
			{
				$ok=1;
				if ($ok == 1) 
				{
					
					$ldtarget=$target;

					$magictime="Бессрочно."; 							
						
					
					if ($user['sex'] == 1) {$action="наложил";}
					else {$action="наложила";}
					if ($user['align'] > '2' && $user['align'] < '3')  
					{
						$angel="Ангел";
					}
					elseif ($user['align'] > '1' && $user['align'] < '2') 
					{
						$angel="Паладин";
					}
					$mess="$angel &quot;{$user['login']}&quot; $action заклятие молчания на &quot;$realnametarget&quot; сроком $magictime";
					$messch="$angel &quot;{$user['login']}&quot; $action заклятие молчания на &quot;$target&quot; сроком $magictime";	

					addch("<img src=i/magic/sleep.gif> $messch",$user['room'],$user['id_city']);
			                if($tar[room]!=$user[room])
			                {
			                	addchp (' <img src=i/magic/sleep.gif> '.$messch,'{[]}'.$tar['login'].'{[]}',$tar['room'],$tar['id_city']);
			                }

					addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
					echo "<font color=red><b>Успешно наложено заклятие молчания на персонажа \"$target\"</b></font>";
				
		
				}
				else 
				{
					echo "<font color=red><b>Вы не можете наложить заклятие молчания на этого персонажа!<b></font>";
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
