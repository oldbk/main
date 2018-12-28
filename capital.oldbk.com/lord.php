<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("lord.php");
	}


	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	include('connect.php');
	include('functions.php');

	if ($user['room'] != 90) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	if (time() >= mktime(0,0,0,11,10,2014)) Redirect("lord2.php");

	if (isset($_GET['exit'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '200' WHERE  `users`.`id`  = '{$user[id]}' ;");
		header('location: restal.php');
		die();
	}


	if (isset($_GET['history'])) {
		if (isset($_GET['p'])) $_GET['p'] = intval($_GET['p']);
		if ($_GET['p'] < 0) $_GET['p'] = 0;

		$res = array();
		$res['users'] = array();
		mysql_query('SET NAMES utf8');
		$q = mysql_query('SELECT users.login,align,klan,points FROM lord_donate LEFT JOIN users ON lord_donate.owner = users.id WHERE users.align != 4 and block = 0 ORDER BY points DESC LIMIT '.($_GET['p']*30).',30');
		$i = 1;	

		while($u = mysql_fetch_assoc($q)) {
			$res['users'][$_GET['p']*30+$i] = $u;
			$i++;
		}

		$q = mysql_query('SELECT count(*) as cccount FROM lord_donate LEFT JOIN users ON lord_donate.owner = users.id WHERE users.align != 4 and block = 0');
		$q = mysql_fetch_assoc($q);
		$res['all'] = ceil($q['cccount'] / 30);
		echo json_encode($res);
		die();
	}


	if (isset($_GET['logoutbank'])) {
		unset($_SESSION['bankid']);
		Redirect('lord.php?qaction=d2');
	}



	$mllord = array(
		0 => array(
			"0"  => "Я хочу пожертвовать на строительство замка:",
			"d1" => "Кредиты.",
			"d2" => "ЕвроКредиты.",
			"d3" => "Репутацию.",
			"d4" => "Вещи.",
			"33333"  => "Я ничего не хочу.",
		),
		1 => array(
			"0"  => "У вас ".$user['money']." кр. Сколько желаете пожертвовать?",
			"1" => '<!-- NOLINK -->Пожертвовать (1 кр. = 1 пожертвование): <form method=post action="lord.php?quest=1"><input type="text" name="howkr"> кр. <input type=submit value="Пожертвовать"></form>',
			"33333"  => "Я передумал.",
		),
		3 => array(
			"0"  => "У вас ".$user['repmoney']." репутации. Сколько желаете пожертвовать?",
			"1" => '<!-- NOLINK -->Пожертвовать (10 реп. = 1 пожертвование): <form method=post action="lord.php?quest=1"><input type="text" name="howrep"> репутации <input type=submit value="Пожертвовать"></form>',
			"33333"  => "Я передумал.",
		),
		4 => array(
			"0" => "Какие вещи вы хотите пожертвовать?",
			"d5&razdel=1" => "Обмундирование",
			"d5&razdel=2" => "Заклятия",
			"d5&razdel=3" => "Ресурсы",
			"d5&razdel=4" => "Прочее",
			"d0" => "Вернуться",
		),
	);

	require_once('item_functions.php');

	function GetItemLordPrice($row) {
		if ($row['ecost'] > 0 && $row['type'] == 12) {
			return round($row['ecost']*20);
		}
		if (($row['prototype'] > 3000 && $row['prototype'] < 3022) || ($row['prototype'] > 103000 && $row['prototype'] < 103022)) {
			return 1;
		}

		if ($row['prototype'] == 3003060) return 3;
		if ($row['prototype'] == 777771) return 25;

                $item_type = round($row['type']);
		
		if($item_type < 12 || $item_type==28) {
			$shop_skupka = 1;
			$pr = curr_price($row,1,1);
			if (!$pr['summ']) $pr['summ'] = $row['cost'];
			$price = $pr['summ'];
	        } else {
			$price = $row['cost'];
		}


		$price = round($price);
		if ($row['unik'] > 0 && $row['ecost'] > 0) {
			return $row['ecost']*50;
		}
		if ($row['ecost'] > 0) {
			return $row['ecost']*25;
		}
		if ($row['unik'] > 0) {
			return $price*2;
		}
		return $price;
	}
	                            

	if (isset($_GET['qaction']) && $_GET['qaction'] == "d5" && isset($_GET['razdel'])) {
		$what_not_to_sell=' AND `prototype` not in (104,100000009,100000010,20000,1006232,1006233,1006234,510,550,599) and (`prototype` < 55510300 OR `prototype` > 55510400) ';
		$mainsql = "SELECT * FROM oldbk.`inventory` WHERE `dressed`= 0 AND setsale=0 and type != 200 AND prokat_idp = 0 ".$what_not_to_sell." AND bs_owner = 0 AND arsenal_klan='' AND present!='Арендная лавка' AND `owner` = ".$user['id'];
		$resurs=' AND ((`prototype`>3000 AND `prototype` <3022 ) OR (`prototype`>103000 AND `prototype` <103022)) ';

		switch($_GET['razdel']) {
			default: die(); break;
			case "1";
				$mainsql .= ' AND cost > 0 AND (`type` < 12 OR `type`=27 OR `type`=28 or `type`=30 OR  `type`=33 ) ';
			break;
			case "2";             
				$mainsql .= ' AND cost > 0 AND type = 12 ';
			break;
			case "3";
				$mainsql .= ' AND cost > 0 '.$resurs;
			break;
			case "4";
				$mainsql .= ' AND prototype IN (3003060,777771) ';
			break;
		}

		if (!isset($_GET['r'])) {
			$q = mysql_query($mainsql);
		}

		if (!isset($_GET['r']) && mysql_num_rows($q) == 0) {
			$mllord[5] = array(
				0 => "У вас нечего жертвовать в данном разделе",
				"d4" => "Выбрать другой раздел",
				"33333" => "Я передумал.",
			);
		} else {
			$mllord[5] = array();
			if (isset($_GET['r'])) {
				// жертвуем вещь
				$item = mysql_query($mainsql.' AND id = '.intval($_GET['r']));
				if (mysql_num_rows($item) != 1) die();
				$item = mysql_fetch_assoc($item);
				$price = GetItemLordPrice($item);
				if (!$price) die();

				mysql_query('START TRANSACTION') or die();				

				// отнимаем деньги
				mysql_query('DELETE FROM inventory WHERE id = '.$item['id']);

				mysql_query('INSERT INTO `lord_donate` (`owner`,`points`) 
							VALUES(
								'.$user['id'].',
								'.($price).'
							) 
							ON DUPLICATE KEY UPDATE
								`points` = `points` + '.($price).'
				') or die();


				// пишем в дело
				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['target']=0;
				$rec['target_login']="Замок Лорда Разрушителя";
				$rec['type']=290; // заплатил
				$rec['item_name']=$item['name'];
				$rec['item_count'] = 1;
				$rec['aitem_id'] = get_item_fid($item);
				$rec['item_type'] = $item['type'];
				$rec['item_cost'] = $item['cost'];
				$rec['item_dur'] = $item['dur'];
				$rec['item_maxdur'] = $item['maxdur'];
				$rec['item_proto'] = $item['prototype'];
				$rec['add_info'] = ($price)." пожертвований.";
	                                               
				add_to_new_delo($rec) or die();
	
				$mllord[5] = array(
					0 => "<font color=red>Вы отдали ".$item['name']." и сделали пожертвование в количестве ".($price).".</font><br>Список вещей: ",
				);

				mysql_query('COMMIT') or die();
				$q = mysql_query($mainsql);
			} else {
				$mllord[5][] = "Список вещей: ";
			}
			$mllord[5]["d4"] = "Вернуться";
			while($row = mysql_fetch_assoc($q)) {
				ob_start();
				$bt .= '<!-- NOLINK --><TABLE>';
				$za = GetItemLordPrice($row);
				if (!$za) continue;
				showitem ($row,0, false,'','<a href=?qaction=d5&razdel='.intval($_GET['razdel']).'&r='.$row['id'].'><small><b>Сдать за '.$za.' пожертвований</b></small></a></td>', 0, 0);
				$bt .= ob_get_contents();                                       
				ob_end_clean();
				$bt .= '</TABLE>';
				$mllord[5][] = $bt;
				$bt = "";
			}
			$mllord[5]["d4"] = "Вернуться";
		}
	}

	if (isset($_POST['bankid'])) {
		$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.intval($_POST['bankid']).' and pass = "'.md5($_POST['bankpass']).'"');
		if (mysql_num_rows($q) > 0) {
			$_SESSION['bankid'] = intval($_POST['bankid']);
			unset($_POST['bankid']);
			$_GET['qaction'] = "d2";
		} else {
			$mllord[2] = array(
				0 => "Неверный пароль.",
				"d2" => "Попробовать еще раз.",
				"33333" => "Я передумал.",
			);
			$_GET['qaction'] = "d2";
		}
	}                                  


	if (!$_SESSION['bankid']) {
		if (!isset($_POST['bankid'])) {
			$mllord[2][0] = 'Для пожертвования екр необходимо выполнить вход в банк.';
			$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id']);
			if (mysql_num_rows($q) == 0) {
				$mllord[2][0] .= 'Нет ни одного счёта в банке';
			} else {
				$mllord[2][1] = '<!-- NOLINK --><form method="POST" action="lord.php?quest=1"><select name="bankid">';
				while($b = mysql_fetch_assoc($q)) {
					$mllord[2][1] .= '<option value="'.$b['id'].'">'.$b['id'].'</option>';
				}                                               
				$mllord[2][1] .= '</select> Пароль: <input type="password" value="" name="bankpass"> <input type="submit" value="Вход">';
			}
			$mllord[2][33333] = 'Я передумал.';
		}
	} else {
		$bank_owner = mysql_fetch_assoc(mysql_query('SELECT * FROM bank WHERE id = '.$_SESSION['bankid']));
		$mllord[2][0] = 'У вас на счету в банке: '.$bank_owner['ekr'].' екр. <input type="button" value="Сменить счёт" OnClick="location.href=\'lord.php?quest=1&logoutbank=1\';">';
		$mllord[2][1] = '<!-- NOLINK -->Пожертвовать (1 екр. = 20 пожертвований): <form method=post action="lord.php?quest=1"><input type="text" name="howekr"> екр. <input type=submit value="Пожертвовать"></form>';
		$mllord[2][33333] = 'Я передумал.';
	}


	if (isset($_GET['qaction']) && strlen($_GET['qaction']) || isset($_GET['quest'])) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}
		if ($qa[0] == "d" && isset($mllord[$num])) {
			$mldiag = $mllord[$num];
		} else {
			unset($_GET['quest']);
			unset($_GET['qaction']);
		}
	}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="windows-1251">
    <title></title>
    <link type="text/css" rel="stylesheet" href="http://i.oldbk.com/i/main.css" />
    <link type="text/css" rel="stylesheet" href="http://i.oldbk.com/i/lord/lordstyles7.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="/i/globaljs.js"></script>
