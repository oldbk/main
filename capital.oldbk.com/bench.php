<?
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";

if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { header('location: fbattle.php'); die(); }

if ( ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') and ($user[room]==51))
{
	$gogo='strah';
}
else
	if ( ($_SERVER["SERVER_NAME"]=='angelscity.oldbk.com') and (($user[room]==51) OR ($user[room]==52) OR ($user[room]==53))  )
	{
		$gogo='strah';
	}
    elseif ( ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') and ($user[room]==52))
	{
		$gogo='zp';
	}
	else
	{ $gogo='bps'; }

?>
<HTML><HEAD>
    <link rel=stylesheet type="text/css" href="i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
    <meta content="text/html; charset=windows-1251" http-equiv=Content-type>
    <META Http-Equiv=Cache-Control Content=no-cache>
    <meta http-equiv=PRAGMA content=NO-CACHE>
    <META Http-Equiv=Expires Content=0>
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
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#E5E1E2 style="background-image: url('http://i.oldbk.com/i/bench1.jpg'); background-repeat:no-repeat; background-position: top right">
<div style='color:#8F0000; font-weight:bold; font-size:16px; text-align:center; float:center;'>Скамейка</div><div style='float:right; padding-right:6px;'>
    <form>
        <div class="btn-control">
            <INPUT class="button-mid btn" TYPE=button value="Вернуться" onClick="returned2('<?=$gogo;?>=1&');">
        </div>
    </form>
</div>
<div style='clear:both;'></div>
<br>
<b><i>Если вы устали от шума боев и суеты центральных улиц, вы можете отдохнуть на скамейке под тенью <br/>парковых деревьев...<br></b></i><br><br>
Здесь можно, уединившись, подумать о вечном, назначить романтическое свидание или просто спокойно поболтать.
<br/>Если только местные хулиганы не помешают вам...

</BODY>

</HTML>

