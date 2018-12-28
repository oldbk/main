<?php
if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
	$miniBB_gzipper_encoding = 'gzip';
}

if (isset($miniBB_gzipper_encoding)) {
	ob_start();
}

session_start();

if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";

if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
if ($_SESSION['boxisopen'] != 'open') { header('location: main.php?edit=1'); die(); }

if (!ADMIN) {
	// если не админ
	if ($user['room'] != 23) {
		header("Location: main.php");
		die();
	}
} else {
	// если админ
	ini_set('display_errors',1);
	error_reporting(E_ERROR);
}

$mf_stat_rate = 1; //сброс МФ и статов за цена*рейт кр
$haos_recharge_50_per = 1;  // перезаряд - используется для временных акций. настраивается под время в нижеследующем конфиге
$close_razdel_mf = 0; // закрыть раздел МФ в $prnt_inf - инфа
$recharge_rate_add = 1; // стоимость перезарядки магии - относительная 100%
$max_ups = 5;
$rune_reset_mf = 10; // стоимость резета мф на рунах в екр
$rune_reset_st = 10; // стоимость резета статов на рунах в екр
$free_rune_reset = 0; // бесплатный сброс рун
$viewperpage = 10; // количество вещей на страницу

// настройки
require_once "config_ko.php";

if(time() > $start_volna && time() < $end_volna) {
	// параметры из config_ko
	$mf_stat_rate=0;  //сброс МФ и статов за цена*рейт к //Ремонтка - сброс статов с оружия (бесплатно)
	$free_rune_reset = 1; // бесплатный сброс рун
}

if ($free_rune_reset) {
	// если бесплатный сброс рун
	$rune_reset_mf = 0;
	$rune_reset_st = 0;
}

// больше раздел МФ не закрываем на скупку
$close_razdel_mf = 0;

// запросы
$query = array();

// мф
$query['mf'] = "
		SELECT SQL_CALC_FOUND_ROWS i.*, sh.cost as shcost FROM oldbk.inventory as i
		INNER JOIN oldbk.shop as sh
		on sh.id = i.prototype
		WHERE (i.type < 12 OR i.type=28 OR i.type=27) AND i.type != 3 and i.ups=0 and (i.art_param is null or i.art_param = '') AND prototype not in (260,262,283,284,946,947,948,949,950,951,952,953,954,955,956,957) AND i.bs_owner = '".$user['in_tower']."'
		AND i.owner = ".$user['id']."
		AND i.dressed = 0 AND
		i.present != 'Арендная лавка' AND
		i.name NOT LIKE '%(мф)%' AND i.name NOT LIKE '%Букет%' and i.naem = 0
		AND i.setsale=0 AND i.prokat_idp=0 AND (i.arsenal_klan = '' OR i.arsenal_owner=1 )
		AND (i.gsila > 0 OR i.glovk > 0 OR i.ginta > 0 OR i.gintel > 0 OR i.mfkrit> 0 OR i.mfakrit > 0 OR
		i.mfuvorot > 0 OR i.mfauvorot > 0 OR i.bron1 > 0 OR i.bron2 > 0 OR i.bron3 > 0 OR i.bron4 > 0 OR i.ghp > 0)
";
// подгон
$query['podgon'] = '
		SELECT SQL_CALC_FOUND_ROWS i . * , sh.cost AS shcost
		FROM oldbk.inventory AS i
		INNER JOIN oldbk.shop AS sh ON sh.id = i.prototype
		WHERE (i.type < 12 OR i.type=28 )  AND i.type != 3 and i.naem = 0
		AND (i.sowner = 0 or i.sowner = '.$user['id'].') AND i.bs_owner = '.$user['in_tower'].' AND i.owner = '.$user['id'].' AND
		i.ups < '.$max_ups.' AND i.dressed = 0 AND i.name LIKE "%(мф)%" AND i.name
		NOT LIKE "%Букет%" AND i.setsale=0 AND (i.arsenal_klan = "" OR i.arsenal_owner=1 ) AND i.present!="Арендная лавка" AND
		(i.mfkrit> 0 OR i.mfakrit > 0 OR i.mfuvorot > 0 OR i.mfauvorot > 0)
';
// рихтовка
$query['riht'] = '
		SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` WHERE bs_owner=0 AND  prototype != 20000 AND prokat_idp = 0 AND present!="Арендная лавка"
		AND `owner` = '.$user['id'].' AND (sowner = 0 or sowner = '.$user['id'].') AND `dressed` = 0 AND (arsenal_klan = "" OR arsenal_owner=1) AND name like "%(мф)%"  AND `setsale`=0 AND LENGTH(mfinfo) > 5
';


// сброс статов-мф
$query['resetms'] = '
	SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` WHERE  (type in (1,2,3,4,5,8,9,10,11,28,27,30,34,35) or (type = 27 and prototype IN (5103,7002))) AND (sowner = 0 or sowner = '.$user['id'].') AND present!="Арендная лавка"
	AND `dressed` = 0 AND `setsale`= 0 AND `owner` = '.$user['id'].' AND bs_owner = 0 AND (arsenal_klan = "" OR arsenal_owner=1)
	AND prototype not in (20000,55510000)
	AND (name NOT LIKE "%[%" or prototype in (260,262,283))

';

$query['repair'] = '
	SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` USE INDEX (owner_5) WHERE owner = '.$user['id'].' AND setsale=0 AND bs_owner = 0 AND duration > 0 AND (sowner = 0 or sowner = '.$user['id'].') and (isrep = 1 or (getfrom = 1 and dategoden > 0 and type < 12 and gold > 0))
';

$query['recharge'] = '
	SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` USE INDEX (owner_5) WHERE owner = '.$user['id'].' AND includemagicmax > 0 and includemagicuses > 0
	AND (sowner = 0 or sowner = '.$user['id'].') AND includemagicdex = 0 AND setsale = 0 AND bs_owner = 0
';

// обмен вещей

$query['exchange'] = '
	SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` WHERE  type in (1,2,3,4,5,8,9,10,11)  AND bs_owner = 0 AND present != "Арендная лавка"  AND  `owner` = '.$user['id'].'
	and naem = 0 AND `dressed` = 0  AND `setsale`=0 AND `prototype` not in (2000,2001,2002,2003,260,262,284,283,100028,100029,195195,173173) and (prototype<18000 OR prototype >19000) and  name like "%[%" and up_level>=7 and ( ISNULL(art_param) OR art_param="") and prokat_idp = 0
';

$query['exchange2'] = '
	SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` WHERE  type in (1,2,4,5,8,9,10,11)  AND bs_owner = 0 AND present != "Арендная лавка"  AND  `owner` = '.$user['id'].'
	and naem = 0 AND `dressed` = 0  AND `setsale`=0 AND `prototype` not in (2000,2001,2002,2003,260,262,284,283,100028,100029,195195,173173) and (prototype<40000 OR prototype >41000) and  name not like "%[%" and nlevel >= 8 and ( ISNULL(art_param) OR art_param="") and getfrom != 1 and prokat_idp = 0 and unik = 0
';

/*
$query['exchange2'] = '
	SELECT SQL_CALC_FOUND_ROWS * FROM oldbk.`inventory` WHERE  type in (1,2,4,5,8,9,10,11)  AND bs_owner = 0 AND present != "Арендная лавка"  AND  `owner` = '.$user['id'].'
	AND `dressed` = 0  AND `setsale`=0 AND `prototype` not in (2000,2001,2002,2003,260,262,284,283,100028,100029,195195,173173) and  name not like "%[%" and nlevel >= 8 and ( ISNULL(art_param) OR art_param="") and getfrom != 1 and prokat_idp = 0 and unik = 0
';
*/

// универсальная банковская авторизация
if (isset($_GET['view'],$_GET['link']) && $_GET['view'] == "bankauth") {
	if (!isset($_GET['type'])) $_GET['type'] = 0;
	$error = 0;
	if (isset($_GET['bankpass'],$_GET['bankid'])) {
		$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.intval($_GET['bankid']).' and pass = "'.md5($_GET['bankpass']).'"');
		if (mysql_num_rows($q) > 0) {
			$_SESSION['bankid'] = intval($_GET['bankid']);
			if (isset($_GET['type']) && intval($_GET['type']) == 1) {
				$bank = mysql_fetch_assoc($q);
				echo '<script>closeinfo();bankauth = true;bankbalance = '.$bank['ekr'].';CalculateRepair();</script>';
			} elseif (isset($_GET['type']) && intval($_GET['type']) == 2) {
				$bank = mysql_fetch_assoc($q);
				echo '<script>closeinfo();bankauth = true;bankbalance = '.$bank['ekr'].';CalculateRecharge();</script>';
			} else {
				$_GET['link'] = str_ireplace('://',"",$_GET['link']);
				echo '<script>closeinfo();bankauth = true;location.href="'.$_GET['link'].'";</script>';
			}
			die();
		} else {
			$error = 1;
		}
	}

	$auth =  '<table border=0 width=400 height=100><tr><td  valign=top align="center" height=5 colspan="4"><font style="COLOR:#8f0000;FONT-SIZE:12pt">';
	$auth .= "Авторизация в банке";
	$auth .= '</font><a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
	<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-20px;right:-220px;" border=0 title="Закрыть"></a></td></tr>
	<tr><td colspan="4" class="center" valign=top>';


	$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id']);
	if (mysql_num_rows($q) > 0) {
		$auth .= '<select id="bankid" style="width:100px" name="bankid">';
		while ($rah = mysql_fetch_array($q)) {
			$auth .= "<option>".$rah['id']."</option>";
		}
		$auth .= "</select> ";
		$auth .= 'Пароль: <input type=password name="bankpass" id="bankpass" style="width:100px"  OnKeyup="if (event.keyCode===13) { doauth(\''.$_GET['link'].'\','.intval($_GET['type']).'); } ;"  > <button style="height:23px;" OnClick="doauth(\''.$_GET['link'].'\','.intval($_GET['type']).');">Войти</button>';

	} else {
		$auth .= '<font color="red">Банковские счета не найдены</font>';
	}

	if ($error > 0) {
		$auth .= '<br><font color="red">Не правильный пароль</font>';
	}

	$auth .= '</td>
		</tr><tr><td align="center"  colspan="3">
		</td></tr>
		</table>';
	$auth .='<script> document.getElementById("bankpass").focus(); </script>';
	echo $auth;
	die();
}

// функции
function percent($a, $b) {
	$c = $b/$a*100;
	return $c;
}

function renderrepitem($row,$act,&$i) {
	$out = "";
	$row[GetShopCount()] = 1;
	if ($i == 0) { $i = 1; $class = 'even2';} else { $i = 0; $class = 'odd2'; }

	$out .= '<tr class="'.$class.'">
			<td class="center vamiddle">
				<ul class="dress-item">
					<li>
						<IMG SRC="http://i.oldbk.com/i/sh/'.$row['img'].'">
					</li>
					<li>
	'.$act.'</li>';

	$out .= '</ul></td><td colspan="7">';
	$out .= showitem($row,0,false,'','',0,0,true);
	$out .= '</td></tr>';
	return $out;
}

