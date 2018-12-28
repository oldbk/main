<?php
//ТИп эфекта для всех свитков 160!!

if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}
$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '160' LIMIT 1;")); 

if (!($effect['id']))
	{
	$mag_exp_bonus[161]=0.1;
	$mag_exp_bonus[162]=0.2;	
	$mag_exp_bonus[163]=0.3;	
	$mag_exp_bonus[164]=0.4;	
	$mag_exp_bonus[165]=0.5;	
	$mag_exp_bonus[166]=0.6;	
	$mag_exp_bonus[167]=0.7;
	$mag_exp_bonus[168]=0.8;	
	$mag_exp_bonus[169]=0.9;	
	$mag_exp_bonus[170]=1;	

	$mag_add_exp=$mag_exp_bonus[$magic[id]];
	if ($mag_add_exp>0)
		{
		$add_time_eff=time()+($magic['time']*60);
		//в  add_info  пишем  $mag_add_exp
		mysql_query("INSERT INTO `effects` SET `type`=160,`name`='{$magic[name]}',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='{$mag_add_exp}' ;");
		mysql_query("UPDATE users set expbonus=expbonus+{$mag_add_exp} where id='{$user[id]}' ; ");
		echo "<font color=red>Удачно использована магия <b>\"Повышеный опыт\". {$magic[name]}</b></font>";
		$bet=1;
		$sbet = 1;
		}
		else
		{
		echo "<font color=red><b>Что-то не так!!!</b></font>";
		}
	} else {
		echo "<font color=red><b>Вы уже использовали такую магию!</b></font>";
	}
		

?>