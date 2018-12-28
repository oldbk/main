 <?php

if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}

// 0 shop
// 1 eshop

/*
- - убрать свиток перевоплота, неведимости, екровый Гнев Ареса III
*/


$addmag[1]=150152; //Гнев Ареса III 
$addmag[2]=920925; //Обман Химеры III
$addmag[3]=130135; //Вой Грифона III
$addmag[4]=930935; //Укус Гидры III, 

$need_astih=get_mag_stih($user); // получаем ид стихии
$need_astih=$need_astih[0]; //на 0м месте родная стихия
$need_astih=$addmag[$need_astih]; //нужный свиток

$l1 = array(
	102 => 0,
	103 => 0,
	129 => 0,
	157 => 1,
	125125 => 0, // 0/2 fix
	180011 => 1,
	180012 => 1,
	180013 => 1,
	180014 => 1,
	180015 => 1,
	144144 => 0,
	56662=>0, //<<< 1) В ассортимент 1 и 3 недели добавить: Cвиток Чарования [II]

);
$l2 = array(
	2525 => 1,
	121121 => 0,
	40000000 => 1,
	//150155 => 1, екр Ареса убрать
	//101111 => 1, перевопл убрать
	//301 => 1, невед убрать
	119119 => 0,
	120120 => 0,
	56663=>1,//В ассортимент 2 и 4 недели добавить: Cвиток Чарования [III],
	200272 => 1, // 0/3 fix
        200273 => 1, // 0/3 fix
	200269 => 1, // 0/3 fix - 180  хилка не встраиваемая
);

$l2[$need_astih]=0;  //добавляем свиток стихий 2) В ассортимент 2 и 4 недели добавить: Cвиток Чарования [III], кредовые с юзами 0/3 Вой Грифона III, Обман Химеры III, Укус Гидры III, Гнев Ареса III (обязательно соответствующую знаку зодиака перса);

if ($user['id']==14897)
	{
	print_r($l2);
	}

$l3 = array(4016 => 0);  //3) Вместо "Рунного опыта 100" 31/10 и 01/11 выдавать Пропуск к Лорду

$ilist = array();

$per = 0;

$tboxstart = mktime(0,0,0,10,7,2015);
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
} else if (time() <=  mktime(23,59,59,11,6,2015) ) 
{
 	$per = 4;
 }

	

if ((date("n") == 10 && date("j") == 31) || (date("n") == 11 && date("j") == 1)) {
	$t = array_rand($l1,1);
	$ilist[$t] = $l1[$t];

	$t = array_rand($l2,1);
	$ilist[$t] = $l2[$t];

	$t = array_rand($l3,1);
	$ilist[$t] = $l3[$t];
} elseif($per == 1 || $per == 3) {
	$t = array_rand($l1,1);
	$ilist[$t] = $l1[$t];
} elseif ($per == 2 || $per == 4) {
	$t = array_rand($l2,1);
	$ilist[$t] = $l2[$t];
}

function mk_my_item($telo, $proto,$shop,$finfo) {
	if ($shop == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} else {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	}

	$dress['present'] = "Администрация ОлдБК"; // все подарком

	if ($proto == 125125) $dress['maxdur'] = 2;
	//if ($proto == 571) $dress['present'] = "Администрация ОлдБК";
	if ($proto == 200272 || $proto == 200273 || $proto == 200269 || $proto ==150152 || $proto ==920925 || $proto ==130135 || $proto ==930935) $dress['maxdur'] = 3;

	$dress['goden']=30;

	if ($dress[id]>0) {
	
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
				$rec['type']=421;//   получил футбольного мяча
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
				echo $dress['name']." [0/".$dress['maxdur']."]"."<br>";
				return $dress['name'];
			} else {
				return false;
			}
	} else {
		return false;
	}
}


//if (count($ilist)) 
if (false)
{
	$q = mysql_query('SELECT * FROM t_use WHERE owner = '.$user['id'].' and `date` = "'.date("d/m/Y").'"') or die(mysql_error());
	if (mysql_num_rows($q) == 0) {
	
		if (($user['id']==14897) OR ($user['id']==8540) OR ($user['id']==6745) OR ($user['id']==182783))
		{
		//админы
		}
		else
		{
		mysql_query('INSERT INTO t_use (`owner`,`date`) VALUES ("'.$user['id'].'","'.date("d/m/Y").'")') or die(mysql_error());
		}

		$finf = '"'.$rowm[name].'" ('.get_item_fid($rowm).')';
		while(list($k,$v) = each($ilist)) {
			// раздаём предметы
			mk_my_item($user,$k,$v,$finf);
		}
	} else {
		echo "Сегодня уже использовали...";
	}
} else {
	echo "Этот Хеллоуин окончен...";
}
?>