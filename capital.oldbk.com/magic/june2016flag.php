<?
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}

 if (!isset($_GET['clearstored']))
{
		$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '669' LIMIT 1;")); 
		$wtime= magicinf(669);
		if (!($effect['id']))
			{
			
			$flagid=(int)($_POST['target']);
			include('../euro2016.php');			
								
			$add_time_eff=time()+($magic['time']*60);
			mysql_query("INSERT INTO `effects` SET `type`=669,`name`='{$euflags[$flagid]['name']}',`time`='{$add_time_eff}',`owner`='{$user[id]}' ,  add_info='{$euflags[$flagid]['flag']}:Футбольная магия, опыт +30%:0.3:users_flag'   ;");
			
				mysql_query("UPDATE users set expbonus=expbonus+0.3 where id='{$user[id]}' ; ");
				if (mysql_affected_rows()>0)
					{
					mysql_query("INSERT INTO `oldbk`.`users_flag` SET `owner`='{$user['id']}',`flag`='{$euflags[$flagid]['flag_big']}',`flag_name`='{$euflags[$flagid]['name']}' on DUPLICATE KEY UPDATE `flag`='{$euflags[$flagid]['flag_big']}',`flag_name`='{$euflags[$flagid]['name']}' ; ");
					
					echo "<font color=red>Вы установили «{$euflags[$flagid]['name']}», и получили эффект <b>+30% опыта</b></font>";
					$bet=1;
					$sbet = 1;
					
								if ($rowm['ekr_flag']==1)
								{
								//первый юз свитка купленного привязываем свиток
								mysql_query("UPDATE oldbk.inventory set ekr_flag=0,  present='Удача' where id='{$rowm['id']}' limit 1;");
								}
					
					}
			} else 
			{
				echo "<font color=red><b>У Вас уже есть такой эффект!</b></font>";
			}
}
?>