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

session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";
include "bank_functions.php";
require_once("config_ko.php");

if (!ADMIN) {
	if ($user['room'] != 72) { header("Location: main.php");  die(); }
	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($_SESSION['boxisopen'] != 'open') { header('location: main.php?edit=1'); die(); }
}


if (isset($_POST['fallfair'])) {
	$_SESSION['fallfair']=(int)$_POST['fallfair'];
} else {
	$_POST['fallfair']=(int)$_SESSION['fallfair'];
}

if (!isset($_SESSION[$_SERVER['PHP_SELF'].'nodbl'])) $_SESSION[$_SERVER['PHP_SELF'].'nodbl'] = mt_rand();

// уровни для расчёта лимитов
$levelmin = 6;
$levelmax = 14;


$charka_config[100016]=array(
'charka'=>'Великое чарование IV',
'charka_level'=>4,
'ghp'=> 35,
'stbonus'=>4,
'mfbonus'=> 40,
'gfire' =>1,
'gwater' => 1,
'gair' => 1,
'gearth' => 1,
'gtopor' => 1,
'gdubina' => 1,
'gmech' => 1 );


$prems_array=array(54907,54914,54999,55907,55914,55999,56907,56914,56999,4008,4020);
/*
	//акция 50% на пермы
	 54907 «Silver» аккаунт на 7 дней
	 54914 «Silver» аккаунт на 14 дней
	 54999 «Silver» аккаунт на 30 дней
	 55907 «Gold» аккаунт на 7 дней
	 55914 «Gold» аккаунт на 14 дней
	 55999 «Gold» аккаунт на 30 дней
	 56907 «Platinum» аккаунт на 7 дней
	 56914 «Platinum» аккаунт на 14 дней
	 56999 «Platinum» аккаунт на 30 дней
*/


if (ADMIN) {
	ini_set('display_errors',1);
	error_reporting(E_ERROR);
}

// удаление вещи из ярмарки
if (isset($_GET['removefromfair']) && ADMIN) {
	$_GET['removefromfair'] = intval($_GET['removefromfair']);

	$q = mysql_query('SELECT * FROM fair_shop WHERE fairid = '.$_GET['removefromfair']);
	if (mysql_num_rows($q) > 0) {
		$item = mysql_fetch_assoc($q);

		mysql_query('DELETE FROM fair_buy WHERE fairprototype = '.$item['fairprototype'].' and fairshoptype = '.$item['fairshoptype']);
		mysql_query('DELETE FROM fair_shop WHERE fairid = '.$_GET['removefromfair']);

		$msg = "Вещь удалена из ярмарки и статистика покупок у юзеров обнулена";
	} else {
		$msg = "Ошибка удаления";
		$typet = "e";
	}
}

// добавление вещи в ярмарку
if (isset($_GET['goitem']) && ADMIN) {
	$arr = array();
	for ($i = $levelmin; $i <= $levelmax; $i++) {
		$arr["level".$i] = $_GET['level'.$i];
	}

	$fairpresent = isset($_GET['itempresent']) ? 1 : 0;
	$fairisowner = isset($_GET['itemisowner']) ? 1 : 0;
	$fairhidecount = isset($_GET['itemhidecount']) ? 1 : 0;
	$fairhidecount2 = isset($_GET['itemhidecount2']) ? 1 : 0;
	$fairnotsell = isset($_GET['itemnotsell']) ? 1 : 0;
	$fairismf = isset($_GET['itemismf']) ? 1 : 0;
	$fairgoden = 0;
	$fairendsell = 0;
	$fairstartsell = 0;
	$fairbanner=isset($_GET['fairbanner']) ? (int)($_GET['fairbanner']): 0;

	$faircharka=isset($_GET['faircharka']) ? (int)($_GET['faircharka']): 0;



	if (isset($_GET['itemgoden']) && !empty($_GET['itemgoden'])) {
		$t = explode("/",$_GET['itemgoden']);
		if (count($t) == 3) {
			$fairgoden = mktime(23,59,59,$t[1],$t[0],$t[2]);
			$_SESSION['fairgoden'] = $_GET['itemgoden'];
		}
	}

	if (isset($_GET['itemendsell']) && !empty($_GET['itemendsell'])) {
		$t = explode("/",$_GET['itemendsell']);
		if (count($t) == 3) {
			$fairendsell = mktime(23,59,59,$t[1],$t[0],$t[2]);
			$_SESSION['fairendsell'] = $_GET['itemendsell'];
		}
	}

	if (isset($_GET['itemstartsell']) && !empty($_GET['itemstartsell'])) {
		$t = explode("/",$_GET['itemstartsell']);
		if (count($t) == 3) {
			$fairstartsell = mktime(0,0,0,$t[1],$t[0],$t[2]);
			$_SESSION['fairstartsell'] = $_GET['itemstartsell'];
		}
	}


	$_GET['itemid'] = trim($_GET['itemid']);

	if (empty($_GET['itemid'])) {
		$shop = $_GET['itemshop'] == 1 ? "eshop" : "shop";
		$q = mysql_query('SELECT * FROM '.$shop.' WHERE name LIKE "%'.$_GET['itemname'].'%"');
		$row = mysql_fetch_assoc($q);
		$_GET['itemid'] = $row['id'];
	}



	$q = mysql_query('SELECT * FROM fair_shop WHERE fairprototype = '.$_GET['itemid'].' and fairshoptype = '.intval($_GET['itemshop']));
	if (mysql_num_rows($q) == 0)
	{
		$msg = "Товар удачно добавлен!";
	} else
	{
		$msg = "Такой товар уже был на ярмарке. Товар удачно добавлен!";
	}


		mysql_query('INSERT INTO fair_shop (fairprototype,faircount,fairprice,fairshoptype,fairlimits,fairpresent,fairisowner,fairgoden,fairhidecount,fairhidecount2,fairnotsell,fairismf,fairstartsell,fairendsell,fairgodenday,fairbanner,faircharka) VALUES("'.intval($_GET['itemid']).'","'.intval($_GET['itemcount']).'","'.intval($_GET['itemprice']).'","'.intval($_GET['itemshop']).'","'.mysql_real_escape_string(serialize($arr)).'","'.$fairpresent.'","'.$fairisowner.'","'.$fairgoden.'","'.$fairhidecount.'","'.$fairhidecount2.'","'.$fairnotsell.'","'.$fairismf.'","'.$fairstartsell.'","'.$fairendsell.'","'.intval($_GET['itemgodenday']).'","'.$fairbanner.'","'.$faircharka.'")') or die(mysql_error());
		//header("Location: ?additem=1&msg=".rawurlencode($okmsg));
		//die();

}

// работа с профилями при добавлении вещей на ярмарку
if (isset($_GET['saveprofile']) && !empty($_GET['saveprofile']) && ADMIN) {
	$res = array();
	$res["itemcount"] = $_GET['itemcount'];
	$res["itemprice"] = $_GET['itemprice'];
	for ($i = $levelmin; $i <= $levelmax; $i++) {
		$res['level'.$i] = $_GET['level'.$i];
	}
	mysql_query('INSERT INTO fair_profiles (profilename,profiledata) VALUES("'.$_GET['saveprofile'].'","'.mysql_real_escape_string(serialize($res)).'")');
	echo mysql_error();
	die();
}

if (isset($_GET['listprofiles']) && !empty($_GET['listprofiles']) && ADMIN) {
	$res = array();
	$q = mysql_query('SELECT * FROM fair_profiles');
	while($i = mysql_fetch_assoc($q)) {
		$res[$i['id']] = unserialize($i['profiledata']);
		$res[$i['id']]['profilename'] = $i['profilename'];
	}
	echo json_encode($res);
	die();
}

if (isset($_GET['deleteprofile']) && !empty($_GET['deleteprofile']) && ADMIN) {
	mysql_query('DELETE FROM fair_profiles WHERE id = '.intval($_GET['deleteprofile']));
	die();

}

