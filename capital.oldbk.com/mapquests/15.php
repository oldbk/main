<?php
	// квест гнилая вода
	$q_status = array(
		0 => "Узнать у Пилигрима о странностях на реке.",
		1 => "Узнать у Ведьмы о странностях на реке.",
		2 => "Попросить у Кузнеца большой котёл и принести его к Ведьме.",
		3 => "Добыть руду для котла (%N1%/10) и принести её к Кузнецу. Принести котёл к Ведьме.",
		4 => "Принести котёл к Ведьме.",
		5 => "Найти для Ведьмы живую воду (%N1%/1), 10 веток с кустов Вангутта (%N2%/10) и рыбий глаз (%N3%/1).",
		6 => "Отнести противоядие Лодочнику.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 15) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlpiligrim" && $step == 0) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ого, вот это новости. Ничего похожего не слыхал и не видал. Но зато на той неделе гулял я возле реки и видел там всадника вида очень неприятного в ярком плаще. Я еще удивился, что за незнакомец к нам забрел. Хотел подойти поближе, но тут он что-то швырнул в воду с такой силой, что плюхнуло, аж на середине реки. А потом развернулся и ускакал, как и не было. Чует мое сердце, все это как-то связано. Сходи-ка ты к Ведьме, кажется это по ее части.",
					3 => "Спасибо, так и сделаю.",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				SetQuestStep($user,15,1) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
				1 => "Беда на реке случилась. Лодочник говорит, что рыба дохнет без счета и чем дальше, тем больше. Может ты слыхал о таком, и что надо делать?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}	
	}

	if ($sf == "mlwitch" && $step == 1) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Вот оно что, а я то, думаю, что за вонь последние дни от реки идет, а это гнилая вода по реке расходится. Знаю такую порчу. Видно кому-то мы здорово насолили, раз гнилой водой нас решили отравить. Вот, что тебе скажу. Беги к Кузнецу и проси у него самый большой котел, какой только он может сделать, а ты притащить. А я пока в книгах нужный рецепт противоядия откопаю. Только не медли, вода гниет быстрее, чем ты думаешь.",
					3 => "Договорились, я скоро вернусь.",
				); 
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				SetQuestStep($user,15,2) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края? ",
				1 => "Беда у нас с рекой, рыба дохнет без счета. Похоже, без колдовства там не обошлось. Может, ты сможешь помочь?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	}

	if ($sf == "mlvillage" && $step == 2) {
		if ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000)) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "Серьезное дело, но ради нашей реки постараюсь. Ты беги за рудой, а я пока раскалю кузницу. Не меньше 10 кусков руды будет нужно.",
						2003 => "Договорились, я скоро вернусь.",
					);
				} elseif ($_GET['qaction'] == 2003) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,15,3) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чем могу тебе помочь?",
					2001 => "Помощь твоя нужна срочно. Реку нашу отравили, но Ведьма может сварить противоядие. Только ей для этого нужен самый большой котел, который ты можешь сделать. Но надо спешить.",
					2002 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}		
		}		
	}

	if ($sf == "mlvillage" && $step == 3) {
		if ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000)) {
			$mlqfound = false;
			$todel = QItemExistsCountID($user,3003005,10);
			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Быстро ты обернулся, а у меня уже все готово. Подожди тут, скоро котел будет готов.",
						2003 => "Я то подожду, да время не ждет. Вода гниет быстрее, чем мы думаем.",
					);
				} elseif ($_GET['qaction'] == 2004 && $mlqfound) {
					// Большой котёл
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,15,4) or QuestDie();
					PutQItem($user,3003052,"Кузнец",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Кузнец передал вам <b>Большой котёл</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 2003 && $mlqfound) {
					$mldiag = array(
						0 => "Ну вот и все, держи свой котел и беги к Ведьме. Вредная она, конечно, старуха, но не злобная. Реку спасет от напасти.",
						2004 => "Спасибо за помощь, еще увидимся.",
					);
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чем могу тебе помочь?",
				);
				if ($mlqfound) $mldiag[2001] = "Да,  вот 10 кусков руды для котла.";
				$mldiag[2002] = "Нет, я еще не успел, пойду дальше.";
			}		
		}		
	}

	if ($sf == "mlwitch" && $step == 4) {
		$mlqfound = false;
		$todel = QItemExistsID($user,3003052);
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Вот и хорошо, а я нашла нужный рецепт. Почти все, что нужно у меня есть. Осталось  найти Живую воду, 10 веток с кустов Вангутта и рыбий глаз. Поторопись, принеси мне то, чего не хватает, и противоядие будет готово.",
					3 => "Хорошо, я принесу все, что тебе нужно.",
				); 
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				SetQuestStep($user,15,5) or QuestDie();
				PutQItemTo($user,"Ведьма",$todel) or QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просила?",
				1 => "Да,  вот самый большой котел, который смог сделать Кузнец",
				2 => "Нет, я еще не успел, пойду дальше.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}                                        
	}

	if ($sf == "mlmage" && $step == 5 && !QItemExists($user,3003054)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Хмм… Реку отравили, говоришь? Оно и верно, жуткая вонь оттуда идет. Есть у меня Живая вода. Просто так тебе не дал бы, но если на реку навел порчу тот, о ком я думаю, то надо спешить. От его яда не многие могут спастись. Не слыхал, может видели в наших краях на днях всадника в странном цветном плаще?",
					3 => "Да, Пилигрим его видел у реки.",
				); 
			} elseif ($_GET['qaction'] == 3) {
				$mldiag = array(
					0 => "Так я и думал… Значит дело плохо. Держи Живую Воду и лети птицей к Ведьме. Если повезет, она еще может успеть.",
					4 => "Спасибо за помощь.",
				); 
			} elseif ($_GET['qaction'] == 4) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003054,"Маг") or QuestDie();

				addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Живая Вода</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Не часто меня гости беспокоят.  Зачем пришел и по какому делу? Говори.",
				1 => "Я к тебе по делу серьезному. Кто-то отравил реку, а Ведьме для противоядия нужна Живая Вода. Ты не знаешь, где ее можно найти?",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}                                        	
	}

	if ($sf == "mlboat" && $step == 5 && !QItemExists($user,3003055)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Гнилая вода?! Слыхал я о таком, но думал, что это сказки выживших из ума стариков. А вот и мне при жизни пришлось такое увидать. Рыбьих глаз у меня тут сколько хочешь… Оглянись, видишь весь берег дохлой рыбой усеян…  Бери, и беги к Ведьме. И да пребудет с нами удача.",
					4 => "Спасибо, скоро вернусь.",
				); 
			} elseif ($_GET['qaction'] == 5) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} elseif ($_GET['qaction'] == 4) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003055,"Лодочник") or QuestDie();

				addchp ('<font color=red>Внимание!</font> Лодочник передал вам <b>Рыбий глаз</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты узнал о том, что я просил?",
				5 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				1 => "Да,  порчу навели на нашу реку. Вода гниет и очень быстро. Ведьма обещала сделать противоядие. Мне очень нужен рыбий глаз.",
				2 => "Нет, я еще не успел, пойду дальше.",
			);
		}                                        	
	}

	if ($sf == "mlboat" && $step < 5) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты узнал о том, что я просил?",
				1 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов).",
				2 => "Нет, я еще не успел, пойду дальше.",
			);
		}                                        	
	}

	if ($sf == "mlwitch" && $step == 5) {
		$mlqfound = false;
		$qi1 = QItemExistsCountID($user,3003053,10);
		$qi2 = QItemExistsID($user,3003054);
		$qi3 = QItemExistsID($user,3003055);
		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3);
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Наконец-то, я уж думала ты никогда не воротишься. Зелье уже почти готово, только тебя и жду. Главное, чтоб силы тебе хватило унести котел. Держи его крепко, да по дороге не расплескай. Каждая капля на вес золота нынче. Скажешь Лодочнику, чтоб разлил его на середине реки. Если мы не опоздали, то все получится.",
					3 => "Спасибо за совет, так и сделаю.",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003056,"Ведьма",0,$todel) or QuestDie();

				addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Противоядие</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				SetQuestStep($user,15,6) or QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();					
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просила?",
				1 => "Да,  вот рыбий глаз, Живая Вода и ветки с кустов Вангутта.",
				2 => "Нет, я еще не успел, пойду дальше.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}                                        	
	}

	if ($sf == "mlboat" && $step == 6) {
		$todel = QItemExistsId($user,3003056);
		$mlqfound = false;
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} elseif ($_GET['qaction'] == 4 && $mlqfound) {
				// награда
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,200) or QuestDie();
				$m = AddQuestM($user,1,"Лодочник") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,144144,"Лодочник",0,$todel,255) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Нападение «Разбойника»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();

			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				$mldiag = array(
					0 => "Надеюсь, что не опоздали! Спасибо тебе за помощь! Сейчас же сяду в лодку,  и дай-то Бог, наша река будет спасена!",
					4 => "Всегда рад помочь (получить награду)",
				);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
				1 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				3 => "Да,  вот держи полный котел противоядия. Разлить его надо на середине реки, и если мы не опоздали, то все получится.",
				2 => "Нет, я еще не успел, пойду дальше.",
			);
			if (!$mlqfound) unset($mldiag[3]);
		}                                        	
	}
?>