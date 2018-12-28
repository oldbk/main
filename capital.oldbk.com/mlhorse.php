<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$qcomplete = false;
	$qlist = array(19,22);
	$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
	if (mysql_num_rows($q) > 0) {
		$questexist = mysql_fetch_assoc($q) or die();
	} else {
		$q = mysql_query('SELECT * FROM map_var WHERE var = "cango" AND owner = '.$user['id'].' AND val > '.time()) or die();
		if (mysql_num_rows($q) > 0) {
			$questexist['q_id'] = 0;
		}

		$q = mysql_query('SELECT * FROM map_qvar WHERE var = "qcomplete" AND owner = '.$user['id']) or die();
		if (mysql_num_rows($q) > 0) {
			$qcomplete = mysql_fetch_assoc($q);
			$qt = explode("/",$qcomplete['val']);
			$qcomplete = array();
			while(list($k,$v) = each($qt)) {
				$qcomplete[$v] = 1;
			}
			$q0 = true;
			reset($qlist);
			while(list($k,$v) = each($qlist)) {
				if (!isset($qcomplete[$v])) {
					$q0 = false;
					break;
				}
			}
			if ($q0) $questexist['q_id'] = 0;
		}
	}


	$mlhorse = array(
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Помощь по конюшне",
			"d3" => "Больная лошадь",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "Хорошо, что ты заглянул. Мне как-раз помощь требуется. Я решил навести порядок в конюшнях. У многих лошадей подковы сбиты, сена запасы к концу подходят, на телегах почтовых кое-где спицы переломаны. Если время у тебя есть помочь – я в долгу не останусь.",
			"q19" => "Хорошо, я помогу, что надо делать?",
			"11111" => "Нет, я не смогу это сделать, пока.",
		),
		3 => array(
			"0"  => "Это просто ужасно! Моя любимая лошадь «Вишенка»  - заболела… Уже третий день она лежит в стойле не в силах встать на ноги. Умоляю – помоги мне найти лекарство! Я слышал у Священника в деревне есть походная аптечка…",
			"q22" => "Хорошо, сделаю что смогу. Пока!",
			"11111" => "Нет, мне это не нтересно, пока.",
		),
		"thx19" => array(
			"0" => "Принеси мне десяток подков, пару десятков спиц деревянных для колес, ну и сена побольше не помешало бы.",
			"11111" => "Хорошо, я принесу тебе все что нужно.",
		),
	);

	if ((isset($_GET['qaction']) && strlen($_GET['qaction']) && $questexist === FALSE) && $user['room'] <= ($maprel+$maprelall+8)) {
		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		} else {
			$num = $_GET['qaction'];
		}
		if ($num < 30000) {
			if ($qa[0] == "d" && isset($mlhorse[$num])) {
				$mldiag = $mlhorse[$num];
	
				// тут квесты режем исполненные
				if (isset($qcomplete[19]) && isset($mldiag["d2"])) unset($mldiag['d2']);
				if (isset($qcomplete[22]) && isset($mldiag["d3"])) unset($mldiag['d3']);
			} elseif ($qa[0] == "q") {
				if ($num == 19 && !isset($qcomplete[19])) {
					// квест - Помощь по конюшне 
					mysql_query('START TRANSACTION') or QuestDie();				
					SetNewQuest($user,19,"0/0/0") or QuestDie();
	                                mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlhorse["thx19"];
				}

				if ($num == 22 && !isset($qcomplete[22])) {
					// квест - Больная лошадь
					mysql_query('START TRANSACTION') or QuestDie();				
					SetNewQuest($user,22) or QuestDie();
	                                mysql_query('COMMIT') or QuestDie();
					unsetQA();
				}

			} else {
				UnsetQA();
			}	
		}
	}
?>


<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>
var loc = parent.location.href.toString();
if (loc.indexOf("/map.php") != -1) {
	parent.location.href = "<?php echo $self; ?>";
}
</script>
<style> 
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }
</style>
<style type="text/css"> 
img, div { behavior: url(/i/city/ie/iepngfix.htc) }
</style>
</HEAD>
<body id="body" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor="#d7d7d7" onResize="return; ImgFix(this);">
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
<TR>
	<TD align=center></TD>
	<TD align=right>
		<div class="btn-control">
            <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlhorse.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlhorse_bg.png" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 300px; top: 100px;" src="http://i.oldbk.com/i/map/mlhorse_pers1.png" alt="Конюх" title="Конюх" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlhorse_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlhorse_pers1.png'"/></a>
<?php

$q_goodhorse = array(19,22,24);

