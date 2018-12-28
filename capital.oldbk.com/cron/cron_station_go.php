#!/usr/bin/php          
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";


if(!lockCreate("cron_station_go")) {
	exit("Script already running.");
}
echo "Running cron_station_go ...\n";

function MyDie($txt) {
	echo time().":".$txt."\n";
	lockDestroy("cron_station_go");
	die();
}


echo date("H:i\n");

// тут запускаем кареты

$q = mysql_query('START TRANSACTION') or mydie();
$q = mysql_query('SELECT * FROM station') or mydie(mysql_error().":".__LINE__);
while($s = mysql_fetch_assoc($q)) {
	$h = date("H");
	$m = date("i");		

	$t = explode(":",$s['starttime']);
	$ht = $t[0];
	$mt = $t[1];

	$bgo = false;

	if ($ht == $h && $mt == $m) {
		echo "Time to go, searching for users\n";
		$bgo = true;

		$q2 = mysql_query('SELECT users.id AS userid, oldbk.inventory.id AS invid, oldbk.inventory.letter AS invletter FROM users LEFT JOIN oldbk.inventory ON users.id = oldbk.inventory.owner WHERE users.room = 61000 AND users.battle = 0 AND users.ldate+240 >= "'.time().'" AND oldbk.inventory.prototype = '.$s['protoid']);
		$uids = array();
		$iids = array();
		if (mysql_num_rows($q2) > 0) {
			// users exists, checking tickets
			while($t = mysql_fetch_assoc($q2)) {
				$test = date("d.m.Y")." в ".$s['starttime'];
				if (strpos($t['invletter'],$test) !== FALSE) {
					$uids[] = $t['userid'];
					$iids[] = $t['invid'];
				}
			}
		}

		if (count($uids) && count($iids)) {
			echo "Users found - moving\n";

			// search for empty room
			$roomid = 61001;
			$q3 = mysql_query('SELECT * FROM `station_go` ORDER BY `room` ASC') or mydie(mysql_error().":".__LINE__);
			if (mysql_num_rows($q3) > 0) {
				while($res = mysql_fetch_assoc($q3)) {
					if ($roomid == $res['room']) {
						$roomid += 1;
					} else {
						break;
					}
				}
			}
			if ($roomid >= 62000) die(mysql_error().":".__LINE__);


			// room found
			mysql_query('INSERT INTO station_go (room,num,endtime,tocity,img) VALUES("'.$roomid.'","'.$s['num'].'","'.(time()+($s['time']*60)).'","'.$s['tocity'].'","'.$s['img'].'")') or mydie(mysql_error().":".__LINE__); 
			
			// users room 
			mysql_query('UPDATE users SET room = '.$roomid.' WHERE id IN ('.implode(",",$uids).')') or mydie(mysql_error().":".__LINE__);

			// delete tickets
			mysql_query('DELETE FROM oldbk.inventory WHERE id IN ('.implode(",",$iids).')') or mydie(mysql_error().":".__LINE__);

			addch_group('<font color=red>Внимание!</font> Карета отправляется! <BR>\'; top.frames[\'main\'].location=\'station_go.php\'; var z = \'   ',$uids);
		} else {
			echo "No users found for moving\n";
		}
	}

	if ($bgo) {
		break;
	}
}
$q = mysql_query('COMMIT') or mydie();


