<?php
	// квест создание амулета
	$q_status = array(
		0 => "Выполнить просьбу Пилигрима и помочь Ведьме.",
		1 => "Принести ведьме 10 крыльев летучей мыши (%N1%/10), 5 капель волчьей крови (%N2%/5), 5 крысиных хвостов (%N3%/5), 10 штук болотного папоротника (%N4%/10) и Зелье Кровавой Луны (%N5%/1).",
		2 => "Вернуть амулет к Магу или к Пилигриму.",
		3 => "Вернуть амулет Пилигриму.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 16) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlpiligrim") {
		$mlqfound = false;
		$todel = QItemExistsID($user,3003057);
		if ($todel !== FALSE) {
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} elseif ($_GET['qaction'] == 4 && ($step == 2 || $step == 3) && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,250) or QuestDie();
				$m = AddQuestM($user,2,"Пилигрим") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,144144,"Пилигрим",0,$todel,255) or QuestDie();
				PutQItem($user,105,"Пилигрим") or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Нападение «Разбойника»</b> и <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();
			} elseif ($_GET['qaction'] == 3 && ($step == 2 || $step == 3) && $mlqfound) {
				$mldiag = array(
					0 => "Вот это другое дело! Этого я и ждал! Держи заслуженную награду!",
					4 => "Спасибо, еще увидимся",
				);
			} elseif ($_GET['qaction'] == 2 && ($step == 2 || $step == 3) && $mlqfound) {
				$mldiag = array(
					0 => "Молодец! А что ж Ведьма, ничего для меня не передала?",
					3 => "Как же не передала?!. Вот амулет для тебя, просила передать.",
				);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты сделал то, что я просил?",
				1 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				2 => "Да, я помог Ведьме и все сделал, что она просила.",
				11111 => "Нет, я просто проходил мимо.",
			);
			if (($step != 2 && $step != 3) || !$mlqfound) unset($mldiag[2]);
		}
	}

	if ($sf == "mlwitch") {
		if ($step == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Вот это хорошая новость! Давно ждала этого известия! Ну что ж, давай приниматься за дело, раз уж ты пришел мне помочь. Мне нужно 10 крыльев летучих мышей, 5 капель волчьей крови, 5 крысиных хвостов, 10 листьев болотного папоротника и  Зелье Кровавой Луны. Волков ты найдешь в лесу, а летучих мышей последнее время много видели около Замка Рыцаря.",
						3 => "Хорошо, я принесу то, что ты просишь.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,16,1) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края? ",
					1 => "Пилигрим просил передать, что послезавтра новолуние, самое удачное время для изготовления амулетов, и просил тебе помочь, если нужно.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
		if ($step == 1) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003058,10);
			$qi2 = QItemExistsCountID($user,3003038,5);
			$qi3 = QItemExistsCountID($user,3003002,5);
			$qi4 = QItemExistsCountID($user,3003059,10);
			$qi5 = QItemExistsID($user,3003061);
			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE && $qi4 !== FALSE && $qi5 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3,$qi4,$qi5);
			}


			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Вот и хорошо. Теперь я могу приступать к изготовлению. Можешь передать Пилигриму, что он прислал ко мне хорошего работника. Да, возьми один из амулетов, передашь Пилигриму.",
						3 => "Спасибо, еще увидимся.",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003057,"Ведьма",0,$todel) or QuestDie();;
	
					addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Амулет</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					SetQuestStep($user,16,2) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просила?",
					1 => "Да, вот  крылья летучих мышей, кровь волков, крысиные хвосты, болотный папоротник и Зелье.",
					2 => "Нет, я еще не все нашел. Пойду дальше.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}
		}
	}

	if ($sf == "mlmage" && $step == 1 && !QItemExists($user,3003061)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Хорошая весть. Я уже заждался, когда она, наконец, это сделает. Дам тебе Зелье, но с условием. Принесешь мне один из амулетов от Ведьмы. А если не принесешь – пеняй на себя – неудачи будут преследовать тебя еще долго.",
					3 => "Хорошо, я принесу тебе амулет.",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003061,"Маг") or QuestDie();;

				addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Зелье Кровавой Луны</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();

			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Не часто меня гости беспокоят.  Зачем пришел и по какому делу?  Говори.",
				1 => "Я пришел к тебе с просьбой от Болотной Ведьмы. Просит Зелье Кровавой Луны для создания амулетов. Сейчас благоприятное время по словам Пилиргима.",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}	
	}

	if ($sf == "mlmage" && $step == 2) {
		$mlqfound = false;
		$todel = QItemExistsID($user,3003057);
		if ($todel !== FALSE) {
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Молодец, держишь слово. Смотрю, на тебя можно положиться, хотя обычно людишки такими верными не бывают. Ну что ж, держи за честность награду и небольшой подарок от меня – если у Мага есть один амулет, то он может сделать из него два! Один мне, другой тебе! Держи такой-же амулет – убережет тебя от несчастий на долгие годы.",
					3 => "Спасибо, еще увидимся",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,667667,"Маг") or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Зелье Старого Мага</b> за бонус квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				SetQuestStep($user,16,3) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA(); 
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
				1 => "Да, вот амулет, который ты просил.",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				2 => "Нет, я еще не все нашел. Пойду дальше.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}	
	}
?>