<?php
	$mself = $_SERVER['PHP_SELF'];

	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		global $mself;
		echo '<script>parent.location.href = "'.$mself.'";</script>';
		die();
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');
	require_once('map_config.php');
	require_once('mlfunctions.php');

	function do_auto_zg($itm) {
		global $user;
		if ($itm>0) {
			if (getcheck_auto_get_zag($user)==1) {
				$AUTO_GET_ITEM="<script> location.href='?side=right&giveqitem={$itm}'; </script>";
			} else {
				$AUTO_GET_ITEM="";
			}
			echo $AUTO_GET_ITEM;
		}
	}

	// есть-нет лошади
	$user['horse'] = $user['podarokAD'];

	if (!($user['room'] >= $maprel && $user['room'] <= $maprel+3600)) {
		reset($map_locations);
		while(list($k,$v) = each($map_locations)) {
			if ($v['room'] == $user['room']) {
				Redirect($v['redirect']);
			}
		}

		Redirect("main.php");
	}

	if ($user['battle'] != 0 || $user['battle_fin'] != 0) { Redirect("fbattle.php"); }

	$outmap = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$user['id_grup']) or mydie();
	$outmap = mysql_fetch_assoc($outmap) or die("no group");

	if (!isset($_SESSION['mappath'])) $_SESSION['mappath'] = array();
	if (!isset($_SESSION['mapcost'])) $_SESSION['mapcost'] = 0;

	$map = unserialize($map);
	$croom = $user['room'] - $maprel;

	if (isset($_GET['return']) && $outmap['status'] == 0 && $outmap['leader'] == $user['id']) {
		// вернуться в локу
		reset($map_locations);
		while(list($k,$v) = each($map_locations)) {
			reset($v['dots']);
			while(list($ka,$va) = each($v['dots'])) {
				if (($va + $maprel) == $user['room']) {
					mysql_query('START TRANSACTION') or mydie();
					mysql_query('DELETE FROM oldbk.`map_groups` WHERE id = '.$outmap['id']);
					$team = $outmap['leader'].",".$outmap['team'];
					$team = substr($team,0,strlen($team)-1);
					mysql_query('UPDATE oldbk.`users` SET room = '.$v['room'].' WHERE id IN ('.$team.')');
					mysql_query('COMMIT') or mydie();
					$_SESSION['mappath'] = array();
					$_SESSION['mapcost'] = 0;

					Redirect($v['redirect']);
				}
			}
		}
	}


	if (isset($_GET['clear'])) {
		$_SESSION['mappath'] = array();
		$_SESSION['mapcost'] = 0;
		Redirect($mself."?side=right&rleft");
	}


	if (isset($_GET['x']) && isset($_GET['y']) && $outmap['leader'] == $user['id'] && $outmap['status'] == 0) {
		$x = intval($_GET['x']);
		$y = intval($_GET['y']);
		
		if ($x >= 0 && $x <= 900 && $y >= 0 && $y <= 400) {
			$x  = floor($_GET['x'] / 10);
			$y  = floor($_GET['y'] / 10);

	                // номер точки
			$curd = ($y*90)+$x;

			$capital = array(3061 => 1, 3062 => 1, 3063 => 1, 3064 => 1, 3065 => 1, 3151 => 1, 3152 => 1, 3153 => 1, 3154 => 1, 3155 => 1, 3241 => 1, 3242 => 1, 3243 => 1, 3244 => 1, 3245 => 1, 3331 => 1, 3332 => 1, 3333 => 1, 3334 => 1, 3335 => 1, 3421 => 1, 3422 => 1, 3423 => 1, 3424 => 1, 3425 => 1);
			$avalon = array(174 => 1, 175 => 1, 176 => 1, 177 => 1, 178 => 1, 264 => 1, 265 => 1, 266 => 1, 267 => 1, 268 => 1, 354 => 1, 355 => 1, 356 => 1, 357 => 1, 358 => 1, 444 => 1, 445 => 1, 446 => 1, 447 => 1, 448 => 1, 534 => 1, 535 => 1, 536 => 1, 537 => 1, 538 => 1);
				
			if (isset($capital[$curd])) {
				$curd = 3155;
				$y = 35;
				$x = 5;
			}

			if (isset($avalon[$curd])) {
				$curd = 537;
				$y = 5;
				$x = 87;
			}

			// проверка на окоёмку
			if ($map[$y][$x] == 0) Redirect($mself."?error=1&side=left");

			// проверка на воду
			if ($map[$y][$x] == 5) Redirect($mself."?error=2&side=left");

			// если на лошади то проверяем
			if ($user['horse']) {
				if ($map_costhorse[$map[$y][$x]] == 99999) {
					Redirect($mself."?error=3&side=left");
				}
			}
				
			if ($curd == $croom) {
				Redirect($mself."?error=4&side=left");
			}
			if ($curd < 0 || $curd > 3600) Redirect($mself."?error=1&side=left");

			// если путь составной
			reset($_SESSION['mappath']);
			while(list($k,$v) = each($_SESSION['mappath'])) {
				if ($curd == $v) {
					Redirect($mself."?go=1&side=left&dbl");
					//Redirect($mself."?error=5&side=left");
				}
			}

			if (count($_SESSION['mappath'])) {
				reset($_SESSION['mappath']);
				$croom = end($_SESSION['mappath']);
			}

			// ищем готовую выборку из кеша
			$q = mysql_query_cache('SELECT * FROM oldbk.map_cache WHERE id = '.$croom.' AND type = '.$user['horse'],false,0);

			if ($q !== FALSE && count($q)) {
				// есть кеш
				$path = unserialize($q[0]['cache']);
			} else {
				// если на лошади то
				if ($user['horse']) {
					$map_cost = $map_costhorse;
				}

				// кеша нет - вычисляем и пишем кеш
				$maxint = PHP_INT_MAX;

				$dist = array(); // расстояния от заданной вершины
				$in_tree = array(); // если для вершины уже посчитали минимальное расстояние

				// инициализация
				for ($i = 0; $i < 3600; $i++) {
					$dist[$i] = PHP_INT_MAX;
					$in_tree[$i] = FALSE;
				}

				$parent = array(); // из какой точки пришли

				$dist[$croom] = 0; // начальная точка - переход 0
				$cur = $croom; // текущая вершина с которой работаем

				while(!$in_tree[$cur]) {
					$in_tree[$cur] = TRUE;

					$cury = floor($cur / 90);
					$curx = $cur - ($cury*90);

					// ищем всё ребра
		
					// up
					if ($cury - 1 >= 0) {
						// считаем расстояние до вершины i:
						// расстояние до cur + вес ребра
						$d = $dist[$cur] + $map_cost[$map[$cury-1][$curx]];
			
						// если оно меньше, чем уже записанное
						$i = (($cury-1)*90)+$curx;
						if($d < $dist[$i]) {
							$dist[$i] = $d;
							$parent[$i] = $cur;
						}
					}

					// down
					if (($cury+1) <= 39) {
						$d = $dist[$cur] + $map_cost[$map[$cury+1][$curx]];
						$i = (($cury+1)*90)+$curx;
						if($d < $dist[$i]) {
							$dist[$i] = $d;
							$parent[$i] = $cur;
						}
					}


					// left
					if (($curx-1) >= 0) {
						$d = $dist[$cur] + $map_cost[$map[$cury][$curx-1]];
						$i = (($cury)*90)+($curx-1);
						if($d < $dist[$i]) {
							$dist[$i] = $d;
							$parent[$i] = $cur;
						}
					}


					// up
					if (($curx+1) <= 89) {
						$d = $dist[$cur] + $map_cost[$map[$cury][$curx+1]];
						$i = (($cury)*90)+($curx+1);
						if($d < $dist[$i]) {
							$dist[$i] = $d;
							$parent[$i] = $cur;
						}
					}
		

					// ищем нерассмотренную вершину
					// с минимальным расстоянием
					$min_dist = PHP_INT_MAX;
					for($i = 0; $i < 3600; $i++) {
						if(!$in_tree[$i] && $dist[$i] < $min_dist) {
							$cur = $i;
							$min_dist = $dist[$i];
						}
					}

				} // end while
		
				
				// в $dist - расстояние и в $parent - обратный путь
				$path = array();
				$path['dist'] = $dist;
				$path['parent'] = $parent;
				mysql_query('INSERT INTO oldbk.`map_cache` (id,type,cache) VALUES ("'.$croom.'","'.$user['horse'].'","'.mysql_real_escape_string(serialize($path)).'")');
			}

			// путь найден - добавляем точки и время
			$_SESSION['mapcost'] += $path['dist'][$curd];

			$mend = $curd;
			$back = array();
			while($mend != $croom) {
				$back[] = $mend;
				$mend = $path['parent'][$mend];
			}
			$back = array_reverse($back);
			while(list($k,$v) = each($back)) {
				$_SESSION['mappath'][] = $v;
			}

		}
		Redirect($mself."?side=left&rright");
		die();
	}


	if (isset($_GET['go'])) {
		if ($user['id'] != $outmap['leader'] || $outmap['status'] != 0 || !count($_SESSION['mappath']) || !$_SESSION['mapcost']) Redirect($mself."?side=right&rleft");

		// проверяем что все кто в группе не в бою - доп проверка
		$in = $outmap['leader'].',';
		if (strlen($outmap['team'])) $in .= $outmap['team'];
		$in = substr($in,0,strlen($in)-1);

		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' FOR UPDATE') or mydie();
		$outmap = mysql_fetch_assoc($q) or mydie();

		if ($outmap['status'] == 0) { 
			$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.$in.') AND battle > 0') or mydie();
			if (mysql_num_rows($q) > 0) {
				$q = mysql_query('COMMIT') or mydie();
				Redirect($mself."?side=right&rleft");
			}

			// проверка на травмы
			$myeff = getalleff($user['id']);
			if ($myeff['owntravma']) {
				$q = mysql_query('COMMIT') or mydie();
				Redirect($mself."?error=17&side=right&rleft");
			}

			// проверяем всех на лошадей
			$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.$in.')') or mydie();
			$info = "";
			while($u = mysql_fetch_assoc($q)) {
				if ($u['podarokAD'] != $outmap['horse']) {
					$info .= $u['login'].",";
				}
			}
			if (strlen($info)) {
				$_SESSION['maperrordescr'] = substr($info,0,strlen($info)-1);
				Redirect($mself."?error=16&side=right&rleft");
				$q = mysql_query('COMMIT') or mydie();
			}

			// проверяем всех на вес
			$in = $outmap['leader'].",";
			$in .= $outmap['team'];
			$in = substr($in,0,strlen($in)-1);

			$wchk = array();
			
			$add_bonus[0]=0;
			$add_bonus[1]=50;
			$add_bonus[2]=250;	
			$add_bonus[3]=500;		
			$q = mysql_query('SELECT * FROM users WHERE id IN ('.$in.')') or mydie();
			while($wr = mysql_fetch_assoc($q)) {
				$nems=mysql_fetch_assoc(mysql_query("select * from users_clons where owner='{$wr[id]}' and naem_status=1"));
				$naem_bonus=0;
				if ($nems['passkills']!='') { 
					$paskill=array();
					$paskill=unserialize($nems['passkills']);
					if ($paskill[20002]['active']==1) {
						$naem_bonus=round($paskill[20002]['procent']);
					}
				}
				$wchk[$wr['id']]['gmeshok'] = $wr['sila']*4+$add_bonus[$wr['prem']]+$naem_bonus;
				$wchk[$wr['id']]['login'] = $wr['login'];
			}


			$q = mysql_query('SELECT owner,sum(massa) as allmassa, sum(gmeshok) as allgmeshok FROM inventory USE INDEX(owner_3) WHERE owner IN ('.$in.') AND bs_owner = 0 AND `dressed` = 0 AND setsale = 0 GROUP BY owner') or mydie();
			while($wr = mysql_fetch_assoc($q)) {
				if ($wr['allmassa'] > $wr['allgmeshok']+$wchk[$wr['owner']]['gmeshok']) {
					$info .= $wchk[$wr['owner']]['login'].",";
				}
			}


			/*
			$q = mysql_query('select id, login AS wlogin,  if (((SELECT IFNULL(sum(`gmeshok`),0) FROM oldbk.inventory WHERE `owner` = u.id AND bs_owner = 0 AND `dressed` = 0 AND `setsale` = 0 AND gmeshok > 0)+(sila*4)) < (select sum(massa) from oldbk.inventory where owner=u.id AND dressed=0 AND bs_owner = 0 AND dressed = 0 AND setsale = 0) ,1,0) as re from oldbk.users u where id IN ('.$in.') HAVING re = 1') or mydie();
			$info = "";
			while($u = mysql_fetch_assoc($q)) {
				$info .= $u['wlogin'].",";
			}
			*/
			
			if (strlen($info)) {
				// есть перевес
				$q = mysql_query('COMMIT') or mydie();

				$info = substr($info,0,strlen($info)-1);
				$_SESSION['maperrordescr'] = $info;
				Redirect($mself."?error=15&side=right&rleft");
			}


			$magicfast = 0;
			if ($outmap['team'] == "" && isset($myeff[4200])) {
				// есть ускорение
				$magicfast = 1;
			}

			// вычисляем стоимость следующего шага
			reset($_SESSION['mappath']);
			list($k, $next) = each($_SESSION['mappath']);
			$cury = floor($next / 90);
			$curx = $next - ($cury*90);
			if ($outmap['horse']) $map_cost = $map_costhorse;
			$nextcost = $map_cost[$map[$cury][$curx]];

			// магия ускорения
			if ($magicfast > 0) {
				if ($outmap['horse']) {
					$map_cost = $map_costm1horse;
				} else {
					$map_cost = $map_costm1;
				}
				$nextcost = $map_cost[$map[$cury][$curx]];
			}


			$pr = 0;
			if ($outmap['team'] == "") {
				$pr = getcheck_mygoto($user);
			}
			// выключаем пассивный бонус
			$pr = 0;

			// первый шаг пересчитываем исходя из наличия скила ускорения
			if ($pr > 0) {
				$nextcost = round($nextcost - ($pr * $nextcost / 100));
				if ($nextcost <= 1) $nextcost = 1;
			}


			// вычисляем заново стоимость пути, из кеша сессии брать нельзя изза магии ускорения статус которой выясняется в момент передвижения
			$_SESSION['mapcost'] = $nextcost;
			reset($_SESSION['mappath']);
			while(list($k,$v) = each($_SESSION['mappath'])) {
				$ty = floor($v / 90);
				$tx = $v - ($ty*90);

				if ($pr > 0) {
					// общую стоимость пути пересчитываем исходя из скила
					$tmp = $map_cost[$map[$ty][$tx]];
					$tmp = round($tmp - ($pr * $tmp / 100));
					if ($tmp <= 1) $tmp = 1;

					$_SESSION['mapcost'] += $tmp;
				} else {
					$_SESSION['mapcost'] += $map_cost[$map[$ty][$tx]];
				}
			}

			// для десятого быстрый ход
			if ($user['id'] == 102904 || $user['id'] == 182783 || $user['id'] == 6745 || $user['id'] == 457757 || $user['id'] == 14897 || $user['id'] == 8540 || $user['id'] == 684792 || $user['id'] == 546433 || $user['id'] == 698171) $nextcost = 1;
			
			// можем двигаться и начинаем
			mysql_query('UPDATE oldbk.map_groups SET skillfast = '.$pr.',magicfast = '.$magicfast.', udate = "'.time().'", status = 1, nextcost = "'.$nextcost.'", lastmove = "'.time().'", path = "'.mysql_real_escape_string(serialize($_SESSION['mappath'])).'", cost = "'.$_SESSION['mapcost'].'" WHERE id = '.$outmap['id']) or mydie();

			$_SESSION['mapudate'] = time();
			$_SESSION['mappath'] = array();
			$_SESSION['mapcost'] = 0;
		}
		$q = mysql_query('COMMIT') or mydie();

		if (isset($_GET['dbl'])) {
			Redirect($mself."?side=left&rright");
		}
		Redirect($mself."?side=right&rleft");
	}

	if (isset($_GET['stop'])) {
		if ($user['id'] != $outmap['leader'] || $outmap['status'] != 1) Redirect($mself."?side=right");

		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' FOR UPDATE') or mydie();
		$outmap = mysql_fetch_assoc($q) or mydie();
		
		if ($outmap['status'] == 1) {
			// тормозим
			$q = mysql_query('UPDATE oldbk.map_groups SET status = 0, path = "", cost = 0 WHERE id = '.$outmap['id']);
			if ($q === FALSE) {
				Redirect($mself."?side=right&stop");
			}
			$_SESSION['mappath'] = unserialize($outmap['path']);
			$_SESSION['mapcost'] = $outmap['cost'];
		}
		$q = mysql_query('COMMIT') or mydie();

		Redirect($mself."?side=right&rleft");
	}

	if (isset($_GET['canjoin'])) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' FOR UPDATE') or mydie();
		$outmap = mysql_fetch_assoc($q) or mydie();
		
		$canjoin = 0;
		if ($outmap['canjoin'] == 0) $canjoin = 1;

		mysql_query('UPDATE oldbk.map_groups SET canjoin = '.$canjoin.' WHERE id = '.$outmap['id']) or mydie();

		$q = mysql_query('COMMIT') or mydie();

		Redirect($mself."?side=left&tab=2");
	}

	if (isset($_POST['jointo'])) {
		if ($user['id'] != $outmap['leader'] || $outmap['status'] != 0) Redirect($mself."?side=left&tab=2");

		// Запрос на присоединения к группе, ищем группу
		$q = mysql_query('SELECT * FROM oldbk.users WHERE `login` = "'.$_POST['jointo'].'" AND id != '.$user['id']) or mydie();
		if (mysql_num_rows($q) != 1) Redirect($mself."?error=6&side=left&tab=2");
		$tojoin = mysql_fetch_assoc($q) or mydie();

		if (!$tojoin['id_grup'] || !($tojoin['room'] >= 50000 && $tojoin['room'] <= 53600)) Redirect($mself."?error=7&side=left&tab=2");
		if ($tojoin['room'] != $user['room']) Redirect($mself."?error=8&side=left&tab=2");

		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$tojoin['id_grup'].' FOR UPDATE') or mydie();
		$tojoin_group = mysql_fetch_assoc($q) or mydie();

		// группа принимает заявки
		if ($tojoin_group['canjoin'] == 0) {
			$q = mysql_query('COMMIT') or mydie();
			Redirect($mself."?error=10&side=left&tab=2");
		}

		// группа не двигается и не в бою
		if ($tojoin_group['status'] != 0) {
			$q = mysql_query('COMMIT') or mydie();
			Redirect($mself."?error=13&side=left&tab=2");
		}

		// проверяем нет ли уже нашей заявки
		$t = explode(",",$tojoin_group['wannajoin']);
		while(list($k,$v) = each($t)) {
			if (!empty($v) && $v == $outmap['id']) {
				$q = mysql_query('COMMIT') or mydie();
				Redirect($mself."?error=14&side=left&tab=2");
			}
		}

		// всё ок - подаём заявку на присоединение
		mysql_query('UPDATE oldbk.map_groups SET wannajoin = CONCAT(wannajoin,"'.$outmap['id'].',") WHERE id = '.$tojoin_group['id']) or mydie();

		$q = mysql_query('COMMIT') or mydie();
		Redirect($mself."?error=9&side=left&tab=2");
	}

	if (isset($_GET['leavegroup'])) {
		if ($user['id'] == $outmap['leader']) Redirect($mself."?side=left&tab=2");

		// сами уходим с группы
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' FOR UPDATE') or mydie();
		$outmap = mysql_fetch_assoc($q) or mydie();

		if (strlen($outmap['team'])) {
			$t = explode(',',$outmap['team']);
			$newt = "";
			while(list($k,$v) = each($t)) {
				if (!empty($v) && $v !== $user['id']) {
					$newt .= $v.',';
				}
			}

			$team_cache = unserialize($outmap['team_cache']);
			$q = mysql_query('SELECT login FROM oldbk.users WHERE id = '.$outmap['leader']) or mydie();
			$llogin = mysql_fetch_assoc($q) or mydie();
			addchp ('<font color=red>Внимание!</font> <b>'.$user['login'].'</b> покинул вашу группу','{[]}'.$llogin['login'].'{[]}') or mydie();
			unset($team_cache[$user['id']]);

			mysql_query('UPDATE oldbk.map_groups SET team = "'.$newt.'", team_cache = "'.mysql_real_escape_string(serialize($team_cache)).'" WHERE id = '.$outmap['id']) or mydie();

			// создаём себе группу
			$tcache = array();
			$tcache[$user['id']] = nick_hist_horse($user);
			$q = mysql_query('INSERT INTO oldbk.map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$user['id'].','.$user['horse'].','.$user['room'].',"","","","'.mysql_real_escape_string(serialize($tcache)).'",0)') or mydie();
			$id = mysql_insert_id();
			mysql_query('UPDATE oldbk.`users` SET id_grup = '.$id.' WHERE id = '.$user['id']) or mydie();
		}

		$q = mysql_query('COMMIT') or mydie();
		Redirect($mself."?side=left&tab=2");
	}


	if (isset($_POST['kickfromgroup'])) {
		// выкидываем с группы пачкой
		if ($user['id'] != $outmap['leader']) Redirect($mself."?side=left&tab=2");

		$tokick = array();
		reset($_POST);
		while(list($k,$v) = each($_POST)) {
			$t = explode('_',$k);
			if (count($t) == 2) {
				if ($t[0] == 'kick') {
					$tokick[] = $t[1];
				}
			}
		}		

		if (count($tokick)) {
			$q = mysql_query('START TRANSACTION') or mydie();
			$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' FOR UPDATE') or mydie();
			$outmap = mysql_fetch_assoc($q) or mydie();

			$team_cache = unserialize($outmap['team_cache']);
			$newt = "";
			$t = explode(',',$outmap['team']);
			$todel = array();
			$tomsg = "";

			// выбираем лошадей
			$horses = array();
			if (strlen($outmap['team'])) {
				$mt = substr($outmap['team'],0,strlen($outmap['team'])-1);
				$q = mysql_query('SELECT id, podarokAD FROM oldbk.users WHERE id IN ('.$mt.')') or mydie();
				while($u = mysql_fetch_assoc($q)) {
					$horses[$u['id']] = $u['podarokAD'];
				}
			}

			while(list($k,$v) = each($t)) {
				if (empty($v)) continue;

				// делаем новый список
				$bfound = false;
				reset($tokick);
				while(list($ka,$va) = each($tokick)) {
					if ($v == $va) {
						$bfound = true;
						break;
					}
				}

				if ($bfound === true) {
					$todel[$v] = $team_cache[$v];
					unset($team_cache[$v]);

					// создаём новые группы для тех кого выкинули
					$tomsg = $v.",";
					$tcache = array();
					$tcache[$v] = $todel[$v];
					$q = mysql_query('INSERT INTO oldbk.map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$v.','.$horses[$v].','.$user['room'].',"","","","'.mysql_real_escape_string(serialize($tcache)).'",0)') or mydie();
					$id = mysql_insert_id();
					mysql_query('UPDATE oldbk.`users` SET id_grup = '.$id.' WHERE id = '.$v) or mydie();
				} else {
					$newt .= $v.",";
				}
				
			}

			if (strlen($tomsg)) {
				$tomsg = substr($tomsg,0,strlen($tomsg)-1);
				$q = mysql_query('SELECT login FROM oldbk.users WHERE id IN('.$tomsg.')') or mydie();
				while($u = mysql_fetch_assoc($q)) {
					addchp ('<font color=red>Внимание!</font> <b>'.$user['login'].'</b> выкинул вас из группы','{[]}'.$u['login'].'{[]}') or mydie();
				}
			}

			mysql_query('UPDATE oldbk.map_groups SET team = "'.$newt.'", team_cache = "'.mysql_real_escape_string(serialize($team_cache)).'" WHERE id = '.$outmap['id']) or mydie();

			$q = mysql_query('COMMIT') or mydie();
		}
		Redirect($mself."?side=left&tab=2");
	}

	if (isset($_POST['accepttogroup'])) {
		// принимаем другие группы
		if ($user['id'] != $outmap['leader'] || $outmap['status'] != 0) Redirect($mself."?side=left&tab=2");

		$accept = array();
		$wanna = array();
		$t = explode(",",$outmap['wannajoin']);
		while(list($k,$v) = each($t)) {
			if (!empty($v)) $wanna[$v] = 1;
		}

		reset($_POST);
		while(list($k,$v) = each($_POST)) {
			$t = explode('_',$k);
			if (count($t) == 2) {
				if ($t[0] == 'acceptreject') {
					if (isset($wanna[$t[1]])) {
						$accept[] = $t[1];
						unset($wanna[$t[1]]);
					}
				}
			}
		}

		if (count($accept)) {
			// тут блокируем все команды которые присоединяем и себя тоже
			$q = mysql_query('START TRANSACTION') or mydie();
			$in = implode(",",$accept);
			$in .= ",".$outmap['id'];
			$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id IN ('.$in.') AND status = 0 AND room = '.$user['room'].' FOR UPDATE') or mydie();

			$newt = ""; // новая команда
			$todel = ""; // какие команды прибиваем
			$tcache = array(); // кеш

			while($t = mysql_fetch_assoc($q)) {
				$ttmp = unserialize($t['team_cache']);
				while(list($k,$v) = each($ttmp)) {
					$tcache[$k] = $v;
				}

				if ($t['id'] == $outmap['id']) {
					$newt .= $t['team'];
					continue;
				} else {
					$newt .= $t['leader'].",".$t['team'];
				}
				$todel .= $t['id'].",";	
			}

			if (strlen($todel)) {
				// есть команды которые присоединили
				if (count($wanna)) {
					$wanna = implode(",",array_keys($wanna)).",";
				} else {
					$wanna = "";
				}
				mysql_query('UPDATE oldbk.map_groups SET team = "'.$newt.'", wannajoin = "'.$wanna.'", team_cache = "'.mysql_real_escape_string(serialize($tcache)).'" WHERE id = '.$outmap['id']) or mydie();

				// удаляем старые группы
				$todel = substr($todel,0, strlen($todel)-1);
				mysql_query('DELETE FROM oldbk.map_groups WHERE id IN ('.$todel.')') or mydie();

				// мессага
				$q = mysql_query('SELECT login FROM oldbk.users WHERE id_grup IN ('.$todel.') AND room >= 50000 AND room <= 53600') or mydie();
				while($u = mysql_fetch_assoc($q)) {
					addchp ('<font color=red>Внимание!</font> <b>'.$user['login'].'</b> принял вас в свою группу','{[]}'.$u['login'].'{[]}') or mydie();
				}


				// обновляем юзеров
				$newt = substr($newt,0,strlen($newt)-1);
				mysql_query('UPDATE oldbk.`users` SET id_grup = '.$outmap['id'].' WHERE id IN('.$newt.')') or mydie();	
			}

			$q = mysql_query('COMMIT') or mydie();
		}

		Redirect($mself."?side=left&tab=2");
	}

	if (isset($_POST['rejecttogroup'])) {
		// отклоняем другие группы
		if ($user['id'] != $outmap['leader'] || $outmap['status'] != 0) Redirect($mself."?side=left&tab=2");

		$reject = array();
		reset($_POST);
		while(list($k,$v) = each($_POST)) {
			$t = explode('_',$k);
			if (count($t) == 2) {
				if ($t[0] == 'acceptreject') {
					$reject[] = $t[1];
				}
			}
		}

		if (count($reject)) {
			$t = explode(',',$outmap['wannajoin']);
			$newt = "";
			$rejlist = "";
			while(list($k,$v) = each($t)) {
				if (empty($v)) continue;

				$bfound = false;
				reset($reject);
				while(list($ka,$va) = each($reject)) {
					if ($va == $v) {
						$bfound = true;
						break;
					}
				}

				if ($bfound == false) {
					$newt = $v.",";
				} else {
					$rejlist = $v.",";
				}
			}

			$q = mysql_query('START TRANSACTION') or mydie();
			$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' AND status = 0 FOR UPDATE') or mydie();
			if (mysql_num_rows($q) == 0) mydie();

			if (strlen($rejlist)) {
				$rejlist = substr($rejlist,0,strlen($rejlist)-1);
				$q = mysql_query('SELECT login FROM oldbk.users WHERE id_grup IN ('.$rejlist.') AND room >= 50000 AND room <= 53600') or mydie();
				while($u = mysql_fetch_assoc($q)) {
					addchp ('<font color=red>Внимание!</font> <b>'.$user['login'].'</b> отказал в принятии в группу','{[]}'.$u['login'].'{[]}') or mydie();
				}
			}

			$q = mysql_query('UPDATE oldbk.map_groups SET wannajoin = "'.$newt.'" WHERE id = '.$outmap['id']) or mydie();
			$q = mysql_query('COMMIT') or mydie();
		}
		Redirect($mself."?side=left&tab=2");
	}

	if (isset($_GET['drophorse'])) {
		if ($user['horse'] && $outmap['status'] == 0) {
			$q = mysql_query('START TRANSACTION') or mydie();
			$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' AND status = 0 FOR UPDATE') or mydie();
			$outmap = mysql_fetch_assoc($q) or mydie();

			$cache = unserialize($outmap['team_cache']);
			$user['podarokAD'] = 0;
			$cache[$user['id']] = nick_hist_horse($user);

			if ($outmap['leader'] == $user['id']) {
				mysql_query('UPDATE oldbk.map_groups SET horse = 0, path="", cost = 0, nextcost = 0, team_cache = "'.mysql_real_escape_string(serialize($cache)).'" WHERE id = '.$outmap['id']) or mydie();
			} else {
				mysql_query('UPDATE oldbk.map_groups SET path="", cost = 0, nextcost = 0, team_cache = "'.mysql_real_escape_string(serialize($cache)).'" WHERE id = '.$outmap['id']) or mydie();
			}

			mysql_query('UPDATE oldbk.users SET podarokAD = 0 WHERE id = '.$user['id']) or mydie();

			// обнуляем пути
			$_SESSION['mappath'] = array();
			$_SESSION['mapcost'] = 0;

			// выкидываем лошадь на местность
			mysql_query('INSERT INTO oldbk.map_items (itemid,type,name,img,room,extra,extra2) VALUES("0","0","Лошадь","","'.$user['room'].'","'.$user['id'].'","'.$user['injury_possible'].'")') or mydie();

			$q = mysql_query('COMMIT') or mydie();
		}
		Redirect($mself."?side=right&rleft");
	}

	if (isset($_GET['giveqitem'])) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or mydie();
		if (mysql_num_rows($q) > 0) {
			$cury = floor($croom / 90);
			$curx = $croom - ($cury*90);

			$q = mysql_fetch_assoc($q) or mydie();
			switch($q['q_id']) {
				case 6:
					if ($q['step'] == 3 && QItemExistsCount($user,3003020,5) && $map[$cury][$curx] == 4) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 1) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003020,"Болото",0,array())) mydie();
						}
					}
				break;
				case 15:
					if ($q['step'] == 5 && QItemExistsCount($user,3003053,10) && $map[$cury][$curx] == 4) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 2) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003053,"Болото",0,array())) mydie();
						}
					}
				break;
				case 16:
					if ($q['step'] == 1 && QItemExistsCount($user,3003058,10)) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 3) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003058,"Болото",0,array())) mydie();
						}
					}
				break;
				case 29:
					$t = explode("/",$q['addinfo']);
					if ($q['step'] == 0 && QItemExistsCount($user,3003205,1)) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 4) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003205,"Болото",0,array())) mydie();
						}
					}
					if (($q['step'] == 9 || $t[0] == 1) && QItemExistsCount($user,3003210,10)) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 5) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003210,"Лес",0,array())) mydie();
						}
					}
				break;
				case 30:
					if (($q['step'] == 5) && QItemExistsCount($user,3003215,5)) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 6) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003215,"Болото",0,array())) mydie();
						}
					}
					if (($q['step'] == 12) && QItemExistsCount($user,3003220,5)) {
						if (isset($_SESSION['map_items'][$croom]) && $_SESSION['map_items'][$croom]["mitype"] == 7) {
							$_SESSION['map_items'][$croom] = array("time" => time()+(60*5), "mitype" => -1);
							if(!PutQItem($user,3003220,"Болото",0,array())) mydie();
						}
					}
				break;
			}
		}
		$q = mysql_query('COMMIT') or mydie();
		Redirect($mself."?side=right");
	}

	if (isset($_GET['givehorse']) && !$user['horse'] && $outmap['status'] == 0) {
		$hid = intval($_GET['givehorse']);

		$q = mysql_query('START TRANSACTION') or mydie();
		$qa = mysql_query('SELECT * FROM oldbk.map_items WHERE type = 0 AND id = '.$hid.' AND room = '.$user['room'].' FOR UPDATE') or mydie();

		if (mysql_num_rows($qa) == 1) {
			// одеваем лошадь
			$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$outmap['id'].' AND status = 0 FOR UPDATE') or mydie();
			$outmap = mysql_fetch_assoc($q) or mydie();


			$cache = unserialize($outmap['team_cache']);
			$user['podarokAD'] = 1;
			$cache[$user['id']] = nick_hist_horse($user);

			if ($outmap['leader'] == $user['id']) {
				mysql_query('UPDATE oldbk.map_groups SET horse = 1, path="", cost = 0, nextcost = 0, team_cache = "'.mysql_real_escape_string(serialize($cache)).'" WHERE id = '.$outmap['id']) or mydie();
			} else {
				mysql_query('UPDATE oldbk.map_groups SET path="", cost = 0, nextcost = 0, team_cache = "'.mysql_real_escape_string(serialize($cache)).'" WHERE id = '.$outmap['id']) or mydie();
			}
			mysql_query('UPDATE oldbk.users SET podarokAD = 1 WHERE id = '.$user['id']) or mydie();

			$_SESSION['mappath'] = array();
			$_SESSION['mapcost'] = 0;

			$horse =  mysql_fetch_assoc($qa);

			if ($horse['extra'] != $user['id']) {
				// отсылаем оповещение хозяину
				$q = mysql_query('SELECT * FROM oldbk.users WHERE id = '.$horse['extra']) or mydie();
				if (mysql_num_rows($q) > 0) {
					$owner = mysql_fetch_assoc($q) or mydie();
					if ($horse['extra2'] == 1) addchp ('<font color=red>Внимание!</font> Ваша лошадь была украдена, её украл <b>'.$user['login'].'</b>','{[]}'.$owner['login'].'{[]}') or mydie();

					// в дело конокраду
					$rec = array();
					$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']="Украл лошадь";
					$rec['type']=266; // украл лошадь
					$rec['add_info'] = $owner['id'].'/'.$owner['login'];
					add_to_new_delo($rec);

					// в дело владельцу
					$rec = array();
					$rec['owner']=$owner['id'];
					$rec['owner_login']=$owner['login'];
					$rec['owner_balans_do']=$owner['money'];
					$rec['owner_balans_posle']=$owner['money'];
					$rec['target']=0;
					$rec['target_login']="Украли лошадь";
					$rec['type']=267; // украл лошадь
					$rec['add_info'] = $user['id'].'/'.$user['login'];
					add_to_new_delo($rec);
				}
			}

			// удаляем
			mysql_query('DELETE FROM oldbk.map_items WHERE id = '.$horse['id']) or mydie();

		}
		$q = mysql_query('COMMIT') or mydie();
		Redirect($mself."?side=right&rleft");
	}

	function SecToM($sec) {
		$t = $sec / 60;
		if (intval($t) == 0) return "полминуты";
		return ceil($t). " минут ";
	}

	if (isset($_POST['saveoptions']) && isset($_POST['updatetime'])) {
		$time = intval($_POST['updatetime']);
		if ($time < 15 || $time > 60) $time = 60;
		$_SESSION['map_updatetime'] = $time;
		Redirect($mself."?side=left&tab=4&rright");
	}

