<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
	if (mysql_num_rows($q) > 0) {
		$questexist = mysql_fetch_assoc($q) or die();
	}

	$q31 = false;
	$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31"') or die();
	if (mysql_num_rows($q) > 0) {
		$q31 = mysql_fetch_assoc($q);
	}
	if ($q31 !== false && $q31['val'] == 13) $q31 = false;

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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlbuyer.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlbuyer_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 120px; top: 90px;" src="http://i.oldbk.com/i/map/mlbuyer_pers1.png" alt="Скупщик" title="Скупщик" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlbuyer_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlbuyer_pers1.png'"/></a>
<?php

$q_goodbuyer = array(9,14,18,20,26,28);

$get_test_baff = mysql_fetch_array(mysql_query("select * from effects where owner = '{$_SESSION['uid']}'  and  type=111010  "));
if (($get_test_baff[id] > 0)) {
	$collection_bonus=1+round(($get_test_baff['add_info']/100),2);
} else {
	$collection_bonus=1;
}

if (isset($_GET['qaction']) && $_GET['qaction'] == 33333) {
	/*
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['how']) && intval($_POST['how']) > 0) {
		$how = intval($_POST['how']);
		mysql_query('START TRANSACTION') or die();

		$q = mysql_query('SELECT * FROM inventory WHERE prototype = 3003060 AND owner = '.$user['id']) or die();
		if (mysql_num_rows($q) < $how) {
			// продаём больше чем есть
			$mldiag = array(
				0 => "У вас нет такого количества.",
				33333 => "Давай попробуем еще раз.",
			);
		} else {
			$ids = "";
			$money = 0;
			for ($i = 0; $i < $how; $i++) {
				$it = mysql_fetch_assoc($q) or die();
				$money += 3;
				mysql_query('DELETE FROM inventory WHERE id = '.$it['id']) or die();
				$ids .= get_item_fid($it).",";
			}

			if ($money > 0) {
				$ids = substr($ids,0,strlen($ids)-1);

				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']+$money;
				$rec['target']=0;
				$rec['target_login']="Скупщик";
				$rec['type']=259; // получил деньги от квестбота
				$rec['aitem_id']=$ids;
				$rec['item_name']="Монета";
				$rec['item_count']=$how;
				$rec['item_type']=50;
				$rec['item_cost']=3;
				$rec['item_dur']=0;
				$rec['item_maxdur']=1;
				$rec['item_proto']=3003060;
				$rec['item_arsenal']='';
				$rec['sum_kr']=$money;

				add_to_new_delo($rec) or die();

				mysql_query('UPDATE users SET money = money + '.$money.' WHERE id = '.$user['id']) or die();

				if ($how == 1) {
					$text = "монету";
				} else {
					$text = "монет";
				}

				$mldiag = array(
					0 => "Вы продали ".$how." ".$text."  за ".$money." кр.",
					11111 => "Удачи, еще увидимся!",
				);
			}
		}
		mysql_query('COMMIT') or die();
	} else*/ 
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['howst']) && intval($_POST['howst']) > 0) {
		$how = intval($_POST['howst']);
		mysql_query('START TRANSACTION') or die();

		$q = mysql_query('SELECT * FROM inventory WHERE prototype in (15551,15552,15553,15554,15555,15556,15557,15558) AND owner = '.$user['id']) or die();
		if (mysql_num_rows($q) < $how) {
			// продаём больше чем есть
			$mldiag = array(
				0 => "У вас нет такого количества.",
				33333 => "Давай попробуем еще раз.",
			);
		} else {
			$ids = "";
			$money = 0;
			for ($i = 0; $i < $how; $i++) {
				$it = mysql_fetch_assoc($q) or die();
				$money += (150*$collection_bonus);
				mysql_query('DELETE FROM inventory WHERE id = '.$it['id']) or die();
				$ids .= get_item_fid($it).",";
			}

			if ($money > 0) {
				$ids = substr($ids,0,strlen($ids)-1);

				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']+$money;
				$rec['target']=0;
				$rec['target_login']="Скупщик";
				$rec['type']=259; // получил деньги от квестбота
				$rec['aitem_id']=$ids;
				$rec['item_name']="Статуя";
				$rec['item_count']=$how;
				$rec['item_type']=50;
				$rec['item_cost']=(150*$collection_bonus);
				$rec['item_dur']=0;
				$rec['item_maxdur']=1;
				$rec['item_proto']=15550;
				$rec['item_arsenal']='';
				$rec['sum_kr']=$money;

				add_to_new_delo($rec) or die();

				mysql_query('UPDATE users SET money = money + '.$money.' WHERE id = '.$user['id']) or die();

				$text = "статуи";

				$mldiag = array(
					0 => "Вы продали ".$how." ".$text." за ".$money." кр.",
					11111 => "Удачи, еще увидимся!",
				);
			}
		}
		mysql_query('COMMIT') or die();
	/*
	} else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['howb']) && intval($_POST['howb']) > 0) {
		// покупаем монеты
		$how = intval($_POST['howb']);

		mysql_query('START TRANSACTION') or die();

		if ($user['money'] < $how*6) {
			// продаём больше чем есть
			$mldiag = array(
				0 => "У вас нет столько кр.",
				33333 => "Давай попробуем еще раз.",
			);
		} else {
			// отнимаем деньги
			mysql_query('UPDATE users SET money = money - '.($how*6).' WHERE id = '.$user['id']);

			// пишем в дело
			$rec = array();
    			$rec['owner']=$user[id];
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money']-($how*6);
			$rec['target']=0;
			$rec['target_login']="Скупщик";
			$rec['type']=252; // заплатил квест боту
			$rec['item_name']="Монета";
			$rec['item_count']=$how;
			$rec['item_type']=50;
			$rec['item_cost']=3;
			$rec['item_dur']=0;
			$rec['item_maxdur']=1;
			$rec['item_proto']=3003060;
			$rec['item_arsenal']='';
			$rec['sum_kr']=($how*6);

			add_to_new_delo($rec) or die();


			$user['money'] -= ($how*6);

			// выдаём монеты
			for ($i = 0; $i < $how; $i++) {
				if (!PutQItem($user,3003060,"Скупщик",0,array(),99)) die();
			}
			
			if ($how == 1) {
				$text = "монету";
			} else {
				$text = "монет";
			}

			$mldiag = array(
				0 => "Вы купили ".$how." ".$text."  за ".($how*6)." кр.",
				11111 => "Удачи, еще увидимся!",
			);

		}
		mysql_query('COMMIT') or die();
	*/
	} else {
//		$q = mysql_query('SELECT * FROM inventory WHERE prototype  = 3003060 AND owner = '.$user['id']) or die();
		$q2 =mysql_query('SELECT * FROM inventory WHERE prototype in (15551,15552,15553,15554,15555,15556,15557,15558) and owner = '.$user['id']) or die();

		$mldiag = array(
			0 => "У вас ".mysql_num_rows($q2)." статуй. Статуи можно продать за ".(150*$collection_bonus)." кр",
			//1 => '<!-- NOLINK -->Продать монет: <form method=post><input type="text" name="how"> штук <input type=submit value="Продать"></form>',
			//2 => '<!-- NOLINK -->Купить: <form method=post><input type="text" name="howb"> штук <input type=submit value="Купить"></form>',
			3 => '<!-- NOLINK -->Продать статуй: <form method=post><input type="text" name="howst"> штук <input type=submit value="Продать"></form>',
			11111 => "Удачи, еще увидимся!",
		);
	}


	$mlquest = "300/100";
	require_once('mlquest.php');		
} else {
	if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
		if ($questexist !== FALSE && in_array($questexist['q_id'],$q_goodbuyer) !== FALSE) {
			// есть квест - подключаем квестовый обработчик
			// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
			require_once('./mapquests/'.$questexist['q_id'].'.php');
		} else {
			// нету квеста, разговариваем
			if ($q31 === false) {
				$mldiag = array(
					0 => "Проходи, проходи. Зачем в гости пожаловал?",
					33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
					11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			} else {
				$mlbuyer = false;
			        if ($q31['val'] == 0 && QItemExists($user,3003092)) {
					$mlbuyer = array(
						0 => array(
							0 => "Проходи, проходи. Зачем в гости пожаловал?",
							"d1" => "У меня к тебе вопрос деликатный. Смотри, вот древний ларец Одинокого Рыцаря, который достался ему от деда, а тому от прадеда, а ему ларец отдал сам Ричард Львиное Сердце. Может, знаешь ты, как его открыть?",
							33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
							11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
						),
						1 => array(
							0 => "Ты не первый с такой просьбой. В моем закутке уже, наверное, 100 ларцов лежит, к которым никакой ключ не подошел. Но про твой ларец знаю один секрет, и секрет этот стоит дорого. Слыхал я, что в Руинах Старого замка находят слитки золота. Если принесешь мне 10 таких слитков, так уж и быть, помогу тебе. Оставь пока ларец у меня, я посмотрю, что с ним можно сделать.",
							"q0" => "Хорошо. Держи ларец, я скоро вернусь к тебе со слитками.",
							"d2" => "Не хожу я в эти руины, я воин, а не археолог. Может чем другим тебе заплатить можно?",
						),
						2 => array(
							0 => "Хм... Можно и другим. Принеси мне 3 статуи и по рукам.",
							"q1" => "Хорошо. Держи ларец, я скоро вернусь к тебе с ними.",
							"q0" => "Нет, пожалуй я лучше поищу слитки. Сохрани ларец, пока я найду тебе слитки.",
							11111 => "Нет, меня такая сделка не устраивает. Пока.",
						),
					);
				}

				$todel = false;
				if ($q31['val'] == 1 || $q31['val'] == 2) {
					$mlbuyer = array(
						0 => array(
							0 => "Ты принес мне то, что я просил?",
							"q3" => "Да, вот твои 10 слитков золота.",
							"q4" => "Да, вот твои 3 статуи.",
							33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
							"11111" => "Нет, я еще не все нашел, вернусь позже.",
						),
						"next" => array(
							0 => "Повозился я с твоим ларцом. Все не так просто, как казалось. Для него нужны особенные ключи и особенная работа. Так что, ты уж извини, но плата подорожала. Слыхал я, что у вас в городе можно добыть чеки на предъявителя. Принеси мне 20 чеков, и я открою тебе твой ларец.",
							"11111" => "Ладно, найду я для тебя эти чеки. Только ты уж постарайся ларец-то открыть.",
						),
						"next2" => array(
							0 => "Нет проблем, я жду твоего возвращения с этой оплатой.",
							"11111" => "Спасибо. Пока.",
						),
					);
					if ($q31['val'] == 1) {
						$todel = QItemExistsCountID($user,777771,10);
						$mlbuyer[0]["q555"] = "Нет,  я передумал, меня больше не устраивает та оплата, на которую мы договорились. Я зашел сказать, что принесу тебе не слитки, а 3 статуи.";
						unset($mlbuyer[0]["q4"]);
						if ($todel === false) unset($mlbuyer[0]["q3"]);
					}
					if ($q31['val'] == 2) {
						$todel = QItemExistsCountIDP($user,array(15551,15552,15553,15554,15555,15556,15557,15558),3);

						$mlbuyer[0]["q555"] = "Нет, я передумал, меня больше не устраивает та оплата, на которую мы договорились. Я зашел сказать, что принесу тебе не статуи, а 10 слитков золота.";
						unset($mlbuyer[0]["q3"]);
						if ($todel === false) unset($mlbuyer[0]["q4"]);
					}
				}

				if ($q31['val'] == 3) {
					$mlbuyer = array(
						0 => array(
							0 => "Ты принес мне то, что я просил?",
							"q5" => "Да, вот твои 20 чеков на предъявителя. Говори свой секрет.",
							33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
							"11111" => "Нет, я еще не все нашел, вернусь позже.",
						),
						"next" => array(
							0 => "Ты со мной честно расплатился, и я слово тоже держать умею. Секрет у ларца особенный. Чтоб его открыть нужен не один ключ, а четыре. Только если все 4 ключа в нем провернутся – ларец откроется. Одна лишь проблема, не знаю я какие именно ключи ему нужны. Сходи-ка ты к Пилигриму, он много по свету бродил и может что-то слышал. А потом  возвращайся, и, если повезет, мы откроем твое сокровище.",
							"11111" => "Хорошо, схожу к Пилигриму. Но ты смотри за ларцом в оба глаза. Не дай Бог пропадет.",
						),
					);

					$todel = QItemExistsCountIDP($user,array(3101,3102,3103,3201,3202,3203,3204,3205,3206,3207),20);
					if ($todel === false) unset($mlbuyer[0]["q5"]);
				}

				if ($q31['val'] == 4 || $q31['val'] == 5) {
					$mlbuyer = array(
						0 => array(
							0 => "Ты узнал то, что я просил?",
							33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
							"11111" => "Нет, я еще не все узнал, вернусь позже.",
						),
					);
				}

				if ($q31['val'] == 6) {
					$mlbuyer = array(
						0 => array(
							0 => "Ты узнал то, что я просил?",
							"d1" => "Да, Пилигрим сказал, что нужны 4 ключа – золотой, серебряный, бронзовый и изумрудно-зеленый. Есть у тебя такие?",
							33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
							"11111" => "Нет, я еще не все узнал, вернусь позже.",
						),
						1 => array(
							0 => "Нет, ключей у меня таких нет, но я скажу тебе где их можно достать. Только для того, чтоб подобрать нужный ключ, нам придется перепробовать хотя-бы по 100 ключей каждого вида. Тогда есть шанс, что среди них окажется нужный.",
							"d2" => "Ладно, говори, где достать эти ключи?",
						),
						2 => array(
							0 => "Такие ключи часто попадаются в Героическом Лабиринте Хаоса. Говорят, что Духи разбрасывают их, чтоб дать путникам шанс выбраться из лабиринта. И говорят, что в Лабиринте бродит и дух самого Ричарда Львиное Сердце. Если нам повезет, то среди ключей могут оказаться те, что подбросит этот Дух. Иди в лабиринт и принеси мне по 100 штук каждого из ключей.",
							"q6" => "Непростая работенка, но я думаю, справлюсь. До встречи!",
						)
					);
				}

				if ($q31['val'] == 7) {
					$mlbuyer = array(
						0 => array(
							0 => "Ты принес мне то, что я просил?",
							"q7" => "Да, вот тебе по 100 ключей каждого вида. Давай попробуем открыть ларец.",
							33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
							"11111" => "Нет, я еще не все нашел, вернусь позже.",
						),
						"next" => array(
							0 => "Сейчас, сейчас... Дай мне пару минут... С такими связками ключей не просто разобраться... Еще немного... Вот! Смотри, все 4 ключа провернулись! Но ларец не открывается. Видимо на нем еще есть какое-то заклятие. Держи-ка ты ларец и сходи к Магу, может быть он сможет тебе помочь.",
							"q6" => "Спасибо за помощь.",
						)
					);

					$qi1 = QItemExistsCountID($user,3301,100);
					$qi2 = QItemExistsCountID($user,3302,100);
					$qi3 = QItemExistsCountID($user,3303,100);
					$qi4 = QItemExistsCountID($user,3304,100);
	
					if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE && $qi4 !== FALSE) {
						$todel = array_merge($qi1,$qi2,$qi3,$qi4);
					} else {
						unset($mlbuyer[0]["q7"]);
					}
				}

				
				if ($mlbuyer !== FALSE) {
					if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";
	
					$qa = $_GET['qaction'];
					$num = -1;
					if (!is_numeric($qa[0])) {
						$num = intval(substr($qa,1));
					}
	
					if ($qa[0] == "d" && isset($mlbuyer[$num])) {
						$mldiag = $mlbuyer[$num];
					} elseif ($qa[0] == "q") {
						if ($num == 0 && $q31['val'] == 0) {
							$todel = QItemExistsCountID($user,3003092,1);
							if ($todel !== FALSE) {
								mysql_query('START TRANSACTION') or QuestDie();
								mysql_query('UPDATE oldbk.map_var SET val = 1 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
								PutQItemTo($user,"Скупщик",$todel);
				                                mysql_query('COMMIT') or QuestDie();
							}
							UnsetQA();
						}
						if ($num == 1 && $q31['val'] == 0) {
							$todel = QItemExistsCountID($user,3003092,1);
							if ($todel !== FALSE) {
								mysql_query('START TRANSACTION') or QuestDie();
								mysql_query('UPDATE oldbk.map_var SET val = 2 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
								PutQItemTo($user,"Скупщик",$todel);
				                                mysql_query('COMMIT') or QuestDie();
							}
							UnsetQA();
						}

						if ($num == 555 && ($q31['val'] == 1 || $q31['val'] == 2)) {
							// забираем слитки из руин
							mysql_query('START TRANSACTION') or QuestDie();
							if ($q31['val'] == 1) {
								mysql_query('UPDATE oldbk.map_var SET val = 2 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
							}
							if ($q31['val'] == 2) {
								mysql_query('UPDATE oldbk.map_var SET val = 1 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
							}
			                                mysql_query('COMMIT') or QuestDie();
							$mldiag = $mlbuyer["next2"];
						}

						if ($num == 3 && $q31['val'] == 1 && $todel !== false) {
							// забираем слитки из руин
							mysql_query('START TRANSACTION') or QuestDie();
							PutQItemTo($user,"Скупщик",$todel);
							mysql_query('UPDATE oldbk.map_var SET val = 3 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
			                                mysql_query('COMMIT') or QuestDie();
							$mldiag = $mlbuyer["next"];
						}
						if ($num == 4 && $q31['val'] == 2 && $todel !== false) {
							// забираем статуи
							mysql_query('START TRANSACTION') or QuestDie();
							mysql_query('UPDATE oldbk.map_var SET val = 3 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
							PutQItemTo($user,"Скупщик",$todel);
			                                mysql_query('COMMIT') or QuestDie();
							$mldiag = $mlbuyer["next"];
						}
						if ($num == 5 && $q31['val'] == 3 && $todel !== false) {
							// забираем чеки
							mysql_query('START TRANSACTION') or QuestDie();
							mysql_query('UPDATE oldbk.map_var SET val = 4 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
							PutQItemTo($user,"Скупщик",$todel);
			                                mysql_query('COMMIT') or QuestDie();
							$mldiag = $mlbuyer["next"];
						}
						if ($num == 6 && $q31['val'] == 6) {
							mysql_query('UPDATE oldbk.map_var SET val = 7 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
							UnsetQA();
						}
						if ($num == 7 && $q31['val'] == 7 && $todel !== FALSE) {
							mysql_query('START TRANSACTION') or QuestDie();
							mysql_query('UPDATE oldbk.map_var SET val = 8 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
							PutQItemTo($user,"Скупщик",$todel);
							addchp ('<font color=red>Внимание!</font> Скупщик передал вам <b>Ларец</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);
							PutQItem($user,3003092,"Скупщик") or QuestDie();
			                                mysql_query('COMMIT') or QuestDie();
							$mldiag = $mlbuyer["next"];
						}
					} else {
						UnsetQA();
					}
				} else {
					$mldiag = array(
						0 => "Проходи, проходи. Зачем в гости пожаловал?",
						33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
						11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
					);
				}
			}
	
		}
	
		$mlquest = "300/100";
		if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
	}                                                                                                       
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