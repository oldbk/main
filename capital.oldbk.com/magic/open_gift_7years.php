<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


function mk_my_item_gift($telo,$proto,$larinfo,$present=false,$ekr_flag=0,$kol=1,$rwproto) {


$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));

	if ($present==true)
			{
			$dress['present'] = 'Администрации ОлдБК';
			}

	if ($dress[id]>0) 
	{
			if (($dress[id]!=105103) and ($dress[id]!=20170129))
			{
			//  срок годности до 23:59 14/02/2017
			$goden_do = mktime(23,59,59,2,14,2017); 
			$goden = round(($goden_do-time())/60/60/24); if ($goden<1) {$goden=1;}
			$dress['goden']=$goden; 
			$godentime = $goden_do;
			
				if (time()>$goden_do) // просрочка
				{
				return false;
				}
			
			}
			else
			{
			
				if ($dress['goden'] > 0) 
					{
					$godentime = time()+($dress['goden']*3600*24);
					
					if (time()>$godentime) // просрочка
						{
						return false;
						}
					
					}
					else 
					{
					$godentime = 0;
					}
			}
	
	if ($ekr_flag>0) { $dress['ekr_flag']=$ekr_flag;}

	$dress['notsell']=0;
	if ($dress[id]==9598) 
				{ 
				$ekr_flag=0; 
				$dress['notsell']=1;
				}

	$aitms=array();
	
	for($i=1;$i<=$kol;$i++)
	{
	if(mysql_query("INSERT INTO oldbk.`inventory`
		(`add_time`,`ekr_flag`,`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`notsell`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`img_big`,`rareitem`,`sowner`
		)
		VALUES
			('".time()."','{$dress['ekr_flag']}','36','{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['notsell']}','{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$godentime."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}' , '{$dress['img_big']}', '{$dress['rareitem']}','".($dress['is_owner']==1?$telo[id]:0)."'
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
			return link_for_item($dress)." ".$good." шт., ";
		} else {
			return false;
		}
	} else 
	{
		return false;
	}
}


if ($rowm['prototype'] ==600662)
	{
	

	$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";
	/*
	 Содержимое упаковки
     oldbk-1504 уникальный подарок "С 7-летием ОлдБК!" - 1 шт., без срока годности = 20170129
     Совершенный свиток «Клонирование» - 7 шт, срок годности до 23:59 14/02/2017 = 119119120
     Совершенный свиток «Переманивание» - 7 шт, срок годности до 23:59 14/02/2017 = 120120121
     Средний свиток «Неукротимая ярость» - 7 шт, срок годности до 23:59 14/02/2017 = 200440
     Средний свиток «Восстановление здоровья 720» - 15 шт., срок годности до 23:59 14/02/2017 = 200277
	Встраивание магии  - 7 шт, срок годности до 23:59 14/02/2017=      9598 
	 Сытный завтрак - 7 шт, срок годности 30 дней (в прототипе уже есть) =     105103
     */

	// Дарим этот подарок всем персонажам в 05:00 14/01/2017, срок годности до 23:59 14/02/2017
	$array_box=array(
	20170129=>1, 
	119119120=>7,
	120120121=>7,
	200440=>7, 
	200277=>15,
	9598=>7,
	105103=>7);
	$out_text='';
	
	
				foreach ($array_box as $proto=>$kol)
				{

								
								$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';
								$present=true;
								

								
								$out_text.=mk_my_item_gift($user,$proto,$lar_inf,$present,$ekr_flag,$kol,$rowm['prototype']);
								
				}
				
				$bet=1;
				$sbet=1;
				$MAGIC_OK=1;
				$out_text=substr($out_text,0,-2);
				echo $echo_out."<br>".$out_text;
				addchp ('<font color=red>Внимание!</font> '.$echo_out." ".$out_text,'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
	}
	

?>