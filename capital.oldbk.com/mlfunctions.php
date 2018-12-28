<?php

	$mlqallcount = 30;

	function SetNewQuest($user,$id,$addinfo = "") {
		$q = mysql_query('INSERT INTO oldbk.map_quests (owner,q_id,step,addinfo) VALUES ('.$user['id'].','.$id.',0,"'.mysql_real_escape_string($addinfo).'")');
		if ($q === FALSE) return false;

		// в дело	                                                                                              
		$rec = array();
		$rec['owner']=$user[id];
		$rec['owner_login']=$user[login];
		$rec['owner_balans_do']=$user['money'];
		$rec['owner_balans_posle']=$user['money'];
		$rec['target']="0";
		$rec['target_login'] = "Загород квест";
		$rec['add_info'] = $id;
		$rec['type']=270; // получил квест
		if(add_to_new_delo($rec) === FALSE) return FALSE;

		return true;
	}

	function QuestDie() {
		header("Location: main.php"); 
		die('<script>location.href = "main.php";</script>');
	}

	function SetQuestStep($user,$id,$step) {
		$q = mysql_query('UPDATE oldbk.map_quests SET step = '.$step.' WHERE q_id = '.$id.' AND owner = '.$user['id']);
		if ($q === FALSE) return false;
		return true;
	}
	function UnsetQuest($user) {
		global $mlqallcount;

		$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
		if (mysql_num_rows($q) > 0) {
			$questexist = mysql_fetch_assoc($q);
			if ($questexist === FALSE) return FALSE;
		} else {
			return true;
		}

		// в дело	                                                                                              
		$rec = array();
		$rec['owner']=$user[id];
		$rec['owner_login']=$user[login];
		$rec['owner_balans_do']=$user['money'];
		$rec['owner_balans_posle']=$user['money'];
		$rec['target']="0";
		$rec['target_login'] = "Загород квест";
		$rec['add_info'] = $questexist['q_id'];
		$rec['type']=271; // закончил квест
		if(add_to_new_delo($rec) === FALSE) return FALSE;


		$q = mysql_query('DELETE FROM oldbk.map_quests WHERE owner = '.$user['id']);
		if ($q === FALSE) return false;

		$timepenalty = 60*60*20;
		
		$bonuseffect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9107' LIMIT 1;")); 
		//9107=>'Уменьшение таймаута на квест в загороде на 20%');
		if ($bonuseffect['id']>0)
			{
			$timepenalty-=(int)($timepenalty*$bonuseffect['add_info']);
			}



		$q = mysql_query('INSERT INTO `map_var` (`owner`,`var`,`val`) 
					VALUES(
						'.$user['id'].',
						"cango",
						'.(time()+$timepenalty).'
					) 
					ON DUPLICATE KEY UPDATE
						`val` = '.(time()+$timepenalty)
		);
		if ($q === FALSE) return FALSE;

		$q = mysql_query('SELECT * FROM map_qvar WHERE var = "qcomplete" AND owner = '.$user['id']);
		if ($q === FALSE) return false;

		$qv = false;
		if (mysql_num_rows($q) > 0) {
			$qv = mysql_fetch_assoc($q);
		}

		$q = mysql_query('INSERT INTO `map_qvar` (`owner`,`var`,`val`) 
					VALUES(
						'.$user['id'].',
						"qcomplete",
						'.$questexist['q_id'].'
					) 
					ON DUPLICATE KEY UPDATE
						`val` = CONCAT(val,"/'.$questexist['q_id'].'")'
		);
		if ($q === FALSE) return FALSE;

		// обнуляем тут счётчик квестов
		if ($qv !== FALSE) {
			$qt = $qv['val'].'/'.$questexist['q_id'];
			$qt = explode('/',$qt);
			if (count($qt) >= $mlqallcount) {
				$q = mysql_query('DELETE FROM map_qvar WHERE var = "qcomplete" AND owner = '.$user['id']);
				if ($q === FALSE) return false;
			
				//колода №2 
	  			$q = mysql_query("select count(id) as kol from inventory where owner='{$user['id']}' and prototype=112010  ");				
				if ($q === FALSE) return false;
			  	$get_test_items=mysql_fetch_array($q);
			  	if (!($get_test_items['kol']>0)) 
			  	{
			  		// делаем дроп футляра
					$q = mysql_query("SELECT * FROM oldbk.shop WHERE `id` = '112010' LIMIT 1;");
					if ($q === FALSE) return false;	
											        
				        $dress = mysql_fetch_array($q);
					if ($dress['id']>0) 
					{
						//кидаем собраную колоду
						$dress['goden'] = 0;
						$dress['dategoden'] = '';
											
						$q = mysql_query("INSERT INTO oldbk.`inventory`
								(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`letter`
								)
								VALUES
								('{$dress['id']}','{$user['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
								'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$dress['dategoden']."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','Лабиринт Хаоса','0','0','{$dress['group']}','{$user['id_city']}','{$dress['letter']}'
							) ;");
					     	if ($q === FALSE) return false;	
					     	
					     		$dress['id']=mysql_insert_id();
							// пишем в дело
			 				$rec=array();
			 				$rec['owner']=$user['id'];
							$rec['owner_login']=$user['login'];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money'];
			 				$rec['target'] = 0;
							$rec['target_login'] = 'Коллекции';
							$rec['type']=1112;
							$rec['sum_kr']=0;
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['item_count']=0;
							$rec['item_id']='cap'.$dress['id'];
							$rec['item_name']=$dress['name'];
							$rec['item_count']=1;
							$rec['item_type']=$dress['type'];
							$rec['item_cost']=$dress['cost'];
							$rec['item_dur']=$dress['duration'];
							$rec['item_maxdur']=$dress['maxdur'];
							if(add_to_new_delo($rec) === FALSE) return FALSE;
							if (addchp ('<font color=red>Внимание!</font> Вы получили: <b>'.$dress['name'].'</b>','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']) === FALSE) return FALSE;
						
					}
				}
			
			}
		}

		return true;
	}
	function UnsetQA() {
		unset($_GET['quest']);
		unset($_GET['qaction']);
		return true;
	}
	function QItemExists($user,$iid) {
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype = '.$iid);
		if (mysql_num_rows($q) > 0) {
			return true;
		}
		return false;
	}

	function QItemExistsInfo($user,$iid) {
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype = '.$iid);
		if (mysql_num_rows($q) > 0) {
			return mysql_fetch_assoc($q);
		}
		return false;
	}


	function QItemExistsID($user,$iid) {
		$toret = array();
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype = '.$iid);		if (mysql_num_rows($q) > 0) {
			while($i = mysql_fetch_assoc($q)) {
				$toret[] = $i['id'];
			}
			if (!count($toret)) return false;
			return $toret;
		}
		return false;
	}

	function QItemCount($user,$iid) {		
		$q = mysql_query('SELECT count(*) AS ccount FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype = '.$iid);
		$q = mysql_fetch_assoc($q);
		return $q['ccount'];
	}


	function QItemExistsCount($user,$iid,$count) {		
		$q = mysql_query('SELECT count(*) AS ccount FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype = '.$iid);
		$q = mysql_fetch_assoc($q);
		if ($q['ccount'] < $count) {
			return true;
		}
		return false;
	}

	function QItemExistsCountIDP($user,$proto,$count) {		
		$toret = array();
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype IN ('.implode(",",$proto).')');
		$i = 0;
		if (mysql_num_rows($q) >= $count) {
			while($it = mysql_fetch_assoc($q)) {
				$toret[] = $it['id'];
				$i++;
				if ($i == $count) break;
			}
			if (!count($toret)) return false;
			return $toret;
		}
		return false;
	}

                                               
	function QItemExistsCountID($user,$iid,$count) {		
		$toret = array();
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND arsenal_klan = "" AND prototype = '.$iid.' AND includemagic = 0');
		$i = 0;
		if (mysql_num_rows($q) >= $count) {
			while($it = mysql_fetch_assoc($q)) {
				$toret[] = $it['id'];
				$i++;
				if ($i == $count) break;
			}
			if (!count($toret)) return false;
			return $toret;
		}
		return false;
	}

	function UpdateQuestInfo($user,$id,$addinfo) {
		$q = mysql_query('UPDATE oldbk.map_quests SET addinfo = "'.mysql_real_escape_string($addinfo).'" WHERE q_id = '.$id.' AND owner = '.$user['id']);
		if ($q === FALSE) return false;
		return true;
	}
	function PutQItemTo($u,$who,$todel) {
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE id IN ('.implode(",",$todel).') AND owner = '.$u['id']);
		if ($q === FALSE) return false;

		$its = array();
		while($it = mysql_fetch_assoc($q)) {
			$its[$it['id']] = $it;
		}

		if (is_array($todel)) {
			$q = mysql_query('DELETE FROM oldbk.inventory WHERE id IN ('.implode(",",$todel).') AND owner = '.$u['id']);
			if ($q === FALSE) return false;
		}

		while(list($k,$dress) = each($its)) {
			$rec = array();
		   	$rec['owner']=$u[id];
			$rec['owner_login']=$u[login];
			$rec['owner_balans_do']=$u['money'];
			$rec['owner_balans_posle']=$u['money'];
			$rec['target_login']=$who;
			$rec['type']= 256;
			$rec['item_id']=get_item_fid(array("id" => $dress['id'], "idcity" => $u['id_city']));
			$rec['item_name']=$dress['name'];
			$rec['item_count']=1;
			$rec['item_type']=$dress['type'];
			$rec['item_cost']=$dress['cost'];
			$rec['item_dur']=$dress['duration'];
			$rec['item_maxdur']=$dress['maxdur'];
			$rec['item_proto']=$dress['id'];
			$rec['item_arsenal']='';
			if(add_to_new_delo($rec) === FALSE) return false;
		}
		return true;
	}

	function PutQItem($u,$iid,$who, $goden = 0, $todel = array(), $dtype = 0, $shop = "shop",$maxdur = 0) {
		$dress = mysql_query_cache('SELECT * FROM oldbk.`'.$shop.'` WHERE id = '.$iid,false,1*3600);
		if ($dress === FALSE || !count($dress)) return false;
		$dress = $dress[0];


		if ($goden == 0 && $dress['goden'] > 0) {
			$goden = $dress['goden']*24*60*60;
			$godendn = $dress['goden'];
		} elseif ($goden > 0) {
			if ($goden > 1000) {
				$godendn = 1;
			} else {
				$godendn = $goden;
				$goden = $goden*24*60*60;
			}
		} else {
			$godendn = 0;
		}

		if ($maxdur > 0) $dress['maxdur'] = $maxdur;

		//TEMP by Bred- шоб свиток падал екровый
		if ($shop == "shop") {
			$dress['ecost'] = 0;
		}

		$q = mysql_query('INSERT INTO oldbk.`inventory`
				(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`duration`,`maxdur`,`isrep`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
				`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
				,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
				`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`img_big`,`getfrom`
				)
						VALUES	(
							"'.mysql_real_escape_string($who).'",
							'.$dress['id'].',
							'.$u['id'].',
							"'.mysql_real_escape_string($dress['name']).'",
							'.$dress['type'].',
							'.$dress['massa'].',
							'.$dress['cost'].',
							'.$dress['ecost'].',							
							"'.mysql_real_escape_string($dress['img']).'",
							'.$dress['duration'].',
							'.$dress['maxdur'].',
							'.$dress['isrep'].',
							'.$dress['gsila'].',
							'.$dress['glovk'].',
							'.$dress['ginta'].',
							'.$dress['gintel'].',
							'.$dress['ghp'].',
							'.$dress['gnoj'].',
							'.$dress['gtopor'].',
							'.$dress['gdubina'].',
							'.$dress['gmech'].',
							'.$dress['gfire'].',
							'.$dress['gwater'].',
							'.$dress['gair'].',
							'.$dress['gearth'].',
							'.$dress['glight'].',
							'.$dress['ggray'].',
							'.$dress['gdark'].',
							'.$dress['needident'].',
							'.$dress['nsila'].',
							'.$dress['nlovk'].',
							'.$dress['ninta'].',
							'.$dress['nintel'].',
							'.$dress['nmudra'].',
							'.$dress['nvinos'].',
							'.$dress['nnoj'].',
							'.$dress['ntopor'].',
							'.$dress['ndubina'].',
							'.$dress['nmech'].',
							'.$dress['nfire'].',
							'.$dress['nwater'].',
							'.$dress['nair'].',
							'.$dress['nearth'].',
							'.$dress['nlight'].',
							'.$dress['ngray'].',
							'.$dress['ndark'].',
							'.$dress['mfkrit'].',
							'.$dress['mfakrit'].',
							'.$dress['mfuvorot'].',
							'.$dress['mfauvorot'].',
							'.$dress['bron1'].',
							'.$dress['bron2'].',
							'.$dress['bron3'].',
							'.$dress['bron4'].',
							'.$dress['maxu'].',
							'.$dress['minu'].',
							'.$dress['magic'].',
							'.$dress['nlevel'].',
							'.$dress['nalign'].',
							"'.(($goden > 0) ? (time()+$goden) : 0).'",
							'. $godendn.',
							'.$dress['razdel'].',
							"0",
							'.$dress['group'].',"'.mysql_real_escape_string($dress['letter']).'",0,0,0,0,"",0,0,0,"0","'.mysql_real_escape_string($dress['img_big']).'",40
					)
		');
		if ($q === FALSE) return false;

		$id = mysql_insert_id();

		if (is_array($todel) && count($todel)) {
			$q = mysql_query('DELETE FROM oldbk.inventory WHERE id IN ('.implode(",",$todel).')');
			if ($q === FALSE) return false;
		}

		$rec = array();
	   	$rec['owner']=$u[id];
		$rec['owner_login']=$u[login];
		$rec['owner_balans_do']=$u['money'];
		$rec['owner_balans_posle']=$u['money'];
		$rec['target_login']=$who;
		if ($dtype == 0) {
			$dtype = 253;
		}
		$rec['type']= $dtype;
		$rec['item_id']=get_item_fid(array("id" => $id, "idcity" => $u['id_city']));
		if (is_array($todel) && count($todel)) {
			$dressid = "";
			foreach ($todel as $k=>$v) {
				$z['id']=$v;
				$z['idcity']=$u['id_city'];
				$dressid .= get_item_fid($z).",";
			}
			$rec['aitem_id'] = substr($dressid,0,strlen($dressid)-1);
		} else {
			$rec['aitem_id']="";
		}
		$rec['item_name']=$dress['name'];
		$rec['item_count']=1;
		$rec['item_type']=$dress['type'];
		$rec['item_cost']=$dress['cost'];
		$rec['item_dur']=$dress['duration'];
		$rec['item_maxdur']=$dress['maxdur'];
		$rec['item_proto']=$dress['id'];
		$rec['item_arsenal']='';
		if(add_to_new_delo($rec) === FALSE) return FALSE;
		
		
		if (addchp("<font color=red>Внимание!</font> Вы подняли <b>«".link_for_item($dress)."»</b>.",'{[]}'.$u['login'].'{[]}',$u['room'],$u['id_city'])=== FALSE) return FALSE;
		
		return true;
	}

	function StartQuestBattleCount($user,$bid, $count, $timeout = 5, $ext = array()) {
		if ($user['hp'] < 3) return false;

		// если бой на карте, всем меняем статус
		$q = mysql_query('select DISTINCT(id_grup) as `mapgroup` FROM oldbk.users WHERE id = '.$user['id'].' AND id_grup != 0');
		if ($q === FALSE) return false;

		$tids = "";
		while($t = mysql_fetch_assoc($q)) {
			$tids .= $t['mapgroup'].",";
		}
		if (strlen($tids)) {
			$tids = substr($tids,0,strlen($tids)-1);
			$q = mysql_query('UPDATE oldbk.`map_groups` SET status = 2 WHERE id IN ('.$tids.')');
			if ($q === FALSE) return false;
		}
		$tids = "";

		// для начала создаём клонов
		$botids = "";
		$bothist = "";
		$bothista = "";

		$BOT = mysql_query_cache('SELECT * FROM oldbk.`users` WHERE `id` = '.$bid, false, 24*3600);
		if ($BOT === FALSE || !count($BOT)) return FALSE;
		$BOT = $BOT[0];

		$BOT['protid'] = $BOT['id'];
		require_once('fsystem.php');
		$BOT_items = load_mass_items_by_id($BOT);

		if (count($ext)) {
			while(list($k,$v) = each($ext)) {
				if (isset($BOT[$k]))
					$BOT[$k] = $v;

				if (isset($BOT_items[$k]))
					$BOT_items[$k] = $v;

			}
		}

		$BOT_items = CoefMaker($BOT_items,$user['level']);

		$BOT['level'] = $user['level'];

		for ($i = 0; $i < $count; $i++) {
			$q = mysql_query('INSERT INTO oldbk.`users_clons` SET 
					`login` = "'.$BOT['login'].'",
					`sex` = "'.$BOT['sex'].'",
					`level` = "'.$BOT['level'].'",
					`align` = "'.$BOT['align'].'",
					`klan` = "'.$BOT['klan'].'",
					`sila` = "'.$BOT['sila'].'",
					`lovk` = "'.$BOT['lovk'].'",
					`inta` = "'.$BOT['inta'].'",
					`vinos` = "'.$BOT['vinos'].'",
					`intel` = "'.$BOT['intel'].'",
					`mudra` = "'.$BOT['mudra'].'",
					`duh` = "'.$BOT['duh'].'",
					`bojes` = "'.$BOT['bojes'].'",
					`noj` = "'.$BOT['noj'].'",
					`mec` = "'.$BOT['mec'].'",
					`topor` = "'.$BOT['topor'].'",
					`dubina` = "'.$BOT['dubina'].'",
					`maxhp` = "'.$BOT['maxhp'].'",
					`hp` = "'.$BOT['maxhp'].'",
					`maxmana` = "'.$BOT['maxmana'].'",
					`mana` = "'.$BOT['mana'].'",
					`sergi` = "'.$BOT['sergi'].'",
					`kulon` = "'.$BOT['kulon'].'",
					`perchi` = "'.$BOT['perchi'].'",
					`weap` = "'.$BOT['weap'].'",
					`bron` = "'.$BOT['bron'].'",
					`r1` = "'.$BOT['r1'].'",
					`r2` = "'.$BOT['r2'].'",
					`r3` = "'.$BOT['r3'].'",
					`helm` = "'.$BOT['helm'].'",
					`shit` = "'.$BOT['shit'].'",
					`boots` = "'.$BOT['boots'].'",
					`nakidka` = "'.$BOT['nakidka'].'",
					`rubashka` = "'.$BOT['rubashka'].'",													
					`shadow` = "'.$BOT['shadow'].'",
					`battle` = 0,
					`bot` = 1,
					`id_user` = "'.$BOT['id'].'",
					`at_cost` = "'.$BOT_items['allsumm'].'",
					`kulak1` = 0,
					`sum_minu` = "'.$BOT_items['min_u'].'",
					`sum_maxu` = "'.$BOT_items['max_u'].'",
					`sum_mfkrit` = "'.$BOT_items['krit_mf'].'",
					`sum_mfakrit` = "'.$BOT_items['akrit_mf'].'",
					`sum_mfuvorot` = "'.$BOT_items['uvor_mf'].'",
					`sum_mfauvorot` = "'.$BOT_items['auvor_mf'].'",
					`sum_bron1` = "'.$BOT_items['bron1'].'",
					`sum_bron2` = "'.$BOT_items['bron2'].'",
					`sum_bron3` = "'.$BOT_items['bron3'].'",
					`sum_bron4` = "'.$BOT_items['bron4'].'",
					`ups` = "'.$BOT_items['ups'].'",
					`injury_possible` = 0, 
					`battle_t` = 0, 
					`bot_online` = 0, 
					`bot_room` = 0
			');
			if ($q === FALSE) return false;

			$botids .= mysql_insert_id().",";
			$bothist .= BNewHist($BOT);
			$bothista .= nick_align_klan($BOT).", ";
		}

		$botids = substr($botids,0,strlen($botids)-1);
		$bothista = substr($bothista,0,strlen($bothista)-2);

		// клонов создали, делаем бой
		$q = mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']);
		if ($q === FALSE) return false;
								
		$q = mysql_query('INSERT INTO oldbk.`battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
				VALUES
				(
					"Квестовый бой",
					"",
					"'.$timeout.'",
					"15",
					"0",
					"'.$user['id'].'",
					"'.str_replace(",",";",$botids).'",
					"'.time().'",
					"'.time().'",
					3,
					"'.mysql_real_escape_string(BNewHist($user)).'",
					"'.mysql_real_escape_string($bothist).'",
					"0","0"
				)
		');
		if ($q === FALSE) return false;
		
		$id = mysql_insert_id();
		
		// теперь обновляем себя и противника что мы в бою
		$q = mysql_query('UPDATE oldbk.`users_clons` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$botids.')');
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$user['id']);
		if ($q === FALSE) return false;

//		$p2 = '<b>'.nick_align_klan($user).'</b> и <b>'.($bothista).'</b>';
//		addlog($id,'Часы показывали <span class=date>'.date("Y.m.d H.i").'</span>, когда '.$p2.' бросили вызов друг другу. <BR>');
		addlog($id,"!:S:".time().":".BNewHist($user).":".$bothist."\n");


		$sex = "";
		if (!$BOT['sex']) $sex = 'a';

		// обновляем телу фрейм
		addchp ('<font color=red>Внимание!</font> На вас напал'.$sex.' '.$BOT['login'].'!<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
		echo '<script>location.href="fbattle.php";</script>';
		return true;
	}

	function CoefMaker($bot,$level) {
		$coefs = array(
			6 => 1.5,
			7 => 2,
			8 => 4,
			9 => 6,
			10 => 8,
			11 => 10,
			12 => 13,
			13 => 16,
		);

		if (isset($coefs[$level])) {
			$coef = $coefs[$level];
		} else {
			$coef = max($coefs);
		}

		$bot['min_u'] *= $coef/7; 
		$bot['max_u'] *= $coef/7; 
		$bot['krit_mf'] *= $coef; 
		$bot['akrit_mf'] *= $coef; 
		$bot['uvor_mf'] *= $coef; 
		$bot['auvor_mf'] *= $coef; 
		$bot['bron1'] *= $coef; 
		$bot['bron2'] *= $coef; 
		$bot['bron3'] *= $coef; 
		$bot['bron4'] *= $coef; 

		return $bot;
	}

	function StartQuestBattle($user,$bid, $ext = array(), $timeout = 5, $text = "") {
		if ($user['hp'] < 3) return;

		// если бой на карте, всем меняем статус
		$q = mysql_query('select DISTINCT(id_grup) as `mapgroup` FROM oldbk.users WHERE id = '.$user['id'].' AND id_grup != 0');
		if ($q === FALSE) return false;

		$tids = "";
		while($t = mysql_fetch_assoc($q)) {
			$tids .= $t['mapgroup'].",";
		}
		if (strlen($tids)) {
			$tids = substr($tids,0,strlen($tids)-1);
			$q = mysql_query('UPDATE oldbk.`map_groups` SET status = 2 WHERE id IN ('.$tids.')');
			if ($q === FALSE) return false;
		}
		$tids = "";

	
		// для начала создаём клонов
		$botids = "";
		$bothist = "";

		$BOT = mysql_query_cache('SELECT * FROM oldbk.`users` WHERE `id` = '.$bid,false,24*3600);
		if ($BOT === FALSE || !count($BOT)) return FALSE;
		$BOT = $BOT[0];

		$BOT['protid'] = $BOT['id'];
		require_once('fsystem.php');
		$BOT_items = load_mass_items_by_id($BOT);

		if (count($ext)) {
			while(list($k,$v) = each($ext)) {
				if (isset($BOT[$k]))
					$BOT[$k] = $v;

				if (isset($BOT_items[$k]))
					$BOT_items[$k] = $v;

			}
		}

		$BOT_items = CoefMaker($BOT_items,$user['level']);

		$BOT['level'] = $user['level'];

		$q = mysql_query('INSERT INTO oldbk.`users_clons` SET 
				`login` = "'.$BOT['login'].'",
				`sex` = "'.$BOT['sex'].'",
				`level` = "'.$BOT['level'].'",
				`align` = "'.$BOT['align'].'",
				`klan` = "'.$BOT['klan'].'",
				`sila` = "'.$BOT['sila'].'",
				`lovk` = "'.$BOT['lovk'].'",
				`inta` = "'.$BOT['inta'].'",
				`vinos` = "'.$BOT['vinos'].'",
				`intel` = "'.$BOT['intel'].'",
				`mudra` = "'.$BOT['mudra'].'",
				`duh` = "'.$BOT['duh'].'",
				`bojes` = "'.$BOT['bojes'].'",
				`noj` = "'.$BOT['noj'].'",
				`mec` = "'.$BOT['mec'].'",
				`topor` = "'.$BOT['topor'].'",
				`dubina` = "'.$BOT['dubina'].'",
				`maxhp` = "'.$BOT['maxhp'].'",
				`hp` = "'.$BOT['maxhp'].'",
				`maxmana` = "'.$BOT['maxmana'].'",
				`mana` = "'.$BOT['mana'].'",
				`sergi` = "'.$BOT['sergi'].'",
				`kulon` = "'.$BOT['kulon'].'",
				`perchi` = "'.$BOT['perchi'].'",
				`weap` = "'.$BOT['weap'].'",
				`bron` = "'.$BOT['bron'].'",
				`r1` = "'.$BOT['r1'].'",
				`r2` = "'.$BOT['r2'].'",
				`r3` = "'.$BOT['r3'].'",
				`helm` = "'.$BOT['helm'].'",
				`shit` = "'.$BOT['shit'].'",
				`boots` = "'.$BOT['boots'].'",
				`nakidka` = "'.$BOT['nakidka'].'",
				`rubashka` = "'.$BOT['rubashka'].'",													
				`shadow` = "'.$BOT['shadow'].'",
				`battle` = 0,
				`bot` = 1,
				`id_user` = "'.$BOT['id'].'",
				`at_cost` = "'.$BOT_items['allsumm'].'",
				`kulak1` = 0,
				`sum_minu` = "'.$BOT_items['min_u'].'",
				`sum_maxu` = "'.$BOT_items['max_u'].'",
				`sum_mfkrit` = "'.$BOT_items['krit_mf'].'",
				`sum_mfakrit` = "'.$BOT_items['akrit_mf'].'",
				`sum_mfuvorot` = "'.$BOT_items['uvor_mf'].'",
				`sum_mfauvorot` = "'.$BOT_items['auvor_mf'].'",
				`sum_bron1` = "'.$BOT_items['bron1'].'",
				`sum_bron2` = "'.$BOT_items['bron2'].'",
				`sum_bron3` = "'.$BOT_items['bron3'].'",
				`sum_bron4` = "'.$BOT_items['bron4'].'",
				`ups` = "'.$BOT_items['ups'].'",
				`injury_possible` = 0, 
				`battle_t` = 0, 
				`bot_online` = 0, 
				`bot_room` = 0
		');
		if ($q === FALSE) return false;

		$botids = mysql_insert_id();
		$bothist = BNewHist($BOT);

		// клонов создали, делаем бой
		$q = mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']);
		if ($q === FALSE) return false;
								
		$q = mysql_query('INSERT INTO oldbk.`battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
				VALUES
				(
					"Квестовый бой",
					"",
					"'.$timeout.'",
					"15",
					"0",
					"'.$user['id'].'",
					"'.$botids.'",
					"'.time().'",
					"'.time().'",
					3,
					"'.mysql_real_escape_string(BNewHist($user)).'",
					"'.mysql_real_escape_string($bothist).'",
					"0","0"
				)
		');
		if ($q === FALSE) return false;
		
		$id = mysql_insert_id();
		
		// теперь обновляем себя и противника что мы в бою
		$q = mysql_query('UPDATE oldbk.`users_clons` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$botids.')');
		if ($q === FALSE) return false;
		$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$user['id']);
		if ($q === FALSE) return false;

	//	$p2 = '<b>'.nick_align_klan($user).'</b> и <b>'.nick_align_klan($BOT).'</b>';
	//	addlog($id,'Часы показывали <span class=date>'.date("Y.m.d H.i").'</span>, когда '.$p2.' бросили вызов друг другу. <BR>');
		addlog($id,"!:S:".time().":".BNewHist($user).":".$bothist."\n");

		if (strlen($text)) {
			//addlog($id,'<span class=date>'.date("H:i").'</span> '.nick_in_battle($BOT,$BOT['battle_t']).' выкрикнул: <b>'.$text.'</b><BR>');
			$text=str_replace(':','^',$text);
			addlog($id,"!:X:".time().'</span> '.nick_new_in_battle($BOT).':'.($BOT[sex]+200).":".$text."\n");
		}

		$sex = "";
		if (!$BOT['sex']) $sex = 'a';
		
		// обновляем телу фрейм
		addchp ('<font color=red>Внимание!</font> На вас напал'.$sex.' '.$BOT['login'].'!<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
		echo '<script>location.href="fbattle.php";</script>';
		return true;
	}

	function AddQuestExp($user) {
		$t = array(
			6 => 1000,
			7 => 2000,
			8 => 3000,
			9 => 5000,
			10 => 10000,
			11 => 20000,
			12 => 40000,
			13 => 80000,
			14 => 160000,
		);	

		if (isset($t[$user['level']])) {
			$exp = $t[$user['level']];
		} else {
			$exp = max($t);
		}

		if ($user['align']==4) 	
		{
			$count = intval($exp * 0.5); //50%  за квесты Загорода для хаосников
		}
		else
			{
			if ($user['prem']) $exp = $exp * 1.1;			
			
			//не работает для хаосников
			$qq=mysql_query("select * from oldbk.ivents where id=5");
			if ($qq === FALSE) return FALSE;
			$get_ivent=mysql_fetch_array($qq);
					if ($get_ivent['stat']==1)
					{
					//5) Неделя Загорода - за выполнение любого квеста загорода дается двойная награда. т.е. все что положено в двойном размере. (кроме предметов)
					$exp=$exp*2;
					}
			}


		$exp = $exp * 2; // поднять в 2 раза награду

		$q = mysql_query('UPDATE users SET exp = exp + '.$exp.' wHERE id = '.$user['id']);
		if ($q === FALSE) return FALSE;
		
		$q = mysql_query("INSERT INTO oldbk.users_progress set owner='{$user['id']}', aquestzag=1 ON DUPLICATE KEY UPDATE aquestzag=aquestzag+1");
		if ($q === FALSE) return FALSE;


		try {
			global $app;
			$User = new \components\models\User($user);
			$Quest = $app->quest
				->setUser($User)
				->get();
			$Checker = new \components\Component\Quests\check\CheckerEvent();
			$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_TOWN_OUT_QUEST_ANY_FINISH;
			if(($Item = $Quest->isNeed($Checker)) !== false) {
				$Quest->taskUp($Item);
			}
			unset($Checker);

		} catch (Exception $ex) {
			$app->logger->addEmergency((string)$ex);
		}

		return $exp;
	}

	function AddQuestRep($user,$base) {
	
		$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
		if (mysql_num_rows($q) > 0) 
		{
			$questexist = mysql_fetch_assoc($q);
			if ($questexist === FALSE) return FALSE;
		} 

	
		$add_rep = $base + (($user['level']-6)*50);
		$ar = 0;
		if (($user['prem']) and ($user['align']!=4))  $ar += 0.1;
		$ua = intval($user['align']);
		if ($ua == 1) $ua = 6;
		if (GetOpRs() == $ua) $ar += 0.05; 
		$add_rep = intval($add_rep * (1+$ar));
		$rec = array();		
		if ($questexist['q_id']>0) { $rec['add_info'] = "За квест №".$questexist['q_id']; }
				
		if ($user['align']==4) 	
				{
				$add_rep = intval($add_rep * 0.5); //50% репы за квесты Загорода для хаосников
				}
			else
			{
			
			//не для хаосников
				$qq=mysql_query("select * from oldbk.ivents where id=5");
				if ($qq === FALSE) return FALSE;
				$get_ivent=mysql_fetch_array($qq);
					if ($get_ivent['stat']==1)
					{
					//5) Неделя Загорода - за выполнение любого квеста загорода дается двойная награда. т.е. все что положено в двойном размере. (кроме предметов)
					$add_rep=$add_rep*2;
					$rec['add_info'] .= ' (x2 Бонус)';
					}
			}


		$add_rep = $add_rep * 2; // поднять в два раза награду
		
		$q = mysql_query('UPDATE oldbk.users SET rep = rep + '.$add_rep.', `repmoney` = `repmoney` + '.$add_rep.' WHERE id =  '.$user['id']);
		if ($q === FALSE) return FALSE;

  		$rec['owner']=$user[id]; 
		$rec['owner_login']=$user[login];
		$rec['owner_balans_do']=$user['money'];
		$rec['owner_balans_posle']=$user['money'];
		$rec['owner_rep_do']=$user['repmoney'];
		$rec['owner_rep_posle']=$user['repmoney']+$add_rep;
		$rec['target_login']="Квесты";
		$rec['sum_rep'] = $add_rep;
		$rec['type'] = 254;
		if (add_to_new_delo($rec) === FALSE) return FALSE;

		return $add_rep;
	}

	function AddQuestM($user,$count,$who) {
		$count += $user['level']-6;

		if ($user['align']==4) 	
		{
			$count = round(($count * 0.5),2); //50%  за квесты Загорода для хаосников
		}
		else
		{
		//для хаосников ивент не работает
		$qq = mysql_query("select * from oldbk.ivents where id=5");
		if ($qq === FALSE) return FALSE;
		$get_ivent = mysql_fetch_array($qq);

			if ($get_ivent['stat'] == 1) {
				//5) Неделя Загорода - за выполнение любого квеста загорода дается двойная награда. т.е. все что положено в двойном размере. (кроме предметов)
				$count = $count * 2;
			}
		}


		$count = $count * 3; // вместо 1 монеты - 3 кр

		$count = $count * 2; // поднять в 2 раза награду

		$q = mysql_query('UPDATE users SET money = money + '.$count.' WHERE id = '.$user['id']);
		if ($q === FALSE) return FALSE;


  		$rec['owner']=$user['id']; 
		$rec['owner_login']=$user['login'];
		$rec['owner_balans_do']=$user['money'];
		$rec['owner_balans_posle']=$user['money']+$count;
		$rec['target_login'] = $who;
		$rec['sum_kr'] = $count;
		$rec['type'] = 259;
		if (add_to_new_delo($rec) === FALSE) return FALSE;

		return $count;
	}
?>