function SaveMFLOG($id,$itemid,$unik) {
	$fp = fopen('/www/other/mf.log','a+b');
	if ($fp) {
		if (flock($fp, LOCK_EX)) {
			fwrite($fp,time().":".$id.":".$itemid.":".$unik."\r\n");
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
}

function SaveCHLOG($dd) {
	$fp = fopen('/www/other/chitems.txt','a+b');
	if ($fp) {
		if (flock($fp, LOCK_EX)) {
			fwrite($fp,time().":".$dd."\r\n");
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
}

function get_chanse ($persent) {
	if($persent > 99) { $persent = 99; };
	$mm = 1000000;
	return (mt_rand($mm, 100 * $mm) <= $persent*$mm);
}


function get_free_stats_up($intel,$proto) {
	return 2;
	if ($proto == 7002 || $proto == 5102) {
		if (mt_rand(0,1500) < ($intel / 10)) {
			return 3;
		}
	} else {
		if (mt_rand(0,1000) < ($intel / 10)) {
			return 3;
		}
	}

	$chance = $intel / 25;
	$range = round($chance);

	if($range < 3) {
		if(get_chanse($chance)) {
			return 2;
		} else {
			return 1;
		}
	} else {
		return 2;
	}
}

function ResetRune($item,$type = 1) {
	// 1 - mf
	// 2 - stats
	global $runs_exp_table;
	if (!isset($runs_exp_table)) die();
	if ($item) {
		$q = mysql_query('SELECT * FROM oldbk.cshop WHERE id = '.$item['prototype']);
		if ($q === FALSE) die();
		$proto = mysql_fetch_assoc($q);
		if ($proto) {
			$sql = "";

			if ($type == 2) {
				if ($proto['gsila'] > 0) $sql .= "gsila = ".$proto['gsila'].",";
				if ($proto['glovk'] > 0) $sql .= "glovk = ".$proto['glovk'].",";
				if ($proto['ginta'] > 0) $sql .= "ginta = ".$proto['ginta'].",";
				if ($proto['gintel'] > 0) $sql .= "gintel = ".$proto['gintel'].",";
				if ($proto['gmp'] > 0) $sql .= "gmp = ".$proto['gmp'].",";
			}

			if ($type == 1) {
				if ($proto['mfkrit'] > 0) $sql .= "mfkrit = ".$proto['mfkrit'].",";
				if ($proto['mfakrit'] > 0) $sql .= "mfakrit = ".$proto['mfakrit'].",";
				if ($proto['mfuvorot'] > 0) $sql .= "mfuvorot = ".$proto['mfuvorot'].",";
				if ($proto['mfauvorot'] > 0) $sql .= "mfauvorot = ".$proto['mfauvorot'].",";
			}

			if (strlen($sql)) {
				if ($type == 1) {
					mysql_query('UPDATE oldbk.inventory SET mfkrit = 0, mfakrit = 0, mfuvorot = 0, mfauvorot = 0 WHERE id = '.$item['id'].' LIMIT 1') or die();
				}
				if ($type == 2) {
					mysql_query('UPDATE oldbk.inventory SET gsila = 0, glovk = 0, ginta = 0, gintel = 0, gmp = 0 WHERE id = '.$item['id'].' LIMIT 1') or die();
				}
				$sql = substr($sql,0,strlen($sql)-1);
				mysql_query('UPDATE oldbk.inventory SET '.$sql.' WHERE id = '.$item['id'].' LIMIT 1') or die();

				$sql = "";
				$stbonus = 0;
				$mfbonus = 0;
				$gintel = 0;
				$gmp = 0;

				for ($i = 0; $i <= $item['up_level']; $i++) {
					if ($type == 1) {
						if ($runs_exp_table[$i]["mfbonus"] > 0) $mfbonus += $runs_exp_table[$i]["mfbonus"];
					}

					if ($type == 2) {
						if ($runs_exp_table[$i]["stbonus"] > 0) $stbonus += $runs_exp_table[$i]["stbonus"];
						if ($runs_exp_table[$i]["gintel"] > 0) $gintel += $runs_exp_table[$i]["gintel"];
						if ($runs_exp_table[$i]["gmp"] > 0) $gmp += $runs_exp_table[$i]["gmp"];
					}
				}

				if ($type == 1) {
					$sql .= "mfbonus = ".$mfbonus.",";
				}
				if ($type == 2) {
					$sql .= "stbonus = ".$stbonus.",";
					if ($gintel > 0) $sql .= "gintel = gintel + ".$gintel.",";
					if ($gmp > 0) $sql .= "gmp = gmp + ".$gmp.",";
				}

				$sql = substr($sql,0,strlen($sql)-1);
				mysql_query('UPDATE oldbk.inventory SET '.$sql.' WHERE id = '.$item['id'].' LIMIT 1') or die();
			} else die();

		} else die();
	}

}


function ResetSpecRune($item,$type = 1) {
	// 1 - mf
	// 2 - stats
	if ($item) {
		$q = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.$item['prototype']);
		if ($q === FALSE) die();
		$proto = mysql_fetch_assoc($q);
		if ($proto) {
			$sql = "";

			if ($type == 2) {
				if ($proto['gsila'] > 0) $sql .= "gsila = ".$proto['gsila'].",";
				if ($proto['glovk'] > 0) $sql .= "glovk = ".$proto['glovk'].",";
				if ($proto['ginta'] > 0) $sql .= "ginta = ".$proto['ginta'].",";
				if ($proto['gintel'] > 0) $sql .= "gintel = ".$proto['gintel'].",";
				if ($proto['gmp'] > 0) $sql .= "gmp = ".$proto['gmp'].",";
			}

			if ($type == 1) {
				if ($proto['mfkrit'] > 0) $sql .= "mfkrit = ".$proto['mfkrit'].",";
				if ($proto['mfakrit'] > 0) $sql .= "mfakrit = ".$proto['mfakrit'].",";
				if ($proto['mfuvorot'] > 0) $sql .= "mfuvorot = ".$proto['mfuvorot'].",";
				if ($proto['mfauvorot'] > 0) $sql .= "mfauvorot = ".$proto['mfauvorot'].",";
			}

			if (strlen($sql)) {
				if ($type == 1) {
					mysql_query('UPDATE oldbk.inventory SET mfkrit = 0, mfakrit = 0, mfuvorot = 0, mfauvorot = 0 WHERE id = '.$item['id'].' LIMIT 1') or die();
				}
				if ($type == 2) {
					mysql_query('UPDATE oldbk.inventory SET gsila = 0, glovk = 0, ginta = 0, gintel = 0, gmp = 0 WHERE id = '.$item['id'].' LIMIT 1') or die();
				}
				$sql = substr($sql,0,strlen($sql)-1);
				mysql_query('UPDATE oldbk.inventory SET '.$sql.' WHERE id = '.$item['id'].' LIMIT 1') or die();

				$sql = "";
				$stbonus = $proto['stbonus'];
				$mfbonus = $proto['mfbonus'];
				$gintel = $proto['gintel'];
				$gmp = $proto['gmp'];

				if ($type == 1) {
					$sql .= "mfbonus = ".$mfbonus.",";
				}
				if ($type == 2) {
					$sql .= "stbonus = ".$stbonus.",";
					if ($gintel > 0) $sql .= "gintel = gintel + ".$gintel.",";
					if ($gmp > 0) $sql .= "gmp = gmp + ".$gmp.",";
				}

				$sql = substr($sql,0,strlen($sql)-1);
				mysql_query('UPDATE oldbk.inventory SET '.$sql.' WHERE id = '.$item['id'].' LIMIT 1') or die();
			} else die();

		} else die();
	}

}


function SetMsg($msg,$typet = "s") {
	$_SESSION['repairmsg'] = $msg;
	$_SESSION['repairmsgtype'] = $typet;
}

function Redirect($path = "?") {
	header("Location: ".$path);
	die();
}

function RepairItem($row,$user,$count,$bank) {
	$arr = GetRepairPrice($row,$user);

	if ($count == "full") $count = $row['duration'];

	if ($count < 1 ) $count = 1; // fix

	if ($count > $row['duration']) $count = $row['duration']; // на всякие

	if ($arr['type'] == 2 && (!isset($_SESSION['bankid']) || !$_SESSION['bankid'])) {
		return 1;
	}

	// костыль для букетов
	$sql_bukets='';
	if ((($row['prototype'] >= 410021) AND ($row['prototype'] <= 410026)) and $row['ekr_flag'] != 0) {
		$sql_bukets=", ekr_flag=0, present='Удача' ";
	}

	$price = $arr['onecost'] * $count;
	$arr['price'] = $price;

	if (($arr['type'] == 1 && $user['money'] < $price) || ($arr['type'] == 2 && $bank['ekr'] < $price) || ($arr['type'] == 4 && $user['gold'] < $price)) {
		return 2;
	}

	mysql_query("UPDATE oldbk.`inventory` SET `duration` = `duration`-".$count." {$sql_bukets}  WHERE `id` = ".$row['id'].' LIMIT 1');
	if ($arr['type'] == 1) {
		mysql_query("UPDATE `users` set `money` = money-'".$price."' WHERE id = ".$user['id'].' LIMIT 1');
	}
	if ($arr['type'] == 2) {
		mysqL_query("UPDATE oldbk.`bank` set `ekr` = ekr-'".$price."' WHERE id = ".$bank['id'].' LIMIT 1');
	}
	if ($arr['type'] == 4) {
		mysql_query("UPDATE `users` set `gold` = gold-'".$price."' WHERE id = ".$user['id'].' LIMIT 1');
	}


	$newduration = $row['duration']-$count;

	$rec['owner']=$user['id'];
	$rec['owner_login']=$user['login'];
	$rec['owner_balans_do']=$user['money'];
	if ($arr['type'] == 1) {
		$user['money'] -= $price;
	}
	if ($arr['type'] == 4) {
		$user['gold'] -= $price;
	}
	$rec['owner_balans_posle']=$user['money'];
	$rec['target']=0;
	$rec['target_login']="Ремонтная мастерская";
	$rec['type']=191;//Ремонт предмета
	if ($arr['type'] == 1) {
		$rec['sum_kr'] = $price;
		$rec['sum_ekr'] = 0;
	}
	if ($arr['type'] == 2) {
		$rec['sum_kr']=0;
		$rec['sum_ekr']=$price;
	}
	if ($arr['type'] == 4) {
		$rec['sum_kr']=0;
		$rec['sum_ekr']=0;
		$rec['add_info'] = $price.'/'.$user['gold'];
	}
	$rec['sum_kom']=0;
	$rec['item_id']=get_item_fid($row);
	$rec['item_name']=$row['name'];
	$rec['item_count']=1;
	$rec['item_type']=$row['type'];
	$rec['item_cost']=$row['cost'];
	$rec['item_dur']=$newduration;
	$rec['item_maxdur']=$row['maxdur'];
	$rec['item_ups']=$row['ups'];
	$rec['item_unic']=$row['unik'];
	$rec['item_incmagic']=$row['includemagicname'];
	$rec['item_incmagic_count']=$row['includemagicuses'];
	$rec['item_arsenal']=$row['arsenal_klan'];

	if ($arr['type'] == 2) {
		$qb = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.bank WHERE id = '.$bank['id']));
		$rec['add_info'] = 'Баланс до '.($qb['ekr']+$price). ' после ' .$qb['ekr'];
		mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Вы отремонтировали вещь за ".$price." екр.</b>, <i>(Итого: {$qb['cr']} кр., {$qb['ekr']} екр.)</i>','{$bank['id']}');");
	}
	add_to_new_delo($rec);

	if($row['naem'] == 0 && (mt_rand(0,9) < 2 || $row['duration'] >= $row['maxdur'] || $arr['type'] == 4)) {
		mysql_query("UPDATE oldbk.`inventory` SET `maxdur` = `maxdur` - 1 WHERE `id` = ".$row['id'].' LIMIT 1');
		$arr['maxdur'] = 1;
	}
	return $arr;
}


function GetRepairPrice($row,$user) {
	$type = 1; // тип починки 0 - ничего, 1 - кр, 2 - екр, 4 - монеты
	$onecost = 0.1; // цена одной починки
	$typetxt = "кр.";

	if ($row['prokat_idp']> 0 || ($row['prototype'] >= 55510301 && $row['prototype'] <= 55510344 && (strpos($row['name'],'(екр)') !== FALSE || strpos($row['name'],'(арт)') !== FALSE))) {
		$type = 2;
		$onecost = 0.02;
		$typetxt = "екр.";
	}

	if ($row['ab_uron'] > 0 || $row['ab_bron'] > 0 || $row['ab_mf'] > 0 || $row['art_param'] != "") {
		$onecost = 1;
		//if ($row['sowner'] > 0) {
			if ($user['prem'] == 1) {
				$onecost = $onecost - ($onecost*0.05);
			}
			if ($user['prem'] == 2) {
				$onecost = $onecost - ($onecost*0.10);
			}
			if ($user['prem'] == 3) {
				$onecost = $onecost - ($onecost*0.15);
			}
		//}
	}

	if (!in_array($row['prototype'],[55510350,55510351,55510352]) && $row['getfrom'] == 1 && $row['dategoden'] > 0 && $row['type'] < 12 && $row['gold'] > 0 && strpos($row['name'],'Букет ') === false) {
		$type = 4;
		$typetxt = '<img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom: -2px;">';
		$onecost = floor($row['gold'] / $row['maxdur']);
		if ($onecost <= 1) $onecost = 1;
	}

	return array('type' => $type, 'onecost' => round($onecost,2), 'typetxt' => $typetxt);

}


function MakeRechargeQuery($json,$query) {
	$query = $query['recharge'];

	if (!isset($json->item_dressed) && !isset($json->item_inventory) && !isset($json->item_naem)) {
		return false;
	} elseif (isset($json->item_dressed) && isset($json->item_inventory) && isset($json->item_naem)) {
		// ничего не добавляем
	} else {
		$query .= 'AND (';
		if (isset($json->item_dressed)) $query .= ' dressed = 1 OR';
		if (isset($json->item_inventory)) $query .= ' dressed = 0 OR';
		if (isset($json->item_naem)) $query .= ' dressed = 2 OR';
		$query = substr($query,0,strlen($query)-3);
		$query .= ')';
	}

	if (!isset($json->item_kr) && !isset($json->item_ekr) && !isset($json->item_rep)) {
		return false;
	}

	$query .= ' and (';

	if (isset($json->item_rep)) {
		$query .= ' (includerechargetype = 3) or';
	}

	if (isset($json->item_ekr)) {
		$query .= ' (includemagicekrcost > 0 or includerechargetype = 2) or';
	}

	if (isset($json->item_kr)) {
		$query .= ' ((includemagiccost > 0 and includemagicekrcost = 0) or includerechargetype = 1) or';
	}

	$query = substr($query,0,-2);

	$query .= ')';

	return $query;
}

function MakeRepairQuery($json,$query) {
	$query = $query['repair'];

	if (!isset($json->item_dressed) && !isset($json->item_inventory) && !isset($json->item_naem)) {
		return false;
	} elseif (isset($json->item_dressed) && isset($json->item_inventory) && isset($json->item_naem)) {
		// ничего не добавляем
	} else {
		$query .= 'AND (';
		if (isset($json->item_dressed)) $query .= ' dressed = 1 OR';
		if (isset($json->item_inventory)) $query .= ' dressed = 0 OR';
		if (isset($json->item_naem)) $query .= ' dressed = 2 OR';
		$query = substr($query,0,strlen($query)-3);
		$query .= ')';
	}

	if (isset($json->item_critical)) {
		$query .= ' AND duration >= maxdur - 2';
	}

	if (isset($json->item_art)) {
		$query .= ' AND NOT ((art_param = "" or art_param IS NULL) AND inventory.ab_uron = 0 AND inventory.ab_mf = 0 AND inventory.ab_bron = 0)';
	} elseif (isset($json->item_aeart)) {
		$query .= ' AND (art_param = "" or art_param IS NULL) AND inventory.ab_uron = 0 AND inventory.ab_mf = 0 AND inventory.ab_bron = 0 AND NOT (getfrom = 1 and dategoden > 0 and gold > 0 and type < 12 and prototype not in (55510350,55510351,55510352))';
	} elseif (isset($json->item_prokat)) {
		$query .= ' AND prokat_idp > 0';
 	} elseif (isset($json->item_fair)) {
		$query .= ' AND (getfrom = 1 and dategoden > 0 and gold > 0 and type < 12) ';
	}


	return $query;
}


$bonusrecharge = false;

function CalculateRechargePrice($row,$user) {
	global $bonusrecharge, $haos_recharge_50_per, $recharge_rate_add;

	$ret = array('price' => 0, 'type' => 0, 'typetxt' => ""); // 1 кр, 2 екр, 3 репа

	// бонусы на перезаряд, например с арены
	if ($bonusrecharge === false) {
		$get_bonus = mysql_query('select * from bonus_items where owner = '.$user['id']);
		if (mysql_num_rows($get_bonus) > 0) {
			while($ff = mysql_fetch_array($get_bonus)) {
				if($ff['flag'] == 1 && $ff['finish'] > time()) {
					$bonusrecharge[$ff['item_id']] = 1;
				} elseif($ff['flag'] == 2 && $ff['finish'] > time()) {
					$bonusrecharge[$ff['item_id']] = 2;
				}
			}
		} else {
			$bonusrecharge = true;
		}

	}

	if (isset($bonusrecharge[$row['id']]) && $bonusrecharge[$row['id']] == 1) {
		$recharge_rate = $recharge_rate_add * 0.5;
	} elseif (isset($bonusrecharge[$row['id']]) && $bonusrecharge[$row['id']] == 2) {
		$recharge_rate = 0;
	} else {
		$recharge_rate = $recharge_rate_add * 1;
	}

	if($haos_recharge_50_per > 0) {
		$recharge_rate = $recharge_rate * $haos_recharge_50_per;
	}

	if ($row['includerechargetype'] > 0 && $row['includeprototype'] > 0) {
		// новый перезаряд магии, ищем прото магии
		if ($row['includerechargetype'] == 1) {
			$proto = mysql_query_cache('SELECT * FROM oldbk.shop WHERE id = '.$row['includeprototype'],false,300);
			$pname = "cost";
			$ret['type'] = 1;
			$ret['typetxt'] = "кр.";
		} elseif ($row['includerechargetype'] == 2) {
			$proto = mysql_query_cache('SELECT * FROM oldbk.eshop WHERE id = '.$row['includeprototype'],false,300);
			$pname = "ecost";
			$ret['type'] = 2;
			$ret['typetxt'] = "екр.";
		} elseif ($row['includerechargetype'] == 3) {
			$proto = mysql_query_cache('SELECT * FROM oldbk.cshop WHERE id = '.$row['includeprototype'],false,300);
			$pname = "repcost";
			$ret['type'] = 3;
			$ret['typetxt'] = "реп.";
		} else {
			die();
		}

		if (!count($proto)) die();
		$proto = $proto[0];

		$ret['price'] = round($proto[$pname]*0.5*$recharge_rate,2);
	} else {
		if ($row['includemagicekrcost']*$recharge_rate > 0 || ((isset($bonusrecharge[$row['id']]) && $bonusrecharge[$row['id']] == 2) && $row['includemagicekrcost'] > 0)) {
			$ret['price'] = round($row['includemagicekrcost']*$recharge_rate,2);
			$ret['type'] = 2;
			$ret['typetxt'] = "екр.";
		} else {
			$ret['type'] = 1;
			$ret['typetxt'] = "кр.";
			$ret['price'] = round($row['includemagiccost']*$recharge_rate,2);
		}
	}
	return $ret;
}


function RechargeItem($row,$user,$bank) {
	$ret = CalculateRechargePrice($row,$user);
	if ($ret['type'] == 1) {
		if ($user['money'] < $ret['price']) {
			return 2;
		}
	} elseif ($ret['type'] == 2) {
		if (!isset($_SESSION['bankid']) || !$_SESSION['bankid']) {
			return 1;
		}
		if ($bank['ekr'] < $ret['price']) {
			return 2;
		}
	} elseif ($ret['type'] == 3) {
		if ($user['repmoney'] < $ret['price']) {
			return 3;
		}
	} else {
		die();
	}

	if($row['includemagicuses'] >= 1) {
		mysql_query('UPDATE oldbk.`inventory` SET `includemagicdex` = `includemagicmax`, `includemagicuses` = `includemagicuses` - 1 WHERE `id` = '.$row['id'].' LIMIT 1');

		$dressid=get_item_fid($row);
		$rec['owner']=$user['id'];
		$rec['owner_login']=$user['login'];
		$rec['owner_balans_do']=$user['money'];
		$rec['owner_balans_posle']=$user['money'];
		$rec['owner_rep_do']=$user['repmoney'];
		$rec['owner_rep_posle']=$user['repmoney'];
		$rec['target']=0;
		$rec['target_login'] = "Ремонтная мастерская";
		$rec['sum_ekr'] = 0;
		$rec['sum_kr'] = 0;
		$rec['sum_rep'] = 0;
		$rec['item_id']=$dressid;
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
		$rec['item_arsenal']=$row['arsenal_klan'];


		if ($ret['type'] == 1) {
			mysql_query('UPDATE `users` set `money` = `money`- '.$ret['price'].' WHERE id = '.$user['id'].' LIMIT 1');

			$rec['type'] = 198;//перезарядка КР
			$rec['sum_kr']=$ret['price'];
			$rec['owner_balans_posle'] = $user['money'] - $ret['price'];
		} elseif($ret['type'] == 2) {
			mysql_query('UPDATE oldbk.`bank` set `ekr` = `ekr`- '.$ret['price'].' WHERE id = '.$bank['id'].' LIMIT 1');
			$qb = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.bank WHERE id = '.$bank['id']));
			$rec['add_info'] = 'Баланс до '.($qb['ekr']+$ret['price']).' екр после '.$qb['ekr'].' екр.';

			$rec['sum_ekr'] = $ret['price'];
			$rec['type'] = 197;//перезарядка ЕКР
		} elseif ($ret['type'] == 3) {
			mysql_query('UPDATE `users` set `repmoney` = `repmoney`- '.$ret['price'].' WHERE id = '.$user['id'].' LIMIT 1');

			$rec['type'] = 199;//перезарядка репутации
			$rec['owner_rep_posle'] = $user['repmoney'] - $ret['price'];
			$rec['sum_rep'] = $ret['price'];
		} else {
			die();
		}

		add_to_new_delo($rec);
		return $ret;
	} else {
		return 4;
	}
}

function MakePages($allcount = 0, $nocheck = 0) {
	global $viewperpage,$msg,$typet;
	$view = $viewperpage;

	if (!$allcount && !$nocheck) {
		$q2 = mysql_query('SELECT FOUND_ROWS() AS `allcount`') or die();
		$allcount = mysql_fetch_assoc($q2);
		$allcount = $allcount['allcount'];
	}

	$cpages = ceil($allcount/$view);

	$page = $_SESSION['reppage'.$_SESSION['reprazdel']];

	if ($page >= $cpages && $page > 0 && $page !== "all") {
		$_SESSION['reppage'.$_SESSION['reprazdel']] = intval($cpages-1);
		if ($_SESSION['reppage'.$_SESSION['reprazdel']] < 0) $_SESSION['reppage'.$_SESSION['reprazdel']] = 0;
		SetMsg($msg,$typet);
		Redirect();
	}

	if ($cpages <= 1) return false;

	$pages = 'Страницы: ';
	for ($i = 0; $i < $cpages; $i++) {
		if ($page === $i) {
			$pages .= '<b> '.($i+1).'</b> ';
		} else {
			$pages .= '<a href="?razdel='.$_SESSION['reprazdel'].'&page='.$i.'">'.($i+1).'</a> ';
		}
	}

	if ($page === "all") {
		$pages .= '<b>[всё]</b> ';
	} else {
		$pages .= '[<a href="?razdel='.$_SESSION['reprazdel'].'&page=all">всё</a>] ';
	}

	return $pages;
}

function MakeLimit() {
	global $viewperpage;

	if ($_SESSION['reppage'.$_SESSION['reprazdel']] === "all") return "";

	return ' LIMIT '.($viewperpage*$_SESSION['reppage'.$_SESSION['reprazdel']]).','.$viewperpage;
}


if (isset($_SESSION['repairmsg'])) {
	$msg = $_SESSION['repairmsg'];
	$typet = $_SESSION['repairmsgtype'];
	unset($_SESSION['repairmsg']);
	unset($_SESSION['repairmsgtype']);
}

// банк
if (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) {
	$bank = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = ".$_SESSION['bankid'].";"));
} else {
	$bank = false;
}

// раздел
if (!isset($_SESSION['reprazdel'])) $_SESSION['reprazdel'] = 0;
if (isset($_GET['razdel'])) $_GET['razdel'] = intval($_GET['razdel']);
if (isset($_GET['razdel']) && $_GET['razdel'] >= 0 && $_GET['razdel'] <= 7) $_SESSION['reprazdel'] = $_GET['razdel'];

// одетые-раздетые
if (!isset($_SESSION['repdressed'])) $_SESSION['repdressed'] = 1;
if (isset($_GET['dressed'])) $_GET['dressed'] = intval($_GET['dressed']);
if (isset($_GET['dressed']) && $_GET['dressed'] >= 0 && $_GET['dressed'] <= 2) $_SESSION['repdressed'] = $_GET['dressed'];

// страницы
if (!isset($_SESSION['reppage'.$_SESSION['reprazdel']])) $_SESSION['reppage'.$_SESSION['reprazdel']] = 0;
if (isset($_GET['page'])) {
	if ($_GET['page'] !== "all") {
		$_GET['page'] = intval($_GET['page']);
	}
	$_SESSION['reppage'.$_SESSION['reprazdel']] = $_GET['page'];
}

function GetItemPrototype($proto) {
	$res = mysql_query_cache("SELECT * FROM shop WHERE id = ".$proto,false,60*60);
	if (!count($res)) {
		$res = mysql_query_cache("SELECT * FROM eshop WHERE id = ".$proto,false,60*60);
	}
	if (!count($res)) {
		$res = mysql_query_cache("SELECT * FROM cshop WHERE id = ".$proto,false,60*60);
	}
	return $res[0];
}

// обработка событий
switch($_SESSION['reprazdel']) {
	case 0:
		if (isset($_GET['dorepair'])) {
			$_GET['dorepair'] = str_replace('&quot;','"',$_GET['dorepair']);
			$json = json_decode($_GET['dorepair']);
			if (!$json) {
				SetMsg("Ошибка починки","e");
				Redirect();
			}

			$query = MakeRepairQuery($json,$query);

			if ($query === false) {
				Redirect();
			}

			$data = mysql_query($query);

			$kr = 0;
			$ekr = 0;
			$gold = 0;
			$i = 0;
			$durbroken = 0;
			while($row = mysql_fetch_assoc($data)) {
				$ret = RepairItem($row,$user,"full",$bank);
				if (is_array($ret)) {
					$i++;
					if ($ret['type'] == 1) {
						$kr += $ret['price'];
						$user['money'] -= $ret['price'];
					}
					if ($ret['type'] == 2) {
						$ekr += $ret['price'];
						$bank['ekr'] -= $ret['price'];
					}
					if ($ret['type'] == 4) {
						$gold += $ret['price'];
						$user['gold'] -= $ret['price'];
					}
					if (isset($ret['maxdur'])) {
						$durbroken++;
					}
				}
			}

			if ($i > 0) {
				$txt = 'Успешно отремонтировано '.declOfNum($i,array("предмет","предмета","предметов")).' на сумму ';
				if ($kr > 0) $txt .= '<b>'.$kr.'</b> кр. ';
				if ($ekr > 0) $txt .= '<b>'.$ekr.'</b> екр. ';
				if ($gold > 0) $txt .= '<b>'.$gold.'</b> <img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom: -2px;">. ';
				if ($durbroken > 0) $txt .= 'К сожалению, максимальная долговечность '.declOfNum($durbroken,array("предмета","предметов","предметов")).' из-за ремонта уменьшилась.';
				SetMSg($txt);
			} else {
				SetMsg("Починка не требуется или невозможна","e");
			}

			Redirect();
		}
		if (isset($_GET['calcrepair'])) {
			$_GET['calcrepair'] = str_replace('&quot;','"',$_GET['calcrepair']);
			$json = json_decode($_GET['calcrepair']);
			if (!$json) die("Ошибка расчёта");

			$query = MakeRepairQuery($json,$query);

			if ($query === false) {
				$ret = array('ekr' => 0, 'kr' => 0, 'gold' => 0);
				die(json_encode($ret));
			}

			$data = mysql_query($query);
			$ret = array('kr' => 0, 'ekr' => 0, 'gold' => 0);

			while($row = mysql_fetch_assoc($data)) {
	                        $arr = GetRepairPrice($row,$user);
				if ($arr['type'] == 1) {
					$ret['kr'] += $arr['onecost']*$row['duration'];
				}
				if ($arr['type'] == 2) {
					$ret['ekr'] += $arr['onecost']*$row['duration'];
				}
				if ($arr['type'] == 4) {
					$ret['gold'] += $arr['onecost']*$row['duration'];
				}
			}
			die(json_encode($ret));
		}
		if (isset($_GET['repair'])) {
			// починка
			$data = mysql_query($query['repair'].' and id = '.intval($_GET['repair']));
			if (mysql_num_rows($data) > 0) {
				$data = mysql_fetch_assoc($data);
				if (!$data) Redirect();

				if (!isset($_GET['count'])) $_GET['count'] = 1;

				$ret = RepairItem($data,$user,$_GET['count'],$bank);
				if ($ret == 1) {
					SetMsg("Не выполнен вход в банк","e");
				} elseif ($ret == 2) {
					SetMsg("Недостаточно денег для починки вещи","e");
				} elseif (is_array($ret)) {
					$txt = 'Произведен ремонт предмета &quot;'.$data['name'].'&quot; за '.$ret['price'].' '.$ret['typetxt'];
					if (isset($ret['maxdur'])) {
						$txt .= ' К сожалению, максимальная долговечность предмета из-за ремонта уменьшилась.';
					}
					SetMsg($txt);
				}
			} else {
				SetMsg("Предмет не найден","e");
			}
			Redirect();
		}
	break;
	case 2 && isset($_GET['dorecharge']):
		$_GET['dorecharge'] = str_replace('&quot;','"',$_GET['dorecharge']);
		$json = json_decode($_GET['dorecharge']);
		if (!$json) {
			SetMsg("Ошибка перезарядки","e");
			Redirect();
		}

		$query = MakeRechargeQuery($json,$query);

		if ($query === false) {
			Redirect();
		}

		$data = mysql_query($query);

		$kr = 0;
		$ekr = 0;
		$rep = 0;
		$i = 0;
		while($row = mysql_fetch_assoc($data)) {
			$ret = RechargeItem($row,$user,$bank);
			if (is_array($ret)) {
				$i++;
				if ($ret['type'] == 1) {
					$kr += $ret['price'];
					$user['money'] -= $ret['price'];
				}
				if ($ret['type'] == 2) {
					$ekr += $ret['price'];
					$bank['ekr'] -= $ret['price'];
				}
				if ($ret['type'] == 3) {
					$rep += $ret['price'];
					$user['repmoney'] -= $ret['price'];
				}
			}
		}

		if ($i > 0) {
			$txt = 'Успешно перезаряжено '.declOfNum($i,array("предмет","предмета","предметов")).' на сумму ';
			if ($kr > 0) $txt .= '<b>'.$kr.'</b> кр. ';
			if ($ekr > 0) $txt .= '<b>'.$ekr.'</b> екр. ';
			if ($rep > 0) $txt .= '<b>'.$rep.'</b> репутации. ';
			SetMSg($txt);
		} else {
			SetMsg("Перезарядка не требуется или невозможна","e");
		}

		Redirect();
	break;
	case 2 && (isset($_GET['calcrecharge'])):
		$_GET['calcrecharge'] = str_replace('&quot;','"',$_GET['calcrecharge']);
		$json = json_decode($_GET['calcrecharge']);
		if (!$json) die("Ошибка расчёта");

		$query = MakeRechargeQuery($json,$query);

		if ($query === false) {
			$ret = array('ekr' => 0, 'kr' => 0, 'rep' => 0);
			die(json_encode($ret));
		}

		$data = mysql_query($query);
		$ret = array('kr' => 0, 'ekr' => 0, 'rep' => 0);

		while($row = mysql_fetch_assoc($data)) {
                        $arr = CalculateRechargePrice($row,$user);
			if ($arr['type'] == 1) {
				$ret['kr'] += $arr['price'];
			}
			if ($arr['type'] == 2) {
				$ret['ekr'] += $arr['price'];
			}
			if ($arr['type'] == 3) {
				$ret['rep'] += $arr['price'];
			}
		}
		die(json_encode($ret));
	break;
	case 2 && (isset($_GET['recharge'])):
		// перезарядка

		$data = mysql_query($query['recharge'].' and id = '.intval($_GET['recharge']));
		if (mysql_num_rows($data) > 0) {
			$data = mysql_fetch_assoc($data);
			if (!$data) Redirect();

			$ret = RechargeItem($data,$user,$bank);

			if ($ret == 1) {
				SetMsg("Не выполнен вход в банк","e");
			} elseif ($ret == 2) {
				SetMsg("Недостаточно денег для перезарядки вещи","e");
			} elseif ($ret == 3) {
				SetMsg("Недостаточно репутации для перезарядки вещи","e");
			} elseif ($ret == 4) {
				SetMsg("Количество перезарядов исчерпано","e");
			} elseif (is_array($ret)) {
				$txt = 'Магия успешно перезаряжена за '.$ret['price'].' '.$ret['typetxt'];
				SetMsg($txt);
			}

		} else {
			SetMsg("Предмет не найден","e");
		}
		Redirect();
	break;
	case 3 && $close_razdel_mf == 0 && isset($_GET['mf']):
		/*
		if ($user['intel'] < 50) {
			SetMsg("Вы должны иметь интеллект выше 50 для модификации","e");
			Redirect();
		}
		*/
		$mf_select = mysql_query($query['mf'].' AND i.id = '.intval($_GET['mf']));
		if (mysql_num_rows($mf_select) > 0) {
			$row = mysql_fetch_array($mf_select);
			if ($row === false) Redirect();

			$charka_hp=0;

			if ($row['charka'] != '') {
				$charka=substr($row['charka'], 2,strlen($row['charka'])-1); //откидываем первые два символа
				$inputbonus=unserialize($charka); //все данные
				if (is_array($inputbonus)) {
					foreach($inputbonus as $blevl => $bdata) {
						foreach($bdata as $pk => $pv) {
							foreach($pv as $k => $v) {
								$row[$k] -= $v;
								if ($k == 'ghp')	{
								 	$charka_hp = $v;
							 	}
							}
						}
					}
				}
	  		}

			$mf_cost = $row['shcost'];

			if (($row['gsila'] == 0) and ($row['glovk'] == 0) and ($row['ginta'] == 0) and ($row['gintel'] == 0)) {
				$mf_cost = round($row['shcost']*0.5, 0);
			}

			$mf_cost = round($mf_cost*0.5);

			if ($user['money'] < $mf_cost) {
				SetMsg("У вас не хватает денег на модификацию.","e");
				Redirect();
			}

			$up_stats = get_free_stats_up($user['intel'],$row['prototype']);
			//$up_hp = round(rand(5, $up_stats * 10));
			//if($up_hp > 20) { $up_hp = 20; }
			$up_hp = 20;
			//$up_bron = round(rand(10, $up_stats * 10)/10);
			//if($up_bron > 3) { $up_bron = 3; }
			$up_bron = 3;

			$mfinfo = array();

			if (($row['gsila'] == 0) and ($row['glovk'] == 0) and ($row['ginta'] == 0) and ($row['gintel'] == 0)) {
				$up_stats = 0;
				$mfinfo['stats'] = 0;
			} else {
				$mfinfo['stats'] = $up_stats;
			}

			$bron1 = (($row['bron1'] > 0) ? ($row['bron1'] + $up_bron) : "0");
			$bron2 = (($row['bron2'] > 0) ? ($row['bron2'] + $up_bron) : "0");
			$bron3 = (($row['bron3'] > 0) ? ($row['bron3'] + $up_bron) : "0");
			$bron4 = (($row['bron4'] > 0) ? ($row['bron4'] + $up_bron) : "0");
			$hp = (($row['ghp'] > 0) ? ($row['ghp'] + $up_hp):"0");

			if ($row['ghp'] > 0) {
				$mfinfo['hp'] = $up_hp;
			} else {
				$mfinfo['hp'] = 0;
			}

			if ($bron1 > 0 || $bron2 > 0 || $bron3 > 0 || $bron4 > 0) {
				$mfinfo['bron'] = $up_bron;
			} else {
				$mfinfo['bron'] = 0;
			}

			if($up_stats == 0) {
				if(!($row['ghp'] > 0)) {
					$hp = $up_hp;
					$mfinfo['hp'] = $up_hp;
				}
			}

			$hp += $charka_hp; // докидываем если есть хп отчарки

			if ($up_stats > 2) {
				$marka = 1;
			} else {
				$marka = 0;
			}

			SaveMFLOG($user['id'],$row['id'],$marka);

			$q = mysql_query("UPDATE oldbk.`inventory` SET
						`mfinfo` = '".mysql_real_escape_string(serialize($mfinfo))."',
						`ghp` = '".$hp."',
						`bron1` = '".$bron1."',
						`bron2` = '".$bron2."',
						`bron3` = '".$bron3."',
						`bron4` = '".$bron4."',
						`stbonus` = `stbonus` + '".$up_stats."',
						`type3_updated`='".$marka."',
						`unik`='".$marka."',
						`cost` = `cost` + '".$mf_cost."',
						`sebescost` = `sebescost` + '".$mf_cost."',
						`name` = CONCAT(`name`, ' (мф)')
						WHERE `id` = ".$row['id']." LIMIT 1"
			) or die(mysql_error());

			mysql_query("UPDATE `users` set `money` = `money`- '".$mf_cost."' WHERE id = ".$user['id']);
                	$dressid=get_item_fid($row);
			$rec['owner']=$user['id'];
			$rec['owner_login']=$user['login'];
			$rec['owner_balans_do']=$user['money'];
			$user['money']=$user['money']-$mf_cost;
			$rec['owner_balans_posle']=$user['money'];
			$rec['target']=0;
			$rec['target_login']="Ремонтная мастерская";
			$rec['type']=179;//Модификация
			$rec['sum_kr']=$mf_cost;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=$dressid;
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
			$rec['item_arsenal']=$row['arsenal_klan'];
			$rec['add_info']='+Броня:'.$up_bron.' +Статы:'.$up_stats.' +Жизни:'.$up_hp;
			add_to_new_delo($rec);

			SetMsg("Вещь модифицирована");

			if(!$_SESSION['beginer_quest']['none']) {
			        $last_q=check_last_quest(30);
			        if($last_q) {
					quest_check_type_30($last_q,$user[id],2,1);
				}
			}
			Redirect();
		} else {
			SetMsg("Предмет не найден","e");
		}
		Redirect();
	break;
	case 4 && isset($_GET['podgon']):
		$row = mysql_query($query['podgon'].' and i.id = '.intval($_GET['podgon']));
		if (mysql_num_rows($row) > 0) {
			$row = mysql_fetch_assoc($row);
			if ($row === false) Redirect();

			//если есть чарка то не учитываем
			if  ($row['charka'] != '') {
				$charka=substr($row['charka'], 2,strlen($row['charka'])-1); //откидываем первые два символа
				$inputbonus=unserialize($charka); //все данные
				if (is_array($inputbonus)) {
					foreach($inputbonus as $blevl => $bdata) {
						foreach($bdata as $pk => $pv) {
							foreach($pv as $k => $v) {
								$row[$k]-=$v;
							}
						}
					}
				}
		  	}

			if (($row['mfkrit']==0) and ($row['mfakrit']==0) and ($row['mfuvorot']==0) and ($row['mfauvorot']==0)) {
				// после снятия чарки нет МФ - нельзя подогнать
				SetMsg("Данную вещь нельзя подогнать","e");
				Redirect();
			}

 			$cost_add = round($row['shcost'], 0);
			$max_ups_left = $max_ups - $row['ups'];
			$costs = upgrade_item($cost_add,$max_ups_left);

			if($user['money'] < $costs['up_cost']) {
				SetMsg("У вас не хватает денег на подгонку.");
				Redirect();
			}

			$q = mysql_query("UPDATE oldbk.`inventory` SET
						`mfbonus` = `mfbonus` + '".$costs['mfbonusadd']."',
						`ups` = `ups`+'1',
						`cost` = `cost` + '".$costs['cost_add']."',
						`sebescost` = `sebescost` + '".$costs['up_cost']."'
						WHERE `id` = ".$row['id']." LIMIT 1");

			mysql_query("UPDATE `users` set `money` = `money`- '".$costs['up_cost']."' WHERE id = ".$user['id']);

			$dressid=get_item_fid($row);
			$rec['owner']=$user['id'];
			$rec['owner_login']=$user['login'];
			$rec['owner_balans_do']=$user['money'];
			$user['money']=$user['money']-$costs['up_cost'];
			$rec['owner_balans_posle']=$user['money'];
			$rec['target']=0;
			$rec['target_login']="Ремонтная мастерская";
			$rec['type']=177;//подгон
			$rec['sum_kr']=$costs['up_cost'];
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=$dressid;
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
			$rec['item_arsenal']=$row['arsenal_klan'];

			add_to_new_delo($rec);
			SetMsg("Вещь подогнана");
		} else {
			SetMsg("Предмет не найден","e");
		}
		Redirect();
	break;
	case 5 && (isset($_GET['dezmf']) || isset($_GET['dezst'])):
		// сброс мф-статов

		if(isset($_GET['dezst'])) {
			$dezst = 1;
			$item = intval($_GET['dezst']);
			$rr = n_fields('stats');
			$rr[] = 'stbonus';
			$txt = 'статы';
			$rrate = $mf_stat_rate;
		} elseif (isset($_GET['dezmf'])) {
			$dezmf = 1;
			$item = intval($_GET['dezmf']);
			$rr = n_fields('mfs');
			$rr[] = 'mfbonus';
			$txt = 'МФ';
			$rrate = $mf_stat_rate;
		}

		$data = mysql_query($query['resetms'].' and id = '.$item);

		if (mysql_num_rows($data) > 0) {
			$data = mysql_fetch_assoc($data);
			if ($data === false) Redirect();

			if ($data['prototype']==6018 or $data['prototype']==6019 or $data['prototype']==6020)
			{
			//спец руны готовые прокаченые которые не качаются и сбрасываются по другому
				if (isset($dezmf))
					{
							if ($data['mfbonus'] > 0) {
							SetMsg("У руны есть нераспределенные МФ","e");
							Redirect();
							}
							else
							{
								ResetSpecRune($data,1);

								SetMsg('Сброшены '.$txt.' предмета "'.$data['name'].'".');
							}
					}
					elseif ($data['stbonus'] > 0) {
						SetMsg("У руны есть нераспределенные параметры","e");
						Redirect();
					} else
							{
								ResetSpecRune($data,2);
								SetMsg('Сброшены '.$txt.' предмета "'.$data['name'].'".');

							}

			}
			elseif ($data['type'] == 30) {
				if ((isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) || $free_rune_reset) {


					if ((isset($dezmf) && $data['up_level'] > 0) || (isset($dezst) && $data['up_level'] >= 2)) {
						if ($free_rune_reset == 0 && isset($dezmf) && $bank['ekr'] < $rune_reset_mf) {
							SetMsg("У вас недостаточно екр для сброса руны","e");
							Redirect();
						} elseif (isset($dezmf)) {
							if ($data['mfbonus'] > 0) {
								SetMsg("У руны есть нераспределенные МФ","e");
								Redirect();
							} else {
								$runereset = $rune_reset_mf;
							}
						}
						if ($free_rune_reset == 0 && isset($dezst) && $bank['ekr'] < $rune_reset_st) {
							SetMsg("У вас недостаточно екр для сброса руны","e");
							Redirect();
						} elseif (isset($dezst)) {
							if ($data['stbonus'] > 0) {
								SetMsg("У руны есть нераспределенные параметры","e");
								Redirect();
							} else {
								$runereset = $rune_reset_st;
							}
						}
						// всё ок - расчитываем сброс рун
						if (isset($dezmf)) {
							ResetRune($data,1);
						}
						if (isset($dezst)) {
							ResetRune($data,2);
						}

						if ($free_rune_reset == 0) {
							mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr`- '".$runereset."' WHERE id = {$bank['id']}");
							mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Вы сбросили руну за ".$runereset." екр.</b>, <i>(Итого: {$bank['cr']} кр., ".($bank['ekr']-$runereset)." екр.)</i>','{$bank['id']}');");

						}
						SetMsg('Сброшены '.$txt.' предмета "'.$data['name'].'" за  '.$runereset.' екр.');
					} else {
						SetMsg("Руна не нуждается в сбросе","e");
						Redirect();
					}
				} else {
					SetMsg("Ошибка сброса руны","e");
					Redirect();
				}
			} else {
				$dn_item = downgrade_item($data,$rr,1);

				if($user['money'] < ($dn_item['money']*$rrate)) {
					SetMsg("Недостаточно денег","e");
					Redirect();
				}

				if(($dn_item['delta_stat'] != $data['stbonus'] || $dn_item['delta_mf'] != $data['mfbonus'])) {
					for($i=0;$i<count($rr);$i++) {
						$str.=" " .$rr[$i]."= '".$dn_item[$rr[$i]]."',";   //собираем апдейт в инвентарь
					}

					// стандартное обновление всего по пользователю.
					$str = substr($str, 0, -1);
					mysql_query("UPDATE oldbk.`inventory` SET ".$str." WHERE id = '".$item."' LIMIT 1");
					mysql_query("UPDATE `users` set `money` = `money` -  '".($dn_item['money']*$rrate)."' WHERE id = '{$user['id']}'");

					SetMsg('Сброшены '.$txt.' предмета "'.$data['name'].'" за  '.($dn_item['money']*$rrate).' кр.');
				} else {
					SetMsg("Предмет не требует сброса или даунгрейда","e");
					Redirect();
				}
			}

			if(isset($dezst)) {
				$rec['type'] = 193;
			}
			if(isset($dezmf)) {
				$rec['type'] = 194;
			}

			$dressid=get_item_fid($data);
			$rec['owner']=$user['id'];
			$rec['owner_login']=$user['login'];
			$rec['owner_balans_do']=$user['money'];
			$rec['target']=0;
			$rec['target_login']="Ремонтная мастерская";

			if (!isset($runereset)) {
				$user['money'] -= ($dn_item['money']*$rrate);
				$rec['owner_balans_posle']=$user['money'];
				$rec['sum_kr']=($dn_item['money']*$rrate);
				$rec['sum_ekr']=0;
				$rec['item_ups']=$data['ups'];
			} else {
				$rec['owner_balans_posle']=$user['money'];
				$rec['sum_kr']=0;
				$rec['item_ups']=0;
				if ($free_rune_reset == 0) {
					$rec['sum_ekr']=$runereset;
					$rec['bank_id']=$bank['id'];
					$rec['add_info']='Баланс до '.($bank['ekr']+$runereset). ' после ' .$bank['ekr'];
				} else {
					$rec['sum_ekr']=0;
				}
			}

			$rec['sum_kom']=0;
			$rec['item_name'] = $data['name'];
			$rec['item_id']=$dressid;
			$rec['item_count']=1;
			$rec['item_type']=$data['type'];
			$rec['item_cost']=$data['cost'];
			$rec['item_dur']=$data['duration'];
			$rec['item_maxdur']=$data['maxdur'];
			$rec['item_unic']=$data['unik'];
			$rec['item_incmagic']=$data['includemagicname'];
			$rec['item_incmagic_count']=$data['includemagicuses'];
			$rec['item_arsenal']=$data['arsenal_klan'];
			add_to_new_delo($rec);
		} else {
			SetMsg("Предмет не найден","e");
		}
		Redirect();
	break;
	case 6 && isset($_GET['riht']) && (isset($_GET['stat']) || isset($_GET['hp']) || isset($_GET['br'])):
		// рихтовка
		$row = mysql_query($query['riht'].' and id = '.intval($_GET['riht']));
		if (mysql_num_rows($row) > 0) {
			$row = mysql_fetch_assoc($row);
			if ($row === false) Redirect();

			$mfinfo = unserialize($row['mfinfo']);

		    	if($_GET['stat']>0) {
			    	$fields=array('stbonus');
			    	$txt='увеличены статы на';
				if (!$mfinfo['stats']) die();

				$how = 2-$mfinfo['stats'];
				if ($how <= 0) die();
			} elseif($_GET['hp']>0) {
			        $fields=array('ghp');
			        $txt='увеличены жизни на';
			        if (!$mfinfo['hp']) die();

				$how = 20-$mfinfo['hp'];
				if ($how <= 0) die();
		    	} elseif($_GET['br']>0) {
				$fields=array('bron1','bron2','bron3','bron4');
		        	$txt='увеличена броня на';
				if (!$mfinfo['bron']) die();

				$how = 3-$mfinfo['bron'];
				if ($how <= 0) die();
		    	} else {
				die();
			}

			$str='';

		    	for($q=0;$q<count($fields)&&$q<4;$q++) {
			    	if($fields[$q]=='stbonus') {
			    		$str.=$fields[$q].' = '.$fields[$q]. '+' .$how.'  ';
			    	} else {
			    		$str.=$fields[$q].' = if('.$fields[$q].'>0,'.$fields[$q].'+'.$how.','.$fields[$q].'), ';
			        }
			}

			$txt=$txt.' '.$how;
			$str=substr($str,0,-2);

			if (strlen($row['mfinfo'])) {
				if ($fields[0] == "stbonus") $mfinfo['stats'] += $how;
				if ($fields[0] == "ghp") $mfinfo['hp'] += $how;
				if ($fields[0] == "bron1") $mfinfo['bron'] += $how;
				$mfinfo = serialize($mfinfo);
				$str .= ', mfinfo = "'.mysql_real_escape_string($mfinfo).'" ';
			}


			$str='UPDATE oldbk.inventory set '.$str.' WHERE id = '.$row['id'].' LIMIT 1';
			mysql_query($str);

            		$dressid=get_item_fid($row);
			$rec['owner']=$user['id'];
			$rec['owner_login']=$user['login'];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money'];
			$rec['target']=0;
			$rec['target_login']="Ремонтная мастерская";
			$rec['type']=196;//сдаем предмет
			$rec['sum_kr']=0;
			$rec['sum_ekr'] = 0;
			$rec['add_info']=get_item_fid($sert);
			$rec['sum_kom']=0;
			$rec['item_id']=$dressid;
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
			$rec['item_arsenal']=$row['arsenal_klan'];
			add_to_new_delo($rec);

			SetMsg("Вещь отрихтована");
			Redirect();
	    	} else {
			SetMsg("Вещь не найдена у вас в инвентаре","e");
	    	}
		Redirect();
	break;
	case 7 && isset($_GET['chng'],$_GET['pr'],$_SESSION['repairchangearr']) && count($_SESSION['repairchangearr']) > 0 && in_array($_GET['pr'],$_SESSION['repairchangearr']) !== false:
  		$mfinfo = array('stats' => 0, 'bron' => 0, 'hp' => 0);


		$glava_sql = '';
		if($user['klan'] != '') {
			$klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans where short='".$user['klan']."';"));
			if($user['id'] == $klan['glava']) {
				$glava_sql = ' OR (arsenal_klan="'.$user['klan'].'" AND arsenal_owner=1)';
				$glava=1;
			}
		}

		$query['exchange'] .= ' AND (arsenal_klan = "" '.$glava_sql.') ';
		$query['exchange2'] .= ' AND (arsenal_klan = "" '.$glava_sql.') ';


		$item = mysql_query($query['exchange'].' and id = '.intval($_GET['chng']));
		$chtype = 1;
		if (mysql_num_rows($item) == 0) {
			$item = mysql_query($query['exchange2'].' and id = '.intval($_GET['chng']));
			$chtype = 2;
		}

		if (mysql_num_rows($item) > 0) {
			$get_chitem = mysql_fetch_assoc($item);

			if ($chtype == 2) {
				$proto = GetItemPrototype($get_chitem['prototype']);
				if ($proto['nlevel'] < 8) die();
			}

			// обмен вещей - сам обмен
			$new_level = $get_chitem['up_level'];

			if ($chtype == 2) {
				$new_level = $proto['nlevel'];
			}

			$new_type_otdel = $get_chitem['otdel'];
			$new_type = $get_chitem['type'];

	  		/*
			if ($chtype == 1) {
				$newitem = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND nlevel='{$new_level}' and ((id>=18000 and id<=19000) OR id in (222222234,222222235,222222241,222222240,222222246,222222247,222222243,222222242,222222244,222222245,222222254,222222253,222222252,222222255,254,263,274,275,270,277,278,279,256,258,255,273,268,266,269,276,285) )  and ab_mf=0 and ab_bron=0 and ab_uron=0 and id = ".intval($_GET['pr'])); // новые шмотки нужного нам уровня и типа
			} elseif ($chtype == 2) {
				$newitem = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND nlevel='{$new_level}' and (id>=40000 and id<=41000)  and ab_mf=0 and ab_bron=0 and ab_uron=0 and new_item = 1 and id = ".intval($_GET['pr'])); // новые шмотки нужного нам уровня и типа
			} else {
				die();
			}
			*/

			if ($new_level == 13) {
				$newitem = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND (nlevel='{$new_level}' or nlevel = 14) and (id>=40000 and id<=41000)  and ab_mf=0 and ab_bron=0 and ab_uron=0 and new_item = 1 and id = ".intval($_GET['pr'])); // новые шмотки нужного нам уровня и типа
			} else {
				$newitem = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND nlevel='{$new_level}' and (id>=40000 and id<=41000)  and ab_mf=0 and ab_bron=0 and ab_uron=0 and new_item = 1 and id = ".intval($_GET['pr'])); // новые шмотки нужного нам уровня и типа
			}

			$newitem = mysql_fetch_assoc($newitem);

	  		if ($newitem['id'] > 0) {
	  			//типа все гуд можно делать обмен!
				//первым делом проверяем - макс износ - возможен ли обмен
				if ($get_chitem['duration'] > $newitem['maxdur']) {
				  	SetMsg('Ошибка! К сожалению, долговечность вашего предмета больше чем максимальная долговечность нового предмета. Для удачного обмена необходимо произвести ремот!',"e");
					Redirect();
				}

				if (strpos($get_chitem['name'], '(мф)') !== false) {
					$newitem['name'].=' (мф)';  // если старая была Мф
					$newitem['unik']=$get_chitem['unik']; // уник флаг
					$newitem['type3_updated']=$get_chitem['type3_updated'];

					//если был уник то 3
					if (($newitem['gsila'] == 0) and ($newitem['glovk'] == 0) and ($newitem['ginta'] == 0) and ($newitem['gintel'] == 0)) {
						$up_stats = 0;
					} else {
						if ($newitem['unik'] == 1) {
							$newitem['stbonus']+=3;
							$up_stats = 3;
							$mfinfo['stats'] = 3;
						} elseif ($newitem['unik'] == 2) {
							$newitem['stbonus'] += 4;
							$up_stats = 4;
							$mfinfo['stats'] = 4;
						} else {
							$newitem['stbonus']+=2;
							$mfinfo['stats'] = 2;
							$up_stats = 2;
						}
					}

					if (($get_chitem['ghp']!=0) OR ($get_chitem['bron1']!=0) OR ($get_chitem['bron2']!=0) OR ($get_chitem['bron3']!=0) OR ($get_chitem['bron4']!=0)) {
						// если Сука у старой бля была ХП - ндао понять сколько хп набажино
						$get_pitem=mysql_fetch_array(mysql_query("SELECT * from oldbk.shop where id='{$get_chitem['prototype']}' LIMIT 1;"));

						if  (!($get_pitem['id']>0)) {
							$get_pitem = mysql_fetch_array(mysql_query("SELECT * from oldbk.eshop where id='{$get_chitem['prototype']}' LIMIT 1;"));
						}

						if  (!($get_pitem['id']>0)) {
							$get_pitem = mysql_fetch_array(mysql_query("SELECT * from oldbk.сshop where id='{$get_chitem['prototype']}' LIMIT 1;"));
						}

						if  ($get_pitem['id']>0) {
							//есть прототип
							$hplvls=array("14" => 35, "13" => 27, "12" => 20,  "11" => 15,  "10" => 12, "9" => 10, "8" => 8, "7" => 6); /// то что дают свитки на каждом  уровне

							$fin_lvl = $get_chitem['nlevel'];// требуемый уровень предмета который  сдаем
							$start_lvl = $get_pitem['nlevel']; // требуемы уровень который  стоит в магазе
							$start_lvl += 1;
							$deltahp=$get_chitem['ghp']-$get_pitem['ghp'];

							$deltabron1=$get_chitem['bron1']-$get_pitem['bron1']+1;
							$deltabron2=$get_chitem['bron2']-$get_pitem['bron2']+1;
							$deltabron3=$get_chitem['bron3']-$get_pitem['bron3']+1;
							$deltabron4=$get_chitem['bron4']-$get_pitem['bron4']+1;

							if ($start_lvl<6) {$start_lvl=6;} // апы начинаются с 7го

							for($yy=$start_lvl;$yy<=$fin_lvl;$yy++) {
								$deltahp-=$hplvls[$yy]; // снимаем каждым свитком
								//каждый ап это -1 брони
								$deltabron1-=1;
								$deltabron2-=1;
								$deltabron3-=1;
								$deltabron4-=1;
							}

							// делаем топ мф
							$up_hp=20;
						} else {
							//нет прототипа
							$up_hp=20; // рандом чуть меньше
						}
					} else {
						// нет Хп
						$up_hp=20; //рандом Макс ХП
					}

					///заглушки
					$deltabron1 = 3;
					$deltabron2 = 3;
					$deltabron3 = 3;
					$deltabron4 = 3;

					// новая бронь от МФ
					// если у нового  есть брони - то добавляем
					$newitem['bron1'] = (($newitem['bron1'] > 0) ? ($newitem['bron1'] + $deltabron1) : "0");
					$newitem['bron2'] = (($newitem['bron2'] > 0) ? ($newitem['bron2'] + $deltabron2) : "0");
					$newitem['bron3'] = (($newitem['bron3'] > 0) ? ($newitem['bron3'] + $deltabron3) : "0");
					$newitem['bron4'] = (($newitem['bron4'] > 0) ? ($newitem['bron4'] + $deltabron4) : "0");

					//если у нового есть хп - то добавляем от МФ
					$newitem['ghp'] = (($newitem['ghp'] != 0) ? ($newitem['ghp'] + $up_hp):$newitem['ghp']);
					if ($newitem['ghp'] != 0) $mfinfo['hp'] = $up_hp;
					if ($newitem['bron1'] > 0 || $newitem['bron2'] > 0 || $newitem['bron3'] > 0 || $newitem['bron4'] > 0) {
						$mfinfo['bron'] = max($deltabron1,$deltabron2,$deltabron3,$deltabron4);
					}

					//цена, только под кольцо. (есть и МФ и СТАТЫ) так что 1/2 от цены
					$shop_cost=$newitem['cost'];
					//ставим цену за ФМ
					$mf_cost_koef=($up_stats>0?0.5:0.25);
					$newitem['cost'] = $shop_cost+round($shop_cost*$mf_cost_koef, 0);
					//Подгоны
					$ups_for_item['cost_add']=0;
					$ups_for_item['mfbonusadd']=0;
					if($get_chitem['ups']>0) { // если у строго были подгоны
						//подгоняем шмотку до нужного подгона
						for($i=$max_ups;($i>$max_ups-$get_chitem['ups']);$i--) {
							//апаем нужное кол-во раз
							$costs=upgrade_item($shop_cost,$i);
							$ups_for_item['cost_add']+=$costs['cost_add'];
							$ups_for_item['mfbonusadd']+=$costs['mfbonusadd'];
						}
					}

					$newitem['cost']+=$ups_for_item['cost_add']; // ставим цену с подгонами
					$newitem['mfbonus']+=$ups_for_item['mfbonusadd']; // ставим кол.свободных МФ
					$newitem['ups']=$get_chitem['ups']; // перенос кол.апов
				}

				//если пушка
				if ($get_chitem['type'] == 3 && $chtype == 1) {
					//если заточка
					if (strpos($get_chitem['name'], '+') !== false) {
						$tempa=explode("+",$get_chitem['name']);
						$sharp=(int)($tempa[1]);

						if ($sharp > 0) {
							$newitem['minu']+=$sharp;
							$newitem['maxu']+=$sharp;
							$newitem['cost']+=30;
							$newitem['name']=$newitem['name']."+".$sharp;

							if ($newitem['otdel']==1) {
						 		$newitem['nnoj']+=$sharp;
						 		$newitem['ninta']+=$sharp;
							} elseif ($newitem['otdel']==11) {
			 					$newitem['ntopor']+=$sharp;
			 					$newitem['nsila']+=$sharp;
					 		} elseif ($newitem['otdel']==12) {
								$newitem['ndubina']+=$sharp;
								$newitem['nlovk']+=$sharp;
							} elseif ($newitem['otdel']==13) {
							 	$newitem['nmech']+=$sharp;
							 	$newitem['nvinos']+=$sharp;
						 	}
							$newitem['sharped']=1;
						}
					}
				}


				//Далее общие поля для МФ и не МФ шмоток
				if ($get_chitem['arsenal_klan']!='') {
					$newitem['arsenal_klan']=$get_chitem['arsenal_klan']; //перенос арса
					$newitem['arsenal_owner']=$get_chitem['arsenal_owner']; // перенос овнера арсенала
					$newitem['prokat_do']=$get_chitem['prokat_do']; // перенос овнера арсенала
					$newitem['letter']=$get_chitem['letter']; // перенос овнера арсенала
					$need_update_ars=true;
				} else {
					$newitem['prokat_do'] = 0;
				}

				$newitem['duration']=$get_chitem['duration']; //переносим долговечность
				$newitem['present']=$get_chitem['present']; //переносим если подарок
				$newitem['idcity']=CITY_ID;

				// встройки ПЕРЕНОСИМ
				if (($get_chitem['includemagic']>0) AND ($get_chitem['includemagicuses']>0)) {
					$newitem['includemagic']=$get_chitem['includemagic'];
					$newitem['includemagicdex']=$get_chitem['includemagicdex'];
					$newitem['includemagicmax']=$get_chitem['includemagicmax'];
					$newitem['includemagicname']=$get_chitem['includemagicname'];
					$newitem['includemagicuses']=$get_chitem['includemagicuses'];
					$newitem['includemagiccost']=$get_chitem['includemagiccost'];
					$newitem['includemagicekrcost']=$get_chitem['includemagicekrcost'];
					$newitem['includerechargetype']=$get_chitem['includerechargetype'];
					$newitem['includeprototype']=$get_chitem['includeprototype'];
					$newitem['nintel']=$newitem['nintel']<$get_chitem['nintel']?$get_chitem['nintel']:$newitem['nintel'];
					$newitem['nmudra']=$newitem['nmudra']<$get_chitem['nmudra']?$get_chitem['nmudra']:$newitem['nmudra'];
					$newitem['nfire']=$newitem['nfire']<$get_chitem['nfire']?$get_chitem['nfire']:$newitem['nfire'];
					$newitem['nwater']=$newitem['nwater']<$get_chitem['nwater']?$get_chitem['nwater']:$newitem['nwater'];
					$newitem['nair']=$newitem['nair']<$get_chitem['nair']?$get_chitem['nair']:$newitem['nair'];
					$newitem['nearth']=$newitem['nearth']<$get_chitem['nearth']?$get_chitem['nearth']:$newitem['nearth'];
					$newitem['nlight']=$newitem['nlight']<$get_chitem['nlight']?$get_chitem['nlight']:$newitem['nlight'];
					$newitem['ngray']=$newitem['ngray']<$get_chitem['ngray']?$get_chitem['ngray']:$newitem['ngray'];
					$newitem['ndark']=$newitem['ndark']<$get_chitem['ndark']?$get_chitem['ndark']:$newitem['ndark'];
				}

				//Далее все готово для удаления строго и вставки нового предмета
				if($get_chitem['add_pick'] != '') {
			        	undress_img($get_chitem);
			        }

				/// если нет статов то снимаем флаг уника
				if  (!(($newitem['gsila']!=0) OR  ($newitem['ginta']!=0) OR  ($newitem['glovk']!=0)  OR  ($newitem['gintel']!=0) OR  ($newitem['gmp']!=0))) {
					$newitem['unik']=0;
				}

				// переносим чарку
				if ($get_chitem['charka'] != '') {
					$newitem['charka'] = mysql_real_escape_string($get_chitem['charka']);

					$charka=substr($get_chitem['charka'], 2,strlen($get_chitem['charka'])-1); //откидываем первые два символа
					$inputbonus=unserialize($charka); //все данные
					if (is_array($inputbonus)) {
						foreach($inputbonus as $blevl => $bdata) {
							foreach($bdata as $pk => $pv) {
								foreach($pv as $k => $v) {
									$newitem[$k] += $v;
								}
							}
						}
					}
				}

				if ($get_chitem['getfrom'] > 0 ) {
					$newitem['getfrom'] = $get_chitem['getfrom'];
				}

				if ($get_chitem['ekr_flag'] > 0 ) {
					$newitem['ekr_flag'] = $get_chitem['ekr_flag'];
				}

				if ($get_chitem['notsell'] > 0 ) {
					$newitem['notsell'] = $get_chitem['notsell'];
				}

				if ($get_chitem['up_level'] > 0 ) {
					$newitem['up_level'] = $get_chitem['up_level'];
				}

				if ($get_chitem['sowner'] > 0 ) {
					$newitem['sowner'] = $get_chitem['sowner'];
				}

				if ($get_chitem['goden'] > 0 ) {
					$newitem['goden'] = $get_chitem['goden'];
				}


				if ($get_chitem['dategoden'] > 0 ) {
					$newitem['dategoden'] = $get_chitem['dategoden'];
				} else {
					$newitem['dategoden'] = 0;
				}



				mysql_query("DELETE FROM oldbk.inventory where id='{$get_chitem['id']}' LIMIT 1;");
				if (mysql_affected_rows()>0) {
					//удалилось// делаем инсерт
					if ($mfinfo['stats'] > 0 || $mfinfo['bron'] > 0 || $mfinfo['hp'] > 0) {
						$mfinfo = mysql_real_escape_string(serialize($mfinfo));
					} else {
						$mfinfo = "";
					}

					mysql_query("INSERT INTO oldbk.`inventory`
					(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,
						`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
						`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,
						`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,
						`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`arsenal_klan` ,
						 `arsenal_owner`,`idcity`,`duration`,`sharped`,
						`otdel`,`gmp`,`gmeshok`, `group`,`up_level`,`mfbonus`,`stbonus`,`includemagic`,`includemagicdex`,
						`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,
						`present`,`ecost`,`sowner`,`unik`,`type3_updated`,`ups`,`add_time`, `update`,`mfinfo`,`nclass`,`charka`,`ekr_flag`,`notsell`,`rareitem`,`repcost`,`includerechargetype`,`includeprototype`,`prokat_do`,`letter`
					)
					VALUES
					('{$newitem['id']}','{$user[id]}','{$newitem['name']}','{$newitem['type']}',{$newitem['massa']},'{$newitem[cost]}','{$newitem['img']}',{$newitem['maxdur']},{$newitem['isrep']},'{$newitem['gsila']}','{$newitem['glovk']}','{$newitem['ginta']}','{$newitem['gintel']}','{$newitem['ghp']}','{$newitem['gnoj']}','{$newitem['gtopor']}','{$newitem['gdubina']}','{$newitem['gmech']}','{$newitem['gfire']}','{$newitem['gwater']}','{$newitem['gair']}','{$newitem['gearth']}','{$newitem['glight']}','{$newitem['ggray']}','{$newitem['gdark']}','{$newitem['needident']}','{$newitem['nsila']}','{$newitem['nlovk']}','{$newitem['ninta']}','{$newitem['nintel']}','{$newitem['nmudra']}','{$newitem['nvinos']}','{$newitem['nnoj']}','{$newitem['ntopor']}','{$newitem['ndubina']}','{$newitem['nmech']}','{$newitem['nfire']}','{$newitem['nwater']}','{$newitem['nair']}','{$newitem['nearth']}','{$newitem['nlight']}','{$newitem['ngray']}','{$newitem['ndark']}',
					'{$newitem['mfkrit']}','{$newitem['mfakrit']}','{$newitem['mfuvorot']}','{$newitem['mfauvorot']}','{$newitem['bron1']}','{$newitem['bron2']}','{$newitem['bron3']}','{$newitem['bron4']}','{$newitem['maxu']}','{$newitem['minu']}','{$newitem['magic']}','{$newitem['nlevel']}',
					'{$newitem['nalign']}','{$newitem['dategoden']}','{$newitem['goden']}',
					'{$newitem['arsenal_klan']}' ,  '{$newitem['arsenal_owner']}',
					'{$user['id_city']}','{$newitem['duration']}','{$newitem['sharped']}'
					,'{$newitem['razdel']}','{$newitem['gmp']}','{$newitem['gmeshok']}','{$newitem['group']}','{$newitem['up_level']}','{$newitem['mfbonus']}','{$newitem['stbonus']}'
					,'{$newitem['includemagic']}','{$newitem['includemagicdex']}','{$newitem['includemagicmax']}','{$newitem['includemagicname']}','{$newitem['includemagicuses']}'
					,'{$newitem['includemagiccost']}','{$newitem['includemagicekrcost']}',
					'{$newitem['present']}','{$newitem['ecost']}','{$newitem['sowner']}','{$newitem['unik']}','{$newitem['type3_updated']}','{$newitem['ups']}','0',NOW(),'{$mfinfo}','{$newitem['nclass']}','{$newitem['charka']}','{$newitem['ekr_flag']}','{$newitem['notsell']}','{$newitem['rareitem']}','{$newitem['repcost']}','{$newitem['includerechargetype']}','{$newitem['includeprototype']}','{$newitem['prokat_do']}','{$newitem['letter']}'
					) ;") or die(mysql_error());

					if (mysql_affected_rows()>0) {
						//вставили
						$newitem['id'] = mysql_insert_id();
						$tolog="DELITEM:".print_r($get_chitem, true);
						$tolog.="\r\n";
						$tolog.="GETITEM:".print_r($newitem, true);
						$tolog.="-----------------------------------------------";
						SaveCHLOG($tolog); //пишем лог

						//пишем дело
						$rec['owner']=$user['id'];
						$rec['owner_login']=$user['login'];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money'];
						$rec['target']=0;
						$rec['target_login']="Ремонтная мастерская";
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;

						$rec['type']=305;//обмен шмотки - сдал старый
						$rec['item_id']=get_item_fid($get_chitem);
						$rec['item_name']=$get_chitem['name'];
						$rec['item_count']=1;
						$rec['item_type']=$get_chitem['type'];
						$rec['item_cost']=$get_chitem['cost'];
						$rec['item_dur']=$get_chitem['duration'];
						$rec['item_maxdur']=$get_chitem['maxdur'];
						$rec['item_ups']=$get_chitem['ups'];
						$rec['item_unic']=$get_chitem['unik'];
						$rec['item_incmagic']=$get_chitem['includemagicname'];
						$rec['item_incmagic_count']=$get_chitem['includemagicuses'];
						$rec['item_arsenal']=$get_chitem['arsenal_klan'];
						add_to_new_delo($rec);

						$rec['type']=306;//обмен шмотки - получил новую
						$rec['item_id']=get_item_fid($newitem);
						$rec['item_name']=$newitem['name'];
						$rec['item_count']=1;
						$rec['item_type']=$newitem['type'];
						$rec['item_cost']=$newitem['cost'];
						$rec['item_dur']=$newitem['duration'];
						$rec['item_maxdur']=$newitem['maxdur'];
						$rec['item_ups']=$newitem['ups'];
						$rec['item_unic']=$newitem['unik'];
						$rec['item_incmagic']=$newitem['includemagicname'];
						$rec['item_incmagic_count']=$newitem['includemagicuses'];
						$rec['item_arsenal']=$newitem['arsenal_klan'];
						add_to_new_delo($rec);


						if ($need_update_ars) {
							//делаем апдейт в арсе меняем старый ид на новый
							mysql_query("UPDATE `oldbk`.`clans_arsenal` SET `id_inventory`='{$newitem['id']}'  WHERE `id_inventory`='{$get_chitem['id']}'");
							//правим ид итема в правах арсенала
							mysql_query("UPDATE `oldbk`.`clans_arsenal_access` SET `item`='{$newitem['id']}' WHERE `item`='{$get_chitem['id']}'");
							//пишем в лог арса
							$log_text = '"'.$user['login'].'" обменял из арсенала "'.$get_chitem['name'].'" ['.$get_chitem['duration'].'/'.$get_chitem['maxdur'].'] [ups:'.$get_chitem['ups'].'/unik:'.$get_chitem['unik'].'/inc:'.$get_chitem['includemagicname'].'] на '.$newitem['name'].'" ['.$newitem['duration'].'/'.$newitem['maxdur'].'] [ups:'.$newitem['ups'].'/unik:'.$newitem['unik'].'/inc:'.$newitem['includemagicname'].']';
							mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$user['klan']}','{$user['id']}','{$log_text}','".time()."')");
						}

						SetMsg('Успешно произведен обмен!');
					} else {
						$tolog="DELITEM:".print_r($get_chitem, true);
						$tolog.="\r\n";
						$tolog.="GETITEM:ERROR INSERT";
						$tolog.="-----------------------------------------------";
						SaveCHLOG($tolog); //пишем лог
						SetMsg("Ошибка обмена 1","e");
					}
				} else {
					$tolog="DELITEM:".print_r($get_chitem, true);
					$tolog.="ERROR DELETE";
					$tolog.="-----------------------------------------------";
					SaveCHLOG($tolog); //пишем лог
					SetMsg("Ошибка обмена 2","e");
				}
			} else {
				SetMsg("Прототип на обмен не найден","e");
			}
		} else {
			SetMsg("Вещь на обмен не найдена","e");
		}
		Redirect();
	break;
	case 7 && isset($_GET['chng']):
		// обмен вещей - выбор прото

		$glava_sql = '';
		if($user['klan'] != '') {
			$klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans where short='".$user['klan']."';"));
			if($user['id'] == $klan['glava']) {
				$glava_sql = ' OR (arsenal_klan="'.$user['klan'].'" AND arsenal_owner=1)';
				$glava=1;
			}
		}

		$query['exchange'] .= ' AND (arsenal_klan = "" '.$glava_sql.') ';
		$query['exchange2'] .= ' AND (arsenal_klan = "" '.$glava_sql.') ';


		$item = mysql_query($query['exchange'].' and id = '.intval($_GET['chng']));
		$chtype = 1;
		if (mysql_num_rows($item) == 0) {
			$item = mysql_query($query['exchange2'].' and id = '.intval($_GET['chng']));
			$chtype = 2;
		}
		if (mysql_num_rows($item) > 0) {
			$get_chitem = mysql_fetch_assoc($item);

			if ($chtype == 2) {
				$proto = GetItemPrototype($get_chitem['prototype']);
				if ($proto['nlevel'] < 8) die();
			}


			if (!$get_chitem) {
				echo '<script>closeinfo();</script>';
				die();

			}

			$change =  '<table border=0 style="width:600px;" height=100><tr><td  valign=top align="center" height=5 colspan="4"><font style="COLOR:#8f0000;FONT-SIZE:12pt">';
			$change .= "Обмен &quot;".$get_chitem['name']."&quot; (".get_item_fid($get_chitem).")";
			$change .= '</font><a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
			<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-20px;right:-120px;" border=0 title="Закрыть"></a></td></tr>
			<tr><td colspan="4" class="center" valign=top><font color=red>На выбранный предмет будут перенесены МФ, подгоны, чарования и встроенная магия со старого предмета.</font><br><br>';


  			$new_level = $get_chitem['up_level'];
			if ($chtype == 2) {
				$new_level = $proto['nlevel'];
			}
  			$new_type_otdel = $get_chitem['otdel'];
  			$new_type = $get_chitem['type'];

			$change .= "<b>Возможные варианты обмена:</b><br><br>";

			if ($chtype == 1) {
				$data = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND nlevel='{$new_level}' and ((id>=18000 and id<=19000) OR id in (222222234,222222235,222222241,222222240,222222246,222222247,222222243,222222242,222222244,222222245,222222254,222222253,222222252,222222255,254,263,274,275,270,277,278,279,256,258,255,273,268,266,269,276,285) )  and ab_mf=0 and ab_bron=0 and ab_uron=0 ;"); // новые шмотки нужного нам уровня и типа
			} elseif ($chtype == 2) {
				if ($new_level == 13) {
					$data = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND (nlevel='{$new_level}' or nlevel = 14) and (id>=40000 and id<=41000)  and ab_mf=0 and ab_bron=0 and ab_uron=0 and new_item = 1 and id != ".$proto['id']); // новые шмотки нужного нам уровня и типа
				} else {
					$data = mysql_query("SELECT * FROM oldbk.shop WHERE type='{$new_type}' and ecost = 0  AND nlevel='{$new_level}' and (id>=40000 and id<=41000)  and ab_mf=0 and ab_bron=0 and ab_uron=0 and new_item = 1 and id != ".$proto['id']); // новые шмотки нужного нам уровня и типа
				}
			} else {
				die();
			}
			if (mysql_num_rows($data) > 0) {
		                $change .= '<table class="table a_strong" cellspacing="0" cellpadding="0"><tbody><colgroup>
			                <col width="150px">
			                <col width="300px">
            				</colgroup>
				';

				$i = 0;
				$_SESSION['repairchangearr'] = array();
			 	while($row = mysql_fetch_array($data)) {
					$_SESSION['repairchangearr'] = $row['id'];
					$act = '<A HREF="?razdel=7&chng='.$get_chitem['id'].'&pr='.$row['id'].'" onclick="if(!confirm(\'Вы уверены в выборе?\')){ return false;}">Обменять</A>';
					$row[GetShopCount()] = 1;
					if (($row['gsila']!=0) OR  ($row['ginta']!=0) OR  ($row['glovk']!=0)  OR  ($row['gintel']!=0) OR  ($row['gmp']!=0)) {
						if ($get_chitem['unik']>0) {
							$row['unik'] = $get_chitem['unik'];
						}
					}

					if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
					$change .= '<TR bgcolor='.$color.'><TD class="vamiddle" align="center"><IMG SRC="http://i.oldbk.com/i/sh/'.$row['img'].'" BORDER="0"><br>';
					$change .= $act;
					$change .= '</TD><TD valign="top" align="left">';
			                $change .= showitem($row,0,false,'','',0,0,true);
					$change .= "</TD></TR>";
				}
				$change .= "</tbody></table>";
			} else {
				$change .= "<font color=red><b>Для данного предмета нет вариантов обмена!</b></font>";
			}


			$change .= '</td>
				</tr><tr><td align="center"  colspan="3">
				</td></tr>
				</table>';
			echo $change;
			die();
		} else {
			echo '<script>closeinfo();</script>';
			die();
		}
	break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="windows-1251">
<title></title>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/json2.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/jquery.serializejson.min.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/jstorage.min.js'></script>

<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<style>
button:focus {
    outline: 0;
}

input:focus {
    outline: 0;
}


.strong {
	font-weight:bold;
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

.mw300 { min-width:300px; }
.mw1400 { min-width:1200px; }
#page-wrapper table.table.headtitlet {
	min-width:900px;
	margin-bottom:0px;
}
.mw1100 {
	min-width:900px;
}
#page-wrapper table td.vamiddle {
	vertical-align: middle;
}
.vamiddle {
	vertical-align: middle;
}
</style>
<script type='text/javascript'>
RecoverScroll.start();

var bankauth = <?php echo (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) ? "true" : "false" ?>;
var krrepair = 0;
var ekrrepair = 0;
var bankbalance = <?php echo (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) ? $bank['ekr'] : "false" ?>;
var krbalance = <?php echo $user['money'] ?>;
var repbalance = <?php echo $user['repmoney'] ?>;
var goldbalance = <?php echo $user['gold'] ?>;


function SetFormV(json) {
	json = JSON.parse(json);
	$.each(json, function(index, value) {
		$('#'+index).prop('checked', true);
	})
}

function dochng(id) {
	$.get('?razdel=7&chng='+id, function(data) {
		$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+100)+'px'  });
		$('#pl').show(200);
		$('#pl').html(data);
		$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		    e.stopImmediatePropagation();
		});
	});
}

