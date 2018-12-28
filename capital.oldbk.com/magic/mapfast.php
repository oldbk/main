<?php
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}

if ($user['battle'] > 0) {
	err("Не в бою...");
	return;
} 

$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner = '{$user[id]}' and type=4200; "));
if ($get_test_baff[id] > 0) {
	err('Вы уже использовали это заклятие, ожидайте его окончания!');
	return;
}

$magic = magicinf($rowm['magic']);

$duration = time()+($magic['time']*60);
		

mysql_query("INSERT INTO `effects` SET `type`='4200',`name`='Ускорение загорода', `time`='".$duration."', `owner`='{$user[id]}'");
if (mysql_affected_rows()>0) {

	// проверяем на карте ли чел и лидер ли команды и команды нет
	$q = mysql_query('START TRANSACTION'); 
	if ($q === FALSE) die();

	$q = mysql_query('SELECT * FROM map_groups WHERE leader = '.$user['id'].' and team = "" FOR UPDATE') or die();
	if (mysql_num_rows($q) > 0) {
		// юзер на карте
		$m = mysql_fetch_assoc($q);
		if ($m['status'] == 1) {
			// если группа двигается
			$newcost = 0;

			require_once('map_config.php');
			$map = unserialize($map);

			if ($outmap['horse']) {
				$map_cost = $map_costm1horse;
			} else {
				$map_cost = $map_costm1;
			}

			$m['path'] = unserialize($m['path']);
			while(list($k,$v) = each($m['path'])) {
				$ty = floor($v / 90);
				$tx = $v - ($ty*90);

				$newcost += $map_cost[$map[$ty][$tx]];;
			}

			$q = mysql_query('UPDATE map_groups SET magicfast = 1, cost = '.$newcost.' WHERE id = '.$m['id']) or die();
		} else {
			$q = mysql_query('UPDATE map_groups SET magicfast = 1 WHERE id = '.$m['id']) or die();
		}
		if ($q === FALSE) die();
	}

	$q = mysql_query('COMMIT'); 
	if ($q === FALSE) die();


	$bet=1;
	$sbet = 1;
	$MAGIC_OK=1;

	echo 'Использовано успешно! Получен эффект «Ускорение»!';
}
?>