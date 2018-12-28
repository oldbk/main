<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$qcomplete = false;
	$qlist = array(13,30);
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

	$mlwood = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Шляпа для лесоруба",
			"d3" => "Дикий зверь",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "О, вот тебя я еще не спрашивал, не  попадалась ли тебе моя шляпа? Где-то оставил, а где не помню. Я к ней так привык, это была моя любимая шляпа. Полностью из кожи, три орлиных пера, внутри была подшита, отборной, дорогой тканью. ",
			"q13" => "Нет, не видал нигде, но я могу помочь сшить новую",
			"d1" => "Нет, нигде не видел и не знаю что делать",
		),
		3 => array(
			"0" => "Это просто невыносимо! Какой-то дикий зверь каждую ночь воет и скулит в лесу, неподалеку отсюда, будто плачет. Этот душераздирающий вой уже целую неделю не дает мне выспаться.",
			"d4"=> "Ты хочешь, что бы я выследил этого зверя для тебя?",
		),
		4 => array(
			"0"  => "Знаешь, это было бы просто великолепно. Я бы и сам мог это сделать, но все же моя стихия - древесина, а не дичь.",
			"q30"=> "Я отправлюсь немедленно и выслежу это животное. Пока!",
			"d1" => "Что-то нет у меня желания бродить по лесам…",
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
		if ($qa[0] == "d" && isset($mlwood[$num])) {
			$mldiag = $mlwood[$num];

			// тут квесты режем исполненные
			if (isset($qcomplete[13]) && isset($mldiag["d2"])) unset($mldiag['d2']);
			if (isset($qcomplete[30]) && isset($mldiag["d3"])) unset($mldiag['d3']);
		} elseif ($qa[0] == "q") {
			if ($num == 13 && !isset($qcomplete[13])) {
				// квест - разбойничья переправа
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,13,"0/0") or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlwood["thx"];
			}
			if ($num == 30 && !isset($qcomplete[30])) {
				// квест - дикий зверь
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,30,"0/0/0") or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlwood["thx"];
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlwood.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlwood_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 400px; top: 150px;" src="http://i.oldbk.com/i/map/mlwood_pers1.png" alt="Лесоруб" title="Лесоруб" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlwood_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlwood_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
	}

	$mlquest = "100/100";
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