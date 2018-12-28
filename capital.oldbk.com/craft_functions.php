<?php
function SetMsg($msg,$typet = "s") {
	$_SESSION['craftmsg'] = $msg;
	$_SESSION['craftmsgtype'] = $typet;
}

if (isset($fromcron) && $fromcron === true) {
	function Redirect() {
		echo mysql_error()."\n";
		die();
	}
} else {
	function Redirect($path = "?") {
		header("Location: ".$path); 
		die();
	}
}


function GetUserProfData($user) {
	global $craftlistname;
	global $craftexptable;
	global $craftlistrname;
	global $craftrooms;

	$id = $user['id'];

	$q = mysql_query('SELECT * FROM users_craft WHERE owner = '.$id);
	if ($q === false) return false;
	if (mysql_num_rows($q) == 0) {
		// первая запись
		$q = mysql_query('INSERT INTO users_craft (owner) VALUES ('.$id.')');
		if ($q === false) return false;
		$q = mysql_query('SELECT * FROM users_craft WHERE owner = '.$id);
		if ($q === false) return false;
	}

	$prof = mysql_fetch_assoc($q);
	if ($prof === false) return false;

	// проверка на ап левел
	reset($craftlistname);
	while(list($k,$v) = each($craftlistname)) {
		if (isset($craftexptable[$prof[$k."level"]+1]) && $prof[$k."exp"] >= $craftexptable[$prof[$k."level"]+1]) {
			// обновляем левел
			$z = addchp ('Поздравляем, теперь вы <b>'.$craftlistrname[$v].'</b> '.($prof[$k."level"]+1).' уровня!','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);
			if ($z === false) return false;

			$q = mysql_query('UPDATE users_craft SET '.$k.'level = '.$k.'level + 1 WHERE owner = '.$id);
			if ($q === false) return false;

			// плюсуем уровень
			$prof[$k."level"]++;

			// проверяем есть ли рецепт в работе и если есть - обновляем ему шанс
			$q = mysql_query('SELECT * FROM craft_job WHERE owner = '.$user['id'].' and loc = '.$user['room']);
			if ($q === false) return false;

			if (mysql_num_rows($q) > 0) {
				$cs = mysql_fetch_assoc($q);

				$q = mysql_query('SELECT * FROM craft_formula WHERE craftid = '.$cs['rcid']);
				if ($q === false) return false;

				if (mysql_num_rows($q) > 0) {
					$rc = mysql_fetch_assoc($q);

					if ($cs && $rc) {
						$ins = CraftGetItem($cs['insproto'],1);
						$ins['prototype'] = $cs['insproto'];
						$loc = $craftrooms[$user['room']];

						$get_ivent = mysql_fetch_array(mysql_query("select * from oldbk.ivents where id = 13"));
						if ($get_ivent['stat'] == 1) {
							$ins['craftbonus'] += 10;
						}

						$newchanse = CraftGetChanse($rc,$prof,$ins,$loc);
						$q = mysql_query('UPDATE craft_job SET craftchance = '.$newchanse.' WHERE id = '.$cs['id'].' LIMIT 1');
						if ($q === false) return false;
					}
				}

			}
		}
	}

	return $prof;
}

function cmpres($a, $b) {
    if ($a['cost'] == $b['cost']) {
        return 0;
    }
    return ($a['cost'] < $b['cost']) ? -1 : 1;
}


function rendercraftitem($row,$user,$prof,$rese,$ins,$loc,&$i,$craft_week = 0) {
	global $craftlistrname, $craftlist, $craftreqs_params, $rzname;

	$out = "";
	$maxres = 0;
	$isreqok = true;

	$row[GetShopCount()] = 1;
	if ($i == 0) { $i = 1; $class = 'even2';} else { $i = 0; $class = 'odd2'; }

	if (!empty($row['img_big'])) {
		$row['img'] = $row['img_big'];
	}


	$out .= '<tr class="'.$class.'">
			<td class="center vamiddle">
				<ul class="dress-item">
					<li>
						<div style="width:100;text-align:center;">
						<div style="float:none;margin:0 auto;" class="gift-block"><a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($row,true).'.html"><img class="gift-image" title="'.$row['name'].'" alt="'.$row['name'].'" src="http://i.oldbk.com/i/sh/'.$row['img'].'"></a>

						</div>
						</div>
					</li>
	';

	// <span class="invgroupcount3">'.$row['craftprotocount'].'</span>

	
	if ($row['craftnotsell']) {
		$row['notsell'] = $row['craftnotsell'];
	}
	if ($row['craftsowner']) {
		$row['sowner'] = $user['id'];
	}
	if ($row['craftis_present'] && strlen($row['craftpresent'])) {
		$row['present'] = $row['craftpresent'];
	}
	if ($row['craftgoden']) {
		$row['goden'] = $row['craftgoden'];
	}


	$out .= '<li>%ACT%</li>';
	
	$out .= '</ul></td><td style="vertical-align:top;">';
	$item = showitem($row,0,false,'','',0,0,true);
	// грубый хак на удаление </tr>
	$item = substr($item,0,strlen($item)-5);
	$out .= $item;
	$out .= '</td><td>';

	if (ADMIN && $row['is_enabled'] == 0) {
		$out .= '<font color="red">ВЫКЛЮЧЕН!</font><BR>';
	}

	$out .= '<b>Ресурсы и материалы: </b><br>';
	if (strlen($row['craftnres'])) {
		$resarr = array();

		// получаем список ресов для крафта рецепта
		$pr = unserialize($row['craftnres']);
		if ($pr !== false) {
			while(list($k,$v) = each($pr)) {
				$resarr[$k] = $v;
			}
		}

		// получаем прототипы из базы по списку выше
		$eshopproto = array();

		$reslist = mysql_query_cache('SELECT * FROM shop WHERE id IN ('.implode(",",array_keys($resarr)).')',false,10*60);
		$resproto = array();
		if (count($reslist)) {
			while(list($k,$v) = each($reslist)) {
				$resproto[$v['id']] = $v;	
				$resarr[$v['id']] = array('cost' => $v['cost'], 'count' => $resarr[$v['id']]);
			}
		}
		reset($resarr);
		// ищем прото которые не нашли в shop
		while(list($k,$v) = each($resarr)) {
			if (!isset($resproto[$k])) $eshopproto[$k] = 1;
		}


		$reslist = mysql_query_cache('SELECT * FROM eshop WHERE id IN ('.implode(",",array_keys($eshopproto)).')',false,10*60);
		if (count($reslist)) {
			while(list($k,$v) = each($reslist)) {
				$resproto[$v['id']] = $v;	
				$resarr[$v['id']] = array('cost' => $v['cost'], 'count' => $resarr[$v['id']]);
			}
		}


		uasort($resarr, 'cmpres');

		// выводим
		reset($resarr);
		while(list($k,$v) = each($resarr)) {
			if ($rese[$k] < $v['count']) $isreqok = false;
			if (!isset($rese[$k])) $rese[$k] = 0;
			$out .= '<div class="gift-block"><a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($resproto[$k],true).'.html"><img class="gift-image" title="'.$resproto[$k]['name'].' '.$rese[$k].'/'.$v['count'].'" alt="'.$resproto[$k]['name'].' '.$rese[$k].'/'.$v['count'].'" src="http://i.oldbk.com/i/sh/'.$resproto[$k]['img'].'"></a>';
			$out .= '<span class="invgroupcount'.($rese[$k] < $v['count'] ? "2" : "").'">'.$v['count'].'</span>';
			$out .= '</div>';
			if ($rese[$k] > 0 && $rese[$k] >= $v['count']) {
				// считаем количество максимальных ресов которых можем сделать
				if ($maxres != 0) {
					$t = floor($rese[$k] / $v['count']);
					if ($maxres > $t) $maxres = $t;
				} else {
					$maxres = floor($rese[$k] / $v['count']);
				}
			}
		}	
	}


	$out .= '<div style="clear:both;padding-top:10px;"><b>Требуется для производства: </b><br><ul class="listreq">';

	reset($craftlist);
	while(list($k,$v) = each($craftlist)) {
		if ($row['craftnprof'.$v] > 0) {
			if ($prof[$v."level"] >= $row['craftnprof'.$v]) {
				$out .= '<li>Ремесло '.$craftlistrname[$k].' '.$row['craftnprof'.$v].' ур.</li>';
			} else {
				$out .= '<li><font color="red">Ремесло '.$craftlistrname[$k].' '.$row['craftnprof'.$v].' ур.</font></li>';
				$isreqok = false;
			}
		}
	}

	if (strlen($row['craftnalign'])) {
		$get_align = (int)($user['align']);
		if ($get_align == 1) {$get_align=6;}
		if ($row['craftnalign'] != $get_align) {
			$out .= '<li><font color="red">Склонность <img src="http://i.oldbk.com/i/align_'.$row['craftnalign'].'.gif"></font></li>';
			$isreqok = false;
		} else {
			$out .= '<li>Склонность <img src="http://i.oldbk.com/i/align_'.$row['craftnalign'].'.gif"></li>';
		}
		
	}

	if ($row['craftnlevel'] > 0) {
		if ($user['level'] < $row['craftnlevel']) {
			$out .= '<li><font color="red">Уровень персонажа: '.$row['craftnlevel'].'</font></li>';
			$isreqok = false;
		} else {
			$out .= '<li>Уровень персонажа: '.$row['craftnlevel'].'</li>';
		}
	}

	reset($craftreqs_params);
	while(list($k,$v) = each($craftreqs_params)) {
		if ($row['craft'.$k] > 0) {
			if ($row['craft'.$k] > $user[$v['check']]) {
				$out .= '<li><font color="red">'.$v['name'].': '.$row['craft'.$k].'</font></li>';
				$isreqok = false;
			} else {
				$out .= '<li>'.$v['name'].': '.$row['craft'.$k].'</li>';
			}
		}
	}

	// кольца - 5
	                 
	$out .= '</ul></div>';
	$out .= '<div style="clear:both;padding-top:10px;"><b>Параметры рецепта: </b><br><ul class="listreq">';
	$oldtime = 0;


	if ($craft_week > 0) {
		$row['crafttime'] = round($row['crafttime']*0.5);
	}

	if ($ins['craftspeedup'] > 0) {
		if (in_array($ins['prototype'],$loc['razdel'][$_SESSION[$rzname]]['ins'])) {
			$oldtime = $row['crafttime'];
			$row['crafttime'] = round($row['crafttime'] - $row['crafttime']*$ins['craftspeedup']/100);
		}
	}

	if ($oldtime > 0) {
		$out .= '<li>Время производства: '.prettyTime(null,time()+1+($row['crafttime']*60)).' (бонус инструмента '.prettyTime(null,time()+1+(($oldtime-$row['crafttime'])*60)).')</li>';
	} else {
		$out .= '<li>Время производства: '.prettyTime(null,time()+1+($row['crafttime']*60)).'</li>';
	}
	if ($row['craftcomplexity'] > 0) {
		$out .= '<li>Сложность: '.$row['craftcomplexity'].'</li>';
	}
	// (уровень профы*10 +бонус инструмента)-сложность рецепта

	if ($craft_week > 0) {
		$ins['craftbonus'] += 10;
	}

	$ch = CraftGetChanse($row,$prof,$ins,$loc);
	if ($ch == 0) $isreqok = false;
	if ($ch < 100) {
		$tmp = '<font color="red">Шанс успеха: '.$ch.'%</font><br>';
	} else {
		$tmp = 'Шанс успеха: '.$ch.'%<br>';
	}

	if ($row['craftmfchance'] > 0) {
		if ($craft_week > 0) {
			$row['craftmfchance'] += 2;
		}

		$out .= '<li>Шанс модификации: '.($row['craftmfchance']+$ins['mfchance']+$prof[$craftlist[$row['craftgetprof']]."level"]*0.5).'%</li>';
	}


	if ($row['craftgetexp'] > 0) {
		if ($prof[$craftlist[$row['craftgetprof']]."level"] - $row['craftnprof'.$craftlist[$row['craftgetprof']]] > 2) {
			$out .= '<li>Опыт в ремесло '.$craftlistrname[$row['craftgetprof']].': <font color="red">0</font> (слишком простой рецепт для вашего уровня ремесла)</li>';
		} else {
			$out .= '<li>Опыт в ремесло '.$craftlistrname[$row['craftgetprof']].': '.$row['craftgetexp'].'</li>';
		}
	}
	$out .= '</ul>';                                                                                      
	$out .= '</div></td></tr>';

	if ($isreqok) {
		if (!$ins) {
			$maxdur = $maxres;
		} else {
			$maxdur = $ins['maxdur']-$ins['duration']-1;
		}
		if ($maxres > $maxdur) $maxres = $maxdur;
		$act = $tmp.'<a OnClick="startcraft(event,'.$row['craftid'].',\'\',1); return false;" href="#">произвести</a> <img src="http://i.oldbk.com/i/up.gif" width="11" height="11" border="0" alt="Произвести несколько штук" style="cursor: pointer" onclick="AddCount(event,'.$row['craftid'].', \''.$row['name'].'\','.$maxres.'); return false;">';
		return str_replace('%ACT%',$act,$out);
	} else {
		return str_replace('%ACT%',$tmp,$out);
	}
}


function MakePages($allcount = 0) {
	global $viewperpage,$msg,$typet,$rzname;
	$view = $viewperpage;

	if (!$allcount) {
		$q2 = mysql_query('SELECT FOUND_ROWS() AS `allcount`') or die();
		$allcount = mysql_fetch_assoc($q2);
		$allcount = $allcount['allcount'];
	}

	$cpages = ceil($allcount/$view);

	$page = $_SESSION['craftpage'.$_SESSION[$rzname]];

	if ($page >= $cpages && $page > 0 && $page !== "all") {
		$_SESSION['craftpage'.$_SESSION[$rzname]] = intval($cpages-1);
		if ($_SESSION['craftpage'.$_SESSION[$rzname]] < 0) $_SESSION['craftpage'.$_SESSION[$rzname]] = 0;
		SetMsg($msg,$typet);
		Redirect();
	}

	if ($cpages <= 1) return false;

	$pages = 'Страницы: ';
	for ($i = 0; $i < $cpages; $i++) {
		if ($page === $i) {
			$pages .= '<b> '.($i+1).'</b> ';
		} else {
			$pages .= '<a href="?razdel='.$_SESSION[$rzname].'&page='.$i.'">'.($i+1).'</a> ';
		}
	}

	if ($page === "all") {
		$pages .= '<b>[всё]</b> ';
	} else {
		$pages .= '[<a href="?razdel='.$_SESSION[$rzname].'&page=all">всё</a>] ';
	}

	return $pages;
}

function MakeLimit() {
	global $viewperpage,$rzname;

	if ($_SESSION['craftpage'.$_SESSION[$rzname]] === "all") return "";

	return ' LIMIT '.($viewperpage*$_SESSION['craftpage'.$_SESSION[$rzname]]).','.$viewperpage;
}

function CraftGetChanse($row,$prof,$ins,$loc) {
	global $craftlist,$rzname;
	if ($row['craftcomplexity'] == 0) return 100;
	if (in_array($ins['prototype'],$loc['razdel'][$_SESSION[$rzname]]['ins'])) {
		$bonus = $ins['craftbonus'];
	} else {
		$bonus = 0;
	}
	$lvlch = ($prof[$craftlist[$row['craftgetprof']]."level"]);
	$t = ((($lvlch*10)+($bonus))/$row['craftcomplexity'])*10;
	$t = round($t);	
	if ($t > 100) $t = 100;
	return $t;
}

function CraftGetItem($id,$shopid) {
	if ($shopid == 1) {
		$shop = "shop";
	} elseif ($shopid == 2) {
		$shop = "eshop";
	} elseif ($shopid == 3) {
		$shop = "cshop";
	}

	$q = mysql_query('SELECT * FROM '.$shop.' WHERE id = '.$id);
	if ($q === false) return false;
	return mysql_fetch_assoc($q);
}

function BNewCraftHist($user,$recipeid) {
	return $user['id'].'|'.$user['level'].'|'.$user['align'].'|'.$user['klan'].'|'.$user['login'].'|'.$recipeid.'#';
}


function CraftCheckComplete($user,$cs,$loc,$rzname,$fromcron = false,$forcecomplete = false) {
	global $craftlist,$craftlistrname;

	if ($fromcron === true) {
		$_SESSION = array();
	}

	if (empty($rzname) && $fromcron === true) {
		$rzname = 'craftrazdel'.$cs['loc'];
		if (!isset($_SESSION[$rzname])) {
			if (count($loc['razdel'])) {
				reset($loc['razdel']);
				list($k,$v) = each($loc['razdel']);
				$_SESSION[$rzname] = $k;
			} else {
				$_SESSION[$rzname] = 0;
			}
		}
	}

	$allids = array();

	if ($cs['craftlefttime'] == 0 || $forcecomplete) {
		if (!$fromcron) $q = mysql_query('START TRANSACTION') or Redirect();

		// лочимся
		if (!$fromcron) {
			if ($forcecomplete) {
				$q = mysql_query('SELECT * FROM craft_job WHERE owner = '.$user['id'].' and loc = '.$user['room'].' and status = 1 FOR UPDATE');
			} else {
				$q = mysql_query('SELECT * FROM craft_job WHERE owner = '.$user['id'].' and loc = '.$user['room'].' and status = 1 and craftlefttime = 0 FOR UPDATE');
			}
		}
		if ($fromcron == true || mysql_num_rows($q) > 0) {
			if (!$fromcron) $cs = mysql_fetch_assoc($q) or Redirect();

			// получаем рецепт чтобы узнать куда и сколько прокачивать опыта
			$q = mysql_query('SELECT * FROM craft_formula WHERE craftid = '.$cs['rcid']) or Redirect();
			$rc = mysql_fetch_assoc($q) or Redirect();

			// крафт закончен, выдаём вещь
			$dress = CraftGetItem($cs['jobprotoid'],$cs['jobprototype']);
			if (!$dress) die();

			$count = 1;
			if ($forcecomplete) $count = $cs['itemleft']+1;
			$goodcomplete = 0;
			$mfcomplete = 0;
			$badcomplete = 0;

			// пишем стату по потраченному времени на крафт
			mysqL_query('INSERT INTO craft_stats (owner,type,val1,val2,count,date)  VALUES ('.$user['id'].',6,'.$user['room'].','.$_SESSION[$rzname].','.$cs['crafttime'].',NOW())
					ON DUPLICATE KEY UPDATE
					`count` = `count` + '.($cs['crafttime']*$count).'
			') or Redirect();


			// проходимся по шансам
			for ($i = 0; $i < $count; $i++) {
				// проверяем удачно ли скрафтили
				if (mt_rand(1,100) > $cs['craftchance']) {
					// крафт неудачный
					$badcomplete++;
				} else {
					$goodcomplete++;
					if ($cs['craftmfchance'] > 0) {
						// получилось ли топ мф?
						if (mt_rand(0,100) < $cs['craftmfchance']) {
							$mfcomplete++;
						}
					}
				}
			}

			$prof = GetUserProfData($user);
			if (!$prof) Redirect();


			// общие допилы на прото
			$str = "";
			$sql = "";

			if($dress['nlevel'] > 6) {
				$str = ",`up_level` ";
				$sql = ",'".$dress['nlevel']."' ";
			}

			$dress['sebescost'] = 0;

			if(isset($dress['goden']) && $dress['goden'] > 0) {
	            		$DateTime = new \DateTime();
	            		$DateTime->modify(sprintf('+%d days', $dress['goden']));
	            		$dress['dategoden'] = $DateTime->getTimestamp();
       			}

	
			if ($rc['craftgoden'] > 0) {
				$dress['goden'] = $rc['craftgoden'];
	            		$DateTime = new \DateTime();
	            		$DateTime->modify(sprintf('+%d days', $dress['goden']));
	           		$dress['dategoden'] = $DateTime->getTimestamp();
			}
	
			if ($rc['craftis_present'] > 0 && strlen($rc['craftpresent'])) {
				$dress['present'] = $rc['craftpresent'];
			}
	
			if ($rc['craftnotsell'] > 0) {
				$dress['notsell'] = $rc['craftnotsell'];
			}
			        
			if ($rc['craftsowner'] > 0) {
				$dress['sowner'] = $user['id'];
			}


			$ismfstart = false;
			$mfids = array();

			for ($z = 0; $z < $goodcomplete; $z++) {
				if ($goodcomplete - $mfcomplete == $z) {
					$ismfstart = true;
					// если раздали обычные, выдаём оставшиемся мф
					$mfinfo = array();
					$mf_cost = $dress['cost'];
			
					if (($dress['gsila'] == 0) and ($dress['glovk'] == 0) and ($dress['ginta'] == 0) and ($dress['gintel'] == 0)) {
						$mf_cost = round($mf_cost*0.5, 0);
					}
			
					$mf_cost = round($mf_cost*0.5);
			
					$up_stats = 2;
					$up_hp = 20;
					$up_bron = 3;
		
					if (($dress['gsila'] == 0) and ($dress['glovk'] == 0) and ($dress['ginta'] == 0) and ($dress['gintel'] == 0)) {
						$up_stats = 0;
						$mfinfo['stats'] = 0;
					} else {
						$mfinfo['stats'] = $up_stats;
					}
		
					$bron1 = (($dress['bron1'] > 0) ? ($dress['bron1'] + $up_bron) : "0");
					$bron2 = (($dress['bron2'] > 0) ? ($dress['bron2'] + $up_bron) : "0");
					$bron3 = (($dress['bron3'] > 0) ? ($dress['bron3'] + $up_bron) : "0");
					$bron4 = (($dress['bron4'] > 0) ? ($dress['bron4'] + $up_bron) : "0");
					$hp = (($dress['ghp'] > 0) ? ($dress['ghp'] + $up_hp):"0");
			
					if ($dress['ghp'] > 0) {
						$mfinfo['hp'] = $up_hp;
					} else {
						$mfinfo['hp'] = 0;
					}
			
					if ($bron1 > 0 || $bron2 > 0 || $bron3 > 0 || $bron4 > 0) {
						$mfinfo['bron'] = $up_bron;
					} else {
						$mfinfo['bron'] = 0;
					}
			
					if($up_stats == 0) {
						if($dress['ghp'] == 0) {
							$hp = $up_hp;
							$mfinfo['hp'] = $up_hp;
						}
					}

					$dress['name'] .= ' (мф)';
					$dress['cost'] += $mf_cost;
					$dress['sebescost'] += $mf_cost;
			
					$dress['ghp'] += $mfinfo['hp'];
					$dress['stbonus'] += $mfinfo['stats'];
					$dress['bron1'] += (($dress['bron1'] > 0) ? $mfinfo['bron'] : 0);
					$dress['bron2'] += (($dress['bron2'] > 0) ? $mfinfo['bron'] : 0);
					$dress['bron3'] += (($dress['bron3'] > 0) ? $mfinfo['bron'] : 0);
					$dress['bron4'] += (($dress['bron4'] > 0) ? $mfinfo['bron'] : 0);
			
			
					$str .= ', mfinfo';
					$sql .= ', "'.mysql_real_escape_string(serialize($mfinfo)).'" ';

				}

				for ($i = 0; $i < $rc['craftprotocount']; $i++) {
					$q = mysql_query("INSERT INTO oldbk.`inventory`
							(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`idcity`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`, `ab_mf`,  `ab_bron` ,  `ab_uron`, `img_big`,
								`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`rareitem`,`stbonus`,`mfbonus`,`unik`,`notsell`,`craftspeedup`,`craftbonus`,`craftedby`,`getfrom`,`naem`,`sebescost` ".$str."
								)
								VALUES
								('{$dress['id']}','{$dress['sowner']}','{$dress['present']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
								'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
								'{$dress['nalign']}','{$dress['dategoden']}','{$dress['goden']}','{$user['id_city']}' , '{$dress[includemagic]}','{$dress[includemagicdex]}','{$dress[includemagicmax]}','{$dress[includemagicname]}','{$dress[includemagicuses]}','{$dress['includemagiccost']}','{$dress['includemagicekrcost']}', '{$dress['ab_mf']}',  '{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['img_big']}'
								,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['rareitem']}','{$dress['stbonus']}','{$dress['mfbonus']}','{$dress['unik']}','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}','".mysql_real_escape_string(BNewCraftHist($user,$rc['craftid']))."','{$cs['loc']}','{$rc['craftnaem']}','{$dress['sebescost']}' ".$sql.")
					") or Redirect();

					if ($ismfstart) {
						$mfids[] = "cap".mysql_insert_id();
					} else {
						$allids[] = "cap".mysql_insert_id();
					}
				}
			}


			// раздали шмотки, пишем статы и оповещение
			if ($badcomplete > 0) {
				// неудачное производство	
				mysql_query('INSERT INTO craft_stats (owner,type,val1,val2,val3,count,date)  VALUES ('.$user['id'].',5,'.$user['room'].','.$_SESSION[$rzname].','.$cs['rcid'].',1,NOW())
						ON DUPLICATE KEY UPDATE
						`count` = `count` + '.$badcomplete.'
				') or Redirect();
	
	
				$rec['owner']=$user['id'];
				$rec['owner_login']=$user['login'];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['target']=0;
				$rec['target_login']="Крафт-".$loc['name'];
				$rec['type'] = 1304;
				$rec['sum_kr'] = 0;
				$rec['sum_ekr'] = 0;
				$rec['sum_kom'] = 0;
				$rec['item_name']=$cs['itemname'];
				$rec['item_count']=$rc['craftprotocount']*$badcomplete;
				add_to_new_delo($rec) or Redirect();

				if (!$fromcron) $q = mysql_query('COMMIT') or Redirect();
			}

			if ($goodcomplete > 0) {
				if ($mfcomplete > 0 && count($mfids)) {
					$mfidsc = count($mfids);
					$mfids = implode(",",$mfids);
	
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']="Крафт-".$loc['name'];
					$rec['type'] = 1303;
					$rec['sum_kr'] = 0;
					$rec['sum_ekr'] = 0;
					$rec['sum_kom'] = 0;
					$rec['item_id']=$mfids;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=$mfidsc;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					add_to_new_delo($rec) or Redirect();

				}

				if (count($allids)) {
					$dress = CraftGetItem($cs['jobprotoid'],$cs['jobprototype']);
					$allidsc = count($allids);
					$allids = implode(",",$allids);
		
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']="Крафт-".$loc['name'];
					$rec['type'] = 1303;
					$rec['sum_kr'] = 0;
					$rec['sum_ekr'] = 0;
					$rec['sum_kom'] = 0;
					$rec['item_id']=$allids;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=$allidsc;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					add_to_new_delo($rec) or Redirect();
				}


				mysqL_query('INSERT INTO craft_stats (owner,type,val1,val2,val3,count,date)  VALUES ('.$user['id'].',4,'.$user['room'].','.$_SESSION[$rzname].','.$cs['rcid'].',1,NOW())
						ON DUPLICATE KEY UPDATE
						`count` = `count` + '.$goodcomplete.'
				') or Redirect();
			}

			if ($prof[$craftlist[$rc['craftgetprof']]."level"] - $rc['craftnprof'.$craftlist[$rc['craftgetprof']]] > 2) {
				$rc['craftgetexp'] = 0;
			}

			if ($count == 1) {
				if ($goodcomplete) {
					if ($rc['craftgetexp'] > 0) {
						$txt = 'Успешно изготовлен предмет «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт., добавлено '.($rc['craftgetexp']*$goodcomplete).' опыта в ремесло <b>'.$craftlistrname[$rc['craftgetprof']].'</b>';
					} else {
						$txt = 'Успешно изготовлен предмет «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт.';
					}
					SetMsg(strip_tags($txt,'<b>'),"s");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				}
				if ($badcomplete) {
					$txt = 'Изготовление предмета «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт. не удалось. Попробуйте использовать ярмарочный инструмент или рецепты с более высоким шансом успеха.';
					SetMsg(strip_tags($txt,'<b>'),"e");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				}
			} elseif ($count > 1) {
				if ($goodcomplete && !$mfcomplete && !$badcomplete) {
					if ($rc['craftgetexp'] > 0) {
						$txt = 'Успешно изготовлен предмет «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт., добавлено '.($rc['craftgetexp']*$goodcomplete).' опыта в ремесло <b>'.$craftlistrname[$rc['craftgetprof']].'</b>';
					} else {
						$txt = 'Успешно изготовлен предмет «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт.';
					}
					SetMsg(strip_tags($txt,'<b>'),"s");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				} elseif ($goodcomplete && !$mfcomplete && $badcomplete) {
					if ($rc['craftgetexp'] > 0) {
						$txt = 'Изготовление предмета «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт. завершено. Успешно создано '.$goodcomplete.' шт., провалено производство '.$badcomplete.' шт., добавлено '.($rc['craftgetexp']*$goodcomplete).' опыта в ремесло <b>'.$craftlistrname[$rc['craftgetprof']].'</b>';
					} else {
						$txt = 'Изготовление предмета «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт. завершено. Успешно создано '.$goodcomplete.' шт., провалено производство '.$badcomplete.' шт.';
					}
					SetMsg(strip_tags($txt,'<b>'),"s");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				} elseif (!$goodcomplete && !$mfcomplete && $badcomplete) {
					$txt = 'Изготовление предмета «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт. не удалось. Попробуйте использовать ярмарочный инструмент или рецепты с более высоким шансом успеха.';
					SetMsg(strip_tags($txt,'<b>'),"e");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				} elseif ($goodcomplete && $mfcomplete && !$badcomplete) {
					if ($rc['craftgetexp'] > 0) {
						$txt = 'Успешно изготовлен предмет «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт., из них с модификацией '.$mfcomplete.' шт., добавлено '.($rc['craftgetexp']*$goodcomplete).' опыта в ремесло <b>'.$craftlistrname[$rc['craftgetprof']].'</b>';
					} else {
						$txt = 'Успешно изготовлен предмет «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт., из них с модификацией '.$mfcomplete.' шт.';
					}
					SetMsg(strip_tags($txt,'<b>'),"s");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				} elseif($goodcomplete && $mfcomplete && $badcomplete) {
					if ($rc['craftgetexp'] > 0) {
						$txt = 'Изготовление предмета «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт. завершено. Успешно создано '.$goodcomplete.' шт., из них с модификацией '.$mfcomplete.' шт, провалено производство '.$badcomplete.' шт. добавлено '.($rc['craftgetexp']*$goodcomplete).' опыта в ремесло <b>'.$craftlistrname[$rc['craftgetprof']].'</b>';
					} else {
						$txt = 'Изготовление предмета «<a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($dress,true).'.html"><b>'.$dress['name'].'</b></a>» в количестве '.($rc['craftprotocount']*$count).' шт. завершено. Успешно создано '.$goodcomplete.' шт., из них с модификацией '.$mfcomplete.' шт, провалено производство '.$badcomplete.' шт.';
					}
					SetMsg(strip_tags($txt,'<b>'),"s");
					addchp ('<font color=red>Внимание!</font> '.$txt,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or Redirect();
				}
			}

			if ($cs['itemleft'] == 0 || $forcecomplete) {
				mysql_query('DELETE FROM craft_job WHERE id = '.$cs['id']) or Redirect();
			} else {
				mysql_query('UPDATE craft_job SET itemleft = itemleft - 1, craftlefttime = crafttime * 60 WHERE id = '.$cs['id']) or Redirect();
			}


			if ($prof[$craftlist[$rc['craftgetprof']]."level"] - $rc['craftnprof'.$craftlist[$rc['craftgetprof']]] > 2) {
				$rc['craftgetexp'] = 0;
			} else {
				if ($goodcomplete > 0) {
					mysql_query('UPDATE users_craft SET '.$craftlist[$rc['craftgetprof']].'exp = '.$craftlist[$rc['craftgetprof']].'exp + '.($rc['craftgetexp']*$goodcomplete).' WHERE owner = '.$user['id']) or Redirect();
		
					mysqL_query('INSERT INTO craft_stats (owner,type,val1,count,date)  VALUES ('.$user['id'].',7,'.$rc['craftgetprof'].','.($rc['craftgetexp']*$goodcomplete).',NOW())
							ON DUPLICATE KEY UPDATE
							`count` = `count` + '.($rc['craftgetexp']*$goodcomplete).'
					') or Redirect();
				}
			}
	
	
			if (!$fromcron) $q = mysql_query('COMMIT') or Redirect();

			GetUserProfData($user);

			if (!$fromcron) Redirect();
		}
	}
	return true;
}

?>