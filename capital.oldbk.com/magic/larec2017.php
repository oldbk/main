<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


if (date("m") != 1 && !ADMIN) {
	echo "Еще не время..."; 
	return;
}

function mk_my_item($telo,$proto,$shoptype,$rowm,$ekrcost = 0) {
	if ($shoptype == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($shoptype == 2) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.cshop where id='{$proto}' ;"));
	}

	if ($dress === false) die();

	$dress['dategoden'] = mktime(23,59,59,2,28,2018);
	$godentime = $dress['dategoden'];
	$dress['goden'] = floor(($dress['dategoden']-time())/(3600*24));
	$dress['present'] = 'Удача';

	$ekr_flag = 0;
	$dress['sowner'] = 0;
	$dress['unik'] = 0;

	if (in_array($proto,array(9090,190190,190191,190192))) {
		$dress['sowner'] = $telo['id'];
		$dress['present'] = 'Удача';
	}


	if ($rowm['prototype'] == 2014004) {
		if (!$dress['ecost'] && $ekrcost > 0) $dress['ecost'] = $ekrcost;

		if (in_array($proto,array(9090,190190,190191,190192))) {
			$dress['sowner'] = $telo['id'];
			$dress['present'] = 'Удача';
		} else {
			$ekr_flag = 1;
			$dress['present'] = "";
		}
	}

	$getfrom = 0;

	if ($rowm['prototype'] == 2014000) {
		$getfrom = 120;
	} elseif ($rowm['prototype'] == 2014001) {
		$getfrom = 121;
	} elseif ($rowm['prototype'] == 2014004) {
		$getfrom = 122;
	}


	if (in_array($proto,array(200001,200002,200005,200010,200025,200050,200100,200250,200500))) {
		$dress['present'] = 'Удача';
		$dress['unik'] = 2;
	}

	$good = 0;


	if(mysql_query("INSERT INTO oldbk.`inventory`
		(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`notsell`,`ekr_flag`,`sowner`,`img_big`,`unik`,`getfrom`
		)
		VALUES
			('{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}',1, '{$ekr_flag}','{$dress['sowner']}','{$dress['img_big']}','{$dress['unik']}',{$getfrom}
			) ;"))
	{
		$good = 1;
		$insert_item_id=mysql_insert_id();
		$dress['idcity']=$telo[id_city];
		$dress['id']=$insert_item_id;
        } else {
		$good = 0;
	}		
		

	if ($good) {
		$rec['owner']=$telo['id'];
		$rec['owner_login']=$telo['login'];
		$rec['target']=0;
		$rec['target_login']='Ларец';
		$rec['owner_balans_do']=$telo['money'];
		$rec['owner_balans_posle']=$telo['money'];
		$rec['type']=419;//   получил из ларца
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
		$rec['item_ups']=0;
		$rec['item_unic']=0;
		$rec['item_incmagic']=$dress['includemagic'];
		$rec['item_incmagic_count']=$dress['includemagicdex'];
		$rec['item_arsenal']='';
		$rec['add_info']="cap".$rowm['id'].' '.$rowm['name'];
		add_to_new_delo($rec) or die();

		return $dress['name'];
	} else {
		die();
	}
}


$l1 = 0;
$l2 = 0;
$maxekr = 0;

if ($rowm['prototype'] == 2014000) {
	$l1 = 37;
	$l2 = 34;
	$maxekr = 5;
} elseif ($rowm['prototype'] == 2014001) {
	$l1 = 35;
	$l2 = 32;
	$maxekr = 20;
} elseif ($rowm['prototype'] == 2014004) {
	$l1 = 36;
	$l2 = 33;
	$maxekr = 100;
}


mysql_query('START TRANSACTION') or die();
// выгребаем обязательные подарки
$q = mysql_query('SELECT * FROM larci WHERE listid = '.$l1) or die();
$txt = "";

while($i = mysql_fetch_assoc($q)) {
	$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm);
	$txt .= ", ";
}

$icount = 0;
$iprice = 0;

// выгребаем рандомные подарки
$noids = array();
$q = mysql_query('SELECT * FROM larci WHERE listid = '.$l2.' and `left` > 0 ORDER BY RAND() FOR UPDATE') or die();
while($i = mysql_fetch_assoc($q)) {
	$noids[] = $i['id'];
	$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm,$i['ekrprice']);
	$txt .= ", ";
	mysql_query('UPDATE larci SET `left` = `left` - 1 WHERE id = '.$i['id']) or die();

	$icount++;
	$iprice += $i['ekrprice'];

	if ($icount == 3) break;
	if ($iprice > $maxekr) break;
}


if ($icount != 3) {
	// выгребаем рандомно с наименьшим прайсом
	$q = mysql_query('SELECT * FROM larci WHERE listid = '.$l2.' and `left` > 0 AND id NOT IN ('.implode(",",$noids).') ORDER BY ekrprice ASC FOR UPDATE') or die();

	while($i = mysql_fetch_assoc($q)) {
		$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm,$i['ekrprice']);
		$txt .= ", ";
		mysql_query('UPDATE larci SET `left` = `left` - 1 WHERE id = '.$i['id']) or die();
	
		$icount++;
	
		if ($icount == 3) break;
	}
}

if ($icount != 3) {
	// выгребаем уже совсем рандом
	$q = mysql_query('SELECT * FROM larci WHERE listid = '.$l2.' and `left` > 0 ORDER BY ekrprice ASC FOR UPDATE') or die();

	while($i = mysql_fetch_assoc($q)) {
		$txt .= mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm,$i['ekrprice']);
		$txt .= ", ";
		mysql_query('UPDATE larci SET `left` = `left` - 1 WHERE id = '.$i['id']) or die();
	
		$icount++;
	
		if ($icount == 3) break;
	}
}


mysql_query('COMMIT') or die();


echo 'Вы открыли '.$rowm['name'].', и получили: <br>';
echo substr($txt,0,-2);
$bet = 1;
$sbet = 1;
?>