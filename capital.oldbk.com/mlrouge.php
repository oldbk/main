<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$qcomplete = false;
	$qlist = array(3,24);
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


	$mlrouge = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Разбойничья переправа",
			"d3" => "Подготовка к зимовке",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "Есть у нас проблема.  Нам бы переправить свои вещи на ту сторону реки, а лодочник ни в какую. Говорит, что мы что-то там у него украли. Ну, ты ж нам веришь , что мы не способны на такое? Если б ты уговорил лодочника переправить нас на ту сторону, а то с нами он говорить не хочет. А мы в долгу не останемся.",
			"q3" => "Хорошо, я попробую договориться",
			"d1" => "Нет, я не смогу это сделать",
		),
		3 => array(
			"0"  => "Тут такое дело – приближаются холода, а у нас с… товарищами нет теплой одежды. Сам понимаешь, в деревне Разбойников не сильно жалуют. Я вот и подумал – нас они знают, а тебя нет, может ты что-нибудь придумаешь и достанешь нам штук пять полушубков, да потеплее?",
			"d4" => "Хорошо, я попробую раздобывать для вас одежду.",
			"d1" => "Нет, боюсь мне это неинтересно…",
		),
		4 => array(
			"0"  => "Постой, постой… Ещё кое что. Сам понимаешь – хижина у нас – не царские палаты. Спим буквально на земле и камнях. Зимой-то совсем плохо станет, хоть в одежде теплой, хоть без нее.  Не принесешь нам ещё чего-нибудь, чтоб на пол постелить? Мы в долгу не останемся.",
			"q24" => "Ладно-ладно, понял я. Вам холодно и вы страдаете. Сделаю что смогу.",
			"d1" => "Нет, боюсь мне это неинтересно…",
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
		if ($qa[0] == "d" && isset($mlrouge[$num])) {
			$mldiag = $mlrouge[$num];

			// тут квесты режем исполненные
			if (isset($qcomplete[3]) && isset($mldiag["d2"])) unset($mldiag['d2']);
			if (isset($qcomplete[24]) && isset($mldiag["d3"])) unset($mldiag['d3']);
		} elseif ($qa[0] == "q") {
			if ($num == 3 && !isset($qcomplete[3])) {
				// квест - разбойничья переправа
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,3) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlrouge["thx"];
			}

			if ($num == 24 && !isset($qcomplete[24])) {
				// квест - Подготовка к зимовке
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,24,"0/0") or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlrouge["thx"];
			}
		} else {
			UnsetQA();
		}
	}


	// 26 квест, сразу если победили
	if ($questexist !== FALSE && !isset($_GET['quest']) && !isset($_GET['qaction']) && $questexist['q_id'] == 26 && $questexist['step'] == 1) {
		$_GET['quest'] = 1;
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
            <input class="button-mid btn" type="button" name="Обновить" style="cursor: pointer;" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlrouge.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlrouge_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 170px; top: 125px;" src="http://i.oldbk.com/i/map/mlrouge_pers1.png" alt="Разбойник" title="Разбойник" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlrouge_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlrouge_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
	}

	$mlquest = "320/100";
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