	<?php
	//session_start();
	//if ($_SESSION['uid'] == null) header("Location: index.php");
	include "connect.php";	
	include "functions.php";	
	$tr = mysql_fetch_array(mysql_query("SELECT * FROM `tur_logs` WHERE `id` = '".(int)$_GET['id']."'"));
	if ((($tr[type] >=240) and ($tr[type] <=269) ) and ((int)($tr[logs])>0) )
		{
		header("Location: logs.php?log={$tr[logs]}");
		}
	?>
<HTML>
	<HEAD>
		<link rel=stylesheet type="text/css" href="i/main.css">
		<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
		<META Http-Equiv=Cache-Control Content=no-cache>
		<meta http-equiv=PRAGMA content=NO-CACHE>
		<META Http-Equiv=Expires Content=0>
		<script type="text/javascript" src="/i/globaljs.js"></script>
	</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=e2e0e0>
<?
		if ($tr[id] > 0)
		{
		$type=$rooms[$tr[type]];
		$winer=$tr[winer];
		echo "<H3>Отчет о турнире: ".$type." </H3>";
		echo $tr['logs'];
		echo "<br>\n";
		}
		else
		{
		echo "<font color=red><b>Турнир не найден!</b></font>";		
		}
?>			
		
</BODY>
</HTML>
