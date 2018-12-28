<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$deny_rooms=array(197,198,199,211,212,213,214,215,216,217,218,219,220,221,222,271,272,273,274,275,276,277,278,279,280,281,282);

$baff_name='Ледяной Интеллект';
$baff_type=826;//827-задержка

if ($user['battle'] != 0) 
{	
	echo "Это не боевая магия..."; 
}
elseif ($user['in_tower'] != 0) 
{	
	echo "Не подходящее место..."; 
}
elseif (in_array($user[room],$deny_rooms))
{	
	echo "Не подходящее место..."; 
}
else {
	$get_imun_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and type='827' ; "));
		if (($get_imun_baff[id] > 0) )
			{
			err("Вы сможете использовать это заклятие  через ".floor(($get_imun_baff['time']-time())/60/60)." ч. ".round((($get_imun_baff['time']-time())/60)-(floor(($get_imun_baff['time']-time())/3600)*60))." мин.");
			}
		else
		{
		$intel_add=$user[level];
		
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`='".(time()+(30*60))."', `intel`='{$intel_add}' ,`owner`='{$user[id]}' ;");
			if (mysql_affected_rows()>0)
				{
			mysql_query("UPDATE `users` SET `intel`=`intel`+'{$intel_add}' where `id`='{$user['id']}';");
			mysql_query("INSERT INTO `effects` SET `type`='827',`name`='Задержка на использование заклятия Ледяной Интеллект',`time`='".(time()+6*60*60)."',`owner`='{$user[id]}' ;");//6часов задержка
		
			 $mag_gif='<img src=i/magic/2n1.jpg>';
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
				echo "Все прошло удачно! Интеллект +{$intel_add}!";
				$MAGIC_OK=1;
				}
			
		}


	} 
	



?>
