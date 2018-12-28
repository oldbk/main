<?php
session_start();

include "connect.php";
include "functions.php";

if (!ADMIN) die();


?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
</head>
<body>
<table>
<?php
	$all = 0;
	$q = mysql_query('SELECT * FROM prival_stats LEFT JOIN users ON users.id = prival_stats.owner ORDER BY `value` DESC');
	while($s = mysql_fetch_assoc($q)) {
		echo '<tr><td>'.$s['login'].'</td><td>'.$s['value'].'</td></tr>';
		$all += $s['value'];
	}
?>
</table>
<br>
<b>Итого: <?=$all?> екр.</b>
</body>
</html>
