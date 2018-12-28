<?php
	// квест Пропавшая грамота 
	$q_status = array(
		0 => "Помочь Почтальону разыскать грамоту.",
		1 => "Сходить поговорить с Трактирщиком.",
		2 => "Сходить поговорить с Рыцарем.",
		3 => "Сходить поговорить с Лодочником.",
		4 => "Сходить поговорить с Разбойником.",
		5 => "Отнести грамоту.",
	);
	

	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 4) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlpost") {
		$mlqfound = false;
		$todel = QItemExistsID($user,3003015);
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Да, это та самая грамота! Вот спасибо! Я уж и не надеялся, что ты ее найдешь. Ты не представляешь, как для меня важна эта работа.  Как и обещал, с меня причитается.",
					3 => "Получить награду",
				);
			} elseif ($_GET['qaction'] == 5) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
                                mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// награда
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,200) or QuestDie();
				$m = AddQuestM($user,1,"Почтальон") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,105,"Почтальон",7,$todel,255) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();


				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
			);

			if ($mlqfound) $mldiag[1] = "Да, вот твоя грамота, она была у Разбойников.";
			$mldiag[5] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[2] = "Нет, я еще не нашел. Пойду дальше.";

		}
	}

	if ($sf == "mlvillage") {
		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Грамота, говоришь? Нет, не видал… Хотя мои постояльцы, если б что нашли, то сразу прибежали б менять на бутылочку портвейна. Но пока никто ничего не предлагал. Сходи к рыцарю, он вечно по дорогам туда-сюда скачет, может, подобрал где-нибудь.",
						3 => "Спасибо, так и сделаю."
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,4,1) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "Почтальон грамоту важную потерял. Ты не находил?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlknight") {
		if ($step == 1) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Да, стареет наш почтальон, вот уже и почту начал терять… Вчера ведь спорил со мной, что выпьет четыре бокала моего бургундского и не захмелеет. Так веришь, еле-еле ушел. Я уж думал, что мне его придется домой на себе тащить. А про грамоту, не знаю. Не находил. Спроси у лодочника, он всегда в курсе всех дел. ",
						3 => "Спасибо, так и сделаю."
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,4,2) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам?",
					1 => "Почтальон грамоту важную потерял. Ты не находил?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlboat") {
		if ($step == 2) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Да помню,  я переправлял я вчера Почтальона, а после него, кстати, Разбойников перевозил. Если он тут обронил, то наверняка они подобрали. Так что считай, пропала грамота. Не верю я, что Разбойники тебе ее вернут. Да и пойти-то к ним не каждый осмелится. ",
						4 => "Спасибо за совет, схожу к Разбойникам, я не из пугливых."
					);
				} elseif ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Переправа недорогая. Заплати 1 кредит, и поехали.",
						33333 => "Заплатить 1 кредит.",
						5 => "Попрощаться и уйти.",
					);
				} elseif ($_GET['qaction'] == 4) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,4,3) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая.",
					1 => "Почтальон грамоту важную потерял. Ты не находил?",
					2 => "Мне бы переправиться на ту сторону. Сколько это будет стоить?",
					3 => "Не надо, я просто проходил мимо. Пойду дальше."
				);
			}
		} else {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2) {
					$mldiag = array(
						0 => "Переправа недорогая. Заплати 1 кредит, и поехали.",
						33333 => "Заплатить 1 кредит.",
						4 => "Попрощаться и уйти.",
					);
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая.",
					2 => "Мне бы переправиться на ту сторону. Сколько это будет стоить?",
					3 => "Не надо, я просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlrouge") {
		if ($step == 3 && !QItemExists($user,3003015)) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Вот это потеха!  Думаешь, значит, что  грамота у нас?  Ну а даже если и у нас, то, тебе-то что с того? Или ты ждешь, что  мы ее тебе так просто отдадим? Ну, ладно мы сегодня добрые  - можем тебе ее продать,  или может у тебя хватит смелости забрать ее у нас силой ?",
						3 => "Нет у меня времени драки устраивать. Вот 10 кредитов за грамоту.",
						4 => "И не только смелости хватит, а еще и сил проучить Вас.",
						5 => "Я, пожалуй, пойду дальше.",
					);

					if ($user['money'] < 10) unset($mldiag[3]);
				} elseif ($_GET['qaction'] == 3 && $user['money'] >= 10) {
					// 10 кредитов за грамоту
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
					add_to_new_delo($rec) or QuestDie(); //юзеру

					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 10 WHERE id = '.$user['id']) or QuestDie();

					// даём вещь
					PutQItem($user,3003015,"Разбойник") or QuestDie();

					// системку
					addchp ('<font color=red>Внимание!</font> Разбойник передал вам <b>Грамота</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} elseif ($_GET['qaction'] == 4) {
					// деремся с разбойником
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
					1 => "Почтальон грамоту важную потерял. Ты не находил?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}
?>