<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$sub_name[3]='Темная';
$sub_name[2]='Нейтральная';
$sub_name[6]='Светлая';
$sub_name[0]='Серая';
$get_align=(int)($user[align]);
if ($get_align==1) {$get_align=6;}

$baff_name=$sub_name[$get_align].' медитация';
$baff_type=830;//831/832/833

$can_use_rooms=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,26,36,50,51,52,53,54,55,56,57,66);

if ($user['battle'] != 0) 
{	
	echo "Это не боевая магия..."; 
}
elseif (!(in_array($user[room],$can_use_rooms)))
{
err('Неподходящее место для медитации');
}
elseif($user[zayavka]>0)
{
err('Неподходящее время для медитации, Вы в заявке на бой!');
}
else {
	$get_imun_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and type='833' ; "));
		if (($get_imun_baff[id] > 0) )
			{
			err("Вы сможете использовать это заклятие  через ".floor(($get_imun_baff['time']-time())/60/60)." ч. ".round((($get_imun_baff['time']-time())/60)-(floor(($get_imun_baff['time']-time())/3600)*60))." мин.");
			}
		else
		{
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`='".(time()+3600)."',`owner`='{$user[id]}' ;");
			if (mysql_affected_rows()>0)
				{
			mysql_query("INSERT INTO `effects` SET `type`='833',`name`='Задержка на использование Медитации',`time`='".(time()+28800)."',`owner`='{$user[id]}' ;");//8 часов задержка
			
			 if ($get_align==0) 
			 {
			 mysql_query("update effects SET `time`=FLOOR(".time()."+((`time`-".time().")*0.5)) , eff_bonus=1 where (type=11 OR  type=12 OR type=13 OR type=14) and eff_bonus=0 and owner='{$user[id]}' ;");
			 }
			
			
			 $mag_gif='<img src=i/magic/'.$get_align.'n5.jpg>';
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
				echo "Все прошло удачно! Вы медитируете!";
				$MAGIC_OK=1;
				}
			
		}


	} 
	



?>