function doauth(mylink,type) {
	var bankid = $('#bankid').val();
	var bankpass = $('#bankpass').val();

	$.get('?view=bankauth&link='+encodeURIComponent(mylink)+'&type='+type+'&bankid='+bankid+'&bankpass='+encodeURIComponent(bankpass), function(data) {
		$('#pl').show(200);
		$('#pl').html(data);
		$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		    e.stopImmediatePropagation();
		});
	});

	return true;
}

function checkbank(mylink,type) {
	if (bankauth) {
		if (type != 1) {
			location.href = mylink;
		}
		return true;
	} else {
		$.get('?view=bankauth&link='+encodeURIComponent(mylink)+'&type='+type, function(data) {
			$('#pl').html(data);
		 	$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'  });
			$('#pl').show(200);
			$('#bankpass').focus();
			$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
			    e.stopImmediatePropagation();
			});
		});

		return false;
	}
}



function showhide(id) {
	if (document.getElementById(id)) {
		document.getElementById(id).style.display="block";
	} else {
		document.getElementById(id).style.display="none";
	}
}

function closehint3(clearstored) {
	document.getElementById("hint3").style.visibility="hidden";
}

function closeinfo() {
	$('#pl').hide(200);
}


$(window).resize(function() {
	$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'  });
});


function SaveSForm(id) {
	$.jStorage.set("storage"+id, JSON.stringify($("#formfilter").serializeJSON()));
}

