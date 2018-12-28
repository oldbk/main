<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$qe = false;
	$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32"') or die();
	if (mysql_num_rows($q) > 0) {
		$qe = mysql_fetch_assoc($q);
	}
	

	if ($qe['val'] >= 8) unsetQA();

	$mlstranger = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? А может быть твоя душа полна любви,  а сердце полно отваги бесстрашного Рыцаря? Если ты свободен во времени и поступках и ищешь приключений, не будешь ли ты добр, помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0" => "Спасибо тебе за желание помочь, но сначала выслушай мою грустную историю. Как ты видишь, я чужой в ваших краях и уже много лет скитаюсь по свету. Когда-то в своей стране я был лучшим воином и не было мне равных. Люди называли меня \"Легендарным воином\" и не было человека, который не знал моего имени. Сначала я помогал многим, но потом гордыня овладела мной и повела по скользкому пути... И вот однажды, за помощью ко мне обратилась ни кто иная, как главная Ведьма нашего королевства, дочь которой похитил злодей... Но я был настолько глуп и горд, что отказал ей в помощи, считая это делом не достойным меня. И в отместку она наложила на меня проклятие...",
			"d2" => "Очень интересная и поучительная история, продолжай... Что за проклятие?",
		),        
		2 => array(
			"0" => "Проклятие сделало меня вечным скитальцем. Границы моего королевства закрыты магическим барьером, через который я не могу перешагнуть. Чтоб побороть проклятие я должен умерить свою гордыню и доказать себе и всему миру, что не бывает в мире лишь одного Легендарного Воина, и что я не настолько велик, как мне казалось. Теперь я вынужден скитаться по всему свету в поисках воина, который сможет повторить мои подвиги. И лишь найдя такого, равного мне или превосходящего меня, я смогу вернуться домой. Не согласишься ли ты мне помочь? Может быть ты хочешь попробовать свои силы и я скажу тебе что надо делать? Если ты сможешь выполнить все задания, то ты станешь Легендарным Воином, а мое проклятие будет снято.",
			"d3" => "Это интересное предложение. Пожалуй я готов попробовать свои силы.  Говори, что надо сделать?",
		),
		3 => array(
			"0" => "Тебя ждет шесть заданий, и каждый раз ты должен будешь выполнять их и отдавать мне немного своей репутации. Эта репутация станет доказательством того, что я нашел тебя, Легендарного Воина, и имею право вернуться домой. Только сосчитав принесенную мной репутацию, Ведьма поверит мне и снимет проклятие.",
			"d4" => "Я готов, говори задание!",
		),
		4 => array(
			"0" => "Итак, первое задание - если ты предпочитаешь сам решать когда и с кем воевать, выиграй 50 кровавых боев в Загороде. Но если ты привык ходить с командой, то выиграй 10 турниров в Руинах Старого Замка.  А потом приходи ко мне за следующим заданием.",
			"q1" => "Я выиграю 50 загородных кровавых боев (взять задание)",
			"q2" => "Я выиграю 10 турниров в Руинах. (взять задание)",
			"33333" => "Нет, я все-таки не готов на эти испытания, прощай!",
		),
		"thx" => array(
			"0" => "Спасибо, ты мне очень поможешь!",
			"11111" => "Пока.",
		),
	);

	$zg = false;
	if (strpos($user['medals'],'k202;') !== false) {
		$zg = true;
	} else {
		$mlstranger[1] = array(
			0 => "Спасибо за желание мне помочь, но для решения моей проблемы необходим 	сильный и отважный воин. В вашем Мире такие воины носят \"знак Героя\". Когда ты заслужишь этот знак, я буду рад принять твою помощь.",
			33333 => "Я не менее силен и отважен, чем те герои, о которых ты говоришь! Но раз таково твое условие, то я вернусь, когда заслужу этот знак. Прощай!",
		);
	}


	if (isset($_GET['qaction']) && strlen($_GET['qaction']) && $qe === FALSE || isset($_GET['quest']) && $qe === FALSE) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}
		if ($qa[0] == "d" && isset($mlstranger[$num])) {
			$mldiag = $mlstranger[$num];
		} elseif ($qa[0] == "q") {
			if ($num == 1 && $zg) {
				// Я выиграю 50 загородных боев
				mysql_query('START TRANSACTION') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32","1")') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s1","0")') or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlstranger["thx"];
			}

			if ($num == 2 && $zg) {
				// Я выиграю 10 турниров в Руинах
				mysql_query('START TRANSACTION') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32","2")') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s2","0")') or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = $mlstranger["thx"];
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
<body id="body" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor="#e2e0e1" onResize="return; ImgFix(this);">
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
<TR>
	<TD align=center></TD>
	<TD align=right>
		<div class="btn-control">
            <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlstranger.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table border = 0 width=1><tr><td valign=top>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlstranger_bg.jpg" id="mainbg">
<?php
if (isset($qe) && $qe !== false && $qe['val'] >= 8) {
} else {
?>
<a href="?quest=1"><img style="z-index:3; position: absolute; left: 470px; top: 125px;" src="http://i.oldbk.com/i/map/mlstranger_pers1.png" alt="Чужестранец" title="Чужестранец" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlstranger_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlstranger_pers1.png'"/></a>
<?php 
}
?>
</div>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($qe !== FALSE) {
		// есть квест - тут квестовый обработчик
		require_once('./mapquests/32.php');
	} else {
		// нету квеста, разговариваем
	}

	$mlquest = "500/100";
	if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
}	
?>
</td></tr></table>
 
</div>
</TD>
</TR>
</TABLE>

<?php
	require_once('mldown.php');
?>