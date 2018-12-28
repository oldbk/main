<?php
include "/www/capitalcity.oldbk.com/cron/init.php";
include "/www/capitalcity.oldbk.com/bank_functions.php";

function EchoLog($txt) {
	echo date("[d/m/Y H:i:s]: ").$txt."\r\n";
}


function mk_my_item($telo,$proto,$addinfo,&$img) {
	$telo = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE id = '.$telo));
	$count = 1;

	if (($pos = strpos($proto,'_')) !== false) {
		$proto = substr($proto,0,$pos);
		EchoLog("New proto: ".$proto);
	} elseif ($proto === 'zodiak') {
		$t = get_mag_stih($telo);
		// $t[0] - от 1 до 4, 1 - огонь, 2 - земля, 3 - воздух, 4 - вода
		if (!isset($t[0]) || empty($t[0])) $t[0] = mt_rand(1,4);
		if ($t[0] == 1) $proto = 150157;
		if ($t[0] == 2) $proto = 920927;
		if ($t[0] == 3) $proto = 130137;
		if ($t[0] == 4) $proto = 930937;
	} elseif (($pos = strpos($proto,'i')) === 0) {
		$name = substr($proto,1);
		EchoLog("i copy: ".$name);

		// копируем
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = 477 and battle = 0 and name = "'.$name.'"');
		$flds = array();
		for ($i = 0;;$i++) {
			$n = mysql_field_name($q,$i);
			if (!$n || $n == "") break;
			$flds[] = $n;
		}
		unset($flds[0]);

		$qs = 'INSERT INTO oldbk.inventory (';
		$fl = "";
		$pos = 0;
		while(list($k,$v) = each($flds)) {
			$fl .= "`".$v."`,";
			if ($v == "art_param") $pos = $k;
		}
		$fl = substr($fl,0,strlen($fl)-1);
		$qs .= $fl;
		$qs .= ') VALUES (';

		$qq = "";
		$dress = mysql_fetch_assoc($q);
		$dress['owner'] = $telo['id'];
		$dress['present'] = "Удача";
		$dress['update'] = date('Y-m-d H:i:s');

		$i2 = 0;
		$qq = $qs;
		while(list($k,$v) = each($dress)) {
			$i2++;
			if ($i2 < 2) continue;
			if ($pos > 0 && $pos == $i2-1) {
				if (strlen($v)) {
					$qq .= '"'.mysql_real_escape_string($v).'",';
				} else {
					$qq .= 'NULL,';
				}
			} else {
				$qq .= '"'.mysql_real_escape_string($v).'",';
			}
		}
		$qq = substr($qq,0,strlen($qq)-1);
		$qq .= ')';
		mysql_query($qq);

		$rec['owner']=$telo['id'];
		$rec['owner_login']=$telo['login'];
		$rec['target']=0;
		$rec['target_login']='Снежное Волшебство';
		$rec['owner_balans_do']=$telo['money'];
		$rec['owner_balans_posle']=$telo['money'];
		$rec['type']=393;//   получил по акции
		$rec['sum_kr']=0;
		$rec['sum_ekr']=$dress['ecost'];
		$rec['sum_kom']=0;
		$dress['id'] = mysql_insert_id();
		$rec['item_id']=get_item_fid($dress);
		$rec['item_name']=$dress['name'];
		$rec['item_count']=1;
		$rec['item_type']=$dress['type'];
		$rec['item_cost']=$dress['cost'];
		$rec['item_dur']=$dress['duration'];
		$rec['item_maxdur']=$dress['maxdur'];
		$rec['item_ups']=0;
		$rec['item_unic']=1;
		$rec['item_incmagic']=$dress['includemagic'];
		$rec['item_incmagic_count']=$dress['includemagicdex'];
		$rec['item_arsenal']='';
		add_to_new_delo($rec);

		$img = $dress['img'];

		return $name;
	} elseif (strpos($proto,'repmoney') === 0) {
		$repmoney = substr($proto,8);
		EchoLog('repmoney: '.$repmoney);
		mysql_query('UPDATE users SET repmoney = repmoney + '.$repmoney.' WHERE id = '.$telo['id']);

		$rec['owner']=$telo[id];
		$rec['owner_login']=$telo[login];
		$rec['owner_balans_do']=$telo['money'];
		$rec['owner_balans_posle']=$telo['money'];
		$rec['owner_rep_do']=$telo['repmoney'];
		$rec['owner_rep_posle']=$telo['repmoney']+$repmoney;
		$rec['target']=0;
		$rec['target_login']='Снежное Волшебство';
		$rec['type']=371;
		$rec['sum_rep']=$repmoney;
		add_to_new_delo($rec);

		$img = "batt_repa.gif";

		return $repmoney." репутации";
	} elseif (strpos($proto,'winstbat') === 0) {
		$winstbat = substr($proto,8);
		EchoLog('Winstbat: '.$winstbat);
		mysql_query('UPDATE users SET winstbat = winstbat + '.$winstbat.' WHERE id = '.$telo['id']);

		$rec['owner']=$telo['id'];
		$rec['owner_login']=$telo['login'];
		$rec['owner_balans_do']=$telo['money'];
		$rec['owner_balans_posle']=$telo['money'];
		$rec['target']=0;
		$rec['target_login']='Снежное Волшебство';
		$rec['type']=392;
		$rec['add_info']=$winstbat;
		add_to_new_delo($rec);

		$img = "fighttype3.gif";

		return $winstbat." Великих Побед";
	} else {
		EchoLog('Standart proto: '.$proto);
	}

	if ($addinfo['shop'] == 0) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
	} elseif ($addinfo['shop'] == 1) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));
	} elseif ($addinfo['shop'] == 2) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.cshop where id='{$proto}' ;"));
	}

	$dress['present'] = "Удача";
	if (isset($addinfo['maxdur'])) $dress['maxdur'] = $addinfo['maxdur'];
	if (isset($addinfo['count'])) $count = $addinfo['count'];

	if ($dress['magic'] > 0) {
		// 1) подарком все свитки
		// 2) срок годности 7 дней
		$dress['present'] = 'Удача';
		$dress['goden'] = 7;
	}

	$dress['is_owner'] = 0;

	if ($proto == 9597 || $proto == 9598 || $proto == 190199 || $proto == 190191 || $proto == 190192) {
		$dress['goden'] = 0;
		$dress['is_owner'] = $telo['id'];
	}


	if ($dress['id'] > 0) {
		for ($i = 0; $i < $count; $i++) {
                        $dress['id'] = $proto;
			if(mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
				`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`,`sowner`
				)
				VALUES
					('{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
					'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
					'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}', '{$dress['is_owner']}'
					) ;"))
				{

				$good = 1;
				$insert_item_id=mysql_insert_id();
				$dress['idcity']=$telo['id_city'];
				$dress['id']=$insert_item_id;
	        	} else {
				$good = 0;
			}


			if ($good) {
				$rec['owner']=$telo[id];
				$rec['owner_login']=$telo[login];
				$rec['target']=0;
				$rec['target_login']='Снежное Волшебство';
				$rec['owner_balans_do']=$telo[money];
				$rec['owner_balans_posle']=$telo[money];
				$rec['type']=393;//   получил по акции
				$rec['sum_kr']=0;
				$rec['sum_ekr']=$dress['ecost'];
				$rec['sum_kom']=0;
				$rec['item_id']=get_item_fid($dress);
				$rec['item_name']=$dress['name'];
				$rec['item_count']=1;
				$rec['item_type']=$dress['type'];
				$rec['item_cost']=$dress['cost'];
				$rec['item_dur']=$dress['duration'];
				$rec['item_maxdur']=$dress['maxdur'];
				$rec['item_ups']=0;
				$rec['item_unic']=1;
				$rec['item_incmagic']=$dress['includemagic'];
				$rec['item_incmagic_count']=$dress['includemagicdex'];
				$rec['item_arsenal']='';
				add_to_new_delo($rec);
			} else {
				EchoLog("false");
			}
		}
		$addtxt = "";
		if ($count > 1) $addtxt = ' (x'.$count.')';
		$img = $dress['img'];
		return $dress['name'].$addtxt;
	} else {
		EchoLog("false:".$proto);
		return false;
	}
}

