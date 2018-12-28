<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


function mk_my_item($telo,$proto,$shoptype,$rowm,$ekrcost = 0, $count = 1, $nopresent = 0) {
	if ($shoptype == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($shoptype == 2) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.cshop where id='{$proto}' ;"));
	}

	if ($dress === false) die("no proto: ".$proto);

	$dress['dategoden'] = time()+(24*3600*90);
	$godentime = $dress['dategoden'];
	$dress['goden'] = 90;

	if (!$nopresent) {
		//$dress['present'] = 'Удача';
	}

	/*
	if ($dress['razdel'] == 5 || $dress['razdel'] == 51) {
        	$ekr_flag = 1;
	}*/
	$dress['sowner'] = 0;
	$dress['unik'] = 0;

	$getfrom = 0;

	if ($rowm['prototype'] == 2016001) {
		$getfrom = 131;
		if (!$nopresent) {
			$ekr_flag = 1;
		}
	} elseif ($rowm['prototype'] == 2016002) {
		if (!$nopresent) {
			$dress['present'] = 'Удача';
		}
		$getfrom = 132;
		if ($proto == 1121) $dress['sowner'] = $telo['id'];
	}

	if ($proto == 33333) {
		$dress['present'] = 'Удача';
		$get_lot = mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;") or die();
		$get_lot=mysql_fetch_array($get_lot);
		mysql_query("INSERT INTO oldbk.`item_loto` SET `loto`={$get_lot[id]},`owner`={$telo[id]},`dil`=0,`lotodate`='".date("Y-m-d H:i:s",$get_lot['lotodate'])."';");
		$new_bil_id=mysql_insert_id();
		$dress['letter']="Следующий обмен купонов на подарки состоится ".date("Y-m-d H:i:s",$get_lot['lotodate']);									
		$dress['upfree']=$get_lot[id];
		$dress['mffree']=$new_bil_id;
		$dress['goden'] = 0;
		$dress['dategoden'] = 0;
	}


	$good = 0;

	for ($i = 0; $i < $count;$i++) {
		if(mysql_query("INSERT INTO oldbk.`inventory`
			(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`notsell`,`ekr_flag`,`sowner`,`img_big`,`unik`,`getfrom`,`upfree`,`mffree`
			)
			VALUES
				('{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
				'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
				'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}',1, '{$ekr_flag}','{$dress['sowner']}','{$dress['img_big']}','{$dress['unik']}',{$getfrom},'{$dress['upfree']}','{$dress['mffree']}'
				) ;"))
		{
			$good = 1;
			$insert_item_id=mysql_insert_id();
			$newdress['idcity']=$telo[id_city];
			$newdress['id']=$insert_item_id;
	        } else {
			$good = 0;
		}		
			
	
		if ($good) {
			$rec['owner']=$telo['id'];
			$rec['owner_login']=$telo['login'];
			$rec['target']=0;
			$rec['target_login']='Яйцо';
			$rec['owner_balans_do']=$telo['money'];
			$rec['owner_balans_posle']=$telo['money'];
			$rec['type']=419;//   получил из ларца
			$rec['sum_kr']=0;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($newdress);
			$rec['item_name']=$dress['name'];
			$rec['item_count']=1;
			$rec['item_type']=$dress['type'];
			$rec['item_cost']=$dress['cost'];
			$rec['item_dur']=$dress['duration'];
			$rec['item_maxdur']=$dress['maxdur'];
			$rec['item_ups']=0;
			$rec['item_unic']=0;
			$rec['item_incmagic']=$dress['includemagic'];
			$rec['item_incmagic_count']=$dress['includemagicdex'];
			$rec['item_arsenal']='';
			$rec['add_info']="cap".$rowm['id'].' '.$rowm['name'];
			add_to_new_delo($rec) or die();
		} else {
			die();
		}
	}
	if ($count > 1) $dress['name'] .= ' x'.$count;
	return $dress['name'];
}


function mk_unik_medal($telo,$med) {
	$imgs['ekr']='http://i.oldbk.com/i/easter_icon2016_e.gif';
	$imgs['kr']='http://i.oldbk.com/i/easter_icon2016_kr.png';	
	
	$txts['ekr']='Со светлым праздником Пасхи!';
	$txts['kr']='С Пасхой';
	
	$img=$imgs[$med];
	$txt=$txts[$med];	
	$ended_timestamp = mktime(0,0,0,4,8,2019);

	$badge_types = array(
	 'ekr' => \components\models\UserBadge::TYPE_EASTER_EKR,
	 'kr' => \components\models\UserBadge::TYPE_EASTER_KR
		);
	$badge_type = $badge_types[$med];

	if(\components\models\UserBadge::addOrUpdateExpire($telo['id'],$img, $txt, $ended_timestamp, $badge_type) === false)
	{
		return false;
	}
	else
	{
		return true;
	}
}


$l1 = 0;
$l2 = 0;
$maxekr = 0;

if ($rowm['prototype'] == 2016001) {
	$l1 = 51;
	$l2 = 57;
	$l3 = 52;
} elseif ($rowm['prototype'] == 2016002) {
	$l1 = 53;
	$l2 = 55;
	$l3 = 54;
} else {
	die("!");
}


mysql_query('START TRANSACTION') or die();
// выгребаем обязательные подарки
$q = mysql_query('SELECT * FROM eggs WHERE listid = '.$l1) or die(mysql_error());
$txt = "";

while($i = mysql_fetch_assoc($q)) {
	$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm,$i['ekrprice'],$i['count']);
	$txt .= ", ";
}



$q = mysql_query('SELECT * FROM eggs WHERE listid = '.$l2.' ORDER BY RAND() LIMIT 1') or die();
while($i = mysql_fetch_assoc($q)) {
	$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm,$i['ekrprice'],$i['count'],1);
	$txt .= ", ";
}



// выгребаем рандомные подарки
$noids = array();
$q = mysql_query('SELECT * FROM eggs WHERE listid = '.$l3.' and `left` > 0 ORDER BY RAND() LIMIT 1 FOR UPDATE') or die(mysql_error());

if (!mysql_num_rows($q)) {
	mysql_query('UPDATE eggs SET `left` = allcount WHERE listid = '.$l3);	
	$q = mysql_query('SELECT * FROM eggs WHERE listid = '.$l3.' and `left` > 0 ORDER BY RAND() LIMIT 1 FOR UPDATE') or die();
	if (!mysql_num_rows($q)) {
		die();
	}
}

$i = mysql_fetch_assoc($q);
$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm,$i['ekrprice'],$i['count']);
$txt .= ", уникальный значок, ";

mysql_query('UPDATE eggs SET `left` = `left` - 1 WHERE id = '.$i['id']) or die();

mysql_query('COMMIT') or die();

mk_unik_medal($user,'ekr');

echo 'Вы открыли '.$rowm['name'].', и получили: <br>';
echo substr($txt,0,-2);
$bet = 1;
$sbet = 1;
?>