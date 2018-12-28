<?
	session_start();
	$google = 1;
	include "connect.php";
	include "functions.php";
	if (($user['klan']!='radminion') and ($user['klan']!='Adminion') ) { die('Ошибка!');}

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
?>
<HTML><HEAD><TITLE>Админ.  Бойцовского клуба </TITLE>
<META content=INDEX,FOLLOW name=robots>
<META http-equiv=Content-type content="text/html; charset=windows-1251">
<META http-equiv=Pragma content=no-cache>
<META http-equiv=Cache-control content=private>
<META http-equiv=Expires content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<LINK href="i/main.css" type=text/css rel=stylesheet>
</HEAD>
<style>
.pleft {
	PADDING-RIGHT: 0px; PADDING-LEFT: 20px; PADDING-BOTTOM: 7px; MARGIN: 0px; PADDING-TOP: 3px
}
</style>
<br>
<br>
Отправить системное сообщение в чат:<br>
<form method=POST name='actform'>
<input type=hidden name='action' id='action' value="sysmsg">
<input type="checkbox" name='tome'> Отправить только себе!<br>
<input type=text name='msg' id='msg' value="" size=200> 
<br> Уровневые системки:<br>
Min. lvl:<input type=text name='minl' id='minl' value=""><br> 
Max. lvl:<input type=text name='maxl' id='maxl' value=""> 
<input type="submit" value="Отправить">
</form>

<?
	echo "<font color='red'><b>";	
	if ($_POST['action']!="") 
	{

				if ((strlen($_POST['msg'])) and ($_POST['tome']) )
				{
				 addchp ('<img src="http://i.oldbk.com/i/klan/radminion.gif"> <b>'.mysql_real_escape_string($_POST['msg']).'</b>','{[]}'.$user['login'].'{[]}');
				echo "Отправлено системное сообщение себе  в приват.";
				}
				else
				if ((strlen($_POST['msg'])) and ((int)$_POST['minl']>0) and ((int)$_POST['maxl']>0) )
				{
					$minl=(int)$_POST['minl'];
					$maxl=(int)$_POST['maxl'];
					addch2levels('<img src="http://i.oldbk.com/i/klan/radminion.gif"> <b>'.mysql_real_escape_string($_POST['msg']).'</b>',$minl,$maxl);
					echo "Отправлено системное сообщение в чат для ".$minl." - ".$maxl." уровней ";
				}				
				else
				if (strlen($_POST['msg'])) 
				{
					addch2all('<img src="http://i.oldbk.com/i/klan/radminion.gif"> <b>'.mysql_real_escape_string($_POST['msg']).'</b>');
					echo "Отправлено системное сообщение в чат.";
				}


		
			
			
			
		}
?>
</b></font><p>
</body>
</html>