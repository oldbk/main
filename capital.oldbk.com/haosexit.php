<?php
	ob_start("ob_gzhandler");
	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";
	include "item_functions.php";
	include 'bank_functions.php';	

	$course_ekr_kr=EKR_TO_KR;
/*
	if ((isset($_POST[qiwimkbill])) AND ((float)($_POST[amount_ekr])>0) AND (isset($_POST[amount_rub])) AND ((int)($_POST[to])>0)) {
		$EKR_DOLG=(float)($_POST[amount_ekr]);
		$RUR=get_rur_curs();	
		$RUR_DOLG=((int)($RUR*$EKR_DOLG)+1); 
		$qiwi_to=(int)($_POST[to]);
	
		if (($_POST[amount_rub]==$RUR_DOLG) AND ($RUR_DOLG>0)) {
			mysql_query("INSERT INTO `oldbk`.`trader_balance_qiwi` SET `owner`='{$user[login]}',`owner_id`='{$user[id]}',`bank_id`='0',`sum_ekr`='{$EKR_DOLG}',`sum_rub`='{$RUR_DOLG}',`qiwi`='{$qiwi_to}', `param`='444' ;");
			if (mysql_affected_rows()> 0 ) {
				$txn_id=mysql_insert_id();
				$linkqiwi="http://w.qiwi.ru/setInetBill.do?frm=1&from=6920&lifetime=0.0&check_agt=false&com=".urlencode("Погашение долга хаоса на {$EKR_DOLG} екр. персонажа {$user['login']}")."&to={$qiwi_to}&txn_id={$txn_id}&amount_rub={$RUR_DOLG}";

				$f=@fopen($linkqiwi,"r");
				if($f) {
					fclose($f);
					echo "<div align=center>";
					err('Счет удачно выставлен!');
					echo "</div>";				
				} else {
					err('Счет не выставлен, повторите позже!');
				}
			} else {
				err('Счет не выставлен, повторите позже!');
			}
		} else {
			err('Счет не выставлен, повторите позже!');
		}
	} else if ((isset($_POST[mk_bill_cost])) and ( (float)($_POST[mk_bill_cost])>0)) {
	die("Ошибка способа оплаты");
		$EKR_DOLG=(float)($_POST[mk_bill_cost]);
		
		// проверим нету ли точно такого же не оплаченного  счета уже
		$get_bill_id=mysql_fetch_array(mysql_query("select * from oldbk.com_service where  owner='{$user[id]}' and type=444 and stat=0"));
		if ($get_bill_id[id] > 0) {
			err('У Вас уже есть не оплаченый счет №SC'.$get_bill_id[id]);			
		} else {
			mysql_query("INSERT INTO `oldbk`.`com_service` SET owner='{$user[id]}' ,  `type`='444',`cost`='{$EKR_DOLG}',`cost_type`='3',`stat`='0',`moder`=0;");
			$bill_id=mysql_insert_id();
			$str_bill='SC'.$bill_id;
			
			mysql_query("INSERT INTO oldbk.`inventory` (`owner`,`name`,`type`,`massa`,`cost`,`img`,`letter`,`maxdur`,`isrep`,`present`) VALUES('{$user[id]}','Счет на оплату услуг','50',0,0,'paper100.gif','Счет на оплату услуги:<b>Оплата досрочного выхода из хаоса</b>.<br>\n Сумма:{$EKR_DOLG} <br>\n Номер счета:<b>{$str_bill}</b>',1,0,'Коммерческий отдел') ;");

			echo "<div align=center>";
			err('Счет <b>№'.$str_bill.'</b> , на Сумму: <b>'.$EKR_DOLG.' екр.</b> - удачно выставлен!  ');
			echo "</div>";	
			
		}
	}
	*/
	
	$can_take=false;
	
	if(($user[align]>1&&$user[align]<2) || ADMIN) {
		$access=check_rights($user);
	}

	if (ADMIN) {
		$access[item_hist] = 1;
	}

	//временно палам
	if ($user['align'] != 4 && !$access[item_hist]) { header('Location: main.php'); die(); }

	if (isset($_GET['telo'])) $_POST['telo'] = $_GET['telo'];
	
	if($user['align']==4) {
		$prisoner=true;
		$telo=$user;
		$can_take=true;
	} elseif($access[item_hist]) {
		$pal=true;
		echo '<form method=post>Введите ник заключенного <input name=telo type=text value='.htmlspecialchars($_POST[telo],ENT_QUOTES).'> <input type=submit value="просмотреть"> <input type=button value="к списку" OnClick="location.href=\'haoslook.php\';"></form>';
		if(isset($_POST[telo]))	{
			$telo=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users WHERE login='".$_POST[telo]."' AND align = 4 AND block=0;"));
			$telo=check_users_city_data($telo[id]);
		}
	}
	
	$effects=mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_city[$telo[id_city]]."effects WHERE owner='".$telo[id]."' AND type=4 and lastup > 0"));
	$vikup=unserialize($effects['add_info']);

	if (!$effects['time']) {
		echo '<html><head><link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/main.css"></head><body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor=e2e0e0>';
		if ($pal == true && isset($_POST['telo']) && !empty($_POST['telo'])) {
			err('Персонаж "'.htmlspecialchars($_POST['telo'],ENT_QUOTES).'" не находится в хаосе');
		} else {
			if ($pal === false) {
				err('Вы не находитесь в хаосе');
			}
		}
		echo '</body></html>';
		die();
	}

	if ($telo['room'] == 197 || $telo['room'] == 198 || $telo['room'] == 199) {
		echo '<html><head><link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/main.css"></head><body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor=e2e0e0>';
		if ($user['id'] == $telo['id']) {
			err('Выйдите из оружейной комнаты');
		} else {
			err('Персонаж "'.$telo['login'].'" находится в оружейной комнате');
		}
		echo '</body></html>';
		die();
	}

	if (!$vikup[kr] && !$vikup[ekr]) {
		echo '<html><head><link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/main.css"></head><body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor=e2e0e0>';
		err('Обратитесь в <a target="_blank" href="http://oldbk.com/commerce/">коммерческий отдел</a>.');
		echo '</body></html>';
		die();
	}

	
	if($pal==true && $effects[pal]==1 && $vikup[kr]>0 && (!$vikup[ekr] || $vikup[ekr]==0)) {
		$can_take=true;
	}
	
	if($_POST[razdel] && $effects['time'] && ($vikup[kr]>0 || $vikup[ekr]>0) && $can_take==true) {
		$_POST['count']=(int)$_POST['count'];
		echo '<br>';
		echo '<br>';
		$data_schet=explode('_',$_POST[razdel]);

		$kr_dolg=$vikup[kr];
		$ekr_dolg=$vikup[ekr];
		
		if($data_schet[1]=='money') {
			if($_POST['count']>0) {
				if($data_schet[0]=='t') {
					if($data_schet[2]=='kr' && $vikup[kr]>0 && $vikup[kr] >= $_POST['count']) {
						if($telo[money]>=$_POST['count']) {
							if(mysql_query("update ".$db_city[$telo[id_city]]."users set money=money-".$_POST['count']." WHERE id= ".$telo[id]." limit 1")) {
								$vikup[kr]-=$_POST['count'];
								$kr_count=$_POST['count'];
								$ekr_count=0;
								$delo_type=5001;
								$bank_id=0;
								$add_inf = 'долг кр:'.$kr_dolg.'/'.$vikup[kr].','.' екр:'.$ekr_dolg.'/'.$vikup[ekr];
								$ok=1;
								$type=1;
							}
						} else {
							err('Недостаточно средств.');
						}
					} else {
						err('Не верная сумма');
					}
				} else if($data_schet[0]=='bank') {
					/// ПЕРЕПИСАТЬ ОПЛАТУ ЕКРАМИ С БАНКА!!!
					$bank=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.bank WHERE id='".(int)$data_schet[2]."' AND owner='".$telo[id]."' limit 1"));
					if($bank[id]) {
						if($data_schet[3]=='kr' && $bank[cr]>=$_POST['count'] && $vikup[kr]>=$_POST['count']) {
							if(mysql_query("UPDATE oldbk.bank SET cr=cr-'".(int)$_POST['count']."' WHERE id=".$bank[id]." AND owner=".$telo[id]." ")) {
								mysql_query("INSERT INTO oldbk.bankhistory SET date='".time()."', text='Списано <b>".$_POST['count']."кр.</b> В счет уплаты долга.', bankid=".$bank[id].";");
								$vikup[kr]-=(int)$_POST['count'];
								$kr_count=$_POST['count'];
								$ekr_count=0;
								$delo_type=5002;
								$bank_id=$bank[id];
								$add_inf='долг кр:'.$kr_dolg.'/'.$vikup[kr].','.' екр:'.$ekr_dolg.'/'.$vikup[ekr].'| банк_кр:'.$bank[cr].'/'.($bank[cr]-$_POST['count']);
								$ok=1;
								$type=2;
							}
						} elseif($data_schet[3]=='ekr' && $bank[ekr]>=$_POST['count'] && $vikup[kr]>=($_POST['count']*$course_ekr_kr)) {
							if(mysql_query("UPDATE oldbk.bank SET ekr=ekr-'".(int)$_POST['count']."' WHERE id=".$bank[id]." AND owner=".$telo[id]." ")) {
								mysql_query("INSERT INTO oldbk.bankhistory SET date='".time()."', text='Списано <b>".$_POST['count']."екр.	</b> В счет уплаты долга.', bankid=".$bank[id].";");
								$vikup[kr]-=(int)$_POST['count']*$course_ekr_kr;
								$kr_count=0;
								$ekr_count=(int)$_POST['count'];
								$delo_type=5003;
								$bank_id=$bank[id];
								$add_inf='долг кр:'.$kr_dolg.'/'.$vikup[kr].','.'| банк_екр:'.$bank[ekr].'/'.($bank[ekr]-$_POST['count']);
								$ok=1;
								$type=3;
							}
						} elseif($data_schet[3]=='ekr' && $bank[ekr]>=$_POST['count'] && $vikup[ekr]>=($_POST['count'])) {
						//оплата екрами 1 к1 если екровый выкуп
							if(mysql_query("UPDATE oldbk.bank SET ekr=ekr-'".(int)$_POST['count']."' WHERE id=".$bank[id]." AND owner=".$telo[id]." ")) {
								mysql_query("INSERT INTO oldbk.bankhistory SET date='".time()."', text='Списано <b>".$_POST['count']."екр.	</b> В счет уплаты долга.', bankid=".$bank[id].";");
								$vikup[ekr]-=(int)$_POST['count'];
								$kr_count=0;
								$ekr_count=(int)$_POST['count'];
								$delo_type=5003;
								$bank_id=$bank[id];
								$add_inf='долг екр:'.$ekr_dolg.'/'.$vikup[ekr].','.'| банк_екр:'.$bank[ekr].'/'.($bank[ekr]-$_POST['count']);
								$ok=1;
								$type=3;
							}
						}
						else {
							err('Не хватает средств на счету или указана не верная сумма');
						}
						
					}
				}
				
				if($ok==1) {
					$rec['owner']=$telo[id];
					$rec['owner_login']=$telo[login];
					$rec['owner_balans_do']=$telo['money'];
					$telo['money']-=$kr_count;
					$rec['owner_balans_posle']=$telo['money'];
					$rec['target']=0;
					$rec['target_login']='Хаос';
					$rec['type']=$delo_type;//передача кредитов в счет долга
					$rec['sum_kr']=$kr_count;
					$rec['sum_ekr']=$ekr_count;
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
					$rec['bank_id']=$bank_id;
					$rec['add_info']=$add_inf;
					add_to_new_delo($rec); //юзеру
					err('Средства успешно списаны.');
				}
			} else {
				err('Введите сумму.');
			}
		} elseif($data_schet[1]=='item') {
			if(is_numeric($data_schet[2])) {
				$item=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.inventory WHERE owner ='".$telo[id]."' AND id='".$data_schet[2]."' AND (type <12 or prototype in (".implode(',',$vaucher).")) AND bs_owner = 0 and labflag = 0 AND (prokat_do = 0 or ISNULL(prokat_do)) AND present!='Арендная лавка' AND labonly = 0 AND setsale = 0 AND dressed = 0"));
				if($vikup[kr]>0) {
					if($item[id]==$data_schet[2] && (($item[unik]==0 && $item[art_param]=='' && ($prisoner==true || $pal==true)) || (($item[unik]!=0 || $item[art_param]!='') && $pal==true))) {
						//разделяем суммы
						if($item[unik]==0 && $item[art_param]=='' && ($prisoner==true || $pal==true)) {
							$itt=curr_price($item,1,1);
							$realcost=$itt[summ];
						} elseif(($item[unik]!=0 || $item[art_param]!='')&&$pal==true && $_POST[count_art]) {
							$realcost=(int)$_POST[count_art];
						}
						
						if($item[add_pick]!='') {
							undress_img($item);
						}

						if(mysql_query("UPDATE oldbk.inventory set owner=8325 WHERE id = ".$item[id]." AND (type <12 or prototype in (".implode(',',$vaucher).")) AND bs_owner = 0 and labflag = 0 AND (prokat_do = 0 or ISNULL(prokat_do)) AND present!='Арендная лавка' AND labonly = 0 AND setsale = 0 AND dressed = 0")) {
							$rec['owner']=$telo[id];
							$rec['owner_login']=$telo[login];
							$rec['owner_balans_do']=$telo[money];
							$rec['owner_balans_posle']=$telo[money];
							$rec['target']=8325;
							$rec['target_login']='Повелитель багов';
							$rec['type']=301;
							$rec['sum_kr']=0;
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_id']=get_item_fid($item);
							$rec['item_name']=$item['name'];
							$rec['item_count']=1;
							$rec['item_type']=$item['type'];
							$rec['item_cost']=$realcost;
							$rec['item_dur']=$item['duration'];
							$rec['item_maxdur']=$item['maxdur'];
							$rec['item_ups']=$item['ups'];
							$rec['item_unic']=$item['unik'];
							$rec['item_incmagic']=$item['includemagicname'];
							$rec['item_incmagic_count']=$item['includemagicuses'];
							$rec['item_arsenal']=$item['arsenal_klan'];
							$type=4;
							
							if($vikup[kr]>=$realcost) {
								$vikup[kr]-=$realcost;
								$rec['add_info']='долг кр:'.$kr_dolg.'/'.$vikup[kr].' В счет долга';
								if ($user['id'] != $telo['id']) $rec['add_info'] .= '. Паладином '.$user['login'];
								add_to_new_delo($rec);
								
								$ok=1;
							} elseif($realcost>$vikup[kr]) {
								$back_money=$realcost-$vikup[kr];
								$vikup[kr]=0;
								$rec['add_info']='долг кр:'.$kr_dolg.'/'.$vikup[kr].' В счет долга';
								if ($user['id'] != $telo['id']) $rec['add_info'] .= '. Паладином '.$user['login'];
								add_to_new_delo($rec);
								
								$ok=1;
								
								mysql_query("UPDATE ".$db_city[$telo[id_city]]."users set money=money+'".$back_money."' WHERE id='".$telo[id]."' limit 1");
								
								$rec['owner']=$telo[id];
								$rec['owner_login']=$telo[login];
								$rec['owner_balans_do']=$telo['money'];
								$telo['money']+=$back_money;
								$rec['owner_balans_posle']=$telo['money'];
								$rec['target']=0;
								$rec['target_login']='Хаос';
								$rec['type']=5004;//Сдача за погашение долга
								$rec['sum_kr']=$back_money;
								$rec['sum_ekr']=0;
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
								$rec['add_info']='';
								add_to_new_delo($rec); //юзеру
							}
							
						}
					} else {
						err('Вешь не найдена.');
					}
				}
				
			} else {
				err('Не надо так делать.');
			}
		} elseif($data_schet[1]=='sertk' || $data_schet[1]=='serte') {
			//Сертификаты
			if(is_numeric($data_schet[2])) {
				$item=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.inventory WHERE owner ='".$telo[id]."' AND id='".$data_schet[2]."' AND prototype in (".implode(',',$vaucher).") "));
				//гасим кредитный долг	
				$bank_rez=(int)$_POST[bank_rez];
				if($vikup[kr]>0 && $data_schet[1]=='sertk') {
					$realcost=$item['ecost']*$course_ekr_kr;
					
					if($item[id]==$data_schet[2]) {				
						if(mysql_query("UPDATE oldbk.inventory set owner=8325 WHERE id=".$item[id]." AND owner='".$telo[id]."' AND prototype in (".implode(',',$vaucher).")")) {
							$rec['owner']=$telo[id];
							$rec['owner_login']=$telo[login];
							$rec['owner_balans_do']=$telo[money];
							$rec['owner_balans_posle']=$telo[money];
							$rec['target']=8325;
							$rec['target_login']='Повелитель багов';
							$rec['type']=301;
							$rec['sum_kr']=0;
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_id']=get_item_fid($item);
							$rec['item_name']=$item['name'];
							$rec['item_count']=1;
							$rec['item_type']=$item['type'];
							$rec['item_cost']=$realcost;
							$rec['item_dur']=$item['duration'];
							$rec['item_maxdur']=$item['maxdur'];
							$rec['item_ups']=$item['ups'];
							$rec['item_unic']=$item['unik'];
							$rec['item_incmagic']=$item['includemagicname'];
							$rec['item_incmagic_count']=$item['includemagicuses'];
							$rec['item_arsenal']=$item['arsenal_klan'];
							
							$type=5;
							
							if($vikup[kr]>=$realcost) {
								$vikup[kr]-=$realcost;
								$rec['add_info']='долг кр:'.$kr_dolg.'/'.$vikup[kr].' В счет долга';
								add_to_new_delo($rec);
								
								$ok=1;
							} elseif($realcost>$vikup[kr]) {
								$back_money=$realcost-$vikup[kr];
								$back_money=$back_money/$course_ekr_kr;
								$vikup[kr]=0;
								
								$back_vaucers=array();
								$vv=array();
								
								$data=mysql_query("SELECT * FROM oldbk.eshop WHERE id in (".implode(',',$vaucher).") order by ecost desc;");

								while($row=mysql_fetch_assoc($data)) {
									$vv[$row[id]]=$row;	
								}
								
								$i=0;
								$to_bank=0;
								while($back_money>=0) {
									foreach($vv as $id=>$val) {
										if($val[ecost]<=$back_money) {
											$back_money-=$val[ecost];
											$back_vaucers[$i]=$val[id];
											$i++;
											break;
										}
									}
									
									if($back_money<5) {
										if($back_money>0) {
											$to_bank = 1;					
										}
										break;
									}
								}

								// тут свуем серты в инвентарь
								if(count($back_vaucers)>0) {
									foreach($back_vaucers as $k=>$v) {
										$dress=$vv[$v];
										if(mysql_query("INSERT INTO oldbk.`inventory`
										(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`maxdur`,`isrep`,
											`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
											`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
											`otdel`,`gmp`,`gmeshok`, `group`,`letter` , `ab_mf`,`ab_bron`,`ab_uron`,`sowner`
										)
										VALUES
										('{$dress['id']}','{$telo['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},{$dress['ecost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
										'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
										'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'
										,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$telo['id']}'
										) ;"))
										{
											$good = 1;
											$new_vid=mysql_insert_id();
											$dress[id]=$new_vid;
										} else {
											$good = 0;
										}

										if ($good==1) {
										 	$rec['owner']=$telo[id];
											$rec['owner_login']=$telo[login];
											$rec['owner_balans_do']=$telo['money'];
											$rec['owner_balans_posle']=$telo['money'];
											$rec['target']=0;
											$rec['target_login']='Хаос';
											$rec['type']=5008;//получил ваучер от диллера
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
											add_to_new_delo($rec); //юзеру
										 
										 }
									
									}
								}

								if($to_bank==1) {
									$b_id=(int)$_POST[bank_rez];
									$data=mysql_query("SELECT * FROM oldbk.bank WHERE owner='".$user[id]."' AND id='".$b_id."' ");
									if(mysql_num_rows($data)>0) {
										//екр в банк
										if(mysql_query("UPDATE oldbk.bank set ekr=ekr+".$back_money." WHERE owner='".$user[id]."' AND id='".$b_id."' "))
										{
											echo "UPDATE oldbk.bank set ekr=ekr+".$back_money." WHERE owner='".$user[id]."' AND id='".$b_id."' ";
											mysql_query("INSERT INTO oldbk.bankhistory 
											SET date='".time()."', text='Зачислено <b>".$back_money."екр.
											</b>Сдача с уплаты долга.', bankid=".$b_id.";");
										
											$rec['owner']=$telo[id];
											$rec['owner_login']=$telo[login];
											$rec['owner_balans_do']=$telo['money'];
											$rec['owner_balans_posle']=$telo['money'];
											$rec['target']=0;
											$rec['target_login']='Хаос';
											$rec['type']=5006;//передача кредитов в счет долга
											$rec['sum_kr']=0;
											$rec['sum_ekr']=$back_money;
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
											$rec['bank_id']=$b_id;
											$rec['add_info']='';
											add_to_new_delo($rec); //юзеру
										}
										
									} else {
										//екр на счет по курсу $course_ekr_kr
										$kr=$back_money*$course_ekr_kr;
										if(mysql_query("update ".$db_city[$telo[id_city]]."users set money=money+".$kr." WHERE id= ".$telo[id]." limit 1")) {
											$rec['owner']=$telo[id];
											$rec['owner_login']=$telo[login];
											$rec['owner_balans_do']=$telo['money'];
											$rec['owner_balans_posle']=$telo['money'];
											$rec['target']=0;
											$rec['target_login']='Хаос';
											$rec['type']=5007;//Возврат сдачи в кр.
											$rec['sum_kr']=$kr;
											$rec['sum_ekr']=0;
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
											$rec['bank_id']='';
											$rec['add_info']='';
											add_to_new_delo($rec); //юзеру
										}
									}
								}
							}
							
						}
					
					}
					
				} elseif($vikup[ekr]>0 && $data_schet[1]=='serte' && is_numeric($bank_rez)) {
					//гасим екр долг
					$realcost=$item['ecost'];

					if($realcost >= $vikup[ekr]) {
						if($item[id]==$data_schet[2]) {
							if(mysql_query("UPDATE oldbk.inventory set owner=8325 WHERE id=".$item[id]." AND owner='".$telo[id]."' AND prototype in (".implode(',',$vaucher).")")) {
								$rec['owner']=$telo[id];
								$rec['owner_login']=$telo[login];
								$rec['owner_balans_do']=$telo[money];
								$rec['owner_balans_posle']=$telo[money];
								$rec['target']=8325;
								$rec['target_login']='Повелитель багов';
								$rec['type']=301;
								$rec['sum_kr']=0;
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								$rec['item_id']=get_item_fid($item);
								$rec['item_name']=$item['name'];
								$rec['item_count']=1;
								$rec['item_type']=$item['type'];
								$rec['item_cost']=$realcost;
								$rec['item_dur']=$item['duration'];
								$rec['item_maxdur']=$item['maxdur'];
								$rec['item_ups']=$item['ups'];
								$rec['item_unic']=$item['unik'];
								$rec['item_incmagic']=$item['includemagicname'];
								$rec['item_incmagic_count']=$item['includemagicuses'];
								$rec['item_arsenal']=$item['arsenal_klan'];
								add_to_new_delo($rec);
								$type=5;
								
								if($vikup[ekr]>=$realcost) {
									$vikup[ekr]-=$realcost;
									//$rec['add_info']='долг eкр:'.$ekr_dolg.'/'.$vikup[ekr].' В счет долга';
									//add_to_new_delo($rec);
									$ok=1;
								} elseif($realcost>$vikup[ekr]) {
									//////////////////
									$back_money=$realcost-$vikup[ekr];
									$vikup[ekr]=0;
									
									//$rec['add_info']='долг eкр:'.$ekr_dolg.'/'.$vikup[ekr].' В счет долга';
									//add_to_new_delo($rec);
									$ok=1;
	
									/// вот тут все дальше меняем на возврат ЕКР сертификата
									
									$back_vaucers=array();
									$vv=array();
									
									$data=mysql_query("SELECT * FROM oldbk.eshop WHERE id in (".implode(',',$vaucher).") order by ecost desc;");
	
									while($row=mysql_fetch_assoc($data)) {
										$vv[$row[id]]=$row;	
									}
									
									$i=0;
									$to_bank=0;
									while($back_money>=0) {
										foreach($vv as $id=>$val) {
											if($val[ecost]<=$back_money) {
												$back_money-=$val[ecost];
												
												$back_vaucers[$i]=$val[id];
											
												//echo '<br>=====<br>'.$back_money . ' | '. $val[ecost]. ' | '.$i . '<br>';
												$i++;
												break;
											}
										}
										
										if($back_money<5) {
											if($back_money>0) {
												$to_bank=1;					
											}
											break;
										}
									}
	
									// тут свуем серты в инвентарь
									if(count($back_vaucers)>0) {
										foreach($back_vaucers as $k=>$v) {
										$dress=$vv[$v];
											if(mysql_query("INSERT INTO oldbk.`inventory`
											(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`maxdur`,`isrep`,
												`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
												`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
												`otdel`,`gmp`,`gmeshok`, `group`,`letter` , `ab_mf`,`ab_bron`,`ab_uron`,`sowner`
											)
											VALUES
											('{$dress['id']}','{$telo['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},{$dress['ecost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
											'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
											'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'
											,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$telo['id']}'
											) ;"))
											{
												$good = 1;
												$new_vid=mysql_insert_id();
												$dress[id]=$new_vid;
											} else {
												$good = 0;
												//echo mysql_error();
											}
	
											if ($good==1) {
											 	$rec['owner']=$telo[id];
												$rec['owner_login']=$telo[login];
												$rec['owner_balans_do']=$telo['money'];
												$rec['owner_balans_posle']=$telo['money'];
												$rec['target']=0;
												$rec['target_login']='Хаос';
												$rec['type']=5008;//получил ваучер от диллера
												$rec['sum_kr']=0;
												$rec['sum_ekr']=$dress['ecost'];
												$rec['sum_kom']=0;
												$rec['item_id']=get_item_fid($dress);
												$rec['item_name']=$dress['name'];
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
												add_to_new_delo($rec); //юзеру
											 }
										
										}
									}
	
									if($to_bank==1) {
										$b_id=(int)$_POST[bank_rez];
										$data=mysql_query("SELECT * FROM oldbk.bank WHERE owner='".$user[id]."' AND id='".$b_id."' ");
										if(mysql_num_rows($data)>0) {
											//екр в банк
											if(mysql_query("UPDATE oldbk.bank set ekr=ekr+".$back_money." WHERE owner='".$user[id]."' AND id='".$b_id."' ")) {
												mysql_query("INSERT INTO oldbk.bankhistory  SET date='".time()."', text='Зачислено <b>".$back_money."екр.</b> Сдача с уплаты долга.', bankid=".$b_id.";");
												$rec['owner']=$telo[id];
												$rec['owner_login']=$telo[login];
												$rec['owner_balans_do']=$telo['money'];
												$rec['owner_balans_posle']=$telo['money'];
												$rec['target']=0;
												$rec['target_login']='Хаос';
												$rec['type']=5006;//передача кредитов в счет долга
												$rec['sum_kr']=0;
												$rec['sum_ekr']=$back_money;
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
												$rec['bank_id']=$b_id;
												$rec['add_info']='';
												add_to_new_delo($rec); //юзеру
											}
											
										} else {
											//екр на счет по курсу $course_ekr_kr
											$kr=$back_money*$course_ekr_kr;
											if(mysql_query("update ".$db_city[$telo[id_city]]."users set money=money+".$kr." WHERE id= ".$telo[id]." limit 1")) {
												$rec['owner']=$telo[id];
												$rec['owner_login']=$telo[login];
												$rec['owner_balans_do']=$telo['money'];
												$rec['owner_balans_posle']=$telo['money'];
												$rec['target']=0;
												$rec['target_login']='Хаос';
												$rec['type']=5007;//Возврат сдачи в кр.
												$rec['sum_kr']=$kr;
												$rec['sum_ekr']=0;
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
												$rec['bank_id']='';
												$rec['add_info']='';
												add_to_new_delo($rec); //юзеру
											}
										}
									}
								}
							}
						}
					} else {
						err('Этого ваучера не достаточно, чтобы покрыть всю сумму выкупа');
					}
				} else {
					err('Не надо так делать. 1');
				}
			} else {
				err('Не надо так делать. 2');
			}
		}

		if($ok==1) {
			if($vikup[kr]<=0 && $vikup[ekr]<=0) {
				// снимаем хаос
				mysql_query("update ".$db_city[$telo[id_city]]."users set `palcom` = '', `align` = 0 where id='".$telo[id]."'");
				
				$mess = "Досрочно выпущен из хаоса, долг погашен.";
				mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$telo['id']."','".$mess."','".time()."');");
				
				$effects['add_info']=serialize($vikup);
				mysql_query("DELETE from ".$db_city[$telo[id_city]]."effects where type=4 AND owner='".$telo[id]."' and lastup > 0");
				if ($user['id'] != $telo['id'])	 header('Location: haosexit.php?telo='.htmlspecialchars($_POST['telo'],ENT_QUOTES)); die();
				header('Location: main.php'); die();				
			} else {
				//обновляем стоимость выкупа
				$effects['add_info'] = serialize($vikup);
				mysql_query("UPDATE ".$db_city[$telo[id_city]]."effects set add_info='".($effects['add_info'])."' WHERE owner=".$telo[id]." AND type=4 and lastup > 0");
			}
		}
	
		if($pal==false && $telo[id]!=$user[id]) {
			err('Не надо так делать.');
			die();
		}
	}

?>
<HTML><HEAD>

<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/main.css">
<link rel="stylesheet" type="text/css" href="i/jail/jail_style.css">
<script type="text/javascript" src="/i/globaljs.js"></script>
<script src="i/jquery.drag.js" type="text/javascript"></script>	

<?
if($prisoner==true) {
?>
<script>
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}

</script>
<?
}
?>
<script>	

			function getformdata(id,param,event)
			{
				if (window.event) 
				{
					event = window.event;
				}
				if (event ) 
				{

				       $.get('haospayform.php?id='+id+'&param='+param+'', function(data) {
					  $('#pl').html(data);
					  $('#pl').show(200, function() {
						});
					});
				
				 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '120px'  });	


				}
				
			}
			
			function closeinfo()
			{
			  	$('#pl').hide(200);
			}
			
	$(window).resize(function() {
	 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '120px'  });
});			
	