?>



<?php
	if (!isset($_GET["side"])) {
?>
		<HTML><HEAD>
		<META content="Old BK, игра, online" http-equiv=Keywords name=Keywords>
		<META content="Old BK" http-equiv=Description name=Description>
		<META content="text/html; charset=windows-1251" http-equiv=Content-type>
		<script type="text/javascript" src="/i/globaljs.js"></script>
		<frameset cols="*,250" name="mainmap" FRAMEBORDER="0" BORDER="0" FRAMESPACING="0">
			<frame name="leftmap" src="<?=$mself;?>?side=left&nocache" scrolling="auto" BORDER="0"  FRAMEBORDER="0" FRAMESPACING=0 MARGINWIDTH=0 MARGINHEIGHT=0>
			<frame name="rightmap" src="<?=$mself;?>?side=right&nocache" scrolling="auto" BORDER="0"  FRAMEBORDER="0" FRAMESPACING=0 MARGINWIDTH=0 MARGINHEIGHT=0>
		</frameset>
		</HEAD>
		</HTML>
<?php

		die();
	} else {
		if ($_GET['side'] == "right") {
?>
			<HTML>
			<HEAD>
			<link rel=stylesheet type="text/css" href="i/main.css">
            <link rel="stylesheet" href="/i/btn.css" type="text/css">
			<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
			<META Http-Equiv=Cache-Control Content=no-cache>
			<meta http-equiv=PRAGMA content=NO-CACHE>
			<META HTTP-EQUIV=Expires CONTENT=0>
			<META HTTP-EQUIV=imagetoolbar CONTENT=no>
			<script type="text/javascript" src="/i/globaljs.js"></script>
			<style>
			.bignum {
				font-size: 15px;
			}
			</style>
			<script>
				function defPosition(event) {
				    var x = y = 0;
				    if (document.attachEvent != null) { // Internet Explorer & Opera
				      x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
				      y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
				    if (window.event.clientY + 72 > document.body.clientHeight) { y-=38 } else { y-=2 }
				    } else if (!document.attachEvent && document.addEventListener) { // Gecko
				      x = event.clientX + window.scrollX;
				      y = event.clientY + window.scrollY;
				    if (event.clientY + 72 > document.body.clientHeight) { y-=38 } else { y-=2 }
				    } else {
				      // Do nothing
				    }
				    return {x:x, y:y};
				}

				var showGR;
				function ShowGroup(evt,s){
					menu=document.createElement("div");
  					menu.style.border='1px solid darkgray';
  					menu.innerHTML = s;
					  menu.id='showinfogroup';
					  menu.style.background='#FFFFE1';
					  menu.style.width='230px';
					  menu.style.fontSize='10px';
					  menu.style.position='absolute';
					  menu.style.padding='5px';
					  menu.style.top = (defPosition(evt).y + 25) + "px";
					  menu.style.left = "0px";
					  menu.style.zIndex = 555;
		
					  showGR=setTimeout(function(){
					      document.body.appendChild(menu);
				     }, 1);
				}

				function HideGroup(){
					  try{
					    ids=document.getElementById('showinfogroup');
					    ids.parentNode.removeChild(ids);
					  }
					  catch (err){
						    clearTimeout(showGR);
					  }
				}
				function RefreshLeft() {
					parent.frames["leftmap"].location.href = "<?=$mself;?>?side=left&"+Math.random();
				}

				function refreshPeriodic() {
					location.href='<?=$mself;?>?side=right&'+Math.random();
				}

				if (location.href.toString().indexOf("&nocache") != -1) refreshPeriodic();


				function CloseInfoMenu() {
					var menu = document.getElementById("oIMenu");
       					menu.style.display = "none";
				}

				function OpenInfoMenu(evt,txt) {
					HideGroup();
					CloseInfoMenu();
    					evt = evt || window.event;
    					evt.cancelBubble = true;

    					var menu = document.getElementById("oIMenu");

    					var html = "<table><tr><td colspan=2 align=right><span style=\"cursor: pointer;\" OnClick=\"CloseInfoMenu();\"><font color=red>X</font></span></td></tr>";
					if (txt.length) {
						t = txt.split(",");
						for (i = 0; i < t.length; i += 2) {
							html += '<tr><td><a href="/inf.php?'+t[i+1]+'" OnClick="CloseInfoMenu();" target="_blank" class="menuItem">'+t[i]+'</a></td><td>&nbsp;</td></tr>';
						}
					}					
					html += "</table>";

					 // Если есть что показать - показываем
    					if (html) {
        					menu.innerHTML = html;
        					menu.style.top = defPosition(evt).y + "px";
        					menu.style.left = defPosition(evt).x + "px";
        					menu.style.display = "";
    					}
    					return false;
				}

				function GT(obj) {
					<?php 	
						$cury = floor($croom / 90);
						$curx = $croom - ($cury * 90);
						$curx -= 4; $cury -= 4;
						$curx = $curx * 10; $cury = $cury * 10;
						$curx += 5; $cury += 5;
					?>

					cellid = obj.id.toString();
					cellid = cellid.substr(5);
					cury = Math.floor(cellid / 9);
					curx = cellid - (cury*9);
					curposx = <?php echo $curx ?>;
					curposy = <?php echo $cury ?>;

					curposx = curposx + (curx)*10;
					curposy = curposy + (cury)*10;

					parent.frames["leftmap"].location.href = "<?=$mself;?>?side=left&x="+curposx+"&y="+curposy+"&"+Math.random();
				}


				setTimeout("refreshPeriodic()",<?php 
					if (isset($_SESSION['map_updatetime'])) {
						echo $_SESSION['map_updatetime']*1000;
					} else {
						echo 15*1000;
					}
				?>);

			</script>
			<style>
			.cellm {width:18px;height:18px;text-align:center;vertical-align:middle;}
			</style>
			</HEAD>
			<body bgcolor=#D7D7D7 leftmargin=0 topmargin=5 marginwidth=0 marginheight=0 style="margin-left:20px;">

			<div>

			<?php

				// время обновления
				if ($outmap['status'] == 1) {
					if (!isset($_SESSION['mapudate']) || ($_SESSION['mapudate']+60) <= time()) {
						if ($outmap['leader'] == $user['id']) mysql_query('UPDATE map_groups SET udate = "'.time().'" WHERE id = '.$outmap['id']);
						$_SESSION['mapudate'] = time();
					}

					echo 'В пути еще '.SecToM($outmap['cost']).' ';
				} elseif ($outmap['status'] == 2) {
					echo 'Группа сражается ';
				} elseif ($outmap['status'] == 0) {
					echo 'Группа стоит ';
				}
			?>

			&nbsp;<img style="cursor: pointer;" src="http://i.oldbk.com/i/map/refresh.png" border=0 value='Обновить' onClick="location.href='<?=$mself;?>?side=right&'+Math.random();"><br>
			</div>

			<?php
				$cury = floor($croom / 90);
				$curx = $croom - ($cury*90);

				//echo $curx.":".$cury;

		
				$lefty = $cury - 4;
				$leftx = $curx - 4;

				$list = array();
				$listshow = array();

				for ($y = $lefty; $y < ($lefty+9); $y++) {
					for ($x = $leftx; $x < ($leftx+9); $x++) {
						if ($y >= 0 && $y <= 39 && $x >= 0 && $x <= 89) {
							$list[] = (($y)*90)+$x+$maprel;
							$listshow[] = (($y)*90)+$x+$maprel;
						} else {
							$listshow[] = 0;
						}
					}
				}
		
				$maplist = array();

				if (count($list)) {
					// собираем группы
					$t = implode(",",$list);
					$q = mysql_query('SELECT m.id AS mgid, m.room AS mgroom, u.*, substrCount(m.team,",") AS mgteamcount FROM oldbk.`map_groups` AS m LEFT JOIN oldbk.users AS u ON m.leader = u.id WHERE (m.room IN ('.$t.') AND (u.ldate + 60) >= '.time().') OR m.id = '.$outmap['id']) or mydie();
					while($m = mysql_fetch_assoc($q)) {
						$maplist[$m['mgroom']][] = $m;
					}
		
					// собираем лошадей
					$q = mysql_query('SELECT u.*, m.id AS miid, m.type AS mitype, m.name AS miname, m.img AS miimg, m.room as miroom FROM oldbk.map_items AS m LEFT JOIN oldbk.users AS u ON m.extra = u.id WHERE m.room IN ('.$t.')') or mydie();
					if (mysql_num_rows($q) > 0) {
						while($i = mysql_fetch_assoc($q)) {
							if ($i['mitype'] == 0) {
								// лошадь
								$maplist[$i['miroom']][] = $i;
							}
						}
					}
				}

				// всё собрали
				/*
				echo '
					<table border=0 style="width: 162px; height: 162px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/small2/'.$croom.'.gif); padding:0px; margin:0px;border-collapse:collapse;"><tr>
				';
				*/
				echo '
					<table border=0 style="width: 162px; height: 162px; background: url(http://i.oldbk.com/i/map/map_big4.jpg) no-repeat -'.(($curx-1)*18).'px -'.(($cury-1)*18).'px; padding:0px; margin:0px;border-collapse:collapse;"><tr>
				';


				for ($i = 1; $i <= count($listshow); $i++) {
					$info = " ";
					if ($listshow[$i-1]) {
						// можем искать инфу
		 				if (isset($maplist[$listshow[$i-1]])) {
							$m = $maplist[$listshow[$i-1]];
							$addinfo = "";
							$addinfom = "";
							while(list($k,$v) = each($m)) {
								if (isset($v['mitype']) && $v['mitype'] == 0) {
									// лошадь
									$addinfo .= "Лошадь оставлена: ".addcslashes(str_replace('"',"'",nick_hist_horse($v)),"\'")."<br>";
								} elseif (isset($v['mgteamcount'])) {
									$addinfo .= "Группа с лидером: ".addcslashes(str_replace('"',"'",nick_hist_horse($v)),"\'")." и еще <font class=bignum><b>".$v['mgteamcount']."</font></b> бойцов с ним<br><br>";
								        if ($v['hidden'] == 0) {
										$addinfom .= $v['login'].",".$v['id'].",";
									} else {
										$addinfom .= '<i>Невидимка</i>,'.$v['hidden'].',';
									}
								}
		
							}

							if (strlen($addinfom)) {
								$addinfom = substr($addinfom,0,strlen($addinfom)-1);
							}
			
							if ($listshow[$i-1] == $user['room']) {
								$info = '<img border=0 oncontextmenu="return OpenInfoMenu(event,\''.$addinfom.'\');" onMouseOut="HideGroup()" onMouseOver="ShowGroup(event,\''.$addinfo.'\');" src="http://i.oldbk.com/i/map/dot_r_self.png">';
							} else {
								$info = '<img border=0 oncontextmenu="return OpenInfoMenu(event,\''.$addinfom.'\');" onMouseOut="HideGroup()" onMouseOver="ShowGroup(event,\''.$addinfo.'\');" src="http://i.oldbk.com/i/map/dot_r_other.png">';
							}
						}
					}		
					echo '<td id="cellm'.($i-1).'" OnClick="GT(this); return false;" class="cellm"><center>'.$info.'</td>';
					if (!($i % 9)) {
						echo '</tr><tr>';
					}
				}
				echo '</table>';

				if (count($_SESSION['mappath']) || $outmap['status'] != 0) {	
					if (count($_SESSION['mappath'])) {
						$mappath = $_SESSION['mappath'];
						$mapcost = $_SESSION['mapcost'];
					} elseif(strlen($outmap['path'])) {
						$mappath = unserialize($outmap['path']);
						$mapcost = $outmap['cost'];
					}
				}

				if($outmap['leader'] == $user['id']) {
					if ($outmap['status'] == 1) {
						echo '<br><img style="cursor: pointer;" src="http://i.oldbk.com/i/map/stop_way.png" OnClick="location.href=\''.$mself.'?stop=1\'"><br>';
					} elseif ($outmap['status'] == 0) {
						if (count($mappath)) echo '<br><img style="cursor: pointer;" src="http://i.oldbk.com/i/map/start_way.png" OnClick="location.href=\''.$mself.'?go=1\'"> <br>';
					}
					if (count($_SESSION['mappath'])) echo '<img style="cursor: pointer;" src="http://i.oldbk.com/i/map/clear_way.png" OnClick="location.href=\''.$mself.'?clear=1\'"><br>'; else echo '<br><br>';
				}


				if ($user['horse']) {
					if ($outmap['status'] == 0) {
						echo '<img style="cursor: pointer;" src="http://i.oldbk.com/i/map/drop_horse1.png" OnClick="location.href=\''.$mself.'?drophorse=1&side=left&tab=2\'">';
					}
				}

				if (isset($_GET['error']) && ($_GET['error'] == 15 || $_GET['error'] == 16 || $_GET['error'] == 17)) {
					echo '<br><br><font color=red>';
						switch($_GET['error']) {
							case 15:
								echo "В группе перевес у: ".$_SESSION['maperrordescr'].", вы не можете двигаться.";
							break;
							case 16:
								if ($outmap['horse'] == 0) {
									echo "Группа не может двигаться, т.к. ".$_SESSION['maperrordescr']." на коне.";
								} else {
									echo "Группа не может двигаться, т.к. ".$_SESSION['maperrordescr']." пеший.";
								}
							break;
							case 17:
								echo "С такой травмой вы не можете двигаться.";
							break;
			
					}	
					echo '</font><br><br>';
				}
			?>

			<?php
				if ($outmap['status'] == 0 && $outmap['leader'] == $user['id']) {
					// вернуться в локу
					reset($map_locations);
					while(list($k,$v) = each($map_locations)) {
						reset($v['dots']);
						while(list($ka,$va) = each($v['dots'])) {
						if (($va + $maprel) == $user['room']) {
								echo '<br><img style="cursor: pointer;" src="http://i.oldbk.com/i/map/enter_loc.png" OnClick="location.href=\''.$mself.'?side=right&return\'"><br><br>';
							}
						}
					}
				}
			?>
	
			<?php
				// квесты
				$myeff = getalleff($user['id']);
				mysql_query('START TRANSACTION');
				if (!isset($_SESSION['q_start'])) $_SESSION['q_start'] = time();
				$qitems = array();
				$q = false;
				if (!strlen($outmap['team'])) $q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or mydie();
				if ($q !== FALSE && mysql_num_rows($q) > 0) {
					$q = mysql_fetch_assoc($q) or mydie();
					switch($q['q_id']) {
						case 6:
							if ($q['step'] == 3 && QItemExistsCount($user,3003020,5) && $map[$cury][$curx] == 4) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 1);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 7:
							if ($q['addinfo'] > 0 && $q['addinfo'] < 11 && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим волка и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,536)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 9:
							$ai = explode("/",$q['addinfo']);
							if ($ai[1] == 2 && QItemExistsCount($user,3003031,5) && !QItemExists($user,3003032) && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим медведя и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,539)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 10:
							if ($q['step'] == 8 && QItemExistsCount($user,3003038,5) && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим волка и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,536)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 12:
							if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
								// генерим волка и сразу нападаем если он есть
								if (mt_rand(0,6) == 2 && $user['hp'] > 50) {
									// нападаем
									$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									if (!$myeff['owntravma']) {
										if(!StartQuestBattle($user,536)) mydie();
										echo '<script>location.href="fbattle.php";</script>';
									}
								} else {
									$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
								}
							}
						break;
						case 13:
							$t = explode("/",$q['addinfo']);
							if ($t[0] > 0 && $t[0] < 11) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим разбойника и сразу нападаем
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,535)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 15:
							if ($q['step'] == 5 && $map[$cury][$curx] == 4 && QItemExistsCount($user,3003053,10)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 2);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 16:
							// болотная трава
							if ($q['step'] == 1 && $map[$cury][$curx] == 4 && QItemExistsCount($user,3003058,10) && QItemExistsCount($user,3003057,1)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 3);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							$attack = false;

							// капля крови
							if ($q['step'] == 1 && QItemExistsCount($user,3003038,5) && $map[$cury][$curx] == 2 && QItemExistsCount($user,3003057,1)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим волка и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,536)) mydie();
											}
											$attack = TRUE;
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							// крылья мышей
							if ($q['step'] == 1 && $attack === FALSE && QItemExistsCount($user,3003059,10) && in_array($croom,$map_area[0]) !== FALSE && QItemExistsCount($user,3003057,1)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим летучую мышь и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,1) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,540)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 17:
							// сердце мышей
							if (QItemExistsCount($user,3003064,1) && in_array($croom,$map_area[0]) !== FALSE) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим летучую мышь и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,1) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,540)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 22:
							if ($q['step'] == 3 && QItemExistsCount($user,3003079,3) && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим медведя и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,539)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 24:
							$t = explode("/",$q['addinfo']);
							if ($t[1] == 1 && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим волка и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,536)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 27:
							if ($q['step'] == 3 && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим волка и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,541)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 29:
							$t = explode("/",$q['addinfo']);
							// книга ведьмы
							if ($q['step'] == 0 && $map[$cury][$curx] == 4 && QItemExistsCount($user,3003205,1)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 4);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
							// крылья мышей
							if ($q['step'] == 1 && QItemExistsCount($user,3003059,1) && in_array($croom,$map_area[0]) !== FALSE) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим летучую мышь и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,1) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,540)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
							if (($q['step'] == 9 || $t[0] == 1) && $map[$cury][$curx] == 2 && QItemExistsCount($user,3003210,10)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 5);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
						case 30:
							if ($q['step'] == 0 && QItemExistsCount($user,3003211,1)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим раненого волка
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,1) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,536,array('login' => "Раненый Волк"))) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							if (($q['step'] == 5) && $map[$cury][$curx] == 4 && QItemExistsCount($user,3003215,5)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 6);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							if ($q['step'] == 7) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим охотника
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,1) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattleCount($user,543,2)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							if ($q['step'] == 11 && QItemExistsCount($user,3003217,5) && $map[$cury][$curx] == 2) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим волка и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,536)) mydie();
											}
											$attack = TRUE;
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							if ($q['step'] == 11 && QItemExistsCount($user,3003218,1) && $map[$cury][$curx] == 4) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим утопленника и сразу нападаем если он есть
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattle($user,544)) mydie();
											}
											$attack = TRUE;
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							if (($q['step'] == 12) && $map[$cury][$curx] == 4 && QItemExistsCount($user,3003220,5)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим вещь в данной точке
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,2) == 1) {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => 7);
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}

							if ($q['step'] == 14 && QItemExists($user,3003222)) {
								if (!isset($_SESSION["map_items"][$croom]) || $_SESSION["map_items"][$croom]["time"] <= time()) {
									// генерим охотника
									if ($_SESSION['q_start']+(60*5) <= time()) {
										if (mt_rand(0,1) == 1 && $user['hp'] > 2) {
											// нападаем
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
											if (!$myeff['owntravma']) {
												if(!StartQuestBattleCount($user,543,2)) mydie();
											}
										} else {
											$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
										}
									} else {
										$_SESSION["map_items"][$croom] = array("time" => time()+(60*5), "mitype" => -1);
									}
								}
							}
						break;
					}
				}
				mysql_query('COMMIT');
	
				// итемы в данной точке
				$q = mysql_query('SELECT u.*, m.id AS miid, m.type AS mitype, m.name AS miname, m.img AS miimg FROM oldbk.map_items AS m LEFT JOIN oldbk.users AS u ON m.extra = u.id WHERE m.room = '.$user['room']) or mydie();
				if (mysql_num_rows($q) > 0 || isset($_SESSION["map_items"][$croom])) {
					$z = 1;
					while($i = mysql_fetch_assoc($q)) {
						if ($i['mitype'] == 0) {
							// лошадь
							$addinfo = "Лошадь, оставлена: ".addcslashes(str_replace('"',"'",nick_hist_horse($i)),"\'")."<br>";
							if (!$user['horse']) echo '<a href="'.$mself.'?side=right&givehorse='.$i['miid'].'"><img border=0 onMouseOut="HideGroup()" onMouseOver="ShowGroup(event,\''.$addinfo.'\');" src="http://i.oldbk.com/i/map/horse4b.gif"></a>';
						}
						if (!($z % 3)) echo '<br>';
						$z++;
					}
	
	
					if (isset($_SESSION["map_items"][$croom])) {
						$i = $_SESSION["map_items"][$croom];
						if ($i['mitype'] == 1) {
							// чародей-трава
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/trava5.gif"></a>';
							do_auto_zg($i['mitype']);
						}
						if ($i['mitype'] == 2) {
							// ветка вангутта
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/trava5.gif"></a>';
							do_auto_zg($i['mitype']);
						}

						if ($i['mitype'] == 3) {
							// болотная травка
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/trava4.gif"></a>';
							do_auto_zg($i['mitype']);							
						}

						if ($i['mitype'] == 4) {
							// книга ведьмы квест 29
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/q29_1.gif"></a>';
							do_auto_zg($i['mitype']);							
						}
							
						if ($i['mitype'] == 5) {
							// темнолист квест 29
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/q29_6.gif"></a>';
							do_auto_zg($i['mitype']);
						}

						if ($i['mitype'] == 6) {
							// обман трава квест 30
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/q30_5.gif"></a>';
							do_auto_zg($i['mitype']);							
						}

						if ($i['mitype'] == 7) {
							// жабы квест 30
							echo '<a href="'.$mself.'?side=right&giveqitem='.$i['mitype'].'"><img src="http://i.oldbk.com/i/sh/q30_10.gif"></a>';
							do_auto_zg($i['mitype']);							
						}


						if (!($z % 3)) echo '<br>';
						$z++;
					}
				}
			?>

			<?php
				if (isset($_GET['rleft'])) {
					echo '<script>parent.frames["leftmap"].location.href = "'.$mself.'?side=left&"+Math.random();</script>';
				}
			?>
			<DIV ID="oIMenu"  style="position:absolute; border:1px solid #666; background-color:#CCC; display:none; "></DIV>
			<?php
				if (isset($myeff['owntravma']) > 0) {
					if (isset($_POST['znahar'])) {
						$effects = mysql_query('select * from effects WHERE type in (11,12,13) AND owner = '.$user['id']) or MyDie();
			
			        		$price = 50;

				        	if($user['money'] >= $price && mysql_num_rows($effects) > 0) {
							while ($owntravma = mysql_fetch_array($effects)) {
								deltravma($owntravma['id']);
							}

				            		mysql_query('UPDATE users SET money = money-'.$price.' WHERE id = '.$user[id].' LIMIT 1;');

							$rec['owner'] = $user['id'];
							$rec['owner_login'] = $user['login'];
							$rec['owner_balans_do'] = $user['money'];
							$user['money'] -= $price;
							$rec['owner_balans_posle'] = $user['money'];
							$rec['target'] = 0;
							$rec['target_login'] = 'Лечение травмы в загороде';
							$rec['type'] = 36;
							$rec['sum_kr'] = $price;
							add_to_new_delo($rec); //юзеру
				            		echo '<font color=red>Травма вылечена.</font><br>';
				        	} else {
				        		echo '<font color=red>У вас не достаточно денег...</font><br>';
				        	}

					} else {
					        echo '<form method="post"><input name="znahar" type="hidden" value="1"><input type="submit" value="Вылечить травму за 50 кр."></form>';
					}
				}
			?>
			</BODY>
			</HTML>