if( !lockCreate("cron_mshow") ) {
    exit("Script already running.");
}

$maxpp = 2;

// раздаём всем по 0.1 екр
$q = mysql_query('SELECT users.*,inventory.owner,count(*) as ccount FROM inventory LEFT JOIN users ON users.id = inventory.owner WHERE prototype = 3006000 GROUP BY owner');

if (mysql_num_rows($q) == 0) {
	// снежинок нет, стопимся
	EchoLog("No items");
	lockDestroy("cron_mshow");
	die();
}



// если меньше 20 уникальных тел со снежинками, то можно выиграть 20 в рыло
if (mysql_num_rows($q) <= 20) $maxpp = 20;

while($c = mysql_fetch_assoc($q)) {
	$bankid = mysql_fetch_array(mysql_query("select * from oldbk.bank where owner=".$c['owner']." order by def desc,id limit 1"));

	EchoLog("bank: ".$bankid['id'].":".$c['owner'].":".$c['login'].":".($c['ccount']*0.1));

	make_ekr_add_bonus($c,$bankid,null,($c['ccount']*0.1),1);

	$txt = '<font color=red>Внимание!</font> Вы получили '.($c['ccount']*0.1).' екр на счёт №'.$bankid['id'].' за акцию &quot;Снежное Волшебство&quot;.';
	if($c['odate'] > (time()-60)) {
		addchp($txt,'{[]}'.$c['login'].'{[]}',-1,-1);
	} else {
		mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$c['owner']."','','".$txt."')");
	}

}

