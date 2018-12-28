<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$targ=($_POST['target']);
$baff_name='Сострадание';
$baff_type=846;//847

if ($user['battle'] != 0) 
{	
	echo "Это не боевая магия..."; 
}
else {

$HH=(int)(date("H",time()));
if (($HH>=9) and ($HH<21))
	{
	//echo "День";
	$get_imun_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and type='847' ; "));
		if (($get_imun_baff[id] > 0) )
			{
			err("Вы сможете использовать это заклятие  через ".floor(($get_imun_baff['time']-time())/60/60)." ч. ".round((($get_imun_baff['time']-time())/60)-(floor(($get_imun_baff['time']-time())/3600)*60))." мин.");
			}
		else
		{
		$us = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));
		
		if ($us[id]==$user[id])
			{
			err('Нельзя использовать на себя!');
			}
 		elseif ($us['battle'] > 0) 
 			{
			err("Персонаж в бою...");
			}
		elseif ($user['room'] != $us['room']) 
			{
			err("Персонаж в другой комнате!");
			}
		elseif ($user['id_city'] != $us['id_city']) 
			{
			err("Персонаж в другом городе!");
			}	
		elseif ($us['ldate'] < (time()-60)) 
			{
			err("Персонаж не в игре!");
			}
		else
			{
			$travma = mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$us['id']."' AND (`type`='11' OR `type`='12' OR `type`='13' );");
				if (!$travma) 
				{
				err("У персонажа нет травм...");
				}
				else			
				{
				
				while ($owntravma=mysql_fetch_array($travma)) 
					{
					if ($owntravma['time']-15*60<=time())
						{
						deltravma($owntravma['id']);
						$good=1;
						}
					}
				
					if ($good==1)
					{
					mysql_query("INSERT INTO `effects` SET `type`='847',`name`='Задержка на использование заклятия Сострадание',`time`='".(time()+1*60*60)."',`owner`='{$user[id]}' ;");//1 часов задержка
					
					//ставим травму
					mysql_query("UPDATE `users` SET  `sila`=`sila`-'50' WHERE `id` = '".$user[id]."' LIMIT 1;");
					mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`sila`,`lovk`,`inta`,`vinos`) values ('".$user[id]."','Сострадание',".(time()+2*60).",14,'50','0','0','0');");
					
					 $mag_gif='<img src=i/magic/6n1.jpg>';
					 if(($user['hidden'] > 0) and ($user['hiddenlog'] ==''))
					 {
					 $fuser['login']='<i>Невидимка</i>';
					 $sexi='использовал';
					 }
					 else
				 	{
					 $fuser=load_perevopl($user); //проверка и загрузка перевопла если надо
					 if ($fuser['sex'] == 1) {$sexi='использовал';  }	else { $sexi='использовала';}
					 }
					addch($mag_gif." <B>{$fuser['login']}</B> использовал магию &quot;".$baff_name."&quot;",$user['room'],$user['id_city']);
					$bet=1;
					$sbet = 1;
					echo "Все прошло удачно!";
					$MAGIC_OK=1;
					}
					else
					{
					err('У персонажа травмы осталось более чем на 15 минут');
					}
				}
			}
			
		}

	}
	else
	{
	err('Можно использовать только днем!');
	}
} 




?>
