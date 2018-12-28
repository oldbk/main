<?php
	// квест прохудившаяся лодка
	$q_status = array(
		0 => "Справиться у Кузнеца о ремонте лодок.",
		1 => "Принести Лодочнику гвозди (%N1%/1), доски (%N2%/1), смолу (%N3%/1)",
		2 => "Принести Кузнецу руду (%N1%/2)",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 21) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");
	
	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlboat") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003075);
		$qi2 = QItemExistsID($user,3003076);
		$qi3 = QItemExistsID($user,3003077);

		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3);
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				$mldiag = array(
					0 => "Премного благодарен, надеюсь этого хватит для ремонта.",
					3 => "Всегда рад помочь. Кстати, литературный кружок на этой неделе заседает у Ведьмы.",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,250) or QuestDie();
				$m = AddQuestM($user,2,"Лодочник") or QuestDie();
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

				PutQItem($user,$item,"Лодочник",0,$todel,255,"eshop") or QuestDie();
				PutQItem($user,105,"Лодочник",7,array(),255,"shop",3) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Большой свиток «Восстановление ".$txt."HP»</b> и <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты узнал о том, что я просил?",
				1 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов).",
				2 => "Узнал? Я не просто узнал, я ещё и принес все что нужно! Это было нелегко, но я справился. Вот доски, гвозди и смола…",
				11111 => "Ещё нет, как узнаю – вернусь!",
			);
			if (!$mlqfound) unset($mldiag[2]);
		}                                        	
	}

	if ($sf == "mlvillage") {
		if ($ai[0] == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "Ох-ох… Какая неприятность. Для ремонта лодки тебе потребуются гвозди, доски и смола. Ну и мои бесценные советы, разумеется. С гвоздями и советами я помогу, а остальное тебе придется найти где-то ещё. ",
						2003 => "Ну, хоть что-то… Ладно, давай гвозди, остальное сам разыщу.",
					);
				} elseif ($_GET['qaction'] == 2003) {
					$mldiag = array(
						0 => "Да, кстати, насчет этого. Кузница давно не топлена, а руды для растопки нет. Принеси мне пару кусков руды, и будут тебе гвозди.",
						2004 => "Ну разумеется руды нет, как же иначе… Хорошо, я все сделаю.",
					);
				} elseif ($_GET['qaction'] == 2004) {
					$mldiag = array(
						0 => "Вот и славненько! До встречи.",
						2005 => "Пока.",
					);
				} elseif ($_GET['qaction'] == 2005) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,21,implode("/",$ai)) or QuestDie();					
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чем могу тебе помочь?",
					2001 => "Приветствую! Лодочник послал меня просить тебя о помощи – у него все лодки потонули или вроде того…",
					2002 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}

		if ($ai[0] == 1 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			$mlqfound = false;
			$todel = QItemExistsCountID($user,3003005,2);

			if ($todel !== FALSE) {
				$mlqfound = true;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Ах, ну да… Славно, славно. Ну раз обещал – будут тебе гвозди. Это быстро, не уходи никуда, сейчас вернусь…",
						2003 => "Стою, жду.",
					);
				} elseif ($_GET['qaction'] == 2003 && $mlqfound) {
					$mldiag = array(
						0 => "Вот, держи. Лодочнику привет передавай с наилучшими пожеланиями. И напомни ему что в эти выходные наш книжный клуб собирается у Ведьмы.",
						2004 => "Обязательно напомню, пока!",
					);
				} elseif ($_GET['qaction'] == 2004 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 2;
					UpdateQuestInfo($user,21,implode("/",$ai)) or QuestDie();					

					PutQItem($user,3003075,"Кузнец",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Кузнец передал вам <b>Гвозди</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();


					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, чем могу тебе помочь?",
					2001 => "Руду принес, как ты просил.",
					2002 => "Боюсь ничем…",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}
		}
	}

	if ($sf == "mlwood" && $ai[0] > 0) {
		if ($ai[1] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Тьфу ты, опять двадцать пять! Я уж не мальчик уже, а вы все туда же, чуть что – так ко мне. Вот как мы поступим – дам я тебе доски. Но ты сперва, за это, отнесешь  охраннику топор, чтоб он его заточил, старый я уже, самому в такую даль мотаться - сил нет.",
						2 => "Нашли себе, черт побери, мальчика…",
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Что говоришь, не слышу?",
						3 => "По рукам, говорю. Давай сюда топор.",
					);
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						0 => "Вот и славно. Отнеси топор и возвращайся за досками.",
						4 => "Пока.",
					);
				} elseif ($_GET['qaction'] == 4) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[1] = 1;
					UpdateQuestInfo($user,21,implode("/",$ai)) or QuestDie();					

					PutQItem($user,3003078,"Лесоруб") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Лесоруб передал вам <b>Топор</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();					
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
					1 => "Скорее второе. На Переправе кризис – бестолковый Лодочник загубил все лодки. Кузнец сказал, что без досок – никак не исправить положение.",
					11111 => "Прости за беспокойство, я лучше пойду…  ",
				);
			}                                        	
		}
		if ($ai[1] == 2) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Да, видимо и правда сделал – охранник всегда говорит, что через пару дней все будет готово, а потом я тут неделю сижу, жду, когда же эти пару дней пройдут. Ладно, спасибо, так или иначе, держи свои доски.",
						2 => "Спасибо, пока.",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[1] = 3;
					UpdateQuestInfo($user,21,implode("/",$ai)) or QuestDie();					

					PutQItem($user,3003076,"Лесоруб") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Лесоруб передал вам <b>Доски</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();					
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
					1 => "Сделал. Охранник сказал – через пару дней все будет готово.",
					11111 => "Пока что нет.",
				);
			}                                        	
		}
	}

	if ($sf == "mlfort" && $ai[1] == 1 && QItemExists($user,3003078)) {
		$todel = QItemExistsID($user,3003078);

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Лесоруб говоришь… Да, помнится обещал я ему новый топор. Ладно, через пару дней пусть приходит, будет ему новый топор.",
					2 => "Пока.",
				);
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[1] = 2;
				UpdateQuestInfo($user,21,implode("/",$ai)) or QuestDie();					
				PutQItemTo($user,"Лесоруб",$todel) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();					
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Проходи путник, не задерживайся. Тут сторожевая башня, а не трактир. Тут все серьезно и по делу. Ну что встал как столб? Проходи, говорю!",
				1 => "Лесоруб меня послал. К тебе. Велел топор передать, говорит, затупился совсем.",
				11111 => "Просто гулял неподалеку. Уже ухожу…",
			);
		}                                        	
	}

	if ($sf == "mlpiligrim" && $ai[0] > 0 && $ai[2] == 0) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Во имя Ангелов! Как такое могло произойти? Я помогу всем, чем смогу, только скажи что нужно!",
					2 => "Я рискую жизнью, в попытках раздобыть все необходимое и ты мог бы чертовски мне помочь... Ты живешь тут один, и, я подумал, вероятно, часто ремонтируешь свое жилище, возможно у тебя найдется немного смолы? Это не для меня, это все ради сироток…",
				);
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Да конечно, сейчас принесу! Бери сколько нужно!",
					3 => "Огромное спасибо, ты буквально спас всех этих людей! Пока!",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[2] = 1;
				UpdateQuestInfo($user,21,implode("/",$ai)) or QuestDie();					

				PutQItem($user,3003077,"Пилигрим") or QuestDie();
				addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Смола</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);

				mysql_query('COMMIT') or QuestDie();
				unsetQA();					
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
				1 => "Приветствую! Я к тебе с небольшой просьбой. Дело в том, что на переправе не осталось ни одной целой лодки и буквально в этот самый момент восемьдесят сироток, сорок три инвалида и кошка, не имея возможности переправиться, замерзают на берегу!",
				11111 => "Ой, кажется, дверью ошибся…",
			);
		}                                        		
	}
?>