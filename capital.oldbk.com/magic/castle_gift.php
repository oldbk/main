<?php

if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}


function mk_my_item($telo, $proto,$finfo) {
	$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	$dress['present'] = "Удача"; // все подарком
	$dress['notsell']=1;	
	
	if ($dress['goden']==0) {
		$dress['goden']=90;
	}

	if ($dress[id]>0) 
		{
	
		if(mysql_query("INSERT INTO oldbk.`inventory`
			(`ekr_flag`,`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`, `img_big` ,`maxdur`,`isrep`,`letter`,`notsell`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
			)
			VALUES
				(0,0,'{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}', '{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['notsell']}',
				'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
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
				$rec['type']=421;//  
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
				echo "Вы получили: «".link_for_item($dress)."»!<br>";
				return $dress['name'];
			} else {
				return false;
			}
	} else {
		return false;
	}
}


if ($rowm['prototype'] == 3004100) {
	mk_my_item($user,571,$rowm['name']);
	mk_my_item($user,105104 ,$rowm['name']);
} elseif ($rowm['prototype'] == 3004101) {
	mk_my_item($user,572,$rowm['name']);
	mk_my_item($user,105102,$rowm['name']);
} elseif ($rowm['prototype'] == 3004102) {
	mk_my_item($user,580,$rowm['name']);
	mk_my_item($user,105103,$rowm['name']);
} elseif ($rowm['prototype'] == 3004103) {
	mk_my_item($user,584,$rowm['name']);
	mk_my_item($user,105103,$rowm['name']);
	mk_my_item($user,105103,$rowm['name']);
	mk_my_item($user,105103,$rowm['name']);
	mk_my_item($user,105103,$rowm['name']);
	mk_my_item($user,105103,$rowm['name']);
} else {
	die();
}


$bet=1;
$sbet = 1;
$MAGIC_OK=1;