function spisanie(id,money,id_txt)
{
	var exist_money=$('#'+id+'_exist_'+money).text();
	
	
	if(money=='kr'){money_txt=' кр.';}
	if(money=='ekr'){money_txt=' екр.';}
	
	if(money=='it' || money=='it1' || money=='serte' || money=='sertk')
	{
		if(money=='it')
		{
			money_txt=' кр.'; 
			var input='К погашению: <b>'+exist_money+'</b>'+money_txt;
		}
		<?
		if($pal==true)
			{
			?>
			if(money=='it1')
			{
				money_txt=' кр.'; 
				var input='Введите сумму: '+money_txt+'<input type=text name=count_art>';
			}
			<?
			}
		?>
		
		if(money=='serte') {
			money_txt=' екр.'; 
			<?
			$data=mysql_query("SELECT * FROM oldbk.bank WHERE owner='".$user[id]."'");
			if(mysql_num_rows($data)>0)
			{
				?>
				var input='Выберите банковский счет для сдачи <small>(в случае если не возмножно отдать ваучерами)</small>:<select name=bank_rez>';
				<?
				
				while($row=mysql_fetch_assoc($data))
				{
					?>
					 input=input+'<option value=<?=$row[id]?>><?=$row[id]?></option>';
					 <?
				}
				?>
				input=input+'</select><Br> К погашению: <b>'+exist_money+'</b>'+money_txt;
				
				<?
			}
			else
			{
				?>
				var input=' К погашению: <b>'+exist_money+'</b>'+money_txt;
				<?
			}
			?>
		}
		if(money=='sertk')
		{
			money_txt=' кр.'; 
			var input='К погашению: <b>'+exist_money+'</b>'+money_txt;
		}
	}
	else
	{
		var input='Введите сумму: '+money_txt+'<input type=text name=count>';
	}
	data='<form action=? method=post><table border=0 width=100%><tr bgcolor="#CCC3AA" ><td align=left><b>'+id_txt+'<b> ( '+exist_money+money_txt+')</td><td width=10 valign=top style="cursor: pointer;" align=right onclick=closehint(3);><b>X</b></td></tr><tr><td>'+input+'<input type="hidden" name="razdel" value="'+id+'_'+money+'"><input type="hidden" name="telo" value="<?=$telo[login]?>"></td><td>' +'<input type=submit value=">>"></td></tr></table></form>';
	

	 $('#hint3').html(data);
	 $("#hint3").css("visibility","visible");
}
function closehint(id)
{
	 $("#hint"+id).css("visibility","hidden");
}
</script>
</HEAD>

