<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }



function mk_my_item_gift($telo,$proto,$larinfo,$present=false,$ekr_flag=0,$kol=1) {

$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));

	if ($present==true)
			{
			$dress['present'] = 'Удача';
			}

	if ($dress[id]>0) 
	{
	
						//срок годности 7 дней, 
						if (in_array($proto,array(246, 249, 200271,24646, 249249, 271271,5202, 5205, 200273)))
						{
						if ($dress['goden']==0) $dress['goden']=7;
						}
						
						
		if ($dress['goden'] > 0) 
		{
			$godentime = time()+($dress['goden']*3600*24);
		} 
			else 
		{
			$godentime = 0;
		}
	
	
	$dress['ekr_flag']=$ekr_flag;
	$aitms=array();
	
	for($i=1;$i<=$kol;$i++)
	{
	if(mysql_query("INSERT INTO oldbk.`inventory`
		(`add_time`,`ekr_flag`,`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`img_big`,`rareitem`,`notsell`
		)
		VALUES
			('".time()."','{$dress['ekr_flag']}','56','{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}' , '{$dress['img_big']}', '{$dress['rareitem']}','1'
			) ;"))
		{
			$good ++;
				
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
			return $dress['name']."[0/".$dress['maxdur']."] x".$good.", ";
		} else {
			return false;
		}
	} else 
	{
		return false;
	}
}



	$can_use=mysql_fetch_assoc(mysql_query("select * from oldbk.stol where owner='{$user['id']}' and stol=5670"));
	
	if (($can_use['count']>0) and ($user['klan']!='radminion') )
	{
		err('Свиток можно использовать только 1 раз в сутки...');
	}
	else
{

	/*
	Механизм действия:
	     Раз в сутки, прочитав заклинание на папирусе (заюзав), персонаж получает рандомно 
	*/ 
	$items_first=array(246, 249, 200271);   //     Малый свиток «Восстановление HP» номиналом 90/180/360 хп   Прототипы: 246, 249, 200271 - подарком от Удача, срок годности 7 дней, notsell, нельзя встроить.
	//уник-чел
	if (strpos($user['medals'], 'k202;') !== false)
	{
	/*
      Если у перса есть медаль за Героический квест в Загороде (http://i.oldbk.com/i/202medal.png),       то он получает рандомно Средний свиток «Восстановление HP» номиналом 90/180/360 хп. 	Прототипы: 24646, 249249, 271271 - подарком от Удача, срок годности 7 дней, notsell, нельзя встроить.	
	*/	
	$items_first=array(24646, 249249, 271271);  
	}
	
	//супер-уник-чел
	if (strpos($user['medals'], 'k203;') !== false)
	{
	/*
	Если у перса есть медаль за  Легендарный квест (http://i.oldbk.com/i/203medal.gif), то он получает рандомно Большой свиток «Восстановление HP» номиналом 90/180/360 хп.
		Прототипы: 5202, 5205, 200273 - подарком от Удача, срок годности 7 дней, notsell, нельзя встроить.
	*/
	$items_first=array(5202, 5205, 200273);  
	}
	shuffle($items_first);
	$rndkey=$items_first[0];
	$array_box[$rndkey]=1;//1 шт.

	/*
     При использовании 5 юзов подряд (5 дней подряд ежедневного использования) в дополнение к свитку хила выдается рандомно:


	По окончании цикла или при его нарушении, начинается новая серия.
	*/
	$set_count_zero=false;
	
	$additems=mysql_fetch_assoc(mysql_query("select * from oldbk.magic_scroll_timer where owner='{$user['id']}' "));

	if (($additems['cnt']>=4) and ( date("Y-m-d",strtotime("-1 day"))==$additems['last_use']  or $user['klan']=='radminion' ) ) //и последний юз был день назад
		{
		$items_rnd=array(105104,105,105103,33000,33101,33010,34007,33100,33102,3001003,3001002,3001004);
		 //	Тост PROTO_ID:105104 - подарком от Удача, notsell
		//	Легкий завтрак PROTO_ID:105 - подарком от Удача, notsell
		//	Сытный завтрак PROTO_ID:105103 - подарком от Удача, notsell
		//	Малый обед воина PROTO_ID:33000 - подарком от Удача, notsell
		//	Малый обед дракона PROTO_ID:33101 - подарком от Удача, notsell
		 //	Хлеб PROTO_ID:33010 - подарком от Удача, notsell
		//	Хлеб PROTO_ID:34007 - подарком от Удача, notsell
		//	Большой обед воина PROTO_ID:33100 - подарком от Удача, notsell
		 //	Большой обед дракона PROTO_ID:33102 - подарком от Удача, notsell
		//	Чаша Триумфа PROTO_ID:3001003 - подарком от Удача, notsell
		 //	Чаша Крови PROTO_ID:3001002 - подарком от Удача, notsell
		//	Чаша Смерти PROTO_ID:3001004 - подарком от Удача, notsell		
		shuffle($items_rnd);
		$rndkey=$items_rnd[0];
		$array_box[$rndkey]=1;
		$set_count_zero=true;
		}

if (is_array($array_box))
	{

	echo "Вы использовали {$rowm[name]}, и получили:<br>";
	
	$out_text='';
	
				foreach ($array_box as $proto=>$kol)
				{

								
								$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';
								$present=true;
								
								$out_text.=mk_my_item_gift($user,$proto,$lar_inf,$present,$ekr_flag,$kol);
								
				}
				
				$bet=1;
				$sbet=1;
				$MAGIC_OK=1;
				$out_text=substr($out_text,0,-2);
				echo $out_text;


				mysql_query("INSERT INTO `oldbk`.`stol` SET `owner`='{$user['id']}',`stol`=5670,`count`='1' ON DUPLICATE KEY UPDATE `count`=`count`+1; "); //+1 юз
				
				
				if (($set_count_zero) or (date("Y-m-d",strtotime("-1 day"))!=$additems['last_use'] and $additems['last_use']!='' and ($user['klan']!='radminion') ) )
				{
				//обнуляем линейку  если дошли до 4х  или последний юз был давно
				mysql_query("UPDATE `oldbk`.`magic_scroll_timer` SET `cnt`=0,`last_use`=NOW()    WHERE  `owner`='{$user['id']}' ");
				}
				else
				{
				//делаем +1
				mysql_query("INSERT INTO `oldbk`.`magic_scroll_timer` SET `owner`='{$user['id']}',`cnt`=1,`last_use`=NOW() ON DUPLICATE KEY UPDATE `cnt`=`cnt`+1,`last_use`=NOW()");
				}

				
				
				//костыль на дюрейшен
				$rowm['duration']=-1; // заглушка чтоб не удалилась
				mysql_query("UPDATE oldbk.`inventory` SET duration=-1 WHERE `id` = {$rowm['id']} LIMIT 1;");
				
	}


}

?>