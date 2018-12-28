<?php
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
	$miniBB_gzipper_encoding = 'gzip';
}
if (isset($miniBB_gzipper_encoding)) {
	ob_start();
}

function percent($a, $b) {
	$c = $b/$a*100;
	return $c;
}

session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";


$craft_week = 0;
$get_ivent = mysql_fetch_array(mysql_query("select * from oldbk.ivents where id = 13"));
if ($get_ivent['stat'] == 1) {
	$craft_week = 1;
}

if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
if ($_SESSION['boxisopen'] != 'open') { header('location: main.php?edit=1'); die(); }

require_once('craft_config.php');
require_once('craft_functions.php');

if (!isset($craftrooms[$user['room']])) {
	header("Location: main.php");
	die();

}

$loc = $craftrooms[$user['room']];
$locnum = $user['room'];
if ($locnum < 90) {
	header("Location: main.php");
	die();
}


// раздел
$rzname = 'craftrazdel'.$user['room'];
if (!isset($_SESSION[$rzname])) {
	if (count($loc['razdel'])) {
		list($k,$v) = each($loc['razdel']);
		$_SESSION[$rzname] = $k;
	} else {
		$_SESSION[$rzname] = 0;
	}
}
if ($craftstatus == 0) {
	if (isset($_GET['razdel'])) $_GET['razdel'] = intval($_GET['razdel']);
	if (isset($_GET['razdel']) && $_GET['razdel'] >= 0 && $_GET['razdel'] <= 7) {
		if (isset($loc['razdel'][$_GET['razdel']])) {
			$_SESSION[$rzname] = $_GET['razdel'];
		}
	}
}
$viewlevel = false;


$qcache = $craftconfigcache; // кеш на тяжёлый запрос по рецептам
$craftstatus = 0; // 0 - нет крафта, 1 - идёт крафт, 2 - пауза
$cs = array(); // массив с заданием
// узнаем текущий статус локи
$q = mysql_query('SELECT * FROM craft_job WHERE owner = '.$user['id'].' and loc = '.$user['room']);
if (mysql_num_rows($q) > 0) {
	$cs = mysql_fetch_assoc($q);
	$craftstatus = $cs['status'];
}

if (count($cs) && $craftstatus == 1 && $cs['craftlefttime'] > 0) {
	// обновляем таймер процесса крафта

	// если больше 180 сек назад был последний апдейт, значит тело в офе было
	$q = mysql_query('START TRANSACTION') or Redirect();
	$q = mysql_query('SELECT * FROM craft_job WHERE id = '.$cs['id'].' FOR UPDATE') or Redirect();
	$cs = mysql_fetch_assoc($q) or Redirect();

	if ($cs['status'] == 1 && $cs['craftlefttime'] > 0) {
		if (time() - $cs['lastupdate'] > 180) $cs['lastupdate'] = time()-1;

		$difftime = time() - $cs['lastupdate']; // получили разницу по времени
		if ($difftime > 0) {
			if ($cs['craftlefttime'] < $difftime) $difftime = $cs['craftlefttime'];
	
			mysql_query('UPDATE craft_job SET craftlefttime = craftlefttime - '.$difftime.', lastupdate = '.time().' WHERE id = '.$cs['id']) or Redirect();
			$cs['craftlefttime'] -= $difftime;
	
			$q = mysql_query('COMMIT') or Redirect();
		
			// если получаем craftlefttime = 0 то редиректим
			if (!isset($_REQUEST['checkcraft'])) {
				CraftCheckComplete($user,$cs,$loc,$rzname);
			}
		} else {
			$q = mysql_query('COMMIT') or Redirect();
		}
	} else {
		$q = mysql_query('COMMIT') or Redirect();
	}
}

if (isset($_REQUEST['checkcraft']) && count($cs)) {
	if ($craftstatus == 1) {
		// выдаём json для прогресса
		$ret = array();
		if ($cs['craftlefttime'] == 0) {
			$ret['redirect'] = 1;
			echo json_encode($ret);
			die();
		} else {
			$ret['redirect'] = 0;
		}


		$ret['none'] = 0;

		$all = $cs['itemcount']*$cs['crafttime']*60;
		$left = (((($cs['itemleft'])*$cs['crafttime'])*60)+$cs['craftlefttime']);
	
		$pr = 100-floor($left*100/$all);
		$ret['craftlefttime'] = $cs['craftlefttime'];
		$ret['procentline'] = $pr;
		$ret['itemall'] = iconv("windows-1251","utf-8",prettyTime(null,time()+$left));
		$craftedcount = (($cs['itemleft']-$cs['itemcount'])*-1)-1;
		$ret['itemtime'] = iconv("windows-1251","utf-8",prettyTime(null,time()+(($craftedcount*$cs['crafttime']*60)+$cs['crafttime']*60-$cs['craftlefttime'])));
		$ret['itemtimenext'] = iconv("windows-1251","utf-8",prettyTime(null,time()+($cs['craftlefttime'])));

		echo json_encode($ret);
	} else {
		$ret = array();
		$ret['none'] = 1;
		echo json_encode($ret);
	}
	die();
}

// проверка на окончание производства
if (count($cs) && $craftstatus == 1) {
	CraftCheckComplete($user,$cs,$loc,$rzname);
}


if (isset($_GET['exit'])) {
	mysql_query('UPDATE `users` SET `users`.`room` = "191" WHERE `users`.`id` = '.$_SESSION['uid']) or die();
	Redirect('city.php');
}


// банк
if (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) {
	$bank = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = ".$_SESSION['bankid']));
} else {
	$bank = false;
}


