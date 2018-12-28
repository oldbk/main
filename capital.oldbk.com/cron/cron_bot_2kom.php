#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
include "/www/".$CITY_NAME.".oldbk.com/repa_conf.php"; // настройки для репы за ботов
include "/www/".$CITY_NAME.".oldbk.com/hill_config.php"; //настройки для кол.хилов в бою ботом


if( !lockCreate("cron_bot_job") ) {
   exit("Script already running.");
}
echo "Running bot fights...\n";
//addchp ('<font color=red>Внимание!</font> боты ходят ','{[]}Bred{[]}');

//система ходов боты против ботов v.4.1a +add set time out by clons+add Bs +add ClanWar
// переменные имперические
$lvlkof=0.01;

$attkof=0.1; // действует на силу удара
$attkritkof=0.1; // действует на силу удара прикрите
$kritblokkof=0.1; // действует на силу удара прикрите через блок

$kritkof=1.3; // действует на шанс
$uvorotkof=1.3; // дествует на шанс

$rabota_boni=0.315; // действует на расчет брони
$rabota_boni_krit=0.315; // действует на расчет брони при крите
$rabota_boni_krit_a=0.315; // действует на расчет брони при крите через блок

$min_uron=10; // минимальный урон

// mt_rand() в 4 раза быстрее radn();



	//$user = mysql_fetch_array(mysql_query('SELECT * FROM `users_clons` WHERE `id` = 10069752 LIMIT 1;'));
	//$real_enemy = mysql_fetch_array(mysql_query('SELECT * FROM `users_clons` WHERE `id` = 10069756 LIMIT 1;'));

	//include 'functions.php';