<?php if ($_SESSION['reprazdel'] == 0) { ?>

var submitdisabled = true;

function DisableSubmit(t) {
	SaveSForm("submitrepair");
	$("#submitrepair").css("color", "gray");
	submitdisabled = true;
	if (t == 1) {
		$("#repaircalc").html("<br>");
	}
}

function CalculateRepair() {
	$("#repaircalc").html("<br>");
	var json =  JSON.stringify($("#formfilter").serializeJSON());
	$.get('?razdel=0&calcrepair='+encodeURIComponent(json), function(data) {
		var ret = JSON.parse(data);
		krrepair = ret['kr'];
		ekrrepair = ret['ekr'];
		goldrepair = ret['gold'];
		if (krrepair > 0 || ekrrepair > 0 || goldrepair > 0) {
			tmp = "Стоимость ремонта:";
			insufmoney = 0;
			if (krrepair > 0) {
				if (krbalance >= krrepair) {
					tmp += " <font color=green>"+krrepair+"</font> кр.";
				} else {
					tmp += " <font color=red>"+krrepair+"</font> кр.";
					insufmoney = 1;
				}
			}
			if (ekrrepair > 0) {
				if (bankbalance === false) {
					$("#repaircalc").html("Требуется <a href='#' OnClick='checkbank(\"\",1);'>вход</a> в банк");
					DisableSubmit();
					return;
				} else {
					if (bankbalance > 0 && bankbalance >= ekrrepair) {
						tmp += " <font color=green>"+ekrrepair+"</font> екр.";
					} else {
						tmp += " <font color=red>"+ekrrepair+"</font> екр.";
						insufmoney = 1;
					}
				}
			}
			if (goldrepair > 0) {
				if (goldbalance >= goldrepair) {
					tmp += ' <font color="green">'+goldrepair+'</font> <img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom: -2px;">';
				} else {
					tmp += ' <font color="red">'+goldrepair+'</font> <img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom: -2px;">';
					insufmoney = 1;
				}
			}

			$("#repaircalc").html(tmp);
			if (insufmoney) {
				DisableSubmit();
			} else {
				$("#submitrepair").css("color", "black");
				submitdisabled = false;
			}
		} else {
			$("#repaircalc").html("Ремонт не требуется");
			DisableSubmit();
		}
		return;
	});
}

function SubmitRepair() {
	if (submitdisabled) return;

	var json =  JSON.stringify($("#formfilter").serializeJSON());
	location.href = '?razdel=0&dorepair='+encodeURIComponent(json);

}
<?php } ?>

