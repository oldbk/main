<?php
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
	$miniBB_gzipper_encoding = 'gzip';
}

if (isset($miniBB_gzipper_encoding)) {
	ob_start();
}

function percent($a, $b) {
	$c = $b/$a*100;
	return $c;
}

session_start();

if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
include "bank_functions.php";

if (isset($_POST['fall'])) {
	$_SESSION['fall']=(int)$_POST['fall'];
} else {
	$_POST['fall']=(int)$_SESSION['fall'];	
}

if (!$user['login']) header("Location: index.php");
if ($user['level'] < 1) { header("Location: main.php");  die(); }
if ($user['room'] != 25) { header("Location: main.php");  die(); }
if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
if ($_SESSION['boxisopen'] != 'open') { header('location: main.php?edit=1'); die(); }

$komok_id = $user['id_city'];

$d[0] = getmymassa($user);

if(!$_GET['kredit']) { $_GET['kredit']=$_POST['kredit']; }
if(!$_GET['n']) { $_GET['n']=(int)$_POST['set']; }

if(!$_SESSION['beginer_quest'][none]) {
	$last_q=check_last_quest(5);
	if($last_q) {
		quest_check_type_5($last_q);
		//проверяем квесты на хар-и
	}

	$last_q=check_last_quest(2);
	if($last_q) {
		quest_check_type_2($last_q);
		//проверяем квесты на хар-и
	}
}

function realpriceitem($item){
	$ret = array('min' => 0, 'max' => 0);

	if ($item['otdel'] == 62) {
		$ret['min'] = round($item['cost']*0.75,2);
		$ret['max'] = round($item['cost']*2,2);
		return $ret;
	}

	// максимум 100 кр для пушек хаоса
	$weap_haos=array(1006232,1006233,1006234,1006241,1006242,199,201,204);
	if (in_array($item['prototype'],$weap_haos)) {
		$ret['min'] = 1;
		$ret['max'] = 100;
		return $ret;
	}

	$max_ups = 5;   // Максимальное кол. апов шмотки - подобная запись есть В РЕМОНТКЕ! Там меня так же!!!

	$prot = mysql_query_cache('select * from oldbk.shop where id = '.$item['prototype'],false,5*60);
	$prote = mysql_query_cache('select * from oldbk.eshop where id = '.$item['prototype'],false,5*60);

	list($k,$prot) = each($prot);
	list($k,$prote) = each($prote);

	if($prot['id'] > 0) {

	} else {
    		$prot['cost'] = $item['cost'];
		$prot['nlevel'] = 0;
	}

	$mf_cost = 0;
	$is_mf = !(strpos($item['name'], '(мф)') === false);

	if($is_mf > 0) {
		$mf_cost = $prot['cost'] * 0.5;

	    	if (($prot['gsila'] == 0) and ($prot['glovk'] == 0) and ($prot['ginta'] == 0) and ($prot['gintel'] == 0) and ($prot['gmp'] == 0)) {
			$mf_cost = round($mf_cost*0.5, 0);
		}

	}


	$cost_add = round($prot['cost'], 0);
	$max_ups_left = $max_ups - $item['ups'];
	$mx_op = array(1=>'5',2=>'4',3=>'3',4=>'2',5=>'1');
	$u_cost = 0;


	// исключение - плащи, футболки
	if ($item['type'] == 27 || $item['type'] == 28) {
		if ($prot['unikflag'] > 0 && $item['unik'] > 0) {
			$ret['min'] = $ret['max'] = EKR_TO_KR*$prot['unikflag'];
		} elseif ($prote['unikflag'] > 0 && $item['unik'] > 0) {
			$ret['min'] = $ret['max'] = EKR_TO_KR*$prote['unikflag'];
		} elseif ($prot['ecost'] > 0) {
			$ret['min']  = $mf_cost+(EKR_TO_KR*$prot['ecost']);
			$ret['max']  = $mf_cost+(EKR_TO_KR*$prot['ecost']);
		} elseif ($prote['ecost'] > 0) {
			$ret['min']  = $mf_cost+(EKR_TO_KR*$prote['ecost']);
			$ret['max']  = $mf_cost+(EKR_TO_KR*$prote['ecost']);
		} else {
			$ret['min'] = $ret['max'] = $prot['cost'];
			$ret['min'] += $mf_cost;
			$ret['max'] += $mf_cost;

			if (!empty($item['includemagicname'])) {
				$ret['min'] += $item['includemagicuses'];
				$ret['max'] += $item['includemagicuses'];
			}

			if($item['ups']>0 && $item['sowner'] == 0) {
				for($cc = $item['ups'];$cc>0;$cc--) {
					$costs[$cc]=upgrade_item($cost_add,$mx_op[$cc]);
					$u_cost += $costs[$cc][up_cost];
				}
				$ret['min'] += $u_cost;
				$ret['max'] += $u_cost;
			}
		}

		if ($item['unik'] == 2) {
			$protm = mysql_query_cache('select * from oldbk.shop where id = 1122',false,5*60);				
			list($k,$protm) = each($protm);

			$ret['min'] += EKR_TO_KR*$protm['ecost'];
			$ret['max'] += EKR_TO_KR*$protm['ecost'];

		}

		$ret['min'] = round($ret['min']*0.75,2);

		return $ret;
	}


	$real_price['sowner'] = $item['sowner'];
	$real_price['prot_cost'] = $prot['cost'];
	    
	$real_price['mf_cost'] = $mf_cost;

    	$real_price['item_cost'] = $real_price['prot_cost'];

	if($item['ups']>0 && $real_price['sowner'] == 0) {
		for($cc = $item['ups'];$cc>0;$cc--) {
			$costs[$cc]=upgrade_item($cost_add,$mx_op[$cc]);
			$u_cost += $costs[$cc][up_cost];
		}
	}

	$real_price['u_cost'] = $u_cost;
		
		
	$sharp_pr = 0;
	if($item['type'] == 3) {
		$sharp= explode("+",$item['name']);
		if((int)($sharp[1])>0) {
			$is_sharp=array(1=>20,2=>40,3=>80,4=>160,5=>320, 6 => 640, 7 => 1280, 8 => 2560 , 9 => 5120);
			$sharp_pr=$is_sharp[$sharp[1]];
		}
	}


	$real_price['sharp_pr'] = $sharp_pr;

	$item['includemagicname'] = trim($item['includemagicname']);

	if (!empty($item['includemagicname'])) {
		$real_price['item_cost'] += $item['includemagicuses'];
		//if ($item['includemagiccost'] > 0) $real_price['item_cost'] += $item['includemagiccost']*2;
	}

	//высчитываем от госцены цена + мф + подгоны + заточка
	//print_r($real_price);
	$real_price['summ'] = $real_price['item_cost']+$real_price['mf_cost']+$real_price['u_cost']+$real_price['sharp_pr'];

	//print_r($real_price);

	if ($item['unik'] > 0) {
		$protm = mysql_query_cache('select * from oldbk.shop where id = 1122',false,5*60);				
		list($k,$protm) = each($protm);

		$real_price['summ'] += EKR_TO_KR*$protm['ecost']*$item['unik'];
	}

	// все остальные
	if ($item['type'] < 12 || $item['type'] == 28) {
		$ret['max'] = $real_price['summ']*1;
	} else {
		$ret['max'] = $real_price['summ']*1.5;
	}
	$ret['min'] = $real_price['summ']*0.75;

	if ($item['type'] == 200) {
		$ret['min'] = $real_price['summ'];
		if ($item['otdel'] == 72 && $real_price['summ'] >= 10) {
			$ret['max'] = 120;
		}
	}
	return $ret;	
}