// тут чистим кареты
$q = mysql_query('SELECT * FROM station_go WHERE endtime < '.time());
while($s = mysql_fetch_assoc($q)) {
	if ($s['endtime']+(10*60) < time()) {
		// выкидываем всех в другой город после 5 минут ожидания
		if ($CITY_NAME == "capitalcity") {
			$q2 = mysql_query('SELECT * FROM users WHERE room = '.$s['room'].' AND id_city = 0');
		} elseif ($CITY_NAME == "avaloncity") {
			$q2 = mysql_query('SELECT * FROM users WHERE room = '.$s['room'].' AND id_city = 1');
		}
		$mids = array();
		$fbattle = false;
		while($u = mysql_fetch_assoc($q2)) {
			if ($u['battle']) {
				$fbattle = true;
				continue;
			}
			if ($u['ldate']+240 < time()) {
				$mids[] = $u['id'];
			} else {
				$fbattle = true;
			}
		}

		if (count($mids)) {
			// выкидываем слоупоков или оффлайнов в другой город

			while(list($k,$v) = each($mids)) {
				// меняем штраф на город
				$q = mysql_query('SELECT * FROM oldbk.map_qvar WHERE owner = '.$v.' AND var = "lastcity"');
				if (mysql_num_rows($q) > 0) {
					// если есть штраф
					$lastcity = mysql_fetch_assoc($q) or die();
					$t = explode(":",$lastcity['val']);
					$t[1] = $s['tocity'];
					mysql_query('UPDATE oldbk.map_qvar SET val = "'.implode(":",$t).'" WHERE owner = '.$v.' AND var = "lastcity"');
				}


				if ($s['tocity'] == 1) {
					mysql_query('UPDATE users SET room = 61000 WHERE id = '.$v);
					// собираем бонусы
					$bonus = array();
					$q = mysql_query('SELECT * FROM users_bonus WHERE owner = '.$v);
					while($b = mysql_fetch_assoc($q)) {
						$bonus[] = $b;
					}

					mysql_query('DELETE from users_bonus where owner = '.$v);

					mysql_query('UPDATE users set odate=0, id_city=1 where id = '.$v.' and id_city=0');
					if (mysql_affected_rows()>0) {
						// переехали
						while(list($ka,$va) = each($bonus)) {
							mysql_query('
								INSERT INTO avalon.users_bonus (owner,sila,sila_count,lovk,lovk_count,inta,inta_count,intel,intel_count,mudra,mudra_count,maxhp,maxhp_count,expbonus,expbonus_count,refresh,battle,usec) 
								VALUES ("'.$va['owner'].'","'.$va['sila'].'","'.$va['sila_count'].'","'.$va['lovk'].'","'.$va['lovk_count'].'","'.$va['inta'].'","'.$va['inta_count'].'","'.$va['intel'].'","'.$va['intel_count'].'","'.$va['mudra'].'","'.$va['mudra_count'].'","'.$va['maxhp'].'","'.$va['maxhp_count'].'","'.$va['expbonus'].'","'.$va['expbonus_count'].'","'.$va['refresh'].'","'.$va['battle'].'","'.$va['usec'].'")
							');
						}

						mysql_query('DELETE from avalon.effects where owner='.$v);						
						mysql_query('INSERT INTO avalon.effects SELECT NULL,`type`,`name`,`time`,`sila`,`lovk`,`inta`,`vinos`, `intel` , `owner`, `lastup`, `idiluz`,`pal`, `add_info`, `battle` ,`eff_bonus` from oldbk.effects where owner = '.$v);
						mysql_query('DELETE from oldbk.effects where owner = '.$v);
					}
				} elseif ($s['tocity'] == 0) {
					mysql_query('UPDATE users SET room = 61000 WHERE id = '.$v);
					// собираем бонусы
					$bonus = array();
					$q = mysql_query('SELECT * FROM users_bonus WHERE owner = '.$v);
					while($b = mysql_fetch_assoc($q)) {
						$bonus[] = $b;
					}
					mysql_query('DELETE from users_bonus where owner='.$v);

					mysql_query('UPDATE users set odate=0, id_city=0 where id='.$v.' and id_city=1;');
					if (mysql_affected_rows()>0) {
						// переехали
						while(list($ka,$va) = each($bonus)) {
							mysql_query('
								INSERT INTO oldbk.users_bonus (owner,sila,sila_count,lovk,lovk_count,inta,inta_count,intel,intel_count,mudra,mudra_count,maxhp,maxhp_count,expbonus,expbonus_count,refresh,battle,usec) 
								VALUES ("'.$va['owner'].'","'.$va['sila'].'","'.$va['sila_count'].'","'.$va['lovk'].'","'.$va['lovk_count'].'","'.$va['inta'].'","'.$va['inta_count'].'","'.$va['intel'].'","'.$va['intel_count'].'","'.$va['mudra'].'","'.$va['mudra_count'].'","'.$va['maxhp'].'","'.$va['maxhp_count'].'","'.$va['expbonus'].'","'.$va['expbonus_count'].'","'.$va['refresh'].'","'.$va['battle'].'","'.$va['usec'].'")
							');
						}

						mysql_query('DELETE from oldbk.effects where owner = '.$v);						
						mysql_query('INSERT INTO oldbk.effects SELECT NULL,`type`,`name`,`time`,`sila`,`lovk`,`inta`,`vinos`, `intel` , `owner`, `lastup`, `idiluz`,`pal`, `add_info`, `battle` ,`eff_bonus`  from avalon.effects where owner='.$v);
						mysql_query('DELETE from avalon.effects where owner='.$v);
					}
				}
			}

		} else {
			if ($fbattle) {
				// есть бой - ничего не делаем, ждём окончание
			} else {
				// удаляем заявку
				mysql_query('DELETE FROM station_go WHERE id = '.$s['id']);
			}
		}
	} else {
		// всем шлём обновление экрана
		$q2 = mysql_query('SELECT * FROM users WHERE room = '.$s['room'].' AND battle = 0');
		$mids = array();
		while($u = mysql_fetch_assoc($q2)) {
			$mids[] = $u['id'];
		}
		if (count($mids)) {
			addch_group('\'; top.frames[\'main\'].location=\'station_go.php\'; var z = \'   ',$mids);
		}
	}
}

echo "Finishing script. Destroy lock.\n";
lockDestroy("cron_station_go");
?>