<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

function mk_my_item_gift($telo,$proto,$larinfo,$present=false,$ekr_flag=0,$kol=1,$rwproto) {

	if ($proto == 20180129) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	}

	if ($present == true) {
		$dress['present'] = 'Администрации ОлдБК';
	}

	if ($dress['id']>0) {
		if (($dress['id'] != 105103) and ($dress['id'] != 20180129)) {
			$goden_do = mktime(23,59,59,2,14,2018); 
			$goden = round(($goden_do-time())/60/60/24); if ($goden<1) {$goden=1;}
			$dress['goden']=$goden; 
			$godentime = $goden_do;
		
			if (time()>$goden_do) {
				return false;
			}
			
		} elseif ($dress['goden'] > 0) {

			$godentime = time()+($dress['goden']*3600*24);
					
			if (time()>$godentime) {
				return false;
			}
			
		} else  {
			$godentime = 0;
		}
	
		if ($ekr_flag>0) { $dress['ekr_flag']=$ekr_flag;}
	
		$dress['notsell']=0;
	
		$aitms=array();
	
		for($i=1;$i<=$kol;$i++) {
			if(mysql_query("INSERT INTO oldbk.`inventory`
			(`add_time`,`ekr_flag`,`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`notsell`,`letter`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`img_big`,`rareitem`,`sowner`
			)
			VALUES
				('".time()."','{$dress['ekr_flag']}','38','{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['notsell']}','{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
				'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
				'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}' , '{$dress['img_big']}', '{$dress['rareitem']}','".($dress['is_owner']==1?$telo[id]:0)."'
				) ;"))
			{
				$good++;
			
				$pdress=array();
				$pdress['idcity']=$telo[id_city];
				$pdress['id']=mysql_insert_id();
				$aitms[]=get_item_fid($pdress);
	        	} else 
	        	{
				$good = 0;
			}		
		}	

		if ($good>0) {
			$rec['owner']=$telo[id];
			$rec['owner_login']=$telo[login];
			$rec['target']=0;
			$rec['target_login']='Упаковка';
			$rec['owner_balans_do']=$telo[money];
			$rec['owner_balans_posle']=$telo[money];
			$rec['type']=419;//   получил из ларца
			$rec['sum_kr']=0;
			$rec['sum_ekr']=$dress['ecost'];
			$rec['sum_kom']=0;
			$rec['aitem_id']=implode(",",$aitms);
			$rec['item_name']=$dress['name'];
			$rec['item_count']=$good;
			$rec['item_type']=$dress['type'];
			$rec['item_cost']=$dress['cost'];
			$rec['item_dur']=$dress['duration'];
			$rec['item_maxdur']=$dress['maxdur'];
			$rec['item_ups']=0;
			$rec['item_unic']=0;
			$rec['item_incmagic']=$dress['includemagic'];
			$rec['item_incmagic_count']=$dress['includemagicdex'];
			$rec['item_arsenal']='';
			$rec['add_info']=$larinfo;
			add_to_new_delo($rec);
			return link_for_item($dress)." ".$good." шт., ";
		} else {
			return false;
		}
	} else {
		return false;
	}
}



if ($rowm['prototype'] ==600665) {
	$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";


	$array_box=array(
		20180129=>1, 
		119119120=>8,
		120120121=>8,
		200440=>8, 
		200277=>8,
		315=>8,
		3001003=>1,
		105103=>8,
		106103=>8,
	);

	$addmag[1]=150152;  //Малый свиток «Гнев Ареса», если совпадает с основной стихией перса
	$addmag[2]=920925; //Малый свиток «Обман Химеры», если совпадает с основной стихией перса
	$addmag[3]=130135; //Малый свиток «Вой Грифона», если совпадает с основной стихией перса
	$addmag[4]=930935;  //Малый свиток «Укус Гидры», если совпадает с основной стихией перса

	$need_astih=get_mag_stih($user); // получаем ид стихии
	$need_astih=$need_astih[0]; //на 0м месте родная стихия
	$need_astih=$addmag[$need_astih]; //нужный свиток


	$array_box[$need_astih] = 8;

	$out_text='';
	
	foreach ($array_box as $proto=>$kol) {
		$lar_inf = '"'.$rowm[name].'" ('.get_item_fid($rowm).')';
		$present = true;
								

								
		$out_text .= mk_my_item_gift($user,$proto,$lar_inf,$present,$ekr_flag,$kol,$rowm['prototype']);
								
	}
				
	$bet=1;
	$sbet=1;
	$MAGIC_OK=1;
	$out_text=substr($out_text,0,-2);

	echo $echo_out."<br>".$out_text;
	addchp ('<font color=red>Внимание!</font> '.$echo_out." ".$out_text,'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
}
	

?>