<body <? if($prisoner==true){?>onload="JavaScript:timedRefresh(20000);"<?}?> leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor=e2e0e0>
<div id="hint3" style="dislay: none; z-index:100; left: 100px; top: 100px;"> qwe </div>
<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 120px;
				width: 750px; height:365px; background-color: #eeeeee; 
				border: 1px solid black; display: none;">
	
</div>

<?

	 //сразу раделяем, чтоб палы могли пользоваться тем же интерфейсом
	
$effects = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_city[$telo[id_city]]."effects WHERE owner='".$telo[id]."' AND type=4 and lastup > 0"));
	
	
function prisoner_inf($telo,$effects) {
	global $prisoner;
	$vikup=unserialize($effects['add_info']);
	?>
	<input type="button" value="Вернуться" OnClick="location.href='myabil.php';">
	<center>
	<table width=100%><tr><td align=left valigh=top><?nick($telo);?></td><td align=right valign=top>
<?
	if($effects['time']) {
	 	echo 'Окончание срока: <b>'.($effects['pal']==1?' Бессрочно':(date("j.m.Y H:i",$effects['time']))).'</b> &nbsp;&nbsp;&nbsp;&nbsp;';
	}
	echo (($vikup[kr]>0) || ($vikup[ekr]>0)?'Досрочный выход за: <b>':'<br>Досрочный выход:</b> обратитесь в коммерческий отдел').($vikup[kr]>0?'<span id="vik_kr">'.$vikup[kr].'</span>кр.':'').($vikup[ekr]>0?'<span id="vik_ekr">'.$vikup[ekr].'</span>$. ':'').'</b>';

	$_SESSION[jail_kr]=0;
	$_SESSION[jail_ekr]=0;

	if ($vikup[kr]>0) {
		$_SESSION[jail_kr]=$vikup[kr];
	} elseif ($vikup[ekr]>0) {
		$_SESSION[jail_ekr]=$vikup[ekr];
	}

	echo '</td></tr></table>'; 
	echo '<br>'; 

?>	</center>
	</TABLE>
	<?
}

