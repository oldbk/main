#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
if( !lockCreate("cron_zayav_job") ) {
    exit("Script already running.");
}

//V4.1
//Доп. Условие: все заявки +- левл надо сделать скрытыми всегда
//[18:25:00] Deni: Все заявки однолевельные - всегда открытые
/*
Меняем на универсальную формулу
Берем число людей одного уровня в он-лайне. Отнимаем тех кто в заявке, в бою, вне клуба. Делим полученное число на 5 и получаем число-максимум людей в заявке на этот уровень. Если это число получается нечетным, то округляем его в сторону уменьшения. Если это число меньше 6, то заявка все равно подается на 6 игроков.
Тоже самое происходит и с сводными заявками 5-7 левелов и 12-13. Время выхода этих заявок не менять.
*/

$time = time();

//$nomagic=(date("i")%2); //  через минуту делаем без магии
$time_to_start=600; //  через сколько старт заявки
$zhide=4; // через сколько заявок делать скрыте

$h=date("H");

echo "Час : $h <br>";

$zlevls=array(6,7,8,9,10,11,12,13,14); //13.5

function get_zcount($lmin,$lmax)
{
$get_zc=mysql_fetch_array(mysql_query("select  * from `oldbk`.`variables` WHERE `var`='zlvl{$lmin}_{$lmax}';"));
//echo "select  * from `oldbk`.`variables` WHERE `var`='zlvl{$lmin}_{$lmax}';" ;
//print_r($get_zc);
return $get_zc['value'];
}

function mk_zcount($lmin,$lmax,$r)
{
	if ($r==0)
	{
	mysql_query("UPDATE `oldbk`.`variables` SET `value`=0 WHERE `var`='zlvl{$lmin}_{$lmax}';");
	}
	else
	{
	mysql_query("UPDATE `oldbk`.`variables` SET `value`=`value`+{$r} WHERE `var`='zlvl{$lmin}_{$lmax}';");
	}
}

