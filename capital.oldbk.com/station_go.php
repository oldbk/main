<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("station.php");
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');

	if (!(($user['room'] > 61000) and ($user['room'] < 62000)))  { Redirect("main.php"); }
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	$ci = mysql_query('SELECT * FROM station_go WHERE room = '.$user['room']);
	$ci = mysql_fetch_assoc($ci) or die("no caret");

		if ($ci['tocity'] == 0) {
			$tc = "Capital city";
		} elseif ($ci['tocity'] == 1) {
			$tc = "Avalon city";
		}
?>
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	</HEAD>
	<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0">
	<tr><td valign=top align=right><?echo nick_hist($user); ?></td><td valign=top align=center><h2>Едем в <?php echo $tc; ?></h2></td><td valign=top align=right><input type=button value='Обновить' onClick="location.href='station_go.php?'+Math.random();">
		<br><br>До места назначения осталось: <br><font color=blue><?php 
			$t = $ci['endtime']-time();
			if ($t < 0 && (date("s") >= 5 && date("s") < 55)) {
				mysql_query("UPDATE `users` SET `users`.`room` = '61000' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");

				$user['room'] = 20; // Хак чтобы сработал телепорт

				if ($ci['tocity'] == 0) {
					$_POST['target'] = "capital";
					require_once('./magic/city_teleport.php');
					die();
				} elseif ($ci['tocity'] == 1) {
					$_POST['target'] = "avalon";
					require_once('./magic/city_teleport.php');
					die();
				}
			} else {
				if ($t < 60) $t = 60;
				$m = ceil($t / 60);
				$h = floor($m / 60);
				echo $h." ч. ".($m-$h*60)." мин.";
			}
		?></font><br>
	</td></tr>
	<tr><td valign=top align=center colspan=2>
		<?php
			$swfname = "p".mt_rand(1,13).".swf";
			$sz = getimagesize("./i/station/".$swfname);
			$w = $sz[0];
			$h = $sz[1];
		?>

		Чтобы вам не было скучно в дороге, у нас есть игрушки:<br>
		<table cellpadding=0 cellspacing=0 border=0 width=<?php echo ($w+20+20);?>>
		<tr>
			<td width="20" colspan=2 height="20" background="http://i.oldbk.com/i/station/up_left2.jpg">&nbsp;</td>
			<td height="20" background="http://i.oldbk.com/i/station/up_bg.jpg">&nbsp;</td>
			<td width="20" colspan=2 height="20" background="http://i.oldbk.com/i/station/up_right2.jpg">&nbsp;</td>
		</tr>
		<tr>
			<td width="20" background="http://i.oldbk.com/i/station/left_bg.jpg">&nbsp;</td>
		<td colspan=3 width=<?php echo $w;?>><center>
		<script>
			var html='';
			if (navigator.userAgent.match(/MSIE/)) {
				// IE gets an OBJECT tag
				html += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?php echo $w; ?>" height="<?php echo $h; ?>" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"><param name="movie" value="http://i.oldbk.com/i/station/<?php echo $swfname; ?>" /><param name="quality" value="high" /><param name="bgcolor" value="#e0e0e0" /></object>';
			} else {
				// all other browsers get an EMBED tag
				html += '<embed bgcolor=#e0e0e0 src="http://i.oldbk.com/i/station/<?php echo $swfname; ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>" quality="best" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
			}
			document.write(html);
		</script>
		</center>
		</td>
		<td width="20" background="http://i.oldbk.com/i/station/right_bg.jpg">&nbsp;</td>
		</tr>
		<tr>
			<td width="20" colspan=2 height="20" background="http://i.oldbk.com/i/station/down_left2.jpg">&nbsp;</td>
			<td height="20" background="http://i.oldbk.com/i/station/down_bg.jpg">&nbsp;</td>
			<td width="20" colspan=2 height="20" background="http://i.oldbk.com/i/station/down_right2.jpg">&nbsp;</td>
		</tr>

		</table>
		<br>(игра сделана на <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash</a>, чтобы сменить игру, нажмите кнопку "обновить")
		</td><td valign=top align=right>
	        <table border=0 cellpadding=0 cellspacing=0 width="380" height="280" background="http://i.oldbk.com/i/station/kareta_main_bg.jpg"><tr><td valign=top align=center>
		<img style="margin-top:85px;" src="http://i.oldbk.com/i/station/kareta_<?php echo $ci['img'];?>_<?php if(((int)date("H") > 5 && (int)date("H") < 22)) echo 'day'; else echo 'night'; ?>.gif">
		</td></tr>
		</table>
		
	</td></tr>
	</table>
	</body>
	</html>