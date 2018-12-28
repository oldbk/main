<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
//$addstat
//$addvalue
//$addtxt
//

	$prof_data=GetUserProfLevels($user); 
	// Повар      Дополнительный бонус от еды: +...НР      20НР за уровень ремесла
	if (($prof_data['cooklevel']>0)	and  ($addstat=='maxhp') )
		{
		$addvalue+=(20*(int)($prof_data['cooklevel']));
		}


$get_food=mysql_fetch_array(mysql_query("select * from users_bonus where owner='{$user[id]}' ;"));



if ($user['battle'] > 0) {echo "Не в бою...";}
else
	if ($get_food['finish_time']!='')
	{
	 echo "<font color=red>У вас есть похожий эффект!</font>";	
	}
else
if ($get_food['usec'] > 0) {
	echo "<br><font color=red>Замковое абилити уже было использовано...</font><br>";}
else
if ($get_food[$addstat] >= $addvalue)
	{
	 echo "<font color=red>Вы уже использовали это...можно попробовать после боя...</font>";	
	}
else
	{
	mysql_query("INSERT into users_bonus SET ".$addstat."='{$addvalue}' , usec = 1, owner='{$user[id]}' ON DUPLICATE KEY UPDATE  ".$addstat."='{$addvalue}' , refresh=refresh+1, usec =usec+1 ; ");

	 
		if (mysql_affected_rows()>0)
					 	{
					 	if ($addvalue<1) {$addvalue=($addvalue*100); $addvalue.='%'; }
					 	echo "<font color=red>Вы усилились на бой</font></br>";
	  					$bet=1;
						$sbet = 1;

		  				}
	}

?>
