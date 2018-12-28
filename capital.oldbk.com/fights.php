#!/usr/bin/php
<?php
$VER_f='v.9.2.2 04/28/2017';

$lvlkof=0.01;
$EXP_TO_LOSE=0; //1- вкл / 0-выкл
$EXP_WIN=1; // 100%  стороне победившей
$attkof=0.1; // действует на силу удара
$attkritkof=0.1; // действует на силу удара прикрите
$kritblokkof=0.1; // действует на силу удара прикрите через блок

$kritkof=1.3; // действует на шанс
$uvorotkof=1.3; // дествует на шанс

$output_attack_magic_dem=0;
$input_attack_magic_dem=0;

$rabota_boni_vinos=true; // переключатель , true= работает плавующая броня из конфига , false - работают параметры те что ниже

if ($rabota_boni_vinos==true)
	{
	$rabota_boni_delta=0.2; // дельта	
	}
	else
	{
	$rabota_boni_delta=0.5; // дельта	
	$rabota_boni=0.5; // действует на расчет брони было 0.3 - везде 11/09/2011=0.315 , 5/18/2016 =0.515
	$rabota_boni_krit=0.5; // действует на расчет брони при крите
	$rabota_boni_krit_a=0.5; // действует на расчет брони при крите через блок
	}

$input_fix_cost_level=0;
$output_fix_cost_level=0;

$min_uron=1; // минимальный урон было 10
// ДЕмон остался нужен только для запуска заявок
//система Деном для заявок старый + мегабитвы (автоходы) v.4.1a +add set time out by clons+add Bs +add ClanWar
    // подключаем mysql, подключаем функции
	ini_set('display_errors','On');

    include "/www/capitalcity.oldbk.com/connect.php";
    include "/www/capitalcity.oldbk.com/functions.php";
    include "/www/capitalcity.oldbk.com/functions.zayavka.php";
    include "/www/capitalcity.oldbk.com/fsystem.php";
    include "/www/capitalcity.oldbk.com/fuc1.php";





//Дополнительная функция отхила в бою чара
function make_hil_battle($telo,$cure_value)
{
 if (($telo[hp]>0) and ($cure_value>0) and ($telo[hp] < $telo[maxhp] ) )
 	{
	
	 	if(($telo['hp'] + $cure_value) > $telo['maxhp'])
		{
			$hp = $telo['maxhp'];
			$add_hp=$telo['maxhp']-$telo['hp'];
		}
		else
		{
			$hp = $telo['hp'] + $cure_value;
			$add_hp=$cure_value;
		}
 		mysql_query("UPDATE `users` SET   `hp` = `hp` + '{$add_hp}'    WHERE `id` = '{$telo[id]}' and hp>0 ");
 		if (mysql_affected_rows()>0)
		{
	
		if (( $telo['hidden'] > 0 ) and ( $telo['hiddenlog'] =='' ) ) { $telo['sex']==1 ; }
		elseif (( $telo['hidden'] > 0 ) and ( $telo['hiddenlog'] !='' ) )
		{
		 $ftelo = load_perevopl($telo);  
		 $telo[sex]=$ftelo[sex];
		}

		if (($telo['hidden'] > 0) and ( $telo['hiddenlog'] =='' ))
		{
			addlog($telo['battle'],"!:H:".time().':'.nick_new_in_battle($telo).":".(($telo[sex]*100)+1).":".(($telo[sex]*100)+1)."::::::".$cure_value."|??:[??/??]\n");		
			
		} else {
			addlog($telo['battle'],"!:H:".time().':'.nick_new_in_battle($telo).":".(($telo[sex]*100)+1).":".(($telo[sex]*100)+1)."::::::".$cure_value.":[".($hp)."/".$telo['maxhp']."]\n");		
		}
		return $add_hp;
		}
		else
		{
	 	return false;		
		}
 	}
 	else
 	{
 	return false;
 	}
return false;
}




addchp ('<font color=red>Внимание!</font> start Cron fights '.$VER_f.' ('.CITY_DOMEN.') ','{[]}Bred{[]}',-1,-1);
addchp ('<font color=red>Внимание!</font> start Cron fights '.$VER_f.' ('.CITY_DOMEN.') ','{[]}Байт{[]}',-1,-1);
addchp ('<font color=red>Внимание!</font> start Cron fights '.$VER_f.' ('.CITY_DOMEN.') ','{[]}Десятый{[]}',-1,-1);
	// переменные времени запуска последний раз
	$time_zayavka = 0;
	$time_status_fights = 0;
	// тайминги запусков в секундах
	$zayavka = 10;
	$status_fights = 10;

	// функция проверки заявок под старт
function check_zayavka($time_now) 
	{
	//запрашиваем бои необходимые для старта
	//3,4,5- склонки,группы,хаоты
	//по времени или набралось вся команда
	$get_all_zay=mysql_query("select * from zayavka where ((`start`<={$time_now}  or ((substrCount(team1,';')>=t1c) AND (substrCount(team2,';')>=t2c)) or (level=5 and zcount>=t1c)  )  AND level in (3,4,5,7) ) OR (`start`<={$time_now} and level=6) ");
	if (mysql_num_rows($get_all_zay))
		  {
			while ( $row = mysql_fetch_array($get_all_zay) ) 
			{
				battlestart( "CHAOS", $row, $row[level] );

			}
		  }
	}


