<?php
session_start();

require_once "connect.php";
require_once "functions.php";

if (!ADMIN) die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="windows-1251">
<title></title>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/json2.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/jquery.serializejson.min.js'></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/jstorage.min.js'></script>

<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
</head>
<body>
<center>
0 Любой<br>
1 Уворотчик<br>
2 Критовик<br>
3 Танк<br>
<br><br>
<table>
<?php

$q = mysql_query('SELECT uclass,count(*) as cc FROM users GROUP BY uclass');
while($u = mysql_fetch_assoc($q)) {
	echo '<tr><td>'.$u['uclass'].'</td><td>'.$u['cc'].'</td></tr>';
}
?>
</center>
</table>
</body>
</html>
