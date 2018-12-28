<?
//error_reporting(E_ALL); 
//ini_set('display_errors','On');
		session_start();
		if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}
		include ("connect.php");
		include "functions.php";
		if (($user['battle']>0) OR ($user['battle_fin'] >0))  { header("Location: fbattle.php"); die(); }
		if ($user['room'] != 300) { header("Location: main.php"); die(); }

		//$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND ((`type` >=11 AND `type` <= 14))  ;"));
		$myeff = getalleff($user['id']);
		
		if ($user[align]==4)
		{
			$begin_error="<font color=red>Хаос не ходит в ристалище...<br></font>";
		}		
		elseif ($user[level]<8)
		{
		 	$begin_error='<font color=red>Вы не можете принять участие в турнире, уровень маловат!</font><br>';
		 }
		elseif($myeff['owntravma']>=1)
		{
			$begin_error="<font color=red>С вашей травмой нельзя драться....</font>";
		}
		
		if (($_GET['exit']) and  ($user[battle]==0))
		{
			mysql_query("UPDATE `users` SET `users`.`room` = '200' WHERE  `users`.`id`  = '{$user[id]}' ;");
			header('location: city.php?strah=1&tmp='.mt_rand(1111,9999));
			die();
		}

///////////////////////////////////////////////////////////////////////////////
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
<style>
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }

    body {
			background-image: url('http://capitalcity.oldbk.com/i/restal/r210_1.jpg');
			background-repeat: no-repeat;
			background-position: top right;
	   }
</style>
<SCRIPT LANGUAGE="JavaScript">
function solo(n)
{

		<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>changeroom=n;
		window.location.href='restal300.php?got=1&level'+n+'=1';

}

function imover(im)
{
	im.filters.Glow.Enabled=true;
//	im.style.visibility="hidden";
}

function imout(im)
{
	im.filters.Glow.Enabled=false;
//	im.style.visibility="visible";
}

		function returned2(s){
			location.href='restal300.php?'+s+'tmp='+Math.random();
		}


function Down() {<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>CtrlPress = window.event.ctrlKey}

	document.onmousedown = Down;




function refreshPeriodic()
			{
			location.href='restal300.php?onlvl=<?=$onlvl;?>';//reload();
			timerID=setTimeout("refreshPeriodic()",30000);
			}
			timerID=setTimeout("refreshPeriodic()",30000);

</SCRIPT>
</HEAD>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0>

 <?

	echo '
	<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
	<TR valign=top>
	<TD width=3% align=center>&nbsp;</TD>
	<TD >
	<div align=center><h3>Бои с пойманными монстрами</h3></div>';

	if ($begin_error) echo $begin_error."<br>";

	echo "<div align=left><P>&nbsp;<H4>Текущие бои</H4></div>";
	
	$get_turs=mysql_query("select * from battle_hist_rist300  where win=3 order by fin_time DESC");
	 if (mysql_num_rows($get_turs)  > 0)	
	 	{
	 		while ($row = mysql_fetch_array($get_turs))
	 			{
				echo "<FONT class=date>".($row['start_time'])."</FONT> Бой <b>«".BNewRender($row['owner_hist']);
				if ($row['win'] == 1) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }				
				echo " против ".BNewRender($row['bot_hist']);
				if ($row['win'] == 2) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }				
				echo " » </b>";
				echo " ,  лог боя <a href=/logs.php?log={$row['battle_id']} target=\"_blank\"> »» </a><br>";
	 			}
	 		
	 	}
	 	else
	 	{
	 	echo "<b>Пока нет текущих боев... стань первым!</b>";
	 	}
	
	echo "<div align=left><P>&nbsp;<H4>История</H4></div>";
	
	$get_turs=mysql_query("select * from battle_hist_rist300 where win!=3 order by fin_time DESC limit 20");
	 if (mysql_num_rows($get_turs)  > 0)	
	 	{
	 		while ($row = mysql_fetch_array($get_turs))
	 			{
				echo "<FONT class=date>".($row['start_time'])."</FONT> Бой <b>«".BNewRender($row['owner_hist']);
				if ($row['win'] == 1) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }				
				echo " против ".BNewRender($row['bot_hist']);
				if ($row['win'] == 2) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }				
				echo " » </b>";
				echo " ,  лог боя <a href=/logs.php?log={$row['battle_id']} target=\"_blank\"> »» </a><br>";
	 			}
	 		
	 	}
	 	else
	 	{
	 	echo "<b>Пока нет истории... стань первым!</b>";
	 	}
	
		

 
?>
 </TD><TD align=right ><br><br>
     <div align=right>
         <form method=GET>
             <div class="btn-control">
                 <INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/r300.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
                 <input class="button-mid btn" type=button value='Обновить' onClick="returned2('refresh=<?= mt_rand(1111,9999) ?>&');">
                 <INPUT class="button-mid btn" TYPE=button value="Вернуться" onClick="returned2('exit=1');"><br>
             </div>
         </form>
     </div>
 </TD><TD align=center>&nbsp;</TD></TR><TR><TD align=center colspan=2>	</TD></TR></table>
</body></html>