/// функция определения статусных битв
function check_status_fights()
{
		// получаем список текущих боев которые идут больше часа
		$time_shift = time()+3600; // 60*60 = 3600
		//7200 - 2 часа разница  в конвертации
		//выбираем все статусные и бои с авто ударом
		$q = "SELECT * FROM battle WHERE ( CHAOS=-1 OR CHAOS=2 OR status_flag>0 ) and win='3' and status=0 "; // статус 0 - не в ообработке

		$bdq = mysql_query("$q"); // win=3 это только НЕзавершенные бои.

		while($bd = mysql_fetch_array($bdq)) {

		if (($bd['type']==4) OR ($bd['type']==5))
		{
		//для  кулачек - делаем авто удары если надо статусы не трогаем 21/12/2013
		 $do_auto=1; 
		 $auto_flag=-1;
		}
		else		
		if (($bd['type']==40) OR ($bd['type']==41) or ($bd['type']==100) or ($bd['type']==101) )
		{
		//для противостояния просто делаем автоудар статусы не трогаем
		 $do_auto=1; 
		 $auto_flag=-1;
		}
		else	
		 if (($bd['status_flag']!=10) and ($bd['type']!=7) )
		 {
				// делаем подсчет реальных юзверей в этом бою.
				$cnt = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM users WHERE battle='".$bd['id']."'"));
				$countall = $cnt[0];
				$current_status = $bd['status_flag'];
				$auto_flag=$bd['CHAOS'];
				$new_status=0;
				$mk_blood='';
				
				if (!($bd['type'] == 3 && $bd['CHAOS'] > 0)) {
					if ($countall >= 666) 
					{
						$new_status=4;
						 if ($bd['blood']==0)
						 	{
							$mk_blood=' , blood=1 ';
							}
					}
					else 
					if ($countall >= 200) 
					{
						$new_status=3;
					} 
					elseif ($countall >= 150) 
					{
						$new_status=2;
					} 
					elseif ($countall >= 100) 
					{
						$new_status=1;
					} 
					else 
					{
						$new_status=0;
					}
				}
				

				// прописываем новый статус если он изменился и стал выше прошлого значения
				if(($new_status != $current_status) && $new_status > $current_status)
				{
					
				      if ($bd['coment']!='<b>Бой с Пятницо</b>')
				      	{
			      		$newtimeout=', `timeout`=3 '; //любой статус 3 мин. таймя
			      		}

					mysql_query("UPDATE battle SET status_flag='".$new_status."'  ".$newtimeout."  ".$mk_blood."  WHERE id='".$bd['id']."'");

					  // если супер мега битва щас получилась
					  // то обновляем - время разменов
					  $t=time();
					  mysql_query("UPDATE battle_fd SET time_blow='{$t}' WHERE battle='".$bd['id']."'");


				}
				else
				 {
				 $do_auto=1;
				 }
		} else
		{

		// fix 1/03/2012
		 // тайм в великих хаотах
		   if ((($bd['status_flag']==10) and ($bd['timeout']!=3)) and ($bd['coment']!='<b>Бой с Пятницо</b>') ) 
		   		{
		   		//если великий хаот и тайм не 3 минуты то правим его на три минуты
		   		mysql_query("UPDATE battle SET timeout=3 WHERE id='".$bd['id']."'");
		   		}
		   		
		 $do_auto=1;	
		 $auto_flag=2;
		}	 
				
				if ($do_auto==1)
				{
					
				// если статус не поменялся
				 /// и текущий статус больше 0, то
				   if ( ($current_status > 0) or ($auto_flag==2) or ($auto_flag==-1) )
				     {
				     		
				     $need_time=($bd['timeout']*60)-60;
				     $sqll="select fd.id, fd.battle, fd.owner, fd.razmen_to, fd.razmen_from, fd.attack, fd.attack2, fd.block, fd.time_blow, fd.lab, usr1.id as usr1id, usr2.id as usr2id from battle_fd as fd
					 inner JOIN users as usr1 on usr1.id=fd.razmen_to
					  inner JOIN users as usr2 on usr2.id=fd.razmen_from
						where fd.battle=".$bd['id']."
						 and
						 (usr1.hp > 0) and (usr2.hp > 0)
						 and
						((usr1.level - usr2.level) = 0 or
						 (usr1.level - usr2.level) = 1 or
						 (usr1.level - usr2.level) = -1 or
						 ( usr1.in_tower=3 AND usr2.in_tower=3)
						 )
						and (fd.time_blow+".$need_time.") <= UNIX_TIMESTAMP() LIMIT 1 ";
				
					$sqlusers=mysql_query($sqll);

				     //надо быбрать нужные размены которые время авто ответа за 60 сек до тайма
				     // и чары +/- 1 уровень - ну и соответственно в этом бою $bd['id']
				     /// тут надо иметь масив в которых подходящие разменя в этом бою
					     while($users = mysql_fetch_array($sqlusers))
					     {
					     		
					     // циклимся
					     // надо иметь ид того кто пропускает
					     // и ид того кто нападает
					     // загрузить все подобно клонам
					     ///выполнить размен согласно всем пунктам как в fbattle
					     // учесть что размен может быть
					    // echo "Ходим за ";

					//делаем актуализированый запрос юзера т.к. он мог уже смениться
					  $user=mysql_fetch_array(mysql_query("select * from users where id='{$users[usr1id]}' ;"));
					
					  
					  $my_wearItems=load_mass_items_by_id($user); // загрузка
					  $my_magicItems=$my_wearItems[incmagic]; // распаковка магии
					  
					  $data_battle=mysql_fetch_array(mysql_query("select * from battle where id='{$bd[id]}' ")); //запрашиваем актуальные данные
					  
					  $BSTAT[win]=$data_battle[win];


					 $real_enemy=mysql_fetch_array(mysql_query("select * from users where id='{$users[usr2id]}' ;"));
				
				          
				        
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 {
	//  $_POST[attack]=(int)($_POST[attack]);
	//  $_POST[defend]=(int)($_POST[defend]);
	//  $_POST[enemy]=(int)($_POST[enemy]);
// $attack=mt_rand(1,4);
 $attack=0;
// $defend=mt_rand(1,4);
 $defend=0; //отключили защиту
 
$output_attack_magic_dem=0;
$input_attack_magic_dem=0;

$input_fix_cost_level=0;
$output_fix_cost_level=0;
 
 	    // 1. проверим вражину на все чтоб было четко было у него ХП и он был де надо
	     $TABLE='users';
	     $HID='id';

	if (($real_enemy[id] > 0) AND ($real_enemy[hp]>0) AND ($user[id] > 0) AND ($user[hp]>0) AND ($data_battle[win]==3) AND ($data_battle[status]==0) AND ($data_battle[t1_dead]=='')  ) // проверка что все живы и бой идет
	{
	//echo "f 8 <br>";
	/// Да настоящий враг и живой
	// 2. проверить если размен от врага в базе разменов
	
     //фиксируем время размена на команду врага
    mysql_query("INSERT INTO `battle_user_time` SET `battle`='{$data_battle[id]}',`owner`='{$user[id]}',`timer{$real_enemy[battle_t]}`='".time()."' ON DUPLICATE KEY UPDATE `timer{$real_enemy[battle_t]}`='".time()."'");	
	$my_enemy_do[id]=$users[0];
	$my_enemy_do[battle]=$users[1];
	$my_enemy_do[owner]=$users[2];
	$my_enemy_do[razmen_to]=$users[3];
	$my_enemy_do[razmen_from]=$users[4];
	$my_enemy_do[attack]=$users[5];
	$my_enemy_do[attack2]=$users[6];	
	$my_enemy_do[block]=$users[7];
	$my_enemy_do[time_blow]=$users[8];
	$my_enemy_do[lab]=$users[9];


	      	 // да -  берем зоны и вызываем функцию хода
	      	 //проводка размена / ид_боя / идтого кто парвый походил / ид того кто ответил / зона удара первого / зона защиты первого /....
	      	 // данные о чарах имеют полные данны в масивах
		// функция чисто "математическая" - возвращает - текстовку для лога - возвращает - урон если был - и оцениный опыт за удар
		// загрузим в мемори шмотки врага
		////////////////////////////////////////////////////
		//загружаем ЕГО шмотки все кроме магий и подарков и всякой херни надо добавить
	$en_wearItems=load_mass_items_by_id($real_enemy); // загрузка
	
	
	//Загружаем боевые эфекты мои и врага если люди не боты
			if ($user[id] < _BOTSEPARATOR_ )
			{
				$user_eff=load_battle_eff($user,$data_battle);
			}
			if ($real_enemy[id] < _BOTSEPARATOR_ )
			{
			$real_enemy_eff=load_battle_eff($real_enemy,$data_battle);
			}
	
		//////////////////////////////////////////////////
		/// 1. атака / кто бил - кто защищался - куда били - где блок -защита юзера
		
		if ($my_enemy_do['attack2']>0)
			{
			$my_enemy_do_attack=array($my_enemy_do['attack'],$my_enemy_do['attack2']);
			}
			else
			{
			$my_enemy_do_attack=$my_enemy_do['attack'];
			}
		
		
		$input_attack=do_attack_in($data_battle,$user,$real_enemy,$my_enemy_do_attack,$defend,$my_wearItems,$en_wearItems,$user_eff,$real_enemy_eff,'from_fights');

		/// 2. защита / кто защищался / кто бил / куда били - где блок - нападаение юзера

		//ослабляем характеристики
		$my_wearItems[min_u]=$my_wearItems[min_u]*0.5; // 50% min урон отнаносимого урона
		$my_wearItems[max_u]=$my_wearItems[max_u]*0.5; //50%  max урон
		
		$my_wearItems[min_u2]=$my_wearItems[min_u2]*0.5; // 50% min урон отнаносимого урона
		$my_wearItems[max_u2]=$my_wearItems[max_u2]*0.5; //50%  max урон
		
		// можно ослабить также любые параметры


		$output_attack=do_attack_out($data_battle,$user,$real_enemy,$attack,$my_enemy_do[block],$my_wearItems,$en_wearItems,$user_eff,$real_enemy_eff,'from_fights');
	

		if ( trim($input_attack[text])!='' ) {	$input_attack[text]=$input_attack[text].":1" ; }
		if ( trim($output_attack[text]) !='' ) { $output_attack[text]=$output_attack[text].":1"; }


		///2.1 - тут делаем проводку - все проверки в функции SQL
		//
		//в размене против человека обновлеяем и таймер противника после размена
		mysql_query("INSERT INTO `battle_user_time` SET `battle`='{$data_battle[id]}',`owner`='{$real_enemy[id]}',`timer{$user[battle_t]}`='".time()."' ON DUPLICATE KEY UPDATE `timer{$user[battle_t]}`='".time()."'");			 

				$HH=(int)(date("H",time()));
			  	 if (($HH>=9) and ($HH<21)) 
				{
				//echo "День";
				//АУРА БЛАГОДАТИ
				if ($real_enemy[pasbaf]==852)
				 	{
			  		$trvkrit=-10;//10%
			  		}
			  	//	АУРА БАЛАНСА
			  		else if ($real_enemy[pasbaf]==861)
				 	{
			  		$trvkrit=-10;//10%
			  		}
			  		else
			  		{
			  		$trvkrit=0;
			  		}
				
				}
				else
				{
				//с 21:00 до 09:00
				//echo "Ночь";
			  	//АУРА ЯРОСТИ
				if ($user[pasbaf]==840)
			  		{
			  		$trvkrit=10;//+10%
			  		}
			  		else
			  		{
			  		$trvkrit=0;
			  		}
				}

	/*
	if ($data_battle[t3]!='')
		{
		$rez=mysql_fetch_array(mysql_query("select do_razmen({$data_battle[id]},{$user[id]},{$user[battle_t]},{$real_enemy[id]},{$real_enemy[battle_t]},{$output_attack[dem]},{$input_attack[dem]},'{$output_attack[type]}','{$input_attack[type]}','{$data_battle[type]}','{$trvkrit}') as ret;"));		
		}
		else
		{
		$rez=mysql_fetch_array(mysql_query("select do_razmen2({$data_battle[id]},{$user[id]},{$user[battle_t]},{$real_enemy[id]},{$real_enemy[battle_t]},{$output_attack[dem]},{$input_attack[dem]},'{$output_attack[type]}','{$input_attack[type]}','{$data_battle[type]}','{$trvkrit}') as ret;"));
		}
	*/	

	if (($user['id']==14897) OR ($real_enemy['id']==14897) )
		{
		addchp('<font color=red>Внимание!</font> Дебаг in fights 1'.$user['login'].'  VS '.$real_enemy['login'].' : DEM='.$input_attack['dem'],'{[]}Bred{[]}',-1,0);	
		}

		  $rez[0]=do_razmen_to_telo($data_battle,$user,$user['battle_t'],$real_enemy,$real_enemy['battle_t'],$output_attack['dem'],($input_attack['dem']-=$input_attack['stone']),$output_attack['type'],$input_attack['type'],$data_battle['type'],$trvkrit);				
	
		if (($user['id']==14897) OR ($real_enemy['id']==14897) )
		{
		addchp('<font color=red>Внимание!</font> Дебаг in fights 2'.$user['login'].'  VS '.$real_enemy['login'].' : DEM='.$input_attack['dem'],'{[]}Bred{[]}',-1,0);	
		}
	
	$USER_MANA=true;
	$ENEMY_MANA=true;
	
	if ($user['mana']<=0)
	{
	$USER_MANA=false;
	}
	
	if ($real_enemy['mana']<=0)
	{
	$ENEMY_MANA=false;
	}	

if ($data_battle[nomagic]==0)
{
////////////////////////////		//////////////////////////// //////////////////////////// //////////////////////////// //////////////////////////// //////////////////////////// ////////////////////////////
//930 обкаст воды - если не попал
if ((is_array($real_enemy_eff[930]) && $real_enemy['in_tower'] == 0) and ($input_attack[dem]==0) and ($ENEMY_MANA==true)  )// не в бс
		{
		//не было урона - значит нулим ему счетчик удачных попаданий
			mysql_query("UPDATE `effects` SET add_info='0'  where owner='{$real_enemy[id]}' AND type=930");
		}
		else
//930 обкаст воды
if ((is_array($real_enemy_eff[930]) && $real_enemy['in_tower'] == 0) and ($input_attack[dem]>0) and ($ENEMY_MANA==true)  )// не в бс
				{
				//делаем +1 если не больше 4 есть
				if ($real_enemy_eff[930]['add_info']<4)
					{
					mysql_query("UPDATE `effects` SET add_info=add_info+1  where owner='{$real_enemy[id]}' AND type=930");
					}
				
				$prhp=$user[hp]-$input_attack[dem];
				
				//костыль 
				if (($real_enemy_eff[930]['lastup']>0) AND ($real_enemy['mwater']<3))
					{
					$real_enemy['mwater']=(int)($real_enemy_eff[930]['lastup']);
					}
				
				if (($prhp>0) AND ($real_enemy['mwater']>0))
				{ 
					//есть урон врага и у него баф
					if ($real_enemy['mwater']>100) { $real_enemy['mwater']=100; }
					
					$real_enemy_mudra=round($real_enemy['mudra']);
					
					if ($real_enemy_mudra>50) {$real_enemy_mudra=50;}

					$baf_dem_min=(int)($real_enemy_mudra+$real_enemy['mwater']) ; 
					
					$baf_dem_max=(int)($real_enemy['mwater']*10)  ; //каждая умелка = максимальному урону
					
					if ($baf_dem_min>1000) {$baf_dem_min=1000; }				
					if ($baf_dem_max>1000) {$baf_dem_max=1000; }
					
					if ($baf_dem_max<1) {$baf_dem_max=1;}
					if ($baf_dem_min<1) {$baf_dem_min=1;}
					
					if ($baf_dem_min>$baf_dem_max) {$baf_dem_min=$baf_dem_max; }					
					
					$baf_dem_baza=mt_rand($baf_dem_min,$baf_dem_max); //100% 
					
					if (!(in_array(4,get_mag_stih($real_enemy,$real_enemy_eff)))) { $baf_dem_baza=(int)($baf_dem_baza*0.5); } // -50% если знак зодиака не совпал
					
					$koluda=(int)($real_enemy_eff[930]['add_info']); // тут хранится счетчик ударов удачных подряд
					
					$demag[0][0]=1; //при первом удачном попадании наносит урон воды 100% по противнику
					$demag[1][0]=0.5; $demag[1][1]=0.5; //при втором (подряд если попал) первому наности 50% и рандомно +- левел 50%
					$demag[2][0]=0.5; $demag[2][1]=0.25; $demag[2][2]=0.25; //при третьем (подряд опять же) первому наносит 50% второму рандомно 25% третьему 25%
					$demag[3][0]=0.5; $demag[3][1]=0.1;   $demag[3][2]=0.2; $demag[4][3]=0.3; //при четвертом (подряд) первому 50% второму 30% третьему 20% четвертому 10%
					$demag[4][0]=0.4; $demag[4][1]=0.1;   $demag[4][2]=0.1; $demag[4][3]=0.2;  $demag[4][4]=0.3; //	при пятом ударе (подряд) первому 40% второму 30% третьему 20% четверотому и пятому по 10%
					
					$demaga=$demag[$koluda]; //вытягиваем  нужный масив для количества удачных попадаений
					
					
					$baf_dem=round($baf_dem_baza*$demaga[0]); // [0] = удар для первой жертвы

					if (is_array($user_eff[557])) $baf_dem = round($baf_dem * $user_eff[557]['add_info']); // снимаем 30% при защите от травм
				
					if ($baf_dem<$prhp) {$prhp-=$baf_dem;}else {$prhp=0;}
				
					if ($prhp>0)
						{
						mysql_query("UPDATE users set hp=hp-'{$baf_dem}' where id='{$user[id]}' and hp>0 LIMIT 1; ");
						}
						else
						{
						mysql_query("UPDATE users set hp=0 where id='{$user[id]}' and hp>0 LIMIT 1; ");
						}
						
					if (mysql_affected_rows()>0)
						{
						$uron_str=$baf_dem;
						//hidden приготовление
						if (($user[hidden] > 0) and ($user[hiddenlog] ==''))
 		               			{   $txtdm='[??/??]';  $uron_str=$baf_dem."|??";   } else  {  $txtdm='['.$prhp.'/'.$user[maxhp].']';    }
						$input_attack[text].="\n!:L:".time().":".nick_new_in_battle($user).":".(220+$user[sex])."::".nick_new_in_battle($real_enemy).":::::".$uron_str.":".$txtdm;
						
						$input_attack_magic_dem=$baf_dem;//пишем в маг урон
						
						if ($prhp<=0)
							{
							$input_attack[text].="\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user);
				
								if (!isset($real_enemy[id_user]) AND ($user['level'] >= ($real_enemy['level']-1)) AND ($data_battle['type'] == 40 || $data_battle['type'] == 41 || $data_battle['type'] == 61 ) )
								{

									if (!isset($user_eff[5577]) || $user_eff[5577]['add_info'] < 10) {
										if(!isset($user_eff[5577])) {
											mysql_query('INSERT INTO `effects` (`type`,`name`,`owner`,`time`,`add_info`) VALUES(5577,"Противостояние - черепа","'.$user['id'].'",1999999999,1) ');
										} else {
											mysql_query('UPDATE `effects` SET add_info = '.($user_eff[5577]['add_info']+1).' WHERE type = 5577 AND owner = '.$user['id']);
										}
	
										mysql_query('INSERT INTO `op_battle_index` (`battle`,`owner`,`value`,`team`) 
										VALUES(
											'.$data_battle['id'].',
											'.$real_enemy['id'].',
											1,
											'.$real_enemy['battle_t'].'
										) 
										ON DUPLICATE KEY UPDATE
											`value` = `value` + 1');
									}
								}
								elseif (!isset($real_enemy[id_user]) and ( $data_battle['type'] ==150 || $data_battle['type'] ==140 ) )
								{
								//клановые битвы
								get_kv_bonus($real_enemy);
								}
							}
							
							
						if ($koluda>0) // если  были уже удачные удары
							{
							//ищем подходящие цели для  волн
							$memorize=array();
							$memorize[]=$user['id']; // запоминаем ид
							$filt='';
							 for ($io=0;$io<count($demaga);$io++)
							 {
										 if (count($memorize)>0)
										{
										$filt=" and users.`id` not in  (".implode(",",$memorize).")  " ; //исключаем тех по ком прошла волна
										}
						 		$demaga[$io]=round($baf_dem_baza*$demaga[$io]);	 //силу урона 
							 	$pesh=mysql_fetch_array(mysql_query("select users.*,effects.type,effects.add_info from users LEFT JOIN effects ON users.id = effects.owner and effects.type = 557 where users.battle='{$data_battle['id']}' and battle_t='{$user['battle_t']}' and (level>=".($real_enemy['level']-1)." and level<=".($real_enemy['level']+1).")   and hp>'{$demaga[$io]}' ".$filt." order by Rand() limit 1 ;"));
							 	if ($pesh['id']>0)
							 		{

									if ($pesh['type'] == 557) $demaga[$io] = round($demaga[$io] * $pesh['add_info']); // 30% защита от травм

							 		mysql_query("UPDATE users set hp=hp-'{$demaga[$io]}' where id='{$pesh['id']}' and hp>'{$demaga[$io]}'  ");
								 		if (mysql_affected_rows()>0)
								 				{
									 				$memorize[]=$pesh['id']; // запоминаем ид
			 										$uron_str=$demaga[$io];
			 										$pesh['hp']-=$demaga[$io];
													if (($pesh[hidden] > 0) and ($pesh[hiddenlog] ==''))
							 		               			{   $txtdm='[??/??]';  $uron_str=$demaga[$io]."|??";   } else  {  $txtdm='['.$pesh['hp'].'/'.$pesh['maxhp'].']';    }
													$input_attack[text].="\n!:L:".time().":".nick_new_in_battle($pesh).":".(220+$pesh['sex'])."::".nick_new_in_battle($real_enemy).":::::".$uron_str.":".$txtdm;
													$input_attack_magic_dem+=$demaga[$io];//пишем в маг урон
								 				}
							 		}
							 		else
							 		{
							 		break ; // нет смысла дальше искать
							 		}
							 }
							}
						
						set_telo_mana($real_enemy,$input_attack_magic_dem);
						}
				}
			}
else
//920 - обкаст земли
if ((is_array($real_enemy_eff[920]) && $real_enemy['in_tower'] == 0) and ($input_attack[dem]>0) and ($ENEMY_MANA==true) )// не в бс
				{
				$prhp=$user[hp]-$input_attack[dem];
				
				//костыль 
				if (($real_enemy_eff[920]['lastup']>0) AND ($real_enemy['mearth']<1))
					{
					$real_enemy['mearth']=(int)($real_enemy_eff[920]['lastup']);
					}
				
				if (($prhp>0) AND ($real_enemy['mearth']>0))
				{ 
					//есть урон врага и у него баф
					if ($real_enemy['mearth']>100) { $real_enemy['mearth']=100; }
					
					$real_enemy_mudra=round($real_enemy['mudra']);
					
					if ($real_enemy_mudra>50) {$real_enemy_mudra=50;}

					$baf_dem_min=(int)($real_enemy_mudra/5) ; //5 мудросит - 1 минимальный урон
					$baf_dem_max=(int)($real_enemy['mearth'])  ; //каждая умелка = максимальному урону
					
					if ($baf_dem_min>1000) {$baf_dem_min=1000; }				
					if ($baf_dem_max>1000) {$baf_dem_max=1000; }
					
					if ($baf_dem_min<1) {$baf_dem_min=1; }
					if ($baf_dem_max<1) {$baf_dem_max=1;}
					

					if ($baf_dem_min>$baf_dem_max) {$baf_dem_min=$baf_dem_max; }					
					
					$baf_dem=mt_rand($baf_dem_min,$baf_dem_max); // урон
					
					if (!(in_array(2,get_mag_stih($real_enemy,$real_enemy_eff)))) { $baf_dem=(int)($baf_dem*0.5); } // -50% если знак зодиака не совпал
					$baf_dem=(int)($baf_dem*0.5);
					if ($baf_dem < 1) $baf_dem = 1;


					if (is_array($user_eff[557])) $baf_dem = round($baf_dem * $user_eff[557]['add_info']); // снимаем 30% при защите от травм
					
					/* старый вариант	
					//ищем подходящие цели  для яда
					$get_all_life=mysql_query("select * from users where battle='{$data_battle['id']}' and battle_t='{$user['battle_t']}' and hp>'{$baf_dem}'  ;");
					$all_cast=mysql_num_rows($get_all_life);
						
					if ($all_cast>0)
							{	
							$mass_magic_data='';
							$uron_str=0;
							$uron_str_all=0;
							$all_render=0;
							while($pesh = mysql_fetch_array($get_all_life))							
							 {
							 	if ($pesh['id']>0)
							 		{
							 		mysql_query("UPDATE users set hp=hp-'{$baf_dem}' where id='{$pesh['id']}' and hp>'{$baf_dem}'  ");
								 		if (mysql_affected_rows()>0)
								 				{
								 				$all_render++;
			 										$uron_str=$baf_dem;
			 										$pesh['hp']-=$baf_dem;
			 										$uron_str_all+=$baf_dem; // общий урон по всем для статистики
													if (($pesh[hidden] > 0) and ($pesh[hiddenlog] ==''))
							 		               			{   $txtdm='[??/??]';  $uron_str=$baf_dem."|??";   } else  {  $txtdm='['.$pesh['hp'].'/'.$pesh['maxhp'].']';    }
													$mass_magic_data.=nick_new_in_battle($pesh)."#".(220+$pesh['sex'])."#".$uron_str."#".$txtdm."#";
													$input_attack_magic_dem+=$baf_dem;//пишем в маг урон - для подсчета опыта
								 				}
							 		}
							 }
							 
							$input_attack[text].="\n!:J:".time().":".$mass_magic_data.":".$all_render."::".nick_new_in_battle($real_enemy).":::::".$uron_str_all.":";
							 }
					*/
					if ($baf_dem>0) {
					
							//загрузка всех у кого защита от магии
							$owners_all_p=array();
							$owners_magp=array();
							$sql_protectmag="";
							
							$get_all_magprot=mysql_query("SELECT * FROM effects WHERE type = 557 and battle='{$data_battle['id']}'");
							while($puser = mysql_fetch_array($get_all_magprot))
								{
								$owners_all_p[]=$puser['owner']; //ид всех у кого защита магии
								$owners_magp[strval($puser['add_info'])][]=$puser['owner']; // ид по группам защиты
								}
						
							if (count($owners_all_p)>0)
								{
									$sql_protectmag="  and id not in (".implode(",",$owners_all_p).")  ";
								}

						//все кроме защитных
						mysql_query_100("UPDATE users set hp=hp-'{$baf_dem}' where battle='{$data_battle['id']}' and battle_t='{$user['battle_t']}' and hp>'{$baf_dem}' ".$sql_protectmag);
						$all_render1 = mysql_affected_rows();

						//обработка тех у кого есть защита
						$all_render_porotect=array();
						$total_summ_all_pip=0;
						
						if (count($owners_magp)>0)
						{
						foreach ($owners_magp as $protect => $powners)
							{
								$pdmage=round($baf_dem*$protect);
								$all_pip=0;
								mysql_query_100("UPDATE users set hp=hp-'".$pdmage."' where battle='{$data_battle['id']}' and battle_t='{$user['battle_t']}' and hp>'".$pdmage."' and id in (".implode(",",$powners).") ");
								$all_pip = mysql_affected_rows();	
								if ($all_pip>0)
									{
									$all_render_porotect[]="\n!:G:".time().":".$pdmage.":".$all_pip.":".$user['battle_t'].":".nick_new_in_battle($real_enemy).":::::".$pdmage*$all_pip.":";
									$total_summ_all_pip+=$pdmage*$all_pip;
									}
							}
						}	

				 		if ($all_render1 >0 || count($all_render_porotect)>0 ) 
				 		{
							if($all_render1 >0) {
								$input_attack['text'].="\n!:G:".time().":".$baf_dem.":".$all_render1.":".$user['battle_t'].":".nick_new_in_battle($real_enemy).":::::".$baf_dem*$all_render1.":";
							}
						
							if (count($all_render_porotect)>0)
							{
								foreach ($all_render_porotect as $k => $render)
									{
									$input_attack['text'].=$render;
									}
							}

							$uron_str_all = round(($all_render1*$baf_dem)+($pdmage*$all_pip));

							//Подлый удар
							$input_fix_cost_level=1; // флаг для фикса маг урона
							$input_attack_magic_dem+=$uron_str_all;//пишем в маг урон - для подсчета опыта
			 			}
			 			set_telo_mana($real_enemy,$input_attack_magic_dem);
			 		}
							
				}
			}
		else
//обкаст воздуха разряд молнии - при аттаке противника
			if ((is_array($real_enemy_eff[130]) && $real_enemy['in_tower'] == 0) and ($input_attack[dem]>0) and ($ENEMY_MANA==true) )// не в бс
				{
				$prhp=$user[hp]-$input_attack[dem];
				
				//костыль 
				if (($real_enemy_eff[130]['lastup']>0) AND ($real_enemy['mair']<3))
					{
					$real_enemy['mair']=(int)($real_enemy_eff[130]['lastup']);
					}
				
				if (($prhp>0) AND ($real_enemy['mair']>0))
				{ 
					//есть урон врага и у него баф
					if ($real_enemy['mair']>100) { $real_enemy['mair']=100; }

					$real_enemy_mudra=$real_enemy['mudra'];
					
					if ($real_enemy_mudra>50) {$real_enemy_mudra=50; }
					
					$baf_dem_min=round(($real_enemy_mudra)+$real_enemy['mair']);
					
					$vl=explode(":",$real_enemy_eff[130]['add_info']);
					$real_enemy_eff[130]['add_info']=$vl[1];
					
					$baf_dem_max=((int)($real_enemy_eff[130]['add_info'])*$real_enemy['mair'])  ;
					
					if ($baf_dem_min>1000) {$baf_dem_min=1000; }				
					if ($baf_dem_max>1000) {$baf_dem_max=1000; }

					if ($baf_dem_min>$baf_dem_max) {$baf_dem_min=$baf_dem_max; }					
					
					$baf_dem_baza=mt_rand($baf_dem_min,$baf_dem_max);
					
					if (!(in_array(3,get_mag_stih($real_enemy,$real_enemy_eff)))) { $baf_dem_baza=(int)($baf_dem_baza*0.5); } // -50% если знак зодиака не совпал
					
					$baf_dem=round($baf_dem_baza*0.4); //но при попаднии противник в которго я подаю получает 40% урона 
					
					$baf_dem_f[1]=round($baf_dem_baza*0.3);//трое других рандомно взятые +- левел получают 30% 20% и 10% соотвественно (итого 100%)
					$baf_dem_f[2]=round($baf_dem_baza*0.2);
					$baf_dem_f[3]=round($baf_dem_baza*0.1);
					
					if (is_array($user_eff[557])) $baf_dem = round($baf_dem * $user_eff[557]['add_info']); // снимаем 30% при защите от травм					
				
					if ($baf_dem<$prhp) {$prhp-=$baf_dem;}else {$prhp=0;}
				
					if ($prhp>0)
						{
						mysql_query("UPDATE users set hp=hp-'{$baf_dem}' where id='{$user[id]}' and hp>0 LIMIT 1; ");
						}
						else
						{
						mysql_query("UPDATE users set hp=0 where id='{$user[id]}' and hp>0 LIMIT 1; ");
						}
						
					if (mysql_affected_rows()>0)
						{
						$uron_str=$baf_dem;
						//hidden приготовление
						if (($user[hidden] > 0) and ($user[hiddenlog] ==''))
 		               			{   $txtdm='[??/??]';  $uron_str=$baf_dem."|??";   } else  {  $txtdm='['.$prhp.'/'.$user[maxhp].']';    }
						$input_attack[text].="\n!:Z:".time().":".nick_new_in_battle($user).":".(220+$user[sex])."::".nick_new_in_battle($real_enemy).":::::".$uron_str.":".$txtdm;
						
						$input_attack_magic_dem=$baf_dem;//пишем в маг урон
						
						if ($prhp<=0)
							{
							$input_attack[text].="\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user);
				
								if (!isset($real_enemy[id_user]) AND ($user['level'] >= ($real_enemy['level']-1)) AND ($data_battle['type'] == 40 || $data_battle['type'] == 41 || $data_battle['type'] == 61 ) )
								{

									if (!isset($user_eff[5577]) || $user_eff[5577]['add_info'] < 10) {
										if(!isset($user_eff[5577])) {
											mysql_query('INSERT INTO `effects` (`type`,`name`,`owner`,`time`,`add_info`) VALUES(5577,"Противостояние - черепа","'.$user['id'].'",1999999999,1) ');
										} else {
											mysql_query('UPDATE `effects` SET add_info = '.($user_eff[5577]['add_info']+1).' WHERE type = 5577 AND owner = '.$user['id']);
										}
	
										mysql_query('INSERT INTO `op_battle_index` (`battle`,`owner`,`value`,`team`) 
										VALUES(
											'.$data_battle['id'].',
											'.$real_enemy['id'].',
											1,
											'.$real_enemy['battle_t'].'
										) 
										ON DUPLICATE KEY UPDATE
											`value` = `value` + 1');
									}
								}
								elseif (!isset($real_enemy[id_user]) and ( $data_battle['type'] ==150 || $data_battle['type'] ==140 ) )
								{
								//клановые битвы
								get_kv_bonus($real_enemy);
								}
							}
							
							//ищем подходящие цели для молний
							$memorize=array();
							$memorize[]=$user['id']; // запоминаем ид
							$filt='';
							 for ($io=1;$io<=3;$io++)
							 {
										 if (count($memorize)>0)
										{
										$filt=" and users.`id` not in  (".implode(",",$memorize).")  " ; //исключаем тех по ком прошла молния
										}
										
							 	$pesh=mysql_fetch_array(mysql_query("select users.*,effects.type,effects.add_info from users LEFT JOIN effects ON users.id = effects.owner and effects.type = 557 where users.battle='{$data_battle['id']}' and battle_t='{$user['battle_t']}' and hp>'{$baf_dem_f[$io]}' ".$filt." order by Rand() limit 1 ;"));
							 	if ($pesh['id']>0)
							 		{

									if ($pesh['type'] == 557) $baf_dem_f[$io] = round($baf_dem_f[$io] * $pesh['add_info']); // 30% защиты от травм

							 		mysql_query("UPDATE users set hp=hp-'{$baf_dem_f[$io]}' where id='{$pesh['id']}' and hp>'{$baf_dem_f[$io]}'  ");
								 		if (mysql_affected_rows()>0)
								 				{
									 				$memorize[]=$pesh['id']; // запоминаем ид
			 										$uron_str=$baf_dem_f[$io];
			 										$pesh['hp']-=$baf_dem_f[$io];
													if (($pesh[hidden] > 0) and ($pesh[hiddenlog] ==''))
							 		               			{   $txtdm='[??/??]';  $uron_str=$baf_dem_f[$io]."|??";   } else  {  $txtdm='['.$pesh['hp'].'/'.$pesh['maxhp'].']';    }
													$input_attack[text].="\n!:Z:".time().":".nick_new_in_battle($pesh).":".(220+$pesh['sex'])."::".nick_new_in_battle($real_enemy).":::::".$uron_str.":".$txtdm;
													$input_attack_magic_dem+=$baf_dem_f[$io];//пишем в маг урон
								 				}
							 		}
							 		else
							 		{
							 		break ;
							 		}
							 }
							
						
						set_telo_mana($real_enemy,$input_attack_magic_dem);
						}
				}
			}
			else
	///тут яд ареса - делаем только на инпут т.к.  атака у авто удара 0
	//Магия Яд Ареса- при аттаке противника
			if ((is_array($real_enemy_eff[150]) && $real_enemy['in_tower'] == 0) and ($input_attack[dem]>0) and ($ENEMY_MANA==true)  )// не в бс
				{
				$prhp=$user[hp]-$input_attack[dem];
				
				//костыль 
				if (($real_enemy_eff[150]['lastup']>0) AND ($real_enemy['mfire']<3))
					{
					$real_enemy['mfire']=(int)($real_enemy_eff[150]['lastup']);
					}
				
				if (($prhp>0) AND ($real_enemy[mfire]>0))
				{ 
					//есть урон врага и у него баф
					if ($real_enemy[mfire]>100) { $real_enemy[mfire]=100; }
					
					$real_enemy_mudra=$real_enemy[mudra];
					
					if ($real_enemy_mudra>50) {$real_enemy_mudra=50; }
					
					$baf_dem_min=round(($real_enemy_mudra)+$real_enemy[mfire]);
					
					$vl=explode(":",$real_enemy_eff[150]['add_info']);
					$real_enemy_eff[150]['add_info']=$vl[1];					
					
					$baf_dem_max=((int)($real_enemy_eff[150][add_info])*$real_enemy[mfire])  ;
					
					if ($baf_dem_min>1000) {$baf_dem_min=1000; }				
					if ($baf_dem_max>1000) {$baf_dem_max=1000; }
					
					if ($baf_dem_min<1) {$baf_dem_min=1; }				
					if ($baf_dem_max<1) {$baf_dem_max=1; }					

					if ($baf_dem_min>$baf_dem_max) {$baf_dem_min=$baf_dem_max; }					
					
					$baf_dem=mt_rand($baf_dem_min,$baf_dem_max);
					if (!(in_array(1,get_mag_stih($real_enemy,$real_enemy_eff)))) { $baf_dem=(int)($baf_dem*0.5) ;  } // -50% если знак зодиака не совпал					

					if (is_array($user_eff[557])) $baf_dem = round($baf_dem * $user_eff[557]['add_info']); // снимаем 30% при защите от травм
				
					if ($baf_dem<$prhp) {$prhp-=$baf_dem;}else {$prhp=0;}
				
					if ($prhp>0)
						{
						mysql_query("UPDATE users set hp=hp-'{$baf_dem}' where id='{$user[id]}' and hp>0 ; ");
						}
						else
						{
						mysql_query("UPDATE users set hp=0 where id='{$user[id]}' and hp>0 ; ");
						}
						
					if (mysql_affected_rows()>0)
						{
						
						$uron_str=$baf_dem;
						//hidden приготовление
						if (($user[hidden] > 0) and ($user[hiddenlog] ==''))
 		               			{   $txtdm='[??/??]';  $uron_str=$baf_dem."|??";   } else  {  $txtdm='['.$prhp.'/'.$user[maxhp].']';    }
						$input_attack[text].="\n!:Y:".time().":".nick_new_in_battle($user).":".(220+$user[sex])."::".nick_new_in_battle($real_enemy).":::::".$uron_str.":".$txtdm;
						
						if ($prhp<=0)
							{
							$input_attack[text].="\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user);
							
							if (!isset($real_enemy[id_user]) AND ($user['level'] >= ($real_enemy['level']-1)) AND ($data_battle['type'] == 40 || $data_battle['type'] == 41 || $data_battle['type'] == 61 ) )
								{

									if (!isset($user_eff[5577]) || $user_eff[5577]['add_info'] < 10) {
										if(!isset($user_eff[5577])) {
											mysql_query('INSERT INTO `effects` (`type`,`name`,`owner`,`time`,`add_info`) VALUES(5577,"Противостояние - черепа","'.$user['id'].'",1999999999,1) ');
										} else {
											mysql_query('UPDATE `effects` SET add_info = '.($user_eff[5577]['add_info']+1).' WHERE type = 5577 AND owner = '.$user['id']);
										}
	
										mysql_query('INSERT INTO `op_battle_index` (`battle`,`owner`,`value`,`team`) 
										VALUES(
											'.$data_battle['id'].',
											'.$real_enemy['id'].',
											1,
											'.$real_enemy['battle_t'].'
										) 
										ON DUPLICATE KEY UPDATE
											`value` = `value` + 1');
									}
								}
								elseif (!isset($real_enemy[id_user]) and ( $data_battle['type'] ==150 || $data_battle['type'] ==140 ) )
								{
								//клановые битвы
								get_kv_bonus($real_enemy);
								}
							
							}
						
						$input_attack_magic_dem=$baf_dem;
						set_telo_mana($real_enemy,$input_attack_magic_dem);
						}
				}
				}
	}//$data_battle[nomagic]
	



	$STING='REZ'.$rez[0];
		switch ($STING)
	     {
		case "REZ11":
		{
		//echo "11";
		// оба живые текстуем в лог
		addlog($data_battle[id],$input_attack[text]."\n".$output_attack[text]."\n");

		//отнимаем в мемори
		$user[hp]-=$input_attack[dem];
		// отнимаем в мемори
		if ($user[battle_t]==1) 
			{ 
			$boec_t1[$user[id]][hp]-=$input_attack[dem];
			} 
			elseif ($user[battle_t]==2) 
			{
			$boec_t2[$user[id]][hp]-=$input_attack[dem] ;
			}
			elseif ($user[battle_t]==3) 
			{
			$boec_t3[$user[id]][hp]-=$input_attack[dem] ;
			}			
			
		if ($real_enemy[battle_t]==1) 
			{$boec_t1[$real_enemy[id]][hp]-=$output_attack[dem];
			} 
			elseif ($real_enemy[battle_t]==2) 
			 {$boec_t2[$real_enemy[id]][hp]-=$output_attack[dem] ;}
			elseif ($real_enemy[battle_t]==3) 
			 {$boec_t3[$real_enemy[id]][hp]-=$output_attack[dem] ;}			 
			 
/////////////// комментируем - комментатор работает если размен нормальный - и нихто не умер после него
			if (mt_rand(1,10)==5) // частота комментатора
			{
		if ($battle_data['type']==20)
						{
						addlog($data_battle[id],get_comment_fifa()."\n"); // комментатор аля футбольный
						}
						else
						{
						addlog($data_battle[id],get_comment()."\n"); // комментатор обыный
						}
			}
		}
		break;

		case "REZ01":
		 {
		// echo "01";
		
		addlog($data_battle[id],$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user)."\n");

 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($input_attack[type]=='krit') OR ($input_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$user[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
	 		addlog($data_battle[id],"!:T:".time().":".nick_new_in_battle($user).":".$user[sex].":".$eff[name]."\n");
	 		 }
 		 	}

		//отнимаем в мемори
		$user[hp]=0;
		$STEP = 4;
		// отнимаем в мемори
		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2)
		 {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3)
		 {unset($boec_t3[$user[id]]);}

		}
		break;


		case "REZ10":
		 {
 		// echo "10";
		 //  наоборот

		addlog($data_battle[id],$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n");		

 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($output_attack[type]=='krit') OR ($output_attack[type]=='krita'))
 		 	{
 		 	$sexi[0]='а';$sexi[1]='';
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$real_enemy[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
	 		addlog($data_battle[id],"!:T:".time().":".nick_new_in_battle($real_enemy).":".$real_enemy[sex].":".$eff[name]."\n");	 		
	 			}
 		 	}


		//отнимаем в мемори /
		$user[hp]-=$input_attack[dem];
		// отнимаем в мемори
		if ($user[battle_t]==1) {$boec_t1[$user[id]][hp]-=$input_attack[dem];} 
		elseif ($user[battle_t]==2)
		 {$boec_t2[$user[id]][hp]-=$input_attack[dem] ;}
		elseif ($user[battle_t]==3)
		 {$boec_t3[$user[id]][hp]-=$input_attack[dem] ;}

		 
		// обрабатываем врага
		if ($real_enemy[battle_t]==1) {unset($boec_t1[$real_enemy[id]]);} 
		elseif ($real_enemy[battle_t]==2)
		 {unset($boec_t2[$real_enemy[id]]) ;}
		elseif ($real_enemy[battle_t]==3)
		 {unset($boec_t3[$real_enemy[id]]) ;}		 
		 }
		break;


		case "REZ00":
		 {
 		// echo "00";
		 // обатрупы - но бой идет дальше

		addlog($data_battle[id],$input_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user)."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n");	
 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($input_attack[type]=='krit') OR ($input_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$user[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
			addlog($data_battle[id],"!:T:".time().":".nick_new_in_battle($user).":".$user[sex].":".$eff[name]."\n");
	 				}
 		 	}

 		addlog($data_battle[id],"!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n"); 		
 		
 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($output_attack[type]=='krit') OR ($output_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$real_enemy[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
			 		addlog($data_battle[id],"!:T:".time().":".nick_new_in_battle($real_enemy).":".$real_enemy[sex].":".$eff[name]."\n");
	 				}
 		 	}

		//отнимаем в мемори
		$user[hp]=0;
		 $STEP = 4;
		// отнимаем в мемори
		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2) {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3) {unset($boec_t3[$user[id]]);}
		
		// враг
		if ($real_enemy[battle_t]==1) {unset($boec_t1[$real_enemy[id]]);} 
		elseif ($real_enemy[battle_t]==2) {unset($boec_t2[$real_enemy[id]]) ;}
		elseif ($real_enemy[battle_t]==3) {unset($boec_t3[$real_enemy[id]]) ;}		
		 }
		break;


		case "REZ1010":
		{
		// echo "1010";
		// последний размен убил последнего врага победа команды юзера
		$fin_add_log=$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n";		

 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($output_attack[type]=='krit') OR ($output_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$real_enemy[id]} and (type=11 OR type=12 OR type=13 OR type=14) ;"));
 		 	 if ($eff[id] > 0 ) {
	 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($real_enemy).":".$real_enemy[sex].":".$eff[name]."\n";	 				
	 				}
 		 	}


 		############################################
 		$win_team_hist='t'.$user[battle_t].'hist';
 		$fin_add_log.="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";		
		
 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
 		if (mysql_affected_rows()>0)
	 			{
	 			//если апдейт прошел то пишем
		 		addlog($data_battle[id],$fin_add_log);
		 		}
 		////////////////////////

		//тут ставим флаг финал боя = победа команды $user[battle_t];
		$BSTAT[win]=($user[battle_t]==3?4:$user[battle_t]);
 		############################################
		//отнимаем в мемори / данные врага не трогаем т.к. уже не надо :)
		$user[hp]-=$input_attack[dem];
		// отнимаем в мемори
		if ($user[battle_t]==1) {$boec_t1[$user[id]][hp]-=$input_attack[dem];} 
		elseif ($user[battle_t]==2) {$boec_t2[$user[id]][hp]-=$input_attack[dem] ;}
		elseif ($user[battle_t]==3) {$boec_t3[$user[id]][hp]-=$input_attack[dem] ;}
		
		if ($real_enemy[battle_t]==1) {unset($boec_t1[$real_enemy[id]]);} 
		elseif ($real_enemy[battle_t]==2) {unset($boec_t2[$real_enemy[id]]) ;}
		elseif ($real_enemy[battle_t]==3) {unset($boec_t3[$real_enemy[id]]) ;}
		$STEP = 5;
		}
		break;



		case "REZ0101":
		{
		// echo "0101";
		// последний размен убил юзера но не врага победа тимы врага
		$fin_add_log=$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user)."\n";

 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($input_attack[type]=='krit') OR ($input_attack[type]=='krita'))
 		 	{

 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$user[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
	 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($user).":".$user[sex].":".$eff[name]."\n";
	 				}
 		 	}

 		############################################
 		$win_team_hist='t'.$real_enemy[battle_t].'hist';
 		$fin_add_log.="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";

			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////

		//тут ставим флаг финал боя = победа команды $real_enemy;
		$BSTAT[win]=($real_enemy[battle_t]==3?4:$real_enemy[battle_t]);
		
 		############################################
		//отнимаем в мемори
		$user[hp]=0;
		// отнимаем в мемори
		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2)	 {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3)	 {unset($boec_t3[$user[id]]);}

		 $STEP = 5;
		}
		break;



		case "REZ0001":
		{
		// echo "0001";
		// юзер погиб последним  и враг погиб команда врага победила т.к. остались живые
		$fin_add_log=$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user)."\n";		
		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($input_attack[type]=='krit') OR ($input_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$user[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
		 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($user).":".$user[sex].":".$eff[name]."\n";
	 				}
 		 	}

		$fin_add_log.="!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n";
		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($output_attack[type]=='krit') OR ($output_attack[type]=='krita'))
 		 	{

 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$real_enemy[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
	 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($real_enemy).":".$real_enemy[sex].":".$eff[name]."\n";
	 				}
 		 	}

 		############################################
 		$win_team_hist='t'.$real_enemy[battle_t].'hist';
 		$fin_add_log.="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////

		//тут ставим флаг финал боя = победа команды $real_enemy;
		$BSTAT[win]=($real_enemy[battle_t]==3?4:$real_enemy[battle_t]);
 		############################################
		//отнимаем в мемори
		$user[hp]=0;
		// отнимаем в мемори
		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2) {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3) {unset($boec_t2[$user[id]]);}		

		// врага не трогаем т.к. пофигу
		 $STEP = 5;
		}
		break;



		case "REZ0010":
		{
		// echo "0010";
		// юзер погиб и враг погиб последним  команда юзера победила т.к. остались живые
		$fin_add_log=$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user)."\n";
 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($input_attack[type]=='krit') OR ($input_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$user[id]} and (type=11 OR type=12 OR type=13 OR type=14) ;"));
 		 	 if ($eff[id] > 0 ) {
	 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($user).":".$user[sex].":".$eff[name]."\n";
	 				}
 		 	}

 		$fin_add_log.="!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n";
 		// юзер труп получил последним крит или крит через блок - проверим подхватил ли травму
 		 if (($output_attack[type]=='krit') OR ($output_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$real_enemy[id]} and (type=11 OR type=12 OR type=13 OR type=14)  ;"));
 		 	 if ($eff[id] > 0 ) {
	 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($real_enemy).":".$real_enemy[sex].":".$eff[name]."\n";
			 		}
 		 	}

 		############################################
 		$win_team_hist='t'.$user[battle_t].'hist';
 		$fin_add_log.="!:F:".time().":0:".$data_battle[$win_team_hist]."\n";
 		
			 		//ставим зарубку в бой о том что сделан последний лог-шоб не дублировать
			 		mysql_query("update battle set t1_dead='finlog' where id={$data_battle[id]} and t1_dead='' ;");
			 		if (mysql_affected_rows()>0)
			 			{
			 			//если апдейт прошел то пишем
				 		addlog($data_battle[id],$fin_add_log);
				 		}
				 	////////////////////////

		//тут ставим флаг финал боя = победа команды $user;
		$BSTAT[win]=($user[battle_t]==3?4:$user[battle_t]);
 		############################################
		//отнимаем в мемори
		$user[hp]=0;
		// отнимаем в мемори
		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2)  {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3)  {unset($boec_t3[$user[id]]);}		
		// врага не трогаем т.к. пофигу
		 $STEP = 5;
		}
		break;

		case "REZ0000":
		{
		// echo "0000";
		// вышла ничья в последних разменах
		$fin_add_log=$input_attack[text]."\n".$output_attack[text]."\n"."!:D:".time().":".nick_new_in_battle($user).":".get_new_dead($user)."\n";

		 if (($input_attack[type]=='krit') OR ($input_attack[type]=='krita'))
 		 	{
 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$user[id]} and (type=11 OR type=12 OR type=13 OR type=14) ;"));
 		 	 if ($eff[id] > 0 ) {
	 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($user).":".$user[sex].":".$eff[name]."\n";
	 				}
 		 	}

		$fin_add_log.="!:D:".time().":".nick_new_in_battle($real_enemy).":".get_new_dead($real_enemy)."\n";

		 if (($output_attack[type]=='krit') OR ($output_attack[type]=='krita'))
 		 	{

 		 	$eff=mysql_fetch_array(mysql_query("select * from effects where battle={$data_battle[id]} and owner={$real_enemy[id]} and (type=11 OR type=12 OR type=13 OR type=14) ;"));
 		 	 if ($eff[id] > 0 ) {
		 				$fin_add_log.="!:T:".time().":".nick_new_in_battle($real_enemy).":".$real_enemy[sex].":".$eff[name]."\n";
			 		}
 		 	}

 		############################################
 		$fin_add_log.="!:F:".time().":0\n";

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

		// отнимаем в мемори

		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2) {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3) {unset($boec_t3[$user[id]]);}		
		// враг
		if ($real_enemy[battle_t]==1) {unset($boec_t1[$real_enemy[id]]);} 
		elseif ($real_enemy[battle_t]==2) {unset($boec_t2[$real_enemy[id]]) ;}
		elseif ($real_enemy[battle_t]==3) {unset($boec_t3[$real_enemy[id]]) ;}		
		 $STEP = 5;
		}
		break;

	     } // fin switch


			////795-баф - магия хила 
			if ( ((is_array($user_eff[795])) and ($user[hp]>0)) AND ($data_battle[nomagic]==0)  )
				{
				$hil_p=0.5; //50% в старте
				$dlev=($user[level]-$real_enemy[level])*0.1;
				$hil_p-=$dlev;
				$cure_value = round($output_attack[dem]*$hil_p);				
				if ($cure_value > 0 ) 
					{
					//хилимся
					$fadd=make_hil_battle($user,$cure_value);
					 if ($fadd) { $user[hp]+=$fadd; }
					}
				}

			////795-баф - магия хила 
			if ( ((is_array($real_enemy_eff[795])) and ($real_enemy[hp]>0)) AND ($data_battle[nomagic]==0)  )
				{
				$hil_p=0.5; //50% в старте
				$dlev=($real_enemy[level]-$user[level])*0.1;
				$hil_p-=$dlev;
				$cure_value = round($input_attack[dem]*$hil_p);				
				if ($cure_value > 0 ) 
					{
					//хилимся
					 $fadd=make_hil_battle($real_enemy,$cure_value);
					 if ($fadd) { $real_enemy[hp]+=$fadd; }
					}
				}


		if (!isset($real_enemy[id_user]) && ($STING=='REZ10' || $STING=='REZ00' || $STING=='REZ1010' || $STING=='REZ0010' || $STING=='REZ0001') && ($data_battle['type'] == 40 || $data_battle['type'] == 41 || $data_battle['type'] == 61)) {
		if ($real_enemy['level'] >= ($user['level']-1)) {
				if (!isset($real_enemy_eff[5577]) || $real_enemy_eff[5577]['add_info'] < 10) {
					if(!isset($real_enemy_eff[5577])) {
						mysql_query('INSERT INTO `effects` (`type`,`name`,`owner`,`time`,`add_info`) VALUES(5577,"Противостояние - черепа","'.$real_enemy['id'].'",1999999999,1) ');
					} else {
						mysql_query('UPDATE `effects` SET add_info = '.($real_enemy_eff[5577]['add_info']+1).' WHERE type = 5577 AND owner = '.$real_enemy['id']);
					}
					mysql_query('INSERT INTO `op_battle_index` (`battle`,`owner`,`value`,`team`)
							VALUES(
								'.$data_battle['id'].',
								'.$user['id'].',
								1,
								'.$user['battle_t'].'
							)               
							ON DUPLICATE KEY UPDATE
								`value` = `value` + 1
					');
				}
			}
		}
		elseif (!isset($real_enemy[id_user]) and ($STING=='REZ10' || $STING=='REZ00' || $STING=='REZ1010' || $STING=='REZ0010' || $STING=='REZ0001') and ( $data_battle['type'] ==150 || $data_battle['type'] ==140 ) )
								{
								//клановые битвы
								get_kv_bonus($user);
								}

		if (!isset($real_enemy[id_user]) && ($STING=='REZ01' || $STING=='REZ00' || $STING=='REZ0101' || $STING=='REZ0010' || $STING == 'REZ0001') && ($data_battle['type'] == 40 || $data_battle['type'] == 41 || $data_battle['type'] == 61)) {
			if ($user['level'] >= ($real_enemy['level']-1)) {
					if (!isset($user_eff[5577]) || $user_eff[5577]['add_info'] < 10) {
						if(!isset($user_eff[5577])) {
							mysql_query('INSERT INTO `effects` (`type`,`name`,`owner`,`time`,`add_info`) VALUES(5577,"Противостояние - черепа","'.$user['id'].'",1999999999,1) ');
						} else {
							mysql_query('UPDATE `effects` SET add_info = '.($user_eff[5577]['add_info']+1).' WHERE type = 5577 AND owner = '.$user['id']);
						}

						mysql_query('INSERT INTO `op_battle_index` (`battle`,`owner`,`value`,`team`) 
									VALUES(
										'.$data_battle['id'].',
										'.$real_enemy['id'].',
										1,
										'.$real_enemy['battle_t'].'
									) 
									ON DUPLICATE KEY UPDATE
										`value` = `value` + 1
						');
					}
			}
		}
		elseif (!isset($real_enemy[id_user]) and ($STING=='REZ01' || $STING=='REZ00' || $STING=='REZ0101' || $STING=='REZ0010' || $STING == 'REZ0001') and ( $data_battle['type'] ==150 || $data_battle['type'] ==140 ) )
								{
								//клановые битвы
								get_kv_bonus($real_enemy);
								}


	     // урон и опыт добавляется прежде чем пойдет фаза завершения
    		//добавин если был мой урон опыту и урону в базу
		if ($output_attack[dem] > 0 )
			{
			 solve_exp($data_battle,$user,$real_enemy,$my_wearItems[allsumm],$en_wearItems[allsumm],$output_attack[dem],$my_wearItems['elka_aura_ids'],$BSTAT[win],0);
			}
		// добавим еще и вражеский урон и опыт если был
		if ($input_attack[dem] > 0 )
			{
			
					if ($input_fix_cost_level==1)
					{
					//высчитываем и суммируем уроны отдельно
					if ($input_attack_magic_dem>0)
						{
						//если есть маг урон с фиксом то сумма врага и уровень такие как у юзера 
						 solve_exp($data_battle,$real_enemy,$real_enemy,$en_wearItems['allsumm'],$en_wearItems['allsumm'],$input_attack_magic_dem,$en_wearItems['elka_aura_ids'],$BSTAT[win],$input_attack_magic_dem);
						 }
					 
					 solve_exp($data_battle,$real_enemy,$user,$en_wearItems['allsumm'],$my_wearItems['allsumm'],($input_attack['dem']+$input_attack['stone']),$en_wearItems['elka_aura_ids'],$BSTAT[win],0);					 
					
					if (($user['id']==14897) OR ($real_enemy['id']==14897) )
					{
					addchp('<font color=red>Внимание!</font> Дебаг in fights 3'.$user['login'].'  VS '.$real_enemy['login'].' : DEM='.($input_attack['dem']+$input_attack['stone']),'{[]}Bred{[]}',-1,0);	
					}
					
					}
					else
					{
					//если флаг фикса не 1 то все как раньше просто
					 solve_exp($data_battle,$real_enemy,$user,$en_wearItems['allsumm'],$my_wearItems['allsumm'],$input_attack['dem']+$input_attack['stone']+$input_attack_magic_dem,$en_wearItems['elka_aura_ids'],$BSTAT[win],$input_attack_magic_dem);
					 
					 if (($user['id']==14897) OR ($real_enemy['id']==14897) )
					{
					addchp('<font color=red>Внимание!</font> Дебаг in fights 4'.$user['login'].'  VS '.$real_enemy['login'].' : DEM='.($input_attack['dem']+$input_attack['stone']+$input_attack_magic_dem),'{[]}Bred{[]}',-1,0);	
					}
					 
					 }
			}

		 //2.2 проверка на окончание боя

		if ($BSTAT[win]==1)
		 {
		 // победа команды 1
		 // апдейтим мемори
		 $data_battle[win]=1;
 		 if ($data_battle[fond] > 0) {   get_win_money_logs($data_battle);  }
	 	   $winrez[0]=finish_battle(1,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
		 if ($data_battle[blood]>0) { 	 addlog($data_battle[id],get_text_travm($data_battle)); }
		 addlog($data_battle[id],get_text_broken($data_battle));
			//БС
			if ($data_battle[type]==10)
			{
				check_bs($data_battle);
			}
		 }
		 else if ($BSTAT[win]==2)
		 {
		 //победа команды 2
 		 // апдейтим мемори
		 $data_battle[win]=2;
 		 $winrez[0]=finish_battle(2,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
 		 if ($data_battle[blood]>0) { 	 addlog($data_battle[id],get_text_travm($data_battle)); }
		 addlog($data_battle[id],get_text_broken($data_battle));
 			//БС
			if ($data_battle[type]==10)
			{
			check_bs($data_battle);
			}
		 }
		 else if ($BSTAT[win]==4)
		 {
		 //победа команды 3
		 $data_battle[win]=4;
 		 $winrez[0]=finish_battle(4,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
 		 if ($data_battle[blood]>0) { 	 addlog($data_battle[id],get_text_travm($data_battle)); }
		 addlog($data_battle[id],get_text_broken($data_battle));
 			//БС
			if ($data_battle[type]==10)
			{
			check_bs($data_battle);
			}
		 }		 
		 else if ($BSTAT[win]==0)
		 {
		 // ничья
 		 // апдейтим мемори
		 $data_battle[win]=0;
 		 $winrez[0]=finish_battle(0,$data_battle,$data_battle[blood],$data_battle[type],$data_battle[fond]);
		 addlog($data_battle[id],get_text_broken($data_battle));
		//БС
			if ($data_battle[type]==10)
			{
			check_bs($data_battle);
			}
		 }
		 else
		 {
		 // бой идет
		/// 3. очистка размена
		// по окончанию надо удалить запись из таблицы разменов
		 mysql_query("delete from `battle_fd` where `battle`={$data_battle[id]} and `razmen_from`={$real_enemy[id]} and `razmen_to`={$user[id]} ; ");
		 }

  }
 }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					//	print_r($user);
					  //   echo "<br>";
     					//	print_r($en);
					 //    echo "<br>";



					     }




				     }

				}
		}
	}


