<?php
	$mlglobal = 1;
	require_once('mlglobal.php');

	if (isset($_GET['quest'])) {
		if ($_GET['quest'] == 1) {
			// драка с рудничем
			// 534
			mysql_query('START TRANSACTION') or QuestDie();
			StartQuestBattle($user,534) or QuestDie();
			mysql_query('COMMIT') or QuestDie();
			Redirect('fbattle.php');
		}
		if ($_GET['quest'] == 2) {
			// драка с крысами
			// 532 - крыса
			mysql_query('START TRANSACTION') or QuestDie();
			StartQuestBattle($user,532) or QuestDie();
			mysql_query('COMMIT') or QuestDie();
			Redirect('fbattle.php');
		}
	}	

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
<body id="body" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor="#d7d7d7" onResize="return; ImgFix(this);">
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
<TR>
	<TD align=center></TD>
	<TD align=right>
		<div class="btn-control">
            <input class="button-mid btn" type="button" style="cursor: pointer;" name="Обновить" value="Обновить" OnClick="location.href='?'+Math.random();">
            <!--INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/mlmine.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"-->
            <input class="button-mid btn" type="button" name="Выход" value="Выход" OnClick="location.href='?exit=1'">
        </div>
	</TD></TR>
	<TR><TD align=center colspan=2>

<table width=1><tr><td>

<div id="maindiv" style="position:relative;"><img src="http://i.oldbk.com/i/map/mlmine_bg.jpg" id="mainbg">
<a href="?quest=1"><img style="z-index:3; position: absolute; left: 275px; top: 65px;" src="http://i.oldbk.com/i/map/mlmine_pers1.png" alt="Рудокоп" title="Рудокоп" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlmine_pers1_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlmine_pers1.png'"/></a>
<a href="?quest=2"><img style="z-index:3; position: absolute; left: 175px; top: 215px;" src="http://i.oldbk.com/i/map/mlmine_pers2.png" alt="Рудокоп" title="Крысы" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/map/mlmine_pers2_h.png'" onmouseout="this.src='http://i.oldbk.com/i/map/mlmine_pers2.png'"/></a>
</div>


</td></tr></table>
 
</div>
</TD>
</TR>
</TABLE>

<?php
	require_once('mldown.php');
?>