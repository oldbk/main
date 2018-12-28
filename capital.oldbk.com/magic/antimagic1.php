<?php
		if (!($_SESSION['uid'] >0))
		{
			header("Location: index.php");
			die();
		}


$eff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='557' ; "));

if ($user['battle'] > 0) {
	echo "Не в бою...";
}
else if ($eff['id'] > 0) {
	echo "Вы уже защищены...";
} else {


		$tt[55557]=0.7; //-30%
		$tt[55558]=0.5; //-50%		
		$tt[55559]=0.3; //-70%				
		$tt[55560]=0.85; //-15%				

		if ($ABIL>0) 	
		{
		$mtype=0.85; //-15%
		}
		else
		{
		$mtype=$tt[$rowm['prototype']];
		}
		
		/*
		$prof_data=GetUserProfLevels($user); 
		//  Алхимик      Защита от магии:+...%      2% за каждый уровень мастерства
		if ($prof_data['alchemistlevel']>0)	
			{
			$mtype-=(0.02*$prof_data['alchemistlevel']);
			}
		*/

		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) values ('".$user['id']."','Защита от магии стихий',".(time()+3600).",557,'{$mtype}');");
		echo "<font color=red><b>Использовано успешно! Получен эффект «Защита от магии стихий».</b></font>";

		$bet=1;
		$sbet = 1;
	}

?>
