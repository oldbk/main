<?php
	// квест подготовка к зимовке
	$q_status = array(
		0 => "Принести к Разбойнику пять полушубков (%N1%/1) и сено (%N2%/1)",
		1 => "Принести волчьи клыки (%N1%/5) к Охотнику",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 24) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");
	
	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlrouge") {
		$mlqfound = false;
		$qi1 = QItemExistsID($user,3003085);
		$qi2 = QItemExistsID($user,3003086);

		if ($qi1 !== FALSE && $qi2 !== FALSE) {
			$mlqfound = true;
			$todel = array_merge($qi1,$qi2);
		}

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				unsetQuest($user) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				unsetQA();		
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				$mldiag = array(
					0 => "Вот это славно, вот это ты молодец! Ну, как и  говорил – в долгу не останемся! Теперь нам зима нипочем!",
					3 => "Обращайся ещё. Пока!",
				);
			} elseif ($_GET['qaction'] == 3 && $mlqfound) {
				// получаем награду
				mysql_query('START TRANSACTION') or QuestDie();

				$r = AddQuestRep($user,150) or QuestDie();
				$m = AddQuestM($user,2,"Разбойник") or QuestDie();
				$e = AddQuestExp($user) or QuestDie();


				$item = 3101;
				$howmuch = 5;
				if (mt_rand(0,100) < 70) {
					$item = 3103;
					$howmuch = 20;
				}

				PutQItem($user,$item,"Разбойник",0,$todel,255,"eshop") or QuestDie();
				PutQItem($user,105,"Разбойник") or QuestDie();
	
				$msg = "<font color=red>Внимание!</font> Вы получили <b>Чек на предьявителя ".$howmuch." кр.</b> и <b>Легкий завтрак</b>, <b>".$r."</b> репутации, <b>".$e."</b> опыта и <b>".$m."</b> кр. за выполнение квеста!";
				addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				UnsetQuest($user) or QuestDie();
				UnsetQA();
				mysql_query('COMMIT') or QuestDie();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Вернулся? А я и не думал, что тебя снова увижу. Ну, какие вести принес?",
				1 => "Нет, я хочу отказаться от задания (я знаю, что смогу взять следующее только через 20 часов)",
				2 => "Да ребята, не любят вас жители. Это было тяжело, но я все же справился. Вот как договаривались – полушубки, да сено, чтоб на пол постелить.",
				11111 => "Пока никаких, попозже зайду…",
			);
			if (!$mlqfound) unset($mldiag[2]);
		}
	}

	if ($sf == "mlvillage") {
		if ($ai[0] == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 2) || (isset($_GET['qaction']) && $_GET['qaction'] > 1000 && $_GET['qaction'] < 2000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1001) {
					$mldiag = array(
						0 => "Это очень похвально сын мой, как могу я помочь тебе на твоем пути в царствие господне?",
						1003 => "Как раз сейчас я ищу пять полушубков, для таких вот страждущих и убогих, что бы имели они шанс пережить суровую зиму, да немного сена, что бы было им, на чем спать морозными ночами.",
					);
				} elseif ($_GET['qaction'] == 1003) {					
					$mldiag = array(
						0 => "Жаль тебя огорчать, но лишней одежды в деревне нет, из-за постоянных набегов этих проклятых разбойников нам пришлось продать все, что только можно, для покупки необходимого. Однако сено… я думаю, ты мог бы попросить немного у Конюха. Скажи, что я послал тебя, и он не откажет.",
						1004 => "Благодарю вас, святой отец.",
					);
				} elseif ($_GET['qaction'] == 1004) {
					mysql_query('START TRANSACTION') or QuestDie();
					$ai[0] = 1;
					UpdateQuestInfo($user,24,implode("/",$ai)) or QuestDie();
					mysql_query('COMMIT') or QuestDie();			
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "Приветствую тебя, путник, в нашей скромной обители. Пришел ли ты просить Господа о благодати и отпущении грехов или помолиться со мной о душах других грешников?",
					1001 => "Святой отец, были времена, когда я грешил и упивался своим грехом. Но теперь, встав на путь раскаяния – стараюсь исправить все содеянное и вымолить прощение у всевышнего, помогая страждущим и убогим.",
					11111 => "Кажется, я не туда попал.",
				);
			}
		}
	}

	if ($sf == "mlhorse" && $ai[0] != 1) {
		$mldiag = array(
			0 => "Приветствую тебя, путник! Чем я могу тебе помочь? В нашей конюшне можно купить лошадь за 10 кр. или продать ее за 5 кр. А может у тебя ко мне какое дело?",
			30000 => "Перейти к лошадям",
			11111 => "Лучше загляну к тебе в другой раз.",
		);
	}

	if ($sf == "mlhorse" && $ai[0] == 1) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "И вправду беда… Но боюсь я не могу тебе помочь. Последнее время я сам в похожей ситуации – из-за проклятых разбойников мне приходится просить сено для лошадей у Рыцаря. Возможно и тебе следует обратиться к нему. Скажи, что пришел от меня.",
					2 => "Спасибо за совет. Пока!",
				);
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[0] = 2;
				UpdateQuestInfo($user,24,implode("/",$ai)) or QuestDie();
				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Приветствую тебя, путник! Чем я могу тебе помочь? В нашей конюшне можно купить лошадь за 10 кр. или продать ее за 5 кр. А может у тебя ко мне какое дело?",
				1 => "Священник посоветовал обратиться к тебе. Дело в том, что я нахожусь в ужасном положении и если мне не удастся раздобыть немного сена – зимой от холода могут погибнуть невинные люди…",
				30000 => "Перейти к лошадям",
				11111 => "Лучше загляну к тебе в другой раз.",
			);
		}
	}

	if ($sf == "mlknight" && $ai[0] == 2) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Жизни и смерти, говоришь? Ну если так, то конечно найдется… Благо прошлогодние запасы ещё и не думают заканчиваться.",
					2 => "Огромное спасибо! Пока!",
				);
			} elseif ($_GET['qaction'] == 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[0] = 3;
				UpdateQuestInfo($user,24,implode("/",$ai)) or QuestDie();

				PutQItem($user,3003085,"Одинокий Рыцарь") or QuestDie();
				addchp ('<font color=red>Внимание!</font> Одинокий Рыцарь передал вам <b>Сено</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам?",
				1 => "Меня к тебе послал Конюх, к которому меня послал Священник… Ай! В общем, не важно. У тебя не найдется немного сена? Это буквально вопрос жизни и смерти!",
				11111 => "Я лучше пойду…",
			);
		}
	}

	if ($sf == "mlhunter" && $ai[1] == 0) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Мы не можем этого допустить… Сколько там детей?",
					2 => "Всего пятеро… Они совсем одни. Но я не знаю что делать, где достать пять теплых полушубков, сорванцам на зиму?",
				);
			} elseif ($_GET['qaction'] == 2) {
				$mldiag = array(
					0 => "Спокойно, добрый муж, мы не бросим детей в беде! Я же лучший специалист по мехам и шубам! Одна только загвоздка - мой нож для свежевания пришел в полную негодность. Но я думаю острый, длинный коготь или клык какого-нибудь дикого зверя вполне сгодился для снятия одной шкуры.",
					3 => "Я все понял! Скоро вернусь!",
				);
			} elseif ($_GET['qaction'] == 3) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[1] = 1;
				UpdateQuestInfo($user,24,implode("/",$ai)) or QuestDie();

				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Эй, путник, ты меня чуть с ног не сбил, куда так торопишься?",
				1 => "Трагедия! Кошмар! Священник рассказал мне о происках мерзавцев-разбойников и о том, что жителям деревни пришлось продать всю теплую одежду, что бы свести концы с концами. Несчастные бездомные детишки могут не пережить эту зиму!",
				11111 => "Мне надо идти, до встречи.",
			);
		}
	}


	if ($sf == "mlhunter" && $ai[1] == 1) {
		$mlqfound = false;
		$todel = QItemExistsCountID($user,3003042,5);
		if ($todel !== FALSE) $mlqfound = true;

		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1 && $mlqfound) {
				$mldiag = array(
					0 => "Да, это как раз то, что нужно! Давай поторопимся, что бы ты смог отнести одежду этим несчастным малышам.",
					2 => "Отлично! Большое спасибо! Пока!",
				);
			} elseif ($_GET['qaction'] == 2 && $mlqfound) {
				mysql_query('START TRANSACTION') or QuestDie();
				$ai[1] = 2;
				UpdateQuestInfo($user,24,implode("/",$ai)) or QuestDie();
				PutQItem($user,3003086,"Охотник",0,$todel) or QuestDie();
				addchp ('<font color=red>Внимание!</font> Охотник передал вам <b>Пять полушубков</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();
				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты принес мне то, что я просил?",
				1 => "Да, вот, держи… Надеюсь этого хватит? Бедные детишки, ах бедные детишки.",
				11111 => "Ещё нет…",
			);
			if (!$mlqfound) unset($mldiag[1]);
		}
	}

	if ($sf == "mlwitch" && $ai[1] == 2 && QItemExists($user,3003042)) {
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

				$ai[1] = 2;
				UpdateQuestInfo($user,24,implode("/",$ai)) or QuestDie();
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
?>