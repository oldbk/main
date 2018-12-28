<?
//помойка
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { header('location: fbattle.php'); die(); }
	if ($user['room'] != 44)  { header('Location: main.php'); die(); }	
	
	if ($_GET[attack])
		{
		$test_room=true;
		//$CP_ATTACK2=true;
    			include "magic/attack.php";
		}
	elseif ($_GET['goto']=='plo')
		{
			if ((ADMIN) OR($user['klan']=='testTest'))
			{
			move_to_trup(20);
			mysql_query("UPDATE `users`  SET `users`.`room` = '20' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: city.php'); die();
			}
			else
			{
			
			}
		}
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script>

function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=15 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><td colspan=2><INPUT TYPE=hidden name=sd4 value="6">'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 1000;
	el.style.top = 75;
	document.getElementById(name).focus();
	Hint3Name = name;
}

function closehint3(clearstored){
	if(clearstored)
	{
		var targetform = document.getElementById('formtarget');
		targetform.action += "&clearstored=1";
		targetform.submit();
	}
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}
		
	</script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#E5E1E2 style="background-image: url('http://i.oldbk.com/i/city/room_test.jpg'); background-repeat:no-repeat; background-position: top center">
<div style='color:#8F0000; font-weight:bold; font-size:16px; text-align:center; float:right;'>Тестовая комната
<br>
<br>
<br>
	<?
	if ($user['klan']=='testTest')
	{
	}
	else
	{
	?>
<a href='edit_item.php'>Редактор предметов</a>
<br>
<a href='timeout.php' target=_blank>Админка боев</a>
<br>
<a href='admin_shop.php' >Admin Shop</a>
<br>
<a href='pers_null_stats_new.php' target=_blank>Сброс статов персонажу</a>
<br>
	<?
	}
	?>
<div align=right  style='font-weight:bold; font-size:10px; text-align:center; float:right;'>
<br>

Для тестовых боев используйте кнопку <br>
<?
echo '<div id=hint3 class=ahint></div>';
echo '<INPUT TYPE="button" value="Напасть" style="background-color:red" onclick="findlogin(\'Введите имя персонажа\', \'roomtest.php?attack=1\', \'target\');" >';
echo '<br><INPUT TYPE="button" value="Карта миров" onClick="location.href=\'main.php?setch=0.467837356797105\';" align="right"> ';

			if ((ADMIN) OR($user['klan']=='testTest'))
			{ 
			echo ' <INPUT TYPE="button" value="Выйти на Центральную площадь" onClick="location.href=\'roomtest.php?goto=plo\';" align="right"> '; 
			}


//echo "<br><font color=red>Хулиганы и бандиты сегодня беспредельничают на улицах города! Будьте осторожны!&nbsp;&nbsp;</font><br>";
//echo '<INPUT TYPE="button" value="Напасть" style="background-color:red" onclick="findlogin(\'Введите имя персонажа\', \'roomtest.php?attack=1\', \'target\');" >';
?>
</div>
</div>


</BODY>

</HTML>

