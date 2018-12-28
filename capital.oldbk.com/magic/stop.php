<?php


if (!($_SESSION['uid'] >0)) header("Location: index.php");

// первые три минуты после начала руин не накладываем путы
$canuse = false;
$stop=false;
$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}';"));
$magic = magicinf($rowm['magic']);
if(!$rowm[id])
{
	$stop='У вас нет такого предмета.';
}

if ($user['ruines'] > 0)
{
	$map = mysql_fetch_array(mysql_query("SELECT * FROM ruines_map WHERE id = ".$user['ruines']));

	$attacktime = 60*3; // задержка на путы

	if (($map['starttime'] + $attacktime) > time())
	{
		echo '<font color=red><b>Наложить путы можно только после 3х минут начала турнира.</b></font>';
	}
	else
	{
		$canuse = true;
	}
}
else
	if(($user['room']==31) || ($user['room']==60) || $user['room'] == 402)
	{
		$stop='Нельзя использовать это здесь!';
	}
	else
	{
		$canuse = true;
	}


if ($canuse && !$stop)
{

	$magictime=time()+($magic['time']*60);

	$target=$_POST['target'];
	$tar = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
	if (!$tar['id']) {
		$tar = mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `login` = '{$_POST['target']}' and id_user = 84;"));
		$tar['room'] = $tar['bot_room'];
	}

	if ($tar['id'])
	{
		if(($tar[klan]=='Adminion' || $tar[klan]=='radminion') and ($user[klan]!='radminion'))
		{
			$tar=$user;
			$target=$user[login];
		}

		if ($user['ruines'] > 0)
		{
			$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$tar['id']}' and `type` = '10' AND `time` >= ".time()." LIMIT 1;"));
		}
		else
		{
			$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$tar['id']}' and `type` = '10' LIMIT 1;"));
		}

		if ($tar['ldate'] < (time()-60) && !isset($tar['bot_room']) && $user['ruines'] == 0) {
			echo "Персонаж не в игре!";
		}
		elseif ($effect['time'])
		{
			echo "<font color=red><b>На персонаже \"$target\" уже есть путы </b></font>";
		}
		else
		{

			if ($tar['room']==$user['room'])
			{
				if (mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$tar['id']."','Путы','$magictime',10);"))
				{
					if ($tar[bot]==0)
					{
						//устанавливаем время окончания пут - в онлайн и онлан чат
						mysql_query("UPDATE users set odate='{$magictime}', ldate='{$magictime}' where id='{$tar['id']}'  LIMIT 1;");
					}

					$ldtarget=$target;

					if(($user['hidden'] > 0) and ($user['hiddenlog'] ==''))
					{
						$fuser['login']='<i>Невидимка</i>';
						$fuser['id']=$user['hidden'];
						$action="наложил";
					}
					else
					{
						$fuser=load_perevopl($user); //проверка и загрузка перевопла если надо
						if ($fuser['sex'] == 1) {$action="наложил";}  else {$action="наложила";}
					}

					addch("<img src=http://i.oldbk.com/i/sh/chains.gif>".link_for_user($fuser)." использовал магию &quot;".link_for_magic('chains.gif','Путы')."&quot;, внезапно ".$action." путы на персонажа ".link_for_user($tar).".",$user['room'],$user['id_city']);

					echo "<font color=red><b>Вы наложили путы на персонажа \"$target\"</b></font>";
					$bet=1;
					$sbet = 1;
				}
				else
				{
					echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
			}
			else
			{
				echo "<font color=red><b>Персонаж в другой комнате<b></font>";
			}
		}
	}
	else
	{
		echo "<font color=red><b>Персонаж \"$target\" не существует!<b></font>";
	}
}
else
{
	echo '<font color=red><b>'.$stop.'<b></font>';
}

?>
