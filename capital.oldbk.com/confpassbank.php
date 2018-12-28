<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<LINK href="http://capitalcity.oldbk.com/i/main.css" type=text/css rel=stylesheet>
		<META http-equiv=Content-type content="text/html; charset=windows-1251">
	</head>
	<title>Востановление пароля на Банковский счет OldBK.com</title>
	<body bottomMargin=0 vLink=#333333 aLink=#000000 link=#000000 bgColor=#666666 leftMargin=0 topMargin=0 rightMargin=0 marginheight="0" marignwidth="0">
		<div style='background-color:#636462; width:13%; float:left;'>&nbsp;</div>
		<div style='float:left; text-align:justify; width:933px; FONT-SIZE: 10pt; FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif; background-color:#F2E5B1; widh:100%;'>

		<table style='font-size:12px; border:0px; margin:0px; padding:0px;' cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td width=124px;><img src='http://i.oldbk.com/i/pict_anketa.jpg' width=126 height=243 /><td width=100% valign=top>
					<br>
		<?php
			$realtime=mktime(date(H), date(i), date(s), date("m")  , date("d"), date("Y"));
			
			$_GET['id']=(int)($_GET['id']);
			

			if ($_GET['newpass']!='' && $_GET['id']!='' && $_GET['timev']!='' && $realtime<=$_GET['timev']) {
				include ("connect.php");
			$_GET['newpass']=mysql_escape_string($_GET['newpass']);
			$_GET['timev']=mysql_escape_string($_GET['timev']);
	


				$sql=mysql_query("select * from confirmpasswd_bank where owner='".$_GET['id']."' and passwd='".$_GET['newpass']."' and date='".$_GET['timev']."' and active=1") or die("Ошибка обработки запроса.");
				if (mysql_num_rows($sql)==0 or mysql_num_rows($sql)=='') die("Ссылка устарела!!");
				$sql=mysql_fetch_array($sql,MYSQL_ASSOC) or die("Ошибка обработки запроса!!");
				mysql_query("update bank set pass='".md5($_GET['newpass'])."' where id='".$sql['bank']."' and owner='".$sql['owner']."'") or die("Ошибка обработки запроса!");
				echo "<center>Пароль изменен. Не забывайте пароль.<br></center>";
				@mysql_query("update confirmpasswd_bank set active=0 where owner='".$sql['owner']."' and bank='".$sql['bank']."' and passwd='".$_GET['newpass']."' and date='".$_GET['timev']."' and active=1");
			}
			else echo "Ссылка устарела.";
		?>
		</td>
		<td width=107 align=right>
			<img src='http://i.oldbk.com/i/paper1.jpg' width=39 height=292 />
		</table>
		<div style='float:left; margin-left:-87px;'></div>
		</div>
		<div style='clear:both'></div><br>
		</table>
	</body>
</html>