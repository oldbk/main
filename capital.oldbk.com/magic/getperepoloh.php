<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bank_functions.php"); //нужна функа для создания билетов

function mk_my_item($telo,$proto,$addinfo,$name) {
	$count = 1;


	if ($addinfo['shop'] == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($addinfo['shop'] == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}

	if (isset($addinfo['goden']))   $dress['goden'] = $addinfo['goden'];
	$dress['present'] = 'Удача';
	if (isset($addinfo['count']))   $count = $addinfo['count'];
	if (isset($addinfo['notsell'])) $dress['notsell'] = $addinfo['notsell'];


	if ($dress['id'] > 0) {
		for ($i = 0; $i < $count; $i++) {
			$dress['id'] = $proto;

			if(mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
				`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`notsell`,`img_big`
				)
				VALUES
					('{$dress['id']}','{$telo['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
					'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
					'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}', '{$dress['notsell']}', '{$dress['img_big']}'
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
				$rec['target_login']=$name;
				$rec['owner_balans_do']=$telo['money'];
				$rec['owner_balans_posle']=$telo['money'];
				$rec['type']=300;
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
				$rec['item_unic']=1;
				$rec['item_incmagic']=$dress['includemagic'];
				$rec['item_incmagic_count']=$dress['includemagicdex'];
				$rec['item_arsenal']='';
				add_to_new_delo($rec);		
			}
		}
		$addtxt = "";
		if ($count > 1) $addtxt = ' (x'.$count.')';
		return $dress['name'].$addtxt.", ";
	} else {
		die("NO PROTO!");
		return false;
	}
}

if ($rowm['prototype'] == 590) {
	$count = 1;
	$ekr = 0;
	$ilist = array(
		104085 => array('shop' => 0, 'goden' => 0),
		104088 => array('shop' => 0, 'goden' => 0),
		104077 => array('shop' => 0, 'goden' => 0),
		104081 => array('shop' => 0, 'goden' => 0),
	);
} elseif ($rowm['prototype'] == 591) {
	$count = 1;
	$ekr = 0;
	$ilist = array(
		104020 => array('shop' => 0, 'goden' => 0),
		104019 => array('shop' => 0, 'goden' => 0),
		104018 => array('shop' => 0, 'goden' => 0),
		104078 => array('shop' => 0, 'goden' => 0),
		104017 => array('shop' => 0, 'goden' => 0),
		103002 => array('shop' => 0, 'goden' => 0),
		104016 => array('shop' => 0, 'goden' => 0),
		104075 => array('shop' => 0, 'goden' => 0),
	);
} elseif ($rowm['prototype'] == 592) {
	$count = 1;
	$ekr = 0;
	$ilist = array(
		105 => 		array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		246 => 		array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		249 => 		array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		200271 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		119 => 		array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		120 => 		array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		4005 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),

	);
} elseif ($rowm['prototype'] == 593) {
	$count = 2;
	$ekr = 1;
	$ilist = array(
		105103 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		24646 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		249249 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		271271 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		119119 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		120120 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		4006 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),

	);
} elseif ($rowm['prototype'] == 594) {
	$count = 3;
	$ekr = 2;
	$ilist = array(
		5202 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		5205 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		200273 => 	array('shop' => 0, 'goden' => 30, 'count' => 3, 'notsell' => 1),
		33052 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),
		33053 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),
		33054 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),
		33055 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),
		4007 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),
		4016 => 	array('shop' => 0, 'goden' => 30, 'count' => 1, 'notsell' => 1),
	);
} else {
	die();
}


function ashuffle (&$arr) {
     uasort($arr, function ($a, $b) {
         return rand(0, 1);
     });
}

for ($i = 0; $i < 10; $i++) {
	ashuffle($ilist);
}

$ret = "";
for ($i = 0; $i < $count; $i++) {
	list($key,$addinfo) = each($ilist);

	$ret .= mk_my_item($user,$key,$addinfo,$rowm['name']);
}

if ($ekr > 0) {
	$bankid = mysql_fetch_array(mysql_query("select * from oldbk.bank where owner=".$user['id']." order by def desc,id limit 1"));	
	if ($bankid) {
		$ret .= $ekr.' екр, ';
		mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr` + '".$ekr."' WHERE id = {$bankid['id']}");
		mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','За вскрытие <b>".$rowm['name']."</b> вы получили <b>".$ekr." екр.</b>, <i>(Итого: {$bankid['cr']} кр., ".($bankid['ekr']+$ekr)." екр.)</i>','{$bankid['id']}');");

		mysql_query('INSERT INTO `prival_stats` (`owner`,`type`,`value`) 
				VALUES(
						'.$user['id'].',
						"0",
						'.$ekr.'
					) 
					ON DUPLICATE KEY UPDATE
						`value` = `value` + '.$ekr.'
		');

	}

}

if (strlen($ret)) {
	echo "Вы вскрыли <b>".$rowm['name']."</b> и получили: ".substr($ret,0,-2);
	$bet = 1;
	$sbet = 1;

} else {
	echo "Ошибка";
}

?>