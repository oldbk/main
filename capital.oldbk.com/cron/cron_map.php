<?php
	include "/www/capitalcity.oldbk.com/cron/init.php";
	require_once('/www/capitalcity.oldbk.com/functions.php');
	require_once('/www/capitalcity.oldbk.com/map_config.php');

	if(!lockCreate("cron_map") ) {
	    exit("Script already running.");
	}

	addchp ('<font color=red>Внимание!</font> start map','{[]}Десятый{[]}',-1,-1);
	addchp ('<font color=red>Внимание!</font> start map','{[]}Bred{[]}',-1,-1);

	unlink('/www/capitalcity.oldbk.com/cron/cron_map_stop');

	function MyDie($txt) {
		echo $txt."\n";
		return FALSE;
	}

	function CronMapRun() {
		global $map, $map_cost, $map_costhorse, $maprel, $map_locations, $map_costm1, $map_costm1horse;

		$q = mysql_query('START TRANSACTION'); 
		if ($q === FALSE) return MyDie(mysql_error().":".__LINE__);

		$q = mysql_query('SELECT * FROM `map_groups` WHERE (lastmove+nextcost) <= '.time().' AND status = 1 LIMIT 1 FOR UPDATE');
		if ($q === FALSE) return MyDie(mysql_error().":".__LINE__);

		if (mysql_num_rows($q) == 0) {
			$q = mysql_query('COMMIT'); 
			if ($q === FALSE) return MyDie(mysql_error().":".__LINE__);
			return 3;
		}

		while($m = mysql_fetch_assoc($q)) {
			if (($m['udate']+90) <= time()) {
				// нет обновления по карте - группу тормозим
				$q4 = mysql_query('UPDATE map_groups SET status = 0 WHERE id = '.$m['id']);
				if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);					

				$q = mysql_query('COMMIT');
				if ($q === FALSE) return MyDie(mysql_error().":".__LINE__);
				
				return TRUE;
			}

			if (strlen($m['path'])) {
				$path = unserialize($m['path']);
				$next = array_shift($path);

				$in = $m['leader'].",";
				$in .= $m['team'];
				$in = substr($in,0,strlen($in)-1);

				// проверяем на отвалившихся оффлайн
				$haveteam = strlen($m['team']) ? 1 : 0;
				$offlist = array();
				$offleader = false;

				if ($haveteam) {
					$q2 = mysql_query('SELECT * FROM users WHERE id IN ('.$in.') AND (ldate + 60) <= '.time());
					if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
				} else {
					// если я один - то проверяем онлайн на себя
					$q2 = mysql_query('SELECT * FROM users WHERE id = '.$in.' AND (ldate + 60) <= '.time());
					if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
					if (mysql_num_rows($q2) > 0) {
						$u = mysql_fetch_assoc($q2);
						if ($u['klan'] != "Adminion" && $u['klan'] != "radminion") {
							$q4 = mysql_query('UPDATE map_groups SET status = 0 WHERE id = '.$m['id']);
							if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);					
							$offleader = true;
						}
					}
				}

				if ($haveteam && mysql_num_rows($q2) > 0) {
					// есть отвалившиеся
					while($u = mysql_fetch_assoc($q2)) {
						$offlist[$u["id"]] = 1;
					}

					if (isset($offlist[$m['leader']])) {
						// разваливаем всю команду
						$offleader = true;

						$q3 = mysql_query('DELETE FROM map_groups WHERE id = '.$m['id']);
						if ($q3 === FALSE) return MyDie(mysql_error().":".__LINE__);

						$t = explode(',',$in);
						$team_cache = unserialize($m['team_cache']);

						while(list($k,$v) = each($t)) {
							if (!empty($v)) {
								$tcache = array();
								$tcache[$v] = $team_cache[$v];

								$q4 = mysql_query('INSERT INTO map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$v.','.$m['horse'].','.$m['room'].',"","","","'.mysql_real_escape_string(serialize($tcache)).'",0)');
								if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);

								$id = mysql_insert_id();

								$q4 = mysql_query('UPDATE `users` SET id_grup = '.$id.' WHERE id = '.$v);
								if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);
							}
						}
						
					} else {
						// откидываем те кто в офф ушёл
						$team_cache = unserialize($m['team_cache']);
						$newt = "";
						$t = explode(',',$m['team']);
						$todel = array();

						// создаём новые группы для тех кого выкинули
						reset($offlist);
						while(list($k,$v) = each($offlist)) {
							// прибиваем кеш
							$todel[$k] = $team_cache[$k];
							unset($team_cache[$k]);
			
							// создаём новые группы для тех кого выкинули
							$tcache = array();
							$tcache[$k] = $todel[$k];

							$q4 = mysql_query('INSERT INTO map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$k.','.$m['horse'].','.$m['room'].',"","","","'.mysql_real_escape_string(serialize($tcache)).'",0)');
							if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);

							$id = mysql_insert_id();

							$q4 = mysql_query('UPDATE `users` SET id_grup = '.$id.' WHERE id = '.$k);
							if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);
						}


						// создаём новую команду
						while(list($k,$v) = each($t)) {
							if (empty($v)) continue;
							if (isset($offlist[$v])) continue;
							$newt .= $v.",";
						}

						$q4 = mysql_query('UPDATE map_groups SET team = "'.$newt.'", team_cache = "'.mysql_real_escape_string(serialize($team_cache)).'" WHERE id = '.$m['id']);
						if ($q4 === FALSE) return MyDie(mysql_error().":".__LINE__);

						$in = $m['leader'].",";
						$in .= $newt;
						$in = substr($in,0,strlen($in)-1);

					}
				}

				if ($offleader === false) {
					if (count($path)) {
						// есть дальше путь - двигаемся
						reset($path);
						list($k,$nextcost) = each($path);
		
						$cury = floor($nextcost / 90);
						$curx = $nextcost - ($cury*90);
						if ($m['magicfast'] > 0 && empty($m['team'])) {
							if ($m['horse'] == 1) {
								$mc = $map_costm1horse;
							} else {
								$mc = $map_costm1;
							}
						} else {
							if ($m['horse'] == 1) {
								$mc = $map_costhorse;
							} else {
								$mc = $map_cost;
							}
						}

						$nextcost = $mc[$map[$cury][$curx]];
						if ($m['skillfast'] > 0) {
							$nextcost = round($nextcost - ($m['skillfast'] * $nextcost / 100));
							if ($nextcost <= 1) $nextcost = 1;
						}

						// фикс для быстрого хождения десятого						
						if ($m['leader'] == 102904 || $m['leader'] == 182783 || $m['leader'] == 6745 || $m['leader'] == 457757 || $m['leader'] == 14897 || $m['leader'] == 8540 || $m['leader'] == 684792 || $m['leader'] == 546433 || $m['leader'] == 698171) $nextcost = 1;

						$allcost = $m['cost'] - $m['nextcost'];
		
						$q2 = mysql_query('UPDATE `map_groups` SET lastmove = "'.time().'", cost = "'.$allcost.'", path = "'.mysql_real_escape_string(serialize($path)).'" ,room = "'.($next+$maprel).'", nextcost = "'.$nextcost.'" WHERE id = '.$m['id']);
						if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
		
						$q2 = mysql_query('UPDATE `users` SET room = '.($maprel+$next).' WHERE id IN ('.$in.')');
						if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
					} else {
						// дошли до конца пути
	
						// проверяем дохождение до локи
						$bfound = false;
						$binfo = array();
						reset($map_locations);
						while(list($k,$v) = each($map_locations)) {
							reset($v['dots']);
							while(list($ka,$va) = each($v['dots'])) {
								if ($next == $va) {
									$bfound = true; 
									$binfo = $v;
									break;
								}
							}
						}
	
						if ($bfound == TRUE) {
							// если дошли до локи
							$q2 = mysql_query('DELETE FROM `map_groups` WHERE id = '.$m['id']);
							if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
	
							$q2 = mysql_query('UPDATE `users` SET room = '.$binfo['room'].', id_grup = 0 WHERE id IN ('.$in.')');
							if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
						} else {
							// если дошли просто до точки на карте
							$q2 = mysql_query('UPDATE `map_groups` SET lastmove = "'.time().'", cost = "0", path = "", room = "'.($next+$maprel).'", nextcost = "0", status = 0 WHERE id = '.$m['id']);
							if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
			
							$q2 = mysql_query('UPDATE `users` SET room = '.($maprel+$next).' WHERE id IN ('.$in.')');
							if ($q2 === FALSE) return MyDie(mysql_error().":".__LINE__);
	
						}
					}
				}
			}
		}

		$q = mysql_query('COMMIT'); 
		if ($q === FALSE) return MyDie(mysql_error().":".__LINE__);

		return TRUE;
	}

	$map = unserialize($map);

	while(TRUE) {
		$r = CronMapRun();
		if ($r === FALSE) {
			echo date("d/m/Y H:i:s").": Base error signal\n";
			break;
		} elseif ($r === 3) {
			//echo date("d/m/Y H:i:s").": No groups, sleep. 1 sec\n";
			Sleep(1);
		} elseif ($r === TRUE) {
			//echo date("d/m/Y H:i:s").": Continue for next group\n";
			continue;
		}
		if (file_exists('/www/capitalcity.oldbk.com/cron/cron_map_stop')) {
			echo "Stop signal\n";
			break;
		}
	}

	addchp ('<font color=red>Внимание!</font> stop map','{[]}Десятый{[]}',-1,-1);
	addchp ('<font color=red>Внимание!</font> stop map','{[]}Bred{[]}',-1,-1);

	lockDestroy("cron_map");
?>