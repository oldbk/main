<?
session_start();
	session_start();
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";
	if (($user['align']!=2.3) and ($user['align']!=2.4) and ($user['align']!=2.41) and ($user['id']!=66432) and ($user['align']!=2.7))  die('Страница не найдена :)');


include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
if($_POST[id])
{
     //if($_POST[owner]=='' && $_POST[klan]=='')
     //{
     //	$_POST[razdel]=71;
     //}

 $goden=0;
 if($_POST[razdel]==71 || $_POST[razdel]==73)
 {
 	$goden=180;
 }
 if($_POST[razdel]==7 ||$_POST[razdel]==77)
 {
 	$goden=90;
 }
	 mysql_query("INSERT INTO oldbk.eshop
(id,name,maxdur,cost,ecost,img,count,avacount,type,massa,razdel,owner,klan,isrep,ekr_flag,goden)
VALUES
('".$_POST[id]."','".$_POST[name]."','1','".$_POST[cost]."','".$_POST[ecost]."','".$_POST[img]."','".$_POST['count']."','".$_POST['avacount']."','200','0.1','".$_POST[razdel]."','".$_POST[owner]."','".$_POST[klan]."','0','0','".$goden."');");

   /*
	mysql_query("INSERT INTO avalon.eshop
	(id,name,maxdur,cost,ecost,img,count,type,massa,razdel,owner,klan,isrep,goden)
	VALUES
	('".$_POST[id]."','".$_POST[name]."','1','".$_POST[cost]."','".$_POST[ecost]."','".$_POST[img]."','".$_POST['count']."','200','0.1','".$_POST[razdel]."','".$_POST[owner]."','".$_POST[klan]."','0','".$goden."');");
     */
     echo mysql_error();
}

$item = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` >='55600000' AND `id` <'55700000' ORDER BY id DESC LIMIT 1;"));
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
		<input type="text" name="name" value="">
	</td>
</tr>
<tr>
	<td>
		картинка
	</td>
	<td>
		<input type="text" name="img" value="gift_cap_1sept01.gif">
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
		екр цена
	</td>
	<td>
		<input type="text" name="ecost" value="0">
	</td>
</tr>
<tr>
	<td>
		количество
	</td>
	<td>
		Кеп <input type="text" name="count" value="99999"> Авалон <input type="text" name="avacount" value="0">
	</td>
</tr>
<tr>
	<td>
		Раздел (72 - Уник, 71,73 обычный, 7,77 открытки, 76 сезонные подарки )
	</td>
	<td>
		<input type="text" name="razdel" value="73">
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
