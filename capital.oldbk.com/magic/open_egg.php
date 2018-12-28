<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


function mk_my_item($telo,$proto,$larinfo) 
{
	$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));

	$dress['cost'] = 1;
	$dress['ecost'] = 0;
	
	if ($dress[id]==105150)
		{
		//лото
		$dress['letter']='Билет на розыгрыш Пасхальной лотереи ';
		$dress['present'] = " Удача";		
		$l=explode("(cap",$larinfo);
		//mysql_query("INSERT INTO `oldbk`.`event_loto` SET `owner`={$telo[id]},`itm`=".(int)($l[1]));
		$dress['letter'].="№".mysql_insert_id();
		$dress['letter'].='Лотерея состоится в 12:00 13го апреля 2015г.';		
		}
		elseif ($dress[id]==580) 
		{
		$dress['present'] = " Удача";
		}
		elseif (($dress[id]>=15561) and ($dress[id]<=15568) )
		{
		$dress['present'] = "Лабиринт";
		$dress['goden']=0;
		}
		elseif ($dress[id]==9595)
		{
		$dress['maxdur']=2; //Встраиваивание магии 0/2
		}
		elseif ($dress[id]==4001)
		{
		$dress['maxdur']=3; //Ключ от лабиринта 0/3
		$dress['goden']=0;
		}		
		elseif ($dress[id]==2525)
		{
		$dress['maxdur']=5; //Кровавое нападение вендетта 0/5
		}
		elseif ($dress[id]==14003)
		{
		$dress['maxdur']=4; //Призыв III 0/4
		}		
		elseif ($dress[id]==4015)
		{
		$dress['maxdur']=2; //Пропуск в Руины 0/2
		$dress['goden']=0;
		}		
		elseif ($dress[id]==15003)
		{
		$dress['maxdur']=3; //Захват III (0/3)
		}		
		elseif ($dress[id]==119119)
		{
		$dress['maxdur']=4; //Клонирование (0/4)
		}		
		elseif ($dress[id]==120120)
		{
		$dress['maxdur']=3; //Переманива (0/3)
		}
		elseif ($dress[id]==11301)
		{
		$dress['maxdur']=5; //Неведимка падает 0/10 (надо сделать 0/5)
		}
		elseif ($dress[id]==101113)
		{
		$dress['maxdur']=3;//Перевоплощение 0/3 
		}
		elseif ($dress[id]==146146)
		{
		$dress['maxdur']=10;//Разбойное нападение падает (0/5) сделать 0/10
		}
		elseif ($dress[id]==147147)
		{
		$dress['maxdur']=10;//Кровавое разбойное нападение падает (0/5) сделать 0/10
		}		



	

	if ($dress[id]>0) 
	{
	if(mysql_query("INSERT INTO oldbk.`inventory`
		(`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
		)
		VALUES
			(33,'{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
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
			$rec['type']=1419;//   получил из ларца
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
			return $dress['name']."[0/".$dress['maxdur']."]".", ";
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
	$rec['type']=1420;//   получил из ларца -абилку
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


if ($rowm[prototype] ==2013005)  
{

$def_array=array(4016,580); // 100% падают
$random_array=array(1,2,3,9595,4001,2525,14003,200273,200272,4015,15561,15562,15563,15564,15565,15566,15567,15568,119119,15003,120120,180011,180012,180013,180014,180015,121121121,40000000,11301,101113,125125,146146,147147); // рандом 1 шт из списка 
////////////////////////абилки-кол.юзов
// Снятие молчания
$rand_abil[1]=4847;
$rand_abil_c[4847]=3; //шт.
// выход из боя
$rand_abil[2]=49;
$rand_abil_c[49]=1; //шт.
// гнев ареса
$rand_abil[3]=5007152;
$rand_abil_c[5007152]=2; //шт.
//////
$m=count($random_array)-1;
$add_one=mt_rand(0,$m);
$def_array[]=$random_array[$add_one];
 		 
			echo "Вы открыли {$rowm[name]}, и получили:<br>";
		 	
		$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';
				

    		foreach($def_array as $k => $itmid)
		{			
		
					if (($itmid==1)OR($itmid==2)OR($itmid==3))
						{
						//личные абилки
						$abilid=$rand_abil[$itmid]; //получаем ид магии 
						$count=$rand_abil_c[$abilid]; // получаем кол. 
						$echo_text .= mk_pers_abil($user,$abilid,$count,$lar_inf).", ";	
						}
					else
						{
						//предметы
						$echo_text .=mk_my_item($user,$itmid,$lar_inf);

						}

		}
			

			$echo_text=substr($echo_text,0,-2);
			echo $echo_text;

		$bet=1;
		$sbet=1;
		$MAGIC_OK=1;

}

?>