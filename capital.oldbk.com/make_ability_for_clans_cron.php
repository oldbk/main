#!/usr/bin/php
<?
include("/www/capitalcity.oldbk.com/clans_abil_conf.php");
include "/www/capitalcity.oldbk.com/connect.php";
include "/www/capitalcity.oldbk.com/functions.php";

echo "------------------------\n";
echo date();
echo " CRON ABIL start ".time()." \n  ";

//обнуление склонносных абилок
mysql_query("UPDATE oldbk.users_babil SET dur=0 where maxdur>0 and btype=1;");
echo "Обнуление абилок";
echo mysql_affected_rows();
echo mysql_error();
echo "-------------------------------\n";

//обнуление личных суточных абилок
mysql_query("UPDATE oldbk.users_abils SET dailyc=daily  where daily>0;");
echo "Обнуление личных суточных абилок";
echo mysql_affected_rows();
echo mysql_error();
echo "-------------------------------\n";

//стата по репе
mysql_query("insert into users_rep_stat select  DATE_FORMAT(dtime,'%Y-%m-%d') AS sdate, @REP:=sum(drep) as re  from users_progress where dtime>=DATE_FORMAT(now(),'%Y-%m-%d') on DUPLICATE KEY update rep=@REP");


//mysql_query("UPDATE oldbk.forum SET close_info='Архивариус,,2,9,0', closepal=83, `close`=1  WHERE type=2 AND parent<100 AND updated < DATE_ADD(NOW(), INTERVAL -120 DAY)");
//mysql_query("UPDATE avalon.forum SET close_info='Архивариус,,2,9,0', closepal=83, `close`=1  WHERE type=2 AND parent<100 AND updated < DATE_ADD(NOW(), INTERVAL -120 DAY)");
mysql_query("UPDATE oldbk.forum SET close_info='Архивариус,,2,9,0', closepal=84, `close`=1   WHERE type=2 AND parent<100  and isnull(closepal) and isnull(fix) and `updated`< NOW()-INTERVAL 30 DAY");


$LIMIT=count($Abil);

$date = date("dmY");
mysql_select_db ("topsites", $mysql);
$poz=mysql_query("select * from top where cat=0 and ban=0 and klan!='' AND memberid!=7 order by hoststoday DESC, hitsin DESC, allhosts DESC LIMIT ".$LIMIT.";");
$poz_pal=mysql_query("select * from top where  memberid=7  order by hoststoday DESC, hitsin DESC, allhosts DESC LIMIT 1;");


mysql_select_db ("oldbk", $mysql);
mysql_query("update `clans_abil` set count=0, maxcount=0, chasha=0 ,userscount='';");
//mysql_query("update avalon.`clans_abil` set count=0, maxcount=0,  userscount='';");
//mysql_query("update angels.`clans_abil` set count=0, maxcount=0,  userscount='';");

    function q_ability_log($tm)
	{
        	/*$date = date("dmY");
        	$load = file("q_ability_".$date);
        	$load=implode('',$load);
		$tm=$load.$tm.';
		';
		$save = fopen("q_ability_".$date,"w");
		fwrite($save,$tm);
		fclose($save);*/
	}

