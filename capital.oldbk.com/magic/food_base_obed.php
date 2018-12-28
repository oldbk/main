<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
//$addstat
//$addvalue
//$addtxt
//
	$prof_data=GetUserProfLevels($user); 
	// Повар      Дополнительный бонус от еды: +...НР      20НР за уровень ремесла
	if (($prof_data['cooklevel']>0)	and ($addstat1=='maxhp') )
		{
		$addvalue1+=(20*(int)($prof_data['cooklevel']));
		}


$get_food=mysql_fetch_array(mysql_query("select * from users_bonus where owner='{$user[id]}' ;"));

if ($user['id']==14897) 
	{
//	print_r($get_food);
	}

if ($user['battle'] > 0) {echo "Не в бою...";}
elseif ($get_food['finish_time']!='')
	{
	 echo "<font color=red>У вас есть похожий эффект!</font>";	
	}
else
	{
	$askul='';
	if (($addstat1!='') and ($get_food[$addstat1] < $addvalue1))		   	{ $askul.=" ".$addstat1."='{$addvalue1}' ,"   ;  }
	if (($addstat2!='') and ($get_food[$addstat2] < $addvalue2))  			{ $askul.=" ".$addstat2."='{$addvalue2}' ,"   ;  }	
	if (($addstat3!='') and ($get_food[$addstat3] < $addvalue3))   			{ $askul.=" ".$addstat3."='{$addvalue3}' ,"   ;  }	
	if (($addstat4!='') and ($get_food[$addstat4] < $addvalue4))  			{ $askul.=" ".$addstat4."='{$addvalue4}' ,"   ;  }	
	if (($addstat5!='') and ($get_food[$addstat5] < $addvalue5)) 			{ $askul.=" ".$addstat5."='{$addvalue5}' ,"   ;  }	
	if (($addstat6!='') and ($get_food[$addstat6] < $addvalue6)) 			{ $askul.=" ".$addstat6."='{$addvalue6}' ,"   ;  }		
	
	if ($askul!='')
	 {
	 mysql_query("INSERT into users_bonus SET ".$askul."  owner='{$user[id]}' ON DUPLICATE KEY UPDATE  ".$askul."  refresh=refresh+1 ; ");
	 if (mysql_affected_rows()>0)
			 	{
			 	echo "<font color=red>Вы подкрепились на бой</font></br>";
				$bet=1;
				$sbet = 1;
  				}
	 }
	 else
	 {
	 echo "<font color=red>Вы уже подкрепились этим...можно попробовать после боя...</font>";	
	 }
	 
	 
	}

?>