function get_max_users($lvl)
{

$onl = mysql_fetch_array(mysql_query("select  count(id) as kol from `users`  WHERE `level` in (".implode(",",$lvl).") and lab=0 and battle=0 and zayavka=0 and in_tower=0 and room<10000  and  `ldate` >= ".(time()-80)." ;"));

$online=$onl['kol'];
print_r($lvl);
echo "online= $online <br>\n";
$p=(int)($online/6);
if ($p%2) { $p-=1; }

if ($p<6) { $p=6; }

$h=date("H");
if (($h>=0) and ($h<=11))
	{
	//c 00 до 11 утра  http://tickets.oldbk.com/issue/oldbk-2586
	if (($lvl[0]==14) and ($p<6) ) { $p=6; } // http://tickets.oldbk.com/issue/oldbk-2586
	}
	else
	{
	if (($lvl[0]==14) and ($p<8) ) { $p=8; } // http://tickets.oldbk.com/issue/oldbk-2586
	}





/*[11:01:16] Денис Фёдоров: Сереге Поменять максимум участников для хаотов 13 уровней обратно с 10 на 6
*/
/*
if (($lvl[0]==13) and ($lvl[1]!=13) )
	{
	//в авто заявках  для 13 -13 ур. сделать максимальное количество людей всегда не меньше 10 чел
	if ($p<10) { $p=10; }
	}


$h=date("H");

	if (($h>=0) and ($h<=11))
	{
	//c 00 до 11 утра  http://tickets.oldbk.com/issue/oldbk-1877
	//Уменьшить колво человек для уровневых хаотов утром (до 11 утра) наполовину, но не менее 6-ти.
	$p=(int)($p*0.5);
	if ($p%2) { $p-=1; }
	if ($p<6) { $p=6; }
	}
*/
echo " mk pip= $p <br>\n";
return $p;
}


		foreach($zlevls as $k=>$lv)
		{
		echo "$lv <br>";
				//if ((!(($h>=11) and ($h<22))) and  ($lv==6) )
				if ($lv==6)
				{
				echo "Проверяем 5-7 <br> ";
				//5  по 7
				$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($lv-1)." and `t1max`=".($lv+1)." and `t2min`=".($lv-1)." and `t2max`=".($lv+1)." and `level`=5 and `coment`='<b>#zlevels</b>' "));
				}
				//elseif ((!(($h>=11) and ($h<22))) and  ($lv==7) )
				elseif ($lv==7)
				{
				echo "пропускаем 7х <br> ";
				continue; //пропускаем
				}
				/*elseif ((!(($h>=11) and ($h<=23))) and  ($lv==12) )
				{
				//12  по 13
				echo "Проверяем 12-13 <br> ";
				$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($lv)." and `t1max`=".($lv+1)." and `t2min`=".($lv)." and `t2max`=".($lv+1)." and `level`=5 and `coment`='<b>#zlevels</b>' "));
				}*/
				elseif ($lv==13.5)
				{
				 //continue; //пропускаем
				//12  по 13
					$tlv=13;
					echo "Проверяем 13-14 <br> ";
					$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($tlv)." and `t1max`=".($tlv+1)." and `t2min`=".($tlv)." and `t2max`=".($tlv+1)." and `level`=5 and `coment`='<b>#zlevels</b>' "));

				}
				else
				{
				echo "Проверяем остальных <br> ";
				///Оставляем хаоты для каждого левела с 11.00 по серверу до 23.00 по серверу
				$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".$lv." and `t1max`=".($lv)." and `t2min`=".$lv." and `t2max`=".($lv)." and `level`=5 and `coment`='<b>#zlevels</b>' "));
				}
				/*
				1) сделать чтобы уровневые закрытые 5-7 стартовали с 22:00, соотв-но уровневые 5, 6 и 7 отключать в это же время и до 11 утра
				2) при этом уровневые закрытые 12-13 должны начинаться в 00:00, соотв-но уровыневые 12 и 13 отключаются в это время и до 11 утра
				*/




			if ($get_have_zay[id]>0)
				{
				//есть уровневая заявка на хаотический бой
				}
				else
				{
				echo "нет заявки надо создать ";
						//if ((!(($h>=11) and ($h<22))) and  ($lv==6) )
						if ($lv==6)
							{
							echo "Создаем 5-6-7";

										$look=array();
										$look[]=($lv-1);
										$look[]=$lv;
										$look[]=($lv+1);
//										$maxu=get_max_users($look);
										$maxu=6; 	//http://tickets.oldbk.com/issue/oldbk-1027

										$zh=1;
										mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zlevels</b>',`type`=3,`team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($lv-1).",`t1max`=".($lv+1).",`t2min`=".($lv-1).",`t2max`=".($lv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`subtype`=0,`zcount`=0,`hz`=1;") ;


							}
						/*elseif ((!(($h>=11) and ($h<=23))) and  ($lv==12) )
							{
							echo "12-13 ";
										$look=array();
										$look[]=$lv;
										$look[]=($lv+1);
										$maxu=get_max_users($look);
										$zh=1;
										mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zlevels</b>',`type`=3,`team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($lv).",`t1max`=".($lv+1).",`t2min`=".($lv).",`t2max`=".($lv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`subtype`=0,`zcount`=0,`hz`=1;") ;
							}*/
						elseif ($lv==13.5)
							{
							$tlv=13;
							echo "13-14 ";
										$look=array();
										$look[]=$tlv;
										$look[]=($tlv+1);
										$maxu=get_max_users($look);

										$zh=1;
										mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zlevels</b>',`type`=3,`team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($tlv).",`t1max`=".($tlv+1).",`t2min`=".($tlv).",`t2max`=".($tlv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`subtype`=0,`zcount`=0,`hz`=1;") ;
							}
							else
							{
							echo " остальные";
										//остальные как обычно
										$look=array();
										$look[]=$lv;
										$maxu=get_max_users($look);
										/*
										 if	(get_zcount($lv,$lv)>=$zhide)
										 	{
											mk_zcount($lv,$lv,0);
											$zh=1;
										 	}
										 	else
										 	{
											$zh=0;
											mk_zcount($lv,$lv,1);
										 	}
										*/
										$zh=0;
										mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zlevels</b>',`type`=3,`team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".$lv.",`t1max`=".($lv).",`t2min`=".$lv.",`t2max`=".($lv).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`subtype`=0,`zcount`=0,`hz`=1;") ;
							}
				}


		}