<?php if ($_SESSION['reprazdel'] == 2) { ?>


var submitdisabled = true;

function DisableSubmit(t) {
	SaveSForm("submitrecharge");
	$("#submitrecharge").css("color", "gray");
	submitdisabled = true;
	if (t == 1) {
		$("#rechargecalc").html("<br>");
	}
}


function CalculateRecharge() {
	$("#rechargecalc").html("<br>");
	var json =  JSON.stringify($("#formfilter").serializeJSON());
	$.get('?razdel=2&calcrecharge='+encodeURIComponent(json), function(data) {
		var ret = JSON.parse(data);
		krrecharge = ret['kr'];
		ekrrecharge = ret['ekr'];
		reprecharge = ret['rep'];
		if (krrecharge > 0 || ekrrecharge > 0 || reprecharge > 0) {

			tmp = "Стоимость перезаряда:";
			insufmoney = 0;
			if (krrecharge > 0) {
				if (krbalance >= krrecharge) {
					tmp += " <font color=green>"+krrecharge+"</font> кр.";
				} else {
					tmp += " <font color=red>"+krrecharge+"</font> кр.";
					insufmoney = 1;
				}
			}
			if (ekrrecharge > 0) {
				if (bankbalance === false) {
					$("#rechargecalc").html("Требуется <a href='#' OnClick='checkbank(\"\",2);'>вход</a> в банк");
					DisableSubmit();
					return;
				} else {
					if (bankbalance > 0 && bankbalance >= ekrrecharge) {
						tmp += " <font color=green>"+ekrrecharge+"</font> екр.";
					} else {
						tmp += " <font color=red>"+ekrrecharge+"</font> екр.";
						insufmoney = 1;
					}
				}
			}
			if (reprecharge > 0) {
				if (repbalance >= reprecharge) {
					tmp += " <font color=green>"+reprecharge+"</font> реп.";
				} else {
					tmp += " <font color=red>"+reprecharge+"</font> реп.";
					insufmoney = 1;
				}
			}

			$("#rechargecalc").html(tmp);
			if (insufmoney) {
				DisableSubmit();
			} else {
				$("#submitrecharge").css("color", "black");
				submitdisabled = false;
			}
		} else {
			$("#rechargecalc").html("Перезарядка не требуется");
			DisableSubmit();
		}
		return;
	});
}


function SubmitRecharge() {
	if (submitdisabled) return;

	var json =  JSON.stringify($("#formfilter").serializeJSON());
	location.href = '?razdel=2&dorecharge='+encodeURIComponent(json);

}

<?php } ?>

