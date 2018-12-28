<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

//защита от скриптов авто нап
$deflastuse = 10;
if ($user['in_tower'] == 15) $deflastuse = 2;

$do_not_help=array(10000,9,190672,101,102,103,104,105,106,107,108,109,110,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187);

if ( (time()-$_SESSION['last_attak_use']>=$deflastuse) OR (!(isset($_SESSION['last_attak_use'])))  )
	{
	$_SESSION['last_attak_use']=time();//запоминаем время юза


include('attack_functions.php');
 if (!($gif_magic)) { $gif_magic='attack.gif'; }
 if (!($blood)) { $blood=0; }
 if (!($kulak)) { $kulak=0; }
 if (!($icom)) { $icom=''; }
 if (!($time_out)) { $time_out=0; }
 if (!($batl_type)) { $batl_type=6; }
 if (!($status_var)) { $status_var=0; }



$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '".mysql_real_escape_string($_POST['target'])."' LIMIT 1;"));

if ($jert['id'] == 190672) {
	// на пятницу всегда кровавый бой
	$blood = 1;
	$time_out=10;
}

if ($jert['id'] == 9) {
	// на пятницу всегда кровавый бой
	$blood = 1;
	$time_out=10;
}

if ($jert['id'] == 10000)
{
	$icom='<b>Бой защитников Кэпитал-сити</b>';
	$time_out=3;
	$blood = 1;
	$batl_type=6;
	$status_var=4;
}


// проверка на клонов
 if (  ( (!($jert[id])) and ( strpos($_POST['target'],"клон" ) !== FALSE))
 	or (strpos($_POST['target'],"Исчадие Хаоса" ) !== FALSE)
or ((strpos($_POST['target'],"(" ) !== FALSE) and  ((strpos($_POST['target'],")" ) !== FALSE)  	) )
 	or (strpos($_POST['target'],"Дух Мерлина" ) !== FALSE)
 	or (strpos($_POST['target'],"Исчадие Дракона" ) !== FALSE)
	or (($jert['id'] == 190672) and ($jert['bot'] == 1)) // нападение на пятницу в бот режиме
	or (($jert['id'] == 9) and ($jert['bot'] == 1)) // нападение на  тыкву
	or (($jert['id'] == 10000) and ($jert['bot'] == 1)) // нападение   бот режиме
	or ($jert['bot'] == 3) //нападение на универсальных ботов (новые боты хаоса 150-177 id)
 	or (strpos($_POST['target'],"pxива" ) !== FALSE && $user['in_tower'] == 15)
      )
    {
    //ищем в клонах с параметрами
     $jert = mysql_fetch_array(mysql_query("SELECT *  FROM `users_clons` WHERE `login` = '".mysql_real_escape_string($_POST['target'])."' and bot_online > 0 and bot in (2,3)  LIMIT 1;"));
     //у таких ботов в боле бот =2
     $jert['ldate']=time();
			     $ibot=$jert[id_user];
			     $bot_error= test_attak_bots($ibot,$user);
    			     $jert['room']=$jert['bot_room'];
    			     $jert['id_city']=$user[id_city];

	if ($jert['id_user'] == 190672)
	{
	// на пятницу всегда кровавый бой
	$blood = 1;
	$time_out=10;
	}

	if ($jert['id_user'] == 9)
	{
	// на пятницу всегда кровавый бой
	$blood = 1;
	$time_out=10;
	}


    }

$myeff = getalleff($user['id']);

include "fsystem.php";

////////////////////
if ($user[battle] > 0) {$need_dress=' AND dressed=1 '; } else{$need_dress='';}
//шансы сработки -
if ($HBOT_ATTACK==true) {$int=101; }
elseif (($CP_ATTACK==true) OR ($CP_ATTACK2==true))  {$int=101; }
else
if ($CHAOS_ATTACK==true) {$int=101; }
else
if ($test_room==true) {$int=101; $CP_ATTACK2=true;  }
else
if ($klan_abil==1)   {  $int=101;  }
else
{
	$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}' ".$need_dress."  ;"));
	if((int)$rowm['magic'] > 0)
	{
	$magic = magicinf($rowm['magic']);
	}
	else
	{
	$magic = magicinf($rowm['includemagic']);
	}

	if ($magic['chanse']>=100) // если шанс у магии 100 или более
	{
		$int = 101;
	}
	else
	if ($user['intel'] >= $rowm['nintel'] )
	{
		$int = $magic['chanse'] + ($user['intel'] - $rowm['nintel']) * 3;
		if ($int > 98){ $int = 99; }
	}
	else
	{
	$int = 0;
	}
}


