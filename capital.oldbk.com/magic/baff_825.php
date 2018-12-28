<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$coma = array (
	"Покупайте чеснок!",
	"Теперь на кровь остальные сбегутся.",
	"Ню-ню, а я осиновый кол точу",
	"Примочки святой водой три раза в день и все пройдет.",
	"По-моему, жена у меня тоже такая :(",
	"А вы думали, что это просто летучие мыши?",
	"Готовьте люди колья!",
	"Ну, прям по расписанию, а я думал уже не укусит.",
	"Это попахивает чем-то потусторонним.",
	"И ничто не предвещало беды.",
	"Я всегда говорил, мой любимый - чесночный суп :)",
	"Тьма наступает!",
	"Никогда к этому не привыкну.",
	"А ведь предупреждали, садись на пенек, ешь пирожок с чесноком :)",
	"Развелось, тут всякой нечисти...",
	"Да что же это делается???",
	"Второй раз будет не так больно.",
	"Кровососы...",
	"Сегодня же полнолуние, вы, что забыли ???",
	"Интересно, а теперь он тоже станет вампиром???",
	"Чеснок - не только при простуде.",
	"Это ж надо такому случиться.",
	"Озверели совсем - на людей кидаются...Не дай бог так оголодать....",
	"Ой, а мне бабушка тоже о вампирах рассказывала"
);

$meffs = getalleff($user['id']);

