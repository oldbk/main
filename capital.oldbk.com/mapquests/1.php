<?php
	// квест лечебное снадобье
	$q_status = array(
		0 => "Охотнику принести снадобье от ведьмы (%N1%/1).",
		1 => "Принести ведьме 5 крысиных хвостов (%N1%/5), помет дракона (%N2%/1) и спирт (%N3%/1).",
	);
	

	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 1) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlhunter") {
		// здесь проверяем наличие снадобья
		$mlqfound = false;
		$todel = QItemExistsID($user,3003004);
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == "1" && $mlqfound) {
				$mldiag = array(
					"0" => "Вот, спасибо, теперь я быстро встану на ноги! Не хочу быть неблагодарным. Мясо дичи в здешних лесах обладает необыкновенными свойствами. За твою помощь я накормлю тебя мясным обедом, который сделает тебя значительно сильнее.",
					"111" => "Получить награду",
				);
			} elseif ($_GET['qaction'] == "2") {
				mysql_query('START TRANSACTION') or QuestDie();
				UnsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				UnsetQA();			
			} elseif ($_GET['qaction'] == "111" && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,150) or QuestDie();
				$m = AddQuestM($user,1,"Охотник") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,105,"Охотник",7,$todel,255,'shop',3) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();				
			} else {
				UnsetQA();
			}
		} else {
			$mldiag = array(
				"0" => "Привет, ты принес мне то, что я просил?",
			);		
			
			if ($mlqfound) $mldiag[1] = "Да, вот тебе снадобье от Ведьмы. Надеюсь, оно тебе поможет.";
			
			$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)"; 
			$mldiag[3] = "Нет, я еще не все собрал. Пойду дальше."; 
		}
	}

	if ($sf == "mlwitch") {
		if ($step == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == "1") {
					$mldiag = array(
						"0" => "Вот как? Мне ночью приснилось что Охотник болеет. Ну что-ж, это хорошо, что он  нашел, кого послать за зельем. Ему я всегда готова помочь, но вот беда, у меня  нет необходиых вещей для его изготовления. Если ты принесешь мне 5 крысиных хвостов, помет дракона и спирт, я все быстро приготовлю, и охотник будет доволен.",
						"3" => "Хорошо, я принесу тебе все, что нужно.",
						"4" => "Нет, это слишком сложно, я пойду дальше по своим делам.",
					);
				} elseif ($_GET['qaction'] == "3") {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,1,1) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					UnsetQA();
				} else {
					UnsetQA();
				}
			} else {
				$mldiag = array(
					"0" => "Проходи, проходи, чужеземец … Что привело тебя в мои края?",
					"1" => "Охотник сильно заболел, просил принести ему снадобье от тебя.",
					"2" => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		} elseif ($step == 1) {
			// здесь проверяем наличие крысиных хвостов, помёта и спирта
			$mlqfound = false;
			$qi1 = QItemExistsID($user,3003001);
			$qi2 = QItemExistsCountID($user,3003002,5);
			$qi3 = QItemExistsID($user,3003003);

			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3);
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					// выдаём лечебное снадобье
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003004,"Ведьма",0,$todel) or QuestDie();;

					addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>Лечебное снадобье</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					SetQuestStep($user,1,2) or QuestDie();;
                                        mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просила?",
				);

				if ($mlqfound) $mldiag[1] = "Да, вот держи все, что нужно для зелья.";
	
				$mldiag[2] = "Нет, я еще не все собрал. Пойду дальше.";
			}
		} elseif (isset($_GET['qaction'])) {
			UnsetQA();
		}
	}

	if ($sf == "mlvillage") {
		// 3003001 - спирт

		if ($step == 1 && !QItemExists($user,3003001) && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == "1") {
					$mldiag = array(
						0 => "Опять эта старая ведьма подсылает ко мне кого-то. Черт знает, что она с этим спиртом делает. Всем рассказывает, что варит снадобья, но как по мне, в ее снадобьях столько же спирта, сколько в муравьиной моче. А вот в ее крови, спирт не выветривается никогда. Ну да ладно, все равно не могу я ей отказать. Когда-то она вылечила меня от страшной болезни, а добро я помню. Если ты хочешь спирт, заплати мне 1 кр и получишь бутылочку. Но если у тебя нет денег, и ты считаешь себя достаточно сильным,  – подерись с моим Вышибалой, у него вечно чешутся кулаки. Если сможешь его побить, я дам тебе спирт в награду. ",
					);

					if ($user['money'] >= 1) $mldiag[3] = "Заплатить 1 кр и взять спирт";
					if ($user['hp'] >= 2) $mldiag[4] = "Подраться с Вышибалой";

					$mldiag[5] = "Повернуться и уйти.";
				} elseif ($_GET['qaction'] == 3 && $user['money'] >= 1) {
					mysql_query('START TRANSACTION') or QuestDie();					
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-1;
					$rec['target']=0;
					$rec['target_login']="Трактирщик";
					$rec['type']=252; // плата квестовому боту
					$rec['sum_kr']=1;
					add_to_new_delo($rec) or QuestDie();

					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 1 WHERE id = '.$user['id']) or QuestDie();

					// даём вещь
					PutQItem($user,3003001,"Трактирщик") or QuestDie();

					// системку
					addchp ('<font color=red>Внимание!</font> Трактирщик передал вам <b>Спирт</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					unsetQA();
					mysql_query('COMMIT') or QuestDie();
				} elseif ($_GET['qaction'] == 4 && $user['hp'] >= 2) {
					// 530 - вышибала
					mysql_query('START TRANSACTION') or QuestDie();
					StartQuestBattle($user,531) or QuestDie();
					mysql_query('COMMIT') or QuestDie();					
					Redirect('fbattle.php');
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "По делу. Ведьме нужен спирт для лечебного снадобья. У тебя есть?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}
?>