$jeff = getalleff($jert['id']);

////////////////////////////////////////////////////

// если жертва в бою готовим данные
if ($jert['battle']>0)
{
	if($jert['battle_t']==1){$tt=2;}else{$tt=1;}

	//проверка за кого мы можем воевать
	$test_dont_help=mysql_fetch_array(mysql_query("SELECT id,login FROM `users` WHERE `id` in (".implode(",",$do_not_help).") and battle='{$jert['battle']}' and battle_t='{$tt}' limit 1;"));

	$test_dont_help_bot=mysql_fetch_array(mysql_query("SELECT id,login FROM `users_clons` WHERE `id_user` in (".implode(",",$do_not_help).") and battle='{$jert['battle']}' and battle_t='{$tt}' limit 1;"));


    $batt=$jert['battle'];
    if($jert[bot]==1 && $user[in_tower]==0)
    {
       $is = mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `id_user` = '".$jert[id]."' ;"));
       $batt=$is['battle'];
    }
	$check_bexit = mysql_fetch_array(mysql_query("SELECT bexit_count,bexit_team FROM `battle_vars`  WHERE `owner` = '{$user['id']}' and battle = '{$batt}' LIMIT 1;"));
	$bd = mysql_fetch_array(mysql_query ('SELECT * FROM `battle` WHERE `id` = '.$batt.' LIMIT 1;'));

	if  ($bd['damage']!='')
		{
		$batslvls=explode('|',$bd['damage']);
		}

	if ( (($bd[type]==2)AND($bd[exp]!='')) AND ($CHAOS_ATTACK!=true))
		{
			$user_align=(int)($user[align]);
			if ($user_align==1)
			{
				$user_align=6;
			}
			//decode
			$aaligns=explode(";",$bd[exp]);
			//переменные для боев склонок
			if ($jert['battle_t']==1)
			{
			$my_aligns1=$aaligns[2];
			$my_aligns2=$aaligns[3];
			$targ_aligns1=$aaligns[0];
			$targ_aligns2=$aaligns[1];
			}
			else
			{
			$my_aligns1=$aaligns[0];
			$my_aligns2=$aaligns[1];
			$targ_aligns1=$aaligns[2];
			$targ_aligns2=$aaligns[3];
			}
		}

		if(($bd['type']==100) or ($bd['type']==101))
		{

		    //проверяем, можем ли мы вмешаться в клановый бой..
			//можем если у нас есть безответная война, и тело из клана с кем война напал на нашего соклана.
			$target_clan=mysql_fetch_array(mysql_query('SELECT * from oldbk.`clans`
		    	    	where short = "'.$jert['klan'].'" LIMIT 1'));

            		$klan=mysql_fetch_array(mysql_query('SELECT * from oldbk.`clans`
		    	    	where short = "'.$user['klan'].'" LIMIT 1'));

	        //времеено берем ID основы для нападения, нужно для четкой статистики в войнах.
		    $bcp_id=$klan[id];

		    $target_clan[id]=($target_clan[base_klan]>0?$target_clan[base_klan]:$target_clan[id]);
		    $klan[id]=($klan[base_klan]>0?$klan[base_klan]:$klan[id]);



               //mystatus будет писаться в бой. (тобишь если деруться рекруты, то в бой будет писаться ID войны основ.
		    $sql='SELECT * from oldbk.`clans_war_2`
		    	WHERE (
		    			(agressor='.$klan[id].' AND defender = '.$target_clan[id].')
		    			   OR
		    			(defender='.$klan[id].' AND agressor = '.$target_clan[id].')
		    		   ) ORDER BY id DESC LIMIT 1';
		    $mystatus=mysql_fetch_array(mysql_query($sql));

		    if($mystatus[defender]==$klan[id] && $mystatus[def_active]==0 && $mystatus['date']>time())
		    {
      	        $can_fight=1;
		    }
		    else
		    {
		      	$can_fight=0;
		    }
		}

}
$grant_continue = false;
$test_naim=false;

	if (($bd['coment']=='<b>#zlevels</b>') and  ($bd['damage']!=''))
		{
		$lvls=explode('|',$bd['damage']);
			if (($lvls[0]<=$user['level']) and ($user['level']<=$lvls[1]) )
				{
				//уровень подходит
				//проверка на лицензию наемника для уровневых авто хаотов
				$naim_lic=mysql_fetch_array(mysql_query("select * from effects  where owner='{$user['id']}' and type=2000"));
					if ( ($naim_lic['id']>0) and ($naim_lic['time']>time()) )
						{
						$test_naim=true;
						}
				}
		}

//комнаты для аркана из которых он вытягивает
$rooms_jert_arkan=array (15,17,18,36,56,54,55);

	if($jert['klan']!='')
	{
		$t_clan=mysql_fetch_array(mysql_query('SELECT * from oldbk.`clans` where short = "'.$jert['klan'].'" LIMIT 1'));

		if($t_clan[base_klan]>0)
		{
			$t_clan[id]=$t_clan[base_klan];
		}
	}
	if($user[klan]!='')
	{
	        $o_klan=mysql_fetch_array(mysql_query('SELECT * from oldbk.`clans` where short = "'.$user['klan'].'" LIMIT 1'));


		if($o_klan[base_klan]>0)
		{
			$o_klan[id]=$o_klan[base_klan];
		}
	}


if (($jert['room']==23) AND ($jert['battle']==0))
	{
	//ремонтка и не в бою
	 if (test_lic_mag($jert))
	 	{
	 	$candoit=false;
		 }
		 else
		 {
		 $candoit=true;
		 }
	}
	else
	{
	$candoit=true;
	}

$low_level='';

	if (($jert['id'] == 190672) or ($jert['id_user'] == 190672) or ($jert['id'] == 9) or ($jert['id_user'] == 9) or ($jert['id'] == 10000) or ($jert['id_user'] == 10000) ) //нападение на пятницу
	{
		if ($user['weap']>0)
				{
				//test weap
				$test_user_weap=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id ='{$user['weap']}' limit 1;"));

					if ((($test_user_weap['prototype'] >=55510301 ) AND ($test_user_weap['prototype'] <=55510352) ) OR ($test_user_weap['prototype'] ==1006233 ) OR ($test_user_weap['prototype'] ==1006232 ) OR ($test_user_weap['prototype'] ==1006234 ) OR ($test_user_weap['otdel'] ==6 )  )
					{
						//елкам и оружию хаоса разрешаем

					}
					else
					if ((($test_user_weap['nlevel']+1) < $user['level'] ) and ($user['level']==14) )
					{
					$low_level='Вы не можете напасть на этого персонажа с оружием ниже своего уровня';
					}
					else
					if (($test_user_weap['nlevel']<$user['level'] ) and ($user['level']< 14) )
						{
						$low_level='Вы не можете напасть на этого персонажа с оружием ниже своего уровня';
						}
				}
				else
				{
				$low_level='Вы не можете напасть на этого персонажа без оружия';
				}
	}

if ($low_level!='')
{
	err($low_level);
}
elseif ($candoit==false)
{
	err("В ремотной мастерской на мага нельзя напасть и начать бой!");
}
elseif ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif ( (($ibot>=42) and ($ibot<=65)) and  (!((($jert['level']==6) and (($user['level']==5) or ($user['level']==6))) or
(($jert['level']==7) and (($user['level']==6) or ($user['level']==7))) or
(($jert['level']==8) and (($user['level']==7) or ($user['level']==8))) or
(($jert['level']==9) and (($user['level']==8) or ($user['level']==9))) or
(($jert['level']==10) and (($user['level']==9) or ($user['level']==10))) or
(($jert['level']==11) and (($user['level']==10) or ($user['level']==11))) or
(($jert['level']==12) and (($user['level']==11) or ($user['level']==12))) or
(($jert['level']==13) and (($user['level']==12) or ($user['level']==13))) or
(($jert['level']==14) and (($user['level']==13) or ($user['level']==14))) ) )  )
{
	err("Вы не можете напасть на этого персонажа!");
}
/*elseif ($ibot >= 150 && $ibot <= 177 && ($jert['level'] != $user['level'] && $jert['level'] != ($user['level']+1)))
{
	err("Вы не можете напасть на этого персонажа!");
}*/
elseif ( (($ibot >= 165 && $ibot <= 186) OR ($ibot >= 281 && $ibot <= 286)) && ($jert['level'] < $user['level']))
{
	err("Ваш уровень не подходит для этого!");
}
elseif ( ($CP_ATTACK2==true) and ($jert['bot']>0 ) ) {
	err('На бота нельзя напасть этим нападением!');
} elseif($jert['id_city']!=$user['id_city']) {
	err('Персонаж в другом городе!');
} elseif ($bot_error!='') {
	err($bot_error);
} elseif ($bd['type'] == 23 && !$user['uclass']) {
	echo 'Для вмешательства в классовый бой у вас должен быть установлен класс';
} elseif ($jert['level']<=5) {
	echo '<font color=red>Грех обижать маленьких!</font>';
} elseif (isset($myeff['owntravma'])) {
	echo "С Вашей травмой, нельзя напасть!";
} elseif (($user['room'] != 1) and ($KRARKAN)) {
	echo "Вы можете использовать кровавый аркан только находясь в Комнате для новичков!";
} elseif (($KRARKAN) and (!(in_array($jert['room'],$rooms_jert_arkan))) ) {
	echo "Персонаж не находится в залах склонностей!";
} elseif ( ($KRARKAN) and ($jert[battle]>0)) {
	echo "Нельзя использовать на персонажа, который уже в бою!";
} elseif (isset($myeff[830])) {
	echo "Вы находитесь под медитацией!";
} elseif (($user['lab']>0) or ($user['room']==45) or ($jert['room']==45) or ($jert['lab']>0) || ($user['room'] >= 70000 && $user['room'] <= 72001))  {
	echo "Нападения в этой локации запрещены!";
} elseif ((($user['room'] >=197)AND($user['room'] <=199)) || (($user['room'] >=91)AND($user['room'] <=97))) {
     echo "Нападения в этой локации запрещены!";
} elseif ($user['room'] == 999 || $user['room'] == 72 || $jert['room'] == 72 || in_array($jert['room'], [400, 401, 402])) {
     echo "Нападения в этой локации запрещены!";
} elseif ($jert['ldate'] < (time()-60) && $jert['in_tower'] == 0) {
	echo "Персонаж не в игре!!";
} elseif ($jert['hidden'] > 0) {
	echo "Персонаж не в игре!!";
} elseif ($jert['deal'] > 0 && $jert['battle'] == 0 && $user['in_tower'] != 15) {
	echo "Нельзя напасть на дилера!";
} elseif ($jert['klan'] =='pal' && $jert['battle'] == 0 && $user['in_tower'] != 15 && !ADMIN) {
	echo "Нельзя напасть на паладина если он не в бою!";
}
elseif ($test_dont_help['id']>0)
{
	echo "Нельзя быть на одной стороне с <b>{$test_dont_help['login']}</b>!";
}
elseif ($test_dont_help_bot['id']>0)
{
	echo "Нельзя быть на одной стороне с <b>{$test_dont_help_bot['login']}</b>!";
}
elseif($jert['id'] == $user['id']) {
	echo "Мазохист?..";
} elseif(($bd['teams']!='') and ($test_room==false) and ($test_naim==false) )
    {
     $h=explode(":||:",$bd['teams']);
      if ($h[0]==20000)
      	{
		echo "Бой изолирован...";
      	}
      	else
      	{
      		if ($bd['coment']=='<b>#zlevels</b>')
		{
			//$lvls=explode('|',$bd['damage']);	if (($lvls[0]>=$user['level']) and ($lvls[1]<=$user['level']) ) 	{ }
			echo "Вмешиваться в уровневые хаотические бои могут только персонажи соответствующего уровня с лицензией наёмника!";
		}
		else
		{
		echo "Бой закрыт от вмешательства...".$bd['teams'];
		}
	}
}
elseif(($bd['type']==140)OR($bd['type']==141)OR($bd['type']==150)OR($bd['type']==151) )
{
	echo "Это клановый бой. Воспользуйтесь клановым нападением.";
}
elseif( (($bd['type']==100)OR($bd['type']==101)) && $can_fight<1){

//===============
	echo "Это клановый бой. Воспользуйтесь клановым нападением.";
//===============

} elseif ($check_bexit['bexit_count']>0 and $check_bexit['bexit_team']==$jert[battle_t] and $jert[battle] > 0) {
	echo "Вы неможете вмешаться за эту сторону...";
} elseif ($user['zayavka'] > 0) {
	echo "Вы ожидаете поединка...";
} elseif ($jert['zayavka'] > 100000000) {
	echo "Персонаж находится в очереди на бой склонностей...";
}
elseif (isset($jeff[830])) {
	echo "Персонаж находится под медитацией...";
}
elseif (((isset($jeff[656])) && ($jert['battle']==0)) and ($jert['in_tower'] == 0))  {
	echo "Персонаж защищен от нападений свитком «Иммунитет»!";
}
 elseif (isset($jeff['owntravma']) && !$jert['battle']) {
	echo "Персонаж тяжело травмирован...";
} elseif ($user['klan'] != '' && ($o_klan[id] == $t_clan[id] && $jert['klan'] != 'radminion') && $user['in_tower'] != 15 && $user['room'] != 44) {
	echo "Чтите честь ваших сокланов.";
} elseif ($user['align'] >=1 && $user['align'] <2 && $jert['align'] >1 && $jert['align'] <2 && $user['in_tower'] != 15) {
	echo "Чтите честь братьев.";
} elseif ($user['align']  == 6 && $jert['align'] >=1 && $jert['align'] < 2 && $jert['battle'] == 0 && $user['in_tower'] != 15) {
	echo "Чтите честь братьев.";
} elseif (($user['room'] == 60) or ($user['room'] == 90)) {
	echo "Тут это не работает...";
} elseif ($user['room'] >= 49998 && $user['room'] <= 53600) {
	echo "Тут это не работает...";
} elseif (($user['room'] >=210)AND($user['room'] <= 300)) {
	echo "Тут это не работает...";
} elseif ($user['room'] != $jert['room'] && !($KRARKAN)) {
	echo "Персонаж в другой комнате!";
} elseif (($jert['room'] == 31) || $jert['room'] == 43 || $jert['room'] == 200 || $jert['room'] == 10000) {
	echo "Нападения в этой локации запрещены!";
} elseif ((($jert['klan'] == 'radminion' || ($jert['align'] > '2' && $jert['align'] < '3')) && $user['klan'] != 'radminion' && $user[align]!=5)  AND $jert['id']!=3 AND $jert['id']!=4 )
{
	 	echo "Какой ужас! Ты уверен? Не сейчас...";
//		settravmazol($_SESSION['uid']);
//		addch("<img src=i/magic/{$gif_magic}> <B>{$user['login']}</B>, попытался напасть на &quot;{$_POST['target']}&quot;, но внезапно почувствовал слабость...",$user['room'],$user['id_city']);
//		mysql_query("UPDATE `users` SET `hp`=0 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
} elseif (($jert['klan'] == 'radminion' || ($jert['align'] > '2' && $jert['align'] < '3')) && $user['klan'] != 'radminion' && $jert['id']!=4 && $jert['id']!=3 && $jert['id']!=2 && $jert['id']!=6)
{
	 	echo "Какой ужас! Ты уверен? Не сейчас...";
		settravmazol($_SESSION['uid']);
		addch("<img src=i/magic/{$gif_magic}> <B>{$user['login']}</B>, попытался напасть на &quot;{$_POST['target']}&quot;, но внезапно почувствовал слабость...",$user['room'],$user['id_city']);
		mysql_query("UPDATE `users` SET `hp`=0 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
}
elseif (($jert['id'] == 12) AND (CITY_ID!=1))  {
	 	echo "Какой ужас! Ты уверен? Не сейчас...";
		settravmazol($_SESSION['uid']);
		addch("<img src=i/magic/{$gif_magic}> <B>{$user['login']}</B>, попытался напасть на &quot;{$_POST['target']}&quot;, но внезапно почувствовал слабость...",$user['room'],$user['id_city']);
		mysql_query("UPDATE `users` SET `hp`=0 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
}
elseif ($jert['level'] < 1 || $jert['id'] == 546433) {
	echo "Новички находятся под защитой мироздателя!";
} elseif ($jert['hp'] < $jert['maxhp']*0.33  && !$jert['battle'] && $user[in_tower]!=15) {
	echo "Жертва слишком слаба!";
} elseif ($user['hp'] < $user['maxhp']*0.33 && $user[in_tower]!=15) {
	echo "Вы слишком ослаблены для нападения!";
} elseif ($bd[type] == 15) {
	echo "Нельзя вмешаться в квестовый бой!";
} elseif ($bd[type] == 20) {
	echo "Нельзя вмешаться в футбольный бой!";
} elseif ($bd[fond]>0) {
	echo "Нельзя вмешаться в бой на деньги!";
} elseif ($bd[type]== 40 || $bd[type] == 41) {
	echo "Не работает для боёв противостояния!";
} elseif ( (($bd[type]==2)AND($bd[exp]!='')) AND ($user_align==0 OR $user_align==4) ) {
	echo "Вы неможете вмешаться в этот бой, у вас не та склонность!";
} elseif (  (($bd[type]==2)AND($bd[exp]!='')) AND ($user_align!=$my_aligns1 AND $user_align!=$my_aligns2) ) {
	echo "Вы неможете вмешаться за эту сторону, у вас не та склонность!";
} elseif (  (($bd[type]==2)AND($bd[exp]!='')) AND (($user_align==$targ_aligns1 OR $user_align==$targ_aligns1)AND($user_align!=2)) ) {
	echo "Вы неможете вмешаться против своей склонности!";
} elseif ($jert['hp'] < 1  && $jert['battle']>0) {
	echo "Вы не можете напасть на погибшего!";
} elseif ($jert['battle']>1 && $check_bexit['bexit_count']>0) {
	echo '<font color=red>Вы достигли лимита выхода-входа в этот бой...</font>';
  	if($user['klan'] == 'radminion') {echo 'attackk ['.$check_bexit['bexit_count'].'/1]';}
}
/* //http://tickets.oldbk.com/issue/oldbk-2583
elseif ( ($jert['battle']==0) && (($jert['level']<$user['level']-1) OR ($jert['level']>$user['level']+1)) && ($user['in_tower']==0) )
	{
	echo '<font color=red>Можно нападать на пересонажа +/- 1 уровень от своего...</font>';
}
*/
elseif ( ($jert['battle']>0) && ($bd['damage']!='') && ($user['in_tower']==0) && ($batslvls[0]!=$user['level'] && $batslvls[1]!=$user['level'])  )
	{
	echo '<font color=red>Зайти в этот бой могут только те уровни, с которых он начат..</font>';
	//echo $batslvls[0];
	//echo $batslvls[1];
	}

elseif(!($_POST['dropability']) && $jert['battle'] && $user['in_tower'] != 15)
{
//echo "0001";
	// заход против соклана
	// только если чар в клане! нахер лишние запросы
	 //пока что по ID для присвоения хаоса.
	if($jert['battle_t']==1){$j_t=2;}else{$j_t=1;}
    $haos_bot_id='102';
    $sql="SELECT * from `users_clons` where `battle_t`='".$j_t."' and `battle`='".$jert['battle']."' and id_user =".$haos_bot_id." LIMIT 1;";
	$bot_in_f=mysql_fetch_array(mysql_query($sql));

    if($bot_in_f[id]>0 && $user[align]!=4 && $user[align]!=5 )
    {
		$grant_continue = false;
				echo "<form id='formability' action='".$_SERVER['PHP_SELF']."?edit=1&use=".$_GET['use']."' method='POST'>
				<input type='hidden' name='sd4' value='".$user[id]."'>
				<input type='hidden' name='use' value='".(int)($_GET['use'])."'>
				<input id='target' name='target' type='hidden' value='".$jert['login']."' />
				<input id='dropability' name='dropability' type='hidden' value='1'/></form>
				<script type='text/javascript'>var cat = confirm('С вac будет снят значек и склонность, если вы продолжите. Напасть?');if(cat) { document.getElementById('formability').submit(); }</script>
				";
    }
    else
    {
		if ($user['klan']!='')
		{
			$is_clan = false;
			$uklan=	mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '".$user['klan']."' LIMIT 1;"));
                if($uklan['base_klan']>0)
                {
                	$baseklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$uklan['base_klan']."' LIMIT 1;"));
                	$sql="'".$user['klan']."', '".$baseklan['short']."'";
                }
                else
                if($uklan['rekrut_klan']>0)
                {
                	$rekrutklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `id` = '".$uklan['rekrut_klan']."' LIMIT 1;"));
                	$sql="'".$user['klan']."', '".$rekrutklan['short']."'";
                }
                else
                {
                	$sql="'".$user['klan']."'";
                }

			$clans_query=mysql_fetch_array(mysql_query("SELECT * from `users` where `klan` in (".$sql.") and `battle_t`='{$jert['battle_t']}' and `battle`='{$jert['battle']}' LIMIT 1;")); //достаточно одной таблетки
			if ($clans_query['id']>0)
			{
				$is_clan = true;
			}

			if($is_clan)
			{
				$grant_continue = false;
				echo "<form id='formability' action='".$_SERVER['PHP_SELF']."' method='POST'><input type='hidden' name='sd4' value='".$user[id]."'><input type='hidden' name='use' value='".(int)($_POST['use'])."'><input id='target' name='target' type='hidden' value='".$jert['login']."' /><input id='dropability' name='dropability' type='hidden' value='1'/></form><script type='text/javascript'>var cat = confirm('С вac будет снят значек и склонность, если вы продолжите. Напасть?');if(cat) { document.getElementById('formability').submit(); }</script>";
			}
			else
			{
				$grant_continue = true;
			}
		} // конец проверки если чар клановый
		else
		{
		  $grant_continue = true;
		}

	}
}
elseif(!(rand(1,100) < $int))
{
	echo "Свиток рассыпался в ваших руках...";
	$bet=1;
}
else
{
	$grant_continue = true;


}

if ($grant_continue) {

		if(isset($_POST['dropability']) && $user['in_tower'] != 15)
		{
			$aalign='0';
			$txt="Вмешался в поединок, и потерял склонность и клан ".$user[klan].", напал на {$_POST['target']}";
			if($jert['battle_t']==1){$j_t=2;}else{$j_t=1;}
		    $haos_bot_id='102';
		    $sql="SELECT * from `users_clons` where `battle_t`='".$j_t."' and `battle`='".$jert['battle']."' and id_user = ".$haos_bot_id." LIMIT 1;";
			$bot_in_f=mysql_fetch_array(mysql_query($sql));

		    if($bot_in_f[id]>0 && $user[aligin]!=4 && $user[align]!=5)
		    {
	                $aalign='4';
	                //за Исчадье - верменный хаос
	                $tti=time()+60*60;
			mysql_query("INSERT INTO `effects` SET `type`=222,`name`='Укус Исчадия Хаоса',`time`={$tti},`owner`={$user['id']},`add_info`='{$user[align]}' ");
			addchp ('<font color=red>Внимание!</font> Вы подверглись укусу «Исчадия Хаоса» на 60 минут','{[]}{$user[login]}{[]}',$user['room'],$user['id_city']);
                	mysql_query("UPDATE users set align=".$aalign." where id='{$user['id']}'");
		    }
		    else
		    {
			Test_Arsenal_Items($user);
			mysql_query("UPDATE users set klan='', status='', align=".$aalign." where id='{$user['id']}'");
			mysql_query("INSERT INTO `lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$user['id']."','".$txt."','".time()."');");
		     }


			ref_drop($user['id']);
		}

		if ($user['sex'] == 1) {$action="напал";}	else {$action="напала";}
			if ($user['align'] > '2' && $user['align'] < '3')  {
				$angel="Ангел";
			} elseif ($user['align'] >= '1' && $user['align'] < '2') {
				$angel="Персонаж";
			}



			if($jert['id']!=$user['id'])
			{
				if($jert[bot]==1)
				{
		                $is = mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `id_user` = '".$jert[id]."' ;"));
			          if ($is[0] >0 ) // если есть подставляем не из юзерса и из клона
			           {
			               $jert=$is;
			           }
			           else
			           {
			           //если нет то выставляем
			               $jert['battle']=0;
			               mysql_query("UPDATE `users` SET `battle` = 0 WHERE `id` = '".$jert[id]."'; ");
			           }
				}


					if($jert['battle'] > 0)
					{
						//вмешиваемся
						 if  (attak_to_battle($bd,$user,$jert,$check_bexit))
						 	{
		 					$bet=1;
							$sbet = 1;
							$link_battle_id=$bd['id'];
 							//header("Location:fbattle.php");
 							js_goto_fbattle();
						 	}
						 	else
						 	{
						 	//err('Что-то не так...');
						 	}

					}
					else
					{
					// начинаем бой
					 $link_battle_id=attak_start_battle($bd,$user,$jert,$blood,$status_var,$kulak,$icom,$time_out,$batl_type,$KRARKAN);
					 if ($link_battle_id)
					 	{
					 	$bet=1;
						$sbet = 1;
							if ($test_room==true)
							{
							 //header("Location: fbattle.php");
  							js_goto_fbattle();
							}
							else
							{
						 	//header("Location:fbattle.php");
 							js_goto_fbattle();
						 	}
					 	}
					 	else
					 	{
					 	err('Что-то не так...');
					 	}
	                		}
			//2 чат
			if ($sbet==1)
			{

				if ( ($CP_ATTACK2==true) OR ($bd['coment']=="<b>Бой на Центральной площади</b>") )
				{
				$user['hidden']=0;
				$user['hiddenlog']='';
				$jert['hidden']=0;
				$jert['hiddenlog']='';
				}


			 	if (($user[hidden] >0) and ($user[hiddenlog]==''))
				 {
					if ($jert[bot]==0)  { addchp ('<font color=red>Внимание!</font> На вас '.$action.' <B><i>Невидимка</i></B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$user['room'],$user['id_city']); }

					if (($get_abil['magic_id']>0) or ($tabil['magic']>0) )
						{
							if ($tabil['magic']>0)
								{
								$magic = magicinf($tabil['magic']);
								}
								else
								{
								$magic = magicinf($get_abil['magic_id']);
								}
						if ($magic['id']==2525) { $magic['img']='attackbv.gif';}
						$rowm['name']=$magic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';
						$rowm['img']=$magic['img'];
						}
					else
					if ($incmagic['name']!='')
						{
						$rowm['name']=$incmagic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';
						$rowm['img']=$magic['img'];
						}
						else
						{
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
						}

					$fuser['login']='<i>Невидимка</i>';
					$fuser['id']=$user['hidden'];

					if ($rowm['img']=='') { $rowm['img']='attack.gif'; $mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';						 }
					if ($rowm['name']=='') { $rowm['name']='Нападение';}

					addch($mag_gif." ".link_for_user($fuser)." использовал магию &quot;".link_for_magic($rowm['img'],$rowm['name'])."&quot;, внезапно <a href=http://capitalcity.oldbk.com/logs.php?log=".$link_battle_id." target=_blank>".$action."</a> на персонажа ".link_for_user($jert).".",$jert['room'],$jert['id_city']);

				}
				else
				{
				        $fuser=load_perevopl($user); //проверка и загрузка перевопла если надо
		        		if ($fuser['sex'] == 1) {$action="напал";}	else {$action="напала";}
					if ($jert[bot]==0)  { addchp ('<font color=red>Внимание!</font> На вас '.$action.' <B>'.$fuser['login'].'</B>.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert['login'].'{[]}',$jert['room'],$jert['id_city']); }

						if (($get_abil['magic_id']>0) or ($tabil['magic']>0) )
						{
							if ($tabil['magic']>0)
								{
								$magic = magicinf($tabil['magic']);
								}
								else
								{
								$magic = magicinf($get_abil['magic_id']);
								}
						if ($magic['id']==2525) { $magic['img']='attackbv.gif';}
						$rowm['name']=$magic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';
						$rowm['img']=$magic['img'];
						}
					else
					if ($incmagic['name']!='')
						{
						$rowm['name']=$incmagic['name'];
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$magic['img'].'>';
						$rowm['img']=$magic['img'];
						}
						else
						{
						$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
						}

					if ($rowm['img']=='') { $rowm['img']='attack.gif'; $mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';						 }
					if ($rowm['name']=='') { $rowm['name']='Нападение';}

					addch($mag_gif." ".link_for_user($fuser)." использовал магию &quot;".link_for_magic($rowm['img'],$rowm['name'])."&quot;, внезапно <a href=http://capitalcity.oldbk.com/logs.php?log=".$link_battle_id." target=_blank>".$action."</a> на персонажа ".link_for_user($jert).".",$jert['room'],$jert['id_city']);


				}
			 }

		} else {
				echo '<font color=red>Мазохист?...</font>';
			}
}


}
else
{
err('Не так быстро!');
}
?>
