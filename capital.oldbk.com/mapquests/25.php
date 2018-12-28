<?php
	// квест Праздничные приготовления
	$q_status = array(
		0 => "Передать приглашения Почтальону",
		1 => "Отнести приглашение Кузнецу",
		2 => "Отнести приглашение Трактирщику",
		3 => "Отнести приглашение Лесорубу",
		4 => "Отнести приглашение Охраннику",
		5 => "Убедиться что почтальон выполнил свою часть работы",
		6 => "Вернуться к Магу",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 25) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");
	
	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlmage") {
		$mlqfound = false;

		if ($step == 2) {
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				$mldiag = array(
					0 => "Сказочно. В этом году я приготовил кое-что… особенное. Хе-хе-кхе-кхе…",
					3 => "Ага, а что это за тринадцать черепов ты расставил вокруг стола? Впрочем, нет, я не хочу знать.",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,150) or QuestDie();
				$m = AddQuestM($user,1,"Маг") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,144144,"Маг",0,$todel,255) or QuestDie();

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
				0 => "Привет, ты сделал то, что я просил?",
				1 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				2 => "Конечно! Все приглашения доставлены, все приглашенные – обещали обязательно прийти. ",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				11111 => "Пока что нет, мне нужно ещё время.",
			);
			if (!$mlqfound) unset($mldiag[2]);
		}	
	}

	if ($sf == "mlpost") {
		if ($step == 0 && QItemExists($user,3003087)) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Тринадцать гостей? Хм. Ну, давай взглянем на те приглашения. Приглашение пилигриму, приглашение охраннику, кузнецу тоже и лесорубу. Приглашение трактирщику… Этот старый фокусник похоже решил собрать в одном месте всех кого знает.",
						3 => "Да уж, наверное, никого не забыл.",
					);
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						0 => "Ведьминого приглашения нет, но оно и не удивительно… Но даже на самом быстром коне – я не успею вовремя объехать всех.",
						4 => "Но маг очень рассчитывает на тебя и я бы не хотел проверять какой будет его реакция, если кто-то не придет на его праздник!",
					);
				} elseif ($_GET['qaction'] == 4) {
					$mldiag = array(
						0 => "Твоя правда, с магом шутить не стоит. Давай поступим вот как. Я возьму часть приглашений, а другую часть возьмешь ты, вместе мы точно уложимся в нужное время! А все-таки забавно что этот старый хрыч не пригласил Ведьму… Ох и разозлится она, если узнает, как пить дать разозлится! Все бы отдал что бы увидеть выражение её лица в этот момент. Хотя сам сообщить ей такие новости я точно не рискну.",
						5 => "С Ведьмой-то маг всяко нехорошо поступил, но вот насчет писем… Вообще-то я не должен выполнять твою работу. Но мага огорчать мне тоже не с руки. Не переживу если увижу как это сморщенное лицо становится ещё более сморщенным принимая грустное выражение.",
					);
				} elseif ($_GET['qaction'] == 5) {
					$mldiag = array(
						0 => "Вот и славно. Вот твоя часть писем.",
						6 => "Не будем терять ни минуты. Пока!",
					);
				} elseif ($_GET['qaction'] == 6) {
					mysql_query('START TRANSACTION') or QuestDie();				
					SetQuestStep($user,25,1);	
					PutQItem($user,3003088,"Почтальон",0,QItemExistsID($user,3003087)) or QuestDie();
					PutQItem($user,3003088,"Почтальон") or QuestDie();
					PutQItem($user,3003088,"Почтальон") or QuestDie();
					PutQItem($user,3003088,"Почтальон") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Почтальон передал вам <b>Приглашение</b> (x4) ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
	
					mysql_query('COMMIT') or QuestDie();			
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую тебя, путник, на моей станции. Срочная почта или просто зашел?",
					1 => "Сверхважное поручение у меня, от Мага, пачку приглашений передал, да наказал, что бы ты их всенепременно доставил в срок. У него там какие-то заморочки с тринадцатью гостями.",
					11111 => "Ничего особенного, ещё увидимся!",
				);
			}	
		}
		if ($step == 1) {
			$mlqfound = false;
			if ($ai[0] == 1 && $ai[1] == 1 && $ai[2] == 1 && $ai[3] == 1) {
				$mlqfound = true;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Я как раз только что вернулся. Все приглашения доставлены к их адресатам.",
						3 => "Вот и отлично, пойду, расскажу об этом магу. Пока!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,25,2);
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
					1 => "Да, я разнес свою часть писем. А как твои успехи?",
					11111 => "Ещё нет, скоро вернусь!",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		}
	}

	if ($sf == "mlvillage") {
		if ($step == 1 && $ai[0] == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003088,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Ох, да неужели? Будто у меня своих дел мало. Хотя сердить этого мухомора беззубого было бы не самым верным решением. Ладно, раз позвал, придется идти…",
						2004 => "Там и увидимся. Пока!",
					);
				} elseif ($_GET['qaction'] == 2004 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,25,implode("/",$ai)) or QuestDie();
					PutQItemTo($user,"Кузнец",$todel) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чем могу тебе помочь?",
					2001 => "Маг приглашает тебя на празднование своего Дня Рождения!",
					11111 => "Да я просто так заглянул, уже ухожу.",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}
		}

		if ($step == 1 && $ai[1] == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003088,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Восхитительно! Этот старикашка вспоминает, что кроме него на свете есть и другие люди, только когда ему что-нибудь нужно! Вина захотел, ишь ты. Хватит с него и самогонки из боярышника. Авось он ею и подавится…",
						3 => "Эм… ну как бы там ни было – увидимся на вечеринке. Пока!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[1] = 1;
					UpdateQuestInfo($user,25,implode("/",$ai)) or QuestDie();
					PutQItemTo($user,"Трактирщик",$todel) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "У меня для тебя приглашение от Мага. Он будет счастлив видеть тебя и твое лучшее вино на праздновании своего Дня Рождения!",
					11111 => "Кажется дверью ошибся.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	

		}
	}

	if ($sf == "mlwood") {
		if ($step == 1 && $ai[2] == 0) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003088,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "День Рождения? У мага? Уф… На последнем его Дне Рождении мой дед бабку мою встретил… Давненько то было. Схожу уж, отчего бы и нет. Будет что внукам рассказать.",
						3 => "Значит там и встретимся! Пока!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[2] = 1;
					UpdateQuestInfo($user,25,implode("/",$ai)) or QuestDie();
					PutQItemTo($user,"Лесоруб",$todel) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
					1 => "По поручению мага я пришел. Он просит передать тебе приглашение на его День Рождения! ",
					11111 => "Просто поздороваться зашел. Уже ухожу.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	

		}
	}

	if ($sf == "mlfort") {
		if ($step == 1 && $ai[3] == 0) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003088,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Ох, как же мне свой пост оставить? Никак нельзя, не по уставу. Хотя с другой стороны… Однако обижать единственного и при том весьма злопамятного поставщика защитных амулетов тоже не стоит. Ладно, загляну на пол часика.",
						3 => "Правильно решение! Там и увидимся. Пока!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[3] = 1;
					UpdateQuestInfo($user,25,implode("/",$ai)) or QuestDie();
					PutQItemTo($user,"Охранник",$todel) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Проходи путник, не задерживайся. Тут сторожевая башня, а не трактир. Тут все серьезно и по делу. Ну что встал как столб? Проходи, говорю!",
					1 => "Я к тебе по делу. Маг просил передать тебе приглашение на его День рождения. ",
					11111 => "Загляну как-нибудь в другой раз…",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	

		}
	}

	if ($sf == "mlwitch") {
		if ($step >= 1 && $ai[4] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "День Рождения у этого шарлатана?! А меня он пригласить не удосужился?! Ну спасибо тебе, что сообщил. Уж теперь-то он получит от меня «подарочек». Да ты не бойся, не бойся… вот, возьми на дорожку.",
						3 => "Ага, спасибо… Я пожалуй пойду. Пока!",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[4] = 1;
					UpdateQuestInfo($user,25,implode("/",$ai)) or QuestDie();

					// получаем бонус
					PutQItem($user,105,"Ведьма",7,array(),255,"shop",3) or QuestDie();

					$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b> за бонус квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края?",
					1 => "Я не совсем по делу, но и не просто так. У мага День Рождения и я хотел спросить – ему правда исполняется сто тринадцать лет?",
					11111 => "Уже ухожу, простите за беспокойство.",
				);
			}	

		}
	}

?>