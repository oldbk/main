<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


function mk_my_item($telo,$proto,$shoptype,$rowm,$ekrcost = 0) {
	if ($shoptype == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($shoptype == 2) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.cshop where id='{$proto}' ;"));
	}

	if ($dress === false) die();

	$dress['dategoden'] = time()+(90*24*3600);
	$godentime = $dress['dategoden'];
	$dress['goden'] = 90;
	$dress['present'] = 'Удача';
	$ekr_flag = 0;
	$dress['sowner'] = 0;
	$dress['unik'] = 0;
	$getfrom = 145;
	$good = 0;

	if ($dress['id'] == 538 || $dress['id'] == 539) {
		$dress['goden'] = 30;
		$dress['dategoden'] = time()+(30*24*3600);
		$godentime = $dress['dategoden'];
	}


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
		$rec['target_login']='Мешок с подарками';
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


$l1 = 64;

mysql_query('START TRANSACTION') or die();
// выгребаем обязательные подарки
$q = mysql_query('SELECT * FROM kotvmeshke WHERE listid = '.$l1.' ORDER BY RAND() LIMIT 1') or die();
$txt = "";
$i = mysql_fetch_assoc($q);

if ($rowm['duration']+1 == $rowm['maxdur']) {


		$goden_do = time()+(7*24*60*60);
		$goden = round(($goden_do-time())/60/60/24); if ($goden<1) {$goden=1;}

	mysql_query('UPDATE inventory SET maxdur = 1, duration = 0, magic = 0, type = 77, dategoden='.$goden_do.' , goden='.$goden.'  , prototype = 2018511 WHERE id ='.$rowm['id'].' LIMIT 1') or die();
} else {
	$bet = 1;
	$sbet = 1;

	if ($rowm['ekr_flag']>0)
		{


		mysql_query('UPDATE inventory SET ekr_flag = 0, ecost=0, present="Торговец Галиас" WHERE id ='.$rowm['id'].' LIMIT 1') or die();
		}

}

mysql_query('COMMIT') or die();
echo 'Вы достали «'.mk_my_item($user,$i['prototype'],$i['shop_id'],$rowm).'»!<br>';
?>