if (($_GET['sale'] && $_GET['kredit'] && $_GET['n']) && $user['align'] != 4) {
	$_GET['kredit'] = round($_GET['kredit'],2);
	$_POST['count']=(int)$_POST['count'];
			
	if ((is_numeric($_GET['kredit']) && $_GET['kredit']>=1) && (is_numeric($_GET['n']) && $_GET['n']>0)) {
		if($_GET['n'] && $_POST['count'] && $_POST['is_sale']==1 && $_GET['tmp']>=0) {
			$sql=' AND prototype="'.(int)$_GET['n'].'" AND duration="'.(int)$_GET['tmp'].'"';
		} else {
			$sql=' AND id="'.(int)$_GET['n'].'"';
		}
				
		if(!$_POST['count']) {$_POST['count']=1;}
				
		if($user['money']>=$_POST['count']) {
			//by Umk - при сдаче предмета снимается 1 кр, возвбращается при продаже. ( первый раз на кроне вернутся шмотки с 1 кр)
			$new_sql='';
			$new_delo='';
			$ff=0;

			$sql="SELECT * FROM oldbk.`inventory` USE INDEX (owner_4) WHERE `setsale`=0 AND
			`dressed`=0 ".$sql." AND `owner` = '{$user['id']}' AND sowner=0 AND prototype 
			not IN (40000001,2123456804,272,5277,5278,121121122,121121123,121121124,100005,100015,100020,100025,100040,100100,100200,100300,2013005,284,18210,18229,18247,18527,2000,2001,2002,260,262,283,3005000) AND `present` = '' AND  otdel not in (74) AND ekr_flag=0 AND type!=77 and notsell = 0 AND bs_owner=0 LIMIT ".$_POST['count'].";";

			$error = "";
			$row = mysql_query($sql);
			if(mysql_num_rows($row) > 0) {
				while($res = mysql_fetch_assoc($row)) {
					if (olditemdress($res,$user) == false) {
						$error = "Этот предмет нельзя сдать, он устарел...";
						$typet = "e";
						$ok = 0;								 	
						$stop = 1;								
					} else {
						$price = realpriceitem($res);
						if($_GET['kredit'] > $price['max'] || $_GET['kredit'] < $price['min']) {
							$error = "Для данного предмета минимальная цена ".$price['min']." кр., максимальная ".$price['max']." кр.";
							$typet = "e";                                                                        
							$ok = 0;								 	
							$stop = 1;
						}
					}
					
					if ($stop != 1) {
						if (($res['prototype']>3000 && $res['prototype']<3022) || ($res['prototype']> 103000 && $res['prototype'] < 103030) || ($res['otdel'] == 62 && empty($res['craftedby']))) {
							$error = "Ресурсы без клейма нельзя сдать...";
							$typet = "e";
							$ok = 0;
						} elseif (in_array($res['prototype'],$vaucher)) {
							$error = "Ваучеры нельзя сдать...";
							$typet = "e";
							$ok = 0;
						} elseif ( $res['prototype']==4307 || $res['prototype']==4308) {
							$error = "Этот предмет нельзя сдать...";
							$typet = "e";
							$ok = 0;
						} elseif($res['prototype']==2010042 || $res['prototype']==2010043 || $res['prototype']==2010046 || $res['prototype']==2010032 || $res['prototype']==2010033 || $res['prototype']==2010036) {
							$error = "Молитвенные пренадлежности нельзя сдать...";
							$typet = "e";
							$ok = 0;
						} elseif($res['duration'] == $res['maxdur'] && $res['isrep'] == 0) {
							$error = "Сломанную вещь нельзя сдать...";
							$typet = "e";
							$ok = 0;
						} elseif($res['prototype']==2123456804 || $res['prototype']==40000001) {
							$error = "Подарки нельзя сдать...";
							$typet = "e";
							$ok = 0;
						} else {
							$item[$ff]=$res;
							$ok = 1;
							$ff++;
						}
					}
				}
			} else {
				$ok = 0;
				$error = 'Невозможно сдать предметы!';
			}
		
			if ($stop != 1)	{
				if (!(give_count($user['id'],count($item)))) {
					$ok = 0;
					$error = 'Невозможно сдать предметы, у Вас недостаточно лимита передач!';
                 		}
                    	}
			
			if($ok == 1 && empty($error)) {
				$add_to_index = array();

				for($jj=0;$jj<count($item);$jj++) {
					$add_to_index[$jj]=$item[$jj]['id'];
					$new_sql.=$item[$jj]['id'].',';
					$new_delo.=get_item_fid($item[$jj]).',';
				}

				$new_sql = substr($new_sql,0,-1);
				$new_delo = substr($new_delo,0,-1);
			
				$summa = $_GET['kredit']*count($item);
				if($item[0]['add_pick'] != '') {
					undress_img($item[0]);
				}
			
				mysql_query("UPDATE oldbk.`inventory` SET `setsale` = '".$_GET['kredit']."' , t_id='{$komok_id}' WHERE `id` in (".$new_sql.") AND `owner` = '{$_SESSION['uid']}';");
				if(mysql_affected_rows()>0) { 
					$sql_to_index="INSERT INTO oldbk.`comission_indexes`
					(id_item,duration,max_dur,prototype,owner,id_city,cost,img,name,nalign,massa,otdel,timer)
					VALUES ";

					foreach($add_to_index as $k=>$v) {
						$sql_to_index.="('".$v."','".$item[0][duration]."','".$item[0][maxdur]."','".$item[0][prototype]."','".$user[id]."','".$komok_id."','".$_GET['kredit']."','".$item[0][img]."',
						'".$item[0][name]."','".$item[0][nalign]."','".$item[0][massa]."','".$item[0][otdel]."','".(time()+60*60*24*14)."'),";
					}

					$sql_to_index=substr($sql_to_index,0,-1).';';
					mysql_query($sql_to_index);
			
					$rec['owner']=$user[id];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$user['money']-=(count($item));
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='Комок';
					$rec['type']=120;//сдача в комок
					$rec['sum_kr']=$summa;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=count($item);
					$rec['item_id']=$new_delo;
					$rec['item_name']=$item[0]['name'];
					$rec['item_count']=count($item);
					$rec['item_type']=$item[0]['type'];
					$rec['item_cost']=$item[0]['cost'];
					$rec['item_dur']=$item[0]['duration'];
					$rec['item_maxdur']=$item[0]['maxdur'];
					$rec['item_ups']=$item[0]['ups'];
					$rec['item_unic']=$item[0]['unik'];
					$rec['item_incmagic_id']=$item[0]['includemagic'];
					$rec['item_ecost']=$item[0]['ecost'];
					$rec['item_proto']=$item[0]['prototype'];
					$rec['item_sowner']=($item[0]['sowner']>0?1:0);
					$rec['item_incmagic']=$item[0]['includemagicname'];
					$rec['item_incmagic_count']=$item[0]['includemagicuses'];
					$rec['item_mfinfo']=$item[0]['mfinfo'];
					$rec['item_level']=$item[0]['nlevel'];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру
					mysql_query("UPDATE users SET money='".$user['money']."' WHERE id='".$user['id']."';");

					$msg = "Вы сдали в магазин \"".$item[0]['name']."\" ".count($item)."шт. на сумму ".$summa." кр. за ".count($item)."кр.";
				}
			} else {
				$msg = $error;
				$typet = "e";
			}
		} else {
			$msg = "У вас недостаточно денег на уплату комиссии.";
			$typet = "e";
		}
	} else {
		$msg = "Не надо так делать";
		$typet = "e";
	}
}

