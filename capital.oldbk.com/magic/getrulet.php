<?php

function mk_my_item($telo,$proto,$addinfo) {
	$count = 1;
	if ($proto == 555 && $telo['sex'] == 1) $proto = 556;
	if ($proto == 556 && $telo['sex'] == 0) $proto = 555;


	if ($addinfo['shop'] == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($addinfo['shop'] == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}

	if (isset($addinfo['maxdur']))  $dress['maxdur'] = $addinfo['maxdur'];
	if (isset($addinfo['goden']))   $dress['goden'] = $addinfo['goden'];
	if (isset($addinfo['present'])) $dress['present'] = '”дача';
	if (isset($addinfo['count']))   $count = $addinfo['count'];

	if ($dress['otdel'] == 76) $dress['otdel'] = 73;
	if ($dress['razdel'] == 76) $dress['razdel'] = 73;


	if ($dress['id'] > 0) {
		for ($i = 0; $i < $count; $i++) {
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
				$rec['target_login']='¬олшебна€ шл€па';
				$rec['owner_balans_do']=$telo[money];
				$rec['owner_balans_posle']=$telo[money];
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
		return $dress['name'].$addtxt;
	} else {
		return false;
	}
}


$ilist = array(
	15561 => array('shop' => 0, 'present' => 1), // осколки статуй
	15562 => array('shop' => 0, 'present' => 1),
	15563 => array('shop' => 0, 'present' => 1),
	15564 => array('shop' => 0, 'present' => 1),
	15565 => array('shop' => 0, 'present' => 1),
	15566 => array('shop' => 0, 'present' => 1),
	15567 => array('shop' => 0, 'present' => 1),
	15568 => array('shop' => 0, 'present' => 1),
	5205  => array('shop' => 1, 'goden' => 7, 'present' => 1), // hp
	200273=> array('shop' => 1, 'goden' => 7, 'present' => 1), // hp
	551   => array('shop' => 0, 'goden' => 1, 'present' => 1), // карнавальные образы
	552   => array('shop' => 0, 'goden' => 1, 'present' => 1), // карнавальные образы
	553   => array('shop' => 0, 'goden' => 1, 'present' => 1), // карнавальные образы
	554   => array('shop' => 0, 'goden' => 1, 'present' => 1), // карнавальные образы
	555   => array('shop' => 0, 'goden' => 1, 'present' => 1), // карнавальные образы
	556   => array('shop' => 0, 'goden' => 1, 'present' => 1), // карнавальные образы
	113010=> array('shop' => 0), // футл€р
	317   => array('shop' => 0, 'goden' => 7, 'present' => 1), // свиток маны
	318   => array('shop' => 0, 'goden' => 7, 'present' => 1), // свиток маны
        // новые добавили
        19102 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        19020 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        19103 => array('shop' => 0, 'goden' => 7, 'present' => 1),
      3004052 => array('shop' => 0, 'goden' => 7, 'present' => 1),
      3004051 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        20104 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        20105 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        20106 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        20107 => array('shop' => 0, 'goden' => 7, 'present' => 1),
        314   => array('shop' => 0, 'goden' => 7, 'present' => 1),
        249249=> array('shop' => 0, 'goden' => 7, 'present' => 1),
        271271=> array('shop' => 0, 'goden' => 7, 'present' => 1),

);


function ashuffle (&$arr) {
     uasort($arr, function ($a, $b) {
         return mt_rand(-1, 1);
     });
}

$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' and prototype = 113010');
if (mysql_num_rows($q) > 0) {
	unset($ilist[113010]);
}

ashuffle($ilist);

list($key,$addinfo) = each($ilist);

$ret = mk_my_item($user,$key,$addinfo);
if ($ret) {
	echo '¬ы открыли волшебную шл€пу и получили ';
	echo $ret;
	$bet=1;
	$sbet=1;
	$MAGIC_OK=1;
} else {
	echo "ќшибка";
}

?>