// разделы
if (ADMIN) {
	$data = mysql_query("SELECT * FROM fair_shop LEFT JOIN shop ON fair_shop.fairprototype = shop.id WHERE fairshoptype = 0");
	$data2 = mysql_query("SELECT * FROM fair_shop LEFT JOIN eshop ON fair_shop.fairprototype = eshop.id WHERE fairshoptype = 1");
} else {
	$data = mysql_query("SELECT * FROM fair_shop LEFT JOIN shop ON fair_shop.fairprototype = shop.id WHERE fairshoptype = 0 and faircount > 0 and (fairendsell = 0 or fairendsell > ".time().") and (fairstartsell = 0 or fairstartsell < ".time().")");
	$data2 = mysql_query("SELECT * FROM fair_shop LEFT JOIN eshop ON fair_shop.fairprototype = eshop.id WHERE fairshoptype = 1 and faircount > 0 and (fairendsell = 0 or fairendsell > ".time().") and (fairstartsell = 0 or fairstartsell < ".time().")");
}

$otdels = array();
$firstrazdel = -1;

while(($row = mysql_fetch_assoc($data)) || ($row = mysql_fetch_assoc($data2))) {
	if ($firstrazdel == -1) $firstrazdel = $row['razdel'];
	$otdels[$row['razdel']] = 1;
}

if (isset($_GET['otdel'])) {
	$_GET['otdel'] = intval($_GET['otdel']);
	$_SESSION['fairlastotdel'] = $_GET['otdel'];
} else {
	if (isset($_SESSION['fairlastotdel'])) {
		if (is_numeric($_SESSION['fairlastotdel'])) {
			$_GET['otdel'] = $_SESSION['fairlastotdel'];
		} else {
			unset($_GET['otdel']);
			$_GET['sellitems'] = 1;
		}
	} else {
		$_SESSION['fairlastotdel'] = $firstrazdel;
		$_GET['otdel'] = $firstrazdel;
	}
}

$d[0] = getmymassa($user);

$gold = $user['gold'];


$sellquery = "SELECT * FROM oldbk.`inventory` LEFT JOIN fair_buy_log ON inventory.id = fair_buy_log.itemid WHERE `owner` = '{$user['id']}' AND `dressed` = 0 AND type!=200 and type!=77
		AND prokat_idp = 0 AND arsenal_klan='' AND `setsale`=0 AND present!='Арендная лавка'
		and getfrom = 1 AND bs_owner = 0 AND fair_buy_log.itemid is not null AND fair_buy_log.itemmaxdur = inventory.maxdur AND duration = 0 AND (inventory.dategoden > ".(time()+30*24*3600)." or inventory.dategoden = 0)
";


