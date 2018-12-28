<?php

function mk_my_item($telo,$proto,$addinfo) {
	if ($proto == 'zodiak') {
		$t = get_mag_stih($telo);
		// $t[0] - от 1 до 4, 1 - огонь, 2 - земля, 3 - воздух, 4 - вода
		if ($t[0] == 1) $proto = 150158;
		if ($t[0] == 2) $proto = 920928;
		if ($t[0] == 3) $proto = 130138;
		if ($t[0] == 4) $proto = 930938;
	}


	if ($addinfo['shop'] == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($addinfo['shop'] == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}

	$dress['present'] = "Лорд Разрушитель";
	$dress['goden'] = 90;

	if ($dress['id'] > 0) {
		$dress['id'] = $proto;
		if(mysql_query("INSERT INTO oldbk.`inventory`
			(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`, `img_big`,`maxdur`,`isrep`,`letter`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`getfrom`,`rareitem`
			)
			VALUES
			('{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}', '{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}',6, '{$dress['rareitem']}'
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
			$rec['target_login']='Рунное могущество';
			$rec['owner_balans_do']=$telo[money];
			$rec['owner_balans_posle']=$telo[money];
			$rec['type']=393;//   получил по акции
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
			$rec['item_unic']=1;
			$rec['item_incmagic']=$dress['includemagic'];
			$rec['item_incmagic_count']=$dress['includemagicdex'];
			$rec['item_arsenal']='';
			add_to_new_delo($rec);		
		} else {
			return false;
		}
	}
	return $dress['name'].$addtxt;
}

echo 'Вы открыли сундук и получили: ';
echo mk_my_item($user,'zodiak',array('shop' => 0)).", ";
echo mk_my_item($user,4016,array('shop' => 0)).", ";
echo mk_my_item($user,3001001,array('shop' => 1));

$bet=1;
$sbet = 1;

?>