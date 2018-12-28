#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";

if( !lockCreate("cron_voln") ) {
    exit("Script already running.");
}

include "/www/".$CITY_NAME.".oldbk.com/fsystem.php";
//include "/www/".$CITY_NAME.".oldbk.com/mobs_config.php";
include "/www/".$CITY_NAME.".oldbk.com/mobs_config_dragon.php";

$VR_GODA=date("n");

$ZIMA_array=array(12,1,2);
$VESNA_array=array(3,4,5);
$LETO_array=array(6,7,8);
$OSEN_array=array(9,10,11);

	$ZIMA = false;
	$VESNA = false;
	$OSEN = false;
	$LETO = false;

if (in_array($VR_GODA,$ZIMA_array)) {
	$ZIMA=true;
} elseif (in_array($VR_GODA,$VESNA_array)) {
	$VESNA=true;
} elseif (in_array($VR_GODA,$OSEN_array)) {
	$OSEN=true;
} else {
	$LETO=true;
}


function mk_bot($proto,$botlogin,$botonlie,$botroom,$team)
{
//
$telo=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$proto}' LIMIT 1;"));

					$telo_items=load_mass_items_by_id($telo);
					mysql_query("INSERT INTO `users_clons` SET `login`='".$botlogin."',`sex`='{$telo['sex']}',
					`level`='{$telo['level']}',`align`='{$telo['align']}',`klan`='{$telo['klan']}',`sila`='{$telo['sila']}',
					`lovk`='{$telo['lovk']}',`inta`='{$telo['inta']}',`vinos`='{$telo['vinos']}',
					`intel`='{$telo['intel']}',`mudra`='{$telo['mudra']}',`duh`='{$telo['duh']}',`bojes`='{$telo['bojes']}',`noj`='{$telo['noj']}',
					`mec`='{$telo['mec']}',`topor`='{$telo['topor']}',`dubina`='{$telo['dubina']}',`maxhp`='{$telo['maxhp']}',`hp`='{$telo['maxhp']}',
					`maxmana`='{$telo['maxmana']}',`mana`='{$telo['mana']}',`sergi`='{$telo['sergi']}',`kulon`='{$telo['kulon']}',`perchi`='{$telo['perchi']}',
					`weap`='{$telo['weap']}',`bron`='{$telo['bron']}',`r1`='{$telo['r1']}',`r2`='{$telo['r2']}',`r3`='{$telo['r3']}',`helm`='{$telo['helm']}',
					`shit`='{$telo['shit']}',`boots`='{$telo['boots']}',`nakidka`='{$telo['nakidka']}',`rubashka`='{$telo['rubashka']}',`shadow`='{$telo['shadow']}',`battle`=0,
					`id_user`='{$telo['id']}',`at_cost`='{$telo_items['allsumm']}',`kulak1`=0,`sum_minu`='{$telo_items['min_u']}',
					`sum_maxu`='{$telo_items['max_u']}',`sum_mfkrit`='{$telo_items['krit_mf']}',`sum_mfakrit`='{$telo_items['akrit_mf']}',
					`sum_mfuvorot`='{$telo_items['uvor_mf']}',`sum_mfauvorot`='{$telo_items['auvor_mf']}',`sum_bron1`='{$telo_items['bron1']}',
					`sum_bron2`='{$telo_items['bron2']}',`sum_bron3`='{$telo_items['bron3']}',`sum_bron4`='{$telo_items['bron4']}',`ups`='{$telo_items['ups']}',
					`injury_possible`=0, `battle_t`='{$team}', bot='{$botonlie}' , bot_online='{$botonlie}', bot_room='{$botroom}' ;");
					$bot = mysql_insert_id();
					//делаем масив для бота шоб не перечитывать из базы
					$bot_data=$telo;
					$bot_data[id]=$bot;
					$bot_data[login]=$botlogin;
return $bot_data;
}



