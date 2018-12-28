<?php
$head = <<<HEADHEAD
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<style>
		div {display: inline;}
	</style>
	</HEAD>

	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>
 	<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0" bgcolor=#e2e0e0>
	<TR valign=top>
	<TD width=3% align=center>&nbsp;</TD>
	<TD width=100%><h3>Замковый турнир - лог</h3></div></TD><TD align=right nowrap>
	</div></TD>
	</TD></TR>
	<TR height=100%><td>&nbsp;</td><TD valign=top colspan=2><br><br>
HEADHEAD;

$bottom = <<<BOTTOM
	</TD></TR>
	</table>
BOTTOM;

	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	session_start();

	if (!isset($_GET['id'])) {
		if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	
	}

	require_once('connect.php');
	require_once('functions.php');

	echo $head;

	$q = mysql_query('SELECT * FROM castles_tur WHERE id = '.intval($_GET['id']));
	if (mysql_num_rows($q) > 0) {
		$l = mysql_fetch_assoc($q);
		echo $l['log'];
	}

	echo $bottom;	
?>