<?php
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

include "magic/magicconf.php";

if ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif ($user['intel'] < 0) {
	echo "У вас недостаточно интеллекта...";
}
elseif ($user['lab'] >0) {
	echo "Неподходящий момент...";
}
else {
	if(isset($_REQUEST['clearstored']))
	{
		$_SESSION['scroll'] = null;
		header("Location: main.php?edit=1");
	}
	$int = 80 + $user['intel'] - 17;
	if ($int > 100) { $int = 100; }

	if(!$_SESSION['scroll']) {
		$_SESSION['scroll'] = $_POST['target'];
		?>
		<body onload="showitemschoice('Выберите предмет, в который перевстраивается свиток', 'items', 'main.php?edit=1&use=<?=$_GET['use']?>')">
		<?
	}
	elseif (rand(1,100) <= $int) {


		$olditem = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '".$_SESSION['scroll']."' AND `owner` = '{$user['id']}' AND `dressed`=0  and labonly=0 and labflag=0 and ((prototype<55510301) OR (prototype>55510401) ) LIMIT 1;"));
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$user['id']}' AND present!='Арендная лавка'  AND `id` = '{$_POST['target']}' AND `includemagic` = 0 AND `dressed`=0 AND `setsale`=0 and type < 12 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 )  and labonly=0 and labflag=0  LIMIT 1;"));
		$_SESSION['scroll'] = null;
		if(!$olditem)
		{
			echo "<font color=red><b>У вас нет такого предмета! [{$_SESSION['scroll']}]<b></font>";
		}
		elseif(!$dress OR $dress['type'] >= 12)
		{
			echo "<font color=red><b>У вас нет такого предмета! [{$_POST['target']}]<b></font>";
		}
		else
		{
			$incmagic = magicinf($olditem['includemagic']);
			if(!$incmagic['img'])
			{
				echo "<font color=red><b>Этот свиток нельзя перевстраивать в предметы!<b></font>";
			}
			else
			{
			//	echo 'ст инклюдмаджик '.$olditem['includemagic'].'<br>';

				$svitok_prot = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.shop WHERE `magic` = '".$olditem['includemagic']."' LIMIT 1;"));
             //   echo '1 '.$svitok_prot[id].'<br>';
                if(!$svitok_prot)
                {
					$svitok_prot = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.eshop WHERE `magic` = '".$olditem['includemagic']."' LIMIT 1;"));
			//		echo '2 '.$svitok_prot[id].'<br>';
                }

                if($svitok_prot[id]>0)
                {
		                $magic=magicinf($olditem['includemagic']);

						$perezar=$olditem['includemagicuses'];
						$new_kr_cost = $olditem['cost'] - $olditem['includemagiccost'] * 2;
						if($new_kr_cost < 0) { $new_kr_cost = 1; }
						$new_ekr_cost = $olditem['ecost'] - $olditem['includemagicekrcost'] * 2;
						if($new_ekr_cost < 0) { $new_ekr_cost = 0; }
						$shop_base = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.shop WHERE id = \''.$olditem['prototype'].'\' LIMIT 1;'));
						if($shop_base[nlevel]<$olditem[up_level])
						{
		                   $shop_base[nlevel]=$olditem[up_level];
						}
//							`nlevel` = '{$shop_base['nlevel']}',
		                   //удалили из старой шмотки встройку.
		                  $sql="UPDATE oldbk.`inventory` SET
							`includemagic` = '',
							`includemagicmax` = '',
							`includemagicname` = '',
							`includemagicuses` = '',
							`includemagiccost` = '',
							`includemagicdex` = '',
							`includemagicekrcost` = '',
							`nintel` = '{$shop_base['nintel']}',
							`nmudra` = '{$shop_base['nmudra']}',
							`ngray` = '{$shop_base['ngray']}',
							`ndark` = '{$shop_base['ndark']}',
							`nlight` = '{$shop_base['nlight']}',
							`cost` = '{$new_kr_cost}',
							`ecost` = '{$new_ekr_cost}',
							`massa` = `massa` - 1,
							`sebescost` = `sebescost` - ".$incmagicprice."
							WHERE `id` = '{$olditem['id']}' LIMIT 1;";

						if (mysql_query($sql))
						{

							if ($svitok_prot['repcost'] > 0) {
								$rtype = 3;
							} elseif ($svitok_prot['ecost'] > 0) {
								$rtype = 2;
							} else {
								$rtype = 1;
							}

								// всунули встройку в новую шмотку
								//									".($dress['nlevel']<=$magic['nlevel']?"`nlevel`='".$magic['nlevel']."',":"")."
                          					$sql="UPDATE oldbk.`inventory`
								SET ".($dress['nintel']<=$svitok_prot['nintel']?"`nintel`='".$svitok_prot['nintel']."',":"")."
									".($dress['nmudra']<=$svitok_prot['nmudra']?"`nmudra`='".$svitok_prot['nmudra']."',":"")."
									".($dress['ngray']<=$svitok_prot['ngray']?"`ngray`='".$svitok_prot['ngray']."',":"")."
									".($dress['ndark']<=$svitok_prot['ndark']?"`ndark`='".$svitok_prot['ndark']."',":"")."
									".($dress['nlight']<=$svitok_prot['nlight']?"`nlight`='".$svitok_prot['nlight']."',":"")."
									`massa`=`massa`+1,`cost`=`cost`+'".$svitok_prot['cost']."', `includemagic` = '".$svitok_prot['magic']."',
									`includemagicdex` = '".($olditem['includemagicdex']>$svitok_prot['maxdur']?$svitok_prot['maxdur']:$olditem['includemagicdex'])."', `includemagicmax` = '".$svitok_prot['maxdur']."',
									`includemagicname` = '".$svitok_prot['name']."', `includemagicuses` = '".$perezar."',
									`includeprototype` = ".$svitok_prot['id'].",
									`includerechargetype` = ".$rtype.",
									`sebescost` = `sebescost` + ".$incmagicprice."
									WHERE `id` = '{$dress['id']}' LIMIT 1;";

							if (mysql_query($sql))
							{
								if($olditem[idcity]==0)
								{
		                             $city='cap';
		                        }
		                        else
		                        if($olditem[idcity]==1)
		                        {
		                        	$city='ava';
		                        }

		                        if($dress[idcity]==0)
								{
		                             $citynew='cap';
		                        }
		                        else
		                        if($dress[idcity]==1)
		                        {
		                        	$citynew='ava';
		                        }
		      					 $delo_txt="Свиток ".$svitok_prot['name']." удачно перенесен из  ".$olditem['name']."(".$city.$olditem['id'].")  в (".$citynew.$dress['id']."). осталось юзов ".$perezar;

								        //new_delo
					  		    		$rec['owner']=$user[id];
									$rec['owner_login']=$user[login];
									$rec['owner_balans_do']=$user['money'];
									$rec['owner_balans_posle']=$user['money'];
									$rec['target']=0;
									$rec['target_login']='Свиток перенос магии';
									$rec['type']=80;
									$rec['sum_kr']=0;
									$rec['sum_ekr']=0;
									$rec['sum_kom']=0;
									$rec['item_id']=get_item_fid($dress);
									$rec['aitem_id']=get_item_fid($olditem);
									$rec['item_name']=$dress['name'];
									$rec['item_count']=1;
									$rec['item_type']=$dress['type'];
									$rec['item_cost']=$dress['cost'];
									$rec['item_dur']=$dress['duration'];
									$rec['item_maxdur']=$dress['maxdur'];
									$rec['item_ups']=$dress['ups'];
									$rec['item_unic']=$dress['unic'];
									$rec['item_incmagic']=$svitok_prot['name'];
									$rec['item_incmagic_count']=$perezar;
									$rec['item_arsenal']=$dress['arsenal_klan'];
									$rec['add_info']=$olditem['name'];
									add_to_new_delo($rec); //юзеру

								echo "<font color=red><b>Свиток \"".$svitok_prot['name']."\" удачно перенесен из  \"".$olditem['name']."\"  в \"".$dress['name']."\"<b></font>";
								$bet=1;
								$sbet = 1;

							}
						}
				  }
				  else
				 {
				 	echo "<font color=red><b>Перенос данного свитка не возможен... Обратитесь к паладинам.<b></font>";

					$_SESSION['scroll'] = null;
				 }
			}
		}

	} else
	{

		echo "<font color=red><b>Cвиток рассыпался в ваших руках...<b></font>";
		$bet=1;
		$_SESSION['scroll'] = null;
	}
}
?>
