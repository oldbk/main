<?php
$coma = array (
"Теперь на кровь остальные сбегутся.",
"Это попахивает чем-то потусторонним.",
"Примочки святой водой три раза в день и все пройдет.",
"По-моему, жена у меня тоже такая :(",
"Ну, прям по расписанию, а я думал уже не укусит.",
"Это попахивает чем-то потусторонним.",
"И ничто не предвещало беды.",
"Абсолютный Хаос наступает!",
"Никогда к этому не привыкну.",
"Развелось, тут всякой нечисти...",
"Да что же это делается???",
"Второй раз будет не так больно.",
"Это ж надо такому случиться.",
"Озверели совсем - на людей кидаются...Не дай бог так оголодать....",
"Это попахивает чем-то потусторонним.");

if ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif (($user['room'] >=210)AND($user['room'] <299)) {
	echo "Тут это не работает...";
}

 else {
		if (!($_SESSION['uid'] >0)) header("Location: index.php");
		$target=$_POST['target'];
		$us = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		$effs = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$us['id']}' and (`type`=222) ;"));
		//echo
		if ($us['battle']) { echo "Персонаж находится в поединке!"; }
		elseif ($us['zayavka']) { echo "Персонаж ожидает поединка!"; }
		elseif ($effs[id]>0) { echo "Персонаж уже под воздействием укуса!"; }
		elseif ($us['id'] == $user['id']) { echo "На самого себя? Хм.... может еще и ногу себе откусишь? :)"; }
		elseif ($us['align'] == 5) { echo "Что ж ты делаешь, гад?! &quot;{$us['login']}&quot; - твой  собрат!"; }
		elseif ($user['hp'] > $user['maxhp']*0.66) { echo "Нет необходимости кусать, силы скоро восстановятся сами "; }
		elseif ($user['hp'] < $user['maxhp']*0.33) { echo "Вы слишком ослаблены для укуса."; }
		elseif ($us['hp'] < $us['maxhp']*0.33) { echo "Жертва слишком слаба."; }
		elseif ($us['level'] < 2) { echo "Нельзя укусить новичка, они защищены Мироздателем!"; }
		elseif ($us['align'] > 2 && $us['align'] < 3) { echo "Вы решили укусить Ангела? ;)"; }
		elseif ($user['room'] != $us['room']) { echo "Персонаж находится в другой комнате.)"; }
		elseif ($user['battle']) { echo "Не в бою..."; }
		elseif ($user['zayavka']) { echo "Не в заявке..."; }
		elseif ($user['room'] == 31 || $user['room'] == 43 || $user['room'] == 200) { echo "Нельзя укусить в этой комнате!"; }
		elseif ($us['level'] > $user['level']) { echo "Нельзя укусить персонажа большего левела!)"; }
		elseif ( ($CHAOS==2)AND($us[align]==4)) { echo "Ему это уже не поможет....";}
		elseif ( ($CHAOS==2)AND($us[klan]!='')) { echo "Этим укусом тут необойтись....";}
		elseif ( ($CHAOS==2)AND($us[align]!=0)) { echo "Этим укусом тут необойтись....";}		
		elseif ($us['odate'] < (time()-60)  && ($user['room']<501 || $user['room']> 560)) { echo "Персонаж находится в оффлайне"; }

		else {
			if ($user['sex'] == 1) {$action="напал"; $golod="Оголодавший"; $pil="выпил";}
			else {$action="напала"; $golod="Оголодавшая"; $pil="выпила";}
			
			if ($us['sex'] == 1) {$otvet="он дал"; $who="его";}
			else {$otvet="она дала"; $who="её";}
			
			if ((int)($us[align]==1)) {  $new_align[0]=3; $new_align[1]=2; $nn=mt_rand(0,1);  }
			 else
		            if ((int)($us[align]==2)) {  $new_align[0]=3; $new_align[1]=6; $nn=mt_rand(0,1);  }
				else
				  if ((int)($us[align]==3)) {  $new_align[0]=2; $new_align[1]=6; $nn=mt_rand(0,1);  }
				    else
					if ((int)($us[align]==6)) {  $new_align[0]=2; $new_align[1]=3; $nn=mt_rand(0,1);  }
					 else
					  { 
					   $new_align[0]=2; $new_align[1]=3; $new_align[1]=6; $nn=mt_rand(0,2); 
					    }
					 $new_align=$new_align[$nn];
			
				
				if ($us['align']!=4)
					{
						if ($CHAOS!=2)
						{
						$tti=time()+60*60;
						mysql_query("INSERT INTO `effects` SET `type`=222,`name`='Укус Абсолютного хаоса',`time`={$tti},`owner`={$us[id]},`add_info`='{$us[align]}' ");
						addchp ('<font color=red>Внимание!</font> Вы подверглись укусу «Абсолютного хаоса» на 60 минут','{[]}{$us[login]}{[]}',$us['room'],$us['id_city']);						
						}
						else
						{
						$skl[2]='нейтральную'; $skl[3]='темную'; $skl[6]='светлую';
						mysql_query("INSERT INTO `delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$us['id']}','Получил ".$skl[$new_align]." склонность от укуса ".$user['login']." ',1,'".time()."');");
						
						mysql_query("INSERT INTO `lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$us['id']."','Получил ".$skl[$new_align]." склонность от укуса ".$user['login']." ','".time()."');");
						addchp ('<font color=red>Внимание!</font> Вы подверглись укусу «Абсолютного хаоса»','{[]}{$us[login]}{[]}',$us['room'],$us['id_city']);												
						}
					
					mysql_query("UPDATE `users` SET  align='{$new_align}',  `hp` = 1 WHERE `id` = '".$us['id']."';");
					mysql_query("UPDATE `users` SET `hp` = `hp`+'".((($user['maxhp']-$user['hp'])<= $us['hp'])?($user['maxhp']-$user['hp']):$us['hp'])."' WHERE `id` = '".$user['id']."';");
				}
				else
					{
					$tti=time()+60*60; 
					mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`sila`,`lovk`,`inta`,`vinos`) values ('".$us[id]."','Потеря Сил...',".$tti.",11,'25','0','0','0');");
					mysql_query("INSERT INTO `effects` SET `type`=222,`name`='Укус Абсолютного хаоса',`time`={$tti},`owner`={$us[id]},`add_info`='{$us[align]}' ");					
					mysql_query("UPDATE `users` SET  sila=sila-25,  `hp` = 1 WHERE `id` = '".$us['id']."';");
					mysql_query("UPDATE `users` SET `hp` = `hp`+'".((($user['maxhp']-$user['hp'])<= $us['hp'])?($user['maxhp']-$user['hp']):$us['hp'])."' WHERE `id` = '".$user['id']."';");
					}

				if ( $user['hidden'] > 0 )
				{
							addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
				}
				else
				{
							addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$user['login']}&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
				}

				addchp($coma[rand(0,count($coma)-1)],"Комментатор",$us['room'],$us['id_city']);
				echo "Все прошло удачно!";
				$bet=1;
				$sbet = 1;
			
			 
			
			
			

		}

}
?>
