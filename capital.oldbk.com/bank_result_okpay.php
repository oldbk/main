<?
/*
пример от сервера 
ip - 78.140.166.69
ok_charset=utf-8
ok_receiver=admin@oldbk.com
ok_receiver_id=968293980
ok_receiver_wallet=OK635569857
ok_receiver_email=admin@oldbk.com
ok_txn_id=5813014 - транза в окпай
ok_txn_kind=payment_link
ok_txn_payment_type=instant
ok_txn_payment_method=SBR
ok_txn_gross=1.00
ok_txn_amount=0.95
ok_txn_net=0.95
ok_txn_fee=0.05
ok_txn_currency=USD
ok_txn_datetime=2016-11-18 10:28:06
ok_txn_status=completed
ok_invoice=
ok_payer_first_name=Anonymous
ok_payer_last_name=Anonymous
ok_payer_email=
ok_payer_country=
ok_payer_city=
ok_payer_country_code=
ok_payer_state=
ok_payer_address_status=
ok_payer_street=
ok_payer_zip=
ok_payer_address_name=
ok_payer_phone=
ok_items_count=1
ok_item_1_name=Пополнение №2221 для персонажа: Подмастерье
ok_item_1_article=5000010 - ид у нас
ok_item_1_type=service
ok_item_1_quantity=1
ok_item_1_gross=1.00
ok_item_1_price=1.00
ok_ipn_id=3163920
*/


	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) )     	 	{     	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_CF_CONNECTING_IP'];    	}
	$ip=$_SERVER['REMOTE_ADDR'];
//логирование
$log = fopen("/www/paylogs/okpay.log", "a");
$logdata='';
fwrite($log, "input - " . gmstrftime ("%b %d %Y %H:%M:%S", time()) . "\n");
fwrite($log, "ip - " .$ip . "\n");
$logdata.="ip:" .$ip . "\n";
foreach($_POST as $key=>$val)
{
   fwrite($log,$key."=".$val."\n");
   $logdata.=$key."=".$val."\n";
}


	//обработка
	if (($ip=='78.140.166.69') and ($_POST['ok_txn_status']=='completed')) // прилетел от куда надо и оплачен
	{
	include "connect.php";
	include "functions.php";
	include "bank_functions.php";	
	include "clan_kazna.php";	
	include "config_ko.php";

	$order_id=(int)($_POST['ok_item_1_article']); // ид у нас
	$transaction_id=$_POST['ok_txn_id']; // их ид
	$PaymentMethod=mysql_real_escape_string($_POST['ok_txn_payment_method']);
	$ok_txn_gross=$_POST['ok_txn_gross'];
	$ok_txn_amount=$_POST['ok_txn_amount'];
	$ok_txn_net=$_POST['ok_txn_net'];
	$ok_txn_fee=$_POST['ok_txn_fee'];
	$ok_txn_datetime=$_POST['ok_txn_datetime'];
	
	$ok_txn_currency=$_POST['ok_txn_currency'];//USD
	
	$pmail=mysql_real_escape_string($_POST['ok_payer_email']);


	/*  для тестирования ok_item_1_article=5000010&ok_txn_id=5813014&ok_txn_payment_method=SBR&ok_txn_gross=1.00&ok_txn_amount=0.95&ok_txn_net=0.95&ok_txn_fee=0.05&ok_txn_datetime=2016-11-18 10:28:06&ok_payer_email=
	
	$order_id=(int)($_GET['ok_item_1_article']); // ид у нас
	$transaction_id=$_GET['ok_txn_id']; // их ид
	$PaymentMethod=mysql_real_escape_string($_GET['ok_txn_payment_method']);
	$ok_txn_gross=$_GET['ok_txn_gross'];
	$ok_txn_amount=$_GET['ok_txn_amount'];
	$ok_txn_net=$_GET['ok_txn_net'];
	$ok_txn_fee=$_GET['ok_txn_fee'];
	$ok_txn_datetime=$_GET['ok_txn_datetime'];
	$pmail=mysql_real_escape_string($_GET['ok_payer_email']);
	*/
	
	
	$logdata=mysql_real_escape_string($logdata);

	
	mysql_query("UPDATE `oldbk`.`trader_balance_okpay` SET status=1, 
													 
													 `ok_txn_amount`='{$ok_txn_amount}',
													 `ok_txn_net`='{$ok_txn_net}',
													 `ok_txn_fee`='{$ok_txn_fee}',
													 `ok_txn_datetime`='{$ok_txn_datetime}',
													 `trans`='{$transaction_id}',
													 `PaymentMethod`='{$PaymentMethod}',
													 `payer_email`='{$pmail}',
													 `logdata`='{$logdata}' WHERE `id`='{$order_id}' and `status`=0  ;");  
	
	if (mysql_affected_rows()>0)
	{
	$PaymentMethod='okpay-'.$PaymentMethod;
	//статус нормально сменился платеж прошел 
	 $qiwi=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.trader_balance_okpay WHERE `id`='{$order_id}' ;"));
	 
	 if (($ok_txn_gross>=$qiwi[sum_ekr]) and ($ok_txn_currency==$qiwi['currency']) )
		{	 
		   if ((($qiwi[id]>0) and ($qiwi[owner_id]>0)  and ($qiwi[sum_ekr]>0) ) and ($qiwi[param]==300) ) //обработка 
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 

				if ($tonick['id']>0) 
				{
				$add_rep=round($balamount * 600);
				$cit[0]='oldbk.';
				$cit[1]='avalon.';
				$cit[2]='angels.';	
				
				
	  							
				if ((time()>$KO_start_time7) and (time()<$KO_fin_time7)) 
				{
					$ko_bonus_rep=$add_rep; // для записи в бонусы
					$add_rep=round(($add_rep*(1+$KO_A_BONUS7)) ,2); //+%
					$ko_bonus_rep=$add_rep-$ko_bonus_rep; // для записи в бонусы		
					$add_bonus_str=iconv("CP1251","CP1251",' Добавлено по акции +'.($KO_A_BONUS7*100).'% ('.$ko_bonus_rep.'реп) ');
				}
				else
				{
					$add_bonus_str='';
				}
					
				mysql_query("UPDATE ".$cit[$tonick[id_city]]."`users` SET  `rep`=`rep`+'$add_rep' WHERE `id`= '".$tonick['id']."' LIMIT 1;"); 
				mysql_query("UPDATE ".$cit[$tonick[id_city]]."`users` SET  `repmoney` = `repmoney` + '$add_rep' WHERE `id`= '".$tonick['id']."' LIMIT 1;"); 
				if (mysql_affected_rows()>0)
				{
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr) values ('{$PaymentMethod}','8383','".iconv("CP1251","CP1251",'Банкир')."','300','{$tonick[login]}','{$balamount}');");

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
							$rec['target_login']=iconv("CP1251","CP1251",'Банкир');
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
							$rec['add_info']=iconv("CP1251","CP1251",'Через: OkPay. Баланс реп до '.$tonick[repmoney]. ' после ' .($tonick[repmoney]+$add_rep).$add_bonus_str);
							add_to_new_delo($rec); //юзеру


					if ($tonick['odate'] > (time()-60))
					{
						addchp(iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Вам  зачислено '.$add_rep.' репутации.'),'{[]}'.$tonick['login'].'{[]}',$tonick['room'],$tonick['id_city']);
					} else 
					{
						// если в офе
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$tonick['id']."','','".iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Вам  зачислено '.$add_rep.' репутации.')."');");
					}
					
					$dil['id']=8383;
					$dil['login']=iconv("CP1251","CP1251",'Банкир');
					$get_bankid=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}' and owner='{$tonick[id]}'; "));
					
					mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира	
					$resultCode=0;
				}
				else
			 	{
			       	$resultCode=301;	 	
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
			  	
				addchp ('<font color=red>Debug to kazna: (Okpay) add ekr</font>:'.$balamount,'{[]}Bred{[]}');				  	
  				
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
								

				if (put_to_kazna($klan_name[id],2,$add_klan_kazna_ekr,$klan_name[short],$tonick,$coment='Пополнение через Okpay, от персонажа '.$tonick[login]))
			   	{
				$fc_nom=100000000+$klan_name[id];
				$fc_name=iconv("CP1251","CP1251",'Клан-Казна:«'.$klan_name[short].'»');			
				
				mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('{$PaymentMethod}','8383','Банкир','{$fc_nom}','".$fc_name."','{$balamount}');");							
				mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$balamount}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира	
				
				if ($ko_bonus > 0)  {  mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('',450,'KO','{$fc_nom}','{$fc_name}','{$ko_bonus}');"); }
				if ($addbonekr>0) {  mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('',450,'KO','{$fc_nom}','{$fc_name}','{$addbonekr}');"); }
				
				
							
				
				telepost_new($tonick,'<font color=red>'.iconv("CP1251","CP1251",'Внимание!</font> Пополнен баланс клановой казны на ').($add_klan_kazna_ekr).' екр. ');	
		   		}			
		   			else
		   			{
					addchp ('<font color=red>okpay, error 1 kazna, teloid:</font>'.$tonick[id],'{[]}Bred{[]}',-1,-1); 			       				 			   		
		   			}
				}
			else
				{
				addchp ('<font color=red>okpay, error 2 kazna, teloid:</font>'.$tonick[id],'{[]}Bred{[]}',-1,-1); 			       				 			   		
				}				

	  	
		  	    $resultCode=0;
			    
			  }			  
			  else  if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and ($qiwi[param]==0) )
			  {
					  //обработка ЕКР
						$tonick = check_users_city_data($qiwi['owner_id']);
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
																 if (put_to_kazna($klan_name['id'],2,$add_ekr_to_kazna,$klan_name['short'],false,"Начисленно по Акции «Клановая казна», благодаря ".$tonick['login']."!"))
																		      {
																			$fc_nom=100000000+$klan_name['id'];
																			$fc_name='Клан-Казна:«'.$klan_name[short].'»';
																			mysql_query("INSERT INTO oldbk.`dilerdelo` (dilerid,dilername,bank,owner,ekr,addition) values	('450','KO','{$fc_nom}','{$fc_name}','{$add_ekr_to_kazna}','0');");
																			telepost_new($tonick,'<font color=red>Внимание!</font> Благодаря '.$tonick['login'].' в казну поступило '.$add_ekr_to_kazna.' екр. по Акции <a href=http://oldbk.com/encicl/?/act_clantreasury.html target=_blank>«Клановая казна»</a>.');	
																			}
																		
															}
													}
											}
			  
								if ((time()>$KO_start_time) and (time()<$KO_fin_time)) 
								{
				
									$ko_bonus=round(($qiwi[sum_ekr]*($pbm+$KO_A_BONUS)) ,2); 
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
				  		    			$rec['owner']=$qiwi['owner_id']; 
									$rec['owner_login']=$qiwi['owner'];
									$rec['target']=8383;
									$rec['target_login']=iconv("CP1251","CP1251",'Банкир');
									$rec['type']=261;
									$rec['sum_ekr']=$qiwi[sum_ekr]+$ko_bonus;
									$rec['bank_id']=$qiwi['bank_id'];
									$rec['add_info']=iconv("CP1251","CP1251",'Пополнение Okpay');
									add_to_new_delo($rec); 
									mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('{$PaymentMethod}','8383','".iconv("CP1251","CP1251",'Банкир')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$qiwi[sum_ekr]}');");					
									mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','".iconv("CP1251","CP1251","Вы пополнили свой счет  на сумму <b>{$qiwi[sum_ekr]}</b> екр. <i>(Итого: {$rezbank['cr']} кр., {$rezbank['ekr']} екр.)</i>")."','{$qiwi[bank_id]}');");					
									telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведено '.($qiwi[sum_ekr]).' екр. Спасибо за покупку!'));	
									
									//запись для бонуса
									if ($ko_bonus > 0)					
									{
									mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,bank,owner,ekr) values ('','450','".iconv("CP1251","CP1251",'KO')."','{$qiwi[bank_id]}','".$qiwi['owner']."','{$ko_bonus}');");					
									mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','".iconv("CP1251","CP1251","Бонус за покупку еврокредитов <b>{$ko_bonus}</b> екр.")."','{$qiwi[bank_id]}');");		
									
									if ((time()>$KO_start_time) and (time()<$KO_fin_time)) 
											{
											telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведен бонус:'.($ko_bonus).' екр. в рамках <a href="http://oldbk.com/encicl/?/act_ekr.html" target="_blank">акции</a>. '));	
											}
											else
											{
											$yyy[1]='Silver';
											$yyy[2]='Gold';
											$yyy[3]='Platinum';
											telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> На ваш счет №'.$qiwi[bank_id].' переведен бонус:'.($ko_bonus).' екр. за наличие "'.$yyy[$tonick[prem]].' account" '));
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
									$dil['login']=iconv("CP1251","CP1251",'Банкир');
									$get_bankid=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}' and owner='{$tonick[id]}'; "));
									mysql_query("UPDATE oldbk.`dealers` set `sumekr` = sumekr-'{$qiwi[sum_ekr]}' WHERE `owner` = '8383' LIMIT 1;"); //  снимаем  из баланса  Банкира
									$resultCode=0;
					       }
					       else
					       {
					       	$resultCode=200;
					       }
					       	  	  
				}
				else if ( ($qiwi['owner_id']>0) and ($qiwi['param']==10000) and ($qiwi['sub_trx'] > 0) )
				{
				//обработка платежей субов
					$tonick = check_users_city_data($qiwi['owner_id']);
					$balamount = $qiwi['sum_ekr'];
					$bankid =$qiwi['bank_id'];
					$param=$qiwi['param'];
					
			  		
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
													mysql_query("INSERT INTO oldbk.`dilerdelo` (sub_trx,paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('{$get_sub_trx['id']}','{$PaymentMethod}','8383','".iconv("CP1251","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$balamount}','{$bankid}' );");
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
																			$rec['target_login']=iconv("CP1251","CP1251",'Банкир');
																			$rec['type']=5858;
																			$rec['sum_kr']=0;
																			$rec['sum_ekr']=$balamount;
																			$rec['sum_kom']=0;
																			$rec['item_id']='';
																			$rec['add_info'] = iconv("CP1251","CP1251",'Оплата счета №'.$get_sub_trx['id'].' - '.$ger_par_info['par_serv_desc'].',  через Okpay '.$balamount);
																			add_to_new_delo($rec);
													
													telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Удачно оплачен счет №'.$get_sub_trx['id'].' - '.$ger_par_info['par_serv_desc'].'. Удачной игры!')); 
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
										
							mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('{$PaymentMethod}','8383','".iconv("CP1251","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$balamount}','{$bankid}' );");
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
													$rec['target_login']=iconv("CP1251","CP1251",'Банкир');
													$rec['type']=578;
													$rec['sum_kr']=0;
													$rec['sum_ekr']=0;
													$rec['sum_kom']=0;
													$rec['item_id']='';
													$rec['add_info'] = iconv("CP1251","CP1251",'Оплата лечения травмы Okpay '.$balamount);
													add_to_new_delo($rec);
							
							telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Ваш персонаж излечен от травм. Удачной игры!')); 

							
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
			else if ( ($qiwi['owner_id']>0) and ($qiwi['param']==88000||$qiwi['param']==88100||$qiwi['param']==88500||$qiwi['param']==881000) ) 
				{
					$tonick = check_users_city_data($qiwi['owner_id']);
					$balamount = $qiwi['sum_ekr'];
					$bankid =$qiwi['bank_id'];
					$param=$qiwi['param'];
					
			  			if ($param==88000) 
				  			{ 
					  		$kol=round($balamount*20);
				  			$eprice=$balamount;
				  			}
							elseif ($param==88100) { $kol=100; $eprice=5; }
							elseif ($param==88500) { $kol=500; $eprice=22.5;  }
							elseif ($param==881000) { $kol=1000; $eprice=35;  }
			  		

			  		
					if (($tonick['id']>0) and  (round($balamount,2)>=round($eprice,2) ) and ($kol>0) )
					{
							$fid = 3003060; // покупка  золотых монеток ввиде баланса
							
										if ((time()>$KO_start_time38) and (time()<$KO_fin_time38)) 
										{
											$kb=act_x2bonus_limit($tonick,3,$kol);
											if ($kb>0)
												{
												$ko_bonus_gold=$kb;
												$kol+=$ko_bonus_gold;
												$ko_bonus_gold_str=iconv("CP1251","CP1251",'. Начислено '.$ko_bonus_gold.' монет по Акции «Двойная выгода».');	
												$ko_bonus_gold_chat=iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Вам начислено '.$ko_bonus_gold.' монет по Акции <a href="http://oldbk.com/encicl/?/act_x2bonus.html" target="_blank">«Двойная выгода»</a>. Бонус выдается единожды и не распространяется на последующие покупки этой валюты.');	
												}
										}
		
							mysql_query("UPDATE oldbk.`users` set `gold` = `gold`+'{$kol}' WHERE `id` = '{$tonick['id']}' LIMIT 1;");
							if (mysql_affected_rows()>0)	
							{
							mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('{$PaymentMethod}','8383','".iconv("CP1251","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$balamount}','{$bankid}' );");
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
													$rec['target_login']=iconv("CP1251","CP1251",'Банкир');
													$rec['type']=3001;
													$rec['sum_kr']=0;
													$rec['sum_ekr']=$balamount;
													$rec['sum_kom']=0;
													$rec['item_id']='';
													$rec['item_name']=iconv("CP1251","CP1251",'Монеты');
													$rec['item_count']=$kol;
													$rec['item_type']=50;
													$rec['add_info'] = $kol."/".($tonick['gold']+$kol).$ko_bonus_gold_str;
													add_to_new_delo($rec);
							
							telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Вам передано <b>'.$kol.'</b> монет. Удачной игры!')); 
							if ($ko_bonus_gold_chat!='')
									{
									telepost_new($tonick,$ko_bonus_gold_chat); 
									}
							
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
			else if ((($qiwi[id]>0) and ($qiwi[bank_id]>0) and ($qiwi[sum_ekr]>0)) and (in_array($qiwi['param'],$leto_bukets) ) ) // покупка букетов
			  {
				$tonick = check_users_city_data($qiwi[owner_id]);
				$balamount = number_format($qiwi[sum_ekr],2,'.',''); 
				$bankid=$qiwi[bank_id];	
				
				$fid = $qiwi['param'];
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$fid}' ;"));
				$dress['prototype'] = $fid;	
				$dress['unik'] = 2;
				
				$bank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$qiwi[bank_id]}'   LIMIT 1;"));
				$kol=1;
				$werrcount=0;
				$k=0;
				
					if (by_item_bank_dil($tonick,$dress,null,1))
					{
					$k++;
						mysql_query("INSERT INTO oldbk.`dilerdelo` (paysyst,dilerid,dilername,addition,owner,ekr,bank) values ('paypal','8383','".iconv("CP1251","CP1251",'Банкир')."','{$fid}','{$tonick[login]}','{$dress['ecost']}','{$qiwi[bank_id]}' );");
						
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
					       mysql_query("INSERT INTO oldbk.`partners_delo` (dealer_id,partner_id,owner_id,ekr,bank,transfer_time) values ('8383','{$partner['id']}','{$tonick['id']}','{$balamount}','0','".time()."');");
					       $id_ins_part_del=mysql_insert_id();
					       $bonus=round(($balamount/100*$partner['percent']),2);
					       mysql_query("UPDATE oldbk.`partners` set `all_ekr` = `all_ekr`+'{$balamount}', `money`=`money`+'{$bonus}' WHERE `id` = '{$partner['id']}' LIMIT 1;");
					      }
	
					
				if ($k>0)
				{
				telepost_new($tonick,iconv("CP1251","CP1251",'<font color=red>Внимание!</font> Вам передан предмет <b>').$dress[name].iconv("CP1251","CP1251",'</b> (x').$k.iconv("CP1251","CP1251",'). Удачной игры!!')); 
				}					
					
					if ($werrcount>0)
						{
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('14897','','<font color=red>Warning!</font> paypal: Pay:{$amount} Param {$param} , UserID {$tonick[id]}, werrcount:{$werrcount} . ');");							    										
						}
				 $resultCode=0;
							
			  
			}			
			 else
			 {
				$resultCode=999;
			 }
			  
			  /* http://tickets.oldbk.com/issue/oldbk-2069#tab=Comments
			  if (($resultCode==0) and ($tonick['id']>0) and ($qiwi[sum_ekr]>=500))
				{
				
				$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='636' ;"));
				$dress['present']='Мироздатель';
				if (by_item_bank_dil($tonick,$dress,null,1))
					{
					telepost_new($tonick,'<font color=red>Внимание!</font> Вы получили <b>'.$dress[name].'</b> за оптовую покупку игровой валюты.');	 
					}
				
				}
			  */
		 
		 if ($resultCode==0)
		 	{
			// print OK signature
			fwrite("OK$inv_id\n");
			echo "OK$inv_id\n";
			}
			else
			{
			fwrite("ERROR-CODE:$resultCode\n");
			echo "ERROR:$resultCode\n";
			}
		}
		else
			{
			fwrite("Error. ok_txn_gross< {$qiwi['sum_ekr']}. $inv_id\n");
			echo "Error. ok_txn_gross< {$qiwi['sum_ekr']}. $inv_id\n";
			}
	}
	else
	{
		fwrite("Error. already complete. $inv_id\n");
		echo "Error. already complete. $inv_id\n";
	}
	}
	else
		{
		echo "Server-ok";
		}
fclose($log);
?>