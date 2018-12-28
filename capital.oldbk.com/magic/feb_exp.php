<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$proto_ids=array(907,908);
//эфект супер валентинок с 907-908


if (($user['id']!=$rowm['sowner']))
	{
	echo "Можно использовать после того как будет подарено!";
	}
elseif (in_array($magic[id],$proto_ids))
	{
	$testeff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}' and `type` in (".implode(", ", $proto_ids).") LIMIT 1;"));
	if ($testeff['id']>0)
		{
		echo "На персонаже уже есть эффект такого типа!"; 		
		}
		else
		{
			if ($magic['id']==907)
			{
			$ef_min=10;
			$ef_max=20;
			$ef_add=mt_rand($ef_min,$ef_max);
			$ef_add=round(($ef_add/100),2);//в процентах
			}
			elseif ($magic['id']==908)
			{
			$ef_min=3;
			$ef_max=5;			
			$ef_add=mt_rand($ef_min,$ef_max);			
			}

		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) values ('".$user['id']."','".$magic['name']."',".(time()+($magic['time']*60)).",".$magic['id'].",'".$ef_add."')");

			if(mysql_affected_rows()>0)
			{
			
				if ($magic['id']==907)
				{
				mysql_query("UPDATE users set expbonus=expbonus+{$ef_add} where id='{$user[id]}' ; ");
				}
			
			echo "<font color=red><b>Вы подверглись магии...</b></font>";
			$bet=1;
			$sbet=1;
			}
		}
	
	}

?>