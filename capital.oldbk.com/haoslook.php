<?php
	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include "functions.php";
	                       
	if(($user[align] > 1 && $user[align] < 2) || ADMIN) {
		$access=check_rights($user);
	}

	if(ADMIN) {
		$access[item_hist]=1;
	}
	
	if (!$access[item_hist]) { header('Location: main.php'); die(); }
?>
<HTML><HEAD>
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/main.css">
</head>
<body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor="e2e0e0">
<?php
$addsql = "";
if (isset($_POST['telo'])) {
	$_POST['telo'] = trim($_POST['telo']);
	if (!empty($_POST['telo'])) {
		$addsql = ' AND users.login = "'.$_POST['telo'].'"';
	}
}

$q = mysql_query('
SELECT * FROM oldbk.effects LEFT JOIN oldbk.users ON oldbk.users.id = oldbk.effects.owner WHERE oldbk.users.id_city = 0 AND oldbk.effects.type = 4 AND oldbk.effects.lastup > 0 '.$addsql.'
UNION
SELECT * FROM avalon.effects LEFT JOIN avalon.users ON avalon.users.id = avalon.effects.owner WHERE avalon.users.id_city = 1 AND avalon.effects.type = 4 AND avalon.effects.lastup > 0 '.$addsql.'
');
?>

<form method=post action="haosexit.php">Введите ник хаосника <input name=telo type=text value="<?php echo htmlspecialchars($_POST[telo],ENT_QUOTES); ?>"><input type=submit value="просмотреть">
<table width=80%>
<tr>
<td valign=top>
<b>Список игроков в хаосе:</b><br>
<?php
$arr = array();
while($u = mysql_fetch_assoc($q)) {
	if (time()-$u['lastup'] > 24*3600*14) $arr[] = $u;
	echo nick_align_klan($u);
	$t = unserialize($u['add_info']);
	if ($t['kr'] > 0) echo ' - (<a target="_blank" href="haosexit.php?telo='.htmlspecialchars($u['login'],ENT_QUOTES).'">просмотр</a>)';
	echo '<br>';
}
?>
</td>
<td valign=top>
<b>Список игроков в хаосе более 2-х недель:</b><br>
<?php
	reset($arr);
	while(list($k,$v) = each($arr)) {
		echo nick_align_klan($v)."<br>";
	}
?>
</td></tr></table>
</body>	
</html>
