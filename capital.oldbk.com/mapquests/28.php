<?php
	// квест диковенные чётки
	$q_status = array(
		0 => "Принести Священнику чётки ручной работы (%N1%/1)",
		1 => "Принести Пилигриму Шелковая нить (%N1%/1),  Драгоценные бусины (%N2%/1), Деревянные звенья(%N3%/1)",
		2 => "Отправиться к Пилигриму",
		3 => "Получить от Трактирщика еду для Разбойников",
		4 => "Доставить Разбойникам еду",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 28) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	$ai = explode("/",$questexist['addinfo']);

	// ai[0] - 0 - начало, 
	// квест через пилигрима (1-2), 
	// разбойнику оплатили 10кр - 3
	// напали на пилигрима - 4
	// забрали чётки у разбойника - 5
	// предупредили пилигрима и прибили разбойника - 6
	// получили чётки от пилигрима за разбойника - 7
	// взялись за услугу для разбойников - 8
	// получили еду от трактирщика - 9

	if ($sf == "mlvillage") {
		if (((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003200,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Ох, хвала небесам. Это лучшее что я мог бы подарить столь уважаемому скитальцу божьему… Благодарю тебя сын мой.",
						2004 => "Всегда рад помочь.",
					);
				} elseif ($_GET['qaction'] == 2099) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} elseif ($_GET['qaction'] == 2004 && $mlqfound) {
					// получаем награду
					mysql_query('START TRANSACTION') or QuestDie();

					if ($ai[0] == 2) {		
						$r = AddQuestRep($user,150) or QuestDie();
						$m = AddQuestM($user,3,"Священник") or QuestDie();
						$e = AddQuestExp($user) or QuestDie();
		
						PutQItem($user,144144,"Священник",0,$todel,255) or QuestDie();

						$msg = "<font color=red>Внимание!</font> Вы получили <b>Нападение «Разбойника»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					}


					if ($ai[0] == 5) {		
						$r = AddQuestRep($user,150) or QuestDie();
						$m = AddQuestM($user,4,"Священник") or QuestDie();
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

						PutQItem($user,$item,"Священник",0,$todel,255,"eshop") or QuestDie();

						$msg = "<font color=red>Внимание!</font> Вы получили <b>Большой свиток «Восстановление ".$txt."HP»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					}

					if ($ai[0] == 10) {
						$r = AddQuestRep($user,200) or QuestDie();
						$m = AddQuestM($user,2,"Священник") or QuestDie();
						$e = AddQuestExp($user) or QuestDie();

						PutQItem($user,105,"Священник",7,$todel,255) or QuestDie();
			
						$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					}

					if ($ai[0] == 7) {
						$r = AddQuestRep($user,150) or QuestDie();
						$m = AddQuestM($user,2,"Священник") or QuestDie();
						$e = AddQuestExp($user) or QuestDie();

						PutQItem($user,50078,"Священник",0,$todel,255,"eshop") or QuestDie();
			
						$msg = "<font color=red>Внимание!</font> Вы получили <b>Познание Лабиринта</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
						addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();					
					}
	
					UnsetQuest($user) or QuestDie();
					UnsetQA();
					mysql_query('COMMIT') or QuestDie();
				} elseif (isset($_GET['qaction']) && $_GET['qaction'] == 2099) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты сделал то, что я просил?",
					2001 => "Принес святой отец, все согласно договору, четки самой что ни наесть ручной работы, от Пилигрима с наилучшими пожеланиями.",
					2099 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Ещё нет, мне нужно больше времени.",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}
		} 
	}


	if ($sf == "mlpiligrim") {
		if ($ai[0] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Ох польстил ты мне, добрый гость… Те четки сам себе сделал. Но уж если так сильно понравились они тебе – принеси мне шелковую нить, драгоценные бусины и деревянные звенья, я и тебе такие сделаю. Хотя честно признаюсь – как по мне – так Всевышнему глубоко плевать, что ты там в руках крутишь…",
						2 => "Буду безгранично тебе благодарен! Скоро вернусь! Пока! ",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
					1 => "Приветствую тебя! По делу пришел, совета просить. Видел я тебя как-то, во время твоей молитвы, помню, четки ты перебирал красоты необыкновенной. Не подскажешь – кто изготовил тебе такие?",
					11111 => "Просто проходил мимо, не обращай внимания.",
				);
			}	
		}
		if ($ai[0] == 1) {
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003201);
			$qi2 = QItemExistsID($user,3003202);
			$qi3 = QItemExistsID($user,3003203);
	
			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3);
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Вот и славно. Это займет буквально несколько минут. Присядь пока, подожди.",
						2 => "Благодарю великодушно, да уж лучше я постою…",
					);
				} elseif ($_GET['qaction'] == 2 && $mlqfound) {
					$mldiag = array(
						0 => "Ну, вот все и готово! Даже краше моих собственных вышли! Священнику передавай мое почтение.",
						3 => "Всенепременно передам. Пока!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003200,"Пилигрим",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Четки ручной работы</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 2;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					1 => "Да, все нашел: нитки, дерево, камушки…",
					11111 => "Пока не принес, скоро вернусь!",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		}
		if ($ai[0] == 3) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "К деревне? Конечно, подскажу! Нет ничего проще… Хм, что это за шум там, снаружи?",
						3 => "Понятия не имею. Впрочем, я бы на твоем месте сходил, проверил. А деревню я сам отыщу! Пока!",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 4;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} elseif ($_GET['qaction'] == 2) {
					// засада, драка с разбойниками
					mysql_query('START TRANSACTION') or QuestDie();
					StartQuestBattle($user,535) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
	
					unsetQA();
					Redirect('fbattle.php');
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
					1 => "Здравствуй! Кажется, я заблудился, не подскажешь, как мне выйти к деревне?...",
					2 => "Осторожнее! Это засада!",
					11111 => "Просто мимо проходил…",
				);
			}	
		}

		if ($ai[0] == 6) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Да, вроде цел. Мне нечем отблагодарить тебя… Хотя постой. Вот, возьми это. Тут скромная награда, за спасение от разбойников и в знак благодарности – мои четки.  Я сам их сделал! К сожалению,мне больше нечего тебе предложить.",
						2 => "Вот спасибо, очень красивые четки и приятный подарок… Еще увидимся!",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003200,"Пилигрим") or QuestDie();

					addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Четки ручной работы</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					// бонус квеста
					$ai[0] = 7;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Ох, спасибо тебе, что помог отбить нападение! Но откуда ты узнал о разбойничьей засаде?",
					1 => "Должно быть шестое чувство. С тобой все в порядке?",
				);
			}	
		}
	}

	if ($sf == "mlwood" && $ai[0] == 1) {
		$mlqfound = false;
		$qi1 = QItemExistsCountID($user,3003201,1);
	
		if ($qi1 === FALSE) {
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Что за звенья? Куда звенья?! Как от церкви?! За что?... Хотя постой. Я же не хожу в церковь… Будто дел у меня других нету, как же… Хотя и отлученным быть – совсем не здорово, а ну как завтра помру, как хоронить-то?... Так что, говоришь, за звенья?",
					2 => "Для четок молитвенных… Деревянные фрагменты. Что такое четки-то ты знаешь?",
				);
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				$mldiag = array(
					0 => "Ну, ты за дурака-то меня не держи, а-то как дам топором по лбу… Звенья, слово-то какое придумал.",
					3 => "Да хоть как их обзови, скажи только, поможешь или нет?!",
				);                         
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				$mldiag = array(
					0 => "Да помогу-помогу… Вот смотри – такие сгодятся? А священнику там передай что от Лесоруба, с наилучшими пожеланиями и аминь…  Чтоб он там у себя свечечку поставил или вроде того.",
					4 => "Отлично, то, что надо! Обязательно передам, что от лесоруба с наилучшими... Пока!",
				);
			} elseif ($_GET['qaction'] == 4 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003201,"Дровосек") or QuestDie();
				addchp ('<font color=red>Внимание!</font> Дровосек передал вам <b>Деревянные звенья</b> ','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
				1 => "Срочное дело у меня к тебе! Священнику необходимы деревянные звенья! Мрачнее тучи ходит, говорит, кто помогать откажется – на месяц от церкви отлучит!",
				11111 => "Я лучше пойду…",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}
	}

	if ($sf == "mlbuyer" && $ai[0] == 1) {
		$mlqfound = false;
		$qi1 = QItemExistsCountID($user,3003202,1);
	
		if ($qi1 === FALSE) {
			$mlqfound = true;
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "А золотишка священнику часом не надобно? Или денег для его прихода? А-то чтож, все же на свете знают – я же прямо-таки сама щедрость… 10 кредитов и камушки твои. И поверь это выгодное предложение.",
					2 => "У меня есть смутное подозрение, что кто-то ищет драки.",
					3 => "Хорошо, вот твои 10 кредитов.",
				);
				if ($user['money'] < 10) unset($mldiag[3]);
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				StartQuestBattle($user,535) or QuestDie();
				mysql_query('COMMIT') or QuestDie();

				unsetQA();
				Redirect('fbattle.php');
			} elseif ($_GET['qaction'] == 3 && $mlqfound && $user['money'] >= 10) {
				mysql_query('START TRANSACTION') or QuestDie();

				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']-10;
				$rec['target']=0;
				$rec['target_login']="Скупщик";
				$rec['type']=252; // плата квестовому боту
				$rec['sum_kr']=10;
				add_to_new_delo($rec) or QuestDie();

				// забираем деньги
				mysql_query('UPDATE oldbk.`users` set money = money - 10 WHERE id = '.$user['id']) or QuestDie();

				// даём вещь
				PutQItem($user,3003202,"Скупщик") or QuestDie();

				// системку
				addchp ('<font color=red>Внимание!</font> Скупщик передал вам <b>Драгоценные бусины</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				unsetQA();

				mysql_query('COMMIT') or QuestDie();				
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Проходи, проходи. Зачем в гости пожаловал?",
				1 => "Священнику для церковных дел позарез нужны драгоценные бусины. Немного, штук десять всего… За это он дарует тебе и твоим лесным дружкам-разбойникам – полную индульгенцию.",
				33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
				11111 => "Поздороваться зашел, уже ухожу.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}
	}

	if ($sf == "mlhunter" && $ai[0] == 1) {
		$mlqfound = false;
		$qi1 = QItemExistsCountID($user,3003203,1);
	
		if ($qi1 === FALSE) {
			$mlqfound = true;
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Ну, для спасения души чего не сделаешь… Тем паче что прощение мне есть за что просить… Вот, держи нитки.",
					2 => "Огромное спасибо! Пока!",
				);
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();

				// даём вещь
				PutQItem($user,3003203,"Охотник") or QuestDie();

				// системку
				addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Шелковая нить</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				unsetQA();

				mysql_query('COMMIT') or QuestDie();				
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
				1 => "Прости, прости… Тороплюсь очень… Слушай, дело к тебе сверхважное есть. Священнику для всяких божественных дел, не описать словами, как нужны шелковые нитки, а я же знаю, что тетиву ты плетешь как раз из таких. Не поможешь мне? На благо церкви так сказать… тороплюсь по своим, и только своим, делам.",
				11111 => "Я лучше пойду…",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}
	}

	if ($sf == "mlrouge") {
		if ($ai[0] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Ну как видишь у нас тут, в лесу, все люди – деловые. Шутки шутить не любим, все серьезно у нас… Что за дело?",
						2 => "Пилигрим владеет одной вещью, которую бы мне очень хотелось иметь у себя. Я понятно  выражаюсь?",
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Вполне. И что же ты можешь предложить нам за такую работу?",
						3 => "Ну а что может быть лучше старого-доброго золота, верно? Как насчет 5кр?",
						4 => "Как насчет услуги за услугу? Может быть, и я могу чем-то вам помочь?",
					);
					if ($user['money'] < 5) unset($mldiag[3]);
				} elseif ($_GET['qaction'] == 3 && $user['money'] >= 5) {
					$mldiag = array(
						0 => "5 монет? Да за такие деньги я бы даже не вышел за пределы нашего леса…",
						5 => "Хорошо, тогда может быть 10кр?",
						11111 => "Ну на нет и суда нет. Я пошел.",
					);
					if ($user['money'] < 10) unset($mldiag[5]);
				} elseif ($_GET['qaction'] == 5 && $user['money'] >= 10) {
					$mldiag = array(
						0 => "Вот это совсем другое дело! Гони монеты.",
						6 => "Держи 10 кр. И как мы все это… провернем?",
						11111 => "Я передумал. Я пошел.",
					);				
				} elseif ($_GET['qaction'] == 6 && $user['money'] >= 10) {
					$mldiag = array(
						0 => "Отправляйся к Пилигриму, а мы последуем за тобой и нападем, когда вы будете вместе. Так и нам не придется его выслеживать и на тебя не ляжет никаких подозрений.",
						7 => "Отлично, так и сделаем!",
					);
				} elseif ($_GET['qaction'] == 7 && $user['money'] >= 10) {
					// отдаём 10 кр
					mysql_query('START TRANSACTION') or QuestDie();
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-10;
					$rec['target']=0;
					$rec['target_login']="Разбойник";
					$rec['type']=252; // плата квестовому боту
					$rec['sum_kr']=10;
					add_to_new_delo($rec) or QuestDie();
	
					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 10 WHERE id = '.$user['id']) or QuestDie();

					$ai[0] = 3;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} elseif ($_GET['qaction'] == 4) {
					// услуга за услугу
					$mldiag = array(
						0 => "Да вообще-то есть тут одно дельце…  Уже неделю ждем поставку съестного от Трактирщика, а еды все как не было, так и нет. Деньги уплачены, хотелось бы и товар увидеть… Может быть он конечно забыл, ну так ты сходи, да напомни. А мы за это время как раз Пилигрима навестим, да вернемся.",
						8 => "По рукам!",
						11111 => "Это не приемлимо, я лучше пойду!",
					);
				} elseif ($_GET['qaction'] == 8) {
					mysql_query('START TRANSACTION') or QuestDie();

					$ai[0] = 8;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Смотрите-ка, к нам гость из города! Сам пришел, да вряд ли сам уйдет. И что же занесло тебя в наш лес?",
					1 => "Тише-тише, горячий лесной парень. По делу я пришел. Скорее даже с деловым предложением.",
					11111 => "Заблудился просто, уже ухожу…",
				);
			}	
		}
		if ($ai[0] == 4) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Четки-то у нас, да вот только больно уж они красивые. Жаль нам с ними расставаться. Сделка отменяется!",
						2 => "Как это отменяется? Не по чести это!",
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "А ты забыл, с кем ты говоришь? Какая честь у разбойников? Только выгода!",
						3 => "Зря вы так. Защищайтесь!",
					);
				} elseif ($_GET['qaction'] == 3) {
					// драка с разбойниками за чётки
					mysql_query('START TRANSACTION') or QuestDie();
					StartQuestBattle($user,535) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
	
					unsetQA();
					Redirect('fbattle.php');
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Ну, здравствуй. Ловко ты выманил Пилигрима. «Пойди, проверь», да, хитро, мы то уж думали все, провалили дельце, когда один из наших угодил ногой в ведро и наделал столько шума!",
					1 => "Да-да, это все очень интересно. Четки у вас?",
				);
			}	
		}

		///////////////
		if ($ai[0] == 8 || $ai[0] == 9) {
			$mlqfound = false;
			if ($ai[0] == 9) {
				$qi1 = QItemExistsID($user,3003204,1);
			} else {
				$qi1 = false;
			}
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}


			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "Четки-то у нас, да вот только больно уж они красивые. Жаль нам с ними расставаться. Сделка отменяется!",
						2 => "Как это отменяется? Не по чести это!",
					);
				} elseif ($_GET['qaction'] == 2 && $mlqfound) {
					$mldiag = array(
						0 => "А ты забыл, с кем ты говоришь? Какая честь у разбойников? Только выгода!",
						3 => "Зря вы так. Защищайтесь!",
					);
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					// драка с разбойниками за чётки
					mysql_query('START TRANSACTION') or QuestDie();
					StartQuestBattle($user,535) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
	
					unsetQA();
					Redirect('fbattle.php');
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес то, что я просил?",
					1 => "Да, вот еда от Трактирщика. Вы выполнили свою часть уговора? Четки у вас?",
					11111 => "Пока что нет, скоро вернусь…",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	
		}

	}

	if ($sf == "mlvillage") {
		if ($ai[0] == 8 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Кушать захотелось, бандитам?! Ну, так пусть они сперва за прошлую еду заплатят!",
						2 => "Они утверждают, что уже уплатили тебе нужную сумму.",
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Не знаю, за что они там платили, а я эти деньги включил в погашение их долга, за побитую в том месяце посуду! Хотят есть – пусть платят!",
						3 => "Боюсь, их не устроит такой ответ, а мне очень важно, что бы они выполнили работу, которую я им поручил. Так что лучше отдай мне разбойничью провизию.",
						5 => "Сколько они тебе должны?",
					);
				} elseif ($_GET['qaction'] == 3) {
					// драка с вышибалой ветка
					$mldiag = array(
						0 => "Слушай, ты вроде парень не глупый, не нарывался бы ты на неприятности? А то, смотри, Вышибалу позову!",
						4 => "Вот сейчас и проверим, кто из нас нарывается!",
					);
				} elseif ($_GET['qaction'] == 4) {
					mysql_query('START TRANSACTION') or QuestDie();
					StartQuestBattle($user,531) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
	
					unsetQA();
					Redirect('fbattle.php');				
				} elseif ($_GET['qaction'] == 5) {
					// ветка заплатить кр
					$mldiag = array(
						0 => "Да всего-то 8 кр. Я вообще не понимаю, к чему спорить о такой ничтожной сумме. Заплатили бы, да и делу конец…",
						6 => "Вот, держи свои 8 кр.",
						3 => "У меня нет лишних денег, а мне очень важно, что бы они выполнили работу, которую я им поручил. Так что лучше отдай мне разбойничью провизию.",
					);
					if ($user['money'] < 8) unset($mldiag[6]);
				} elseif ($_GET['qaction'] == 6 && $user['money'] >= 8) {
					$mldiag = array(
						0 => "Великолепно! С тобой приятно иметь дело! Вот, держи провизию, что я обещал Разбойникам и передавай им от меня пламенный привет!",
						7 => "Обязательно. Пока!",
					);
				} elseif ($_GET['qaction'] == 7 && $user['money'] >= 8) {
					mysql_query('START TRANSACTION') or QuestDie();
	
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-8;
					$rec['target']=0;
					$rec['target_login']="Трактирщик";
					$rec['type']=252; // плата квестовому боту
					$rec['sum_kr']=8;
					add_to_new_delo($rec) or QuestDie();
	
					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 8 WHERE id = '.$user['id']) or QuestDie();
	
					// даём вещь
					PutQItem($user,3003204,"Трактирщик") or QuestDie();
	
					// системку
					addchp ('<font color=red>Внимание!</font> Трактирщик передал вам <b>Еда  для Разбойников</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 9;
					UpdateQuestInfo($user,28,implode("/",$ai)) or QuestDie();

					mysql_query('COMMIT') or QuestDie();				
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "По делу, да только не по-своему.  Разбойники меня послали, велели тебя поторопить, а-то с голоду помирают в своем лесу.",
					11111 => "Кажется, я ошибся дверью…",
				);
			}
		}
	}


?>