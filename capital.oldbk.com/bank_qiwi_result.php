<?php


$i = file_get_contents('php://input');

$l = array('/<login>(.*)?<\/login>/', '/<password>(.*)?<\/password>/');
$s = array('/<txn>(.*)?<\/txn>/', '/<status>(.*)?<\/status>/');

preg_match($l[0], $i, $m1);
preg_match($l[1], $i, $m2);

preg_match($s[0], $i, $m3);
preg_match($s[1], $i, $m4);

$blog='E0';	
#password
//$password = "38qoiCpelofsp5dvn";#real_pass - наш
$password = "TZeHfE9rfUgRlxECJRAi";# дарли = заточено на снятие баланса у DarliBankа

//$hash = strtoupper(md5($m3[1].strtoupper(md5($password))));#подстановка
$hash = strtoupper(strtoupper(md5($password)));#подстановка

$course_ekr_kr=100; //курс екров к кредиту для оплаты темницы


if ($hash !== $m2[1]){ #сравнение нашего пароля с полученным,если не равны код "150"
   $resultCode=150;
  }
  else
  {

   if ($m4[1]==60) // отлавливаем статус 60 - оплачено!
    {
	 if (!(strpos($m3[1],"advert")===false)) // запрос с блогов
	    {
	      $resultCode=0;
	      $blog='Blogi-ok';
	    } 
	  else
	{    
	include "connect.php";
	include "functions.php";
	include "bank_functions.php";	
	include "config_ko.php";
	include "clan_kazna.php";	
	
	//$m3temp=explode("_TEST_",$m3[1]);
	//$m3[1]=$m3temp[1];
	
	//апдейтим статус
	mysql_query("update oldbk.trader_balance_qiwi set `status`=1 where `id`='{$m3[1]}' and `status`=0  ;");  
	if (mysql_affected_rows()>0)
	{
	
	//статус нормально сменился платеж прошел - можно зачислить деньги на счет
	// выбираем этот счет
	 $qiwi=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.trader_balance_qiwi WHERE `id`='{$m3[1]}' ;"));

  		addchp ('Debug QIWI 1:'.$qiwi[owner_id].'/PARAM:'.$qiwi[param].'/SUM:'.$qiwi[sum_ekr],'{[]}Bred{[]}');		
	 
	  	  if ((($qiwi[id]>0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==300) ) //обработка 
			  {
  			//addchp ('Debug QIWI 2:'.$qiwi[owner_id].'/PARAM:'.$qiwi[param].'/SUM:'.$qiwi[sum_ekr],'{[]}Bred{[]}');					  
			  //покупка репутации
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 

				if ($tonick['id']) 
				{
		  	//	addchp ('Debug QIWI 3:'.$qiwi[owner_id].'/PARAM:'.$qiwi[param].'/SUM:'.$qiwi[sum_ekr],'{[]}Bred{[]}');		
				$add_rep=round($balamount * 600);
				$cit[0]='oldbk.';
				$cit[1]='avalon.';
				$cit[2]='angels.';	
				
				if ((time()>$KO_start_time7) and (time()<$KO_fin_time7)) 
				{
					$ko_bonus_rep=$add_rep; // для записи в бонусы
					$add_rep=round(($add_rep*(1+$KO_A_BONUS7)) ,2); //+50%
					$ko_bonus_rep=$add_rep-$ko_bonus_rep; // для записи в бонусы		
					$add_bonus_str=' Добавлено по акции +'.($KO_A_BONUS7*100).'% ('.$ko_bonus_rep.'реп) ';	
							
				}elseif ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
				{
					$kb=act_x2bonus_limit($tonick,2,$add_rep);
					if ($kb>0)
						{
						$ko_bonus_rep=$kb; // для записи в бонусы		
						$add_rep=round($add_rep+$ko_bonus_rep,2);
						$add_bonus_str='. Начислено '.$ko_bonus_rep.' репутации по Акции «Двойная выгода».';	
						$add_bonus_str_chat='Внимание!</font> Вам начислено '.$ko_bonus_rep.' репутации по Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.';	
						}
						else
						{
						$add_bonus_str='';
						}
				}
				else
				{
					$add_bonus_str='';
				}
					
				//делаем два запроса для того что б не сработал триггер в таблице юзерс и не дало бонус от свитка
				mysql_query("UPDATE ".$cit[$tonick[id_city]]."`users` SET  `rep`=`rep`+'$add_rep'  WHERE `id`= '".$tonick['id']."' LIMIT 1;"); 				
				mysql_query("UPDATE ".$cit[$tonick[id_city]]."`users` SET  `repmoney` = `repmoney` + '$add_rep' WHERE `id`= '".$tonick['id']."' LIMIT 1;"); 				
				if (mysql_affected_rows()>0)
				{
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr) values ('qiwi','124836','".iconv("UTF-8","CP1251",'DarliBank')."','300','{$tonick[login]}','{$balamount}');");

					      // Партнерка
						CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('124836','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }

				      	//new_delo - записываем в новый тип
  		    			$rec['owner']=$tonick[id];
					$rec['owner_login']=$tonick[login];
					$rec['owner_balans_do']=$tonick['money'];
					$rec['owner_balans_posle']=$tonick['money'];
					$rec['owner_rep_do']=$tonick[repmoney];
					$rec['owner_rep_posle']=($tonick[repmoney]+$add_rep);
					$rec['target']=124836;
					$rec['target_login']=iconv("UTF-8","CP1251",'DarliBank');
					$rec['type']=2828;//перевод от диллера репы
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$balamount;
					$rec['sum_rep']=$add_rep;					
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
					$rec['bank_id']=0;
					$rec['add_info']=iconv("UTF-8","CP1251",'Баланс реп до '.$tonick[repmoney]. ' после ' .($tonick[repmoney]+$add_rep).$add_bonus_str);
					add_to_new_delo($rec); //юзеру


					telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> Вам зачислено '.$add_rep.' репутации. Удачной игры!')); 
					
					if ($add_bonus_str_chat!='')
						{
						telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",$add_bonus_str_chat)); 
						}
					
					$dil['id']=124836;
					$dil['login']=iconv("UTF-8","CP1251",'DarliBank');
					$get_bankid=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}' and owner='{$tonick[id]}'; "));
					
					if ($get_bankid['id']>0)
						{
						//make_ekr_add_bonus($tonick,$get_bankid,$balamount,$dil);
						}
					

					make_discount_bonus($tonick,$balamount,3);						
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '124836' LIMIT 1;"); //  снимаем  из баланса  DarliBankа	
					
					  $resultCode=0;
				}
				else
			 	{
			       	$resultCode=300;	 	
			 	}	
				
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
			  }
			else
	    if ((($qiwi[id]>0) and ($qiwi[bank_id]==0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==666) ) //обработка - пополнения казны кланов
 	  {
 	  
		$tonick = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE id='{$qiwi[owner_id]}'  LIMIT 1;"));
		if ($tonick[id_city]==1)  {  $tonick = mysql_fetch_array(mysql_query("SELECT * FROM avalon.`users` WHERE id='{$qiwi[owner_id]}' LIMIT 1;"));}
		 
		$klan_name=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$tonick[klan]}' LIMIT 1;")); 
		
		$kkazna=clan_kazna_have($klan_name[id]);
		if ($kkazna)	  
		{
		$klan_kazna_ekr=$qiwi[sum_ekr];
		
					$bonekr=get_ekr_addbonus();
					if ($bonekr>0)
					{
					$addbonekr=round(($qiwi[sum_ekr]*$bonekr),2);
					}
		
		if ((time()>$KO_start_time) and (time()<$KO_fin_time))  
				{
				$add_klan_kazna_ekr=round(($klan_kazna_ekr*(1+1.5)) ,2); //+%
				$ko_bonus=$add_klan_kazna_ekr-$klan_kazna_ekr; // для записи в бонусы
				}
				else
				{
				$add_klan_kazna_ekr=$klan_kazna_ekr;
				}			
	   $add_klan_kazna_ekr+=$addbonekr;				
	   
	   if (put_to_kazna($klan_name[id],2,$add_klan_kazna_ekr,$klan_name[short],$tonick,$coment=iconv("UTF-8","CP1251",'Пополнение через QIWI, от персонажа '.$tonick[login])))
			   	{
				$fc_nom=100000000+$klan_name[id];
				$fc_name='Клан-Казна:«'.$klan_name[short].'»';			
				mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('qiwi','124836','".iconv("UTF-8","CP1251",'DarliBank')."','{$fc_nom}','".iconv("UTF-8","CP1251",$fc_name)."','{$qiwi[sum_ekr]}');");							
				mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '124836' LIMIT 1;"); //  снимаем  из баланса  DarliBankа	
				if ($ko_bonus > 0)  {  mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('',450,'KO','{$fc_nom}','".iconv("UTF-8","CP1251",$fc_name)."','{$ko_bonus}');"); }
				if ($addbonekr>0) {  mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('',450,'KO','{$fc_nom}','".iconv("UTF-8","CP1251",$fc_name)."','{$addbonekr}');"); }
				telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> Пополнен баланс клановой казны на '.($add_klan_kazna_ekr).' екр. '));	
		   		}			
		   		else
		   		{
				addchp (iconv("UTF-8","CP1251",'<font color=red>Внимание!</font>Пополнение казны, пришла оплата qiwi, но ошибка зачсления, teloid:'.$qiwi[owner_id]),'{[]}Bred{[]}',-1,-1); 			       				 			   		
		   		}
		}
		else
		{
			addchp (iconv("UTF-8","CP1251",'<font color=red>Внимание!</font>Пополнение казны, пришла оплата qiwi, но чел не имееет/заморожена казны, teloid:'.$qiwi[owner_id]),'{[]}Bred{[]}',-1,-1); 			       				 	
		}
 	  $resultCode=0;
 	  }	 
	 else
	  if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==0) )
	  {
	  //обработка ЕКР
	  
	  $tonick = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE id='{$qiwi[owner_id]}'  LIMIT 1;"));
	  
  		$pbm=0;// выключаем бонусы
	  	
	  
	  			if ((time()>$KO_start_time40) and (time()<$KO_fin_time40)) 
				{
					$add_ekr_to_kazna=round($qiwi[sum_ekr]*0.5,2);
						
						if ($tonick['klan']!='')
						{
							$klan_name=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$tonick['klan']}' LIMIT 1;"));
				      			$kkazna=clan_kazna_have($klan_name['id']);
							if ($kkazna)
							        {
									 if (put_to_kazna($klan_name['id'],2,$add_ekr_to_kazna,$klan_name['short'],false,iconv("UTF-8","CP1251","Начисленно по Акции «Клановая казна», благодаря ".$tonick['login']."!")))
											      {
												$fc_nom=100000000+$klan_name['id'];
												$fc_name=iconv("UTF-8","CP1251",'Клан-Казна:«'.$klan_name[short].'»');
												mysql_query("INSERT INTO oldbk.`dilerdelo` (dilerid,dilername,bank,owner,ekr,addition) values	('450','KO','{$fc_nom}','{$fc_name}','{$add_ekr_to_kazna}','0');");
												telepost_new($tonick,'<font color=red>'.iconv("UTF-8","CP1251",'Внимание!</font> Благодаря '.$tonick['login'].' в казну поступило '.$add_ekr_to_kazna.' екр. по Акции <a href=http://oldbk.com/encicl/?/act_clantreasury.html target=_blank>«Клановая казна»</a>.'));	
												}
											
								}
						}
				}
	  
				if ((time()>$KO_start_time) and (time()<$KO_fin_time)) 
				{
					$ko_bonus=round(($qiwi[sum_ekr]*($pbm+$KO_A_BONUS)) ,2); 
				}
				elseif ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
				{
					$kb=act_x2bonus_limit($tonick,1,$qiwi[sum_ekr]);
					if ($kb>0)
						{
						$ko_bonus=$kb;
						}
						else
						{
						$ko_bonus=0;
						}
				}
				else
				{
					$ko_bonus=round(($qiwi[sum_ekr]*$pbm) ,2);
				}
	  
	  
	  mysql_query("UPDATE `oldbk`.`bank` SET `ekr` = `ekr` + '".($qiwi[sum_ekr]+$ko_bonus)."' WHERE `id`= '{$qiwi[bank_id]}' LIMIT 1;");
	  
	///пишем в таблицу биржи
	mysql_query("INSERT INTO `oldbk`.`exchange_log` SET `owner`='{$qiwi[owner_id]}' , `dilekr`='".($qiwi[sum_ekr]+$ko_bonus)."'  ON DUPLICATE KEY UPDATE dilekr=dilekr+'".($qiwi[sum_ekr]+$ko_bonus)."' ");
	  
	  if (mysql_affected_rows()>0)
	      {
		$rezbank = mysql_fetch_array(mysql_query("SELECT * FROM `oldbk`.`bank` WHERE `id`= '{$qiwi[bank_id]}' ;"));
	      
	 				 //new_delo
  		    			$rec['owner']=$qiwi[owner_id]; 
					$rec['owner_login']=$qiwi['owner'];
					$rec['target']=83;
					$rec['target_login']=iconv("UTF-8","CP1251",'DarliBank');
					$rec['type']=261;
					$rec['sum_ekr']=$qiwi[sum_ekr]+$ko_bonus;
					$rec['bank_id']=$qiwi[bank_id];
					$rec['add_info']=iconv("UTF-8","CP1251",'Пополнение QIWI');
					add_to_new_delo($rec); 
					
					mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('qiwi','124836','".iconv("UTF-8","CP1251",'DarliBank')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$qiwi[sum_ekr]}');");					
					mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','".iconv("UTF-8","CP1251","Вы пополнили свой счет  на сумму <b>{$qiwi[sum_ekr]}</b> екр. <i>(Итого: {$rezbank['cr']} кр., {$rezbank['ekr']} екр.)</i>")."','{$qiwi[bank_id]}');");					
					
					telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведено '.($qiwi[sum_ekr]).' екр. Удачной игры!!'));	
					
					//запись для бонуса
					if ($ko_bonus > 0)					
					{
					mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('','450','".iconv("UTF-8","CP1251",'KO')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$ko_bonus}');");					
					mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','".iconv("UTF-8","CP1251","Бонус за покупку еврокредитов <b>{$ko_bonus}</b> екр.")."','{$qiwi[bank_id]}');");		
					
					if ((time()>$KO_start_time) and (time()<$KO_fin_time)) 
							{
							telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведен бонус:'.($ko_bonus).' екр. в рамках <a href="http://oldbk.com/encicl/?/act_ekr.html" target="_blank">акции</a>. '));	
							}
					elseif ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
							{
							telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведено '.$ko_bonus.' екр. от  Коммерческого отдела, в рамках Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.'));	
							}							
							else
							{
							$yyy[1]='Silver';
							$yyy[2]='Gold';
							$yyy[3]='Platinum';
							
							telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведен бонус:'.($ko_bonus).' екр. за наличие "'.$yyy[$tonick[prem]].' account" '));
							}
					}

					 // Партнерка
					CheckRealPartners($qiwi[owner_id],$qiwi[sum_ekr],0);
					 $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$qiwi[owner_id]}' LIMIT 1;"));
				     	 $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
				   	 if ($p_user['partner']!='' and $p_user['fraud']!=1)
				       {
				       	 mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('124836','{$partner['id']}','{$qiwi[owner_id]}','{$qiwi[sum_ekr]}','{$qiwi[bank_id]}','".time()."');");
				      	 $bonus=round(($qiwi[sum_ekr]/100*$partner['percent']),2);
				      	 mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$qiwi[sum_ekr]}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
				      }
					
					$dil['id']=124836;
					$dil['login']=iconv("UTF-8","CP1251",'DarliBank');
					$get_bankid=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}' and owner='{$tonick[id]}'; "));
					if ($get_bankid['id']>0)
						{
						//make_ekr_add_bonus($tonick,$get_bankid,$qiwi[sum_ekr],$dil);
						}
						
					make_discount_bonus($tonick,$qiwi['sum_ekr'],2);	
					
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '124836' LIMIT 1;"); //  снимаем  из баланса  DarliBankа	
					
	       $resultCode=0;
	       }
	       else
	       {
	       	$resultCode=300;
	       }
	       	  	  
	  }
	elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==88000||$qiwi[param]==88100||$qiwi[param]==88500||$qiwi[param]==881000) ) //обработка  покупка пакетов монет 200-500-1000
					  {
						$tonick = check_users_city_data($qiwi[owner_id]);
						$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
						$bankid=$qiwi[bank_id];	
		
							$fid = 3003060; // покупка  золотых монеток ввиде баланса
							if ($qiwi[param]==88000) 
								{
								 $kol=round($balamount*20);
								 }							
							elseif ($qiwi[param]==88100) { $kol=100; }
							elseif ($qiwi[param]==88500) { $kol=500; }
							elseif ($qiwi[param]==881000) { $kol=1000; }
					
							if ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
							{
								$kb=act_x2bonus_limit($tonick,3,$kol);
								if ($kb>0)
									{
									$ko_bonus_gold=$kb;
									$kol+=$ko_bonus_gold;
									$ko_bonus_gold_str='. Начислено '.$ko_bonus_gold.' монет по Акции «Двойная выгода».';	
									$ko_bonus_gold_chat='Внимание!</font> Вам начислено '.$ko_bonus_gold.' монет по Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.';	
									}
							}
		
							mysql_query("UPDATE oldbk.`users` set `gold` = `gold`+'{$kol}' WHERE `id` = '{$tonick['id']}' LIMIT 1;");
							if (mysql_affected_rows()>0)	
							{
							mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('qiwi','124836','".iconv("UTF-8","CP1251",'DarliBank')."','{$fid}','{$tonick[login]}','{$balamount}','{$qiwi[bank_id]}' );");
							mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '124836' LIMIT 1;"); //  снимаем  из баланса  DarliBankа										
		
						      // Партнерка
							CheckRealPartners($tonick['id'],$balamount,0);
							     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
							     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
							     if ($p_user['partner']!='' and $p_user['fraud']!=1)
							      {
							       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('124836','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
							       $id_ins_part_del=mysql_insert_id();
							       $bonus=round(($balamount/100*$partner['percent']),2);
							       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
							      }
			
													//запись в дело!
													$rec['owner']=$tonick[id];
													$rec['owner_login']=$tonick[login];
													$rec['owner_balans_do']=$tonick['money'];
													$rec['owner_balans_posle']=$tonick['money'];
													$rec['target']=124836;
													$rec['target_login']=iconv("UTF-8","CP1251",'DarliBank');
													$rec['type']=3001;
													$rec['sum_kr']=0;
													$rec['sum_ekr']=$balamount;
													$rec['sum_kom']=0;
													$rec['item_id']='';
													$rec['item_name']='Монеты';
													$rec['item_count']=$kol;
													$rec['item_type']=50;
													$tonick['gold']+=$kol;
													$rec['add_info'] = $kol."/".($tonick['gold']).iconv("UTF-8","CP1251",$ko_bonus_gold_str);
													add_to_new_delo($rec);
							
							telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",'Внимание!</font> Вам передано <b>'.$kol.'</b> монет. Удачной игры!')); 
								if ($ko_bonus_gold_chat!='')
									{
									telepost_new($tonick,"<font color=red>".iconv("UTF-8","CP1251",$ko_bonus_gold_chat)); 
									}
									
							make_discount_bonus($tonick,$balamount,1);	
							
							}
							else
								{
								mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
								}
								
						 $resultCode=0;
									
					  
				}
	  else
	  {
	  $resultCode=210;
	    $blog='E1';
	  }


	}
	else
	{
	//ошибка - уже был такой запрос
	$resultCode=210;
	    $blog='E2';	
	}
	
	
	}
	
    }
    else
    {
     $resultCode=0;#успешно код "0"
    }
    
    }

	$fp = fopen('/www/paylogs/qiwi.log','a+');
	fwrite($fp,time().":".$m1['1'].":".$m2['1'].":".$m3['1'].":".$m4['1'].":resultCod:".$resultCode.":".$blog."\r\n");
	fclose($fp);


    $file = fopen('xml', 'r');
    $text = fread($file, filesize('xml'));
    fclose($file);
    $text = str_replace('status',$resultCode , $text);
    header('content-type: text/xml; charset=UTF-8');
    echo $text;
    
?> 