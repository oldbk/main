<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$qcomplete = false;
	$qlist = array(15,21);
	$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
	if (mysql_num_rows($q) > 0) {
		$questexist = mysql_fetch_assoc($q) or die();
	} else {
		$q = mysql_query('SELECT * FROM map_var WHERE var = "cango" AND owner = '.$user['id'].' AND val > '.time()) or die();
		if (mysql_num_rows($q) > 0) {
			$questexist['q_id'] = 0;
		}

		$q = mysql_query('SELECT * FROM map_qvar WHERE var = "qcomplete" AND owner = '.$user['id']) or die();
		if (mysql_num_rows($q) > 0) {
			$qcomplete = mysql_fetch_assoc($q);
			$qt = explode("/",$qcomplete['val']);
			$qcomplete = array();
			while(list($k,$v) = each($qt)) {
				$qcomplete[$v] = 1;
			}
			$q0 = true;
			reset($qlist);
			while(list($k,$v) = each($qlist)) {
				if (!isset($qcomplete[$v])) {
					$q0 = false;
					break;
				}
			}
			if ($q0) $questexist['q_id'] = 0;
		}
	}

	if (isset($_GET['qaction']) && $_GET['qaction'] == "33333" && $user['money'] >= 1) {
		// переправляемся
		if ($user['money'] >= 1) {
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
	
			if ($user['room'] == ($maprel+$maprelall+1)) {
				mysql_query('UPDATE oldbk.users SET money = money - 1, room = '.($maprel+$maprelall+2).' WHERE id = '.$user['id']) or QuestDie();
			} elseif ($user['room'] == ($maprel+$maprelall+2)) {
				mysql_query('UPDATE oldbk.users SET money = money - 1, room = '.($maprel+$maprelall+1).' WHERE id = '.$user['id']) or QuestDie();
			}
			mysql_query('COMMIT') or QuestDie();
		}
		Redirect("mlboat.php");
	}
?>


<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>
var loc = parent.location.href.toString();
if (loc.indexOf("/map.php") != -1) {
	parent.location.href = "<?php echo $self; ?>";
}
</script>
<style> 
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }
</style>
<style type="text/css"> 
img, div { behavior: url(/i/city/ie/iepngfix.htc) }
</style>
</HEAD>
<body id="body" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor="#d7d7d7" onResize="return; ImgFix(this);">
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
<TR>
	<TD align=center></TD>
	<TD align=right>
		<div class="btn-control">
            <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlboat1.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<?php

$q_goodboat = array(3,4,14,15,18,21,30);

if ($user['room'] == ($maprel+$maprelall+1)) {
?>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlboat1_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 320px; top: 85px;" src="http://i.oldbk.com/i/map/mlboat_pers1.png" alt="Лодочник" title="Лодочник" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlboat_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlboat_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE && in_array($questexist['q_id'],$q_goodboat) !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Переправа недорогая. Заплати 1 кредит, и поехали.",
					33333 => "Заплатить 1 кредит.",
					4 => "Попрощаться и уйти.",
				);
			} elseif ($_GET['qaction'] == 11 && $questexist === FALSE) {
				$mldiag = array(
					0 => "Беда случилась на реке… Каждый раз на переправе вижу все больше и больше дохлой рыбы… Сначала грешил на браконьеров, но похоже тут дело в чем-то другом. Чувствую я, что если срочно не найти причину, то реку будет не спасти. К кому обратиться за помощью, ума не приложу. Разве что Пилигрим  слышал о чем-то похожем в своих странствиях. Поможешь разобраться?",
					10 => "Я постараюсь помочь",
					2 => "Нет, я не смогу этого сделать",
				);
			} elseif ($_GET['qaction'] == 10 && $questexist === FALSE && !isset($qcomplete[15])) {
				// квест - прохудившаяся лодка
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,15) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = array(
					0 => "Спасибо, ты мне очень поможешь!",
					11111 => "Пока",
				);
			} elseif ($_GET['qaction'] == 14 && $questexist === FALSE && !isset($qcomplete[21])) {
				// квест - гнилая вода
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,21,"0/0/0") or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = array(
					0 => "Спасибо, ты мне очень поможешь!",
					11111 => "Пока",
				);
			} elseif ($_GET['qaction'] == 13 && $questexist === FALSE) {
				$mldiag = array(
					0 => "Честно говоря… сам я мало знаю о ремонте лодок, в отличии от управления ими. Возможно, тебе следует спросить помощи у Кузнеца?",
					14 => "Хорошо, я так и сделаю.",
					2 => "Боюсь у меня нет на это времени. ",
				);

			} elseif ($_GET['qaction'] == 12 && $questexist === FALSE) {
				$mldiag = array(
					0 => "Мои лодки много лет верой и правдой служили всем, кто хотел переправиться через реку, но теперь все они либо разбиты, либо дали течь. Помоги мне решить эту проблему, что бы я и дальше мог переправлять путников с одного берега на другой.",
					13 => "А что от меня требуется?",
					2 => "Боюсь у меня нет на это времени. ",
				);
			} elseif ($_GET['qaction'] == 2 && $questexist === FALSE) {
				$mldiag = array(
					0 => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
					11 => "Гнилая вода",
					12 => "Прохудившаяся лодка",
					11111 => "Мне не нравится ни одна из твоих просьб. Прощай.",
				);

				// тут режим квесты лодочника
				if (isset($qcomplete[15])) unset($mldiag[11]);
				if (isset($qcomplete[21])) unset($mldiag[12]);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая. Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
				1 => "Мне бы переправиться на ту сторону. Сколько это будет стоить?",
				2 => "Я готов помочь. Говори, что надо сделать.",
				3 => "Не надо, я просто проходил мимо. Пойду дальше.",
			);
			if ($questexist !== FALSE) {
				$mldiag[0] = "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая";
				unset($mldiag[2]);
			}
		}
	}

	$mlquest = "0/0";
	if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
}	
?>
</div>

