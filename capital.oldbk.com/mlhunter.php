<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$qcomplete = false;
	$qlist = array(1,7,10);
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


	$mlhunter = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Лечебное снадобье",
			"d3" => "Новые стрелы",
			"d4" => "Оборотень",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "Недавно на охоте меня здорово подрал медведь, и теперь, как видишь, я с трудом встаю с кровати. Болотная ведьма может сварить снадобье, которое быстро поднимет меня на ноги, но сам я сходить за ним не могу. Принеси мне снадобье от Ведьмы и я постараюсь отблагодарить тебя за помощь.",
			"q1" => "Хорошо, я принесу тебе снадобье",
			"d1" => "Нет, я не смогу это сделать",
		),
		3 => array(
			"0"  => "Есть для тебя одно дело важное! Мне нужно много стрел заготовить, а все нужное для этого уже кончается.  Древки кончились, перья орлиные кончились. И яда для стрел уже на донышке. Если поможешь, я в долгу не останусь, ты же знаешь. Только перьев нужно не меньше 10ти. Не забудь. ",
			"q7" => "Хорошо, я принесу тебе все, что нужно",
			"d1" => "Нет, я не смогу это сделать",
		),	
		4 => array(
			"0"  => "Беда нагрянула на наш лес. Уже неделю нахожу в лесу растерзанных зверей, огромные следы когтей, жуткий вой стал раздаваться в лесу по ночам. Порой мне кажется, что он все ближе и ближе! В нашем лесу завелось что-то страшное. Я никогда не слыхал о подобном. Если сможешь разузнать что это, и, как его уничтожить – спасешь наш лес и всех его жителей. ",
			"q10" => "Хорошо, я постараюсь вам помочь",
			"d1" => "Нет, я не смогу это сделать",
		),	
		"thx7" => array(
			"0" => "Ходить тебе нужно будет много, держи на дорожку окорок -  сам коптил! Если проголодаешься – будет, чем подкрепиться.",
			"11111" => "Спасибо за заботу.",
		),
		"thx10" => array(
			"0" => "Попробуй расспросить Пилигрима. Он много странствовал и многое повидал. Может, он что подскажет.",
			"11111" => "Спасибо, так и поступлю.",
		),
		"thx" => array(
			"0" => "Спасибо, ты мне очень поможешь!",
			"11111" => "Пока.",
		),

	);

	if (isset($_GET['qaction']) && strlen($_GET['qaction']) && $questexist === FALSE || isset($_GET['quest']) && $questexist === FALSE) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}
		if ($qa[0] == "d" && isset($mlhunter[$num])) {
			$mldiag = $mlhunter[$num];

			// тут квесты режем исполненные
			if (isset($qcomplete[1]) && isset($mldiag["d2"])) unset($mldiag['d2']);
			if (isset($qcomplete[7]) && isset($mldiag["d3"])) unset($mldiag['d3']);
			if (isset($qcomplete[10]) && isset($mldiag["d4"])) unset($mldiag['d4']);

		} elseif ($qa[0] == "q") {
			if ($num == 1 && !isset($qcomplete[1])) {
				// квест - лечебное снадобье
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,1) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlhunter["thx"];
			}
			if ($num == 7 && !isset($qcomplete[7])) {
				// квест - новые стрелы
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,7,"0") or QuestDie();
				PutQItem($user,3003022,"Охотник") or QuestDie();
				addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Копченный окорок</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);
                                mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlhunter["thx7"];
			}
			if ($num == 10 && !isset($qcomplete[10])) {
				// квест - лечебное снадобье
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,10) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlhunter["thx10"];
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlhunter.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlhunter_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 180px; top: 125px;" src="http://i.oldbk.com/i/map/mlhunter_pers1.png" alt="Охотник" title="Охотник" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlhunter_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlhunter_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
	}

	$mlquest = "20/5";
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