if (isset($_GET['set']) || isset($_POST['set'])) {
	$data = $_GET;
	if (isset($_POST['set'])) {
		$data = $_POST;
	}
	$data['count'] = intval($data['count']);
	if (!isset($data['count']) || $data['count'] < 1) $data['count'] = 1;

	$data['set'] = intval($data['set']);

	$fair = mysql_fetch_assoc(mysql_query('SELECT * FROM fair_shop WHERE fairid = '.$data['set']));
	if ($fair) {
		$shop = $fair['fairshoptype'] == 1 ? "eshop" : "shop";

		if (ADMIN) {
			$dress = mysql_fetch_assoc(mysql_query('SELECT * FROM fair_shop LEFT JOIN '.$shop.' ON fair_shop.fairprototype = '.$shop.'.id WHERE faircount > 0 and fairid = '.$data['set']));
		} else {
			$dress = mysql_fetch_assoc(mysql_query('SELECT * FROM fair_shop LEFT JOIN '.$shop.' ON fair_shop.fairprototype = '.$shop.'.id WHERE faircount > 0 and (fairendsell = 0 or fairendsell > '.time().') and (fairstartsell = 0 or fairstartsell < '.time().') and fairid = '.$data['set']));
		}

		if ((time()>$KO_start_time48) and (time()<$KO_fin_time48)) {
			if (in_array($dress['fairprototype'] ,$prems_array)) {
				$dress['fairprice']=(int)($dress['fairprice']*0.5);
			}
		} else if ($dress['fairbanner']==10||$dress['fairbanner']==20||$dress['fairbanner']==30||$dress['fairbanner']==40||$dress['fairbanner']==50) {
			$dress['fairprice']=ceil($dress['fairprice']*(100-$dress['fairbanner'])*0.01);
		} elseif ((time()>$KO_start_time41) and (time()<$KO_fin_time41)) {
			if (in_array($dress['fairprototype'] ,$FSHOP_ITEMS_41)) {
				$dress['fairprice']=ceil($dress['fairprice']*(100-$FSHOP_RATE_41)*0.01);
			} elseif ($dress['fairprototype']==3005000) {
				$dress['fairprice']=(int)($dress['fairprice']*0.5);
			}
		}


		if ($dress['fairgoden'] > 0) {
			$goden_do = $dress['fairgoden'];
			$goden = round(($goden_do-time())/60/60/24); if ($goden<1) {$goden=1;}
		} elseif ($dress['fairgodenday'] > 0) {
			$goden = $dress['fairgodenday'];
			$goden_do = time()+($goden*3600*24);
		} else {
			if ($dress['goden'] > 0) {
				$goden = $dress['goden'];
				$goden_do = time()+($goden*3600*24);
			}
		}

		if ($dress['fairpresent']) $dress['present'] = "Торговец Галиас";
		if ($dress['fairisowner']) {
			$dress['sowner'] = $user['id'];
		} else {
			$dress['sowner'] = 0;
		}

		$dress['dategoden'] = $goden_do;
		$dress['goden'] = $goden;

		$dress['charka']='';

		if (($dress['faircharka']>0) and is_array($charka_config[$dress['faircharka']]) )
			{
			//есть чарка в настройке
			$add_array=array();
			$inputbonus=array();
			$add_array=$charka_config[$dress['faircharka']];
			$charka_level=$add_array['charka_level'];
			unset($add_array['charka']);
			unset($add_array['charka_level']);
			foreach ($add_array as $k=>$v)
					{
					$dress[$k]+=$v;
					$inputbonus[$charka_level][][$k]=$v;
					}
			$dress['charka']=$charka_level."|".serialize($inputbonus);
			}



		if (($dress['massa']*$data['count']+$d[0]) > (get_meshok())) {
			$msg = "Недостаточно места в рюкзаке.";
			$typet = "e";
		} elseif($gold >= $dress['fairprice']*$data['count'] && $dress['faircount'] >= $data['count']) {

			$buyed = array();
			$q = mysql_query('SELECT * FROM fair_buy WHERE fairowner = '.$user['id'].' and fairprototype = '.$fair['fairprototype'].' and fairshoptype = '.$fair['fairshoptype']);
			while($i = mysql_fetch_assoc($q)) {
				$buyed[$i['fairprototype']][$i['fairshoptype']] = $i['faircount'];
			}


			$dress['fairlimits'] = unserialize($dress['fairlimits']);

			$limit = $dress['fairlimits']['level'.$user['level']];
			if (!$limit) $limit = 0;
			if (isset($buyed[$dress['fairprototype']][$dress['fairshoptype']])) {
				$limit = $limit - $buyed[$dress['fairprototype']][$dress['fairshoptype']];
			}
			if ($limit < 0) $limit = 0;
			$dress['fairlimit'] = $limit;

			if ($data['count'] <= $dress['fairlimit']) {

				if ($_GET['nc'] != $_SESSION[$_SERVER['PHP_SELF'].'nodbl']) {
					header("Location: ".$_SERVER['PHP_SELF'].'?showmsg=1');
					die();
				} else {
					$_SESSION[$_SERVER['PHP_SELF'].'nodbl'] = mt_rand();
				}


				$q = mysql_query('UPDATE fair_shop SET faircount = faircount - '.$data['count'].' WHERE `fairid` = '.$data['set'].' AND faircount >= '.$data['count']);

				if (mysql_affected_rows() > 0) {
					$insert_id=array();

					$good = 0;

					for($k=1;$k<=$data['count'];$k++) {
					$str='';
					$sql='';
          $dress['up_level']=0;
          $dress['add_time']=0;
					if($dress[nlevel]>6)
					{
					  $dress['up_level']=$dress[nlevel];
					}

						$dress['unik'] = 0;
						if ($dress['id']>=946 and $dress['id']<=957) {
							// уникальные сразу
							$dress['unik'] = 2;
						}
						$dress['notsell'] = $dress['fairnotsell'];


						$name = $dress['name'];
						$bron1 = $dress['bron1'];
						$bron2 = $dress['bron2'];
						$bron3 = $dress['bron3'];
						$bron4 = $dress['bron4'];
						$ghp = $dress['ghp'];
						$stbonus = $dress['stbonus'];

						$mfinfo = "";

						if ($dress['fairismf'] > 0) {
							$dress['unik'] = 1;

							if ($dress['gsila'] > 0 || $dress['glovk'] > 0 || $dress['ginta'] > 0 || $dress['gintel'] > 0 || $dress['gmudra'] > 0) {
								$stbonus += 3;
							}

							if ($dress['ghp'] > 0) {
								$ghp += 20;
							}

							if ($dress['bron1'] > 0) $bron1 += 3;
							if ($dress['bron2'] > 0) $bron2 += 3;
							if ($dress['bron3'] > 0) $bron3 += 3;
							if ($dress['bron4'] > 0) $bron4 += 3;

							$name .= ' (мф)';

						}

						if ($dress['id'] == 55510350 || $dress['id'] == 55510351 || $dress['id'] == 55510352) $dress['unik'] = 2;
						if ($dress['id'] == 410130 || $dress['id'] == 410131 || $dress['id'] == 410132 || $dress['id'] == 410133 || $dress['id'] == 410134 || $dress['id'] == 410135) $dress['unik'] = 2;
						if ($dress['id'] == 410021 || $dress['id'] == 410022 || $dress['id'] == 410024 || $dress['id'] == 410025 || $dress['id'] == 410023 || $dress['id'] == 410026 || $dress['id'] == 410027 || $dress['id'] == 410028) $dress['unik'] = 2;

						if ($dress['id']==33333) {
							//лото
							$get_lot=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;"));
							mysql_query("INSERT INTO oldbk.`item_loto` SET `loto`={$get_lot[id]},`owner`={$user[id]},`dil`=0,`lotodate`='".date("Y-m-d H:i:s",$get_lot['lotodate'])."';");
							$new_bil_id=mysql_insert_id();
//							$dress['letter']="Купон №".$new_bil_id."<br>Розыгрыш №".$get_lot[id]."<br>Cостоится ".date("Y-m-d H:i:s",$get_lot['lotodate']);
							$dress['letter']="Следующий обмен купонов на подарки состоится ".date("Y-m-d H:i:s",$get_lot['lotodate']);
							$dress['upfree']=$get_lot[id];
							$dress['mffree']=$new_bil_id;
						}

						$dress['ekr_flag']=0;//default
						$dress['ecost']=0;	//default

						if ($dress['id'] == 2016401) {
							// КОТ
							$dress['ekr_flag']=1;
							$dress['ecost']=10;
							$dress['present']='';
						}

						if($dress['id']==6018) {
	            $dress['ups']=257500;
	            $dress['up_level']=10;
	            $dress['add_time']=300000;
	          } elseif($dress['id']==6019) {
	            $dress['ups']=1200000;
	            $dress['up_level']=20;
	            $dress['add_time']=1500000;
	          } elseif($dress['id']==6020) {
	              $dress['ups']=11500000;
	              $dress['up_level']=30;
	              $dress['add_time']=115000000;
	            }


						if(mysql_query("INSERT INTO oldbk.`inventory`
						(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,`ups`,`up_level`,`add_time`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`ekr_flag`,`ecost`,
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`idcity`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`, `ab_mf`,  `ab_bron` ,  `ab_uron`, `img_big`,`charka`,
							`otdel`,`gmp`,`gmeshok`, `group`,`letter`, `upfree`, `mffree`  ,`getfrom`,`rareitem`,`stbonus`,`mfbonus`,`unik`,`notsell`,`craftspeedup`,`craftbonus`,`gold`,`nclass` ".$str."
						)
						VALUES
						('{$dress['id']}','{$dress['sowner']}','{$dress['present']}','{$_SESSION['uid']}','{$name}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['ups']}','{$dress['up_level']}','{$dress['add_time']}', '{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$ghp}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}','{$dress['ekr_flag']}','{$dress['ecost']}',
						'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$bron1}','{$bron2}','{$bron3}','{$bron4}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
						'{$dress['nalign']}','{$dress['dategoden']}','{$dress['goden']}','{$user['id_city']}' , '{$dress[includemagic]}','{$dress[includemagicdex]}','{$dress[includemagicmax]}','{$dress[includemagicname]}','{$dress[includemagicuses]}','{$dress[includemagiccost]}','{$dress[includemagicekrcost]}', '{$dress['ab_mf']}',  '{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['img_big']}', '{$dress['charka']}' ,
						'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['upfree']}','{$dress['mffree']}','1','{$dress['rareitem']}','{$stbonus}','{$dress['mfbonus']}','{$dress['unik']}','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}','{$dress['fairprice']}','{$dress['nclass']}' ".$sql."
						) ;"))
						{
							$good = 1;
							$insert_id[$k] = mysql_insert_id();
							mysql_query('INSERT INTO fair_buy_log (itemid,itempricegold,itemgoden,itemmaxdur) VALUES ("'.$insert_id[$k].'","'.$dress['fairprice'].'","'.$dress['dategoden'].'","'.$dress['maxdur'].'")');
						} else {
							die();
						}

					}

					if ($good == 1) {
						if ($dress['fairprototype'] == 3006000) {
							// если снежинки покупаем - допиливаем екры на банк счёт
							// счётчик продаж
							mysql_query('UPDATE oldbk.variables_int SET value = value + '.$data['count'].' WHERE var = "snowsell"');
							$bankid = mysql_fetch_array(mysql_query("select * from oldbk.bank where owner='{$user['id']}' order by def desc,id limit 1"));
                                                        make_ekr_add_bonus($user,$bankid,false,$data['count'],1);
							$msg = "Вы купили {$data['count']} шт. \"{$dress['name']}\" и получили {$data['count']} екр. на банковский счёт.";
						} else {
							$msg = "Вы купили {$data['count']} шт. \"{$dress['name']}\".";
						}

						$gold -= $data['count']*$dress['fairprice'];
						$limit = $data['count'];


						mysql_query('INSERT INTO shop_stats (owner,shoptype,shopprototype,shopcount,lastupdate)
								VALUES ('.$user['id'].',5,'.$dress['id'].','.$data['count'].','.time().')
								ON DUPLICATE KEY UPDATE
									`shopcount` = `shopcount` + '.$data['count'].', lastupdate = '.time()
						);


						$q = mysql_query('UPDATE users SET gold = gold - '.($data['count']*$dress['fairprice']).' WHERE id = '.$user['id'].' LIMIT 1');

						// добавляем лимиты
						mysql_query('
							INSERT INTO `fair_buy` (`fairowner`,`fairshoptype`,`fairprototype`,`faircount`)
							VALUES(
								'.$user['id'].',
								'.$dress['fairshoptype'].',
								'.$dress['fairprototype'].',
								"'.$data['count'].'"
							)
							ON DUPLICATE KEY UPDATE
								`faircount` = `faircount` + '.$data['count'].'
						');

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
							$dresscount="(x".$data['count'].") ";
						}

						$allcost = $data['count']*$dress['fairprice'];

						$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user[money];
						$rec['owner_balans_posle']=$user[money];
						$rec['target']=0;
						$rec['target_login']='Ярмарка';
						$rec['type']=509;
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['item_id']=$dressid;
						$rec['item_name']=$dress['name'];
						$rec['item_count']=$data['count'];
						$rec['item_type']=$dress['type'];
						$rec['item_cost']=$dress['cost'];
						$rec['item_dur']=$dress['duration'];
						$rec['item_maxdur']=$dress['maxdur'];
						$rec['item_ups']=0;
						$rec['item_unic']=0;
						$rec['item_incmagic']='';
						$rec['item_incmagic_count']='';
						$rec['item_arsenal']='';
						$rec['add_info'] = $allcost."/".$gold;
						add_to_new_delo($rec);
					}
				} else {
					$msg = "Недостаточно денег или нет вещей в наличии.";
					$typet = "e";
				}
			} else {
				if ($dress['fairlimit'] == 0) {
					$msg = "Вам не доступен этот товар.";
				} else {
					$msg = "Вам доступно не более чем ".$dress['fairlimit']." шт.";
				}
				$typet = "e";
			}
		} else {
			$msg = "Недостаточно денег или нет вещей в наличии.";
			$typet = "e";
		}
	} else {
		$msg = "Товар не найден";
		$typet = "e";
	}
} elseif (isset($_GET['sellitems'],$_GET['id'])) {
	$data = mysql_query($sellquery.' and id = '.intval($_GET['id']));
	if (mysql_num_rows($data)) {
		$row = mysql_fetch_assoc($data);

		if($row['add_pick'] != '') {
			undress_img($row);
		}

		$price = floor($row['itempricegold']*0.9);

		mysql_query("UPDATE `users` set `gold` = `gold`+ '".$price."' WHERE id = ".$user['id']." LIMIT 1");
		mysql_query("DELETE FROM oldbk.`inventory` WHERE owner = ".$user['id']." and id = ".$row['id']." LIMIT 1");
		mysql_query("DELETE FROM fair_buy_log WHERE itemid = ".$row['id']." LIMIT 1");

		if ($row['prototype'] == 33333 && $row['mffree'] > 0) {
			//лото
			mysql_query("DELETE FROM oldbk.`item_loto` WHERE id = ".$row['mffree']." LIMIT 1");
		}


		$rec['owner']=$user[id];
		$rec['owner_login']=$user[login];
		$rec['owner_balans_do']=$user[money];
		$rec['owner_balans_posle']=$user[money];
		$rec['target']=0;
		$rec['target_login']='Ярмарка';
		$rec['type']=508;
		$rec['sum_kr']=0;
		$rec['sum_ekr']=0;
		$rec['sum_kom']=0;
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
		$rec['add_info'] = $price."/".$user['gold'];
		add_to_new_delo($rec);

		$msg = "Вы продали \"{$row['name']}\" 1 шт. и получили ".$price." монет.";
		$user['gold'] += $price;
		$gold += $price;

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
<style>
html {
    overflow-y: scroll;
}
</style>
<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="/i/bank9.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
<script src="i/jquery.drag.js" type="text/javascript"></script>
	<script>
		function getformdata(id,param,event)
		{
			if (window.event)
			{
				event = window.event;
			}
			if (event )
			{
				$.get('payform.php?id='+id+'&param='+param+'', function(data) {
					$('#pl').html(data);
					$('#pl').show(200, function() {
					});
				});
			}

		}

		function closeinfo()
		{
			$('#pl').hide(200);
		}
	</script>
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

table#questdiag {
	width: 500px;
}
table#questdiag td img {
    display: block;
}

#page-wrapper #questdiag {
	table-layout: auto;
}

#page-wrapper #questdiag td {
	padding: 0px;
}


.noty_message { padding: 5px !important;}
</style>
<SCRIPT LANGUAGE="JavaScript">

function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}

function AddCount(event,name, txt, sale, href) {
	var el = document.getElementById("hint3");
	var sale=0;
	var sale_txt= 'Купить неск. штук';
	var a_href='';

	el.innerHTML = '<form action="'+href+'" method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B style="font-size:11pt;">'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><B style="font-size:11pt;">x</B></TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B style="font-size:11pt;"><I>'+txt+'</td></tr><tr><td width=80% align=right style="font-size:11pt;">'+
	'Количество (шт.) <INPUT TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	y = event.pageY;
	el.style.top = (y -120) + 'px';
	document.getElementById("count").focus();
}

var dialog;

// Закрывает окно
function closehint3() {
	document.getElementById("hint3").style.visibility="hidden";
}


</SCRIPT>
</HEAD>
<body id="arenda-body">
<div id="pl" style="z-index: 300; position: absolute; left: 50%; top: 120px;
				width: 750px; height:365px; background-color: #eeeeee;
				margin-left: -375px;
				border: 1px solid black; display: none;"></div>
<div id="page-wrapper" style="min-width: 1100px;">
    <div class="title">
        <div class="h3">Ярмарка</div>
        <div id="buttons">
            <a class="button-dark-mid btn" onclick="window.open('help/fair.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes'); return false;" title="Подсказка">Подсказка</a>
            <a class="button-mid btn" OnClick="location.href='?tmp='+Math.random(); return false;" title="Обновить">Обновить</a>
            <a class="button-mid btn" OnClick="document.getElementById('cityform').submit(); return false;" title="Вернуться">Вернуться</a>
	    <FORM action="city.php" style="margin:0px;padding:0px;display:block;" id="cityform" method="GET"><INPUT TYPE="hidden" value="bps" name="bps"></form>
        </div>
    </div>
	<div id="shop">
		<table cellspacing="0" cellpadding="0">
			<colgroup>
				<col width="248px">
				<col>
				<col width="320px">
			</colgroup>
			<tbody>
			<tr>
				<td valign="top" align="center" style="background: url(http://i.oldbk.com/i/torg_BG_color90.jpg) no-repeat;">
					<a href="?quest=1"><img src="http://i.oldbk.com/i/bot_yarm_torgovec.png" OnMouseOver="this.src='http://i.oldbk.com/i/bot_yarm_torgovec_h.png';" OnMouseOut="this.src='http://i.oldbk.com/i/bot_yarm_torgovec.png';"></a>
					<?php
						$mldiag = array();
						$mlquest = "50/30";

						if(isset($_GET['qaction']) && isset($_GET['d'])) {
							$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_GALIAS);
							//зашли в движок квестов
							$dialog_id = isset($_GET['d']) ? (int)$_GET['d'] : null;
							$action_id = isset($_GET['a']) ? (int)$_GET['a'] : null;
							$dialog = $BotDialog->dialog($dialog_id, $action_id);
							if($dialog !== false) {
								$mldiag[0] = $dialog['message'];
								foreach ($dialog['actions'] as $action) {
									$key = '&a='.$action['action'];
									if(isset($action['dialog'])) {
										$key .= '&d='.$action['dialog'];
									}
									$mldiag[$key] = $action['message'];
								}
							}
						}

						if (isset($_GET['quest']) && empty($mldiag)) {
							$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_GALIAS);

							$mldiag[0] = '– Желаете посмотреть новые товары? У нас завоз каждую неделю.';

							foreach ($BotDialog->getMainDialog() as $dialog) {
								$key = '&d='.$dialog['dialog'];
								$mldiag[$key] = $dialog['title'];
							}
							$mldiag[4] = '– Спасибо, я пока просто присматриваюсь...';

						}
						if(!empty($mldiag)) {
							require_once('mlquest.php');
						}
					?>

					<table><tr>
					<td class="hint-block size11 center" style="text-align:justify;">
						Галиас - странствующий торговец и коллекционер редких предметов. Многие товары в его лавке отличаются своей уникальностью и невероятной мощью, но при этом весьма недолговечны.
					</td>
					</tr></table>

				</td>
				<td>
					<div style="color:red;text-align:center;width:100%;">
					Внимание! При выборе покупок учитывайте, что нельзя одновременно надеть более четырех предметов обмундирования, в том числе одного кольца. Ассортимент Ярмарки обновляется каждую неделю.</div>
					<div>&nbsp;</div>
					<table id="mainheader" class="table border" style="margin-bottom: 0;" cellspacing="0" cellpadding="0">
						<colgroup>
							<col width="400px">
							<col>
						</colgroup>
						<thead>
						<tr class="head-line">
							<th>
								<div class="head-left"></div>
								<div class="head-title">Отдел &quot;<?php
									if (isset($_GET['additem']) && ADMIN)  {
										echo "Добавить вещь на ярмарку";
									} elseif (isset($_GET['sellitems'])) {
										echo "Вернуть вещи";
									} else {
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
											case 55:
												echo "Лицензии";
												break;
											case 6:
												echo "Артефакты";
												break;
											case 60:
												echo "Молитвенные предметы";
												break;
											case 61:
												echo "Еда";
												break;
											case 62:
												echo "Ресурсы";
												break;
											case 63:
												echo "Производство";
												break;
											case 64:
												echo "Купоны";
												break;
											case 65:
												echo "Премиум аккаунты";
												break;
											case 66:
												echo "Хеллоуин";
												break;
											case 78:
												echo "Акции";
												break;
											case 99:
												echo "Прилавок Великих";
												break;
										}
									}
									?>&quot;
								</div>
							</th>
							<th class="filter">
								<form method="POST" id="fallfair" style="margin:0px;padding:0px;display:block;">
									<div class="head-title" style="top:1px;">
										<select name="fallfair" style="width:250px;height:14px;margin:0px;top:0px;" OnChange="document.getElementById('fallfair').submit();">
											<option value = "0" <? if ((int)($_POST['fallfair'])==0) { echo ' selected ' ; } ?>>Показывать все вещи</option>
											<option value = "1" <? if ($_POST['fallfair']>0) { echo ' selected ' ; $viewlevel=true; } ?>>Показывать вещи только моего уровня</option>
										</select>
									</div>
								</form>
								<div class="head-right"></div>
							</th>
						</tr>
						</thead>
					</table>
					<?
					if (isset($_GET['additem']) && ADMIN) {
						?>
						<script>
						var profiledata;
						function applyprofile(id) {
							if (profiledata[id] != null) {
								$("#itemcount").val(profiledata[id].itemcount);
								$("#itemprice").val(profiledata[id].itemprice);
								for (i = <?=$levelmin; ?>; i <= <?=$levelmax ?>; i++) {
									eval('$("#level'+i+'").val(profiledata[id].level'+i+');');
								}
							}
						}
						function deleteprofile(id) {
							if (confirm("Точно удалять?")) {
								$.ajax({
									url: "?deleteprofile="+id,
									cache: false,
									async: true,
									success: function(data) {
										fairlistprofiles();
										alert("Удалено");
									}
								});
							}
						}


						function fairlistprofiles() {
							$.ajax({
								url: "?listprofiles=1",
								cache: false,
								async: true,
								dataType: "json",
								success: function(data) {
									profiledata = data;
									html = "";
									for(var key in data) {
										html += '<a href="#" OnClick="applyprofile('+key+');return false;">'+data[key].profilename+'</a> <a OnClick="deleteprofile('+key+');return false;" href="#"><font color=red>X</font></a><br>';
									}
								        $("#profilelist").html(html);
								}
							});
						}

						function fairaddprofile() {
							result = prompt("Введите имя профиля");
							if (result != null && result.length > 0) {
								itemcount = parseInt($("#itemcount").val());
								itemprice = parseInt($("#itemprice").val());
								if (isNaN(itemcount) || itemcount < 1) {
									alert("Общее количество должно быть положительным");
									return;
								}
								if (isNaN(itemprice) || itemprice < 1) {
									alert("Цена должна быть положительным целым числом");
									return;
								}

								for (i = <?=$levelmin?>; i < <?=$levelmax?>; i++) {
									tmp = parseInt($("#level"+i).val());
									if (isNaN(tmp) || tmp < 0) {
										alert("Неверное значения для уровня "+i);
										return;
									}
								}

								$.ajax({
									url: "?saveprofile="+result+"&"+$("#profileform").serialize(),
									cache: false,
									async: true,
									success: function(data){
										fairlistprofiles();
										alert("Сохранено");
									}
								});
							} else {
								alert("Имя профиля не должно быть пустым");
							}
						}

						function CheckForm() {
							itemcount = parseInt($("#itemcount").val());
							itemprice = parseInt($("#itemprice").val());
							if (isNaN(itemcount) || itemcount < 1) {
								alert("Общее количество должно быть положительным");
								return false;
							}
							if (isNaN(itemprice) || itemprice < 1) {
								alert("Цена должна быть положительным целым числом");
								return false;
							}

							for (i = <?=$levelmin?>; i < <?=$levelmax?>; i++) {
								tmp = parseInt($("#level"+i).val());
								if (isNaN(tmp) || tmp < 0) {
									alert("Неверное значения для уровня "+i);
									return false;
								}
							}
							return true;
						}

						</script>
						<form id="profileform" method="GET">
						<input type="hidden" name="additem" value="1">
						ID вещи: <input type="text" name="itemid" value="<?=$_GET['itemid']; ?>"> Магазин: <select name="itemshop"><option value="0">shop</option><option <?=($_GET['itemshop'] == 1 ? "selected" : "") ?> value="1">eshop</option></select>
						<br>
						<br>
						Имя вещи: <input type="text" name="itemname" value="<?=$_GET['itemname']; ?>">
						<input type="submit" style="height:20px;" value="Найти"><br><br>

						<?




						if (isset($_GET['itemid'],$_GET['itemshop']) && !isset($_GET['goitem'])) {
							$shop = $_GET['itemshop'] == 1 ? "eshop" : "shop";
							if (isset($_GET['itemname']) && !empty($_GET['itemname'])) {
								$q = mysql_query('SELECT * FROM '.$shop.' WHERE name LIKE "%'.$_GET['itemname'].'%"');
							} else {
								$q = mysql_query('SELECT * FROM '.$shop.' WHERE id = '.intval($_GET['itemid']));
							}



							if (mysql_num_rows($q) > 0) {
								$row = mysql_fetch_assoc($q);

							$aq = mysql_query('SELECT * FROM fair_shop WHERE fairprototype = '.$row['id'].' and fairshoptype = '.intval($shop));
								if (mysql_num_rows($aq) > 0)
								{
									echo "<font color=red><b>Такой товар уже есть на ярмарке! </b></font><br>";
								}

								$_GET['itemid'] = $row['id'];
								$row[GetShopCount()] = 1;
								if (!empty($row['img_big'])) $row['img'] = $row['img_big'];

								echo '<table "class="table border a_strong" cellspacing="0" cellpadding="0" style="width:650px;margin-bottom:0px;">
			                      				<tbody>
			                      				<tr class="even2">
								';

								$color = '#C7C7C7';
								echo "<TD align=center style='width:100px;vertical-align:middle;'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
								?>
								<BR>
								</TD>
								<?php
								echo "<TD valign=top>";
								showitem ($row);
								echo "</TD></TR></td></TR></TABLE>";
								?>
								<div>
								<table style="width:400px;">
									<tr><td>Общее количество</td><td><input id="itemcount" value="99999" style="width:50px;" type=text name="itemcount"></td><td rowspan=<?=($levelmax-$levelmin+2)?>><div id="profilelist"></div></td></tr>
									<tr><td>Цена монет</td><td><input id="itemprice" style="width:50px;" type=text name="itemprice"></td></tr>
									<tr><td>Подарок?</td><td><input checked id="itempresent" type=checkbox name="itempresent"></td></tr>
									<tr><td>Привязка?</td><td><input id="itemisowner" type=checkbox name="itemisowner"></td></tr>
									<tr><td>Скрывать кол-во?</td><td><input id="itemhidecount" checked type=checkbox name="itemhidecount"></td></tr>
									<tr><td>Скрывать доступно?</td><td><input id="itemhidecount2" checked type=checkbox name="itemhidecount2"></td></tr>
									<tr><td>Флаг notsell</td><td><input id="itemnotsell" checked type=checkbox name="itemnotsell"></td></tr>
									<tr><td>Флаг top mf</td><td><input id="itemismf" type=checkbox name="itemismf"></td></tr>

									<tr><td>Чарка</td><td><select name="faircharka">
										<option value="0" selected>нет</option>
										<?
										foreach ($charka_config as $k=>$v)
											{
											echo "<option  value=\"{$k}\">{$v['charka']}</option>";
											}

										?>
										</select></td></tr>


									<tr><td>Годен до (23:59)</td><td><input type=text value="<?php echo (isset($_SESSION['fairgoden']) ? $_SESSION['fairgoden'] : ""); ?>" id="itemgoden" name="itemgoden" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <input style="height:20px;" type=button id="itemgoden-trigger" value='...'></td></tr>
									<tr><td>Старт продаж (00:00)</td><td><input type=text value="<?php echo (isset($_SESSION['fairstartsell']) ? $_SESSION['fairstartsell'] : ""); ?>" id="itemstartsell" name="itemstartsell" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <input style="height:20px;" type=button id="itemstartsell-trigger" value='...'></td></tr>
									<tr><td>Продажи до (23:59)</td><td><input type=text value="<?php echo (isset($_SESSION['fairendsell']) ? $_SESSION['fairendsell'] : ""); ?>" id="itemendsell" name="itemendsell" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <input style="height:20px;" type=button id="itemendsell-trigger" value='...'></td></tr>
									<tr><td>Годность дней</td><td><input value="90" id="itemgodenday" style="width:50px;" type=text name="itemgodenday"></td></tr>
									<tr><td>Баннер</td><td><select name="fairbanner"><option value="0" selected>нет</option><option  value="1">Новинка!</option><option  value="2">Акция!</option><option  value="10">-10%</option><option  value="20">-20%</option><option  value="30">-30%</option><option  value="40">-40%</option><option  value="50">-50%</option></select></td></tr>


									<tr><td colspan=2 style="padding:0px;">
									<a href="#" OnClick="showhide('levellimits'); return false;">+ ограничение по уровням</a><br>
									<table style="padding:0px;margin:0px;display:none;" id="levellimits">
									<?php
									for ($level = $levelmin; $level <= $levelmax; $level++) {
										echo '<tr><td>Лимит '.$level.' ур.</td><td><input id="level'.$level.'" type=text style="width:40px;" value="9999" name="level'.$level.'"></td></tr>';
									}
									?>
									</table>
									</tr></td>
								</table>
								<input type="submit" style="height:20px;" OnClick="return CheckForm();" name="goitem" value="Добавить"> <input type="button" style="height:20px;" OnClick="fairaddprofile();" value="Добавить профиль">

								<script>
								Calendar.setup({
									trigger    : "itemgoden-trigger",
									inputField : "itemgoden",
									dateFormat : "%d/%m/%Y",
									onSelect   : function() { this.hide() }
								});
								document.getElementById('itemgoden-trigger').setAttribute("type","BUTTON");

								Calendar.setup({
									trigger    : "itemendsell-trigger",
									inputField : "itemendsell",
									dateFormat : "%d/%m/%Y",
									onSelect   : function() { this.hide() }
								});
								document.getElementById('itemendsell-trigger').setAttribute("type","BUTTON");

								Calendar.setup({
									trigger    : "itemstartsell-trigger",
									inputField : "itemstartsell",
									dateFormat : "%d/%m/%Y",
									onSelect   : function() { this.hide() }
								});
								document.getElementById('itemstartsell-trigger').setAttribute("type","BUTTON");

								</script>

								</div>
								<?
							}
						}
						echo '</form>';
					} elseif (isset($_GET['sellitems'])) {
						$_SESSION['fairlastotdel'] = "sellitems";
						$data = mysql_query($sellquery.' ORDER by `update` DESC');
						if (mysql_num_rows($data)) {
							$z = 0;
							while(($row = mysql_fetch_assoc($data))) {
								echo '<table id="z'.$z.'"class="table border a_strong" cellspacing="0" cellpadding="0" style="float:left; width:650px;margin-bottom:0px;visibility:hidden;">
		                      				<tbody>
		                      				<tr class="even2">
								';

								$z++;

								if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
								if (!empty($row['img_big'])) $row['img'] = $row['img_big'];
								echo "<TD align=center style='width:100px;vertical-align:middle;'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
								echo '<center><small>('.get_item_fid($row).')</small></center>';
								$row[GetShopCount()] = 1;
								?>
								<BR><A HREF="?sellitems&id=<?=$row['id']?>">Вернуть за <?=floor($row['itempricegold']*0.9)?> монет</A></TD>
								<?php
								echo "<TD valign=top>";
								showitem ($row);
								echo "</TD></TR></td></TR></TABLE>";
							}
						} else {
							echo '<br><center><b>У вас нет подходящих товаров Ярмарки. Вернуть можно только неиспользованные товары со сроком годности более 30-ти дней.</b></center>';
						}

					} else {
						if ($viewlevel==true) {
							if ($user['level']>14) {
								$addlvl=" and nlevel=14";
							} else {
								$addlvl=" and nlevel={$user['level']} ";
							}
						} else {
							$addlvl="";
						}

						// делаем список уже купленных
						$buyed = array();
						$q = mysql_query('SELECT * FROM fair_buy WHERE fairowner = '.$user['id']);
						while($i = mysql_fetch_assoc($q)) {
							$buyed[$i['fairprototype']][$i['fairshoptype']] = $i['faircount'];
						}

						if (ADMIN) {
							$data = mysql_query("SELECT * FROM fair_shop LEFT JOIN shop ON fair_shop.fairprototype = shop.id WHERE fairshoptype = 0 AND `razdel` = ".$_GET['otdel']." ".$addlvl." ORDER BY fairprice ASC");
							$data2 = mysql_query("SELECT * FROM fair_shop LEFT JOIN eshop ON fair_shop.fairprototype = eshop.id WHERE fairshoptype = 1 AND `razdel` = ".$_GET['otdel']." ".$addlvl." ORDER BY fairprice ASC");
						} else {
							$data = mysql_query("SELECT * FROM fair_shop LEFT JOIN shop ON fair_shop.fairprototype = shop.id WHERE fairshoptype = 0 and faircount > 0 AND `razdel` = ".$_GET['otdel']." ".$addlvl." and (fairendsell = 0 or fairendsell > ".time().") and (fairstartsell = 0 or fairstartsell < ".time().") ORDER BY fairprice ASC");
							$data2 = mysql_query("SELECT * FROM fair_shop LEFT JOIN eshop ON fair_shop.fairprototype = eshop.id WHERE fairshoptype = 1 and faircount > 0 AND `razdel` = ".$_GET['otdel']." ".$addlvl." and (fairendsell = 0 or fairendsell > ".time().") and (fairstartsell = 0 or fairstartsell < ".time().") ORDER BY fairprice ASC");
						}

						function cmpfairprice($a, $b) {
							if ($a['fairbanner'] == 1 && $b['fairbanner'] == 1) return 0;
							if ($a['fairbanner'] == 1 && $b['fairbanner'] != 1) return -1;
							if ($a['fairbanner'] != 1 && $b['fairbanner'] == 1) return 1;

							if ($a['fairprice'] == $b['fairprice']) {
								if ($a['nlevel'] == $b['nlevel']) {
									return 0;
								}
								return ($a['nlevel'] < $b['nlevel']) ? -1 : 1;
							}
							return ($a['fairprice'] < $b['fairprice']) ? -1 : 1;
						}



						$z = 0;
						$list = array();
						while(($row = mysql_fetch_assoc($data)) || ($row = mysql_fetch_assoc($data2))) {
							$list[] = $row;
						}


						uasort($list,"cmpfairprice");

						$icon_count=0;

						while(list($k,$row) = each($list)) {
							if($row['fairprototype'] == 55510350 || $row['fairprototype'] == 55510351 || $row['fairprototype'] == 55510352) $row['unik'] = 2;
							if($row['fairprototype'] == 410130 || $row['fairprototype'] == 410131 || $row['fairprototype'] == 410132 || $row['fairprototype'] == 410133 || $row['fairprototype'] == 410134 || $row['fairprototype'] == 410135) $row['unik'] = 2;
							if($row['fairprototype'] == 410021 || $row['fairprototype'] == 410022 || $row['fairprototype'] == 410024 || $row['fairprototype'] == 410025 || $row['fairprototype'] == 410023 || $row['fairprototype'] == 410026 || $row['fairprototype'] == 410027 || $row['fairprototype'] == 410028) $row['unik'] = 2;

							if ($row['id']>=946 and $row['id']<=957) {
								// уникальные сразу
								$row['unik'] = 2;
							}

							if ((time()>$KO_start_time48) and (time()<$KO_fin_time48)) {
								if (in_array($row['fairprototype'] ,$prems_array)) {
									$row['fairprice']=(int)($row['fairprice']*0.5);
									$row['fairicon']=50;
								}
							} elseif ($row['fairbanner']==10||$row['fairbanner']==20||$row['fairbanner']==30||$row['fairbanner']==40||$row['fairbanner']==50) {
								//ручная установка банера скидки
								//10 //20 //30 //40 //50

								$row['fairprice']=ceil($row['fairprice']*(100-$row['fairbanner'])*0.01);
								$row['fairicon']=$row['fairbanner'];

							} elseif ((time()>$KO_start_time41) and (time()<$KO_fin_time41)) {

								if (in_array($row['fairprototype'] ,$FSHOP_ITEMS_41)) {
									$row['fairprice']=ceil($row['fairprice']*(100-$FSHOP_RATE_41)*0.01);
									$row['fairicon']=$FSHOP_RATE_41;
								} elseif ($row['fairprototype']==3005000) {
									$row['fairprice']=(int)($row['fairprice']*0.5);
									$row['fairicon']=50;
								}
							}



							echo '<table id="z'.$z.'"class="table border a_strong" cellspacing="0" cellpadding="0" style="float:left; width:650px;margin-bottom:0px;visibility:hidden;">
	                      				<tbody>
	                      				<tr class="even2">
							';

							$z++;

							$row[GetShopCount()] = $row['faircount'];

							if (ADMIN && !$row['faircount']) {
								$row[GetShopCount()] = -1;
							}

							if ($row['fairpresent']) $row['present'] = "Торговец Галиас";
							if ($row['fairisowner']) $row['is_owner'] = 1;

							if ($row['fairgoden'] > 0) {
								$row['dategoden']=$row['fairgoden'];
								$row['goden']=round(($row['fairgoden']-time())/60/60/24);
								if ($row['goden']<1) {
									$row['goden']=1;
								}
							} elseif ($row['fairgodenday'] > 0) {
								$row['goden'] = $row['fairgodenday'];
								$row['dategoden']=time()+($row['goden']*3600*24);
							}

							$row['fairlimits'] = unserialize($row['fairlimits']);

							$limit = $row['fairlimits']['level'.$user['level']];
	                                                if (!$limit) $limit = 0;
							if (isset($buyed[$row['fairprototype']][$row['fairshoptype']])) {
								$limit = $limit - $buyed[$row['fairprototype']][$row['fairshoptype']];
							}
							if ($limit < 0) $limit = 0;
							$row['fairlimit'] = $limit;

							if ($row['faircount'] < $row['fairlimit']) {
								$row['fairlimit'] = $row['faircount'];
							}

							$row['ekr_flag'] = 0;

							if ($row['id']==2016401) {
								$row['ekr_flag']=1;
								//$row['ecost']=10;
								$row['present']='';
								//КОТ
							}

							if($row['id']==6018) {
		            $row['ups']=257500;
		            $row['up_level']=10;
		            $row['add_time']=300000;
		          } elseif($row['id']==6019) {
		            $row['ups']=1200000;
		            $row['up_level']=20;
		            $row['add_time']=1500000;
		          } elseif($row['id']==6020) {
		              $row['ups']=11500000;
		              $row['up_level']=30;
		              $row['add_time']=115000000;
		            }

							if (($row['faircharka']>0) and is_array($charka_config[$row['faircharka']]) )
										{
										//есть чарка в настройке
										$add_array=array();
										$inputbonus=array();
										$add_array=$charka_config[$row['faircharka']];
										$charka_level=$add_array['charka_level'];
										unset($add_array['charka']);
										unset($add_array['charka_level']);
										foreach ($add_array as $k=>$v)
												{
												$row[$k]+=$v;
												$inputbonus[$charka_level][][$k]=$v;
												}

										$row['charka']=$charka_level."|".serialize($inputbonus);
										}


							$row['notsell'] = $row['fairnotsell'];

							if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
							if (!empty($row['img_big'])) $row['img'] = $row['img_big'];
							echo "<TD align=center style='width:100px;";


							if ($row['fairicon']>0) {
								echo "vertical-align:top;'>";
								echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -10px;top: -11px;\" src='http://i.oldbk.com/i/icon_s_".(int)($row['fairicon']).".png'>";
								$icon_count++;
							} elseif ($banners[$row['fairbanner']]!='') {
								$banner=$banners[$row['fairbanner']];
								echo "vertical-align:top;'>";
								echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -10px;top: -11px;\" ".$banner.">";
								$icon_count++;
							} else {
								echo "vertical-align:middle;'>";
							}

							echo "<IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";

							if ($row[GetShopCount()] != -1) {
								?>
								<BR><A HREF="?otdel=<?=$_GET['otdel']?>&set=<?=$row['fairid']?>&nc=<?=$_SESSION[$_SERVER['PHP_SELF'].'nodbl'];?>">купить</A>
								<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Купить несколько штук" style="cursor: pointer" onclick="AddCount(event,'<?=$row['fairid']?>', '<?=$row['name']?>','0','<?=$_SERVER['PHP_SELF'];?>?nc=<?=$_SESSION[$_SERVER['PHP_SELF'].'nodbl'];?>')">
								<?php
							}

							// уник мф
							if ($row['fairismf'] > 0) {
								if ($row['gsila'] > 0 || $row['glovk'] > 0 || $row['ginta'] > 0 || $row['gintel'] > 0 || $row['gmudra'] > 0) {
									$row['stbonus'] += 3;
								}
								if ($row['ghp'] > 0) {
									$row['ghp'] += 20;
								}

								if ($row['bron1'] > 0) $row['bron1'] += 3;
								if ($row['bron2'] > 0) $row['bron2'] += 3;
								if ($row['bron3'] > 0) $row['bron3'] += 3;
								if ($row['bron4'] > 0) $row['bron4'] += 3;

								$row['unik'] = 1;
								$row['name'].= ' (мф)';
							}


							if (ADMIN) {
								echo '<BR><BR> <a href="?otdel='.$_GET['otdel'].'&removefromfair='.$row['fairid'].'" OnClick="return confirm(\'Точно удалять?\');">Удалить из ярмарки</a>';
							}
							?>
							</TD>
							<?php
							echo "<TD valign=top>";
							showitem ($row);
							echo "</TD></TR></td></TR></TABLE>";
						}
					}
				?>

				<td>
					<table id="filter" cellspacing="0" cellpadding="0">
						<tbody>
						<tr>
							<td class="center">
								<strong>Масса всех ваших вещей:

									<?php
									echo $d[0];?>/<?=get_meshok()?>
								</strong><br>
								<strong>У Вас в наличии: <span class="money"><?=$gold?></span> <img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom: -2px;"></strong><br>
							</td>
						</tr>
						<tr>
							<td align="center">
							<?
							if ($_SESSION['bankid'] > 0) {


								if ((time()>$KO_start_time28) and (time()<$KO_fin_time28))
									{
									echo "<a href=\"#\" onClick=\"getformdata(88,0,event);\"><img src=http://i.oldbk.com/i/knopka_coin3.gif title='Купить монеты' ></a>";
									}
									else
									{
									echo "<a href=\"#\" onClick=\"getformdata(87,0,event);\"><img src=http://i.oldbk.com/i/knopka_coin3.gif title='Купить монеты' ></a>";
									}
							} else {
								echo "<a href=\"bank.php\"><img src=http://i.oldbk.com/i/knopka_coin3.gif title='Купить монеты' ></a>";
							}
							?>

							</td>
						</tr>
						<tr><td align="center">

						</tr></td>

						<?php if (isset($otdels[1]) || isset($otdels[11]) || isset($otdels[12]) || isset($otdels[13])) { ?>
						<tr>
							<td class="filter-title">Оружие</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>

								<li>
									<A HREF="?otdel=6&tmp=<?echo mt_rand(1111111,9999999);?>"><img src='http://i.oldbk.com/i/sh/elka_legend.gif' height=20 width=20> Ёлки</A>
								</li>

									<?php if (isset($otdels[1])) { ?>
									<li>
										<A HREF="?otdel=1&tmp=<?echo mt_rand(1111111,9999999);?>">Кастеты, ножи</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[11])) { ?>
									<li>
										<A HREF="?otdel=11&tmp=<?echo mt_rand(1111111,9999999);?>">Топоры</A>
									</li>
									<?php } ?>


									<?php if (isset($otdels[12])) { ?>
									<li>
										<A HREF="?otdel=12&tmp=<?echo mt_rand(1111111,9999999);?>">Дубины, булавы</A>
									</li>
									<?php } ?>


									<?php if (isset($otdels[13])) { ?>
									<li>
										<A HREF="?otdel=13&tmp=<?echo mt_rand(1111111,9999999);?>">Мечи</A>
									</li>
									<?php } ?>
								</ul>
							</td>
						</tr>
						<?php } ?>

						<?php if (isset($otdels[2]) || isset($otdels[21]) || isset($otdels[22]) || isset($otdels[23]) || isset($otdels[24]) || isset($otdels[3]) || isset($otdels[4]) || isset($otdels[41]) || isset($otdels[42])) { ?>
						<tr>
							<td class="filter-title">Обмундирование</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>

									<?php if (isset($otdels[2])) { ?>
									<li>
										<A HREF="?otdel=2&tmp=<?echo mt_rand(1111111,9999999);?>">Сапоги</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[21])) { ?>
									<li>
										<A HREF="?otdel=21&tmp=<?echo mt_rand(1111111,9999999);?>">Перчатки</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[22])) { ?>
									<li>
										<A HREF="?otdel=22&tmp=<?echo mt_rand(1111111,9999999);?>">Легкая броня</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[23])) { ?>
									<li>
										<A HREF="?otdel=23&tmp=<?echo mt_rand(1111111,9999999);?>">Тяжелая броня</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[24])) { ?>
									<li>
										<A HREF="?otdel=24&tmp=<?echo mt_rand(1111111,9999999);?>">Шлемы</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[3])) { ?>
									<li>
										<A HREF="?otdel=3&tmp=<?echo mt_rand(1111111,9999999);?>">Щиты</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[4])) { ?>
									<li>
										<A HREF="?otdel=4&tmp=<?echo mt_rand(1111111,9999999);?>">Cерьги</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[41])) { ?>
									<li>
										<A HREF="?otdel=41&tmp=<?echo mt_rand(1111111,9999999);?>">Ожерелья</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[42])) { ?>
									<li>
										<A HREF="?otdel=42&tmp=<?echo mt_rand(1111111,9999999);?>">Кольца</A>
									</li>
									<?php } ?>
								</ul>
							</td>
						</tr>
						<?php } ?>

						<?php if (isset($otdels[5]) || isset($otdels[51])) { ?>
						<tr>
							<td class="filter-title">Заклинания</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>
									<?php if (isset($otdels[5])) { ?>
									<li>
										<A HREF="?otdel=5&tmp=<?echo mt_rand(1111111,9999999);?>">Нейтральные</A>
									</li>
									<?php } ?>


									<?php if (isset($otdels[51])) { ?>
									<li>
										<A HREF="?otdel=51&tmp=<?echo mt_rand(1111111,9999999);?>">Боевые и защитные</A>
									</li>
									<?php } ?>
								</ul>
							</td>
						</tr>
						<?php } ?>

						<?php if (isset($otdels[55]) || isset($otdels[6]) || isset($otdels[61]) || isset($otdels[60]) || isset($otdels[64]) || isset($otdels[65]) || isset($otdels[66]) || isset($otdels[78]) || isset($otdels[72])) { ?>
						<tr>
							<td class="filter-title">Прочее</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>
									<?php if (isset($otdels[78])) { ?>
									<li>
										<A HREF="?otdel=78&tmp=<?echo mt_rand(1111111,9999999);?>">Акции</A>
									</li>
									<?php } ?>
									<?php if (isset($otdels[55])) { ?>
									<li>
										<A HREF="?otdel=55&tmp=<?echo mt_rand(1111111,9999999);?>">Лицензии</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[6])) { ?>
									<li>
										<A HREF="?otdel=6&tmp=<?echo mt_rand(1111111,9999999);?>">Артефакты</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[61])) { ?>
									<li>
										<A HREF="?otdel=61&tmp=<?echo mt_rand(1111111,9999999);?>">Еда</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[60])) { ?>
									<li>
										<A HREF="?otdel=60&tmp=<?echo mt_rand(1111111,9999999);?>">Молитвенные предметы</A>
									</li>
									<?php } ?>
									<?php if (isset($otdels[64])) { ?>
									<li>
										<A HREF="?otdel=64&tmp=<?echo mt_rand(1111111,9999999);?>">Купоны</A>
									</li>
									<?php } ?>
									<?php if (isset($otdels[65])) { ?>
									<li>
										<A HREF="?otdel=65&tmp=<?echo mt_rand(1111111,9999999);?>">Премиум аккаунты</A>
									</li>
									<?php } ?>
									<?php if (isset($otdels[82])) { ?>
									<li>
										<A HREF="?otdel=82&tmp=<?echo mt_rand(1111111,9999999);?>">Хеллоуин</A>
									</li>
									<?php } ?>
									<?php if (isset($otdels[72])) { ?>
									<li>
										<A HREF="?otdel=72&tmp=<?echo mt_rand(1111111,9999999);?>">Подарки</A>
									</li>
									<?php } ?>
								</ul>
							</td>
						</tr>
						<?php } ?>

						<?php if (isset($otdels[62]) || isset($otdels[63])) { ?>
						<tr>
							<td class="filter-title">Производство</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>
									<?php if (isset($otdels[62])) { ?>
									<li>
										<A HREF="?otdel=62&tmp=<?echo mt_rand(1111111,9999999);?>">Ресурсы</A>
									</li>
									<?php } ?>

									<?php if (isset($otdels[63])) { ?>
									<li>
										<A HREF="?otdel=63&tmp=<?echo mt_rand(1111111,9999999);?>">Инструменты</A>
									</li>
									<?php } ?>
								</ul>
							</td>
						</tr>
						<?php } ?>


						<?php
							if (ADMIN) { ?>

						<tr>
							<td class="filter-title">Завоз</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>
									<li>
										<A HREF="?additem&tmp=<?echo mt_rand(1111111,9999999);?>">Добавить вещь</A>
									</li>
							</td>
						</tr>
						<?php
							}
						?>
						<tr>
							<td class="filter-title">Вернуть товары</td>
						</tr>
						<tr>
							<td class="filter-item">
								<ul>
									<li>
										<A HREF="?sellitems&tmp=<?echo mt_rand(1111111,9999999);?>">Вернуть вещи</A>
									</li>
							</td>
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
<?php
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

<script>
function ResizeItems() {
	var itemc = 0;

	for (i = 0;;i++) {
		tmp = document.getElementById("z"+i);
		if (tmp) {
			tmp.style.visibility = "hidden";
			tmp.style.height = "auto";
			itemc++;
		} else {
			break;
		}
	}

	var z = $("#mainheader").width();
	var cc = Math.round(z / 650);
	if (itemc < cc) cc = itemc;
	var hlines = new Array();

	z = Math.round(z / cc)-1;

	for (i = 0;;i++) {
		tmp = document.getElementById("z"+i);

		if (tmp) {
			tmp.style.width = z+"px";
		} else {
			break;
		}
	}

	if (cc > 1) {

		for (i = 0;;i++) {
			tmp = document.getElementById("z"+i);

			if (tmp) {
				var line = (Math.floor(i / cc));
				if (hlines[line] === undefined || $("#z"+i).outerHeight() > hlines[line]) {
					hlines[line] = $("#z"+i).outerHeight();
				}
			} else {
				break;
			}
		}
	}


	for (i = 0;;i++) {
		tmp = document.getElementById("z"+i);
		if (tmp) {
			var line = (Math.floor(i / cc));
			if (cc > 1) tmp.style.height = (hlines[line]+5)+"px";
			tmp.style.visibility = "visible";
		} else {
			break;
		}
	}
}

var zoom = $("#mainheader").width();
$(window).resize(function() {
    var zoomNew = $("#mainheader").width();
    if (zoom != zoomNew) {
	ResizeItems();
        zoom = zoomNew
    }
});

$(function() {
	ResizeItems();
});

<?php
if (isset($_GET['additem']) && ADMIN) {
	echo 'fairlistprofiles();';
}
?>
</script>
<?
if (strlen($msg) || isset($_GET['showmsg'],$_SESSION['fairlastmsg'])) {
	if (isset($_GET['showmsg'],$_SESSION['fairlastmsg'])) {
		$msg = $_SESSION['fairlastmsg'];
		$typet = $_SESSION['fairlasttypet'];
	}

	$_SESSION['fairlastmsg'] = $msg;
	$_SESSION['fairlasttypet'] = $typet;


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

</BODY>
</HTML>
<?php

function percent($a, $b) {
	$c = $b/$a*100;
	return $c;
}

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
