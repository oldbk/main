<?
//print_r($_POST);
//print_r($_GET);
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}

$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner = '{$_SESSION['uid']}'  and  type=113010  "));
	if (($get_test_baff[id] > 0) )
	{
		err('На персонаже уже есть эффект от этой коллекции!');
	}
else
{
$che=(int)($_GET['use']);
$item = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='{$che}' and owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$user['id']}') AND type=99 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 ;"));

if (($item['id']>0) and  ($item['prototype']==113010)) //Футляр коллекции №3';
	{
		//отработка
		$get_all_cards=mysql_query("select * from oldbk.inventory where owner = '{$_SESSION['uid']}'  and prototype>=113001 and prototype<=113008 group by prototype");
		$ccids=array();
		$ckol=0;
		while($rcards = mysql_fetch_assoc($get_all_cards)) 
			{
			$ccids[]=$rcards['id'];
			$ckol++;
			}
		
		if ($ckol==8)
			{
				mysql_query("DELETE FROM oldbk.inventory where owner = '{$_SESSION['uid']}' and id in (".implode(",",$ccids).") ");
				if (mysql_affected_rows()==8)
				{
				//собралась
				        $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.shop WHERE `id` = '113000' LIMIT 1;"));
					if ($dress['id']>0)
							{
							//кидаем собраную колоду
							/*$dress['goden']=90; //90 дней
							$dress['dategoden']=$dress['goden']*24*60*60+time();
							*/
							//include("cards_config.php");
							include "/www/capitalcity.oldbk.com/cards_config.php";
							
							$dress['dategoden'] = $coll3_end; 
							$dress['goden'] = round(($dress['dategoden']-time())/60/60/24); if ($dress['goden']<1) {$dress['goden']=1;}
							
							if (mysql_query("INSERT INTO oldbk.`inventory`
							(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`letter`
							)
							VALUES
							('{$dress['id']}','{$user['id']}','0','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
							'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$dress['dategoden']."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','Удача','0','0','{$dress['group']}','{$user['id_city']}','{$dress['letter']}'
							) ;") )
						     	{
						     		$dress['id']=mysql_insert_id();
 	      	            

												// пишем в дело
								 				$rec=array();
								 				$rec['owner']=$user['id'];
												$rec['owner_login']=$user['login'];
												$rec['owner_balans_do']=$user['money'];
												$rec['owner_balans_posle']=$user['money'];
								 				$rec['target'] = 0;
												$rec['target_login'] = 'Коллекции';
												$rec['type']=1130;
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_kom']=0;
												$rec['item_count']=0;
												$rec['item_id']=get_item_fid($dress);
												$rec['item_name']=$dress['name'];
												$rec['item_count']=1;
												$rec['item_type']=$dress['type'];
												$rec['item_cost']=$dress['cost'];
												$rec['item_dur']=$dress['duration'];
												$rec['item_maxdur']=$dress['maxdur'];
												$rec['add_info']="Коллекция \"Зимняя\"! Из Карт:(".implode(",",$ccids).") Футляр:".$che;
												add_to_new_delo($rec); //юзеру
								 				$rec=array();
				 								//пишем в эффекты
				 						//Собранная коллекция дает право обменивать статуи у торговца с наценкой 20%
				 						mysql_query("INSERT INTO `effects` SET `type`='113010',`name`='{$dress['name']}', add_info='1'  ,`time`='".$dress['dategoden']."', `owner`='{$user[id]}' ");
										if (mysql_affected_rows()>0)
											{
											echo "<font color=red><b>Поздравляем! Удачно собрана коллекция \"Зимняя\"!<b></font> ";
											$bet=1;
											$sbet = 1;
											}
							}
							}
				}
			}
			else
			{
			echo "<font color=red><b>Недостаточно элементов для активации коллекции!<b></font> ";
			}
	}
}

?>