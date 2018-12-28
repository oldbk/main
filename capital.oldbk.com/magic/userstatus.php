<?php
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}

if ($user['battle'] > 0) {
	err("Не в бою...");
	return;
} 

$magic = magicinf($rowm['magic']);
$duration = time()+($magic['time']*60);


if (isset($_POST['target'])) {
	$_POST['target'] = substr(html_entity_decode($_POST['target'],ENT_QUOTES,"cp1251"),0,60);
}

if (!isset($_POST['target']) || !strlen($_POST['target'])) {
	err("Статус не должен быть пустым");
	return;
}

$get_pal_baff= mysql_fetch_array(mysql_query("select * from effects where owner = '{$user[id]}' and type=5; "));

if ($get_pal_baff['id']>0)
	{
	err("На Вас Заклятие обезличивания!");
	return;
	}
else
	{
		$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner = '{$user[id]}' and type=4201; "));
		if ($get_test_baff['id'] > 0) {
			mysql_query("UPDATE `effects` SET `time`='".$duration."' WHERE id = ".$get_test_baff['id']." LIMIT 1");
			if (mysql_affected_rows()>0) {                                       
				mysql_query('UPDATE users SET unikstatus = "'.mysql_real_escape_string($_POST['target']).'" WHERE id = '.$user['id'].' LIMIT 1');
				$bet=1;
				$sbet = 1;
				$MAGIC_OK=1;
				echo 'Использовано успешно! Обновлён эффект «Уникальный статус»!';
			}
		} else 
		{
			mysql_query("INSERT INTO `effects` SET `type`='4201',`name`='Уникальный статус', `time`='".$duration."', `owner`='{$user[id]}'");
			if (mysql_affected_rows()>0) {
				mysql_query('UPDATE users SET unikstatus = "'.mysql_real_escape_string($_POST['target']).'" WHERE id = '.$user['id'].' LIMIT 1');
				$bet=1;
				$sbet = 1;
				$MAGIC_OK=1;
				echo 'Использовано успешно! Получен эффект «Уникальный статус»!';
			}
		}
	}
?>