</script>
</head>

<body>
<div id="page-wrapper">
<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 120px;
				width:600px; background-color: #eeeeee; cursor: move;
				border: 1px solid black; display: none;">
</div>
<?php
// квесты и мессаги
if(!isset($_SESSION['beginer_quest']['none']) || !$_SESSION['beginer_quest']['none']) {
	$last_q = check_last_quest(5);
	if($last_q) {
		quest_check_type_5($last_q);
	}

	$last_q=check_last_quest(2);
	if($last_q) {
		quest_check_type_2($last_q);
	}
}

make_quest_div(true);

if (isset($msg) && strlen($msg)) {
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
    <div class="title">
        <div class="h3">
            Ремонтная мастерская
        </div>
        <div id="buttons">
            <a class="button-dark-mid btn" href="#" onclick="window.open('help/repair.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes');return false;" title="Подсказка">Подсказка</a>
            <a class="button-mid btn" OnClick="location.href='?tmp='+Math.random(); return false;" title="Обновить">Обновить</a>
            <a class="button-mid btn" OnClick="document.getElementById('cityform').submit(); return false;" title="Вернуться">Вернуться</a>
	    <FORM action="city.php" style="margin:0px;padding:0px;display:block;" id="cityform" method="GET"><INPUT TYPE="hidden" value="cp" name="cp"></form>
        </div>
    </div>
    <div id="rem">
        <table cellspacing="0" cellpadding="0" class="mw1400">
            <colgroup>
                <col>
                <col width="300px">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <table class="table a_strong headtitlet" cellspacing="0" cellpadding="0">
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 0) echo 'class="active"'; ?> href="?razdel=0">Ремонт</a>
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 2) echo 'class="active"'; ?> href="?razdel=2">Перезарядка</a>
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 3) echo 'class="active"'; ?> href="?razdel=3">Модификация</a>
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 4) echo 'class="active"'; ?> href="?razdel=4">Подгонка</a>
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 5) echo 'class="active"'; ?> href="?razdel=5">Сброс МФ/Статов</a>
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 6) echo 'class="active"'; ?> href="?razdel=6">Рихтовка</a>
                                </div>
                                <div class="head-separate"></div>
                            </th>
                            <th>
                                <div class="head-title p">
                                    <a <?php if ($_SESSION['reprazdel'] == 7) echo 'class="active"'; ?> href="?razdel=7">Обмен вещей</a>
                                </div>
                                <div class="head-right"></div>
                            </th>
                        </tr>
                    </table>
                    <table class="table a_strong border mw1100" cellspacing="0" cellpadding="0">
		            <colgroup>
		                <col width="200px">
		                <col>
		            </colgroup>

