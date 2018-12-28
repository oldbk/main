<?php
	// квест сумка почтальона
	$q_status = array(
		0 => "Помочь Почтальону собрать пряжки (%N1%/1), кожу (%N2%/1) и вышитых узоров для сумки. (%N3%/1)",
		1 => "Принести кузнецу руду. (%N1%/2)",
		2 => "Принести охотнику вино. (%N1%/1)",
	);
	

	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 5) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	if ($sf == "mlpost") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003018);
		$qi2 = QItemExistsID($user,3003017);
		$qi3 = QItemExistsID($user,3003016);

		if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
			$todel = array_merge($qi1,$qi2,$qi3);
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Быстрый ты какой. Может, пойдешь ко мне в помощники? Я-то с каждым годом старею, уж не так быстро бегаю, а почту все ждут. Да шучу-шучу, красивые пряжки, а узоры просто загляденье. Думаю, сегодня уже моя сумка будет готова. Спасибо за помощь. Твои письма доставлю первыми, если напишешь.",
					3 => "Получить награду",
				);
			} elseif ($_GET['qaction'] == 5) {
                                mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
                                mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,150) or QuestDie();
				$m = AddQuestM($user,1,"Почтальон") or QuestDie();
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

				PutQItem($user,$item,"Почтальон",0,$todel,255,"eshop") or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Большой свиток «Восстановление ".$txt."HP»</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
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

			if ($mlqfound) $mldiag[1] = "Да, вот все, что нужно для сумки.";
			$mldiag[5] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[2] = "Нет, я еще не все собрал. Пойду дальше.";
		}
	}

	if ($sf == "mlvillage") {
		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			if (QItemExists($user,3003016)) {
				if (isset($_GET['qaction'])) unsetQA();
				return;
			}

			$ai = explode("/",$questexist['addinfo']);
			$mlqfound = false;

			if ($ai[0] == 1) {
				$todel = QItemExistsCountID($user,3003005,2);
	
				if ($todel !== FALSE) {
					$mlqfound = true;
				}
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "Почтальон заболел? То-то я думаю, давно он за письмами не заходит. А вон оно как, оказывается. Ну, дай Б-г ему здоровья, хороший человек, отзывчивый. Сделаю для него пряжки, но как на грех руда кончилась. Если принесешь мне руды, выкую все в лучшем виде. На две пряжки два куска руды думаю, хватит.",
						2003 => "Хорошо, я принесу тебе все, что нужно.",
						2004 => "Нет, это слишком сложно, я пойду дальше по своим делам.",
					);
				} elseif ($_GET['qaction'] == 2005 && $mlqfound) {
	                                mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003016,"Кузнец",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Кузнец передал вам <b>Пряжки</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[0] = 0;
					UpdateQuestInfo($user,5,implode("/",$ai)) or QuestDie();;
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} elseif ($_GET['qaction'] == 2003 && $ai[0] == 0) {
					$ai[0] = 1;
					mysql_query('START TRANSACTION') or QuestDie();
					UpdateQuestInfo($user,5,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				if ($ai[0] == 0) {
					$mldiag = array(
						0 => "Привет, чем могу тебе помочь?",
						2001 => "Почтальон заболел и сидя дома шьет себе новую сумку, но не нашлось нужных пряжек. Ты не поможешь?",
						2002 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
					);
				} else {
					$mldiag = array(
						0 => "Привет, ты принес мне то, что я просил?",
					);
					if ($mlqfound) $mldiag[2005] = "Да, вот руда, что ты просил.";
					$mldiag[2006] = "Нет, я еще не все собрал. Пойду дальше.";
				}
			}
		}

		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			$ai = explode("/",$questexist['addinfo']);
			if ($ai[1] != 1) {
				if (isset($_GET['qaction'])) unsetQA();
				return;
			}

			if (QItemExists($user,3003012)) {
				if (isset($_GET['qaction'])) unsetQA();
				return;
			}

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Да было время, когда охотник не пил. И дичь всегда свежая была, и шкур полные сараи. А сейчас спился, на охоту раз в неделю ходит, совсем плох стал. Но ведь кроме него, никто больше в наших краях дичи не добудет. А без дичи, какой трактир? Клиенты у меня придирчивые, сам понимаешь. Приходится ублажать сердешного.  Держи бутылочку вина, передашь ему, только скажи, что от меня принес.",
						3 => "Договорились, обязательно  передам.",
					);
				} elseif ($_GET['qaction'] == 3) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003012,"Трактирщик",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Трактирщик передал вам <b>Вино</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();				
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Привет, гостям в моем трактире всегда рады. Ты проголодался или по делу?",
					1 => "Охотник просит принести бутылочку портвейна, у тебя не найдется?",
					2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

	if ($sf == "mlhunter") {
		if ($step == 0) {
			if (QItemExists($user,3003017)) {
				if (isset($_GET['qaction'])) unsetQA();
				return;
			}

			$ai = explode("/",$questexist['addinfo']);
			$mlqfound = false;
			$todel = QItemExistsID($user,3003012);
			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Хммм…. Кожу надо? А сколько? Для Почтальона, конечно, не жалко, он меня всегда выручал, но запасы подходят к концу. Было время, я все налево и направо раздавал. А теперь просто так не отдаю. Могу поменяться, если хочешь на бутылочку портвейна. Идет?",
						3 => "Хорошо, я принесу тебе все, что нужно.",
						4 => "Нет, это слишком сложно, я пойду дальше по своим делам. .",
					);
				} elseif ($_GET['qaction'] == 5 && $ai[1] == 1 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003017,"Охотник",0,$todel) or QuestDie();

					addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Кожа</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					$ai[1] = 0;
					UpdateQuestInfo($user,5,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();								
				} elseif ($_GET['qaction'] == 3 && $ai[1] == 0) {
					$ai[1] = 1;
					mysql_query('START TRANSACTION') or QuestDie();
					UpdateQuestInfo($user,5,implode("/",$ai)) or QuestDie();;
                                        mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				if ($ai[1] == 0) {
					$mldiag = array(
						0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься? ",
						1 => "У Почтальона не хватает кожи на новую сумку, ты не поможешь?",
						2 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
					);
				} else {
					$mldiag = array(
						0 => "Привет, ты принес мне то, что я просил?"
					);
			
					if ($mlqfound) $mldiag[5] = "Да, вот твоя бутылка портвейна, трактирщик передал с приветом от него. ";
					$mldiag[6] = "Нет, я еще не все нашел. Пойду дальше.";
				}
			}
		}
	}

	if ($sf == "mlknight") {
		if ($step == 0) {
			if (QItemExists($user,3003018)) {
				if (isset($_GET['qaction'])) unsetQA();
				return;
			}


			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1) {
					$mldiag = array(
						0 => "Вот и хорошо, присаживайся. Я сегодня весь день в замке прибирал, умаялся. На чердаке такой бардак, ты даже не представляешь,  что я там нашел. Вон  в сундуке погляди, целая стопка узоров, что покойница вышивала. Скучаю я по ней, ой скучаю…",
						3 => "Красивые узоры. Можно парочку взять, уж больно понравились?"
					);
				} elseif ($_GET['qaction'] == 6) {
					// выдаём узор
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003018,"Рыцарь",0,array()) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					addchp ('<font color=red>Внимание!</font> Рыцарь передал вам <b>Вышитые узоры</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					unsetQA();
				} elseif ($_GET['qaction'] == 3) {
					$mldiag = array(
						5 => "Бери, конечно, мне-то что с ними делать? А ты может применение, какое найдешь. И ей приятно будет, коли люди ее добрым словом вспомнят.",
						6 => "Спасибо, я пойду, еще загляну как-нибудь."
					);
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "О, как удачно, что ты зашел. Проходи-проходи. Я как раз на стол накрыл, бутылочку достал. День сегодня грустный  у меня, жену покойную поминаю. Добрейшая женщина была и рукодельница. А красавица была, глаз не отвести…  Да в одиночку пить, сам знаешь, совсем не весело. Может, выпьешь со мной, и о жизни поговорим?",
					1 => "Да, конечно, с хорошим человеком по любому поводу поговорить приятно.",
					2 => "Нет, я просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

?>