<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Неукротимая ярость';
$baff_type=440;

	//. проверить нету  ли на мне уже такого бафа
$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='{$baff_type}' ; "));
if ($get_test_baff[id] > 0) {
	err('На Вас уже есть это заклятие!');
}
elseif (($user['battle'] > 0) and ($magic['us_type']==2)) {
	err('Можно использовать только вне боя!');
}
elseif ($user['in_tower'] != 0) {
	err('Здесь это не работает!');
} else {
	$magictime=time()+($magic['time']*60);
	
	$prc_a[200440]=0.1; //10%
	$prc_b[200440]=0.05; //5%	

	
	$prc_a=$prc_a[$rowm['prototype']];
	$prc_b=$prc_b[$rowm['prototype']];	
	
	if (($prc_a>0) and ($prc_b>0))
	{
		
		mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',  add_info='{$rowm['img']}:{$prc_a}:{$prc_b}' ,`name`='{$baff_name}',`time`='{$magictime}',`owner`='{$user[id]}';");
		if (mysql_affected_rows()>0) {
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
