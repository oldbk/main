#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";
include "/www/".$CITY_NAME.".oldbk.com/fsystem.php"; //настройки для кол.хилов в бою ботом

function MyDie($txt) {
	echo time().":".$txt."\n";
	lockDestroy("cron_op");
	die();
}

function GetUrlOp($ip,$path,$city) {
	$fp = fsockopen($ip, 80, $errno, $errstr, 30);
	if (!$fp) {
		return FALSE;
	} else {
		$out = "GET ".$path." HTTP/1.0\r\n";
		$out .= "Host: ".$city."\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		$in = "";	
		while (!feof($fp)) {
			$in .= fgets($fp, 128);
		}
		fclose($fp);
		return TRUE;
	}
}


if( !lockCreate("cron_op") ) {
	exit("Script already running.");
}
echo "Running cron_op ...\n";
echo date("d/m/Y H:i:s\n");

mysql_query('START TRANSACTION') or mydie(mysql_error().":".__LINE__);
if (date("j") == 1 && date("H") == 0) {
	// запускаемся раз в месяц первого числа в 00.00
 	mysql_query('UPDATE oldbk.`clans` SET voinst=0 WHERE voinst > 0') or mydie(mysql_error().":".__LINE__);

	try {
		$_voin_list = mysql_query('SELECT u.* FROM oldbk.`users_voin` uv inner join users u on u.id = uv.owner where u.bot=0 AND u.klan!="radminion" and u.klan!="Adminion" order by uv.voin desc LIMIT 100;');
		$_i_voin = 0;
		$_voin_reward_goden = 30;
		$_voin_reward__end = (new DateTime())->modify('+'.$_voin_reward_goden.' day');
		while ($row = mysql_fetch_array($_voin_list)) {
			$_i_voin++;

		    $item_link = 'svitok_exp_40';
			$item_id = 4164;
			$item_name = 'Повышенный опыт (+40%)';
			switch (true) {
				case ($_i_voin < 11):
					$item_id = 4170;
					$item_name = 'Повышенный опыт (+100%)';
					$item_link = 'svitok_exp_100';
					break;
				case ($_i_voin > 10 && $_i_voin < 26):
					$item_id = 4168;
					$item_name = 'Повышенный опыт (+80%)';
					$item_link = 'svitok_exp_80';
					break;
				case ($_i_voin > 25 && $_i_voin < 51):
					$item_id = 4166;
					$item_name = 'Повышенный опыт (+60%)';
					$item_link = 'svitok_exp_60';
					break;
			}
			put_bonus_item($item_id, $row, 'Удача', [], [
				'goden'     => $_voin_reward_goden,
				'dategoden' => $_voin_reward__end->getTimestamp()
			]);

			$message = sprintf('<font color=red>Внимание!</font> Вы получаете <b><a href="https://oldbk.com/encicl/mag1/%s.html" target="_blank">%s</a></b> за %d место в <a href="http://top.oldbk.com/rate/ppl" target="_blank">Топ-100 бойцов</a>, прославившихся своей воинственностью в клановых войнах.', $item_link, $item_name, $_i_voin);
			addchp ($message,'{[]}'.$row['login'].'{[]}',-1,$row['id_city']) or mydie(mysql_error().":".__LINE__);
		}

		//чистим таблицу личной воинственности
		mysql_query('TRUNCATE `oldbk`.`users_voin`') or mydie(mysql_error().":".__LINE__);
    } catch (Exception $ex) {
	    \components\Helper\FileHelper::writeException($ex, 'cron_op');
    }


	$q = mysql_query('SELECT count(oldbk.inventory.id) as allcount,oldbk.users.login as mylogin,oldbk.users.id_city as id_city FROM oldbk.inventory LEFT JOIN oldbk.users ON oldbk.users.id = oldbk.inventory.owner WHERE prototype = 3002500 GROUP BY owner') or mydie(mysql_error().":".__LINE__);
	if (mysql_num_rows($q) > 0) {
		while($u = mysql_fetch_assoc($q)) {
			addchp ('<font color=red>Внимание!</font> Изъят "Череп" (x'.$u['allcount'].') в связи с окончанием периода Противостояния и началом нового.','{[]}'.$u['mylogin'].'{[]}',-1,$u['id_city']) or mydie(mysql_error().":".__LINE__);
		}
	}
	mysql_query('DELETE FROM oldbk.inventory WHERE prototype = 3002500') or mydie(mysql_error().":".__LINE__);

	// изымаем осколки
	$q = mysql_query('SELECT count(oldbk.inventory.id) as allcount,oldbk.users.login as mylogin,oldbk.users.id_city as id_city FROM oldbk.inventory LEFT JOIN oldbk.users ON oldbk.users.id = oldbk.inventory.owner WHERE (prototype = 3002501 or prototype = 3002502 or prototype = 3002503) GROUP BY owner') or mydie(mysql_error().":".__LINE__);
	if (mysql_num_rows($q) > 0) {
		while($u = mysql_fetch_assoc($q)) {
			addchp ('<font color=red>Внимание!</font> Изъят "Осколок черепа" (x'.$u['allcount'].') в связи с окончанием периода Противостояния и началом нового.','{[]}'.$u['mylogin'].'{[]}',-1,$u['id_city']) or mydie(mysql_error().":".__LINE__);
		}
	}
	mysql_query('DELETE FROM oldbk.inventory WHERE (prototype = 3002501 or prototype = 3002502 or prototype = 3002503) ') or mydie(mysql_error().":".__LINE__);

	

	// узнаём какая склонка круче
	$best = 6;
	$q = mysql_query('SELECT align, sum( score ) AS `sumscore` FROM avalon.op_score GROUP BY align ORDER BY sumscore DESC') or mydie(mysql_error().":".__LINE__);
	if (mysql_num_rows($q) > 0) {
		$best = mysql_fetch_assoc($q);
		$best = $best['align'];
	}

	echo "Best: ".$best."\r\n";

	// вешаем "За заслуги перед Склонностью"
	$uids = array();

	for ($level = 7; $level <= 14; $level++) {
		// вешаем светлым
		echo "Медали ".$level."\r\n";
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =0 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 3  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =1 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 3 )
					ORDER BY score DESC LIMIT 3")  or mydie(mysql_error().":".__LINE__);;
		while($u = mysql_fetch_assoc($q)) {
			$uids[] = $u['id'];
		}
		if (count($uids)) {
			print_r($uids);
			mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"099;") WHERE id IN ('.implode(",",$uids).') AND id_city = 0') or mydie(mysql_error().":".__LINE__);
			mysql_query('UPDATE avalon.users SET medals = CONCAT(medals,"099;") WHERE id IN ('.implode(",",$uids).') AND id_city = 1') or mydie(mysql_error().":".__LINE__);
			$uids = array();
		}


		// вешаем нейтралам
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =0 and users.align=2 ORDER BY score DESC LIMIT 3  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =1 and users.align=2 ORDER BY score DESC LIMIT 3 )
					ORDER BY score DESC LIMIT 3")	 or mydie(mysql_error().":".__LINE__);;
		while($u = mysql_fetch_assoc($q)) {
			$uids[] = $u['id'];
		}
		if (count($uids)) {
			print_r($uids);
			mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"200;") WHERE id IN ('.implode(",",$uids).') AND id_city = 0') or mydie(mysql_error().":".__LINE__);
			mysql_query('UPDATE avalon.users SET medals = CONCAT(medals,"200;") WHERE id IN ('.implode(",",$uids).') AND id_city = 1') or mydie(mysql_error().":".__LINE__);
			$uids = array();
		}


		// вешаем тёмным
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =0 and users.align=3 ORDER BY score DESC LIMIT 3  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =1 and users.align=3 ORDER BY score DESC LIMIT 3 )
					ORDER BY score DESC LIMIT 3") 	or mydie(mysql_error().":".__LINE__);;
		while($u = mysql_fetch_assoc($q)) {
			$uids[] = $u['id'];
		}
		if (count($uids)) {
			print_r($uids);
			mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"201;") WHERE id IN ('.implode(",",$uids).') AND id_city = 0') or mydie(mysql_error().":".__LINE__);
			mysql_query('UPDATE avalon.users SET medals = CONCAT(medals,"201;") WHERE id IN ('.implode(",",$uids).') AND id_city = 1') or mydie(mysql_error().":".__LINE__);
			$uids = array();
		}
	}

	function PutExpScroll($id,$own) {
	 	$dress = mysql_fetch_assoc(mysql_query('select * from oldbk.shop where id='.$id));
		if (!$dress) return;
	   
		$q = mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`idcity`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`present`,
				`otdel`,`gmp`,`gmeshok`, `group`,`letter`
				)
				VALUES
				('{$dress['id']}','{$own}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}','0',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
				'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','Храм Короля Артура'
				,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}'
				)"
		) or mydie(mysql_error().":".__LINE__);
	}

	// вешаем телепорты и повышенный опыт
	for ($level = 7; $level <= 14; $level++) {
		echo "Плюхи ".$level."\r\n";
		// светлые
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =0 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 10  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =1 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 10 )
					ORDER BY score DESC LIMIT 10")  or mydie(mysql_error().":".__LINE__);
		if (mysql_num_rows($q) > 0) {
			for ($i = 10; $i > 0; $i--) {
				if ($level == 14 && $i < 8) break;
				echo $i."\r\n";

				$op = mysql_fetch_assoc($q);
				if ($op === FALSE) break;

	                        echo $op['id']."\r\n";

				PutExpScroll(4160+$i,$op['id']);

				mysql_query('
					INSERT INTO oldbk.users_abils (owner,magic_id, allcount, findata)
					VALUES(
						"'.$op['id'].'",
						"2526",
						"'.$i.'",
						"0"
					) ON DUPLICATE KEY UPDATE
						`allcount` = `allcount` + '.$i
				) or mydie(mysql_error().":".__LINE__);
			}
		}

		// нейтралы
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =0 and users.align=2 ORDER BY score DESC LIMIT 10  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =1 and users.align=2 ORDER BY score DESC LIMIT 10 )
					ORDER BY score DESC LIMIT 10")	 or mydie(mysql_error().":".__LINE__);;
		if (mysql_num_rows($q) > 0) {
			for ($i = 10; $i > 0; $i--) {
				if ($level == 14 && $i < 8) break;
				echo $i."\r\n";

				$op = mysql_fetch_assoc($q);
				if ($op === FALSE) break;

	                        echo $op['id']."\r\n";

				PutExpScroll(4160+$i,$op['id']);
	
				mysql_query('
					INSERT INTO oldbk.users_abils (owner,magic_id, allcount, findata)
					VALUES(
						"'.$op['id'].'",
						"2526",
						"'.$i.'",
						"0"
					) ON DUPLICATE KEY UPDATE
						`allcount` = `allcount` + '.$i
				) or mydie(mysql_error().":".__LINE__);
			}
		}


		// тёмные
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =0 and users.align=3 ORDER BY score DESC LIMIT 10  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =1 and users.align=3 ORDER BY score DESC LIMIT 10 )
					ORDER BY score DESC LIMIT 10") 	or mydie(mysql_error().":".__LINE__);;
		if (mysql_num_rows($q) > 0) {
			for ($i = 10; $i > 0; $i--) {
				if ($level == 14 && $i < 8) break;
				echo $i."\r\n";

				$op = mysql_fetch_assoc($q);
				if ($op === FALSE) break;

	                        echo $op['id']."\r\n";

				PutExpScroll(4160+$i,$op['id']);
	
				mysql_query('
					INSERT INTO oldbk.users_abils (owner,magic_id, allcount, findata)
					VALUES(
						"'.$op['id'].'",
						"2526",
						"'.$i.'",
						"0"
					) ON DUPLICATE KEY UPDATE
						`allcount` = `allcount` + '.$i
				) or mydie(mysql_error().":".__LINE__);
			}
		}
	}

	// передаём урлы на тех кто выиграл противостояние
	GetUrlOp("capitalcity.oldbk.com","/op_result.php?key=".rawurlencode("25tyvu574yvmu345gkisrgnkjir")."&data=".$best,"capitalcity.oldbk.com") or mydie(mysql_error().":".__LINE__);
	//GetUrlOp("46.51.184.60","/op_result.php?key=".rawurlencode("qm8954byuh8y84qfjqarge")."&data=".$best,"avaloncity.oldbk.com") or mydie(mysql_error().":".__LINE__);

	// Обнуляем всю инфу в храме
	mysql_query('DELETE FROM avalon.op_score') or mydie(mysql_error().":".__LINE__);
} elseif (date("j") != 1 && date("H") == 6) {
	// запускаемся каждый день в 6.00 кроме первого числа каждого месяца
	
	// раздаём топ 10 медалей в инфу
	$q = mysql_query('SELECT * FROM oldbk.variables WHERE var = "opposition_skulls"') or mydie(mysql_error().":".__LINE__);
	$d = mysql_fetch_assoc($q);
	$d = $d['value'];

	if (strlen($d))	{
		// снимаем старые черепа
		mysql_query('UPDATE oldbk.users SET medals = REPLACE(medals,"098;","") WHERE id IN ('.$d.') AND id_city = 0') or mydie(mysql_error().":".__LINE__);
		mysql_query('UPDATE avalon.users SET medals = REPLACE(medals,"098;","") WHERE id IN ('.$d.') AND id_city = 1') or mydie(mysql_error().":".__LINE__);
	}

	// вешаем новые
	$uids = array();

	for ($level = 7; $level <= 14; $level++) {
		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =0 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 10  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =1 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 10 )
					ORDER BY score DESC LIMIT 10")  or mydie(mysql_error().":".__LINE__);;
		while($u = mysql_fetch_assoc($q)) {
			// вешаем светлым
			$uids[] = $u['id'];
		}

		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =0 and users.align=2 ORDER BY score DESC LIMIT 10  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =1 and users.align=2 ORDER BY score DESC LIMIT 10 )
					ORDER BY score DESC LIMIT 10")	 or mydie(mysql_error().":".__LINE__);;
		while($u = mysql_fetch_assoc($q)) {
			// вешаем нейтралам
			$uids[] = $u['id'];
		}

		$q = mysql_query("(SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =0 and users.align=3 ORDER BY score DESC LIMIT 10  )
					 UNION 
					( SELECT users.id, login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner WHERE level =  ".$level." and id_city =1 and users.align=3 ORDER BY score DESC LIMIT 10 )
					ORDER BY score DESC LIMIT 10") 	or mydie(mysql_error().":".__LINE__);;
		while($u = mysql_fetch_assoc($q)) {
			// вешаем тёмным
			$uids[] = $u['id'];
		}
	}

	if (count($uids)) {
		mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"098;") WHERE id IN ('.implode(",",$uids).') AND id_city = 0') or mydie(mysql_error().":".__LINE__);
		mysql_query('UPDATE avalon.users SET medals = CONCAT(medals,"098;") WHERE id IN ('.implode(",",$uids).') AND id_city = 1') or mydie(mysql_error().":".__LINE__);
		mysql_query('UPDATE oldbk.variables SET value = "'.implode(",",$uids).'" WHERE var = "opposition_skulls"') or mydie(mysql_error().":".__LINE__);
	} else {
		mysql_query('UPDATE oldbk.variables SET value = "" WHERE var = "opposition_skulls"') or mydie(mysql_error().":".__LINE__);
	}
}

if (date("H") == 5) {
	// запускаемся каждый день, в 5 утра и чистим эффекты
	mysql_query('DELETE FROM effects WHERE type = 5577');
}

if (date("H") == 0) {
	// запускаемся каждый день, выставляем дни
	$q = mysql_query('SELECT * FROM oldbk.variables WHERE var = "opposition_today"') or mydie(mysql_error().":".__LINE__);
	$op = mysql_fetch_assoc($q);
	if ($op !== FALSE) {
		if ($op['value'] == date("d/m/Y")) {
			// двигаем противостояние на 4 дня вперёд
			$newt = time()+(3600*24*4);
			mysql_query('UPDATE oldbk.variables SET value = "'.date("d/m/Y",$newt).'" WHERE var = "opposition_today"') or mydie(mysql_error().":".__LINE__);
		}
	}
}
	
mysql_query('COMMIT') or mydie(mysql_error().":".__LINE__);	
echo "Finishing script. Destroy lock.\n";
lockDestroy("cron_op");
?>