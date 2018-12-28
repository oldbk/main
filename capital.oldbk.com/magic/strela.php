<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$targ=($_POST['target']);
$baff_name='«Легкая влюбленность»';
$baff_type=2025;

if ($targ=='')
{
err('Персонаж  не найден!');
}
else
if ($user['battle'] > 0) 
{	
	echo "Нельзя использовать в бою..."; 
}
else {
	
	$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$targ}'   LIMIT 1;"));
	if (($jert['id'] ==$user['id']) ) // нельзя юзать на себя в этих типах
	{
	err('Нельзя использовать на себя :(');
	}
	else if (($jert['id'] >0) AND  ($jert['id_city']!=$user['id_city']) )
	{
	err('Персонаж в другом городе...');
	}
	else if (($jert['id'] >0) AND  ($jert['room']!=$user['room']) )
	{
	err('Персонаж в другой комнате...');
	}
	elseif ($jert['id'] >0)
			{
			$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert['id']}' and (type='{$baff_type}') ; "));
			if (($get_test_baff[id] > 0) )
			{
				err('На персонаже уже есть эта магия!');
			}
			else
			 {
			 $magictime=time()+3*60*60;
			 				mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}', `time`='".$magictime."', `owner`='{$jert[id]}'  ;");
							if (mysql_affected_rows()>0)
							{
							addchp('<font color=red>Внимание! Внимание! Персонаж '.$user['login'].' пронзил вас стрелой амура! Получен эффект «Легкая влюбленность».</font>  ','{[]}'.$jert['login'].'{[]}',-1,$jert['id_city']);
							$bet=1;
							$sbet = 1;
							echo "Все прошло удачно!";
							$MAGIC_OK=1;
						 	}
						}
			}
			else
			     {
			     err('Персонаж  "'.$targ.'"  не найден!');
			     }
	
}



	



?>
