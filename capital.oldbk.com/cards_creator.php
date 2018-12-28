<?
session_start();
//include "/www/kitezhgrad.oldbk.com/connect.php";
//include "/www/kitezhgrad.oldbk.com/functions.php";
include "/www/capitalcity.oldbk.com/connect.php";
include "/www/capitalcity.oldbk.com/functions.php";
$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

if (($user['align']!=2.4) and ($user['align']!=2.7))  die('Страница не найдена :)');

if($_POST[id])
{
     //if($_POST[owner]=='' && $_POST[klan]=='')
     //{
     //	$_POST[razdel]=71;
     //}


	 mysql_query("INSERT INTO oldbk.eshop (id,name,maxdur,cost,img,count,type,massa,razdel,owner,klan,isrep)
	 								VALUES
	 								('".$_POST[id]."','".$_POST[name]."','1','".$_POST[cost]."','".$_POST[img]."','".$_POST['count']."','200','1','".$_POST[razdel]."','".$_POST[owner]."','".$_POST[klan]."','0');");
	 
}

$item = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` >='10000' AND `id` <'20000' ORDER BY id DESC LIMIT 1;"));
if(!$item[id]){$item[id]=9999;}
$id=$item[id]+1;



//print_r($_POST);

?>
<HTML>
<head></head>
<body>

<h3> Создать подарок </h3>
<FORM METHOD="POST">
<table>
<input name="id" type="hidden" value="<?=$id?>">
<tr>
	<td>
		Название
	</td>
	<td>
		<input type="text" name="name" value="Открытка ">
	</td>
</tr>
<tr>
	<td>
		картинка
	</td>
	<td>
		<input type="text" name="img" value="card_001.gif">
	</td>
</tr>
<tr>
	<td>
		ID владельца
	</td>
	<td>
		<input type="text" name="owner" value="">
	</td>
</tr>
<tr>
	<td>
		название клана
	</td>
	<td>
		<input type="text" name="klan" value="">
	</td>
</tr>
<tr>
	<td>
		цена
	</td>
	<td>
		<input type="text" name="cost" value="1">
	</td>
</tr>
<tr>
	<td>
		количество
	</td>
	<td>
		<input type="text" name="count" value="9999">
	</td>
</tr>
<tr>
	<td>
		Раздел (72 - Уник, 71 обычный)
	</td>
	<td>
		<input type="text" name="razdel" value="7">
	</td>
</tr>




<tr>
	<td>
	</td>
	<td>
		<input type="submit" value="NEXT">
	</td>
</tr>
</table>

</FORM>
</body>
</HTML>
