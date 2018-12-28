<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


require_once($_SERVER["DOCUMENT_ROOT"]."/bank_functions.php"); //нужна функа для создания билетов


function mk_my_item($telo,$proto,$larinfo,$maxdex=0) {
	if ($proto == 571 || $proto == 574) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
		$dress['present'] = "Администрация";
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}
	if ($dress[id]>0) {
	
	if ($maxdex > 0) { $dress['maxdur']=$maxdex;}
	
		
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


if (!(time() >= mktime(0,0,0,4,20) && time() <= mktime(23,59,59,4,27))) {
	echo "Еще не время..."; 
} elseif (/*($rowm[prototype] ==2014005) OR ($rowm[prototype] ==2014006) OR*/ ($rowm[prototype] ==2014007) OR ($rowm[prototype] ==2014008)) {

	//$boxs_type[2014005]=1;
	//$boxs_type[2014006]=2;    
	$boxs_type[2014007]=3;   
	$boxs_type[2014008]=4;
  
	// Установки юзов для предметов

	// Улучшение вещи 7  
	$dex_arr[4][16218]=10;
	$dex_arr[3][16218]=8;
	//$dex_arr[2][16218]=8;
	//$dex_arr[1][16218]=8;

	// Улучшение вещи 8  
	$dex_arr[4][16219]=10;
	$dex_arr[3][16219]=6;
	//$dex_arr[2][16219]=8;
	//$dex_arr[1][16219]=6;

	// Улучшение вещи 9  
	$dex_arr[4][16220]=7;
	$dex_arr[3][16220]=4;
	//$dex_arr[2][16220]=5;
	//$dex_arr[1][16220]=4;

	// Улучшение вещи 10  
	$dex_arr[4][16221]=6;
	$dex_arr[3][16221]=3;
	//$dex_arr[2][16221]=4;
	//$dex_arr[1][16221]=3;

	// Улучшение вещи 11
	$dex_arr[4][16321]=5;
	$dex_arr[3][16321]=2;
	//$dex_arr[2][16321]=3;
	//$dex_arr[1][16321]=2;

	// Улучшение вещи 12
	$dex_arr[4][16322]=4;
	$dex_arr[3][16322]=1;
	//$dex_arr[2][16322]=2;
	//$dex_arr[1][16322]=1;

	// встраивание магии
	$dex_arr[4][19595]=4;
	$dex_arr[3][19595]=2;
	//$dex_arr[2][19595]=2;
	//$dex_arr[1][19595]=4;

	// вендетта
	$dex_arr[4][2525]=10;
	$dex_arr[3][2525]=4;
	//$dex_arr[2][2525]=6;
	//$dex_arr[1][2525]=4;

	// невидимость
	$dex_arr[4][11301]=10;
	$dex_arr[3][11301]=4;
	//$dex_arr[2][11301]=6;
	//$dex_arr[1][11301]=4;

	// клонирование
	$dex_arr[4][119200]=5;
	$dex_arr[3][119200]=4;
	//$dex_arr[2][119200]=3;
	//$dex_arr[1][119200]=3;

	// призыв III
	$dex_arr[4][14033]=4;
	$dex_arr[3][14033]=3;
	//$dex_arr[2][14033]=3;
	//$dex_arr[1][14033]=3;

	// захват III
	$dex_arr[4][15000]=4;
	$dex_arr[3][15000]=2;
	//$dex_arr[2][15000]=3;
	//$dex_arr[1][15000]=2;

	// переманить клона
	$dex_arr[4][120121]=4;
	$dex_arr[3][120121]=3;
	//$dex_arr[2][120121]=3;
	//$dex_arr[1][120121]=2;

	// Восстановление энергии 360HP
	$dex_arr[4][200276]=4;
	$dex_arr[3][200276]=3;
	//$dex_arr[2][200276]=3;
	//$dex_arr[1][200276]=3;

	
	$dex_arr[4][200275]=4;
	$dex_arr[3][200275]=3;
	//$dex_arr[2][200275]=3;
	//$dex_arr[1][200275]=3;

	////////////////////////абилки-кол.юзов

	// Снятие молчания
	$dex_arr[4][4847]=10;
	$dex_arr[3][4847]=4;
	//$dex_arr[2][4847]=6;
	//$dex_arr[1][4847]=4;

	// выход из боя
	$dex_arr[4][49]=10;
	$dex_arr[3][49]=4;
	//$dex_arr[2][49]=6;
	//$dex_arr[1][49]=4;

	// Лечение травм
	$dex_arr[4][57]=10;
	$dex_arr[3][57]=4;
	//$dex_arr[2][57]=6;
	//$dex_arr[1][57]=4;

	// кровавое нападение 
	$dex_arr[4][56]=10;
	$dex_arr[3][56]=4;
	//$dex_arr[2][56]=6;
	//$dex_arr[1][56]=4;

	// заступиться
	$dex_arr[4][53]=10;
	$dex_arr[3][53]=4;
	//$dex_arr[2][53]=6;
	//$dex_arr[1][53]=4;

	// гнев ареса
	$dex_arr[4][5007152]=10;
	$dex_arr[3][5007152]=4;
	//$dex_arr[2][5007152]=6;
	//$dex_arr[1][5007152]=4;

	// защита от травм
	$dex_arr[4][4846]=1;
	$dex_arr[3][4846]=1;
	//$dex_arr[2][4846]=1;
	//$dex_arr[1][4846]=1;

	// бесплатный сброс статов
	$dex_arr[4][4848]=1;
	$dex_arr[3][4848]=1;
	//$dex_arr[2][4848]=1;
	//$dex_arr[1][4848]=1;

	
	mysql_query("UPDATE oldbk.boxsapril set  is_open=1 where item_id='{$rowm[id]}' and is_open=0 and  box_type='{$boxs_type[$rowm['prototype']]}'  ; ");
	if (mysql_affected_rows()>0) {
		 $get_info_boxs=mysql_fetch_array(mysql_query("SELECT * from oldbk.boxsapril where item_id='{$rowm[id]}'  ;"));
		 if ($get_info_boxs[id]>0) {
			echo "Вы открыли {$rowm[name]}, и получили:<br>";
			
		 	
			$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';

			//лото	- 2 шт.
			$dill[id_city]=$user[id_city];
			$dill[id]=450;
				
			if ($boxs_type[$rowm['prototype']] == 4) {
				get_bonus_bill_loto($user,5,$dill);
				echo "Лотерейный билет ОлдБк - 5шт, ";

				//предметы
				for ($cc=2;$cc<=5;$cc++) {
					$idx='i'.$cc;
					if ($get_info_boxs[$idx]>0) {
						$mdex=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]];//получаем макс.долговечность для предмета по его ид и типу коробки
						$item_name=mk_my_item($user,$get_info_boxs[$idx],$lar_inf,$mdex);
					}
						
					if (($get_info_boxs[$idx]==9090) OR ($get_info_boxs[$idx]==190190)) {
						//текст поздравления - 
						//- заточка 6 или 7
						//- ваучер
						//- защита от травм на 3 дня по защите чтоб писало типа получил личную абилити
						if ($user[sex]==0) { $ak='а'; 	} else  { $ak=''; }
						$TEXTsta="<font color=red>Поздравляем! Персонажу ".$user[login]." улыбнулась фортуна, он{$ak} обнаружил{$ak} \"{$item_name}\" !</font>";
						addch2all($TEXTsta);
					}  
				}
				
	
				//личные абилки
				for ($cc=6;$cc<=8;$cc++) {
					$idx='i'.$cc;
					if ($get_info_boxs[$idx]>0) {
						//абилки
						if ($get_info_boxs[$idx] == 155) $get_info_boxs[$idx] = 5007152;
						$count = $dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
						$echo_text .= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
				
						 if ($get_info_boxs[$idx]==4846) {
							if ($user[sex]==0) { $ak='а'; 	} else  { $ak=''; }
							/*
		 					$TEXTsta="<font color=red>Поздравляем! Персонажу ".$user[login]." улыбнулась фортуна, он{$ak} получил{$ak} личную абилити \"Защита от травм на 3 дня\" !</font>";
							addch2all($TEXTsta,$bot_city);	 					
							*/
						}
					}
				}
			} elseif ($boxs_type[$rowm['prototype']] == 3) {
				get_bonus_bill_loto($user,2,$dill);
				echo "Лотерейный билет ОлдБк - 2шт, ";


				for ($cc=2;$cc<=3;$cc++) {
					$idx='i'.$cc;
					if ($get_info_boxs[$idx]>0) {
					        if (substr($get_info_boxs[$idx],0,5) == "99999") {
							$get_info_boxs[$idx] = substr($get_info_boxs[$idx],5);
							if ($get_info_boxs[$idx] == 155) $get_info_boxs[$idx] = 5007152;

							//абилки
							$count=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
							$echo_text.= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
						
							if ($get_info_boxs[$idx]==4846) {
								if ($user[sex]==0) { $ak='а'; 	} else  { $ak=''; }
									/*
				 					$TEXTsta="<font color=red>Поздравляем! Персонажу ".$user[login]." улыбнулась фортуна, он{$ak} получил{$ak} личную абилити \"Защита от травм на 3 дня\" !</font>";
									addch2all($TEXTsta,$bot_city);	 					
									*/
							}
						} else {
							$mdex=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]];//получаем макс.долговечность для предмета по его ид и типу коробки
							$item_name=mk_my_item($user,$get_info_boxs[$idx],$lar_inf,$mdex);
						}
					}
						
					if (($get_info_boxs[$idx]==9090) OR ($get_info_boxs[$idx]==190190)) {
						//текст поздравления - 
						//- заточка 6 или 7
						//- ваучер
						//- защита от травм на 3 дня по защите чтоб писало типа получил личную абилити
						if ($user[sex]==0) { $ak='а'; 	} else  { $ak=''; }
						$TEXTsta="<font color=red>Поздравляем! Персонажу ".$user[login]." улыбнулась фортуна, он{$ak} обнаружил{$ak} \"{$item_name}\" !</font>";
						addch2all($TEXTsta);
					}
				}
				
	
				//личные абилки
				$cc = 4;
				$idx='i'.$cc;
				if ($get_info_boxs[$idx]>0) {
					//абилки
					$count=$dex_arr[$get_info_boxs[box_type]][$get_info_boxs[$idx]]; // количество
					$echo_text.= mk_pers_abil($user,$get_info_boxs[$idx],$count,$lar_inf).", ";	
			
					 if ($get_info_boxs[$idx]==4846) {
						if ($user[sex]==0) { $ak='а'; 	} else  { $ak=''; }
						/*
		 				$TEXTsta="<font color=red>Поздравляем! Персонажу ".$user[login]." улыбнулась фортуна, он{$ak} получил{$ak} личную абилити \"Защита от травм на 3 дня\" !</font>";
						addch2all($TEXTsta,$bot_city);	 					
						*/
					}
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
						"'.mysql_real_escape_string($rowm['type']).'",
						"0",
						"'.mysql_real_escape_string($rowm['letter']).'",
						"'.mysql_real_escape_string($rowm['massa']).'",
						"'.mysql_real_escape_string($rowm['isrep']).'",
						"'.mysql_real_escape_string($rowm['prototype']).'",
						"'.mysql_real_escape_string($rowm['otdel']).'",
						"'.mysql_real_escape_string($rowm['add_time']).'",
						"'.mysql_real_escape_string($rowm['present_text']).'",
						"'.mysql_real_escape_string($rowm['present']).'",
						"30","'.(time()+(3600*24*30)).'"
					)
				';
				mysql_query($sql) or die(mysql_error());

			}
		}
	}
}

?>
