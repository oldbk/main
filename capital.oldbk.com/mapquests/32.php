<?php
	// легендарный квест
	if (!isset($qe) || $qe === FALSE) return;
	$step = $qe['val'];

	if ($step == 1 || $step == 2) {
		$s1val = 0;
		$s2val = 0;
		if ($step == 1) {
			$s1val = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s1"'));
			$s1val = $s1val['val'];
		}
		if ($step == 2) {
			$s2val = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s2"'));
			$s2val = $s2val['val'];
		}

		$fstep = false;
		if ($step == 1 && $s1val >= 50) $fstep = true;
		if ($step == 2 && $s2val >= 10) $fstep = true;


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Да, выбирай снова, что ты хочешь - выиграть 50 кровавых боев в Загороде или 10 турниров в Руинах Старого замка? Но учти, что после этого твои прошлые достижения будут не засчитываться.",
					2 => "Я выиграю 50 загородных кровавых боев (взять задание)",
					3 => "Я выиграю 10 турниров в Руинах. (взять задание)",
					11112 => "Нет, я передумал менять задание.",
				);
				if ($step == 1) unset($mldiag[2]);
				if ($step == 2) unset($mldiag[3]);
			} elseif ($_GET['qaction'] == 2 && $step != 1) {
				mysql_query('START TRANSACTION') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s1"') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s2"') or QuestDie();
				mysql_query('UPDATE oldbk.map_var SET val = 1 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s1","0")') or QuestDie();
				mysql_query('COMMIT') or QuestDie();					
				unsetQA();
			} elseif ($_GET['qaction'] == 3 && $step != 2) {
				mysql_query('START TRANSACTION') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s1"') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s2"') or QuestDie();
				mysql_query('UPDATE oldbk.map_var SET val = 2 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s2","0")') or QuestDie();
				mysql_query('COMMIT') or QuestDie();					
				unsetQA();
			} elseif ($_GET['qaction'] == 4 && $fstep) {
				$mldiag = array(
					0 => "Молодец! Готов ли ты отдать мне 50 000 своей репутации в доказательство?",
					5 => "Да, забирай репутацию.",
					11112 => "Нет, у меня столько еще не набралось, я зайду попозже.",
				);
				if ($user['repmoney'] < 50000) unset($mldiag[5]);
			} elseif ($_GET['qaction'] == 5 && $fstep && $user['repmoney'] >= 50000) {
				$mldiag = array(
					0 => "С первым заданием ты удачно справился. Твое второе задание  - доказать, не только свою смелость, но и бескорыстие. Принеси мне 40 любых чеков, которые в вашем Мире находятся в разных боевых локациях. Я знаю, что их находят в Башне Смерти, в Лабиринте Хаоса, на Ристалище и в других опасных местах. Если принесешь мне 40 чеков, это будет означать, что ты совершил немало подвигов и провел немало непростых битв, за которые ты не взял деньги.",
					6 => "Я принесу тебе чеки.",
				);			
			} elseif ($_GET['qaction'] == 6 && $fstep && $user['repmoney'] >= 50000) {
				mysql_query('START TRANSACTION') or QuestDie();

				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s1"') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s2"') or QuestDie();
				mysql_query('UPDATE oldbk.map_var SET val = 3 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();

				$q = mysql_query('UPDATE oldbk.users SET `repmoney` = `repmoney` - 50000 WHERE id = '.$user['id']);
				if ($q === FALSE) return FALSE;

		  		$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user['repmoney'];
				$rec['owner_rep_posle']=$user['repmoney']-50000;
				$rec['target_login']="Квесты";
				$rec['sum_rep'] = 50000;
				$rec['type'] = 395;
				if (add_to_new_delo($rec) === FALSE) QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты выполнил то, что обещал?",
				11111 => "Нет, я еще не все сделал. Я зайду позже.",
				1 => "Нет, я хочу изменить задание, это возможно?",
				4 => "Да, я выполнил все что обещал.",
				11112 => "Просто мимо проходил, уже ухожу.",
			);
			if (!$fstep) unset($mldiag[4]);
		}	
	}

	if ($step == 3) {
		$fstep = false;

		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = "'.$user['id'].'" AND prototype IN (3101,3102,3103,3201,3202,3203,3204,3205,3206,3207) LIMIT 40');
		if (mysql_num_rows($q) == 40) $fstep = true;
		$todel = array();
		while($i = mysql_fetch_assoc($q)) {
			$todel[] = $i['id'];
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1  && $fstep) {
				$mldiag = array(
					0 => "Молодец! Готов ли ты отдать мне 50 000 своей репутации в доказательство?",
					2 => "Да, забирай репутацию.",
					11112 => "Нет, у меня столько еще не набралось, я зайду попозже.",
				);
				if ($user['repmoney'] < 50000) unset($mldiag[2]);
			} elseif ($_GET['qaction'] == 2 && $fstep && $user['repmoney'] >= 50000) {
				$mldiag = array(
					0 => "Ты смелый воин, вот тебе третье задание, и оно будет посложнее предыдущих. Ты должен доказать, что ты победитель, и выиграть 100 хаотических боев, 20 боев на Центральной Площади. Кроме этого ты должен доказать, что ты не можешь жить без битв, и 50 походов к Лорду Разрушителю.",
					3 => "Я сделаю это.",
				);
			} elseif ($_GET['qaction'] == 3 && $fstep && $user['repmoney'] >= 50000) {
				mysql_query('START TRANSACTION') or QuestDie();

				PutQItemTo($user,'Чужестранец',$todel) or QuestDie();

				mysql_query('UPDATE oldbk.map_var SET val = 4 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();

				$q = mysql_query('UPDATE oldbk.users SET `repmoney` = `repmoney` - 50000 WHERE id = '.$user['id']);
				if ($q === FALSE) return FALSE;

		  		$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user['repmoney'];
				$rec['owner_rep_posle']=$user['repmoney']-50000;
				$rec['target_login']="Квесты";
				$rec['sum_rep'] = 50000;
				$rec['type'] = 395;
				if (add_to_new_delo($rec) === FALSE) QuestDie();

				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s41","0")') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s42","0")') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s43","0")') or QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты выполнил то, что обещал?",
				11111 => "Нет, я еще не все сделал. Я зайду позже.",
				1 => "Да, я выполнил все что обещал. Забирай 40 чеков.",
				11112 => "Просто мимо проходил, уже ухожу.",
			);
			if (!$fstep) unset($mldiag[1]);
		}		
	}

	if ($step == 4) {
		$fstep = false;

		$qq1 = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s41"'));
		$qq2 = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s42"'));
		$qq3 = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s43"'));
		if ($qq1['val'] >= 100 && $qq2['val'] >= 20 && $qq3['val'] >= 50) $fstep = true;


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1  && $fstep) {
				$mldiag = array(
					0 => "Молодец! Готов ли ты отдать мне 50 000 своей репутации в доказательство?",
					2 => "Да, забирай репутацию.",
					11112 => "Нет, у меня столько еще не набралось, я зайду попозже.",
				);
				if ($user['repmoney'] < 50000) unset($mldiag[2]);
			} elseif ($_GET['qaction'] == 2 && $fstep && $user['repmoney'] >= 50000) {
				$mldiag = array(
					0 => "Ты вселяешь в меня надежду. Так далеко, как ты еще никто не заходил! Четвертое задание будет немного странным. Когда-то я победил Злого Мага и опустошил его склад, в котором было 50 магических зелий. Теперь ты должен добыть  50 зелий Мага и принести мне.",
					3 => "Я принесу тебе зелья Мага.",
				);
			} elseif ($_GET['qaction'] == 3 && $fstep && $user['repmoney'] >= 50000) {
				mysql_query('START TRANSACTION') or QuestDie();

				mysql_query('UPDATE oldbk.map_var SET val = 5 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();

				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s41"') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s42"') or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s43"') or QuestDie();

				$q = mysql_query('UPDATE oldbk.users SET `repmoney` = `repmoney` - 50000 WHERE id = '.$user['id']);
				if ($q === FALSE) return FALSE;

		  		$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user['repmoney'];
				$rec['owner_rep_posle']=$user['repmoney']-50000;
				$rec['target_login']="Квесты";
				$rec['sum_rep'] = 50000;
				$rec['type'] = 395;
				if (add_to_new_delo($rec) === FALSE) QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты выполнил то, что обещал?",
				11111 => "Нет, я еще не все сделал. Я зайду позже.",
				1 => "Да, я выполнил все что обещал.",
				11112 => "Просто мимо проходил, уже ухожу.",
			);
			if (!$fstep) unset($mldiag[1]);
		}		
	}


	if ($step == 5) {
		$fstep = false;

		$todel = QItemExistsCountID($user,667667,50);

		if ($todel !== FALSE) {
			$fstep = true;
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1  && $fstep) {
				$mldiag = array(
					0 => "Молодец! Готов ли ты отдать мне 50 000 своей репутации в доказательство?",
					2 => "Да, забирай репутацию.",
					11112 => "Нет, у меня столько еще не набралось, я зайду попозже.",
				);
				if ($user['repmoney'] < 50000) unset($mldiag[2]);
			} elseif ($_GET['qaction'] == 2 && $fstep && $user['repmoney'] >= 50000) {
				$mldiag = array(
					0 => "Ты готов к пятому заданию! Оно докажет, что ты не боишься встретиться лицом к лицу с любым, самым страшным монстром и что ты беспощаден к врагам. Победи Исчадие Хаоса 30 раз и принеси мне 10 черепов своих врагов.",
					3 => "Я сделаю это.",
				);
			} elseif ($_GET['qaction'] == 3 && $fstep && $user['repmoney'] >= 50000) {
				mysql_query('START TRANSACTION') or QuestDie();

				mysql_query('UPDATE oldbk.map_var SET val = 6 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s6","0")') or QuestDie();

				PutQItemTo($user,'Чужестранец',$todel) or QuestDie();

				$q = mysql_query('UPDATE oldbk.users SET `repmoney` = `repmoney` - 50000 WHERE id = '.$user['id']);
				if ($q === FALSE) return FALSE;

		  		$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user['repmoney'];
				$rec['owner_rep_posle']=$user['repmoney']-50000;
				$rec['target_login']="Квесты";
				$rec['sum_rep'] = 50000;
				$rec['type'] = 395;
				if (add_to_new_delo($rec) === FALSE) QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты выполнил то, что обещал?",
				11111 => "Нет, я еще не все сделал. Я зайду позже.",
				1 => "Да, я выполнил все что обещал. Забирай зелья.",
				11112 => "Просто мимо проходил, уже ухожу.",
			);
			if (!$fstep) unset($mldiag[1]);
		}		
	}

	if ($step == 6) {
		$fstep = false;

		$qq1 = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s6"'));
		$todel = QItemExistsCountID($user,3002500,10);

		if ($todel !== FALSE && $qq1['val'] >= 30) {
			$fstep = true;
		}


		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1  && $fstep) {
				$mldiag = array(
					0 => "Молодец! Готов ли ты отдать мне 50 000 своей репутации в доказательство?",
					2 => "Да, забирай репутацию.",
					11112 => "Нет, у меня столько еще не набралось, я зайду попозже.",
				);
				if ($user['repmoney'] < 50000) unset($mldiag[2]);
			} elseif ($_GET['qaction'] == 2 && $fstep && $user['repmoney'] >= 50000) {
				$mldiag = array(
					0 => "Итак, остался всего один шаг, до победы! Ты Великий Воин, теперь докажи, что ты не просто велик, а легендарен! Последний подвиг, который тебе надо совершить, это доказать, что ты готов броситься в самую гущу самых огромных сражений, а не прячешься по кустам, осиживаясь, пока другие гибнут в страшных битвах. Прими участие в 50 статусных боях или в 1 судном дне. Только учти, это должны быть настоящие статусные бои, а не тренировки. Поэтому даже великие хаоты или бои с Исчадием - не считаются. Что ты сможешь выполнить первым, то и засчитается за подвиг. 50 статусных битв или 1 битву судного дня - как выполнишь - сразу приходи за наградой.",
					3 => "Я сделаю это.",
				);
			} elseif ($_GET['qaction'] == 3 && $fstep && $user['repmoney'] >= 50000) {
				mysql_query('START TRANSACTION') or QuestDie();

				mysql_query('UPDATE oldbk.map_var SET val = 7 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();

				PutQItemTo($user,'Чужестранец',$todel) or QuestDie();
				mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s6"') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s71","0")') or QuestDie();
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q32s72","0")') or QuestDie();

				$q = mysql_query('UPDATE oldbk.users SET `repmoney` = `repmoney` - 50000 WHERE id = '.$user['id']);
				if ($q === FALSE) return FALSE;

		  		$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money'];
				$rec['owner_rep_do']=$user['repmoney'];
				$rec['owner_rep_posle']=$user['repmoney']-50000;
				$rec['target_login']="Квесты";
				$rec['sum_rep'] = 50000;
				$rec['type'] = 395;
				if (add_to_new_delo($rec) === FALSE) QuestDie();

				mysql_query('COMMIT') or QuestDie();
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Привет, ты выполнил то, что обещал?",
				11111 => "Нет, я еще не все сделал. Я зайду позже.",
				1 => "Да, я выполнил все что обещал. Вот, забери черепа.",
				11112 => "Просто мимо проходил, уже ухожу.",
			);
			if (!$fstep) unset($mldiag[1]);
		}		
	}
	if ($step == 7) {
		$qq1 = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s71"'));
		$qq2 = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.map_var WHERE owner = "'.$user['id'].'" AND var = "q32s72"'));

		$fstep = false;
		if ($qq1['val'] >= 50) $fstep = 1;
		if ($qq2['val'] >= 1) $fstep = 2;

		if ($fstep == 2) {
			// судный
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1  && $fstep) {
					$mldiag = array(
						0 => "Весть о битве судного дня уже разнеслась по всему миру и дошла до моего королевства. Поэтому на этот раз мне не нужна твоя репутация в доказательство. Ты совершил легендарные подвиги! Я с радостью отдаю тебе свой значок Легендарного Воина. Носи его с честью! Ну а кроме того, он поможет тебе немного, усилив твой комплект улучшенных уникальных вещей. Удачи тебе, Легендарный Воин! А мне пора собираться домой...",
						2 => "Удачи тебе, Чужестранец. Хорошей дороги домой! (завершить квест)",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
	
					mysql_query('UPDATE oldbk.map_var SET val = 8 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();
	
					mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s71"') or QuestDie();
					mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s72"') or QuestDie();
					mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"k203;") WHERE id = '.$user['id']) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					Redirect("mlstranger.php");
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты выполнил то, что обещал?",
					11111 => "Нет, я еще не все сделал. Я зайду позже.",
					1 => "Да, я выполнил все что обещал.",
					11112 => "Просто мимо проходил, уже ухожу.",
				);
			}		
		} elseif ($fstep == 1) {
			// 50 побед
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1  && $fstep) {
					$mldiag = array(
						0 => "Молодец! Готов ли ты отдать мне 50 000 своей репутации в доказательство?",
						2 => "Да, забирай репутацию.",
						11112 => "Нет, у меня столько еще не набралось, я зайду попозже.",
					);
					if ($user['repmoney'] < 50000) unset($mldiag[2]);
				} elseif ($_GET['qaction'] == 2 && $fstep && $user['repmoney'] >= 50000) {
					$mldiag = array(
						0 => "Ты совершил легендарные подвиги! Я с радостью отдаю тебе свой значок Легендарного Воина. Носи его с честью! Ну а кроме того, он поможет тебе немного, усилив твой комплект улучшенных уникальных вещей. Удачи тебе, Легендарный Воин! А мне пора собираться домой...",
						3 => "Удачи тебе, Чужестранец! Хорошей дороги домой! (завершить квест).",
					);
				} elseif ($_GET['qaction'] == 3 && $fstep && $user['repmoney'] >= 50000) {
					mysql_query('START TRANSACTION') or QuestDie();
	
					mysql_query('UPDATE oldbk.map_var SET val = 8 WHERE owner = '.$user['id'].' AND var = "q32"') or QuestDie();
	
					mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s71"') or QuestDie();
					mysql_query('DELETE FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q32s72"') or QuestDie();
	
					$q = mysql_query('UPDATE oldbk.users SET `repmoney` = `repmoney` - 50000 WHERE id = '.$user['id']);
					if ($q === FALSE) return FALSE;
	
			  		$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['owner_rep_do']=$user['repmoney'];
					$rec['owner_rep_posle']=$user['repmoney']-50000;
					$rec['target_login']="Квесты";
					$rec['sum_rep'] = 50000;
					$rec['type'] = 395;
					if (add_to_new_delo($rec) === FALSE) QuestDie();
					mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"k203;") WHERE id = '.$user['id']) or QuestDie();	
					mysql_query('COMMIT') or QuestDie();
					Redirect("mlstranger.php");
				}
			} else {
				$mldiag = array(
					0 => "Привет, ты выполнил то, что обещал?",
					11111 => "Нет, я еще не все сделал. Я зайду позже.",
					1 => "Да, я выполнил все что обещал.",
					11112 => "Просто мимо проходил, уже ухожу.",
				);
			}		
		} else {
			$mldiag = array(
				0 => "Привет, ты выполнил то, что обещал?",
				11111 => "Нет, я еще не все сделал. Я зайду позже.",
				11112 => "Просто мимо проходил, уже ухожу.",
			);
		}
	}
