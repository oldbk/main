<?php
	// квест пропавшая икона
	$q_status = array(
		0 => "Вернуть украденную икону",
		1 => "Разыскать преступников ограбивших церковь", // step 0
		2 => "Найти того, кому разбойники продали икону", // step 2
		3 => "Найти того, кто купил икону", // step 3
		4 => "Узнать у Священника как усмирить духа", // step 4
		5 => "Отнести рыцарю святую воду и свечу", // step 5
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 26) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlrouge" && $step == 0) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Мы берем то, что считаем нужным и то, что можно подороже продать, разумеется! Вот, к примеру, твой меч мне очень симпатичен…",
					3 => "Ну давай, рискни здоровьем…"
				);
			} elseif ($_GET['qaction'] == 3) {
				// бой с разбойником
				mysql_query('START TRANSACTION') or QuestDie();
				StartQuestBattle($user,535) or QuestDie();
				mysql_query('COMMIT') or QuestDie();					
				Redirect('fbattle.php');
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Смотрите-ка, к нам гость из города! Сам пришел, да вряд ли сам уйдет. И что же занесло тебя в наш лес?",
				1 => "Из церкви была похищена икона и моя интуиция подсказывает, что без вас тут не обошлось. Верните икону по-хорошему.",
				2 => "Просто прогуливаюсь по лесу, ничего особенного…",
			);
		}	
	}

	if ($sf == "mlrouge" && $step == 1) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "У нас её давно уж нет, мы не очень-то верим в вашего Б-га, мы верим в золото и серебро… Продали мы ту икону, спроси у Скупщика, быть может она все ещё у него.",
					3 => "Если ты солгал – я вернусь. И шестеро твоих гипотетических детей останутся сиротами.",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				SetQuestStep($user,26,2) or QuestDie();
				mysql_query('COMMIT') or QuestDie();					
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Пощади, умоляю, у меня шестеро детей, три младших брата и бабушка на иждивении!",
				1 => "Говори где икона, быстро!",
			);
		}	
	}

	if ($sf == "mlbuyer" && $step == 2) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Знаешь, вообще-то за такой тон мне следовало бы позвать своего, кхм, специалиста по охране жизни и здоровья, однако делать я этого не стану по той лишь причине, что иконы у меня все-равно уже нет.",
					2 => "А где же она, если не у тебя?!",
				);
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Продал одному частному коллекционеру. Я не раскрываю имена своих клиентов, особенно тем, кто вламывается в мой дом и начинает грубить, однако я уже наслышан, как лихо ты отделал беднягу-разбойника, так что намекну – покупатель живет один, но в его доме поместилась бы целая армия…",
					3 => "Кажется, я понял. Спасибо. Пока!",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();				
				SetQuestStep($user,26,3) or QuestDie();
				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Проходи, проходи. Зачем в гости пожаловал?",
				33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
				1 => "Разбойники украли древнюю икону из храма и сдали её тебе. Верни её иначе хуже будет…",
				11111 => "В лесу заблудился, уже ухожу…",
			);
		}
	} elseif ($sf == "mlbuyer") {
		$mldiag = array(
			0 => "Проходи, проходи. Зачем в гости пожаловал?",
			33333 => "Есть у меня кое-что для тебя интересное, а может и у тебя для меня. Не хочешь обменяться?",
			11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
		);
	}

	if ($sf == "mlknight" && $step == 3) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ох, должно быть ты говоришь о той старинной иконе. Ты хочешь сказать, что она была украдена? Я не знал об этом!",
					2 => "Думаю, священник из той церкви, что была разграблена, сможет простить тебя. Тебе только придется вернуть икону её законному владельцу.",
				);
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Я бы и рад, но не могу. Нет-нет, не смотри на меня так… Дело в том, что я купил икону только для того, что бы изгнать призрака, что обитает в стенах моего замка. Если бы ты только знал, сколько всего я уже перепробовал. Надеялся, что икона поможет, но она только ещё больше разозлила духа и теперь к ней не подойти!",
					3 => "Я расскажу об этом Священнику. Возможно, он сумеет что-то придумать. ",
				);
			} elseif ($_GET['qaction'] == 3) {
				$mldiag = array(
					0 => "Попроси у него что-то, что поможет изгнать этого жуткого духа, и я не останусь в долгу.",
					4 => "Скоро вернусь, не пропади тут без меня!",
				);
			} elseif ($_GET['qaction'] == 4) {
				mysql_query('START TRANSACTION') or QuestDie();				
				SetQuestStep($user,26,4) or QuestDie();
				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам?",
				1 => "Ты, случайно, в последнее время не покупал ничего ценного у недобросовестных продавцов? Или может быть у торговцев краденным?",
				11111 => "Извини, много дел, поболтаем в другой раз.",
			);
		}
	}

	if ($sf == "mlknight" && $step == 5) {
		$mlqfound = false;
		$qi1 = QItemExistsCountID($user,3003090,1);

		if ($qi1 !== FALSE) {
			$mlqfound = true;
			$todel = $qi1;
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Не будем мешкать! Как только беспокойный дух отступит – я верну тебе икону.",
					2 => "Приступим…",
				);
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				$mldiag = array(
					0 => "Кажется, все получилось! Он исчез! Хвала небесам. Вот, держи икону и передай от меня священнику, что я безгранично ему благодарен. И тебе, разумеется, тоже.",
					3 => "Вот и славно. Пока!",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				SetQuestStep($user,26,6) or QuestDie();

				PutQItem($user,3003089,"Рыцарь",0,$todel) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Рыцарь передал вам <b>Икона</b>";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
				1 => "Вот, держи, священник велел окропить святой водой все углы замка и зажечь свечу там, где призрак проявляет себя сильнее всего.",
				11111 => "Извини, много дел, поболтаем в другой раз.",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}
	}


	if ($sf == "mlvillage") {
		if ($step == 4 && ((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "Приведения, сын мой? Несчастные души, что не обрели покой. Вот, отнеси рыцарю святую воду и освященную свечу, пусть окропит водой каждый угол своего замка, а свечу зажжет там, где духи проявляют себя сильнее всего.",
						2004 => "Благодарю Вас, ваше святейшество, если это поможет – я уверен, Рыцарь не станет более удерживать икону у себя. Пока!",
					);
				} elseif ($_GET['qaction'] == 2099) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} elseif ($_GET['qaction'] == 2004) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,26,5) or QuestDie();

					PutQItem($user,3003090,"Священник") or QuestDie();

					$msg = "<font color=red>Внимание!</font> Священник передал вам <b>Святая вода и свеча</b>";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
					2001 => "К сожалению, нет, но я нашел икону! Сейчас она находится в замке Рыцаря, но вернуть он её не может по исключительно объективным причинам – у него какая-то проблема с не упокоенными душами, святой отец, посоветуйте как быть?",
					2099 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "К сожалению, нет.",
				);
			}
		} elseif ($step == 6 && ((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003089,1);
	
			if ($qi1 !== FALSE) {
				$mlqfound = true;
				$todel = $qi1;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "Сила веры, сын мой, сила веры. Но одной верой сыт не будешь, не так ли?...",
						2004 => "Воистину не будешь…",
					);
				} elseif ($_GET['qaction'] == 2099) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} elseif ($_GET['qaction'] == 2004 && $mlqfound) {
					// получаем награду
					mysql_query('START TRANSACTION') or QuestDie();
		
					$r = AddQuestRep($user,100) or QuestDie();
					$m = AddQuestM($user,2,"Священник") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
	
					PutQItem($user,4002,"Священник",0,$todel,255,"eshop") or QuestDie();

					$msg = "<font color=red>Внимание!</font> Вы получили <b>Большой Антидот</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
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
					2001 => "Да, вот, держи икону. Рыцарь передает тебе, что ты буквально спас его и его замок.",
					2099 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
					11111 => "Ещё нет, мне нужно больше времени.",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}
		} elseif ((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000)) {
			if (isset($_GET['qaction']) && $_GET['qaction'] == 2099) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			}
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
				2099 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				11111 => "К сожалению, нет.",
			);
		}
	}
?>