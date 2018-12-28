<?php
	// квест магическое зеркало
	$q_status = array(
		0 => "Найти для мага резную оправу (%N1%/1), железные ножки (%N2%/1) и драгоценные камни для украшения (%N3%/1).",
		1 => "Принести Лесорубу обед (%N1%/1).",
		2 => "Принести Кузнецу кабанью ногу (%N1%/1).",
		3 => "Принести Охотнику кастет медведя (%N1%/1) или когти медведя (%N2%/5).",
	);


	// Выбираем вопрос-ответ.
	function SelectQuestion($quest = 0) {
		if ($quest == 0) {
			$query      = mysql_query("SELECT count(*) AS count FROM oldbk.victorina");
			$count      = mysql_fetch_assoc($query);
			$quest = rand(1, $count['count']);
		}
		$data = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`victorina` WHERE `id` = ".$quest." LIMIT 1;"));
		$data['Q'] = ObfuscateQ($data['Q']);
		return $data;
	}

	// Меняем буквы против тупого гугления.
	function ObfuscateQ($q)	{
		$letters = array("а", "е", "А", "о", "р", "и", "Е", "К", "М", "С", "с", "у", "З", "Н", "В", "О", "Р", "к", "п", "Т", "Х", "ь");
		$replace = array("a", "e", "A", "o", "p", "u", "E", "K", "M", "C", "c", "y", "3", "H", "B", "O", "P", "k", "n", "T", "X", "b");
	
		$q=str_replace($letters,$replace,$q);
		return $q;
	}
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 9) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlmage") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003030);
		$qi2 = QItemExistsID($user,3003033);
		$qi3 = QItemExistsID($user,3003034);

		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$todel = array_merge($qi1,$qi2,$qi3);
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Хм, весьма неплохо…. Камушки, могли бы быть и почище, но, зато,  на оправе резьба тонкая и узорчатая! Хорошее зеркало получится. Ну, а я слов своих не нарушаю, держи оплату за труды.",
					4 => "Всегда к вашим услугам (получить награду)",
				);
			} elseif ($_GET['qaction'] == 4 && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,200) or QuestDie();
				$m = AddQuestM($user,4,"Маг") or QuestDie();
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


				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();

				unsetQA();
			} elseif ($_GET['qaction'] == 3) {
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
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
			);
			if ($mlqfound) $mldiag[1] = "Да, вот оправа, ножки и драгоценные камни.";
			$mldiag[3] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[2] = "Нет, я еще не все достал. Пойду дальше.";

		}
	}

	if ($sf == "mlwood") {
		if (QItemExists($user,3003030)) return;

		$ai = explode("/",$questexist['addinfo']);

		if ($ai[0] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Могу, почему бы и нет. Все зеркала у Рыцаря в замке сделаны вот этими руками. Постараюсь для тебя, но я с утра крошки во рту не держал. Принеси мне поесть, а я пока займусь твоим заказом.",
						3 => "Договорились, я скоро вернусь.",
					);	
				} elseif ($_GET['qaction'] == 3) {
					$ai[0] = 1;
					mysql_query('START TRANSACTION') or QuestDie();
					UpdateQuestInfo($user,9,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
					1 => "Здравствуй, мне нужна резная оправа для зеркала, ты можешь такую сделать?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
		if ($ai[0] == 1) {
			$mlqfound = false;
			$todel = QItemExistsID($user,3003029);
			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Вот спасибо, теперь не помру с голоду. Держи свою оправу. Как и обещал, резная из лучшего дерева.",
						3 => "Благодарю",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003030,"Лесоруб",0,$todel) or QuestDie();

					// системку
					addchp ('<font color=red>Внимание!</font> Лесоруб передал вам <b>Оправа для зеркала</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 0;
					UpdateQuestInfo($user,9,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
				);
				if ($mlqfound) $mldiag[1] = "Да, вот твой обед, а оправа готова?";
				$mldiag[2] = "Еще нет. Пойду дальше.";
			}		
		}
	}

	if ($sf == "mlhunter") {
		if (QItemExists($user,3003032)) return;

		$ai = explode("/",$questexist['addinfo']);
		if ($ai[1] == 1) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Было дело, обещал я ему кабанью ногу. Но и он мне кое-что обещал. Говорил, что принесет мне кастет из когтей медведя. Уговор есть уговор. Принесешь мне кастет, тогда и ногу получишь. ",
						3 => "Кузнец ничего мне про это не говорил. А где такой кастет можно найти?",
					);
				} elseif ($_GET['qaction'] == 4) {
					$ai[1] = 2;
					mysql_query('START TRANSACTION') or QuestDie();
					UpdateQuestInfo($user,9,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						0 => "Говорят, что в городе такой кастет часто встречается. Но если там не найдешь, можешь принести мне 5 когтей медведя. Я сам сделаю не хуже городского. Медведей вокруг моей хижины много бродит. Если ты не робкого десятка, можешь рискнуть.",
						4 => "Договорились, принесу тебе или кастет или когти.",
					);
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
					1 => "Я с поручением от Кузнеца, говорит, что еще на той неделе у тебя кабанью ногу просил, но сам зайти не может. Дай мне, я ему отнесу.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше."
				);
			}
		}
		if ($ai[1] == 2) {
			$mlq1 = QItemEXistsCountID($user,1,1);
			$mlq2 = QItemEXistsCountID($user,3003031,5);

			if (isset($_GET['qaction'])) {
				if (($_GET['qaction'] == 1 && $mlq1) || ($_GET['qaction'] == 2 && $mlq2)) {
					$mldiag = array(
						0 => "Ну что-ж, ты обещание сдержал и я свое сдержу. Вот тебе кабанья нога, и передай Кузнецу, что у меня топор затупился, скоро загляну к нему за новым лезвием.",
						11111 => "Спасибо, обязательно передам.",
					);

					mysql_query('START TRANSACTION') or QuestDie();
					if ($_GET['qaction'] == 1) {
						PutQItem($user,3003032,"Охотник",0,$mlq1) or QuestDie();
					} else {
						PutQItem($user,3003032,"Охотник",0,$mlq2) or QuestDie();
					}
                                        mysql_query('COMMIT') or QuestDie();
					// системку
					addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Кабанья нога</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
				);
				if ($mlq1) $mldiag[1] = "Да, вот то, что ты просил. (отдать кастет)";
				if ($mlq2) $mldiag[2] = "Да, вот то, что ты просил. (отдать 5 когтей)";
				$mldiag[11111] = "Нет, я еще не все достал. Пойду дальше.";
			}		
		}
	}

	if ($sf == "mlvillage") {
		$ai = explode("/",$questexist['addinfo']);

		if ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000)) {
			if (QItemExists($user,3003033)) return;

			if ($ai[1] == 0) {
				if (isset($_GET['qaction'])) {
					if ($_GET['qaction'] == 2001) {
						$mldiag = array(
							0 => "Выковать железные ножки это уйма работы - часа 3-4 точно. А ко мне вечером приятели должны прийти. Я обещал их угостить кабаньей ногой на углях. Так что я за работу, а ты ступай к Охотнику. Скажи, что я еще на той неделе у него просил, но сам теперь зайти не могу.",
							2003 => "Договорились, я скоро вернусь."
						);
					} elseif ($_GET['qaction'] == 2003) {
						$ai[1] = 1;
						mysql_query('START TRANSACTION') or QuestDie();
						UpdateQuestInfo($user,9,implode("/",$ai)) or QuestDie();
						mysql_query('COMMIT') or QuestDie();
						unsetQA();
					} else {
						unsetQA();
					}
				} else {
					$mldiag = array(
						2000 => "Привет, чем могу тебе помочь?",
						2001 => "Мне нужны железные ножки для зеркала. Ты можешь сделать?",
						2002 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
					);
				}			
			}
			if ($ai[1] == 1 || $ai[1] == 2) {
				$mlqfound = false;
				$todel = QItemExistsID($user,3003032);
				if ($todel !== FALSE) $mlqfound = true;

				if (isset($_GET['qaction'])) {
					if ($_GET['qaction'] == 2001 && $mlqfound) {
						$mldiag = array(
							0 => "Вот это нога, так нога. На всех гостей хватит! Молодец! Держи железные ножки, что ты заказывал, а я пойду разжигать угли, скоро гости нагрянут!",
							2003 => "Спасибо, хорошей вечеринки!",
						);
					} elseif ($_GET['qaction'] == 2003 && $mlqfound) {
						mysql_query('START TRANSACTION') or QuestDie();
						PutQItem($user,3003033,"Кузнец",0,$todel) or QuestDie();

						// системку
						addchp ('<font color=red>Внимание!</font> Кузнец передал вам <b>Ножки для зеркала</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

						$ai[1] = 0;
						UpdateQuestInfo($user,9,implode("/",$ai)) or QuestDie();
						mysql_query('COMMIT') or QuestDie();
						unsetQA();					
					} else {
						unsetQA();
					}
				} else {
					$mldiag = array(
						0 => "Привет, ты принес мне то, что я просил?",
					);
	
					if ($mlqfound) $mldiag[2001] = "Да, вот кабанья нога.";
					$mldiag[2002] = "Нет, я еще не все достал. Пойду дальше.";
				}
				
			}
		}

		if ($ai[0] == 1 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			if (QItemExists($user,3003029)) return;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "И то тебе дай и это. И денег у тебя вечно нет. Хотя, есть тут загвоздка одна – вчера ко мне заходил Пилигрим, чайку попить, и загадал мне загадку. Поверишь, с тех пор голову  ломаю, никак решить не могу. Поможешь мне найти ответ – дам все, что ты просишь бесплатно. А нет – плати как положено и забирай еду.",
					);

					if ($ai[3] < 6) $mldiag[3] = "Договорились, давай загадку.";
					if ($user['money'] >= 2) $mldiag[4] = "Нет, я лучше заплачу. Держи 2 кредита.";
					$mldiag[11111] = "Извини, но мне надо идти";
				} elseif ($_GET['qaction'] == 4 && $user['money'] >= 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-2;
					$rec['target']=0;
					$rec['target_login']="Трактирщик";
					$rec['type']=252; // плата квестовому боту
					$rec['sum_kr']=2;
					add_to_new_delo($rec) or QuestDie(); //юзеру

					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 2 WHERE id = '.$user['id']) or QuestDie();

					// даём вещь
					PutQItem($user,3003029,"Трактирщик") or QuestDie();
					mysql_query('COMMIT') or QuestDie();

					// системку
					addchp ('<font color=red>Внимание!</font> Трактирщик передал вам <b>Обед лесоруба</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 6) {
					unset($_SESSION['map_quest9']);
					$mldiag = array(
						0 => "Нет, это не правильный ответ. Попробуешь еще раз?",
						3 => "Да, попробую еще раз.",
					);
					if ($user['money'] >= 2) $mldiag[4] = "Нет, я лучше заплачу. Держи 2 кредита.";
				} elseif ($_GET['qaction'] == 7) {
					unset($_SESSION['map_quest9']);
					$mldiag = array(
						0 => "Нет, это не правильный ответ. Попробуешь еще раз?",
					);
					if ($user['money'] >= 2) $mldiag[4] = "Нет, я лучше заплачу. Держи 2 кредита.";
					$mldiag[11111] = "Извини, но мне надо идти.";
				} elseif ($_GET['qaction'] == 3 && $ai[3] < 6) {
					if (!isset($_SESSION['map_quest9'])) {
						$q = SelectQuestion();
						$_SESSION['map_quest9'] = $q['id'];
					} else {
						$q = SelectQuestion($_SESSION['map_quest9']);
					}

					if (isset($_POST['answer'])) {
						if ($q['A'] != $_POST['answer']) {
							$ai[3]++;
							mysql_query('START TRANSACTION') or QuestDie();
							UpdateQuestInfo($user,9,implode("/",$ai)) or QuestDie();
							mysql_query('COMMIT') or QuestDie();
							if ($ai[3] < 6) {
								Redirect("mlvillage.php?qaction=6");
							} else {
								Redirect("mlvillage.php?qaction=7");
							}
						} else {
							// даём вещь
							mysql_query('START TRANSACTION') or QuestDie();
							PutQItem($user,3003029,"Трактирщик") or QuestDie();
							mysql_query('COMMIT') or QuestDie();

							// системку
							addchp ('<font color=red>Внимание!</font> Трактирщик передал вам <b>Обед лесоруба</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
							$mldiag = array(
								0 => "Точно, как я сам не догадался! Вот тебе обед для Лесоруба и ступай с Б-гом!",
								11111 => "Пока.",
							);
							return;
						}
					}

					// загадываем загадку
					$mldiag = array(
						0 => $q['Q'],
						1 => '<!-- NOLINK -->Ответ: <form method=post><input type="text" name="answer"><input type=submit value="Ответить"></form>',
					);
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "Я от лесоруба, он проголодался сильно, просил еды ему принести. Не поможешь?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlbuyer") {
		if (QItemExists($user,3003034))	{
			$mldiag = array(
				0 => "Проходи, проходи. Зачем в гости пожаловал?",
				33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
				11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
			return;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Камушки, говоришь? Да где ж в таком захолустье камушки найдешь?  Люди мы бедные, добропорядочные,  живем одиноко, проблем не делаем, законов не нарушаем. Разве что, могу посмотреть в сундучке покойной матушки, может там и найдется.",
					3 => "Вот-вот, может в сундучке завалялись? Глянь, будь добр.",
				);
			} elseif ($_GET['qaction'] == 4) {
				// стартуем бой с крысами
				mysql_query('START TRANSACTION') or QuestDie();
				StartQuestBattleCount($user,532, 10) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				Redirect("fbattle.php");
				unsetQA();
			} elseif ($_GET['qaction'] == 5) {
				if ($user['money'] >= 5) {
					mysql_query('START TRANSACTION') or QuestDie();
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-5;
					$rec['target']=0;
					$rec['target_login']="Скупщик";
					$rec['type']=252; // плата квестовому боту
					$rec['sum_kr']=5;
					add_to_new_delo($rec) or QuestDie(); //юзеру
	
					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 5 WHERE id = '.$user['id']) or QuestDie();
	
					addchp ('<font color=red>Внимание!</font> Скупщик передал вам <b>Драгоценные камни</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					// даём вещь
					PutQItem($user,3003034,"Скупщик") or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					$mldiag = array(
						0 => "Вот, держи Драгоценные камни",
						11111 => "Спасибо, пока",
					);
				} else {
					$mldiag = array(
						0 => "У вас нет 5 кр.",
						11111 => "Извини, пока.",
					);
				}
			} elseif ($_GET['qaction'] == 3) {
				$mldiag = array(
					0 => "Ну вот, есть парочка цветных стекляшек. Просто так не отдам, сам понимаешь, память матушкина, но вот за работу могу и подарить. Завелись у меня в подвале крысы, напасть такая. Грызут все подряд. Убытков от них не сосчитать. Если сможешь их перебить, получишь свои камушки.",
					4 => "Договорились, начнем охоту!",
					5 => "Нет, не до борьбы с крысами мне сейчас. Держи 5 кредитов и давай камни.",
				);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Проходи, проходи. Зачем в гости пожаловал?",
				33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
				1 => "Камушки ищу драгоценные, говорят у тебя можно найти. Не поможешь?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}

	}
?>