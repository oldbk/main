#!/usr/bin/php
<?php
ini_set('display_errors','On');
Error_Reporting(ALL);

include "/www/capitalcity.oldbk.com/cron/init.php";
require_once('/www/capitalcity.oldbk.com/functions.zayavka.php'); // функи за€вок


if( !lockCreate("cron_stur_job") ) {
    exit("Script already running.");
}

function mydie($txt) {
 echo time().":".$txt."\n";
 lockDestroy("cron_stur_job");
 die();
}


function insert_items($owners_by_team,$wc,$oc) // ид тел по командам , кол- оружи€, кол. предметов, тип турнира - раскидывает нужное кол. шмота - сразу на два тела
{
$ty=8;
$item_wep[8]=array(1000267,1000268,1000269,1000270,1000271,1000272,1000273,1000274,1000275,1000276,1000277);
$slots_arr=array(1,2,4,5,5,5,8,9,10,11,28); // масив слотов - кроме пушек
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////------------//////-----------//////////------------///////////////
$item_arr[8][1]=array(1000231,1000232,1000233,1000234,1000235,1000236,1000237,1000238,1000239,1000240,1000241,1000242,1000243,1000244,1000245,1000246,1000247,1000248);
$item_arr[8][2]=array(1000249,1000250,1000251,1000252,1000253,1000254,1000255,1000256,1000257,1000258,1000259,1000260,1000261,1000262,1000263,1000264,1000265,1000266);
$item_arr[8][4]=array(1000278,1000279,1000280,1000281,1000282,1000283,1000284,1000285,1000286,1000287,1000288,1000289,1000290,1000291,1000292);
$item_arr[8][5]=array(1000296,1000297,1000298,1000299,1000300,1000301,1000302,1000303,1000304,1000305,1000306,1000307);
$item_arr[8][8]=array(1000201,1000202,1000203,1000204,1000205,1000206);
$item_arr[8][9]=array(1000207,1000208,1000209,1000210,1000211,1000212,1000213,1000214,1000215,1000216,1000217,1000218,1000219);
$item_arr[8][10]=array(1000220,1000221,1000222,1000223,1000224,1000225,1000226);
$item_arr[8][11]=array(1000227,1000228,1000229,1000230);
$item_arr[8][28]=array(1000293,1000294,1000295);
///-----------------------------------------------------------------------------------------------
$item_weps=$item_wep[$ty]; // назначаем масив по типу  турнира
$item_arrs=$item_arr[$ty]; // назначаем масив по типу  турнира


	// 1. раздаем пушки
	  for($i=1;$i <=$wc;$i++) //цикл - кол. пушек
	  {

	  	shuffle($item_weps); $rand_wep=mt_rand(0,(count($item_weps)-1) );
	  	$rand_wep=$item_weps[$rand_wep];
	  	if ($rand_wep>0)
	  		{

	  		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id={$rand_wep}")) or mydie(mysql_error().":".__LINE__);
	  		if ($item[id]>0)
	  		    {	
	  			for($tt=1;$tt <=2;$tt++) 	// кидаем зеркально каждой команде
	  			{  
	  			$mowner=$owners_by_team[$tt];
	  			
	  			mysql_query("INSERT INTO `oldbk`.`inventory` SET `name`='{$item[name]}',`duration`='{$item[duration]}',`maxdur`='{$item[maxdur]}',`cost`='{$item[cost]}',`owner`='{$mowner}',
	  			`nlevel`='{$item[nlevel]}',`nsila`='{$item[nsila]}',`nlovk`='{$item[nlovk]}',`ninta`='{$item[ninta]}',`nvinos`='{$item[nvinos]}',`nintel`='{$item[nintel]}',`nmudra`='{$item[nmudra]}',
	  			`nnoj`='{$item[nnoj]}',`ntopor`='{$item[ntopor]}',`ndubina`='{$item[ndubina]}',`nmech`='{$item[nmech]}',`nalign`='{$item[nalign]}',`minu`='{$item[minu]}',`maxu`='{$item[maxu]}',
	  			`gsila`='{$item[gsila]}',`glovk`='{$item[glovk]}',`ginta`='{$item[ginta]}',`gintel`='{$item[gintel]}',`ghp`='{$item[ghp]}',`mfkrit`='{$item[mfkrit]}',`mfakrit`='{$item[mfakrit]}',`mfuvorot`='{$item[mfuvorot]}',`mfauvorot`='{$item[mfauvorot]}',
	  			`gnoj`='{$item[gnoj]}',`gtopor`='{$item[gtopor]}',`gdubina`='{$item[gdubina]}',`gmech`='{$item[gmech]}',`img`='{$item[img]}',
	  			`text`='{$item[text]}',`dressed`='0',`bron1`='{$item[bron1]}',`bron2`='{$item[bron2]}',`bron3`='{$item[bron3]}',`bron4`='{$item[bron4]}',`dategoden`='{$item[dategoden]}',`magic`='{$item[magic]}',`type`='{$item[type]}',
	  			`present`='{$item[present]}',`sharped`='{$item[sharped]}',`massa`='{$item[massa]}',`goden`='{$item[goden]}',`needident`='{$item[needident]}',`nfire`='{$item[nfire]}',`nwater`='{$item[nwater]}',`nair`='{$item[nair]}',`nearth`='{$item[nearth]}',`nlight`='{$item[nlight]}',
	  			`ngray`='{$item[ngray]}',`ndark`='{$item[ndark]}',`gfire`='{$item[gfire]}',`gwater`='{$item[gwater]}',`gair`='{$item[gair]}',`gearth`='{$item[gearth]}',`glight`='{$item[glight]}',
	  			`ggray`='{$item[ggray]}',`gdark`='{$item[gdark]}',`letter`='{$item[letter]}',`isrep`='{$item[isrep]}',`prototype`='{$item[prototype]}',`otdel`='{$item[otdel]}',`bs`='{$item[bs]}',`gmp`='{$item[gmp]}',
	  			`includemagic`='{$item[includemagic]}',`includemagicdex`='{$item[includemagicdex]}',`includemagicmax`='{$item[includemagicmax]}',`includemagicname`='{$item[includemagicname]}',`includemagicuses`='{$item[includemagicuses]}',
	  			`includemagiccost`='{$item[includemagiccost]}',`includemagicekrcost`='{$item[includemagicekrcost]}',
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='3',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity`=0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}';") or mydie(mysql_error().":".__LINE__);
	  			}
	  		    }
	  		}
	  }
	  
	  //перемешиваем слоты
	  shuffle($slots_arr);
	  
	  //указатель на слот
	  $uk_slot=0;
	  
	  //2, раздаем остальное
	  for ($i=1;$i<=$oc;$i++) // цикл кол. остальных предметов
	  {
	 
	 		if (!($slots_arr[$uk_slot] > 0))
		   	{
		   	//если  добрались до конца скидываем указатель в 0 и заново мешаем масив слотов
	   		  $uk_slot=0;
		   	  shuffle($slots_arr);
		   	}
		   	
		   //спрашиваем шмотку
		$mas_items=$item_arrs[$slots_arr[$uk_slot]]; //масив дл€ нужного слота
		shuffle($mas_items); // перемешиваем
		$get_itm_id=$mas_items[0];

		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id={$get_itm_id}"  )) or mydie(mysql_error().":".__LINE__);
		   	
		if ($item[id]>0)
	  	 {	
	  		for($tt=1;$tt <=2;$tt++) 	// кидаем зеркально каждой команде
	  		{  
				$mowner=$owners_by_team[$tt];
	  			mysql_query("INSERT INTO `oldbk`.`inventory` SET `name`='{$item[name]}',`duration`='{$item[duration]}',`maxdur`='{$item[maxdur]}',`cost`='{$item[cost]}',`owner`='{$mowner}',
	  			`nlevel`='{$item[nlevel]}',`nsila`='{$item[nsila]}',`nlovk`='{$item[nlovk]}',`ninta`='{$item[ninta]}',`nvinos`='{$item[nvinos]}',`nintel`='{$item[nintel]}',`nmudra`='{$item[nmudra]}',
	  			`nnoj`='{$item[nnoj]}',`ntopor`='{$item[ntopor]}',`ndubina`='{$item[ndubina]}',`nmech`='{$item[nmech]}',`nalign`='{$item[nalign]}',`minu`='{$item[minu]}',`maxu`='{$item[maxu]}',
	  			`gsila`='{$item[gsila]}',`glovk`='{$item[glovk]}',`ginta`='{$item[ginta]}',`gintel`='{$item[gintel]}',`ghp`='{$item[ghp]}',`mfkrit`='{$item[mfkrit]}',`mfakrit`='{$item[mfakrit]}',`mfuvorot`='{$item[mfuvorot]}',`mfauvorot`='{$item[mfauvorot]}',
	  			`gnoj`='{$item[gnoj]}',`gtopor`='{$item[gtopor]}',`gdubina`='{$item[gdubina]}',`gmech`='{$item[gmech]}',`img`='{$item[img]}',
	  			`text`='{$item[text]}',`dressed`='0',`bron1`='{$item[bron1]}',`bron2`='{$item[bron2]}',`bron3`='{$item[bron3]}',`bron4`='{$item[bron4]}',`dategoden`='{$item[dategoden]}',`magic`='{$item[magic]}',`type`='{$item[type]}',
	  			`present`='{$item[present]}',`sharped`='{$item[sharped]}',`massa`='{$item[massa]}',`goden`='{$item[goden]}',`needident`='{$item[needident]}',`nfire`='{$item[nfire]}',`nwater`='{$item[nwater]}',`nair`='{$item[nair]}',`nearth`='{$item[nearth]}',`nlight`='{$item[nlight]}',
	  			`ngray`='{$item[ngray]}',`ndark`='{$item[ndark]}',`gfire`='{$item[gfire]}',`gwater`='{$item[gwater]}',`gair`='{$item[gair]}',`gearth`='{$item[gearth]}',`glight`='{$item[glight]}',
	  			`ggray`='{$item[ggray]}',`gdark`='{$item[gdark]}',`letter`='{$item[letter]}',`isrep`='{$item[isrep]}',`prototype`='{$item[prototype]}',`otdel`='{$item[otdel]}',`bs`='{$item[bs]}',`gmp`='{$item[gmp]}',
	  			`includemagic`='{$item[includemagic]}',`includemagicdex`='{$item[includemagicdex]}',`includemagicmax`='{$item[includemagicmax]}',`includemagicname`='{$item[includemagicname]}',`includemagicuses`='{$item[includemagicuses]}',
	  			`includemagiccost`='{$item[includemagiccost]}',`includemagicekrcost`='{$item[includemagicekrcost]}',
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='3',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity`=0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}';") or mydie(mysql_error().":".__LINE__);	  		
	  		}
	  	}
	  $uk_slot++; 
	  }

}

//ищем за€вки дл€ старта
$start_room=80000;

$log_type=1210;


	$get_zt=mysql_query("select * from stur_users where stat=1 and sttime<=now() and battle=0 and t1_owner>0 and t2_owner>0"); 
	// поиск готовых к старту бо€
	
	 if (mysql_num_rows($get_zt)  > 0)
   	{
   	//обработка старта  - 1- статус
		while ($zt_row = mysql_fetch_array($get_zt))
		{
		//мен€ем статус
		mysql_query("UPDATE stur_users set stat=2 where stur='{$zt_row[stur]}' and room='{$zt_row[room]}'");
			if (mysql_affected_rows() >0)
				{
				echo "—тарт бо€ турнир {$zt_row[stur]} рум '{$zt_row[room]}' <br> \n";
				//пишем в лог - начало турнира
				echo "------------------------------------\n";			
  								//обработка  - создание бо€ 
								// загружаем чаров  дл€ создани€ бо€ - которые в турнире
										$to_battle_id['team1'] = array();
										$to_battle_id['team2'] = array();
										
										$to_battle_login['team1'] = array();
										$to_battle_login['team2'] = array();			
										
										$to_battle_hist['team1'] = array();
										$to_battle_hist['team2'] = array();
										
										$inf_log_t1=array();
										$inf_log_t2=array();
								
						
								$get_all_users=mysql_query("SELECT * from users where  id='{$zt_row['t1_owner']}'  or  id='{$zt_row['t2_owner']}'  ");
						
								
								$tt1=0;
								$tt2=0;
								
								
								 while ($u = mysql_fetch_array($get_all_users))
									 {
									 //заносим данные согласно командам
									 
									 
										if ($u[id]==$zt_row['t1_owner'])
										{
												$to_battle_id['team1'][] = $u[id];
						               					$to_battle_hist['team1'][]=BNewHist($u); // koд - дл€ хистори в battle
												$to_battle_login['team1'][]=make_login_battle($u); // просто логины чистые	
												$inf_log_t1[]=make_html_login_battle($u);
											$tt1++;	
						
										}
										elseif ($u[id]==$zt_row['t2_owner'])
										{
												$to_battle_id['team2'][] =  $u[id];
						               					$to_battle_hist['team2'][]=BNewHist($u); // koд - дл€ хистори в battle
												$to_battle_login['team2'][]=make_login_battle($u); // просто логины чистые				            
												$inf_log_t2[]=make_html_login_battle($u);						
											$tt2++;						
										}
									 }
								echo "Ѕой {$tt1} /  {$tt2} N:{$zt_row[stur]} \n";
								
								///----------/-/-/-/-/--/-/-/-/--/-/-/-/-/-/-/-/-/-/-/-/
								// создаем бой 16 на 16
								$chaos_flag=2; //авто удар
								$time=time();
								$mkbattype=1210;
								$mkbatcom='“урнирный бой';
						
									// создаем лог
									$rrc="<b>".implode(",",$to_battle_login['team1'])."</b> и <b>".implode(",",$to_battle_login['team2'])."</b>"; //дл€ текста в чат
									$hist1=implode("",$to_battle_hist['team1']);//собираем хистори дл€ T1 дл€ battle
									$hist2=implode("",$to_battle_hist['team2']);//собираем хистори дл€ T2 дл€ battle
						
									
									mysql_query("INSERT INTO `battle` ( `t1`, `t2` , `t1hist` , `t2hist`, `coment`,`timeout`,`type`,`status`,`to1`,`to2`,`blood`,`CHAOS`, `nomagic` )  VALUES	(  '".implode(";",$to_battle_id['team1'])."' , '".implode(";",$to_battle_id['team2'])."' , '{$hist1}', '{$hist2}' ,  '{$mkbatcom}','3','{$mkbattype}','0','".$time."','".$time."','0','".$chaos_flag."' , 1 )");
							
									if (mysql_affected_rows()>0)
									{
									//бой создалc€
										$battle_id = mysql_insert_id();
										//обновл€ем людей  кидем в бой - баттл_т не трогаем 
						
										mysql_query_100("UPDATE users SET `battle` ={$battle_id}, battle_t=1  WHERE id='{$zt_row['t1_owner']}' ");
										echo "UPDATE users SET `battle` ={$battle_id}, battle_t=1  WHERE id='{$zt_row['t1_owner']}' " ;
										
										mysql_query_100("UPDATE users SET `battle` ={$battle_id}, battle_t=2  WHERE id='{$zt_row['t2_owner']}' ");										
										echo "UPDATE users SET `battle` ={$battle_id}, battle_t=2  WHERE id='{$zt_row['t2_owner']}' " ;
										
										//лог бо€
										addlog($battle_id,"!:S:".time().":".$hist1.":".$hist2."\n");
										//отправл€ем групповую системку
										addch_group('<font color=red>¬нимание!</font> ¬аш бой началс€! <BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ', array_merge($to_battle_id['team1'],$to_battle_id['team2']));
										//системка
										addch ("<a href=logs.php?log=".$battle_id." target=_blank>ѕоединок</a> между <B>".$rrc."</B> началс€.   ",($start_room+$zt_row[room]),CITY_ID);		
										
										//записываем в таблицу турнира ид бо€ - шоб невызывать и увеличиваем фазу +1
										mysql_query_100("UPDATE `stur_users` SET `battle`='{$battle_id}'   WHERE `room`='{$zt_row[room]}' "); 
										
										if ($zt_row['krug']>0)
											{
											$log_str='Ѕой 1/'.$zt_row['krug'];
											}
											else
											{
											$log_str='Ѕой финал';
											}
										
										$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>{$log_str}:</b>".implode(",",$inf_log_t1)." <b>против</b>  ".implode(",",$inf_log_t2)." <a href=\"/logs.php?log={$battle_id}\" target=\"_blank\"> її </a> <BR>";
									 	mysql_query_100("UPDATE  `stur_logs` SET `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id={$zt_row['stur']}  ;");
										
									}
									else
									{
										mydie(mysql_error().":".__LINE__);
									}
								
								
					
						
									
					
				}
		} 
	}
	else
	{
	echo "Ќет готовых к старту бо€";
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////

	//поиск тел дл€ комбинировани€
		$cand=mysql_fetch_array(mysql_query("select * from stur_users where (t1_owner=0 or t2_owner=0) and sttime!='' and stat!=10  order by krug desc  limit 1; ")); //поиск первого

		if ($cand['stur'] >0)
			{		 		
				if ($cand['t1_owner']>0)
					{
					$candt=1;
					$candt2=2;
					$candid=$cand['t1_owner'];
					}
					else
					{
					$candt=2;
					$candt2=1;					
					$candid=$cand['t2_owner'];					
					}
			
				$cand2=mysql_fetch_array(mysql_query("select * from stur_users where (t1_owner=0 or t2_owner=0) and t".$candt."_owner!='{$candid}' and krug='{$cand['krug']}' and sttime!=''  and stat!=10  limit 1; ")); //поиск второго такого же круга
				echo "select * from stur_users where (t1_owner=0 or t2_owner=0) and t".$candt."_owner!='{$candid}' and krug='{$cand['krug']}' and sttime!=''  limit 1; " ;
				
				if ($cand2['stur'] >0)
					{
					
						if ($cand2['t1_owner']>0)
								{
								$candid2=$cand2['t1_owner'];
								}
								else
								{
								$candid2=$cand2['t2_owner'];
								}
					
					// есть оба
					//дружим пишим назначаем врем€ старта бо€
					echo "оба найдены";

					
					mysql_query("UPDATE `stur_users` SET `t".$candt2."_owner`='{$candid2}' , stat=1, sttime=now()+interval 300 second  WHERE stur='{$cand['stur']}' and krug='{$cand['krug']}' and room='{$cand['room']}' and t".$candt."_owner='{$candid}'  ");

						if (mysql_affected_rows() >0)
						{
							$en=mysql_fetch_array(mysql_query("select * from  users where id='{$candid2}' "));				
							$us=mysql_fetch_array(mysql_query("select * from  users where id='{$candid}' "));				
							
							
							mysql_query("DELETE from stur_users where  stur='{$cand2['stur']}' and  room='{$cand2['room']}' ;");
							echo "DELETE from stur_users where  stur='{$cand2['stur']}' and  room='{$cand2['room']}' " ;
							
							
							mysql_query("UPDATE users set room=".($start_room+$cand['room'])."  where id='{$candid2}' limit 1");

							
							
							if ($cand['krug']>0)
								{
								$altext="1/".$cand['krug'];
								}
								else
								{
								$altext=" финале ";								
								}
										
							$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b> ¬  ".$altext." :</b> ”частвуют: ".make_html_login_battle($en)."  против  ".make_html_login_battle($us)."<BR>";										
							
							mysql_query_100("UPDATE `stur_logs` SET  `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id='{$cand['stur']}' "); 	
						
						
							//кинуть шмотки
							$owners[1]=$us['id'];
							$owners[2]=$en['id'];							
							
							insert_items($owners,5,31);

						
						
						
						}
						
					
					}
					else
					{
					echo "Ќет готовых к подбору в кандидат 2  <br>\n";		 					
					}
			 
			 }
			 else
			 {
			echo "Ќет готовых к подбору в кандидат 1 <br>\n";		 
			 }
	
   	

lockDestroy("cron_stur_job");

?>