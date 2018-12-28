<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');
	require_once('map_config.php');

	// есть-нет лошади
	$user['horse'] = $user['podarokAD'];

	$self = basename($_SERVER['PHP_SELF']);
	$bfound = false;
	reset($map_locations);
	while(list($k,$v) = each($map_locations)) {
		if ($v['redirect'] == $self && $v['room'] == $user['room']) {
			$bfound = true;
			break;
		}
	}

	if ($bfound === FALSE) Redirect("main.php");
	if ($user['battle'] != 0 || $user['battle_fin'] != 0) { Redirect("fbattle.php"); }

	if ($user['level'] < 6 && !isset($_GET['error'])) {
		Redirect("aoutcity.php?error=2");
	}

	/*	
	if (isset($_GET['qaction']) && $_GET['qaction'] == 2 && !isset($_GET['error'])) {
		if ($user['horse']) {
			Redirect("aoutcity.php?error=0");
		}

		$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"') or die();
		if (mysql_num_rows($q) > 0) {
			$lastcity = mysql_fetch_assoc($q) or die();
			$t = explode(":",$lastcity['val']);
			if ($t[0] > time()) {
				// есть штраф на время, проверяем город
				if ($t[1] != 1) {
					Redirect("aoutcity.php?error=3");
				}
			} else {
				// штраф прошёл - проверяем, если город изменился то вешаем новый
				if ($t[1] != 1) {
					mysql_query('UPDATE map_qvar SET val = "'.((time()+(24*3600*7)).':1').'" WHERE owner = '.$user['id'].' AND var = "lastcity"') or die();
				}
			}
		} else {
			mysql_query('INSERT INTO `map_qvar` (`owner`,`var`,`val`) VALUES('.$user['id'].',"lastcity","'.((time()+(24*3600*7)).':1').'")') or die();
		}


		$_POST['target'] = "avalon";
		mysql_query("UPDATE `users`  SET `users`.`room` = '26' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
		$user['room'] = 26;
		$ABIL = 1;
		$NOCHANGECITY = 1;
		require_once('./magic/city_teleport.php');
		die();

	}


	if (isset($_GET['qaction']) && $_GET['qaction'] == 3 && !isset($_GET['error'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '".($maprel+$maprelall+1000)."' WHERE `users`.`id` = '{$_SESSION['uid']}' ;") or die();
		Redirect('city.php');	
	}
	*/


	if (isset($_GET['qaction']) && $_GET['qaction'] == 1 && !isset($_GET['error'])) {
		$_SESSION['mappath'] = array();
		$_SESSION['mapcost'] = 0;

		$q = mysql_query('START TRANSACTION') or die();

		$roomtoexit = 627;
		$roomtoexit += $maprel;

		$teamcache[$user['id']] = nick_hist_horse($user);

		$q = mysql_query('INSERT INTO map_groups (leader,horse,room,team,path,wannajoin,team_cache,nextcost) VALUES ('.$user['id'].','.$user['horse'].','.$roomtoexit.',"","","","'.mysql_real_escape_string(serialize($teamcache)).'",0)') or die();
		$id = mysql_insert_id();

		mysql_query('UPDATE `users` SET room = '.$roomtoexit.', id_grup = '.$id.' WHERE id = '.$_SESSION['uid']) or die();

		$q = mysql_query('COMMIT') or die();
		Redirect('map.php');	
	}

	/*
	if (isset($_GET['qaction']) && $_GET['qaction'] == 66 && !isset($_GET['error'])) {
		$q = mysql_query('START TRANSACTION') or die();

		$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "cfromcity"') or die();
		if (mysql_num_rows($q) > 0) {
			mysql_query('UPDATE map_qvar SET val = "1" WHERE owner = '.$user['id'].' AND var = "cfromcity"') or die();
		} else {
			mysql_query('INSERT INTO `map_qvar` (`owner`,`var`,`val`) VALUES('.$user['id'].',"cfromcity","1")') or die();
		}

		mysql_query('UPDATE `users` SET room = 70000 WHERE id = '.$_SESSION['uid']) or die();
		$q = mysql_query('COMMIT') or die();
		Redirect('castles.php');	
	}*/

?>


<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
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
		<input type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();"> 
		<!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/outcity.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlgate2_bg.jpg" id="mainbg">
<a href="?quest"><img style="z-index:3; position: absolute; left: 245px; top: 150px;" src="http://i.oldbk.com/i/map/mlgate_pers1.png" alt="Страж" title="Страж" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlgate_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlgate_pers1.png'"/></a>
<?php
if (isset($_GET['quest']) || isset($_GET['error'])) {
	$mlquest = "5/100";
	if (isset($_GET['error'])) {
		$err = "";
		switch($_GET['error']) {
			/*
			case 0:
				$err = 'На лошади запрещен вход в город, верните её в конюшню';
			break;
			case 2:
				$err = "Вход только с 6го уровня.";
			break;
			case 3:
				$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "lastcity"') or die();
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
			*/
		}
		/*
		if (empty($err)) die();

		$mldiag = array(
			3 => $err,
			//2 => "Пройти на Парковую улицу.",
			66=> "Перейти к Замкам.",
			4 => "Пока!",
		);

		if ($_GET['error'] == 3 || $_GET['error'] == 0) unset($mldiag[2]);
		*/
	} else {
		$mldiag = array(
			0 => "Проход закрыт!",
			1 => "Хочу выйти из ворот и отправиться путешествовать.",
			//2 => "Пройти на Парковую улицу.",
			//66=> "Перейти к Замкам.",
			//3 => "Проведи меня в конюшню.",
			//4 => "Нет, мне ничего не надо, пока!",
		);
	}
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