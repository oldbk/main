<?php
	// квест почтовый дилижанс
	$q_status = array(
		0 => "Отнести всем письма (Священнику, Магу, Охотнику), получить три расписки (%N1%/3) и вернуться к Почтальону.",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 12) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");

	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlpost") {
		$mlqfound = false;
		$todel = QItemExistsCountID($user,3003041,3);
		if ($todel !== FALSE && $ai[0] == 1 && $ai[1] == 1 && $ai[2] == 1) {
			$mlqfound = true;
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Спасибо, ты мне очень помог! Не хочу быть неблагодарным. Награжу тебя, как могу.",
					3 => "Всегда готов помочь (получить награду)",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// выдаём награду за квест
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,250) or QuestDie();
				$m = AddQuestM($user,2,"Почтальон") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();

				if ($user['level'] == 6) {
					$item = 33029;
				} elseif ($user['level'] == 7) {
					$item = 33030;
				} else {
					$item = 33031;
				}

				PutQItem($user,$item,"Почтальон",0,$todel,255) or QuestDie();
				PutQItem($user,119,"Почтальон",0,array(),255) or QuestDie();

				$msg = "<font color=red>Внимание!</font> Вы получили <b>Обед воина</b>, <b>Клонирование</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 2) {
				// отбираем письма
				$it = QItemExistsID($user,3003040);
				mysql_query('START TRANSACTION') or QuestDie();
				if ($it !== FALSE) {
					PutQItemTo($user,'Почтальон',$it) or QuestDie();
				}

				// отбираем расписки
				$it = QItemExistsID($user,3003041);
				if ($it !== FALSE) {
					PutQItemTo($user,'Почтальон',$it) or QuestDie();
				}

				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты сделал то, что я просил?",
			);
			if ($mlqfound) $mldiag[1] = "Да,  вот три расписки о получении писем – от Священника, Мага и Охотника. Сделал все, как ты просил.";
			$mldiag[2] = "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)";
			$mldiag[1111] = "Нет, я еще не успел, пойду дальше.";
		}
	}

	if ($sf == "mlwitch" && $ai[3] == 0 && QItemExists($user,3003042)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Да, в полнолуние уже не первый раз такое случается. Но не всегда так было. Когда-то тише было в лесу и спокойнее. А с тех пор, как Оборотень побывал в наших краях, вот такое началось. Может, покусал он кого, а может, в крови у них что забурлило, не знаю. Но в полнолуние они теперь с ума сходят. А ты когда от них отбивался, ничего себе в трофеи не оставил?",
					2 => "Оставил, а как же! Вот целая куча волчьих клыков, смотри.",
					
				);
			} elseif ($_GET['qaction'] == 3) {
				// получаем 20 кр за клыки
				mysql_query('START TRANSACTION') or QuestDie();				
				$rec = array();
	    			$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']+20;
				$rec['target']=0;
				$rec['target_login']="Ведьма";
				$rec['type']=259; // получил деньги от квестбота
				$rec['sum_kr']=20;
				add_to_new_delo($rec) or QuestDie(); //юзеру

				// забираем деньги
				mysql_query('UPDATE oldbk.`users` set money = money + 20 WHERE id = '.$user['id']) or QuestDie();

				// отбираем все клыки
				$it = QItemExistsID($user,3003042);
				PutQItemTo($user,'Ведьма',$it) or QuestDie();

				// системку
				addchp ('<font color=red>Внимание!</font> Ведьма передала вам <b>20 кр.</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[3] = 1;
				UpdateQuestInfo($user,12,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Ох, вот это ты молодец. Это ты хорошо, что ко мне заглянул. Я давно мечтаю себе ожерелье из волчьих клыков сделать. Может, ты мне их продашь? Денег не пожалею, 20 кредитов за них тебе дам. Продашь?",
					3 => "Продам, конечно, зачем они мне, только место занимают. А монеты мне нравятся больше. Забирай свои клыки.",
				);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Последнее время ко мне зачастили гости, и всем что-то надо. Что привело тебя в мои края?",
				1 => "Я к тебе с одним вопросом. Бродил я сегодня по лесу и волки набрасывались на меня, как бешенные. Почтальон говорит, что это в полнолуние они такие злые. А ты что скажешь об этом?",
				11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}	
	}

	if ($sf == "mlhunter" && $ai[0] == 0 && QItemExists($user,3003040)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1001) {
				$mldiag = array(
					0 => "Вот спасибо, ты пришел вовремя как никогда. Я уже волноваться начал, что ответа не получаю, а это оказывается Почтальон хворает. Давай скорее письмо и держи расписку. Тебе небось перед ним отчитываться придется. А в лесу и правда неспокойно, береги себя.",
					1002 => "Письмо забирай, конечно, но дай мне расписку, чтоб Почтальону показать.",
				);
			} elseif ($_GET['qaction'] == 1002) {
				// системку
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003041,"Охотник",0,QItemExistsCountID($user,3003040,1)) or QuestDie();

				addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Расписка</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[0] = 1;
				UpdateQuestInfo($user,12,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
				1001 => "Письмо тебе принес от Почтальона. Он приболел, да и неспокойно сейчас в лесу, вот и просил меня разнести почту.",
				11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	}

	if ($sf == "mlmage" && $ai[1] == 0 && QItemExists($user,3003040)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1001) {
				$mldiag = array(
					0 => "Вот этого письма я и ждал! Давай сюда скорее и проваливай, а то волки уже за тобой к замку подбираются, слышишь вой? Нет у меня настроения с ними сегодня воевать.",
					1003 => "Письмо забирай, конечно, но дай мне расписку, чтоб Почтальону показать.",
				);
			} elseif ($_GET['qaction'] == 1003) {
				$mldiag = array(
					0 => "Расписки, бумажки, вечно у вас у людей всякие сложности. Ничего просто и нормально сделать не можете. Вот тебе расписка и уходи, не путайся под ногами.",
					1002 => "Удачи, не буду мешать.",
				);
			} elseif ($_GET['qaction'] == 1002) {
				// системку
				mysql_query('START TRANSACTION') or QuestDie();
				PutQItem($user,3003041,"Маг",0,QItemExistsCountID($user,3003040,1)) or QuestDie();

				addchp ('<font color=red>Внимание!</font> Маг передал вам <b>Расписка</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				$ai[1] = 1;
				UpdateQuestInfo($user,12,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Не часто меня гости беспокоят.  Зачем пришел и по какому делу?  Говори.",
				1001 => "Принес тебе почту срочную от Почтальона. Он захворал, просил меня передать. ",
				33333 => "Говорят, ты можешь собрать Магическую Книгу из отдельных страниц. Хочу попросить тебя о такой услуге.",
				44444 => "В городе ходят слухи, что ты можешь сделать Магические руны сильнее. Если это правда, то мне бы не помешало усилиться.",
				11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
			);
		}
	}

	if ($sf == "mlvillage" && $ai[2] == 0 && QItemExists($user,3003040)) {
		if (((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 1000 && $_GET['qaction'] < 2000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1001) {
					$mldiag = array(
						0 => "Вот спасибо, я как-раз ждал этой почты. Дай Б-г Почтальону здоровья, а я не забуду упомянуть его в своей вечерней молитве. Держи расписку и ступай с миром.",
						1002 => "Всего хорошего, Святой Отец",
					);
				} elseif ($_GET['qaction'] == 1002) {
					// системку
					mysql_query('START TRANSACTION') or QuestDie();
					PutQItem($user,3003041,"Священник",0,QItemExistsCountID($user,3003040,1)) or QuestDie();
	
					addchp ('<font color=red>Внимание!</font> Священник передал вам <b>Расписка</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
	
					$ai[2] = 1;
					UpdateQuestInfo($user,12,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую тебя, путник, в нашей скромной обители. Пришел ли ты просить Господа о благодати и отпущении грехов или помолиться со мной о душах других грешников?",
					1001 => "Я к тебе с письмом от Почтальона, Святой Отец. Он захворал и просил меня разнести почту. Вот ваше письмо, и распишись, в получении.",
					11111 => "Ничего особенного, просто проходил мимо. Пойду дальше.",
				);
			}
		}
	}

?>