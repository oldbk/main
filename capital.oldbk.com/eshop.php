<?php

// прототипы на которые скидка 50% - для кланов
$arts_50=array(/*2003,18527,18247,18229,18210,2002,2001,2000,284,283,262,260*/);

// тип отображения мессаги
$typet = "s";
$saleprotos = [5101,5102,5103,7001,7002,7003,7005,7006,100028,100029,100030,100031];

session_start();

if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";
require_once('config_ko.php');

// выбор отдела
$unikrazdel = array(6,22);//6,2,21,22,23,24,3,4,41,42);
if (ADMIN) $unikrazdel = array(6,2,21,22,23,24,3,4,41,42);

if (isset($_GET['otdel'])) {
	$arr = array(12,14,7,71,72,73,75);
	if (in_array($_GET['otdel'],$arr) !== FALSE) die();
}

$otdel = isset($_GET['otdel']) ? intval($_GET['otdel']) : "5";
$g_otdel = array(5,50,51,52,61,78,64,100,300,500,700,106,62,63,82);
if (in_array($otdel,$g_otdel) === FALSE && in_array($otdel,$unikrazdel) === FALSE) die();

/*
if (ADMIN) {
	$KO_start_time41=time()-1;
	$KO_fin_time41=time()+1;
}
*/

$city_base[0]='oldbk';
$city_base[1]='avalon';
$city_base[2]='angels';

if ($user['room'] != 35) { header("Location: main.php"); die(); }

include "clan_kazna.php";

if (isset($_POST['fall'])) {
	$_SESSION['fall']=(int)$_POST['fall'];
} else {
	$_POST['fall']=(int)$_SESSION['fall'];
}

if ($user['klan'] != '') {
	$clan_id=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user[klan]}' LIMIT 1;"));
	if ($clan_id['id'] >0) {
		if ($clan_id['glava']==$user['id']) {
			$clan_kazna=clan_kazna_have($clan_id[id]);
		}
	}
}

// если выбран раздел для главкланов а чар таким не является
if ((($_GET['otdel']) == 106) and  (!$clan_kazna) ) { $_GET['otdel'] = 5; }

$d[0] = getmymassa($user);
if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
if ($_SESSION['boxisopen']!='open') { header('location: main.php?edit=1'); die(); }

if(isset($_POST['enter']) && isset($_POST['pass'])) {
	$data = mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = '".$user['id']."' AND `id`= '".$_POST['id']."' AND `pass` = '".md5($_POST['pass'])."';");

	$data = mysql_fetch_array($data);
	if($data) {
		$_SESSION['bankid'] = $_POST['id'];
		$akk_err = 'Удачный вход';
	} else {
		err('Ошибка входа.');
	}

}

if ($_SESSION['bankid']>0) {
	$bank = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = ".$_SESSION['bankid'].";"));
}
if($bank['owner'] > 0 && ($bank['owner'] != $user['id'])) {  err('Попытка чита...'); $_SESSION['bankid'] = null; }

//if ($user['klan'] != "radminion") die();

