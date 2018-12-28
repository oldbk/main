<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$qcomplete = false;
	$qlist = array(29);
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


	$mlwitch = array(
		0 => array(
			"0"  => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Приворотное зелье.",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "Тут такая история – приходила ко мне давеча девица, вроде бы даже из благородных, выла, что твой волк! Говорит, мол, люблю его, окаянного, что аж зубы по ночам сводит. Сказать по правде – боялась она меня до жути, так спешно уходила, что забыла свой плащ, но оно и не удивительно…",
			"d3" => "Я безумно счастлив за этого «окаянного», да и плащ вроде бы неплохой, коли не вернется твоя гостья за ним, так тебе в хозяйстве пригодится. Ну, так, а чем я могу помочь?",
		),
		3 => array(
			"0"  => "Ах, ну да. В общем, попросила меня сварить ей приворотное зелье. Ну мне-то не сложно, я-то всегда помогу, я же не маг какой-нибудь… Да только вот в чем проблема - думала сварить, гляжу, а книжки-то с рецептами и нету. ",
			"d4" => "Боюсь, я нужного рецепта точно не знаю.",
		),
		4 => array(
			"0"  => "Конечно же, не знаешь. Однако возможно ты смог бы разыскать мою пропажу? Собирала я вчера лягушек на болоте, должно быть там книжку и обронила.",
			"q29"=> "Хорошо, схожу,поищу твою книжку.",
			"d1" => "Делать мне больше нечего как по болотам рыскать.",
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
		if ($qa[0] == "d" && isset($mlwitch[$num])) {
			$mldiag = $mlwitch[$num];

			// тут квесты режем исполненные
			if (isset($qcomplete[29]) && isset($mldiag["d2"])) unset($mldiag['d2']);
		} elseif ($qa[0] == "q") {
			if ($num == 29 && !isset($qcomplete[29])) {
				// квест - приворотное зелье
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,29,"0/0/0") or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlwitch["thx"];
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlwitch.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlwitch_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 255px; top: 55px;" src="http://i.oldbk.com/i/map/mlwitch_pers1.png" alt="Ведьма" title="Ведьма" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlwitch_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlwitch_pers1.png'"/></a>
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