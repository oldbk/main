<?
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}


function mk_my_item($telo, $proto,$finfo) {

	if ($proto == 19020 ||  $proto == 3001003) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	}
	$dress['present'] = "Удача"; // все подарком
	$dress['notsell']=1;	
	
	if ($dress['goden']==0) 
			{
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
				(1,32,'{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}', '{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['notsell']}',
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


	
	//id=> count - все предметы падают из eshop
	$dropitem=array(
			33053, //Большой ужин дракона (Гос магазин - 33053)
			3003133, //Жёлтая Магическая Книга (Гос магазин - 3003133)
			3003131, //Зеленая Магическая Книга (Гос магазин - 3003131)
			3003132,//Красная Магическая Книга (Гос магазин - 3003132)
			3003134, //Синяя Магическая Книга (Гос магазин - 3003134)
			3003135, //Чёрная Магическая Книга (Гос магазин - 3003135)
			33055, //Большой ужин викинга (Гос магазин - 33055)
			33052, //Малый ужин дракона (Гос магазин - 33052)
			33054, //Малый ужин викинга (Гос магазин - 33054)
			4170, //Повышенный опыт (+100%) (Гос магазин - 4170)
			4168, //Повышенный опыт (+80%) (Гос магазин - 4168)
			4166, //Повышенный опыт (+60%) (Гос магазин - 4166)
			4016, //Пропуск к Лорду Разрушителю (Гос магазин - 4016)
			4164, //Повышенный опыт (+40%) (Гос магазин - 4164)
			15553, //Статуя Удачи (Гос магазин - 15553)
			15552, //Статуя Мироздателя (Гос магазин - 15552)
			15551, //Статуя Мусорщика (Гос магазин - 15551)
			15556, //Статуя Пятницы (Гос магазин - 15556)
			15558, //Статуя Хранителя (Гос магазин - 15558)
			15554, //Статуя Исчадия Хаоса (Гос магазин - 15554)
			585, //Рунный опыт 10000 (Гос магазин - 585)
			586, //Рунный опыт 15000 (Гос магазин - 586)
			580, //Рунный опыт 1к (Гос магазин - 580)
			584, //Рунный опыт 5000 (Гос магазин - 584)
			56907, //«Platinum» аккаунт на 7 дней (Гос магазин - 56907)
			56914, //«Platinum» аккаунт на 14 дней (Гос магазин - 56914)
			56999, //«Platinum» аккаунт на 30 дней (Гос магазин - 56999)
			200277, //Средний свиток «Восстановление 720HP» (Гос магазин - 200277)
			55559, //Совершенный свиток «Защита от магии» (Гос магазин - 55559)
			19108, //Большой свиток «Рунный опыт» (Гос магазин - 19108)
			3001001, //Чаша Могущества (Гос магазин - 3001001)
			4007, //Большой свиток «Пропуск в Лабиринт» (Гос магазин - 4007)
			4019, //Большой свиток «Пропуск в Руины» (Гос магазин - 4019)
			1002225, //Большая аптечка (Гос магазин - 1002225)
			11303, //Совершенный свиток «Невидимость» (Гос магазин - 11303)
			11302, //Большой свиток «Невидимость» (Гос магазин - 11302)
			1122, //Большой свиток «Улучшение предмета» (Гос магазин - 1122)
			1002224, //Средняя аптечка (Гос магазин - 1002224)
			56666, //Великое чарование IV (Гос магазин - 56666)
			19020, //Репутации +20% (Березка - 19020)
			3001003  //Чаша Триумфа (Березка - 3001003)			 
			);

	
			//мешаем масив
			shuffle($dropitem);
			$drop_itm=$dropitem[0];
			
			// раздаём предметы
			$finf = '"'.$rowm[name].'" ('.get_item_fid($rowm).')';
				if (mk_my_item($user,$drop_itm,$finf))
					{
					$sbet = 1;
					$bet = 1;

					if ($rowm['ekr_flag']==1)
						{
						//первый юз свитка купленного привязываем свиток
						mysql_query("UPDATE oldbk.inventory set ekr_flag=0,  present='Удача' where id='{$rowm['id']}' limit 1;");
						}
						
					}
	
?>