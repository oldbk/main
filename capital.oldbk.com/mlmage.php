<?php

	$mlglobal = 1;
	require_once('mlglobal.php');

	require_once('castles_config.php');
	require_once('castles_functions.php');

	$questEngine = false;
	if(isset($_GET['qaction']) && isset($_GET['d'])) {
		$questEngine = true;
	}
	$mldiag = array();


		$questexist = false;
		$qcomplete = false;
		$q31 = false;
	if($questEngine === false) {

		$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31"') or die();
		if (mysql_num_rows($q) > 0) {
			$q31 = mysql_fetch_assoc($q);
		}
		if ($q31 !== false && $q31['val'] == 13) $q31 = false;


		$qlist = array(9,14,17,25);
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

		$mlmage = array(
			0 => array(
				"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
				"d1" => "Да, я готов помочь. Говори, что надо сделать.",
				"33333" => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				"44444" => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
			),
			1 => array(
				"0"  => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
				"d2" => "Магическое зеркало",
				"d3" => "Магическая сила",
				"d4" => "Цветные сердца",
				"d5" => "Праздничные приготовления",
				"1"  => "Мне не нравится ни одна из твоих просьб. Прощай.",
			),
			2 => array(
				"0"  => "Ты не зря зашел ко мне, воин. Сегодня утром я разбил свое любимое магическое зеркало. Мне надо, чтобы ты, как можно быстрее, достал  резную оправу, железные ножки и драгоценные камни для украшения. Тогда я соберу новое зеркало, а тебе обещаю достойную награду.",
				"q9" => "Хорошо, я достану все, что ты просишь",
				"d1" => "Нет, я не смогу этого сделать",
			),
			3 => array(
				"0"  => "Уже неделю я готовлю важный и древнейший магический обряд, собираю все, что для него нужно. Есть уже крылья летучей мыши, волчья ягода, 15 манускриптов с заклинаниями, змеиный яд, ушная сера, пепел бамбукового дерева, щепотка трухи и рыбий глаз…. Осталось совсем немного, но мне еще готовить жертвенный огонь и переплавлять магические свечи. Не хватает только чистой речной воды, заячьей лапки и древнего манускрипта. Когда-то он хранился у Ведьмы, но где сейчас, точно не знаю. Если принесешь мне все, чего не хватает – отблагодарю.",
				"q14" => "Хорошо, я достану все, что ты просишь",
				"d1" => "Нет, я не смогу этого сделать",
			),
			4 => array(
				"0"  => "Ты появился вовремя. Сегодня правильный день для проведения старинного магического обряда. Именно в этот день три сердца меняют свой цвет – Дракона, Орла и Летучей мыши. Если принесешь мне сердца разных цветов, я смогу выполнить обряд. Да не забудь заглянуть к Болотной ведьме за Эликсиром Вечной Молодости и к Пилигриму за связкой пшеничных зерен. Если принесешь все, что мне нужно – отблагодарю.",
				"q17" => "Хорошо, я принесу все, что ты просишь.",
				"d1" => "Нет, я не смогу этого сделать",
			),
			5 => array(
				"0"  => "Старый я уж. Но не настолько старый что бы не отпраздновать свой День Рождения! А вот пригласить всех, кого надо, лично – сил уже никаких нет. Возьми эти тринадцать приглашений и отнеси их Почтальону, но учти, все они должны быть доставлены в срок. Если все тринадцать гостей не посетят меня в эту ночь – чуда не произойдет, кхм, я имею ввиду праздник не удастся.",
				"d6" => "Неужели в твоем возрасте празднуют Дни Рождения? Тебе лет сто не меньше.",
			),
			6 => array(
				"0"  => "Сто тринадцать, вообще-то. Ну так что, выполнишь мою просьбу или это слишком сложная задача для храброго война?",
				"q25" => "Ладно, ладно. Передам твои тринадцать приглашений. Ты только к тому времени не помри. Пока!",
				"d1" => "Вообще-то работенка действительно не из легких… (отказаться)",
			),
			"thx" => array(
				"0" => "Спасибо, ты мне очень поможешь!",
				"11111" => "Пока.",
			),
		);

		$todel = false;
		if ($q31 !== FALSE && QItemExists($user,3003092)) {
			if ($q31['val'] == 8) {
				$mlmage[0]["d7"] = "Принес тебе показать древний ларец, который достался по наследству Одинокому Рыцарю. Все ключи в нем провернулись, но ларец не открывается. Может ты знаешь, что за заклятие на него наложено?";
				$mlmage[7] = array(
					0 => "Хмм... Интересная вещица. Про такую я только слыхал, но в руках не держал ни разу. Будет интересно с ней повозиться. Принеси мне 50 «Зельев Мага» и я смогу создать из них элексир, который позволит ларцу открыться.",
					"q31" => "Договорились, я принесу тебе эти зелья.",
				);
			}

			if ($q31['val'] == 9) {
				$mlmage[0] = array(
					"0"  => "Приветствую тебя, путник! Что привело тебя сюда? Жажда наживы, побед, развлечений или просто случай? Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем? Или ты принес мне то, что я просил?",
					"d7" => "Да, держи 50 «Зельев Мага» для твоего элексира.",
					"1"  => "Нет, я еще не все нашел, вернусь позже.",
					"33333" => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					"44444" => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
				);
				$todel = QItemExistsCountID($user,667667,50);
				if ($todel == false) unset($mlmage[0]["d7"]);

				$mlmage[7] = array(
					0 => "Быстро же ты обернулся. Ну что ж... Элексир готов, но есть в нем одна особенность. Ларец заколдован так, что только руки Ричарда Львиное Сердце могут его открыть. Вот элексир, который должен выпить тот, кто хочет открыть ларец, и тогда ларец примет его, как своего нового хозяина.  Но этого мало. Нужны еще эпические подвиги, которые должен совершить герой, прежде, чем откроет ларец. Держи элексир и сходи к Священнику, может у него в церковной библиотеке найдется манускрипт, в котором написано о подвигах.",
					"q32" => "Спасибо за помощь! Пока!",
				);
			}
		}

		if (isset($_GET['qaction']) && strlen($_GET['qaction']) && $questexist === FALSE || isset($_GET['quest']) && $questexist === FALSE) {
			if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

			$qa = $_GET['qaction'];
			$num = -1;
			if (!is_numeric($qa[0])) {
				$num = intval(substr($qa,1));
			} else {
				$num = intval($qa);
			}
			if ($qa[0] == "d" && isset($mlmage[$num])) {
				$mldiag = $mlmage[$num];

				// тут квесты режем исполненные
				if (isset($qcomplete[9]) && isset($mldiag["d2"])) unset($mldiag['d2']);
				if (isset($qcomplete[14]) && isset($mldiag["d3"])) unset($mldiag['d3']);
				if (isset($qcomplete[17]) && isset($mldiag["d4"])) unset($mldiag['d4']);
				if (isset($qcomplete[25]) && isset($mldiag["d5"])) unset($mldiag['d5']);
			} elseif ($qa[0] == "q") {
				if ($num == 9 && !isset($qcomplete[9]))  {
					// квест - . Магическое зеркало
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,9,"0/0/0/0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlmage["thx"];
				} elseif ($num == 14 && !isset($qcomplete[14])) {
					// квест - . Магическая сила
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,14,"0/0/0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlmage["thx"];
				} elseif ($num == 17 && !isset($qcomplete[17])) {
					// квест - . Цветные сердца
					mysql_query('START TRANSACTION') or QuestDie();
					SetNewQuest($user,17,"0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					$mldiag = $mlmage["thx"];
				} elseif ($num == 25 && !isset($qcomplete[25])) {
					// квест - праздничные приготовления
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003087,"Маг") or QuestDie();
					addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Приглашения</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);
					SetNewQuest($user,25,"0/0/0/0/0") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($num == 31 && $q31 !== false && $q31['val'] == 8) {
					mysql_query('UPDATE oldbk.map_var SET val = 9 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
					UnsetQA();
				} elseif ($num == 32 && $q31 !== false && $q31['val'] == 9 && $todel !== FALSE) {
					mysql_query('START TRANSACTION') or QuestDie();
					mysql_query('UPDATE oldbk.map_var SET val = 10 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
					PutQItemTo($user,"Маг",$todel);
					PutQItem($user,3003093,"Маг") or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					UnsetQA();
				}
			} elseif ($num >= 33333) {
				// do nothing
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlmage.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlmage_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 225px; top: 80px;" src="http://i.oldbk.com/i/map/mlmage_pers1.png" alt="Маг" title="Маг" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlmage_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlmage_pers1.png'"/></a>
<?php
$q_goodbuyer = array(10,12,14,15,16,17,22,23,25,6,8,9,29,30);

$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_MAG);

if (isset($_GET['qaction']) && ($_GET['qaction'] >= 44444 && $_GET['qaction'] <= 44450) && $questEngine === false) {
	if ($_GET['qaction'] == 44444) {
		$mldiag = array(
			0 => "Сколько столетий живу, столько поражаюсь людям, - никакого понятия о магии, а все лезут и лезут... Хотя ладно, помогу тебе. Но учти, если твои руны недостаточно прокачаны магическим опытом, то моих сил хватит подтянуть их только до 10 уровня и оплата будет репутацией, ибо она мне сейчас очень нужна для нового магического зелья.",
			//44445 => " Моя руна прокачана магическим опытом и она ниже 20 уровня. Надо просто поднять уровень. Сколько это будет стоить?",
			44446 => "Моя руна недостаточно прокачана магическим опытом, но она ниже 10 уровня. Сколько будет стоить быстрее ее докачать?",
			//44447 => "Моя руна прокачана магическим опытом и она выше 20 уровня. Какой свиток тебе нужен?",
			11111 => "Я, пожалуй, зайду попозже.",
		);
	}
	/*
	if ($_GET['qaction'] == 44445) {
		$q = mysql_query('select * from oldbk.inventory where owner = '.$user['id'].' and type=30 and dressed=0 and up_level<20 and ups>=add_time ORDER BY `update` DESC ') or die();
		if (mysql_num_rows($q) >0) {
			$bt = "";
			if ((int)($_GET['r'])>0) {
				$rid=(int)($_GET['r']);
				$answ=mk_runs_lvl_up($rid);
				$bt .= "<font color=red>".$answ[msg]."</font><br><br>";
				$q = mysql_query('select * from oldbk.inventory where owner = '.$user['id'].' and type=30 and dressed=0 and up_level<20 and ups>=add_time ORDER BY `update` DESC ') or die();
			}


			$bt .= '<font color=red>Руны готовые к поднятию уровня:</font>';	
			$mldiag[0] = $bt;
			$bt = "";

			while($row = mysql_fetch_assoc($q)) {
				ob_start();
				$bt .= '<!-- NOLINK --><TABLE>';
				showitem ($row,0, false,'','<a href=?qaction=44445&r='.$row[id].'><small><b>(поднять уровень за '.($row[up_level]+1).' монет)</b></small></a>', 0, 0);
				$bt .= ob_get_contents();
				ob_end_clean();
				$bt .= '</TABLE>';
				$mldiag[] = $bt;
				$bt = "";
			}
			$mldiag[11111] = "Я, пожалуй, зайду попозже.";
		} else {
			$mldiag = array(
				0 => "В рюкзаке не найдено рун, готовых к поднятию уровня.",
				11111 => "Я, пожалуй, зайду попозже.",
			);
		}
	}
	else
	*/
	if ($_GET['qaction'] == 44446) {
		$q = mysql_query('select * from oldbk.inventory where owner = '.$user['id'].' and type=30 and dressed=0 and up_level<10 ORDER BY `update` DESC ') or die();
		if (mysql_num_rows($q) >0) {
			$bt = "";
			if ((int)($_GET['r'])>0) {
				$rid=(int)($_GET['r']);
				$answ=mk_runs_full_lvl_up($rid);
				$bt.="<font color=red>".$answ[msg]."</font><br><br>";
				$q = mysql_query('select * from oldbk.inventory where owner = '.$user['id'].' and type=30 and dressed=0 and up_level<10 ORDER BY `update` DESC ') or die();
			}

			$bt .= 'Руны ниже 10 уровня возможные к быстрой прокачке:';	
			$mldiag[0] = $bt;
			$bt = "";

			while($row = mysql_fetch_assoc($q)) {
				ob_start();
				$bt .= '<!-- NOLINK --><TABLE>';
				showitem ($row,0, false,'','<a href=?qaction=44446&r='.$row[id].'><small><b>(докачать уровень за '.$runs_lvl_cost[$row[up_level]+1].' реп.)</b></small></a>', 0, 0);
				$bt .= ob_get_contents();
				ob_end_clean();
				$bt .= '</TABLE>';
				$mldiag[] = $bt;
				$bt = "";
			}
			$mldiag[11111] = "Я, пожалуй, зайду попозже.";
		} else {
			$mldiag = array(
				0 => "В рюкзаке не найдено рун ниже 10 уровня возможных к быстрой прокачке.",
				11111 => "Я, пожалуй, зайду попозже.",
			);
		}
	}
	/*
	else
		if ($_GET['qaction'] == 44447) 
		{
		//дополнительный диалог
			$mldiag = array(
			0 => "Для рун такого высокого уровня только моих сил недостаточно. Мне нужен \"Рунический свиток развития\", который продается в городе в Магазине только на прилавке Великих. Посмотри сам какой свиток тебе нужен:<br>
			<small><b>
			Руну 20 уровня поднять на 21 - Рунический свиток развития I<br>
			Руну 21 уровня поднять на 22 - Рунический свиток развития II<br>
			Руну 22 уровня поднять на 23 - Рунический свиток развития III<br>
			Руну 23 уровня поднять на 24 - Рунический свиток развития IV<br>
			Руну 24 уровня поднять на 25 - Рунический свиток развития V<br>
			Руну 25 уровня поднять на 26 - Рунический свиток развития VI<br>
			Руну 26 уровня поднять на 27 - Рунический свиток развития VII<br>
			Руну 27 уровня поднять на 28 - Рунический свиток развития VIII<br>
			Руну 28 уровня поднять на 29 - Рунический свиток развития IX<br>
			Руну 29 уровня поднять на 30 - Рунический свиток развития X.</b></small>",
			11111 => " Хорошо, я вернусь со свитком.",
			44448 => " У меня есть нужный свиток.",
		);
		
		
		}
	else
	if ($_GET['qaction'] == 44448) {
	//руны больше 20го
		$q = mysql_query('select * from oldbk.inventory where owner = '.$user['id'].' and type=30 and dressed=0 and up_level>=20 and ups>=add_time ORDER BY `update` DESC ') or die();
		if (mysql_num_rows($q) >0) {
			$bt = "";
			if ((int)($_GET['r'])>0) {
				$rid=(int)($_GET['r']);
				$answ=mk_runs_lvl_up($rid);
				$bt .= "<font color=red>".$answ[msg]."</font><br><br>";
				$q = mysql_query('select * from oldbk.inventory where owner = '.$user['id'].' and type=30 and dressed=0 and up_level>=20 and ups>=add_time ORDER BY `update` DESC ') or die();
			}


			$bt .= '<font color=red>Руны готовые к поднятию уровня:</font>';	
			$mldiag[0] = $bt;
			$bt = "";
 	  		$scnam=array("21"=>"I" , "22"=>"II" , "23"=>"III" , "24"=>"IV" , "25"=>"V" , "26"=>"VI" , "27"=>"VII" , "28"=>"VIII" , "29"=>"IX" , "30"=>"X"  ) ;
			while($row = mysql_fetch_assoc($q)) {
				ob_start();
				$bt .= '<!-- NOLINK --><TABLE>';
	 	  		//названия
				 $addtext=' "Рунический свиток развития '.$scnam[($row[up_level]+1)].'"'; 
				showitem ($row,0, false,'','<a href=?qaction=44448&r='.$row[id].'><small><b>(поднять уровень за '.$addtext.')</b></small></a>', 0, 0);
				$bt .= ob_get_contents();
				ob_end_clean();
				$bt .= '</TABLE>';
				$mldiag[] = $bt;
				$bt = "";
			}
			$mldiag[11111] = "Я, пожалуй, зайду попозже.";
		} else {
			$mldiag = array(
				0 => "В рюкзаке не найдено рун, готовых к поднятию уровня.",
				11111 => "Я, пожалуй, зайду попозже.",
			);
		}
	}
	*/

	//$mlquest = "400/100";

	//if (!ADMIN) unset($mldiag[44444]);

	//require_once('mlquest.php');
}

if (isset($_GET['qaction']) && ($_GET['qaction'] >= 33333 && $_GET['qaction'] <= 33341) && $questEngine === false) {
	// тут страницы в книги
	if (($_GET['qaction'] >= 33335 && $_GET['qaction'] <= 33341)) {
		$booknum = $_GET['qaction']-33335;

		$pages = array();
	        $q = mysql_query("SELECT *,(select `all_access` from oldbk.clans_arsenal ars where ars.id_inventory=i.id) as all_access FROM  oldbk.`inventory` i WHERE i.arsenal_klan='{$user[klan]}' AND i.owner='22125' AND i.arsenal_owner!='{$user[id]}' and i.type = 300"); 
		while($b = mysql_fetch_assoc($q)) {
			$color = ceil(($b['prototype'] - 3003100) / 5)-1;
			$pages[$color][$b['prototype']]++;
		}

		$ids = array();

		if (count($pages[$booknum]) == 5) {
			$q = mysql_query("SELECT *,(select `all_access` from oldbk.clans_arsenal ars where ars.id_inventory=i.id) as all_access FROM  oldbk.`inventory` i WHERE i.arsenal_klan='{$user[klan]}' AND i.owner='22125' AND i.arsenal_owner!='{$user[id]}' and i.type = 300 AND i.prototype >= ".(3003100+(($booknum)*5)+1)." AND i.prototype <= ".(3003100+((($booknum)*5)+5))." GROUP BY i.prototype");
			while($b = mysql_fetch_assoc($q)) {
				$ids[] = $b['id'];
				if (count($ids) == 5) break;
			}
		}

		$emoney = false;
		if (count($ids) == 5) {
			// выгребаем монеты 
			/*
			$q = mysql_query('SELECT * FROM inventory WHERE prototype = 3003060 AND owner = '.$user['id'].' LIMIT 6') or die();
			if (mysql_num_rows($q) == 6) {
				while($m = mysql_fetch_assoc($q)) {
					$ids[] = $m['id'];
				}
			} else {
			*/
			if ($user['money'] < 250) {
				$mldiag = array(
					0 => "Бесплатно не работаю, возвращайся когда будет 250 кр.",
					11111 => "Понял, я вернусь.",
				);
			} else {
				$emoney = true;
			}
		}

		if ($emoney) {
			// выдаём книгу
			mysql_query('START TRANSACTION') or die();
			mysql_query('DELETE FROM oldbk.inventory WHERE id IN ('.implode(",",$ids).')') or die();

			mysql_query('UPDATE users SET money = money - 250 WHERE id = '.$user['id']) or die();
			                                                             
			$selfclan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$user['klan'].'"') or die();
			$selfclan = mysql_fetch_assoc($selfclan);
			if ($selfclan) {
				PutBookToArs($user,3003131,$booknum,$selfclan['id']) or die();
			}

			$log_text = '"'.$user[login].'" обменял у Мага 5 страниц на '.$cbookpagesm[$booknum]["name"].' Магическую Книгу';
			mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$user[klan]}','{$user[id]}','{$log_text}','".time()."')") or die();
			mysql_query('COMMIT') or die();
			
			$mldiag = array(
				0 => "Держи свою книгу и проваливай, я устал.",
				11111 => "Спасибо.",
			);
		}
	}

	if ($_GET['qaction'] == 33333) {
		$pages = array();
	        $q = mysql_query("SELECT *,(select `all_access` from oldbk.clans_arsenal ars where ars.id_inventory=i.id) as all_access FROM  oldbk.`inventory` i WHERE i.arsenal_klan='{$user[klan]}' AND i.owner='22125' AND i.arsenal_owner!='{$user[id]}' and i.type = 300"); 
		while($b = mysql_fetch_assoc($q)) {
			$color = ceil(($b['prototype'] - 3003100) / 5)-1;
			$pages[$color][$b['prototype']]++;
		}

		$mldiag = array(
			0 => "Правду говорят, могу собрать Книгу. Но бесплатную работу даже Конюхи не делают. Давай 250 кр. и нужные страницы и Магическая Книга станет твоей.",
		);

		while(list($k,$v) = each($pages)) {
			if (count($v) == 5) {
				$mldiag[33335+$k] = "Собери мне ".$cbookpagesm[$k]["name"]." Магическую книгу";
			}
		}

		$mldiag[11111] = "Нет, я передумал. Я лучше зайду в другой раз.";
	}

	//$mlquest = "400/100";

	//if (!ADMIN) unset($mldiag[44444]);

	//require_once('mlquest.php');
} elseif($questEngine === false) {
	if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
		if ($questexist !== FALSE && in_array($questexist['q_id'],$q_goodbuyer) !== FALSE) {
			// есть квест - подключаем квестовый обработчик
			// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
			require_once('./mapquests/'.$questexist['q_id'].'.php');
		} else {
			// нету квеста, разговариваем
			if (empty($mldiag)) {
				$mldiag = array(
					0  => "Не часто меня гости беспокоят.  Зачем пришел и по какому делу? Говори.",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				);

				$mldiag[11111] = "Ничего особенного, просто проходил мимо. Пойду дальше.";
			}
		}

		if(!isset($_GET['qaction']) || $_GET['qaction'] == 'd0' || $_GET['qaction'] == '') {
			$_temp = isset($mldiag[11111]) ? $mldiag[11111] : null;
			unset($mldiag[11111]);
			foreach ($BotDialog->getMainDialog() as $dialog) {
				$key = '&d='.$dialog['dialog'];
				$mldiag[$key] = $dialog['title'];
			}
			if($_temp) {
				$mldiag[11111] = $_temp;
			}
		}

		//$mlquest = "400/100";

		//if (!ADMIN) unset($mldiag[44444]);

		//if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');
	}	
}

if($questEngine === true) {
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

if (isset($_GET['quest']) || isset($_GET['qaction'])) {
	$mlquest = "400/100";
	require_once('mlquest.php');
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