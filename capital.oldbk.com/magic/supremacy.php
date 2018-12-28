<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Полное превосходство';
$baff_type=441;

	//. проверить нету  ли на мне уже такого бафа
$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='{$baff_type}' ; "));
if ($get_test_baff[id] > 0) {

	err('На Вас уже есть это заклятие!');
}
elseif (($user['battle'] == 0) and ($magic['us_type']==1)) {
	err('Можно использовать только в бою!');
}
elseif ($user['in_tower'] != 0) {
	err('Здесь это не работает!');
} else {
	$magictime=1999999999; //боевой эффект

	$prc_hp[200441]=500; 
	$prc_mf[200441]=250; 	

	$prc_hp=$prc_hp[$rowm['prototype']];
	$prc_mf=$prc_mf[$rowm['prototype']];
	
	
	if (($prc_hp>0) and ($prc_mf>0))
	{
		mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}', battle='{$user['battle']}' , add_info='{$rowm['img']}:{$prc_hp}:{$prc_mf}' ,`name`='{$baff_name}',`time`='{$magictime}',`owner`='{$user[id]}';"); 
		if (mysql_affected_rows()>0) {
		mysql_query("UPDATE `users` SET `maxhp`=`maxhp`+'{$prc_hp}' ,`hp`=`hp`+'{$prc_hp}'  WHERE `id`='{$user['id']}'  ");
			$bet=1;
			$sbet = 1;
			echo "Использовано успешно! Получен эффект «{$baff_name}».<br>";
			$MAGIC_OK=1;

		  	//пишем в лог если надо
			if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
			elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
		
			addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.(($user[sex]*100000)+$rowm['prototype'] )."\n");  	
       	       			
		}
	}
	else
		{
		echo "Ошибка свитка!";
		}
} 
	



?>