<?php
		} elseif ($_GET['side'] == "left") {
?>
			<HTML>
			<HEAD>
			<link rel=stylesheet type="text/css" href="i/main.css">
            <link rel="stylesheet" href="/i/btn.css" type="text/css">
			<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
			<META Http-Equiv=Cache-Control Content=no-cache>
			<meta http-equiv=PRAGMA content=NO-CACHE>
			<META HTTP-EQUIV=Expires CONTENT=0>
			<META HTTP-EQUIV=imagetoolbar CONTENT=no>
	               <script type="text/javascript" src="/i/globaljs.js"></script>
			<SCRIPT LANGUAGE="JavaScript" >
			var Hint3Name = '';

			function findlogin(title, script, name) {
			    var el = document.getElementById("hint3");
				el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
				'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
				'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT id="'+name+'" TYPE=text NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
				el.style.visibility = "visible";
				el.style.left = 100 + 'px';
				el.style.top = 100 + 'px';
				document.getElementById(name).focus();
				Hint3Name = name;
			}
		
			function closehint3(){
				document.getElementById("hint3").innerHTML = "";
				Hint3Name='';
			}

			var timerID = -1;

			function getObjParams(obj) {
				var preOx = obj.offsetLeft;
				var preOy = obj.offsetTop;
				while(obj.offsetParent) {
			   		if(obj==document.getElementsByTagName('body')[0]) {
						break;
					} else { 
						obj=obj.offsetParent; 
					}
			   		preOx += obj.offsetLeft;
					preOy += obj.offsetTop;
				}
			
				return {x: preOx, y: preOy};
			}
			

			function GT(evnt,obj) {
				var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
				var is_safari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
				x = evnt.offsetX;
				y = evnt.offsetY;
				if (is_safari)  {
					t = getObjParams(document.getElementById("imgareamap"));
					x = evnt.offsetX - t.x;
					y = evnt.offsetY - t.y;
				} 
				if (is_chrome) {
	  				ua = navigator.userAgent.substring(navigator.userAgent.indexOf('Chrome/')+7);
	  				uaver = ua.substring(0,ua.indexOf('.'));

					if (uaver >= 39) {
						t = getObjParams(document.getElementById("imgareamap"));
						x = evnt.offsetX;
						y = evnt.offsetY;
					} else {
						t = getObjParams(document.getElementById("imgareamap"));
						x = evnt.offsetX - t.x;
						y = evnt.offsetY - t.y;
					}
				}

				if (!x || !y) {
					x = evnt.layerX;
					y = evnt.layerY;
				}

				location.href  = "<?=$mself;?>?side=left&x="+x+"&y="+y;
			}

			function GTxy(x,y) {
				location.href  = "<?=$mself;?>?side=left&x="+x+"&y="+y;
			}


			function refreshPeriodic() {
				location.href='<?=$mself;?>?side=left&'+Math.random();
			}

			if (location.href.toString().indexOf("&nocache") != -1) refreshPeriodic();

			var LastTAB = 1;

			function SwitchTab(newt) {
				for (i = 1; i < 5; i++) {
					document.getElementById("m"+i).style.backgroundImage='url(http://i.oldbk.com/i/map/passive_bg.jpg)';
					document.getElementById("t"+i).style.color="#a4a4a4";
					document.getElementById("m"+i).style.fontWeight="normal";
					document.getElementById("d"+i).style.display="none";
				}

				document.getElementById("m"+newt).style.backgroundImage='url(http://i.oldbk.com/i/map/active_bg.jpg)';
				document.getElementById("t"+newt).style.color="#464646";
				document.getElementById("d"+newt).style.display="block";
				document.getElementById("m"+newt).style.fontWeight="bold";

				if (timerID != -1) clearTimeout(timerID);

				if (newt == 1) {
					timerID = setTimeout("refreshPeriodic()",60000);
				}
				LastTAB = newt;
			}

			function Prv(logins) {
				top.frames['bottom'].window.document.F1.text.focus();
				top.frames['bottom'].document.forms[0].text.value = logins + top.frames['bottom'].document.forms[0].text.value;
			}
			
		
			</script>
			</HEAD>
			<body bgcolor=#D7D7D7 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">
			<center>
			<table border=0 style="text-align:center; padding:0px; margin:0px;border-collapse:collapse;">
			<tr>
			<td id="m1" OnClick="location.href='<?=$mself;?>?side=left&tab=1';" style="white-space: nowrap; cursor: pointer; width: 168px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t1" color="#a4a4a4">Карта</td>
			<td id="m2" OnClick="location.href='<?=$mself;?>?side=left&tab=2';" style="white-space: nowrap; cursor: pointer; width: 168px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t2" color="#a4a4a4">Группы</td>
			<td id="m3" OnClick="location.href='<?=$mself;?>?side=left&tab=3';" style="white-space: nowrap; cursor: pointer; width: 168px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t3" color="#a4a4a4">Задания</td>
			<td id="m4" OnClick="SwitchTab(4);" style="white-space: nowrap; cursor: pointer; width: 168px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t4" color="#a4a4a4">Настройки</td>
			<td>&nbsp;<img style="cursor: pointer;" src="http://i.oldbk.com/i/map/refresh.png" border=0 value='Обновить' onClick="location.href='<?=$mself;?>?side=left&tab='+LastTAB+'&'+Math.random();"><br></td>
			</tr>
			</table>

			<?php
				if (!isset($_GET['tab']) && !isset($_POST['tab'])) { 
					$tab = 1;
				} else {
					$tab = isset($_GET["tab"]) ? intval($_GET['tab']) : "1";
					if ($tab == 1) {
						$tab = isset($_POST["tab"]) ? intval($_POST['tab']) : "1";
					}
				}
			?>


			<div id="d1" style="display:none;">
				<?php if ($tab == 1) { ?>
				<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=1 border=0 cellspacing="0" cellpadding="0" style="background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/tab_bg.jpg);"><tr><td valign=top>
				<div style="position:relative;">
				<img id="imgareamap" height="400" src="http://i.oldbk.com/i/map/map5.jpg" width="900" height="400" usemap="#areamap" border="0" style="z-index:1;">
				<?php
					if ($outmap['leader'] == $user['id']) {
				?>
					<MAP name="areamap">
						<area onclick="GT(window.event || event, this); return false;" shape="RECT" alt="" coords="0,0,900,400">
					</MAP>
				<?php
					}
				?>

				<br>
		
				<?php
					$imgglobal = 1;

					$y = floor($croom / 90);
					$x = $croom - ($y*90);
					$top = ($y*10);
					$left =($x*10);
					echo '<img id="dot'.$imgglobal.'" src="http://i.oldbk.com/i/map/dot_l_self.png" style="z-index:3; position: absolute; left: '.$left.'px; top: '.$top.'px;">';
					$imgglobal++;

					// 0 - переправа1 - 2201
					// 1 - переправа2 - 2023
					// 2 - деревня - 3035
					// 3 - хижина пилигрима - 2726
					// 4 - разбойничье логово - 1222
					// 5 - рудник - 273 
					// 6 - дом лесоруба - 3139 
					// 7 - хижина ведьмы - 3387 
					// 8 - пещера дракона - 238
					// 9 - замок рыцаря - 1655
					// 10 - орлиное гнездо - 1725 
					// 11 - почтовая станция - 2495
					// 12 - сторожевой пост - 2260
					// 12-1 - сторожевой пост - 1253
					// 13 - охотничий бивак - 394
					// 14 - башня мага - 2245
					// 15 - конюшня1 - 3000
					// 16 - конюшня2 - 1450 
					// 17 - конюшня3 - 1968
					// 18 - конюшня4 - 1192
					// 19 - конюшня5 - 1126
					// 20 - конюшня6 - 697
					// 21 - скупщик - 1504
					// 22 - Чужестранец - 2355
					

					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 25px; top: 25px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Рудник" title="Рудник" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(35,35);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 95px; top: 155px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Конюшня" title="Конюшня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(105,165);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 95px; top: 245px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Сторожевой пост" title="Сторожевой пост" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(105,255);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 145px; top: 185px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Орлиное гнездо" title="Орлиное гнездо" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(155,195);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 215px; top: 125px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Конюшня" title="Конюшня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(225,135);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 345px; top: 175px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Замок одинокого рыцаря" title="Замок одинокого рыцаря" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(355,185);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 405px; top: 235px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Переправа" title="Переправа" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(415,245);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 295px; top: 325px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Конюшня" title="Конюшня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(305,335);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 255px; top: 295px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Хижина пилигрима" title="Хижина пилигрима" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(265,305);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 565px; top: 365px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Хижина ведьмы" title="Хижина ведьмы" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(575,375);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 645px; top: 325px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Деревня" title="Деревня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(655,335);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 645px; top: 265px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Почтовая станция" title="Почтовая станция" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(655,275);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 785px; top: 335px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Дом лесоруба" title="Дом лесоруба" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(795,345);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 845px; top: 235px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Башня мага" title="Башня мага" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(855,245);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 775px; top: 205px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Конюшня" title="Конюшня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(785,215);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 825px; top: 125px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Сторожевой пост" title="Сторожевой пост" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(835,135);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 665px; top: 65px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Конюшня" title="Конюшня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(675,75);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 635px; top: 155px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Скупщик краденого" title="Скупщик краденого" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(645,165);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 575px; top: 15px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Пещера дракона" title="Пещера дракона" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(585,25);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 515px; top: 125px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Разбойничье логово" title="Разбойничье логово" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(525,135);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 455px; top: 115px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Конюшня" title="Конюшня" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(465,125);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 335px; top: 35px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Охотничий бивак" title="Охотничий бивак" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(345,45);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 425px; top: 215px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Переправа" title="Переправа" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(435,225);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 145px; top: 255px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Чужестранец" title="Чужестранец" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(155,265);" />';

					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 165px; top: 334px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Тренировочный лагерь" title="Тренировочный лагерь" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(175,345);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 175px; top: 34px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Лаборатория алхимика" title="Лаборатория алхимика" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(185,45);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 444px; top: 284px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Жилище чародейки" title="Жилище чародейки" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(455,295);" />';
					echo '<img id="dot'.($imgglobal++).'" width=20 height=20 style="z-index:3; position: absolute; left: 566px; top: 244px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Логово оракула" title="Логово оракула" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/map/active_p.png\'" onmouseout="this.src=\'http://i.oldbk.com/i/map/empty_gif.gif\'" onclick="GTxy(575,255);" />';

					$mappath = array();
					$mapcost = 0;
	
					if (count($_SESSION['mappath']) || $outmap['status'] != 0) {	
						if (count($_SESSION['mappath'])) {
							$mappath = $_SESSION['mappath'];
							$mapcost = $_SESSION['mapcost'];
						} elseif(strlen($outmap['path'])) {
							$mappath = unserialize($outmap['path']);
							$mapcost = $outmap['cost'];
						}

						
						reset($_SESSION['mappath']);
						end($_SESSION['mappath']);
						list($ka,$va) = each($_SESSION['mappath']);
	
						reset($_SESSION['mappath']);
						while(list($k,$v) = each($mappath)) {
							$y = floor($v / 90);
							$x = $v - ($y*90);
							$top = ($y*10);
							$left = ($x*10);
							$end = "";
							if ($k == $ka && $v == $va) {
								$end = ' onclick="GTxy('.$left.','.$top.'); return false;" ';
							}
							echo '<img id="dot'.$imgglobal.'" '.$end.' src="http://i.oldbk.com/i/map/way_transit.png" style="z-index:3; position: absolute; left: '.$left.'px; top: '.$top.'px;">';
							$imgglobal++;
						}
					}

					if (isset($_GET['error'])) {
						echo '<font color=red>';
						switch($_GET['error']) {
							case 1:
								echo "Вы не можете туда пойти.";
							break;
							case 2:
								echo "Вы не можете зайти в воду.";
							break;
							case 3:
								echo "Вы не можете пройти туда на лошади.";
							break;
							case 4:
								echo "Вы уже тут находитесь.";
							break;
							case 5:
								echo "Ваш маршрут уже проходит про этому месту.";
							break;
						}	
						echo '</font><br><br>';
					}
				?>
				
				</div>
				</td></tr></table></TD></TR></TABLE>
				<?php } ?>
			</div>

			<div id="d2" style="display:none;">
				<?php if ($tab == 2) { ?>
				<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=900 border=0 height=205 cellspacing="0" cellpadding="0" style="background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/tab_bg.jpg);"><tr><td valign=top>
				<form method="POST">
				<br><br>
				<div style="margin-left:20px;">
				<?php
				if ($outmap['leader'] == $user['id']) {
					// я лидер группы
					echo 'Разрешить присоединяться к группе <input OnClick="location.href=\''.$mself.'?side=left&tab=2&canjoin=change\';" type=checkbox '.($outmap['canjoin'] ? "checked" : "").'><br>';
			
					if ($outmap['status'] == 0) {
						echo '<a href="#" OnClick="findlogin(\'Присоединиться к группе\',\''.$mself.'?side=left&tab=2\',\'jointo\'); return false;">Присоединиться к другой группе</a><br>';
					}
	
					if (strlen($outmap['wannajoin']) && $outmap['status'] == 0 && $outmap['canjoin']) {
						echo '<br><br>';
						$t = substr($outmap['wannajoin'],0,strlen($outmap['wannajoin'])-1);
						$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id IN('.$t.') AND status = 0 AND room = '.$user['room']) or mydie();
			
						if (mysql_num_rows($q) > 0) {
							echo 'Список желающих присоединится: <br>';
							$myeff = getalleff($user['id']);
							$i = 1;
							while($t = mysql_fetch_assoc($q)) {
								$grp = implode(', ',unserialize($t['team_cache']));
								echo $i.' Группа: '.$grp.' <input type="checkbox" name="acceptreject_'.$t['id'].'" value="1"><br>';
								$i++;
							}
							if (isset($myeff[4200])) {
								echo '<font color=red>Внимание! Эффект «Ускорение» будет игнорироваться!</font><BR>';
							}
							$pr = 0;
							$pr = getcheck_mygoto($user);
							if ($pr > 0) {
								echo '<font color=red>Внимание! Пассивные умения наёмника "Ускорение" будет игнорироваться!</font><BR>';
							}

							echo '<br>';
							echo '<input type="submit" name="accepttogroup" value="Принять в группу"> ';
							echo '<input type="submit" name="rejecttogroup" value="Отказать в принятии">';
						}
					}
				} else {
					// если я участник группы
					echo '<a href="'.$mself.'?leavegroup=1&side=left&tab=2&rright">Покинуть группу</a><br><br>';
				}

				if (isset($_GET['error']) && $_GET['error'] >= 6 && $_GET['error'] <= 14) {
					echo '<br><br><font color=red>';
					switch($_GET['error']) {
						case 6:
							echo "Персонаж не найден.";
						break;
						case 7:
							echo "Персонаж не находится на карте.";
						break;
						case 8:
							echo "Группа не находится рядом с вами.";
						break;
						case 9:
							echo "<font color=green>Вы удачно подали заявку на присоединение к группе.</font>";
						break;
						case 10:
							echo "Эта группа не принимает заявок на присоединение.";
						break;
						case 13:
							echo "Эта группа двигается или находится в бою, поэтому не может принять вашу заявку.";
						break;
						case 14:
							echo "Ваша заявка уже есть в этой группе. Ожидайте решения лидера.";
						break;
					}	
					echo '</font><br><br>';
				}

			
				echo '<br><br>Список группы: <br>';
				$team = unserialize($outmap['team_cache']);
				echo 'Лидер: '.$team[$outmap['leader']].'<br>';

			
				if (strlen($outmap['team'])) {
					$t = explode(',',substr($outmap['team'],0,strlen($outmap['team'])-1));
					$team_cache = unserialize($outmap['team_cache']);
					$i = 1;
					while(list($k,$v) = each($t)) {
						// показываем членов группы
						if (!empty($v)) {
							echo $i.' '.$team_cache[$v];
							if ($outmap['leader'] == $user['id']) echo '<input type="checkbox" name="kick_'.$v.'" value="1"><br>'; else echo '<br>';
							$i++;
						}                                                                        
					}
					if ($outmap['leader'] == $user['id']) echo '<br><input type="submit" name="kickfromgroup" value="Выкинуть из группы"><br><br>';
				}
				?>
				<br>
				<IMG SRC=http://i.oldbk.com/i/lock.gif WIDTH=20 HEIGHT=15 BORDER=0 ALT="приват группе" style="cursor:pointer" onClick="Prv('private [zgroup] ')"> приват группе<br>

				</div>
				</form>
				</td></tr></table></TD></TR></TABLE>
				<?php } ?>
			</div>

			<div id="d3" style="display:none;">
				<?php if ($tab == 3) { ?>
				<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=900 border=0 height=205 cellspacing="0" cellpadding="0" style="background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/tab_bg.jpg);"><tr><td valign=top>
				<br><br>
				<div style="margin-left:20px;">
				<?php
					$q31exists = false;
					$q31 = false;
					$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31"') or die();
					if (mysql_num_rows($q) > 0) {
						$q31 = mysql_fetch_assoc($q);
						$q31exists = true;
					}
					if ($q31 !== false && $q31['val'] == 13) {
						$q31 = false;
						$q31exists = false;
					}
					if ($q31 !== false) {
						echo '<b>Задание Героического квеста:</b> ';
						if ($q31['val'] == 0) echo 'Подойти к Скупщику.<br>';
						if ($q31['val'] == 1) echo 'Принести 10 слитков золота скупщику краденого.<br>';
						if ($q31['val'] == 2) echo 'Принести 3 статуи скупщику краденого.<br>';
						if ($q31['val'] == 3) echo 'Принести 20 чеков на предъявителя скупщику краденого.<br>';
						if ($q31['val'] == 4) echo 'Сходить к Пилигриму.<br>';
						if ($q31['val'] == 5) echo 'Принести 15 черепов Пилигриму.<br>';
						if ($q31['val'] == 6) echo 'Вернуться к Скупщику.<br>';
						if ($q31['val'] == 7) echo 'Принести Скупщику по 100 ключей каждого вида (Ключ №1, Ключ №2, Ключ №3, Ключ №4).<br>';
						if ($q31['val'] == 8) echo 'Сходить к Магу.<br>';
						if ($q31['val'] == 9) echo 'Принести Магу 50 штук «Зелье Мага».<br>';
						if ($q31['val'] == 10) echo 'Сходить к Священнику.<br>';
						if ($q31['val'] == 11) echo 'Победить в 600 хаотических битвах и нанести урон в 30 битвах против Исчадия Хаоса<br>';
						if ($q31['val'] == 12) echo 'Отдать Одинокому Рыцарю 150 тысяч своей репутации, «Ларец» от Скупщика краденого и «Эликсир» от Мага<br>';
					}

					$qalllist = array(
						1 => "Лечебное снадобье",
						2 => "Бабушкин пирог",
						3 => "Разбойничья переправа",
						4 => "Пропавшая грамота",
						5 => "Сумка почтальона",
						6 => "Загадочный клинок",
						7 => "Новые стрелы",
						8 => "Потерявшийся ребенок",
						9 => "Магическое зеркало",
						10 => "Оборотень",
						11 => "Чемпион",
						12 => "Почтовый дилижанс",
						13 => "Шляпа для лесоруба",
						14 => "Магическая сила",
						15 => "Гнилая вода",
						16 => "Создание амулета",
						17 => "Цветные сердца",
						18 => "Семейный секрет",
						19 => "Помощь по конюшне",
						20 => "Странная находка",
						21 => "Прохудившаяся лодка",
						22 => "Больная лошадь",
						23 => "Сейф для трактирщика",
						24 => "Подготовка к зимовке",
						25 => "Праздничные приготовления",
						26 => "Украденная икона",
						27 => "Людоед",
						28 => "Диковинные чётки",
						29 => "Приворотное зелье",
						30 => "Дикий зверь",
					);



					$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or mydie();
					if (mysql_num_rows($q) > 0) {
						$q = mysql_fetch_assoc($q);
						$qu = array();
						echo '<b>Текущий квест:</b> '.$qalllist[$q['q_id']].'<br>';
						require_once('./mapquests/'.$q['q_id'].'.php');
						switch($q['q_id']) {
							case 1:								
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003004),$q_status[0]);
								$qu[] = $q_status[0];
								if ($q['step'] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003002),$q_status[1]);
									$q_status[1] = str_replace("%N2%",QItemCount($user,3003003),$q_status[1]);
									$q_status[1] = str_replace("%N3%",QItemCount($user,3003001),$q_status[1]);
									$qu[] = $q_status[1];
								}
							break;
							case 2:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003006),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003010),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003008),$q_status[0]);

								$qu[] = $q_status[0];

								$t = explode("/",$q['addinfo']);
								if ($t[0] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003005),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($t[1] == 1) {
									$q_status[2] = str_replace("%N1%",QItemCount($user,3003007),$q_status[2]);
									$qu[] = $q_status[2];
								}
								if ($t[2] == 1) $qu[] = $q_status[3];
							break;
							case 3:
								if ($q['step'] != 2) {
									$qu[] = $q_status[0];
									if ($q['step'] == 1) {
										$q_status[1] = str_replace("%N1%",QItemCount($user,3003011),$q_status[1]);
										$q_status[1] = str_replace("%N2%",QItemCount($user,3003012),$q_status[1]);
										$q_status[1] = str_replace("%N3%",QItemCount($user,3003013),$q_status[1]);
										$q_status[1] = str_replace("%N4%",QItemCount($user,3003014),$q_status[1]);
										$qu[] = $q_status[1];
									}
								} else {
									$qu[] = $q_status[2];
								}	
							break;
							case 4:
								$qu[] = $q_status[0];
								if ($q['step'] == 0) $qu[] = $q_status[1];
								if ($q['step'] == 1) $qu[] = $q_status[2];
								if ($q['step'] == 2) $qu[] = $q_status[3];
								if ($q['step'] == 3) {
									if(QItemExistsID($user,3003015)) {
										$qu[] = $q_status[5];
									} else {
										$qu[] = $q_status[4];
									}
								}
							break;
							case 5:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003016),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003017),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003018),$q_status[0]);

								$qu[] = $q_status[0];
								$t = explode("/",$q['addinfo']);
								if ($t[0] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003005),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($t[1] == 1) {
									$q_status[2] = str_replace("%N1%",QItemCount($user,3003012),$q_status[2]);
									$qu[] = $q_status[2];
								}
							break;
							case 6:
								$qu[] = $q_status[0];
								if ($q['step'] == 1) $qu[] = $q_status[1];
								if ($q['step'] == 2) $qu[] = $q_status[2];
								if ($q['step'] == 3) {
									$q_status[3] = str_replace("%N1%",QItemCount($user,3003020),$q_status[3]);
									$qu[] = $q_status[3];
								}
								if ($q['step'] == 4) $qu[] = $q_status[4];
								if ($q['step'] == 5) $qu[] = $q_status[5];

							break;
							case 7:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003023),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003024),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003025),$q_status[0]);

								$qu[] = $q_status[0];
								if ($q['addinfo'] > 0) {
									$q_status[1] = str_replace("%N1%",$q['addinfo']-1,$q_status[1]);
									$qu[] = $q_status[1];
								}

							break;
							case 8:
								$qu[] = $q_status[0];
								if ($q['step'] == 0) $qu[] = $q_status[1];
								if ($q['step'] == 1) $qu[] = $q_status[2];
								if ($q['step'] == 2) $qu[] = $q_status[3];
								if ($q['step'] == 3) $qu[] = $q_status[4];
								if ($q['step'] == 4) $qu[] = $q_status[5];
								if ($q['step'] == 5) $qu[] = $q_status[5];
								if ($q['step'] == 6) $qu[] = $q_status[6];
							break;
							case 9:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003030),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003033),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003034),$q_status[0]);

								$qu[] = $q_status[0];
								$t = explode("/",$q['addinfo']);
								if ($t[0] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003029),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($t[1] == 1) {
									$q_status[2] = str_replace("%N1%",QItemCount($user,3003032),$q_status[2]);
									$qu[] = $q_status[2];
								}
								if ($t[1] == 2) {
									$q_status[2] = str_replace("%N1%",QItemCount($user,3003032),$q_status[2]);
									$qu[] = $q_status[2];
									if (!QItemExists($user,3003032)) {
										$q_status[3] = str_replace("%N1%",QItemCount($user,1),$q_status[3]);
										$q_status[3] = str_replace("%N2%",QItemCount($user,3003031),$q_status[3]);
										$qu[] = $q_status[3];
									}
								}
							break;
							case 10:
								$qu[] = $q_status[0];
								if ($q['step'] == 0) $qu[] = $q_status[1];
								if ($q['step'] == 1) $qu[] = $q_status[2];
								if ($q['step'] == 2) $qu[] = $q_status[3];
								if ($q['step'] == 3) {
									$q_status[4] = str_replace("%N1%",QItemCount($user,3003005),$q_status[4]);
									$qu[] = $q_status[4];
								}
								if ($q['step'] == 4) $qu[] = $q_status[5];
								if ($q['step'] == 5) $qu[] = $q_status[6];
								if ($q['step'] == 6) {
									$q_status[7] = str_replace("%N1%",QItemCount($user,3003005),$q_status[7]);
									$qu[] = $q_status[7];
								}
								if ($q['step'] == 7) {
									$qu[] = $q_status[8];
								}
								if ($q['step'] == 8) {
									$q_status[9] = str_replace("%N1%",QItemCount($user,3003038),$q_status[9]);
									$qu[] = $q_status[9];
								}
								if ($q['step'] == 9) $qu[] = $q_status[10];
							break;
							case 11:
								$qu[] = $q_status[0];
							break;
							case 12:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003041),$q_status[0]);
								$qu[] = $q_status[0];
							break;
							case 13:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003045),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003044),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003046),$q_status[0]);

								$qu[] = $q_status[0];
								$t = explode("/",$q['addinfo']);
								if ($t[0] > 0) {
									$q_status[1] = str_replace("%N1%",$t[0]-1,$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($t[1] > 0 && $t[1] != 2) $qu[] = $q_status[2];
							break;
							case 14:

								$q_status[0] = str_replace("%N1%",QItemCount($user,3003050),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003051),$q_status[0]);
								$q_status[0] = str_replace("%N3%",((QItemCount($user,3003047)+QItemCount($user,3003048))/2),$q_status[0]);

								$qu[] = $q_status[0];
								$t = explode("/",$q['addinfo']);
								if ($t[0] == 0) $qu[] = $q_status[1];
								if ($t[0] == 1) $qu[] = $q_status[2];
								if ($t[0] == 2) $qu[] = $q_status[3];
								if ($t[0] == 3 && ($t[1] == 0 || $t[2] == 0)) $qu[] = $q_status[4];
								if ($t[1] == 1) {
									$q_status[5] = str_replace("%N1%",QItemCount($user,3003049),$q_status[5]);
									$qu[] = $q_status[5];
								}
							break;
							case 15:
								if ($q['step'] == 0) $qu[] = $q_status[0];
								if ($q['step'] == 1) $qu[] = $q_status[1];
								if ($q['step'] == 2) $qu[] = $q_status[2];
								if ($q['step'] == 3) {
									$q_status[3] = str_replace("%N1%",QItemCount($user,3003005),$q_status[3]);
									$qu[] = $q_status[3];
								}
								if ($q['step'] == 4) $qu[] = $q_status[4];
								if ($q['step'] == 5) {
									$q_status[5] = str_replace("%N1%",QItemCount($user,3003054),$q_status[5]);
									$q_status[5] = str_replace("%N2%",QItemCount($user,3003053),$q_status[5]);
									$q_status[5] = str_replace("%N3%",QItemCount($user,3003055),$q_status[5]);
									$qu[] = $q_status[5];
								}
								if ($q['step'] == 6) $qu[] = $q_status[6];
							break;
							case 16:
								$qu[] = $q_status[0];
								if ($q['step'] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003059),$q_status[1]);
									$q_status[1] = str_replace("%N2%",QItemCount($user,3003038),$q_status[1]);
									$q_status[1] = str_replace("%N3%",QItemCount($user,3003002),$q_status[1]);
									$q_status[1] = str_replace("%N4%",QItemCount($user,3003058),$q_status[1]);
									$q_status[1] = str_replace("%N5%",QItemCount($user,3003061),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($q['step'] == 2) $qu[] = $q_status[2];
								if ($q['step'] == 3) $qu[] = $q_status[3];
							break;
							case 17:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003062),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003063),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003064),$q_status[0]);
								$q_status[0] = str_replace("%N4%",QItemCount($user,3003065),$q_status[0]);
								$q_status[0] = str_replace("%N5%",QItemCount($user,3003066),$q_status[0]);
								$qu[] = $q_status[0];
								if ($q['addinfo'] == 1) $qu[] = $q_status[1];
								if ($q['addinfo'] == 2) $qu[] = $q_status[2];
							break;
							case 18:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003005),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003067),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003050),$q_status[0]);
								$q_status[0] = str_replace("%N4%",QItemCount($user,3003034),$q_status[0]);
								$qu[] = $q_status[0];
								if ($q['addinfo'] == 1) $qu[] = $q_status[1];
							break;
							case 19:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003069),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003070),$q_status[0]);
								$q_status[0] = str_replace("%N3%",QItemCount($user,3003072),$q_status[0]);
								$qu[] = $q_status[0];
								$t = explode("/",$q['addinfo']);
								if ($t[0] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003005),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($t[1] == 1) {
									$qu[] = $q_status[2];
								}
							break;
							case 20:
								if ($q['step'] == 0) $qu[] = $q_status[0];
								if ($q['step'] == 1) $qu[] = $q_status[1];
								if ($q['step'] == 2) $qu[] = $q_status[2];
								if ($q['step'] == 3) $qu[] = $q_status[3];
							break;
							case 21:
								$t = explode("/",$q['addinfo']);
								if ($t[0] == 0) $qu[] = $q_status[0];
								if ($t[0] > 0) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003075),$q_status[1]);
									$q_status[1] = str_replace("%N2%",QItemCount($user,3003076),$q_status[1]);
									$q_status[1] = str_replace("%N3%",QItemCount($user,3003077),$q_status[1]);
									$qu[] = $q_status[1];
									if ($t[0] == 1) {
										$q_status[2] = str_replace("%N1%",QItemCount($user,3003005),$q_status[2]);
										$qu[] = $q_status[2];
									}
								}
							break;
							case 22:
								if ($q['step'] != 4 && $q['step'] != 5) $qu[] = $q_status[0];								
								if ($q['step'] == 0) $qu[] = $q_status[1];
								if ($q['step'] == 1) $qu[] = $q_status[2];
								if ($q['step'] == 2) $qu[] = $q_status[3];
								if ($q['step'] == 3) {
									$q_status[4] = str_replace("%N1%",QItemCount($user,3003080),$q_status[4]);
									$q_status[4] = str_replace("%N2%",QItemCount($user,3003079),$q_status[4]);
									$q_status[4] = str_replace("%N3%",QItemCount($user,3003003),$q_status[4]);
									$qu[] = $q_status[4];
								}
								if ($q['step'] == 4 || $q['step'] == 5) $qu[] = $q_status[5];				
							break;
							case 23:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003084),$q_status[0]);
								$qu[] = $q_status[0];
								if ($q['step'] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003005),$q_status[1]);
									$q_status[1] = str_replace("%N2%",QItemCount($user,3003082),$q_status[1]);
									$q_status[1] = str_replace("%N3%",QItemCount($user,3003083),$q_status[1]);
									$qu[] = $q_status[1];
								}
							break;
							case 24:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003086),$q_status[0]);
								$q_status[0] = str_replace("%N2%",QItemCount($user,3003085),$q_status[0]);	
								$qu[] = $q_status[0];
							        $t = explode("/",$q['addinfo']);
								if ($t[1] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003042),$q_status[1]);
									$qu[] = $q_status[1];
								}
							break;
							case 25:
								if ($q['step'] == 0) {
									$qu[] = $q_status[0];
								}
								$t = explode("/",$q['addinfo']);
								if ($q['step'] == 1) {
									if ($t[0] == 0) $qu[] = $q_status[1];
									if ($t[1] == 0) $qu[] = $q_status[2];
									if ($t[2] == 0) $qu[] = $q_status[3];
									if ($t[3] == 0) $qu[] = $q_status[4];
									$qu[] = $q_status[5];
								}
								if ($q['step'] == 2) {
									$qu[] = $q_status[6];
								}
							break;			
							case 26:
								$qu[] = $q_status[0];
								if ($q['step'] == 0) {
									$qu[] = $q_status[1];
								}
								if ($q['step'] == 2) {
									$qu[] = $q_status[2];
								}
								if ($q['step'] == 3) {
									$qu[] = $q_status[3];
								}
								if ($q['step'] == 4) {
									$qu[] = $q_status[4];
								}
								if ($q['step'] == 5) {
									$qu[] = $q_status[5];
								}
							break;
							case 27:
								$qu[] = $q_status[$q['step']];
							break;			
							case 28:
								$q_status[0] = str_replace("%N1%",QItemCount($user,3003200),$q_status[0]);
								$qu[] = $q_status[0];
								$t = explode("/",$q['addinfo']);
								if ($t[0] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003203),$q_status[1]);
									$q_status[1] = str_replace("%N2%",QItemCount($user,3003202),$q_status[1]);
									$q_status[1] = str_replace("%N3%",QItemCount($user,3003201),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($t[0] == 3) {
									$qu[] = $q_status[2];
								}
								if ($t[0] == 8) {
									$qu[] = $q_status[3];
								}
								if ($t[0] == 9) {
									$qu[] = $q_status[4];
								}

							break;
							case 29:
								if ($q['step'] == 0) {
									$qu[] = $q_status[0];
								}
								if ($q['step'] == 1) {
									$q_status[1] = str_replace("%N1%",QItemCount($user,3003002),$q_status[1]);
									$q_status[1] = str_replace("%N2%",QItemCount($user,3003059),$q_status[1]);
									$q_status[1] = str_replace("%N3%",QItemCount($user,3003206),$q_status[1]);
									$qu[] = $q_status[1];
								}
								if ($q['step'] >= 2) {
									if ($q['step'] == 9) {
										$q_status[9] = str_replace("%N1%",QItemCount($user,3003210),$q_status[9]);
									}
									$qu[] = $q_status[$q['step']];
								}
							break;
							case 30:
								if ($q['step'] == 5) {
									$q_status[5] = str_replace("%N1%",QItemCount($user,3003213),$q_status[5]);
									$q_status[5] = str_replace("%N2%",QItemCount($user,3003214),$q_status[5]);
									$q_status[5] = str_replace("%N3%",QItemCount($user,3003215),$q_status[5]);
								}
								if ($q['step'] == 11) {
									$q_status[11] = str_replace("%N1%",QItemCount($user,3003216),$q_status[11]);
									$q_status[11] = str_replace("%N2%",QItemCount($user,3003217),$q_status[11]);
									$q_status[11] = str_replace("%N3%",QItemCount($user,3003218),$q_status[11]);
								}
								if ($q['step'] == 12) {
									$q_status[12] = str_replace("%N1%",QItemCount($user,3003220),$q_status[12]);
								}
								$qu[] = $q_status[$q['step']];
							break;							

						}
						if (count($qu)) {
							while(list($k,$v) = each($qu)) {
								echo '<b>Задание:</b> '.$v.'<br>';
							}
						} else {
							if (!$q31exists) echo 'Текущие задания отсутствуют.';
						}
					} else {
						if (!$q31exists) echo 'Текущие задания отсутствуют.';
					}
				?>
				<br><br>
				<?php 
					$q = mysql_query('SELECT * FROM map_qvar WHERE var = "qcomplete" AND owner = '.$user['id']);
					if ($q !== FALSE) {
						if (mysql_num_rows($q) > 0) {
							$qv = mysql_fetch_assoc($q);
							$qclist = explode("/",$qv['val']);
							echo 'Выполнено заданий: <b>'.count($qclist).'</b>/<b>'.$mlqallcount.'</b>.<br><br><b>Список выполненных:</b> <br>';

							$i = 1;
							while(list($k,$v) = each($qclist)) {
								echo $i.'. '.$qalllist[$v].'<br>';
								$i++;
							}
						}
					}
				?>
				</div>
				</td></tr></table></TD></TR></TABLE>
				<?php } ?>
			</div>

			<div id="d4" style="display:none;">
				<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=900 border=0 height=205 cellspacing="0" cellpadding="0" style="background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/tab_bg.jpg);"><tr><td valign=top>
				<br><br>
				<div style="margin-left:20px;">
					<form action="<?=$mself;?>?side=left&tab=4" method="POST">
						Время обновления миникарты: 
							<select name="updatetime">
								<option value="15" <?php if (isset($_SESSION['map_updatetime']) && $_SESSION['map_updatetime'] == 15) echo 'selected'; ?>>15 секунд</option>
								<option value="30" <?php if (isset($_SESSION['map_updatetime']) && $_SESSION['map_updatetime'] == 30) echo 'selected'; ?>>30 секунд</option>
								<option value="60" <?php if (isset($_SESSION['map_updatetime']) && $_SESSION['map_updatetime'] == 60) echo 'selected'; ?>>60 секунд</option>
							</select><br><br>
						<div class="btn-control">
                            <input class="button-mid btn" type="submit" name="saveoptions" value="сохранить">
                        </div>
					</form>
				</div>
				<div style="height:100px;">&nbsp;</div>
				</td></tr></table></TD></TR></TABLE>
			</div>


			<script>
			<?php
				echo 'SwitchTab('.$tab.')';
			?>
			</script>
			</center>

			<div id=hint3 class=ahint></div>
			<script>top.onlineReload(true)</script>

			<?php
				if (isset($_GET['rright'])) {
					echo '<script>parent.frames["rightmap"].location.href = "'.$mself.'?side=right&"+Math.random();</script>';
				}
			?>                                                 
			</body>
			</html>
<?php
		} else die();
	}
?>