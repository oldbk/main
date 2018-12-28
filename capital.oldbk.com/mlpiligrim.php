<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$q31 = false;
	$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31"') or die();
	if (mysql_num_rows($q) > 0) {
		$q31 = mysql_fetch_assoc($q);
	}
	if ($q31 !== false && $q31['val'] == 13) $q31 = false;

	$questexist = false;
	$qcomplete = false;
	$qlist = array(6,16,20);
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
			if ($q0 && !$q31) $questexist['q_id'] = 0;
		}
	}

	$mlpiligrim = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Загадочный клинок",
			"d3" => "Создание амулета",
			"d4" => "Странная находка",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "Удачно ты ко мне зашел. Как опытный воин, может, скажешь мне, что это. Да, я и сам вижу, что это кинжал, я его вчера в лесу нашел. Но вот на лезвии и на ручке какая-то надпись и рисунки странные. Не разобрать толком. Может, ты разузнаешь, что это и кому принадлежит?",
			"q6" => "Хорошо, я попробую выяснить",
			"d1" => "Нет, я не смогу это сделать",
		),
		3 => array(
			"0"  => "Духи сказали мне, что наступает благоприятное время для изготовления амулетов. Сходи к Болотной Ведьме и передай ей, что послезавтра новолуние , самое удачное время. Я обещал ей помочь собрать все необходимое, но у меня много дел, помоги ей ты, а я тебя отблагодарю.",
			"q16" => "Хорошо, я сделаю то, что ты просишь",
			"d1" => "Нет, я не смогу этого сделать",
		),
		4 => array(
			"0"  => "Недавно я осматривал окрестности и наткнулся на странную вещицу.  Взгляни? Может ты знаешь, что это или кому принадлежит? Похоже на какой-то топорик, но вроде бы и не он. Возможно, обронивший его - волнуется и ищет…",
			"d5" => "Если я правильно понял – ты хочешь что бы я узнал кому принадлежит твоя находка?",
		),
		5 => array(
			"0"  => "Совершенно верно! Да, это было бы просто чудесно. И можешь сразу вернуть её законному владельцу.",
			"q20" => "Хорошо, я постараюсь.",
			"d1" => "Нет, я не смогу этого сделать",
		),
		"thx" => array(
			"0" => "Спасибо, ты мне очень поможешь!",
			"11111" => "Пока.",
		),
	);

	$todel = false;
	if ($q31 !== FALSE) {
		if ($q31['val'] == 4) {
			$mlpiligrim[0]["d6"] = "По делу я к тебе сегодня. Не слыхал ты в своих странствиях про ларец древний, который был у самого Ричарда Львиное Сердце? Достался такой нашему Одинокому Рыцарю по наследству, да никто не знает какие ключи к нему нужны, хотя говорят, что ключа нужно сразу четыре.";
			$mlpiligrim[6] = array(
				0 => "Не тот ли это, в котором магический Знак Героя заперт? Если он, то знаю какие ключи нужны. Только, раз уж ты пришел, сделай доброе дело. Задумал я забор свой украсить, да так, чтоб случайные путники не заходили. Принеси мне 15 черепов, чтоб я их на колья забора насадил. А я тебе помогу с ключами.",
				"q31" => "Добыть черепа будет сложновато, но я попробую.",
			);
		}
		if ($q31['val'] == 5) {
			$mlpiligrim[0] = array(
				"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем? Или ты принес мне то, что я просил?",
				"d1" => "Да, я готов помочь. Говори, что надо сделать.",
				"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
				"q32" => "Да, вот тебе 15 черепов. Расскажи мне теперь про ключи.",
				"1"  => "Нет, я еще не все нашел, вернусь позже.",
			);
			$todel = QItemExistsCountID($user,3002500,15);
			if ($todel == false) unset($mlpiligrim[0]["q32"]);
			$mlpiligrim["next"] = array(
				0 => "Спасибо, теперь случайные путники будут меньше меня беспокоить. А для твоего ларца нужны  4 особенных ключа – золотой, серебрянный, бронзовый и изумрудно-зеленый. Если найдешь нужные ключи и провернешь все четыре в замке, то ларец откроется.",
				"11111" => "Спасибо за помощь. Удачи!",
			);
		}
	}


	if (isset($_GET['qaction']) && strlen($_GET['qaction']) && $questexist === FALSE || isset($_GET['quest']) && $questexist === FALSE) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}
		if ($qa[0] == "d" && isset($mlpiligrim[$num])) {
			$mldiag = $mlpiligrim[$num];

			// тут квесты режем исполненные
			if (isset($qcomplete[6]) && isset($mldiag["d2"])) unset($mldiag['d2']);
			if (isset($qcomplete[16]) && isset($mldiag["d3"])) unset($mldiag['d3']);
			if (isset($qcomplete[20]) && isset($mldiag["d4"])) unset($mldiag['d4']);
		} elseif ($qa[0] == "q") {
			if ($num == 6 && !isset($qcomplete[6])) {
				// квест - загадочный клинок 
				mysql_query('START TRANSACTION') or QuestDie();				
				SetNewQuest($user,6) or QuestDie();
				PutQItem($user,3003019,"Пилигрим",0,$todel) or QuestDie();
                                mysql_query('COMMIT') or QuestDie();
				addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Загадочный кинжал</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);

				$mldiag = $mlpiligrim["thx"];
			}
			if ($num == 16 && !isset($qcomplete[16])) {
				// квест - Создание амулетов 
				mysql_query('START TRANSACTION') or QuestDie();				
				SetNewQuest($user,16) or QuestDie();
                                mysql_query('COMMIT') or QuestDie();

				$mldiag = $mlpiligrim["thx"];
			}
			if ($num == 20 && !isset($qcomplete[20])) {
				// квест - странная находка
				mysql_query('START TRANSACTION') or QuestDie();				
				PutQItem($user,3003073,"Пилигрим") or QuestDie();
				addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Ледоруб</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);
				SetNewQuest($user,20) or QuestDie();
                                mysql_query('COMMIT') or QuestDie();

				$mldiag = $mlpiligrim["thx"];
			}
			if ($num == 31 && $q31 !== false && $q31['val'] == 4) {
				mysql_query('UPDATE oldbk.map_var SET val = 5 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
				UnsetQA();
			}
			if ($num == 32 && $q31 !== false && $q31['val'] == 5 && $todel !== false) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItemTo($user,"Пилигрим",$todel);
				mysql_query('UPDATE oldbk.map_var SET val = 6 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
                                mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlpiligrim["next"];
			}
		} else {
			UnsetQA();
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlpiligrim.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlpiligrim_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 285px; top: 70px;" src="http://i.oldbk.com/i/map/mlpiligrim_pers1.png" alt="Пилигрим" title="Пилигрим" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlpiligrim_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlpiligrim_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
	}

	$mlquest = "0/0";
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