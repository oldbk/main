#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php"; //CAPLITAL CITY ONLY
if( !lockCreate("cron_auc_job") ) {
    exit("Script already running.");
}


	function MyDie() {
		lockDestroy("cron_auc_job");
		die();
	}

	function CronAuction() {
		$auctaxsell = 0.1; // налог после продажи - 10%
		$auctionid = 445;

		// выбираем все вещи у которых закончился аукцион
		mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('
			SELECT auc.id AS aucid, auc.itemid AS aucitemid, auc.owner AS aucowner, auc.endtime AS aucendtime, auc.newowner AS aucnewowner, auc.rate AS aucrate, auc.clan_id AS aucclan_id, auc.repcost as aucrepcost, auc.blic as aucblic , inv.* FROM oldbk.`auction` AS `auc`
			LEFT JOIN oldbk.`inventory` AS inv
			ON auc.itemid = inv.id
			WHERE endtime <= '.time().' FOR UPDATE
		') or mydie();

		while($item = mysql_fetch_assoc($q)) {
			$item_need_update=''; $add_info=''; $bmes=''; $bmessars='';
			
			if ($item['aucrepcost'] == 1)
			{
			//обработка валютных лотов за репу
					if ($item['aucnewowner'] > 0) 
						{
						// вещь продалась в ауке
						// отсылаем сообщение
						$q2 = mysql_query('SELECT * FROM oldbk.`users` WHERE id = '.$item['aucnewowner']) or mydie();
						$data = mysql_fetch_assoc($q2) or mydie();	
						$wmess='<font color=red>Внимание!</font> На аукционе выиграла Ваша ставка на: "'.htmlspecialchars($item['name'],ENT_QUOTES).'". Аукцион поздравляет Вас с приобретением.';
						cron_send_mess ($data,$wmess) ;
						
						$rec = array();
		  	 			$rec['owner']=$data['id']; 
						$rec['owner_login']=$data['login'];
						$rec['owner_balans_do']=$data['money'];
						$rec['owner_balans_posle']=$data['money'];
						$rec['target']=$auctionid;
						$rec['target_login']="аукцион";
						$rec['type']=334;
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_rep']=$item['aucrate'];						
						$rec['sum_kom']=0;
						$rec['item_id']=get_item_fid($item);
						$rec['item_name']=$item['name'];
						$rec['item_count']=1;
						$rec['item_type']=$item['type'];
						$rec['item_cost']=$item['cost'];
						$rec['item_dur']=$item['duration'];
						$rec['item_maxdur']=$item['maxdur'];
						$rec['item_ups']=$item['ups'];
						$rec['item_unic']=$item['unik'];
						$rec['item_ecost']=$item['ecost'];
						$rec['item_incmagic']=$item['includemagicname'];
						$rec['item_incmagic_count']=$item['includemagicuses'];
						$rec['add_info'] = $item['aucowner'];
						$rec['item_incmagic_id']=$item['includemagic'];
						$rec['item_proto']=$item['prototype'];
						add_to_new_delo($rec); //юзеру
						// бабки не снимаем, они снялись при ставке
						// удаляем запись в ауке
						mysql_query('DELETE FROM oldbk.`auction` WHERE id = '.$item['aucid']) or mydie();
						mysql_query('UPDATE oldbk.`inventory` SET owner = '.$item['aucnewowner'].' , sowner = '.$item['aucnewowner'].'  , present="Аукцион CapitalCity"   WHERE id = '.$item['aucitemid']) or mydie();
						
						if ($data['klan']!="radminion")
						{
						//пишем в лог покупки баксы за какие продалось
						mysql_query("INSERT INTO `oldbk`.`money_out` SET `sdate`='".date("Y-m-d",time()-300)."',`stype`=1,`val`='{$item['ecost']}' ON DUPLICATE KEY UPDATE val=val+'{$item['ecost']}'");
						//пишем репу за какую продалось
						mysql_query("INSERT INTO `oldbk`.`money_out` SET `sdate`='".date("Y-m-d",time()-300)."',`stype`=2,`val`='{$item['aucrate']}' ON DUPLICATE KEY UPDATE val=val+'{$item['aucrate']}'");									
						}
						
						
						}
						else
						{
						//возвращаем для статистики бамажку на служебный ид который стоит в таблице
						mysql_query('UPDATE oldbk.`inventory` SET owner = '.$item['aucowner'].' WHERE id = '.$item['aucitemid']) or mydie();					
						}
			}
			else if ($item['aucrepcost'] == 2)
			{
			//аук за ваучеры
						if ($item['aucnewowner'] > 0) 
						{
						// вещь продалась в ауке
						$q2 = mysql_query('SELECT * FROM oldbk.`users` WHERE id = '.$item['aucnewowner']) or mydie();
						$data = mysql_fetch_assoc($q2) or mydie();	
						$wmess='<font color=red>Внимание!</font> На аукционе выиграла Ваша ставка на: "'.htmlspecialchars($item['name'],ENT_QUOTES).'". Аукцион поздравляет Вас с приобретением.';
						cron_send_mess ($data,$wmess) ;
						
						$rec = array();
		  	 			$rec['owner']=$data['id']; 
						$rec['owner_login']=$data['login'];
						$rec['owner_balans_do']=$data['money'];
						$rec['owner_balans_posle']=$data['money'];
						$rec['target']=$auctionid;
						$rec['target_login']="аукцион";
						$rec['type']=353;
						$rec['sum_kr']=0;
						$rec['sum_ekr']=$item['aucrate'];
						$rec['sum_rep']=0;						
						$rec['sum_kom']=0;
						$rec['item_id']=get_item_fid($item);
						$rec['item_name']=$item['name'];
						$rec['item_count']=1;
						$rec['item_type']=$item['type'];
						$rec['item_cost']=$item['cost'];
						$rec['item_dur']=$item['duration'];
						$rec['item_maxdur']=$item['maxdur'];
						$rec['item_ups']=$item['ups'];
						$rec['item_unic']=$item['unik'];
						$rec['item_ecost']=$item['ecost'];
						$rec['item_incmagic']=$item['includemagicname'];
						$rec['item_incmagic_count']=$item['includemagicuses'];
						$rec['add_info'] = $item['aucowner'];
						$rec['item_incmagic_id']=$item['includemagic'];
						$rec['item_proto']=$item['prototype'];
						add_to_new_delo($rec); //юзеру
						// бабки не снимаем, они снялись при ставке
						// удаляем запись в ауке
						mysql_query('DELETE FROM oldbk.`auction` WHERE id = '.$item['aucid']) or mydie();
						mysql_query('UPDATE oldbk.`inventory` SET owner = '.$item['aucnewowner'].' , sowner = '.$item['aucnewowner'].'  , present="Аукцион CapitalCity"   WHERE id = '.$item['aucitemid']) or mydie();
						}
						else
						{
						//не продалась - возврат - хозяину-Админу или боту  ид451
						mysql_query('UPDATE oldbk.`inventory` SET owner = '.$item['aucowner'].' WHERE id = '.$item['aucitemid']) or mydie();					
						}
			}
			else
			{
			//простой аук
			
				if ($item['aucnewowner'] > 0) {
				// вещь продалась в ауке, владельцу деньги, покупателю стулья :)

				// владельцу 
				$tosellertax = round($item['aucrate'] * $auctaxsell,2);
				$toseller  = round($item['aucrate'] * (1-$auctaxsell),2);

				// отсылаем сообщение
				$q2 = mysql_query('SELECT * FROM oldbk.`users` WHERE id = '.$item['aucowner']) or mydie();
				$data = mysql_fetch_assoc($q2) or mydie();	
				$wmess='<font color=red>Внимание!</font> На аукционе продался лот: "'.htmlspecialchars($item['name'],ENT_QUOTES).'", вам перечислено '.$toseller.' кр. Комиссия аукциона составила: '.$tosellertax.' кр.';
				cron_send_mess ($data,$wmess) ;
				
				if ($item['arsenal_klan'] !='') {
					//выбираем клан казну 
					$c_kazna=mysql_fetch_assoc(mysql_query("select * from oldbk.clans_kazna where clan_id=(select id from oldbk.clans where short='{$item['arsenal_klan']}')"));
					if ($c_kazna[clan_id] > 0) {
						//есть казна
						// добавляем денег
						mysql_query('UPDATE oldbk.`clans_kazna` SET kr = kr + '.$toseller.' WHERE clan_id = '.$c_kazna[clan_id]) or mydie();
						//пишем в казну 
						$itemdescr = mysql_escape_string('"'.$item['name'].'" id:('.get_item_fid($item).') 1 шт. ['.$item['duration'].'/'.$item['maxdur'].'] [ups:'.$item['ups'].'/unik:'.$item['unik'].'/inc:'.$item['includemagicname'].']');
						$txt='На аукционе продалась '.$itemdescr.' за '.$item['aucrate'].' кр., аукцион перечислил '.$toseller.' кр.';	
						mysql_query("INSERT INTO oldbk.`clans_kazna_log` (`method` ,`ktype`, `clan_id`, `owner`, `target`, `kdate`)  VALUES  ('1','1','{$c_kazna[clan_id]}','{$item['aucowner']}','".$txt."','".time()."');");
				    
						$item_need_update=' , arsenal_klan="" , arsenal_owner="" ';

					        //new_delo
						$rec = array();
		  	 			$rec['owner']=$data[id]; 
						$rec['owner_login']=$data[login];
						$rec['owner_balans_do']=$data['money'];
						$rec['owner_balans_posle']=$data['money'];
						$rec['target']=$auctionid;
						$rec['target_login']="аукцион";
						$rec['type']=224; // продал на аукционе вещь, бабки ушли в клан казну
						$rec['sum_kr']=$toseller;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=$tosellertax;
						$rec['item_id']=get_item_fid($item);
						$rec['item_name']=$item['name'];
						$rec['item_count']=1;
						$rec['item_type']=$item['type'];
						$rec['item_cost']=$item['cost'];
						$rec['item_dur']=$item['duration'];
						$rec['item_maxdur']=$item['maxdur'];
						$rec['item_ups']=$item['ups'];
						$rec['item_unic']=$item['unik'];
						$rec['item_incmagic']=$item['includemagicname'];
						$rec['item_incmagic_count']=$item['includemagicuses'];
						$rec['item_arsenal']=$data['klan'];
						$rec['add_info'] = $item['aucnewowner'];
						$rec['item_incmagic_id']=$item['includemagic'];
						$rec['item_proto']=$item['prototype'];
						$rec['item_sowner']=($item['sowner']>0?1:0);
						add_to_new_delo($rec); //юзеру
				    
						//удаляем запись из индекса арсенала
						mysql_query("DELETE FROM oldbk.clans_arsenal WHERE id_inventory='{$item['id']}'");
						//пишем в лог казны
						mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$data[klan]}','{$data[id]}','{$txt}','".time()."')");
					}
				 	// шмотка арсенальная деньги в казну клана
				} else {
				 	//личная шмотка
					// добавляем денег
					if ($data[id_city]==0) {
						mysql_query('UPDATE oldbk.`users` SET money = money + '.$toseller.' WHERE id = '.$item['aucowner']) or mydie();
					} elseif ($data[id_city]==1) {
						mysql_query('UPDATE avalon.`users` SET money = money + '.$toseller.' WHERE id = '.$item['aucowner']) or mydie();					
					} elseif ($data[id_city]==2) {
						mysql_query('UPDATE angels.`users` SET money = money + '.$toseller.' WHERE id = '.$item['aucowner']) or mydie();					
					}

				        //new_delo
					$rec = array();
	  	 			$rec['owner']=$data[id]; 
					$rec['owner_login']=$data[login];
					$rec['owner_balans_do']=$data['money'];
					$rec['owner_balans_posle']=$data['money']+$toseller;
					$rec['target']=$auctionid;
					$rec['target_login']="аукцион";
					$rec['type']=225; // продал на аукционе вещь, бабки себе
					$rec['sum_kr']=$toseller;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=$tosellertax;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unik'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['add_info'] = $item['aucnewowner'];
					$rec['item_incmagic_id']=$item['includemagic'];
					$rec['item_proto']=$item['prototype'];
					$rec['item_sowner']=($item['sowner']>0?1:0);
					add_to_new_delo($rec); //юзеру
				}
			

				// покупателю
				// отсылаем сообщение
				$q2 = mysql_query('SELECT * FROM oldbk.`users` WHERE id = '.$item['aucnewowner']) or mydie();
				$data = mysql_fetch_assoc($q2) or mydie();	
				
				$itemdescr = mysql_escape_string('"'.$item['name'].'" id:('.get_item_fid($item).') 1 шт. ['.$item['duration'].'/'.$item['maxdur'].'] [ups:'.$item['ups'].'/unik:'.$item['unik'].'/inc:'.$item['includemagicname'].']');
				$sexi[0]='a';
				$sexi[1]='';				

				// возвращаем новому владельцу				
				if ($item[aucclan_id] > 0) {
					//покупается в арсенал
					// ищем имя клана
					$toclan=mysql_fetch_assoc(mysql_query("select * from oldbk.clans where id='{$item[aucclan_id]}' "));
					mysql_query('UPDATE oldbk.`inventory` SET owner = 22125 , arsenal_klan="'.$toclan['short'].'" , arsenal_owner="1"  WHERE id = '.$item['aucitemid']) or mydie();
				
					//пишем в лог нового арсенала

					//делаем запись в индекс
					//1- т.к. покупка за казну
					mysql_query("INSERT INTO oldbk.clans_arsenal (id_inventory, klan_name, owner_original) 	VALUES 	('{$item[id]}','{$data['klan']}','1')");

					//пишем в лог казны
					$txt='"'.mysql_escape_string($data['login']).' выиграл'.$sexi[$user[sex]].' ставку на аукционе CapitalCity: '.$itemdescr.' за '.$item['aucrate'].' кр."';
					mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$data[klan]}','{$data[id]}','{$txt}','".time()."')");

					$wmess='<font color=red>Внимание!</font> На аукционе выиграла Ваша ставка для клана <b>'.$toclan['short'].'</b> на: "'.htmlspecialchars($item['name'],ENT_QUOTES).'". Аукцион поздравляет Вас с приобретением.';
					cron_send_mess ($data,$wmess) ;

				} else {
					// получатель в кепитале просто апдейт
					mysql_query('UPDATE oldbk.`inventory` SET owner = '.$item['aucnewowner'].' '.$item_need_update.'   WHERE id = '.$item['aucitemid']) or mydie();

                                        if ($item['aucblic'] > 0 && $item['aucblic'] == $item['aucrate']) {
						$wmess='<font color=red>Внимание!</font> Лот "'.htmlspecialchars($item['name'],ENT_QUOTES).'" успешно куплен по блиц-цене. Поздравляем вас с приобретением.';
					} else {
						$wmess='<font color=red>Внимание!</font> На аукционе выиграла Ваша ставка на: "'.htmlspecialchars($item['name'],ENT_QUOTES).'". Аукцион поздравляет Вас с приобретением.';
					}
					cron_send_mess ($data,$wmess) ;

				}
				

			        //new_delo
				$rec = array();
  	 			$rec['owner']=$data[id]; 
				$rec['owner_login']=$data[login];
				$rec['owner_balans_do']=$data['money'];
				$rec['owner_balans_posle']=$data['money'];
				$rec['target']=$auctionid;
				$rec['target_login']="аукцион";
				if ($item[aucclan_id] > 0) {
					$rec['type']=226; // выиграл вещь на аукционе в арсенал
				} else {
					$rec['type']=227; // выиграл вещь на аукционе
				}
				$rec['sum_kr']=$item['aucrate'];
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=get_item_fid($item);
				$rec['item_name']=$item['name'];
				$rec['item_count']=1;
				$rec['item_type']=$item['type'];
				$rec['item_cost']=$item['cost'];
				$rec['item_dur']=$item['duration'];
				$rec['item_maxdur']=$item['maxdur'];
				$rec['item_ups']=$item['ups'];
				$rec['item_unic']=$item['unik'];
				$rec['item_incmagic']=$item['includemagicname'];
				$rec['item_incmagic_count']=$item['includemagicuses'];
				if ($item[aucclan_id] > 0) { $rec['item_arsenal']=$toclan['short']; }
				$rec['add_info'] = $item['aucowner'];
				$rec['item_incmagic_id']=$item['includemagic'];
				$rec['item_proto']=$item['prototype'];
				$rec['item_sowner']=($item['sowner']>0?1:0);
				add_to_new_delo($rec); //юзеру

				// бабки не снимаем, они снялись при ставке

				// удаляем запись в ауке
				mysql_query('DELETE FROM oldbk.`auction` WHERE id = '.$item['aucid']) or mydie();
			} else {
				// вещь возвращаем хозяину
				
				if ($item['arsenal_klan'] !='') {
					//возвращаем в арсенал
					mysql_query('UPDATE oldbk.`inventory` SET owner =22125  WHERE id = '.$item['aucitemid']) or mydie();
					$bmes='( в арсенал )';
				} else {
					mysql_query('UPDATE oldbk.`inventory` SET owner = '.$item['aucowner'].' WHERE id = '.$item['aucitemid']) or mydie();					
				}
				
				
					
				// отсылаем сообщение
				$q2 = mysql_query('SELECT * FROM oldbk.`users` WHERE id = '.$item['aucowner']) or mydie();
				$data = mysql_fetch_assoc($q2) or mydie();	
				$wmess='<font color=red>Внимание!</font> Аукцион вернул вам '.$bmes.' "'.htmlspecialchars($item['name'],ENT_QUOTES).'", ваша вещь не продалась.';
				cron_send_mess ($data,$wmess) ;

			        //new_delo
				$rec = array();
  	 			$rec['owner']=$data[id]; 
				$rec['owner_login']=$data[login];
				$rec['owner_balans_do']=$data['money'];
				$rec['owner_balans_posle']=$data['money'];
				$rec['target']=$auctionid;
				$rec['target_login']="аукцион";
				if ($item[aucclan_id] > 0) {
					$rec['type']=228; // вернулась вещь в арсенал изза непродажи
				} else {
					$rec['type']=229; // вернулась вещь владельцу
				}
				$rec['sum_kr']=0;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=get_item_fid($item);
				$rec['item_name']=$item['name'];
				$rec['item_count']=1;
				$rec['item_type']=$item['type'];
				$rec['item_cost']=$item['cost'];
				$rec['item_dur']=$item['duration'];
				$rec['item_maxdur']=$item['maxdur'];
				$rec['item_ups']=$item['ups'];
				$rec['item_unic']=$item['unik'];
				$rec['item_incmagic']=$item['includemagicname'];
				$rec['item_incmagic_count']=$item['includemagicuses'];
				if ($item[aucclan_id] > 0) { $rec['item_arsenal']=$data['klan']; }
				$rec['add_info'] = '';
				$rec['item_incmagic_id']=$item['includemagic'];
				$rec['item_proto']=$item['prototype'];
				$rec['item_sowner']=($item['sowner']>0?1:0);
				add_to_new_delo($rec); //юзеру
				
				// удаляем запись в ауке
				mysql_query('DELETE FROM oldbk.`auction` WHERE id = '.$item['aucid']) or mydie();
			}
		   }
		}                             
		mysql_query('COMMIT') or mydie();		
	}



	CronAuction();


lockDestroy("cron_auc_job");
?>