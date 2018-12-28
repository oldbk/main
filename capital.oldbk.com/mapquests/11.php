<?php
	// квест чемпион
	$q_status = array(
		0 => "Узнать про подпольные бои.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 11) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlfort") {
		$mlqfound = false;
		$todel = QItemExistsID($user,3003039);
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $step == 4 && $mlqfound) {
				$mldiag = array(
					0 => "Ух ты, какая кружка! Ручной работы… надо же… Ты уж извини, но кружку мне придется забрать в доказательство. А тебе, думаю, золотые кругляшки не помешают. Оставь себе монеты, что в ней насыпаны.",
					10 => "Спасибо, всегда готов помочь (получить награду)",
				);
			} elseif ($_GET['qaction'] == 10 && $step == 4 && $mlqfound) {
				// получаем награду с кружкой
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,200) or QuestDie();
				$m = AddQuestM($user,3,"Охранник") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItemTo($user,"Охранник",$todel) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 11 && $step == 4 && !$mlqfound) {
				// получаем награду без кружки
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,200) or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>".$r."</b> репутации, <b>".$e."</b> опыта за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 2 && $step == 4 && !$mlqfound) {
				$mldiag = array(
					0 => "Хмм… А слухи говорили другое… Но я тебе верю, ты воин серьезный, и если уж говоришь,  что все проверил, значит и правда там нет никакой нелегальщины. Ну, это и к лучшему, мне бы не хотелось разочароваться в Трактирщике. Слишком забористый у него эль и хорошо прожаренные отбивные. А за помощь твою, держи небольшую награду.",
					11 => "Спасибо, всегда готов помочь (получить награду)",
				);
			} elseif ($_GET['qaction'] == 5) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();				
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты сделал то, о чем я просил?",
			);
			if ($step == 4) {
				if ($mlqfound) {
					$mldiag[1] = "Да,  вот кружка, которую дают за победу в нелегальных боях в Трактире";
				} else {
					$mldiag[2] = "Да, я все проверил, ни о каких нелегальных боях в Трактире речи нет.";
				}
				
			}
			$mldiag[5] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[11111] = "Нет, я еще не успел, пойду дальше.";
		}	
	}

	if ($step == 0 && (isset($_GET['quest']) && $_GET['quest'] == 1 || isset($_GET['qaction']) && $_GET['qaction'] < 1000)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Да ну, перестань. Какие бои, какой приз? Мордобой у нас в заведении почти каждый день, это да. Но призов за это не выдают, разве что пара красивейших синяков на память. Хахахаха!!!...",
					3 => "Да нет, я о других боях говорю. Понимаю, ты не очень доверчив к незнакомцам. Но может 10 монет помогут мне сделать ставку в этот турнир?",
				);
			} elseif ($_GET['qaction'] == 5 && $user['money'] >= 10) {
					mysql_query('START TRANSACTION') or QuestDie();
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-10;
					$rec['target']=0;
					$rec['target_login']="Трактирщик";
					$rec['type']=252; // плата квестовому боту
					$rec['sum_kr']=10;
					add_to_new_delo($rec) or QuestDie(); //юзеру

					// забираем деньги
					mysql_query('UPDATE oldbk.`users` set money = money - 10 WHERE id = '.$user['id']) or QuestDie();

					SetQuestStep($user,11,1) or QuestDie();
					$step = 1;
					$_GET['qaction'] = 1;
					mysql_query('COMMIT') or QuestDie();
			} elseif ($_GET['qaction'] == 4) {
					$mldiag = array(
						0 => "Ну, хорошо, тебе повезло. Ты зашел как-раз вовремя. У тебя есть шанс сразиться с пятью лучшими нашими бойцами. Все они не раз побеждали в турнирах, и одержать над ними победу будет нелегко. Если хочешь, давай 10 кредитов и можешь испытать свои силы и удачу.",
					);
					if ($user['money'] >= 10) $mldiag[5] = "Да, вот деньги, я согласен вступить с ними в бой.";
					$mldiag[6] = "Извини, но у меня нет сейчас столько денег.";
			} elseif ($_GET['qaction'] == 3) {
				$mldiag = array(
					0 => "Хмм… монеты говоришь? Вижу, ты человек серьезный. Ну ладно, уговорил. Надеюсь, не сдашь меня, а то будут у меня большие проблемы.",
					4 => "Что ты, конечно, не сдам, просто кулаки чешутся."
				);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
				1 => "Не совсем по делу, но с интересом. Ходят слухи о каких-то боях, которые у тебя в трактире бывают. И говорят, что приз за эти бои какой-то особенный. Я боец неплохой, может попытаю счастья.",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}	
	}

	if ($step == 1 && (isset($_GET['quest']) && $_GET['quest'] == 1 || isset($_GET['qaction']) && $_GET['qaction'] < 1000)) {
		$ai = explode("/",$questexist['addinfo']);
		for ($i = 0; $i < count($ai); $i++) {
			if ($ai[$i] == 1) {
				// проиграл битву
				mysql_query('START TRANSACTION') or QuestDie();
				SetQuestStep($user,11,2) or QuestDie();
				$step = 2;
				mysql_query('COMMIT') or QuestDie();
				unset($_GET['qaction']);
			}
		}

		$bgood = true;
		for ($i = 0; $i < count($ai); $i++) {
			if ($ai[$i] != 2) {
				$bgood = false;
			}
		}
		if ($bgood) {
			// выиграл всех
			mysql_query('START TRANSACTION') or QuestDie();
			SetQuestStep($user,11,3) or QuestDie();
			mysql_query('COMMIT') or QuestDie();
			$step = 3;
			unset($_GET['qaction']);
		}


		if ($step == 1) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(0 => "Выбирай");
					if ($ai[0] == 0) $mldiag[2] = "Вышибала 1";
					if ($ai[1] == 0) $mldiag[3] = "Вышибала 2";
					if ($ai[2] == 0) $mldiag[4] = "Вышибала 3";
					if ($ai[3] == 0) $mldiag[5] = "Вышибала 4";
					if ($ai[4] == 0) $mldiag[6] = "Вышибала 5";
				} elseif ($_GET['qaction'] == 6 && $ai[4] == 0) {
					// танк
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[4] = 1;					
					UpdateQuestInfo($user,11,implode("/",$ai)) or QuestDie();
					unsetQA();
					StartQuestBattle($user,531,array(
						'krit_mf' => 120,
						'akrit_mf' => 100,
						'uvor_mf' => 100,
						'auvor_mf' => 120,
						'bron1' => 16,
						'bron2' => 17,
						'bron3' => 13, 
						'bron4' => 14,
					)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					Redirect('fbattle.php');
				} elseif ($_GET['qaction'] == 5 && $ai[3] == 0) {
					// антиуворот
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[3] = 1;
					UpdateQuestInfo($user,11,implode("/",$ai)) or QuestDie();
					unsetQA();
					StartQuestBattle($user,531,array(
						'krit_mf' => 80,
						'akrit_mf' => 135,
						'uvor_mf' => 130,
						'auvor_mf' => 130,
						'bron1' => 16,
						'bron2' => 17,
						'bron3' => 13, 
						'bron4' => 14,
					)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					Redirect('fbattle.php');
				} elseif ($_GET['qaction'] == 4 && $ai[2] == 0) {
					// антикрит
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[2] = 1;
					unsetQA();
					UpdateQuestInfo($user,11,implode("/",$ai)) or QuestDie();
					StartQuestBattle($user,531,array(
						'krit_mf' => 150,
						'akrit_mf' => 185,
						'uvor_mf' => 100,
						'auvor_mf' => 100,
						'bron1' => 16,
						'bron2' => 27,
						'bron3' => 13, 
						'bron4' => 14,
					)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					Redirect('fbattle.php');
				} elseif ($_GET['qaction'] == 3 && $ai[1] == 0) {
					// уварот
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[1] = 1;
					UpdateQuestInfo($user,11,implode("/",$ai)) or QuestDie();
					unsetQA();
					StartQuestBattle($user,531,array(
						'krit_mf' => 150,
						'akrit_mf' => 50,
						'uvor_mf' => 50,
						'auvor_mf' => 150,
						'bron1' => 16,
						'bron2' => 17,
						'bron3' => 13, 
						'bron4' => 14,
					)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					Redirect('fbattle.php');
				} elseif ($_GET['qaction'] == 2 && $ai[0] == 0) {
					// крит
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,11,implode("/",$ai)) or QuestDie();
					unsetQA();
					StartQuestBattle($user,531,array(
						'krit_mf' => 150,
						'akrit_mf' => 155,
						'uvor_mf' => 86,
						'auvor_mf' => 141,
						'bron1' => 16,
						'bron2' => 17,
						'bron3' => 13, 
						'bron4' => 14,
					)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					Redirect('fbattle.php');
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Вернулся? Выбирай противника.",
					1 => "Давай.",
				);
			}
		}
	}

	if ($step == 2 && (isset($_GET['quest']) && $_GET['quest'] == 1 || isset($_GET['qaction']) && $_GET['qaction'] < 1000)) {
		// проиграл
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,105,"Трактирщик",7,array(),255,"shop",3) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b> за участие в нелегальном турнире!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				SetQuestStep($user,11,4) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA(); 
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Хм… Не такой уж ты сильный боец, как о себе рассказывал. Но ничего, может когда-нибудь в следующий раз тебе повезет больше. Не хочется тебя отпускать ни с чем, держи на дорогу мой фирменный бутерброд и распрощаемся.",
				1 => "Спасибо, еще увидимся.",
			);
		}	
	}

	if ($step == 3 && (isset($_GET['quest']) && $_GET['quest'] == 1 || isset($_GET['qaction']) && $_GET['qaction'] < 1000)) {
		// выиграл всех
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003039,"Трактирщик",0,array()) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Кружка с монетами</b> за участие в нелегальном турнире!";

				SetQuestStep($user,11,4) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Ох, ну ты и силен! Не думал я, что ты с ними справишься. Но раз победил, то положена тебе награда. Вот держи кружку пивную ручной работы. В нее помещается целая пинта славного, доброго эля. Ну, или приличная горсть золотых монет.",
				1 => "Красивая кружка, да и турнир был непростым! ",
			);
		}		
	}

?>