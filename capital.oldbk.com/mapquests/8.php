<?php
	// квест потерявшийся ребенок
	$q_status = array(
		0 => "Найти потерявшегося ребёнка.",
		1 => "Узнать в трактире про ребёнка.",
		2 => "Узнать у Охотника про ребёнка.",
		3 => "Узнать у Лесоруба про ребёнка.",
		4 => "Узнать у Разбойников про ребёнка.",
		5 => "Отбить ребёнка у Дракона.",
		6 => "Вернуть ребёнка.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 8) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlfort") {
		if ($step == 6) {
			$mlqfound = false;
			$todel = QItemExistsID($user,3003027);
			if ($todel !== FALSE) $mlqfound = true;
	
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					if (!$questexist['addinfo']) {
						$mldiag = array(
							0 => "Слава богу, он жив! Скажу тебе по секрету, родители этого малыша – очень влиятельные люди в нашей округе. К тому же, их род уходит в глубину веков, и фамильные вещи у них бывают не самые простые. Видишь амулет на шее у ребенка? Эта магическая вещица может натворить много хороших и плохих дел, смотря в какие руки попадет. Ты не только ребенка спас, но и много человеческих жизней. За это дам тебе двойную награду.",
							4 => "Спасибо (получить награду)",
						);
					} else {
						$mldiag = array(
							0 => "Слава богу, он жив! Скажу тебе по секрету, родители этого малыша – очень влиятельные люди в нашей округе. К тому же, их род уходит в глубину веков, и фамильные вещи у них бывают не самые простые. На шее этого ребенка был амулет. Эта магическая вещица может натворить много хороших и плохих дел, смотря в какие руки попадет. Если она осталась у Дракона, или попала в чужие руки – может быть беда. Но делать теперь нечего.  Ребенка спасли – уже хорошо. Держи обещанную награду",
							4 => "Спасибо (получить награду)",
							5 => "Подожди, я догадываюсь, где может быть амулет, я принесу его тебе."
						);
					}
				} elseif ($_GET['qaction'] == 5 && $mlqfound && $questexist['addinfo']) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItemTo($user,"Охранник",$todel) or QuestDie();
					SetQuestStep($user,8,7) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} elseif ($_GET['qaction'] == 4 && $mlqfound) {
					// получаем награду
					mysql_query('START TRANSACTION') or QuestDie();

					if (!$questexist['addinfo']) {
						$r = AddQuestRep($user,200) or QuestDie();
						$m = AddQuestM($user,1,"Охранник") or QuestDie();
						$e = AddQuestExp($user) or QuestDie();
			
						PutQItem($user,105,"Охранник",7,$todel,255) or QuestDie();
	
						$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					} else {
						$r = AddQuestRep($user,100) or QuestDie();
						$m = AddQuestM($user,1,"Охранник") or QuestDie();
						$e = AddQuestExp($user) or QuestDie();
			
						PutQItem($user,105,"Охранник",7,$todel,255,"shop",3) or QuestDie();
	
						$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					}
	
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 2) {
					unsetQA();
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();	
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты  сделал то, что я просил?",
				);
				if ($mlqfound) $mldiag[1] = "Да, вот ребенок. Он был у Дракона.";
				$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
				$mldiag[3] = "Еще нет. Пойду дальше.";
			}
		} elseif ($step == 7) {
			$mlqfound = false;
			$todel = QItemExistsID($user,3003028);
			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Слава богу! Теперь я спокоен за нашу округу и ее жителей! Побегу обрадую родителей малыша, что амулет нашелся! Ты спас много жизней сегодня! За это дам тебе двойную награду.",
						4 => "Спасибо.",
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Ну, ничего не поделаешь, будем уповать на милость высших сил, чтоб амулет не попал в дурные руки. Ребенка ты все-таки спас и получи заслуженную награду.",
						5 => "Спасибо.",
					);				
				} elseif ($_GET['qaction'] == 5 && !$mlqfound) {
					// получаем награду без амулета
					mysql_query('START TRANSACTION') or QuestDie();

					$r = AddQuestRep($user,100) or QuestDie();
					$m = AddQuestM($user,1,"Охранник") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
			
					PutQItem($user,105,"Охранник",7,array(),255,"shop",3) or QuestDie();
	
					$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 4 && $mlqfound) {
					// получаем награду за амулет
					mysql_query('START TRANSACTION') or QuestDie();

					$r = AddQuestRep($user,150) or QuestDie();
					$m = AddQuestM($user,2,"Охранник") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
			
					PutQItem($user,105,"Охранник",7,$todel,255,"shop",3) or QuestDie();
	
					$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(0 => "Привет, ты  принес то, что обещал?");
				if ($mlqfound) {
					$mldiag[1] = "Да, вот амулет, который ты искал.";
				} else {
					$mldiag[2] = "Нет, я не смог найти амулет.";
				}
				$mldiag[3] = "Нет, я не смог найти амулет. Пойду поищу дальше.";
			}
		} else {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2) {
					unsetQA();
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты  сделал то, что я просил?",
				);
				$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов).";
				$mldiag[3] = "Еще нет. Пойду дальше.";
			}
		}
	}

	if ($sf == "mlvillage") {
		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Хм, потеряться у нас можно много где, особенно если не следить за малышом. Помню, года два назад тоже ребенка в лесу потеряли, но вовремя спохватились. Когда отыскали, уже вокруг него волки рыскали. Считай,  повезло, еще чуть-чуть и плохо б дело кончилось.  Сходи-ка ты к Охотнику, может он что слышал.",
						3 => "Спасибо большое! Так и сделаю.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,8,1) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "Охранник послал меня на поиски ребенка, говорит, два дня уже найти не могут, ты ничего не знаешь про это?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlhunter") {
		if ($step == 1) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Ребенок, говоришь?  Я сегодня полдня по лесу проходил. Ничего не слышал. Ни крика, ни плача. Я ходил в сторону опушки леса, с уверенностью могу сказать, что там нет никого. Если такая беда приключилась тебе стоит поспешить, в лесу много диких зверей, да и не только звери могут быть опасностью для ребенка. Попробуй у Лесоруба спросить, вдруг он заметил чего.",
						3 => "Спасибо, так и поступлю.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,8,2) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
					1 => "Привет, ты не слыхал чего про потерявшегося ребенка? ",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlwood") {
		if ($step == 2) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Знаешь, когда я деревья рублю, удары топора раздаются по всему лесу. Если бы ребенок  был тут, то он  наверняка пришел бы на звук.  А я никого не видал…  Попробуй наведаться к Разбойникам,  если не боишься. Может они что подскажут. Они всегда в курсе всего.",
						3 => "Придется отправиться к разбойникам, это последняя надежда.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,8,3) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
					1 => "Привет, тут ребенок пропал, но никто ничего не знает. Может ты заметил чего?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlrouge") {
		if ($step == 3) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Есть такое дело, слышали мы детский плач около Драконьего логова, но подойти к нему не рискнули. Зайдя к Дракону можно и не выйти. Он последнее время сильно буйным стал, бессонницей страдает, не спит вообще, все логово свое охраняет. А может это и с ребенком связано. Если рисковый ты, можешь, конечно, туда отправиться, но вернуться  - шансов мало.",
						3 => "Выбора нет, ребенка надо спасать.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,8,4) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Ух-ты, смотрите, какие к нам незваные гости пожаловали! За жизнь свою не боишься?",
					1 => "Я по делу. Тут малыш пропал пару дней назад, все с ног сбились, никто ничего не знает и не слышал. Может вы заметили чего на дорогах или еще где?",
					2 => "Просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlmage") {
		if ($step == 4 && !QItemExists($user,3003026)) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Любопытно… Что это за ребенок такой, если Дракону понадобился. Обычно он, то корову украдет, то барана утащит, но людей не трогает. Да и охранник награду назначил... Очень-очень любопытно... Давай сделаем так, я тебе дам зелье сна, чтобы дракона усыпить, а ты, прежде чем ребенка вернуть охраннику, заглянешь ко мне. Уж очень интересно на это дитя посмотреть.",
						3 => "Договорились, обещаю зайти к тебе с ребенком.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003026,"Маг",0,array()) or QuestDie();
					mysql_query('COMMIT') or QuestDie();

					addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Зелье сна</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Не часто меня гости беспокоят.  Зачем пришел и по какому делу?  Говори.",
					1 => "Прости, за нежданный визит. Тут ребенок потерялся, охранник за него награду обещал. Оказалось, что  ребенка украл Дракон и теперь бушует. Никак его не победить, а ребенка спасать надо. Может знаешь, как помочь?",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
		if ($step == 5) {
			if (isset($_GET['qaction'])) {
				unsetQA();
				return;
			}
			$mldiag = array(
				0 => "Привет, ты привел ребенка?",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				1 => "Еще нет. Пойду дальше.",
			);
		}

		if ($step == 6) {
			if ($questexist['addinfo']) {
				return;
			}

			$mlqfound = false;
			$info = QItemExistsID($user,3003027);
			if ($info !== FALSE) $mlqfound = true;
	
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Симпатичный мальчишка. Но намного симпатичнее амулет, что висит у него на шее. Знаешь, мои услуги дорого стоят, но за то, что я тебе помог малыша спасти, я возьму только этот амулет. А ребенка отдай родителям, да и награду получишь – себе оставь.",
						3 => "Нет , я не распоряжаюсь чужим добром. Амулет был на ребенке и с ним он к родителям и вернется.",
						4 => "Договорились, бери амулет. Ведь главное, что ребенок жив.",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					$mldiag = array(
						0 => "Ну, что-ж, дело твое, но смотри, как бы потом не пожалеть!",
						5 => "Я передумал, бери амулет.",
						6 => "Может, и пожалею, но честь превыше всего.",
					);
				} elseif (($_GET['qaction'] == 4 || $_GET['qaction'] == 5) && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					mysql_query('UPDATE oldbk.`inventory` SET `img` = "baby3.gif" WHERE id = '.$info[0]) or QuestDie();
					PutQItem($user,667667,"Маг") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Зелье старого мага</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					UpdateQuestInfo($user,8,"1") or QuestDie();
                                        mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты привел ребенка?",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
					2 => "Еще нет. Пойду дальше.",
				);
				if ($mlqfound) $mldiag[1] = "Да, вот ребенок, которого Дракон украл";
				ksort($mldiag);
			}
		}
		if ($step == 7) {
			if (QItemExists($user,3003028) !== FALSE || $questexist['addinfo'] == 3) {
				return;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Ты сам отдал мне амулет, в обмен на награду, а теперь хочешь назад? Да, ты хитер, как посмотрю! Проваливай из моего дома, а не то, я выдворю тебя отсюда силой!",
						3 => "Нет, без амулета я не уйду! Хочешь войны – посмотрим, кто кого!",
						4 => "Ну, раз так, то делать нечего. Пойду дальше.",
					);
				} elseif ($_GET['qaction'] == 3) {
					if ($questexist['addinfo'] > 1) {
						// бой c магом - убийцей )
						mysql_query('START TRANSACTION') or QuestDie();
						StartQuestBattle($user,538, array(
							"hp" => 9000, 
							"maxhp" => 9000,
							"sila" => 9000,
							"lovk" => 9000,
							"inta" => 9000,
							"min_u" => 900,
							"max_u" => 1000,
							"vinos" => 9000,
							"level" => $user['level'],
							),1,'«Безумец! Смертный! Один раз ты уже попытался меня убить! Неужто ты не понял, что Великого Мага тебе не одолеть никогда?!»') or QuestDie();
						mysql_query('COMMIT') or QuestDie();

						unsetQA();
						Redirect('fbattle.php');
					} else {
						// бой с обычным магом
						mysql_query('START TRANSACTION') or QuestDie();
						StartQuestBattle($user,538) or QuestDie();
						UpdateQuestInfo($user,8,"2") or QuestDie();
						mysql_query('COMMIT') or QuestDie();
						unsetQA();
						Redirect('fbattle.php');
					}
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Снова пожаловал? Что на этот раз нужно?",
					1 => "У тебя остался амулет, который нужно вернуть хозяевам. Он слишком дорогой, чтобы им расплачиваться за твои услуги.",
					33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

?>