<?php
$head = '
<tr class="title">
	<td colspan="8" class="center">
		%TEXT%
	</td>
</tr>
';

$head2= '
<tr class="even2">
	<td colspan="8" class="center">
		<a '.($_SESSION['repdressed'] == 1 ? '' : 'style="font-weight:normal;"').' href="?dressed=1">На персонаже</a> | <a '.($_SESSION['repdressed'] == 0 ? '' : 'style="font-weight:normal;"').' href="?dressed=0">В рюкзаке</a> | <a '.($_SESSION['repdressed'] == 2 ? '' : 'style="font-weight:normal;"').' href="?dressed=2">У наёмников</a>
	</td>
</tr>
';


// секция отображения вещей
switch($_SESSION['reprazdel']) {
	case 0:
		echo str_replace("%TEXT%",'<strong>Починка поврежденных предметов. Ремонт прокатных вещей осуществляется за екр.</strong>',$head);
		echo $head2;

		if ($_SESSION['repdressed'] == 1) {
			$query['repair'] .= ' and dressed = 1';
		} elseif ($_SESSION['repdressed'] == 2) {
			$query['repair'] .= ' and naem > 0';
		} else {
			$query['repair'] .= ' and dressed = 0';
		}

		$data = mysql_query($query['repair'].' ORDER BY `duration` DESC '.MakeLimit());

		$p = MakePages();
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}


		$out = "";
		$i = 0;

		while($row = mysql_fetch_array($data)) {
		        $arr = GetRepairPrice($row,$user);
			$act = '<small>('.get_item_fid($row).')</small><br>';

			if($row['duration'] < $row['maxdur']) {
				if($row['duration'] > 0) {
					if ($arr['type'] == 2) {
						$act .= '<A HREF="#" OnClick="checkbank(\'?razdel=0&repair='.$row['id'].'\'); return false;">Ремонт 1 ед. за '.$arr['onecost'].' '.$arr['typetxt'].'</A><br>';
					} else {
						$act .= '<A HREF="?razdel=0&repair='.$row['id'].'">Ремонт 1 ед. за '.$arr['onecost'].' '.$arr['typetxt'].'</A><br>';
					}
				}
				if($row['duration'] >= 10) {
					if ($arr['type'] == 2) {
						$act .= '<A HREF="#" OnClick="checkbank(\'?razdel=0&repair='.$row['id'].'&count=10\'); return false;">Ремонт 10 ед. за '.($arr['onecost']*10).' '.$arr['typetxt'].'</A><br>';
					} else {
						$act .= '<A HREF="?razdel=0&repair='.$row['id'].'&count=10">Ремонт 10 ед. за '.($arr['onecost']*10).' '.$arr['typetxt'].'</A><br>';
					}
				}
			}

			if($row['duration'] >1) {
				if ($arr['type'] == 2) {
					$act .= '<A HREF="#" OnClick="checkbank(\'?razdel=0&repair='.$row['id'].'&count=full\'); return false;">Полный ремонт за '.($arr['onecost']*$row['duration']).' '.$arr['typetxt'].'</A><br>';
				} else {
					$act .= '<A HREF="?razdel=0&repair='.$row['id'].'&count=full">Полный ремонт за '.($arr['onecost']*$row['duration']).' '.$arr['typetxt'].'</A><br>';
				}
			}

			$out .= renderrepitem($row,$act,$i);
		}

		echo $out;
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
	break;
	case 2:
		echo str_replace("%TEXT%",'<strong>Перезарядка встроеной магии</strong><br><i>Если в предмет встроена магия, мы поможем ее перезарядить за умеренную плату. Учтите, ничто не вечно под луной, в том числе и магия, рано или поздно встроенный свиток исчерпает все свои ресурсы, и мы уже не сможем его перезарядить.</i>',$head);
		echo $head2;
		// перезарядка

		if ($_SESSION['repdressed'] == 1) {
			$query['recharge'] .= ' and dressed = 1';
		} elseif ($_SESSION['repdressed'] == 2) {
			$query['recharge'] .= ' and naem > 0';
		} else {
			$query['recharge'] .= ' and dressed = 0';
		}

		$data = mysql_query($query['recharge']. ' ORDER by `update` DESC '.MakeLimit());
		$p = MakePages();
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}


		$out = "";
		$i = 0;

		while($row = mysql_fetch_array($data)) {
			$ret = CalculateRechargePrice($row,$user);
			$act = '<small>('.get_item_fid($row).')</small><br>';
			if ($ret['type'] == 2) {
				$act .= '<a HREF="#" OnClick="checkbank(\'?razdel=2&recharge='.$row['id'].'\'); return false;">Перезарядить за '.$ret['price'].' '.$ret['typetxt'].'</a>';
			} else {
				$act .= '<a href="?razdel=2&recharge='.$row['id'].'">Перезарядить за '.$ret['price'].' '.$ret['typetxt'].'</a>';
			}
			$out .= renderrepitem($row,$act,$i);
		}
		echo $out;
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
	break;
	case 3:
		// мф
		if ($close_razdel_mf) {
			echo str_replace("%TEXT%",$prnt_inf,$head);
			break;
		}

		echo str_replace("%TEXT%",'<strong>Модификация предметов</strong><br><i>Наши мастера помогут вам модифицировать ваши доспехи. К сожалению, технология не позволяет повторно модифицировать вещи.</i>',$head);

		$mf_select = $query['mf'].' ORDER by i.update DESC '.MakeLimit();

		$out = "";
		$i = 0;
		$data = mysql_query($mf_select);

		$p1 = MakePages();
		if ($p1) {
			echo str_replace("%TEXT%",$p1,$head);
		}

		while($row = mysql_fetch_assoc($data)) {
			$p = array();
			if ($row['charka'] != '') {
	  			$charka=substr($row['charka'], 2,strlen($row['charka'])-1); //откидываем первые два символа
				$inputbonus=unserialize($charka); //все данные
				if (is_array($inputbonus)) {
					foreach($inputbonus as $blevl => $bdata) {
						foreach($bdata as $pk => $pv) {
							foreach($pv as $k => $v) {
								$p[$k]+=$v;
							}
						}
					}
				}
  			}

			$no_stats = false;
			if ((($row['gsila']-$p['gsila']) <= 0) and (($row['glovk']-$p['glovk']) <= 0) and (($row['ginta']-$p['ginta']) <= 0) and (($row['gintel']-$p['gintel']) <= 0)) {
				$no_stats = true;
			}

			$cost = (int)$row['shcost'];
			if($no_stats) {
				$cost = round(($row['shcost'])*0.5, 0);
			}

			$cost=round($cost*0.5);

			$act = '<small>('.get_item_fid($row).')</small><br><A HREF="?razdel=3&mf='.$row['id'].'" onclick="if(!confirm(\'Вы действительно хотите модифицировать эту вещь?\')){ return false;}">Модифицировать за '.$cost.' кр.</A><BR>';
			$out .= renderrepitem($row,$act,$i);
		}
		echo $out;
		if ($p1) {
			echo str_replace("%TEXT%",$p1,$head);
		}
	break;
	case 4:
		// подгон
		echo str_replace("%TEXT%",'<strong>Подгонка предметов</strong><br><i>Наши мастера помогут вам подогнать ваши модифицированые доспехи.<br>Всего возможно подогнать: 5 раз</i>',$head);

		$out = "";
		$i = 0;
		$data = mysql_query($query['podgon'].' ORDER by i.id DESC '.MakeLimit());
		$p = MakePages();
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}

		while($row = mysql_fetch_assoc($data)) {
			$up_cost=$row['shcost'];
			$max_ups_left = $max_ups - $row['ups'];
			$costs = upgrade_item($up_cost,$max_ups_left);

			$act = '<small>('.get_item_fid($row).')</small><br><A HREF="?razdel=4&podgon='.$row['id'].'" onclick="if(!confirm(\'Вы действительно хотите подогнать эту вещь?\')){ return false;}">Подогнать за '.$costs['up_cost'].' кр.</A>';
			$out .= renderrepitem($row,$act,$i);
		}
		echo $out;
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
	break;
	case 5:
		// сброс мф-статов
		echo str_replace("%TEXT%",'<strong>Тут вы можете сбросить МФ и статы на ваших вещах или рунах.</strong><br> У рун должны быть распределены МФ или статы для сброса. Для сброса статов и модификаторов обмундирования, его необходимо снять с персонажа.',$head);
		$data = mysql_query($query['resetms'].' ORDER BY `id` DESC');

		$mf_stat_rate_a = $mf_stat_rate;

		if(time() > $start_volna && time() < $end_volna)
		{
			if (($user['id']==14897) or ($user['id']==194066) )
			{
		//	echo "VOLNA".$mf_stat_rate_a;
			}
		}

		$out = "";
		$i = 0;
		$count = 0;
	    	while($row = mysql_fetch_assoc($data)) {
			// эмулиция страниц, т.к. тут лимит в запросе не проканает
			if ($row['type'] == 30) {
				if ($row['up_level'] > 0) {
					$act = '<small>('.get_item_fid($row).')</small><br>';
					$can = 0;

					if (($row['prototype']==6018) OR ($row['prototype']==6019) OR ($row['prototype']==6020))
					{
					//спец руны
						if ($row['mfbonus'] == 0) {
																			$act .= '<A href="#" OnClick="if(confirm(\'Вы уверены что хотите сбросить МФ?\')) { location.href=\'?razdel=5&dezmf='.$row['id'].'\';} return false;">Сбросить МФ (бесплатно)</A><br>';
																			$can = 1;
																			}
						if ($row['up_level'] >= 2 && $row['stbonus'] == 0) {
																			$act .= '<A href="#" OnClick="if(confirm(\'Вы уверены что хотите сбросить статы?\')) {location.href = \'?razdel=5&dezst='.$row['id'].'\';} return false;">Сбросить статы (бесплатно)</A><br>';
																			$can = 1;
																			}
					}
				else {
					if ($row['mfbonus'] == 0) {
						if ($free_rune_reset)  {
							$act .= '<A href="#" OnClick="if(confirm(\'Вы уверены что хотите сбросить МФ?\')) { location.href=\'?razdel=5&dezmf='.$row['id'].'\';} return false;">Сбросить МФ ('.$rune_reset_mf.' екр.)</A><br>';
						} else {
							$act .= '<A href="#" OnClick="if(confirm(\'Вы уверены что хотите сбросить МФ?\')) { checkbank(\'?razdel=5&dezmf='.$row['id'].'\');} return false;">Сбросить МФ ('.$rune_reset_mf.' екр.)</A><br>';
						}
						$can = 1;
					}

					if ($row['up_level'] >= 2 && $row['stbonus'] == 0) {
						if ($free_rune_reset) {
							$act .= '<A href="#" OnClick="if(confirm(\'Вы уверены что хотите сбросить статы?\')) {location.href = \'?razdel=5&dezst='.$row['id'].'\';} return false;">Сбросить статы ('.$rune_reset_mf.' екр.)</A><br>';
						} else {
							$act .= '<A href="#" OnClick="if(confirm(\'Вы уверены что хотите сбросить статы?\')) {checkbank(\'?razdel=5&dezst='.$row['id'].'\');} return false;">Сбросить статы ('.$rune_reset_mf.' екр.)</A><br>';
						}
						$can = 1;
					}
				}
					
					if ($can) {
						$count++;
						if ($_SESSION['reppage'.$_SESSION['reprazdel']] === "all" || ($count > $_SESSION['reppage'.$_SESSION['reprazdel']]*$viewperpage && $count <= (($_SESSION['reppage'.$_SESSION['reprazdel']]+1)*$viewperpage))) {
							$out .= renderrepitem($row,$act,$i);
						}
					}
				}
			} else {
	        		$rr = n_fields('ups');
	        		$dn_item = downgrade_item($row,$rr,1);

	  			if($dn_item['delta_stat'] != $row['stbonus'] || $dn_item['delta_mf'] != $row['mfbonus'] || (($row['nlevel']>$dn_item['prot_nlevel'] && $row['up_level']>$dn_item['prot_nlevel']))) {
					$act = '<small>('.get_item_fid($row).')</small><br>';
					$can = 0;


				  	if($dn_item['delta_stat']>0 && ($dn_item['sh_stat']-$row['stbonus']) != $dn_item['prot_stat']) {
						$act .= '<A HREF="?razdel=5&dezst='.$row['id'].'">Сбросить статы ('.($mf_stat_rate_a > 0 ? $dn_item['pr_stat']*$mf_stat_rate_a : '0') .' кр.)</A><br>';
						$can = 1;
					}
					if($dn_item['delta_mf']>0 && ($dn_item['sh_mf'] - $row['mfbonus']) != $dn_item['prot_mf']) {
						$act .= '<A HREF="?razdel=5&dezmf='.$row['id'].'">Сбросить МФ ('.($mf_stat_rate_a > 0 ? $dn_item['pr_mf']*$mf_stat_rate_a : '0').' кр.)</A><br>';
						$can = 1;
					}


					if ($can) {
						$count++;

						if ($_SESSION['reppage'.$_SESSION['reprazdel']] === "all" || ($count > $_SESSION['reppage'.$_SESSION['reprazdel']]*$viewperpage && $count <= (($_SESSION['reppage'.$_SESSION['reprazdel']]+1)*$viewperpage))) {
							$out .= renderrepitem($row,$act,$i);
						}
					}
	        		}
			}
		}
		$p = MakePages($count,1);
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
		echo $out;
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
	break;
	case 6:
		// рихтовка
		echo str_replace("%TEXT%",'<strong>Тут вы можете отрихтовать свои вещи. Это совершенно бесплатно.</strong>',$head);

		$out = "";
		$i = 0;
		$data = mysql_query($query['riht']. " ORDER by `id` DESC");

		$count = 0;

		while($row = mysql_fetch_array($data)) {
			$mfinfo = unserialize($row['mfinfo']);

			if(($mfinfo['hp'] < 20 && $mfinfo['hp'] > 0) || ($mfinfo['stats'] < 2 && $mfinfo['stats'] > 0) || ($mfinfo['bron'] < 3 && $mfinfo['bron'] > 0)) {
				$act = '<small>('.get_item_fid($row).')</small><br>Рихтовать бесплатно: <br>';

				if($mfinfo['stats'] < 2 && $mfinfo['stats'] > 0) {
				        $act .= '<a href="?razdel=6&riht='.$row['id'].'&stat=1">Статы +'.(2-$mfinfo['stats']).'</a><br><br>';
			        }

			        if($mfinfo['hp'] < 20 && $mfinfo['hp'] > 0) {
			        	$act .= '<a href="?razdel=6&riht='.$row['id'].'&hp=1">Жизни +'.(20-$mfinfo['hp']).'</a><br><br>';
			        }

			        if($mfinfo['bron'] < 3 && $mfinfo['bron'] > 0) {
			        	$act .= '<a href="?razdel=6&riht='.$row['id'].'&br=1">Броню +'.(3-$mfinfo['bron']).'</a><br><br>';
			        }

				$count++;

				if ($_SESSION['reppage'.$_SESSION['reprazdel']] === "all" || ($count > $_SESSION['reppage'.$_SESSION['reprazdel']]*$viewperpage && $count <= (($_SESSION['reppage'.$_SESSION['reprazdel']]+1)*$viewperpage))) {
					$out .= renderrepitem($row,$act,$i);
				}
			}
		}

		$p = MakePages($count,1);
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
		echo $out;
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
	break;
	case 7:
		// обмен вещей
		echo str_replace("%TEXT%",'<strong>Бесплатный обмен слот-в-слот устаревших вещей 8-го уровня и выше (кроме оружия, легкой брони, рун, артефактов и плащей).</strong><br>Обменять устаревшие артефакты, уникальные и улучшенные уникальные предметы слот-в-слот можно бесплатно в Коммерческом отделе.',$head);

		$glava_sql = '';
		if($user['klan'] != '') {
			$klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans where short='".$user['klan']."';"));
			if($user['id'] == $klan['glava']) {
				$glava_sql = ' OR (arsenal_klan="'.$user['klan'].'" AND arsenal_owner=1)';
				$glava=1;
			}
		}

		$query['exchange'] .= ' AND (arsenal_klan = "" '.$glava_sql.') ';
		$query['exchange2'] .= ' AND (arsenal_klan = "" '.$glava_sql.') ';


		$out = "";
		$i = 0;
		$data = mysql_query($query['exchange'].' ORDER BY `update` DESC '.MakeLimit());
		$chtype = 1;

		if (mysql_num_rows($data) == 0) {
			$data = mysql_query($query['exchange2'].' ORDER BY `update` DESC '.MakeLimit());
			$chtype = 2;
		}

		$count = 0;

	    	while($row = mysql_fetch_assoc($data)) {
			if ($chtype == 2) {
				$proto = GetItemPrototype($row['prototype']);
				if ($proto['nlevel'] < 8) continue;
			}

			$count++;

			$act = '<small>('.get_item_fid($row).')</small><br><A href="#" OnClick="dochng('.$row['id'].'); return false;">Обменять</A>';

			if ($_SESSION['reppage'.$_SESSION['reprazdel']] === "all" || ($count > $_SESSION['reppage'.$_SESSION['reprazdel']]*$viewperpage && $count <= (($_SESSION['reppage'.$_SESSION['reprazdel']]+1)*$viewperpage))) {
				$out .= renderrepitem($row,$act,$i);
			}
		}

		$p = MakePages($count,1);
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}

		echo $out;
		if ($p) {
			echo str_replace("%TEXT%",$p,$head);
		}
	break;
}

