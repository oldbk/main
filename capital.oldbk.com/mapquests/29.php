<?php
	// квест приворотное зелье
	$q_status = array(
		0 => "Найти Книгу рецептов Ведьмы и вернуть",
		1 => "Найти Крысиный хвост (%N1%/3), Крыло летучей мыши (%N2%/1), Любисток (%N3%/1) для Ведьмы",
		2 => "Отнести приворотное зелье Одинокому рыцарю",
		3 => "Вернуться к ведьме",
		4 => "Узнать, что за девушка приходила к Ведьме",
		5 => "Получить информацию в деревне",
		6 => "Найти способ определить владельца плаща",
		7 => "Вернуться к Рыцарю",
		8 => "Принести Магу свиток идентификации",
		9 => "Собрать темнолистья для Мага (%N1%/10)",
		10=> "Расспросить Священника",
		11=> "Вернуться к Рыцарю",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 29) return;

	$step = $questexist['step'];
                                            
	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	$ai = explode("/",$questexist['addinfo']);

	// вернуться к ведьме - 3
	// предупредить рыцаря и искать бабу - 4
	// плащь через деревню - 5
	// плащь через драку - 6
	// плащь через деревню и отдали священнику - 7
	// несём идент - 8,10,11
	// несём траву - 9

	if ($sf == "mlwitch") {
		if ($step == 0) {
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003205,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Ох, чудесно, чудесно. Так, ну что же, посмотрим. Зелье сглаза, зелье мора, зелье родового проклятия, зелье для покраски волос, зелье для снятия лака… вот оно! Зелье приворотное. Ингредиенты, ага, это есть, это тоже есть. А вот это проблема…",
						3 => "Ингредиентов не хватает?",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					$mldiag = array(
						0 => "Ну, раз ты сам предложил! Это так любезно с твоей стороны! Вот, держи список того, что мне нужно, думаю, у тебя не возникнет особых проблем с крысиными хвостами и крыльями летучих мышей. А вот любисток придется поискать.",
						4 => "Любичто? Боюсь, я даже не представляю, что это.",
					);
				} elseif ($_GET['qaction'] == 4 && $mlqfound) {
					$mldiag = array(
						0 => "Любисток – это такой лепесток.  Но только для любимых. Любисток ты сможешь найти лишь в одном месте – в пещере дракона. Удачи!",
						5 => "Удача мне не понадобится. Пока!",
					);
				} elseif ($_GET['qaction'] == 5 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					// отбираем книгу
					$it = QItemExistsID($user,3003205);
					PutQItemTo($user,'Ведьма',$it) or QuestDie();

					SetQuestStep($user,29,1) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просила?",
					1 => "Да, вот, нашел твою книжку.",
					99 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Нет, я еще не все сделал.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	

		} elseif ($step == 1) {
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003206);
			$qi2 = QItemExistsID($user,3003059);
			$qi3 = QItemExistsCountID($user,3003002,3);
	
			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3);
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Славненько. Я в тебе не сомневалась, так что начала приготовления в твое отсутствие, теперь осталось добавить только то, что раздобыл ты и прочесть правильное заклинание…<br>Сердечко к сердечку<br>Привяжется прочно<br>Как к волку овечка<br>Сама придет ночью<br>Хвостик крысиный, <br>И крылышко мыши<br>Цветок для любимых,<br>Он зов твой услышит!<br>",
						3 => "Так ты ещё и стихи пишешь?",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					$mldiag = array(
						0 => "Это заклинание и лучше бы тебе над ним не смеяться... Всё, зелье готово. Осталось только отнести его одинокому Рыцарю. Я даже не знаю, кого мне попросить об этом? Ты не знаешь такого храброго воина, кто за щедрое вознаграждение согласился бы выполнить ещё одну, самую последнюю  мою просьбу?",
						4 => "Мягко стелешь, да боюсь, жестко будет спать… Хорошо, отнесу зелье Рыцарю.",
					);
				} elseif ($_GET['qaction'] == 4 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003207,"Ведьма",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Приворотное зелье</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					SetQuestStep($user,29,2) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просила?",
					1 => "Вот все ингредиенты. И любисток.",
					99 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Нет, я еще не все сделал.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		} elseif ($step == 3) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Красивая - не то слово! Почти так же прекрасна как я в молодости… Ладно, ты сегодня сильно мне помог. Вот твоя награда, как я и обещала.",
						3 => "Большое спасибо. Обращайся если что. ",
					);
				} elseif ($_GET['qaction'] == 3) {
					// выдаём награду
					// ТУТ ВСЁ ОК
					mysql_query('START TRANSACTION') or QuestDie();

					$r = AddQuestRep($user,150) or QuestDie();
					$m = AddQuestM($user,3,"Ведьма") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
	
					PutQItem($user,105,"Ведьма",7,array(),255) or QuestDie();

					$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
	
					UnsetQuest($user) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просила?",
					1 => "Конечно, сделал. Признаться мне чертовски интересно будет взглянуть на одурманенного рыцаря. Девица-то, я надеюсь, красивая была?",
					99 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Нет, я еще не все сделал.",
				);
			}	
		} elseif ($step == 4) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					// плащь через деревню
					$mldiag = array(
						0 => "Кто такая - не знаю. Однако на шее у неё был серебряный крестик, красоты необыкновенной, явно старинный. Я оттого и решила, что передо мной не какая-нибудь простушка… Вот, возьми её плащ, да ступай в деревню. Поспрашивай там – не терял ли такой кто.",
						3 => "Большое спасибо. Обращайся если что. ",
					);
				} elseif ($_GET['qaction'] == 3) {
					// выдаём плащь
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003208,"Ведьма") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Походный плащ</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					SetQuestStep($user,29,5) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} elseif ($_GET['qaction'] == 2) {
					// плащь через драку
					$mldiag = array(
						0 => "Что?! Как ты посмел обмануть меня?! Попрощайся со своей жалкой жизнью!",
						4 => "Ну, это мы еще посмотрим…",
					);
				} elseif ($_GET['qaction'] == 4) {
					// стартуем бой с ведьмой
					mysql_query('START TRANSACTION') or QuestDie();
					StartQuestBattle($user,542) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
	
					unsetQA();
					Redirect('fbattle.php');
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просила?",
					1 => "И тебе не хворать, ведунья. Да, я сделал все как ты и просила… Однако, просто из любопытства, спрошу – может быть у тебя есть догадки, что же это была за девушка, попросившая тебя об услуге?",
					2 => "Нет, я бы никогда не предал доверие сэра Рыцаря! А теперь отвечай – кто такая была эта девица?!",
					99 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Не понимаю о чем ты. Лучше я пойду…",
				);
			}	
		} else {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чего пожаловал?",
					99 => "Я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Просто мимо гулял...",
				);
			}	
		}

	}

	if ($sf == "mlknight") {
		if ($step == 2 && QItemExists($user,3003207)) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					// быстрая ветка
					$mldiag = array(
						0 => "Это так великодушно с его стороны! А ты, должно быть, устал с дороги? Присаживайся, отведаем вместе того вина!",
						3 => "Да нет, спасибо, я пожалуй откажусь. Столько дел, знаешь ли, столько дел! А ты пей, пей, потом расскажешь как оно. Пока!",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();

					// отбираем зелье
					$it = QItemExistsID($user,3003207);
					PutQItemTo($user,'Рыцарь',$it) or QuestDie();

					SetQuestStep($user,29,3) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "И что же эта ведунья удумала такого, о чем мне надо знать? Неужто козни против меня решила строить?",
						4 => "Не совсем козни, да и не совсем Ведьма… Приходила к ней накануне девица, просила сварить приворотное зелье. Говорила - любит тебя, да так сильно, что аж спать по ночам не может.",
					);
				} elseif ($_GET['qaction'] == 4) {
					$mldiag = array(
						0 => "Спасибо за предупреждение! Даже не знаю, как тебя вознаградить. Впредь я буду осторожен! Хотя, конечно, интересно было бы узнать, что же это за юная особа, ищущая путь к сердцу мужчины, буквально через его желудок.",
						5 => "Рад был помочь. Но вот что касается дел сердечных – скажем строго – не моя область.",
						6 => "Думаю, я мог бы разузнать что-нибудь для тебя.",
					);
				} elseif ($_GET['qaction'] == 5) {
					// самая быстрая ветка
					// ТУТ ВСЁ ОК
					mysql_query('START TRANSACTION') or QuestDie();

					// отбираем зелье
					$it = QItemExistsID($user,3003207);
					PutQItemTo($user,'Рыцарь',$it) or QuestDie();

					// выдаём награду
					$r = AddQuestRep($user,150) or QuestDie();
					$m = AddQuestM($user,2,"Рыцарь") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
	
					PutQItem($user,15567,"Рыцарь",0,array(),255,"eshop") or QuestDie();

					$msg = "<font color=red>Внимание!</font> Вы получили <b>Осколок статуи Лорда Разрушителя</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
	
					UnsetQuest($user) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 6) {
					$mldiag = array(
						0 => "Это столь великодушно с твоей стороны! Я буду благодарен за любую информацию, об этой незнакомке!",
						7 => "Отправлюсь на поиски немедленно!",
					);				
				} elseif ($_GET['qaction'] == 7) {
					mysql_query('START TRANSACTION') or QuestDie();

					// отбираем зелье
					$it = QItemExistsID($user,3003207);
					PutQItemTo($user,'Рыцарь',$it) or QuestDie();

					SetQuestStep($user,29,4) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам?",
					1 => "Приветствую сэр Рыцарь! У меня тут посылка для тебя. Трактирщик пузырек какой-то передал, велел в вино добавить, мол, улучшает вкусовой букет и все такое.",
					2 => "Здравствуй. Я пришел, что бы предупредить тебя о планах болотной Ведьмы…",
					11111 => "Нет, я еще не все сделал.",
				);
			}	

		}

		if ($step == 7 || $step == 11) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Так кто же она, прошу, говори, не томи!",
						3 => "Дочь деревенского Священника! Удивительно и как только не испугалась гнева своего отца, узнай он, что она обратилась за помощью к Ведьме…",
					);
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						0 => "Великолепно! Я немедленно отправлюсь в город, что бы взглянуть на эту отважную юную особу!  И вот, прими это, в знак моей искренней благодарности!",
						4 => "Большое спасибо.",
					);
				} elseif ($_GET['qaction'] == 4) {
					// выдаём награду
					// ТУТ ЕБОЛА
					mysql_query('START TRANSACTION') or QuestDie();

					// выдаём награду
					if ($step == 7) {
						$r = AddQuestRep($user,200) or QuestDie();
						$m = AddQuestM($user,3,"Рыцарь") or QuestDie();
						$e = AddQuestExp($user) or QuestDie();
		
						PutQItem($user,15562,"Рыцарь",0,array(),255,"eshop") or QuestDie();
	
						$msg = "<font color=red>Внимание!</font> Вы получили <b>Осколок статуи Мироздателя</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					}

					if ($step == 11) {
						// тут две концовки, одна через идент, вторая через темнолист
						if ($ai[1] == 1) {
							// собирали листя
							$r = AddQuestRep($user,150) or QuestDie();
							$m = AddQuestM($user,2,"Рыцарь") or QuestDie();
							$e = AddQuestExp($user) or QuestDie();
			
							PutQItem($user,667667,"Рыцарь",0,array(),255) or QuestDie();

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

							PutQItem($user,$item,"Рыцарь",0,array(),255,"eshop") or QuestDie();

							$msg = "<font color=red>Внимание!</font> Вы получили <b>Зелье Старого Мага</b> и <b>Большой свиток «Восстановление ".$txt."HP»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
							addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();         

						} else {
							// нисли идент
							$r = AddQuestRep($user,200) or QuestDie();
							$m = AddQuestM($user,3,"Рыцарь") or QuestDie();
							$e = AddQuestExp($user) or QuestDie();
			
							PutQItem($user,105,"Рыцарь",7,array(),255) or QuestDie();
							$item = 3101;
							$howmuch = 5;
							if (mt_rand(0,100) < 70) {
								$item = 3103;
								$howmuch = 20;
							}
	
							PutQItem($user,$item,"Рыцарь",0,array(),255,"eshop") or QuestDie();
		
							$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b> и <b>Чек на предьявителя ".$howmuch." кр.</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
							addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();         
						}
					}
	
					UnsetQuest($user) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 6) {
					$mldiag = array(
						0 => "Это столь великодушно с твоей стороны! Я буду благодарен за любую информацию, об этой незнакомке!",
						7 => "Отправлюсь на поиски немедленно!",
					);				
				} elseif ($_GET['qaction'] == 7) {
					mysql_query('START TRANSACTION') or QuestDie();

					// отбираем зелье
					$it = QItemExistsID($user,3003207);
					PutQItemTo($user,'Рыцарь',$it) or QuestDie();

					SetQuestStep($user,29,4) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
					1 => "Да! Я узнал, что за девушка просила помощи у ведьмы! Ты не поверишь…",
					11111 => "Мне нужно ещё время…",
				);
			}	

		}
	}

	if ($sf == "mlvillage") {
		if (($step == 5 && QItemExists($user,3003208)) || ($step == 10 && QItemExists($user,3003209)) && ((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "Ох, сын мой, конечно я её знаю. То дочь моя! Спасибо что вернул её плащ. ",
						2004 => "Не стоит благодарности, на моем месте так поступил бы всякий праведный и честный человек. ",
					);
				} elseif ($_GET['qaction'] == 2004) {
					mysql_query('START TRANSACTION') or QuestDie();

					// отбираем плащь
					if ($step == 5) {
						$it = QItemExistsID($user,3003208);
						PutQItemTo($user,'Священник',$it) or QuestDie();
						SetQuestStep($user,29,7) or QuestDie();
					}

					if ($step == 10) {
						$it = QItemExistsID($user,3003209);
						PutQItemTo($user,'Священник',$it) or QuestDie();
						SetQuestStep($user,29,11) or QuestDie();
					}

		
					UnsetQA();
					mysql_query('COMMIT') or QuestDie();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую тебя, путник, в нашей скромной обители. Пришел ли ты просить Господа о благодати и отпущении грехов или помолиться со мной о душах других грешников?",
					2001 => "День добрый, ваше святейшество. С необычным вопросом пришел я к вам. У меня тут походный плащ, что принадлежит одной девушке. Быть может, знаете её? Она ещё носит старинный серебряный крестик на шее…",
					11111 => "Кажется я ошибся дверью…",
				);
			}
		}
	}

	if ($sf == "mlmage") {
		if ($step == 6 && QItemExists($user,3003208)) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Время у меня, возможно и есть, но вот причин помогать тебе  - я не вижу.",
						2 => "Дело в том, что Ведьма грозится наложить страшное проклятье на ни в чем неповинную девушку, а я не могу предупредить её, потому что даже не представляю где искать… ",
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Хм… Я могу найти человека с помощью магии, однако мне нужно что-нибудь из его вещей.",
						3 => "У меня есть её походный плащ, этого достаточно?",
					);
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						0 => "Вполне. Мне теперь  мне лишь нужно начертать заклятие идентификации… Хм. Вот незадача.",
						4 => "Что-то не так?",
					);
				} elseif ($_GET['qaction'] == 4) {
					$mldiag = array(
						0 => "Вполне. Мне теперь  мне лишь нужно начертать заклятие идентификации… Хм. Вот незадача.",
						5 => "Что-то не так?",
					);
				} elseif ($_GET['qaction'] == 5) {
					$mldiag = array(
						0 => "Смешно сказать, но, похоже, у меня кончились чернила. Тебе придется немного потрудиться, если эта девица действительно так важна. Ступай в лес и собери мне десяток темнолистьев если конечно у тебя в сумке не завалялся свиток с нужным заклинанием, что вряд ли.",
						6 => "Я принесу тебе свиток.",
						7 => "Немедленно отправляюсь в лес на поиски.",
					);
				} elseif ($_GET['qaction'] == 6) {
					// несём свиток
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,29,8) or QuestDie();
					// отбираем плащь
					$it = QItemExistsID($user,3003208);
					PutQItemTo($user,'Священник',$it) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 7) {
					// идём собирать траву
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,29,9) or QuestDie();
					// отбираем плащь
					$it = QItemExistsID($user,3003208);
					PutQItemTo($user,'Священник',$it) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Не часто меня гости беспокоят. Зачем пришел и по какому делу? Говори.",
					1 => "Приветствую тебя, чародей. Я пришел просить тебя о помощи, если конечно у тебя есть время.",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					11111 => "Просто гулял неподалеку.",
				);
			}	
		}
		// идент
		if ($step == 8) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,9,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "А листья мне самому значит собирать? Обленилась молодежь нынче… Ладно, давай сюда свой свиток, поглядим кто такая эта твоя девица.",
						3 => "Ну, кто же? Кто?",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					$mldiag = array(
						0 => "Ха! Девица и впрямь хороша! Но вот папаша её тот ещё гремлин… В общем, в деревню тебе надобно, к Священнику. Удачи!",
						4 => "Огромное спасибо!",
					);
				} elseif ($_GET['qaction'] == 4 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003209,"Маг",0,$todel) or QuestDie();
					addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Походный плащ дочери Священника</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					SetQuestStep($user,29,10) or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,29,implode("/",$ai)) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					1 => "Да, вот свиток",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					11111 => "Нет, я еще не все сделал.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		}

		// темнолистья
		if ($step == 9) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003210,10);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Великолепно. Мне нужно некоторое время что бы изготовить чернила и начертать нужное тебе заклинание… Подожди пару секунд. Уже почти все.",
						3 => "Ничего страшного, я подожду.",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					$mldiag = array(
						0 => "Так, всё готово, осталось лишь прочесть заклинание… Ха! Девица и впрямь хороша! Но вот папаша её тот ещё гремлин… В общем, в деревню тебе надобно, к Священнику. Удачи!",
						5 => "Огромное спасибо!",
					);
				} elseif ($_GET['qaction'] == 5 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003209,"Маг",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Походный плащ дочери Священника</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 1;
					UpdateQuestInfo($user,29,implode("/",$ai)) or QuestDie();

					SetQuestStep($user,29,10) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					1 => "Да, вот, собрал темнолистья как ты и просил!",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					11111 => "Нет, я еще не все сделал.",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		}

		// бонус за темнолистья
		if (($step == 10 || $step == 11) && $ai[0] == 1) {
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003210);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Хм, это неожиданно с твоей стороны, помочь мне, несмотря на то, что тебе уже не было никакой в том необходимости. В благодарность за твой поступок прими от меня это скромное вознаграждение.",
						3 => "Огромное спасибо! Если нужна будет какая-то помощь – я всегда к твоим услугам!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					$ai[0] = 0;
					UpdateQuestInfo($user,29,implode("/",$ai)) or QuestDie();

					// получаем бонус
					PutQItem($user,667667,"Маг",0,$todel,255,"shop",1) or QuestDie();

					$msg = "<font color=red>Внимание!</font> Вы получили <b>Зелье Старого Мага</b> за бонус квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();


					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 99) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Не часто меня гости беспокоят. Зачем пришел и по какому делу? Говори.",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					1 => "Ты мне так помог в моих поисках, мудрейший, что я решил помочь тебе взамен. Вот, держи, собрал для тебя немного темнолистьев, что бы тебе не пришлось самому рыскать по лесу!",
					11111 => "Простите за беспокойство…",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		}
	}

?>