function bank($telo) {
	global $prisoner, $vaucher, $vikup;
	
	if($vikup[kr]>0) {
		$data=mysql_query("SELECT * FROM oldbk.bank WHERE owner='".$telo[id]."';");
		if(mysql_num_rows($data)>0) {
			echo "<table><tr><td>";
			
			?>

			<table border=0>
			<tr><td>В наличии : </td><td>&nbsp;</td><td><b><span id="t_money_exist_kr"><?=$telo[money]?></span> кр.</b></td><td>&nbsp;&nbsp;&nbsp;</td><td><a href=# onclick="spisanie('t_money','kr',' Списать с наличных<br>на персонаже<br>')">Списать в счет долга</a></td></tr></table><br>
			<table border=0>

			<?
			while($bank=mysql_fetch_assoc($data)) {
				echo "<tr><td>Счет № ". $bank[id] . " : </td><td>&nbsp;</td><td><b><span id='bank_money_".$bank[id]."_exist_kr'>" .$bank[cr]. "</span> кр.</b></td><td>&nbsp;&nbsp;&nbsp;</td><td><a href=# onclick=\"spisanie('bank_money_".$bank[id]."','kr',' Списать кредиты<br>Счет № ".$bank[id]."<br>')\">Списать со счета.</a></td></tr> <tr valign=top><td>Счет № ". $bank[id] . " : </td><td>&nbsp;</td><td><b><span id='bank_money_".$bank[id]."_exist_ekr'>".$bank[ekr]."</span> екр.</b></td><td>&nbsp;&nbsp;&nbsp;</td><td><a href=# onclick=\"spisanie('bank_money_".$bank[id]."','ekr',' Списать еврокредиты<br>Счет № ".$bank[id]."<br>')\">Списать со счета.</a><br><br></td></tr>";
			}
			echo "</table></td></tr></table>";
		}
	}
}

