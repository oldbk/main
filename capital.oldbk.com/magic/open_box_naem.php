<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


function mk_my_item_gift($telo,$proto,$larinfo,$present=false,$ekr_flag=0,$kol=1,$rwproto) {


$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));

	if ($present==true)
			{
			$dress['present'] = 'Удача';
			}

	if ($dress[id]>0) 
	{
		
		if ($dress['goden'] > 0) 
		{
			$godentime = time()+($dress['goden']*3600*24);
		} 
			else 
		{
			$godentime = 0;
		}
	
	$dress['ecost']=0;
	$dress['cost']=0;	
	
	$dress['ekr_flag']=$ekr_flag;
	$aitms=array();
	
	for($i=1;$i<=$kol;$i++)
	{
	if(mysql_query("INSERT INTO oldbk.`inventory`
		(`add_time`,`ekr_flag`,`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`img_big`,`rareitem`,`sowner`
		)
		VALUES
			('".time()."','{$dress['ekr_flag']}','".($rwproto-7050)."','{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
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


	if ($rowm['prototype']==7201)
	{
	$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";
	/*
	Содержит 1-3 серых ресурсов для производства предметов наемника Капитана Бронна.
	(на выдачу 3х ресов шанс 15%, на выдачу 2х ресов шанс 35%, на выдачу 1го реса шанс 50%)
	Выдаем рандомные серые итемы:
	PROTO_ID:104102 Суперклей
	PROTO_ID:104106 Стальная труба
	PROTO_ID:104110 Бархат
	PROTO_ID:104114 Стальная проволока
	*/
	
	/*
	7201 Малый капитанский сундук
	     PROTO_ID:104102 Суперклей 30%
	     PROTO_ID:104106 Стальная труба 30%
	     PROTO_ID:104110 Бархат 30%
	     PROTO_ID:104114 Стальная проволока 10%	
	*/
	
		$shrnd=mt_rand(1,100);
		
		if ($shrnd<=15)
		{
		$kol=3;
		}
		elseif ($shrnd<=50)
		{
		$kol=2;
		}
		else
		{
		$kol=1;
		}
		
		$pconfig_box=array(104102=>30,104106=>30,104110=>30,104114=>10);
		
	}
	else
	if ($rowm['prototype']==7202)
	{
	$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";
	/*
	Содержит 1-2 зеленых ресурсов для производства предметов наемника Капитана Бронна.
	(на выдачу 2х ресов шанс 35%, на выдачу 1го реса шанс 65%)
	Выдаем рандомные зеленые итемы:
	PROTO_ID:104103 Суперклей
	PROTO_ID:104107 Стальная труба
	PROTO_ID:104111 Бархат
	PROTO_ID:104115 Стальная проволока
	*/
		$shrnd=mt_rand(1,100);
		if ($shrnd<=35)
		{
		$kol=2;
		}
		else
		{
		$kol=1;
		}
	$pconfig_box=array(104103=>30,104107=>30,104111=>30,104115=>10);
	}
	else
	if ($rowm['prototype']==7203)
	{
	$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";
	/*
	Содержит 1 синий ресурс для производства предметов наемника Капитана Бронна.
	Выдаем рандомный синий итем:
	PROTO_ID:104104 Суперклей
	PROTO_ID:104108 Стальная труба
	PROTO_ID:104112 Бархат
	PROTO_ID:104116) Стальная проволока
	*/
	$kol=1;
	$pconfig_box=array(104104=>30,104108=>30,104112=>30,104116=>10);
	}
	else
	if ($rowm['prototype']==7204)
	{
	$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";
	/*
	Содержит 1-3 фиолетовых ресурсов для производства предметов наемника Капитана Бронна.
	(на выдачу 3х ресов шанс 25%, на выдачу 2х ресов шанс 25%, на выдачу 1го реса шанс 50%)
	Выдаем рандомные фиолетовые итемы:
	PROTO_ID:104105 Суперклей
	PROTO_ID:104109 Стальная труба
	PROTO_ID:104113 Бархат
	PROTO_ID:104117 Стальная проволока
	*/
		$shrnd=mt_rand(1,100);
		if ($shrnd<=25)
		{
		$kol=3;
		}
		elseif ($shrnd<=50)
		{
		$kol=2;
		}
		else
		{
		$kol=1;
		}
	$pconfig_box=array(104105=>30,104109=>30,104113=>30,104117=>10);
	}
		
	$matrix_array=array();
	
	foreach ($pconfig_box as $proto=>$k)		
			{
				for($i=1;$i<=$k;$i++)
				{
				$matrix_array[]=$proto;
				}
			}
	shuffle($matrix_array);			
	
	for ($i=0;$i<$kol;$i++)			
		{
		$array_box[$i]=$matrix_array[$i];
		}
	
	
	$out_text='';
	
	if (count($array_box)>0)
	{
				foreach ($array_box as $nn=>$proto)
				{

								
								$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';
								$present=true;
								$out_text.=mk_my_item_gift($user,$proto,$lar_inf,$present,$ekr_flag,1,$rowm['prototype']);
								
				}
				
				$bet=1;
				$sbet=1;
				$MAGIC_OK=1;
				$out_text=substr($out_text,0,-2);
				echo $echo_out."<br>".$out_text;
				addchp ('<font color=red>Внимание!</font> '.$echo_out." ".$out_text,'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
	}
?>