if ($_GET['back']) {	
	if (is_numeric($_GET['back']) && $_GET['back'] > 0) {
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `dressed`=0 AND  `id` = '{$_GET['back']}' AND `owner` = '{$_SESSION['uid']}' AND t_id='{$komok_id}' AND `setsale` > '0' LIMIT 1;"));
		
		if($dress['id']) {
			if ((give_count($user['id'],1))) {
				mysql_query("UPDATE oldbk.`inventory` SET `setsale` = '0' WHERE `id` = '{$_GET['back']}' AND `owner` = '{$_SESSION['uid']}' LIMIT 1;");
				mysql_query("DELETE FROM oldbk.`comission_indexes` WHERE id_item='".$_GET['back']."' LIMIT 1");
			
				$rec['owner']=$user['id'];
				$rec['owner_login']=$user['login'];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['target']=0;
				$rec['target_login']='Комок';
				$rec['type']=121;//забрал из госа
				$rec['sum_kr']=0;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=get_item_fid($dress);
				$rec['item_name']=$dress['name'];
				$rec['item_count']=1;
				$rec['item_type']=$dress['type'];
				$rec['item_cost']=$dress['cost'];
				$rec['item_dur']=$dress['duration'];
				$rec['item_maxdur']=$dress['maxdur'];
				$rec['item_ups']=$dress['ups'];
				$rec['item_unic']=$dress['unik'];
				$rec['item_incmagic_id']=$dress['includemagic'];
				$rec['item_ecost']=$dress['ecost'];
				$rec['item_proto']=$dress['prototype'];
				$rec['item_sowner']=($dress['sowner']>0?1:0);
				$rec['item_incmagic']=$dress['includemagicname'];
				$rec['item_incmagic_count']=$dress['includemagicuses'];
				$rec['item_mfinfo']=$dress['mfinfo'];
				$rec['item_level']=$dress['nlevel'];
				$rec['item_arsenal']='';
				add_to_new_delo($rec); //юзеру
			
				$msg = "Вы забрали из магазина \"".$dress['name']."\" </b></font>";
			} else {
				$msg = 'Невозможно забрать предмет, у Вас недостаточно лимита передач!';
				$typet = "e";
			}
		} else {
			$msg = "Произошла ошибка. Вещь не найдена в магазине!";
			$typet = "e";
		}
	} else {
		$msg = "Не надо так делать";
		$typet = "e";
	}
} elseif($_GET['prodl']) {
	if (is_numeric($_GET['prodl']) && $_GET['prodl'] > 0) {
		if($user['money'] > 0) {	
			$dress = mysql_fetch_array(mysql_query("SELECT i.*,ci.timer FROM oldbk.`inventory` i 
			LEFT JOIN oldbk.comission_indexes ci
			ON i.id=ci.id_item
			WHERE i.`dressed`=0 AND  i.`id` = '{$_GET['prodl']}' AND i.`owner` = '{$_SESSION['uid']}' AND i.t_id='{$komok_id}' AND i.`setsale` > '0' AND bs_owner=".$user[in_tower]." LIMIT 1;"));
			
			if($dress['id'] && (time()+60*60*24)>$dress['timer']) { 
				mysql_query("UPDATE oldbk.`comission_indexes` SET `timer` = '".(time()+60*60*24*14)."' WHERE `id_item` = '{$dress['id']}' AND `owner` = '{$_SESSION['uid']}' LIMIT 1;");
				if(mysql_affected_rows()>0) {
					mysql_query("UPDATE `users` set `money` = `money`- 1 WHERE id = {$_SESSION['uid']}");
					
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$user['money']-=1;
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='Комок';
					$rec['type']=320;//заплатил за продление зранения
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=1;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress['name'];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress['ups'];
					$rec['item_unic']=$dress['unik'];
					$rec['item_incmagic_id']=$dress['includemagic'];
					$rec['item_ecost']=$dress['ecost'];
					$rec['item_proto']=$dress['prototype'];
					$rec['item_sowner']=($dress['sowner']>0?1:0);
					$rec['item_incmagic']=$dress['includemagicname'];
					$rec['item_incmagic_count']=$dress['includemagicuses'];
					$rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру
				
					$msg = "Вы продлили срок хранения предмета \"".$dress['name']."\" в магазине до ".(date('d-m H:i',(time()+60*60*24*14)))." </b></font>";
				}
			} else {
				$msg = "Произошла ошибка. Вещь не найдена в магазине или срок хранения еще рано продлять!";
				$typet = "e";
			}
		} else {
			$msg = "У Вас недостаточно денег!";
			$typet = "e";
		}
	} else {
		$msg = "Не надо так делать";
		$typet = "e";
	}
}

$_GET['set']=(int)$_GET['set'];
$_POST['set']=(int)$_POST['set'];
	
if (($_GET['set'] || $_POST['set'])&&!$_POST['count'] && $_POST['is_sale'] != 1) {
	if ($_GET['set']) { $set = $_GET['set']; }
	if ($_POST['set']) { $set = $_POST['set']; }
	if (!$_POST['count'] || !(int)($_POST['count']) || $_POST['count']<=0) { $_POST['count'] = 1;}

	if ((int)($set) && $set>0) {
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `dressed`=0 AND `id` = '{$set}' AND `setsale` > '0' AND bs_owner=".$user[in_tower]." AND t_id='{$komok_id}' LIMIT 1;"));

		$userfrom = check_users_city_data($dress['owner']);

		if ($userfrom['id'] && $dress['id']) {
			if (($dress['massa']+$d[0]) > (get_meshok())) {
				$msg = "Недостаточно места в рюкзаке.";
				$typet = "e";
			} elseif ($user['money'] >= $dress['setsale']) {
				if (give_count($user['id'],1)) {
					mysql_query("UPDATE oldbk.`inventory` SET `owner` = '{$user['id']}', `setsale` = 0, `t_id`=0 WHERE `id` = '{$set}' AND `setsale` > '0' AND t_id='{$komok_id}' LIMIT 1;");
					if(mysql_affected_rows()>0) {
						mysql_query("DELETE FROM oldbk.`comission_indexes` WHERE id_item='".$set."' LIMIT 1");

						$msg = "Вы купили \"".$dress['name']."\".";
						$moneyto=round($dress['setsale']*0.90,2);
						$komiss = round($dress['setsale']*0.10,2);

						mysql_query("UPDATE `users` set `money` = `money`- '".$dress['setsale']."' WHERE id = {$_SESSION['uid']}");

						mysql_query("UPDATE ".$db_city[$userfrom[id_city]]."`users` set `money` = `money`+ '".($moneyto+1)."' WHERE id = {$userfrom['id']}");

	                			$rec['owner']=$user['id'];
						$rec['owner_login']=$user['login'];
						$rec['owner_balans_do']=$user['money'];
						$user['money'] -= $dress['setsale'];
						$rec['owner_balans_posle']=$user['money'];
						$rec['target']=$userfrom['id'];
						$rec['target_login']=$userfrom['login'];
						$rec['type']=122;//купил через комок
						$rec['sum_kr']=$dress['setsale'];
						$rec['sum_ekr']=0;
						$rec['sum_kom']=$komiss;
						$rec['item_id']=get_item_fid($dress);
						$rec['item_name']=$dress['name'];
						$rec['item_count']=1;
						$rec['item_type']=$dress['type'];
						$rec['item_cost']=$dress['cost'];
						$rec['item_dur']=$dress['duration'];
						$rec['item_maxdur']=$dress['maxdur'];
						$rec['item_ups']=$dress['ups'];
						$rec['item_unic']=$dress['unik'];
						$rec['item_incmagic_id']=$dress['includemagic'];
	                    			$rec['item_ecost']=$dress['ecost'];
						$rec['item_proto']=$dress['prototype'];
						$rec['item_sowner']=($dress['sowner']>0?1:0);
						$rec['item_incmagic']=$dress['includemagicname'];
						$rec['item_incmagic_count']=$dress['includemagicuses'];
						$rec['item_arsenal']='';
						$rec['item_mfinfo']=$dress['mfinfo'];
						$rec['item_level']=$dress['nlevel'];
						add_to_new_delo($rec); //купил через комок
	                			$rec['sum_kom']=$komiss;
						$rec['owner']=$userfrom['id'];
						$rec['owner_login']=$userfrom['login'];
						$rec['owner_balans_do']=$userfrom['money'];
						$rec['owner_balans_posle']=$userfrom['money']+$moneyto+1;
						$rec['target']=$user['id'];
						$rec['target_login']=$user['login'];
						$rec['type']=123;//продал через комок предмет
						add_to_new_delo($rec); //кому
					
						$text='<font color=red>Внимание!</font> Успешно продан предмет "'.$dress['name'].'" за '.$dress['setsale'].' кр.  Комиссия составила '.$komiss.' кр. Вам перечислено от комиссионного магазина '.$moneyto.' кр. и 1кр. за сданные вещи';
						telepost_new($userfrom,$text) ;
					} else {
						$msg = "Недостаточно денег или нет вещей в наличии.";
						$typet = "e";
					}
				} else {
                    		   	$msg = 'Невозможно купить предмет, у Вас недостаточно лимита передач!';
					$typet = "e";
                    		}
			} else {
				$msg = "Недостаточно денег или нет вещей в наличии.";
				$typet = "e";
			}
		} else {
			$msg = "Вещь не найдена в магазине.";
			$typet = "e";
		}
	} else {
		$msg = "Не надо так делать.";
		$typet = "e";
	}
} elseif(($_GET['set'] || (int)$_POST['set']) && (int)$_POST['count'] >0 && (int)$_POST['is_sale']==0) {
	$_POST['count']=(int)$_POST['count'];

	$item=array();
	$gg=0;
	$summ=0;
	$massa=0;
	$mycount=0;
	$mysumm=0;
	if($_POST['count'] > 10){$_POST['count']=10;}

	$data = mysql_query('SELECT oldbk.inventory.* FROM oldbk.comission_indexes 
	LEFT JOIN oldbk.inventory ON oldbk.inventory.id = oldbk.comission_indexes.id_item WHERE oldbk.comission_indexes.prototype = '.intval($_POST['set']).' AND oldbk.comission_indexes.id_city = '.$komok_id.' AND (oldbk.inventory.type = 12 or oldbk.inventory.otdel = 62) AND oldbk.inventory.setsale> 0 AND oldbk.inventory.dressed = 0 
	ORDER BY oldbk.comission_indexes.`cost` ASC LIMIT '.intval($_POST['count']));


	while($dress=mysql_fetch_assoc($data)) {
		$summ+=$dress['setsale'];
		$massa+=$dress['massa'];
		$item[$dress['owner']][]=$dress;
		$gg++;
	}

	if (($massa+$d[0]) > (get_meshok())) {
		$msg = "Недостаточно места в рюкзаке.";
		$typet = "e";
	} elseif ($user['money'] >= $summ) {
		if (give_count($user['id'],$gg)) {
			foreach($item as $k => $v) {
				$delo_sql='';
				$id_sql='';
				$saller_summ=0;
				$count=0;

				$userfrom = check_users_city_data($k);
				for($hg=0;$hg<count($v);$hg++) {

					$id_sql.=$v[$hg]['id'].',';
					$delo_sql.=get_item_fid($v[$hg]).',';
					$saller_summ+=$v[$hg]['setsale'];
					$count++;
					$mycount++;
					$mysumm += $v[$hg]['setsale'];
				}

				$id_sql = substr($id_sql, 0, -1);
				$delo_sql = substr($delo_sql, 0, -1);
				$moneyto = round($saller_summ*0.90,2);
				$komiss = round($saller_summ*0.10,2);
	
				mysql_query("UPDATE oldbk.`inventory` SET `owner` = ".$user['id'].", `setsale` = 0, `t_id`=0 WHERE `id` in (".$id_sql.") AND `setsale` > '0';");

				if(mysql_affected_rows() >0 ) {
					$sql_del="DELETE FROM oldbk.`comission_indexes` WHERE id_item in (".$id_sql.")";

					mysql_query($sql_del);
					mysql_query("UPDATE `users` set `money` = `money`- '".$saller_summ."' WHERE id = {$_SESSION['uid']}");
					mysql_query("UPDATE ".$db_city[$userfrom[id_city]]."`users` set `money` = `money`+ '".($moneyto+$count)."' WHERE id = {$userfrom['id']}");
			
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$user['money'] -= $saller_summ;
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$userfrom['id'];
					$rec['target_login']=$userfrom['login'];
					$rec['type']=122;//купил через комок
					$rec['sum_kr']=$saller_summ;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=$komiss;
					$rec['item_id']=$delo_sql;
					$rec['item_name']=$v[0]['name'];
					$rec['item_count']=$count;
					$rec['item_type']=$v[0]['type'];
					$rec['item_cost']=$v[0]['cost'];
					$rec['item_dur']=$v[0]['duration'];
					$rec['item_maxdur']=$v[0]['maxdur'];
					$rec['item_ups']=$v[0]['ups'];
					$rec['item_unic']=$v[0]['unik'];
					$rec['item_incmagic_id']=$v[0]['includemagic'];
					$rec['item_ecost']=$v[0]['ecost'];
					$rec['item_proto']=$v[0]['prototype'];
					$rec['item_sowner']=($v[0]['sowner']>0?1:0);
					$rec['item_incmagic']=$v[0]['includemagicname'];
					$rec['item_incmagic_count']=$v[0]['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['item_mfinfo']=$dress['mfinfo'];
					$rec['item_level']=$dress['nlevel'];
					add_to_new_delo($rec); //купил через комок
					$rec['sum_kom']=$komiss;
					$rec['owner']=$userfrom['id'];
					$rec['owner_login']=$userfrom['login'];
					$rec['owner_balans_do']=$userfrom['money'];
					$rec['owner_balans_posle']=$userfrom['money']+$moneyto+$count;
					$rec['target']=$user['id'];
					$rec['target_login']=$user['login'];
					$rec['type']=123;//продал через комок предмет и получил бабло
					add_to_new_delo($rec); //кому
	
					$text='<font color=red>Внимание!</font> Успешно продан предмет "'.$v[0]['name'].'" (x'.$count.') за '.$saller_summ.' кр.  Комиссия составила '.$komiss.' кр. Вам перечислено от комиссионного магазина '.$moneyto.' кр. и '.$count.'кр. за сданные вещи ';
					telepost_new($userfrom,$text) ;
				}
			}

			$msg = "Вы купили \"".$v[0]['name']."\". (х".$mycount.") за ".$mysumm."кр.";
		} else {
			$msg = 'Невозможно купить предметы, у Вас недостаточно лимита передач!';
			$typet = "e";
		}
	} else {
		$msg = "Недостаточно денег или нет вещей в наличии.";
		$typet = "e";
	}
}

if ($_GET['unsale'] && $_GET['kredit'] && $_GET['id']) {
	if ((is_numeric($_GET['kredit']) && $_GET['kredit']>=1) && (is_numeric($_GET['id']) &&  $_GET['id']>0)) {
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `dressed`=0 AND `id` = '{$_GET['id']}' AND `owner` = '{$_SESSION['uid']}' AND `setsale` > 0 AND bs_owner=".$user[in_tower]." and t_id='{$komok_id}' LIMIT 1;"));

		if($dress['id']) {
			$price = realpriceitem($dress);
			if($_GET['kredit'] > $price['max'] || $_GET['kredit'] < $price['min']) {
				$error = "Для данного предмета минимальная цена ".$price['min']." кр., максимальная ".$price['max']." кр.";
				$typet = "e";                                                                        
				$ok = 0;								 	
				$stop = 1;
			}
			if ($stop!=1) {
				if($user['money'] >= 0.1) {
					mysql_query("UPDATE oldbk.`inventory` SET `setsale` = '".$_GET['kredit']."' WHERE `id` = '{$_GET['id']}' AND `owner` = '{$_SESSION['uid']}' LIMIT 1;");
					mysql_query("UPDATE `users` set `money` = `money`- '0.1' WHERE id = {$_SESSION['uid']}");
					$sql="UPDATE oldbk.`comission_indexes` set cost='".$_GET['kredit']."' WHERE id_item= '".$_GET['id']."' LIMIT 1;";
					mysql_query($sql);
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$user['money'] -= 0.1;
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='Комок';
					$rec['type']=124;//Смена цены
					$rec['sum_kr']=0.1;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress['name'];
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress['ups'];
					$rec['item_unic']=$dress['unik'];
					$rec['item_incmagic_id']=$dress['includemagic'];
					$rec['item_ecost']=$dress['ecost'];
					$rec['item_proto']=$dress['prototype'];
					$rec['item_sowner']=($dress['sowner']>0?1:0);
					$rec['item_incmagic']=$dress['includemagicname'];
					$rec['item_incmagic_count']=$dress['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['item_mfinfo']=$dress['mfinfo'];
					$rec['item_level']=$dress['nlevel'];
					add_to_new_delo($rec);
				
					$msg = "Вы изменили цену \"{$dress['name']}\" на {$_GET['kredit']} кр.";
				} else {
					$msg = "У вас недостаточно денег на выполнение операции.";
					$typet = "e";
				}
			} else {
				$msg = $error;
				$typet = "e";
			}
		} else {
			$msg = "Предмет не найден.";
			$typet = "e";
		}
	} else {
		$msg = "Не надо так делать";
		$typet = "e";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="windows-1251">
<title></title>
<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<style>
button:focus {
    outline: 0;
}

input:focus {
    outline: 0;
}

SELECT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
TEXTAREA {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
INPUT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}

.sell-dialog-class .ui-dialog-title {
	FONT-FAMILY: Tahoma;
	FONT-SIZE: 13px;
}

.noty_message { padding: 5px !important;}

#page-wrapper table td {
	vertical-align:middle;
}

</style>
<SCRIPT LANGUAGE="JavaScript">

function sale(name, txt, n, kr)
{
	var s = prompt("Сдать в магазин \""+txt+"\" (налог 1кр.). Укажите цену:", kr);
	if ((s != null)&&(s != '')&&(s>=1)) {
		location.href="?sale="+name+"&kredit="+s+"&n="+n;
	}
}
function chsale(name, txt, id, category, kr)
{
	var s = prompt("Сменить цену для предмета \""+txt+"\". Укажите новую цену:", kr);
	if ((s != null)&&(s != '')&&(s>=1)) {
		location.href="?unsale="+name+"&id="+id+"&sc="+category+"&kredit="+s;
	}
}

function showhide(id)
	{
	 if (document.getElementById(id).style.display=="none")
	 	{document.getElementById(id).style.display="block";}
	 else
	 	{document.getElementById(id).style.display="none";}
	}

function AddCount(name, txt, sale, href) {
	var el = document.getElementById("hint3");
    if(sale==1)
    {
    var sale_txt= 'Сдать неск. штук (налог 1 кр. за каждую вещь)';
    var a_href='action="'+href+'"';
    var input_txt='Цена (за штуку)&nbsp;&nbsp;<INPUT TYPE="text" NAME="kredit" size=4 ></td><td width=20%></td></tr><tr><td align=left>';
    }
    else
    {
    var sale_txt= 'Купить неск. штук (Будут куплены вещи с наименьшей ценой. Макс 10 шт.)';
    var a_href='action="'+href+'"';
    var input_txt='';
    }

	el.innerHTML = '<form '+a_href+' method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B style="font-size:11pt;">'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();return false;"><BIG><B>x</TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="is_sale" value="'+sale+'"><INPUT TYPE="hidden" name="set" value="'+name+'">'+
	'<td colspan=2 align=center><B><I style="font-size:11pt;">'+txt+'</td></tr><tr><td width=80% align=left style="font-size:11pt;">'+
	input_txt+
	'Количество (шт.)&nbsp;<INPUT TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	el.style.top = (document.body.scrollTop + 5) + 100 + 'px';
	document.getElementById("count").focus();
}
function closehint3()
{
	document.getElementById("hint3").style.visibility="hidden";
}
</SCRIPT>
</HEAD>
<body id="arenda-body">
<script type='text/javascript'>
RecoverScroll.start();
</script>
<div id="page-wrapper">
    <div class="title">
        <div class="h3">Комиссионный Магазин</div>
        <div id="buttons">
            <a class="button-dark-mid btn" onclick="window.open('help/comission.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes'); return false;" title="Подсказка">Подсказка</a>
            <a class="button-mid btn" OnClick="location.href='?tmp='+Math.random(); return false;" title="Обновить">Обновить</a>
            <a class="button-mid btn" OnClick="document.getElementById('cityform').submit(); return false;" title="Вернуться">Вернуться</a>
	    <FORM action="city.php" style="margin:0px;padding:0px;display:block;" id="cityform" method="GET"><INPUT TYPE="hidden" value="cp" name="cp"></form>
        </div>
    </div>
    <div id="shop">
        <table cellspacing="0" cellpadding="0">
            <colgroup>
                <col>
                <col width="320px">
            </colgroup>
            <tbody>
            <tr>
                <td style="vertical-align:top;">
                    <table class="table border" style="margin-bottom: 0;" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="600px">
                            <col>
                        </colgroup>
                        <thead>
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title">
				<?php
				 	if((int)$_REQUEST['max'] && ($_REQUEST['otdel']==5 || $_REQUEST['otdel']==51 || $_REQUEST['otdel']==62)) {
	 					echo 'Купить несколько ';
	 					?><IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Купить несколько штук" style="cursor: pointer" onclick="AddCount('<?=$_REQUEST['max']?>', '','0','?otdel=<?=$_REQUEST['otdel']?>&max=<?=$_REQUEST['max']?>'); return false;"><?
	 				}
				?>
				<?php
				if (isset($_REQUEST['max'])) {
					echo '<a href="?otdel='.$_GET['otdel'].'">&lt;&lt; ';
				}
				?>Отдел &quot;<?php

	$_REQUEST['max']=isset($_REQUEST['max']) ? strip_tags($_REQUEST['max']):'';

	// fix for look item in ordel if мф ок up
	$_REQUEST['max'] = preg_replace('#\[(.*?)\]#si', '', $_REQUEST['max']);
	$_REQUEST['max'] = preg_replace('#\((.*?)\)#si', '', $_REQUEST['max']);

	$_GET['otdel']=isset($_GET['otdel'])?(int)($_GET['otdel']):1;


	if ($_REQUEST['sale']) {
		echo "Сдать вещи";
	} elseif ($_REQUEST['unsale']) {
		echo "Забрать вещи";
	} else

	switch ($_GET['otdel']) {
		case null:
			if (!$_REQUEST['max']) {
			echo "Кастеты, ножи"; }
			else echo $_REQUEST['max'];
			$_GET['otdel'] = 1;
		break;
		case 1:
			echo "Кастеты, ножи";
		break;
		case 11:
			echo "Топоры";
		break;
		case 12:
			echo "Дубины,булавы";
		break;
		case 13:
			echo "Мечи";
		break;
		case 2:
			echo "Сапоги";
		break;
		case 21:
			echo "Перчатки";
		break;
		case 22:
			echo "Легкая броня";
		break;
		case 23:
			echo "Тяжелая броня";
		break;
		case 24:
			echo "Шлемы";
		break;
		case 3:
			echo "Щиты";
		break;
		case 4:
			echo "Серьги";
		break;
		case 41:
			echo "Ожерелья";
		break;
		case 42:
			echo "Кольца";
		break;
		case 5:
			echo "Заклинания: нейтральные";
		break;
		case 51:
			echo "Боевые и защитные";
		break;
		case 52:
			echo "Прочее";
		break;
		case 6:
			echo "Амуниция";
		break;
		case 61:
			echo "Еда";
		break;
		case 62:
			echo "Ресурсы";
		break;
		case 63:
			echo "Инструменты";
		break;
		case 7:
			echo "Сувениры: открытки";
		break;
		case 71:
			echo "Сувениры: другие подарки";
		break;
		case 73:
			echo "Сувениры: подарки";
		break;
		case 72:
			echo "Сувениры: уникальные подарки";
		break;
	}

	if (isset($_REQUEST['max'])) {
		echo '</a>';
	}

	?>&quot;
				</div>
                            </th>
                            <th class="filter">
			    	<form method="POST" id="fall" style="margin:0px;padding:0px;display:block;">
                                <div class="head-title" style="top:1px;">
                                    <select name="fall" style="width:250px;height:14px;margin:0px;top:0px;" OnChange="document.getElementById('fall').submit();">
					<option value = "0" <? if ((int)($_POST['fall'])==0) { echo ' selected ' ; } ?>>Показывать все вещи</option>
					<option value = "1" <? if ($_POST['fall']>0) { echo ' selected ' ; $viewlevel=true; } ?>>Показывать вещи только моего уровня</option>
                                    </select>
                                </div>
				</form>
                                <div class="head-right"></div>
                            </th>
                        </tr>       
                        </thead>
                    </table>
                    <table class="table border a_strong" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="200px">
                            <col>
                        </colgroup>
                        <tbody>
                        <tr class="even2">
<?

	if (strlen($msg)) {
		echo '	
			<script>
				var n = noty({
					text: "'.addslashes($msg).'",
				        layout: "topLeft2",
				        theme: "relax2",
					type: "'.($typet == "e" ? "error" : "success").'",
				});
			</script>
		';
	}
    



if ($_REQUEST['max']) {
  	if ($viewlevel == true) {
		if ($user['level']>12) {
			$addlvl=" and nlevel='12'  ";  			
  		} else {
			$addlvl=" and nlevel='{$user['level']}'  ";
	  	}
  	} else {
		$addlvl="";
  	}

	$ot=(int)$_GET[otdel];	

	if ($ot == 72) {
		$fixq=' and ci.prototype!=4016';
	} elseif ($ot==6) {
		$fixq=' or ci.prototype=4016';	
	} else {
		$fixq='';	
	}

	$data = mysql_query('SELECT oldbk.inventory.* FROM oldbk.comission_indexes ci
	LEFT JOIN oldbk.inventory ON oldbk.inventory.id = ci.id_item 
	WHERE ci.prototype = '.intval($_REQUEST['max']).' 
	AND ci.id_city = '.$komok_id.' 
	AND oldbk.inventory.setsale> 0 AND ( ci.otdel="'.$ot.'" '.$fixq.'  )
	AND oldbk.inventory.dressed = 0 '.$addlvl.' ORDER BY ci.`cost` ASC');

	while($row = mysql_fetch_array($data)) {
		$row[GetShopCount()] = 1;

		if ($row['cost'] == 0) {
			$row['cost']=0.0001;
		}

		if (!empty($row['img_big'])) $row['img'] = $row['img_big'];

		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
		echo "<TR bgcolor={$color}><TD align=center style='width: 150px;'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		?>
		<BR><A HREF="?otdel=<?=$_GET['otdel']?>&set=<?=$row['id']?>&max=<?=$_GET['max']?>&sid=">купить</A></TD>
		<?php
		echo '<TD style="vertical-align:top;">';
		showitem ($row);
		echo "</TD></TR>";
	}
} elseif ($_REQUEST['sale'] && $user['align'] != 4) {
	echo "<TR bgcolor=#C7C7C7><TD align=center colspan=2><b>Комиссия за услуги магазина составляет 10% от цены, по которой вы предлагаете предмет.</b></TD></TR>";
	
	$data = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX (owner_4) WHERE `setsale` = 0 AND name!='Лотерейный билет' AND `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND otdel not in (74) AND type!=77  AND  sowner=0 AND ekr_flag=0  AND `present` = '' AND prototype not IN (40000001,2123456804,272,5277,5278,121121122,121121123,121121124,100005,100015,100020,100025,100040,100100,100200,100300,284,18210,18229,18247,18527,2000,2001,2002,260,262,283,3005000) AND bs_owner=".$user[in_tower]." and notsell = 0 ORDER by `update` DESC; ");
	while($row = mysql_fetch_array($data)) {
		if ($row['otdel'] == 62 && empty($row['craftedby'])) continue;

		if($row['present'] != ''){
			$prez=1;
		} else {
			$prez=0;
		}
		$inv_shmot[$prez][$row[duration]][$row['prototype']][]=$row;
  		$inv_gr_key[$row['prototype']]=$row[group];
	}

	foreach ($inv_shmot as $key2 => $value2) {
		foreach ($value2 as $key => $value) {
			foreach ($value as $key1 => $value1) {
				if($inv_gr_key[$key1] == 1) {
					$group_key=1;
				} else {
					$group_key=count($value1);
				}
	
				for($i=0;$i<$group_key;$i++) {
					if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }
			 		if($inv_gr_key[$key1]==1) {
						$value1[$i][GetShopCount()] =  count($value1);
					} else { 
						$value1[$i][GetShopCount()] = 1;
					}
	
			        	$price = round($value1[$i]['cost']*0.5,2);
			        	$item_type = (int)$value1[$i]["type"];
					if($item_type <= 11) {
						$price = round($value1[$i]['cost']*0.5,2);
					}
	
					if($item_type == 3) {
						$price = round($value1[$i]['cost']*0.5,2);
					}

					if($value1[$i]['add_pick'] != '' && $value1[$i]['pick_time'] > time()) {
						$value1[$i]['img']=$value1[$i]['add_pick'];
					}

					if (!empty($value1[$i]['img_big'])) $value1[$i]['img'] = $value1[$i]['img_big'];
	
					echo "<TR bgcolor={$color}>
					<TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$value1[$i]['img']}\" BORDER=0><BR>";
					?><BR><A onClick="sale('1', '<?=$value1[$i]['name']?>', '<?=$value1[$i]['id']?>', '<?=$value1[$i]['cost']?>'); return false;" HREF="#">cдать в магазин (комиссия за 1 кр.)</A> <?

					$retprice = realpriceitem($value1[$i]);
					echo '<br>Мин: '.$retprice['min'].' кр. Макс: '.$retprice['max'].' кр.';

	
					if($value1[$i]['group'] == 1) {
			        		?>
				        	<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Сдать несколько штук (комиссия 1 кр. за каждую)" style="cursor: pointer"
				        	onclick="AddCount('<?=$value1[$i][prototype]?>', '<?=$value1[$i][name]?>','1','?sale=1&tmp=<?=$value1[$i]['duration']?>');return false;">
				        	<?
					}
		
					echo '</TD><TD style="vertical-align:top;">';
					showitem($value1[$i]);
					echo "</TD></TR>";
				}
			}
	   	}
	}
} elseif ($_REQUEST['unsale']) {
	$data = mysql_query("SELECT i.*,ci.timer FROM oldbk.`inventory` i
		LEFT JOIN oldbk.comission_indexes ci
		ON i.id=ci.id_item
		WHERE i.`setsale` > 0 AND i.`owner` = '{$_SESSION['uid']}' 
		AND i.`dressed` = 0  and i.t_id='{$komok_id}' 
		ORDER by ci.`timer`;");
		
	while($row = mysql_fetch_array($data)) {
		$row[GetShopCount()] = 1;
		if (!empty($row['img_big'])) $row['img'] = $row['img_big'];
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		?>
		<BR><A HREF="?back=<?=$row['id']?>&sid=&unsale=1">забрать</A>
		<BR><A onClick="chsale('1', '<?=$row['name']?>', <?=$row['id']?>, '1', '<?=$row['setsale']?>'); return false;" HREF="#">сменить цену<BR>за 0.1 кр.</A>
		<?
		echo "<br><small>до ".(date('d-m H:i',$row['timer']))."<small>";
		
		if((time()+60*60*24)>$row['timer']) {
			echo '<br><a href=?prodl='.$row['id'].'&sid=&unsale=1>Продлить до <br>('.(date('d-m H:i',(time()+60*60*24*14))).') 1кр</a>';
		}
		
		?>
		<BR>Цена: <b><?=$row['setsale']?></b> кр.
		</TD>
		<?php
		echo '<TD style="vertical-align:top;">';
		showitem ($row);
		echo "</TD></TR>";
	}
} else {
	//для первого апреля
	if($_GET['otdel'] == 71) {
		$_GET['otdel']='71,74';
	}
		
	$sql = $_GET['otdel'];

	if ($sql == 72) {
		$fixq=' and ci.prototype!=4016';
	} elseif ($sql == 6) { 
		$fixq=' or ci.prototype=4016';	
	} else {
		$fixq='';	
	}

        if ($viewlevel == true) {
		if ($user['level'] > 13) {
	  		$al=13;	
  		} else {
			$al=$user['level'];
	  	}

		$data = mysql_query("SELECT DISTINCT  ci.`img`, ci.`name`, ci.`nalign`,ci.`massa`, ci.`prototype`,ci.`otdel`  from oldbk.comission_indexes ci LEFT JOIN oldbk.inventory i ON id_item=i.id where id_city='0' AND (ci.`otdel` in (".$sql.") ".$fixq."  ) and i.nlevel={$al}  GROUP BY ci.`prototype`   ORDER by ci.`cost` ASC ");

	} else {
		$data = mysql_query("SELECT DISTINCT `img`, `name`, `nalign`,`massa`, `prototype`,`otdel`  FROM oldbk.`comission_indexes` ci  WHERE id_city='{$komok_id}'	AND ( `otdel` in (".$sql.") ".$fixq."  )  GROUP BY `prototype` ORDER by `cost` ASC");
	}
	
 	$razdel=array(1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun", 61=>'eda');
	
	while($row = mysql_fetch_array($data)) {
	
		if (strpos($row[1], '%') !== false) {
			// ниче не делаем
			$item_name=$row[1];
		} else {
			$item_name1=str_replace("+1","",$row[1]);
			$item_name1=str_replace("+2","",$item_name1);
			$item_name1=str_replace("+3","",$item_name1);
			$item_name1=str_replace("+4","",$item_name1);
			$item_name1=str_replace("+5","",$item_name1);
			$item_name=str_replace(" (мф)","",$item_name1);
			// fix names
			$item_name= preg_replace('#\[(.*?)\]#si', '', $item_name);
			$item_name= preg_replace('#\((.*?)\)#si', '', $item_name);
		}
	
		$ot=(int)$_GET[otdel];
		$item = mysql_fetch_array(mysql_query("SELECT count(`id`) as id, min(duration) as min_duration, min(max_dur) as min_maxdur, max(duration) as max_duration,
		max(max_dur) as max_maxdur, min(cost) as min_setsale, max(cost) as max_setsale
		FROM oldbk.`comission_indexes` ci  WHERE  id_city='{$komok_id}' AND prototype = '".$row['prototype']."' AND (ci.otdel='".$ot."' ".$fixq."  ) ;"));
	
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
		?>
		<TR bgcolor=<?=$color?>>
		<TD align=center style='width: 150px;'><IMG SRC="http://i.oldbk.com/i/sh/<?=$row['img']?>" ALT="" ><BR><A HREF="?&max=<?=$row['prototype']?>&otdel=<?=$_GET['otdel']?>">подробнее</A></TD>
		<?
		$ehtml=str_replace('.gif','',$row['img']);
			
		?>
	
		<TD style="vertical-align:top;"><a target="_blank" href="http://oldbk.com/encicl/<?=link_for_item($row,true);?>.html"><?=$item_name?></a>
		<IMG SRC="http://i.oldbk.com/i/align_<?=$row['nalign']?>.gif" WIDTH="12" HEIGHT="15" ALT="">  (Масса: <?=$row['massa']?>) <BR>
		<b>Цена: <?=round($item['min_setsale'],2)?> - <?=round($item['max_setsale'],2)?> кр.</b> <small>(количество: <?=$item['id']?>)</small><BR>
	
		Долговечность: <?=$item['min_duration']?>-<?=$item['min_maxdur']?>/<?=$item['max_duration']?>-<?=$item['max_maxdur']?></FONT><BR>
	
		</TD>
		</TR>
		<?
	}
}

?>
	</TR></TABLE>

                <td style="vertical-align:top;">
                    <table id="filter" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td class="center">
                                <strong>Масса всех ваших вещей:

				<?php
					echo $d[0];?>/<?=get_meshok()?>
				</strong><br>
                                <strong>У Вас в наличии: <span class="money"><?=$user['money']?></span> кр.</strong><br>
				<?php if ($clan_kazna) { echo '<strong>В казне: <span class="money">'.round($clan_kazna['kr'],2).'</span> кр.</strong><br>'; } ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="hint-block size11 center">
                                <span style="color: red">
    					Получить дополнительные кредиты возможно обменяв еврокреды на кредиты в Банке.<br />
					Еврокредиты можно приобрести у любого дилера либо купить в Банке за WMZ.
                                </span>
                            </td>
                        </tr>
			<tr><td align="center">
				<form style="margin:0px;padding:0px;" method="post">
				<? if ($user['align'] != 4) { ?><a class="button-big btn" href="?sale=1">Продать вещи</a><br /> <? } ?>
				<a class="button-sbig btn" href="?unsale=1">Забрать вещи из магазина</a>
				</form>
			</tr></td>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="filter-title">Оружие</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
					<A HREF="?otdel=1&tmp=<?echo mt_rand(1111111,9999999);?>">Кастеты, ножи</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=11&tmp=<?echo mt_rand(1111111,9999999);?>">Топоры</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=12&tmp=<?echo mt_rand(1111111,9999999);?>">Дубины, булавы</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=13&tmp=<?echo mt_rand(1111111,9999999);?>">Мечи</A>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td class="filter-title">Обмундирование</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
					<A HREF="?otdel=2&tmp=<?echo mt_rand(1111111,9999999);?>">Сапоги</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=21&tmp=<?echo mt_rand(1111111,9999999);?>">Перчатки</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=22&tmp=<?echo mt_rand(1111111,9999999);?>">Легкая броня</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=23&tmp=<?echo mt_rand(1111111,9999999);?>">Тяжелая броня</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=24&tmp=<?echo mt_rand(1111111,9999999);?>">Шлемы</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=3&tmp=<?echo mt_rand(1111111,9999999);?>">Щиты</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=4&tmp=<?echo mt_rand(1111111,9999999);?>">Cерьги</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=41&tmp=<?echo mt_rand(1111111,9999999);?>">Ожерелья</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=42&tmp=<?echo mt_rand(1111111,9999999);?>">Кольца</A>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td class="filter-title">Заклинания</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
					<A HREF="?otdel=5&tmp=<?echo mt_rand(1111111,9999999);?>">Нейтральные</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=51&tmp=<?echo mt_rand(1111111,9999999);?>">Боевые и защитные</A>
                                    </li>

                                    <li>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                         	 <td class="filter-title">Производство</td>
			</tr>
                        <tr>                            
				<td class="filter-item">
                                <ul>
				    <li>
					<A HREF="?otdel=62&tmp=<?echo mt_rand(1111111,9999999);?>">Ресурсы</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=63&tmp=<?echo mt_rand(1111111,9999999);?>">Инструменты</A>
                                    </li>
	                             </ul>
                            </td>
                        </tr>


                        <tr>
                            <td class="filter-title">Прочее</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
					<A HREF="?otdel=6&tmp=<?echo mt_rand(1111111,9999999);?>">Амуниция</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=61&tmp=<?echo mt_rand(1111111,9999999);?>">Еда</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=52&tmp=<?echo mt_rand(1111111,9999999);?>">Прочее</A>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td class="filter-title">Сувениры</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
					<A HREF="?otdel=7&tmp=<?echo mt_rand(1111111,9999999);?>">Открытки</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=73&tmp=<?echo mt_rand(1111111,9999999);?>">Подарки</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=72&tmp=<?echo mt_rand(1111111,9999999);?>">Уникальные подарки</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=71&tmp=<?echo mt_rand(1111111,9999999);?>">Другие подарки</A>
                                    </li>

                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?
make_quest_div();
include "end_files.php";
?>
<!--Rating@Mail.ru counter-->
<div align=left><script language="javascript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
<script language="javascript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled();js=11;//--></script>
<script language="javascript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height;
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
<script language="javascript1.3" type="text/javascript"><!--
js=13;//--></script><script language="javascript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_top">'+
'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
<noscript><a target="_top" href="http://top.mail.ru/jump?from=1765367">
<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
<script language="javascript" type="text/javascript"><!--
if(11<js)d.write('--'+'>');//--></script></div>
<!--// Rating@Mail.ru counter-->
</div>
<div id="hint3" class="ahint"></div>
</BODY>
</HTML>
<?

if (isset($miniBB_gzipper_encoding)) {
	$miniBB_gzipper_in = ob_get_contents();
	$miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	$miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
	$miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
	$gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
	$percent = round($gzpercent);
	$miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	ob_clean();
	header('Content-Encoding: '.$miniBB_gzipper_encoding);
	echo $miniBB_gzipper_out;
}
?>