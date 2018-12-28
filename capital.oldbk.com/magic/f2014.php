<?php

if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}

// 0 shop
// 1 eshop

$l1 = array(
	102 => 0,
	103 => 0,
	129 => 0,
	157 => 1,
	125125 => 0, // 0/2 fix
	250 => 0,
	251 => 0,
	252 => 0,
	253 => 0,
	353 => 0,
	354 => 0,
);
$l2 = array(
	2525 => 1,
	121121 => 0,
	40000000 => 1,
	150155 => 1,
	101111 => 1,
	301 => 1,
	119119 => 0,
	120120 => 0,
	200272 => 1, // 0/3 fix
        200273 => 1, // 0/3 fix
	//5205 => 1, // 0/3 fix
	200269 => 1, // 0/3 fix - 180  хилка не встраиваемая
);

$l3 = array(571 => 0);

$ilist = array();

$per = 0;

if(time() >= mktime(0,0,0,6,16,2014) && time() <= mktime(23,59,59,6,27,2014)) {
	$per = 1;
} elseif (time() >= mktime(0,0,0,6,28,2014) && time() <= mktime(23,59,59,7,12,2014)) {
	$per = 2;
} else if (time() >= mktime(0,0,0,7,13,2014) && time() <= mktime(23,59,59,7,14,2014)) {
	$per = 3;
}


// $per = 3;

if($per == 1) {
	$t = array_rand($l1,1);
	$ilist[$t] = $l1[$t];
} elseif ($per == 2) {
	$t = array_rand($l2,1);
	$ilist[$t] = $l2[$t];
} elseif ($per == 3) {
	$t = array_rand($l1,1);
	$ilist[$t] = $l1[$t];

	$t = array_rand($l2,1);
	$ilist[$t] = $l2[$t];

	$t = array_rand($l3,1);
	$ilist[$t] = $l3[$t];

}

function mk_my_item($telo, $proto,$shop,$finfo) {
	if ($shop == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}

	if ($proto == 125125) $dress['maxdur'] = 2;
	if ($proto == 571) $dress['present'] = "Администрация ОлдБК";
	if ($proto == 200272 || $proto == 200273 || $proto == 5205) $dress['maxdur'] = 3;

	if ($dress[id]>0) {
	
		if(mysql_query("INSERT INTO oldbk.`inventory`
			(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
			)
			VALUES
				('{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
				'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
				'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}'
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
				$rec['owner']=$telo[id];
				$rec['owner_login']=$telo[login];
				$rec['target']=0;
				$rec['target_login']='Упаковка';
				$rec['owner_balans_do']=$telo[money];
				$rec['owner_balans_posle']=$telo[money];
				$rec['type']=421;//   получил футбольного мяча
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
				$rec['item_ups']=0;
				$rec['item_unic']=0;
				$rec['item_incmagic']=$dress['includemagic'];
				$rec['item_incmagic_count']=$dress['includemagicdex'];
				$rec['item_arsenal']='';
				$rec['add_info']=$finfo;
				add_to_new_delo($rec);
				echo $dress['name']." [0/".$dress['maxdur']."]"."<br>";
				return $dress['name'];
			} else {
				return false;
			}
	} else {
		return false;
	}
}


if (count($ilist)) {
	$q = mysql_query('SELECT * FROM f_use WHERE owner = '.$user['id'].' and `date` = "'.date("d/m/Y").'"') or die(mysql_error());
	if (mysql_num_rows($q) == 0) {
		mysql_query('INSERT INTO f_use (`owner`,`date`) VALUES ("'.$user['id'].'","'.date("d/m/Y").'")') or die(mysql_error());

		$finf = '"'.$rowm[name].'" ('.get_item_fid($rowm).')';
		while(list($k,$v) = each($ilist)) {
			// раздаём предметы
			mk_my_item($user,$k,$v,$finf);
		}
	} else {
		echo "Сегодня уже использовали...";
	}
} else {
	echo "Футбольный чемпионат уже окончен...";
}
?>