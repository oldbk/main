<?
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
require_once('config_ko.php');



if ($user['room'] != 48) { header("Location: main.php");  die(); }
if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
if ($_SESSION['boxisopen']!='open') { header('location: main.php?edit=1'); die(); }


$d[0] = getmymassa($user);

if(!$_SESSION['beginer_quest']['none']) {
	$last_q=check_last_quest(5);
	if($last_q) {
		quest_check_type_5($last_q);
	}

	$last_q=check_last_quest(2);
	if($last_q) {
		quest_check_type_2($last_q);
	}
}


$showsell = true;


$sellquery = "SELECT inventory.id AS invid, inventory.name AS invname, cshop.*, inventory.* FROM oldbk.`inventory` LEFT JOIN cshop ON inventory.prototype = cshop.id WHERE inventory.`owner` = '{$user['id']}' AND inventory.`dressed` = 0 AND inventory.type!=200 and inventory.type!=77 AND (inventory.sowner = 0 or inventory.sowner = ".$user['id'].") and inventory.duration = 0
	AND inventory.prokat_idp = 0 AND inventory.arsenal_klan='' AND inventory.setsale=0 AND inventory.present!='Арендная лавка'
	and getfrom = 43 AND bs_owner = 0 AND cshop.id is not null AND ((cshop.maxdur = inventory.maxdur AND inventory.duration = 0) OR (inventory.type = 12 AND inventory.magic > 0) or inventory.type = 50) AND (inventory.dategoden > ".(time()+30*24*3600).' OR inventory.dategoden = 0) and inventory.repcost > 0 ';


if (isset($_GET['set']) || isset($_POST['set'])) {
	if (isset($_GET['set'])) { $set = $_GET['set']; }
	if (isset($_POST['set'])) { $set = $_POST['set']; }
	if (!isset($_POST['count']) || $_POST['count'] == 0 || $_POST['count'] < 0) {
		$count = 1;
	} else {
		$count = intval($_POST['count']);
	}

	$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`cshop` WHERE `id` = '{$set}' LIMIT 1;"));
       	$base_price=$dress['cost'];
       	$base_rep_price=$dress['repcost'];

        $item_dategoden=$dress['dategoden'];

      	if ( ((time()>$KO_start_time24) and (time()<$KO_fin_time24))) {
		if (strpos($dress['name'], 'Восстановление') !== false) {
			$dress['repcost']=round($dress['repcost']*0.8,2);
		}
	}

	if ( ((time()>$KO_start_time41) and (time()<$KO_fin_time41))) {
		if (in_array($dress['id'],$CSHOP_ITEMS_41)) {
			$dress['repcost']=round($dress['repcost']*(1-($CSHOP_RATE_41*0.01)),2);
		}
	}


	if (($dress['massa'] * $count + $d[0]) > (get_meshok())) {
		$typet = "e";
		$msg='Недостаточно места в рюкзаке!';
	} elseif(($user['repmoney'] >= ($dress['repcost'] * $count)) && ($dress[GetShopCount()] >= $count)) {
        	mysql_query("UPDATE oldbk.`cshop` SET `".GetShopCount()."`=`".GetShopCount()."`-{$count} WHERE `id` = '{$set}'  AND `".GetShopCount()."` >= {$count} ");
            	if(mysql_affected_rows()>0) {

			mysql_query('INSERT INTO shop_stats (owner,shoptype,shopprototype,shopcount,lastupdate)
					VALUES ('.$user['id'].',4,'.$set.','.$count.','.time().')
					ON DUPLICATE KEY UPDATE
						`shopcount` = `shopcount` + '.$count.', lastupdate = '.time()
			);

             		if ($dress['id'] != 100028)  {
				$dress['up_level'] = $dress['nlevel'];
			} else {
				$dress['up_level']=0;
			}

			if ($dress['id'] == 5277) {
				$dress['razdel']=42;
			} elseif ($dress['id'] == 5278) {
				$dress['razdel']=42;
			} elseif ($dress['id'] == 121121122) {
				$dress['razdel']=42;
			} elseif ($dress['id'] == 121121123) {
				$dress['razdel']=42;
			} elseif ($dress['id'] == 121121124) {
				$dress['razdel']=42;
			} elseif ($dress['id'] == 18210) {
				$dress['razdel']=11;
			} elseif ($dress['id'] == 18229) {
				$dress['razdel']=12;
			} elseif ($dress['id'] == 18247) {
				$dress['razdel']=13;
			} elseif ($dress['id'] == 18527) {
				$dress['razdel']=13;
			} elseif (($dress['id']>=222222230 and $dress['id']<=222222235) or ($dress['id']>=222222242 and $dress['id']<=222222255)) {
				$dress['razdel']=42;
			} elseif($dress['id'] == 2003) {
				$dress['razdel']=42;
			} elseif($dress['id'] == 2000) {
	            		$dress['razdel']=11;
			} elseif($dress['id'] == 2001) {
				$dress['razdel']=13;
			} elseif($dress['id'] == 2002) {
				$dress['razdel']=12;
			} elseif($dress['id'] == 260) {
				$dress['razdel']=23;
			} elseif($dress['id'] == 262) {
				$dress['razdel']=23;
			} elseif($dress['id'] == 283) {
	            		$dress['razdel']=23;
			} elseif($dress['id'] == 284) {
	            		$dress['razdel']=12;
			}



			$str =", `present` ";
			$sql =", 'Мироздатель' ";


			if($user['repmoney'] < ($dress['repcost'] * $count)) {
				$msg = "Недостаточно репутации покупки.";
				$typet = "e";
			} else {
				$result = 0;
				$insert_id=array();

				if($dress['type']==30) {
					$str .= ",`add_time`";
					$sql .= ",'".$runs_exp_table[0]['next']."'";
					$dress['is_owner']=1;
					$dress['up_level']=0;
				}
			        $dress['notsell'] = 1;

				$goden_sql=" '".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'  ";
				if ($item_dategoden>0)
							{
							$goden_do = $item_dategoden;
							$goden = floor(($goden_do-time())/(24*3600)); if ($goden<1) {$goden=1;}
							$goden_sql=" '{$goden_do}' , '{$goden}' ";
							}

				for($i = 1; $i <= $count; $i++) {

					if(mysql_query("INSERT INTO oldbk.`inventory`
					(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `repcost`, `img`, `img_big`,`maxdur`,`isrep`,`nclass`,`letter`,`rareitem`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,
						`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,
						`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,
						`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group` ,`gmp`,`gmeshok`,`ecost`,`mfbonus`,`sowner`,`up_level`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`getfrom`,`notsell`,`craftspeedup`,`craftbonus` ".$str."
					)
					VALUES
					('{$dress['id']}','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$base_price}, {$base_rep_price},'{$dress['img']}','{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}', '{$dress['letter']}', '{$dress['rareitem']}',
					'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}',
					'{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}',
					'{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}',
					'{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}',
					'{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}',
					'{$dress['ngray']}','{$dress['ndark']}', '{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}',
					'{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}',
					'{$dress['nlevel']}','{$dress['nalign']}',".$goden_sql.",'{$dress['razdel']}','{$dress['group']}' ,
					'{$dress['gmp']}','{$dress['gmeshok']}','{$dress['ecost']}','{$dress['mfbonus']}','".($dress[is_owner]==1?$user[id]:0)."','{$dress['up_level']}','{$user[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','43','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}' ".$sql."
					) ;"))
					{
						$result = 1;
						$insert_id[$k]=mysql_insert_id();
					}

				}

				if($result==1) {
					$msg = "Вы купили {$count} шт. \"{$dress['name']}\".";

					$take_rep_query = " `repmoney` = `repmoney`- '".($count * $dress['repcost'])."'";
					mysql_query("UPDATE `users` set ".$take_rep_query." WHERE id = {$_SESSION['uid']} ;");

					$invdb = mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` in (".implode(',',$insert_id).")");
					if ($count == 1) {
						$dressinv = mysql_fetch_array($invdb);
						$dressid = get_item_fid($dressinv);
						$dresscount=" ";
					} else {
						$dressid="";
						while ($dressinv = mysql_fetch_array($invdb)) {
							$dressid .= get_item_fid($dressinv).",";
						}
						$dresscount="(x".$_POST['count'].") ";
					}

					$allcostr = $count * $dress['repcost'];

                    			$dressid = get_item_fid($dressinv); //принудительно указываем
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['owner_rep_do']=$user['repmoney'];
					$rec['owner_rep_posle']=$user['repmoney'] - $allcostr;
					$rec['target']=0;
					$rec['target_login']='храм.лавка.';
					$rec['type']=172;//покупка из госа
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['sum_rep']=$allcostr;
					$rec['item_id']=$dressid;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=$count;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_proto']=$dress['id'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					add_to_new_delo($rec);

					$user['repmoney'] -= $count * $dress['repcost'];
				}                                                                                                                                                        //".$allcost." кр. и
			}
		} else {
			$msg = "Недостаточно репутации или нет вещей в наличии.";
			$typet = "e";
		}
	} else 	{
		$msg = "Недостаточно репутации или нет вещей в наличии.";
		$typet = "e";
	}
} else if (($_GET['do']=='up') AND ($_POST['target'] != '') AND ($_GET['up'] != '')) {
	//1. проверка данных о "свитке"
	$upid=(int)($_GET['up']);
	$sitem=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`cshop` WHERE id='{$upid}' and  id in (6219,6220,6221,6321,6322,6323,6324) ")); //6218,6323
	if ($sitem['id']>0) {
		//2. проверяем хватит ли у меня репы для операции
		if ($user['repmoney']>=$sitem['repcost']) {
			//4. проверим чтоб это был арт а не любой предмет
			$_POST['target']=(int)($_POST['target']);
			$artitem = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='{$_POST['target']}' AND owner = '{$user['id']}' AND type!=30 AND prototype not in (1006233,1006232,1006234)  AND `dressed`= 0 AND bs_owner = '".$user['in_tower']."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;"));

			if ($artitem['id']>0) {
				//5. вытягиваем файл магии
				$mag=mysql_fetch_array(mysql_query("select * from oldbk.magic where id='{$sitem['magic']}'"));
				if (($mag['id']>0) AND ($mag['file']!='')) {
					$sbet = 0;
					ob_start();
					include("./magic/".$mag['file']);
					$msg = strip_tags(ob_get_contents());
					ob_clean ();

					if ($bet == 1 && $sbet == 1) {
						mysql_query("UPDATE `users` set `repmoney` = `repmoney`- '{$sitem['repcost']}'  WHERE id = {$user['id']} ;");

						$rec['owner']=$user['id'];
						$rec['owner_login']=$user['login'];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money'];
						$rec['owner_rep_do']=$user['repmoney'];
						$rec['owner_rep_posle']=$user['repmoney'] - $sitem['repcost'];
						$rec['target']=0;
						$rec['target_login']='храм.лавка.';
						$rec['type']=386;// апдейт арта в лавке
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['sum_rep']=$sitem['repcost'];
						$rec['item_id']=get_item_fid($artitem);
						$rec['item_name']=$artitem['name'];
						$rec['item_count']=1;
						$rec['item_type']=$artitem['type'];
						$rec['item_cost']=$artitem['cost'];
						$rec['item_proto']=$artitem['prototype'];
						$rec['item_dur']=$artitem['duration'];
						$rec['item_maxdur']=$artitem['maxdur'];
						$rec['item_ups']=$artitem['ups'];
						$rec['item_unic']=$artitem['unik'];
						$rec['item_incmagic']=$artitem['includemagicname'];
						$rec['item_incmagic_count']=$artitem['includemagicuses'];
						$rec['item_arsenal']=$artitem['arsenal_klan'];
						$rec['add_info']=$sitem['name'];
						add_to_new_delo($rec);
						$user['repmoney'] -=$sitem['repcost'];
					} else {
						$typet = "e";
					}
				} else {
					$msg = "Ошибка магии!";
					$typet = "e";
				}
			} else {
				$msg = "У Вас нет такого артефакта!";
				$typet = "e";
			}
		} else {
			$msg = "У Вас недостаточно репутации покупки для совершения операции!";
			$typet = "e";
		}
	} else {
		$msg = "Ошибка выбора!";
		$typet = "e";
	}
} else if (($_GET['do']=='down') AND ($_POST['target']!='') AND ($_GET['down']!='')) {
	//1. проверка данных о "свитке"
	$downid=(int)($_GET['down']);
	$sitem=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`cshop` WHERE id='{$downid}' and  id in (7217,7218,7219,7220,7221,7321,7322) "));
	if ($sitem['id']>0) {
		//2. проверяем хватит ли у меня репы для операции
		if ($user['repmoney']>=$sitem['repcost']) {
			//4. проверим чтоб это был арт а не любой предмет
			$_POST['target']=(int)($_POST['target']);
			$artitem = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='{$_POST['target']}' AND owner = '{$user['id']}' AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user['in_tower']."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;"));

			if ($artitem['id']>0) {
				//5. вытягиваем файл магии
				$mag=mysql_fetch_array(mysql_query("select * from oldbk.magic where id='{$sitem['magic']}'"));
				if (($mag['id']>0) AND ($mag['file']!='')) {
					$sbet = 0;
					ob_start();
					include("./magic/".$mag['file']);
					$msg = strip_tags(ob_get_contents());
					ob_clean ();

					if ($bet==1 && $sbet == 1) {
						// все ок снимаем репу пишем вдело
						mysql_query("UPDATE `users` set `repmoney` = `repmoney`- '{$sitem['repcost']}'  WHERE id = {$user['id']} ;");

						$rec['owner']=$user['id'];
						$rec['owner_login']=$user['login'];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money'];
						$rec['owner_rep_do']=$user['repmoney'];
						$rec['owner_rep_posle']=$user['repmoney'] - $sitem['repcost'];
						$rec['target']=0;
						$rec['target_login']='храм.лавка.';
						$rec['type']=387;// деап арта в лавке
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['sum_rep']=$sitem['repcost'];
						$rec['item_id']=get_item_fid($artitem);
						$rec['item_name']=$artitem['name'];
						$rec['item_count']=1;
						$rec['item_type']=$artitem['type'];
						$rec['item_cost']=$artitem['cost'];
						$rec['item_proto']=$artitem['prototype'];
						$rec['item_dur']=$artitem['duration'];
						$rec['item_maxdur']=$artitem['maxdur'];
						$rec['item_ups']=$artitem['ups'];
						$rec['item_unic']=$artitem['unik'];
						$rec['item_incmagic']=$artitem['includemagicname'];
						$rec['item_incmagic_count']=$artitem['includemagicuses'];
						$rec['item_arsenal']=$artitem['arsenal_klan'];
						$rec['add_info']=$sitem['name'];
						add_to_new_delo($rec);
						$user['repmoney'] -=$sitem['repcost'];
					} else {
						$typet = "e";
					}
				} else {
					$msg = "Ошибка магии!";
					$typet = "e";
				}
			} else {
				$msg = "У Вас нет такого артефакта!";
				$typet = "e";
			}
		} else {
			$msg = "У Вас недостаточно репутации покупки для совершения операции!";
			$type = "e";
		}
	} else {
		$msg = "Ошибка выбора!";
		$typet = "e";
	}
} elseif (isset($_GET['sellitems'],$_GET['id']) && $showsell === true) {
	$data = mysql_query($sellquery.' and inventory.id = '.intval($_GET['id']));
	if (mysql_num_rows($data)) {
		$row = mysql_fetch_assoc($data);
		$row['id'] = $row['invid'];
		$row['name'] = $row['invname'];
		if($row['add_pick'] != '') {
			undress_img($row);
		}

		$price = $row['repcost']*0.5;

		if ($row['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
			$add_money = floor($price-$row['duration']*($price/$row['maxdur']));
		} else {
			$add_money = floor($price);
		}

		$price = $add_money;


		mysql_query("UPDATE `users` set `repmoney` = `repmoney`+ '".$price."' WHERE id = ".$user['id']." LIMIT 1");
		mysql_query("DELETE FROM oldbk.`inventory` WHERE owner = ".$user['id']." and id = ".$row['invid']." LIMIT 1");

		$rec['owner']=$user['id'];
		$rec['owner_login']=$user['login'];
		$rec['owner_balans_do']=$user['money'];
		$rec['owner_balans_posle']=$user['money'];
		$rec['target']=0;
		$rec['target_login']='храм.лавка.';
		$rec['type']=588;
		$rec['sum_kr']=0;
		$rec['sum_ekr']=0;
		$rec['sum_kom']=0;
		$rec['sum_rep']=$price;
		$rec['owner_rep_do']=$user['repmoney'];
		$rec['owner_rep_posle']=$user['repmoney'] + $price;
		$rec['item_id']=get_item_fid($row);
		$rec['item_name']=$row['name'];
		$rec['item_count']=1;
		$rec['item_type']=$row['type'];
		$rec['item_cost']=$row['cost'];
		$rec['item_dur']=$row['duration'];
		$rec['item_maxdur']=$row['maxdur'];
		$rec['item_ups']=0;
		$rec['item_unic']=0;
		$rec['item_incmagic']='';
		$rec['item_incmagic_count']='';
		$rec['item_arsenal']='';
		add_to_new_delo($rec);

		$msg = "Вы продали \"{$row['name']}\" 1 шт. и получили ".$price." репутации.";
		$user['repmoney'] += $price;
	} else {
		$msg = "Товар не найден";
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
#hint3 a {
	color: #003585;
	font-weight:bold;
	text-decoration:none;
}

#hint3 {
    font-family: Tahoma;
    font-size: 13px;
}


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

.noty_message { padding: 5px !important;}

#page-wrapper table td {
	vertical-align:middle;
}

#itemcontainer table td {
	color:black;
}

#itemcontainer table td a {
	font-weight:bold;
}


</style>
<SCRIPT LANGUAGE="JavaScript">

function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}

function AddCount(event,name,txt, razdel, maxlen) {
	var el = document.getElementById("hint3");
	var sale_txt= 'Купить несколько штук';

	el.innerHTML = '<form action="?otdel='+razdel+'" method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B style="font-size:11pt;">'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();return false;"><B style="font-size:11pt;">x</B></TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B style="font-size:11pt;"><I>'+txt+'</td></tr><tr><td width=80% align=right style="font-size:11pt;">'+
	'Кол-во (макс '+maxlen+' шт.) <INPUT TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" style="height:16px;margin-top:2px;" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';

	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	y = event.pageY;
	el.style.top = (y -120) + 'px';
	document.getElementById("count").focus();
}

function closehint3() {
	document.getElementById("hint3").style.visibility="hidden";
}

function showitemschoice(title, type, script) {
	var choicehtml = "<form style='display:none' id='formtarget' action='" + script + "' method=POST><input type='hidden' id='target' name='target'>";
	choicehtml += "</form><table width='100%' cellspacing='1' cellpadding='0' bgcolor='CCC3AA'>";
	choicehtml += "<tr><td align='center' style='font-size:13px;'><B>" + title + "</td>";
	choicehtml += "<td width='20' align='right' valign='top' style='cursor: pointer' onclick='closehint3(true);return false;'>";
	choicehtml += "<big><b>x</td></tr><tr><td colspan='2'><div id='itemcontainer' style='width:100%;font-size:13px;'>";
	choicehtml += "</div></td></tr></table>";

	var el = document.getElementById("hint3");
	el.innerHTML = choicehtml;
	el.style.width = 600 + 'px';
	el.style.visibility = "visible";
	el.style.left = 300 + 'px';
	el.style.top = 50 + 'px';
	Hint3Name = "target";

	getchoice(type);
}

function getchoice(type) {
	$.get("itemschoice.php?"+type+"&get=1", function(data) {
		$('#itemcontainer').html(data);
	});
}


function selecttarget(scrollid) {
	var targertinput = document.getElementById('target');
	targertinput.value = scrollid;

	var targetform = document.getElementById('formtarget');
	targetform.submit();
}

function getformdata(id,param,event) {
	if (window.event) {
		event = window.event;
	}
	if (event) {
		$.get('payform.php?id='+id+'&param='+param+'', function(data) {
				$('#pl').html(data);
				$('#pl').show(200, function() {

				});
		});

		$('#pl').css({ position:'absolute',left: (($(window).width()-$('#pl').outerWidth())/2)+200, top: '200px'  });

	}
}

function closeinfo() {
  	$('#pl').hide(200);
}

</SCRIPT>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
</head>


<body id="arenda-body">
<div id="pl" style="z-index: 300; position: absolute; left: 50%; top: 120px;
				width: 750px; height:365px; background-color: #eeeeee;
				margin-left: -375px;
				border: 1px solid black; display: none;"></div>
<script type='text/javascript'>
RecoverScroll.start();
</script>
<?php
   	make_quest_div();
?>

<div id="page-wrapper">
    <div class="title">
        <div class="h3">Храмовая Лавка</div>
        <div id="buttons">
            <a class="button-dark-mid btn" onclick="window.open('help/cshop.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes'); return false;" title="Подсказка">Подсказка</a>
            <a class="button-mid btn" OnClick="location.href='?tmp='+Math.random(); return false;" title="Обновить">Обновить</a>
            <a class="button-mid btn" OnClick="document.getElementById('cityform').submit(); return false;" title="Вернуться">Вернуться</a>
	    <FORM action="city.php" style="margin:0px;padding:0px;display:block;" id="cityform" method="GET"><INPUT TYPE="hidden" value="zp" name="zp"></form>
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
		    <?php
			$lasttime = 30*24*3600; // за последний месяц
			$q = mysql_query('SELECT cshop.* FROM shop_stats LEFT JOIN cshop ON shopprototype = cshop.id WHERE shop_stats.owner = '.$user['id'].' and shoptype = 4 and lastupdate > '.(time()-$lasttime).' ORDER BY shopcount DESC, lastupdate DESC LIMIT 12');
			if (mysql_num_rows($q) > 0) {
		    ?>

                    <table class="table border" style="margin-bottom: 0;" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title">Мои популярные покупки</div>
				<div class="head-right"></div>
			    </th>
			</tr>
			<tr class="even2">
			<td>
				<ul id="favorite">
					<?php while($row = mysqL_fetch_assoc($q)) {
					      	if ( ((time()>$KO_start_time24) and (time()<$KO_fin_time24))) {
							if (strpos($row['name'], 'Восстановление') !== false) {
								$row['repcost']=round($row['repcost']*0.8,2);
								$row['shopicon'] = 20;
							}
						}

						if ( ((time()>$KO_start_time41) and (time()<$KO_fin_time41))) {
							if (in_array($row['id'],$CSHOP_ITEMS_41)) {
								$row['repcost']=round($row['repcost']*(1-($CSHOP_RATE_41*0.01)),2);
								$row['shopicon']=$CSHOP_RATE_41;
							}
						}


						if (!empty($row['img_big'])) {
							$row['img'] = $row['img_big'];
						} ?>
						<li style="display:inline-block;">
							<div class="img-block" style="text-align:center;">
								<img alt="<?=$row['name']?>" title="<?=$row['name']?>" src="http://i.oldbk.com/i/sh/<?= $row['img']; ?>">
							</div>
							<div class="btn-block" style="text-align:center;">
								<a href="?otdel=<?=$row['razdel']; ?>&set=<?= $row['id'] ?>">купить</a>
								<img src="http://i.oldbk.com/i/up.gif" width="11" height="11" border="0" alt="Купить несколько штук" style="cursor: pointer" onclick="AddCount(event,'<?= $row['id']?>', '<?= $row['name']?>',<?=$row['razdel'];?>,'<? echo floor($user['repmoney'] / $row['repcost']);?>'); return false;">
							</div>
						</li>
					<?php } ?>
				</ul>
			</td></tr>
		    </table>

		    <br>
                    <?php } ?>


                    <table class="table border" style="margin-bottom: 0;" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="400px">
                            <col>
                        </colgroup>
                        <thead>
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title">
						<b>Отдел &quot;<?
							$ku=intval(isset($_GET['otdel']) ? $_GET['otdel'] : 0);

							$nazv['up']="Повысить уровень артефакта";
							$nazv['down']="Понизить уровень артефакта";

							$nazv[6]="Амуниция";
							$nazv[61]="Еда";
							$nazv[62]="Ресурсы";
							$nazv[63]="Инструменты";
							$nazv[5]="Заклинания: нейтральные";
							$nazv[51]="Заклинания: боевые и защитные";
							$nazv[52]="Руны";
							$nazv[82]="Хеллоуин";
							$nazv[60]="Молитвенные предметы";
							$nazv[78]="Прочее";
							$nazv['sellitems'] = "Вернуть вещи";

							if (isset($_GET['do']) && $_GET['do'] =='up') {
								echo $nazv['up'];
							} elseif (isset($_GET['do']) && $_GET['do'] == 'down') {
								echo $nazv['down'];
							} elseif (isset($_GET['sellitems'])) {
								echo $nazv['sellitems'];
							} elseif ($ku == 0) {
								echo $nazv[6];$_GET['otdel']=6;
							} else {
								echo $nazv[$ku];
							}
?>&quot;
				</div>
                            </th>
                            <th class="filter">
			    	<form method="POST" id="fall" style="margin:0px;padding:0px;display:block;">
                                <div class="head-title" style="top:1px;">
                                    <select name="fall" style="width:250px;height:14px;margin:5px;top:0px;" OnChange="document.getElementById('fall').submit();">
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

	if ($_GET['do']=='up') 	{
		//витрина апа
		$data = mysql_query("SELECT * FROM oldbk.`cshop` WHERE  id in (6219,6220,6221,6321,6322,6323,6324) ORDER by `id`, cost ASC"); //6218,6323 -13й ап
		while($row = mysql_fetch_array($data))
		{
		if ($i == 0) { $i = 1; $color = '#C7C7C7';}
		else { $i = 0; $color = '#D5D5D5'; }
		echo "<tr bgcolor='{$color}'><td align='center' style='width:150px'><img src=\"http://i.oldbk.com/i/sh/{$row['img']}\" border='0'/>";

		$lv=str_replace('.gif','',$row['img']);
		$lv=str_replace('UP','',$lv);
		$lv=str_replace('_e','',$lv);
		$lv=(int)($lv);

		echo "<br/><a href=\"#\" onclick=\""."showitemschoice('Выберите артефакт для улучшения', 'upgradeart_".$lv."', '?do=up&up=".$row['id']."');return false;"."\">Использовать</a>";
		echo "</TD>";
	        $priv=($row[is_owner]==1?1:0);
		echo "<td valign='top'>";

		$ht=str_replace('.gif','.html',$row['img']);

		echo "<a href=http://oldbk.com/encicl/mag1/{$ht} target=_blank>{$row['name']}</a><BR>";
		echo "<b>Стоимость:{$row['repcost']} реп.</b><BR>";
		echo '<font color=maroon>Описание:</font> При использовании позволяет повысить уровень Храмового артефакта до '.$lv.'-го уровня <BR></td></TR>';

		}
	} elseif ($_GET['do']=='down') {
		//витрина ДЕапа
		$data = mysql_query("SELECT * FROM oldbk.`cshop` WHERE  id in (7321,7322,7221,7220,7219,7218,7217) ORDER by `id` DESC, cost DESC");
		while($row = mysql_fetch_array($data)) {
			if ($i == 0) { $i = 1; $color = '#C7C7C7';}
			else { $i = 0; $color = '#D5D5D5'; }
			echo "<tr bgcolor='{$color}'><td align='center' style='width:150px'><img src=\"http://i.oldbk.com/i/sh/{$row['img']}\" border='0'/>";

			$lv=str_replace('.gif','',$row['img']);
			$lv=str_replace('DUP','',$lv);
			$lv=str_replace('_e','',$lv);
			$lv=(int)($lv);

			echo "<br/><a href=\"#\" onclick=\""."showitemschoice('Выберите артефакт для понижения уровня', 'downgradeart_".$lv."', '?do=down&down=".$row['id']."');return false;"."\">Использовать</a>";
			echo "</TD>";
		        $priv=($row[is_owner]==1?1:0);
			echo "<td valign='top'>";

			$ht=str_replace('.gif','.html',$row['img']);

			echo "<a href=http://oldbk.com/encicl/mag1/{$ht} target=_blank>{$row['name']}</a><BR>";
			echo "<b>Стоимость:{$row['repcost']} реп.</b><BR>";
			echo '<font color=maroon>Описание:</font> При использовании позволяет понизить уровень Храмового артефакта с '.($lv+1).' до '.$lv.'-го уровня <BR></td></TR>';

		}
	} elseif (isset($_GET['sellitems']) && $showsell === true) {
		$data = mysql_query($sellquery.' ORDER by inventory.update DESC');
		if (mysql_num_rows($data)) {
			$z = 0;
			while(($row = mysql_fetch_assoc($data))) {
				if ($row['img_big']!='') { $row['img']=$row['img_big']; }
				$row['name'] = $row['invname'];
				$row['id'] = $row['invid'];
				if ($i == 0) { $i = 1; $color = '#C7C7C7';}
				else { $i = 0; $color = '#D5D5D5'; }
				$row[GetShopCount()] = 1;
				echo "<tr bgcolor='{$color}'><td align='center' style='width:150px;";
				echo "vertical-align:middle;'>";
				echo "<img src=\"http://i.oldbk.com/i/sh/{$row['img']}\" border='0'/>";
				echo '<center><small>('.get_item_fid($row).')</small></center>';

				$price = $row['repcost']*0.5;
				if ($row['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
					$add_money = floor($price-$row['duration']*($price/$row['maxdur']));
				} else {
					$add_money = floor($price);
				}

				?>
				<BR><A HREF="?sellitems&id=<?=$row['invid']?>">Вернуть за <?=$add_money;?> репутации</A></TD>
				<?php
				echo "<td valign='top'>";
				showitem ($row);
				echo "</td></tr>";
			}
		} else {
			echo '<br><center><b>У вас нет подходящих товаров из храмовой лавки. Вернуть можно только неиспользованные товары со сроком годности более 30-ти дней.</b></center>';
		}

	} else {
		//витрина магаза
		if ($_GET['otdel']==6) { $adda='OR `razdel`=22 '; } else { $adda=''; }
		$data = mysql_query("SELECT * FROM oldbk.`cshop` WHERE `".GetShopCount()."` > 0 AND (`razdel` = '{$_GET['otdel']}' ".$adda." )ORDER by `repcost`, cost ASC");

		$icon_count=0;
		while($row = mysql_fetch_array($data)) 	{
			if ($row['img_big']!='') { $row['img']=$row['img_big']; }
			$item_dategoden=$row['dategoden'];

			if ( ((time()>$KO_start_time24) and (time()<$KO_fin_time24))) {
				if (strpos($row['name'], 'Восстановление энергии') !== false || strpos($row['name'], 'свиток «Восстановление') !== false) {
					$row['repcost']=round($row['repcost']*0.8,2);
					$row['shopicon']=20;
				}
			}

			if ( ((time()>$KO_start_time41) and (time()<$KO_fin_time41))) {
				if (in_array($row['id'],$CSHOP_ITEMS_41)) {
					$row['repcost']=round($row['repcost']*(1-($CSHOP_RATE_41*0.01)),2);
					$row['shopicon']=$CSHOP_RATE_41;
				}
			}

			if ($item_dategoden>0)
				{
				$row['dategoden'] = $item_dategoden;
				$row['goden'] = floor(($item_dategoden-time())/(24*3600));
				}

			if ($i == 0) { $i = 1; $color = '#C7C7C7';}
			else { $i = 0; $color = '#D5D5D5'; }
			echo "<tr bgcolor='{$color}'><td align='center' style='width:150px;";


			if ($row['shopicon']>0)	{
				echo "vertical-align:top;'>";
				echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -55px;top: -11px;\" src='http://i.oldbk.com/i/icon_s_".(int)($row['shopicon']).".png'>";
				$icon_count++;
			} elseif ($banners[$row['shopbanner']]!='') {
				$banner=$banners[$row['shopbanner']];
				echo "vertical-align:top;'>";
				echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -55px;top: -11px;\" ".$banner.">";
				$icon_count++;
			} else {
				echo "vertical-align:middle;'>";
			}

			echo "<br><a name=\"{$row['id']}\"><img src=\"http://i.oldbk.com/i/sh/{$row['img']}\" border='0'/>";
			echo "<br/><a href=\"?otdel={$_GET['otdel']}&set={$row['id']}&rep=1\">купить за реп.</a>";
			?>
			<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Купить несколько штук" style="cursor: pointer" onclick="AddCount(event,'<?= $row['id']?>', '<?= $row['name']?>',<?=$row['razdel']?>,'<? echo floor($user['repmoney'] / $row['repcost']) ?>');return false;"></TD>
			<?
		        $priv=($row['is_owner']==1?1:0);
			echo "<td valign='top'>";
			$row['notsell'] = 1;
			$row['present']='Мироздатель';

			showitem ($row, 0, true,'','',$rep_rate,$priv);
			echo "</td></tr>";
		}
	}

?>
	</TR></TABLE>

                <td style="vertical-align:top;">
                    <table id="filter" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
                                <strong>Вес всех ваших вещей:
				<?php
					echo $d[0];?>/<?=get_meshok()?>
				</strong><br>
				Ваша репутация: <span class="money"><strong><?=$user['rep']?></strong></span><br>
				Репутация для покупок: <span class="money"><strong><?=$user['repmoney']?></strong></span>
				<br><center>
				<?php
			    	if (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) {
					echo '<br><a onclick="getformdata(9,300,event);" href="#"><img src=http://i.oldbk.com/i/bank/knopka_repa.gif title="Купить репутацию" alt="Купить репутацию" ></a></a>';
				} else {
					echo ' <a href="bank.php"><img src=http://i.oldbk.com/i/bank/knopka_repa.gif title="Купить репутацию" alt="Купить репутацию" ></a>';
				}
				?>
				</center>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="filter-title">Отделы магазина</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a HREF="?otdel=6&tmp=<?=mt_rand(1111,9999);?>">Амуниция</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=52&tmp=<?=mt_rand(1111,9999);?>">Руны</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=61&tmp=<?=mt_rand(1111,9999);?>">Еда</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=60&tmp=<?=mt_rand(1111,9999);?>">Молитвенные предметы</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=78&tmp=<?=mt_rand(1111,9999);?>">Прочее</a>
                                    </li>
																		<li>
                                        <a HREF="?otdel=82&tmp=<?=mt_rand(1111,9999);?>">Хеллоуин</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td class="filter-title">Отделы заклинаний</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a HREF="?otdel=5&tmp=<?=mt_rand(1111,9999);?>">Нейтральные</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=51&tmp=<?=mt_rand(1111,9999);?>">Боевые и защитные</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td class="filter-title">Услуги магазина</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a HREF="?do=up&tmp=<?=mt_rand(1111,9999);?>">Повысить уровень артефакта</a>
                                    </li>
                                    <li>
                                        <a HREF="?do=down&tmp=<?=mt_rand(1111,9999);?>">Понизить уровень артефакта</a>
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
                                        <a HREF="?otdel=62&tmp=<?=mt_rand(1111,9999);?>">Ресурсы</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=63&tmp=<?=mt_rand(1111,9999);?>">Инструменты</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
			<?php if ($showsell === true) { ?>
                        <tr>
                            <td class="filter-title">Вернуть товары</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a HREF="?sellitems=1&tmp=<?=mt_rand(1111,9999);?>">Вернуть вещи</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
			<?php } ?>
                       	</tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
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
<div style="width:300px;" id="hint3" class="ahint"></div>
</BODY>
</HTML>
