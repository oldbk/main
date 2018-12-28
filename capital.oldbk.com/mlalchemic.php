<?php
	/*
	$mlglobal = 1;
	require_once('mlglobal.php');
	*/

	$mlglobal = 1;
	require_once('mlglobal.php');

	$mldiag = array();
	$mlquest = "540/162";

?>


<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>
var loc = parent.location.href.toString();
if (loc.indexOf("/map.php") != -1) {
	parent.location.href = "<?php echo $self; ?>";
}
</script>
<style> 
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }
</style>
<style type="text/css"> 
img, div { behavior: url(/i/city/ie/iepngfix.htc) }
</style>
</HEAD>
<body id="body" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor="#e2e0e1" onResize="return; ImgFix(this);">
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
<TR>
	<TD align=center></TD>
	<TD align=right>
		<div class="btn-control">
            <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlalchemic.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table border = 0 width=1><tr><td valign=top>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlalchemic_bg.jpg" id="mainbg">
<!--
<a href="?quest=1"><img style="z-index:3; position: absolute; left: 180px; top: 90px;" src="http://i.oldbk.com/i/map/mlalchemic_pers1.png" alt="Алхимик" title="Алхимик" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlalchemic_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlalchemic_pers1.png'"/></a>
-->
</div>
			<?php
			$mldiag = array(0 => 'Алхимик переехал в Мастерскую магов и алхимиков Кэпитал-сити');
			require_once('mlquest.php');
			/*
			if (isset($_GET['quest']) && empty($mldiag)) {
				$mldiag = array(
						0 => "Привет, привет, юноша...",
				);

				$mldiag[4] = "Пока!";
			}

			if(!empty($mldiag)) {
				require_once('mlquest.php');
			}
			*/
			?>
</td></tr></table>
 
</div>
</TD>
</TR>
</TABLE>

<?php
	require_once('mldown.php');
?>