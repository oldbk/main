<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Бонус на все мф 1%';
$baff_type=793;

	//. проверить нету  ли на мне уже такого бафа
$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='{$baff_type}' ; "));
if ($get_test_baff[id] > 0) {
	err('На Вас уже есть это заклятие!');
} elseif ($user['in_tower'] == 16) {
	err('Здесь это не работает!');
} else {
	mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$user[id]}';");
	if (mysql_affected_rows()>0) {
		$bet=1;
		$sbet = 1;
		echo "Все прошло удачно!";
		$MAGIC_OK=1;
	}
} 
	



?>
