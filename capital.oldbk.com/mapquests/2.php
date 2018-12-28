<?php
	// квест бабушкин пирог
	$q_status = array(
		0 => "Принести трактирщику противень (%N1%/1), фруктовое варенье (%N2%/1) и особенной травки «утешение желудка» (%N3%/1).",
		1 => "Принести кузнецу 4 осколка руды. (%N1%/4)",
		2 => "Принести Ведьме 2 хвороста. (%N1%/2)",
		3 => "Отнести письмо Пилигрима к Почтальону и вернуться за наградой.",
	);
	

	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 2) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlvillage") {
		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			// трактирщик
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003006); // протвень
			$qi2 = QItemExistsID($user,3003008); // травка
			$qi3 = QItemExistsID($user,3003010); // варенье
			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3);
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Ах, как я рад! Ты принес все, что мне нужно, и теперь я испеку самый вкусный в мире пирог! Позволь тебя отблагодарить за помощь. Ты наверняка проголодаешься в дороге, я упакую тебе несколько моих бутербродов.",
						5 => "Получить награду",
					);
				} elseif ($_GET['qaction'] == 5 && $mlqfound) {
					// награда
					mysql_query('START TRANSACTION') or QuestDie();

					$r = AddQuestRep($user,250) or QuestDie();
					$m = AddQuestM($user,2,"Трактирщик") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();

					if ($user['level'] == 6) {
						$item = 33029;
					} elseif ($user['level'] == 7) {
						$item = 33030;
					} else {
						$item = 33031;
					}

	
					PutQItem($user,$item,"Трактирщик",0,$todel,255) or QuestDie();
	
					$msg = "<font color=red>Внимание!</font> Вы получили <b>Обед воина</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					UnsetQuest($user) or QuestDie();
					UnsetQA();
					mysql_query('COMMIT') or QuestDie();
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();					
					UnsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					UnsetQA();			
				} else {
					UnsetQA();
				}
				
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
				);
				if ($mlqfound) $mldiag[1] = "Да, вот все, что нужно для пирога.";

				$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
				$mldiag[3] = "Нет, я еще не все собрал. Пойду дальше.";
			}
		}

		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			// кузнец
			if (QItemExists($user,3003006)) {
				if (isset($_GET['qaction'])) unsetQA();
				return;
			}

			$ai = $questexist['addinfo'];
			$ai = explode("/",$ai);

			$mlqfound = false;
			$todel = QItemExistsCountID($user,3003005,4);

			if ($todel !== FALSE) {
				$mlqfound = true;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "Опять ему нужен противень? Интересно, что он с ними делает? Буквально неделю назад он уже присылал ко мне человека. Подозреваю, что он их гнет на спор об голову своего Вышибалы. Принеси мне 4 осколка руды с рудника, и я сделаю ему новый.",
						2100 => "Хорошо, я принесу тебе все, что нужно.",
						11111 => "Нет, это слишком сложно, я пойду дальше по своим делам.",
					);
				} elseif ($_GET['qaction'] == 2003 && $mlqfound) {
					$mldiag = array(
						0 => "Ты быстро управился.  Видно, что Трактирщик нашел правильного человека для своей просьбы. Держи противень.",
						2200 => "Забрать противень.",
					);
				} elseif ($_GET['qaction'] == 2200 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();					
					PutQItem($user,3003006,"Кузнец",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Кузнец передал вам <b>Противень</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 0;
					UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} elseif ($_GET['qaction'] == 2100) {
					$ai[0] = 1;
					mysql_query('START TRANSACTION') or QuestDie();					
					UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();					
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				if ($ai[0] == 0) {
					$mldiag = array(
						0 => "Привет, чем могу тебе помочь?",
						2001 => "Трактирщик послал меня к тебе за противнем.",
						11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
					);
				} elseif ($ai[0] == 1) {
					$mldiag = array(
						0 => "Привет, ты принес мне то, что я просил?",
					);				
					if ($mlqfound) $mldiag[2003] = "Да, вот 4 осколка руды, что ты просил.";
					$mldiag[2004] = "Нет, я еще не все собрал. Пойду дальше.";
				}
			}
		}
	}

	if ($sf == "mlwitch") {
		if (QItemExists($user,3003008)) {
			if (isset($_GET['qaction'])) unsetQA();
			return;
		}

		$ai = $questexist['addinfo'];
		$ai = explode("/",$ai);

		$mlqfound = false;
		$todel = QItemExistsCountID($user,3003007,2);
		if ($todel !== FALSE) {
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Да, есть у меня такая, но, чтоб получить ее выполни мою маленькую просьбу. По ночам становится холодно, а топить мне нечем. Будь добр, принеси мне пару вязанок хвороста с Дерева Жизни, что растет у Драконьего логова. Только не тащи ничего другого, лишь этот хворост горит долго и дает много жара.",
					3 => "Хорошо, я принесу тебе все, что нужно.",
					4 => "Нет, это слишком сложно, я пойду дальше по своим делам.",
				);
			} elseif ($_GET['qaction'] == 10 && $mlqfound) {
				$mldiag = array(
					0 => "Принес хворост? Вот молодец! Теперь мне будет, чем топить и я перестану мерзнуть. Держи свою травку, ты ее честно заслужил. Удачи тебе на пути!",
					12 => "Забрать травку «Утешение желудка»",
				);
			} elseif ($_GET['qaction'] == 12 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003008,"Ведьма",0,$todel) or QuestDie();

				addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Травка «Утешение желудка»</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[1] = 0;
				UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} elseif ($_GET['qaction'] == 3) {
				$ai[1] = 1;
				mysql_query('START TRANSACTION') or QuestDie();
				UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			if ($ai[1] == 0) {
				$mldiag = array(
					0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края?",
					1 => "Трактирщик просит травку «Утешение желудка» для своего любимого пирога.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			} elseif ($ai[1] == 1) {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просила?",
				);
				if ($mlqfound) $mldiag[10] = "Да, вот хворост с Дерева Жизни у Драконьего логова.";
				$mldiag[11] = "Нет, я еще не все собрал. Пойду дальше.";
			}
		}
	}

	if ($sf == "mlpiligrim") {
		if (QItemExists($user,3003010)) {
			if (isset($_GET['qaction'])) unsetQA();
			return;
		}

		$ai = $questexist['addinfo'];
		$ai = explode("/",$ai);

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Да, Трактирщик знает у кого просить. Никто, кроме меня не умеет варить такое вкусное варенье. Слушай, сделай доброе дело? Что-то последнее время Почтальон редко стал ко мне захаживать, а у меня есть срочное письмо. Отнеси его Почтальону и возвращайся, я как-раз успею приготовить для тебя варенье. ",
					3 => "Хорошо, я сделаю то, что ты просишь.",
					4 => "Нет, это слишком сложно, я пойду дальше по своим делам.",
				);
			} elseif ($_GET['qaction'] == 3 && $ai[2] == 0) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003009,"Пилигрим",0) or QuestDie();

				addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Письмо</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[2] = 1;
				UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} elseif ($_GET['qaction'] == 5 && $ai[2] == 2) {
				$mldiag = array(
					0 => "Спасибо тебе за помощь. Зуб даю, что Почтальон ворчал как старая бабка, верно? Не страшно, главное, что мое письмо будет доставлено вовремя. Держи варенье и неси его Трактирщику. Только не открывай банку по дороге, а то испортится.",
					7 => "Забрать варенье",
				);
			} elseif ($_GET['qaction'] == 7 && $ai[2] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003010,"Пилигрим",0) or QuestDie();

				addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Варенье</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[2] = 0;
				UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} else {
				unsetQA();
			}
		} else {
			if ($ai[2] == 0) {
				$mldiag = array(
					0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
					1 => "Трактирщик просил принести ему варенье от тебя.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			} elseif ($ai[2] == 1 || $ai[2] == 2) {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
				);
	
				if ($ai[2] == 2) $mldiag[5] = "Да, твое письмо уже у Почтальона.";

				$mldiag[6] = "Нет, я еще не все сделал. Пойду дальше.";
			}
		}
	}

	if ($sf == "mlpost") {
		$ai = $questexist['addinfo'];
		$ai = explode("/",$ai);

		$mlqfound = false;
		$todel = QItemExistsID($user,3003009);
		if ($todel !== FALSE) {
			$mlqfound = true;
		}
	
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == "1" && $mlqfound) {
				$mldiag = array(
					0 => "Ну надо-же, а я завтра собирался к нему за почтой. Никогда у него нет терпения дождаться моего прихода, вечно ищет с кем передать мне свои письма. Все у него срочно-срочно-срочно. Можно подумать, что мир обрушится от одного дня ожидания. Давай письмо, раз уж пришел.",
					3 => "Отдать письмо.",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItemTo($user,"Почтальон",$todel) or QuestDie();

				$ai[2] = 2;
				UpdateQuestInfo($user,2,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();							
			} else {
				unsetQA();
			}
		} else {
			if ($ai[2] == 1) {
				$mldiag = array(
					0 => "Приветствую тебя, путник, на моей станции. Срочная почта или просто зашел?",
				);

		        	if ($mlqfound) $mldiag[1] = "Пилигрим передал для тебя письмо срочное.";
				$mldiag[2] = "Ничего особенного, просто проходил мимо. Пойду дальше.";
			}
		}
	}
?>