if (isset($_GET['bankauth'])) {
	if (isset($_GET['bankpass'],$_GET['bankid'])) {
		$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.intval($_GET['bankid']).' and pass = "'.md5($_GET['bankpass']).'"');	
		if (mysql_num_rows($q) > 0) {
			$_SESSION['bankid'] = intval($_GET['bankid']);					
			echo '<script>closeinfo();bankauth = true;location.href = "?speedup=1";</script>';
			die();
		} else {
			$error = 1;
		}
	}                                  

	$auth =  '<table border=0 width=400 height=100><tr><td  valign=top align="center" height=5 colspan="4"><font style="COLOR:#8f0000;FONT-SIZE:12pt">'; 
	$auth .= "Авторизация в банке";
	$auth .= '</font><a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
	<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-20px;right:-220px;" border=0 title="Закрыть"></a></td></tr>
	<tr><td colspan="4" class="center" valign=top>';   
	$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id']);
	if (mysql_num_rows($q) > 0) {
		$auth .= '<select id="bankid" style="width:100px" name="bankid">';
		while ($rah = mysql_fetch_array($q)) {
			$auth .= "<option>".$rah['id']."</option>";
		}
		$auth .= "</select> ";
		$auth .= 'Пароль: <input type=password name="bankpass" id="bankpass" style="width:100px"> <button style="height:20px;" class="button-mid btn" OnClick="doauth();">Войти</button>';
	} else {
		$auth .= '<font color="red">Банковские счета не найдены</font>';
	}

	if ($error > 0) {
		$auth .= '<br><font color="red">Не правильный пароль</font>';				
	}

	$auth .= '</td>
		</tr><tr><td align="center"  colspan="3">
		</td></tr>
		</table>';
	echo $auth;
	die();
}

// страницы
$viewperpage = 10; // количество вещей на страницу
if (!isset($_SESSION['craftpage'.$_SESSION[$rzname]])) $_SESSION['craftpage'.$_SESSION[$rzname]] = 0;
if (isset($_GET['page'])) {
	if ($_GET['page'] !== "all") {
		$_GET['page'] = intval($_GET['page']);
	}
	$_SESSION['craftpage'.$_SESSION[$rzname]] = $_GET['page'];
}


// отображение по левелам
if (isset($_POST['fall'])) {
	$_SESSION['fall']=(int)$_POST['fall'];
} else {
	$_POST['fall']=(int)$_SESSION['fall'];	
}

$res = array();
$ins = false;
$prof = false;

if ($craftstatus == 0) {
	// ресурсов в наличии
	$q = mysql_query('SELECT prototype, COUNT(*) as cc FROM inventory USE INDEX (owner_8) WHERE owner = '.$user['id'].' and otdel = 62 and dressed = 0 and setsale = 0 GROUP BY prototype');
	while($row = mysql_fetch_assoc($q)) {
		$res[$row['prototype']] = $row['cc'];
	}

	// данные по профам
	$prof = GetUserProfData($user);

}
if ($craftstatus == 0 || $craftstatus == 2) {
	// данные по инструменту в руке
	$q = mysql_query('SELECT * FROM inventory WHERE owner = '.$user['id'].' and dressed = 1 and type = 3');
	$ins = mysql_fetch_assoc($q);
}


// обработчики

