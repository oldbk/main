<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");
$ust=(int)($_POST['target']);

if ($user['battle']>0) 
{ 
	echo "Персонаж находится в поединке!"; 
} elseif ($ust==$user['smagic'] )
{
	echo "У Вас уже установлена такая стихия! "; 
} elseif (($ust>=1) and ($ust<=4) )
{
		$st[1]='Огня';
		$st[2]='Земли';
		$st[3]='Воздуха';
		$st[4]='Воды';
		
				$abil[1]=5007152; //арес  1; // Огонь
				$abil[2]=5007154; //Подлый удар wrath_ground   2  Земля 
				$abil[3]=5007153; //Потрясение/ wrath_air 3; //Воздух (Весы, Водоле
				$abil[4]=5007155; //Отравление ядом	wrath_water  4; //Вода 
		
	mysql_query("UPDATE `users` SET `smagic`='{$ust}' WHERE `id`='{$user['id']}' ");
	if (mysql_affected_rows()>0)
		{
			if (($user['prem']==3) )
				{
				//платина
					//переводим абилки
					$get_mag_abil=mysql_query("select * from  `oldbk`.`users_abils`  where `owner`='{$user['id']}' and `magic_id` in (5007152,5007154,5007153,5007155) ");
					while ($row = mysql_fetch_array($get_mag_abil)) 
					{
						$new_row['allcount']+=$row['allcount'];
						$new_row['dailyc']+=$row['dailyc'];						
						$new_row['daily']+=$row['daily'];												
					}
					//print_r($new_row);
					//удаляем все типы
					mysql_query("DELETE from `oldbk`.`users_abils`  where `owner`='{$user['id']}' and `magic_id` in (5007152,5007154,5007153,5007155) ");
			
					//вставляем новый тип  и сумму переменных
					mysql_query("INSERT INTO `oldbk`.`users_abils` SET `owner`='{$user[id]}',`magic_id`='{$abil[$ust]}', `allcount`='{$new_row['allcount']}' ,`dailyc`='{$new_row['dailyc']}',`daily`='{$new_row['daily']}';");
					
					
				}
			
			echo "Все прошло удачно! Вы установили стихию <b>".$st[$ust]."</b>! ";
			$bet=1;
			$sbet = 1;
		}
}
?>