function start_drevos()
{
$BOTS_conf=array(86,87);

	$get_snows_time=mysql_fetch_array(mysql_query("select * from `variables` where `var`='drevos_out_time' ;"));
	$outtime=$get_snows_time['value'];

	if (date("H")>=$outtime)
	{
	//проверяем выпускались ли они сегодня
	$get_snows=mysql_fetch_array(mysql_query("select * from `variables` where `var`='drevos_out' ;"));

	if (($get_snows[value]>0) )
	{
	echo "Боты-куча мала(Деревья):были боты \n";
	}
	else
	{

  		echo "Боты-куча мала: выпускаем... \n";
		//1. ищем вдруг боты уже на свободе - страховка
		$get_bots=mysql_fetch_array(mysql_query("select * from users_clons where id_user in (".implode(",",$BOTS_conf).") and bot_online!=-2 limit 1;"));
		if ($get_bots[id]>0)
		{
	 		echo "Боты-куча мала(Деревья): боты уже есть! \n";
		}
		else
		{
		echo "Боты-куча мала(Деревья): ботов нету... делаем \n";
		// запишем что боты уже седня выпустились
		mysql_query("UPDATE `variables` SET `value`=`value`+1 WHERE `var`='drevos_out';");

		$broom=20; // комната где будет бой
		$botnames=array();
		$botids=array();


				$get_allbot_name=mysql_query("select id, login  from users where id in (".implode(",",$BOTS_conf).")");
				while($r=mysql_fetch_array($get_allbot_name))
				{
				$botnames[]=$r['login'];
				$botids[]=$r['id'];
				}

				if ($botids[1]==0)
					{
					$botids[1]=$botids[0]; // если был только 1 ид  делаем два
					$botnames[1]=$botnames[0];
					$botnames[0].=' (первый)';
					$botnames[1].=' (второй)';
					}

			 $bot1=mk_bot($botids[0],$botnames[0],2,$broom,1);
 			 $bot2=mk_bot($botids[1],$botnames[1],2,$broom,2);

			mysql_query("INSERT INTO `battle`
						(
							`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`t1hist`,`t2hist`,`blood`,`status_flag`,`CHAOS`
						)
						VALUES
						(
							NULL,'<b>Куча-мала</b>','Куча','3','3','0','".$bot1['id']."','".$bot2['id']."','".time()."','".time()."','".BNewHist($bot1)."','".BNewHist($bot2)."','0','10','2'
						)");
			$id = mysql_insert_id();
			//ставим ботам бой
			mysql_query("UPDATE `users_clons` SET `battle` = {$id} WHERE `id` = {$bot1[id]} or `id` = {$bot2[id]} ;");
			echo "Куча-мала: создаем лог {$id}  \n";

			addlog($id,"!:S:".time().":".BNewHist($bot1).":".BNewHist($bot2)."\n");
			echo "БОЙ КУЧА МАЛА (Деревья) СОЗДАН!! УРА!";

			$TEXT='<font color=red> Вниманию жителей города! На Центральной площади сошлись в поединке Древобороды! Спешите принять участие в Великой битве и получить за победу 150% рунного <a href="http://oldbk.com/encicl/?/runes_info.html" target=_blank>коэффициента</a> и до 2000 репутации! </font>';
			addch2all($TEXT);

		   }






       }
    }
    elseif((int)(date("H"))==2)
    {
   	echo "Куча-мала (Деревья): обнуляем время! \n";
     	mysql_query("UPDATE `variables` SET `value`=0 WHERE `var`='drevos_out';");
     	$mtime=mt_rand(16,21);
     	mysql_query("UPDATE `variables` SET `value`='{$mtime}' WHERE `var`='drevos_out_time';");
    }
    else
    {
    echo "Куча-мала (Деревья): не время \n";
    }

}

function drevos_stop_hil()
{
$BOTS_conf=array(86,87);
	$get_snow_bot=mysql_fetch_array(mysql_query("select * from users_clons where id_user in (".implode(",",$BOTS_conf).") and bot_online!=-2 and hil<10000  Limit 1;"));




  if ($get_snow_bot[id]>0)
  {
  	//2. ищем бой и его стартовое время
  	 $get_snow_battle=mysql_fetch_array(mysql_query("select UNIX_TIMESTAMP(`date`) as d  from battle where id='{$get_snow_bot[battle]}' Limit 1;"));
  	 if ($get_snow_battle[d]>0)
  	 {
  	 //3, проверяем время стартабоя
  	 	if (($get_snow_battle['d']+2400)<= time() )
  	 	{
		 //echo " больше 40 мин";
		 //правим ботам хилки
		 mysql_query("UPDATE users_clons set hil=10000 where id_user in (".implode(",",$BOTS_conf).") and bot_online!=-2 ;");
  	 	}
  	 	else
  	 	{
  	 	//echo " меньше 40 мин";
  	 	}

  	 }
  }

}


function start_snowmans()
{
$BOTS_conf=array(89);

	$get_snows_time=mysql_fetch_array(mysql_query("select * from `variables` where `var`='snowmans_out_time' ;"));
	$outtime=$get_snows_time['value'];

	if (date("H")>=$outtime)
	{
	//проверяем выпускались ли они сегодня
	$get_snows=mysql_fetch_array(mysql_query("select * from `variables` where `var`='snowmans_out' ;"));

	if (($get_snows[value]>0) )
	{
	echo "Боты-куча мала:были боты \n";
	}
	else
	{

  		echo "Боты-куча мала: выпускаем... \n";
		//1. ищем вдруг боты уже на свободе - страховка
		$get_bots=mysql_fetch_array(mysql_query("select * from users_clons where id_user in (".implode(",",$BOTS_conf).")  and bot_online!=-2 limit 1;"));
		if ($get_bots[id]>0)
		{
	 		echo "Боты-куча мала: боты уже есть! \n";
		}
		else
		{
		echo "Боты-куча мала: ботов нету... делаем \n";
		// запишем что боты уже седня выпустились
		mysql_query("UPDATE `variables` SET `value`=`value`+1 WHERE `var`='snowmans_out';");

		$broom=20; // комната где будет бой
		$botnames=array();
		$botids=array();


				$get_allbot_name=mysql_query("select id, login  from users where id in (".implode(",",$BOTS_conf).")");
				while($r=mysql_fetch_array($get_allbot_name))
				{
				$botnames[]=$r['login'];
				$botids[]=$r['id'];
				}

				if ($botids[1]==0)
					{
					$botids[1]=$botids[0]; // если был только 1 ид  делаем два
					$botnames[1]=$botnames[0];
					$botnames[0].=' (первый)';
					$botnames[1].=' (второй)';
					}

			 $bot1=mk_bot($botids[0],$botnames[0],2,$broom,1);
 			 $bot2=mk_bot($botids[1],$botnames[1],2,$broom,2);

			mysql_query("INSERT INTO `battle`
						(
							`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`t1hist`,`t2hist`,`blood`,`status_flag`,`CHAOS`
						)
						VALUES
						(
							NULL,'<b>Куча-мала</b>','Куча','3','3','0','".$bot1['id']."','".$bot2['id']."','".time()."','".time()."','".BNewHist($bot1)."','".BNewHist($bot2)."','0','10','2'
						)");
			$id = mysql_insert_id();
			//ставим ботам бой
			mysql_query("UPDATE `users_clons` SET `battle` = {$id} WHERE `id` = {$bot1[id]} or `id` = {$bot2[id]} ;");
			echo "Куча-мала: создаем лог {$id}  \n";

			addlog($id,"!:S:".time().":".BNewHist($bot1).":".BNewHist($bot2)."\n");
			echo "БОЙ КУЧА МАЛА СОЗДАН!! УРА!";

			$TEXT='<font color=red> Вниманию жителей города! На Центральной площади сошлись в поединке Снеговики! Спешите принять участие в Великой битве и получить за победу 150% рунного <a href="http://oldbk.com/encicl/?/runes_info.html" target=_blank>коэффициента</a> и до 2000 репутации! </font>';
			addch2all($TEXT);

		   }






       }
    }
    elseif((int)(date("H"))==2)
    {
   	echo "Куча-мала: обнуляем время! \n";
     	mysql_query("UPDATE `variables` SET `value`=0 WHERE `var`='snowmans_out';");
     	$mtime=mt_rand(16,21);
     	mysql_query("UPDATE `variables` SET `value`='{$mtime}' WHERE `var`='snowmans_out_time';");
    }
    else
    {
    echo "Куча-мала снеговики: не время \n";
    }

}

function snowmans_stop_hil()
{
$BOTS_conf=array(89);

	 //1. ищем ботов хоть одного

	$get_snow_bot=mysql_fetch_array(mysql_query("select * from users_clons where id_user in (".implode(",",$BOTS_conf).") and bot_online!=-2 and hil<10000  Limit 1;"));




  if ($get_snow_bot[id]>0)
  {
  	//2. ищем бой и его стартовое время
  	 $get_snow_battle=mysql_fetch_array(mysql_query("select UNIX_TIMESTAMP(`date`) as d  from battle where id='{$get_snow_bot[battle]}' Limit 1;"));
  	 if ($get_snow_battle[d]>0)
  	 {
  	 //3, проверяем время стартабоя
  	 	if (($get_snow_battle['d']+2400)<= time() )
  	 	{
		 //echo " больше 40 мин";
		 //правим ботам хилки
		 mysql_query("UPDATE users_clons set hil=10000 where id_user in (".implode(",",$BOTS_conf).") and bot_online!=-2 ;");
  	 	}
  	 	else
  	 	{
  	 	//echo " меньше 40 мин";
  	 	}

  	 }
  }

}

function make_attack_by_bot($BOT,$jert) // масивы
{
//
//столбим - по новому по уму!
	mysql_query("UPDATE `users_clons` SET `battle` = 1 WHERE `id`= ".$BOT[id]." and battle=0 and bot_room=".$jert[room]." ; ");
		if (mysql_affected_rows()>0)
		{
		//бот успешно застолбился - был не в бою и в нужной комнате
			//столбим чара
			mysql_query("UPDATE `users` SET `battle` = 1 WHERE `id`= ".$jert[id]." and battle=0 and hp > 10 and zayavka=0 and room=".$BOT[bot_room]." ; ");
			if (mysql_affected_rows()>0)
			{
			//чар успешно застолбился - т.е. не в бою и находится в комнате бота
			// начинаем бой
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sv = array(10,10,10,10,10);  //делаем тайм
$blood=0; // по умолчанию бои не кровывые теперь вовсе
//2. создаем бой
				if (($BOT[id_user]>=42) and ($BOT[id_user]<=65))
				{
				$bbcom='<b>Бой с Волнами Драконов</b>';

						if ($BOT['level']>=10)
						{
						$blood=1;
						}
				}
				else
				{
				$bbcom='<b>Бой с порождением Хаоса</b>';
				}
				mysql_query("INSERT INTO `battle` (`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`,`status_flag`)
							VALUES
							(NULL,'{$bbcom}','','".$sv[rand(0,4)]."','6','0','".$BOT['id']."','".$jert['id']."','".time()."','".time()."',3,'".BNewHist($BOT)."','".BNewHist($jert)."','{$blood}','0','0')");
				$battleid = mysql_insert_id();
				//обновляем бота
				mysql_query("UPDATE `users_clons` SET `battle` = {$battleid} , `battle_t`=1  WHERE `id`= {$BOT['id']}");
				//обновление жертвы
				if($jert['hp'] > $jert['maxhp'])
					{
					mysql_query("UPDATE `users` SET `hp` = `maxhp`, `battle_t`=2, `battle`={$battleid}  WHERE `id` = {$jert['id']} ;");
					}
				 else
					 {
					   mysql_query("UPDATE `users` SET  `battle_t`=2, `battle`={$battleid}   WHERE `id` = {$jert['id']} ;");
					 }
//3. создаем лог
					//$rr = "<b>".nick_align_klan($BOT)."</b> и <b>".nick_align_klan($jert)."</b>";
//добавить чат
					$attack_txt=array('напал','набросился', 'накинулся');
					//addlog($battleid,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
					addlog($battleid,"!:S:".time().":".BNewHist($BOT).":".BNewHist($jert)."\n");
					addchp('<font color=red>Внимание!</font> <B>'.$BOT[login].'</B> '.($attack_txt[mt_rand(0,(count($attack_txt)-1))]).' на вас.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$jert[login].'{[]}',$jert[room],$jert[id_city]);

//все удачно
			return true;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
			else
			{
			//чар не застолбился т.е. убежал или дето в бою уже....делаем откат боту
			mysql_query("UPDATE `users_clons` SET `battle` = 0 WHERE `id`= ".$BOT[id]." ; ");
			// неудачно выходим
			return false;
			}

		}
		else
		{
		//незастолбился выходим
		return false;
		}



}

function get_mobs_bylvlgroup($input,$lvl)
{
//функа выгребает ботов нужного уровня из исходного масива (конфига)
$out=array();
	foreach($input as $id=>$dat)
		{
		foreach($dat as $key=>$val)
			{
				if (($key=='group_level') and ($val==$lvl) )
					{
					$out[]=$dat;
					}
			}
		}

return $out;
}


if (true)
{
//Бот Демон Велиар
	$get_d=mysql_query("select * from variables where var='demon_time'  ");
	if (mysql_num_rows($get_d) >0)
		{
		$f=mysql_fetch_array($get_d);

		  if ($f['value'] <= time())
		  			{
		  			echo "1. проверка  бота <br>";
		  				   $bots_online = mysql_fetch_array(mysql_query("select * from `users_clons`  WHERE `id_user`=10000 and  `bot_online`=2; "));
		  				   if ($bots_online['id']>0)
		  				   	{
		  				   	echo "есть бот <br>";

			  				   	// кидаем в чат месагу

									/*
		  				   			if (((date("i")%5)==0) ) //  or (true)
		  				   			{
									echo "системка в чат <br>";
		  				   			// 1 раз в 5 мин
		  				   			$mi=round(date("i")/5);

		  				   				//Список случайных фраз, которые надо кидать в чат 1 раз в 5 минут:
										$msg= array('Сегодня всю ночь думала о запуске большого адронного коллайдера...',
										'У меня сегодня прекрасное настроение. Хочу общаться.',
										'Ну, давайте, делитесь новостями.',
										'Бесконечность конечна, если невозможное возможно. И чего меня сегодня умничать тянет?..',
										'Моя невозмутимость меня просто бесит!',
										'Люди - моя слабость!',
										'Что такое гиппопотомонстросесквипедалиофобия?',
										'В моих нервных клетках нервничают самые добрые мысли!',
										'Сегодня всю ночь думала о запуске большого адронного коллайдера...',
										'У меня сегодня прекрасное настроение. Хочу общаться.',
										'Ну, давайте, делитесь новостями.',
										'Бесконечность конечна, если невозможное возможно. И чего меня сегодня умничать тянет?..',
										'Моя невозмутимость меня просто бесит!');

									 addchp($msg[$mi],$bots_online['login'],$bots_online[bot_room],$bot_city);
									 }
									*/

								if ($bots_online['battle']>0)
								{
								echo "Всего клонов вышло: {$bots_online['bot_count']} \n";
								if ($bots_online['bot_count']<13)
									{
									echo "надо пускать клонов <br>";
									// делаем основному боту хил
									//1. смотрим сколько живых

           	   	 								$bots_online_clons = mysql_fetch_array(mysql_query("select sum(hp) as hp_clons from `users_clons`  WHERE `id_user`=10000 and `battle`='{$bots_online[battle]}' and hp>0 and  `bot_online`=0; "));

           	   	 								$pack=300000; //  предл по хп

           	   	 								echo "хв: {$bots_online_clons['hp_clons']} hp \n";


           	   	 						if (($bots_online_clons['hp_clons'] <$pack ))
									   {

             	   	 								$BOT= mysql_fetch_array(mysql_query("select * from users where id=10000; "));
											$BOT[protid]=$BOT[id];
											$BOT[protlogin]=$BOT[login];
											$BOT_items=load_mass_items_by_id($BOT);

										//выпускаем по  4 бота
										for ($keyto=$bots_online['bot_count'];$keyto<=($bots_online['bot_count']+4);$keyto++)
		       					           	   	{

	       					           	   	 		//перед выпском проверяем не закончился ли бой
		           	   	 			   			$data_battle=mysql_fetch_array(mysql_query("SELECT SQL_NO_CACHE * FROM battle where id={$bots_online['battle']} ; "));
					           	   	 			if ( ($data_battle[win]==3)AND($data_battle[status]==0) AND($data_battle['t1_dead']=='' ) )
					           	   	 			{


	       					           	   	 		$BOT['login']=$BOT['protlogin'];
											$BOT['login']=$BOT['login']." (Kлон ".($keyto+1).")";

											echo " пускаю клона {$BOT['login']} <br>";

											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
											`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
											`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
											`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
											`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
											`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
											`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
											`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`='0',`bot`=2,
											`id_user`='{$BOT[protid]}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
											`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
											`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
											`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
											`injury_possible`=0, `battle_t`='{$bots_online[battle_t]}' , bot_online = 0, bot_room='{$bots_online[bot_room]}'   ;"); //онлайн =0 т.к. это  клоны


											$BOT['id'] = mysql_insert_id();//новый бот-клон
	       					           	   	 		// конкат в бой новый ид и хистори
	       					           	   	 		$time=time();
	       					           	   	 		$za=$bots_online[battle_t];

	       					           	   	 		$add_sql='';
	       					           	   	 		if  ($data_battle['status_flag']!=4)
	       					           	   	 				{
	       					           	   	 				//правим бой по статусу
			       					           	   	 		$add_sql=" status_flag=4,  type=6, blood=1, coment='<b>Бой защитников Кэпитал-сити</b>' , " ;

	       					           	   	 				}

 	      					           	   	 		mysql_query('UPDATE `battle` SET '.$add_sql.' to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$BOT['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($BOT).'\') WHERE `id` = '.$bots_online[battle].' and win=3 and t1_dead=""  ');
      					           	   	 			if (mysql_affected_rows()>0)
      					           	   	 				{
      					           	   	 				//бой идет ставим боту ид боя
      					           	   	 				mysql_query("UPDATE `users_clons` SET `battle`='{$bots_online[battle]}' WHERE `id`='{$BOT['id']}' ");

				      					           	   	 	// отправляем системку и в лог
													$BOT[battle_t]=$bots_online[battle_t];
													$ac=($BOT[sex]*100)+mt_rand(1,2);

													addlog($bots_online[battle],"!:W:".time().":".BNewHist($BOT).":".$BOT[battle_t].":".$ac."\n");


			       					           	   	 		// Добавляем счетчик
			       					           	   	 		mysql_query("UPDATE `users_clons` SET `bot_count`=`bot_count`+1 WHERE `id`='{$bots_online['id']}';");
			       					           	   		}
			       					           	   		else
			       					           	   		{
			       					           	   		// бой закончился - клон не успел войти - удаляем его
			       					           	   		mysql_query("DELETE FROM `users_clons` WHERE `id`='{$BOT['id']}'  LIMIT 1 ");
			       					           	   		break;
			       					           	   		}
	       					           	   	 		}
	       					           	   	 		else
	       					           	   	 		{
												break;
	       					           	   	 		}

	       					           	   		}
	       					           	   	    }

	       					           	   	}
	       					           	   	elseif ($bots_online['hil']!=100000)
	       					           	   		{
	       					           	   		echo "останавливаем хил <br>";
	       					           	   		mysql_query("UPDATE users_clons set hil=100000 where id_user=10000 and bot_online=2;");
	       					           	   		echo "ставим дату следующего бота<br>";

										$next_time=mktime(mt_rand(19,21),0,0,date("n"),(date("d")),date("Y")+1); //+1 год

	       					           	   		mysql_query("UPDATE `oldbk`.`variables` SET `value`='{$next_time}' WHERE `var`='demon_time';");

	       					           	   		}
	       					           	   	else
	       					           	   	{
	       					           	   		echo "уже все было...сливаемся <br>";
	       					           	   	}
	       					           	   	}
	       					           	   	else
	       					           	   	{
	       					           	   	echo "нет боя? \n";
	       					           	   	}






		  				   	}
		  				   	else
		  				   	{
		  				   	echo "нет бота  создаем";
							//ставим для этого мастер бота флаг что мы его завели в онлайн
											$BOT= mysql_fetch_array(mysql_query("select * from users where id=10000 ;"));
											//доступные боту комнаты
											$botroom=50; //50

											//системка что бот появился тут в этой комнате

											$TEXTsta="<font color=red>Внимание! Демон Велиар вырвался на свободу! Всем защитникам Кэпитал-сити срочно прибыть на Замковую площадь, городу нужна ваша помощь!</font>";
											addch2all($TEXTsta,$bot_city);


											$BOT[protid]=$BOT[id];
											$BOT_items=load_mass_items_by_id($BOT);
											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
												`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
												`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
												`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
												`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
												`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
												`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
												`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=0,`bot`=2,
												`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
												`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
												`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
												`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
												`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$botroom}'   ;");
												if (mysql_affected_rows() > 0)
													{
													$BOT['id'] = mysql_insert_id();
													echo "Бот создан {$BOT['id']} <br> ";
													}
													else
													{
													echo "error 1";
													}
		  				   	}
		  			}
		  			elseif ($f['value']-3600<= time())
		  			{
		  				/*
					     За 6 часов до появления пятницы всем шлем системку в виде:
		  				*/
		  				/*
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='friday_time_1h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<img src=http://i.oldbk.com/i/align_4.9.gif><b>Демон Велиар <a href=http://capitalcity.oldbk.com/inf.php?10000 target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> До заветной встречи остался <b>один час</b>! Все готовы к пятничному бою?";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='friday_time_1h' ;");
				           	}
				           	*/
					}
		  			elseif ($f['value']-10800<= time())
		  			{

		  				/*
					     За 3 часов до появления пятницы всем шлем системку в виде:
		  				*/
		  				/*
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='friday_time_3h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<img src=http://i.oldbk.com/i/align_4.9.gif><b>Демон Велиар <a href=http://capitalcity.oldbk.com/inf.php?10000 target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> Заскочу в гости уже через <b>три часа</b>. Ух, повеселимся!";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='friday_time_3h' ;");
				           	}
				           	*/
					}
		  			elseif ($f['value']-21600<= time())
		  			{
		  				/*
					     За 6 часов до появления пятницы всем шлем системку в виде:
		  				*/
		  				/*
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='friday_time_6h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<img src=http://i.oldbk.com/i/align_4.9.gif><b>Демон Велиар<a href=http://capitalcity.oldbk.com/inf.php?10000 target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> До нашей встречи осталось всего лишь <b>шесть часов</b>! Жду с нетерпением!";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='friday_time_6h' ;");
				           	}
				           	*/
					}
		  			else
		  			{
		  			echo "Еще не время для Демона \n";
		  			}
		}
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//if (date("w")==5) // пятница
{
//Бот Пятница
	$get_friday=mysql_query("select * from variables where var='friday_time'  ");
	if (mysql_num_rows($get_friday) >0)
		{
		$f=mysql_fetch_array($get_friday);

		  if ($f['value'] <= time())
		  			{
		  				   echo "1. проверка  пятницы <br>\n";
		  				   $bots_online = mysql_fetch_array(mysql_query("select * from `users_clons`  WHERE `id_user`=190672 and  `bot_online`=2; "));
		  				   if ($bots_online['id']>0)
		  				   	{
		  				   	echo "есть бот <br>";
		  				   	// кидаем в чат месагу
		  				   		if (((date("i")%5)==0) ) //  or (true)
		  				   			{
									echo "системка в чат <br>";
		  				   			// 1 раз в 5 мин
		  				   			$mi=round(date("i")/5);

		  				   				//Список случайных фраз, которые надо кидать в чат 1 раз в 5 минут:
										$msg= array('Сегодня всю ночь думала о запуске большого адронного коллайдера...',
										'У меня сегодня прекрасное настроение. Хочу общаться.',
										'Ну, давайте, делитесь новостями.',
										'Бесконечность конечна, если невозможное возможно. И чего меня сегодня умничать тянет?..',
										'Моя невозмутимость меня просто бесит!',
										'Люди - моя слабость!',
										'Что такое гиппопотомонстросесквипедалиофобия?',
										'В моих нервных клетках нервничают самые добрые мысли!',
										'Сегодня всю ночь думала о запуске большого адронного коллайдера...',
										'У меня сегодня прекрасное настроение. Хочу общаться.',
										'Ну, давайте, делитесь новостями.',
										'Бесконечность конечна, если невозможное возможно. И чего меня сегодня умничать тянет?..',
										'Моя невозмутимость меня просто бесит!');

									 addchp($msg[$mi],$bots_online['login'],$bots_online[bot_room],$bot_city);
									 }

								if ($bots_online['battle']>0)
								{
								echo "Всего клонов вышло: {$bots_online['bot_count']} \n";
								if ($bots_online['bot_count']<4) // всего ботов
									{
									echo "надо пускать клонов <br> \n";
									// делаем основному боту хил
									//1. смотрим сколько живых

           	   	 								$bots_online_clons = mysql_fetch_array(mysql_query("select sum(hp) as hp_clons from `users_clons`  WHERE `id_user`=190672 and `battle`='{$bots_online[battle]}' and hp>0 and  `bot_online`=0; "));

           	   	 								$pack=300000; //  предл по хп

           	   	 								echo "хв: {$bots_online_clons['hp_clons']} hp \n";


           	   	 						if (($bots_online_clons['hp_clons'] <$pack ))
									   {

             	   	 								$BOT= mysql_fetch_array(mysql_query("select * from users where id=190672; "));
											$BOT[protid]=$BOT[id];
											$BOT[protlogin]=$BOT[login];
											$BOT_items=load_mass_items_by_id($BOT);

										//выпускаем по  4 бота
										for ($keyto=$bots_online['bot_count'];$keyto<=($bots_online['bot_count']+4);$keyto++)
		       					           	   	{

	       					           	   	 		//перед выпском проверяем не закончился ли бой
		           	   	 			   			$data_battle=mysql_fetch_array(mysql_query("SELECT SQL_NO_CACHE * FROM battle where id={$bots_online['battle']} ; "));
					           	   	 			if ( ($data_battle[win]==3)AND($data_battle[status]==0) AND($data_battle['t1_dead']=='' ) )
					           	   	 			{


	       					           	   	 		$BOT['login']=$BOT['protlogin'];
											$BOT['login']=$BOT['login']." (Kлон ".($keyto+1).")";

											echo " пускаю клона {$BOT['login']} <br>";

											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
											`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
											`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
											`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
											`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
											`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
											`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
											`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`='0',`bot`=2,
											`id_user`='{$BOT[protid]}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
											`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
											`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
											`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
											`injury_possible`=0, `battle_t`='{$bots_online[battle_t]}' , bot_online = 0, bot_room='{$bots_online[bot_room]}'   ;"); //онлайн =0 т.к. это  клоны


											$BOT['id'] = mysql_insert_id();//новый бот-клон
	       					           	   	 		// конкат в бой новый ид и хистори
	       					           	   	 		$time=time();
	       					           	   	 		$za=$bots_online[battle_t];
 	      					           	   	 		mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$BOT['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($BOT).'\') WHERE `id` = '.$bots_online[battle].' and win=3 and t1_dead=""  ');
      					           	   	 			if (mysql_affected_rows()>0)
      					           	   	 				{
      					           	   	 				//бой идет ставим боту ид боя
      					           	   	 				mysql_query("UPDATE `users_clons` SET `battle`='{$bots_online[battle]}' WHERE `id`='{$BOT['id']}' ");

				      					           	   	 	// отправляем системку и в лог
													$BOT[battle_t]=$bots_online[battle_t];
													$ac=($BOT[sex]*100)+mt_rand(1,2);

													addlog($bots_online[battle],"!:W:".time().":".BNewHist($BOT).":".$BOT[battle_t].":".$ac."\n");


			       					           	   	 		// Добавляем счетчик
			       					           	   	 		mysql_query("UPDATE `users_clons` SET `bot_count`=`bot_count`+1 WHERE `id`='{$bots_online['id']}';");
			       					           	   		}
			       					           	   		else
			       					           	   		{
			       					           	   		// бой закончился - клон не успел войти - удаляем его
			       					           	   		mysql_query("DELETE FROM `users_clons` WHERE `id`='{$BOT['id']}'  LIMIT 1 ");
			       					           	   		break;
			       					           	   		}
	       					           	   	 		}
	       					           	   	 		else
	       					           	   	 		{
												break;
	       					           	   	 		}

	       					           	   		}
	       					           	   	    }

	       					           	   	}
	       					           	   	elseif ($bots_online['hil']!=100000)
	       					           	   		{

	       					           	   			$data_battle=mysql_fetch_array(mysql_query("SELECT SQL_NO_CACHE * FROM battle where id={$bots_online['battle']} ; "));
	       					           	   			if ( ($data_battle[win]==3)AND($data_battle[status]==0) AND($data_battle['t1_dead']=='' ) )
	       					           	   				{
	       					           	   				//бой идет
	       					           	   					$std=date_create($data_battle['date']);
	       					           	   					if (time()-$std->getTimestamp() >=5400) //бой идет больше чем 1,5 ч
	       					           	   					{
				       					           	   		echo "останавливаем хил <br> \n";
				       					           	   		mysql_query("UPDATE users_clons set hil=100000 where id_user=190672 and bot_online=2;");
				       					           	   		}
				       					           	   		else
				       					           	   		{
													echo "бой идет ".(time()-$std->getTimestamp())."сек. не время останавливать хил <br>\n";
				       					           	   		}
			       					           	   		}
	       					           	   		}
	       					           	   	else
	       					           	   	{
	       					           	   		echo "уже все было...сливаемся <br>\n";
	       					           	   	}
	       					           	   	}
	       					           	   	else
	       					           	   	{
	       					           	   	echo "нет боя? \n";
	       					           	   	}






		  				   	}
		  				   	else
		  				   	{
		  				   	//нет бота
		  				   	$get_out=mysql_fetch_array(mysql_query("select * from variables where var='friday_out'"));
		  				   	if ($get_out['value']>0)
		  				   		{
		  				   		echo "бот уже был \n";

	       					           	   		echo "ставим дату следующей пятницы <br> \n";
										$next_time=mktime(mt_rand(19,21),0,0,date("n"),(date("d")+7),date("Y"));
	       					           	   		mysql_query("UPDATE `oldbk`.`variables` SET `value`='{$next_time}' WHERE `var`='friday_time';");
	       					           	   		mysql_query("UPDATE `variables` SET `value`=0  where `var`='friday_time_1h' or `var`='friday_time_3h' or `var`='friday_time_6h' or var='friday_out' ");

		  				   		}
		  				   		else
		  				   		{

						  				   	echo "нет бота  создаем";
											//ставим для этого мастер бота флаг что мы его завели в онлайн
											$BOT= mysql_fetch_array(mysql_query("select * from users where id=190672 ;"));
											//доступные боту комнаты
											$botroom=21;

											//системка что бот появился тут в этой комнате
											 $TEXTsta="<font color=red>Внимание! Пятницо приветствует вас и приглашает присоединится к пятничной битве на Страшилкиной улице!</font>";
											addch2all($TEXTsta,$bot_city);

											$BOT[protid]=$BOT[id];
											$BOT_items=load_mass_items_by_id($BOT);
											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
												`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
												`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
												`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
												`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
												`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
												`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
												`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=0,`bot`=2,
												`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
												`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
												`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
												`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
												`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$botroom}'   ;");
												if (mysql_affected_rows() > 0)
													{
													$BOT['id'] = mysql_insert_id();
													echo "Бот создан {$BOT['id']} <br> ";
													mysql_query("UPDATE `oldbk`.`variables` SET `value`='1' WHERE `var`='friday_out'");
													}
													else
													{
													echo "error 1";
													}
								}
		  				   	}
		  			}
		  			elseif ($f['value']-3600<= time())
		  			{
		  				/*
					     За 6 часов до появления пятницы всем шлем системку в виде:
		  				*/
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='friday_time_1h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<img src=http://i.oldbk.com/i/align_4.9.gif><b>Пятницо<a href=http://capitalcity.oldbk.com/inf.php?190672 target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> До заветной встречи остался <b>один час</b>! Все готовы к пятничному бою?";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='friday_time_1h' ;");
				           	}
					}
		  			elseif ($f['value']-10800<= time())
		  			{
		  				/*
					     За 6 часов до появления пятницы всем шлем системку в виде:
		  				*/
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='friday_time_3h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<img src=http://i.oldbk.com/i/align_4.9.gif><b>Пятницо<a href=http://capitalcity.oldbk.com/inf.php?190672 target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> Заскочу в гости уже через <b>три часа</b>. Ух, повеселимся!";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='friday_time_3h' ;");
				           	}
					}
		  			elseif ($f['value']-21600<= time())
		  			{
		  				/*
					     За 6 часов до появления пятницы всем шлем системку в виде:
		  				*/
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='friday_time_6h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<img src=http://i.oldbk.com/i/align_4.9.gif><b>Пятницо<a href=http://capitalcity.oldbk.com/inf.php?190672 target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> До нашей встречи осталось всего лишь <b>шесть часов</b>! Жду с нетерпением!";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='friday_time_6h' ;");
				           	}
					}
		  			else
		  			{
		  			echo "Еще не время пятницы \n ";
		  			}
		}
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (true) // ТЫКВА
{
//Бот
$PROTO_BOT=9;
$botroom=20; //ЦП

	$get_tbot=mysql_query("select * from variables where var='tykvabot_time'  ");
	if (mysql_num_rows($get_tbot) >0)
		{
		$f=mysql_fetch_array($get_tbot);

		  if ($f['value'] <= time())
		  			{
		  			echo "1. проверка  тыквы <br>";
		  				   $bots_online = mysql_fetch_array(mysql_query("select * from `users_clons`  WHERE `id_user`='{$PROTO_BOT}' and  `bot_online`=2; "));
		  				   if ($bots_online['id']>0)
		  				   	{
		  				   	echo "есть бот <br>";

		  				   		// кидаем в чат месагу
		  				   		/*
		  				   		if (((date("i")%5)==0) ) //  or (true)
		  				   			{
									echo "системка в чат <br>";
		  				   			// 1 раз в 5 мин
		  				   			$mi=round(date("i")/5);

		  				   				//Список случайных фраз, которые надо кидать в чат 1 раз в 5 минут:
										$msg= array('Сегодня всю ночь думала о запуске большого адронного коллайдера...',
										'У меня сегодня прекрасное настроение. Хочу общаться.',
										'Ну, давайте, делитесь новостями.',
										'Бесконечность конечна, если невозможное возможно. И чего меня сегодня умничать тянет?..',
										'Моя невозмутимость меня просто бесит!',
										'Люди - моя слабость!',
										'Что такое гиппопотомонстросесквипедалиофобия?',
										'В моих нервных клетках нервничают самые добрые мысли!',
										'Сегодня всю ночь думала о запуске большого адронного коллайдера...',
										'У меня сегодня прекрасное настроение. Хочу общаться.',
										'Ну, давайте, делитесь новостями.',
										'Бесконечность конечна, если невозможное возможно. И чего меня сегодня умничать тянет?..',
										'Моя невозмутимость меня просто бесит!');

									 addchp($msg[$mi],$bots_online['login'],$bots_online[bot_room],$bot_city);
									 }
								*/

								if ($bots_online['battle']>0)
								{
								echo "Всего клонов вышло: {$bots_online['bot_count']} \n";
								if ($bots_online['bot_count']<4) // всего ботов
									{
									echo "надо пускать клонов <br>";
									// делаем основному боту хил
									//1. смотрим сколько живых

           	   	 								$bots_online_clons = mysql_fetch_array(mysql_query("select sum(hp) as hp_clons from `users_clons`  WHERE `id_user`='{$PROTO_BOT}' and `battle`='{$bots_online[battle]}' and hp>0 and  `bot_online`=0; "));

           	   	 								$pack=300000; //  предл по хп

           	   	 								echo "хв: {$bots_online_clons['hp_clons']} hp \n";


           	   	 						if (($bots_online_clons['hp_clons'] <$pack ))
									   {

             	   	 								$BOT= mysql_fetch_array(mysql_query("select * from users where id='{$PROTO_BOT}'; "));
											$BOT[protid]=$BOT[id];
											$BOT[protlogin]=$BOT[login];
											$BOT_items=load_mass_items_by_id($BOT);

										//выпускаем по  4 бота
										for ($keyto=$bots_online['bot_count'];$keyto<=($bots_online['bot_count']+4);$keyto++)
		       					           	   	{

	       					           	   	 		//перед выпском проверяем не закончился ли бой
		           	   	 			   			$data_battle=mysql_fetch_array(mysql_query("SELECT SQL_NO_CACHE * FROM battle where id={$bots_online['battle']} ; "));
					           	   	 			if ( ($data_battle[win]==3)AND($data_battle[status]==0) AND($data_battle['t1_dead']=='' ) )
					           	   	 			{


	       					           	   	 		$BOT['login']=$BOT['protlogin'];
											$BOT['login']=$BOT['login']." (Kлон ".($keyto+1).")";

											echo " пускаю клона {$BOT['login']} <br>";

											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
											`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
											`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
											`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
											`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
											`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
											`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
											`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`='0',`bot`=2,
											`id_user`='{$BOT[protid]}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
											`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
											`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
											`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
											`injury_possible`=0, `battle_t`='{$bots_online[battle_t]}' , bot_online = 0, bot_room='{$bots_online[bot_room]}'   ;"); //онлайн =0 т.к. это  клоны


											$BOT['id'] = mysql_insert_id();//новый бот-клон
	       					           	   	 		// конкат в бой новый ид и хистори
	       					           	   	 		$time=time();
	       					           	   	 		$za=$bots_online[battle_t];
 	      					           	   	 		mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$BOT['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($BOT).'\') WHERE `id` = '.$bots_online[battle].' and win=3 and t1_dead=""  ');
      					           	   	 			if (mysql_affected_rows()>0)
      					           	   	 				{
      					           	   	 				//бой идет ставим боту ид боя
      					           	   	 				mysql_query("UPDATE `users_clons` SET `battle`='{$bots_online[battle]}' WHERE `id`='{$BOT['id']}' ");

				      					           	   	 	// отправляем системку и в лог
													$BOT[battle_t]=$bots_online[battle_t];
													$ac=($BOT[sex]*100)+mt_rand(1,2);

													addlog($bots_online[battle],"!:W:".time().":".BNewHist($BOT).":".$BOT[battle_t].":".$ac."\n");


			       					           	   	 		// Добавляем счетчик
			       					           	   	 		mysql_query("UPDATE `users_clons` SET `bot_count`=`bot_count`+1 WHERE `id`='{$bots_online['id']}';");
			       					           	   		}
			       					           	   		else
			       					           	   		{
			       					           	   		// бой закончился - клон не успел войти - удаляем его
			       					           	   		mysql_query("DELETE FROM `users_clons` WHERE `id`='{$BOT['id']}'  LIMIT 1 ");
			       					           	   		break;
			       					           	   		}
	       					           	   	 		}
	       					           	   	 		else
	       					           	   	 		{
												break;
	       					           	   	 		}

	       					           	   		}
	       					           	   	    }

	       					           	   	}
	       					           	   	elseif ($bots_online['hil']!=100000)
	       					           	   		{
	       					           	   		echo "останавливаем хил <br>";
	       					           	   		mysql_query("UPDATE users_clons set hil=100000 where id_user='{$PROTO_BOT}' and bot_online=2;");

	       					           	   		echo "ставим дату следующего<br>";

										$next_time=mktime(12,0,0,date("n"),date("d"),(date("Y")+1) );

	       					           	   		mysql_query("UPDATE `oldbk`.`variables` SET `value`='{$next_time}' WHERE `var`='tykvabot_time';");


	       					           	   		mysql_query("UPDATE `variables` SET `value`=0  where `var`='tykvabot_time_1h' or `var`='tykvabot_time_3h' or `var`='tykvabot_time_6h' ");


	       					           	   		}
	       					           	   	else
	       					           	   	{
	       					           	   		echo "уже все было...сливаемся <br>";
	       					           	   	}
	       					           	   	}
	       					           	   	else
	       					           	   	{
	       					           	   	echo "нет боя? \n";
	       					           	   	}






		  				   	}
		  				   	else
		  				   	{
		  				   	echo "нет бота  создаем";
							//ставим для этого мастер бота флаг что мы его завели в онлайн
											$BOT= mysql_fetch_array(mysql_query("select * from users where id='{$PROTO_BOT}' ;"));
											//доступные боту комнаты

											//системка что бот появился тут в этой комнате


											$TEXTsta="<font color=red>Внимание! Тыква приветствует вас и приглашает присоединится к праздничной битве на Центральной площади!</font>";
											addch2all($TEXTsta,$bot_city);

											$BOT[protid]=$BOT[id];
											$BOT_items=load_mass_items_by_id($BOT);
											mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
												`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
												`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
												`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
												`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
												`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
												`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
												`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=0,`bot`=2,
												`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
												`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
												`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
												`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
												`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$botroom}'   ;");
												if (mysql_affected_rows() > 0)
													{
													$BOT['id'] = mysql_insert_id();
													echo "Бот создан {$BOT['id']} <br> ";
													}
													else
													{
													echo "error 1";
													}
		  				   	}
		  			}

		  			elseif ($f['value']-3600<= time())
		  			{
		  				// За 1 часов до появления пятницы всем шлем системку в виде:
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='tykvabot_time_1h'  "));
		  				if ($get_6h['value']==0)
		  				{
						 $TEXTsta="<b>Тыква<a href=http://capitalcity.oldbk.com/inf.php?{$PROTO_BOT} target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> До заветной встречи остался <b>один час</b>! Все готовы к праздничному бою?";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='tykvabot_time_1h' ;");
				           	}
					}
		  			elseif ($f['value']-10800<= time())
		  			{
						//   За 2 часов до появления пятницы всем шлем системку в виде:
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='tykvabot_time_3h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<b>Тыква<a href=http://capitalcity.oldbk.com/inf.php?{$PROTO_BOT} target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> Заскочу в гости уже через <b>три часа</b>. Ух, повеселимся!";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='tykvabot_time_3h' ;");
				           	}
					}
		  			elseif ($f['value']-21600<= time())
		  			{
						//    За 6 часов до появления пятницы всем шлем системку в виде:
	  					$get_6h=mysql_fetch_array(mysql_query("select * from variables where var='tykvabot_time_6h'  "));
		  				if ($get_6h['value']==0)
		  				{
						$TEXTsta="<b>Тыква<a href=http://capitalcity.oldbk.com/inf.php?{$PROTO_BOT} target=_blank><img src=http://i.oldbk.com/i/inf.gif></a>:</b> До нашей встречи осталось всего лишь <b>шесть часов</b>! Жду с нетерпением!";
						addch2all($TEXTsta,$bot_city);
				           	mysql_query("UPDATE `variables` SET `value`=1  where `var`='tykvabot_time_6h' ;");
				           	}
					}
		  			else
		  			{
		  			echo "Еще не время тыквы";
		  			}
		}
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Драконы - с независимыми уровнями по времени выхода
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (true)
{
$all_lvl=0;
$not_time=0;
//1. запрашиваем время страта всех волн
$sqlget="select * from variables where var like 'bots_start_time_level_%' ";
$q_get=mysql_query($sqlget);
if (mysql_num_rows($q_get) >0)
 {
 // есть данные работаем
  echo "есть данные...работаем<br>";

  	$bots_lvls=array();
	while($r=mysql_fetch_array($q_get))
			{
			$lvls=(int)str_replace('bots_start_time_level_','',$r['var']);
			$bots_lvls[$lvls]=$r['value'];
			$all_lvl++;
			}
ksort($bots_lvls);
print_r($bots_lvls);

	foreach($bots_lvls as $bot_lvl=>$bot_time)
		{
echo "Боты: ".$bot_lvl."  \n ";
				   if ($bot_time <= time())
				   	{
				   	echo "<b>время выпуска  для $bot_lvl </b><br> \n ";

									//системка о старте для уровня
				  					$getmsg=mysql_fetch_array(mysql_query("select * from variables where var='message".$bot_lvl."_time_start'  "));
					  				if ($getmsg['value']==0)
					  				{
									$TEXT="Внимание! Тревога! Всем жителям города! Монстры атакуют Центральную Площадь! Все на защиту города от нашествия!";
									echo $TEXT;
									echo "\n";
									addch2levels($TEXT,$bot_lvl,$bot_lvl,0);
							           	mysql_query("UPDATE `variables` SET `value`=1  where var='message".$bot_lvl."_time_start'  ;");
							           	}



				   	$config_mobs=get_mobs_bylvlgroup($v_mobs,$bot_lvl); // получаем из конфига нужных ботов
				   	$dead_bot=0;
			   		$all_bot=0;

						//Главный цикл по ботам
							foreach($config_mobs as $keymob=>$valmob)
							{
							if ($config_mobs[$keymob]['master_bot'] > 0)
						                       {
							$all_bot++;
										echo "запросим статус мастер бота из базы клонов <br>\n";
											// может он уже в онлайне
											// у мастерботов bot_online = 2 у остальных больше 2-х
											$bbot=mysql_query("select * from users_clons where id_user=".$config_mobs[$keymob]['master_bot']." and bot_online = 2;");
											        if (mysql_num_rows($bbot) >0)
												{
												$bbot=mysql_fetch_array($bbot);
												echo " есть запись значит мастер бот онлайн  <br>\n";

												if (mt_rand(1,200)<=5)
												 {
												 //системка просто разговора мастер бот в онлайне
												 $fr=mt_rand(0,count($bot_mess)-1);
												 addchp($bot_mess[$fr],$bbot[login],$bbot[bot_room],$bot_city);
												 }

												//проверяем в бою или нет первый раз
												 if (!($bbot[battle] > 0))
												 {
												 $nextgo=false;

							       					       echo "Бот: ".$bbot[login]." НЕ в бою! <br>\n";
							       					        ///
							       					        // начало кода нападения
							       					        // ищем на кого напасть
							       					        /* http://tickets.oldbk.com/issue/oldbk-1507 - отключить авто нападение*/
													$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=14"));
													//Неделя осады драконов
													if ($get_ivent['stat']==1)
													{
							       					        echo "Неделя осады драконов!!! ищу жертву! <br>\n";
							       					        $kandid = "select * FROM  `users`  WHERE `odate` >= ".(time()-60)." AND
							       					        					 `room` = ".$bbot['bot_room']." AND bot=0  AND
							       					        					  hp > (maxhp*0.33) AND battle=0 AND zayavka=0  AND klan!='radminion' AND klan!='Adminion' AND
							       					        					  `level` >= ".$config_mobs[$keymob]['min_level']." AND
							       					        					  `level` <= ".$config_mobs[$keymob]['max_level']." AND
							       					        					  id not in (SELECT `owner` FROM `effects` WHERE (`type` = 11 OR `type` = 12 OR `type` = 13 OR `type` = 14 OR `type` = 830 ) ) ORDER by level DESC;";

													$kand=mysql_query($kandid);
													if (mysql_num_rows($kand)>0)
							       					        		{
							       					        		$telo=mysql_fetch_array($kand);
							       					        		echo "есть кандидат...пытаюсь напасть на ".$telo[login]."  <br>\n";

							       					        		if (make_attack_by_bot($bbot,$telo))
						     					        		     	     {
						     					        		     	     //системка бот напал хаха
						     					        		     	     echo "Получилось напасть <br>";
						     					        		     	     //перечитываем данные
						     					        		     	     $bbot=mysql_query("select * from users_clons where id_user=".$config_mobs[$keymob]['master_bot']." and bot_online = 2;");
						     					        		     	     $bbot=mysql_fetch_array($bbot);

															     $fr=mt_rand(0,count($bot_mess)-1);
										 					     addchp($bot_mess[$fr],$bbot[login],$bbot[bot_room],$bot_city);
						     					        		     	     }
						     					        		     	     else
						     					        		     	     {
						     					        		     	     echo "Напасть не вышло...cидим тут ждем минуту  <br>\n";
						     					        		     	     }

							       					        		}
							       					        		else
							       					        		{
							       					        		$nextgo=true;
							       					        		}
							       					        }
					       					        		else
						       					        		{
						       					        		$nextgo=true;
						       					        		}



							       					        	if ($nextgo==true)
							       					        		{
							       					        		echo " перемещаюсь в другую комнату  <br>\n";
							       					        		// системка о переходе в другую комнату
															//доступные боту комнаты
															$br=$config_mobs[$keymob]['room'];
															$rnd_room=mt_rand(0,count($br)-1);
															$botroom=$br[$rnd_room];
															echo " комната где бот появится $botroom  <br>\n";
							       					        		mysql_query("UPDATE `users_clons` SET `bot_room`='{$botroom}' WHERE `id`='{$bbot['id']}';");
							       					        		}
							       					   }

												//проверяем в бою или нет? 2-й раз
												 if ($bbot[battle] > 0)
							       					        {
							       					        // да в бою
							       					        echo "<i>Бот:</i> ".$bbot[login]." в бою!  <br>\n";
							       					          // проверяем выпустил ли он свою команду ботов
							       					           $bots_team=$config_mobs[$keymob]['bots'];
							       					           if ($bbot[bot_count] >=(count($bots_team)-1))
							       					           	{
							       					           	 echo "Боты группы уже выпущены  <br>\n";
							       					           	}
							       					           	else
							       					           	{
							       					           	echo "Ботов нет или не все - выпускаем  <br>\n";
							       					           	   foreach($bots_team as $keyto=>$valto)
							       					           	   	{
							       					           	   	 if ($keyto>$bbot[bot_count])
							       					           	   	 		{
							       					           	   	 		//перед выпском проверяем не закончился ли бой
								           	   	 			   			$data_battle=mysql_fetch_array(mysql_query("SELECT SQL_NO_CACHE * FROM battle where id={$bbot[battle]} ; "));
											           	   	 			if ( ($data_battle[win]!=3)AND($data_battle[status]!=0))
											           	   	 			{
											           	   	 			break;
											           	   	 			}

							       					           	   	 	echo "выпускаем бота - $valto  <br>\n";

						             	   	 								$BOT= mysql_fetch_array(mysql_query("select * from users where id=".$valto." ;"));
																	$BOT[protid]=$BOT[id];
																	$BOT_items=load_mass_items_by_id($BOT);
																	$BOT['login']=$BOT['login']." (".($keyto+1).")";

																	mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
																	`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
																	`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
																	`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
																	`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
																	`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
																	`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
																	`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`='{$bbot[battle]}',`bot`=2,
																	`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
																	`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
																	`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
																	`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
																	`injury_possible`=0, `battle_t`='{$bbot[battle_t]}' , bot_online = 1, bot_room='{$bbot[bot_room]}'   ;"); //онлайн =1 т.к. это не мастер боты
																	echo mysql_error();
																	$BOT['id'] = mysql_insert_id();
							       					           	   	 		// конкат в бой новый ид и хистори
							       					           	   	 		$time=time();
							       					           	   	 		$za=$bbot[battle_t];
						 	      					           	   	 		mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t'.$za.'`=CONCAT(`t'.$za.'`,\';'.$BOT['id'].'\') , `t'.$za.'hist`=CONCAT(`t'.$za.'hist`,\''.BNewHist($BOT).'\') WHERE `id` = '.$bbot[battle].'  ;');

							       					           	   	 		// отправляем системку и в лог
							       					           	   	 		if ($BOT['sex'] == 1) {$action="заступился";}	else {$action="заступилась";}
							       					           	   	 		$sexi[0]='вмешалась';
																	$sexi[1]='вмешался';

																	$BOT[battle_t]=$bbot[battle_t];
																	$ac=($BOT[sex]*100)+mt_rand(1,2);

																	addlog($bbot[battle],"!:W:".time().":".BNewHist($BOT).":".$BOT[battle_t].":".$ac."\n");

							       					           	   	 		// Добавляем счетчик
							       					           	   	 		mysql_query("UPDATE `users_clons` SET `bot_count`=`bot_count`+1 WHERE `id`='{$bbot['id']}';");

							       					           	   	 		}
							       					           	   	}

							       					           	}


							       					        }
												}
												else
												{
												echo " нету бота в онлайне id:{$config_mobs[$keymob]['master_bot']} <br>\n";

												// запросим данные из верибелс надо ли выпускать может он уже был выпущен?
									           		$run_master=mysql_fetch_array(mysql_query("select * from variables where var='bots_lvl_".$config_mobs[$keymob]['group_level']."_".$config_mobs[$keymob]['master_bot']."_is_run' ; "));
												           if ($run_master[value] >0)
											                        	{
									                		        	// в этой волне мастер бот уже выходил и был бой и его убили
									                        			echo "Этот бот уже был в онлайне его убили  <br>\n";
									                        			 $dead_bot++;
									                        			}
								                        				else
												                	    	{

																	echo "пора его завести в онлайн <br>\n";

																	//ставим для этого мастер бота флаг что мы его завели в онлайн
																	mysql_query("INSERT `variables` (`var`,`value`) values('bots_lvl_".$config_mobs[$keymob]['group_level']."_".$config_mobs[$keymob]['master_bot']."_is_run', '1' ) ON DUPLICATE KEY UPDATE `value` =1;");

																	$BOT= mysql_fetch_array(mysql_query("select * from users where id=".$config_mobs[$keymob]['master_bot']." ;"));
																	//доступные боту комнаты
																	$br=$config_mobs[$keymob]['room'];
																	$rnd_room=mt_rand(0,count($br)-1);
																	$botroom=$br[$rnd_room];
																	echo " комната где бот появится $botroom  <br>\n";

																	//системка что бот появился тут в этой комнате
																	 $fr=mt_rand(0,count($bot_mess)-1);
																	 addchp($bot_mess[$fr],$BOT['login']." (1)",$botroom,$bot_city);

																	$BOT[protid]=$BOT[id];
																		$BOT_items=load_mass_items_by_id($BOT);
																		mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']." (1)',`sex`='{$BOT['sex']}',
																		`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
																		`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
																		`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
																		`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
																		`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
																		`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
																		`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=0,`bot`=2,
																		`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
																		`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
																		`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
																		`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
																		`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$botroom}'   ;");
																		echo mysql_error();
																		$BOT['id'] = mysql_insert_id();

																}
												}
										} // мастер бот
						                       } //гл.цикл


						   		if ($dead_bot >=$all_bot)
						   			{
						   			echo "группа ботов погибла - для этого уровня <br>\n";

										$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=14"));
										//Неделя осады драконов
										if ($get_ivent['stat']==1)
										{
										$next_time=time()+(6*60*60);//6 ч следующая волна
										echo "Неделя осады драконов!!! 6ч  следующая волна <br>\n";
										}
										else
										{
										$next_time=time()+(12*60*60);//12 ч следующая волна
										echo "12ч  следующая волна <br>\n";
										}


									echo "обнуляем всем ботам этой уровневой группы флаг выхода <br>\n";
							           	mysql_query("UPDATE `variables` SET `value`=0  where `var` like 'bots_lvl_".$bot_lvl."_%_is_run' ;");
									echo "UPDATE `variables` SET `value`=0  where `var` like 'bots_lvl_".$bot_lvl."_%_is_run' ; <br> \n" ;

									echo "обнуляем уровневой группы флаги системок <br>\n";
							           	mysql_query("UPDATE `variables` SET `value`=0  where var like 'message".$bot_lvl."_time_%'  ;");
							           	echo "UPDATE `variables` SET `value`=0  where var like 'message".$bot_lvl."_time_%'  ;";

							           	  //ставим группе время след.выхода
							  	 	mysql_query("UPDATE `variables` SET `value`='{$next_time}' where `var`='bots_start_time_level_".$bot_lvl."' ;");
							  	 	echo "UPDATE `variables` SET `value`='{$next_time}' where `var`='bots_start_time_level_".$bot_lvl."' ; <br>\n" ;

									$TEXT="<font color=red>Внимание! Атака монстров отбита. Тишина и покой вновь вернулись на улицы города...</font>";
									echo $TEXT;echo "\n";
									addch2levels($TEXT,$bot_lvl,$bot_lvl,0);
						   			}
				   	echo "LVL: $bot_lvl / DEAD=$dead_bot /  ALL=$all_bot <br> \n";
				   	echo "--------------------------------<br> \n";

				   	}
				   	else  if ($bot_time-900 <= time())
				   	{
				   	echo "Не времяя для $bot_lvl -х ботов <br> - проверка системки 900 \n";
	  					$getmsg=mysql_fetch_array(mysql_query("select * from variables where var='message".$bot_lvl."_time_15m'  "));
		  				if ($getmsg['value']==0)
		  				{
		  					$cmobs=get_mobs_bylvlgroup($v_mobs,$bot_lvl); // получаем из конфига нужных ботов
		  					$botid=$cmobs[0]['master_bot'];
		  					$Bt= mysql_fetch_array(mysql_query("select * from users where id=".$botid." ;"));

						$TEXT="<img src=http://i.oldbk.com/i/align_4.9.gif><b>".$Bt['login']."<a href=http://capitalcity.oldbk.com/inf.php?".$Bt['id']." target=_blank><img src=http://i.oldbk.com/i/inf.gif></a></b></font><font color=black>: Глупцы! У вас было время, чтобы покинуть свои жилища. Но теперь поздно! Через <b>15 минут</b> всех вас зажарим и съедим! ";
						echo $TEXT;
						echo "\n";
						addch2levels($TEXT,$bot_lvl,$bot_lvl,0);
				           	mysql_query("UPDATE `variables` SET `value`=1  where var='message".$bot_lvl."_time_15m'  ;");
				           	}

				   	}
				   	else  if ($bot_time-10800 <= time())
				   	{
				   	echo "Не времяя для $bot_lvl -х ботов <br> - проверка системки 10800 \n";
				   		//Дракон (12)Информация: Три часа. Вам осталось всего три часа! Мы уже близко. Совсем скоро никого из вас не останется в живых!
	  					$getmsg=mysql_fetch_array(mysql_query("select * from variables where var='message".$bot_lvl."_time_3h'  "));
		  				if ($getmsg['value']==0)
		  				{
		  					$cmobs=get_mobs_bylvlgroup($v_mobs,$bot_lvl); // получаем из конфига нужных ботов
		  					$botid=$cmobs[0]['master_bot'];
		  					$Bt= mysql_fetch_array(mysql_query("select * from users where id=".$botid." ;"));

						$TEXT="<img src=http://i.oldbk.com/i/align_4.9.gif><b>".$Bt['login']."<a href=http://capitalcity.oldbk.com/inf.php?".$Bt['id']." target=_blank><img src=http://i.oldbk.com/i/inf.gif></a></b></font><font color=black>: Три часа. Вам осталось всего <b>три часа</b>! Мы уже близко. Совсем скоро никого из вас не останется в живых! ";
						echo $TEXT;
						echo "\n";
						addch2levels($TEXT,$bot_lvl,$bot_lvl,0);
				           	mysql_query("UPDATE `variables` SET `value`=1  where var='message".$bot_lvl."_time_3h'  ;");
				           	}


				   	}
				   	else  if ($bot_time-21600 <= time())
				   	{
				   	echo "Не времяя для $bot_lvl -х ботов <br> - проверка системки 21600 \n";
				   		//Дракон (12)Информация: Стая уже в пути! Нам нужно всего шесть часов на перелёт, затем мы спалим ваш город до тла!
				   		$getmsg=mysql_fetch_array(mysql_query("select * from variables where var='message".$bot_lvl."_time_6h'  "));
		  				if ($getmsg['value']==0)
		  				{
		  					$cmobs=get_mobs_bylvlgroup($v_mobs,$bot_lvl); // получаем из конфига нужных ботов
		  					$botid=$cmobs[0]['master_bot'];
		  					$Bt= mysql_fetch_array(mysql_query("select * from users where id=".$botid." ;"));

						$TEXT="<img src=http://i.oldbk.com/i/align_4.9.gif><b>".$Bt['login']."<a href=http://capitalcity.oldbk.com/inf.php?".$Bt['id']." target=_blank><img src=http://i.oldbk.com/i/inf.gif></a></b></font><font color=black>: Стая уже в пути! Нам нужно всего <b>шесть часов</b> на перелёт, затем мы спалим ваш город до тла!";
						echo $TEXT;
						echo "\n";
						addch2levels($TEXT,$bot_lvl,$bot_lvl,0);
				           	mysql_query("UPDATE `variables` SET `value`=1  where var='message".$bot_lvl."_time_6h'  ;");
				           	}

				   	}
				   	else
				   	{
				   	echo "Не времяя для $bot_lvl -х ботов <br> \n";
				   	echo date("Y.m.d H:i:s",$bot_time);
				   	echo "<br>\n";
					$not_time++;
				   	}
		}

   //системки
