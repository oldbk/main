<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	include('connect.php');
	include('functions.php');
	require_once('castles_config.php');
	require_once('castles_functions.php');

	if ($user['room'] != 70000) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	if (isset($_GET['exit'])) {
		$q = mysql_query('SELECT * FROM map_qvar WHERE owner = '.$user['id'].' AND var = "cfromcity"') or die();
		$var = mysql_fetch_assoc($q) or die();

		if ($var['val'] == 0) {
			mysql_query('UPDATE `users` SET room = 49999 WHERE id = '.$_SESSION['uid']) or die();		
			Redirect("outcity.php");
		} else {
			mysql_query('UPDATE `users` SET room = 49998 WHERE id = '.$_SESSION['uid']) or die();		
			Redirect("aoutcity.php");
		}
		
	}

	if (isset($_GET['tur'])) {
		mysql_query('UPDATE `users` SET room = 72001 WHERE id = '.$_SESSION['uid']) or die();		
		Redirect("castles_tur.php");
	}

	
	if (isset($_GET['enter'])) {
		$_GET['enter'] = intval($_GET['enter']);

		if ($_GET['enter'] == 999) {
			mysql_query('UPDATE `users` SET room = 72002 WHERE id = '.$_SESSION['uid']) or die();
			Redirect("castles_osada.php");		
		} else {
			$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$_GET['enter']);
			if (mysql_num_rows($q) > 0) {
				mysql_query('UPDATE `users` SET room = '.(70000+$_GET['enter']).' WHERE id = '.$_SESSION['uid']) or die();
				Redirect("castles_pre.php");		
			}
		}
	}

	$castles = array();
	$q = mysql_query('SELECT * FROM castles WHERE id != 155');

	while($c = mysql_fetch_assoc($q)) {
		$castles[$c['nlevel']][$c['num']] = $c;
	}


	if ($user['id'] == 102904) {
		$q = mysql_query('SELECT * FROM castles WHERE id = 155');
	
		while($c = mysql_fetch_assoc($q)) {
			$castles[$c['nlevel']][$c['num']] = $c;
		}	
	}

	if (!isset($_GET['level'])) {
		if (isset($castles[$user['level']])) {
			$level = $user['level'];
		} else {
			$k = array_keys($castles);
			if (count($k)) {
				$level = $k[0];
			} else {
				die("E1");
			}
		}
	} else {
		$level = intval($_GET['level']);
		if (!isset($castles[$level]) && $level != 999 && $level != 400) {
			$k = array_keys($castles);
			if (count($k)) {
				$level = $k[0];
			} else {
				die("E1");
			}
		}

		if($level == 400 && \components\Component\Config::isTester($user)) {
			header('location: /action/street/clan/enter');die();
        }
	}

	/*
	статусы:
		0 - замок удерживается. защищен от нападения еще time()-timeout или открыт для нападения если time() >= timeout
		1 - замок удерживается. клан YYY обьявил о нападении. начало боя timeoutb
		2 - идёт сражение за замок
	*/		
?>
<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>
	function SwitchTab(newt) {
		document.getElementById("m"+newt).style.backgroundImage='url(http://i.oldbk.com/i/map/active_bg.jpg)';
		document.getElementById("t"+newt).style.color="#464646";
		document.getElementById("m"+newt).style.fontWeight="bold";
	}

</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="http://i.oldbk.com/i/castles/jquery.qtip.min.js" type="text/javascript"></script>
<link href="http://i.oldbk.com/i/castles/jquery.qtip.css" type="text/css" rel="stylesheet">
<script>
$(document).ready(function() {
	$('img').qtip({
		position: {
			my: 'left center',
			at: 'right center'
		},
		show: {
			solo: true
		}
	});
});