// ускорение производства
if (isset($_REQUEST['speedup']) && count($cs) && $craftstatus == 1) {
	$spprice = round(($cs['craftlefttime'] / 3600 * $craftspeedupprice)+($cs['itemleft']*$cs['crafttime'] / 60 * $craftspeedupprice),2);
	if ($spprice < $craftspeedupmin) $spprice = $craftspeedupmin;

	if ($bank['ekr'] >= $spprice) {
		$q = mysql_query('START TRANSACTION') or Redirect();

		$rec['owner'] = $user['id'];
		$rec['owner_login'] = $user['login'];
		$rec['owner_balans_do'] = $user['money'];
		$rec['owner_balans_posle'] = $user['money'];
		$rec['target']=0;
		$rec['target_login']="Крафт-".$loc['name'];
		$rec['type'] = 1302;
		$rec['sum_kr'] = 0;
		$rec['sum_ekr'] = $spprice;
		$rec['sum_kom'] = 0;
		$rec['item_name'] = $cs['itemname'];
		$rec['item_count'] = 1;
		$rec['add_info']='Баланс до '.$bank['ekr']. ' екр. после ' .($bank['ekr']-$spprice).' екр.';
		add_to_new_delo($rec) or Redirect();
	
	
		mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr`- '".$spprice."' WHERE id = {$bank['id']}") or Redirect();
		mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Вы ускорили производство за ".$spprice." екр.</b>, <i>(Итого: {$bank['cr']} кр., ".($bank['ekr']-$spprice)." екр.)</i>','{$bank['id']}');") or Redirect();
	
		mysqL_query('INSERT INTO craft_stats (owner,type,val1,val2,val3,count,countnumeric,date)  VALUES ('.$user['id'].',3,'.$user['room'].','.$_SESSION[$rzname].','.$cs['rcid'].',1,"'.$spprice.'",NOW())
				ON DUPLICATE KEY UPDATE
				`count` = `count` + 1,
				`countnumeric` = `countnumeric` + '.$spprice.'
		') or Redirect();
	
		$q = mysql_query('COMMIT') or Redirect();
	
		CraftCheckComplete($user,$cs,$loc,$rzname,false,true);
	} else {
		SetMsg("Недостаточно екр для ускорения производства.","e");
	}
	Redirect();
}

// отмена
if (isset($_REQUEST['cancel']) && count($cs) && ($craftstatus == 1 || $craftstatus == 2)) {
	$q = mysql_query('START TRANSACTION') or Redirect();
	$rec['owner'] = $user['id'];
	$rec['owner_login'] = $user['login'];
	$rec['owner_balans_do'] = $user['money'];
	$rec['owner_balans_posle'] = $user['money'];
	$rec['target']=0;
	$rec['target_login']="Крафт-".$loc['name'];
	$rec['type']=1301;
	add_to_new_delo($rec) or Redirect();

	mysql_query('DELETE FROM craft_job WHERE id = '.$cs['id']) or Redirect();

	mysqL_query('INSERT INTO craft_stats (owner,type,val1,val2,val3,count,date)  VALUES ('.$user['id'].',2,'.$user['room'].','.$_SESSION[$rzname].','.$cs['rcid'].','.($cs['itemleft']+1).',NOW())
			ON DUPLICATE KEY UPDATE
			`count` = `count` + '.($cs['itemleft']+1).'
	') or Redirect();


	$q = mysql_query('COMMIT') or Redirect();

	SetMsg("Производство отменено");
	Redirect();
}

// начало крафта
if (isset($_REQUEST['craftid']) && $craftstatus == 0) {
	// начало производства
	$cid = intval($_REQUEST['craftid']);

	$ret = array(
		'ok' => 0,
		'captcha' => 0,
		'data' => '',
	);


	// check captcha
	$captcha = true;

	if ($user['prem'] >= 2) $captcha = false;
	if ($ins['getfrom'] == 1) $captcha = false;


	$q = mysql_query('SELECT * FROM craft_captcha WHERE owner = '.$user['id']);

	if (mysql_num_rows($q) > 0) {
		$cc = mysql_fetch_assoc($q);

		if (time() < $cc['nextcaptcha']) {
			$captcha = false;
		}
	}                 

	//if ($user['id'] == 684792) $captcha = true;

	if ($captcha) {
		if (!\components\Helper\Captcha::validate()) {
			header('Content-Type: text/html; charset=UTF-8');
			$c =  '<script>setTimeout("checkcenter();",500); function checkcenter() { if ($(\'#g-recaptcha-response\') && $(\'#g-recaptcha-response\').val() && $(\'#g-recaptcha-response\').val().length) startcraft(null,'.$cid.',$(\'#g-recaptcha-response\').val()); else setTimeout("checkcenter();",500);} </script><table border=0 width=400 height=100><tr><td  valign=top align="center" height=5 colspan="4"><font style="COLOR:#8f0000;FONT-SIZE:12pt">';
			$c .= '<a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
				<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-20px;right:-395px;" border=0 title="Закрыть"></a>Защита от автоматизации</font><div><small>Персонажи с <a href="http://oldbk.com/encicl/?/prem.html" target="_blank"><b>Platinum-аккаунтом</b></a> и мастера с ярмарочным инструментом<br>не проходят проверку на автоматизацию.</small></div>';
			$c .= '</td></tr>
				<tr><td colspan="4" class="center" valign=top>';   
			$c .= \components\Helper\Captcha::render();
			$c .= '</td>
			</tr><tr><td align="center"  colspan="3">
			</td></tr>
			</table>';
	
			$ret['data'] = iconv("windows-1251","UTF-8",$c);
			$ret['captcha'] = 1;
			die(json_encode($ret, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE));
		} else {
			mysql_query('INSERT INTO `craft_captcha` (`owner`,`nextcaptcha`) 
						VALUES (
							'.$user['id'].',
							'.(time()+(mt_rand(15,60)*60)).'
						) 
						ON DUPLICATE KEY UPDATE
							`nextcaptcha` = '.(time()+(mt_rand(15,60)*60)).'
			');
		}
	}

	$count = isset($_REQUEST['count']) ? intval($_REQUEST['count']) : 1;
	if ($count < 0) $count = 1;
	

	// проверяем производство в других локах
	$q = mysql_query('SELECT * FROM craft_job WHERE owner = '.$user['id']);
	if (mysql_num_rows($q) > 0) {
		SetMsg("Вы не можете начать новое производство, пока не закончите текущую работу.","e");
		die(json_encode($ret));
	}

	if ($user['align'] == 4) {
		SetMsg("Производство со склонностью хаоса запрещено","e");
		die(json_encode($ret));
	}

	if ($count > 1 && $user['prem'] != 3) {
		SetMsg("Массовое производство доступно только персонажам с Platinum-аккаунтом","e");
		die(json_encode($ret));
	}

	$q = mysql_query('SELECT * FROM craft_formula 
		WHERE craftloc = '.$locnum.' AND craftrazdel = '.$_SESSION[$rzname].' and craftid = '.$cid);

	if (!mysql_num_rows($q)) {
		SetMsg("Репецт не найден","e");
		die(json_encode($ret));
	}

	$rc = mysql_fetch_assoc($q) or die();
	if ($ins['craftspeedup'] > 0) {
		$rc['crafttime'] = round(($rc['crafttime'] - $rc['crafttime']*$ins['craftspeedup']/100),2);
	}

	if ($craft_week > 0) {
		$rc['crafttime'] = round($rc['crafttime']*0.5);
	}


	// проверяем инструмент в руке
	if ($ins === false || !in_array($ins['prototype'],$loc['razdel'][$_SESSION[$rzname]]['ins'])) {
		SetMsg("Необходимый инструмент не надет на персонажа","e");
		die(json_encode($ret));
	}

	if ($ins['maxdur']- $ins['duration'] -1 < $count) {
		SetMsg("Износ инструмента недостаточен для производства","e");
		die(json_encode($ret));
	}

	if ($rc['craftnlevel'] > $user['level']) {
		SetMsg("Уровень персонажа слишком мал для этого рецепта","e");
		die(json_encode($ret));
	}

	if (strlen($rc['craftnalign'])) {
		$get_align = (int)($user['align']);
		if ($get_align == 1) {$get_align=6;}
		if ($rc['craftnalign'] != $get_align) {
			SetMsg("Склонность вашего персонажа не совпадает для этого рецепта","e");
			die(json_encode($ret));
		}

	}

	reset($craftlist);
	while(list($k,$v) = each($craftlist)) {
		if ($rc['craftnprof'.$v] > 0) {
			if ($prof[$v."level"] < $rc['craftnprof'.$v]) {
				SetMsg("Недостаточный уровень ремесла для старта производства","e");
				die(json_encode($ret));
			}
		}
	}

	if ($count > 1 && $rc['crafttime']*$count > 6*60) {
		SetMsg("Время массового производства не может превышать 6 часов!","e");
		die(json_encode($ret));
	}

	// проверяем ресурсы
	$pr = unserialize($rc['craftnres']);
	if ($pr !== false) {
		while(list($k,$v) = each($pr)) {
			// $k - прото, $v - колво
			if ($res[$k] < $v*$count) {
				SetMsg("Недостаточно ресурсов для выполнения старта производства","e");
				die(json_encode($ret));
			}
		}
	}

	// проверяем требования
	reset($craftreqs_params);
	while(list($k,$v) = each($craftreqs_params)) {
		if ($rc['craft'.$k] > 0) {
			if ($rc['craft'.$k] > $user[$v['check']]) {
				SetMsg("Недостаточно параметров для выполнения старта производства","e");
				die(json_encode($ret));
			}
		}
	}

	if ($craft_week > 0) {
		$ins['craftbonus'] += 10;
	}

	$chance = CraftGetChanse($rc,$prof,$ins,$loc);
	if ($chance == 0) {
		SetMsg("Шанс производства 0%","e");
		die(json_encode($ret));
	}


	// всё ок, $pr - ресы, $rc - рецепт, $ins - инструмент вруках
	$q = mysql_query('START TRANSACTION') or Redirect();

	// находим айдишники ресурсов для старта
	$ids = array();
	reset($pr);
	while(list($k,$v) = each($pr)) {
		$q = mysql_query('SELECT * FROM inventory WHERE owner = '.$user['id'].' and prototype = '.$k.' and setsale = 0 and dressed = 0 LIMIT '.($v*$count)) or Redirect();
		if (mysql_num_rows($q) != $v*$count) {
			SetMsg("Недостаточно ресурсов для выполнения старта производства","e");
			die(json_encode($ret));
		}
		while($row = mysql_fetch_assoc($q)) {
			$ids[] = $row['id'];
		}
	}


	$dress = CraftGetItem($rc['craftprotoid'],$rc['craftprototype']);
	if (!$dress) {
		die(json_encode($ret));
	}

	$q = mysql_query('SELECT * FROM inventory WHERE id IN ('.implode(",",$ids).') and owner = '.$user['id'].' and setsale = 0 and dressed = 0 LIMIT '.count($ids)) or Redirect();

	while($row = mysql_fetch_assoc($q)) {
		$aids[] = get_item_fid($row)."/".$row['name'];
	}

	$rec['owner'] = $user['id'];
	$rec['owner_login'] = $user['login'];
	$rec['owner_balans_do'] = $user['money'];
	$rec['owner_balans_posle'] = $user['money'];
	$rec['target']=0;
	$rec['target_login']="Крафт-".$loc['name'];
	$rec['type']=1300;
	$rec['sum_kr']=0;
	$rec['sum_ekr']=0;
	$rec['sum_kom']=0;
	$rec['aitem_id']=implode(",",$aids);
	$rec['item_name']=$dress['name'];
	$rec['item_count']=$count;
	$rec['item_type']=$dress['type'];
	$rec['item_cost']=$dress['cost'];
	$rec['item_dur']=$dress['duration'];
	$rec['item_maxdur']=$dress['maxdur'];
	$rec['add_info'] = $ins['id']."/".$ins['name'];
	add_to_new_delo($rec) or die(json_encode($ret));

	// выпиливаем ресы и снимаем у оружия вынос
	mysql_query('UPDATE inventory SET duration = duration + '.$count.' WHERE id = '.$ins['id'].' LIMIT 1') or Redirect();
	mysql_query('DELETE FROM inventory WHERE id IN ('.implode(",",$ids).') and owner = '.$user['id'].' and setsale = 0 and dressed = 0 LIMIT '.count($ids)) or Redirect();



	$mfchance = 0;

	if ($rc['craftmfchance'] > 0) {
		if ($craft_week > 0) {
			$rc['craftmfchance'] += 2;
		}
		$mfchance = $rc['craftmfchance']+$ins['mfchance']+$prof[$craftlist[$rc['craftgetprof']]."level"]*0.5;
	}


	// начинаем крафт
	mysql_query('INSERT INTO craft_job 
		(owner,loc,itemcount,itemleft,crafttime,craftlefttime,status,jobprotoid,jobprototype,itemname,itemimg,rcid,linkcache,insproto,craftchance,craftmfchance,lastupdate)
		VALUES(
			'.$user['id'].',
			'.$user['room'].',
			'.$count.',
			'.($count-1).',
			'.$rc['crafttime'].',
			'.($rc['crafttime']*60).',
			1,
			'.$rc['craftprotoid'].',
			'.$rc['craftprototype'].',
			"'.mysql_real_escape_string($dress['name']).'",
			"'.mysql_real_escape_string($dress['img']).'",
			'.$rc['craftid'].',
			"'.mysqL_real_escape_string(link_for_item($dress)).'",
			'.$ins['prototype'].',
			'.$chance.',
			'.$mfchance.',
			'.time().'
		)
	') or die(json_encode($ret));

	mysqL_query('INSERT INTO craft_stats (owner,type,val1,val2,val3,count,date)  VALUES ('.$user['id'].',1,'.$user['room'].','.$_SESSION[$rzname].','.$rc['craftid'].','.$count.',NOW())
			ON DUPLICATE KEY UPDATE
			`count` = `count` + '.$count.'
	') or die(json_encode($ret));


	$q = mysql_query('COMMIT') or die(json_encode($ret));

	SetMsg('Начато производство «'.$dress['name'].'» в количестве '.$count.' шт.');

	$ret['ok'] = 1;
	die(json_encode($ret));
}


$head = '
<tr class="title">
	<td colspan="3" class="center">
		%TEXT%
	</td>
</tr>
';

// мессаги
if (isset($_SESSION['craftmsg'])) {
	$msg = $_SESSION['craftmsg'];
	$typet = $_SESSION['craftmsgtype'];
	unset($_SESSION['craftmsg']);
	unset($_SESSION['craftmsgtype']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="windows-1251">
<title></title>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/json2.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/jquery.serializejson.min.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/jstorage.min.js'></script>
<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<style>
button:focus {
    outline: 0;
}

input:focus {
    outline: 0;
}

.strong {
	font-weight:bold;
}

SELECT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
TEXTAREA {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
INPUT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}

.noty_message { padding: 5px !important;}
#page-wrapper .listreq {
	list-style-type:disc;
	margin-left:15px;
}

#page-wrapper .nop {
	padding:0px;
}

.mw300 { min-width:300px; }
.mw1400 { min-width:1200px; }
#page-wrapper table.table.headtitlet {
	min-width:900px;
	margin-bottom:0px;
}
.mw1100 {
	min-width:900px;
}
#page-wrapper table td.vamiddle { 
	vertical-align: middle;
}
.vamiddle { 
	vertical-align: middle;
}

.invgroupcount {
	position: absolute;
	bottom: 4px;
	right: 5px;
	font-weight:bold;
	background-color:#717070;
	width:30px;
	color:white;
	filter:alpha(opacity=90);
	-moz-opacity: 0.9;
	opacity: 0.9;
	text-align:center;
}

.invgroupcount3 {
	position: absolute;
	bottom: 4px;
	right: 3px;
	font-weight:bold;
	background-color:#717070;
	width:30px;
	color:white;
	filter:alpha(opacity=90);
	-moz-opacity: 0.9;
	opacity: 0.9;
	text-align:center;
}

.invgroupcount2 {
	position: absolute;
	bottom: 4px;
	right: 5px;
	font-weight:bold;
	background-color:#F25858;
	width:30px;
	color:white;
	filter:alpha(opacity=90);
	-moz-opacity: 0.9;
	opacity: 0.9;
	text-align:center;
}

.gift-block {
	margin-top:5px;
	width: 64px;
	float:left;
	position:relative;
}
.gift-block .gift-image {
	opacity: 1;
}
#page-wrapper ul.listreq li {
    padding: 0px;
}

#page-wrapper .tnop td {
    padding: 0px;
}
table#questdiag {
	width: 500px;
}
table#questdiag td img {
    display: block;
}

#page-wrapper #questdiag {
	table-layout: auto;
}

#page-wrapper #questdiag td {
	padding: 0px;
}

#maindiv a.npc {
	position: absolute;
}
#maindiv .npc_hover {
	display: none;
}
#maindiv a.npc:hover .npc_hover {
	display: block;
}
#maindiv a#npc-friday {
	top: 20px;
	left: 68px;
	width: 226px;
	height: 407px;
}
#maindiv a#npc-smithy {
	top: 20px;
	left: 100px;
	width: 170px;
	height: 407px;
}
#maindiv a#npc-armorer {
	left: 11px;
	top: 27px;
	width: 226px;
	height: 407px;
}
#maindiv a#npc-magiclab {
	left: 120px;
	top: 56px;
	width: 136px;
	height: 251px;
}
#maindiv a#npc-jeweler {
	left: 16px;
	top: 18px;
	width: 283px;
	height: 407px;
}
#maindiv a#npc-carpentry {
	left: 80px;
	top: 34px;
	width: 226px;
	height: 407px;
}
#maindiv a#npc-alexandro {
	left: 15px;
	top: 29px;
	width: 283px;
	height: 407px;
}

</style>
<script type='text/javascript'>
RecoverScroll.start();

var bankauth = <?php echo (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) ? "true" : "false" ?>;
var bankbalance = <?php echo (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) ? $bank['ekr'] : "false" ?>;

function doauth() {
	var bankid = $('#bankid').val();
	var bankpass = $('#bankpass').val();

	$.get('?bankauth=1&bankid='+bankid+'&bankpass='+bankpass, function(data) {
		$('#pl').show(200);
		$('#pl').html(data);
		$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
		    e.stopImmediatePropagation();
		});
	});
					
	return true;
}

function hl() {
	location.href = "?speedup=1";
}

function checkbank() {
	if (bankauth) {
		hl();
		return true;
	} else {
		$.get('?bankauth=1', function(data) {
			$('#pl').html(data);
		 	$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'  });
			$('#pl').show(200);

			$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
			    e.stopImmediatePropagation();
			});
		});
					
		return false;
	}
}


function showhide(id) {
	if (document.getElementById(id).style.display=="none") {
		document.getElementById(id).style.display="block";
	} else {
		document.getElementById(id).style.display="none";
	}
}

function closehint3(clearstored) {
	document.getElementById("hint3").style.visibility="hidden";
}

function closeinfo() {
	$('#pl').hide(200);
}
			
$(window).resize(function() {
	$('#pl').css({position:'absolute',left:($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+120)+'px'});
});			

function AddCount(event,craftid, craftname, maxres) {

    var el = document.getElementById("hint3");
	el.innerHTML = '<form onsubmit="startcraft(event,'+craftid+',\'\',$(\'#craftcount\').val()); return false;" method="post" style="margin:0px; padding:0px;"><INPUT TYPE="hidden" name="craftid" value="'+craftid+'"><table class="tnop" style="FONT-SIZE: 10pt; FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif" border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B>Произвести несколько шт.</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3(); return false;"><BIG><B>x</TD></tr><tr><td colspan=2>'+
	'<table class="tnop" border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><colgroup><col width="85%"><col width="20%"></colgroup><tr><td colspan=2 align=center><B><I>'+craftname+'</td></tr><tr><td align=right>'+
	'Кол-во (макс '+maxres+' шт.) <INPUT TYPE="text" id="craftcount" NAME="count" size=4 ></td><td align="center"><INPUT style="height:17px;" OnClick="startcraft(event,'+craftid+',\'\',$(\'#craftcount\').val()); return false;" TYPE="button" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	y = event.pageY;
	el.style.top = (y -120) + 'px';
	document.getElementById("craftcount").focus();
}
<? if ($craftstatus == 1) { ?>

var CraftTimer = -1;

<?php
$craftinterval  = 61;
if ($cs['craftlefttime'] < 120) {
	$craftinterval = 45;
}
if ($cs['craftlefttime'] < 60) {
	$craftinterval = 20;
}
if ($cs['craftlefttime'] < 30) {
	$craftinterval = 10;
}
?>
var CraftInterval = <?=$craftinterval;?>;

function ucp() {
	$.get('?checkcraft=1', function(data) {
		CraftInterval = 61;

		if (!data.length) {
			CraftTimer = setTimeout('ucp()', CraftInterval*1000);			
			return;
		}

		var ret = JSON.parse(data);

		if (ret['none'] == 1) {
			return;
		}

		if (ret['redirect'] == 1) {
			location.href = "?";
			return;
		}

		$("#itemall").html(ret['itemall']);
		$("#procentline").css("width",ret['procentline']+"%");
		$("#itemtime").html(ret['itemtime']);
		$("#itemtimenext").html(ret['itemtimenext']);
	
	
		if (ret['craftlefttime'] < 120) {
			CraftInterval = 45;
		}
		if (ret['craftlefttime'] < 60) {
			CraftInterval = 20;
		}
		if (ret['craftlefttime'] < 30) {
			CraftInterval = 10;
		}

		CraftTimer = setTimeout('ucp()', CraftInterval*1000);
	}).fail(function() {
		CraftTimer = setTimeout('ucp()', CraftInterval*1000);
	});
}

CraftTimer = setTimeout('ucp()', CraftInterval*1000);

<?php } ?>

function startcrafterr() {
	alert("Ошибка! Повторите попытку!");
}


function startcraft(event,id,gr,count) {
	if (gr === undefined) gr = "";
	if (count === undefined) count = 1;

	$.post('?craftid='+id+'&count='+count,'g-recaptcha-response='+gr, function(data) {
		if (!data.length) {
			startcrafterr();
			return;
		}

		var ret = JSON.parse(data);
		if (ret['ok'] == 0) {
			if (ret['captcha'] == 1) {
				el = document.getElementById("pl");
				y = event.pageY;
				el.style.top = (y -120) + 'px';

				$('#pl').show(200);
				$('#pl').html(ret['data']);
				$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
				    e.stopImmediatePropagation();
				});
				return;
			}
		}
		location.href = "?";
	}).fail(function() {
		startcrafterr();
	});
}

</script>
</head>
<body>
<div id="page-wrapper">
<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 120px;
				width:600px; background-color: #eeeeee; cursor: move;
				border: 1px solid black; display: none;">
</div>
<?php
// квесты и мессаги
if (isset($msg) && strlen($msg)) {
	echo '	
		<script>
			var n = noty({
				text: "'.addslashes($msg).'",
			        layout: "topLeft2",
			        theme: "relax2",
				type: "'.($typet == "e" ? "error" : "success").'",
			});
		</script>
	';
}

?>
    <div class="title">
        <div class="h3">
            <?=$loc['name'];?>
        </div>
        <div id="buttons">
            <a class="button-dark-mid btn" href="#" onclick="window.open('help/<?=$loc['helpname'];?>.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes');return false;" title="Подсказка">Подсказка</a>
            <a class="button-mid btn" OnClick="location.href='?tmp='+Math.random(); return false;" title="Обновить">Обновить</a>
            <a class="button-mid btn" OnClick="document.getElementById('cityform').submit(); return false;" title="Вернуться">Вернуться</a>
	    <FORM action="craft.php" style="margin:0px;padding:0px;display:block;" id="cityform" method="GET"><INPUT TYPE="hidden" value="1" name="exit"></form>
        </div>
    </div>
    <div id="rem">
        <table cellspacing="0" cellpadding="0" class="mw1400">
            <colgroup>                                 
                <col>
                <col width="310px">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <table class="table a_strong headtitlet" cellspacing="0" cellpadding="0">
                        <tr class="head-line">
<?php
$razdels = $loc['razdel'];
$razdelsend = end($razdels);

$first = true;
reset($razdels);
while(list($k,$v) = each($razdels)) {
	echo '<th>';
	if ($first) {
		echo '<div class="head-left"></div>';
		$first = false;
	}
	echo '<div class="head-title p">';
        echo '<a '.($_SESSION[$rzname] == $k ? 'class="active"' : '').'  href="?razdel='.$k.'">'.$v['name'].'</a>';
	echo '</div>';
	if ($v == $razdelsend) {
		echo '<div class="head-right"></div>';
	} else {
		echo '<div class="head-separate"></div>';
	}
	echo '</th>';
}
?>
                        </tr>
                    </table>
                    <table class="table a_strong border mw1100" cellspacing="0" cellpadding="0">		
		            <colgroup>
		                <col width="200px">
		                <col>
		                <col width="550px">
		            </colgroup>
				<tr class="title">
				<td colspan="3" class="center" style="padding:0px;">
<?php

// статус
if ($craftstatus == 0) {
	echo '<div style="padding-top:5px;padding-bottom:5px;"><b>'.$loc['razdel'][$_SESSION[$rzname]]['desc'].'</b></div>';
} else {

	$all = $cs['itemcount']*$cs['crafttime']*60;
	$left = (((($cs['itemleft'])*$cs['crafttime'])*60)+$cs['craftlefttime']);

	$pr = 100-floor($left*100/$all);

	echo '<div style="position:relative;width:100%;height:15px;padding-top:5px;padding-bottom:5px;">';
	echo '<div style="z-index:2;position:absolute;left:0px;top:0px;width:100%;height:100%;margin-top:5px;"><b>До завершения производства всей партии <span id="itemall">'.prettyTime(null,time()+$left).'</span>'.'</b></div>';
	echo '<div id="procentline" style="z-index:1;position:absolute;left:0px;top:0px;width:'.$pr.'%;height:100%;background-color:#ffc1c1;"></div>';
	echo '</div>';
}

?>
				</td>
				</tr>
			    <?php if ($craftstatus == 0) { ?>
                            <tr><td colspan="3" align="right">
			    	<form method="POST" id="fall" style="margin:0px;padding:0px;display:block;">
                                <div class="head-title" style="top:1px;">
                                    <select name="fall" style="width:350px;height:14px;margin:0px;top:0px;" OnChange="document.getElementById('fall').submit();">
					<option value = "0" <? if ((int)($_POST['fall'])==0) { echo ' selected ' ; } ?>>Показывать все рецепты</option>
					<option value = "1" <? if ($_POST['fall']>0) { echo ' selected ' ; $viewlevel=true; } ?>>Показывать рецепты подходящие по требованию ремесла</option>
                                    </select>
                                </div>
				</td></form>
                            </tr>
			    <? } ?>
<?php
// основное окно
$showproflist = array();

if ($craftstatus == 0) {
	// просмотр рецептов

	$addsql = "";
	if (isset($viewlevel) && $viewlevel == true) {
		$addsql .= ' and ( ';
		reset($craftlist);
		while(list($k,$v) = each($craftlist)) {
			$cname = 'craftnprof'.$v;
			$addsql .= ' '.$cname.' <= '.$prof[$v.'level'].' and ';
		}
		$addsql .= ' 1=1 ) ';

	}

	$defsql = " and is_enabled = 1 ";
	if (ADMIN) $defsql = "";
	
	$query = '
		SELECT %COUNT% FROM (
			(SELECT  * FROM craft_formula 
				LEFT JOIN shop ON craft_formula.craftprotoid = shop.id
				WHERE craft_formula.craftprototype = 1 and is_deleted = 0 '.$defsql.' and craftloc = '.$locnum.' AND craftrazdel = '.$_SESSION[$rzname].' '.$addsql.'
			)
			UNION
			(SELECT  * FROM craft_formula 
				LEFT JOIN eshop ON craft_formula.craftprotoid = eshop.id
				WHERE craft_formula.craftprototype = 2 and is_deleted = 0 '.$defsql.' and craftloc = '.$locnum.' AND craftrazdel = '.$_SESSION[$rzname].' '.$addsql.'
			)
			UNION
			(SELECT  * FROM craft_formula 
				LEFT JOIN cshop ON craft_formula.craftprotoid = cshop.id
				WHERE craft_formula.craftprototype = 3 and is_deleted = 0 '.$defsql.' and craftloc = '.$locnum.' AND craftrazdel = '.$_SESSION[$rzname].' '.$addsql.'
			)
		) as allitems ORDER BY allitems.craftcomplexity ASC, allitems.cost ASC
	';
	
	// считаем колво
	$count = mysql_query_cache(str_replace("%COUNT%","count(*) as cc",$query),false,$qcache);
	// сами рецепты
	$q = mysql_query_cache(str_replace("%COUNT%","*",$query.MakeLimit()),false,$qcache);

	$p = MakePages($count[0]['cc']);
	if ($p) {
		echo str_replace("%TEXT%",$p,$head);
	}
	
	$out = "";
	$i = 0;
	
	while(list($k,$row) = each($q)) {
		$isreqok = true;
		$showproflist[$row['craftgetprof']] = 1;
		$out .= rendercraftitem($row,$user,$prof,$res,$ins,$loc,$i,$craft_week);
	}
	
	echo $out;


	if ($p) {
		echo str_replace("%TEXT%",$p,$head);
	}
} elseif ($craftstatus == 1 || $craftstatus == 2) {
	// $cs  - статус производства	
	// производство в процессе
	$out .= '<tr class="even2">
			<td class="center vamiddle">
				<ul class="dress-item">
					<li>
						<b>'.$cs['linkcache'].'</b><br>
					</li>
					<li>&nbsp;</li>
					<li>
						<IMG SRC="http://i.oldbk.com/i/sh/'.$cs['itemimg'].'"><br>
					</li>
	';

	if ($craftstatus == 1) {
		$spprice = round(($cs['craftlefttime'] / 3600 * $craftspeedupprice)+($cs['itemleft']*$cs['crafttime'] / 60 * $craftspeedupprice),2);
		if ($spprice < $craftspeedupmin) $spprice = $craftspeedupmin;

		$out .= '<li>';
		$out .= '<a href="#" OnClick="if (confirm(\'Вы уверены, что хотите ускорить производство всей продукции за '.$spprice.' екр?\')) {checkbank(); return false;}">Ускорить за '.$spprice.' екр.</a>';
		$out .= '</li>';
	}

	$out .= '<li><a href="#" OnClick="if (confirm(\'При отмене производства потраченные на старт производства ресурсы будут потеряны. \nВы уверены?\')) location.href = \'?cancel=1\';">Отменить</a></li>';
	$out .= '</ul></td><td colspan="2">';
	$out .= '<div style="clear:both;"><b>Статистика текущего производства: </b><br><ul class="listreq">';
	$out .= '<li>Заказано предметов: '.$cs['itemcount'].' шт.</li>';
	$craftedcount = (($cs['itemleft']-$cs['itemcount'])*-1)-1;
	$out .= '<li>Произведено предметов: <span id="itemleft">'.$craftedcount.'</span> шт.</li>';
	$out .= '<li>Общее время производства: <span id="itemtime">'.prettyTime(null,time()+(($craftedcount*$cs['crafttime']*60)+$cs['crafttime']*60-$cs['craftlefttime'])).'</span></li>';
	$out .= '<li>Следующий предмет будет готов через: <span id="itemtimenext">'.prettyTime(null,time()+($cs['craftlefttime'])).'</span></li>';
	$out .= '</div></td></tr>';
	echo $out;	
}
?>


                    </table>
                </td>
                <td>
		    <form id="formfilter">
                    <table id="filter" class="mw300" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
				<strong>
                                Вес всех ваших вещей:
				<?php
					$my_massa=0;         
					$q = mysql_query("SELECT IFNULL(sum(`massa`),0) as massa , setsale, bs_owner, dressed  FROM oldbk.inventory WHERE `owner` = '{$user['id']}'   GROUP by setsale,bs_owner,dressed ");
					while ($row = mysql_fetch_array($q)) {
						if (($user['in_tower'] == $row['bs_owner']) AND   ($row['setsale'] ==0 )  AND   ($row['dressed'] ==0)) {
							$my_massa+=$row['massa'];
						}
					}
					echo $my_massa;?>/<?=get_meshok()?>
				</strong>
				<br>
                                У Вас в наличии: <span class="money strong"><?=$user['money']?></span><strong> кр.</strong><br>
				<?php if ($bank) { ?>
                                В банке: <span class="money strong"><?=$bank['ekr']?></span><strong> екр.</strong><br> 
				<?php } ?>
				<?php
					if (count($showproflist)) {
						echo '<br><b>Ваше ремесло:</b><br>';
						while(list($k,$v) = each($showproflist)) {
							echo $craftlistrname[$k].' ['.$prof[$craftlist[$k].'level'].'], опыт '.$prof[$craftlist[$k].'exp'].'/'.$craftexptable[$prof[$craftlist[$k].'level']+1].'<br>';
						}                                           
					}                        
				?>
                            </td>
                        </tr>
			<tr><td align="center">
			<div id="maindiv" style="position:relative;z-index:1;">
			<?php

			$bot_id = null;
			switch($user['room']) {
				case 91:
					// кузница
					?>
					<img class="npc_bg" src="http://i.oldbk.com/i/craft/npc_smithy.jpg" alt="Мастер Кальвис" title="Мастер Кальвис"/>
					<a class="npc" href="?quest=1" id="npc-smithy">
						<img class="npc_hover" src="http://i.oldbk.com/i/craft/npc_smithy_h3.png" alt="Мастер Кальвис" title="Мастер Кальвис"/>
					</a>
	
					<?php
					$bot_id = \components\Helper\BotHelper::BOT_MASTER_KALVIS;
					$fdiag = '– Изволите сделать заказ?';
					$ldiag = '– Спасибо, не сейчас.';
				break;
				case 92:
					// таверна
					?>
					<img class="npc_bg" src="http://i.oldbk.com/i/craft/npc_friday.jpg" alt="бармен Пятницо" title="бармен Пятницо"/>
					<a class="npc" href="?quest=1" id="npc-friday">
						<img class="npc_hover" src="http://i.oldbk.com/i/craft/npc_friday_h.png" alt="бармен Пятницо" title="бармен Пятницо"/>
					</a>
	
					<?php
					$bot_id = \components\Helper\BotHelper::BOT_BARMEN_PYATNICO;
					$fdiag = '– Желаете выпить, перекусить или снять комнату?';
					$ldiag = '– Заманчиво, но не сейчас.';
				break;
				case 93:
					// Лаборатория магов и алхимиков
					?>
					<img class="npc_bg" src="http://i.oldbk.com/i/craft/npc_magiclab.jpg" alt="Алхимик Агниус" title="Алхимик Агниус"/>
					<a class="npc" href="?quest=1" id="npc-magiclab">
						<img class="npc_hover" src="http://i.oldbk.com/i/craft/npc_alchemist_h.png" alt="Алхимик Агниус" title="Алхимик Агниус"/>
					</a>

					<?php
					$bot_id = \components\Helper\BotHelper::BOT_ALHIMIK;
					$fdiag = '– Ничего не трогайте руками, пожалуйста!';
					$ldiag = '– Не очень то и хотелось. Пока.';
				break;
				case 94:
					// Мастерская ювелиров и портных
					if (in_array($_SESSION[$rzname],array(4,5,6))) {
						?>
						<img class="npc_bg" src="http://i.oldbk.com/i/craft/fon_tailor.jpg" alt="Алехандро" title="Алехандро"/>
						<a class="npc" href="?quest=1" id="npc-alexandro">
							<img class="npc_hover" src="http://i.oldbk.com/i/craft/npc_tailor_hover.png" alt="Алехандро" title="Алехандро"/>
						</a>
	
						<?php
						$bot_id = \components\Helper\BotHelper::BOT_ALEXANDRO;
						$fdiag = '– Рад приветствовать!';
						$ldiag = '– Ничего не нужно, я уже ухожу.';
					} else {
						?>
						<img class="npc_bg" src="http://i.oldbk.com/i/craft/npc_jeweler.jpg" alt="Джулиус" title="Джулиус"/>
						<a class="npc" href="?quest=1" id="npc-jeweler">
							<img class="npc_hover" src="http://i.oldbk.com/i/craft/npc_jeweler_h.png" alt="Джулиус" title="Джулиус"/>
						</a>
	
						<?php
						$bot_id = \components\Helper\BotHelper::BOT_JULIA;
						$fdiag = '– Не стойте в дверях, проходите!';
						$ldiag = '– Я только посмотреть, зайду позже.';
					}
				break;
				case 95:
					// Мастерская плотника
					?>
					<img class="npc_bg" src="http://i.oldbk.com/i/craft/npc_carpentry.jpg" alt="Ду Рандир" title="Ду Рандир"/>
					<a class="npc" href="?quest=1" id="npc-carpentry">
						<img class="npc_hover" src="http://i.oldbk.com/i/craft/npc_carpenter_h.png" alt="Ду Рандир" title="Ду Рандир"/>
					</a>

					<?php
					$bot_id = \components\Helper\BotHelper::BOT_DU_RANDIR;
					$fdiag = '– Здрасте! Можно мне вам помогать?';
					$ldiag = '– Спасибо, пока мне ничего не нужно. Зайду в следующий раз.';
				break;
				case 96:
					// Башня оружейников
					?>
					<img src="http://i.oldbk.com/i/craft/npc_armorer.jpg" class="npc_bg" alt="Клэр" title="Клэр"/>
					<a class="npc" href="?quest=1" id="npc-armorer">
						<img src="http://i.oldbk.com/i/craft/npc_armorer_h.png" class="npc_hover" alt="Клэр" title="Клэр"/>
					</a>
	
					<?php
					$bot_id = \components\Helper\BotHelper::BOT_KLER;
					$fdiag = '– Чем могу помочь?';
					$ldiag = '– Ничем, уже ухожу.';
				break;
			}

			$mldiag = array();
			$mlquest = "-250/110";
			if(isset($_GET['qaction']) && isset($_GET['d']) && $bot_id) {
				$BotDialog = new \components\Component\Quests\QuestDialogNew($bot_id);
				//зашли в движок квестов
				$dialog_id = isset($_GET['d']) ? (int)$_GET['d'] : null;
				$action_id = isset($_GET['a']) ? (int)$_GET['a'] : null;
				$dialog = $BotDialog->dialog($dialog_id, $action_id);
				if($dialog !== false) {
					$mldiag[0] = $dialog['message'];
					foreach ($dialog['actions'] as $action) {
						$key = '&a='.$action['action'];
						if(isset($action['dialog'])) {
							$key .= '&d='.$action['dialog'];
						}
						$mldiag[$key] = $action['message'];
					}
				}
			}

			if (isset($_GET['quest']) && empty($mldiag) && $bot_id) {
				$BotDialog = new \components\Component\Quests\QuestDialogNew($bot_id);
				
				$mldiag[0] = $fdiag;

				foreach ($BotDialog->getMainDialog() as $dialog) {
					$key = '&d='.$dialog['dialog'];
					$mldiag[$key] = $dialog['title'];
				}

				$mldiag[4] = $ldiag;

			}
			if(!empty($mldiag)) {
				require_once('mlquest.php');
			}

			?>
			</div>
                </td>
            </tr>
            </tbody>
        </table>
        </form>
<div id="hint3" class="ahint"></div>
<script type="text/javascript">
$(function() {
	$("#pl").draggable();
	$(window).resize();
});
</script>
</body>
</html>
<?php
if (isset($miniBB_gzipper_encoding)) {
	$miniBB_gzipper_in = ob_get_contents();
	$miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	$miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
	$miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
	$gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
	$percent = round($gzpercent);
	$miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	ob_clean();
	header('Content-Encoding: '.$miniBB_gzipper_encoding);
	echo $miniBB_gzipper_out;
}
?>