</head>
<body>
    <INPUT TYPE=button value="Вернуться" onClick="location.href='lord.php?exit=1';" style="z-index:3; position: absolute; right: 20px; top: 20px;">
    <div id="n_lord_castle">
	<?php
		if (isset($_POST['howekr']) && isset($_SESSION['bankid'])) {
			$bank_owner = mysql_fetch_assoc(mysql_query('SELECT * FROM bank WHERE id = '.$_SESSION['bankid']));
			$_POST['howekr'] = intval($_POST['howekr']);
			if ($_POST['howekr'] > 0 && $_POST['howekr'] <= $bank_owner['ekr']) {
				mysql_query('START TRANSACTION') or die();				

				// отнимаем деньги
				mysql_query('UPDATE bank SET ekr = ekr - '.($_POST['howekr']).' WHERE id = '.$_SESSION['bankid']);

				mysql_query('INSERT INTO `lord_donate` (`owner`,`points`) 
							VALUES(
								'.$user['id'].',
								'.($_POST['howekr']*20).'
							) 
							ON DUPLICATE KEY UPDATE
								`points` = `points` + '.($_POST['howekr']*20).'
				') or die();


				// пишем в дело
				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['target']=0;
				$rec['target_login']="Замок Лорда Разрушителя";
				$rec['type']=289; // заплатил
				$rec['item_name']="";
				$rec['item_count'] = 0;
				$rec['item_type'] = 0;
				$rec['item_cost'] = 0;
				$rec['item_dur'] = 0;
				$rec['item_maxdur'] = 0;
				$rec['item_proto'] = 0;
				$rec['item_arsenal'] = '';
				$rec['add_info'] = ($_POST['howekr']*20)." пожертвований.";
				$rec['sum_ekr'] = $_POST['howekr'];
	                                               
				add_to_new_delo($rec) or die();
	
				$bank_owner['ekr'] -= ($_POST['howekr']);
		
				$mldiag = array(
					0 => "Спасибо! Вы отдали ".$_POST['howekr']." екр. и сделали пожертвование в количестве ".($_POST['howekr']*20).".",
					11111 => "Пожалуйста!",
				);

				mysql_query('COMMIT') or die();
			} else {
				$mldiag = array(
					0 => "У вас нет столько екр.",
					33333 => "Я ошибся.",
				);	
			}
		}


		if (isset($_POST['howrep'])) {
			$_POST['howrep'] = intval($_POST['howrep'] / 10)*10;
			if ($_POST['howrep'] > 0 && $_POST['howrep'] <= $user['repmoney']) {
				mysql_query('START TRANSACTION') or die();				

				// отнимаем деньги
				mysql_query('UPDATE users SET repmoney = repmoney - '.($_POST['howrep']).' WHERE id = '.$user['id']);

				mysql_query('INSERT INTO `lord_donate` (`owner`,`points`) 
							VALUES(
								'.$user['id'].',
								'.($_POST['howrep'] / 10).'
							) 
							ON DUPLICATE KEY UPDATE
								`points` = `points` + '.($_POST['howrep'] / 10).'
				') or die();


				// пишем в дело
				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user[repmoney];
				$rec['owner_rep_posle']=($user[repmoney]-$_POST['howrep']);					
				$rec['target']=0;
				$rec['target_login']="Замок Лорда Разрушителя";
				$rec['type']=289; // заплатил
				$rec['item_name']="";
				$rec['item_count'] = 0;
				$rec['item_type'] = 0;
				$rec['item_cost'] = 0;
				$rec['item_dur'] = 0;
				$rec['item_maxdur'] = 0;
				$rec['item_proto'] = 0;
				$rec['item_arsenal'] = '';
				$rec['add_info'] = ($_POST['howrep'] / 10)." пожертвований.";
				$rec['sum_rep'] = $_POST['howrep'];
	                                               
				add_to_new_delo($rec) or die();
	
				$user['repmoney'] -= ($_POST['howrep']);
		
				$mldiag = array(
					0 => "Спасибо! Вы заплатили ".$_POST['howrep']." репутации и сделали пожертвование в количестве ".($_POST['howrep'] / 10).".",
					11111 => "Пожалуйста!",
				);

				mysql_query('COMMIT') or die();
			} else {
				$mldiag = array(
					0 => "У вас нет столько репутации.",
					33333 => "Я ошибся.",
				);	
			}
		}


		if (isset($_POST['howkr'])) {
			$_POST['howkr'] = intval($_POST['howkr']);
			if ($_POST['howkr'] > 0 && $_POST['howkr'] <= $user['money']) {
				mysql_query('START TRANSACTION') or die();				

				// отнимаем деньги
				mysql_query('UPDATE users SET money = money - '.($_POST['howkr']).' WHERE id = '.$user['id']);

				mysql_query('INSERT INTO `lord_donate` (`owner`,`points`) 
							VALUES(
								'.$user['id'].',
								'.$_POST['howkr'].'
							) 
							ON DUPLICATE KEY UPDATE
								`points` = `points` + '.$_POST['howkr'].'
				') or die();


				// пишем в дело
				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']-($_POST['howkr']);
				$rec['target']=0;
				$rec['target_login']="Замок Лорда Разрушителя";
				$rec['type']=289; // заплатил
				$rec['item_name']="";
				$rec['item_count'] = 0;
				$rec['item_type'] = 0;
				$rec['item_cost'] = 0;
				$rec['item_dur'] = 0;
				$rec['item_maxdur'] = 0;
				$rec['item_proto'] = 0;
				$rec['item_arsenal'] = '';
				$rec['add_info'] = $_POST['howkr']." пожертвований.";
				$rec['sum_kr'] = $_POST['howkr'];
	                                               
				add_to_new_delo($rec) or die();
	
				$user['money'] -= ($_POST['howkr']);
		
				$mldiag = array(
					0 => "Спасибо! Вы отдали ".$_POST['howkr']." кр и сделали пожертвование в количестве ".$_POST['howkr'].".",
					11111 => "Пожалуйста!",
				);

				mysql_query('COMMIT') or die();
			} else {
				$mldiag = array(
					0 => "У вас нет столько кр.",
					33333 => "Я ошибся.",
				);	
			}
		}


		$mlquest = "500/200";
		if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquestlord.php');
	?>
        <img src="http://i.oldbk.com/i/lord/t.gif" class="tlong" width="1350" height="510" alt="" usemap="#n_map"/>
        <ul>
            <li class="n_book-hover" style="display: none;"></li>
            <li class="n_gate-hover" style="display: none;"></li>
            <li class="n_chest-hover" style="display: none;"></li>
        </ul>
        <a href="?quest=1" class="n_hover"><div class="n_area-chest"></div></a>
    </div>
    <map name="n_map" id="n_map">
        <area class="n_area-book" shape="poly" href="#" coords="310,345,368,329,397,365,344,393" />
        <area class="n_area-gate" shape="poly" coords="605,348,605,208,631,173,679,153,725,169,753,208,751,351" />
    </map>
<div id="n_book_popup">
    <div id="n_book_wrapper">
        <div id="n_book_close"></div>
        <div id="n_book_body">
            <ul id="n_left" class="n_left">
            </ul>
            <ul id="n_right" class="n_right">
            </ul>
        </div>
        <div id="n_book_pagination">
            <ul id="n_pagination_body">
                <li id="n_p1" class="n_first"></li>
                <li id="n_p2" class="n_second"></li>
                <li id="n_p3" class="n_third"></li>
                <li id="n_p4" class="n_fourth"></li>
                <li id="n_p5" class="n_fifth"></li>
                <li id="n_p6" class="n_sixth"></li>
                <li id="n_p7" class="n_seventh"></li>
                <li id="n_p8" class="n_eighth"></li>
            </ul>
        </div>
	<div id="n_book_bottom" style="font-size:14px;">
	           Вы пожертвовали: <?php $s = mysql_fetch_assoc(mysql_query('SELECT * FROM lord_donate WHERE owner = '.$user['id'])); if (!$s['points']) $s['points'] = 0; echo $s['points']; ?>
        </div>
    </div>
</div>
<div id="n_page-dark"></div>
</body>
<script>
	var currentpage = 1;
	var allpages = 0;

	function ShowPersPages() {
		for (i = 1; i < 9; i++) {
			$("#n_p"+i).html("");
			$("#n_p"+i).unbind('click');
			$("#n_p"+i).removeClass('active');
		}
		if (allpages <= 8) {
			for (i = 1; i < allpages+1; i++) {
				if (i == currentpage) $("#n_p"+i).addClass("active");
				$("#n_p"+i).html(i);
				$("#n_p"+i).bind( "click", {msg:i},function(event) {ChangePersPage(event.data.msg-1)});
			}
		} else {
			if (currentpage <= 5) {
				for (i = 1; i < 7; i++) {
					if (i == currentpage) $("#n_p"+i).addClass("active");
					$("#n_p"+i).html(i);
					$("#n_p"+i).bind( "click", {msg:i},function(event) {ChangePersPage(event.data.msg-1)});
				}
				$("#n_p7").html("...");
				$("#n_p8").html(allpages);
				$("#n_p8").bind( "click", function() {
					ChangePersPage(allpages-1);
				});
			} else if (currentpage+4 >= allpages) {
				$("#n_p1").html("1");
				$("#n_p1").bind( "click", function() {
					ChangePersPage(0);
				});
				$("#n_p2").html("...");


				for (i = 8; i > 2; i--) {
					$("#n_p"+i).html(allpages+i-8);
					$("#n_p"+i).bind( "click", {msg:allpages+i-8},function(event) {ChangePersPage(event.data.msg-1)});
					if (allpages+i-8 == currentpage) $("#n_p"+i).addClass("active");
				}
			} else {
				$("#n_p1").html("1");
				$("#n_p1").bind( "click", function() {
					ChangePersPage(0);
				});
				$("#n_p2").html("...");

				$("#n_p3").html(currentpage-1);
				$("#n_p3").bind( "click", {msg:currentpage-1},function(event) {ChangePersPage(event.data.msg-1)});

				$("#n_p4").html(currentpage);
				$("#n_p4").bind( "click", {msg:currentpage},function(event) {ChangePersPage(event.data.msg-1)});
				$("#n_p4").addClass("active");

				$("#n_p5").html(currentpage+1);
				$("#n_p5").bind( "click", {msg:currentpage+1},function(event) {ChangePersPage(event.data.msg-1)});
				$("#n_p6").html(currentpage+2);
				$("#n_p6").bind( "click", {msg:currentpage+2},function(event) {ChangePersPage(event.data.msg-1)});
				
				$("#n_p7").html("...");
				$("#n_p8").html(allpages);
				$("#n_p8").bind( "click", function() {
					ChangePersPage(allpages-1);
				});

			}
		}		
	}

	function ChangePersPage(i) {
		currentpage = i+1;
		$.getJSON("lord.php?history=1&p="+i,  
			function(data){
				ShowPersInfo(data);
			}
		);
	}

	function ShowPersInfo(data) {
		allpages = data.all;
		$("#n_left").html("");
		$("#n_right").html("");

		k = 0;
		htmlleft = "";
		htmlright = "";
		$.each(data.users, function(i, item) {
			html = '<li><div class="n_number">'+i+'</div><div class="n_nickname"><img src="http://i.oldbk.com/i/align_'+item.align+'.gif"> ';
			if (item.klan != null && item.klan.length) html += '<img src="http://i.oldbk.com/i/klan/'+item.klan+'.gif">';
			html += item.login+'</div><div class="n_count">'+item.points+'</div></li>';
			if (k < 15) {
				htmlleft += html;
			} else {
				htmlright += html;
			}
			k++;
		});
		$("#n_left").html(htmlleft);
		$("#n_right").html(htmlright);

		ShowPersPages();
	}

    function nBookShow() {
	$.getJSON("lord.php?history=1",  
		function(data){
			ShowPersInfo(data);
		}
	);

	
        $('#n_page-dark').show();
        $('#n_book_popup').show();
        $('#mlquest').hide();
    }
    function nBookHide() {
        $('#n_page-dark').hide();
        $('#n_book_popup').hide();
    }
    $(document).ready(function () {
        var areaAction = function ($target, action) {
            var areaClass = $.trim($target.attr('class')).replace(/^n_area\-/i, '');
            $('.n_' + areaClass + '-hover')[action]('fast');
        };

        $('map').hover(
                function (e) {
                    areaAction($(e.target), 'fadeIn');
                },
                function (e) {
                    areaAction($(e.target), 'fadeOut');
                }
        );

        $(document.body).on('click', '.n_area-book', function(event){ event.preventDefault(); nBookShow();});
        $(document.body).on('click', '#n_book_popup #n_book_wrapper #n_book_close', function(){nBookHide();});
    });
</script>
</html>