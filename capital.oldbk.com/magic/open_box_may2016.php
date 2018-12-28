<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


function mk_my_item_gift($telo,$proto,$larinfo,$present=false,$ekr_flag=0,$kol=1) {

$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));

	if ($present==true)
			{
			$dress['present'] = 'Администрация ОлдБК';
			}

	if ($dress[id]>0) 
	{
		if ($dress['goden']==0) $dress['goden']=30; // срок годности у всего,  30 дней; кроме того что уже есть 
		
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
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`img_big`,`rareitem`
		)
		VALUES
			('".time()."','{$dress['ekr_flag']}','5','{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}' , '{$dress['img_big']}', '{$dress['rareitem']}'
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


if ( ($rowm['prototype'] ==2016004)  or ($rowm['prototype'] ==2016005)  or ($rowm['prototype'] ==2016006) )
	{
	echo "Вы открыли {$rowm[name]}, и получили:<br>";


	/*
 	2016011 Золотой наградной сундук item2016_boxgold.gif
     Вексель прокатной лавки на 3 мес. (3 шт.)
     200273 Свиток восстановление 360хп 0/4 (30 шт)
     4016 Пропуск к Лорду (5 шт)
     Большой свиток магии (арес, гидра и так далее) на 6 часов (5 шт) (oldbk-542) =>1234
     119119119 Большой свиток клонирования 0/4 (5 шт)
     120120120 Большой свиток переманивания 0/3 (5 шт)
     14004 Большой свиток призыва 0/5 (5 шт)
     15004 Большой свиток захвата 0/3 (5 шт)	
	*/
	$config_box[2016004]=array(
		2016011=>3 , 
		200273=>30, 
		4016=>5, 1234=>5, 119119119=>5,120120120=>5,14004=>5,15004=>5);
	
	/*
	 Вексель прокатной лавки на 2 мес. (3 шт.)
	 200273 Свиток восстановление 360хп 0/4 (20 шт)
	 4016 Пропуск к Лорду (3 шт)
	 Средний свиток магии (арес, гидра и так далее) на 3 часа (3 шт) (oldbk-542)
	 119119119 Большой свиток клонирования 0/4 (3 шт)
	 120120120 Большой свиток переманивания 0/3 (3 шт)
	 14004 Большой свиток призыва 0/5 (3 шт)
	 15004 Большой свиток захвата 0/3 (3 шт)
	*/
	$config_box[2016005]=array(2016012=>3 , 200273=>20, 4016=>3, 1235=>3, 119119119=>3,120120120=>3,14004=>3,15004=>3);
	/*
	     Вексель прокатной лавки на 1мес. (3 шт.)
	     200273 Свиток восстановление 360хп 0/4 (10 шт)
	     4016 Пропуск к Лорду (2 шт)
	     Свиток магии (арес, гидра и так далее) на 1,5 часов (2 шт)
	     119119119 Большой свиток клонирования 0/4 (2 шт)
	     120120120 Большой свиток переманивания 0/3 (2 шт)
	     14004 Большой свиток призыва 0/5 (2 шт)
	     15004 Большой свиток захвата 0/3 (2 шт)
	*/	
	
	$config_box[2016006]=array(2016013=>3 , 200273=>10, 4016=>2, 1236=>2, 119119119=>2,120120120=>2,14004=>2,15004=>2);
	
	
	$array_box=$config_box[$rowm['prototype']];
	$out_text='';
	
	
				foreach ($array_box as $proto=>$kol)
				{

								
								$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';

								$present=true;
								
								if ($proto==1234)
									{
									$t = get_mag_stih($user);
									// $t[0] - от 1 до 4, 1 - огонь, 2 - земля, 3 - воздух, 4 - вода
									if ($t[0] == 1) $proto = 150157;
									if ($t[0] == 2) $proto = 920927;
									if ($t[0] == 3) $proto = 130137;
									if ($t[0] == 4) $proto = 930937;
									}
								elseif ($proto==1235)
									{
									$t = get_mag_stih($user);
									// $t[0] - от 1 до 4, 1 - огонь, 2 - земля, 3 - воздух, 4 - вода
									if ($t[0] == 1) $proto = 150158;
									if ($t[0] == 2) $proto = 920928;
									if ($t[0] == 3) $proto = 130138;
									if ($t[0] == 4) $proto = 930938;
									}
								elseif ($proto==1236)
									{
									$t = get_mag_stih($user);
									// $t[0] - от 1 до 4, 1 - огонь, 2 - земля, 3 - воздух, 4 - вода
									if ($t[0] == 1) $proto = 150152;
									if ($t[0] == 2) $proto = 920925;
									if ($t[0] == 3) $proto = 130135;
									if ($t[0] == 4) $proto = 930935;
									}
																		
									    
									
								
								$out_text.=mk_my_item_gift($user,$proto,$lar_inf,$present,$ekr_flag,$kol);
								
				}
				
				$bet=1;
				$sbet=1;
				$MAGIC_OK=1;
				$out_text=substr($out_text,0,-2);
				echo $out_text;
	}
	

?>