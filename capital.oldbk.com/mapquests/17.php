<?php
	// квест Цветные сердца
	$q_status = array(
		0 => "Помочь Магу собрать Сердце Дракона (%N1%/1), Сердце Орла (%N2%/1), Сердце Летучей мыши (%N3%/1), Эликсир Вечной Молодости (%N4%/1) и Пшеничных зёрен (%N5%/1).",
		1 => "Сходить к Священнику и рассказать про Мага",
		2 => "Сходить к Ведьме и рассказать про молитву",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 17) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlmage") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003062);
		$qi2 = QItemExistsID($user,3003063);
		$qi3 = QItemExistsID($user,3003064);
		$qi4 = QItemExistsID($user,3003065);
		$qi5 = QItemExistsID($user,3003066);
		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE && $qi4 !== FALSE && $qi5 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3,$qi4,$qi5);
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Вот и хорошо. Давай все сюда и ступай себе с наградой. А у меня мало времени и много дел, не мешайся под ногами.",
					3 => "Спасибо, еще увидимся.",
					4 => "Подожди, у меня к тебе еще сообщение от Пилигрима!",
				);
			} elseif ($_GET['qaction'] == 4 && $mlqfound) {
				$mldiag = array(
					0 => "Сообщение? Ну что-ж, говори, но учти, если ты зря отнимешь мое время, тебе это дорого обойдется. Каждая минута сейчас на вес золота!",
					5 => "Он просил передать тебе привет и сказать, что Духи рассказали ему, что год нынче высокосный и зерен должно быть на 1 больше, чем обычно.",
					3 => "Не буду занимать твое время, я лучше пойду",
				);
			} elseif ($_GET['qaction'] == 5 && $mlqfound) {
				$mldiag = array(
					0 => "Хм.. на 1 больше? А ты ничего не путаешь? Ну хорошо… Это полезная весть. Держи награду за работу и еще добавок за обязательность. Хм… на 1 больше… Ладно, иди уже, мне некогда!",
					6 => "Спасибо, еще увидимся",
				);
			} elseif ($_GET['qaction'] == 66) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} elseif ($_GET['qaction'] == 6 && $mlqfound) {
				// награда больше
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,200) or QuestDie();
				$m = AddQuestM($user,1,"Маг") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				if ($user['level'] == 6) {
					$item = 5202;
					$txt = "90";
				} elseif ($user['level'] == 7) {
					$item = 5202;
					$txt = "90";
				} elseif ($user['level'] == 8) {
					$item = 5205;
					$txt = "180";
				} elseif ($user['level'] == 9) {
					$item = 5205;
					$txt = "180";
				} else {
					$item = 5205;
					$txt = "180";
				}

				PutQItem($user,$item,"Маг",0,$todel,255,"eshop") or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Большой свиток «Восстановление ".$txt."HP»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// награда меньше
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,100) or QuestDie();
				$m = AddQuestM($user,1,"Маг") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				if ($user['level'] == 6) {
					$item = 5202;
					$txt = "90";
				} elseif ($user['level'] == 7) {
					$item = 5202;
					$txt = "90";
				} elseif ($user['level'] == 8) {
					$item = 5205;
					$txt = "180";
				} elseif ($user['level'] == 9) {
					$item = 5205;
					$txt = "180";
				} else {
					$item = 5205;
					$txt = "180";
				}

				PutQItem($user,$item,"Маг",0,$todel,255,"eshop") or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Большой свиток «Восстановление ".$txt."HP»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
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
				1 => "Да, вот тебе три сердца, Эликсир Вечной Молодости и связка пшеничных зерен.",
				66 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				2 => "Нет, я еще не все нашел. Пойду дальше.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}
	}

	if ($sf == "mlwitch") {
		if (!$questexist['addinfo']) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Хм… Да, кажется я знаю, что за обряд он собирается провести. Хитрый жук.. Небось просил тебя еще разные сердца добыть? Вечно он чужими руками все делает. Эликсир я бы тебе дала, да услуга за услугу. Сходи-ка ты к Священнику, да расскажи все, что знаешь. Он будет знать, что делать. А, как вернешься, получишь свой Эликсир.",
						3 => "Хорошо, я сделаю то, что ты просишь.",
						2 => "Нет, это слишком сложно для меня.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();				
					UpdateQuestInfo($user,17,"1") or QuestDie();	
					mysql_query('COMMIT') or QuestDie();			
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края?",
					1 => "Я к тебе с просьбой от Мага. Он собирается провести старинный магический обряд и просит у тебя Эликсир Вечной Молодости.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}			
		} elseif ($questexist['addinfo'] == 1) {
			$mldiag = array(
				0 => "Привет, ты сделал то, что я просила?",
				11111 => "Нет, я еще не все сделал. Пойду дальше.",
			);
		} elseif ($questexist['addinfo'] == 2) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Вот теперь мне спокойнее. Не получится у Мага, то, что он задумал. Теперь можешь спокойно нести ему Эликсир, все равно он ему мало чем поможет.",
						2 => "Спасибо, еще увидимся",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003065,"Ведьма") or QuestDie();;
	
					addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Эликсир Вечной Молодости</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					UpdateQuestInfo($user,17,"3") or QuestDie();	
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просила?",
					1 => "Да, я предупредил Священника и он пошел собирать паству на молитву.",
					11111 => "Нет, я еще не все сделал. Пойду дальше.",
				);
			}		
		}
	}

	if ($sf == "mlvillage" && $questexist['addinfo'] == 1) {
		if (((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 1000 && $_GET['qaction'] < 2000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1001) {
					$mldiag = array(
						0 => "Ох, ты, Господи! Этого я и опасался, чувствовал, что надвигается напасть, но что такая – даже не предполагал. И Ведьма, хоть и Болотная, но все же остались в ней капли человечности, как видно. Спасибо тебе за известие. Пойду бить в колокола и собирать паству на срочную молитву. Верю я, что наши мольбы не дадут свершиться несправедливости. Ступай с Богом, у меня осталось мало времени, но вера наша крепка и она нас защитит.",
						1003 => "Хорошо, еще увидимся.",
					);
				} elseif ($_GET['qaction'] == 1003) {
					mysql_query('START TRANSACTION') or QuestDie();				
					UpdateQuestInfo($user,17,"2") or QuestDie();				
					mysql_query('COMMIT') or QuestDie();			
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую тебя, путник, в нашей скромной обители. Пришел ли ты просить Господа о благодати и отпущении грехов или помолиться со мной о душах других грешников?",
					1001 => "Святой Отец, я пришел к тебе с известием о том, что Маг сегодня собирается провести какой-то древний обряд с разноцветными сердцами. Ведьма сказала, что ты будешь знать что делать.",
					1002 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlpiligrim" && !QItemExists($user,3003066)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Связку зерен? Погоди-погоди, а сердца он тебя принести не просил? Да можешь не отвечать… Вот хитрый старый жук… Так я и знал, что он не пропустит этот день. Ну что-ж… Ссорится с магом – себе дороже. Дам я тебе связку зерен, но и ты сделай для меня кое-что. Вместе со связкой, передай ему от меня привет и скажи что Духи рассказали мне о том, что год нынче високосный и зерен должно быть на 1 больше, чем обычно.",
					2 => "Хорошо, я сделаю, как ты сказал.",
				);
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003066,"Пилигрим") or QuestDie();;
	
				addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Пшеничные зёрна</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();			
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
				1 => "Я к тебе с просьбой от Мага. Просит связку пшеничных зерен для какого-то обряда.",
				3 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}		
	}
?>