//   1. "Атака монстров отбита..." выдавать только если все уровни Драконов ушли на кулдаун
//2. "Монстры атакуют город" выдавать с появлением первых драконов после кулдауна

   	/*
	if ($not_time==$all_lvl) // у всех ботов не время
           	{
           	// назначение нового времени
           	echo "Все боты погибли - волна по времени <br> \n";
           	$sys_warn="select * from variables where var='bots_finish_sysm' ; ";
		$sys_warn=mysql_fetch_array(mysql_query($sys_warn));
			 if ($sys_warn[value]==0) //системки еще не было
			 	{
		  	 	$TEXT="<font color=red>Внимание! Атака монстров отбита. Тишина и покой вновь вернулись на улицы города...</font>";
		  	 	echo $TEXT;
		  	 	echo "<br>\n";
				addch2all($TEXT,$bot_city);
				mysql_query("UPDATE variables set value=1 where var='bots_finish_sysm' ; ");
				//делаем сброс стартовой системке
				mysql_query("UPDATE  variables  set  value =0 where var='bots_start_sysm' ; ");
				}
				else
				{
				echo "системка о финише  уже была <br>\n";
				}

           	}
           	else
           	   if ($not_time==$all_lvl-1)
			   	{
			   	$sys_warn="select * from variables where var='bots_start_sysm' ; ";
				$sys_warn=mysql_fetch_array(mysql_query($sys_warn));
				 if ($sys_warn[value]==0) //системки еще не было
				 	{
					$TEXT="<font color=red>Внимание! Тревога! Всем жителям города! Монстры атакуют Центральную Площадь! Все на защиту города от нашествия!</font>";
					addch2all($TEXT,$bot_city);
			  	 	echo $TEXT;
			  	 	echo "<br>\n";
					mysql_query("UPDATE variables set value=1 where var='bots_start_sysm' ; ");
					//делаю сброс финишной системке
					mysql_query("UPDATE variables set value=0 where var='bots_finish_sysm' ; ");
					}
					else
					{
					echo "системка о старте волны уже была <br>\n";
					}
			   	}
           	*/


          }
