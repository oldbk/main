<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");
if ($user['battle'] > 0) {
	echo "Не в бою...";
} else	{
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE naem = 0 and `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND (arsenal_klan = '' OR arsenal_owner=1 ) AND `owner` = '{$user['id']}' AND `name` = '{$_POST['target']}' AND `sharped` = 0 AND type=3 and gmeshok = 0 LIMIT 1;"));
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE  magic=90 AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND (arsenal_klan = '' OR arsenal_owner=1 )  AND `owner` = '{$user['id']}' LIMIT 1;"));

		if ($dress && $svitok) {
		
			 if ($dress[otdel]==1) 
			 		{ 
			 		$asql="`nnoj` = `nnoj`+5, `ninta` = `ninta`+5";
			 		}
			 else
			 if ($dress[otdel]==11) 
			 		{
 					$asql="`ntopor` = `ntopor`+5, `nsila` = `nsila`+5";
			 		}
			 else
			 if ($dress[otdel]==12) 
			 		{
					 $asql="`ndubina` = `ndubina`+5, `nlovk` = `nlovk`+5";
					}
			else
 			 if ($dress[otdel]==13) 
 			 		{
				 	$asql="`nmech` = `nmech`+5, `nvinos` = `nvinos`+5";
				 	}
				 	else
				 	{
				 	$asql='';
				 	}
		
			if ($asql!='')
			      {
				if (mysql_query("UPDATE oldbk.`inventory` SET `sharped` = 1, `name` = CONCAT(`name`,'+5'), `minu` = `minu`+5, `maxu`=`maxu`+5, `cost` = `cost`+30 , ".$asql."   WHERE `id` = {$dress['id']} LIMIT 1;")) 
				{
				echo "<font color=red><b>Предмет \"{$_POST['target']}\" удачно заточен +5.<b></font> ";
				$bet=1;
				$sbet = 1;
				}
				else 
				{
				echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
			     }
			     else
			     {
				echo "<font color=red><b>Этот тип оружия не заточить этим свитком!<b></font>";			     
			     }
		} else {
			echo "<font color=red><b>Неправильное имя предмета или неправильный свиток<b></font>";
		}

	 
}
?>