function inv($telo) {
	global $prisoner, $vaucher, $vikup, $pal, $can_take;
	$serts=array();
	$items=array();
		
	$data=mysql_query("SELECT * FROM oldbk.inventory WHERE owner = '".$telo[id]."' AND (type <12 or prototype in (".implode(',',$vaucher).")) AND bs_owner = 0 and labflag = 0 AND (prokat_do = 0 or ISNULL(prokat_do)) AND present!='Арендная лавка' AND labonly = 0 AND setsale = 0 AND dressed = 0");
	while($row=mysql_fetch_assoc($data)) {
		if(in_array($row[prototype], $vaucher)) {
			$serts[$row[id]]=$row;
		} elseif($vikup[kr]>0) {
			$items[$row[id]]=$row;
		}
	}

	?>
	
	<TR>
	<? if($vikup[kr]>0) {
		?><td width="487px" valign=top>
        	<fieldset>
            	<legend><b>Инвентарь</b></legend>
		<?
		foreach($items as $k=>$v) {
			$v['count'] = 1;
			$v['avacount'] = 1;
			$itt=curr_price($v,1,1);

			?>
			<table>
				<tr>
					<td width=80 align=center>
						<img src=http://i.oldbk.com/i/sh/<?=$v[img]?>>
						
						(<?=get_item_fid($v)?>)
						
						
						<?
						if(($v[unik]==1 || $v[art_param]!='') && $prisoner==true) {
							echo '<font color=red><b>Для изъятия этой вещи обратитесь к паладину.</b></font>';
						}
						else
						if($pal==true && ($v[unik]==1 || $v[art_param]!='')) {
							?>
							<a href=# onclick="spisanie('t_item_<?=$v[id]?>','it1',' Отдать <b><?=$v[name]?></b> в счет долга')">Отдать в счет долга</a>
							<br><span id="t_item_<?=$v[id]?>_exist_it1"><?=$itt[summ]?></span>кр.
							<?
						} else {						
							?>
							<a href=# onclick="spisanie('t_item_<?=$v[id]?>','it',' Отдать <b><?=$v[name]?></b> в счет долга')">Отдать в счет долга</a>
							<br><span id="t_item_<?=$v[id]?>_exist_it"><?=$itt[summ]?></span>кр.
							<?
						}
						?>
						
					</td>
					<td align=left>
					 <? showitem($v);?>
					</td>
				</tr>
			</table>
			<hr>
			<?
		}
		?>
		 	</fieldset>
		</td>
		 <td width="15px">
                	<br>
                </td>
		<?
	}
	
/*	
	if($vikup[ekr]>0 || $vikup[kr]>0) {
	?>
	 <td width="487px" valign=top>
        	<fieldset>
            	<legend><b>Ваучеры</b></legend>
	
		<?
		if(count($serts)==0) {
			echo '<br><b>У Вас нет ваучеров</b>';
		}
		foreach($serts as $k=>$v) {
			$v['count']=1;$v['avacount']=1;
		?>
		<table>
			<td width=80 align=center>
				<img src=http://i.oldbk.com/i/sh/<?=$v[img]?>>
				<?if($vikup[kr]>0) {
					?>
					<a href=# onclick="spisanie('t_sertk_<?=$v[id]?>','sertk',' Отдать <b><?=$v[name]?></b> в счет долга')">Отдать в счет КР долга</a>
					<br><span id="t_sertk_<?=$v[id]?>_exist_sertk"><?=($v[ecost]*10)?></span>кр. <br><br>
					<?
				}
				
				if($vikup[ekr]>0) {	
					?>
					<a href=# onclick="spisanie('t_serte_<?=$v[id]?>','serte',' Отдать <b><?=$v[name]?></b> в счет долга')">Отдать в счет EКР долга</a>
					<br><span id="t_serte_<?=$v[id]?>_exist_serte"><?=$v[ecost]?></span>екр. <br><br>
				<?
				}
				?>	
					
				</td>
				<td align=left>
				 <? showitem($v);?>
				</td>
		</table>
		<hr>
		<?
		}
		?>
		</fieldset>
		</td>
	<?
	}
	*/
	if($vikup[ekr]>0 || $vikup[kr]>0) {
	
	echo ' <td width="487px" valign=top>';

		if ( ($_SESSION['bankid']) and ($_GET['exitbank']) )
		{
			unset($_SESSION['bankid']);
		}
		else
		if (  (!$_SESSION['bankid'])  AND ($_POST['enter'] && $_POST['pass']) )  
		{

					$data = mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = '".$telo['id']."' AND `id`= '".$_POST['id']."' AND `pass` = '".md5($_POST['pass'])."';");
					$bank = mysql_fetch_array($data);
					if($bank) {
						$_SESSION['bankid'] = $bank['id'];
						err('Удачный вход.');
					}
					else
					{
						err('Ошибка входа.');
					}


		}

		if(!$_SESSION['bankid']) 
		{
		//логин в банк
						?>
							<form method=post action="haosexit.php">
							<fieldset style="width:200px; height:130px;">
								<legend><b>Войти в счет</b></legend><BR> &nbsp; №
						<?
						
						
							$banks = mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = ".$telo['id'].";");
							echo "<select style='width:150px' name=id>";
							while ($rah = mysql_fetch_array($banks)) {
									echo "<option>",$rah['id'],"</option>";
							}
							echo "</select>";
						?>
							<BR> &nbsp; Пароль <input type=password name=pass size=21>
							<BR><BR>
							<center><input type=submit name='enter' value='Войти'>
							</form>
							</fieldset>
						<?
	     }
	     else
	     		{
			echo '<fieldset style="height:130px;">
			<legend><b>Банк</b></legend><BR>';
     			$bank = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = ".$_SESSION['bankid'].";"));
	     		// вывод денег и кнопка оплаты
	     		echo "<table>";
			echo "<tr valign=top><td>Счет № ". $bank[id] . " : </td><td>&nbsp;</td><td><b><span id='bank_money_".$bank[id]."_exist_ekr'>".$bank[ekr]."</span> екр.</b></td><td>&nbsp;&nbsp;&nbsp;</td><td><a href=# onclick=\"spisanie('bank_money_".$bank[id]."','ekr',' Списать еврокредиты<br>Счет № ".$bank[id]."<br>')\">Списать со счета.</a><br><br></td></tr>";
			echo "<tr valign=top><td><a href=?exitbank=1>Сменить банковский счет</a></td><td>&nbsp;</td><td><b>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;</td></tr>";			
	     		echo "</table>
	     		</fieldset>";
	     		}
	echo '</td>';

	}	
	
}


