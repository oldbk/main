<?php
/*$_POST['data']='eyJ2ZXJzaW9uIjozLCJwdWJsaWNfa2V5IjoiaTU0NzIyMzE3NjI3IiwiYW1vdW50IjoiMS4wMCIsImN1cnJlbmN5IjoiVVNEIiwiZGVzY3JpcHRpb24iOiLQn9C+0L/QvtC70L3QtdC90LjQtSDQsdCw0L3QutC+0LLRgdC60L7Qs9C+INGB0YfQtdGC0LAg4oSWMjA2MTUg0LTQu9GPINC/0LXRgNGB0L7QvdCw0LbQsDogRGFybGlCYW5rIiwidHlwZSI6ImJ1eSIsIm9yZGVyX2lkIjoiMTAwMDUzIiwibGlxcGF5X29yZGVyX2lkIjoiMjA2MDd1MTQzMDE1MzQ0MjE5ODkzNyIsInN0YXR1cyI6IndhaXRfYWNjZXB0IiwiZXJyX2NvZGUiOm51bGwsInRyYW5zYWN0aW9uX2lkIjo1NjEwNjI1Nywic2VuZGVyX3Bob25lIjoiMzgwOTg5MDA1MDAwIiwic2VuZGVyX2NvbW1pc3Npb24iOjAsInJlY2VpdmVyX2NvbW1pc3Npb24iOjAuNjYsImFnZW50X2NvbW1pc3Npb24iOjB9';
$_POST['signature']='CguVR1CpLHozNvoQDDdhVZa8JlM=';
*/

	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) )     	 	{     	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_CF_CONNECTING_IP'];    	}
	$ip=$_SERVER['REMOTE_ADDR'];

 	$add_info='';

 if (($_POST['data']) AND ($_POST['signature']))
 {
 	if ($ip=='54.229.105.178')
 	{
	//$private_key='D7PO0DyXKDEJRYj5yLAGCaW8KCkv8dAm9neWU9KV'; 
	$private_key='P7ROKlOYV2ARpeBeADF7aQ25b0PTNi0IGrCKhNyN'; 
	
	$test_signature = base64_encode(sha1($private_key.$_POST['data'].$private_key, 1));
 	$in_sig=($_POST['signature']);
 	if (($in_sig==$test_signature) and ($in_sig!='') )
	{
		$in_data=base64_decode($_POST['data']);
	//	$public_key='i54722317627';	
		$public_key='i85877192887';		
		
		$string=json_decode($in_data, true);// получаем масив
		
	if ( ($string['version']==3) AND ($string['public_key']==$public_key) AND ($string['type']=='buy') and (($string['status']=='success') /*OR ($string['status']=='wait_accept')  */ )    ) // отлавливаем только 'success'
	{
	include "connect.php";
	include "functions.php";
	include "bank_functions.php";	
	include "config_ko.php";
	include "clan_kazna.php";
	
	//апдейтим статус
	$order_id=(int)($string['order_id']);
	$transaction_id=$string['transaction_id'];
	$sender_phone=$string['sender_phone'];
	$real_amount=floatval($string['amount']);
	
	mysql_query("update oldbk.trader_balance_liqpay set `status`=1 ,  `transaction_id`='{$transaction_id}' , `sender_phone`='{$sender_phone}'   where `id`='{$order_id}' and `status`=0  ;");  
	if (mysql_affected_rows()>0)
	{
	//статус нормально сменился платеж прошел 
	 $qiwi=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.trader_balance_liqpay WHERE `id`='{$order_id}' ;"));
	 
	 $qiwi[sum_ekr]=floatval($qiwi[sum_ekr]);
	 
	addchp ('Debug LiqPay 1:'.$qiwi[owner_id].'/PARAM:'.$qiwi[param].'/SUM:'.$qiwi[sum_ekr],'{[]}Bred{[]}');		
 	$add_info='start debug log:';	 
 	$add_info.='Debug LiqPay 1:'.$qiwi[owner_id].'/PARAM:'.$qiwi[param].'/SUM:'.$qiwi[sum_ekr];
	 
	 if ($qiwi[sum_ekr]<=$real_amount) // если прилетело больше или сколько надо пропускаем в обработку
	 	{
	 	$add_info='init ';
	 	if ((($qiwi[id]>0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==2018||$qiwi[param]==2118||$qiwi[param]==2218||$qiwi[param]==2318||$qiwi[param]==2418||$qiwi[param]==2518) ) //обработка 
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
			 	$add_info.="/start bilet";			 	
			 	
				if ($tonick['id']) 
				{
					$ss=array(2018=>"", 2118=>"S", 2218=>"M", 2318=>"L", 2418=>"XL", 2518=>"XXL");
					$fsize=$ss[$qiwi[param]];			
			
					if (get_buy_bilet($tonick,$fsize,$balamount,iconv("UTF-8","CP1251",'Банкир')))
					{
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$qiwi[param]}','{$tonick[login]}','{$balamount}');");
						$resultCode=0;
					}
					else
				 	{
				 	$add_info.="/Error get_buy_bilet";
				       	$resultCode=300;	 	
				 	}	
				}
			 	else
			 	{
			 	$add_info.="/Error param bilet";			 	
			       	$resultCode=300;	 	
			 	}			
			  
			  }
			else
		  	  if ((($qiwi[id]>0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==300)  ) //обработка 
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 

				if ($tonick['id']) 
				{
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
				}
				elseif ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
				{
					$kb=act_x2bonus_limit($tonick,2,$add_rep);
					if ($kb>0)
						{
						$ko_bonus_rep=$kb; // для записи в бонусы		
						$add_rep=round($add_rep+$ko_bonus_rep,2);
						$add_bonus_str='. Начислено '.$ko_bonus_rep.' репутации по Акции «Двойная выгода».';	
						$add_bonus_str_chat='<font color=red>Внимание!</font> Вам начислено '.$ko_bonus_rep.' репутации по Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.';	
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
				mysql_query("UPDATE ".$cit[$tonick[id_city]]."`users` SET  `rep`=`rep`+'$add_rep' WHERE `id`= '".$tonick['id']."' LIMIT 1;"); 				
				mysql_query("UPDATE ".$cit[$tonick[id_city]]."`users` SET  `repmoney` = `repmoney` + '$add_rep' WHERE `id`= '".$tonick['id']."' LIMIT 1;"); 				
				if (mysql_affected_rows()>0)
				{
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','300','{$tonick[login]}','{$balamount}');");

					      // Партнерка
						CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
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
					$rec['target']=8383;
					$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
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
					$rec['add_info']=iconv("UTF-8","CP1251",'Через: LiqPay. Баланс реп до '.$tonick[repmoney]. ' после ' .($tonick[repmoney]+$add_rep).$add_bonus_str);
					$tonick[repmoney]+=$add_rep;
					add_to_new_delo($rec); //юзеру
					
					telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам зачислено '.$add_rep.' репутации. Удачной игры!')); 
					
					if ($add_bonus_str_chat!='')
						{
						telepost_new($tonick,iconv("UTF-8","CP1251",$add_bonus_str_chat)); 
						}
					
					
					$dil['id']=8383;
					$dil['login']=iconv("UTF-8","CP1251",'Банкир');
					$get_bankid=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}' and owner='{$tonick[id]}'; "));
					
					if ($get_bankid['id']>0)
						{
						//make_ekr_add_bonus($tonick,$get_bankid,$balamount,$dil);
						}
					make_discount_bonus($tonick,$qiwi['sum_ekr'],3);	
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира	

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
			   if ((($qiwi[id]>0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==666) ) //обработка 
			  	{
  				
			  	//обработка платежей пополнение казны клана
		  	  	$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				
				  	 $tonick = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE id='{$qiwi[owner_id]}'  LIMIT 1;"));
				 	  $dbc='oldbk.';
	 	  			  if ($tonick[id_city]==1)
					  {
					  $tonick = mysql_fetch_array(mysql_query("SELECT * FROM avalon.`users` WHERE id='{$qiwi[owner_id]}' LIMIT 1;"));				
				 	  $dbc='avalon.';	  
					  }
			  	
	  			$klan_name=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$tonick[klan]}' LIMIT 1;")); 
				$kkazna=clan_kazna_have($klan_name[id]);
				if ($kkazna)	  
				{
					$klan_kazna_ekr=$balamount;
		
					$bonekr=get_ekr_addbonus();
					if ($bonekr>0)
					{
					$addbonekr=round(($balamount*$bonekr),2);
					}
		
					if ((time()>$KO_start_time) and (time()<$KO_fin_time))  
					{
					$add_klan_kazna_ekr=round(($klan_kazna_ekr*1.5) ,2); //+10%
					$ko_bonus=$add_klan_kazna_ekr-$klan_kazna_ekr; // для записи в бонусы
					}
					else
					{
					$add_klan_kazna_ekr=$klan_kazna_ekr;
					}
					
				$add_klan_kazna_ekr+=$addbonekr;
								

				if (put_to_kazna($klan_name[id],2,$add_klan_kazna_ekr,$klan_name[short],$tonick,$coment=iconv("UTF-8","CP1251",'Пополнение через LiqPay, от персонажа '.$tonick[login]) ))
			   	{
				$fc_nom=100000000+$klan_name[id];
				$fc_name=iconv("UTF-8","CP1251",'Клан-Казна:«'.$klan_name[short].'»');
				
				mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fc_nom}','".$fc_name."','{$balamount}');");							
				mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира	
				
				if ($ko_bonus > 0)  {  mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('',450,'".iconv("UTF-8","CP1251",'KO')."','{$fc_nom}','{$fc_name}','{$ko_bonus}');"); }
				if ($addbonekr>0) {  mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('',450,'".iconv("UTF-8","CP1251",'KO')."','{$fc_nom}','{$fc_name}','{$addbonekr}');"); }
				
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Пополнен баланс клановой казны на '.($add_klan_kazna_ekr).' екр. '));	
		   		}			
		   			else
		   			{
					addchp (iconv("UTF-8","CP1251",'<font color=red>Внимание!</font>Пополнение казны, пришла оплата liqpay, но ошибка зачсления, teloid:'.$tonick[id]),'{[]}Bred{[]}',-1,-1); 			       				 			   		
		   			}
				}
			else
				{
				addchp (iconv("UTF-8","CP1251",'<font color=red>Внимание!</font>Пополнение казны, пришла оплата , но чел не имееет/заморожена казны, teloid:'.$tonick[id]),'{[]}Bred{[]}',-1,-1); 			       				 			   		

				}				

	  	
		  	    $resultCode=0;
			    
			  }
			  else if ( ($qiwi['owner_id']>0) and ($qiwi['param']==10000) and ($qiwi['sub_trx'] > 0) )
				{
				//обработка платежей субов
					$tonick = check_users_city_data($qiwi['owner_id']);
					$balamount = $qiwi['sum_ekr'];
					$bankid =$qiwi['bank_id'];
					$param=$qiwi['param'];
					$PaymentMethod='liqpay';
					
			  		
					if ($tonick['id']>0)
					{
							$fid = $param; // param
						
							$get_sub_trx=mysql_fetch_array(mysql_query("select * from `oldbk`.`trader_partn_trans` where id='{$qiwi['sub_trx']}' limit 1; "));
														
							if (($get_sub_trx['id']>0) and ( $get_sub_trx['ekr']>=$balamount) )
							{
										mysql_query("UPDATE `oldbk`.`trader_partn_trans` SET `status`=1,`pay_syst`='{$PaymentMethod}' WHERE `id`='{$get_sub_trx['id']}' and  `status`=0  LIMIT 1;");
										if (mysql_affected_rows()>0)
										{	
												
										$ger_par_info=mysql_fetch_array(mysql_query("select * from trader_partners where par_id='{$get_sub_trx['par_id']}' and par_serv='{$get_sub_trx['par_serv']}' limit 1;"));
												
										if ($ger_par_info['id']>0)
													{
													
														//начисление партнеру от его процентов
														$pay_ekr=round($balamount*$ger_par_info['par_rate']*0.01,2);
														mysql_query("UPDATE `oldbk`.`trader_partners` SET `sum_ekr`=`sum_ekr`+'{$pay_ekr}'  WHERE `id`='{$ger_par_info['id']}' limit 1;");
													//логирование
													mysql_query("INSERT INTO oldbk.`dilerdelo` (sub_trx,paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('{$get_sub_trx['id']}','{$PaymentMethod}','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$balamount}','{$bankid}' );");
													mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира	
						
												      	// Партнерка
													CheckRealPartners($tonick['id'],$balamount,0);
													     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
													     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
													     if ($p_user['partner']!='' and $p_user['fraud']!=1)
													      {
													       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
													       $id_ins_part_del=mysql_insert_id();
													       $bonus=round(($balamount/100*$partner['percent']),2);
													       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
													      }
									
																			//запись в дело!
																			$rec['owner']=$tonick[id];
																			$rec['owner_login']=$tonick[login];
																			$rec['owner_balans_do']=$tonick['money'];
																			$rec['owner_balans_posle']=$tonick['money'];
																			$rec['target']=8383;
																			$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
																			$rec['type']=5858;
																			$rec['sum_kr']=0;
																			$rec['sum_ekr']=$balamount;
																			$rec['sum_kom']=0;
																			$rec['item_id']='';
																			$rec['add_info'] = iconv("UTF-8","CP1251",'Оплата счета №'.$get_sub_trx['id'].' - '.$ger_par_info['par_serv_desc'].',  через liqpay '.$balamount);
																			add_to_new_delo($rec);
													
													telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Удачно оплачен счет №'.$get_sub_trx['id'].' - '.$ger_par_info['par_serv_desc'].'. Удачной игры!')); 
													$resultCode=0;
													}
													else
													{
													$resultCode=803;
													}
										}
										else
										{
										$resultCode=802;
										}
							
							}
							else
								{
								$resultCode=801;
								}
						}
						else
						{
						$resultCode=800;
						}
			} 
				else if ( ($qiwi['owner_id']>0) and ($qiwi['param']==1001) )
				{
					$tonick = check_users_city_data($qiwi['owner_id']);
					$balamount = $qiwi['sum_ekr'];
					$bankid =$qiwi['bank_id'];
					$param=$qiwi['param'];
					$PaymentMethod='liqpay';
			  		
					if ($tonick['id']>0)
					{
							$fid = 1001; // оплата лечения
							
							$owntravma=mysql_fetch_array(mysql_query("select * from effects where type=14 and owner='{$tonick['id']}' LIMIT 1"));
							
							if ($owntravma['id']>0)
							{
								
							mysql_query("DELETE FROM `effects` WHERE `id` = '".$owntravma['id']."' LIMIT 1;");
							mysql_query("UPDATE users SET `sila`=`sila`+'{$owntravma['sila']}', `lovk`=`lovk`+'{$owntravma['lovk']}', `inta`=`inta`+'{$owntravma['inta']}' WHERE `id` = '{$tonick['id']}'  LIMIT 1;");
							
								if (!(mysql_affected_rows()>0))
									{
									addchp ('<font color=red>Error update travm 14 </font>','{[]}Bred{[]}');				  	
									}

							mysql_query("UPDATE `oldbk`.`plugin_user_warning` SET `count`=0 WHERE `user_id`='{$tonick['id']}' "); // апдейт статы
										
							mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('{$PaymentMethod}','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$balamount}','{$bankid}' );");
							mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира	

						      	// Партнерка
							CheckRealPartners($tonick['id'],$balamount,0);
							     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
							     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
							     if ($p_user['partner']!='' and $p_user['fraud']!=1)
							      {
							       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
							       $id_ins_part_del=mysql_insert_id();
							       $bonus=round(($balamount/100*$partner['percent']),2);
							       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
							      }
			
													//запись в дело!
													$rec['owner']=$tonick[id];
													$rec['owner_login']=$tonick[login];
													$rec['owner_balans_do']=$tonick['money'];
													$rec['owner_balans_posle']=$tonick['money'];
													$rec['target']=8383;
													$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
													$rec['type']=578;
													$rec['sum_kr']=0;
													$rec['sum_ekr']=0;
													$rec['sum_kom']=0;
													$rec['item_id']='';
													$rec['add_info'] = iconv("UTF-8","CP1251",'Оплата лечения травмы liqpay '.$balamount);
													add_to_new_delo($rec);
							
							telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Ваш персонаж излечен от травм. Удачной игры!')); 

							
							$resultCode=0;
							}
							else
								{
								$resultCode=801;
								}
						}
						else
						{
						$resultCode=800;
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
								 $kol=$balamount*20;
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
									$ko_bonus_gold_chat='<font color=red>Внимание!</font> Вам начислено '.$ko_bonus_gold.' монет по Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.';	
									}
							}
		
							mysql_query("UPDATE oldbk.`users` set `gold` = `gold`+'{$kol}' WHERE `id` = '{$tonick['id']}' LIMIT 1;");
							if (mysql_affected_rows()>0)	
							{
							mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$balamount}','{$qiwi[bank_id]}' );");
							mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
		
						      // Партнерка
							CheckRealPartners($tonick['id'],$balamount,0);
							     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
							     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
							     if ($p_user['partner']!='' and $p_user['fraud']!=1)
							      {
							       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
							       $id_ins_part_del=mysql_insert_id();
							       $bonus=round(($balamount/100*$partner['percent']),2);
							       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
							      }
			
													//запись в дело!
													$rec['owner']=$tonick[id];
													$rec['owner_login']=$tonick[login];
													$rec['owner_balans_do']=$tonick['money'];
													$rec['owner_balans_posle']=$tonick['money'];
													$rec['target']=8383;
													$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
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
							
							telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передано <b>'.$kol.'</b> монет. Удачной игры!')); 
								if ($ko_bonus_gold_chat!='')
									{
									telepost_new($tonick,iconv("UTF-8","CP1251",$ko_bonus_gold_chat)); 
									}
									
							make_discount_bonus($tonick,$balamount,1);	
							
							}
							else
								{
								mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
								}
								
						 $resultCode=0;
									
					  
				}			  
			else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (($qiwi['param']==602||$qiwi['param']==603||$qiwi['param']==606 ) ) ) 
			{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];	
				
				$sklonka = $qiwi['param']-600;
				$ali_cos=15;	
				$sklonka_array=array(2,3,6);
				$eff_align_type=5001;
				$eff_align_time=time()+60*60*24*30*2;

				if ($sklonka == 2) {$skl=iconv("UTF-8","CP1251","нейтральная"); $skl2=iconv("UTF-8","CP1251","нейтральную");}
					elseif ($sklonka == 3) {$skl=iconv("UTF-8","CP1251","темная"); $skl2=iconv("UTF-8","CP1251","темную");}
						 elseif ($sklonka == 6) {$skl=iconv("UTF-8","CP1251","светлая"); $skl2=iconv("UTF-8","CP1251","светлую");}
			
						$cheff=mysql_fetch_array(mysql_query("SELECT * from  effects WHERE type = '".$eff_align_type."' AND owner = '".$tonick['id']."' LIMIT 1;"));
						if ($cheff['id']>0)
						{
						//удаляем то что есть!
						mysql_query("DELETE from  effects WHERE id='{$cheff['id']}' LIMIT 1; ");
						}
				
				
						//склонку
						mysql_query("UPDATE oldbk.`users` set `align` = '{$sklonka}' WHERE `id` = '{$tonick['id']}' LIMIT 1;") ;
				if (mysql_affected_rows()>0)	
					{
						//лог
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$sklonka}','{$tonick[login]}','{$ali_cos}','{$qiwi[bank_id]}' );");
						//баланс
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$ali_cos}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										

						
						//квесты
						$qlist=array();
					        $i=0;
					        $data=mysql_query("SELECT * FROM oldbk.beginers_quest_list WHERE  aganist like '%".$sklonka."%';");
					        while($q_data=mysql_fetch_array($data))
					        {
					     		$qlist[$i]=$q_data[id];
					     		$i++;
					        }

					        mysql_query("UPDATE oldbk.beginers_quests_step set status =1 WHERE owner='".$tonick['id']."' AND quest_id in (".(implode(",",$qlist)).")");
	
						$la=0;
						$last_aligh=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users_last_align` WHERE `owner` = '".$tonick['id']."' LIMIT 1;"));   //тут живет склонка по истечению эфекта
						if($last_aligh[id]>0)
						{
							$la=$last_aligh[align];
						}

						//новый штраф склонки
						if($la!=$sklonka)
						{
							$sql="INSERT INTO effects (`type`, `name`, `owner`, `time`, `add_info`)  VALUES  ('".$eff_align_type."','".iconv("UTF-8","CP1251","Штраф склонки")."','".$tonick['id']."','".$eff_align_time."','".$sklonka."');";
							mysql_query($sql);
							//штрафа нет, добавляем.
						}

						// + абилку сброса
						mysql_query("INSERT INTO `oldbk`.`users_abils` SET `owner`='{$tonick[id]}',`magic_id`=4848,`allcount`='1' ON DUPLICATE KEY UPDATE `allcount`=`allcount`+'1' ;");

						//new_delo
	  		    			$rec['owner']=$tonick[id];
						$rec['owner_login']=$tonick[login];
						$rec['owner_balans_do']=$tonick['money'];
						$rec['owner_balans_posle']=$tonick['money'];
						$rec['target']=8383;
						$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
						$rec['type']=58;//покупка склонки от диллера
						$rec['sum_kr']=0;
						$rec['sum_ekr']=$ali_cos;
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
						$rec['add_info']=$skl;
	
						add_to_new_delo($rec); //юзеру

						//в личку						
						mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tonick['id']."','".iconv("UTF-8","CP1251","Дилер &quot;Банкир&quot; присвоил &quot;").$tonick['login']."&quot; ".$skl2." ".iconv("UTF-8","CP1251","склонность")."','".time()."');");
						//в чат
						telepost_new($tonick,'<font color=red>'.iconv("UTF-8","CP1251","Внимание! ").'</font> '.iconv("UTF-8","CP1251","Абилити «Сброс параметров» х1 добавлена в ").'<a href="javascript:void(0)" onclick='.(!is_array($_SESSION['vk'])?"top.":"parent.").'cht("http://capitalcity.oldbk.com/myabil.php#my")>'.iconv("UTF-8","CP1251","список ваших личных реликтов").'</a>.');	
						
						//бонусные серты
						$dil['id']=8383;
						$dil['login']=iconv("UTF-8","CP1251",'Банкир');
						present_bonus_sert($tonick,$dil);
						
					}
				else
					{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, new-align error . ');");							    										
					}
			 $resultCode=0;
			}				
			else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (in_array($qiwi['param'],$leto_bukets) ) ) //  букетов
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];	
				
				$fid = $qiwi['param'];
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));

				$dress['unik'] = 2;		
				$dress['prototype'] = $fid;	
			
				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=1;
				$werrcount=0;
				$k=0;

					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
									      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!!')); 
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
							
			  
			}				
	  		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==94) ) //обработка  покупка тыквы
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];	
				$fid = 2014103;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$fid}' ;"));
				$dress['ecost'] = 5;
				$dress['prototype'] = $fid;	
				$dress['present'] =iconv("UTF-8","CP1251",'Удача');		
				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=1;
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}
									      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!!')); 
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
							
			  
		}
	  		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==60) ) //обработка  покупка 
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];	
				$fid = 56664;
				
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				$dress['ecost'] = 15;
				$dress['prototype'] = $fid;	

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}
									      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!!')); 
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
							
			  
		}		
		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==90||$qiwi[param]==89) ) //обработка  покупка пропуски
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = 4016;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				$dress['ecost'] = 3;
				$dress['goden']=90;
				$dress['ekr_flag']=1;
				$dress['prototype'] = $fid;	
	
				if ($qiwi[param]==89)
						{
						$prise=2;						
						}
						else
						{
						$prise=3;
						}

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				
				$kol=round($balamount/$prise); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$prise}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$prise}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}


				      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}		
		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==81) ) //обработка  покупка 
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = 3001005;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				$dress['prototype'] = $fid;	

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}


				      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}		
		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==91) ) //обработка  покупка 
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = 100199;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				$dress['ecost'] = 5;
				//$dress['present'] =iconv("UTF-8","CP1251",'Банк');		
				$dress['prototype'] = $fid;	

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}


				      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}		
		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==51 or $qiwi[param]==52 or $qiwi[param]==53 ) ) //обработка  покупка 
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
					if ($qiwi[param]==53)
						{
						$fid = 14200;
						}
					else				
					if ($qiwi[param]==51)
						{
						$fid = 2016614;
						}
						else
						{
						$fid = 2016615;
						}
					$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));

					if ($qiwi[param]==53)
						{
						$dress['ecost'] = 50;
						}
					else					
					if ($qiwi[param]==51)
						{
						$dress['ecost'] = 5;
						}
						else
						{
						$dress['ecost'] = 2;
						}
	
				$dress['prototype'] = $fid;	
				$dress['ekr_flag']=1;		
				
					if ( ($qiwi[param]==51) or ($qiwi[param]==52) )
						{
						$dress['dategoden']=mktime(23,59,59,7,11,2016); 
						$dress['goden'] = round(($dress['dategoden']-time())/60/60/24); if ($dress['goden']<1) {$dress['goden']=1;}
						}
						

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}


				      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}		
	elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==92) ) //обработка  покупка валентинок
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = 910;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$fid}' ;"));
				$dress['ecost'] = 1;
				$dress['prototype'] = $fid;	

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','555','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}


				      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}		
	elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==84) ) //обработка  покупка яиц
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = 2016001;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$fid}' ;"));
				$dress['ecost'] = 5;
				$dress['prototype'] = $fid;	
				
				$dress['dategoden'] = mktime(23,59,59,5,2); //Срок годности яиц ДО 23:59 2 мая.
				$dress['goden'] = round(($dress['dategoden']-time())/60/60/24); 
				if ($dress['goden']<1) {$dress['goden']=1;}

				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}

				      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!!')); 
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}		
	  		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and (in_array($qiwi['param'],$exprun_param))  ) //обработка  покупка свиток опыта рун
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = $qiwi['param'];
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				
				$dress['ecost']=$exprun_prise[$fid];
				$dress['prototype'] = $fid;	
				$dress['ekr_flag']=1;		

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						//make_ekr_add_bonus($tonick,$bank,null,1,1);
						//$bank['ekr']+=1;
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}
									      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
					telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));				
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}
	  		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and (in_array($qiwi['param'],$artup_param))  ) //обработка  покупка свиток опыта рун
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = $qiwi['param'];
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				
				$dress['ecost']=$artup_prise[$fid];
				$dress['prototype'] = $fid;	


				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}
									      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
					telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",'). Удачной игры!'));				
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}				
	  		elseif (($qiwi[id]>0) and ($qiwi[owner_id]>0) and ($qiwi[bank_id]>0)  and ($qiwi[sum_ekr]>0) and ($qiwi[param]==95) ) //обработка  покупка снежинок
		{
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				if (($tonick['id'])  )
				{	
				$fid = 3006000;
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				$dress['ecost'] = 1;

				$dress['prototype'] = $fid;	

				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=round($balamount/$dress['ecost']); //общую сумму в екр делим на стоимость = определяем количество
				$werrcount=0;
				$k=0;
				for ($i=0;$i<$kol;$i++) 
					{
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						make_ekr_add_bonus($tonick,$bank,null,1,1);
						$bank['ekr']+=1;
						mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$dress['ecost']}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
					}
						else
						{
						$werrcount++;
						}
					
					}
				     mysql_query('UPDATE oldbk.variables_int SET value = value + '.$k.' WHERE var = "snowsell"');
									      // Партнерка
					CheckRealPartners($tonick['id'],$balamount,0);
					     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
					     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
					     if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      {
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",') и начислено ').($k).iconv("UTF-8","CP1251",' екр на банковский счёт №').$bankid.iconv("UTF-8","CP1251",'. Удачной игры!!')); 
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> liqpay Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
				}
			 	else
			 	{
			       	$resultCode=300;	 	
			 	}			
			  
		}
			else
	  	  if ((($qiwi[id]>0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==33333) ) //обработка   
			  {
			    //покупка лото
    				$cit[0]='oldbk.';
				$cit[1]='avalon.';
				$cit[2]='angels.';	
				
			    $tonick = check_users_city_data($qiwi[owner_id]);
			
				$balamount = number_format($qiwi[sum_ekr],2,'.','');
				
				$bilprice=2; //2 екр - билет
				$kol=round($balamount/$bilprice);
				
				if ($tonick['id']) 
				{
				
				$get_lot=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;"));
				if ($get_lot[id] >0)
				     {
					//if (($get_lot[lotodate]-300) >=time() ) // отключаем время
					{
					for ($kkk=1;$kkk<=$kol;$kkk++)
					{
					if(mysql_query("INSERT INTO oldbk.`item_loto` SET `loto`={$get_lot[id]},`owner`={$tonick[id]},`dil`=8383,`lotodate`='".date("Y-m-d H:i:s",$get_lot[lotodate])."';"))
					{
					$good = 1;
					$new_bil_id=mysql_insert_id();
					mysql_query("INSERT INTO oldbk.`inventory` (`getfrom`,`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`,`ekr_flag`)
					VALUES (30,'".iconv("UTF-8","CP1251",'Лотерейный билет ОлдБк')."',0,1,0,{$tonick[id]},0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'oldbkloto.gif','',0,0,0,0,0,0,0,210,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".iconv("UTF-8","CP1251","Билет №".$new_bil_id."<br>Тираж №".$get_lot[id]."<br>Cостоится ".date("Y-m-d H:i:s",$get_lot[lotodate]))."',1,'".date("Y-m-d H:i:s")."',0,33333,'6',0,0,0,0,0,'',0,0,0,0,0,0,0,{$get_lot[id]},0,0,{$new_bil_id},0,0,0,NULL,0,0,0,0,NULL,'',0,0,2,0,NULL,0,NULL,NULL,0,'{$tonick[id_city]}','1');");
					$dress[id]=mysql_insert_id();
					$dress[idcity]=$tonick[id_city];
					}
					else 
					{
						$good = 0;
					}

				if ($good) 
					{
					mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr,addition) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','0','{$tonick['login']}','$bilprice','33333');");
					$id_ins_part_del=mysql_insert_id();
					//new_delo
  		    			$rec['owner']=$tonick[id];
					$rec['owner_login']=$tonick[login];
					$rec['owner_balans_do']=$tonick['money'];
					$rec['owner_balans_posle']=$tonick['money'];
					$rec['target']=8383;
					$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
					$rec['type']=54;//получил предмет от диллера
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$bilprice;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=iconv("UTF-8","CP1251",'Лотерейный билет ОлдБк');
					$rec['item_count']=1;
					$rec['item_type']=210;
					$rec['item_cost']=0;
					$rec['item_dur']=0;
					$rec['item_maxdur']=1;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['bank_id']='';
					$rec['add_info']=iconv("UTF-8","CP1251",'через LiqPay');
					add_to_new_delo($rec); //юзеру


					  // Партнерка
					CheckRealPartners($tonick['id'],$bilprice,0);
				    	 $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
				     	 $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
				   	  if ($p_user['partner']!='' and $p_user['fraud']!=1)
					      	 {
				       	 	mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$tonick['id']}','{$bilprice}','0','".time()."');");
					      	 $bonus=round(($bilprice/100*$partner['percent']),2);
					      	 mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$bilprice}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					     	 }
					}
					}
					
					if ($good) 
					{
					 $resultCode=0;
					 
					 if ($tonick['odate'] > (time()-60) )
					{
						addchp(iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передано <b>Лотерейный билет ОлдБк x'.$kol.' </b>. Спасибо за покупку!'),'{[]}'.$tonick['login'].'{[]}',$tonick['room'],$tonick['id_city']);
					} else {
						// если в офе
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$tonick['id']."','','".iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передано <b>Лотерейный билет ОлдБк x'.$kol.'</b>. Спасибо за покупку! ')."');");
					}
					 
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира 
					 
					}
					else
				 	{
				       	$resultCode=300;	 	
				 	}
					
				    }
				
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
	  if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==0) )
	  {
	  //обработка ЕКР
	  
	  $tonick = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE id='{$qiwi[owner_id]}'  LIMIT 1;"));
	  if ($tonick[id_city]==1)
	  {
	  $tonick = mysql_fetch_array(mysql_query("SELECT * FROM avalon.`users` WHERE id='{$qiwi[owner_id]}' LIMIT 1;"));				
	  }
	  
	  
				/*  if (BONUS_REAL_PREM)
					{
	  				$pbm[0]=0;
					$pbm[1]=0.01;
					$pbm[2]=0.02;
					$pbm[3]=0.05;
					$pbm=$pbm[$tonick['prem']];
					}
					else
				*/
					{
					$pbm=0;// выключаем бонусы
					}
	  
	  
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
					$rec['target']=8383;
					$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
					$rec['type']=261;
					$rec['sum_ekr']=$qiwi[sum_ekr]+$ko_bonus;
					$rec['bank_id']=$qiwi[bank_id];
					$rec['add_info']=iconv("UTF-8","CP1251",'Пополнение LiqPay');
					add_to_new_delo($rec); 
					mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$qiwi[sum_ekr]}');");					
					mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','".iconv("UTF-8","CP1251","Вы пополнили свой счет  на сумму <b>{$qiwi[sum_ekr]}</b> екр. <i>(Итого: {$rezbank['cr']} кр., {$rezbank['ekr']} екр.)</i>")."','{$qiwi[bank_id]}');");					
					
					telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведено '.($qiwi[sum_ekr]).' екр. Спасибо за покупку!'));	
					
					//запись для бонуса
					if ($ko_bonus > 0)					
					{
					mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('','450','".iconv("UTF-8","CP1251",'KO')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$ko_bonus}');");					
					mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','".iconv("UTF-8","CP1251","Бонус за покупку еврокредитов <b>{$ko_bonus}</b> екр.")."','{$qiwi[bank_id]}');");		
					
					if ((time()>$KO_start_time) and (time()<$KO_fin_time)) 
							{
							telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведен бонус:'.($ko_bonus).' екр. в рамках <a href="http://oldbk.com/encicl/?/act_ekr.html" target="_blank">акции</a>. '));	
							}
					elseif ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
							{
							telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведено '.$ko_bonus.' екр. от  Коммерческого отдела, в рамках Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.'));	
							}							
							else
							{
							$yyy[1]='Silver';
							$yyy[2]='Gold';
							$yyy[3]='Platinum';
							
							telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведен бонус:'.($ko_bonus).' екр. за наличие "'.$yyy[$tonick[prem]].' account" '));
							}
					
								
					}

					 // Партнерка
					CheckRealPartners($qiwi[owner_id],$qiwi[sum_ekr],0);
					 $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$qiwi[owner_id]}' LIMIT 1;"));
				     	 $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
				   	 if ($p_user['partner']!='' and $p_user['fraud']!=1)
				       {
				       	 mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$qiwi[owner_id]}','{$qiwi[sum_ekr]}','{$qiwi[bank_id]}','".time()."');");
				      	 $bonus=round(($qiwi[sum_ekr]/100*$partner['percent']),2);
				      	 mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$qiwi[sum_ekr]}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
				      }
					
					$dil['id']=8383;
					$dil['login']=iconv("UTF-8","CP1251",'Банкир');
					$get_bankid=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}' and owner='{$tonick[id]}'; "));
					if ($get_bankid['id']>0)
						{
						//make_ekr_add_bonus($tonick,$get_bankid,$qiwi[sum_ekr],$dil);
						}
					make_discount_bonus($tonick,$qiwi[sum_ekr],2);	
	       $resultCode=0;
      					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира
	       }
	       else
	       {
	       	$resultCode=300;
	       }
	       	  	  
	  }
	  else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (in_array($qiwi[param],$akks_types))  )
	  {
	  //обработка акков
	  
	  					  if ($qiwi[param]>300)
						{
						//платина
						$sub_type=$qiwi[param]-300;
						$qiwi[param]=3;
						}
						elseif ($qiwi[param]>200)
						{
						//голд
						$sub_type=$qiwi[param]-200;
						$qiwi[param]=2;
						}
						else
						{
						//сильвер
						$sub_type=$qiwi[param]-100;
						$qiwi[param]=1;
						}
	  
		$tonick = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$qiwi[owner_id]}' LIMIT 1;"));
		$tonick = check_users_city_data($tonick[id]);
		
		$chbnkpr= mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = '".$tonick[id]."' AND id='".$qiwi[bank_id]."' LIMIT 1;"));
		if($tonick[id]>0 && $chbnkpr[id]>0)
		{
			if ( (($tonick[prem]==$qiwi[param]) or ($tonick[prem]==0) ) and ($tonick[id]>0))		    		    
		    	{
		    	$dill[id_city]=0;
		    	$dill[id]=8383;
		    	$dill[login]=iconv("UTF-8","CP1251",'Банкир');
			$dill[paysys]='liqpay';		    			    			    	
		    	
		    		$exp=main_prem_akk($tonick,$qiwi[param],$dill,$sub_type);
				if ($exp>0)
					{
					by_prem_from_bank($tonick,$qiwi[param],$chbnkpr,$exp,$dill,$sub_type);
					
					$resultCode=0;	 	
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира					
					}
				 	else
				 	{
		       			$resultCode=300;
 							//addchp ('<font color=red>Внимание!</font>Debug1 qiwi','{[]}Bred{[]}'); 			       				 	
				 	}
		    	}
		 	else
		 	{
		       	$resultCode=300;	 	
 							//addchp ('<font color=red>Внимание!</font>Debug2 qiwi','{[]}Bred{[]}'); 			       	
		 	}
	  	}
	  	else
	 	{
		       	$resultCode=300;	
 							//addchp ('<font color=red>Внимание!</font>Debug3 qiwi','{[]}Bred{[]}'); 	
	 	}
	  }
	  else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (($qiwi[param]>=2014001) and ($qiwi[param]<=2014004) )  )
	  {
	  //обработка ларцов
		$tonick = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$qiwi[owner_id]}' LIMIT 1;"));
		$tonick = check_users_city_data($tonick[id]);
		
		$par=$qiwi[param];
		$lartid=$par;
		if (test_larec($larec_type[$par],$tonick))
			{
			//тест на покупку ларца прошел - выдаем ларец
				$get_my_box=mysql_fetch_array(mysql_query("select * from oldbk.boxs where box_type='{$larec_type[$par]}' and item_id=0 ORDER BY id  LIMIT 1"));
				if ($get_my_box[id] > 0) {
					$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$lartid}' ;"));
					if ($dress[id]>0)  {
						mysql_query("update oldbk.boxs set item_id=1 where id='{$get_my_box[id]}' ;");
						if (mysql_affected_rows() > 0) {
						
							$goden_do = mktime(23,59,59,1,31,date("Y")+1); 
							$goden = round(($goden_do-time())/60/60/24); if ($goden<1) {$goden=1;}
								
								if(mysql_query("INSERT INTO oldbk.`inventory`
									(`getfrom`,`prototype`,`owner`, `sowner` ,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,
									`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
									`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
									`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`, `present`,`ekr_flag`
									)
									VALUES
									(30,'{$dress['id']}','{$tonick['id']}','0','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
									'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}',
									'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$goden_do."',
									'{$goden}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$tonick[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}', '','{$dress['ekr_flag']}'
									) ;"))
								{
									$larbox_id=mysql_insert_id();
									mysql_query("update oldbk.boxs set item_id='{$larbox_id}' where id='{$get_my_box[id]}' ;");							
									$good = 1;
									$dress[id]=$larbox_id;
								} else 
								{
									$good = 0;
								}
	
								if ($good) 
								{
									mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr,addition) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$qiwi[sum_ekr]}','2013');");														
									mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира										
									//new_delo
				  		    			$rec['owner']=$tonick['id'];
									$rec['owner_login']=$tonick['login'];
									$rec['owner_balans_do']=$tonick['money'];
									$rec['owner_balans_posle']=$tonick['money'];
									$rec['target']=83;
									$rec['target_login']=iconv("UTF-8","CP1251",'Коммерческий отдел');
									$rec['type']=54;//получил предмет от диллера
									$rec['sum_kr']=0;
									$rec['sum_ekr']=$dress['ecost'];
									$rec['sum_kom']=0;
									$rec['item_id']=get_item_fid($dress);
									$rec['item_name']=$dress[name];
									$rec['item_count']=1;
									$rec['item_type']=$dress['type'];
									$rec['item_cost']=$dress['cost'];
									$rec['item_dur']=$dress['duration'];
									$rec['item_maxdur']=$dress['maxdur'];
									$rec['item_ups']=$dress['ups'];
									$rec['item_unic']=$dress['unic'];
									$rec['item_incmagic']=$dress['includemagicname'];
									$rec['item_incmagic_count']=$dress['includemagicuses'];
									$rec['item_arsenal']='';
									$rec['bank_id']='';
									$rec['item_proto']=$dress['prototype'];
									$rec['item_sowner']=($dress['sowner']>0?1:0);
									$rec['item_incmagic_id']=$dress['includemagic'];
									$rec['add_info']=iconv("UTF-8","CP1251",'через Liqpay');
									add_to_new_delo($rec); //юзеру
	
									mysql_query('INSERT INTO oldbk.boxs_history (`owner`,`box_type`,`selldate`) VALUES("'.$tonick['id'].'","'.$larec_type[$par].'","'.date("d/m/Y").'")');
									
									 // Партнерка
									CheckRealPartners($qiwi[owner_id],$qiwi[sum_ekr],0);
									 $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$qiwi[owner_id]}' LIMIT 1;"));
								     	 $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
								   	 if ($p_user['partner']!='' and $p_user['fraud']!=1)
									      {
									       	 mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$qiwi[owner_id]}','{$qiwi[sum_ekr]}','{$qiwi[bank_id]}','".time()."');");
									      	 $bonus=round(($qiwi[sum_ekr]/100*$partner['percent']),2);
									      	 mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$qiwi[sum_ekr]}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
									      }
									      
									telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress['name'].iconv("UTF-8","CP1251",'</b> 1шт. Удачной игры!!'));	
									      
		     	 						$resultCode=0;	 
								}
								else
									{
									// пишем челу что ошибка надо сообщить администрации
									telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Ошибка! Невозможно передать Ларец - сообщите Администрации!. '));	
								       	$resultCode=304;
									}
						} 
						else
						{
						// пишем челу что ошибка надо сообщить администрации
						telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Ошибка! Невозможно передать Ларец - сообщите Администрации!. '));	
					       	$resultCode=303;
						}
						
		   			} 
				}
				else
				{
				// пишем челу что ошибка надо сообщить администрации
				telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Ошибка! Невозможно передать Ларец - сообщите Администрации!. '));	
			       	$resultCode=302;
				}

			}
			else
			{
			// пишем челу что ошибка надо сообщить администрации
			telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Ошибка! Невозможно передать Ларец - сообщите Администрации!. '));	
		       	$resultCode=301;
			}
	  }
else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (in_array($qiwi[param],$bukets) ) )
	  	{	
  		addchp ('Debug 2 liqpay if:'.$qiwi[owner_id].'/PARAM:'.$qiwi[param].'/SUM:'.$qiwi[sum_ekr],'{[]}Bred{[]}');			  	
 			 //покупка елок
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				require_once('ny_events.php');
				
				if (($tonick['id']) AND ((time() > $ny_events['elkadropstart'] && time() < $ny_events['elkadropend'])) ) 
				{
				$tobank	= mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = '{$bankid}' LIMIT 1;"));
				$elkaid=(int)$qiwi[param];
				$prise=$bukets_prise[$elkaid];
				
				if ($balamount>=$prise)
				{				
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$elkaid}' ;"));
				$bukname=$dress[name];

				$elkagoden = $ny_events_cur_m == 12 ? mktime(23,59,59,2,29,$ny_events_cur_y+1) : mktime(23,59,59,2,29,$ny_events_cur_y);
				$elkatime = time()+($dress['goden']*3600*24);
				if ($elkatime > $elkagoden) { 		$elkatime = $elkagoden; 		}
		
				mysql_query("INSERT INTO oldbk.`inventory`
					(`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
						`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`ekr_flag`,`stbonus`
					)
					VALUES
					(30,'{$dress['id']}','{$tonick['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}',
					'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$elkatime."',
					'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$user[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}','{$dress['ekr_flag']}','{$dress['stbonus']}'
					) ;");

			if (mysql_affected_rows()>0)					
				{
					$buket_id=mysql_insert_id();
					$good = 1;
					$dress[id]=$buket_id;
					
					mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','2011','{$tonick[login]}','{$prise}','{$qiwi[bank_id]}' );");
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$prise}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира																											
					
					//new_delo
  		    			$rec['owner']=$tonick[id];
					$rec['owner_login']=$tonick[login];
					$rec['owner_balans_do']=$tonick['money'];
					$rec['owner_balans_posle']=$tonick['money'];
					$rec['target']=8383;
					$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
					$rec['type']=54;//получил предмет от диллера
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$dress['ecost'];
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress[name];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress['ups'];
					$rec['item_unic']=$dress['unic'];
					$rec['item_incmagic']=$dress['includemagicname'];
					$rec['item_incmagic_count']=$dress['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['bank_id']=$bankid;
					$rec['item_proto']=$dress['prototype'];
					$rec['item_sowner']=($dress['sowner']>0?1:0);
					$rec['item_incmagic_id']=$dress['includemagic'];
					$rec['add_info']=iconv("UTF-8","CP1251",'через Liqpay');
					add_to_new_delo($rec); //юзеру


												      // Партнерка
												     CheckRealPartners($tonick['id'],$balamount,0);
												     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
												     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
												     if ($p_user['partner']!='' and $p_user['fraud']!=1)
												      {
												       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('83','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
												       $id_ins_part_del=mysql_insert_id();
												       $bonus=round(($balamount/100*$partner['percent']),2);
												       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
												      }
					telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передана <b>').$bukname.iconv("UTF-8","CP1251",'</b>. Удачной игры!'));	
  			    		$resultCode=0;	 
					/*
					//защита от травм
					$dr=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id=55555"));
					$dr['ecost'] = 1;
					$dr['prototype'] = 55555;
					$dr['sowner']=$tonick['id'];				
					$dr['present'] =iconv("UTF-8","CP1251",'Удача');							
					by_item_bank_dil($tonick,$dr,null);
					*/
			}	else  $resultCode=311;	 
			} 	else  $resultCode=312;	 
			} 	else  $resultCode=313;	 	  	
	}
	  else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (in_array($qiwi[param],$podar) ) )
	  	{
	  	//покупка подарочных сертификатов
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];
				$param=$qiwi[param];
				if ($tonick['id']) 
				{
				require_once('ny_events.php');
				$tobank	= mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = '{$bankid}' LIMIT 1;"));
				$total_summ=0;
				$prise=$podar_prise[$param];				
				$kol=round($balamount/$prise);
										for ($kkk=1;$kkk<=$kol;$kkk++)
										{
										$ekr_bonus=round(($prise*$podar_ekr[$param]),2);
										$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$param}' ;"));
											if ($dress[id]>0)
											  {
								
											        if (time() >= $ny_events['sertstart'] && time() <= $ny_events['sertend']) {
													$dress['img'] = $podarny_img[$dress['id']];
												}              
								
											        if (time() >= mktime(0,0,0,2,13,$ny_events_cur_y) && time() <= mktime(23,59,59,2,20,$ny_events_cur_y)) {
													$dress['img'] = $podarvl_img[$dress['id']];
													reset($podarvl);
													while(list($ka,$va) = each($podarvl)) {
														if ($dress['id'] == $va) {
															$dress['name'] = $ka;
														}
													}
												}              
								
								
											        if (time() >= mktime(0,0,0,2,21,$ny_events_cur_y) && time() <= mktime(23,59,59,3,4,$ny_events_cur_y)) {
													$dress['img'] = $podar23_img[$dress['id']];
													reset($podar23);
													while(list($ka,$va) = each($podar23)) {
														if ($dress['id'] == $va) {
															$dress['name'] = $ka;
														}
													}
												}              
								
								
								
											        if (time() >= mktime(0,0,0,3,5,$ny_events_cur_y) && time() <= mktime(23,59,59,3,31,$ny_events_cur_y)) {
													$dress['img'] = $podar8_img[$dress['id']];
													reset($podar8);
													while(list($ka,$va) = each($podar8)) {
														if ($dress['id'] == $va) {
															$dress['name'] = $ka;
														}
													}
												}              
								
								
												mysql_query("INSERT INTO oldbk.`inventory`
													(`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`maxdur`,`isrep`,
														`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
														`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
														`otdel`,`gmp`,`gmeshok`, `group`,`letter` ".$str." , `ab_mf`,`ab_bron`,`ab_uron`,`sowner`,`unik`
													)
													VALUES
													(30,'{$dress['id']}','{$tonick['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},{$dress['ecost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
													'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
													'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'
													,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}' ".$sql." ,'{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$tonick['id']}',2
													) ;");
											if (mysql_affected_rows()>0)
													{
														$good = 1;
														$dress['prototype']=$dress[id];
														$new_vid=mysql_insert_id();
														$dress[id]=$new_vid;
														$dress['idcity']=0;
												
												mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('liqpay','8383','".iconv("UTF-8","CP1251",'Банкир')."','55','{$tonick[login]}','{$prise}','{$qiwi[bank_id]}' );");

												//new_delo
													$rec=array();
								  		    			$rec['owner']=$tonick[id];
													$rec['owner_login']=$tonick[login];
													$rec['owner_balans_do']=$tonick['money'];
													$rec['owner_balans_posle']=$tonick['money'];
													$rec['target']=8383;
													$rec['target_login']=iconv("UTF-8","CP1251",'Банкир');
													$rec['type']=82;//получил подарочный сертификат от диллера
													$rec['sum_kr']=0;
													$rec['sum_ekr']=$prise;
													$rec['sum_kom']=0;
													$rec['item_id']=get_item_fid($dress);
													$rec['item_name']=$dress[name];
													$rec['item_count']=1;
													$rec['item_type']=$dress['type'];
													$rec['item_cost']=$dress['cost'];
													$rec['item_dur']=$dress['duration'];
													$rec['item_maxdur']=$dress['maxdur'];
													$rec['item_ups']=$dress['ups'];
													$rec['item_unic']=$dress['unic'];
													$rec['item_incmagic']=$dress['includemagicname'];
													$rec['item_incmagic_count']=$dress['includemagicuses'];
													$rec['item_arsenal']='';
													$rec['add_info']=$ekr_bonus;
													$rec['bank_id']=$bankid;
													$rec['item_proto']=$dress['prototype'];
													$rec['item_sowner']=($dress['sowner']>0?1:0);
													$rec['item_incmagic_id']=$dress['includemagic'];
													$rec['add_info']=iconv("UTF-8","CP1251",'через LiqPay');
													add_to_new_delo($rec); //юзеру
													
												mysql_query("UPDATE oldbk.`bank` set `ekr` = ekr+'{$ekr_bonus}' WHERE `id` = '{$bankid}' LIMIT 1;");
												$tobank['ekr']+=$ekr_bonus;
												mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','".iconv("UTF-8","CP1251","Бонус за покупку подарочного сертификата<b> {$ekr_bonus} екр.</b>,<i>(Итого: {$tobank[cr]} кр., {$tobank['ekr']} екр.)</i>")."','{$bankid}');");
												$total_summ+=$prise;
												telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress['name'].iconv("UTF-8","CP1251",'</b> и ').$ekr_bonus.iconv("UTF-8","CP1251",'екр на счет №').$bankid.iconv("UTF-8","CP1251",'. Спасибо за покупку!'));	
													}
												}
											  } //for

												      // Партнерка на всю сумму
												CheckRealPartners($tonick['id'],$balamount,0);
												     $p_user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners_users` WHERE `id` = '{$tonick['id']}' LIMIT 1;"));
												     $partner = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`partners` WHERE `id` = '{$p_user['partner']}' LIMIT 1;"));
												     if ($p_user['partner']!='' and $p_user['fraud']!=1)
												      {
												       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
												       $id_ins_part_del=mysql_insert_id();
												       $bonus=round(($balamount/100*$partner['percent']),2);
												       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
												      }											
											  
									//бонус
								//	make_ekr_add_bonus($tonick,$tobank,$total_summ,null);	

  			    		$resultCode=0;	 	
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира
	  			        }	  	
  	
	  	}
	  else
	  {
	  $resultCode=210;
	  }
	
				/* http://tickets.oldbk.com/issue/oldbk-2069#tab=Comments
				
				 if (($resultCode==0) and ($tonick['id']>0) and ($qiwi[sum_ekr]>=500))
				{
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='636' ;"));
				$dress['present']=iconv("UTF-8","CP1251",'Мироздатель');
				if (by_item_bank_dil($tonick,$dress,null,1))
					{
					telepost_new($tonick,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> Вы получили <b>').$dress[name].iconv("UTF-8","CP1251",'</b> (x').$k.iconv("UTF-8","CP1251",')  за оптовую покупку игровой валюты.')); 
					}
				
				}
				*/
	
	
		}
		else
		{
		$resultCode=211;
		$bred['id']=14897;
		$bred['login']='Bred';
		telepost_new($bred,iconv("UTF-8","CP1251",'<font color=red>Внимание!</font> LiqPay Попытка чита! login:'.$qiwi['owner'])); 
		
		}

	}
	else
	{
	//ошибка - уже был такой запрос
	$resultCode=210;
	}

	}
	else
	{
	$resultCode=112;
	}	

	
	}
	else
	{
	$resultCode=111;
	}	
	
	}
	else
		{
		$in_data=base64_decode($_POST['data']);
		$string=json_decode($in_data, true);// получаем масив		
		$resultCode='DENY FOR THIS IP';
		}
	
	$re = print_r($string, true);
	$fp = fopen('/www/paylogs/liqpay.log','a+');
	fwrite($fp, "ip - " .$ip . "\n");		
	fwrite($fp,time().":Result_CODE:".$resultCode.":".$re." \r\n");
	fwrite($fp,time().":D:".$add_info.":".$re." \r\n");	

	$repo = print_r($_POST, true);	
	fwrite($fp,time().":POST::".$repo." \r\n");
	fclose($fp);

    $text = "status:".$resultCode;
    header('content-type: text/html; charset=UTF-8');
    echo $text;
 }
 	else
 	{
 	echo "Server:ok";
 	}   
?> 