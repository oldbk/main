<?php

	$skin_id = "dragon2016_bot22_";

	$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '302' LIMIT 1;")); 
	if (!$effect['id']) {			
		mysql_query("INSERT INTO `effects` SET `type`= '302',`name`='Иллюзия Дракона',`time`='1999999999',`owner`='{$user[id]}', add_info='".$skin_id."'");
	}
	
?>