<?php
} elseif ($user['room'] == ($maprel+$maprelall+2)) {
?>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlboat2_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 480px; top: 100px;" src="http://i.oldbk.com/i/map/mlboat_pers1.png" alt="Лодочник" title="Лодочник" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlboat_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlboat_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE && in_array($questexist['q_id'],$q_goodboat) !== FALSE) {
		// есть квест - подключаем квестовый обработчик, квестовые обработчики должны сами показывать диалоги переправы
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "Переправа недорогая. Заплати 1 кредит, и поехали.",
					33333 => "Заплатить 1 кредит.",
					4 => "Попрощаться и уйти.",
				);
			} elseif ($_GET['qaction'] == 11 && $questexist === FALSE) {
				$mldiag = array(
					0 => "Беда случилась на реке… Каждый раз на переправе вижу все больше и больше дохлой рыбы… Сначала грешил на браконьеров, но похоже тут дело в чем-то другом. Чувствую я, что если срочно не найти причину, то реку будет не спасти. К кому обратиться за помощью, ума не приложу. Разве что Пилигрим  слышал о чем-то похожем в своих странствиях. Поможешь разобраться?",
					10 => "Я постараюсь помочь",
					2 => "Нет, я не смогу этого сделать",
				);
			} elseif ($_GET['qaction'] == 10 && $questexist === FALSE && !isset($qcomplete[15])) {
				// квест - прохудившаяся лодка
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,15) or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = array(
					0 => "Спасибо, ты мне очень поможешь!",
					11111 => "Пока",
				);
			} elseif ($_GET['qaction'] == 14 && $questexist === FALSE && !isset($qcomplete[21])) {
				// квест - гнилая вода
				mysql_query('START TRANSACTION') or QuestDie();
				SetNewQuest($user,21,"0/0/0") or QuestDie();
				mysql_query('COMMIT') or QuestDie();
				$mldiag = array(
					0 => "Спасибо, ты мне очень поможешь!",
					11111 => "Пока",
				);
			} elseif ($_GET['qaction'] == 13 && $questexist === FALSE) {
				$mldiag = array(
					0 => "Честно говоря… сам я мало знаю о ремонте лодок, в отличии от управления ими. Возможно, тебе следует спросить помощи у Кузнеца?",
					14 => "Хорошо, я так и сделаю.",
					2 => "Боюсь у меня нет на это времени. ",
				);

			} elseif ($_GET['qaction'] == 12 && $questexist === FALSE) {
				$mldiag = array(
					0 => "Мои лодки много лет верой и правдой служили всем, кто хотел переправиться через реку, но теперь все они либо разбиты, либо дали течь. Помоги мне решить эту проблему, что бы я и дальше мог переправлять путников с одного берега на другой.",
					13 => "А что от меня требуется?",
					2 => "Боюсь у меня нет на это времени. ",
				);
			} elseif ($_GET['qaction'] == 2 && $questexist === FALSE) {
				$mldiag = array(
					0 => "У меня есть несколько просьб к тебе, но одновременно ты сможешь выполнить только одну. Выбери, в чем ты готов мне помочь сегодня?",
					11 => "Гнилая вода",
					12 => "Прохудившаяся лодка",
					11111 => "Мне не нравится ни одна из твоих просьб. Прощай.",
				);

				// тут режим квесты лодочника
				if (isset($qcomplete[15])) unset($mldiag[11]);
				if (isset($qcomplete[21])) unset($mldiag[12]);
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая. Если ты свободен во времени и поступках и ищешь приключений, не захочешь ли ты помочь мне кое в чем?",
				1 => "Мне бы переправиться на ту сторону. Сколько это будет стоить?",
				2 => "Я готов помочь. Говори, что надо сделать.",
				3 => "Не надо, я просто проходил мимо. Пойду дальше.",
			);
			if ($questexist !== FALSE) {
				$mldiag[0] = "Приветствую. Мои лодки всегда к услугам путников. Переправа быстрая и недорогая";
				unset($mldiag[2]);
			}
		}
	}

	$mlquest = "0/0";
	if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
}	
?>
</div>


<?php
}
?>


</td></tr></table>
 
</div>
</TD>
</TR>
</TABLE>

<?php
	require_once('mldown.php');
?>