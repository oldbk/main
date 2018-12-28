<?php
	function TimeLeftToText($time) {
		if (time() > $time) $time = time();
		$h = floor(($time-time())/60/60);
		$m = floor((($time-time())/60)-(floor(($time-time())/3600)*60));

		return $h." ч. ".$m. " мин.";
	}

	function CanAttackCastle($c) {
		if ($c['nlevel'] == 9 || $c['nlevel'] == 10) return false;

		if ($c['dayofweek'] == date("N") && $c['hourofday'] == date("G")) {
			return true;
		}
		return false;
	}

	function NextFree($c) {
		$i = time();
		$cur = date("N",$i);
		do {

			if ($cur == $c['dayofweek']) {
				if (date("d.m.Y") == date("d.m.Y",$i) && date("G") > $c['hourofday']) {
					// ничего не делаем, т.к. это сегодняшний день и час убежал
				} else {
					return date("d.m.Y ".$c['hourofday'].":00",$i);
				}
			}
			$i += 3600*24;
			$cur = date("N",$i);
			$p++;
		} while(true);
	}


	// status 0. замок вне атак. clanshort - владеет
	// status 1. идёт турнир на замок
	// status 2. турнир окончен, ожидание окончательный турнир
	// status 3. идёт окончательный турнир.

	function GetCastleStatus($user,$v) {
		$txt = "";
		if ($user['klan'] == $v['clanshort'] && !empty($user['klan'])) {
			// замок принадлежит моему клану
			$txt .= "Замок принадлежит вашему клану. ";
		} elseif (strlen($v['clanshort'])) {
			// замок чужому клану
			$txt .= "Замок принадлежит клану ".$v['clanshort'].". ";
		} else {
			$txt .= "Замок никому не принадлежит. ";
		}

		if ($v['status'] == 0) {
			if (CanAttackCastle($v)) {
				$txt .= 'Открыт для заявок на турнир.';
			} else {
				$txt .= 'Защищен от нападения до '.NextFree($v);
			}
		} elseif ($v['status'] == 1) {
			$txt .= 'Идёт турнир за замок';
		} elseif ($v['status'] == 2) {
			$txt .= 'Ожидается начало битвы между кланами '.$v['clanashort1'].' и '.$v['clanashort2'].' за право владения замком.';
		} elseif ($v['status'] == 3) {
			$txt .= 'Идёт битва между кланами '.$v['clanashort1'].' и '.$v['clanashort2'].' за право владения замком.';
		}
		return $txt;
	}

	function CGetClan($clan) {
		if($clan['short'] == 'pal') {
			$align='1.99';
		} else {
			$align = $clan['align'];
		}
		$klan = '<img src=http://i.oldbk.com/i/align_'.$align.'.gif><img title='.($clan['short']=='pal'?'Орден паладинов':$clan['short']).' src=http://i.oldbk.com/i/klan/'.$clan['short'].'.gif> <b>'.($clan['short']=='pal'?'Орден паладинов':$clan['short']).'</b><a target=_blank href=http://oldbk.com/encicl/klani/clans.php?clan='.$clan['short'].'> <img src=http://i.oldbk.com/i/inf.gif></a>';
        	return $klan;
    	}

	function CGetClan2($short) {
		$clan = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$short.'"'));
		if($clan['short'] == 'pal') {
			$align='1.99';
		} else {
			$align = $clan['align'];
		}
		$klan = '<img src=http://i.oldbk.com/i/align_'.$align.'.gif><img title='.($clan['short']=='pal'?'Орден паладинов':$clan['short']).' src=http://i.oldbk.com/i/klan/'.$clan['short'].'.gif> <b>'.($clan['short']=='pal'?'Орден паладинов':$clan['short']).'</b><a target=_blank href=http://oldbk.com/encicl/klani/clans.php?clan='.$clan['short'].'> <img src=http://i.oldbk.com/i/inf.gif></a>';
        	return $klan;
    	}


	function CGetClan3($short) {
		$clan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$short.'"');
		if ($clan === FALSE) return FALSE;
		$clan = mysql_fetch_assoc($clan);
		if ($clan === FALSE) return array();

		if($clan['short'] == 'pal') {
			$align='1.99';
		} else {
			$align = $clan['align'];
		}
		$klan = '<img src=http://i.oldbk.com/i/align_'.$align.'.gif><img title='.($clan['short']=='pal'?'Орден паладинов':$clan['short']).' src=http://i.oldbk.com/i/klan/'.$clan['short'].'.gif> <b>'.($clan['short']=='pal'?'Орден паладинов':$clan['short']).'</b><a target=_blank href=http://oldbk.com/encicl/klani/clans.php?clan='.$clan['short'].'> <img src=http://i.oldbk.com/i/inf.gif></a>';
        	return $klan;
    	}


	function CGetSecondClan($klan) {
		if ($klan['rekrut_klan'] > 0) {
			// мы клан основа
			$q = mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$klan['rekrut_klan']);
			$c = mysql_fetch_assoc($q);
			return $c['short'];
		} elseif ($klan['base_klan'] > 0) {
			// мы клан рекрут
			$q = mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$klan['base_klan']);
			$c = mysql_fetch_assoc($q);
			return $c['short'];
		} else {
			return false;
		}
	}

	function CGetSecondClan2($klan) {
		$klan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$klan.'"');
		if ($klan === FALSE) return 3;
		$klan = mysql_fetch_assoc($klan);
		if ($klan === FALSE) return false;
		if ($klan['rekrut_klan'] > 0) {
			// мы клан основа
			$q = mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$klan['rekrut_klan']);
			if ($q === FALSE) return 3;
			$c = mysql_fetch_assoc($q);
			return $c['short'];
		} elseif ($klan['base_klan'] > 0) {
			// мы клан рекрут
			$q = mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$klan['base_klan']);
			if ($q === FALSE) return 3;
			$c = mysql_fetch_assoc($q);
			return $c['short'];
		} else {
			return false;
		}
	}


	function PutBookToArs($u,$iid,$booknum,$clanid) {
		global $cbookpagesa;
		$q = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.($iid+$booknum-1));
		if ($q === FALSE) return false;

		$dress = mysql_fetch_assoc($q);
		if ($dress === FALSE) return false;

		$dress['maxdur'] = 10;

		$q = mysql_query('INSERT INTO oldbk.`inventory`
				(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`duration`,`maxdur`,`isrep`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
				`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
				,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
				`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`arsenal_klan`,`arsenal_owner`
				)
						VALUES	(
							"'.mysql_real_escape_string("Замок").'",
							'.($dress['id']).',
							"22125",
							"'.mysql_real_escape_string($cbookpagesa[$booknum]["name"]." Магическая Книга").'",
							'.$dress['type'].',
							'.$dress['massa'].',
							'.$dress['cost'].',
							'.$dress['ecost'].',							
							"'.mysql_real_escape_string("cbp".$booknum.".gif").'",
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
							"0",
							"0",
							'.$dress['razdel'].',
							"0",'.$dress['group'].',"'.mysql_real_escape_string($dress['letter']).'",0,0,0,0,"",0,0,0,"0","'.$u['klan'].'","1"
					)
		');

		if ($q === FALSE) return false;

		$id = mysql_insert_id();

		$q = mysql_query('
			INSERT INTO clans_arsenal (`id_inventory`,`klan_name`,`owner_original`,`owner_current`,`gift`,`all_access`)
			VALUES("'.$id.'","'.$u['klan'].'","1","0","0","1")
		');

		if ($q === FALSE) return false;

		return true;
	}


	function PutPageToArs($u,$iid,$pagenum,$pagecolor,$clanid) {
		$q = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.$iid);
		if ($q === FALSE) return false;

		$dress = mysql_fetch_assoc($q);
		if ($dress === FALSE) return false;

		$q = mysql_query('INSERT INTO oldbk.`inventory`
				(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`duration`,`maxdur`,`isrep`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
				`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
				,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
				`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`arsenal_klan`,`arsenal_owner`
				)
						VALUES	(
							"'.mysql_real_escape_string("Замок").'",
							'.($dress['id']+($pagecolor*5)+$pagenum).',
							"22125",
							"'.mysql_real_escape_string($pagenum."я страница магической Книги").'",
							'.$dress['type'].',
							'.$dress['massa'].',
							'.$dress['cost'].',
							'.$dress['ecost'].',							
							"'.mysql_real_escape_string("cbp".$pagecolor."_".$pagenum.".gif").'",
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
							"0",
							"0",
							'.$dress['razdel'].',
							"0",'.$dress['group'].',"'.mysql_real_escape_string($dress['letter']).'",0,0,0,0,"",0,0,0,"0","'.$u['klan'].'","1"
					)
		');

		if ($q === FALSE) return false;

		$id = mysql_insert_id();

		$rec = array();
	   	$rec['owner']=$u[id];
		$rec['owner_login']=$u[login];
		$rec['owner_balans_do']=$u['money'];
		$rec['owner_balans_posle']=$u['money'];
		$rec['target_login']="Замок";
		$rec['type']= 313;
		$rec['item_id']=get_item_fid(array("id" => $id, "idcity" => $u['id_city']));
		$rec['item_name']=$pagenum."я страница магической Книги";
		$rec['item_count']=1;
		$rec['item_type']=$dress['type'];
		$rec['item_cost']=$dress['cost'];
		$rec['item_dur']=$dress['duration'];
		$rec['item_maxdur']=$dress['maxdur'];
		$rec['item_proto']=$dress['id']+$pagenum;
		$rec['item_arsenal']=$u['klan'];
		$rec['add_info'] = $u['klan'].'/'.$pagecolor.'/'.$pagenum;

		if(add_to_new_delo($rec) === FALSE) return FALSE;

		$q = mysql_query('
			INSERT INTO clans_arsenal (`id_inventory`,`klan_name`,`owner_original`,`owner_current`,`gift`,`all_access`)
			VALUES("'.$id.'","'.$u['klan'].'","1","0","0","1")
		');

		if ($q === FALSE) return false;

		return true;
	}

	function StartCastleBattleVSpl($alist,$dlist,$cid) {
		$ulist = array();

		$users1ids = "";
		$users1hist = "";
		$users1hista = "";

		while(list($k,$v) = each($alist)) {
			$users1ids .= $v['id'].",";
			$ulist[] = $v['id'];
			$users1hist .= BNewHist($v);
			$users1hista .= nick_align_klan($v).", ";
		}

		$users1ids = substr($users1ids,0,strlen($users1ids)-1);
		$users1hista = substr($users1hista,0,strlen($users1hista)-2);


		$users2ids = "";
		$users2hist = "";
		$users2hista = "";

		while(list($k,$v) = each($dlist)) {
			$users2ids .= $v['id'].",";
			$ulist[] = $v['id'];
			$users2hist .= BNewHist($v);
			$users2hista .= nick_align_klan($v).", ";
		}

		$users2ids = substr($users2ids,0,strlen($users2ids)-1);
		$users2hista = substr($users2hista,0,strlen($users2hista)-2);
		                  
		// делаем бой
		$q = mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` IN ('.$users1ids.','.$users2ids.')');
		if ($q === FALSE) return false;
								
		$q = mysql_query('INSERT INTO oldbk.`battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
				VALUES
				(
					"Бой за замок",
					"",
					"2",
					"171",
					"1",
					"'.str_replace(",",";",$users1ids).'",
					"'.str_replace(",",";",$users2ids).'",
					"'.time().'",
					"'.time().'",
					3,
					"'.mysql_real_escape_string($users1hist).'",
					"'.mysql_real_escape_string($users2hist).'",
					"2","2"
				)
		');
		if ($q === FALSE) return false;
		
		$id = mysql_insert_id();
		
		// теперь обновляем себя и противника что мы в бою
		$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `hp` = `maxhp`, `battle_t` = 1  WHERE `id` IN ('.$users1ids.')');
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `hp` = `maxhp`, `battle_t` = 2  WHERE `id` IN ('.$users2ids.')');
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE oldbk.`castles` SET status = 3, `battle` = '.$id.' WHERE `id` = '.$cid);
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE `battle` SET `status` = 0  WHERE id = '.$id);
		if ($q === FALSE) return false;


		$q = addch_group('<font color=red>Внимание!</font> Бой за замок начался.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ',$ulist);
		if ($q === FALSE) return false;

		$p2 = '<b>'.($users1hista).'</b> и <b>'.($users2hista).'</b>';
		addlog($id,"!:S:".time().":".$users1hist.":".$users2hist."\n");

		return true;
	}

	function StartCastleBattleVSpl2($alist,$dlist,$cid) {
		$ulist = array();

		$users1ids = "";
		$users1hist = "";
		$users1hista = "";

		while(list($k,$v) = each($alist)) {
			$users1ids .= $v['id'].",";
			$ulist[] = $v['id'];
			$users1hist .= BNewHist($v);
			$users1hista .= nick_align_klan($v).", ";
		}

		$users1ids = substr($users1ids,0,strlen($users1ids)-1);
		$users1hista = substr($users1hista,0,strlen($users1hista)-2);


		$users2ids = "";
		$users2hist = "";
		$users2hista = "";

		while(list($k,$v) = each($dlist)) {
			$users2ids .= $v['id'].",";
			$ulist[] = $v['id'];
			$users2hist .= BNewHist($v);
			$users2hista .= nick_align_klan($v).", ";
		}

		$users2ids = substr($users2ids,0,strlen($users2ids)-1);
		$users2hista = substr($users2hista,0,strlen($users2hista)-2);
		                  
		// делаем бой
		$q = mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` IN ('.$users1ids.','.$users2ids.')');
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp` WHERE `id` IN ('.$users1ids.','.$users2ids.')');
		if ($q === FALSE) return false;
								
		$q = mysql_query('INSERT INTO oldbk.`battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`,`nomagic`)
				VALUES
				(
					"Бой за замок",
					"",
					"2",
					"170",
					"1",
					"'.str_replace(",",";",$users1ids).'",
					"'.str_replace(",",";",$users2ids).'",
					"'.time().'",
					"'.time().'",
					3,
					"'.mysql_real_escape_string($users1hist).'",
					"'.mysql_real_escape_string($users2hist).'",
					"2","2","1"
				)
		');
		if ($q === FALSE) return false;
		
		$id = mysql_insert_id();
		
		// теперь обновляем себя и противника что мы в бою
		$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id` IN ('.$users1ids.')');
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$users2ids.')');
		if ($q === FALSE) return false;

		$q = mysql_query('UPDATE `battle` SET `status` = 0 WHERE id = '.$id);
		if ($q === FALSE) return false;

		$q = addch_group('<font color=red>Внимание!</font> Турнирный бой начался.<BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ',$ulist);
		if ($q === FALSE) return false;

		$p2 = '<b>'.($users1hista).'</b> и <b>'.($users2hista).'</b>';
		addlog($id,"!:S:".time().":".$users1hist.":".$users2hist."\n");

		return $id;
	}

	function CastleExitDress($telo) {
		     ///загружаем параметры prof=0 для выхода
		$telo_real=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`castles_profile` WHERE  prof=0 and  `owner` = '{$telo[id]}' LIMIT 1;"));
		if ($telo_real[bpbonushp] > 0) {
			// если был боныс хп - проверяем незакончился ли он
			$hp_bonus=mysql_fetch_array(mysql_query("select * from effects where owner='{$telo[id]}' and (type=1001 or  type=1002 or type=1003)"));
			if ($hp_bonus[id]>0) {
		       		//все ок эфект еще висит
			} else {
				//эфекта такого уже нет!
				//снимаем его ручками, т.к. в кроене он не снялся
				$telo_real[maxhp]=$telo_real[maxhp]-$telo_real[bpbonushp];
				$telo_real[bpbonushp]=0;
				if ($telo_real[hp]>$telo_real[maxhp]) {
					$telo_real[hp]=$telo_real[maxhp];
				}
			}
		}


	     //идем дальше-стравмы - если есть
		     $eff = mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$telo['id']."' AND (`type` >=11 AND `type` <= 14);");
		     while($effrow=mysql_fetch_array($eff))
				{
				$telo_real[sila]=$telo_real[sila]-$effrow[sila];
				$telo_real[lovk]=$telo_real[lovk]-$effrow[lovk];
				$telo_real[inta]=$telo_real[inta]-$effrow[inta];
				$telo_real[vinos]=$telo_real[vinos]-$effrow[vinos];
				}



		//обновляем инвентарь
		//1. удаляем шаблонные вещи
		mysql_query_100("delete from oldbk.inventory  where owner='{$telo[id]}' and bs_owner=10 and type!=12");

		//2.устанавливаем родные шмотки
		mysql_query_100("update oldbk.inventory  set dressed=1 where id in ({$telo_real[sergi]},{$telo_real[kulon]},{$telo_real[perchi]},{$telo_real[weap]},{$telo_real[bron]},{$telo_real[r1]},{$telo_real[r2]},{$telo_real[r3]},{$telo_real[helm]},{$telo_real[shit]},{$telo_real[boots]},{$telo_real[nakidka]},{$telo_real[rubashka]},{$telo_real[runa1]},{$telo_real[runa2]},{$telo_real[runa3]}) AND owner='{$telo[id]}' and dressed=0 ");

		//3. обновляем чарчика
		$sk_row=" `sila`='{$telo_real[sila]}',`lovk`='{$telo_real[lovk]}',`inta`='{$telo_real[inta]}',`vinos`='{$telo_real[vinos]}',`intel`='{$telo_real[intel]}',
		`mudra`='{$telo_real[mudra]}',`duh`='{$telo_real[duh]}',`bojes`='{$telo_real[bojes]}',`noj`='{$telo_real[noj]}',`mec`='{$telo_real[mec]}',`topor`='{$telo_real[topor]}',`dubina`='{$telo_real[dubina]}',
		`maxhp`='{$telo_real[maxhp]}',`hp`='{$telo_real[hp]}',`maxmana`='{$telo_real[maxmana]}',`mana`='{$telo_real[mana]}',`sergi`='{$telo_real[sergi]}',`kulon`='{$telo_real[kulon]}',`perchi`='{$telo_real[perchi]}',
		`weap`='{$telo_real[weap]}',`bron`='{$telo_real[bron]}',`r1`='{$telo_real[r1]}',`r2`='{$telo_real[r2]}',`r3`='{$telo_real[r3]}',`runa1`='{$telo_real[runa1]}',`runa2`='{$telo_real[runa2]}',`runa3`='{$telo_real[runa3]}',`helm`='{$telo_real[helm]}',`shit`='{$telo_real[shit]}',`boots`='{$telo_real[boots]}',
		`stats`='{$telo_real[stats]}',`master`='{$telo_real[master]}',`nakidka`='{$telo_real[nakidka]}',`rubashka`='{$telo_real[rubashka]}',`mfire`='{$telo_real[mfire]}',`mwater`='{$telo_real[mwater]}',`mair`='{$telo_real[mair]}',`mearth`='{$telo_real[mearth]}',
		`mlight`='{$telo_real[mlight]}',`in_tower` = 0, `id_grup` = 0,`mgray`='{$telo_real[mgray]}',`mdark`='{$telo_real[mdark]}', `bpbonushp`='{$telo_real[bpbonushp]}'  ";

		mysql_query_100("UPDATE `users` SET ".$sk_row." WHERE `users`.`id` = '{$telo[id]}' LIMIT 1;");
	}


	function WriteToCastle($cid,$txt) {
		$q = mysql_query('INSERT INTO oldbk.castles_history (castle_id,time,text) VALUES
			("'.$cid.'","'.time().'","'.mysql_real_escape_string($txt).'")
		');
		if ($q === FALSE) return false;
		return true;
	}

	function UndressCastlesAllTrz($arr) {
		while(list($k,$v) = each($arr)) {
			// раздеваем
			$res = undressalltrz($v);
			if ($res == false) return false;

			// удаляем шмот
			$q = mysql_query('DELETE FROM inventory WHERE owner = '.$v.' and bs_owner = 16');
			if ($q === false) return false;
			                  
			// выставляем статы
			$qa = mysql_query('SELECT * FROM `castles_realchars` WHERE `owner` = '.$v);
			if ($qa === false) return false;

			$tec = mysql_fetch_assoc($qa);

			$q2 = mysql_query("select * from effects where owner='{$v}' and type>=1001 and type<=1003");
			if ($q2 === false) return false;

			$hp_bonus = mysql_fetch_array($q2);
			if ($hp_bonus['id']>0) {
				// эффект еще есть		
			} else {
				//эфекта такого уже нет!
				
				$tec['maxhp'] = $tec['maxhp']-$tec['bpbonushp'];
				$tec['bpbonushp'] = 0;
		
				if ($tec['hp'] > $tec['maxhp']) {
					$tec['hp'] = $tec['maxhp'];
				}
			}

			$hp = $tec['vinos']*6 + ($tec['bpbonushp']);

			$q = mysql_query('UPDATE `users` SET 
					`sila` = "'.($tec['sila']+$tec['bpbonussila']).'",
					`lovk` = "'.$tec['lovk'].'",
					`inta` = "'.$tec['inta'].'",
					`vinos` = "'.$tec['vinos'].'",
					`intel` = "'.$tec['intel'].'",
					`mudra` = "'.$tec['mudra'].'",
					`stats` = "'.$tec['stats'].'",
					`noj` = "'.$tec['noj'].'",
					`mec` = "'.$tec['mec'].'",
					`topor` = "'.$tec['topor'].'",
					`dubina` = "'.$tec['dubina'].'",
					`mfire` = "'.$tec['mfire'].'",
					`mwater` = "'.$tec['mwater'].'",
					`mair` = "'.$tec['mair'].'",
					`mearth` = "'.$tec['mearth'].'",
					`mlight` = "'.$tec['mlight'].'",
					`mgray` = "'.$tec['mgray'].'",
					`mdark` = "'.$tec['mdark'].'",
					`master` = "'.$tec['master'].'",
					`mana` = "'.$tec['mana'].'",
					`maxmana` = "'.$tec['mana'].'",
					`maxhp` = "'.$hp.'",
					`in_tower` = 0,
					`bpbonussila` = '.$tec['bpbonussila'].',
					`bpbonushp` = '.$tec['bpbonushp'].' WHERE `id` = '.$v
			);
			if ($q === false) return false;

			$q = mysql_query('DELETE FROM `castles_realchars` WHERE `owner` = '.$v);	
			if ($q === false) return false;
			
		}
		return true;
	}

	function UndressCastlesAllNoTrz($arr) {
		while(list($k,$v) = each($arr)) {
			// раздеваем
			$res = undressall($v);
			// удаляем шмот
			$q = mysql_query('DELETE FROM inventory WHERE owner = '.$v.' and bs_owner = 16');
			                  
			// выставляем статы
			$qa = mysql_query('SELECT * FROM `castles_realchars` WHERE `owner` = '.$v);
			$tec = mysql_fetch_assoc($qa);

			$q2 = mysql_query("select * from effects where owner='{$v}' and type>=1001 and type<=1003");
			$hp_bonus = mysql_fetch_array($q2);

			if ($hp_bonus['id']>0) {
				// эффект еще есть		
			} else {
				//эфекта такого уже нет!
				
				$tec['maxhp'] = $tec['maxhp']-$tec['bpbonushp'];
				$tec['bpbonushp'] = 0;
		
				if ($tec['hp'] > $tec['maxhp']) {
					$tec['hp'] = $tec['maxhp'];
				}
			}

			$hp = $tec['vinos']*6 + ($tec['bpbonushp']);

			$q = mysql_query_100('UPDATE `users` SET 
					`sila` = "'.($tec['sila']+$tec['bpbonussila']).'",
					`lovk` = "'.$tec['lovk'].'",
					`inta` = "'.$tec['inta'].'",
					`vinos` = "'.$tec['vinos'].'",
					`intel` = "'.$tec['intel'].'",
					`mudra` = "'.$tec['mudra'].'",
					`stats` = "'.$tec['stats'].'",
					`noj` = "'.$tec['noj'].'",
					`mec` = "'.$tec['mec'].'",
					`topor` = "'.$tec['topor'].'",
					`dubina` = "'.$tec['dubina'].'",
					`mfire` = "'.$tec['mfire'].'",
					`mwater` = "'.$tec['mwater'].'",
					`mair` = "'.$tec['mair'].'",
					`mearth` = "'.$tec['mearth'].'",
					`mlight` = "'.$tec['mlight'].'",
					`mgray` = "'.$tec['mgray'].'",
					`mdark` = "'.$tec['mdark'].'",
					`master` = "'.$tec['master'].'",
					`mana` = "'.$tec['mana'].'",
					`maxmana` = "'.$tec['mana'].'",
					`maxhp` = "'.$hp.'",
					`in_tower` = 0,
					`bpbonussila` = '.$tec['bpbonussila'].',
					`bpbonushp` = '.$tec['bpbonushp'].' WHERE `id` = '.$v
			);

			$q = mysql_query('DELETE FROM `castles_realchars` WHERE `owner` = '.$v);	
			
		}
		return true;
	}

?>