<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Магическое исцеление';
$baff_type=795;

	//. проверить нету  ли на мне уже такого бафа
$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='{$baff_type}' ; "));
if ($get_test_baff[id] > 0) {
	err('На Вас уже есть это заклятие!');
} elseif ($user['in_tower'] == 16) {
	err('Здесь это не работает!');
} else {

	$ubatuse=ture;
	
	if ($user['battle'] > 0)
	{
	$test_baff= mysql_fetch_array(mysql_query("select * from battle_vars  where owner='{$user[id]}' and battle='{$user[battle]}' ; "));
	 if ($test_baff[baf_795_use] >0) 
	 		{
 			err('Можно использовать только 1 раз в бою!');
 			$ubatuse=false;
			 }
	}


	if ($ubatuse==true)
	{
	if ($user['battle'] > 0)  {  $inbatt = " , battle={$user[battle]}   ";    }  	else { $inbatt=''; }
	mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999, `lastup`=20 ".$inbatt." ,  `owner`='{$user[id]}';"); //20 ходов
	if (mysql_affected_rows()>0) 
	{
		$bet=1;
		$sbet = 1;
		echo "Все прошло удачно!";
		$MAGIC_OK=1;
	}
	}
} 
	



?>
