<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	}
	$mldiag = array(); //действия пользователя в диалоге с ботом

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');
	require_once('map_config.php');

	$self = basename($_SERVER['PHP_SELF']);

	// есть-нет лошади
	$user['horse'] = $user['podarokAD'];

	if ($user['room'] != 49999) Redirect("main.php");
	if ($user['battle'] != 0 || $user['battle_fin'] != 0) { Redirect("fbattle.php"); }

	if (isset($_GET['qaction']) && $_GET['qaction'] == 2) {
		if ($user['horse']) {
			Redirect("outcity.php?error=0");
		}

/*
		$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"') or die();
		if (mysql_num_rows($q) > 0) {
			$lastcity = mysql_fetch_assoc($q) or die();
			$t = explode(":",$lastcity['val']);
			if ($t[0] > time()) {
				// есть штраф на время, проверяем город
				if ($t[1] != 0) {
					Redirect("outcity.php?error=3");
				}
			} else {
				// штраф прошёл - проверяем, если город изменился то вешаем новый
				if ($t[1] != 0) {
					mysql_query('UPDATE map_qvar SET val = "'.((time()+(24*3600*7)).':0').'" WHERE owner = '.$user['id'].' AND var = "lastcity"') or die();
				}
			}
		} else {
			mysql_query('INSERT INTO `map_qvar` (`owner`,`var`,`val`) VALUES('.$user['id'].',"lastcity","'.((time()+(24*3600*7)).":0").'")') or die();
		}
		*/
		mysql_query("UPDATE `users` SET `users`.`room` = '26' WHERE `users`.`id` = '{$_SESSION['uid']}' ;") or die();


		Redirect('city.php');	
	}

	if ($user['level'] < 6 && !isset($_GET['error'])) {
		Redirect("outcity.php?error=2");
	}

	if (isset($_GET['qaction']) && $_GET['qaction'] == 1 && !isset($_GET['error'])) {
		$_SESSION['mappath'] = array();
		$_SESSION['mapcost'] = 0;

		$q = mysql_query('START TRANSACTION') or die();
		
		$roomtoexit = 3156;
		$roomtoexit += $maprel;

		$teamcache[$user['id']] = nick_hist_horse($user);

		$q = mysql_query('INSERT INTO map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$user['id'].','.$user['horse'].','.$roomtoexit.',"","","","'.mysql_real_escape_string(serialize($teamcache)).'",0)') or die();
		$id = mysql_insert_id();

		mysql_query('UPDATE `users` SET room = '.$roomtoexit.', id_grup = '.$id.' WHERE id = '.$_SESSION['uid']) or die();

		$q = mysql_query('COMMIT') or die();
		Redirect('map.php');	
	}

	if (isset($_GET['qaction']) && $_GET['qaction'] == 66 && !isset($_GET['error']) /*&& ADMIN*/) {
		$q = mysql_query('START TRANSACTION') or die();

		$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "cfromcity"') or die();
		if (mysql_num_rows($q) > 0) {
			mysql_query('UPDATE map_qvar SET val = "0" WHERE owner = '.$user['id'].' AND var = "cfromcity"') or die();
		} else {
			mysql_query('INSERT INTO `map_qvar` (`owner`,`var`,`val`) VALUES('.$user['id'].',"cfromcity","0")') or die();
		}

		mysql_query('UPDATE `users` SET room = 70000 WHERE id = '.$_SESSION['uid']) or die();
		$q = mysql_query('COMMIT') or die();
		Redirect('castles.php');	
	}


	if (isset($_GET['qaction']) && $_GET['qaction'] == 3 && !isset($_GET['error'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '".($maprel+$maprelall+999)."' WHERE `users`.`id` = '{$_SESSION['uid']}' ;") or die();
		Redirect('city.php');	
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
	<TD align=center> </TD>
	<TD align=right>
        <div class="btn-control">
		    <!--input type="button" style="cursor: pointer;" name="Выйти на Большую Парковую" value="Выйти на Большую Парковую" OnClick="location.href='?qaction=2';"> -->
		    <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
		    <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/outcity.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
        </div>
	</TD>
	</TR>
	<TR><TD align=center valign=top colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlgate1_bg2.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 245px; top: 150px;" src="http://i.oldbk.com/i/map/mlgate_pers1.png" alt="Страж" title="Страж" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlgate_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlgate_pers1.png'"/></a>
<a href="?qaction=1"><img style="z-index:3; position: absolute; left: 245px; top: 100px;" src="http://i.oldbk.com/i/map/mlgate_up.png" alt="Выйти в Загород" title="Выйти в Загород" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlgate_up_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlgate_up.png'"/></a>
<a href="?qaction=66"><img style="z-index:3; position: absolute; left: 20px; top: 250px;" src="http://i.oldbk.com/i/map/mlgate_arrow_l.png" alt="Пройти к замкам" title="Пройти к замкам" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlgate_arrow_l_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlgate_arrow_l.png'"/></a>
<a href="?qaction=3"><img style="z-index:3; position: absolute; left: 490px; top: 250px;" src="http://i.oldbk.com/i/map/mlgate_arrow_r.png" alt="Пройти в конюшню" title="Пройти в конюшню" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlgate_arrow_r_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlgate_arrow_r.png'"/></a>
<a href="?qaction=2"><img style="z-index:3; position: absolute; left: 220px; top: 342px;" src="http://i.oldbk.com/i/map/mlgate_down.png" alt="Перейти на Большую парковую улицу" title="Перейти на Большую парковую улицу" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlgate_down_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlgate_down.png'"/></a>
<?php

$BotDialog = new \components\Component\Quests\QuestDialogNew(\components\Helper\BotHelper::BOT_STRAZH);
if(isset($_GET['qaction']) && isset($_GET['d'])) {
	//зашли в движок квестов
	$dialog_id = isset($_GET['d']) ? (int)$_GET['d'] : null;
	$action_id = isset($_GET['a']) ? (int)$_GET['a'] : null;
	$dialog = $BotDialog->dialog($dialog_id, $action_id);
	if($dialog !== false) {
		$mldiag[0] = $dialog['message'];
		foreach ($dialog['actions'] as $action) {
			$key = '&a='.$action['action'];
			if(isset($action['dialog'])) {
				$key .= '&d='.$action['dialog'];
			}
			$mldiag[$key] = $action['message'];
		}
	}
}
if ((isset($_GET['quest']) || isset($_GET['error'])) && empty($mldiag)) {
	$mlquest = "60/60";
	if (isset($_GET['error'])) {
		$err = "";
		switch($_GET['error']) {
			case 0:
				$err = 'На лошади запрещен вход в город, верните её в конюшню.';
			break;
			case 2:
				$err = "Вход только с 6го уровня.";
			break;
			case 3:
				$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"');
				$lastcity = mysql_fetch_assoc($q) or die();
				$t = explode(":",$lastcity['val']);
				$timeleft = $t[0]-time();
				if ($timeleft > 0) {
					$t = $timeleft;
					if ($t < 60) $t = 60;
					$m = ceil($t / 60);
					$h = floor($m / 60);

					$err = "К сожалению я не могу пропустить тебя в Город. Переход через Загород в другой Город возможен не чаще, чем раз в неделю. Ты сможешь пройти через Городские ворота только через ".$h." ч. ".($m-$h*60)." мин.";
				}
			break;
		}

		if (empty($err)) die();

		$mldiag = array(
			4 => $err,
			2 => "Перейти на Большую парковую улицу.",
			3 => "Проведи меня в конюшню.",
			66 => "Перейти к Замкам.",
			5 => 'Пока!',
		);
		

		if ($user['horse']) unset($mldiag[2]);
		

		//if (!ADMIN) unset($mldiag[66]);

		//if ($_GET['error'] == 3 || $_GET['error'] == 0) unset($mldiag[2]);
		if ($user['level'] < 6) unset($mldiag[66]);
	} else {
		$mldiag = array(
			0 => "Приветствую тебя путник! Городские ворота всегда на замке, но я готов их открыть для тебя. Скажи, куда ты направляешься? Если ты идешь за город, тебе может не помешать лошадь, которую можно взять в конюшне.Если ты идешь в город, не забудь вернуть лошадь в конюшню, улицы города слишком тесны для конников.",
		//	1 => "Хочу выйти из города и отправиться путешествовать.",
		//	2 => "Перейти на Большую парковую улицу.",
		//	66 => "Перейти к Замкам.",
		//	3 => "Проведи меня в конюшню.",
		);

		foreach ($BotDialog->getMainDialog() as $dialog) {
			$key = '&d='.$dialog['dialog'];
			$mldiag[$key] = $dialog['title'];
		}
		
		$mldiag[4] = "Нет, мне ничего не надо, пока!";

		//if (!ADMIN) unset($mldiag[66]);
	}
}

if(!empty($mldiag)) {
	$mlquest = "60/60";
	require_once('mlquest.php');
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