<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	$questexist = false;
	$q = mysql_query('SELECT * FROM oldbk.map_quests WHERE owner = '.$user['id']) or die();
	if (mysql_num_rows($q) > 0) {
		$questexist = mysql_fetch_assoc($q) or die();
	}

	// 8ой квест
	if (isset($_GET['questget8']) && $questexist !== FALSE && $questexist['q_id'] == 8 && ($questexist['step'] == 4 || $questexist['step'] == 5)) {
		if ($questexist['step'] == 4) {
			mysql_query('START TRANSACTION') or QuestDie();
			StartQuestBattle($user,533, array(
				"hp" => 9000, 
				"min_u" => 900,
				"max_u" => 1000,
				"maxhp" => 9000,
				"sila" => 9000,
				"lovk" => 9000,
				"inta" => 9000,
				"vinos" => 9000,
				"level" => $user['level'],
				),1,'«Глупец, неужто ты решил, что сможешь одолеть меня? Меня не убить простым оружием, а тебя ждет смерть лютая!»') or QuestDie();
			mysql_query('COMMIT') or QuestDie();
			Redirect('fbattle.php');
		} elseif ($questexist['step'] == 5) {
			// ребёнка в инвентарь
			mysql_query('START TRANSACTION') or QuestDie();			
				PutQItem($user,3003027,"Дракон") or QuestDie();
				SetQuestStep($user,8,6) or QuestDie();
			mysql_query('COMMIT') or QuestDie();
			Redirect('mldragon.php');
		}
	}

	// 1ый квест
	if (mt_rand(0,1) == 1 && isset($_GET['questget'])) {
		mysql_query('START TRANSACTION') or QuestDie();
		StartQuestBattle($user,533) or QuestDie();
		mysql_query('COMMIT') or QuestDie();
		Redirect('fbattle.php');
	}

	// 20ый квест
	if (mt_rand(0,1) == 1 && isset($_GET['questget20']) && $questexist !== FALSE && $questexist['q_id'] == 20 && $questexist['step'] == 2) {
		mysql_query('START TRANSACTION') or QuestDie();
		StartQuestBattle($user,533) or QuestDie();
		mysql_query('COMMIT') or QuestDie();
		Redirect('fbattle.php');
	}

	// 29ый квест
	if (mt_rand(0,1) == 1 && isset($_GET['questget29']) && $questexist !== FALSE && $questexist['q_id'] == 29 && $questexist['step'] == 1) {
		mysql_query('START TRANSACTION') or QuestDie();
		StartQuestBattle($user,533) or QuestDie();
		mysql_query('COMMIT') or QuestDie();
		Redirect('fbattle.php');
	}


	// драка-нападение
	if (isset($_GET['quest'])) {
		// драка с драконом
		mysql_query('START TRANSACTION') or QuestDie();
		StartQuestBattle($user,533) or QuestDie();
		mysql_query('COMMIT') or QuestDie();
		Redirect('fbattle.php');
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
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mldragon.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table border = 0 width=1><tr><td valign=top>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mldragon_bg.jpg" id="mainbg">
<a href="?quest=1"><img style="z-index:3; position: absolute; left: 250px; top: 150px;" src="http://i.oldbk.com/i/map/mldragon_pers1.png" alt="Дракон" title="Дракон" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mldragon_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mldragon_pers1.png'"/></a>
</div>
<?php
if ($questexist !== FALSE) {
	
	// для первого квеста
	if ($questexist['q_id'] == 1 && $questexist['step'] == 1) {
		mysql_query('START TRANSACTION') or QuestDie();
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND prototype = 3003003') or QuestDie();
		if (mysql_num_rows($q) == 0) {
			if (isset($_GET['questget'])) {
				PutQItem($user,3003003,"Дракон",60*30) or QuestDie();
			} else {
				echo '</td><td valign=top nowrap><a href="?questget"><img alt="Помёт дракона" title="Помёт дракона" src="http://i.oldbk.com/i/sh/pomet_drakona.gif"></a>';
			}
		}
		mysql_query('COMMIT') or QuestDie();	
	}

	// для двадцатого квеста
	if ($questexist['q_id'] == 20 && $questexist['step'] == 2) {
		mysql_query('START TRANSACTION') or QuestDie();
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND prototype = 3003074') or QuestDie();
		if (mysql_num_rows($q) == 0) {
			if (isset($_GET['questget20'])) {
				PutQItem($user,3003074,"Дракон") or QuestDie();
				SetQuestStep($user,20,3) or QuestDie();	
			} else {
				echo '</td><td valign=top nowrap><a href="?questget20"><img alt="Череп с золотыми зубами" title="Череп с золотыми зубами" src="http://i.oldbk.com/i/sh/skull_w_gold.gif"></a>';
			}
		}
		mysql_query('COMMIT') or QuestDie();	
	}

	// для двадцатого девятого квеста
	if ($questexist['q_id'] == 29 && $questexist['step'] == 1) {
		mysql_query('START TRANSACTION') or QuestDie();
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$user['id'].' AND prototype = 3003206') or QuestDie();
		if (mysql_num_rows($q) == 0) {
			if (isset($_GET['questget29'])) {
				PutQItem($user,3003206,"Дракон") or QuestDie();
			} else {
				echo '</td><td valign=top nowrap><a href="?questget29"><img alt="Цветок «Любисток»" title="Цветок «Любисток»" src="http://i.oldbk.com/i/sh/q29_2.gif"></a>';
			}
		}
		mysql_query('COMMIT') or QuestDie();	
	}


	// для восьмого квеста
	if ($questexist['q_id'] == 8 && ($questexist['step'] == 4 || $questexist['step'] == 5)) {
		echo '</td><td valign=top nowrap><a href="?questget8"><img alt="Ребёнок" title="Ребёнок" src="http://i.oldbk.com/i/sh/babe4.gif"></a>';
	}
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