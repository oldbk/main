<?
session_start();
include "connect.php";
if (!ADMIN) { die(); }
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/rocketscript" data-rocketsrc="/i/globaljs.js"></script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=e2e0e0>
<H3 style="margin-bottom: 0px;"> Админка боев </H3>
<?




	if (isset($_POST['battleid']) and isset($_POST['t']))
	{
	$tim=(int)($_POST['t']);
	$bat=(int)($_POST['battleid']);
		if ($bat>0)
		{
			mysql_query("UPDATE `oldbk`.`battle` SET `timeout`='{$tim}'  WHERE `id`='{$bat}' limit 1");
			if (mysql_affected_rows()>0)
			{
			echo "Готово <br>";
			echo "Для боя {$bat}  timeout = {$tim} <br> ";					
			}
		}
	}
	else
	if (isset($_POST['battleid']) and (isset($_POST['typ']) or  isset($_POST['coment']) ) )
	{
	$ty=(int)($_POST['typ']);
	$bat=(int)($_POST['battleid']);
	$comm='';
	if ($bat>0)
	 {
	 
	 if ($_POST['coment']=='#zlevels')
	 	{
	 	$_POST['coment']='<b>#zlevels</b>';
	 	}
	 
		if  ((isset($_POST['coment'])) and ($_POST['coment']!=''))
		{
		$comm=" , `coment`='".mysql_real_escape_string($_POST['coment'])."' " ;
		}
	
		mysql_query("UPDATE `oldbk`.`battle` SET `type`='{$ty}'  ".$comm."  WHERE `id`='{$bat}' limit 1");
		if (mysql_affected_rows()>0)
		{
			echo "Готово. <br>";
			echo "Для боя {$bat}  тип {$ty} {$comm} <br>";		
		}
	 }
	}

echo "<br><hr><form method=post>";
echo "Тайм <input type=text name=t value=10>";
echo "Ид боя<input type=text name=battleid value=0>";
echo "<input type=submit  value=отправить>";
echo "</form> <br><br>";

echo "<br><hr><form method=post>";
echo "Тип боя <input type=text name=typ value=3>";
echo "Комент боя <input type=text name=coment value=''>";
echo "Ид боя<input type=text name=battleid value=0>";
echo "<input type=submit  value=Установить>";
echo "</form>";


?>
</body>
</html>
