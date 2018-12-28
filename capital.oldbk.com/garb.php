<?
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); } 
	include "connect.php";
	include "functions.php";
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { header('location: fbattle.php'); die(); }
	if ($user['room'] != 80)  { header('Location: main.php'); die(); }	
?>
<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<META Http-Equiv=Expires Content=0>
	<meta http-equiv=PRAGMA content=NO-CACHE>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>
		function returned2(s){
			//if (top.oldlocation != '') { top.frames['main'].navigate(top.oldlocation+'?'+s+'tmp='+Math.random()); top.oldlocation=''; }
			//else {
			//top.frames['main'].location='city.php?'+s+'tmp='+Math.random()
			//}
			location.href='city.php?'+s+'tmp='+Math.random();
		}
	</script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#E5E1E2 style="background-image: url('http://i.oldbk.com/i/city/oldbk_garb.jpg'); background-repeat:no-repeat; background-position: top center">
<div style='color:#8F0000; font-weight:bold; font-size:16px; text-align:center; float:center;'>Помойка</div><div style='float:right; padding-right:6px;'>


</BODY>

</HTML>

