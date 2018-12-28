<?php

//+100% опыта на 360 мин
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}
$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '669' LIMIT 1;")); 
$wtime= magicinf(669);
if (!($effect['id']))
	{
	$add_time_eff=time()+($magic['time']*60);
	mysql_query("INSERT INTO `effects` SET `type`=669,`name`='{$rowm['name']}',`time`='{$add_time_eff}',`owner`='{$user[id]}' ,  add_info='{$rowm['img']}:{$rowm['letter']}:1'   ;");
	
		mysql_query("UPDATE users set expbonus=expbonus+1 where id='{$user[id]}' ; ");
		if (mysql_affected_rows()>0)
			{
			echo "<font color=red>Вы сделали глоток из фляги, получен эффект <b>+100% опыта</b></font>";
			$bet=1;
			$sbet = 1;
			}
	} else 
	{
		echo "<font color=red><b>Вы уже выпили из фляги!</b></font>";
	}
		

?>