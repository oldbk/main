<?
	session_start();
	$google = 1;
	include "connect.php";
	include "functions.php";
	if (($user['klan']!='radminion') and ($user['klan']!='Adminion') ) { die('Ошибка!');}

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
?>
<HTML>
<HEAD>
<TITLE>Установить себе уровень</TITLE>
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
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 ><br>
<br>
<br>
Установить себе уровень<br>
<form method=POST name='actform'>
Установить уровень:<input type=text name='setlevel' value="<?=$user['level'];?>" size=7> 
<input type="submit" value="Выполнить">
</form>

<?
	echo "<font color='red'><b>";	
	if ($_POST['setlevel']!="") 
	{
		$slvl=(int)$_POST['setlevel'];
		mysql_query("INSERT INTO `oldbk`.`admin_log` SET `dtype`='Установил себе уровень',`kto`='{$user['login']}',`komu`='{$user['login']}',`val`='{$slvl}';");
		if (mysql_affected_rows()>0)
			{
			mysql_query("UPDATE `oldbk`.`users` SET `level`='{$slvl}' WHERE `id`='{$user['id']}' LIMIT 1;");
					if (mysql_affected_rows()>0)
						{
							echo "Вам установленн ".$slvl."-й уровень.";
						}
			}
	}
?>
</b></font><p>
</body>
</html>