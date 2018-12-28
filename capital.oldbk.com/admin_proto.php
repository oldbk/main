<?
session_start();
/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<title>Поиск по названию или прототипу</title>
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<style>
	.row {
		cursor:pointer;
	}
</style>
<SCRIPT>
function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}
</SCRIPT>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 ><br>
<h2>Поиск по названию или прототипу</h2><br><br>
<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
	if (ADMIN)
	{
			
		echo "<form method=post>";
		echo "Поиск по прототипу <input type=text name='findproto' value='".(int)($_POST['findproto'])."'>";	echo "<input type=submit name='fbyproto' value='Найти'><br>";
		echo "</form>";
		
		echo "<form method=post>";
		echo "Поиск по названию<input type=text name=findname value='".mysql_real_escape_string($_POST['findname'])."'>";	echo "<input type=submit name='fbyname' value='Найти'><br>";
		echo "</form>";

		
		
		if (($_POST['upletter']) and ($_POST['findproto'] > 0)  )
			{
			
				$new_letter=mysql_real_escape_string($_POST['letter']);	
				$proto=(int)($_POST['findproto']);	
																				
					if ($_POST['allupdate'])
					{
						mysql_query("UPDATE `oldbk`.`shop` SET `letter`='{$new_letter}' WHERE `id`= '{$proto}' ");
						$k=mysql_affected_rows();
						if ($k>0) { echo "Обновили в shop. <br> "; }
						mysql_query("UPDATE `oldbk`.`eshop` SET `letter`='{$new_letter}' WHERE `id`= '{$proto}' ");
						$k=mysql_affected_rows();
						if ($k>0) { echo "Обновили в eshop. <br> "; }
						mysql_query("UPDATE `oldbk`.`cshop` SET `letter`='{$new_letter}' WHERE `id`= '{$proto}' ");
						$k=mysql_affected_rows();
						if ($k>0) { echo "Обновили в cshop. <br> "; }						
						mysql_query("UPDATE `oldbk`.`cshop` SET `letter`='{$new_letter}' WHERE `id`= '{$proto}' ");
						$k=mysql_affected_rows();
						if ($k>0) { echo "Обновили в cshop. <br> "; }						
						mysql_query("UPDATE `oldbk`.`inventory` SET `letter`='{$new_letter}' WHERE `prototype`= '{$proto}' ");
						$k=mysql_affected_rows();
						if ($k>0) { echo "Обновили в инвентарь: ".$k." шт. <br> "; }								
					}
					elseif (($_POST['magaz']>=1) and ($_POST['magaz']<=3))
					{

					$shops[1]='shop';
					$shops[2]='eshop';					
					$shops[3]='cshop';		
					$shop=$shops[$_POST['magaz']];
					
					mysql_query("UPDATE `oldbk`.`{$shop}` SET `letter`='{$new_letter}' WHERE `id`= '{$proto}' ");

						$k=mysql_affected_rows();
						if ($k>0) { echo "Обновили только в {$shop}. <br> "; }	else { }
					}
					else
					{
					echo "Ошибка";
					}
			}
		
		if (($_POST['fbyproto'])  or  ($_POST['fbyname']) )
				{

					$shops[1]=array('name'=>'Гос.Маг.', 'table'=> 'shop');
					$shops[2]=array('name'=>'Березка/Цветочка', 'table'=> 'eshop');
					$shops[3]=array('name'=>'Храм Лавка', 'table'=> 'cshop');
			
									if ($_POST['fbyproto'])
									{
									$proto=(int)($_POST['findproto']);									
									echo "<h5>Результаты поиска по прототипу: {$proto}</h5>";												
									$sql=" id='{$proto}'  ";
									}
									elseif ($_POST['fbyname'])
									{
									$name=mysql_real_escape_string($_POST['findname']);									
									echo "<h5>Результаты поиска по названию: {$name}</h5>";			
									$sql=" name like '%{$name}%'";
									}
			
					
					for ($i=1;$i<=3;$i++)
					{
						echo "<h4>Поиск {$shops[$i]['name']} </h4>";
						
						$data=mysql_query("select * from oldbk.{$shops[$i]['table']} where {$sql} ");

						$kol_f=mysql_num_rows($data);
						if ($kol_f >0)
							{
							echo "<table border=1  WIDTH=100% CELLSPACING='0' CELLPADDING='2' BGCOLOR='#A5A5A5'>";							
							while($row=mysql_fetch_array($data))
									{
									if ($col==0) { $col = 1; $color = '#C7C7C7';} else { $col = 0; $color = '#D5D5D5';}
									echo "<tr BGCOLOR={$color}>";
									echo "<td align=center><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><br>
									<small>{$row['cost']} кр. <br> {$row['ecost']} екр. <br> {$row['repcost']} реп. <br>
									(PROTO_ID:{$row['id']}) 
									<form method=post>
									Описание:<br>
									<input type=hidden name=fbyproto value='1'>									
									<input type=hidden name=findproto value='".$row['id']."'>
									<input type=hidden name=magaz value='".$i."'>									
									<textarea rows=\"5\" cols=\"40\" NAME=\"letter\">".$row['letter']."</textarea><br>
									<input type=checkbox name=allupdate><small>Обновить инвентарь и все магазины (может занять много времени на обработку)</small><br>									
									<INPUT TYPE=submit name=upletter value=\"Обновить описание\">
									</form>
									</small></td>";
									
									echo "<td>";
									if ($row[count]<=0) { $row[count]=1; }
									

									showitem ($row,0,false,$color,'',0, 0,0,'');
									echo "</td>";
									echo "</tr>";
									}
							echo "</table>";
							}
							else
							{
							echo "Прототип не найден!";
							}
					
					
					}
					
						
				
				}
				
		
	/*
 Создать инструмент на отдельной странице "Поиск по названию или прототипу" перевода айди прототипа в предмет и наоборот, пример:
- вводим айди прототипа 10002, жмем показать - открывает этот предмет с характеристиками, картинкой и ссылкой на библу или список предметов, если таковых несколько
- вводим название предмета или его часть, жмем показать - открывает предмет с айди, характеристиками, картинкой и ссылкой в библу или список предметов, если таковых несколько	
	*/
	
	
	}
	else
	{
	echo "Доступ закрыт!";
	}
?>	
</body>
</html>
<?
/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////
?>