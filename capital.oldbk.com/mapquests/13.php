<?php
	// квест шляпа для лесоруба
	$q_status = array(
		0 => "Принести лесорубу красивые орлиные перья (%N1%/3), кожи (%N2%/1) и отборной дорогой ткани (%N3%/1)",
		1 => "Убить 10 Разбойников и вернуться (%N1%/10)",
		2 => "Отнести письмо Рыцаря к Почтальону",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 13) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlwood") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003044);
		$qi2 = QItemExistsID($user,3003046);
		$qi3 = QItemExistsCountID($user,3003045,3);
		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3);
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Ты умеешь держать слово, как я погляжу. Все что обещал, все принес. Я тоже не останусь неблагодарным. Держи за труды.",
					3 => "Спасибо, всегда рад помочь ",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,250) or QuestDie();
				$m = AddQuestM($user,2,"Лесоруб") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,15562,"Лесоруб",0,$todel,255,"eshop") or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Осколок статуи Мироздателя</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что обещал?",
			);
			if ($mlqfound) $mldiag[1] = "Да,  вот держи, лучшая кожа, красивые орлиные перья, отборный шелк на подкладку. Можно сшить новую шляпу, лучше старой.";
			$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[11111] = "Нет, я еще не успел, пойду дальше.";
		}		
	}

	if ($sf == "mlhunter" && !QItemExists($user,3003044)) {
		if ($ai[0] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Лесоруб мужик хороший, и лес  любит и бережет. Лишнего дерева никогда не свалит. Отчего ж не помочь. Но и у меня к тебе просьба есть – последнее время разбойники в лесу совсем разбушевались. Никакого слада с ними нет. Раньше только путников ловили, а теперь еще и зверям жить мешают. Да и охоту мне портят. То силки расставят в ненужном месте, то ловушки мои попортят, то зверей распугают… В одиночку мне с ними не справиться. По следам насчитал я человек 10, которые нынче лесу хозяйничают. Пойди, разберись с ними, уму-разуму научи. А я тебе пока кожу подготовлю.",
						2 => "Договорились, я помогу тебе наказать разбойников.",
					);
				} elseif ($_GET['qaction'] == 2) {
					$ai[0] = 1;
					mysql_query('START TRANSACTION') or QuestDie();
					UpdateQuestInfo($user,13,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
					1 => "Пришел просить у тебя кожу на шляпу для Лесоруба. Он так расстраивается, что свою потерял.",
					11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		} else {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $ai[0] == 11) {
					$mldiag = array(
						0 => "Да, вижу, нелегко тебе пришлось. Потрепали они тебя здорово, но надеюсь, ты им доставил еще больше хлопот! Хахахах!... Держи кожу, что ты просил. Лучшая, что у меня сегодня есть. Лесорубу хорошая шапка получится.",
						2 => "Спасибо, еще увидимся.",
					);
				} elseif ($_GET['qaction'] == 2 && $ai[0] == 11) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003044,"Охотник",0,array()) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Кожа</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 0;
					UpdateQuestInfo($user,13,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
				);
				if ($ai[0] == 11) {
					$mldiag[1] = "Да, разобрался с разбойниками. Теперь в лесу станет тише.";
				}
				$mldiag[11111] = "Нет, я еще не успел, пойду дальше.";
			}
		}
	}

	if ($sf == "mlpost" && QItemExists($user,3003043)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Вовремя ты подоспел. Почтовый дилижанс через 5 минут отправится, я еще успею туда добавить это письмо. Молодец, расторопный. Передай Рыцарю, что все будет отправлено в лучшем виде. И пусть заглядывает иногда, давно мы с ним по душам не болтали за бокалом хорошего вина.",
					2 => "Обязательно передам, удачи.",
				);
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItemTo($user,"Почтальон",QItemExistsID($user,3003043)) or QuestDie();

				$ai[1] = 2;
				UpdateQuestInfo($user,13,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Приветствую тебя, путник, на моей станции. Срочная почта или просто зашел?",
				1 => "Рыцарь просил тебе письмо срочное передать. Вот держи.",
				11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}		
	}

	if ($sf == "mlknight" && !QItemExists($user,3003046)) {
		if ($ai[1] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Скажу тебе, ты удачно пришел. На той неделе перебирал чердак и как-раз наткнулся на отрез отборного шелка. Еще сидел и думал, куда бы его приспособить. Только теперь бы мне вспомнить, куда я его засунул. Давай договоримся так – ты пока сбегай к Почтальону, отнеси от меня письмо, а я на чердаке покопаюсь. К твоему возвращению, думаю, найду отрез.",
						2 => "Хорошо, давай письмо, я скоро вернусь.",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003043,"Одинокий Рыцарь",0,array()) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Рыцарь передал вам <b>Письмо</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 1;
					UpdateQuestInfo($user,13,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам?",
					1 => "Пришел к тебе с просьбой. Лесоруб свою любимую шапку потерял, ему бы новую сшить, а то расстраивается сильно. Может у тебя на чердаке найдется хорошая ткань ему на подкладку?",
					11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		} else {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $ai[1] == 2) {
					$mldiag = array(
						0 => "Вот спасибо, ты мне здорово помог! А я для тебя нашел тот отрез шелка, что говорил. Вот держи. Лучшая ткань, сегодня уже такую не делают.",
						2 => "Спасибо, еще увидимся.",
					);
				} elseif ($_GET['qaction'] == 2 && $ai[1] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003046,"Одинокий Рыцарь",0,array()) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Рыцарь передал вам <b>Шелковая ткань</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 0;
					UpdateQuestInfo($user,13,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
				);
				if ($ai[1] == 2) {
					$mldiag[1] = "Да,  передал Почтальону твое письмо, сегодняшней почтой отправит. И просил передать, что  тебя в гости ждет на днях.";
				}
				$mldiag[11111] = "Нет, я еще не успел, пойду дальше.";
			}
		}
	}

?>