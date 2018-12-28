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
	$qlist1 = array(2,23);
	$qlist2 = array(26,27,28);
	$qlist3 = array(18);
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
			
			if (isset($_GET['qaction'])) {
				$qa = $_GET['qaction'];
				if (!is_numeric($qa[0])) {
					$num = intval(substr($qa,1));
				} else {
					$num = intval($qa);
				}
	
				if ($num < 1000) {
					$_GET['quest'] = 1;
				} elseif ($num > 1000 && $num < 2000) {
					$_GET['quest'] = 2;
				} elseif ($num > 2000 && $num < 3000) {
					$_GET['quest'] = 3;
				}
			}

			$q0 = true;
			$qname = "qlist".$_GET['quest'];
			if (isset($$qname) && is_array($$qname)) {
				reset($$qname);
				while(list($k,$v) = each($$qname)) {
					if (!isset($qcomplete[$v])) {
						$q0 = false;
						break;
					}
				}
			}
			if ($q0 && !$q31) $questexist['q_id'] = 0;
		}
	}


	$mlvillage1 = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2" => "Бабушкин пирог",
			"d3" => "Сейф для трактирщика",
			"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2 => array(
			"0"  => "Скоро у меня день рождения, и я хочу угостить друзей пирогом по рецепту моей бабушки. Для этого мне не хватает протвиня, фруктового варенья и особенной травки «утешение желудка». Если ты принесешь мне все это, я не останусь неблагодарным.",
			"q2" => "Хорошо, я принесу тебе все, что нужно.",
			"d1" => "Нет, я не смогу это сделать",
		),
		3 => array(
			"0" => "В общем-то, я сам не знаю, как так вышло, но дела мои последнее время идут в гору! Клиентов все больше и как следствие – прибыли растут. Да так растут, что под подушкой в носке уже весь свой капитал не спрячешь… Нужен мне сейф, да попрочней. Организуешь? Я в долгу не останусь.",
			"d4"=> "Сейф? Было бы тебе от кого прятаться… Охранники у тебя вино берут, разбойники – провизию. Ты вообще везде поспеваешь, не так ли?",
			"d1" => "Нет, я не смогу это сделать",			
		),
		4 => array(
			"0" => "Тсссссс… Ты покричи ещё об этом  - не все услышали. Берешься за задание или нет?",
			"q23"=> "Ладно, будет тебе сейф. Пока!",
			"d1" => "Пожалуй, мой ответ - нет. ",			
		),
		"t1" => array(
			"0" => "Спасибо, ты мне очень поможешь!",
			"11111" => "Пока.",
		),
	);

	$mlvillage2 = array(
		0 => array(
			"0"  => "Приветствую тебя, путник, в нашей скромной обители. Пришел ли ты просить Господа о благодати и отпущении грехов или помолиться со мной о душах других грешников? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d1001" => "Да, я готов помочь. Говори, что надо сделать.",
			"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		1001 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d1002" => "Украденная икона",
			"d1003" => "Людоед",
			"d1004" => "Диковинные четки",
			"11111"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		1002 => array(
			"0"  => "Страшное преступление было совершено против Б-га и против каждого из нас. Икона, что хранилась в нашей церкви множество лет, оберегавшая всех жителей деревни – была украдена! Её необходимо, слышишь, необходимо вернуть!",
			"q1026" => "Такое преступление нельзя оставить безнаказанным!",
			"d1001" => "Честно говоря, мне все равно…",
		),
		1003 => array(
			"0"  => "Странные вещи творятся в последнее время, сын мой. Люди уходят в лес или к реке и не возвращаются, а потом кто-нибудь находит обглоданные кости. Слухи разные ходят... Разузнай, что сможешь. По части слухов, сам знаешь, Трактирщику нет равных. Если бы ты согласился, очень бы помог всей нашей деревне.",
			"q1027" => "Хорошо, я постараюсь разузнать, что это за напасть.",
			"d1001" => "Нет, это слишком опасно, я не рискну.",
		),
		1004 => array(
			"0"  => "Сын мой, до меня дошли слухи, что в скором времени нашу скромную деревеньку посетит паломник из заморских краев. Говорят, вот уже год прошел, с тех пор как он начал свое путешествие к Святой Земле…",
			"d1005" => "Чем я могу помочь, святой отец? Надобно его сопроводить? Охранять или может быть убить?",
			"d1001" => "Мне это мало интересно.",
		),
		1005 => array(
			"0"  => "Что ты! Что ты! Господь с тобой! Убивать никого не надо. Я долго думал, какой дар можно было бы преподнести ему, как знак доброй воли и нашей поддержки в его нелегком путешествии ради Господа нашего.",
			"d1006" => "Очевидно, вы ничего не придумали, святой отец, коль скоро просите о помощи?",
		),
		1006 => array(
			"0"  => "Отчего же? Напротив… Было мне озарение свыше – подарить ему надобно четки ручной работы, дабы не покидала его молитва и память о нашей деревне. Видел я похожие у пилигрима. Но разбойники, говорят, хвалились похожей добычей. Даже не знаю, где лучше искать, но может ты сумеешь раздобыть такие?",
			"q1028" => "Отчего же не суметь… Скоро вернусь с четками! Пока! ",
			"d1001" => "Нет, не думаю что это задание по мне.",
		),
		"t1026" => array(
			"0" => "Спасибо, ты мне очень поможешь!",
			"11111" => "Пока.",
		),
	);

	$q31stepcomplete = false;
	if ($q31 !== FALSE) {
		if ($q31['val'] == 10 && QItemExists($user,3003092)) {
			$mlvillage2[0]["d1007"] = "Я пришел к тебе за помощью, Святой Отец. Не видал ли ты в манускриптах церковной библиотеки что-нибудь о великих подвигах, которые надо совершить, чтоб открыть древний ларец Ричарда Львиное Сердце?";
			$mlvillage2[1007] = array(
				0 => "Не тот ли это  ларец, где Знак Героя хранится?  Читал про него. Да только последний рыцарь, сумевший его открыть, и был сам Ричард Львиное Сердце.  Победил он для этого 15 заклятых врагов, украсив их черепами колья своего забора, около 100 раз прошел весь героический  лабиринт, наполненный духами и монстрами, раздал бедным людям свою казну, в которой были монеты, слитки золота и бесценные чеки, победил в 600 хаотических битвах, участвовал в 30 боях против Исчадия Хаоса и бескорыстно отдал кровью заработанную многотысячную репутацию своему соотечественнику. Вот, что сделал Ричард Львиное Сердце.",
				"d1008" => "Смотри-ка... Да часть из этого я уже сделал. Осталось только выиграть 600 хаотических битв, нанести урон в 30 битвах против Исчадия Хаоса и разобраться с репутацией.  Это мне по силам! А что там было про репутацию, Святой Отец?",
			);
			$mlvillage2[1008] = array(
				0 => "Не помню точно. А ты и вправду решил открыть этот ларец?  Тогда поступим так. Иди, воюй в хаотических битвах и участвуй в битвах против Исчадия Хаоса. А я пока поищу тот манускрипт и к твоему возвращению узнаю точно, что надо сделать с репутацией.",
				"q1031" => "Спасибо, Святой Отец! Так и поступим! Жди меня скоро с победами.",
			);
		}
		if ($q31['val'] == 11) {
			$mlvillage2[0] = array(
				"0"  => "Приветствую тебя, путник, в нашей скромной обители. Пришел ли ты просить Господа о благодати и отпущении грехов или помолиться со мной о душах других грешников? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем? Или может выиграл 600 хаотических битв и нанёс урон в 30 битвах против Исчадия Хаоса?",
				"d1001" => "Да, я готов помочь. Говори, что надо сделать.",
				"d1008" => "Да, я одержал победы в 600 хаотических битв и нанёс урон в 30 битвах против Исчадия Хаоса!",
				"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
			);
			// проверяем выиграл 600 хаотических битв и 30 против ИХ
			$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31a" AND val >= 30');
			$q2 = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31v" AND val >= 600');
			if (mysql_num_rows($q) > 0 && mysql_num_rows($q2) > 0) {
				$q31stepcomplete = true;
			}
			if (!$q31stepcomplete) unset($mlvillage2[0]["d1008"]);

			$mlvillage2[1008] = array(
				0 => "Да ты настоящий герой! Еще немного и ты сможешь открыть этот ларец. Все, что тебе осталось, это бескорыстно отдать 300 тысяч своей репутации тому, кто отдал тебе также бескорыстно что-то другое и не менее ценное. Более ничего в манускрипте не написано. Может ты знаешь такого человека?",
				"q1032" => "Конечно знаю! Это же Одинокий Рыцарь! Он бескорыстно отдал мне этот бесценный ларец и ничего не просил взамен! Пора мне к нему вернуться и завершить начатое.",
			);
		}
	}




	$mlvillage3 = array(
		0 => array(
			"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
			"d2001" => "Да, я готов помочь. Говори, что надо сделать.",
			"2001"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
		),
		2001 => array(
			"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
			"d2002" => "Семейный секрет",
			"11111"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
		),
		2002 => array(
			"0"  => "Была у меня мечта, сделать уникальный меч по старинному семейному рецепту, который передавался в нашей семье от отца к сыну на протяжении столетий.  Его надо делать не только из особых ингредиентов, но и в особое время. Сегодня наступило третье полнолуние лунного года и у меня есть три дня на его изготовление. Готов ли ты мне помочь в этом?",
			"q2018" => "Да, я готов тебе помочь",
			"d2001" => "Нет, это слишком сложно для меня",
		),
		"2999thx" => array(
			"0" => "Спасибо, ты мне очень поможешь!",
			"11111" => "Пока.",
		),
	);


	if (isset($_GET['qaction']) && strlen($_GET['qaction']) && $questexist === FALSE || isset($_GET['quest']) && $questexist === FALSE) {
		if (isset($_GET['qaction'])) {
			$qa = $_GET['qaction'];
			if (!is_numeric($qa[0])) {
				$num = intval(substr($qa,1));
			} else {
				$num = intval($qa);
			}

			if ($num < 1000) {
				$_GET['quest'] = 1;
			} elseif ($num > 1000 && $num < 2000) {
				$_GET['quest'] = 2;
			} elseif ($num > 2000 && $num < 3000) {
				$_GET['quest'] = 3;
			}
		}

		if ($_GET['quest'] == 1) {
			if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";
	
			$qa = $_GET['qaction'];
			$num = -1;
			if (!is_numeric($qa[0])) {
				$num = intval(substr($qa,1));
			}
			if ($qa[0] == "d" && isset($mlvillage1[$num])) {
				$mldiag = $mlvillage1[$num];

				// тут квесты режем исполненные
				if (isset($qcomplete[2]) && isset($mldiag["d2"])) unset($mldiag['d2']);
				if (isset($qcomplete[23]) && isset($mldiag["d3"])) unset($mldiag['d3']);
			} elseif ($qa[0] == "q") {
				if ($num == 2 && !isset($qcomplete[2])) {
					// квест - бабушкин пирог
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,2,"0/0/0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlvillage1["t1"];
				}                 			

				if ($num == 23 && !isset($qcomplete[23])) {
					// квест - сейф для трактирщика
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,23) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				}                 			
			} else {
				UnsetQA();
			}
		}

		if ($_GET['quest'] == 2) {
			if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";
	
			$qa = $_GET['qaction'];
			$num = -1;
			if (!is_numeric($qa[0])) {
				$num = intval(substr($qa,1));
			}
			if ($qa[0] == "d" && isset($mlvillage2[$num])) {
				$mldiag = $mlvillage2[$num];

				// тут квесты режем исполненные
				if (isset($qcomplete[26]) && isset($mldiag["d1002"])) unset($mldiag['d1002']);
				if (isset($qcomplete[27]) && isset($mldiag["d1003"])) unset($mldiag['d1003']);
				if (isset($qcomplete[28]) && isset($mldiag["d1004"])) unset($mldiag['d1004']);
			} elseif ($qa[0] == "q") {
				if ($num == 1026 && !isset($qcomplete[26])) {
					// квест - икона пропавшка
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,26) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlvillage2["t1026"];
				}                 			
				if ($num == 1027 && !isset($qcomplete[27])) {
					// квест - людоед
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,27) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlvillage2["t1026"];
				}                 			
				if ($num == 1028 && !isset($qcomplete[28])) {
					// квест - диковенные чётки
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,28,"0/0/0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlvillage2["t1026"];
				}                 			
				if ($num == 1031 && $q31 !== false && $q31['val'] == 10) {
					mysql_query('UPDATE oldbk.map_var SET val = 11 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
					unsetQA();
				}
				if ($num == 1032 && $q31 !== false && $q31['val'] == 11 && $q31stepcomplete == true) {
					mysql_query('UPDATE oldbk.map_var SET val = 12 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
					unsetQA();
				}
			} else {
				UnsetQA();
			}
		}


		if ($_GET['quest'] == 3) {
			if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";
	
			$qa = $_GET['qaction'];
			$num = -1;
			if (!is_numeric($qa[0])) {
				$num = intval(substr($qa,1));
			}
			if ($qa[0] == "d" && isset($mlvillage3[$num])) {
				$mldiag = $mlvillage3[$num];

				// тут квесты режем исполненные
				if (isset($qcomplete[18]) && isset($mldiag["d2002"])) unset($mldiag['d2002']);
			} elseif ($qa[0] == "q") {
				if ($num == 2018 && !isset($qcomplete[18])) {
					// квест - Семейный секрет
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,18,"0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlvillage3["2999thx"];
				}                 			
			} else {
				UnsetQA();
			}
		}
	}

	// 11 квест, сразу диалог
	if ($questexist !== FALSE && !isset($_GET['quest']) && !isset($_GET['qaction']) && $questexist['q_id'] == 11 && ($questexist['step'] == 1 || $questexist['step'] == 2 || $questexist['step'] == 3)) {
		$_GET['quest'] = 1;
		$_GET['qaction'] = 1;
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
		<div>
            <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlvillage.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlvillage_bg.jpg" id="mainbg">
<a href="?quest=1"><img style="z-index:3; position: absolute; left: 55px; top: 180px;" src="http://i.oldbk.com/i/map/mlvillage_pers1.png" alt="Трактирщик" title="Трактирщик" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlvillage_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlvillage_pers1.png'"/></a>
<a href="?quest=2"><img style="z-index:3; position: absolute; left: 250px; top: 185px;" src="http://i.oldbk.com/i/map/mlvillage_pers2.png" alt="Священник" title="Священник" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlvillage_pers2_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlvillage_pers2.png'"/></a>
<a href="?quest=3"><img style="z-index:3; position: absolute; left: 420px; top: 180px;" src="http://i.oldbk.com/i/map/mlvillage_pers3.png" alt="Кузнец" title="Кузнец" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlvillage_pers3_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlvillage_pers3.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
	}

	if (isset($_GET['quest']) && $_GET['quest'] == 1 || isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] < 1000) {
		$mlquest = "0/0";
	} elseif (isset($_GET['quest']) && $_GET['quest'] == 2 || isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 1000 && $_GET['qaction'] < 2000) {
		$mlquest = "100/70";
	} elseif (isset($_GET['quest']) && $_GET['quest'] == 3 || isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000) {
		$mlquest = "200/70";
	} else die();
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