/*
$dayof=date("w");
$h=date("G");

//заявки на велики

 if ( ( (($dayof==0) or ($dayof==6))  and ($h>=14) and ($h<=18) )  OR       // в выходные дни 14:00-18:00
      ( (($dayof>=1) and ($dayof<=5)) and ($h>=19) and ($h<=22) )  )  //     в будние 19:00-22:00
      {
      // проверяем нет ли такой заявки
			     $get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=1 and `t1max`=21 and `t2min`=1 and `t2max`=21 and `level`=5 and  `coment`='<b>Великий Хаотический Бой!</b>' "));
			     if ($get_have_zay[id]>0)
				{
				//есть уровневая заявка на хаотический бой
				}
				else
				{
				//нет, надо создать
				mysql_query("INSERT INTO `oldbk`.`zayavka` SET `coment`='<b>Великий Хаотический Бой!</b>',`type`=3,`team1`='',`team2`='',`start`='".(time()+1800)."',`timeout`=3,`t1min`=1,`t1max`=21,`t2min`=1,`t2max`=21,`level`=5,`podan`='".date("H:i")."',`t1c`=50,`t2c`=50,`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=0,`subtype`=0,`zcount`=0,`hz`=1;") ;
				}
      }
      else
      {
      echo "еще не время для велика";
      }

*/
echo "--------------------------------------\n";

