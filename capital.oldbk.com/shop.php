<?php
// для криворуких плагинов
if (isset($_REQUEST['rzd0']) || isset($_REQUEST['ssave'])) die();

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

$arr_pril_vel=array(100,300,500,2000,3000,4000,5000,6500,8000,10000,12000,15000,20000);
$r_excarray = array(222222230,222222231,222222232,222222233,222222234,222222235);

session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";

//if ($user[id]!=28453) { echo 'Временно закрыт&nbsp;&nbsp;<form action=city.php method=GET><INPUT TYPE="submit" value="Вернуться" name="cp"></form>'; die(); }

include "item_functions.php";
require_once('config_ko.php');

if (isset($_POST['fall'])) {
	$_SESSION['fall']=(int)$_POST['fall'];
} else {
	$_POST['fall']=(int)$_SESSION['fall'];
}

if (isset($_POST['fallclass'])) {
	$_SESSION['fallclass']=(int)$_POST['fallclass'];
} else {
	$_POST['fallclass']=(int)$_SESSION['fallclass'];
}


if (ADMIN) {
	ini_set('display_errors',1);
	error_reporting(E_ERROR);
}

include "clan_kazna.php";

if ($user['klan']!='') {
  	$clan_id=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user[klan]}' LIMIT 1;"));
	if ($clan_id['id'] > 0) {
	    	if ($clan_id['glava'] == $user['id']) {
			$clan_kazna = clan_kazna_have($clan_id[id]);
		}
	}
}

$d[0] = getmymassa($user);

$what_not_to_sell=' AND `prototype` not in (104,100000009,100000010,20000,1006232,1006233,1006234,510,550,599,946,947,948,949,950,951,952,953,954,955,956,957) and (`prototype` < 55510300 OR `prototype` > 55510400) ';
$resurs=' AND (`prototype`>3000 AND `prototype` <3022 ) ';
$notresurs=' AND NOT (`prototype`>3000 AND `prototype` <3022 ) ';

$kk_res=array(0=>'0.45',1=>'0.45',2=>'0.45',3=>'0.45',4=>'0.45',5=>'0.43',6=>'0.42',7=>'0.41',8=>'0.4',9=>'0.38',10=>'0.38',11=>'0.35',12=>'0.35',13=>'0.33',14=>'0.33',15=>'0.33');
while(list($k,$v) = each($kk_res)) {
	$kk_res[$k] += 0.14;
}

include "action_days_config.php";

if($shop_skupka == 1) {
	//проверяем привязку вещи при скупке. Если привязана, то стоимость подгонов не учитываем.
	$check_sowner=1;

	//условие скупки
   	//$row[type] < 12 && ($row[nlevel] <= 4 || $row[type]=5 || ($row[nlevel] <= 5 && $row[type] = 10))
}

$skupka_sql = '';

$msg = "";

if($user[klan]!='radminion') {
	if ($user['room'] != 22) { header("Location: main.php");  die(); }
	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($_SESSION['boxisopen'] != 'open') { header('location: main.php?edit=1'); die(); }
}


if (isset($_GET['showres'])) {
	$q = mysql_query('SELECT *,count(*) as ccount FROM oldbk.inventory USE INDEX (owner_5) WHERE owner = '.$user['id'].$resurs.' AND cost>0 AND setsale=0 and type!=200 and type!=77 AND prokat_idp = 0 and bs_owner = 0 AND arsenal_klan="" AND present!="Арендная лавка" and notsell = 0 group by prototype');
	if (mysqL_num_rows($q) > 0) {
		$out = "";
		$allprice = 0;
		$scrarr = '<script>var protoprices = new Array();';
		while($i = mysql_fetch_assoc($q)) {
			$out .= '<tr><td><input OnChange="changeresprice('.$i['prototype'].',this);" name="protocheck[]" value="'.$i['prototype'].'" checked type="checkbox"></td><td><img src="http://i.oldbk.com/i/sh/'.$i['img'].'" border="0"></td>';
			$out .= '<td>'.$i['name'].' x '.$i['ccount'].'</td>';

			$price = 0;

			if($i["prototype"] > 3000 && $i["prototype"] < 3022) {
				$price = round($i['cost']*0.5,2);
				$price+= round($price*$kk_res[$user['level']],2);
				$price = round($price*$i['ccount'],2);
			} else {
				$price = round($i['cost']*0.5,2);
				$price = round($price*$i['ccount'],2);
			}

			$scrarr .= 'protoprices['.$i['prototype'].'] = '.$price.';';
			$allprice += $price;

			$out .= '<td><b>'.$price.'</b> кр.</td>';
			$out .= '</tr>';
		}
		echo '<form method="POST" action="?sellfullres"><table width=100%>';
		echo '<tr><td colspan="4" align="center"><input id="sellresbutton" type="submit" value="Продать выбранные ресурсы за '.$allprice.' кр."></td></tr>';
		echo $out;
		echo '</table></form>';
		$scrarr .= 'ressellprice = '.$allprice.';</script>';
		echo $scrarr;
	} else {
		echo 'Ресурсы не найдены';
	}
	die();
}