$i=0;
while($objpoz = mysql_fetch_assoc($poz))
	{
	  $i++;
	  if ($objpoz[klan]=='align_1.99')
	  {
	  	$klan='pal' ;
	  }
	  else
	  {
	  	$klan=$objpoz[klan];
	  }


		foreach ($Abil[$i] as $k=>$v)
		{


		      	$data=mysql_fetch_assoc(mysql_query('SELECT cl.short, cl.rekrut_klan, cl_a.id, cl_a.magic, cl_a.userscount, cl_a.recrut_count
		      	from clans_abil as cl_a       	left JOIN clans as cl 	      	ON cl_a.klan = cl.short 	      	WHERE cl.short ="'.$klan.'" AND magic = "'.$k.'" ;'));
                	q_ability_log('reit:_'.$date.'_'.$klan);
                	
                	$skl_osn_n='';
                	
	                if($data[recrut_count])
	                {
	                    if(($v-$data[recrut_count])>0)
	                    {
		                //	echo $data[recrut_count]. ' ';
		                //	print_r($data);

		                	$recr=mysql_fetch_assoc(mysql_query('SELECT * FROM clans where id="'.$data[rekrut_klan].'" LIMIT 1;'));
		                        $sql='INSERT `clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` ='.$data[recrut_count].';';

							/*$sql_ava='INSERT avalon.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` ='.$data[recrut_count].';';
							
							$sql_ang='INSERT angels.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` ='.$data[recrut_count].';';*/


		                    mysql_query($sql);
		                  //  mysql_query($sql_ava); // абилки в авалон -для рекрутов
		                   // mysql_query($sql_ang); 
		                    $v=$v-$data[recrut_count];
	                    }
	                    else
	                    {
	                    //нехватает - надо обнулить recrut_count - у основы
	                    $skl_osn_n='  recrut_count=0 , ';
	                    }
	                    
	                }
				$sql2='INSERT `clans_abil`  (`klan`,      `magic`,  `count`,`maxcount`)  values("'.$klan.'","'.$k.'",0,"'.$v.'")   ON DUPLICATE KEY UPDATE `count` = 0 , '.$skl_osn_n.'  `maxcount` ='.$v.';';
				//$sql2_ava='INSERT avalon.`clans_abil`  (`klan`,      `magic`,  `count`,`maxcount`)  values("'.$klan.'","'.$k.'",0,"'.$v.'")    ON DUPLICATE KEY UPDATE `count` = 0 , '.$skl_osn_n.'  `maxcount` ='.$v.';'; 
				//$sql2_ang='INSERT angels.`clans_abil` 	(`klan`,      `magic`,  `count`,`maxcount`) values("'.$klan.'","'.$k.'",0,"'.$v.'")    ON DUPLICATE KEY UPDATE `count` = 0 , '.$skl_osn_n.'  `maxcount` ='.$v.';';
				 mysql_query($sql2);
				// mysql_query($sql2_ava); // абилки в авалон для основы
				// mysql_query($sql2_ang); 
			}
//	echo "<br>";
	}
//pal
$i=0;
while($objpoz = mysql_fetch_assoc($poz_pal))
	{
	  $i++;
	  if ($objpoz[klan]=='align_1.99')
	  {
	  	$klan='pal' ;
	  }
	  else
	  {
	  	$klan=$objpoz[klan];
	  }


		foreach ($Abil[$i] as $k=>$v)
		{


			      	$data=mysql_fetch_assoc(mysql_query('SELECT cl.short, cl.rekrut_klan, cl_a.id, cl_a.magic, cl_a.userscount, cl_a.recrut_count
			      	from oldbk.clans_abil as cl_a
			      	left JOIN oldbk.clans as cl
			      	ON cl_a.klan = cl.short
			      	WHERE cl.short ="'.$klan.'" AND magic = "'.$k.'" ;'));
	                	q_ability_log('reit:_'.$date.'_'.$klan);
		                if($data[recrut_count])
		                {
		                    if(($v-$data[recrut_count])>0)
		                    {
			                //	echo $data[recrut_count]. ' ';
			                //	print_r($data);
	
			                	$recr=mysql_fetch_assoc(mysql_query('SELECT * FROM clans where id="'.$data[rekrut_klan].'" LIMIT 1;'));
			                    	$sql='INSERT oldbk.`clans_abil`
								(`klan`, `magic`, `count`,`maxcount`)
							 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
								ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` ='.$data[recrut_count].';';
	
						/*$sql_ava='INSERT avalon.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` ='.$data[recrut_count].';';
							
						$sql_ang='INSERT angels.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` ='.$data[recrut_count].';'; */
	
	
			                    mysql_query($sql);
			                   // mysql_query($sql_ava); // абилки в авалон -для рекрутов
			                    //mysql_query($sql_ang); 
			                    $v=$v-$data[recrut_count];
		                    }
		                }
					$sql2='INSERT oldbk.`clans_abil`
							(`klan`,      `magic`,  `count`,`maxcount`)
					 		values("'.$klan.'","'.$k.'",0,"'.$v.'")
							   ON DUPLICATE KEY UPDATE `count` = 0 , `maxcount` ='.$v.';';
	
					/*
					$sql2_ava='INSERT avalon.`clans_abil`
							(`klan`,      `magic`,  `count`,`maxcount`)
					 		values("'.$klan.'","'.$k.'",0,"'.$v.'")
							   ON DUPLICATE KEY UPDATE `count` = 0 , `maxcount` ='.$v.';';

					$sql2_ang='INSERT angels.`clans_abil`
							(`klan`,      `magic`,  `count`,`maxcount`)
					 		values("'.$klan.'","'.$k.'",0,"'.$v.'")
							   ON DUPLICATE KEY UPDATE `count` = 0 , `maxcount` ='.$v.';';							   
					*/
	
	
				 mysql_query($sql2);
//				 mysql_query($sql2_ava); // абилки в авалон для основы
//				 mysql_query($sql2_ang); // абилки в авалон для основы				 

			}
//	echo "<br>";
	}


//add war bonus

//ADD clear reiting in every first day of month
$poz=mysql_query("select co.*, cr.short as crshort, cr.voinst as crvoinst, co.voinst+ifnull(cr.voinst,0) as allvoinst from oldbk.clans co
    left join oldbk.clans cr
    on co.rekrut_klan=cr.id
    where (co.voinst >0 OR cr.voinst>0) order by co.voinst+ifnull(cr.voinst,0) desc LIMIT ".$LIMIT.";");

$i=0;
while($objpoz = mysql_fetch_assoc($poz))
	{
	  //print_r($objpoz);
	  $i++;
	  if ($objpoz[short]=='align_1.99')
	  {
	  	 $klan='pal' ;
	  }
	  else
	  {
	  	 $klan=$objpoz[short];
	  }
		foreach ($Abil[$i] as $k=>$v)
		{


		      	$data=mysql_fetch_assoc(mysql_query('SELECT cl.short, cl.rekrut_klan, cl_a.id, cl_a.magic, cl_a.userscount, cl_a.recrut_count
		      	from oldbk.clans_abil as cl_a
		      	left JOIN oldbk.clans as cl
		      	ON cl_a.klan = cl.short
		      	WHERE cl.short ="'.$klan.'" AND magic = "'.$k.'" ;'));
                 	q_ability_log('voinst:_'.$date.'_'.$klan);
	                if($data[recrut_count])
	                {
	                    if(($v-$data[recrut_count]>0)){
		                //	echo $data[recrut_count]. ' ';
		                //	print_r($data);
		                	$recr=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans where id="'.$data[rekrut_klan].'" LIMIT 1;'));
		                    $sql='INSERT oldbk.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` =maxcount+'.$data[recrut_count].';';

		                   /*$sql_ava='INSERT avalon.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` =maxcount+'.$data[recrut_count].';';
							
		                   $sql_ang='INSERT angels.`clans_abil`
							(`klan`, `magic`, `count`,`maxcount`)
						 	values("'.$recr[short].'","'.$k.'",0,'.$data[recrut_count].')
							ON DUPLICATE KEY UPDATE `count` =0 , `maxcount` =maxcount+'.$data[recrut_count].';';							
				*/

		                    mysql_query($sql);
		                  //  mysql_query($sql_ava);
		                   // mysql_query($sql_ang);		                    
		                    $v=$v-$data[recrut_count];
	                    }
	                }
				$sql2='INSERT oldbk.`clans_abil`
						(`klan`,      `magic`,  `count`,`maxcount`)
				 		values("'.$klan.'","'.$k.'",0,"'.$v.'")
						   ON DUPLICATE KEY UPDATE `count` = 0 , `maxcount` =maxcount+'.$v.';';
				/*
				$sql2_ava='INSERT avalon.`clans_abil`
						(`klan`,      `magic`,  `count`,`maxcount`)
				 		values("'.$klan.'","'.$k.'",0,"'.$v.'")
						   ON DUPLICATE KEY UPDATE `count` = 0 , `maxcount` =maxcount+'.$v.';';
				$sql2_ang='INSERT angels.`clans_abil`
						(`klan`,      `magic`,  `count`,`maxcount`)
				 		values("'.$klan.'","'.$k.'",0,"'.$v.'")
						   ON DUPLICATE KEY UPDATE `count` = 0 , `maxcount` =maxcount+'.$v.';';						   
				*/


				mysql_query($sql2);
//				mysql_query($sql2_ava);
//				mysql_query($sql2_ang);				
		}
//	echo "<br>";
	}


///обновление абилок источника древних для кланов

mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values     (\'Family\',\'59\',\'0\',\'1400\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'1400\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values     (\'Family\',\'59\',\'0\',\'1400\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'1400\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values     (\'Family\',\'59\',\'0\',\'1400\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'1400\';');



mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values   (\'DarkClan\',\'59\',\'0\',\'2100\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'2100\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values   (\'DarkClan\',\'59\',\'0\',\'2100\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'2100\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values   (\'DarkClan\',\'59\',\'0\',\'2100\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'2100\';');


//mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values   (\'AD\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values   (\'AD\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values   (\'AD\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');


/// 1 Колодец от DarkInLife перенеси на Mercenaries
mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Mercenaries\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Mercenaries\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Mercenaries\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');


mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'MiB\',\'59\',\'0\',\'3500\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'3500\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'MiB\',\'59\',\'0\',\'3500\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'3500\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'MiB\',\'59\',\'0\',\'3500\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'3500\';');


mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Longriders\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Longriders\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Longriders\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');

mysql_query('INSERT `clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Kiev\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Kiev\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');
//mysql_query('INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values        (\'Kiev\',\'59\',\'0\',\'700\' ) ON DUPLICATE KEY UPDATE `count` =\'0\' , `maxcount` =\'700\';');


//обнулялка вызова морозного духа
mysql_query('update effects set lastup=0 where type=113010');

///начисление колодцев из индексной таблицы, выбираем тех у кого есть дни начисления
$get_war_abil=mysql_query("select * , (select short from oldbk.clans where id=klan) as short from oldbk.clans_abil_war_new where leftdays>0  order by `count` DESC;");
//1. выдали
$mem=array();
while($wab = mysql_fetch_assoc($get_war_abil))
	{
	
	  if (!(in_array($wab['short'],$mem)))
		{
		//если нет в масиве
		
		$mem[]=$wab['short']; // запоминаем
		if ($wab[magic]==59)
		{
		//начисляем колодцы
		mysql_query("INSERT oldbk.`clans_abil` (`klan`,`magic`,`count`,`maxcount`,`chasha`) values  ('{$wab[short]}','59','0','".(700*$wab[count])."', '{$wab[count]}' ) ON DUPLICATE KEY UPDATE `count` ='0' , `maxcount` =`maxcount` + '".(700*$wab[count])."' , `chasha`='{$wab[count]}'  ;");
//		mysql_query("INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values  ('{$wab[short]}','59','0','".(700*$wab[count])."' ) ON DUPLICATE KEY UPDATE `count` ='0' , `maxcount` =`maxcount` + '".(700*$wab[count])."';");	
//		mysql_query("INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values  ('{$wab[short]}','59','0','".(700*$wab[count])."' ) ON DUPLICATE KEY UPDATE `count` ='0' , `maxcount` =`maxcount` + '".(700*$wab[count])."';");			
		}
		else
		{
		//начисляем другую магию, хз на будущее
		mysql_query("INSERT oldbk.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values  ('{$wab[short]}','{$wab[magic]}','0','".$wab[count]."' ) ON DUPLICATE KEY UPDATE `count` ='0' , `maxcount` =`maxcount` + '".$wab[count]."';");
//		mysql_query("INSERT avalon.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values  ('{$wab[short]}','{$wab[magic]}','0','".$wab[count]."' ) ON DUPLICATE KEY UPDATE `count` ='0' , `maxcount` =`maxcount` + '".$wab[count]."';");			
//		mysql_query("INSERT angels.`clans_abil` (`klan`,`magic`,`count`,`maxcount`) values  ('{$wab[short]}','{$wab[magic]}','0','".$wab[count]."' ) ON DUPLICATE KEY UPDATE `count` ='0' , `maxcount` =`maxcount` + '".$wab[count]."';");					
		}
		
		//2. отняли -1 день, тем кого выбирали :)
		//mysql_query("update oldbk.clans_abil_war_new set leftdays=leftdays-1 where id={$wab['id']} ");
		}
		
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//2. отняли -1 день,  по новой схеме отнимаем день у всех
mysql_query("update oldbk.clans_abil_war_new set leftdays=leftdays-1 where  leftdays>0 ");

// обнулялка арканов - для незавершенных войн -- для старых войн
//mysql_query("update oldbk.clans_war_log set agrr_arkan_count=0, def_arkan_count=0 where winner=0 and type=2 ;");
//для новых войн
mysql_query("update oldbk.clans_war_new set agr_ark=0, def_ark=0 where winner=0;");





//////////////////////////////////////
//// начисление покупных абилок
 //выбираем все купленые абилки которые незакончились
 /* - отключено всязи сновой концепцией
   $get_all_buy_abil=mysql_query("select * from oldbk.abil_buy_clans where all_count>0;");
	while($abil = mysql_fetch_assoc($get_all_buy_abil)) 
	{
	//ищем клан
	$toklan=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans where id="'.$abil[klan_id].'" LIMIT 1;'));
	if ($toklan[id]>0)
	   {
	   //клан есть
		//теперь выдаем
		if (($abil[all_count]-$abil[day_count])>=0)
			{
			//если остаток больше нуля или 0
			// выдаем и отнимаем
                    mysql_query('INSERT oldbk.`clans_abil` (`klan`, `magic`, `count`,`maxcount`)
			 	values("'.$toklan[short].'","'.$abil[magic_id].'",0,'.$abil[day_count].')
				ON DUPLICATE KEY UPDATE `maxcount` =`maxcount`+'.$abil[day_count].';');

                    mysql_query('INSERT avalon.`clans_abil` (`klan`, `magic`, `count`,`maxcount`)
			 	values("'.$toklan[short].'","'.$abil[magic_id].'",0,'.$abil[day_count].')
				ON DUPLICATE KEY UPDATE `maxcount` =`maxcount`+'.$abil[day_count].';');
		     //отнимаем
		     mysql_query("UPDATE `oldbk`.`abil_buy_clans` SET `all_count`=`all_count`-'{$abil[day_count]}' WHERE `klan_id`='{$abil[klan_id]}' AND `magic_id`='{$abil[magic_id]}'");	
			}
			else
			{
			//в остатке меньше чем выдается в день!!
			//выдаем остатки
			 mysql_query('INSERT oldbk.`clans_abil` (`klan`, `magic`, `count`,`maxcount`)
			 	values("'.$toklan[short].'","'.$abil[magic_id].'",0,'.$abil[all_count].')
				ON DUPLICATE KEY UPDATE `maxcount` =`maxcount`+'.$abil[all_count].';');

	                mysql_query('INSERT avalon.`clans_abil` (`klan`, `magic`, `count`,`maxcount`)
			 	values("'.$toklan[short].'","'.$abil[magic_id].'",0,'.$abil[all_count].')
				ON DUPLICATE KEY UPDATE `maxcount` =`maxcount`+'.$abil[all_count].';');
			//нулим
		     	mysql_query("UPDATE `oldbk`.`abil_buy_clans` SET `all_count`=0 WHERE `klan_id`='{$abil[klan_id]}' AND `magic_id`='{$abil[magic_id]}'");	
			}
	  }	
	}
*/
////


//каждый понедельник добавляем один халявный стат
if((date('l', time()+60*60))=='Monday')
{
 	mysql_query('UPDATE users_znahar_sfree SET free_count=free_count+1 WHERE free_count<15;');
}

//чистим  сообщения с жалобами ( дня назад )
mysql_query('DELETE FROM zhalobi WHERE m_id<'.(time()-60*60*24*2).';');

//обнуление счетчиков для ивента стола2-НГ стол1-хеллоуин
mysql_query("DELETE from `stol` where `stol` in (1,2,22,23,5670);");
//mysql_query("DELETE from avalon.`stol` where `stol` in (1,2,22,23);");
//mysql_query("DELETE from angels.`stol` where `stol` in (1,2,22,23);");

mysql_query('delete from gellery WHERE exp_date<'.time().';');

	//бекап чата
    {
	     $last_id=0;
		 $i=0;
	     $filename="chat_hist_".(date('d_m_y'));
		 $fp = fopen ("/www/www_logs/chat_hist/".$filename.".txt","a"); //открытие
		       flock ($fp,LOCK_EX); //БЛОКИРОВКА ФАЙЛА
		      // echo $filename;
	     $data=mysql_query('SELECT * FROM `chat_hist` ');
	     while($row=mysql_fetch_array($data))
	     {
	     	 $last_id=$row[id];
			fputs($fp ,"".$row[id]."[|]".$row[text]."[|]".$row[city]."[|]".$row[room]."[|]".$row[owner]." \r\n"); //работа с файлом
			fflush ($fp); //ОЧИЩЕНИЕ ФАЙЛОВОГО БУФЕРА И ЗАПИСЬ В ФАЙЛ
	  	    $i++;
	     }
	     flock ($fp,LOCK_UN); //СНЯТИЕ БЛОКИРОВКИ
		 fclose ($fp); //закрытие
		 mysql_query("delete from chat_hist where id<=".$last_id.";");
	//     echo "Обработано ".$i." записей, последний ID=".$last_id;
    }

// упаковка
foreach (glob("/www/www_logs/chat_hist/*.txt") as $filename) {
	$zip_name = $filename.".gz";
	$data = file_get_contents($filename);

	$gzdata = gzencode($data, 9);
	$fp = fopen($zip_name, "w");
	fwrite($fp, $gzdata);
	fclose($fp);

	unlink($filename);
}


	//раздача подарков

 	//2010-08-03 12:27:09
 	

	$time=time()+60*60*5; //это точно следующий день (старт крона + 5 часов)	
	$born=date("Y-m-d",$time);	
	$born=explode('-',$born);
	$v_ear=date("L",$time);
	if($v_ear==1)
	{
		//год високосный, ниего не делаем 1ого марта, ибо 29 уже все выдалось
		$add_sql='';
	}
	else
	if($born[1]==03 && $born[2]==01)
	{
		//остальые года - добавляем подарки 1-марта 29-м февраля
		$add_sql='-02-29';
	}
		
	for($i=2010;$i<(int)$born[0];$i++) //проверяем все года
	{
	
	
		
		$born2=$i.'-'.$born[1].'-'.$born[2];

		$sql = "SELECT * FROM oldbk.`users` where (DATE(borntime) = '".$born2."'  ".($add_sql!=""?" or DATE(borntime) = '".$i.$add_sql."'":"")." ) and bot=0 and block=0;";
		echo $sql.'<br>';
		$cur_users =   mysql_query($sql);
		while($row=mysql_fetch_assoc($cur_users))
		{
			echo $row[login].' ' . $row[borntime].'<br>';
			
			$sql="insert into oldbk.inventory  (name,maxdur,cost,owner,img,isrep,type,massa,magic,prototype,otdel,add_time,present_text,present)  values ('Подарок -Благословение Ангелов-',1,0,".$row[id].",'gift_hb_126.gif',0,200,5,2000,10001,6 ,'".time()."','Благословение Ангелов','Администрации')";
			mysql_query($sql);
			$pritem1[name]='Подарок -Благословение Ангелов-';
			$pritem1[id]=mysql_insert_id();
			
			
			$sql2="insert into oldbk.inventory  (name,maxdur,cost,owner,img,isrep,type,massa,magic,prototype,otdel,add_time,present_text,present)  values ('Торт',1,0,".$row[id].",'gift_hb_135.gif',0,200,1,0000,10615,72,'".time()."','С днем рождения персонажа! Удачной игры и хорошего настроения!','Администрации')";
			mysql_query($sql2);			
			$pritem2[name]='Торт';
			$pritem2[id]=mysql_insert_id();
			
		        addchp ('<font color=red>Внимание!</font> Вы получили подарок на День Рождения персонажа от Администрации.','{[]}'.$row['login'].'{[]}',$row['room'],$row['id_cty']);
			
		       	                //new_delo
		       	                $rec=array();
  		    			$rec['owner']=$row['id'];
					$rec['owner_login']=$row[login];
					$rec['owner_balans_do']=$row['money'];
					$rec['owner_balans_posle']=$row['money'];
					$rec['target']=0;
					$rec['target_login']='Администрация';
					$rec['type']=98; 
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;//комиссия
					$rec['item_id']=get_item_fid($pritem1).",".get_item_fid($pritem2);
					$rec['item_name']=$pritem1[name].",".$pritem2[name];
					$rec['item_count']=2;
					$rec['item_type']=200;
					$rec['item_cost']=0;
					$rec['item_dur']=0;
					$rec['item_maxdur']=1;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['bank_id']=0;
					$rec['add_info']='День Рождения персонажа';
					add_to_new_delo($rec); //юзеру
			
		}
	}

////////////////
// ЛОмбард

function cron_send_mess ($telo,$text) 
{
$cron_city='capitalcity.oldbk.com';


//если в авалоне то переспрашиваем из авалона данные
	if ( ($telo[id_city]==1)and($cron_city=='capitalcity.oldbk.com') )
	{
	$q2 = mysql_query('SELECT * FROM avalon.`users` WHERE id = '.$telo[id]) or die();
	$telo = mysql_fetch_assoc($q2) or die();	
	}
elseif ( ($telo[id_city]==2)and($cron_city=='capitalcity.oldbk.com') )
	{
	$q2 = mysql_query('SELECT * FROM angels.`users` WHERE id = '.$telo[id]) or die();
	$telo = mysql_fetch_assoc($q2) or die();	
	}	


if($telo[odate]>=(time()-60))
	{
	$to_login='{[]}'.$telo[login].'{[]}';
	$txt_to_file=":[".time()."]:[{$to_login}]:[".($text)."]:[".$telo[room]."]"; 
	
	//{$telo[room]} test
	
	mysql_query("INSERT INTO `oldbk`.`chat` SET `text`='".mysql_real_escape_string($txt_to_file)."',`city`='".($telo[id_city]+1)."' , `room`='-1'  ;");				
	} 
	else 
  	{
	// если в офе
	mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$telo['id']."','','".'['.date("d.m.Y H:i").'] '.$text.'  '."');");		
	}

}


	function CronPawnBroker() {
		// выбираем все вещи из ломбарда
		$q = mysql_query('
				 SELECT pawn.*, inv.name AS name
				    FROM `pawnbroker` AS pawn
				    LEFT JOIN oldbk.`inventory` AS inv
				    ON pawn.itemid = inv.id
			    WHERE (pawn.warning = 0 AND pawn.endtime < '.(3*60*60*24+time()).' AND pawn.endtime > '.time().') OR (pawn.endtime < '.time().')
		') or die();
		while($item = mysql_fetch_assoc($q)) {
			// оповещение
			$t = time();

			// проверяем чтобы вещь была не просрочена и чтобы время было меньше 3х суток
			// и что сообщение мы не слали
			if ($item['warning'] == 0 && ($item['endtime'] - $t) > 0 && ($item['endtime'] - $t) < (3*60*60*24)) {
				// шлём варнинг и обновляем в базе

				// отсылаем сообщение
				$q2 = mysql_query('SELECT * FROM users WHERE id = '.$item['owner']) or die();
				$data = mysql_fetch_assoc($q2) or die();

				$wmess='<font color=red>Внимание!</font> Ломбард напоминает, что вы должны выкупить "'.htmlspecialchars($item['name'],ENT_QUOTES).'" до '.date("d/m/Y H:i:s",$item['endtime']).' иначе ломбард заберёт вещь навсегда.';
				cron_send_mess ($data,$wmess) ;
				// Обновляем в базе, что варнинг был выслан
				$q2 = mysql_query('UPDATE `pawnbroker` SET warning = 1 WHERE id = '.$item['id']) or die();

			}

			// проверяем если вещь просрочена
			if ($item['endtime'] < $t) {
				// меняем овнера на 446 - Делитера
				mysql_query('UPDATE oldbk.`inventory` SET `owner` = 446 WHERE id = '.$item['itemid']) or die();

				// удаляем из таблицы ломбарда
				mysql_query('DELETE FROM `pawnbroker` WHERE id = '.$item['id']) or die();
			}
		}
	}

	CronPawnBroker();


	//авто выгонялка людей из оружейки

		$go_way=mysql_query("select id, login, room, ldate from users where room in (197,198,199) and ldate < ".(time()-24*60*60));
	//	$go_way=mysql_query("select id, login, room, ldate from users where id=14897");

		 while ($telo= mysql_fetch_array($go_way))
		   {
		   
		   $napr[197]=210;
		   $napr[198]=240;		   
   		   $napr[199]=270;
		   $goto=$napr[$telo[room]];
		   if ($goto>0)
		     {
		     ///загружаем параметры prof=0 для выхода
		     $user_real=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users_profile` WHERE  prof=0 and  `owner` = '{$telo[id]}' LIMIT 1;"));
		     if ($user_real[bpbonushp] >0)
		     {
		     //если был боныс хп - проверяем незакончился ли он
		     $hp_bonus=mysql_fetch_array(mysql_query("select * from effects where owner='{$telo[id]}' and (type=1001 or  type=1002 or type=1003)"));
		     if ($hp_bonus[id]>0)
		       {
		       //все ок эфект еще висит
		       
		       }
		       else
		       {
		       //эфекта такого уже нет! 
		       //снимаем его ручками, т.к. в кроене он не снялся
		       $user_real[maxhp]=$user_real[maxhp]-$user_real[bpbonushp];
       		       $user_real[bpbonushp]=0;
			       if ($user_real[hp]>$user_real[maxhp]) 
			       		{
			       		$user_real[hp]=$user_real[maxhp];
			       		}
		       }
		     }
		     //идем дальше
		     //обновляем инвентарь
		     //1. удаляем шаблонные вещи
		     mysql_query_100("delete from oldbk.inventory  where owner='{$telo[id]}' and bs_owner=3 and type!=12");
		     //2.устанавливаем родные шмотки
		     mysql_query("update oldbk.inventory  set dressed=1 where id in ({$user_real[sergi]},{$user_real[kulon]},{$user_real[perchi]},{$user_real[weap]},{$user_real[bron]},{$user_real[r1]},{$user_real[r2]},{$user_real[r3]},{$user_real[runa1]},{$user_real[runa2]},{$user_real[runa3]},{$user_real[helm]},{$user_real[shit]},{$user_real[boots]},{$user_real[nakidka]},{$user_real[rubashka]}) AND owner='{$telo[id]}' and dressed=0 ");
		     //3. обновляем чарчика
		     $sk_row=" `sila`='{$user_real[sila]}',`lovk`='{$user_real[lovk]}',`inta`='{$user_real[inta]}',`vinos`='{$user_real[vinos]}',`intel`='{$user_real[intel]}',
		`mudra`='{$user_real[mudra]}',`duh`='{$user_real[duh]}',`bojes`='{$user_real[bojes]}',`noj`='{$user_real[noj]}',`mec`='{$user_real[mec]}',`topor`='{$user_real[topor]}',`dubina`='{$user_real[dubina]}',
		`maxhp`='{$user_real[maxhp]}',`hp`='{$user_real[hp]}',`maxmana`='{$user_real[maxmana]}',`mana`='{$user_real[mana]}',`sergi`='{$user_real[sergi]}',`kulon`='{$user_real[kulon]}',`perchi`='{$user_real[perchi]}',
		`weap`='{$user_real[weap]}',`bron`='{$user_real[bron]}',`r1`='{$user_real[r1]}',`r2`='{$user_real[r2]}',`r3`='{$user_real[r3]}',`helm`='{$user_real[helm]}',`shit`='{$user_real[shit]}',`boots`='{$user_real[boots]}',
		`stats`='{$user_real[stats]}',`master`='{$user_real[master]}',`nakidka`='{$user_real[nakidka]}',`rubashka`='{$user_real[rubashka]}',`mfire`='{$user_real[mfire]}',`mwater`='{$user_real[mwater]}',`mair`='{$user_real[mair]}',`mearth`='{$user_real[mearth]}',
		`mlight`='{$user_real[mlight]}',`mgray`='{$user_real[mgray]}',`mdark`='{$user_real[mdark]}', `bpbonushp`='{$user_real[bpbonushp]}' ,  `runa1`='{$user_real[runa1]}',`runa2`='{$user_real[runa2]}',`runa3`='{$user_real[runa3]}'   ";
		      mysql_query("UPDATE `users` SET ".$sk_row." , `users`.`room` = '{$goto}'  WHERE `users`.`id`  = '{$telo[id]}' ;");

		     }
		     else
		     {
		     echo "Ошибка направления...";
		     }
		   }


//include "/www/capitalcity.oldbk.com/cron/cron_castles_ability.php";

//чистка активных боев
 mysql_query("DELETE from active_battle where bdate<=NOW()-INTERVAL 30 DAY");

// чистим тех у кого прошёл bantime для вывода
$q = mysql_query('SELECT * FROM users_money WHERE bantime > 0 and ban = 1 and bantime <= '.time());
while($bb = mysql_fetch_assoc($q)) {
	mysql_query('UPDATE users_money SET ban = 0, bantime = 0 WHERE owner = '.$bb['owner']);
}

//чистка просроченых бонусных перезарядов
mysql_query("delete from bonus_items  where finish<".time()." ");

//чистка таблика fair_buy_log
mysql_query("delete from fair_buy_log where itemgoden > 0 and itemgoden < ".time());

mysql_query('truncate table users_fortune');

mysql_query('truncate table castles_osada_uses');

echo "------FIN: ".time()."-------\n";
?>