?>

                        </tbody>
                    </table>
                </td>
                <td>
		    <form id="formfilter">
                    <table id="filter" class="mw300" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
				<strong>
                                Вес всех ваших вещей:
				<?php
					$my_massa=0;
					$q = mysql_query("SELECT IFNULL(sum(`massa`),0) as massa , setsale, bs_owner, dressed  FROM oldbk.inventory WHERE `owner` = '{$user['id']}'   GROUP by setsale,bs_owner,dressed ");
					while ($row = mysql_fetch_array($q)) {
						if (($user['in_tower'] == $row['bs_owner']) AND   ($row['setsale'] ==0 )  AND   ($row['dressed'] ==0)) {
							$my_massa+=$row['massa'];
						}
					}
					echo $my_massa;?>/<?=get_meshok()?>
				</strong>
				<br>
                                У Вас в наличии: <span class="money strong"><?=$user['money']?></span><strong> кр.</strong><br>
				<?php if ($bank) { ?>
                                В банке: <span class="money strong"><?=$bank['ekr']?></span><strong> екр.</strong><br>
				<?php } ?>
                                Репутация покупки: <span class="money strong"><?=$user['repmoney']?></span><strong> реп.</strong><br>
                                Золотых монет: <span class="money strong"><?=$user['gold']?></span> <img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom: -2px;"><br><br>
                            </td>
                        </tr>
			<?php if ($_SESSION['reprazdel'] == 0) { ?>
                        <tr>
                            <td class="hint-block center">Ремонтировать предметы</td>
                        </tr>
                        <tr>
                            <td><input checked name="item_dressed" id="item_dressed" type="checkbox" OnClick="DisableSubmit(1);"> надетые на персонажа</td>
                        </tr>
                        <tr>
                            <td><input name="item_inventory" id="item_inventory" type="checkbox" OnClick="DisableSubmit(1);"> из рюкзака персонажа</td>
                        </tr>
                        <tr>
                            <td><input name="item_naem" id="item_naem" type="checkbox" OnClick="DisableSubmit(1);"> у наёмников</td>
                        </tr>
                        <tr>
                            <td class="hint-block center">Укажите тип предмета</td>
                        </tr>
                        <tr>
                            <td><input checked name="item_critical" id="item_critical" type="checkbox" OnClick="DisableSubmit(1);"> только в критическом состоянии</td>
			</tr>
			<tr>
                            <td><input name="item_art" id="item_art" OnClick="$('#item_aeart').prop('checked', false);$('#item_prokat').prop('checked', false);$('#item_fair').prop('checked', false);DisableSubmit(1);" type="checkbox"> только артефакты</td>
			</tr>
			<tr>
                            <td><input name="item_aeart" id="item_aeart" OnClick="$('#item_art').prop('checked', false);$('#item_prokat').prop('checked', false);$('#item_fair').prop('checked', false);DisableSubmit(1);" type="checkbox"> все, кроме артефактов и вещей ярмарки</td>
			</tr>
			<tr>
                            <td><input name="item_prokat" id="item_prokat" OnClick="$('#item_art').prop('checked', false);$('#item_aeart').prop('checked', false);$('#item_fair').prop('checked', false);DisableSubmit(1);" type="checkbox"> только прокатные вещи</td>
			</tr>
			<tr>
                            <td><input name="item_fair" id="item_fair" OnClick="$('#item_art').prop('checked', false);$('#item_aeart').prop('checked', false);$('#item_prokat').prop('checked', false);DisableSubmit(1);" type="checkbox"> только вещи ярмарки</td>
			</tr>
			<tr>
                            <td><a href="#" id="calculaterepair" class="button-big btn" title="Рассчитать" onClick="CalculateRepair();return false;">Рассчитать</a></td>
			</tr>
			<tr>
                            <td><span id="repaircalc"></span></td>
			</tr>
			<tr>
                            <td><a href="#" id="submitrepair" class="button-big btn" title="Ремонтировать" onClick="SubmitRepair();return false;">Ремонтировать</a></td>
			</tr>
			<?php } ?>

			<?php if ($_SESSION['reprazdel'] == 2) { ?>
                        <tr>
                            <td class="hint-block center">Перезаряжать магию</td>
                        </tr>
                        <tr>
                            <td><input checked name="item_dressed" id="item_dressed" type="checkbox" OnClick="DisableSubmit(1);"> надетые на персонажа</td>
                        </tr>
                        <tr>
                            <td><input name="item_inventory" id="item_inventory" type="checkbox" OnClick="DisableSubmit(1);"> из рюкзака</td>
                        </tr>
                        <tr>
                            <td class="hint-block center">Выберите тип встройки</td>
                        </tr>
                        <tr>
                            <td><input checked name="item_kr" id="item_kr" type="checkbox" OnClick="DisableSubmit(1);"> за кредиты</td>
			</tr>
			<tr>
                            <td><input name="item_ekr" id="item_ekr" OnClick="DisableSubmit(1);" type="checkbox"> за еврокредиты</td>
			</tr>
			<tr>
                            <td><input name="item_rep" id="item_rep" OnClick="DisableSubmit(1);" type="checkbox"> за репутацию</td>
			</tr>
			<tr>
                            <td><a href="#" id="calculaterepair" class="button-big btn" title="Рассчитать" onClick="CalculateRecharge();return false;">Рассчитать</a></td>
			</tr>
			<tr>
                            <td><span id="rechargecalc"></span></td>
			</tr>
			<tr>
                            <td><a href="#" id="submitrecharge" class="button-big btn" title="Перезарядить" onClick="SubmitRecharge();return false;">Перезарядить</a></td>
			</tr>
			<?php } ?>
                        </tbody>
                    </table>
                    <img src="http://i.oldbk.com/i/images/rem/rem_illustration.jpg">
		    </form>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="hint3" class="ahint"></div>
<script type="text/javascript">
$(function() {
	$("#pl").draggable();
	$(window).resize();
	if (typeof CalculateRepair !== 'undefined' && typeof CalculateRepair === 'function') {
		var s = $.jStorage.get("storagesubmitrepair");
		if (s != null) {
			SetFormV(s);
		}
		CalculateRepair();
	}
	if (typeof CalculateRecharge !== 'undefined' && typeof CalculateRecharge === 'function') {
		var s = $.jStorage.get("storagesubmitrecharge");
		if (s != null) {
			SetFormV(s);
		}
		CalculateRecharge();
	}
});
</script>
</body>
</html>
<?php
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
