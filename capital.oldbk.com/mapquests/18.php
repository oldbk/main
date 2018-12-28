<?php
	// квест Семейный секрет
	$q_status = array(
		0 => "Принести Кузнецу 10 Осколков руды (%N1%/10), 2 Капли крови дракона (%N2%/2), Чистейшей речной воды (%N3%/1) и Драгоценных камней (%N4%/1).",
		1 => "Сходить к Разбойникам за Драгоценными Камнями и вернуться к Скупщику.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 18) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlvillage") {
		if (((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003005,10);
			$qi2 = QItemExistsCountID($user,3003067,2);
			$qi3 = QItemExistsID($user,3003034);
			$qi4 = QItemExistsID($user,3003050);
			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE && $qi4 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3,$qi4);
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Вот молодец, успел как-раз вовремя! Полнолуние только началось и самое время приступать к работе. Не стой тут, никому нельзя видеть секрет изготовления этого меча. Держи свою награду и ступай с миром. А я займусь делом.",
						2002 => "Спасибо, еще увидимся",
					);
				} elseif ($_GET['qaction'] == 2066) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} elseif ($_GET['qaction'] == 2002 && $mlqfound) {
					// награда
					mysql_query('START TRANSACTION') or QuestDie();
	
					$r = AddQuestRep($user,200) or QuestDie();
					$m = AddQuestM($user,1,"Кузнец") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
	
					PutQItem($user,15564,"Кузнец",0,$todel,255,"eshop") or QuestDie();

					$msg = "<font color=red>Внимание!</font> Вы получили <b>Осколок статуи Исчадия Хаоса</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

	
					UnsetQuest($user) or QuestDie();
					UnsetQA();
					mysql_query('COMMIT') or QuestDie();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					2001 => "Да, вот драгоценные камни, капля чистейшей речной воды, кровь дракона и руда.",
					2066 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					2002 => "Нет, я еще не все нашел. Пойду дальше.",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}			
		}		
	}

	if ($sf == "mlboat") {
		$mlqfound = QItemExists($user,3003050);

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ну, конечно, кто как не лодочник может достать самой лучшей речной водицы. На середине реки она самая чистая и холодная. Но одному мне туда плыть неохота. Заплати мне за переправу, там и наберем вместе.",
				);

				if ($user['money'] >= 1) {
					$mldiag[3] = "Хорошо, держи 1 кр., и поплыли.";
				} else {
					$mldiag[11111] = "Извини, но денег нет.";
				}
			} elseif ($_GET['qaction'] == 3 && $mlqfound === FALSE) {
				// переправляемся
				mysql_query('START TRANSACTION') or QuestDie();
				$rec = array();
				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']-1;
				$rec['target']=0;
				$rec['target_login']="Лодочник";
				$rec['type']=252; // плата квестовому боту
				$rec['sum_kr']=1;
				add_to_new_delo($rec) or QuestDie();

				PutQItem($user,3003050,"Лодочник");

				addchp ('<font color=red>Внимание!</font> Вы получили <b>Чистая речная вода</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				if ($user['room'] == ($maprel+$maprelall+1)) {
					mysql_query('UPDATE oldbk.users SET money = money - 1, room = '.($maprel+$maprelall+2).' WHERE id = '.$user['id']) or QuestDie();
				} elseif ($user['room'] == ($maprel+$maprelall+2)) {
					mysql_query('UPDATE oldbk.users SET money = money - 1, room = '.($maprel+$maprelall+1).' WHERE id = '.$user['id']) or QuestDie();
				}
				mysql_query('COMMIT') or QuestDie();
				echo '<script>location.href="mlboat.php";</script>';
				Redirect("mlboat.php");
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Переправа недорогая. Заплати 1 кредит, и поехали.",
					33333 => "Заплатить 1 кредит.",
					11111 => "Попрощаться и уйти.",
				);			
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая. ",
				1 => "Мне не нужно переправляться. У меня к тебе просьба. Нужно мне немного чистейшей речной воды, но у берега такой не достать. Может, наберешь мне с середины реки немного?",
				2 => "Мне бы переправиться на ту сторону. Сколько это будет стоить?",
				11111 => "Не надо, я просто проходил мимо. Пойду дальше.",
			);
			if ($mlqfound !== FALSE) unset($mldiag[1]);
		}
	}

	if ($sf == "mlbuyer") {
		if (!$questexist['addinfo']) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Камушки, говоришь? Да, было несколько. Я и рад бы был оказать тебе такую услугу, да украли их у меня давеча разбойники. Прятал-прятал я мешочек с камушками, но не уберег. Коли сможешь забрать их у разбойников, половину тебе отдам.",
						2 => "Хорошо, я верну тебе твои камушки.",
						3 => "Нет, это слишком сложно, я пойду дальше по своим делам.",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();				
					UpdateQuestInfo($user,18,"1") or QuestDie();	
					mysql_query('COMMIT') or QuestDie();			
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Проходи, проходи. Зачем в гости пожаловал?",
					33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
					1 => "Камушки ищу драгоценные, говорят у тебя можно найти. Не поможешь?",
					11111 => "Ничего особенного, просто проходил мимо. Пойду дальше."
				);
			}
		} elseif ($questexist['addinfo'] == 1) {
			$mlqfound = false;
			$todel =  QItemExistsID($user,3003068);
			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Да, они самые! Ну, что ж, уговор дороже денег. Держи половину камней, да только помни на будущее, я не всегда так щедр на подарки, за все надо платить.",
						2 => "Спасибо, еще увидимся."
					);
				} elseif ($_GET['qaction'] == 2 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003034,"Скупщик",0,$todel) or QuestDie();

					UpdateQuestInfo($user,18,"2") or QuestDie();	
	
					addchp ('<font color=red>Внимание!</font> Скупщик передал вам <b>Драгоценные камни</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();

				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес то, что я просил?",
					1 => "Да, вот твой мешочек с драгоценными камнями. Ты обещал мне половину.",
					11111 => "Нет, я еще не все сделал. Пойду дальше.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}
		} else {
			$mldiag = array(
				0 => "Проходи, проходи. Зачем в гости пожаловал?",
				33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
				11111 => "Ничего особенного, просто проходил мимо. Пойду дальше."
			);
		}
	}

	if ($sf == "mlrouge" && $questexist['addinfo'] == 1 && !QItemExists($user,3003068)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ох, ты, какой непуганый да говорливый к нам пожаловал! Миром у нас еще никто ничего не забирал. Да и войной редко у кого получалось. Ну, раз ты такой крутой, пеняй на себя!",
					3 => "Ничего, посмотрим кто кого.",
					2 => "Хм.. я, пожалуй, пойду…",
				);	
			} elseif ($_GET['qaction'] == 3) {
				// стартуем бой с крысами
				mysql_query('START TRANSACTION') or QuestDie();
				StartQuestBattleCount($user,535, mt_rand(2,3)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				Redirect("fbattle.php");
				unsetQA();			
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Смотрите-ка, к нам гость из города! Сам пришел, да вряд ли сам уйдет.  И что же занесло тебя в наш лес?",
				1 => "Не из пугливых я. А пришел я к вам забрать то, что вам не принадлежит. Камушки драгоценные вами украдены недавно. Или миром вернете или войной заберу.",
				2 => "Ничего, просто мимо проходил. Извини.",
			);
		}	
	}
?>