///////////////////////////////////////////////////////////////////////////////////////////////////////////
	   else
				{
				echo "нет данных о старте волн!...\n";
				}
	}

///////////////////
/////////боты хаоса select * from users where id>=137 and id<=164
	$hbots_rooms=array(44);
	//выбираем ботов которых надо выпустить или передвинуть
	$get_hbots=mysql_query("select * from users where id>=137 and id<=164 and fullhptime<=UNIX_TIMESTAMP()");
	if (mysql_num_rows($get_hbots) >0)
		{
			while($hbot=mysql_fetch_array($get_hbots))
			{
			shuffle($hbots_rooms);
			$hbotroom=$hbots_rooms[0];

				$get_move_bots=mysql_fetch_array(mysql_query("select * from users_clons where id_user={$hbot['id']};"));
				if ($get_move_bots['id']>0)
					{
						echo "есть такой Hбот protoBOT {$hbot['id']} => {$get_move_bots['id']} --- " ;
						if ($get_move_bots['battle']==0)
							{
							echo "не в бою можно двигать в room {$hbotroom}  \n";
							//переходим по комнате
							mysql_query("update users_clons set bot_room='{$hbotroom}' where id='{$get_move_bots['id']}' and battle=0 limit 1;");
							}
							else
							{
							echo " в бою \n";
							}

					}
					else
					{
					echo "нет такого Hбота protoBOT {$hbot['id']} = создаем! \n";
						$hbot_items=load_mass_items_by_id($hbot);
						mysql_query("INSERT INTO `users_clons` SET `login`='".$hbot['login']."',`sex`='{$hbot['sex']}',
						`level`='{$hbot['level']}',`align`='{$hbot['align']}',`klan`='{$hbot['klan']}',`sila`='{$hbot['sila']}',
						`lovk`='{$hbot['lovk']}',`inta`='{$hbot['inta']}',`vinos`='{$hbot['vinos']}',
						`intel`='{$hbot['intel']}',`mudra`='{$hbot['mudra']}',`duh`='{$hbot['duh']}',`bojes`='{$hbot['bojes']}',`noj`='{$hbot['noj']}',
						`mec`='{$hbot['mec']}',`topor`='{$hbot['topor']}',`dubina`='{$hbot['dubina']}',`maxhp`='{$hbot['maxhp']}',`hp`='{$hbot['maxhp']}',
						`maxmana`='{$hbot['maxmana']}',`mana`='{$hbot['mana']}',`sergi`='{$hbot['sergi']}',`kulon`='{$hbot['kulon']}',`perchi`='{$hbot['perchi']}',
						`weap`='{$hbot['weap']}',`bron`='{$hbot['bron']}',`r1`='{$hbot['r1']}',`r2`='{$hbot['r2']}',`r3`='{$hbot['r3']}',`helm`='{$hbot['helm']}',
						`shit`='{$hbot['shit']}',`boots`='{$hbot['boots']}',`nakidka`='{$hbot['nakidka']}',`rubashka`='{$hbot['rubashka']}',`shadow`='{$hbot['shadow']}',`battle`=0,`bot`=3,
						`id_user`='{$hbot['id']}',`at_cost`='{$hbot_items['allsumm']}',`kulak1`=0,`sum_minu`='{$hbot_items['min_u']}',
						`sum_maxu`='{$hbot_items['max_u']}',`sum_mfkrit`='{$hbot_items['krit_mf']}',`sum_mfakrit`='{$hbot_items['akrit_mf']}',
						`sum_mfuvorot`='{$hbot_items['uvor_mf']}',`sum_mfauvorot`='{$hbot_items['auvor_mf']}',`sum_bron1`='{$hbot_items['bron1']}',
						`sum_bron2`='{$hbot_items['bron2']}',`sum_bron3`='{$hbot_items['bron3']}',`sum_bron4`='{$hbot_items['bron4']}',`ups`='{$hbot_items['ups']}',
						`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$hbotroom}'   ;");
						echo mysql_error();
					}


			}
		}
		else
		{
		echo "Нет ботов хаоса для обработки. \n";
		}
////////////

//боты тыквы
if (false)
{
///////////////////
/////////боты Тыквы - добавить время ивента и комнаты
	$hbots_rooms=array(1,5,9,10,8,20,21,66,26,50);
	//выбираем ботов которых надо выпустить или передвинуть
	$get_hbots=mysql_query("select * from users where ((id>=165 and id<=186) OR (id>=281 and id<=286))   and fullhptime<=UNIX_TIMESTAMP()");
	if (mysql_num_rows($get_hbots) >0)
		{
			while($hbot=mysql_fetch_array($get_hbots))
			{
			shuffle($hbots_rooms);
			$hbotroom=$hbots_rooms[0];

				$get_move_bots=mysql_fetch_array(mysql_query("select * from users_clons where id_user={$hbot['id']};"));
				if ($get_move_bots['id']>0)
					{
						echo "есть такой Hбот protoBOT {$hbot['id']} => {$get_move_bots['id']} --- " ;
						if ($get_move_bots['battle']==0)
							{
							echo "не в бою можно двигать в room {$hbotroom}  \n";
							//переходим по комнате
							mysql_query("update users_clons set bot_room='{$hbotroom}' where id='{$get_move_bots['id']}' and battle=0 limit 1;");
							}
							else
							{
							echo " в бою \n";
							}

					}
					else
					{
					echo "нет такого Hбота protoBOT {$hbot['id']} = создаем! \n";
						$hbot_items=load_mass_items_by_id($hbot);
						mysql_query("INSERT INTO `users_clons` SET `login`='".$hbot['login']."',`sex`='{$hbot['sex']}',
						`level`='{$hbot['level']}',`align`='{$hbot['align']}',`klan`='{$hbot['klan']}',`sila`='{$hbot['sila']}',
						`lovk`='{$hbot['lovk']}',`inta`='{$hbot['inta']}',`vinos`='{$hbot['vinos']}',
						`intel`='{$hbot['intel']}',`mudra`='{$hbot['mudra']}',`duh`='{$hbot['duh']}',`bojes`='{$hbot['bojes']}',`noj`='{$hbot['noj']}',
						`mec`='{$hbot['mec']}',`topor`='{$hbot['topor']}',`dubina`='{$hbot['dubina']}',`maxhp`='{$hbot['maxhp']}',`hp`='{$hbot['maxhp']}',
						`maxmana`='{$hbot['maxmana']}',`mana`='{$hbot['mana']}',`sergi`='{$hbot['sergi']}',`kulon`='{$hbot['kulon']}',`perchi`='{$hbot['perchi']}',
						`weap`='{$hbot['weap']}',`bron`='{$hbot['bron']}',`r1`='{$hbot['r1']}',`r2`='{$hbot['r2']}',`r3`='{$hbot['r3']}',`helm`='{$hbot['helm']}',
						`shit`='{$hbot['shit']}',`boots`='{$hbot['boots']}',`nakidka`='{$hbot['nakidka']}',`rubashka`='{$hbot['rubashka']}',`shadow`='{$hbot['shadow']}',`battle`=0,`bot`=3,
						`id_user`='{$hbot['id']}',`at_cost`='{$hbot_items['allsumm']}',`kulak1`=0,`sum_minu`='{$hbot_items['min_u']}',
						`sum_maxu`='{$hbot_items['max_u']}',`sum_mfkrit`='{$hbot_items['krit_mf']}',`sum_mfakrit`='{$hbot_items['akrit_mf']}',
						`sum_mfuvorot`='{$hbot_items['uvor_mf']}',`sum_mfauvorot`='{$hbot_items['auvor_mf']}',`sum_bron1`='{$hbot_items['bron1']}',
						`sum_bron2`='{$hbot_items['bron2']}',`sum_bron3`='{$hbot_items['bron3']}',`sum_bron4`='{$hbot_items['bron4']}',`ups`='{$hbot_items['ups']}',
						`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$hbotroom}'   ;");
						echo mysql_error();
					}


			}
		}
		else
		{
		echo "Нет ботов тыкв для обработки. \n";
		}
////////////
}






	if  ($ZIMA)
	{
		echo "авто пуск снеговиков \n";

		start_snowmans();
		snowmans_stop_hil();
	}
	else
	{
		///авто пуск  деревья
		start_drevos();
		drevos_stop_hil();
	}

lockDestroy("cron_voln");
?>