EchoLog("Maxpp: ".$maxpp);

// шмотки, 0 - shop, 1 - eshop
$ilist = array(
	200021 => array('shop' => 0), // Средний свиток «Шквал молний»
	200022 => array('shop' => 0), // Большой свиток «Шквал молний»
	571 => array('shop' => 0), // рунный опыт
	572 => array('shop' => 0), // рунный опыт
	573 => array('shop' => 0), // рунный опыт
	574 => array('shop' => 0), // рунный опыт
	575 => array('shop' => 0), // рунный опыт
	576 => array('shop' => 0), // рунный опыт
	577 => array('shop' => 0), // рунный опыт
	578 => array('shop' => 0), // рунный опыт
	579 => array('shop' => 0), // рунный опыт
	580 => array('shop' => 0), // рунный опыт
	"repmoney300" => array(), // репа
	"repmoney600" => array(),
	"repmoney1000" => array(),
	"repmoney1500" => array(),
	"repmoney2000" => array(),
	3203 => array('shop' => 1), // чек 20
	3204 => array('shop' => 1), // чек 50
	3205 => array('shop' => 1), // чек 100
	15561 => array('shop' => 0), // осколки статуй
	15562 => array('shop' => 0),
	15563 => array('shop' => 0),
	15564 => array('shop' => 0),
	15565 => array('shop' => 0),
	15566 => array('shop' => 0),
	15567 => array('shop' => 0),
	15568 => array('shop' => 0),
	103 => array('shop' => 0), // молча 30 мин
	156 => array('shop' => 1), // напа
	157 => array('shop' => 1), // кров напа
        "zodiak" => array('shop' => 0), // зодиакальные
	55556 => array('shop' => 0), // защита от травм 0/10 на бой
	55560 => array('shop' => 0), // Малый свиток «Защита от магии»
	55557 => array('shop' => 1), // Средний свиток «Защита от магии»
	55558 => array('shop' => 1),  // Большой свиток «Защита от магии»
	3001002 => array('shop' => 1), // Чаша Крови 150%
	3001003 => array('shop' => 1), // Чаша Триумфа 170%
	3001004 => array('shop' => 1), // Чаша Смерти 100%
	9597 => array('shop' => 0), // встройка
	9598 => array('shop' => 1), // встройка
	4005 => array('shop' => 0), // ключ от лабы
	4017 => array('shop' => 0), // ключ от лабы
        2206 => array('shop' => 1), // выход из боя
        353 => array('shop' => 1), // заступ
	14005 => array('shop' => 0), // призыв III
        14004 => array('shop' => 1),  //8 призыв
        121121121 => array('shop' => 1), // путы
        301 => array('shop' => 1), // невед
        40000000 => array('shop' => 0), // ловушка
        3333 => array('shop' => 1), // ключ 666
        4002 => array('shop' => 1), // антидот 0/10
        15003 => array('shop' => 0), // захват iii
        15004 => array('shop' => 1), // захват iii
        119119 => array('shop' => 0), // клон 0/2
        119119119 => array('shop' => 1), // клон 0/3
        2525 => array('shop' => 1), // вендетта
	5100 => array('shop' => 0), // фамильный герб двойной
        315 => array('shop' => 0), // средний свиток «Восстановление маны»
        318 => array('shop' => 1), // мана
	271271 => array('shop' => 0), // Малый свиток «Восстановление 360HP»
    	200273 => array('shop' => 1), // Большой свиток «Восстановление 360HP»
        145145 => array('shop' => 1), // разбойное 0/5
	125125 => array('shop' => 0, 'maxdur' => 5), // лечение травм
	19102  => array('shop' => 1), // опыт +20%
);

function ashuffle (&$arr) {
     uasort($arr, function ($a, $b) {
         return rand(-1, 1);
     });
}

ashuffle($ilist);

