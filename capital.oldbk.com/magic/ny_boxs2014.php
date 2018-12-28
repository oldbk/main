<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


require_once($_SERVER["DOCUMENT_ROOT"]."/bank_functions.php"); //нужна функа для создания билетов


function mk_my_item($telo,$proto,$larinfo,$maxdex=0) {
	$origproto = $proto;

	if (in_array($proto,array(112001,15551,100500,573,580,54999,55999,125125,134134,4163,4165,538,539,540,541,33052,33053,33054,33055))) {
		if ($proto == 112001) $proto = mt_rand(112001,112007); // карты
		if ($proto == 15551) $proto = mt_rand(15551,15558); // статуи из лабы
		if ($proto == 100500) {
			$t = get_mag_stih($telo);
			// $t[0] - от 1 до 4, 1 - огонь, 2 - земля, 3 - воздух, 4 - вода
			if ($t[0] == 1) $proto = 150152;
			if ($t[0] == 2) $proto = 920925;
			if ($t[0] == 3) $proto = 130135;
			if ($t[0] == 4) $proto = 930935;
		}

		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));

	}

	//if (!$dress) var_dump($proto);

	$dress['goden'] = 30;
	$dress['present'] = 'Администрация';


	if (in_array($proto,array(56999,55999,190190,190191,190192,531,532,533,534,535,536,537,540,541,1000004,1000005,1000006,100040000)) || in_array($origproto,array(112001))) {
		$dress['goden'] = 0;
	}



	if ($dress[id]>0) {
	
	if ($maxdex > 0) { $dress['maxdur']=$maxdex;}

	if ($dress['goden'] > 0) {
		$godentime = time()+($dress['goden']*3600*24);
	} else {
		$godentime = 0;
	}

	if (in_array($proto,array(3003131,3003132,3003133,3003134,3003135,538,539))) {
		$ny_events_cur_m = date("m");
		$ny_events_cur_y = date("Y");

		$godentime = $ny_events_cur_m == 12 ? mktime(23,59,59,1,31,$ny_events_cur_y+1) : mktime(23,59,59,1,31,$ny_events_cur_y);
		$dress['goden'] = ceil(($godentime - time()) / (3600*24));
	}

	
		
	if(mysql_query("INSERT INTO oldbk.`inventory`
		(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
		)
		VALUES
			('{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
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
			$rec['type']=419;//   получил из ларца
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
			$rec['add_info']=$larinfo;
			add_to_new_delo($rec);
			echo $dress['name']."[0/".$dress['maxdur']."]".", ";
			return $dress['name'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function mk_pers_abil($telo,$magic,$count,$larinfo) {
	$magic_info=mysql_fetch_array(mysql_query("select * from oldbk.magic where id='{$magic}' ;"));

	mysql_query('INSERT INTO oldbk.users_abils (owner,magic_id, allcount, findata)
		VALUES(
			"'.$telo['id'].'",
			"'.$magic.'",
			"'.$count.'",
			"0"
			) ON DUPLICATE KEY UPDATE
			`allcount` = `allcount` + '.$count
		);

	$rec['owner']=$telo[id];
	$rec['owner_login']=$telo[login];
	$rec['target']=0;
	$rec['target_login']='Упаковка';
	$rec['owner_balans_do']=$telo[money];
	$rec['owner_balans_posle']=$telo[money];
	$rec['type']=420;//   получил из ларца -абилку
	$rec['sum_kr']=0;
	$rec['sum_ekr']=0;
	$rec['sum_kom']=0;
	$rec['item_id']=$magic_info[id];
	$rec['item_name']=$magic_info['name'];
	$rec['item_count']=$count;
	$rec['item_type']=0;
	$rec['item_cost']=0;
	$rec['item_dur']=0;
	$rec['item_maxdur']=0;
	$rec['item_ups']=0;
	$rec['item_unic']=0;
	$rec['item_incmagic']=0;
	$rec['item_incmagic_count']=0;
	$rec['item_arsenal']='';
	$rec['add_info']=$larinfo;
	add_to_new_delo($rec);
	return "Абилити ".$magic_info['name']."  ".$count." шт." ;				
}

if (date("m") != 1) {
	echo "Еще не время..."; 
} elseif (($rowm[prototype] ==2014001) OR ($rowm[prototype] ==2014004)) {

	$boxs_type[2014001]=1;   
	$boxs_type[2014004]=4;
  
	// Установки юзов для предметов
	// 1 - бомж
        // 4 - Золотой

	// ключ от лабы 
	$dex_arr[1][4001]=5;
	$dex_arr[4][4001]=10;

	// переманить клона
	$dex_arr[1][120121]=3;
	$dex_arr[4][120121]=4;

	// пропуск в руины
	$dex_arr[1][4015]=5;
	$dex_arr[4][4015]=10;

	// невидимость
	$dex_arr[1][11301]=5;
	$dex_arr[4][11301]=10;

	// встраивание магии
	$dex_arr[1][19595]=2;
	$dex_arr[4][19595]=4;

	// клонирование
	$dex_arr[1][119200]=4;
	$dex_arr[4][119200]=5;

	// призыв III
	$dex_arr[1][14033]=3;
	$dex_arr[4][14033]=4;

	// захват III
	$dex_arr[1][15000]=2;
	$dex_arr[4][15000]=4;

	// Восстановление энергии 360HP
	$dex_arr[1][200276]=3;
	$dex_arr[4][200276]=4;
	
	$dex_arr[1][200275]=3;
	$dex_arr[4][200275]=4;

	// выход из боя
	$dex_arr[1][6206]=4;
	$dex_arr[4][6206]=10;

	// лечение травм
	$dex_arr[1][125125]=4;
	$dex_arr[4][125125]=10;

	// кровавое
	$dex_arr[1][134134]=4;
	$dex_arr[4][134134]=10;

	// вендетта
	$dex_arr[1][22525]=4;
	$dex_arr[4][22525]=10;

	// заступ
	$dex_arr[1][353353]=4;
	$dex_arr[4][353353]=10;

	////////////////////////абилки-кол.юзов


	// Снятие молчания
	$dex_arr[1][4847]=4;
	$dex_arr[4][4847]=10;

	// бесплатный сброс статов
	$dex_arr[1][4848]=1;
	$dex_arr[4][4848]=1;

	
	mysql_query("UPDATE oldbk.boxs set is_open=1 where item_id='{$rowm[id]}' and is_open=0 and  box_type='{$boxs_type[$rowm['prototype']]}'");
	if (mysql_affected_rows()>0) {
		 $get_info_boxs=mysql_fetch_array(mysql_query("SELECT * from oldbk.boxs where item_id='{$rowm[id]}'  ;"));
		 if ($get_info_boxs[id]>0) {
			echo "Вы открыли {$rowm[name]}, и получили:<br>";
			
		 	
			$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';

			// лото
			$dill[id_city]=$user[id_city];
			$dill[id]=450;
				
			if ($boxs_type[$rowm['prototype']] == 4) {
				get_bonus_bill_loto($user,2,$dill);
				echo "Лотерейный билет ОлдБк - 2шт, ";

				//предметы
				for ($cc=2;$cc<=7;$cc++) {
					$idx='i'.$cc;
					if ($get_info_boxs[$idx]>0) {
					        if (substr($get_info_boxs[$idx],0,5) == "99999") {
							$get_info_boxs[$idx] = substr($get_info_boxs[$idx],5);
							//абилки
							$count=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
							$echo_text.= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
						} else {
							$mdex=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]];//получаем макс.долговечность для предмета по его ид и типу коробки
							$item_name=mk_my_item($user,$get_info_boxs[$idx],$lar_inf,$mdex);
						}
					}					
				}
				
	
				//личные абилки
				$cc = 8;
				$idx='i'.$cc;
				if ($get_info_boxs[$idx]>0) {
					//абилки
					$count = $dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
					$echo_text .= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
				}
			} elseif ($boxs_type[$rowm['prototype']] == 1) {
				get_bonus_bill_loto($user,1,$dill);
				echo "Лотерейный билет ОлдБк - 1шт, ";


				for ($cc=2;$cc<=4;$cc++) {
					$idx='i'.$cc;
					if ($get_info_boxs[$idx]>0) {
					        if (substr($get_info_boxs[$idx],0,5) == "99999") {
							$get_info_boxs[$idx] = substr($get_info_boxs[$idx],5);
							//абилки
							$count=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
							$echo_text.= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
						} else {
							if ($get_info_boxs[$idx] == 15551) {
								$get_info_boxs[$idx] = mt_rand(15551,15558); // рандомим статуи
								$mdex=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]];//получаем макс.долговечность для предмета по его ид и типу коробки
								$item_name=mk_my_item($user,$get_info_boxs[$idx],$lar_inf,$mdex);
							} else {
								$mdex=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]];//получаем макс.долговечность для предмета по его ид и типу коробки
								$item_name=mk_my_item($user,$get_info_boxs[$idx],$lar_inf,$mdex);
							}
						}
					}
				}
				
	
				//личные абилки
				$cc = 5;
				$idx='i'.$cc;
				if ($get_info_boxs[$idx]>0) {
					//абилки
					$count=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
					$echo_text.= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
				}
			}

			$echo_text=substr($echo_text,0,-2);
			echo $echo_text;

			$bet=1;
			$sbet=1;
			$MAGIC_OK=1;

			if (!empty($rowm['present'])) {
	            		$sql = '
					insert into oldbk.inventory
			         	(name,maxdur,cost,owner,img,type,magic,letter,massa,isrep,prototype,otdel,add_time,present_text,present, goden, dategoden)
			    	 	VALUES
					(
						"'.mysql_real_escape_string($rowm['name']).'",
						"'.mysql_real_escape_string($rowm['maxdur']).'",
						"0",
						"'.mysql_real_escape_string($rowm['owner']).'",
						"'.mysql_real_escape_string($rowm['img']).'",
						"200",
						"0",
						"'.mysql_real_escape_string($rowm['letter']).'",
						"'.mysql_real_escape_string($rowm['massa']).'",
						"'.mysql_real_escape_string($rowm['isrep']).'",
						"'.mysql_real_escape_string($rowm['prototype']).'",
						"'.mysql_real_escape_string($rowm['otdel']).'",
						"'.mysql_real_escape_string($rowm['add_time']).'",
						"'.mysql_real_escape_string($rowm['present_text']).'",
						"'.mysql_real_escape_string($rowm['present']).'",
						"60","'.(time()+(3600*24*60)).'"
					)
				';
				mysql_query($sql) or die(mysql_error());

			}
		}
	}
}

?>