if($telo[id] && $can_take==true) {
	//prisoner_inf($telo,$effects);
	//bank_inv($telo);
} elseif($pal==true && $can_take==false && $_POST) {
	err('Вы не можете просмотреть этого персонажа.');
}

?>	

<div class="main">
	<div class="cols">
		<div class="mid_col_l">
			<div class="mid_col_r">
				<div class="top_col_l">
					<div class="top_col_r">
						<div class="bot_col_l">
							<div class="bot_col_r">
							    <div class="content">
							    	<?
							    	if($telo[id] && $can_take==true) {
									prisoner_inf($telo,$effects);
								}
								if($telo[id] && $can_take==true) {
							    	?>
							        <table cellpadding="2" cellspacing="0" border="0" width="100%">
							        	<tr>
								            	<?if($prisoner==true && $vikup[kr]>0) {
								                ?>
								            	<td <?=($prisoner==true?'':'colspan="3"')?> >
								                	<fieldset>
								                    	<legend><b>Наличные</b></legend>
								             			<?
								             			if($telo[id] && $can_take==true) {
													//prisoner_inf($telo,$effects);
													bank($telo);
												}
								             			?>
								                	</fieldset>
								                </td>
								                
								                
								                <?
										}
								                 
								                if($prisoner==true && $vikup[kr]>0) {
								                ?>
								                 <td>
								                
								                </td>
								                <?
								                }
								                ?>
								                <?if($prisoner==true) {
								                ?>
								                <td valign=top>
								                       <fieldset>
								                    	<legend><b>Другие способы оплаты</b></legend>
								                    	<?
												echo "<div align=center>";
												//echo "<a href=\"#\" onClick=\"getformdata(11,0,event);\"><img src=http://i.oldbk.com/i/bank/knopka_qiwi.gif title='Оплатить с помощью QiWi' ></a>&nbsp;";
												echo "<a href=\"#\" onClick=\"getformdata(12,0,event);\"><img src=http://i.oldbk.com/i/bank/knopka_wmz.gif title='Оплатить с помощью WMZ' ></a><br>";
												//echo "<a href=\"#\" onClick=\"getformdata(16,0,event);\"><img src=http://i.oldbk.com/i/bank/priz_5.png title='Оплатить с помощью счета от Диллера' ></a>&nbsp;";	
												echo "<a href=\"#\" onClick=\"getformdata(13,0,event);\"><img src=http://i.oldbk.com/i/bank/knopka_wmr.gif title='Оплатить с помощью WMR' ></a>";
												//echo "<br><a href=\"#\" onClick=\"getformdata(14,0,event);\"><img src=http://i.oldbk.com/i/bank/knopka_aclick.gif title='Оплатить с помощью \"Альфа-Клик\"' ></a>&nbsp;";
												//echo "<a href=\"#\" onClick=\"getformdata(15,0,event);\"><img src=http://i.oldbk.com/i/bank/knopka_brs2.gif title='Оплатить с  помощью \"Русский стандарт\"' ></a>";
												echo "</div>";		
											?>	
											</fieldset>
								                </td>
								                <?
								                }
								                ?>
									</tr>
								        <tr>
								                        	<?
								                        	if($telo[id] && $can_take==true) {
													//prisoner_inf($telo,$effects);
													inv($telo);
												}
								                        	?>
								               
								                        	
								                
								        </tr>
							  	</table>
							  	<?
							  	}
							  	?>
							    </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</BODY>
</HTML>