$ilist2 = array(
//	3005000 => array('shop' => 0, 'need' => 2500), // вексель
	2018160 => array('shop' => 0, 'need' => 300), // Малый сундук «Рунное могущество»
	2018161 => array('shop' => 0, 'need' => 500), // Средний сундук «Рунное могущество»
	2018162 => array('shop' => 0, 'need' => 1000), // Большой сундук «Рунное могущество»
	2018163 => array('shop' => 0, 'need' => 1300), // Совершенный сундук «Рунное могущество»
	190199 => array('shop' => 2, 'need' => 300), // точка +7
	190191 => array('shop' => 1, 'need' => 1300), // точка +8
	190192 => array('shop' => 1, 'need' => 1500), // точка +9
	542 => array('shop' => 0, 'need' => 500), // Сертификат на личный образ
	535 => array('shop' => 0, 'need' => 400), // Сертификат на уникальный подарок
	543 => array('shop' => 0, 'need' => 125), // Сертификат на личную картинку
	533 => array('shop' => 0, 'need' => 500), // Сертификат на личный смайл
	538 => array('shop' => 0, 'need' => 50), // Сертификат на екровую ёлку
	539 => array('shop' => 0, 'need' => 250), // Сертификат на артовую ёлку
	"iЛегендарная футболка Учителей (мф)" => array('prototype' => 100031, 'need' => 1300),
	"iПлащ легендарного героя (мф)" => array('prototype' => 7006, 'need' => 1300),
	1200001 => array('shop' => 0, 'need' => 50), // Великое улучшение личного артефакта I
	1200002 => array('shop' => 0, 'need' => 100), // Великое улучшение личного артефакта II
	1200003 => array('shop' => 0, 'need' => 150), // Великое улучшение личного артефакта III
	1200004 => array('shop' => 0, 'need' => 200), // Великое улучшение личного артефакта IV
	1200005 => array('shop' => 0, 'need' => 300), // Великое улучшение личного артефакта V
	1200006 => array('shop' => 0, 'need' => 500), // Великое улучшение личного артефакта VI
	56666 => array('shop' => 0, 'need' => 300), // Великое Чарование III
	540 => array('shop' => 0, 'need' => 200), //Сертификат на бесплатный обмен уникальной вещи в КО
	541 => array('shop' => 0, 'need' => 200),   //Сертификат на бесплатный обмен артефакта в КО
);

$listtodo = array();

ashuffle($ilist2);

// разбираемся что у нас с блотным списком
$varneed = mysql_fetch_assoc(mysql_query('SELECT * FROM variables_int WHERE var = "snowsell"'));
EchoLog(serialize($varneed));

$q = mysql_query('SELECT * FROM variables WHERE var = "snowsell2"');
$var = mysql_fetch_assoc($q);
if (empty($var['value'])) {
	EchoLog("New item2 circle");
	// начало цикла
	list($k,$v) = each($ilist2);
	unset($ilist2[$k]);
	$arr = array();
	$arr['current'] = array('k' => $k, 'v' => $v);
	$arr['ilist2'] = $ilist2;
	EchoLog("Set new item2: ".$k.":".serialize($v));
	mysql_query('UPDATE variables SET value = "'.mysql_real_escape_string(serialize($arr)).'" WHERE var = "snowsell2"');
	$var['value'] = $arr;
} else {
	$var['value'] = unserialize($var['value']);
}

EchoLog("Status: ".serialize($var['value']));


if ($varneed['value'] >= $var['value']['current']['v']['need']) {
	EchoLog("Adding item2 to loto: ".serialize($var['value']['current']));
	$listtodo[$var['value']['current']['k']] = $var['value']['current']['v'];

	// обнуляем счётчик
	mysql_query('UPDATE variables_int SET value = 0 WHERE var = "snowsell"');

	if (count($var['value']['ilist2'])) {
		EchoLog('Setting next item2');
		list($k,$v) = each($var['value']['ilist2']);
		unset($var['value']['ilist2'][$k]);

		$arr['current'] = array('k' => $k, 'v' => $v);
		$arr['ilist2'] = $var['value']['ilist2'];
		EchoLog("Set new item2: ".$k.":".serialize($v));
		mysql_query('UPDATE variables SET value = "'.mysql_real_escape_string(serialize($arr)).'" WHERE var = "snowsell2"');
	} else {
		EchoLog("Reseting ilist2 circle");
		mysql_query('UPDATE variables SET value = "" WHERE var = "snowsell2"');
	}
} else {
	EchoLog("Checked: ".$varneed['value'].":".$var['value']['current']['v']['need']);
}

if (count($listtodo) && $maxpp == 20) $maxpp++;


// раздаём
$i = 0;
while(list($k,$v) = each($ilist)) {
	if ($i >= 20) break;
	$listtodo[$k] = $v;
	$i++;
}

//$listtodo = array_reverse($listtodo,true);