if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif (($user['lab']>0) or ($user['room']==45)  or ($user['room']==60) or ($user['room']==999)  )  {
	echo "Кусать в этой локации нельзя!";
} elseif (($user['room'] >=197)AND($user['room'] <=199)) {
	echo "Кусать в этой локации нельзя!";
} elseif ($user['in_tower'] >0) {
	echo "Кусать в этой локации нельзя!";
} elseif ($user['room'] >= 50000 && $user['room'] <= 60000) {
	echo "Кусать в этой локации нельзя!";
} elseif (isset($meffs[830])) { 
	echo "Вы находитесь под медитацией"; 
} elseif (($user['room'] >=210)AND($user['room'] <299)) {
	echo "Тут это не работает...";
} else {		
	$target=$_POST['target'];

	$us = mysql_fetch_array(mysql_query("SELECT *,
	(SELECT `id` FROM oldbk.`inventory` WHERE  `bs_owner`=`users`.`in_tower` and `setsale` = 0 AND `owner` = `users`.`id` AND `prototype` = 86 LIMIT 1) AS `che`,
	(SELECT `id` FROM oldbk.`inventory` WHERE `bs_owner`=`users`.`in_tower` and `setsale` = 0 AND  `owner` = `users`.`id` AND `name` LIKE '%Осиновый кол%' LIMIT 1) AS `kol`
	 FROM `users` WHERE `login` = '{$_POST['target']}' and bot=0 LIMIT 1;"));

	$effs = getalleff($us['id']);

	$mp = new moonPhase("");
	$fullmoon = false;
	if ($mp->getPhaseName() == "Full Moon")	$fullmoon = true;

	if ($us['battle']) { echo "Персонаж находится в поединке!"; }
	elseif ($us['id'] == $user['id']) { echo "На самого себя? Хм.... может еще и ногу себе откусишь? :)"; }
	elseif ($us['zayavka'] > 100000000) {  echo "Персонаж находится в очереди на бой склонностей...";}
	elseif ($us['align'] == 3) { echo "Что ж ты делаешь, гад?! &quot;{$us['login']}&quot; - твой темный собрат!"; }
	elseif ($user['hp'] > $user['maxhp']*0.66) { echo "Нет необходимости кусать, силы скоро восстановятся сами "; }
	elseif ($user['hp'] < $user['maxhp']*0.33) { echo "Вы слишком ослаблены для укуса."; }
	elseif ($us['hp'] < $us['maxhp']*0.33) { echo "Жертва слишком слаба."; }
	elseif ($us['level'] < 4) { echo "Нельзя укусить новичка, они защищены Мироздателем!"; }
	elseif ($us['align'] > 2 && $us['align'] < 3) { echo "Вы решили укусить Ангела? ;)"; }
	elseif ($us['align'] > 1 && $us['align'] < 2 && $user['klan'] != "FallenAngels" && $fullmoon == false) { echo "Не грешите с паладинами..."; }
	elseif ($us['align'] == 6 && $fullmoon == false) { echo "Не грешите со светом..."; }
	elseif ($user['room'] != $us['room']) { echo "Персонаж находится в другой комнате.)"; }
	elseif ($user['battle']) { echo "Не в бою..."; }
	elseif ($user['zayavka']) { echo "Не в заявке..."; }
	elseif ($user['room'] == 31 || $user['room'] == 43 || $user['room'] == 200 || $user['room'] == 999 ) { echo "Нельзя укусить в этой комнате!"; }
	elseif (((int)date("H") < 21) && ((int)date("H") > 8)) { echo "Вампиры кусают только по ночам"; }
	elseif ($us['level'] > $user['level']) { echo "Нельзя укусить персонажа большего левела!)"; }
	elseif ($us['odate'] < (time()-60) && ($user['room']<501 || $user['room']> 560)) { echo "Персонаж находится в оффлайне"; }
	elseif (isset($effs[830])) { echo "Персонаж находится под медитацией"; }
	else {
	
		$ftelo=load_perevopl($user);
		$user['sex']=$ftelo[sex];
		
		if ($user['sex'] == 1) {
			$action="напал"; 
			$golod="Оголодавший"; 
			$pil="выпил";
		} else {
			$action="напала"; 
			$golod="Оголодавшая"; 
			$pil="выпила";
		}
		if ($us['sex'] == 1) {
			$otvet="он дал"; 
			$who="его";
		} else {
			$otvet="она дала"; 
			$who="её";
		}

		$chever = 30;
		if ($user['pasbaf'] == 841) {
			$chever += 10;
		}
		if ($us['pasbaf'] == 860) {
			$chever -= 10;
		}
				

		if (($us['che']==0) && ($us['kol']==0)) {
			// нет ни кола, ни чеснока
			mysql_query("UPDATE `users` SET `hp` = 1 WHERE `id` = '".$us['id']."';");
			mysql_query("UPDATE `users` SET `hp` = `hp`+'".((($user['maxhp']-$user['hp']) <= $us['hp']) ? ($user['maxhp']-$user['hp']) : $us['hp'])."' WHERE `id` = '".$user['id']."';");

			if (($user['hidden'] > 0) and ($user['hiddenlog'] =='') ) {
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
			} else {
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$ftelo['login']}&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
			}

			addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
			echo "Все прошло удачно!";
		} elseif (($us['kol'] != 0 && rand(1,100) < 30) || ($us['id'] == 83 && rand(1,100) < 20)) {
			// есть кол, но не сработал 
			mysql_query("UPDATE `users` SET `hp` = 1 WHERE `id` = '".$us['id']."';");
			mysql_query("UPDATE `users` SET `hp` = `hp`+'".((($user['maxhp']-$user['hp'])<= $us['hp'])?($user['maxhp']-$user['hp']):$us['hp'])."' WHERE `id` = '".$user['id']."';");
			if (($user['hidden'] > 0) and ($user[hiddenlog]==''))  {
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
			} else {
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$ftelo['login']}&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
			}

			addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
			echo "Все прошло удачно!";
		} elseif (($us['kol']!=0) || ($us['id'] == 83)) {
			// кол сработал
			if (isset($effs['owntravma'])) {
				// если есть средняя или тяжёлая травма, то всегда прокусываем внезависимости от кола
				mysql_query("UPDATE `users` SET `hp` = 1 WHERE `id` = '".$us['id']."';");
				mysql_query("UPDATE `users` SET `hp` = `hp`+'".((($user['maxhp']-$user['hp']) <= $us['hp']) ? ($user['maxhp']-$user['hp']) : $us['hp'])."' WHERE `id` = '".$user['id']."';");
	
				if (($user['hidden'] > 0) and ($user['hiddenlog'] =='') )  {
					addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
				} else {
					addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$ftelo['login']}&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
				}
	
				addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
				echo "Все прошло удачно!";
			} else {
				// травм нет
				echo "Полный провал!..";
				mysql_query("UPDATE `users` SET `hp` = '".(round(($user['hp']/2),0))."' WHERE `id` = '".$user['id']."';");
				mysql_query("UPDATE oldbk.`inventory` SET `duration` = `duration`+1 WHERE `id` = '".$us['kol']."' LIMIT 1;");

				if (($user['hidden'] > 0) and ($user[hiddenlog]==''))   
				{
					addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot;, но {$otvet} достойный отпор вампиру.",$user['room'],$user['id_city']);
				} else 
				{
				
				
					addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$ftelo['login']}&quot; {$action} на &quot;{$target}&quot;, но {$otvet} достойный отпор вампиру.",$user['room'],$user['id_city']);
				}

				if (($user['hidden'] > 0) and ($user['hiddenlog'] =='') )   {
					$user_nick="<B><i>Невидимка</i></B>";
				} else {
					$user_nick=nick_align_klan($user);
				}

				$jert = $us;

				// если чел в заявке, выбиваем его
				if($jert['zayavka'] > 0) {
					//грузив всю заявку один раз
					$zay = mysql_fetch_array(mysql_query("SELECT * FROM `zayavka` WHERE `id`=".$jert['zayavka'].";"));

					// делаем масив жертвы
					$jertv_team = explode(";",$zay['team1']);
					if (in_array ($jert['id'],$jertv_team)) {
						// да он тут
						$new_team = str_replace($jert['id'].";","",$zay['team1']);
						$needup=1;
						$other_team=$zay['team2'];
					} else 	{
						//значит тут
						$new_team = str_replace($jert['id'].";","",$zay['team2']);
						$needup=2;		
						$other_team=$zay['team1'];					
					}
						
					// если заявка была на бабки
					if ($zay[price]>0) {
				  		$current_money=$jert[money];	 
				  	
						if (mysql_query("UPDATE users SET money=money+".$zay[price]." WHERE id='".$jert['id']."'")) {
							//new_delo
			  		    		$rec['owner']=$jert[id];
							$rec['owner_login']=$jert[login];
							$rec['owner_balans_do']=$jert['money'];
							$jert[money]=$jert[money]+$zay[price];													
							$rec['owner_balans_posle']=$jert['money'];
							$rec['target']=0;
							$rec['target_login']='';
							$rec['type']=69;
							$rec['sum_kr']=$zay[price];
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_id']='';
							$rec['item_name']='';
							$rec['item_count']=0;
							$rec['item_type']=0;
							$rec['item_cost']=0;
							$rec['item_dur']=0;
							$rec['item_maxdur']=0;
							$rec['item_ups']=0;
							$rec['item_unic']=0;
							$rec['item_incmagic']='';
							$rec['item_incmagic_count']='';
							$rec['item_arsenal']='';
							add_to_new_delo($rec); //юзеру
							addchp ('<font color=red>Внимание!</font> Вам возвращено '.$zay[price].' кр. ставки. ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']);
							$fond_sql="  ,`fond`=`fond`-{$zay[price]} ";
						}
					} else {
						$fond_sql='';
					}
						
					if (($new_team=='') AND ($other_team=='')) {
						mysql_query("DELETE FROM `zayavka` WHERE id = {$jert['zayavka']};");
					} else 	{
						mysql_query("UPDATE  `zayavka` SET  zcount=zcount-1,  team{$needup} = '{$new_team}' , t{$needup}hist = replace (t{$needup}hist,',".BNewHist($jert)."','') ".$fond_sql."  WHERE id = {$jert['zayavka']};");
					}
				} // zay
					

				//рандом тайм - в минутах
				$sv = array(3,4,5);

				//бой  кровавый
				mysql_query("INSERT INTO `battle`
					(
					`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`blood`
					)
					VALUES
					(
						NULL,'','','".$sv[mt_rand(0,2)]."','6','1','".$user['id']."','".$jert['id']."','".time()."','".time()."','1'
					)"
				);

				$battle_id = mysql_insert_id();
				$time = time();
	
				// создаем лог
				$rr = "<b>".$user_nick."</b> и <b>".nick_align_klan($jert)."</b>";
					
				addch ("<a href=logs.php?log=".$battle_id." target=_blank>Бой</a> между <B><b>".$user_nick."</b> и <b>".nick_align_klan($jert)."</b> начался.   ",$user['room'],$user['id_city']);
				//addlog($battle_id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
			
				$time = time();
					
				//вставка данных
				mysql_query("UPDATE `users` SET `battle` = {$battle_id} , `zayavka`=0 , `battle_t`=2 WHERE `id` = {$jert['id']} ;");
				$jert[battle_t]=2;
				mysql_query("UPDATE `users` SET `battle` = {$battle_id} , `zayavka`=0 , `battle_t`=1 WHERE `id` = {$user['id']} ;");
				$user[battle_t]=1;				
				mysql_query("UPDATE battle set `status`=0,`t1hist`='".BNewHist($user)."' , `t2hist`='".BNewHist($jert)."' where id={$battle_id};");					

				addlog($battle_id,"!:S:".time().":".BNewHist($user).":".BNewHist($jert)."\n");

				addchp ('<font color=red>Внимание!</font> На вас напал вампир!<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$us['login'].'{[]}',$us['room'],$us['id_city']);
				addchp ('<font color=red>Внимание!</font> У персонажа был осиновый кол!<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$user['login'].'{[]}',$us['room'],$us['id_city']);
					
				header("Location:fbattle.php");
			}
		} elseif ($us['che']!=0 && rand(1,100) < $chever) {
			// чеснок есть, но он не сработал
			mysql_query("UPDATE `users` SET `hp` = 1 WHERE `id` = '".$us['id']."';");
			mysql_query("UPDATE `users` SET `hp` = `hp`+'".((($user['maxhp']-$user['hp'])<= $us['hp'])?($user['maxhp']-$user['hp']):$us['hp'])."' WHERE `id` = '".$user['id']."';");

			if (($user['hidden'] > 0) and ($user[hiddenlog]==''))  {
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
			} else 	{
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$ftelo['login']}&quot; {$action} на &quot;{$target}&quot; и {$pil} всю {$who} энергию.",$user['room'],$user['id_city']);
			}

			addchp($coma[rand(0,count($coma)-1)],"Комментатор",$user['room'],$user['id_city']);
			echo "Все прошло удачно!";
		} else {
			// напаролись на чеснок
			echo "Полный провал!..";
			mysql_query("UPDATE `users` SET `hp` = 1 WHERE `id` = '".$user['id']."';");
			mysql_query("UPDATE oldbk.`inventory` SET `duration` = `duration`+1 WHERE `id` = '".$us['che']."' LIMIT 1;");
			mysql_query("DELETE FROM oldbk.`inventory` WHERE `duration` >= `maxdur` and `name` ='Чеснок (защита от вампиров)' AND `id` = '".$us['che']."' LIMIT 1;");
			if (($user['hidden'] > 0) and ($user[hiddenlog]==''))  {
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;невидимка&quot; {$action} на &quot;{$target}&quot;, но {$otvet} достойный отпор вампиру.",$user['room'],$user['id_city']);
			} else 	{
				addch("<img src=i/magic/vampir.gif>{$golod} &quot;{$ftelo['login']}&quot; {$action} на &quot;{$target}&quot;, но {$otvet} достойный отпор вампиру.",$user['room'],$user['id_city']);
			}
		}
	}
}
?>
