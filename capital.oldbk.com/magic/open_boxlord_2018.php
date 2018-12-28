<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }



function mk_my_item_gift($telo,$proto,$larinfo,$present=false,$ekr_flag=0,$kol=1,$goden=0,$getfr=160) {

$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));

	if ($present==true)
			{
			$dress['present'] = 'Торговец Галиас';
			}

	if ($dress[id]>0)
	{

		$dress['goden']=$goden;


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
			('".time()."','{$dress['ekr_flag']}','{$getfr}','{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
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




	$array_box[2018160][19108]=1;
	$array_box[2018160][3004053]=1;
	$array_box[2018160][4016]=3;
	$array_box[2018160][3001001]=3;
	$array_box[2018160][200277]=60;

	/*
	– PROTO_ID:19108 (Большой свиток «Рунный опыт») - 1 шт.
	– PROTO_ID:4016 (Пропуск к Лорду Разрушителю) - 3 шт.
	– PROTO_ID:3001001 (Чаша Могущества) - 3 шт.
	– PROTO_ID:200277 (Средний свиток «Восстановление 720HP») - 60 шт.
	 - Каменный снаряд 1шт PROTO_ID:3004053
	*/

	$array_box[2018161][19108]=1;
	$array_box[2018161][3004053]=3;
	$array_box[2018161][4016]=5;
	$array_box[2018161][3001001]=5;
	$array_box[2018161][200277]=100;

	/*
	– PROTO_ID:19108 (Большой свиток «Рунный опыт») - 1 шт.
	– PROTO_ID:4016 (Пропуск к Лорду Разрушителю) - 5 шт.
	– PROTO_ID:3001001 (Чаша Могущества) - 5 шт.
	– PROTO_ID:200277 (Средний свиток «Восстановление 720HP») - 100 шт.
	 - Каменный снаряд 3шт PROTO_ID:3004053
	*/

	$array_box[2018162][19108]=1;
	$array_box[2018162][3004053]=5;
	$array_box[2018162][4016]=10;
	$array_box[2018162][3001001]=10;
	$array_box[2018162][200277]=200;

	/*
	– PROTO_ID:19108 (Большой свиток «Рунный опыт») - 1 шт.
	– PROTO_ID:4016 (Пропуск к Лорду Разрушителю) - 10 шт.
	– PROTO_ID:3001001 (Чаша Могущества) - 10 шт.
	– PROTO_ID:200277 (Средний свиток «Восстановление 720HP») - 200 шт.
	 - Каменный снаряд 5шт PROTO_ID:3004053
	*/

	$array_box[2018163][19108]=2;
	$array_box[2018163][3004053]=10;
	$array_box[2018163][4016]=30;
	$array_box[2018163][3001001]=30;
	$array_box[2018163][200277]=600;

	/*
 PROTO_ID:19108 (Большой свиток «Рунный опыт») - 2 шт.
– PROTO_ID:4016 (Пропуск к Лорду Разрушителю) - 30 шт.
– PROTO_ID:3001001 (Чаша Могущества) - 30 шт.
– PROTO_ID:200277 (Средний свиток «Восстановление 720HP») - 600 шт.
 - Каменный снаряд 10шт PROTO_ID:3004053
	*/

$array_box=$array_box[$rowm['prototype']];

$getfr=$rowm['prototype']-2018000;

if (is_array($array_box))
	{

	echo "Вы открыли {$rowm[name]}, и получили:<br>";

	$out_text='';

				foreach ($array_box as $proto=>$kol)
				{


								$lar_inf='"'.$rowm[name].'" ('.get_item_fid($rowm).')';
								$present=true;

								$out_text.=mk_my_item_gift($user,$proto,$lar_inf,$present,0,$kol,$rowm['goden'],$getfr);

				}

				$bet=1;
				$sbet=1;
				$MAGIC_OK=1;
				$out_text=substr($out_text,0,-2);
				echo $out_text;

	}
?>