</script>
</head>
<body bgcolor=#D7D7D7 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">
<br><br>
<center>
<table border=0 style="text-align:center; padding:0px; margin:0px;border-collapse:collapse;">
<tr>
<?php
	reset($castles);
	while(list($k,$v) = each($castles)) {
		echo '<td id="m'.$k.'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?level='.$k.'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t'.$k.'" color="#a4a4a4">Уровень '.$k.'</td>';
	}

	echo '<td id="m'.($k+1).'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?tur='.$k.'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t'.($k+1).'" color="#a4a4a4">Турниры <img src="http://i.oldbk.com/i/castles/castle_icon.png"></td>';
	echo '<td id="m999" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?level=999\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t999" color="#a4a4a4">Осада замка</td>';
	if(\components\Component\Config::isTester($user)) {
		echo '<td id="m400" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?level=400\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t400" color="#a4a4a4">Клан замок</td>';
    }
	echo '<td id="m'.($k+3).'" OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?exit='.$k.'\';" style="white-space: nowrap; cursor: pointer; width: 108px; height: 26px; background-repeat: no-repeat; background-image: url(http://i.oldbk.com/i/map/passive_bg.jpg);"><font id="t'.($k+3).'" color="#a4a4a4" onmouseout="document.getElementById(\'backi\').src=\'http://i.oldbk.com/i/castles/back_button.png\';" onmouseover="document.getElementById(\'backi\').src=\'http://i.oldbk.com/i/castles/back_button_hover2.png\';">Вернуться <img src="http://i.oldbk.com/i/castles/back_button_hover2.png" align="middle"></td>';
?>
</tr>
</table>
<script>
<?php
	echo 'SwitchTab('.$level.')';
?>
</script>

<div id="d1">
	<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0">
		<TR><TD align=center valign=top>
		<table width=1 border=0 cellspacing="0" cellpadding="0"><tr><td valign=top>
		<div style="position:relative;">
		<?php
			$day = "http://i.oldbk.com/i/castles/cday2.jpg";
			$night = "http://i.oldbk.com/i/castles/cnight3.jpg";
			if((int)date("H") > 5 && (int)date("H") < 22) {
				$bg = $day;
			} else{
				$bg = $night;
			}

		?>
		<img id="imgareamap" src="<?php echo $bg; ?>" border="0" style="z-index:1;">
		<?php
            if ($level === 999) {
				echo '<img OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?enter=999\';" id="castle2" style="cursor:pointer; z-index:3; position: absolute; left: 320px; top: 50px;" src="http://i.oldbk.com/i/castles/osada_zamok.png" alt="Старый замок" title="Старый замок" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/castles/osada_zamok_h.png\';" onmouseout="this.src=\'http://i.oldbk.com/i/castles/osada_zamok.png\';" />';
			} else {
				reset($castles[$level]);
				while(list($k,$v) = each($castles[$level])) {
					echo '<img OnClick="location.href=\''.$_SERVER['PHP_SELF'].'?enter='.$v['id'].'\';" id="castle'.$v['num'].'" style="cursor:pointer; z-index:3; position: absolute; left: '.$castles_config[$v['num']]['left'].'px; top: '.$castles_config[$v['num']]['top'].'px;" src="http://i.oldbk.com/i/castles/c'.$v['num'].'.png" alt="'.$castles_config[$v['num']]['name'].' замок ['.$level.'].<br>'.GetCastleStatus($user,$v).'" title="'.$castles_config[$v['num']]['name'].' замок ['.$level.'].<br>'.GetCastleStatus($user,$v).'" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/castles/c'.$v['num'].'_h.png\';" onmouseout="this.src=\'http://i.oldbk.com/i/castles/c'.$v['num'].'.png\';" />';
					if ($v['clanshort']) {
						echo '<img id="castle'.$v['num'].'icon" style="z-index:4; position: absolute; left: '.($castles_config[$v['num']]['ileft']).'px; top: '.($castles_config[$v['num']]['itop']).'px;" src="http://i.oldbk.com/i/klan/'.$v['clanshort'].'.gif" alt="'.$v['clanshort'].'" title="'.$v['clanshort'].'" />';
					}
				}
			}
		?>
		<?php if ($level != 999) { ?>
			<img id="banner" style="z-index:4; position: absolute; left: 312px; top: 25px;" src="http://i.oldbk.com/i/castles/red<?php echo $level; ?>.png" class="aFilter2" />
		<?php } ?>
		</div>
	</td></tr></TABLE></td></tr></table>
</div>
</body>
</html>
