<?php
	// квест Новые стрелы 
	$q_status = array(
		0 => "Помочь Охотнику собрать древки для стрел (%N1%/1), перья (%N2%/10) и яд (%N3%/1).",
		1 => "Убить 10 волков в лесу вокруг хижины Лесоруба (%N1%/10)",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 7) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlhunter") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003023);
		$qi2 = QItemExistsCountID($user,3003024,10);
		$qi3 = QItemExistsID($user,3003025);

		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3);
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == "1" && $mlqfound) {
				// всё достал
				$mldiag = array(
					0 => "Хорошая работа, быстро управился. Теперь и у меня работа пойдет. Наделаю стрел и на охоту! Держи обещанную награду!",
					7 => "Благодарю (получить награду)",
				);
			} elseif ($_GET['qaction'] == "7" && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,250) or QuestDie();
				$m = AddQuestM($user,3,"Охотник") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				if ($user['level'] == 6) {
					$item = 33029;
				} elseif ($user['level'] == 7) {
					$item = 33030;
				} else {
					$item = 33031;
				}


				PutQItem($user,$item,"Охотник",0,$todel,255) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Обед воина</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();


				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
				
				
			} elseif ($_GET['qaction'] == "2") {
				$todel = QItemExistsID($user,3003022);
				mysql_query('START TRANSACTION') or QuestDie();
				if ($todel !== FALSE) {
					PutQItemTo($user,"Охотник",$todel) or QuestDie();
				}

				UnsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				UnsetQA();			
			} elseif ($_GET['qaction'] == "4" && !QItemExists($user,3003022)) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003022,"Охотник",0,array()) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Копченный окорок</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				UnsetQA();
			} else {
				UnsetQA();
			}
		} else {
			$mldiag = array(
				"0" => "Привет, ты принес мне то, что я просил?",
			);		
			
			if ($mlqfound) $mldiag[1] = "Да, вот твои древки, орлиные перья и яд для стрел.";
			$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)"; 
			$mldiag[3] = "Нет, я еще не все достал. Пойду дальше."; 
			if (!QItemExists($user,3003022)) $mldiag[4] = "Нет, я еще не все достал, но проголодался. Ты был прав насчет окорока. Не дашь ли немного подкрепиться?"; 
		}
	}
	if ($sf == "mlwood") {
		if (QItemExists($user,3003023)) {
			return;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && !$questexist['addinfo']) {
				$mldiag = array(
					0 => "Ну, Охотнику помочь всегда благое дело. Суровый мужик и с понятиями. Хоть и пить начал, но порядочности и нюха не потерял. Зверье лесное чует как никто. И не жмот, всегда угостит свежей дичью. Древки я тебе дам, но сначала помоги и мне. Вокруг моей хижины последнее время волчий вой раздается. Неуютно по ночам стало. Стая немалая, думаю, волков 10 уже бегает. Убей их и возвращайся, а я в долгу не останусь.",
					5 => "Договорились, очищу лес от волков.",
				);
			} elseif ($_GET['qaction'] == 3 && $questexist['addinfo'] == 11) {
				$mldiag = array(
					0 => "Вот спасибо, а то знаешь, мороз по коже от этого воя. Держи свои древки, и привет Охотнику от меня передай. Скажи, скоро загляну в гости.",
					6 => "Обязательно передам. Счастливо.",
				);
			} elseif ($_GET['qaction'] == 6 && $questexist['addinfo'] == 11) {
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003023,"Лесоруб",0,array()) or QuestDie();
				addchp ('<font color=red>Внимание!</font> Лесоруб передал вам <b>Древки для стрел</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				UpdateQuestInfo($user,7,0) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 5) {
				mysql_query('START TRANSACTION') or QuestDie();
				UpdateQuestInfo($user,7,1) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			if ($questexist['addinfo']) {
				$mldiag = array(0 => "Привет, ты сделал то, что я просил?");
				if ($questexist['addinfo'] == 11) {
					// убили всех
					$mldiag[3] = "Да, волков в твоем лесу больше не осталось, можешь спать спокойно.";
				}
				$mldiag[4] = "Нет, я еще не все сделал. Пойду дальше.";
			} else {
				$mldiag = array(
					0 => "Эх, опять городские пожаловали. И неймется вам в городе, что вас так в лес тянет. Грибы собираешь или дров просить пришел?",
					1 => "Охотник просит древка для стрел новых. Ты не поможешь?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlwitch") {
		if (QItemExists($user,3003025)) {
			return;
		}

		$mlqfound = false;
		$todel = QItemExistsID($user,3003022);
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(0 => "Ох уж этот охотник все ему дай, да принеси, а обо мне бы кто позаботился? Хоть передал бы когда дичи свежей или еще чего. Так нет, все ему да ему. Давеча присылал кого-то за мазями от порезов, сегодня тебя за ядом подослал. Я ему в работники не нанималась!");
				if ($mlqfound) $mldiag[3] = "Ну конечно, как я мог забыть! Он же окорок копченый прислал тебе в благодарность!";
				$mldiag[4] = "Ну, раз так, то делать нечего.  Пойду дальше.";
			} elseif ($_GET['qaction'] == 6 && $mlqfound) {
				// забираем яд
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003025,"Ведьма",0,$todel) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Яд</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				$mldiag = array(
					0 => "Надо же, окорок… Что-то на него не похоже. Не свой ли ты мне окорок притащил? Ну да ладно, хочешь своей едой за него расплачиваться – дело твое. Держи яд и ступай с миром.",
					6 => "Спасибо (забрать яд)",
				);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края?",
				1 => "Пришел попросить яду для охотничьих стрел.",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	}
?>