/////////functions ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include "/www/".$CITY_NAME.".oldbk.com/fsystem.php";
///end functions////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//дополнительная функция по хилу бота
function get_hil_bot($telo)
{
global $mob_hil;
$needh=(int)$mob_hil[$telo[id_user]];
//if ($telo[battle]==5288147)
{
//echo 'test';  echo "$telo[id_user]....$telo[hp]/$telo[maxhp] ....$telo[hil]....$needh.....battle:$telo[battle]<br>";
if (( ($telo[hp]+179)<$telo[maxhp]) AND ($telo[hil]<$needh) )
	{
		$cure_value=180;

		if(($telo['hp'] + $cure_value) > $telo['maxhp'])
		{
			$hp = $telo['maxhp'];
		}
		else
		{
			$hp = $telo['hp'] + $cure_value;
		}
		echo 'hill...';
		if ($telo['sex'] == 1) { $action = ""; }
		else { $action="а"; }

	mysql_query("UPDATE `users_clons` SET  `hil`=`hil`+1, `hp` = ".$hp." WHERE `id` = ".$telo['id']." and hp>0 ;");
	if (mysql_affected_rows()>0)
		{
		addlog($telo['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($telo,$telo[battle_t]).' использовал'.$action.' заклятие восстановления энергии и восстановил'.$action.' уровень жизни <B>+'.$cure_value.'</B> ['.($hp).'/'.$telo['maxhp'].']<BR>');
		return $hp;
		}


	}
  }
return false;
}

   // получаем живых ботов в т1
   $bots1=mysql_query('SELECT * FROM `users_clons` WHERE hp >0 and battle_t=1 ;');
   $membattle=array(); $ke=0;
   // проходимся по каждому
while ($user = mysql_fetch_array($bots1))
{

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// тут унас типы таймы и все флажки бой идет
   $data_battle=mysql_fetch_array(mysql_query("SELECT * FROM battle where id={$user[battle]} ; "));
	   //
	   if ($data_battle[win]==3)
	   {
	   // бой все еещ идет
	   $battle_ok=true; // флаг идити дальше
	   }
	   else
	   {
	   /// бой уже кончился  но еще не начислен опыт и не выпущено тело из боя
	   $battle_ok=false; // не идти дальше концовка боя
	   
	   	if (($data_battle[status]==1) and ($data_battle[t1_dead]=='finbatt'))
	   		{
	   		//бой кончился удаляем этого клона
	   		mysql_query("DELETE FROM users_clons WHERE id='{$user[id]}';");
	   		}
	   }

if ($battle_ok)
{
$hhh=0;
	//хил в команде ботов 1
	$hhh=get_hil_bot($user);
	if ($hhh>0)
		{
		$use_ok=1;
		$user[hp]=(int)$hhh;
		}
		else
		{
		$use_ok=0;
		}
   $ke++;
   $cko=0;
   $membattle[$ke]=$user[battle]; // запоминаем идшники боев для их исключения в запросе на тайм команды 2 см. ниже

// теперь получим  противника бота если он есть в этом бою - живого!
   $bots2=mysql_query('SELECT * FROM `users_clons` WHERE hp >0 and battle_t=2 and battle='.$user[battle].' ;');
    while ($real_enemy = mysql_fetch_array($bots2))
	{
	$hhh=0;
	$hhh2=0;
	  	if ($use_ok==1)
	  	{
	  	//только что хильнулся ставим в 0 для следующего раза
	  	$use_ok=0;
	  	}
	  	else
	  	{
	  	   //хил в команде ботов 1
	  	      $hhh=get_hil_bot($user);
		      if ($hhh > 0) {$user[hp]=$hhh;}
		}

		//Вызов хила если надо хильнется
		//хил в команде ботов 2
		$hhh2=get_hil_bot($real_enemy);
		if ($hhh2>0) { $real_enemy=$hhh2; }


	   $cko++;
//загружаем мои шмотки все кроме магий они подарков и всякой херни надо добавить
// загруженный масив буит нужен для расчетов и отображения
		$my_wearItems=load_mass_items_by_id($user); // загрузка
		$my_magicItems=$my_wearItems[incmagic]; // распаковка магии



		// моя тима и тима врага
		if ($user[battle_t]==1) { $my_team_n=1; $en_team_n=2; } else { $my_team_n=2; $en_team_n=1; }

		$BSTAT[win]=$data_battle[win];
		if (($BSTAT[win]==3) and ($data_battle[status]==0))
		{

			if ($user[hp]>0) // если я не труп
		  {
//////////////////////////////////////////
	         $my_enemy_do[attack]=mt_rand(1,4);
	         $my_enemy_do[block]=mt_rand(1,4);

	         $im_do[defend]=mt_rand(1,4);
	         $im_do[attack]=mt_rand(1,4);
////////////////////////////////////////////////////////////

			if ($user[id] < _BOTSEPARATOR_ )
			{
				$user_eff=load_battle_eff($user,$data_battle);
			}
			if ($real_enemy[id] < _BOTSEPARATOR_ )
			{
			$real_enemy_eff=load_battle_eff($real_enemy,$data_battle);
			}

			$en_wearItems=load_mass_items_by_id($real_enemy); // загрузка
		//////////////////////////////////////////////////
			$input_attack=do_attack_in($data_battle,$user,$real_enemy,$my_enemy_do[attack],$im_do[defend],$my_wearItems,$en_wearItems,$user_eff,$real_enemy_eff,'from_cron_bot');
			write_stat($input_attack[stat],$data_battle[id]); // пишем статистику

			$output_attack=do_attack_out($data_battle,$user,$real_enemy,$im_do[attack],$my_enemy_do[block],$my_wearItems,$en_wearItems,$user_eff,$real_enemy_eff,'from_cron_bot');
			write_stat($output_attack[stat],$data_battle[id]); // пишем статистику

	 // в обеих процедурах добавить в определения конца боя код по ботам
			$rez=mysql_fetch_array(mysql_query("select do_razmen_to_bot_from_bot({$data_battle[id]},{$user[id]},{$user[battle_t]},{$real_enemy[id]},{$real_enemy[battle_t]},{$output_attack[dem]},{$input_attack[dem]}, {$data_battle[type]}) as ret;"));

			echo "<b>MAIN RESULT: $rez[0] </b><br> ";
			$STING='REZ'.$rez[0];
			switch ($STING)
		     {
				case "REZ11":
				{
				//echo "11";
				// оба живые текстуем в лог
				addlog($data_battle[id],$input_attack[text].$output_attack[text]);
		/////////////// комментируем - комментатор работает если размен нормальный - и нихто не умер после него
				if ($battle_data['type']==20)
								{
								addlog($data_battle[id],get_comment_fifa()); // комментатор аля футбольный
								}
								else
								{
								addlog($data_battle[id],get_comment()); // комментатор обыный
								}

				}
				break;

				case "REZ01":
				 {
					// echo "01";
					 // $user труп - $real_enmy -жив
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
			 		addlog($data_battle[id],$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' <b>'.$action[$rda].$sexi[$user[sex]].'</b>!<BR>');

					//отнимаем в мемори
					$user[hp]=0;
					$STEP = 4;
				}
				break;

				case "REZ10":
				{
			 		// echo "10";
					 //  наоборот
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					addlog($data_battle[id],$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($real_enemy,$real_enemy[battle_t]).' <b>'.$action[$rda].$sexi[$real_enemy[sex]].'</b>!<BR>');
					//отнимаем в мемори /
					$user[hp]-=$input_attack[dem];
		 		}
				break;
				case "REZ00":
				 {
			 		// echo "00";
					 // обатрупы - но бой идет дальше
			 		$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					addlog($data_battle[id],$input_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' '.$action[$rda].$sexi[$user[sex]].'!<BR>'.$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($real_enemy,$real_enemy[battle_t]).' '.$action[$rda].$sexi[$real_enemy[sex]].'!<BR>');
					//отнимаем в мемори
					$user[hp]=0;
					 $STEP = 4;
				 }
				break;

				case "REZ1010":
				{
					// echo "1010";
					// последний размен убил последнего врага победа команды юзера
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					//fix 29/07/2011
					$fin_add_log=$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($real_enemy,$real_enemy[battle_t]).' <b>'.$action[$rda].$sexi[$real_enemy[sex]].'</b>!<BR>';

			 		############################################
			 		$win_team_hist='t'.$user[battle_t].'hist';
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> Бой закончен, победа за <b>'.BNewRender($data_battle[$win_team_hist]).'</b>!<BR>';
			 		//fix 29/07/2011
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////

					//тут ставим флаг финал боя = победа команды $user[battle_t];
					$BSTAT[win]=$user[battle_t];
			 		############################################
					//отнимаем в мемори / данные врага не трогаем т.к. уже не надо :)
					$user[hp]-=$input_attack[dem];
					$STEP = 5;
				}
				break;

				case "REZ0101":
				{
					// echo "0101";
					// последний размен убил юзера но не врага победа тимы врага
					//fix 29/07/2011
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					$fin_add_log=$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' <b>'.$action[$rda].$sexi[$user[sex]].'</b>!<BR>';
			 		############################################
			 		$win_team_hist='t'.$real_enemy[battle_t].'hist';
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> Бой закончен, победа за <b>'.BNewRender($data_battle[$win_team_hist]).'</b>!<BR>';
			 		//fix 29/07/2011
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////
					//тут ставим флаг финал боя = победа команды $real_enemy;
					$BSTAT[win]=$real_enemy[battle_t];
			 		############################################
					//отнимаем в мемори
					$user[hp]=0;
					 $STEP = 5;
				}
				break;

				case "REZ0001":
				{
					// echo "0001";
					// юзер погиб последним  и враг погиб команда врага победила т.к. остались живые
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					//fix 29/07/2011
					$fin_add_log=$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' <b>'.$action[$rda].$sexi[$user[sex]].'</b>!<BR>';
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> '.nick_in_battle($real_enemy,$real_enemy[battle_t]).' <b>'.$action[$rda].$sexi[$real_enemy[sex]].'</b>!<BR>';
			 		############################################
			 		$win_team_hist='t'.$real_enemy[battle_t].'hist';
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> Бой закончен, победа за <b>'.BNewRender($data_battle[$win_team_hist]).'</b>!<BR>';

			 		//fix 29/07/2011
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////


					//тут ставим флаг финал боя = победа команды $real_enemy;
					$BSTAT[win]=$real_enemy[battle_t];
			 		############################################
					//отнимаем в мемори
					$user[hp]=0;
					// врага не трогаем т.к. пофигу
					 $STEP = 5;
				}
				break;
				case "REZ0010":
				{
					// echo "0010";
					// юзер погиб и враг погиб последним  команда юзера победила т.к. остались живые
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					$fin_add_log=$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' <b>'.$action[$rda].$sexi[$user[sex]].'</b>!<BR>';
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> '.nick_in_battle($real_enemy,$real_enemy[battle_t]).' <b>'.$action[$rda].$sexi[$real_enemy[sex]].'</b>!<BR>';
			 		############################################
			 		$win_team_hist='t'.$user[battle_t].'hist';
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> Бой закончен, победа за <b>'.BNewRender($data_battle[$win_team_hist]).'</b>!<BR>';
			 		//fix 29/07/2011
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////
					//тут ставим флаг финал боя = победа команды $user;
					$BSTAT[win]=$user[battle_t];
			 		############################################
					//отнимаем в мемори
					$user[hp]=0;
					// врага не трогаем т.к. пофигу
					 $STEP = 5;
				}
				break;

				case "REZ0000":
				{
					// echo "0000";
					// вышла ничья в последних разменах
					$sexi[0]='ла';$sexi[1]=''; $action[0]='умер';$action[1]='погиб';$rda=mt_rand(0,1);
					$fin_add_log=$input_attack[text].$output_attack[text].'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' <b>'.$action[$rda].$sexi[$user[sex]].'</b>!<BR>';
					$fin_add_log.='<span class=date>'.date("H:i").'</span> '.nick_in_battle($real_enemy,$real_enemy[battle_t]).' <b>'.$action[$rda].$sexi[$real_enemy[sex]].'</b>!<BR>';

			 		############################################
			 		$fin_add_log.='<span class=date>'.date("H:i").'</span> Бой закончен. Ничья.<BR>';
			 		//fix 29/07/2011
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////
					//тут ставим флаг финал боя = ничья
					$BSTAT[win]=0;
			 		############################################
					//отнимаем в мемори
					$user[hp]=0;
					 $STEP = 5;
				}
				break;

	         } // fin switch

/////
// юзерские клоны против лабовских монстров
// юзеровские клоны находятся в $user
if (    ($real_enemy[id_user] > 89) AND ($real_enemy[id_user] < 230) 	  AND //$real_enemy[id_user] - у жиивых людей этого параметра нету!!!!
	 (  ( $STING=='REZ10') OR ( $STING=='REZ00') OR ( $STING=='REZ1010') OR ( $STING=='REZ0010') ) // точки размена где враг умер
	 )
	 {
 	$aadd_rep=(int)($mob_rep[$real_enemy[id_user]]);
 	//addchp ('<font color=red>Внимание!</font> будет добавленно репы'.$aadd_rep,'{[]}Bred{[]}');
 	///$user[id_user] - хозяин клона в команде Т1
	mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id_user]."', 'labkillrep', '".$aadd_rep."' ) ON DUPLICATE KEY UPDATE `val` =`val`+".$aadd_rep.";");

	 }

////



	     // урон и опыт добавляется прежде чем пойдет фаза завершения
	    		//добавин если был мой урон опыту и урону в базу
			if ($output_attack[dem] > 0 )
				{
				 solve_exp($data_battle,$user,$real_enemy,$my_wearItems[allsumm],$en_wearItems[allsumm],$output_attack[dem]);
				}
		// добавим еще и вражеский урон и опыт если был
			if ($input_attack[dem] > 0 )
				{
				 solve_exp($data_battle,$real_enemy,$user,$en_wearItems[allsumm],$my_wearItems[allsumm],$input_attack[dem]);
				}

		 //2.2 проверка на окончание боя

			if ($BSTAT[win]==1)
			 {
			 // победа команды 1
			 // апдейтим мемори
			 	$data_battle[win]=1;
			 //
				mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
				 $winrez[0]=finish_battle(1,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
			 //чистка разменов убрано в finish_battle
			 //mysql_query("delete from `battle_fd` where `battle`={$data_battle[id]} ; ");
			 //mysql_query("delete from `users_clons` where `battle`={$data_battle[id]} ; ");

	 		 	if ($data_battle[blood]>0) { 	 addlog($data_battle[id],get_text_travm($data_battle)); }
			 	addlog($data_battle[id],get_text_broken($data_battle));



			 }
			 else if ($BSTAT[win]==2)
			 {
				 //победа команды 2
		 		 // апдейтим мемори
				 $data_battle[win]=2;
				 mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
				 $winrez[0]=finish_battle(2,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
				 //чистка разменов убрано в finish_battle
				 //mysql_query("delete from `battle_fd` where `battle`={$data_battle[id]} ; ");
				 //mysql_query("delete from `users_clons` where `battle`={$data_battle[id]} ; ");

		 		 if ($data_battle[blood]>0) { 	 addlog($data_battle[id],get_text_travm($data_battle)); }
				 addlog($data_battle[id],get_text_broken($data_battle));


			 }
			 else if ($BSTAT[win]==0)
			 {
				 // ничья
		 		 // апдейтим мемори
				 $data_battle[win]=0;
				 mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
				 $winrez[0]=finish_battle(0,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
				 //чистка разменов убрано в finish_battle
				 //mysql_query("delete from `battle_fd` where `battle`={$data_battle[id]} ; ");
				 //mysql_query("delete from `users_clons` where `battle`={$data_battle[id]} ; ");

		 // 		 if ($data_battle[blood]>0) { 	 addlog($data_battle[id],get_text_travm($data_battle)); }
				 addlog($data_battle[id],get_text_broken($data_battle));

			 	

			 }
			 else
			 {
			 // бой идет
			/// 3. очистка размена
			/*-----*/
			// по окончанию надо удалить запись из таблицы разменов
	//		 mysql_query("delete from `battle_fd` where `battle`={$data_battle[id]} and `razmen_from`={$real_enemy[id]} and `razmen_to`={$user[id]} ; ");
			 }



		  } //проверка на хп
		   else
		   {
		   // юзер труп


		   }

 		} /////// проверка на бой еще еидет
	 else
	 {
	 	$STEP = 5;
	 }

 // проверка на рамку тайма
	  if ($STEP==2)
	   {
	   //проверка тайма
	   //print_r($data_battle);
	       if (get_timeout($data_battle,$user) )
		       {
		       	$STEP=3;
		       }

	   }


	} //$bots2 цикл

	if   ($cko==0)
	 {
	 // бой есть но в бою нет ответного бота на стороне 2
	 // тут надо тогда проверять тайм по не ответу на клона
	 	$data_battle=mysql_fetch_array(mysql_query("SELECT * FROM battle where id={$user[battle]} ; "));
	 	if (($data_battle[win]==3) and ($data_battle[status]==0))
	      {
	      	if (get_timeout($data_battle,$user) )
	      	{
	      			// лочим
	      			mysql_query("UPDATE battle set status=1 where id={$data_battle[id]}");
	      			$data_battle[win]=$user[battle_t];
		 		//$data_battle[blood]=1;// ставим ее чтоб проигравшие получили травму
		 		if ($data_battle[type]!=10)	//если бой не в БС
			 		 {
			 		    // тут надо поставить травму тому кто остался
			 		    if ($user[battle_t]==1) { $my_team_n=1; $en_team_n=2; } else { $my_team_n=2; $en_team_n=1; }
			 		    
		 		    if ($data_battle[blood]==2)
			 		      {
			 		         mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<30, settravmatv2_new(id,'sila',sila,level,{$data_battle[id]},{$data_battle[type]},align,trv,pasbaf), sila), lovk = IF(@RR>=30 AND @RR<60, settravmatv2_new(id,'lovk',lovk,level,{$data_battle[id]},{$data_battle[type]},align,trv,pasbaf), lovk), inta = IF(@RR>=60, settravmatv2_new(id,'inta',inta,level,{$data_battle[id]},{$data_battle[type]},align,trv,pasbaf), inta) where battle={$data_battle[id]} and battle_t={$en_team_n} and hp > 0 and `level` > 3 and align!=5;");
			 		      }
			 		    else
			 		     if ($data_battle[type]!=1) //не физ
			 		       {
			 		         mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<30, settravmat2(id,'sila',sila,level,{$data_battle[id]},{$data_battle[type]},trv), sila), lovk = IF(@RR>=30 AND @RR<60, settravmat2(id,'lovk',lovk,level,{$data_battle[id]},{$data_battle[type]},trv), lovk), inta = IF(@RR>=60, settravmat2(id,'inta',inta,level,{$data_battle[id]},{$data_battle[type]},trv), inta) where battle={$data_battle[id]} and battle_t={$en_team_n} and hp > 0 and `level` > 3 and align!=5;");
						$errrr=mysql_error();
						//addchp ('<font color=red>Внимание!</font>'.$errrr,'{[]}Bred{[]}');				 		         
			 		       }
			 		       else
			 		       {
			 		       //физ - уточняяем что до 4 лвл травмы не получаем
		       	 		       mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<30, settravmat2(id,'sila',sila,level,{$data_battle[id]},{$data_battle[type]},trv), sila), lovk = IF(@RR>=30 AND @RR<60, settravmat2(id,'lovk',lovk,level,{$data_battle[id]},{$data_battle[type]},trv), lovk), inta = IF(@RR>=60, settravmat2(id,'inta',inta,level,{$data_battle[id]},{$data_battle[type]},trv), inta) where battle={$data_battle[id]} and battle_t={$en_team_n} and hp > 0 and `level` > 3 and align!=5;");
			 		       }
				     }



		 		$win_team_hist='t'.$data_battle[win].'hist';

		 		//fix 29/07/2011
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],'<span class=date>'.date("H:i").'</span> Бой закончен '.($vrag_exit==1?"":"по таймауту").', победа за <b>'.BNewRender($data_battle[$win_team_hist]).'</b>!<BR>');
							if ($vrag_exit!=1)
							{
				 			mysql_query("INSERT INTO `battle_time_out` SET `battle`='{$data_battle[id]}',`owner`='{$user[id]}',`login`='{$user[login]}';");
				 			}

				 		}
				 	////////////////////////



				$winrez[0]=finish_battle($data_battle[win],$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);

				 addlog($data_battle[id],get_text_travm($data_battle));
				 addlog($data_battle[id],get_text_broken($data_battle));

	      			//addchp ('<font color=red>Внимание!</font> ТАйм от клона В Т1 ('.$data_battle[id].') ','{[]}Bred{[]}');
	      	}
	      }
	 }


}//условие если бой идет

} // $bots1 цикл

	///// синхронная часть для тайма команды 2
	// получаем данные о боях клонов команды 2 не попавших в первых запросах группируюем по боям если к примеру
	//  в одном бою много клонов на одной стороне - то тогда нужена только одна запись
	//$restal_bots_arr=array(247,248,249,250,251);	
	
	
	//$bots2=mysql_query('SELECT * FROM `users_clons` WHERE hp >0 and battle_t=2 and battle not in (".implode(",",$membattle).") GROUP BY battle ;');
	$bots2=mysql_query('SELECT * FROM `users_clons` WHERE hp >0 and battle_t=2 GROUP BY battle  ;');
	
	  // проходимся по каждому
	   while ($user = mysql_fetch_array($bots2))
	    {
	     $data_battle=mysql_fetch_array(mysql_query("SELECT * FROM battle where id={$user[battle]} ; "));
	    	 
	    	 
	    	if (($data_battle[win]==3) and ($data_battle[status]==0))
	      	{
	      	//бой идет
		      if (get_timeout($data_battle,$user) )
	      			{
	      			// лочим
		      			mysql_query("UPDATE battle set status=1 where id={$data_battle[id]}");
		      			$data_battle[win]=$user[battle_t];
	 		 //$data_battle[blood]=1;// ставим ее чтоб проигравшие получили травму
	 		  //if ($data_battle[blood]!=1) // если бой не кровавый то ставим тому кто остался травму, если кровавый - то! и так все получат!
	 		  if ($data_battle[type]!=10)	//если бой не в БС
				 		   {
				 		    // тут надо поставить травму тому кто остался
				 		    if ($user[battle_t]==1) { $my_team_n=1; $en_team_n=2; } else { $my_team_n=2; $en_team_n=1; }
				 		    
				 		    if ($data_battle[blood]==2)
				 		      {
				 		         mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<30, settravmatv2_new(id,'sila',sila,level,{$data_battle[id]},{$data_battle[type]},align,trv,pasbaf), sila), lovk = IF(@RR>=30 AND @RR<60, settravmatv2_new(id,'lovk',lovk,level,{$data_battle[id]},{$data_battle[type]},align,trv,pasbaf), lovk), inta = IF(@RR>=60, settravmatv2_new(id,'inta',inta,level,{$data_battle[id]},{$data_battle[type]},align,trv,pasbaf), inta) where battle={$data_battle[id]} and battle_t={$en_team_n} and hp > 0 and `level` > 3 and align!=5;");
				 		      }
				 		    else
				 		     if ($data_battle[type]!=1) //не физ
				 		       {
				 		         mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<30, settravmat2(id,'sila',sila,level,{$data_battle[id]},{$data_battle[type]},trv), sila), lovk = IF(@RR>=30 AND @RR<60, settravmat2(id,'lovk',lovk,level,{$data_battle[id]},{$data_battle[type]},trv), lovk), inta = IF(@RR>=60, settravmat2(id,'inta',inta,level,{$data_battle[id]},{$data_battle[type]},trv), inta) where battle={$data_battle[id]} and battle_t={$en_team_n} and hp > 0 and `level` > 3 and align!=5;");
				 		       }
				 		       else
				 		       {
				 		       //физ - уточняяем что до 4 лвл травмы не получаем
			       	 		       mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<30, settravmat2(id,'sila',sila,level,{$data_battle[id]},{$data_battle[type]},trv), sila), lovk = IF(@RR>=30 AND @RR<60, settravmat2(id,'lovk',lovk,level,{$data_battle[id]},{$data_battle[type]},trv), lovk), inta = IF(@RR>=60, settravmat2(id,'inta',inta,level,{$data_battle[id]},{$data_battle[type]},trv), inta) where battle={$data_battle[id]} and battle_t={$en_team_n} and hp > 0 and `level` > 3 and align!=5;");
				 		       }
						 }




				 		$win_team_hist='t'.$data_battle[win].'hist';
						//fix 29/07/2011
			 			//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 			mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 			if (mysql_affected_rows()>0)
			 			{
				 		addlog($data_battle[id],'<span class=date>'.date("H:i").'</span> Бой закончен '.($vrag_exit==1?"":"по таймауту").', победа за <b>'.BNewRender($data_battle[$win_team_hist]).'</b>!<BR>');
							if ($vrag_exit!=1)
							{
				 			mysql_query("INSERT INTO `battle_time_out` SET `battle`='{$data_battle[id]}',`owner`='{$user[id]}',`login`='{$user[login]}';");
				 			}				 		

				 		}

						$winrez[0]=finish_battle($data_battle[win],$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);


					 addlog($data_battle[id],get_text_travm($data_battle));
					 addlog($data_battle[id],get_text_broken($data_battle));


	      			//addchp ('<font color=red>Внимание!</font> ТАйм от клона T2 ('.$data_battle[id].') ','{[]}Bred{[]}');
	      			}
		}
		else
		{
		//бой окончен
		// проверяем точно ли все закончилось если да чистим ботов в этом бою тна стороне т2
			if (($data_battle[status]==1) and ($data_battle[t1_dead]=='finbatt') and ($data_battle[win]!=3))
	   		{
	   		//бой кончился удаляем этого клона
	   		mysql_query("DELETE FROM users_clons WHERE battle='{$data_battle[id]}';");
	   		}
		
		
		
		}

	    }


echo "Finishing script. Destroy lock.\n";
lockDestroy("cron_bot_job");
?>