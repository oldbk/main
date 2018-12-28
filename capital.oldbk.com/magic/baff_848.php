<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


$baff_name='Фортуна';
$baff_type=848;//849

if ($user['battle'] != 0) 
{	
	echo "Это не боевая магия..."; 
}
else {
	$get_imun_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and type='849' ; "));
		if (($get_imun_baff[id] > 0) )
			{
			err("Вы сможете использовать это заклятие  через ".floor(($get_imun_baff['time']-time())/60/60)." ч. ".round((($get_imun_baff['time']-time())/60)-(floor(($get_imun_baff['time']-time())/3600)*60))." мин.");
			}
		else
		{
		
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`='".(time()+(60*60))."', `owner`='{$user[id]}' ;");
			if (mysql_affected_rows()>0)
				{
			mysql_query("INSERT INTO `effects` SET `type`='849',`name`='Задержка на использование заклятия Фортуна',`time`='".(time()+24*60*60)."',`owner`='{$user[id]}' ;");//24 часов задержка
		
			 $mag_gif='<img src=i/magic/2n3.jpg>';
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
			addch($mag_gif." <B>{$fuser['login']}</B> {$sexi} магию &quot;".$baff_name."&quot;",$user['room'],$user['id_city']);
				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			
		}


	} 
	



?>
