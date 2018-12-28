<?php
	// квест Помощь по конюшне 
	$q_status = array(
		0 => "Принести Конюху подковы (%N1%/1), деревянные спицы (%N2%/1) и сено (%N3%/1).",
		1 => "Принести Кузнецу 10 Осколков руды (%N1%/10)",
		2 => "Принести Лесорубу топор.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 19) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlhorse") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003069);
		$qi2 = QItemExistsID($user,3003070);
		$qi3 = QItemExistsID($user,3003072);

		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3);
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Вот спасибо! Хорошая работа! Теперь я наконец все приведу в порядок! А ты держи заслуженную награду. Да заглядывай почаще, может, еще работенка найдется.",
					2 => "Спасибо, еще загляну",
				);
			} elseif ($_GET['qaction'] == 66) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,150) or QuestDie();
				$m = AddQuestM($user,2,"Конюх") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();
	
				PutQItem($user,144144,"Конюх",0,$todel,255) or QuestDie();
	
				$msg = "<font color=red>Внимание!</font> Вы получили <b>Нападение «Разбойника»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
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
				1 => "Да, вот тебе спицы для колес, сено и новые подковы.",
				30000 => "Перейти к лошадям",
				66 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				11111 => "Нет, я еще не все сделал. Пойду дальше.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}		
	}

	if ($sf == "mlvillage" && $ai[0] < 2) {
		if ($ai[0] == 0 && (((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000)))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "А… ну это просьба не сложная, да только руда нужна для подков. Принесешь десяток осколков руды – сделаю тебе подковы за 5 минут.",
						2003 => "Хорошо, я принесу тебе все, что нужно.",
						2002 => "Нет, это слишком сложно, я пойду дальше по своим делам.",
					);
				} elseif ($_GET['qaction'] == 2003) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чем могу тебе помочь?",
					2001 => "Я к тебе от Конюха с просьбой. Подковы у лошадей сбились. Просит десяток новых.",
					2002 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
		if ($ai[0] == 1 && (((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000)))) {
			$mlqfound = false;
			$todel = QItemExistsCountID($user,3003005,10);
			if ($todel !== FALSE) {
				$mlqfound = true;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Быстро обернулся. Ну и я быстро свою работу сделаю. Держи свои подковы, да Конюху привет от меня передай.",
						2003 => "Спасибо, обязательно передам.",
					);
				} elseif ($_GET['qaction'] == 2003 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003069,"Кузнец",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Кузнец передал вам <b>Подкова</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 2;
					UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					2001 => "Да, вот 10 осколков руды, что ты просил.",
					2002 => "Нет, я еще не все собрал. Пойду дальше.",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}
		}
	}

	if ($sf == "mlwood" && $ai[1] < 2) {
		if ($ai[1] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Спицы нужны, говоришь? Могу, конечно. Да только топор совсем затупился. Сбегай к охраннику, он мне новый давно обещал, да все никак не передаст. Принесешь топор, и все быстро сделаем.",
						3 => "Хорошо, я принесу тебе топор.",
						2 => "Нет, это слишком сложно, я пойду дальше по своим делам. ",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[1] = 1;
					UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
					1 => "У Конюха спицы на почтовых дилижансах поломались. Просит сделать пару десятков, ты не поможешь?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}

		if ($ai[1] == 1) {
			$mlqfound = false;
			$todel = QItemExistsID($user,3003071);
			if ($todel !== FALSE) {
				$mlqfound = true;
			}

			$qi1 = QItemExistsID($user,3003012);

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Вот хороший топор, не обманул Охранник. Вот спасибо! Держи свои спицы для телег. Да, кстати, у меня сегодня день рождения, а выпить не с кем и нечего.  Была б бутылочка вина – угостил бы! Ну да ладно, бери спицы, иди по своим делам.",
						3 => "Спасибо, еще увидимся.",
					);
					if ($qi1 !== FALSE) {
						$mldiag[4] = "О, так у меня ж бутылочка вина с погребов Рыцаря с собой! Держи! Выпей на здоровье!";
					}
				} elseif ($_GET['qaction'] == 4 && $mlqfound && $qi1) {
					$mldiag = array(
						0 => "Вот спасибо! Очень вовремя! Порадовал в праздник! Неблагодарным не буду. Держи награду, заработал!",
						5 => "Спасибо, еще увидимся",
					);
				} elseif ($_GET['qaction'] == 5 && $mlqfound && $qi1) {
					mysql_query('START TRANSACTION') or QuestDie();

					$todel = array_merge($qi1,$todel);

					PutQItem($user,3003070,"Лесоруб",0,$todel) or QuestDie();

					PutQItem($user,105,"Лесоруб",7,array(),255,"shop",3) or QuestDie();
	
					$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b> за бонус квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 2;
					UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003070,"Лесоруб",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Лесоруб передал вам <b>Спицы</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 2;
					UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					1 => "Да, вот топор, что ты просил.",
					2 => "Нет, я еще не все сделал. Пойду дальше.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}
		}
	}

	if ($sf == "mlfort" && $ai[1] == 1 && !QItemExists($user,3003071)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ах, топор! Да, верно! Уже 2 недели валяется, отобрали у разбойников. Шикарный топор, алмазной стали и рубит не хуже чем меч. Волос разрубает на лету. Держи, передай ему, да не потеряй	по дороге.",
					3 => "Спасибо, передам.",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003071,"Лесоруб",0,array()) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				

			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Проходи путник, не задерживайся. Тут сторожевая башня, а не трактир. Тут все серьезно и по делу. Ну что встал как столб? Проходи, говорю!",
				1 => "Я к тебе с просьбой от Лесоруба. Говорит, ты ему давно топор новый обещал, да никак передать не можешь.",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}		
	}

	if ($sf == "mlknight" && $ai[2] == 0) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Сено? Да, не жалко! В том году столько запасли, что я уж думал куда девать, чтоб не сгнило. Заходи, забирай сколько увезти сможешь. А я тебе на дорогу вина хорошего бутылочку дам. День сегодня добрый, хочется и другим настроение поднять.",
					3 => "Спасибо. А за вино – премного благодарен. Загляну как-нибудь еще, посидим, поговорим за жизнь.",
				);
			} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003072,"Рыцарь") or QuestDie();
					PutQItem($user,3003012,"Рыцарь") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Рыцарь передал вам <b>Сено</b> и <b>Вино</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[2] = 2;
					UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам?",
				1 => "Я по делу к тебе. Конюх порядок у себя в конюшнях наводит и жалуется, что сена не хватает. Может, ты на своей конюшне избыток имеешь?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}			
	}

	if ($sf == "mlwood" && $ai[1] == 2 && QItemExists($user,3003012)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Вот спасибо! Очень вовремя! Порадовал в праздник! Неблагодарным не буду. Держи награду, заработал!",
					3 => "Спасибо, еще увидимся",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				$todel = QItemExistsID($user,3003012);

				PutQItem($user,105,"Лесоруб",7,$todel,255,"shop",3) or QuestDie();
	
				$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b> за бонус квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[1] = 3;
				UpdateQuestInfo($user,19,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, может ты принес мне вино?",
				1 => "Да, держи бутылочку вина с погребов Рыцаря! Выпей на здоровье!",
				2 => "Нет, просто мимо проходил.",
			);
		}		
	}
?>