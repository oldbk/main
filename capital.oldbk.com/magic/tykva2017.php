 <?php

if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}



 function get_rand_item($arr)
 {
	 global $per;

	 $tmp=array();
	 //заполняем матрицу согласно шансам
	 foreach($arr as $itm=>$ch)
	 {
		 for ($i=1;$i<=$ch;$i++)
		 {
			 $tmp[]=$itm;
		 }
	 }
	 $t = array_rand($tmp,1);

	 if (($per==1) or ($per==3))
	 {
		 $stcount='count1';
	 }
	 else
	 {
		 $stcount='count2';
	 }

	 mysql_query("UPDATE `oldbk`.`t_use_tykva` SET `{$stcount}`=`{$stcount}`-1 WHERE `proto`='{$tmp[$t]}' ");


	 return $tmp[$t];
 }

$ilist = array();

$per = 0;

$tboxstart = mktime(0,0,0,10,17,2018);
$p1 = $tboxstart+(24*3600*7);
$p2 = $p1+(24*3600*7);
$p3 = $p2+(24*3600*7);
$p4 = $p3+(24*3600*7)+3600;

if(time() >= $tboxstart && time() <= $p1) {
	$per = 1;
} elseif (time() >= $p1 && time() <= $p2) {
	$per = 2;
} else if (time() >= $p2 && time() <= $p3) {
	$per = 3;
} else if (time() >= $p3 && time() <= $p4) {
	$per = 4;
} else if (time() <=  mktime(23,59,59,11,13,2018) )
{
 	$per = 4;
 }


if (($per==1) or ($per==3))
	{
	$stcount='count1';
	}
	else
	{
	$stcount='count2';
	}

if (($user['id']==182783) OR ($user['id']==698798) or ($user['id']==698800) or ($user['id']==698805))
	{
	$per = 2;
	}



	/* ограничитель
	$get_stop_item=mysql_query("select * from t_use_tykva;");
	while ($rw = mysql_fetch_assoc($get_stop_item))
		{
			if ($rw[$stcount]==0)
			{
				//уже все выбрали для этого прототипа
				if ($stcount=='count1')
					{
					$rnd1[180015]=$rnd1[180015]+$rnd1[$rw['proto']]; //шанс заточки отдаем скупке
					$rnd1[$rw['proto']]=0;
					}
					else
					{
					$rnd2[119119]=$rnd2[119119]+$rnd2[$rw['proto']]; //шанс заточки отдаем //Средний свиток «Клонирование»шанс 15%
					$rnd2[$rw['proto']]=0;
					}
			}
		}
	*/

$_start = (new DateTime('2018-10-31 00:00:00'))->getTimestamp();
$_end = (new DateTime('2018-11-01 23:59:59'))->getTimestamp();
$_current = time();
 if ($_current >= $_start && $_current <= $_end)
 {
	 if ($user['id']==14897) echo "A1";

	 if (count($rnd1)>0)
	 {
		 $t = get_rand_item($rnd1);
		 $ilist[$t] = $l1[$t];
	 }

	 if (count($rnd2)>0)
	 {
		 $t = get_rand_item($rnd2);
		 $ilist[$t] = $l2[$t];
	 }

	 if (count($rnd3)>0)
	 {
		 $t = get_rand_item($rnd3);
		 $ilist[$t] = $l3[$t];
	 }
 } elseif($per == 1 || $per == 3) {
	 if ($user['id']==14897) echo "A2";
	 if (count($rnd1)>0)
	 {
		 $t = get_rand_item($rnd1);
		 $ilist[$t] = $l1[$t];
	 }
 } elseif ($per == 2 || $per == 4) {
	 if ($user['id']==14897) echo "A3";
	 if (count($rnd2)>0)
	 {
		 $t = get_rand_item($rnd2);
		 $ilist[$t] = $l2[$t];
	 }
 }



function mk_my_item($telo, $proto,$shop,$finfo,$TKV_GODEN) {



	if ($shop == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	}
	elseif ($shop == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.cshop where id='{$proto}' ;"));
	}
	else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}

	$dress['present'] = "Удача"; // все подарком
	$dress['goden']=$TKV_GODEN;
	$dress['sowner']=$telo['id'];
	$dress['notsell']=1;


	if ($dress[id]>0) {


		if(mysql_query("INSERT INTO oldbk.`inventory`
			(`getfrom`,`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`img_big`,`rareitem`,`ekr_flag`,`notsell`,`maxdur`,`isrep`,`letter`,
			`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
			`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
			`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
			)
			VALUES
				(35,'{$dress['id']}','{$telo[id]}','{$dress['sowner']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}', '{$dress['img_big']}', '{$dress['rareitem']}','{$dress['ekr_flag']}','{$dress['notsell']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
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
				$rec['type']=421;//   получил
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

				return link_for_item($dress)." ".$good." шт., ";
			} else {
				return false;
			}
	} else {
		return false;
	}
}


if (count($ilist)) {
	$q = mysql_query("SELECT * FROM t_use WHERE owner = '{$user['id']}' and proto='{$rowm['magic']}' and `date` = '".date("d/m/Y")."' ") or die(mysql_error());
	if (mysql_num_rows($q) == 0)
	{

		if (($user['klan']=='radminion') OR ($user['klan']=='testTest'))
		{
		//админы
		}
		else
		{
		mysql_query('INSERT INTO t_use (`owner`,`date`, `proto` ) VALUES ("'.$user['id'].'","'.date("d/m/Y").'", "'.$rowm['magic'].'" )') or die(mysql_error());
		}

		$finf = '"'.$rowm[name].'" ('.get_item_fid($rowm).')';
		while(list($k,$v) = each($ilist))
		{
			// раздаём предметы
			$out_text.=mk_my_item($user,$k,$v,$finf,$TKV_GODEN);
		}

			if ($out_text!='')
			{
			$echo_out="Вы открыли <b>{$rowm[name]}</b>, и получили:";

				$out_text=substr($out_text,0,-2);
				echo $echo_out."<br>".$out_text;
				addchp ('<font color=red>Внимание!</font> '.$echo_out." ".$out_text,'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
			}
	} else {
		echo "Сегодня уже использовали такую тыкву...";
	}
} else {
	echo "Хеллоуин окончен...";
}
?>
