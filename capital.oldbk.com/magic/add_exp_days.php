<?php
//ТИп эфекта  171 - магия 172

if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}


if (!(isset($_GET['clearstored'])))
{
$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '171' LIMIT 1;")); 

$sdays[180001]=1;
$sdays[180002]=2;
$sdays[180003]=3;
$sdays[180004]=4;
$sdays[180005]=5;
$sdays[180006]=6;
$sdays[180007]=7;


	$nnf=date("N");	// от 1 (понедельник) до 7 (воскресенье)
	

	$useday=$sdays[$rowm['prototype']];
	$mag_add_exp=0.1;
	
	
	if ($useday==$nnf)
		{
		$add_time_eff=time()+($magic['time']*60);
		$updok=0;
		if ($effect['id']>0)
		{
		//есть обновляем
			mysql_query("UPDATE `oldbk`.`effects` SET `time`='{$add_time_eff}', add_info='{$rowm['name']}' WHERE `id`='{$effect['id']}' ");
			if(mysql_affected_rows()>0)
			{
			$updok=1;
			}
		}
		if ($updok==0)
		{
		//нету вставляем
		mysql_query("INSERT INTO `effects` SET `type`=171,`name`='{$magic[name]}',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='{$rowm['name']}' ;");
		mysql_query("UPDATE users set expbonus=expbonus+{$mag_add_exp} where id='{$user[id]}' ; ");		
		}
		

		echo "<font color=red>Удачно использована магия <b>\"Повышеный опыт\". {$magic[name]}</b></font>";
		$bet=1;
		$sbet = 1;
		}
		else
		{
		echo "<font color=red><b>Можно использовать только тот день недели который сегодня :)</b></font>";
		}
	 
		
}
?>