$clist = array();
reset($listtodo);
$tosend = array();

$q = mysql_query('SELECT owner,login,odate,level FROM inventory LEFT JOIN users ON users.id = inventory.owner WHERE prototype = 3006000 ORDER BY RAND()');
//$q = mysql_query('SELECT owner,login,odate,level FROM inventory LEFT JOIN users ON users.id = inventory.owner WHERE level > 10 GROUP BY owner ORDER BY rand() LIMIT 100');

$firstitem = false;

while($i = mysql_fetch_assoc($q)) {
	if (!count($listtodo)) {
		// всё раздали
		break;
	}

	EchoLog(serialize($i));
	if ($clist[$i['owner']] >= $maxpp) continue;


	reset($listtodo);
	list($k,$v) = each($listtodo);

	if ($firstitem == false) {
		// первому что выпадает
		$firstitem = true;
		/*
		$qtmp = mysql_query('SELECT owner,login,odate,level FROM inventory LEFT JOIN users ON users.id = inventory.owner WHERE users.id = 676084 LIMIT 1');
		$i = mysql_fetch_assoc($qtmp);
		*/
	}

	if (($k >= 571 && $k <= 580) || strpos($k,'repmoney') !== false) {
		// снижаем вероятность получения
		if ($i['level'] <= 6) {
			$min = 100;
		} else {
			$min = 100 - (($i['level'] - 6) * 10);
		}
		EchoLog("Min: ".$min);
		if (!(mt_rand(0,100) <= $min)) continue;
	}

	if ($k == 3005000 && $i['level'] > 10) continue;
	if (($k == 1000001 || $k == 1000002 || $k == 1000003) && $i['level'] < 10) continue;

	$clist[$i['owner']]++;

	if (isset($ilist2[$k]) && $maxpp != 20) {
		// если получил из жирного списка то мелких не получит
		$clist[$i['owner']] = $maxpp;
	}

	unset($listtodo[$k]);

	EchoLog("Item ".$k." to ".$i['owner'].":".$i['login'].":".$i['level']);

	$img = "";
	$txtitem = mk_my_item($i['owner'],$k,$v,$img);
	if (strlen($txtitem)) {
		$txt = '<font color=red>Внимание!</font> Вы получили &quot;'.$txtitem.'&quot; за акцию &quot;Снежное Волшебство&quot;';
		if($i['odate'] > (time()-60)) {
			addchp($txt,'{[]}'.$i['login'].'{[]}',-1,-1);
		} else {
			mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$i['owner']."','','".$txt."')");
		}
	}
	$tosend[] = array('uid' => $i['owner'], 'item' => $txtitem, 'img' => $img);
}

EchoLog(serialize($tosend));
for ($i = 0; $i < 5; $i++) {
	if (SendToBlog($tosend)) break;
	EchoLog("Send to blog false");
}

addch2all('Внимание! Только-что прошел очередной ежедневный розыгрыш призов в рамках беспроигрышной лотереии по акции - <b>"Снежное Волшебство!"</b>. Все волшебные Снежинки, имеющиеся в Игре участвовали в розыгрыше, и каждая из них принесла своему обладателю<b> 0.1 екр</b>. <b>Полный список разыгранных ценных призов</b> и ники счастливчиков можно посмотреть в новом посте от <b>Удачи</b> на <a href="http://capitalcity.oldbk.com/blog_auth.php" target=_blank><img src="http://i.oldbk.com/i/newd/chn/up_butt2_chat.jpg"> <b>Блогах ОлдБК</b></a>');

function SendToBlog($arr) {
	EchoLog("Sending to blog");
	$content = http_build_query(array('Loto' => $arr, 'oldbk_key' => '7XttXsFvpOmUQebCbgMGOpUXG0QI'));

	$fp = fsockopen('tls://blog.oldbk.com', 443);
	if ($fp) {
		fwrite($fp, "POST /api/loto.html HTTP/1.0\r\n");
		fwrite($fp, "Host: blog.oldbk.com\r\n");
		fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
		fwrite($fp, "Content-Length: ".strlen($content)."\r\n");
		fwrite($fp, "Connection: close\r\n");
		fwrite($fp, "\r\n");

		fwrite($fp, $content);

		$str = "";
		while(!feof($fp)) $str .= fgets($fp, 128);
		fclose($fp);

		if (strpos($str,'truesozdalsya') !== false) {
			EchoLog("Sending to blog ok");
			return true;
		}

	}
	return false;
}


//mysql_query('UPDATE oldbk.inventory SET ecost = 0 WHERE prototype = 3006000');

lockDestroy("cron_mshow");

?>
