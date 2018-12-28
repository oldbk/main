<?php
	session_start();

	include "connect.php";	
	include "functions.php";
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
	if($_GET['id']>0) {	
		$tr = mysql_fetch_array(mysql_query("SELECT * FROM `dt_map` WHERE `id` = '".(int)$_GET['id']."'"));
		$trlog = mysql_fetch_array(mysql_query("SELECT * FROM `dt_log` WHERE `dt_id` = '".(int)$_GET['id']."'"));
		if (!$trlog) die("Лог не найден</body></html>");
		?>

				
				
		<H3>Башня смерти. Отчет о турнире . <? if($tr['arttype'] == 1) {echo "<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'> Артовая БС.";} ?></H3>
		Призовой фонд: <B><?=$tr['prize']?> кр.</B><BR>
		<?php
			if ($tr['darktype'] && $tr['active']) {
				if (ADMIN) {
					echo $trlog['log']."<BR>";
				} else {
					echo substr($trlog['log'],0,strpos($trlog['log'],'<BR>')+4);
					echo 'Тьма окутывает Башню Смерти<BR>';
				}
			} else {
				echo $trlog['log']."<BR>";
			}
	}		
	?>				
</BODY>
</HTML>
	