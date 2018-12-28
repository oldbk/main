<?php

	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$q31 = false;

	$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
	if (mysql_num_rows($q) > 0) {
		$questexist = mysql_fetch_assoc($q) or die();
	}

	$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31"') or die();
	if (mysql_num_rows($q) > 0) {
		$q31 = mysql_fetch_assoc($q);
	}

	$q = mysql_query('SELECT * FROM map_var WHERE var = "cango" AND owner = '.$user['id'].' AND val > '.time()) or die();
	if (mysql_num_rows($q) > 0) {
		$questexist['q_id'] = 0;
	}


	//if ($q31 !== false && $q31['val'] == 13) $q31 = false;

	$isrepok = false;

	if ($q31 === false) {
		$mlknight = array(
			0 => array(
				"0"  => "Заходи, добрый путник. Желаешь отдохнуть с дороги и погреться у камина, или спешишь по делам? Или хочешь помочь мне в одной просьбе? Но должен предупредить, что просьба очень сложная и работа предстоит немалая.",
				"d1" => "Да, я готов помочь. Говори, что надо сделать.",
				"1"  => "Нет, я не из тех, кто помогает всем подряд. Прощай.",
			),
			1 => array(
				"0"  => "У меня есть одна просьба к тебе, не похожая ни на какие другие просьбы жителей загорода. Только настоящий Герой может выполнить это задание. Готов ли ты показать свою силу и упорство?",
				"d2" => "Взять Героический квест (<small>не входит в цикл загородных квестов и может выполняться параллельно с другими квестами. Требует много времени, усилий и затрат. <a href=\"http://oldbk.com/encicl/?/geroickv.html\" target=\"_blank\">Подробнее в Библиотеке ОлдБК.</a></small>)",
				"11111"  => "Мне не нравится твоя просьба. Прощай.",
			),
			2 => array(
				"0"  => "Есть у меня ларец, доставшийся мне еще от моего деда, которому передал его прадед, которому передал его сам Ричард Львиное Сердце. Ларец древнейший и очень ценный. Есть одна загвоздка – ключ от него давно утерян, да и семейная легенда гласит, что просто так его не открыть.  Только настоящий герой, совершивший невероятные подвиги, сможет это сделать.  Слыхал  я, что открывший тот ларец, получит магический Знак Героя, который сделает его еще более сильным.",
				"q31" => "Да, я хочу попробовать его открыть",
				"11111"  => "Нет, я не не буду пытаться",
			),
			"thx31" => array(
				"0" => "Вот держи ларец. Попробуй отнести его к скупщику краденого. Он вечно скупает все подряд. Может у него в закромах есть старые ключи, которые смогут тебе помочь.",
				"11111" => "Хорошо, я попробую. Скоро вернусь.",
			),
		);
	} else {
		if ($q31['val'] != 12 && $q31['val'] != 13)  {
			$mlknight = array(
				0 => array(
					"0" => "Приветствую тебя, мой друг. Смог узнать что-нибудь про ларец?",
					"11111" => "Нет еще, но я работаю над этим.",
				),
			);
		} elseif (QItemExists($user,3003092) && QItemExists($user,3003093) && $q31['val'] != 13) {
			$mlknight = array(
				0 => array(
					"0" => "Приветствую тебя, мой друг. Смог узнать что-нибудь про ларец?",
					"d1" => "Да, я узнал многое и совершил многие подвиги. Даже достал магический элексир, который поможет нам открыть ларец.  Осталось одно незавершенное дело. В благодарность за то, что ты бескорыстно поделился со мной этим магическим ларцом, я хочу отдать тебе 150 тысяч своей репутации. Согласен ли ты ее принять?",
					"11112" => "Да, я узнал многое и совершил многие подвиги. Даже достал магический элексир, который поможет нам открыть ларец.  Осталось одно незавершенное дело, и я вернусь с ним к тебе чуть позже.",
					"11111" => "Нет еще, но я работаю над этим.",
				),
				1 => array(
					0 => "Вот это да!.. Даже не знаю как тебя благодарить за такой щедрый подарок... Спасибо!",
					"d2" => "Ну что-ж, тогда и вопросов больше нет. Моя репутация теперь твоя! Осталось выпить  элексир и попробовать открыть  ларец.",
				),
				2 => array(
					0 => "Смотри! Ларец открылся! О, Боже... что это в нем? Знак Героя! Никогда его не видел! Ну что-ж, он по праву принадлежит тебе! Ты совершил великие подвиги и был бесконечно щедр к людям. Носи это Знак с честью, и с этого момента ты будешь еще сильнее, чем был прежде.",
					"q32" => "Спасибо, Одинокий Рыцарь. Но без тебя ничего бы не было. Поэтому Знак Героя и заберу себе, а тебе хочу оставить этот ларец на память, все-таки это твоя семейная реликвия.",
				),
			);

			if ($user['repmoney'] >= 150000) {
				unset($mlknight[0]["11112"]);
				$isrepok = true;
			} else {
				unset($mlknight[0]["d1"]);
			}
		} else {
			if (!QItemExists($user,3003092) && $q31['val'] != 13) {
				$mlknight = array(
					0 => array(
						"0" => "Приветствую тебя, мой друг. Я не вижу у тебя Ларца.",
						"11111" => "Буду искать.",
					),
				);
			} elseif(!QItemExists($user,3003093) && $q31['val'] != 13) {
				$mlknight = array(
					0 => array(
						"0" => "Приветствую тебя, мой друг. Я не вижу у тебя Эликсира.",
						"11111" => "Буду искать.",
					),
				);
			} else {
				$mlknight = array(
					0 => array(
						0 => "Привет, давно не виделись, как дела?",
						11111 => "Все хорошо, спасибо, забежал на минутку. Пока.",
					),
				);
			}
		}
	}


	if (isset($_GET['qaction']) && strlen($_GET['qaction']) && $questexist === FALSE || isset($_GET['quest']) && $questexist === FALSE) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}

		if ($qa[0] == "d" && isset($mlknight[$num])) {
			$mldiag = $mlknight[$num];
		} elseif ($qa[0] == "q") {
			if ($num == 31 && $q31 == false) {
				// героический квест
				$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner = '.$user['id'].' AND var = "q31"') or die();
				if (mysql_num_rows($q) > 0) {
					$q31 = mysql_fetch_assoc($q);
				}
				if ($q31 === false) {
					mysql_query('START TRANSACTION') or QuestDie();
					addchp ('<font color=red>Внимание!</font> Одинокий Рыцарь передал вам <b>Ларец</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']);
					$mldiag = $mlknight["thx31"];
					PutQItem($user,3003092,"Одинокий Рыцарь") or QuestDie();
					mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`) VALUES ('.$user['id'].',"q31","0")') or QuestDie();
	                                mysql_query('COMMIT') or QuestDie();
				}
			}
			if ($num == 32 && $q31 !== false && $q31['val'] == 12 && $isrepok == true) {
				// завершаем героический квест
				$t1 = QItemExistsID($user,3003092);
				$t2 = QItemExistsID($user,3003093);
				if ($t1 !== FALSE && $t2 !== FALSE) {
					$todel = array_merge($t1,$t2);
					mysql_query('START TRANSACTION') or QuestDie();
					mysql_query('UPDATE oldbk.map_var SET val = 13 WHERE owner = '.$user['id'].' AND var = "q31"') or QuestDie();
					mysql_query('UPDATE oldbk.users SET repmoney = repmoney - 150000 WHERE id = '.$user['id']) or QuestDie();
					mysql_query('UPDATE oldbk.users SET medals = CONCAT(medals,"k202;") WHERE id = '.$user['id']) or QuestDie();
					PutQItemTo($user,"Одинокий Рыцарь",$todel);

					// в дело	                                                                                              
					$rec = array();
					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['owner_rep_do']=$user['repmoney'];
					$rec['owner_rep_posle']=$user['repmoney']-150000;
					$rec['sum_rep']=150000;
					$rec['target']="0";
					$rec['target_login'] = "Загород квест";
					$rec['add_info'] = "31";
					$rec['type']=271; // закончил квест
					if(add_to_new_delo($rec) === FALSE) QuestDie();

	                                mysql_query('COMMIT') or QuestDie();
					unsetQA();
				}
			}
		} else {
			UnsetQA();
		}
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlknight.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlknight_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 300px; top: 65px;" src="http://i.oldbk.com/i/map/mlknight_pers1.png" alt="Рыцарь" title="Рыцарь" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlknight_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlknight_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	if ($questexist !== FALSE) {
		// есть квест - подключаем квестовый обработчик
		// в $midiag будет результат обработчика, если не будет - значит перс не отновится к квесту и будет "пустой" диалог ниочём взят из miquest.php
		require_once('./mapquests/'.$questexist['q_id'].'.php');
	} else {
		// нету квеста, разговариваем

	}
	
	$mlquest = "0/100";
	if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
}	

?>
</div>


</td></tr></table>
 
</div>
</TD>
</TR>
</TABLE>

<?php
	require_once('mldown.php');
?>