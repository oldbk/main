<?php
	// квест магическая сила
	$q_status = array(
		0 => "Принести Магу чистой речной воды (%N1%/1), заячью лапу (%N2%/1) и древний манускрипт (%N3%/1).",
		1 => "Сходить к Ведьме и узнать про манускрипт.",
		2 => "Сходить к Разбойнику и узнать про манускрипт.",
		3 => "Сходить к Скупщику и узнать про манускрипт.",
		4 => "Сходить к Пилигриму и к Трактирщику и узнать про манускрипт.",
		5 => "Принести Пилигриму Зуб дракона (%N1%/1).",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 14) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlmage") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003047);
		$qi2 = QItemExistsID($user,3003048);
		$qi3 = QItemExistsID($user,3003050);
		$qi4 = QItemExistsID($user,3003051);
		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE && $qi4 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2,$qi3,$qi4);
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Ну, что ж, ты неплохо справился с поручением. Держи свою награду за работу.",
					5 => "Удачи (взять награду)",
				);
			} elseif ($_GET['qaction'] == 5 && $mlqfound) {
				// награда
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,150) or QuestDie();
				$m = AddQuestM($user,1,"Маг") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				PutQItem($user,105,"Маг",7,$todel,255,"shop",3) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();


				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();					
				UnsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				UnsetQA();			
			} else {
				UnsetQA();
			}
			
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
			);
			if ($mlqfound) $mldiag[1] = "Да,  вот тебе заячья лапка, чистая речная вода и манускрипт.";
        		$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[3] = "Нет, я еще не все собрал. Пойду дальше.";
		}
	}

	if ($sf == "mlwitch" && $ai[0] == 0) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ах, да, знаю, о чем ты говоришь. Был такой манускрипт у меня, да только в прошлом году исчез. Украл его кто-то. Рукопись древняя, ценная, наверняка ради наживы забрали. Боюсь, без разбойников тут не обошлось. Да я к ним соваться не стала, не люблю с ними связываться.",
					3 => "Ясно, спасибо, попробую поискать",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[0] = 1;
				UpdateQuestInfo($user,14,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края? ",
				1 => "Ищу я древний манускрипт, который, говорят, у тебя хранился. Маг собирается делать какой-то важный обряд и прислал меня за этим свитком.",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	}

	if ($sf == "mlrouge" && $ai[0] == 1) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ха-Ха! Да, было дело! Красивая была бумажка. Ведьма, дура старая, двери на простую щеколду закрывает, думает, ее бормотания на крыльце кого-то напугают. Правда, после этого один из наших долго болел, но ничего, не окочурился. Лихорадку подхватил наверняка, но уж всяко не от ее наговоров. А бумажку ту мы давно продали Скупщику. Можешь к нему отправляться, если не лень ноги топтать.",
					3 => "Ясно, схожу, пожалуй, к Скупщику.",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[0] = 2;
				UpdateQuestInfo($user,14,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Смотрите-ка, к нам гость из города! Сам пришел, да вряд ли сам уйдет.  И что же занесло тебя в наш лес?",
				1 => "Я манускрипт древний разыскиваю. Когда-то он был у Ведьмы, но говорят, пропал. Не ваших ли это рук дело? Может, продадите мне его?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	}

	if ($sf == "mlbuyer" && $ai[0] == 2) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Хмм.. Может и знаю… Была эта бумажка однажды у меня. Хотел я ее продать Пилигриму, но там история смешная получилась. Когда Пилигрим ее разглядывал, ко мне как-раз заглянул Трактирщик. Увидал грамоту и давай ее у Пилигрима выдирать. А тот вцепился, не отдает. Бумажка-то древняя, многим ее получить хочется. Ну и порвали они ее на две части. Правда потом убытки мне оплатили оба, но бумажку так пополам по домам и растащили. Так что ищи половину у Пилигрима, а вторую у Трактирщика. Если, конечно, они ее давно не выкинули.",
					3 => "Спасибо за рассказ, так и сделаю.",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[0] = 3;
				UpdateQuestInfo($user,14,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Проходи, проходи. Зачем в гости пожаловал?",
				33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
				1 => "Ищу грамоту одну, древний манускрипт. Когда-то она была у Ведьмы, потом у Разбойников, а потом, говорят, ее продали-перепродали. Ты многих продавцов и покупателей знаешь. Может, знаешь, у кого она сейчас?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	} elseif ($sf == "mlbuyer") {
		$mldiag = array(
			0 => "Проходи, проходи. Зачем в гости пожаловал?",
			33333 => "Есть у меня кое-что для тебя интересное. Не хочешь посмотреть, а может и купить?",
			11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
		);
	}

	if ($sf == "mlpiligrim" && $ai[0] == 3) {
		if ($ai[1] == 0) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "А, да! Был такой случай. А зачем тебе этот клочок? Я уж и не вспомню, наверное, куда я его задевал. Может, выкинул, а может, камин растопил зимой.",
						3 => "Да, оказывается, там какое-то заклинание древнее было написано. Маг просил меня помочь ему разыскать.",
					);
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						0 => "Хм… Ну с  Магом я б связываться не стал, хоть это и твое дело, но лишний раз к нему заглядывать не стоит. Но, раз уж ввязался ты в это, постараюсь тот обрывок разыскать. Рукопись ценная, как погляжу. Просто так  не отдам, но обменяю. Я давно мечтаю сделать себе кулон из Драконьего зуба. Вот на него, я манускрипт поменял бы, пожалуй.",
						5 => "Договорились, добуду тебе зуб Дракона.",
					);
				} elseif ($_GET['qaction'] == 5) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[1] = 1;
					UpdateQuestInfo($user,14,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, наконец, и ко мне кто-то забрел. Здесь редко бывают гости. Признавайся, ты просто заглянул поболтать или тебя привело дело?",
					1 => "Да вот, заглянул по делу небольшому. Помнишь, где-то год назад вы с Трактирщиком древний манускрипт разорвали пополам? Может, сохранилась у тебя эта половина? Ничего особенного, просто проходил мимо. Пойду дальше.",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		} elseif ($ai[1] == 1) {
			$mlqfound = false;
			$todel = QItemExistsID($user,3003049);
			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003047,"Пилигрим",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Пилигрим передал вам <b>Половина рукописи</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 2;
					UpdateQuestInfo($user,14,implode("/",$ai)) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты принес мне то, что я просил?",
				);
				if ($mlqfound) $mldiag[1] = "Да, вот тебе Драконий зуб. Давай рукопись.";
				$mldiag[2] = "Нет, я еще не успел, пойду дальше.";
			}		
		}
	}

	if ($sf == "mlvillage" && $ai[0] == 3) {
		if ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && $_GET['qaction'] < 1000)) {
			if ($ai[2] == 0) {
				if (isset($_GET['qaction'])) {
					if ($_GET['qaction'] == 1) {
						$mldiag = array(
							0 => "Хы, вот уж не думал, что этот клочок бумаги кому-то понадобится. Но раз ты спрашиваешь, значит и в нем есть толк. А раз такое дело, просто так я тебе его теперь не отдам. Меньше, чем за 5 кредитов, даже не думай. А если вдруг решишь забрать силой, учти, мой вышибала тебе мозги  вышибет.",
							3 => "Вышибала, говоришь? Давай его сюда!",
						);
						if ($user['money'] >= 5) $mldiag[4] = "Нет проблем, вот 5 кредитов, давай грамоту.";
						$mldiag[11111] = "Некогда мне бегать в поисках всякой ерунды. ";
					} elseif ($_GET['qaction'] == 3) {
						mysql_query('START TRANSACTION') or QuestDie();
						StartQuestBattle($user,531) or QuestDie();
						mysql_query('COMMIT') or QuestDie();					
						Redirect('fbattle.php');
						unsetQA();
					} elseif ($_GET['qaction'] == 4 && $user['money'] >= 5) {
						mysql_query('START TRANSACTION') or QuestDie();					
						$rec = array();
			    			$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money']-5;
						$rec['target']=0;
						$rec['target_login']="Трактирщик";
						$rec['type']=252; // плата квестовому боту
						$rec['sum_kr']=5;
						add_to_new_delo($rec) or QuestDie();
	
						// забираем деньги
						mysql_query('UPDATE oldbk.`users` set money = money - 5 WHERE id = '.$user['id']) or QuestDie();
	
						// даём вещь
						PutQItem($user,3003048,"Трактирщик") or QuestDie();
	
						// системку
						addchp ('<font color=red>Внимание!</font> Трактирщик передал вам <b>Половину рукописи</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
						$ai[2] = 1;
						UpdateQuestInfo($user,14,implode("/",$ai)) or QuestDie();
						unsetQA();
						mysql_query('COMMIT') or QuestDie();
					} else {
						unsetQA();
					}
				} else {
					$mldiag = array(
						0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
						1 => "Привет, я по делу. Говорят, у тебя есть половина древней рукописи. А вторая половина осталась у Пилигрима. Если она тебе не нужна, может, отдашь?",
						2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
					);
				}
			}
		}
	}

	if ($sf == "mlboat") {
		$mlqfound = QItemExists($user,3003050);

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ну, конечно, кто как не лодочник может достать самой лучшей речной водицы. На середине реки она самая чистая и холодная. Но одному мне туда плыть неохота. Заплати мне за переправу, там и наберем вместе.",
				);

				if ($user['money'] >= 1) {
					$mldiag[3] = "Хорошо, держи 1 кр., и поплыли.";
				} else {
					$mldiag[11111] = "Извини, но денег нет.";
				}
			} elseif ($_GET['qaction'] == 3 && $mlqfound === FALSE) {
				// переправляемся
				mysql_query('START TRANSACTION') or QuestDie();
				$rec = array();
				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']-1;
				$rec['target']=0;
				$rec['target_login']="Лодочник";
				$rec['type']=252; // плата квестовому боту
				$rec['sum_kr']=1;
				add_to_new_delo($rec) or QuestDie();

				PutQItem($user,3003050,"Лодочник");

				addchp ('<font color=red>Внимание!</font> Вы получили <b>Чистая речная вода</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				if ($user['room'] == ($maprel+$maprelall+1)) {
					mysql_query('UPDATE oldbk.users SET money = money - 1, room = '.($maprel+$maprelall+2).' WHERE id = '.$user['id']) or QuestDie();
				} elseif ($user['room'] == ($maprel+$maprelall+2)) {
					mysql_query('UPDATE oldbk.users SET money = money - 1, room = '.($maprel+$maprelall+1).' WHERE id = '.$user['id']) or QuestDie();
				}
				mysql_query('COMMIT') or QuestDie();

				Redirect("mlboat.php");
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Переправа недорогая. Заплати 1 кредит, и поехали.",
					33333 => "Заплатить 1 кредит.",
					11111 => "Попрощаться и уйти.",
				);			
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая. ",
				1 => "Мне не нужно переправляться. У меня к тебе просьба. Нужно мне немного чистейшей речной воды, но у берега такой не достать. Может, наберешь мне с середины реки немного?",
				2 => "Мне бы переправиться на ту сторону. Сколько это будет стоить?",
				11111 => "Не надо, я просто проходил мимо. Пойду дальше.",
			);
			if ($mlqfound !== FALSE) unset($mldiag[1]);
		}

	}

	if ($sf == "mlhunter") {
		$mlqfound = QItemExists($user,3003051);

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Ха-ха! И ты тоже веришь в эти суеверия?! Говорят, что она удачу приносит. Послушай, я этих лапок на своем веку знаешь сколько перевидал? А удачи нет, как нет. Но, каждому, как говорится, свое. Хочешь лапку, держи свою лапку. Но поверь, много пользы тебе с нее не будет.",
					3 => "Спасибо, лишняя удача не помешает никогда.",
				);

			} elseif ($_GET['qaction'] == 3 && $mlqfound === FALSE) {
				// переправляемся
				mysql_query('START TRANSACTION') or QuestDie();

				PutQItem($user,3003051,"Охотник");

				addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Заячья лапка</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();

				Redirect("mlboat.php");
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
				1 => "Да вот, пришел к тебе с просьбой необычной. Не найдется ли у тебя заячьей лапки?",
				2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
			if ($mlqfound !== FALSE) unset($mldiag[1]);
		}

	}

?>