//////////////////////////////////////////////////////////////////////////////
// циклим демона
	$count_no_sleeps = 0;
	$good_exit=0;

   while($good_exit<=1)
     {
    ////////////////////////////////
	$ddd=mysql_fetch_array(mysql_query("select * from `variables` where `var`='fights_exit' ; "));
	if ($ddd[value]>0)
	{
	//правильный обрыв цикла
	mysql_query("update `variables` set `value`=0 where  `var`='fights_exit' ; ");
	$good_exit=1;
	break;
	}
    ///////////////////////////////
	$time_now = time();
	$count_no_sleeps++;
	// Обработка заявок
	if($time_now >= $time_zayavka+$zayavka) 
	{
		$time_zayavka = $time_now;
		check_zayavka($time_now);
	}


// Обработка 	назначения статусных битв и авто ударов
	if($time_now >= $time_status_fights+$status_fights) 
	{
		$time_status_fights = $time_now;
		check_status_fights();
	}


	// задержка
    if($count_no_sleeps == 20) { sleep(1); $count_no_sleeps = 0;}
    }

addchp ('<font color=red>Внимание!</font> fights exit - '.CITY_DOMEN,'{[]}Bred{[]}',-1,0);	
addchp ('<font color=red>Внимание!</font> fights exit - '.CITY_DOMEN,'{[]}Байт{[]}',-1,0);	
addchp ('<font color=red>Внимание!</font> fights exit - '.CITY_DOMEN,'{[]}Десятый{[]}',-1,0);	


?>