if (($_GET['do']==1) AND (($_GET['up']==54999) /* OR ($_GET['up']==55999) OR ($_GET['up']==56999) */ ) AND ($bank['owner'] > 0)) {
	// установка аккаунтов
	$setup_arr[54999]=1;
	$setup_arr[55999]=2;
	$setup_arr[56999]=3;

	$akkcost_arr[54999]=5;
	$akkcost_arr[55999]=20;
	$akkcost_arr[56999]=35;

	$akneed=$setup_arr[$_GET['up']];
	$akkcost=$akkcost_arr[$_GET['up']];


	if ((time()>$KO_start_time48) and (time()<$KO_fin_time48))
				{
				$akkcost=round($akkcost*0.5,2);
				}


	if (($akneed!=$user['prem']) and ($user['prem'] >0 ) )   {

		if ($akneed<$user['prem']) {
			$akk_err='<font color="red">У Вас уже установлен более высокий аккаунт. Вы можете выбрать аккаунт выше либо продлить текущий! У Вас:';
		} else {
			$akk_err='<font color="red">У Вас уже есть аккаунт. Вы можете установить новый аккаунт либо продлить текущий! Отказаться от существующего можно на странице "Состояние". У Вас:';
		}

		if($user['prem']==1) {
			$akk_err.= ' <a href=https://oldbk.com/encicl/prem.html target=_blank>"Silver account"</a> ';
		} elseif($user['prem']==2) {
			$akk_err.=' <a href=https://oldbk.com/encicl/prem.html target=_blank>"Gold account"</a> ';
		} elseif($user['prem']==3) {
			$akk_err.=' <a href=https://oldbk.com/encicl/prem.html target=_blank>"Platinum account"</a> ';
		}

		$akk_err.='</font>';
	} else {

		if ($akkcost>$bank['ekr']) {
			$typet = "e";
			$akk_err='У Вас недостаточно средств для покупки!';
		} else {
			$akkcosts[1]=5; $strtype[1]='Silver'; $exp_bonus[1]="0.1"; $eff_type[1]=4999;
			$akkcosts[2]=20; $strtype[2]='Gold'; $exp_bonus[2]="0.15"; $eff_type[2]=5999;
			$akkcosts[3]=35; $strtype[3]='Platinum'; $exp_bonus[3]="0.2"; $eff_type[3]=6999;

			include "bank_functions.php";
		    	////ставим акк////

		    	$dill['id_city']=$user['id_city'];
			$dill['id']=450;
			$dill['login']='KO';
			$exp = main_prem_akk($user,$akneed,$dill);

			if ($exp>0) {
				mysql_query("UPDATE oldbk.`bank` set `ekr` = ekr-'{$akkcost}' WHERE `id` = '{$bank['id']}' LIMIT 1;");
				if(mysql_affected_rows()>0) {
					//new_delo
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$dill['id'];
					$rec['target_login']=$dill[login];
						$actty[1]=459;
		 				$actty[2]=559;
						$actty[3]=558;
					$rec['type']=$actty[$akneed] ;//покупка silvera/gold/platinum
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$akkcost;
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
					$rec['bank_id']=$bank['id'];
					$rec['add_info']=(date('d-m-Y',$exp)). '. Баланс счета до '.$bank[ekr]. ' после ' .($bank[ekr]-$akkcost);
					add_to_new_delo($rec); //юзеру

					mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Куплен <b>{$strtype[$akneed]} account за {$akkcost} екр. </b>(<i>Итого:".($bank['ekr']-$akkcost)." екр. </i>)','{$bank['id']}');");
					$bank[ekr]-=$akkcost;
					addchp('<font color=red>Внимание!</font> Вам присвоен '.$strtype[$akneed].' account','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
					$akk_err = 'Вам присвоен '.$strtype[$akneed].' account до '.(date('d-m-Y',$exp));
				}
			}
		}
	}
} elseif (in_array($_GET['otdel'],$unikrazdel) && isset($_GET['set'],$_GET['stype'])) {
	// покупаем уник
	$_GET['set'] = intval($_GET['set']);
	$_GET['stype'] = intval($_GET['stype']);
	if ($_GET['stype'] == 0) {
		$type = "shop";
	} elseif ($_GET['stype'] == 1) {
		$type = "eshop";
	} else {
		die();
	}

	if ($user['klan'] == 'pal') {
		$faliq=' or nalign=6 or nalign=1';
	} elseif ($user['align'] > 0) {
		$faliq=' or nalign='.(int)($user['align']);
	} else {
		$faliq='';
	}

	$dress = mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.".$type." where unikflag>0 and (nalign=0 ".$faliq." ) and id != 7006 and razdel = ".$_GET["otdel"]." and id = ".$_GET['set']));

//	echo "SELECT * FROM oldbk.".$type." where unikflag>0 and (nalign=0 ".$faliq." ) and razdel = ".$_GET["otdel"]." and id = ".$_GET['set'];

	if (!$dress) die();
	$dress['ecost'] = $dress['unikflag'];

	$allcost = $dress['ecost'];

	if ( ((time()>$KO_start_time41) and (time()<$KO_fin_time41)))
			{
				if ($ESHOP_ITEMS_41=='unikflag')
				{
					$allcost=round($allcost*(1-($ESHOP_RATE_41*0.01)),2);

				}
			}



	if (($dress['massa']+$d[0]) > (get_meshok())) {
		$akk_err = "Недостаточно места в рюкзаке.";
		$typet = "e";
	} elseif($bank['ekr']>=$allcost) {
		$as_present = '';
		$as_sowner = '';

		if($dress[nlevel]>6) {
			$str = ",`up_level` ";
			$sql = ",'".$dress[nlevel]."' ";
		}

		$insert_id = array();
		$dress['ekr_flag'] = 2;

		// уник мф
		$mfinfo = array();
		if ($dress['gsila'] > 0 || $dress['glovk'] > 0 || $dress['ginta'] > 0 || $dress['gintel'] > 0 || $dress['gmudra'] > 0) {
			$dress['stbonus'] += 3;
			$mfinfo['stats'] = 3;
		} else {
			$mfinfo['stats'] = 0;
		}
		if ($dress['ghp'] > 0) {
			$dress['ghp'] += 20;
			$mfinfo['hp'] = 20;
		} else {
			$mfinfo['hp'] = 0;
		}

		if ($dress['bron1'] > 0) $dress['bron1'] += 3;
		if ($dress['bron2'] > 0) $dress['bron2'] += 3;
		if ($dress['bron3'] > 0) $dress['bron3'] += 3;
		if ($dress['bron4'] > 0) $dress['bron4'] += 3;

		if ($dress['bron1'] > 0 || $dress['bron2'] > 0 || $dress['bron3'] > 0 || $dress['bron4'] > 0) {
			$mfinfo['bron'] = 3;
		} else {
			$mfinfo['bron'] = 0;
		}

		$dress['unik'] = 1;
		$dress['name'].= ' (мф)';
		$as_present = 'Удача';

		$mfinfo = mysql_real_escape_string(serialize($mfinfo));

		$sql = "INSERT INTO oldbk.`inventory`
			(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,  `img_big`,`maxdur`,`isrep`,`nclass`,`rareitem`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark` ".$str." ,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`ekr_flag`, `unik`,`stbonus`,`mfinfo`,`getfrom`,`notsell`,`craftspeedup`,`craftbonus`
			)
			VALUES
			('{$dress['id']}','{$as_sowner}','{$as_present}','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}', '{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['rareitem']}',
			'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}' ".$sql." ,
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$user[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}','{$dress['ekr_flag']}','{$dress['unik']}','{$dress['stbonus']}','{$mfinfo}','42','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}'
			) ;";


		if(mysql_query($sql))
		{
			$good = 1;
			$insert_id[$k]=mysql_insert_id();
		}


		if ($good==1) {
			$invdb = mysql_query("SELECT * FROM oldbk.`inventory` WHERE id in (".implode(',',$insert_id).") ORDER by `id` DESC ;" );

			$dressinv = mysql_fetch_array($invdb);
			$dressid = get_item_fid($dressinv);
			$dresscount = " ";




			$bank_was = mysql_fetch_array(mysql_query("SELECT * FROM `oldbk`.`bank` WHERE `id` = '".$bank['id']."' ;"));
			mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr`- '".($allcost)."' WHERE id = {$bank['id']}");

			$bank = mysql_fetch_array(mysql_query("SELECT * FROM `oldbk`.`bank` WHERE `id` = '".$bank['id']."' ;"));

			$rec['owner']=$user[id];
			$rec['owner_login']=$user[login];
			$rec['target']=0;
			$rec['target_login']='Березка';
			$rec['type']=140;//покупка за казну
			$rec['sum_kr']=0;
			$rec['sum_ekr']=$allcost;
			$rec['sum_kom']=0;
			$rec['item_id']=$dressid;
			$rec['item_name']=$dress['name'];
			$rec['item_count']=1;
			$rec['item_type']=$dress['type'];
			$rec['item_cost']=$dress['cost'];
			$rec['item_dur']=$dress['duration'];
			$rec['item_maxdur']=$dress['maxdur'];
			$rec['item_ups']=0;
			$rec['item_unic']=1;
			$rec['item_incmagic']='';
			$rec['item_incmagic_count']='';
			$rec['item_arsenal']='';
			$rec['item_mfinfo']=$dressinv['mfinfo'];
			$rec['item_level']=$dress['nlevel'];

			$rec['add_info']='Баланс до '.$bank_was[ekr]. ' после ' .$bank[ekr];
			add_to_new_delo($rec);

			mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Покупка в Берёзке <b>".$dress['name']."</b> за <b>{$allcost} екр.</b>, <i>(Итого: ".($bank['cr'])." кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');");
			$akk_err = "Вы купили 1 шт. \"{$dress['name']}\"";
		}
	} else {
		$akk_err = "Недостаточно денег или нет вещей в наличии";
		$typet = "e";
	}
} elseif (($_GET['set']) and ($_GET['set_ars']) and ($clan_kazna)) {
	//покупка в арсенал излан казны

	$set=(int)($_GET['set']);
	if (($_GET['otdel']==106) and ($clan_kazna))  {
		$glava=' AND glava=1 '; $adda='OR `razdel`>0 ';
	}

	$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` = '{$set}' AND (razdel = ".$otdel." ".$adda."  ) ".$glava."  and need_wins=0  LIMIT 1;"));

 	if (($dress['id']>=55510301) and ($dress['id']<=55510344)) {
		$dress['ecost']=round(($dress['ecost']/2),2);
	} else if (in_array($dress['id'],$arts_50)) {
	 	$dress_ecost_full=$dress['ecost'];
	 	$dress['ecost']=round(($dress['ecost']*0.5)); // арты в полцены
	}



	if (($dress['ecost'] > 0) and ($dress[GetShopCount()] > 0)) {
		$coment="\"".$user['login']."\" купил новый товар: \"".$dress['name']."\" [0/".$dress['maxdur']."] за ".$dress['ecost']." екр. в клановый арсенал";
		mysql_query("UPDATE oldbk.`eshop` SET `".GetShopCount()."`=`".GetShopCount()."`-1 WHERE `id` = '{$set}' AND `".GetShopCount()."` >= 1 LIMIT 1;");
		if(mysql_affected_rows()>0) {
			ob_start();
			if(by_from_kazna($clan_id[id],2,$dress['ecost'],$coment)) {
				$good = 0;
			        $str='';
				$sql='';
				if($dress[nlevel]>6) {
					$str=",`up_level` ";
					$sql=",'".$dress[nlevel]."' ";
				}


				if (!($dress_ecost_full>0)) { $dress_ecost_full=$dress['ecost']; }

				//oldbk. -надо т.к. арс хранится в базе кепа!
				if(mysql_query("INSERT INTO oldbk.`inventory`
					(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`, `img_big`,`maxdur`,`isrep`,`nclass`,`rareitem`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
					`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark` ".$str." ,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`ekr_flag`,`getfrom`,`notsell`,`craftspeedup`,`craftbonus`
					)
					VALUES
					('{$dress['id']}','22125','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress_ecost_full}, '{$dress['img']}', '{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['rareitem']}',
					'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}' ".$sql." ,
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
					'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
					'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$user[klan]}','1','{$user[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}','{$dress['ekr_flag']}','42','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}'
					) ;"))
				{
					$good = 1;
					$insert_item_id=mysql_insert_id();
				}

      				if ($good) {
       					$invdb = mysql_query("SELECT * FROM oldbk.`inventory` WHERE id='{$insert_item_id}' ;" );
       					$dressinv = mysql_fetch_array($invdb);
					$dressid = get_item_fid($dressinv,0); //читать из кепа
					$dresscount=" ";

					mysql_query("INSERT INTO oldbk.clans_arsenal (id_inventory, klan_name, owner_original) 	VALUES 	('{$dressinv[id]}','{$user['klan']}','1')");
					mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$user[klan]}','{$user[id]}','{$coment}','".time()."')");

					$akk_err = "Вы купили 1 шт. \"{$dress['name']}\". (<i>в клановый арсенал</i>)";
					$clan_kazna['ekr']-=$dress['ecost'];
				}
			} else {
				$akk_err = ob_get_clean();
				$akk_err .= ' Ошибка покупки в клановый арсенал!';
				$typet = "e";
	       		}
	       	} else {
			$akk_err = 'Вы не можете купить эту вещь!';
			$typet = "e";
		}
	} else {
		$akk_err = 'Вы не можете купить эту вещь!';
		$typet = "e";
	}
} elseif (($_GET['set'] OR $_POST['set'])) {
	if ($_GET['set']) { $set = $_GET['set']; }
	if ($_POST['set']) { $set = $_POST['set']; }
	if ($_POST['count'] < 1) { $_POST['count'] =1; }
	$_POST['count']  = round($_POST['count']);

	if ((($_GET['otdel']==100)and($user[winstbat]>=100))  OR
		(($_GET['otdel']==300)and($user[winstbat]>=300))  OR
		(($_GET['otdel']==500)and($user[winstbat]>=500))
	) {
		$adda='OR `razdel`>0 ';
	} else {
		$adda='';
	}

	if ($_GET['otdel']==50) { $otdel=5; }

	$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` = '{$set}' AND (razdel = ".$otdel."  ".$adda."   )AND glava=0  LIMIT 1;"));

	$dress_ecost_gos=$dress['ecost'];
	if (((time()>$KO_start_time24) and (time()<$KO_fin_time24))) {
		if (strpos($dress['name'], 'Восстановление энергии') !== false || strpos($dress['name'], 'свиток «Восстановление') !== false) {
			$dress['ecost']=round($dress['ecost']*0.8,2);
		}
	}


	if ($dress['need_wins']>$user['winstbat']) {
		$akk_err = "У вас нехватает побед в Великих битвах для покупки этого предмета!";
		$typet = "e";
	} elseif (($dress['massa']*$_POST['count']+$d[0]) > (get_meshok())) {
		$akk_err = "Недостаточно места в рюкзаке.";
		$typet = "e";
	} elseif(($bank['ekr']>= ($dress['ecost']*$_POST['count'])) && ($dress[GetShopCount()] >= $_POST['count'])) {
		if (give_count($user['id'],$_POST['count'])) {
			mysql_query("UPDATE oldbk.`eshop` SET `".GetShopCount()."`=`".GetShopCount()."`-{$_POST['count']} WHERE `id` = '{$set}' AND `".GetShopCount()."` >= {$_POST['count']}  LIMIT 1");
			if(mysql_affected_rows()>0) {

				mysql_query('INSERT INTO shop_stats (owner,shoptype,shopprototype,shopcount,lastupdate)
						VALUES ('.$user['id'].',2,'.$set.','.$_POST['count'].','.time().')
						ON DUPLICATE KEY UPDATE
							`shopcount` = `shopcount` + '.$_POST['count'].', lastupdate = '.time()
				);

				//привязываем шмотки из раздела великих = если прописаны нужные попебы то одаем подарком
				if (($dress[need_wins]>0) and ($dress[type]==12)) {
					$as_present='Удача';
					$as_sowner=0;
				} elseif (($dress[need_wins]>0) and ($dress[type]!=12)) {
					//если кольца то не подарком а привязанное
					$as_present='';
					$as_sowner=$user['id'];
				} elseif ($dress['is_owner']) {
					$as_present='';
					$as_sowner=$user['id'];
				} elseif ($dress['id'] == 56664) {
					$as_present='Удача';
					$as_sowner=0;
				} else {
					$as_sowner=0;
					$as_present='';
				}


				$as_present='Удача';

				$insert_id=array();
				$good = 0;

				for($k=1;$k<=$_POST['count'];$k++)
				 {
					if($dress[nlevel]>6) {
						$str=",`up_level` ";
						$sql=",'".$dress[nlevel]."' ";
					}

						if ($dress['id']==33333) {
							//лото
							$get_lot=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;"));
							mysql_query("INSERT INTO oldbk.`item_loto` SET `loto`={$get_lot[id]},`owner`={$user[id]},`dil`=0,`lotodate`='".date("Y-m-d H:i:s",$get_lot['lotodate'])."';");
							$new_bil_id=mysql_insert_id();
//							$dress['letter']="Купон №".$new_bil_id."<br>Розыгрыш №".$get_lot[id]."<br>Cостоится ".date("Y-m-d H:i:s",$get_lot['lotodate']);
							$dress['letter']="Следующий обмен купонов на подарки состоится ".date("Y-m-d H:i:s",$get_lot['lotodate']);
							$dress['upfree']=$get_lot[id];
							$dress['mffree']=$new_bil_id;
							$as_present='Мироздатель';
						}

					if(mysql_query("INSERT INTO oldbk.`inventory`
						(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`, `img_big`,`maxdur`,`isrep`,`nclass`,`letter`,`upfree`,`mffree`,`rareitem`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
							`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark` ".$str." ,
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`ekr_flag`,`getfrom`,`notsell`,`craftspeedup`,`craftbonus`
						)
						VALUES
						('{$dress['id']}',{$as_sowner},'{$as_present}','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress_ecost_gos}, '{$dress['img']}', '{$dress['img_big']}' ,{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['letter']}','{$dress['upfree']}','{$dress['mffree']}','{$dress['rareitem']}',
						'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}' ".$sql." ,
						'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
						'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
						'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$user[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}','{$dress['ekr_flag']}','42','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}'
						) ;"))
					{
						$good = 1;
						$insert_id[$k]=mysql_insert_id();
					}
				}

				if ($good==1) {
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
					$allcost=$_POST['count']*$dress['ecost'];

					$bank_was = mysql_fetch_array(mysql_query("SELECT * FROM `oldbk`.`bank` WHERE `id` = '".$bank['id']."' ;"));
					mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr`- '".($allcost)."' WHERE id = {$bank['id']}");
					$bank = mysql_fetch_array(mysql_query("SELECT * FROM `oldbk`.`bank` WHERE `id` = '".$bank['id']."' ;"));

					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['target']=0;
					$rec['target_login']='Березка';
					$rec['type']=140;//покупка за казну
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$allcost;
					$rec['sum_kom']=0;
					$rec['item_id']=$dressid;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=$_POST['count'];
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['item_mfinfo']=$dress['mfinfo'];
					$rec['item_level']=$dress['nlevel'];

					$rec['add_info']='Баланс до '.$bank_was[ekr]. ' после ' .$bank[ekr];
					add_to_new_delo($rec);

					$akk_err = "Вы купили {$_POST['count']} шт. \"{$dress['name']}\"";
				}
			} else {
				$akk_err = "Недостаточно денег или нет вещей в наличии";
				$typet = "e";
			}
		} else {
			$akk_err = "У Вас недостаточно лимита передач на сегодня!";
			$typet = "e";
		}
	} else {
		$akk_err = "Недостаточно денег или нет вещей в наличии";
		$typet = "e";
	}
} elseif (isset($_GET['newsale'],$_GET['sid']) && $user['align'] != 4) {
	$sql = "SELECT *, (select stavka from oldbk.skupka where itemid=i.id) as stavka FROM oldbk.`inventory` as i WHERE `dressed`= 0 AND prototype IN (".implode(",",$saleprotos).")
			and notsell = 0 AND ecost>0 AND setsale=0 and type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0) AND prokat_idp = 0 AND bs_owner = 0 AND ( sowner=0 or sowner='{$user['id']}') AND (repcost=0 or ekr_flag > 0) AND arsenal_klan='' AND present!='Арендная лавка' AND `owner` = '{$user['id']}'";

	$data = mysql_query($sql.' and id = '.intval($_GET['sid']));
	if (mysql_num_rows($data) > 0) {
		$row = mysql_fetch_assoc($data) or die();
		// получаем прототип
		$proto = mysql_query_cache('SELECT * FROM shop WHERE id = '.$row['prototype'],false,10*60);
		if (!($proto === false || !count($proto))) {
			$proto = $proto[0];

			// высчитываем ценник
			if ($row['stavka'] > 0) {
				$ekr_price = round((($row['ecost']/$proto['maxdur'])*$row['maxdur']*($row['stavka']/100)),2);
			} else {
				$ekr_price = round((($row['ecost']/$proto['maxdur'])*$row['maxdur']*0.50),2);
			}

			$akk_err = "Вы продали \"".$row['name']."\" за ".$ekr_price." екр.";

		    	$rec['owner']=$user['id'];
			$rec['owner_login']=$user['login'];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money'];
			$rec['target']=0;
			$rec['target_login']='Березка';
			$rec['type']=3434;//продажа в березку
			$rec['sum_kr']=0;
			$rec['sum_ekr']=$ekr_price;
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
			$rec['bank_id']=$bank['id'];
			$rec['add_info'] = 'Баланс до '.$bank['ekr']. ' после ' .($bank['ekr']+$ekr_price);
			add_to_new_delo($rec);

			mysql_query("UPDATE oldbk.`bank` set `ekr` = ekr+'{$ekr_price}' WHERE `id` = '{$bank['id']}' LIMIT 1;");
			mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Продан предмет <b>{$row['name']}<b> за {$ekr_price} екр. </b>(<i>Итого:".($bank['ekr']+$ekr_price)." екр. </i>)','{$bank['id']}');");
			destructitem($row['id']);
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="windows-1251">
<title></title>
<link rel="StyleSheet" href="newstyle_loc4.css" type="text/css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="/i/bank9.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<style>
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

</style>
<SCRIPT LANGUAGE="JavaScript">

function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}

function AddCount(event,name, txt, razdel) {

    var el = document.getElementById("hint3");
	el.innerHTML = '<form action="?otdel='+razdel+'" method=post style="margin:0px; padding:0px;"><table style="FONT-SIZE: 10pt; FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif" border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B>Купить неск. штук</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3(); return false;"><BIG><B>x</TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B><I>'+txt+'</td></tr><tr><td width=80% align=right>'+
	'Количество (шт.) <INPUT id="count" TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	y = event.pageY;
	el.style.top = (y -120) + 'px';
	document.getElementById("count").focus();
}
// Закрывает окно
function closehint3() {
	document.getElementById("hint3").style.visibility="hidden";
}


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

				 $('#pl').css({ position:'absolute',left: (($(window).width()-$('#pl').outerWidth())/2)+200, top: '200px'  });


				}

			}

			function closeinfo()
			{
			  	$('#pl').hide(200);
			}
</SCRIPT>
</HEAD>
<body id="arenda-body">
<div id="pl" style="z-index: 300; position: absolute; left: 50%; top: 120px;
				width: 750px; height:365px; background-color: #eeeeee;
				margin-left: -375px;
				border: 1px solid black; display: none;"></div>
<script type='text/javascript'>
RecoverScroll.start();
</script>
<div id="page-wrapper">
    <div class="title">
        <div class="h3">Магазин &quot;Березка&quot;</div>
        <div id="buttons">
            <a class="button-dark-mid btn" onclick="window.open('help/eshop.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes'); return false;" title="Подсказка">Подсказка</a>
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
                <td>
		    <?php
			$lasttime = 30*24*3600; // за последний месяц
			$q = mysql_query('SELECT eshop.* FROM shop_stats LEFT JOIN eshop ON shopprototype = eshop.id WHERE shop_stats.owner = '.$user['id'].' and shoptype = 2 and lastupdate > '.(time()-$lasttime).' ORDER BY shopcount DESC, lastupdate DESC LIMIT 12');
			if (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0 && mysql_num_rows($q) > 0) {
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
					<?php while($row = mysqL_fetch_assoc($q))
					{
						if (!empty($row['img_big'])) {
							$row['img'] = $row['img_big'];
						} ?>
						<li style="display:inline-block;">
							<div class="img-block" style="text-align:center;">
								<img alt="<?=$row['name']?>" title="<?=$row['name']?>" src="http://i.oldbk.com/i/sh/<?= $row['img']; ?>">
							</div>
							<div class="btn-block" style="text-align:center;">
								<a href="?otdel=<?=$row['razdel']; ?>&set=<?= $row['id'] ?>">купить</a>
								<img src="http://i.oldbk.com/i/up.gif" width="11" height="11" border="0" alt="Купить несколько штук" style="cursor: pointer" onclick="AddCount(event,'<?= $row['id']?>', '<?= $row['name']?>',<?=$row['razdel'];?>); return false;">
							</div>
						</li>
					<?php } ?>
				</ul>
			</td></tr>
		    </table>

		    <br>
                    <?php } ?>


                    <table class="table border" style="margin-bottom: 0;<?php if (!isset($_SESSION['bankid'])) echo "display:none;" ?>" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="400px">
                            <col>
                        </colgroup>
                        <thead>
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title">

<?php
				if ($_REQUEST['compare'] && !$_REQUEST['common'] && !$_REQUEST['present']) {
					echo "Составление подарочного букета";
				} elseif ($_REQUEST['present']) {
					//echo "Составление подарочного букета";
				} elseif ($_GET['do']=='1') {
					echo "Установка премиум аккаунта";
				} else {
					switch ($_GET['otdel']) {
						case null:
							if (isset($_GET['newsale'])) {
								echo "Продать вещи";
							} else {
								echo "Заклинания: нейтральные";
								$_GET['otdel'] = 5;
							}
						break;
						case 5:
							echo "Заклинания: нейтральные";
						break;
						case 51:
							echo "Заклинания: боевые и защитные";
						break;
						case 52:
							echo "Заклинания: сервисные";
						break;
						case 50:
							echo "Лицензии";
						break;
						case 106:
							echo "Клановая Амуниция (раздел для глав кланов)";
						break;
						case 61:
							echo "Еда";
						break;
						case 64:
							echo "Купоны";
						break;
						case 7:
							echo "Сувениры: открытки";
						break;
						case 71:
							echo "Сувениры: подарки";
						break;
						case 73:
							echo "Сувениры: Лето";
						break;
						case 75:
							echo "Сувениры: Новогодние сюрпризы";
						break;
						case 99:
							echo "Прилавок Великих";
						break;
						case 100:
							echo "Прилавок Великих (100 побед) ";
						break;
						case 300:
							echo "Прилавок Великих (300 побед) ";
						break;
						case 500:
							echo "Прилавок Великих (500 побед) ";
						break;
						case 700:
							echo "Прилавок Великих (700 побед) ";
						break;

						case 6:
							echo "Плащи";
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
							echo "Тяжёлая броня";
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

	?>
				</div>
                            </th>
                            <th class="filter">
			    	<form method="POST" id="fall" style="margin:0px;padding:0px;display:block;">
                                <div class="head-title">
					<?php if (!isset($_GET['newsale'])) { ?>
	                                    <select name="fall" style="width:250px;height:14px;margin:0px;top:0px;" OnChange="document.getElementById('fall').submit();">
						<option value = "0" <? if ((int)($_POST['fall'])==0) { echo ' selected ' ; } ?>>Показывать все вещи</option>
						<option value = "1" <? if ($_POST['fall']>0) { echo ' selected ' ; $viewlevel=true; } ?>>Показывать вещи только моего уровня</option>
	                                    </select>
					<?php } ?>
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
if(!$_SESSION['bankid']) {
?>
<table cellspasing=3 cellpadding=2><tr>
<td>
	<form method=post>

	<fieldset style="width:200px; height:130px;">
		<legend>Войти в счет</legend><BR> &nbsp; №
<?
	$banks = mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = ".$user['id'].";");
	echo "<select style='width:150px' name=id>";
	while ($rah = mysql_fetch_array($banks)) {
			echo "<option>",$rah['id'],"</option>";
	}
	echo "</select>";
?>
        <BR> &nbsp;
            Пароль <input type=password name=pass size=21>
        <BR><BR>
            <div style="text-align: center" class="btn-control">
                <input class="button-mid btn" style='height: 17px;' type=submit name='enter' value='Войти'>
            </div>
        </form>

        <form action="city.php" method="GET">
            <div style="text-align: center" class="btn-control">
                <INPUT class="button-mid btn" TYPE="submit" value="Вернуться" style="height: 17px;" name="cp">
            </div>
        </form>
    </fieldset>
<br>
    <b>
        <font color="#003388">В магазине "Березка" вы можете купить вещи за еврокредиты.<br></font>
        <font color="red">
            Еврокредиты можно приобрести у любого дилера, либо купить в Банке игры.<br>
            Также, на Бирже имеется возможность купить еврокредиты за кредиты у других игроков.</font><br></b>

    </td>
    </tr>
</table>
<?
	die();
}
if (strlen($akk_err)) {
	echo '
		<script>
			var n = noty({
				text: "'.addslashes($akk_err).'",
			        layout: "topLeft2",
			        theme: "relax2",
				type: "'.($typet == "e" ? "error" : "success").'",
			});
		</script>
	';
}

if ($_GET['do'] == 1) {
	//услуги
	$icon_count=0;
	$data = mysql_query("SELECT * FROM oldbk.`eshop` WHERE  id in (54999) ORDER by `id`, cost ASC");  //55999,56999
	while($row = mysql_fetch_array($data)) {

		if ($i == 0) { $i = 1; $color = '#C7C7C7';}
		else { $i = 0; $color = '#D5D5D5'; }
		echo "<tr bgcolor='{$color}'><td align='center' style='width:150px;";

				if ((time()>$KO_start_time48) and (time()<$KO_fin_time48))
				{
				$row['ecost']=round($row['ecost']*0.5,2);
				$row['shopicon']=50;
				}



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


		echo "<center><img src=\"http://i.oldbk.com/i/sh/item_{$row['id']}.gif\" border='0'/>";

		echo "<br><a href=\"?do=1&up=".$row['id']."\">Использовать</a></center>";
		echo "</TD>";

		echo "<td valign='top'>";

		$ht=str_replace('.gif','.html',$row['img']);

		if ($ht=='036.html') {
			$ht= "prem.html";
		} elseif ($ht=='prem.html') {
			$ht= "prem.html";
		} elseif ($ht=='137.html') {
			$ht= "prem.html";
		}

		echo "<a href=https://oldbk.com/encicl/{$ht} target=_blank>{$row['name']}</a><BR>";



		echo "<b>Цена: {$row['ecost']} екр.</b><BR>";
		$magic = magicinf ($row['magic']);
		echo "<span style='display: inline-block;border: 1px solid;padding: 4px;border-style: inset;border-width: 1px;margin-top: 6px;margin-bottom: 6px;'> ".$row['letter']."</span><br>";
		echo "<br><b>Свойства:</b><br>";
		echo "• ".$magic['name'];
		echo "</td></TR>";
	}
} elseif (($_GET['otdel']==100)and($user[winstbat]<100)) {
	$akk_err = '<div align=center><b>У вас еще нет 100 побед в Великих битвах!</b></div>';
} elseif (($_GET['otdel']==300)and($user[winstbat]<300)) {
	$akk_err = '<div align=center><b>У вас еще нет 300 побед в Великих битвах!</b></div>';
} elseif (($_GET['otdel']==500)and($user[winstbat]<500)) {
	$akk_err = '<div align=center><b>У вас еще нет 500 побед в Великих битвах!</b></div>';
} elseif (in_array($_GET['otdel'],$unikrazdel)) {
	if ($user['klan'] == 'pal') {
		$faliq=' or nalign=6 or nalign=1';
	} elseif ($user['align'] > 0) {
		$faliq=' or nalign='.(int)($user['align']);
	} else {
		$faliq='';
	}

	if ($viewlevel) {
		$addlvl = ' and nlevel = '.$user['level'];
	} else {
		$addlvl = '';
	}


	function cmpecost($a, $b) {
    		if ($a['unikflag'] == $b['unikflag']) {
        		return 0;
    		}
    		return ($a['unikflag'] < $b['unikflag']) ? -1 : 1;
	}

	$q1=mysql_query("SELECT *,0 as stype FROM oldbk.shop where unikflag>0 and (nalign=0 ".$faliq." ) and id != 7006 and razdel = ".$_GET["otdel"].$addlvl.' order by nlevel ASC');
	$q2=mysql_query("SELECT *,1 as stype FROM oldbk.eshop where unikflag>0 and (nalign=0 ".$faliq.") and id != 7006 and razdel = ".$_GET["otdel"].$addlvl.' order by nlevel ASC');

	$rows = array();

	while (($row=mysql_fetch_assoc($q1)) || ($row=mysql_fetch_assoc($q2))) {
		$rows[] = $row;

	}

	usort($rows, "cmpecost");
	$icon_count=0;
	while(list($k,$row) = each($rows)) {
		if ($row['img_big']!='') { $row['img']=$row['img_big']; }
		$row['ecost'] = $row['unikflag'];

		$row['present'] = 'Удача';

		if ( ((time()>$KO_start_time41) and (time()<$KO_fin_time41))) {
				if ($ESHOP_ITEMS_41=='unikflag')
				{

					$row['ecost']=round($row['ecost']*(1-($ESHOP_RATE_41*0.01)),2);
					$row['shopicon']=$ESHOP_RATE_41;
				}
			}


		// уник мф
		if ($row['gsila'] > 0 || $row['glovk'] || $row['ginta'] || $row['gintel'] || $row['gmudra']) $row['stbonus'] += 3;
		if ($row['ghp'] > 0) $row['ghp'] += 20;

		if ($row['bron1'] > 0) $row['bron1'] += 3;
		if ($row['bron2'] > 0) $row['bron2'] += 3;
		if ($row['bron3'] > 0) $row['bron3'] += 3;
		if ($row['bron4'] > 0) $row['bron4'] += 3;
		$row['unik'] = 1;
		$row['name'].= ' (мф)';
		$row['present'] = 'Удача';

		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
		echo "<TR bgcolor={$color}><TD align=center style='width:150px;";

		if ($row['shopicon']>0) {
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

		echo "<br><a name=\"{$row['id']}\"><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";

		?>
		<BR><A HREF="?otdel=<?=$_GET['otdel']?>&set=<?=$row['id']?>&stype=<?php echo $row['stype']; ?>">купить</A>
		</TD>
		<?php
		echo "<TD valign=top>";
		$row[GetShopCount()] = 1;
		showitem ($row);
		echo "</TD>";
	}
} elseif (isset($_GET['newsale']) && $user['align'] != 4) {
	$sql = "SELECT *, (select stavka from oldbk.skupka where itemid=i.id) as stavka FROM oldbk.`inventory` as i WHERE `dressed`= 0 AND prototype IN (".implode(",",$saleprotos).")
			and notsell = 0 AND ecost>0 AND setsale=0 and type not in (200,77,30) and (ISNULL(art_param) and ab_uron = 0 and ab_bron = 0 and ab_mf=0) AND ( sowner=0 or sowner='{$user['id']}')  AND  (repcost=0 or ekr_flag > 0) AND prokat_idp = 0 AND bs_owner = 0 AND arsenal_klan='' AND present!='Арендная лавка' AND `owner` = '{$user['id']}'";

	$data = mysql_query($sql);

	while($row = mysql_fetch_array($data)) {
		$row[GetShopCount()] = 1;
		if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }

		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><br>";
		echo '<center><small>('.get_item_fid($row).')</small></center>';

		// получаем прототип
		$proto = mysql_query_cache('SELECT * FROM shop WHERE id = '.$row['prototype'],false,10*60);
		if ($proto === false || !count($proto)) continue;
		$proto = $proto[0];

		// высчитываем ценник
		if ($row['stavka'] > 0) {
			$ekr_price = round((($row['ecost']/$proto['maxdur'])*$row['maxdur']*($row['stavka']/100)),2);
		} else {
			$ekr_price = round((($row['ecost']/$proto['maxdur'])*$row['maxdur']*0.50),2);
		}

		?>
		<BR><A HREF="?newsale=1&sid=<?=$row['id']?>">продать за <?=$ekr_price;?> екр.</A>
		<?php
		echo "</TD><TD valign=top>";
		showitem($row);
		echo "</TD></TR>";
	}
} else {
	if (($_GET['otdel']==106) and ($clan_kazna)) {
		$glava=' AND glava=1 '; $adda='OR `razdel`>0 ';
	} else {
		$glava=' AND glava=0 ';
	}

	if ((($_GET['otdel']==100)and($user[winstbat]>=100))  OR
	    (($_GET['otdel']==300)and($user[winstbat]>=300))  OR
	    (($_GET['otdel']==500)and($user[winstbat]>=500)) )
	{
		$vitrina=" OR  `razdel`>0) AND need_wins='{$_GET['otdel']}' ";
	} else {
		$vitrina=" ) AND need_wins=0 ";
	}

	if ($_GET['otdel']==5) {
		$adda=" and name not like 'Лицензия%' ";
	} elseif ($_GET['otdel']==50) {
		$otdel=5;
		$adda=" and name like 'Лицензия%' ";
	}

	if ($viewlevel==true) {
		if ($user['level']>13) {
			$addlvl=" and nlevel='13' ";
  		} else {
			$addlvl=" and nlevel='{$user['level']}' ";
	  	}
  	} else {
		$addlvl = "";
  	}

	$sql = "SELECT * FROM oldbk.`eshop` WHERE `".GetShopCount()."` > 0  ".$addlvl."  AND ((`razdel` = '{$otdel}' ".$adda."  ".$vitrina."    )  and `ecost` > 0 ".$glava." ORDER by `ecost` ASC,`id` DESC";
	$data = mysql_query($sql);


	$icon_count=0;

	while($row = mysql_fetch_array($data)) {
		$ddd.=$row['id'].",";
		if ($row['img_big']!='') { $row['img']=$row['img_big']; }
		if (($row['id']>=55510301) and ($row['id']<=55510344)) {
			$row['ecost']=round(($row['ecost']/2),2);
		}

		if ( ((time()>$KO_start_time24) and (time()<$KO_fin_time24)))
		{
			if (strpos($row['name'], 'Восстановление энергии') !== false || strpos($row['name'], 'свиток «Восстановление') !== false) {
				$row['ecost']=round($row['ecost']*0.8,2);
				$row['shopicon']=20;
			}
		}

		if ($row['id']==33333) {
			$row['present']='Мироздатель';
		}

		if ($row['id']==56664) {
			$row['present']='Мироздатель';
		}

		$row['present']='Удача';


		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
			echo "<TR bgcolor={$color}><TD align=center style='width:150px;";

			if ($banners[$row['shopbanner']]!='') {
				$banner=$banners[$row['shopbanner']];
				echo "vertical-align:top;'>";
				echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -55px;top: -11px;\" ".$banner.">";
				$icon_count++;
			} elseif ($row['shopicon']>0) {
				echo "vertical-align:top;'>";
				echo "<img id=\"icon".$icon_count."\" style=\"position:relative;left: -55px;top: -11px;\" src='http://i.oldbk.com/i/icon_s_".(int)($row['shopicon']).".png'>";
				$icon_count++;
			} else {
				echo "vertical-align:middle;'>";
			}
			echo "<br><a name=\"{$row['id']}\"><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";

		if ($_GET['otdel']!=106) {
		?>
			<BR><A HREF="?otdel=<?=$_GET['otdel']?>&set=<?=$row['id']?>">купить</A>
			<IMG SRC="i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Купить несколько штук" style="cursor: pointer" onclick="AddCount(event,'<?=$row['id']?>', '<?=$row['name']?>',<?=$row['razdel'];?>); return false;">
		<?
		}

	   	if (((($row[type]>=1) and ($row[type]<=11)) or ($row[type]==28) ) AND ($row[need_wins]==0)) {
			if ($clan_kazna) {
				echo '<BR><A HREF="?otdel='.$_GET['otdel'].'&set='.$row['id'].'&set_ars=1">купить в арсенал</A>';
				if (in_array($row['id'],$arts_50)) {
					$row['ecost']=round($row['ecost']*0.5);
				}
			} else {
				echo '';
			}
	   	}

		?>
		</TD>
		<?php
		echo "<TD valign=top>";
	        $priv=($row['is_owner']==1?1:0);
		showitem ($row, 0, false,'','',0,$priv);
		echo "</TD>";
	}
}
?>
	</TR></TABLE>

                <td>
                    <table id="filter" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
                                <strong>Вес всех ваших вещей:
				<?php
					echo $d[0];?>/<?=get_meshok()?>
				</strong><br>
                                У Вас в наличии: <span class="money"><strong><?=$bank['ekr']?></strong></span><strong> екр.</strong>
				<?php if ($clan_kazna) { echo '<br>В казне: <span class="money"><strong>'.$clan_kazna['ekr'].'</strong></span><strong> eкр.</strong>'; }
				if ($_SESSION['bankid']>0) {
					echo '</td></tr><tr><td align="center"><a onclick="getformdata(99,0,event);" href="#"><img src="http://i.oldbk.com/i/bank/knopka_ekr.gif"  alt="Купить еврокредиты через Банк" alt="Купить еврокредиты через Банк"></a>';
				}
				?>
                            </td>
                        </tr>
                        <?/*
                        <tr>
                            <td class="hint-block size11 center">
                                <span style="color: red">
                                    Еврокредиты можно приобрести у любого дилера, либо купить в Банке игры.
                                </span>
                            </td>
                        </tr>*/ ?>
			<tr>
                            <td class="hint-block size11 center">
                                <strong><span class="money">Курс 1 екр = 1$</span></strong>
                            </td>
                        </tr>
			<tr>
                            <td class="size11 center">
				<? if ($user['align'] != 4) { ?><a class="button-big btn" href="?newsale=1">Продать вещи</a> <?}?>
                            </td>
                        </tr>

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
                            <td class="filter-title">Заклинания и свитки</td>
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
                                    <li>
                                        <a HREF="?otdel=52&tmp=<?=mt_rand(1111,9999);?>">Сервисные</a>
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
                                        <a HREF="?otdel=6&tmp=<?=mt_rand(1111,9999);?>">Плащи</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=22&tmp=<?=mt_rand(1111,9999);?>">Легкая броня</a>
                                    </li>
				    <? if (ADMIN) { ?>
                                    <li>
                                        <a HREF="?otdel=2&tmp=<?=mt_rand(1111,9999);?>">Сапоги</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=21&tmp=<?=mt_rand(1111,9999);?>">Перчатки</a>
                                    </li>

                                    <li>
                                        <a HREF="?otdel=23&tmp=<?=mt_rand(1111,9999);?>">Тяжёлая броня</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=24&tmp=<?=mt_rand(1111,9999);?>">Шлемы</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=3&tmp=<?=mt_rand(1111,9999);?>">Щиты</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=4&tmp=<?=mt_rand(1111,9999);?>">Серьги</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=41&tmp=<?=mt_rand(1111,9999);?>">Ожерелья</a>
                                    </li>
                                    <li>
                                        <a HREF="?otdel=42&tmp=<?=mt_rand(1111,9999);?>">Кольца</a>
                                    </li>
				    <?php } ?>

				    <?php if ($clan_kazna) { ?>
                                    <li>
                                        <a HREF="?otdel=106&tmp=<?=mt_rand(1111,9999);?>">Амуниция Клановая</a>
                                    </li>
				    <?php } ?>
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
                                        <a HREF="?otdel=78&tmp=<?=mt_rand(1111,9999);?>">Акции</a>
                                    </li>
																		<li>
																				<a HREF="?otdel=82&tmp=<?=mt_rand(1111,9999);?>">Хеллоуин</a>
																		</li>
                                    <li>
                                        <a HREF="?otdel=61&tmp=<?=mt_rand(1111,9999);?>">Еда</a>
                                    </li>
				  <li>
                                        <a HREF="?otdel=64&tmp=<?=mt_rand(1111,9999);?>">Купоны</a>
                                    </li>
                                    <li>
					<A HREF="?do=1&tmp=<?=mt_rand(1111,9999);?>">Премиум аккаунты</A><BR>
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
                                        <a HREF="?otdel=63&tmp=<?=mt_rand(1111,9999);?>">Инструменты</a>
                                    </li>
																		<li>
																				<a HREF="?otdel=62&tmp=<?=mt_rand(1111,9999);?>">Ресурсы</a>
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
                                    <li>
                                        <a HREF="?otdel=100&tmp=<?=mt_rand(1111,9999);?>">100 побед</a>
                                    </li>
				    <?php if ($user[winstbat]>=100) { ?>
	                                    <li>
	                                        <a HREF="?otdel=300&tmp=<?=mt_rand(1111,9999);?>">300 побед</a>
	                                    </li>
				    <?php } ?>
				    <?php if ($user[winstbat]>=300) { ?>
	                                    <li>
	                                        <a HREF="?otdel=500&tmp=<?=mt_rand(1111,9999);?>">500 побед</a>
	                                    </li>
				    <?php } ?>
                                </ul>
                            </td>
                        </tr>
                        -->
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
<div id="hint3" class="ahint"></div>
</BODY>
</HTML>