require_once("/www/capitalcity.oldbk.com/config_ko.php");
			///авто заявки с букетами
			if (((time()>$KO_start_time22) and (time()<$KO_fin_time22)) OR ((time()>mktime(0,0,0,3,7,2018) ) and (time()<mktime(23,59,59,3,16,2018) )) )
			{
			echo "Проверка букетных заявк \n <br>";
			foreach($zlevls as $k=>$lv)
					{
					echo "$lv <br>";
							if ($lv==6)
							{
							echo "Проверяем 5-7 <br> ";
							//5  по 7
							$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($lv-1)." and `t1max`=".($lv+1)." and `t2min`=".($lv-1)." and `t2max`=".($lv+1)." and `level`=5 and  subtype=2 and `coment`='<b>#zbuket</b>' "));
							}
							elseif ($lv==7)
							{
							echo "пропускаем 7х <br> ";
							continue; //пропускаем
							}
							elseif ($lv==13.5)
							{
								$tlv=13;
								echo "Проверяем 13-14 <br> ";
								$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($tlv)." and `t1max`=".($tlv+1)." and `t2min`=".($tlv)." and `t2max`=".($tlv+1)." and `level`=5 and  subtype=2 and `coment`='<b>#zbuket</b>'  "));
							}
							else
							{
							echo "Проверяем остальных <br> ";
							$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".$lv." and `t1max`=".($lv)." and `t2min`=".$lv." and `t2max`=".($lv)." and `level`=5 and  subtype=2 and `coment`='<b>#zbuket</b>'  "));
							}


						if ($get_have_zay[id]>0)
							{
							//есть уровневая заявка на хаотический бой
							}
							else
							{
							echo "нет заявки надо создать ";
									if ($lv==6)
										{
										echo "Создаем 5-6-7";

													$look=array();
													$look[]=($lv-1);
													$look[]=$lv;
													$look[]=($lv+1);
			//										$maxu=get_max_users($look);
													$maxu=6; 	//http://tickets.oldbk.com/issue/oldbk-1027

													$zh=1;
													mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zbuket</b>',`type`=3, `subtype`=2 , `team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($lv-1).",`t1max`=".($lv+1).",`t2min`=".($lv-1).",`t2max`=".($lv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`zcount`=0,`hz`=1;") ;



										}
									elseif ($lv==13.5)
										{
										$tlv=13;
										echo "13-14 ";
													$look=array();
													$look[]=$tlv;
													$look[]=($tlv+1);
													$maxu=get_max_users($look);

													$zh=1;
													mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zbuket</b>',`type`=3, `subtype`=2 , `team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($tlv).",`t1max`=".($tlv+1).",`t2min`=".($tlv).",`t2max`=".($tlv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`zcount`=0,`hz`=1;") ;
										}
										else
										{
										echo " остальные";
													//остальные как обычно
													$look=array();
													$look[]=$lv;
													$maxu=get_max_users($look);
													$zh=0;
													mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zbuket</b>',`type`=3, `subtype`=2 , `team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".$lv.",`t1max`=".($lv).",`t2min`=".$lv.",`t2max`=".($lv).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`zcount`=0,`hz`=1;") ;
										}
							}


					}



			}
			else
			{
			echo "Не время букетных боев \n";
			}
      ///авто заявки с елками
			if (    (time()>$KO_start_time19) and (time()<$KO_fin_time19) )
			{
			echo "Проверка букетных заявк \n <br>";
			foreach($zlevls as $k=>$lv)
					{
					echo "$lv <br>";
							if ($lv==6)
							{
							echo "Проверяем 5-7 <br> ";
							//5  по 7
							$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($lv-1)." and `t1max`=".($lv+1)." and `t2min`=".($lv-1)." and `t2max`=".($lv+1)." and `level`=5 and  subtype=1 and `coment`='<b>#zelka</b>' "));
							}
							elseif ($lv==7)
							{
							echo "пропускаем 7х <br> ";
							continue; //пропускаем
							}
							elseif ($lv==13.5)
							{
								$tlv=13;
								echo "Проверяем 13-14 <br> ";
								$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".($tlv)." and `t1max`=".($tlv+1)." and `t2min`=".($tlv)." and `t2max`=".($tlv+1)." and `level`=5 and  subtype=1 and `coment`='<b>#zelka</b>'  "));
							}
							else
							{
							echo "Проверяем остальных <br> ";
							$get_have_zay=mysql_fetch_array(mysql_query("select * from zayavka where `type`=3 and `t1min`=".$lv." and `t1max`=".($lv)." and `t2min`=".$lv." and `t2max`=".($lv)." and `level`=5 and  subtype=1 and `coment`='<b>#zelka</b>'  "));
							}


						if ($get_have_zay[id]>0)
							{
							//есть уровневая заявка на хаотический бой
							}
							else
							{
							echo "нет заявки надо создать ";
									if ($lv==6)
										{
										echo "Создаем 5-6-7";

													$look=array();
													$look[]=($lv-1);
													$look[]=$lv;
													$look[]=($lv+1);
			//										$maxu=get_max_users($look);
													$maxu=6; 	//http://tickets.oldbk.com/issue/oldbk-1027

													$zh=1;
													mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zelka</b>',`type`=3, `subtype`=1 , `team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($lv-1).",`t1max`=".($lv+1).",`t2min`=".($lv-1).",`t2max`=".($lv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`zcount`=0,`hz`=1;") ;



										}
									elseif ($lv==13.5)
										{
										$tlv=13;
										echo "13-14 ";
													$look=array();
													$look[]=$tlv;
													$look[]=($tlv+1);
													$maxu=get_max_users($look);

													$zh=1;
													mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zelka</b>',`type`=3, `subtype`=1 , `team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".($tlv).",`t1max`=".($tlv+1).",`t2min`=".($tlv).",`t2max`=".($tlv+1).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`zcount`=0,`hz`=1;") ;
										}
										else
										{
										echo " остальные";
													//остальные как обычно
													$look=array();
													$look[]=$lv;
													$maxu=get_max_users($look);
													$zh=0;
													mysql_query("INSERT INTO `oldbk`.`zayavka` SET  `hide`='{$zh}' , `coment`='<b>#zelka</b>',`type`=3, `subtype`=1 , `team1`='',`team2`='',`start`='".(time()+$time_to_start)."',`timeout`=3,`t1min`=".$lv.",`t1max`=".($lv).",`t2min`=".$lv.",`t2max`=".($lv).",`level`=5,`podan`='".date("H:i")."',`t1c`={$maxu},`t2c`={$maxu},`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`=0,`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='',`t2hist`='',`bcl`=1,`zcount`=0,`hz`=1;") ;
										}
							}


					}



			}
			else
			{
			echo "Не время елок боев \n";
			}





lockDestroy("cron_zayav_job");
?>