if (isset($_GET['showcraft'])) {
	$q = mysql_query("SELECT inventory.*, skupka.stavka FROM oldbk.inventory USE INDEX (owner_6) LEFT JOIN skupka ON skupka.itemid=inventory.id WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0)
			".$what_not_to_sell." ".$notresurs." AND prokat_idp = 0 AND arsenal_klan='' AND cost>0  AND `setsale`=0 AND present!='Арендная лавка'
			and notsell = 0 AND bs_owner = 0 AND LENGTH(craftedby) > 5 AND skupka.stavka is NULL ORDER by `update` DESC; ");

	if (mysql_num_rows($q) > 0) {
		$arr = array();
		$protocount = array();
		while($i = mysql_fetch_assoc($q)) {
			$t = explode("|",$i['craftedby']);
			if ($t[0] == $user['id']) {
				$row = $i;

		                $item_type = round($row['type']);
		                $price = round($row['cost']*0.5,2);
				if($item_type <= 11 || $item_type==27 || $item_type==28) {
					$price = round($row['cost']*0.75,2);
				}

				if($item_type == 3) {
					$price = round($row['cost']*0.75,2);
				}

				if ($shop_skupka == 1) {
	                    		if($item_type < 12 || $item_type==28) {
						$pr = curr_price($row,$check_sowner);
						$price = $pr['summ'];
						$price *= $price_koef; // скупка
					}
				}

				if($row["prototype"] >= 410000 && $row["prototype"] <= 410030) {
					$price = 1;
					$row["duration"]=0;
				}

				if ($row['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
					$add_money = round($price-$row['duration']*($price/$row['maxdur']),2);
				} else {
			                //$add_money = round($price-$row['duration']*($row['cost']/($row['maxdur']*10)),2);
					$add_money = $price;
				}

		                if (in_array($row['prototype'],$r_excarray) && $row['massa'] == "1.1") {
					$price  = 1;
					$add_money=1;
				}

		                if ($add_money<0) {$add_money=0;}

				if (isset($arr[$i['prototype']])) {
					$arr[$i['prototype']]['ccount']++;
					$arr[$i['prototype']]['allprice'] += $add_money;
				} else {
					$arr[$i['prototype']] = $i;
					$arr[$i['prototype']]['ccount'] = 1;
					$arr[$i['prototype']]['allprice'] = $add_money;
				}
			}
		}


		$out = "";
		$allprice = 0;
		$scrarr = '<script>var protoprices = new Array();';
		while(list($k,$i) = each($arr)) {
			$out .= '<tr><td><input OnChange="changecraftprice('.$k.',this);" name="protocheck[]" value="'.$k.'" type="checkbox"></td><td align="left"><img src="http://i.oldbk.com/i/sh/'.$i['img'].'" border="0"></td>';
			$out .= '<td>'.$i['name'].' x '.$i['ccount'].'</td>';

			$out .= '<td><b>'.$i['allprice'].'</b> кр.</td>';
			$out .= '</tr>';
			$scrarr .= 'protoprices['.$k.'] = '.$i['allprice'].';';
		}

		echo '<form method="POST" action="?sellcraft"><table width=100%>';
		echo '<tr><td colspan="4" align="center"><input id="sellcraftbutton" type="submit" value="Продать выбранные вещи за 0 кр."></td></tr>';
		echo $out;
		echo '</table></form>';
		$scrarr .= 'craftsellprice = 0;</script>';
		echo $scrarr;

	} else {
		echo 'Крафтовые вещи не найдены';
	}
	die();
}

if (isset($_SESSION['shopmsg'])) {
	$msg = $_SESSION['shopmsg'];
	$typet = $_SESSION['shopmsgt'];
	unset($_SESSION['shopmsg']);
}

if (isset($_GET['sellcraft']) && isset($_POST['protocheck']) && count($_POST['protocheck']) && is_array($_POST['protocheck'])) {
	$allprice = 0;
	$protoids = array();
	while(list($k,$v) = each($_POST['protocheck'])) {
		$v = intval($v);
		if ($v > 0) {
			$protoids[] = $v;
		}
	}

	$q = mysql_query("SELECT inventory.*, skupka.stavka FROM oldbk.inventory USE INDEX (owner_6) LEFT JOIN skupka ON skupka.itemid=inventory.id WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0)
			".$what_not_to_sell." ".$notresurs." AND prokat_idp = 0 AND arsenal_klan='' AND cost>0  AND `setsale`=0 AND present!='Арендная лавка'
			and notsell = 0 AND bs_owner = 0 AND LENGTH(craftedby) > 5 AND skupka.stavka is NULL AND prototype IN (".implode(",",$protoids).")");

	while($i = mysql_fetch_assoc($q)) {
		$add_money = 0;

		// вычисляем цену
		$t = explode("|",$i['craftedby']);
		if ($t[0] != $user['id']) continue;

		$row = $i;

                $item_type = round($row['type']);
                $price = round($row['cost']*0.5,2);
		if($item_type <= 11 || $item_type==27 || $item_type==28) {
			$price = round($row['cost']*0.75,2);
		}

		if($item_type == 3) {
			$price = round($row['cost']*0.75,2);
		}

		if ($shop_skupka == 1) {
			if($item_type < 12 || $item_type==28) {
				$pr = curr_price($row,$check_sowner);
				$price = $pr['summ'];
				$price *= $price_koef; // скупка
			}
		}

		if($row["prototype"] >= 410000 && $row["prototype"] <= 410030) {
			$price = 1;
			$row["duration"]=0;
		}

		if ($row['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
			$add_money = round($price-$row['duration']*($price/$row['maxdur']),2);
		} else {
	                //$add_money = round($price-$row['duration']*($row['cost']/($row['maxdur']*10)),2);
			$add_money = $price;
		}

                if (in_array($row['prototype'],$r_excarray) && $row['massa'] == "1.1") {
			$price  = 1;
			$add_money=1;
		}

                if ($add_money<0) {$add_money=0;}

		$allprice += $add_money;

		$rec['owner']=$user['id'];
		$rec['owner_login']=$user['login'];
		$rec['owner_balans_do']=$user['money'];
		$user['money'] += $add_money;
		$rec['owner_balans_posle']=$user['money'];
		$rec['target']=0;
		$rec['target_login']='гос.маг.';
		$rec['type']=34;//продажа в гос госа
		$rec['sum_kr']=$add_money;
		$rec['sum_ekr']=0;
		$rec['sum_kom']=0;
		$rec['item_id']=get_item_fid($row);
		$rec['item_name']=$row['name'];
		$rec['item_count']=1;
		$rec['item_type']=$row['type'];
		$rec['item_cost']=$row['cost'];
		$rec['item_dur']=$row['duration'];
		$rec['item_maxdur']=$row['maxdur'];
		$rec['item_ups']=$row['ups'];
		$rec['item_unic']=$row['unik'];
		$rec['item_incmagic']=$row['includemagicname'];
		$rec['item_incmagic_count']=$row['includemagicuses'];
		$rec['item_arsenal']='';
		add_to_new_delo($rec);

		sellitems($row['id'],$row,$add_money,"");
	}

	if ($allprice > 0) {
		$_SESSION['shopmsg'] = "Вы продали продукцию на сумму ".$allprice." кр.";
	}
	header('Location: '.$_SERVER['PHP_SELF'].'?tmp='.mt_rand(1111111,99999999));
	die();
}


if (isset($_GET['sellfullres']) && isset($_POST['protocheck']) && count($_POST['protocheck']) && is_array($_POST['protocheck'])) {
	$allprice = 0;
	$protoids = array();
	while(list($k,$v) = each($_POST['protocheck'])) {
		$v = intval($v);
		if ($v > 0) {
			$protoids[] = $v;
		}
	}

	$allprice = 0;
	$q = mysql_query('SELECT *,count(*) as ccount FROM oldbk.inventory USE INDEX (owner_5) WHERE owner = '.$user['id'].$resurs.' AND cost>0 AND setsale=0 and type!=200 and type!=77 AND prokat_idp = 0 and bs_owner = 0 AND arsenal_klan="" AND present!="Арендная лавка" AND prototype IN ('.implode(",",$protoids).') and notsell = 0 group by prototype');
	while($i = mysql_fetch_assoc($q)) {
		$price = 0;
		$currprice = 0;

		if($i["prototype"] > 3000 && $i["prototype"] < 3022) {
			$price = round($i['cost']*0.5,2);
			$price+= round($price*$kk_res[$user['level']],2);
			$price = round($price*$i['ccount'],2);
		} else {
			$price = round($i['cost']*0.5,2);
			$price = round($price*$i['ccount'],2);
		}

		$allprice += $price;
		$currprice = $price;

		$q2 = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].$resurs.' AND cost>0 AND setsale=0 and type!=200 and type!=77 AND prokat_idp = 0 and bs_owner = 0 AND arsenal_klan="" AND present!="Арендная лавка" and prototype = '.$i['prototype']);

		$del_id = "";
		$delo_id = "";
		$ok = 0;
		$cc = 0;

		while($row = mysql_fetch_assoc($q2)) {
			$del_id  .= $row[id].', ';
			$delo_id .= get_item_fid($row).',';
			$item_type = $row['type'];
			$item_dur=$row['duration'];
			$item_mdur=$row['maxdur'];
			$item_cost=$row['cost'];
			$item_ups=$row['ups'];
			$item_unic=$row['unik'];
			$item_mag=$row['includemagicname'];
			$item_mag_c=$row['includemagicuses'];

			$ok = 1;
			$cc++;

			$name = $row['name'];
		}

		if($ok == 1) {
			$del_id = substr($del_id,0,-2);
			$delo_id = substr($delo_id,0,-2).')';

			//new_delo
			$rec['owner']=$user[id];
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user[money];
			$user['money'] += $currprice;
			$rec['owner_balans_posle']=$user[money];
			$rec['target']=0;
			$rec['target_login']='гос.маг.';
			$rec['type']=34;//продажа в гос госа
			$rec['sum_kr']=$currprice;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($row);
			$rec['item_name']=$name;
			$rec['item_count']=$cc;
			$rec['item_type']=$item_type;
			$rec['item_cost']=$item_cost;
			$rec['item_dur']=$item_dur;
			$rec['item_maxdur']=$item_mdur;
			$rec['item_ups']=$item_ups;
			$rec['item_unic']=$item_unic;
			$rec['item_incmagic']=$item_mag;
			$rec['item_incmagic_count']=$item_mag_c;
			$rec['item_arsenal']='';
			add_to_new_delo($rec);

	                sellitems($del_id,0,$currprice,"");
		}

	}

	if ($allprice > 0) {
		$_SESSION['shopmsg'] = "Вы продали все ресурсы на сумму ".$allprice." кр.";
	}
	header('Location: '.$_SERVER['PHP_SELF'].'?tmp='.mt_rand(1111111,99999999));
	die();
}

function sellitems($id,$row,$add_money,$echo_txt) {
	global $msg;
	if(is_array($row) && $row['add_pick'] != '') {
		undress_img($row);
	}

	mysql_query("UPDATE `users` set `money` = `money`+ '".$add_money."' WHERE id = {$_SESSION['uid']}");

	$msg = $echo_txt;
	$shmot=array(1,2,3,4,5,8,9,10,11,22,27,28,30);

	if(is_array($row) && in_array($row['type'],$shmot)) {
		destructitem($row['id']);
	} else {
		mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` in (".$id.") and owner=".$_SESSION['uid'].";");
	}

	if (is_array($row) && $row['stavka'] > 0) {
		mysql_query("DELETE FROM `oldbk`.`skupka` WHERE `itemid`='{$row['id']}' ");
	}
}

if (isset($_GET['gotoshop']) || isset($_GET['newsale_ars'])) {
	unset($_SESSION['shopfilter_otdel']);
	unset($_SESSION['shopfilter_name']);
	unset($_SESSION['shopfilter_sort']);
	unset($_SESSION['shopfilter_cost']);
	unset($_SESSION['shopfilter_llow']);
	unset($_SESSION['shopfilter_lmax']);
	unset($_SESSION['shopfilter_view']);
}


if ((((isset($_GET['sed']) && $_GET['sed'] > 0) || (isset($_POST['is_newsale']) && $_POST['is_newsale'] == 1))) && $user['align'] != 4) {

	if(!$_POST['count']) {
		if ($_GET['newsale_ars'] and $clan_kazna) {
			//oldbk. -надо т.к. арс хранится в базе кепа
			$row = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE owner='22125' and arsenal_klan='{$user[klan]}' and arsenal_owner=1  AND `id` = '{$_GET['sed']}'
	  	    		".$what_not_to_sell." AND cost>0 and notsell = 0 and type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0) AND `setsale`=0 AND bs_owner = 0 LIMIT 1"));

		} else {

			$_GET['sed'] = intval($_GET['sed']);

			$row = mysql_fetch_array(mysql_query("SELECT *, (select stavka from oldbk.skupka where itemid=i.id) as stavka FROM oldbk.`inventory` as i WHERE `dressed`= 0 AND `id` = '{$_GET['sed']}'
				and notsell = 0 AND cost>0 AND setsale=0 and type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0) AND prokat_idp = 0 ".$what_not_to_sell." AND bs_owner = 0 AND arsenal_klan='' AND present!='Арендная лавка' AND `owner` = '{$_SESSION['uid']}' LIMIT 1;"));
		}


		if($row['id']) {
	                $item_type = round($row['type']);
	                $price = round($row['cost']*0.5,2);
			if($item_type <= 11 || $item_type==27 || $item_type==28) {
				$price = round($row['cost']*0.75,2);
			}

			if($item_type == 3) {
				$price = round($row['cost']*0.75,2);
			}

			if($row["prototype"] > 3000 && $row["prototype"] < 3022) {
				$price=round($row['cost']*0.5,2);
				$price+=$price*$kk_res[$user[level]];
			}

			if (($shop_skupka==1) or ($row['stavka']>0)) {
                    		if($item_type < 12 || $item_type==27 || $item_type==28) {
					$pr = curr_price($row,$check_sowner);
					$price = $pr['summ'];

					if ($row['stavka']>0) {
						$price *= ($row['stavka']/100); // скидка закастована
						if ($row['includemagicuses'] > 0) {
							if ($row['includemagicuses']>150) {
								$price += 150;
							} else	{
								$price += $row['includemagicuses'];
							}
						}
					} else {
						$price *= $price_koef; // новогодняя
					}
				}
			}

			if($row["prototype"] >= 410000 && $row["prototype"] <=410030) {
				$price=1;
				$row["duration"]=0;
			}

			if ($row['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
				$add_money = round($price-$row['duration']*($price/$row['maxdur']),2);
			} else {
		                //$add_money = round($price-$row['duration']*($row['cost']/($row['maxdur']*10)),2);
				$add_money = $price;
			}

	                if (in_array($row['prototype'],$r_excarray) && $row['massa'] == "1.1") {
				$price  = 1;
				$add_money=1;
			}

	                if ($add_money<0) {$add_money=0;}

			$echo_txt = "Вы продали \"".$row['name']."\" за ".$add_money." кр.";


			if (strlen($row['craftedby'])) {
				mysql_query('INSERT INTO craft_sellstats (owner,itemname,itemcount,selltime,itemprice,getmoney)
						VALUES ('.$user['id'].',"'.mysql_real_escape_string($row['name']).'",1,NOW(),"'.$row['cost'].'","'.$add_money.'")
				');
			}


			if ($_GET['newsale_ars'] and $clan_kazna) {
				$delo_txt="\"".$user['login']."\" продал в магазин товар: \"".$row['name']."\" (x1) id:(".get_item_fid($row).") [".$row['duration']."/".$row['maxdur']."] [ups:".$row['ups']."/free:".$row['upfree']."/inc:".$row['includemagicname']."] за ".$add_money." кр.";
				$echo_txt.="<i>(в казну клана)</i>";
				if (sell_to_kazna($clan_id[id],$add_money,$row,$delo_txt)) {
	  			        $delo_txt.="<i>(из арсенала в казну клана)</i>";
	     		  		$clan_kazna[kr]+=$add_money;
	  		    		$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];
					$rec['owner_balans_posle']=$user[money];
					$rec['target']=0;
					$rec['target_login']='гос.маг.';
					$rec['type']=34;//продажа в гос госа
					$rec['sum_kr']=$add_money;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($row);
					$rec['item_name']=$row['name'];
					$rec['item_count']=1;
					$rec['item_type']=$row['type'];
					$rec['item_cost']=$row['cost'];
					$rec['item_dur']=$row['duration'];
					$rec['item_maxdur']=$row['maxdur'];
					$rec['item_ups']=$row['ups'];
					$rec['item_unic']=$row['unik'];
					$rec['item_incmagic']=$row['includemagicname'];
					$rec['item_incmagic_count']=$row['includemagicuses'];
					$rec['item_arsenal']=$user[klan];
					add_to_new_delo($rec);

					$msg = $echo_txt;
					destructitem($row['id'],true);
				}
			} else {
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];
				$user['money']+=$add_money;
				$rec['owner_balans_posle']=$user[money];
				$rec['target']=0;
				$rec['target_login']='гос.маг.';
				$rec['type']=34;//продажа в гос госа
				$rec['sum_kr']=$add_money;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=get_item_fid($row);
				$rec['item_name']=$row['name'];
				$rec['item_count']=1;
				$rec['item_type']=$row['type'];
				$rec['item_cost']=$row['cost'];
				$rec['item_dur']=$row['duration'];
				$rec['item_maxdur']=$row['maxdur'];
				$rec['item_ups']=$row['ups'];
				$rec['item_unic']=$row['unik'];
				$rec['item_incmagic']=$row['includemagicname'];
				$rec['item_incmagic_count']=$row['includemagicuses'];
				$rec['item_arsenal']='';
				add_to_new_delo($rec);

		                sellitems($row['id'],$row,$add_money,$echo_txt);
	                }
		} else {
			$msg = "Ошибка предмет не найден!";
			$typet = "e";
		}
	} elseif((int)$_POST['count']>0 && (int)$_GET['tmp']>=0 && (int)$_GET['gift']>=0) {
		$del_id='';
		$delo_id='(';
		$price='';
		$add_money='';
		$cc=0;

		/*
		if($_GET['gift']==0) {
			$ss="AND present = ''";
		} else {
			$ss="AND present!= ''";
		}*/

		$data = mysql_query("SELECT *, (select stavka from oldbk.skupka where itemid=i.id) as stavka  FROM oldbk.`inventory` as i USE INDEX (owner_4) WHERE `dressed`= 0 AND `group` = 1 and type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0)
			AND `prototype` = '".(int)$_POST['set']."' AND duration='".(int)$_GET['tmp']."' ".$ss." AND cost>0 ".$what_not_to_sell." AND prokat_idp = 0 AND bs_owner = 0
	            	AND `notsell` = 0 AND setsale=0 AND present!='Арендная лавка' AND `owner` = '{$_SESSION['uid']}' LIMIT ".(int)$_POST['count'].";");

		$cr_addmoney = 0;
		$cr_itemcount = 0;

		while($row=mysql_fetch_assoc($data)) {
			if($row['id'] && $row['group'] ==1) {
				$del_id.=$row[id].', ';
				$delo_id.=get_item_fid($row).',';
				$item_type = round($row['type']);
				$item_dur=$row['duration'];
				$item_mdur=$row['maxdur'];
				$item_cost=$row['cost'];
				$item_ups=$row['ups'];
				$item_unic=$row['unik'];
				$item_mag=$row['includemagicname'];
				$item_mag_c=$row['includemagicuses'];

				$price = round($row['cost']*0.5,2);

				if($item_type <= 11 || $item_type==27 || $item_type==28) {
					$price = round($row['cost']*0.75,2);
				}

				if($item_type == 3) {
					$price = round($row['cost']*0.75,2);
				}

				if($row["prototype"] > 3000 && $row["prototype"] < 3022) {
					$price=round($row['cost']*0.5,2);
					$price+=$price*$kk_res[$user[level]];
				}

				if (($shop_skupka==1) or ($row['stavka']>0)) {
					if($item_type < 12 || $item_type == 28) {
						$pr = curr_price($row,$check_sowner);
						$price=$pr[summ];

						if ($row['stavka'] > 0) {
							$price *= ($row['stavka']/100); // скидка закастована

							if ($row['includemagicuses'] > 0) {
								if ($row['includemagicuses'] > 150) {
									$price += 150;
								} else {
									$price += $row['includemagicuses'];
								}
							}
						} else {
							$price*=$price_koef;
						}
					}
				}

				$ok = 1;

				$cc++;

				$name = $row['name'];

				if($row["prototype"] >= 410000 && $row["prototype"] <=410030) {
					$price=1;
					$row["duration"]=0;
				}

				$tmp = 0;

				if ($row['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
					$tmp = round($price-$row['duration']*($price/$row['maxdur']),2);
					$add_money += $tmp;
				} else {
					//$tmp = round($price-$row['duration']*($row['cost']/($row['maxdur']*10)),2);
					//$add_money += $tmp;
					$add_money += $price;
				}

				if (strlen($row['craftedby'])) {
					$cr_addmoney += $tmp;
					$cr_itemcount++;
				}


				if($row['add_pick'] != '') {
					undress_img($row);
				}


				mysql_query("DELETE FROM `oldbk`.`skupka` WHERE `itemid`='{$row['id']}' ");
			}
		}

		if($ok == 1) {
			$del_id=substr($del_id,0,-2);
			$delo_id=substr($delo_id,0,-2).')';

			//new_delo
			$rec['owner']=$user[id];
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user[money];
			$user['money']+=$add_money;
			$rec['owner_balans_posle']=$user[money];
			$rec['target']=0;
			$rec['target_login']='гос.маг.';
			$rec['type']=34;//продажа в гос госа
			$rec['sum_kr']=$add_money;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($row);
			$rec['item_name']=$name;
			$rec['item_count']=$cc;
			$rec['item_type']=$item_type;
			$rec['item_cost']=$item_cost;
			$rec['item_dur']=$item_dur;
			$rec['item_maxdur']=$item_mdur;
			$rec['item_ups']=$item_ups;
			$rec['item_unic']=$item_unic;
			$rec['item_incmagic']=$item_mag;
			$rec['item_incmagic_count']=$item_mag_c;
			$rec['item_arsenal']='';
			add_to_new_delo($rec);

			if ($cr_addmoney > 0) {
				mysql_query('INSERT INTO craft_sellstats (owner,itemname,itemcount,selltime,itemprice,getmoney)
						VALUES ('.$user['id'].',"'.mysql_real_escape_string($name).'",'.$cr_itemcount.',NOW(),"'.$item_cost.'","'.$cr_addmoney.'")
				');
			}


	                $echo_txt = "Вы продали \"".$name."\" (x".$cc.") за ".$add_money." кр.";

	                sellitems($del_id,0,$add_money,$echo_txt);
		}
	}

	$_SESSION['shopmsg'] = $msg;
	$_SESSION['shopmsgt'] = $typet;

	header('Location: '.$_SERVER['PHP_SELF'].'?newsale=1&tmp='.mt_rand(1111111,99999999));
	die();
}

if (($_GET['set']) and ($_GET['set_ars']) and ($clan_kazna)) {
	// покупка в арсенал излан казны
	// казну не покупаются вещи с need_win >0 - великие вещи не покупаются

	$set=(int)($_GET['set']);
	$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` = '{$set}' and type in (1,2,3,4,5,6,7,8,9,10,11,28) and need_wins=0 LIMIT 1;"));

	if (($dress['cost'] > 0) and ($dress[GetShopCount()] > 0)) {
		mysql_query("UPDATE oldbk.`shop` SET `".GetShopCount()."` = `".GetShopCount()."` -1 WHERE `id` = '{$dress[id]}'  AND `".GetShopCount()."` >= 1 LIMIT 1;");
		if(mysql_affected_rows()>0) {

			mysql_query('INSERT INTO shop_stats (owner,shoptype,shopprototype,shopcount,lastupdate)
					VALUES ('.$user['id'].',1,'.$set.',1,'.time().')
					ON DUPLICATE KEY UPDATE
						`shopcount` = `shopcount` + 1, lastupdate = '.time()
			);

			$coment="\"".$user['login']."\" купил новый товар: \"".$dress['name']."\" [0/".$dress['maxdur']."] за ".$dress[cost]." кр. в клановый арсенал";
			if (by_from_kazna($clan_id['id'],1,$dress['cost'],$coment)) {
				$str='';
				$sql='';

				if($dress['type']==30) {
					$str=",`add_time`  ";
					$sql=",'".$runs_exp_table[0]['next']."' ";
					$as_sowner=$user['id'];
				} elseif($dress['nlevel']>6) {
					$str=",`up_level` ";
					$sql=",'".$dress['nlevel']."' ";
				}

				$good = 0;

				//oldbk. -надо т.к. арс хранится в базе кепа!
				if(mysql_query("INSERT INTO oldbk.`inventory`
					(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`img_big`,`maxdur`,`isrep`,`nclass`,`rareitem`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`arsenal_klan` , `arsenal_owner`,`idcity`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`ab_mf`,  `ab_bron` ,  `ab_uron`,
						`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`getfrom`,`notsell`,`craftspeedup`,`craftbonus` ".$str."
					)
					VALUES
					('{$dress['id']}','22125','{$dress['name']}','{$dress['type']}',{$dress['massa']},'{$dress[cost]}','{$dress['img']}','{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['rareitem']}',
					'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
					'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}', '{$user[klan]}' , '1','{$user[id_city]}', '{$dress[includemagic]}','{$dress[includemagicdex]}','{$dress[includemagicmax]}','{$dress[includemagicname]}','{$dress[includemagicuses]}','{$dress[includemagiccost]}','{$dress[includemagicekrcost]}', '{$dress['ab_mf']}',  '{$dress['ab_bron']}' ,  '{$dress['ab_uron']}'
					,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','41','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}' ".$sql."
				) ;")) {
					$good = 1;
					$insert_item_id=mysql_insert_id();

				}


	       			if ($good == 1) {
					//mysql_query("UPDATE oldbk.`shop` SET `".GetShopCount()."` = `".GetShopCount()."` -1 WHERE `id` = '{$set}' LIMIT 1;");
	       				$invdb = mysql_query("SELECT * FROM oldbk.`inventory` WHERE id='{$insert_item_id}' ;" );
	       				$dressinv = mysql_fetch_array($invdb);
					$dressid = get_item_fid($dressinv);//принудительно указываем
					$dresscount=" ";
					mysql_query("INSERT INTO oldbk.clans_arsenal (id_inventory, klan_name, owner_original) 	VALUES 	('{$dressinv[id]}','{$user['klan']}','1')");
					mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$user[klan]}','{$user[id]}','{$coment}','".time()."')");

					//new delo

					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];
					$clan_kazna[kr]-=$dress[cost];
					$rec['owner_balans_posle']=$user[money];
					$rec['target']=0;
					$rec['target_login']='гос.маг.';
					$rec['type']=1;//покупка из госа
					$rec['sum_kr']=$dress[cost];
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=$dressid;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']=$user[klan];
					add_to_new_delo($rec);

					$msg = "Вы купили 1 шт. \"{$dress['name']}\". (<i>в клановый арсенал</i>)";
				}
			} else {
				$msg = 'Ошибка покупки в клановый арсенал!';
				$typet = "e";
			}
		} else {
			$msg = "Вы не можете купить эту вещь!";
			$typet = "e";
		}
	} else {
		$msg = "Вы не можете купить эту вещь!";
		$typet = "e";
	}
} elseif (($_GET['set'] OR $_POST['set'])) {
	if ($_GET['set']) { $set = intval($_GET['set']); }
	if ($_POST['set']) { $set = intval($_POST['set']); }

	if($_POST['is_newsale']==0) {
		if ((!$_POST['count']) OR ($_POST['count']==0) OR ($_POST['count']<0) ) { $_POST['count'] =1; }

		$_POST['count']=(int)$_POST['count'];
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` = '{$set}' LIMIT 1;"));

		$gos_cost=$dress['cost'];
		if ( ((time()>$KO_start_time24) and (time()<$KO_fin_time24))) {
			//акция на хилы -20%
			if (strpos($dress['name'], 'Восстановление энергии') !== false || strpos($dress['name'], 'свиток «Восстановление') !== false) {
				$dress['cost']=$dress['cost']*0.8;
			}
		}
		$item_dategoden=$dress['dategoden'];


		$goden_sql="'".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}'";


		if ($dress['need_wins'] > $user['winstbat']) {
		 	$msg = "У вас нехватает побед в Великих битвах для покупки этого предмета!";
			$typet = "e";
		} elseif (($dress['massa']*$_POST['count']+$d[0]) > (get_meshok())) {
			$msg = "Недостаточно места в рюкзаке.";
			$typet = "e";
		} elseif(($user['money']>= ($dress['cost']*$_POST['count'])) && ($dress[GetShopCount()] >= $_POST['count'])) {
			if (give_count($user['id'],$_POST['count'])) {
				//привязываем шмотки из раздела великих = если прописаны нужные попебы то одаем подарком
			 	if (($dress['need_wins'] > 0) and ($dress['type'] == 12)) {
					$as_present='Удача';
					$as_sowner=0;
				} elseif (($dress[need_wins]>0) and ($dress['id']>=4401) and ($dress['id']<=4410)) {
					$as_present='Удача';
					$as_sowner=$user[id];
				} elseif (($dress['need_wins'] > 0) and ($dress['type'] != 12)) {
					//если вещи  то не подарком а привязанное
					$as_present='';
					$as_sowner=$user[id];
				} elseif (($dress['id'] == 3001003 || $dress['id'] == 3001002 || $dress['id'] == 3001001 || $dress['id'] == 3001004)) {
	    			    	$as_present='';
					$as_sowner=$user[id];
				} elseif ($dress['id']==2014000) {
					$as_present = 'Удача';
					$as_sowner= 0;
					$goden_do = mktime(23,59,59,1,31,2018);
					$goden = round(($goden_do-time())/(24*3600)); if ($goden<1) {$goden=1;}
					$goden_sql=" '{$goden_do}' , '{$goden}' ";
				} elseif ($dress['id']==2016001 || $dress['id']==2016002) {
					$as_present = 'Мироздатель';
					$as_sowner= 0;
					$goden_do = mktime(23,59,59,4,17,2017);
					$goden = floor(($goden_do-time())/(24*3600)); if ($goden<1) {$goden=1;}
					$goden_sql=" '{$goden_do}' , '{$goden}' ";
				} elseif ($dress['razdel'] == 62) {
					$as_present='Мироздатель';
					$as_sowner=0;
				} elseif ($dress['id'] == 122121) {
					$as_present='Мироздатель';
					$as_sowner=0;
				} elseif (($dress['id'] == 2017101) OR ($dress['id'] == 106601) OR ($dress['id'] == 20180101)) {
					$as_present='Удача';
					$as_sowner=0;
				} else {
					$as_sowner=0;
					$as_present='';
				}

			if ($item_dategoden>0)
							{
							$goden_do = $item_dategoden;
							$goden = floor(($goden_do-time())/(24*3600)); if ($goden<1) {$goden=1;}
							$goden_sql=" '{$goden_do}' , '{$goden}' ";
							}


				mysql_query("UPDATE oldbk.`shop` SET `".GetShopCount()."`= `".GetShopCount()."`-{$_POST['count']} WHERE `id` = '{$set}' AND `".GetShopCount()."` >= {$_POST['count']}");

				if (mysql_affected_rows() > 0) {

					mysql_query('INSERT INTO shop_stats (owner,shoptype,shopprototype,shopcount,lastupdate)
							VALUES ('.$user['id'].',1,'.$set.','.$_POST['count'].','.time().')
							ON DUPLICATE KEY UPDATE
								`shopcount` = `shopcount` + '.$_POST['count'].', lastupdate = '.time()

					);

					$insert_id=array();
					$good = 0;

					for($k=1;$k<=$_POST['count'];$k++) {
						$str='';
						$sql='';

						if($dress['type']==30) {
							$str=",`add_time`  ";
							$sql=",'".$runs_exp_table[0][next]."'  ";
							$as_sowner=$user[id];
						} elseif($dress['nlevel']>6) {
							$str=",`up_level` ";
							$sql=",'".$dress[nlevel]."' ";
						}


						if(mysql_query("INSERT INTO oldbk.`inventory`
						(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`img_big`,`maxdur`,`isrep`,`nclass`,`rareitem`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`idcity`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`, `ab_mf`,  `ab_bron` ,  `ab_uron`,
							`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`getfrom`,`notsell` ".$str."
						)
						VALUES
						('{$dress['id']}',{$as_sowner},'{$as_present}','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$gos_cost},'{$dress['img']}','{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['rareitem']}',
						'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
						'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
						'{$dress['nalign']}',".$goden_sql.",'{$user['id_city']}' , '{$dress[includemagic]}','{$dress[includemagicdex]}','{$dress[includemagicmax]}','{$dress[includemagicname]}','{$dress[includemagicuses]}','{$dress[includemagiccost]}','{$dress[includemagicekrcost]}', '{$dress['ab_mf']}',  '{$dress['ab_bron']}' ,  '{$dress['ab_uron']}'
						,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','41','{$dress['notsell']}' ".$sql."
						) ;"))
						{
							$good = 1;
							$insert_id[$k]=mysql_insert_id();
						}

					}

					if ($good == 1) {
						$msg = "Вы купили {$_POST['count']} шт. \"{$dress['name']}\".";
						mysql_query("UPDATE `users` set `money` = `money`- '".($_POST['count']*$dress['cost'])."' WHERE id = {$_SESSION['uid']} ;");
						//$user['money'] -= $_POST['count']*$dress['cost'];
						$limit=$_POST['count'];
						$invdb = mysql_query("SELECT * FROM oldbk.`inventory` WHERE id in (".implode(',',$insert_id).") ORDER by `id` DESC ;" );

						if ($limit == 1) {
							$dressinv = mysql_fetch_array($invdb);
							$dressid = get_item_fid($dressinv);
							$dresscount=" ";
						} else {
							$dressid="";
							while ($dressinv = mysql_fetch_array($invdb))  {
								$dressid .= get_item_fid($dressinv).",";
							}
							$dresscount="(x".$_POST['count'].") ";
						}

						$allcost=$_POST['count']*$dress['cost'];

						$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user[money];
						$user['money'] -= $_POST['count']*$dress['cost'];
						$rec['owner_balans_posle']=$user[money];
						$rec['target']=0;
						$rec['target_login']='гос.маг.';
						$rec['type']=1;//покупка из госа
						$rec['sum_kr']=$allcost;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['item_id']=$dressid;
						$rec['item_name']=$dress['name'];
						$rec['item_count']=$_POST['count'];
						$rec['item_type']=$dress['type'];
						$rec['item_cost']=$gos_cost;
						$rec['item_dur']=$dress['duration'];
						$rec['item_maxdur']=$dress['maxdur'];
						$rec['item_ups']=0;
						$rec['item_unic']=0;
						$rec['item_incmagic']='';
						$rec['item_incmagic_count']='';
						$rec['item_arsenal']='';
						add_to_new_delo($rec);
					}
				} else {
					$msg = "Недостаточно денег или нет вещей в наличии.";
					$typet = "e";
				}
			} else {
				$msg = "У Вас недостаточно лимита передач на сегодня!";
				$typet = "e";
			}
		} else {
			$msg = "Недостаточно денег или нет вещей в наличии.";
			$typet = "e";
		}
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

function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}

function AddCount(event,name, txt, sale, href, maxlen) {
	var el = document.getElementById("hint3");
	if(sale==1) {
		var sale_txt= 'Продать несколько штук';
		var a_href='action="?newsale=1&id=1'+href+'"';

		el.innerHTML = '<form '+a_href+' method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B style="font-size:11pt;">'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();return false;"><B style="font-size:11pt;">x</B></TD></tr><tr><td colspan=2>'+
		'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="is_newsale" value="'+sale+'"><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B style="font-size:11pt;"><I>'+txt+'</td></tr><tr><td width=80% align=right style="font-size:11pt;">'+
		'Кол-во (макс '+maxlen+' шт.) <INPUT id="itemcount" TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" style="height:16px;margin-top:2px;" value=" »» ">'+
		'</TD></TR></TABLE></td></tr></table></form>';
	} else {
		var sale=0;
		var sale_txt= 'Купить несколько штук';
		var a_href='';

		el.innerHTML = '<form '+a_href+' method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B style="font-size:11pt;">'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();return false;"><B style="font-size:11pt;">x</B></TD></tr><tr><td colspan=2>'+
		'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="is_newsale" value="'+sale+'"><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B style="font-size:11pt;"><I>'+txt+'</td></tr><tr><td width=80% align=right style="font-size:11pt;">'+
		'Кол-во (макс '+maxlen+' шт.) <INPUT id="itemcount" TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" style="height:16px;margin-top:2px;" value=" »» ">'+
		'</TD></TR></TABLE></td></tr></table></form>';

	}

	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	y = event.pageY;
	el.style.top = (y -120) + 'px';
	document.getElementById("itemcount").focus();
}

var dialog;
var dialog2;

// Закрывает окно
function closehint3() {
	document.getElementById("hint3").style.visibility="hidden";
}

function ShowRes() {
	dialog.dialog( "open" );

	$.ajax({
		url: "?showres",
		cache: false,
		async: true,
		success: function(data){
			$("#sellress-form").html(data);
		}
	});

}

function ShowCraftSell() {
	dialog2.dialog( "open" );

	$.ajax({
		url: "?showcraft",
		cache: false,
		async: true,
		success: function(data){
			$("#sellcraft-form").html(data);
		}
	});

}

var craftsellprice = 0;
var ressellprice = 0;

function changecraftprice(proto,fr) {
	if($(fr).is(':checked')) {
		craftsellprice += protoprices[proto];
	} else {
		craftsellprice -= protoprices[proto];
	}
	tmp = craftsellprice.toFixed(2);
	if (tmp < 0) tmp = 0;
	$("#sellcraftbutton").val("Продать выбранные вещи за "+tmp+" кр.");
}

function changeresprice(proto,fr) {
	if($(fr).is(':checked')) {
		ressellprice += protoprices[proto];
	} else {
		ressellprice -= protoprices[proto];
	}
	tmp = ressellprice.toFixed(2);
	if (tmp < 0) tmp = 0;
	$("#sellresbutton").val("Продать выбранные ресурсы за "+tmp+" кр.");
}


</SCRIPT>
</HEAD>
<body id="arenda-body">
<script type='text/javascript'>
RecoverScroll.start();
</script>
<div id="sellress-form" title="Продать ресурсы">
<img src="http://i.oldbk.com/i/ajax-loader.gif" border="0">
</div>
<div id="sellcraft-form" title="Продать крафтовые вещи">
<img src="http://i.oldbk.com/i/ajax-loader.gif" border="0">
</div>



<SCRIPT LANGUAGE="JavaScript">
dialog = $("#sellress-form" ).dialog({
	autoOpen: false,
      	width: 550,
      	modal: true,
	position: { my: "top", at: "top", of: window },
	dialogClass: 'sell-dialog-class',
	close: function() {
		$("#sellress-form").html('<img src="http://i.oldbk.com/i/ajax-loader.gif" border=0>');
	}
});

dialog2 = $("#sellcraft-form" ).dialog({
	autoOpen: false,
      	width: 550,
      	modal: true,
	position: { my: "top", at: "top", of: window },
	dialogClass: 'sell-dialog-class',
	close: function() {
		$("#sellcraft-form").html('<img src="http://i.oldbk.com/i/ajax-loader.gif" border=0>');
	}
});

</script>

<div id="page-wrapper">
    <div class="title">
        <div class="h3">Магазин</div>
        <div id="buttons">
            <a class="button-dark-mid btn" onclick="window.open('help/shop.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes'); return false;" title="Подсказка">Подсказка</a>
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
		    <?php
			$lasttime = 30*24*3600; // за последний месяц
			$q = mysql_query('SELECT shop.* FROM shop_stats LEFT JOIN shop ON shopprototype = shop.id WHERE shop_stats.owner = '.$user['id'].' and shoptype = 1 and lastupdate > '.(time()-$lasttime).' ORDER BY shopcount DESC, lastupdate DESC LIMIT 12');
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
							if (strpos($row['name'], 'Восстановление энергии') !== false || strpos($row['name'], 'свиток «Восстановление') !== false) {
								$row['cost']=$row['cost']*0.8;
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
								<img src="http://i.oldbk.com/i/up.gif" width="11" height="11" border="0" alt="Купить несколько штук"  title="Купить несколько штук"  style="cursor: pointer" onclick="AddCount(event,'<?= $row['id']?>', '<?= $row['name']?>','0','','<? echo floor($user['money'] / $row['cost']) ?>'); return false;">
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
                                <div class="head-title">Отдел &quot;<?php
	if ($_GET['newsale_ars'] and $clan_kazna) {
		echo 'Скупка из арсенала' ;
	} elseif ($_POST['newsale'] || $_GET['newsale']) {
		echo 'Скупка' ;
	} else if ($_GET['otdel'] >=100) {
		foreach ($arr_pril_vel as $ke => $va) {
			if ($_GET['otdel'] == $va) {
				echo "Прилавок Великих (".$va." побед)";
			}
		}

	} else {

		if (!isset($_GET['otdel'])) {
			$q = mysql_query('SELECT * FROM shop WHERE razdel = 78 and count > 0');
			if (mysql_num_rows($q) > 0) {
				$_GET['otdel'] = 78;
			} else {
				$_GET['otdel'] = 61;
			}
		}

		switch ($_GET['otdel']) {
			case null:
				echo "Кастеты, ножи";
				$_GET['otdel'] = 1;
			break;
			case 1:
				echo "Кастеты, ножи";
			break;
			case 11:
				echo "Топоры";
			break;
			case 12:
				echo "Дубины, булавы";
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
				echo "Нейтральные";
			break;
			case 51:
				echo "Боевые и защитные";
			break;
			case 52:
				echo "Заклинания: сервисные";
			break;
			case 55:
				echo "Лицензии";
			break;
			case 6:
				echo "Амуниция";
			break;
			case 60:
				echo "Молитвенные предметы";
			break;
			case 61:
				echo "Еда";
			break;
			case 99:
				echo "Прилавок Великих";
			break;

			case 62:
				echo "Ресурсы";
			break;

			case 63:
				echo "Инструменты";
			break;
			case 82:
				echo "Хеллоуин";
			break;
			case 78:
				echo "Акции";
			break;

		}
	}

?>&quot;
				</div>
                            </th>
                            <th class="filter">
				<?php if (!isset($_REQUEST['newsale']) && !isset($_REQUEST['newsale_ars'])) { ?>
			    	<form method="POST" action="?otdel=<?=intval($_GET['otdel']);?>" id="fall" style="margin:0px;padding:0px;display:block;">

                                <div class="head-title" style="right:272px;">
                                    <select name="fallclass" style="width:250px;height:14px;margin:0px;top:0px;" OnChange="document.getElementById('fall').submit();">
					<option value = "0" <? if ((int)($_POST['fallclass'])==0) { echo ' selected ' ; } ?>>Показывать все классы</option>
					<option value = "-1" <? if ($_POST['fallclass'] == -1) { echo ' selected ' ; $viewclass=-1; } ?>>Показывать только мой класс</option>
					<option value = "1" <? if ($_POST['fallclass'] == 1) { echo ' selected ' ; $viewclass=1; } ?>>Уворотчик</option>
					<option value = "2" <? if ($_POST['fallclass'] == 2) { echo ' selected ' ; $viewclass=2; } ?>>Критовик</option>
					<option value = "3" <? if ($_POST['fallclass'] == 3) { echo ' selected ' ; $viewclass=3; } ?>>Танк</option>
                                    </select>
                                </div>


                                <div class="head-title" style="top:1px;">
                                    <select name="fall" style="width:250px;height:14px;margin:0px;top:0px;" OnChange="document.getElementById('fall').submit();">
					<option value = "0" <? if ((int)($_POST['fall'])==0) { echo ' selected ' ; } ?>>Показывать все вещи</option>
					<option value = "1" <? if ($_POST['fall']>0) { echo ' selected ' ; $viewlevel=true; } ?>>Показывать вещи только моего уровня</option>
                                    </select>
                                </div>
				</form>
				<?php } ?>
                                <div class="head-right"></div>
                            </th>
                        </tr>
                        </thead>
                    </table>
<?php
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
?>
                    <table class="table border a_strong" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="200px">
                            <col>
                        </colgroup>
                        <tbody>
<?


	$inv_shmot = array();
	$inv_gr_key = array();
	$qwe = 0;

	if ((($_REQUEST['newsale']) OR ($_REQUEST['newsale_ars'] and $clan_kazna)) && $user['align'] != 4) {

		$data = false;

		// фильтры

		$viewsf = [50,100,500];

		$sorts = [
			0 => "По дате годности",
			1 => "По названию",
			2 => "По износу",
		];

		// сброс фильтров
		if (isset($_POST['reset']) && $_POST['reset'] == "Yes") {
			unset($_SESSION['shopfilter_otdel']);
			unset($_SESSION['shopfilter_name']);
			unset($_SESSION['shopfilter_sort']);
			unset($_SESSION['shopfilter_cost']);
			unset($_SESSION['shopfilter_llow']);
			unset($_SESSION['shopfilter_lmax']);
			unset($_SESSION['shopfilter_view']);
			unset($_POST['iname']);
			unset($_POST['isort']);
			unset($_POST['iview']);
			unset($_POST['ilevellow']);
			unset($_POST['ilevelmax']);
		}

		// параметры
		if (isset($_GET['otdel'])) {
			$_SESSION['shopfilter_otdel'] = intval($_GET['otdel']);
		}
		if (isset($_POST['iview']) && in_array($_POST['iview'],$viewsf)) {
			$_SESSION['shopfilter_view'] = $_POST['iview'];
		}
		if (isset($_POST['isort']) && isset($sorts[$_POST['isort']])) {
			$_SESSION['shopfilter_sort'] = $_POST['isort'];
		}
		if (isset($_POST['isortcost']) && isset($sorts[$_POST['isortcost']])) {
			$_SESSION['shopfilter_cost'] = $_POST['isortcost'];
		}
		if (isset($_POST['ilevellow'])) {
			$_SESSION['shopfilter_llow'] = intval($_POST['ilevellow']);
		}
		if (isset($_POST['ilevelmax'])) {
			$_SESSION['shopfilter_lmax'] = intval($_POST['ilevelmax']);
		}
		if (isset($_POST['iname'])) {
			$_POST['iname'] = trim($_POST['iname']);
			if(!empty($_POST['iname'])) {
				$_SESSION['shopfilter_name'] = $_POST['iname'];
			} else {
				unset($_SESSION['shopfilter_name']);
			}
		}


		// запросы
		$sql = '';
		if (isset($_SESSION['shopfilter_otdel'])) {
			if ($_SESSION['shopfilter_otdel'] == 6) {
				$sql .= ' and (otdel = '.$_SESSION['shopfilter_otdel'].' or otdel = 78)';
			} else {
				$sql .= ' and otdel = '.$_SESSION['shopfilter_otdel'];
			}
		}
		if (isset($_SESSION['shopfilter_name'])) {
			$sql .= ' and name LIKE "%'.mysql_real_escape_string($_SESSION['shopfilter_name']).'%"';
		}
		if (isset($_SESSION['shopfilter_llow'],$_SESSION['shopfilter_lmax'])) {
			$sql .= ' and nlevel >= '.$_SESSION['shopfilter_llow'].' and nlevel <= '.$_SESSION['shopfilter_lmax'];
		}

		$sortsql = "";
		if (isset($_SESSION['shopfilter_sort'])) {
			$s = $_SESSION['shopfilter_sort'];
			if ($s == 0) $sortsql = 'dategoden DESC,' ;
			if ($s == 1) $sortsql = 'name ASC,' ;
			if ($s == 2) $sortsql = 'maxdur ASC,' ;
		}


		if (!isset($_SESSION['shopfilter_view'])) {
			$_SESSION['shopfilter_view'] = 50;
		}

		if (!isset($_SESSION['shopfilter_cost'])) {
			$_SESSION['shopfilter_cost'] = 0;
		}

		if ($_REQUEST['newsale']) {
			$data = mysql_query("SELECT *, (select stavka from oldbk.skupka where itemid=i.id) as stavka FROM oldbk.`inventory` as i USE INDEX (owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0)
			".$what_not_to_sell." AND prokat_idp = 0 AND arsenal_klan='' AND cost>0  AND `setsale`=0 AND present!='Арендная лавка'
			".$sql." and notsell = 0 AND bs_owner = 0 ORDER by ".$sortsql." `update` DESC");
		} elseif ($_REQUEST['newsale_ars']) {
	  		// oldbk. -надо т.к. арс -хранится в базе кепа
	  		$data = mysql_query("SELECT * FROM oldbk.`inventory` WHERE owner='22125' and arsenal_klan='{$user[klan]}' and arsenal_owner=1 and type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0)
	  			".$what_not_to_sell." and notsell = 0 AND cost>0  AND `setsale`=0  AND bs_owner = 0 ORDER by `update` DESC");

		} else {
	   		die("Ошибка меню");
		}

		if (!mysql_num_rows($data)) {
			?>
			<tr class="even2"><td colspan=2><table class="table border" border cellspacing="0" cellpadding="0" border="0"> <tbody><tr><td align="center" bgcolor="white">В этом отделе вещи не найдены, выберите другой раздел или измените фильтр.</td></tr></tbody></table>
			<?php
		}


		while($row = mysql_fetch_assoc($data)) {
        		$price = round($row['cost']*0.5,2);
        		$item_type = (int)$row["type"];

			$row['shopprice'] = 0;

			if($item_type <= 11 || $item_type==27 || $item_type==28) {
				$price = round($row['cost']*0.75,2);
			} elseif($item_type == 3) {
				$price = round($row['cost']*0.75,2);
			}

			if($row["prototype"] > 3000 && $row["prototype"] < 3022) {
				$price = round($row['cost']*0.5,2);
				$price += $price*$kk_res[$user['level']];
			}

			if(($shop_skupka==1) or ($row['stavka']>0)) {
	                        if($item_type < 12 || $item_type==28)                   {
					$pr = curr_price($row,$check_sowner);
					$price = $pr['summ'];

					if ($row['stavka'] > 0) {
						$price *= ($row['stavka']/100); // скидка закастована

						if ($row['includemagicuses'] > 0) {
							if ($row['includemagicuses']>150) {
								$price += 150;
							} else {
								$price += $row['includemagicuses'];
							}
						}
					} else {
						$price *= $price_koef;
					}
				}
			}

			if($row["prototype"] >= 410000 && $row["prototype"] <= 410030) {
				$price = 1;
			}

			if (in_array($row['prototype'],$r_excarray) && $row['massa'] == "1.1") {
				$price = 1;
			} else {
				if ($value1[$i]['magic'] > 0 && ($row['type'] == 12 || $row['type'] == 50)) {
					$price = (round($price-$row['duration']*($price/$row['maxdur']),2));
				}
			}


			$row['shopprice'] = $price * 100;

			$inv_shmot[$row['shopprice']][$row['duration']][$row['prototype']][]=$row;
  			$inv_gr_key[$row['prototype']] = $row['group'];
		}

		$allcount = 0;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

		function cmp($a, $b) {
		    if ($a == $b) {
		        return 0;
		    }
		    return ($a < $b) ? -1 : 1;
		}

		function cmp2($a, $b) {
		    if ($a == $b) {
		        return 0;
		    }
		    return ($a > $b) ? -1 : 1;
		}


		if (count($inv_shmot)) {

			if ($_SESSION['shopfilter_cost']) {
				uksort($inv_shmot, "cmp2");
			} else {
				uksort($inv_shmot, "cmp");
			}


			foreach ($inv_shmot as $key2 => $value2) {
		 		foreach ($value2 as $key => $value) {
					foreach ($value as $key1 => $value1) {
				    		if($inv_gr_key[$key1] == 1) {
		                			$group_key = 1;
						} else {
		                			$group_key = count($value1);
						}
						$allcount += $group_key;
					}
				}
			}


			$cpages = ceil($allcount / $_SESSION['shopfilter_view']);

			if ($page > $cpages) $page = 0;

			$pages = "";

			if ($cpages > 1 && !isset($_REQUEST['newsale_ars']))  {
				$pages = '
				<tr class="title">
					<td colspan = 2 class="center">
					Страницы:
				';

				$otdel = "";

				if (isset($_SESSION['shopfilter_otdel'])) {
					$otdel = 'razdel='.$_SESSION['shopfilter_otdel'].'&';
				}

				for ($i = 0; $i < $cpages; $i++) {
					if ($page === $i) {
						$pages .= '<b> '.($i+1).'</b> ';
					} else {
						$pages .= '<a href="?newsale=1&'.$otdel.'page='.$i.'">'.($i+1).'</a> ';
					}
				}

				$pages .= '</td></tr>';

				echo $pages;
			} else {
				echo '<tr class="even2">';
			}
		} else {
			echo '<tr class="even2">';
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
						$count++;

                                            	if ($count > $page*$_SESSION['shopfilter_view'] && $count <= ($page+1)*$_SESSION['shopfilter_view']) {

							if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }

				 			if ($inv_gr_key[$key1] == 1)     {
								$value1[$i][GetShopCount()] =  count($value1);
							} else {
								$value1[$i][GetShopCount()] = 1;
							}

				        		$price = $value1[$i]['shopprice'] / 100;


							if($value1[$i]['add_pick'] != '' && $value1[$i]['pick_time']>time()) {
					            		$value1[$i]['img']=$value1[$i]['add_pick'];
							}

							$look_count++;

							if (!empty($value1[$i]['img_big'])) $value1[$i]['img'] = $value1[$i]['img_big'];

							echo "<TR bgcolor={$color}><TD align=center style='width:150px'><a name=\"{$look_count}\"><IMG SRC=\"http://i.oldbk.com/i/sh/{$value1[$i]['img']}\" BORDER=0><br>";
							echo '<center><small>('.get_item_fid($value1[$i]).')</small></center>';


							if ($_REQUEST['newsale']) {
						 		echo '<A HREF="?sed='.$value1[$i][id].'&page='.$page.'&newsale=1">продать за ';
							} else if ($_REQUEST['newsale_ars']) {
								echo '<A HREF="?sed='.$value1[$i][id].'&page='.$page.'&newsale_ars=1">продать за ';
							}

							echo $price.'</A>';

							if ($value1[$i]['stavka']>0) {
								echo " <small>(Скупка: ".$value1[$i]['stavka']."%)</small>";
							}

							if(($value1[$i]['group']==1) and (!($_REQUEST['newsale_ars']))) {
						        	?>
							        	<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Продать несколько штук" style="cursor: pointer"
							        	onclick="AddCount(event,'<?=$value1[$i]['prototype']?>', '<?=$value1[$i]['name']?>','1','&tmp=<?=$value1[$i]['duration']?>&gift=<?=$key2?>','<?= $value1[$i]['count'] ?>'); return false;">
						        	<?
							}

							echo "</TD><TD valign=top>";
							showitem($value1[$i]);
							echo "</TD></TR>";
						}
					}
				}
   			}
   		}

		if (strlen($pages) && !isset($_REQUEST['newsale_ars'])) echo $pages;
	} else {
 		if (($_GET['otdel']>=100) AND ($user[winstbat]<(int)($_GET['otdel'])  ) AND (in_array($_GET['otdel'],$arr_pril_vel))) {
			$msg = 'У вас еще нет '.$_GET['otdel'].' побед в Великих битвах!';
			$typet = "e";
		} else if (($_GET['otdel']>=100) AND ($user['winstbat']<(int)($_GET['otdel'])  ) AND (!(in_array($_GET['otdel'],$arr_pril_vel)))) {
			$msg = 'Нет такого отдела';
			$typet = "e";
		} else {
			if (($_GET['otdel']>=100) AND ($user[winstbat]>=(int)($_GET['otdel'])  ) AND (in_array($_GET['otdel'],$arr_pril_vel))) {
				$vitrina=" OR  `razdel`>0) AND need_wins='{$_GET['otdel']}' ";
			} else {
  	  			$vitrina=" ) AND need_wins=0 ";
			}

			$addlvl="";

			if ($viewlevel == true) {
				$addlvl=" and nlevel='{$user['level']}' ";
			}

			if (isset($viewclass)) {
				if ($viewclass == -1) {
					$addlvl .=" and nclass='{$user['uclass']}' ";
				} else {
					$addlvl .=" and nclass='".$viewclass."' ";
				}

			}

			$icon_count = 0;

			$data = mysql_query("SELECT * FROM oldbk.`shop` WHERE `".GetShopCount()."` > 0 ".$addlvl." AND ( (`razdel` = '{$_GET['otdel']}' ".$vitrina."  ) ORDER by `nlevel` ASC");
			while($row = mysql_fetch_array($data)) {
			$item_dategoden=$row['dategoden'];

				if ($row['img_big']!='') { $row['img']=$row['img_big']; }
				if ( ((time()>$KO_start_time24) and (time()<$KO_fin_time24))) {
					if (strpos($row['name'], 'Восстановление энергии') !== false || strpos($row['name'], 'свиток «Восстановление') !== false) {
						$row['cost']=$row['cost']*0.8;
						$row['shopicon']=20;
					}
				}

				if (($row['id']==2016002) OR ($row['id']==20180101))  {
					$row['present']='Удача';
				}

				if ($row['id']==122121) {
					$row['present']='Мироздатель';
				}

				if (($row['id'] == 2017101) OR ($row['id'] == 106601))
				 {
					$row['present']='Удача';
				}


				if ($row['id']==2014000) {
					$row['present'] = 'Удача';
					$do = mktime(23,59,59,1,31,2018);
					$row['dategoden'] = $do;
					$row['goden'] = round(($do-time())/(24*3600));
				}
				if ($row['id']==2016001 || $row['id']==2016002) {
					$row['present'] = 'Мироздатель';
					$do = mktime(23,59,59,4,17,2017);
					$row['dategoden'] = $do;
					$row['goden'] = floor(($do-time())/(24*3600));
				}

				if ($item_dategoden>0)
							{
							$row['dategoden'] = $item_dategoden;
							$row['goden'] = floor(($item_dategoden-time())/(24*3600));
							}


				if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
				echo "<TR bgcolor={$color}><TD align=center style='width:150px;";

								if ($banners[$row['shopbanner']]!='')
								{
								$banner=$banners[$row['shopbanner']];
								echo "vertical-align:top;'>";
								echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -55px;top: -11px;\" ".$banner.">";
								$icon_count++;
								}
							else
							if ($row['shopicon']>0)
								{
								echo "vertical-align:top;'>";
								echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -55px;top: -11px;\" src='http://i.oldbk.com/i/icon_s_".(int)($row['shopicon']).".png'>";
								$icon_count++;
								}
								else
								{
								echo "vertical-align:middle;'>";
								}

				echo "<br><a name=\"{$row['id']}\"><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
				?>
					<BR><A HREF="?otdel=<?=$_GET['otdel']?>&set=<?=$row['id']?>">купить</A>
					<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Купить несколько штук" style="cursor: pointer" onclick="AddCount(event,'<?=$row['id']?>', '<?=$row['name']?>','0','','<? echo floor($user['money'] / $row['cost']) ?>'); return false;">
				<?
	 			if (((($row['type']>=1) and ($row['type']<=11)) or ($row['type']==28) ) AND ($row['need_wins']==0)) {
					if ($clan_kazna) { echo '<BR><A HREF="?otdel='.$_GET['otdel'].'&set='.$row['id'].'&set_ars=1">купить в арсенал</A>';  } else { echo ''; }
	   			}
				?>
				</TD>
				<?php

				echo "<TD valign=top>";
				showitem ($row);
				echo "</TD></TR>";
			}
		}
	}
?>
	</TR></TABLE>
                <td style="vertical-align:top;">
		    <form action="?newsale=1" method="POST" id="filter" name="filter">
			<input type="hidden" name="reset" id="reset" value="">

                    <table id="filter" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
                                <strong>Вес всех ваших вещей

				<?php
					echo $d[0];?>/<?=get_meshok()?>
				</strong><br>
                                У Вас в наличии: <span class="money"><strong><?=$user['money']?></strong></span><strong> кр.</strong><br>
				<?php if ($clan_kazna) { echo '<strong>В казне: <span class="money">'.round($clan_kazna['kr'],2).'</span> кр.</strong><br>'; } ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="hint-block size11 center">
                                <span style="color: red">
    					Получить дополнительные кредиты возможно обменяв еврокреды на кредиты в Банке.<br>
					Еврокредиты можно приобрести у любого дилера либо купить в Банке за WMZ.
                                </span>
                            </td>
                        </tr>
			<tr><td align="center">
				<form style="margin:0px;padding:0px;" method="post">
				<? if ($user['align'] != 4) { ?>
					<?php if (isset($_REQUEST['newsale'])) { ?>
						<a class="button-sbig btn" href="?gotoshop">Вернуться в Магазин</a>
					<?php } else { ?>
						<a class="button-big btn" href="?newsale=1">Продать вещи</a>
					<?php }
					?>

					<a class="button-big btn" OnClick="ShowRes();return false;">Продать ресурсы</a>
				<br><a class="button-sbig btn" OnClick="ShowCraftSell();return false;">Продать продукцию</a><? } ?>
				<?
				if ($clan_id['glava'] == $user['id']) {
					if ($clan_kazna) { echo '<br><a href="?newsale_ars=1" class="button-sbig btn">Продать вещи из арсенала</a>'; } else { echo '<small>У Вашего клана нет казны!</small> '; }
				}
				?>
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
                        <?php
			if (isset($_REQUEST['newsale'])) {
				$sale = '&newsale=1';

				$namef = isset($_SESSION['shopfilter_name']) ? $_SESSION['shopfilter_name'] : "";
				$sortf = isset($_SESSION['shopfilter_sort']) ? $_SESSION['shopfilter_sort'] : 0;
				$sortc = isset($_SESSION['shopfilter_cost']) ? $_SESSION['shopfilter_cost'] : 0;
				$llowf = isset($_SESSION['shopfilter_llow']) ? $_SESSION['shopfilter_llow'] : 0;
				$lmaxf = isset($_SESSION['shopfilter_lmax']) ? $_SESSION['shopfilter_lmax'] : 14;
				$viewf = isset($_SESSION['shopfilter_view']) ? $_SESSION['shopfilter_view'] : 50;

			?>

                        <tr>
                            <td class="hint-block center">
                                Воспользуйтесь фильтрами для поиска нужных Вам вещей.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input placeholder="Название предмета..." style="width: 205px;" name="iname" value="<?=$namef?>">
                                <a href="javascript:void(0);" class="button-mid btn" title="Искать" onClick="document.filter.submit();" >Искать</a>
                                <a href="javascript:void(0);" class="button-mid btn" title="Сбросить" onClick="document.getElementById('reset').value='Yes';document.filter.submit();" >Сбросить</a><br>

                            </td>
                        </tr>
                        <tr>
                        <td>Сортировать по: <select name="isort">
				<?php
				foreach ($sorts as $k => $v) {
					if ($k == $sortf) {
						echo '<option selected value="'.$k.'">'.$v.'</option>';
					} else {
						echo '<option value="'.$k.'">'.$v.'</option>';
					}
				}
				?>
			</select>
			</td>
                        </tr>
                        <tr>
                        <td>Цена по: <select name="isortcost">
				<option <?php if ($sortc == 0) echo 'selected' ?> value="0">по возврастанию</option>
				<option <?php if ($sortc == 1) echo 'selected' ?> value="1">по убыванию</option>
			</select>
			</td>
                        </tr>
                        <tr>
				<td>Уровень от
                                <select name="ilevellow" >
				<?php
					$opt = "";
					for ($k=0;$k<=14;$k++) {
						$opt.='<option value="'.$k.'"';
						if ($k==$llowf) $opt.=' selected ';
						$opt.='>'.$k.'</option>';
					}
					echo $opt;
				?>

                                </select> до
                                <select name="ilevelmax">
				<?php
					$opt = "";
					for ($k=14;$k>=1;$k--) {
						$opt.='<option value="'.$k.'"';
						if ($k==$lmaxf) $opt.=' selected ';
						$opt.='>'.$k.'</option>';
					}
					echo $opt;
				?>
                                </select>
                            </td>
                        </tr>
                        <tr>
	                        <td>Выводить по: <select name="iview">
					<?php
					foreach ($viewsf as $k => $v) {
						if ($v == $viewf) {
							echo '<option selected value="'.$v.'">'.$v.'</option>';
						} else {
							echo '<option value="'.$v.'">'.$v.'</option>';
						}
					}
					?>
				</select>
				</td>
			</tr>
			<tr><td><a href="javascript:void(0);" class="button-mid btn" title="Искать" onClick="document.filter.submit();" >Применить</a></td></tr>

			<?php
			} else {
				$sale = '';
			}
			?>
                        <tr>
                            <td class="filter-title">Оружие</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
					<A HREF="?otdel=1<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Кастеты, ножи</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=11<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Топоры</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=12<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Дубины, булавы</A>
                                    </li>
                                    <li>
					<A HREF="?otdel=13<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Мечи</A>
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
					<A HREF="?otdel=2<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Сапоги</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=21<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Перчатки</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=22<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Легкая броня</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=23<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Тяжелая броня</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=24<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Шлемы</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=3<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Щиты</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=4<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Cерьги</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=41<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Ожерелья</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=42<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Кольца</A>
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
					<A HREF="?otdel=5<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Нейтральные</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=51<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Боевые и защитные</A>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=52<?=$sale?>&tmp=<?=mt_rand(1111,9999);?>">Сервисные</a>
                                    </li>
                                    <li>
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
					<?php if (!strlen($sale)) { ?>
                                    <li>
					<A HREF="?otdel=78<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Акции</A>
                                    </li>
					<?php } ?>
																		<li>
													<A HREF="?otdel=82<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Хеллоуин</A>
																		</li>
                                    <li>
					<A HREF="?otdel=55<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Лицензии</A>
                                    </li>
					<?php if (strlen($sale)) { ?>
                                    <li>
					<A HREF="?otdel=6<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Прочее</A>
                                    </li>
					<?php } else { ?>
                                    <li>
					<A HREF="?otdel=6<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Амуниция</A>
                                    </li>
					<?php } ?>

                                    <li>
					<A HREF="?otdel=61<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Еда</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=60<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Молитвенные предметы</A>
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
					<A HREF="?otdel=62<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Ресурсы</A>
                                    </li>

                                    <li>
					<A HREF="?otdel=63<?=$sale?>&tmp=<?echo mt_rand(1111111,9999999);?>">Инструменты</A>
                                    </li>
	                             </ul>
                            </td>
                        </tr>
			<!--
                        <tr>
                            <td class="filter-title">Прилавок Великих</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
				<?php
	 			foreach ($arr_pril_vel as $ke => $va) {
					if ($user['winstbat'] >= $va) {
						echo '
			                                    <li>
			                                        <a HREF="?otdel='.$va.'&tmp='.mt_rand(1111,9999).'">'.$va.' побед</a>
			                                    </li>
						';

					}
	 			}
				?>
                                </ul>
                            </td>
                        </tr>
                        -->
                        </tbody>
                    </table>
                </td>
		</form>
            </tr>
            </tbody>
        </table>
    </div>

<?php
if(!$_SESSION['beginer_quest'][none]) {
	$last_q=check_last_quest(5);
	if($last_q) {
		quest_check_type_5($last_q);
		//проверяем квесты на хар-и
	}

	$last_q = check_last_quest(2);
	if($last_q) {
		quest_check_type_2($last_q);
		//проверяем квесты на хар-и
	}
}

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
<div style="width:300px;" id="hint3" class="ahint"></div>
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
