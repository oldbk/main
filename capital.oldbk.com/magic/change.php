<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Дополнительная смена противнака';
$baff_type=796;

	//. проверить нету  ли на мне уже такого бафа
$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='{$baff_type}' ; "));
if ($get_test_baff[id] > 0) {

	err('На Вас уже есть этот эффект!');
}
elseif (($user['battle'] > 0) and ($magic['us_type']==2)) {
	err('Можно использовать только вне боя!');
}
elseif ($user['in_tower'] != 0) {
	err('Здесь это не работает!');
} else {
	$magictime=1999999999; //боевой эффект

	$prc[796]=3;  //+3 cмены

	$prc_add=$prc[$rowm['prototype']];

	
	
	if ($prc_add>0)
	{
		mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}', add_info='{$rowm['img']}:{$prc_add}:{$prc_mf}' ,`name`='{$baff_name}',`time`='{$magictime}',`owner`='{$user[id]}';"); 
		if (mysql_affected_rows()>0) 
		{
			$bet=1;
			$sbet = 1;
			echo "Использовано успешно! Получен эффект «{$baff_name}».<br>";
			$MAGIC_OK=1;
		}
	}
	else
		{
		echo "Ошибка свитка!";
		}
} 
	



?>