function HorseSD($user,$maprel,$maprelall,$questexist) {
	$map_horseprice = 10;
	$addtxt = " Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?";

	$mldiag = array();

	if (isset($_GET['qaction'])) {
		if ($_GET['qaction'] == 30001 && $user['horse']) {
			$q = mysql_query('START TRANSACTION') or QuestDie();
			$addmoney = $map_horseprice*0.5;
			mysql_query('UPDATE oldbk.`users` SET money = money + '.$addmoney.', podarokAD = 0 WHERE id = '.$user['id']) or QuestDie();
			$rec = array();

    			$rec['owner']=$user[id]; 
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money']+$addmoney;
			$rec['target_login']="Конюх";
			$rec['sum_kr'] = $addmoney;
			$rec['type'] = 258;
			if(add_to_new_delo($rec) === FALSE) QuestDie();
		
			$q = mysql_query('COMMIT') or QuestDie();

			$mldiag = array(
				0 => "Вы продали лошадь и получили ".$addmoney." кр.",
				11111 => "Спасибо, пока!",
			);				
		} elseif ($_GET['qaction'] == 30002 && !$user['horse']) {
			$mldiag = array(
				0 => "Если твою лошадь кто-то украдёт, хочешь ли ты узнать его имя? Мы можем тебе в этом помочь.",
				30003 => "Да, я хочу знать кто посмеет украсть мою лошадь.",
				30004 => "Нет, мне все равно, кто это сделает.",
			);
		} elseif (($_GET['qaction'] == 30003 || $_GET['qaction'] == 30004) && !$user['horse']) {
			$q = mysql_query('START TRANSACTION') or QuestDie();
			$getmoney = $map_horseprice;

			if ($user['money'] < $getmoney) {
				Redirect('mlhorse.php');
			}

	
			$alarmhorse = $_GET['qaction'] == 30003 ? 1 : 0;

			mysql_query('UPDATE oldbk.`users` SET money = money - '.$getmoney.', podarokAD = 1, injury_possible  = '.$alarmhorse.' WHERE id = '.$user['id']) or QuestDie();

			$rec = array();
    			$rec['owner']=$user[id]; 
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money']-$getmoney;
			$rec['sum_kr'] = $getmoney;
			$rec['target_login']="Конюх";
			$rec['type'] = 257;
			if(add_to_new_delo($rec) === FALSE) QuestDie();

			$q = mysql_query('COMMIT') or QuestDie();

			$mldiag = array(
				0 => "Вы приобрели лошадь и заплатили: ".$getmoney." кр.",
				11111 => "Спасибо, пока!",
			);
		} elseif ($_GET['qaction'] == 30000) {
			$mldiag = array(
				0 => "Приветствую тебя, путник! Чем я могу тебе помочь? В нашей конюшне можно купить лошадь за 10 кр. или продать ее за 5 кр. ",
			);
			if ($user['horse']) {
				$mldiag[30001] = "Я хочу продать лошадь за ".($map_horseprice*0.5)." кр.";
			} else {
				if ($user['money'] >= 10) $mldiag[30002] = "Я хочу купить лошадь за ".$map_horseprice." кр.";
			}
			if ($user['room'] <= ($maprel+$maprelall+8) && $questexist === FALSE) {
				$mldiag[0] .= $addtxt;
				$mldiag["d1"] = "Да, я готов помочь. Говори, что надо сделать.";
			}
			$mldiag[11111] = "Нет, мне ничего не надо, пока!";
		} else {
			unsetQA();
		}
	} else {
		$mldiag = array(
			0 => "Приветствую тебя, путник! Чем я могу тебе помочь? В нашей конюшне можно купить лошадь за 10 кр. или продать ее за 5 кр.",
		);
		if ($user['horse']) {
			$mldiag[30001] = "Я хочу продать лошадь за ".($map_horseprice*0.5)." кр.";
		} else {
			if ($user['money'] >= 10) $mldiag[30002] = "Я хочу купить лошадь за ".$map_horseprice." кр.";
		}

		if ($user['room'] <= ($maprel+$maprelall+8) && $questexist === FALSE) {
			$mldiag[0] .= $addtxt;
			$mldiag["d1"] = "Да, я готов помочь. Говори, что надо сделать.";
		}
		$mldiag[11111] = "Нет, мне ничего не надо, пока!";
	}
	return $mldiag;
}



if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE && in_array($questexist['q_id'],$q_goodhorse) !== FALSE) {
		// есть квест - подключаем квестовый обработчик, квестовые обработчики должны сами показывать диалоги связанные с покупкой-продажей лошади
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		if (isset($_GET['qaction']) && $_GET['qaction'] >= 30000) {
			$mldiag = HorseSD($user,$maprel,$maprelall,$questexist);
		} else { 
			require_once('./mapquests/'.$questexist['q_id'].'.php');
		}
	} else {
		// нету квеста, разговариваем
		if (isset($_GET['quest']) || (isset($_GET['qaction']) && $_GET['qaction'] >= 30000)) {
			$mldiag = HorseSD($user,$maprel,$maprelall,$questexist);
		}
	}

	$mlquest = "5/100";
	if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
}	
?>
</div>


</td></tr></table>
 
</div>
</TD>
</TR>
</TABLE>

<?php
	require_once('mldown.php');
?>