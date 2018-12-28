<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if ($user['battle'] > 0) {
	echo "<font color=red>Не в бою...</font>";
} else	{

if (($user['lab'] ==0 ) OR ($user['lab'] ==1 ))
	{
	echo "<font color=red>Можно использовать только в Героическом или Легендарном Лабиринте...</font>";
	}
	else
	{
			$usrlab=mysql_fetch_array(mysql_query("SELECT * FROM `labirint_users` WHERE `owner` = '{$user[id]}' LIMIT 1;"));
			
			if (($usrlab[map]>0)AND($usrlab[x]>0)and($usrlab[y]>0)	)
			{
			$map=file('/www/capitalcity.oldbk.com/labmaps/'.$usrlab[map].'.map');
			$room_map=$map[$usrlab[x]][$usrlab[y]];
			
			if (($room_map=='F') OR ($room_map=='5') OR ($room_map=='2'))
				{
				err('Тут нельзя ставить портал!');
				}
				else
				{
					$testport=mysql_fetch_array(mysql_query("SELECT * FROM `labirint_items` WHERE `map` = '{$usrlab[map]}' AND x='{$usrlab[x]}' AND y='{$usrlab[y]}'   LIMIT 1;"));		
					
					if ($testport['item']=='T')
						{
						echo "<font color=red>Тут уже есть открытый портал...</font>";
						}
						else
						{
						mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`,`add_info`) 
						values('".$usrlab[map]."','T','".$usrlab[x]."','".$usrlab[y]."','1','1','{$user['login']}' ) 
						ON DUPLICATE KEY UPDATE `active` =1, `count`=`count`+1;");
						echo "<font color=red>Удачно использовано: <i>Открыть Портал</i>...</font>";
						if ($user[sex]==1) { $sexi='использовал'; } else { $sexi='использовала'; }
						addch("<img src=i/magic/openport.gif> {$user[login]} ".$sexi." <i>Открыть Портал</i>",$user['room'],$user['id_city']);
						$bet=1;
						$sbet = 1;
						}
				}
			}
			else
			{
			echo "<font color=red>Тут нельзя использовать...</font>";
			}
	   
	  }	
	  
	
	

}
?>

