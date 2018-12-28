<?
if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); }
//type = 75

if ($user['battle'] > 0) {
	echo "<font color=red>Не в бою...</font>";
} else	
{

	if ($user['lab'] !=4) 
	{
	echo "<font color=red>Можно использовать только в Легендарном  Лабиринте...</font>";
	}
	else
	{
		$eff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='75' LIMIT 1; "));
	
		if ($eff['id']>0)
			{
			$addinfo=explode("::",$eff['add_info']);
			$mtype=$addinfo[0]."::".($addinfo[1]+1);
			mysql_query("UPDATE `effects` SET  add_info='{$mtype}'  WHERE `id`='{$eff['id']}' ");
			}
			else
			{
			$mtype=$rowm['img']."::1";
			mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) values ('".$user['id']."','{$rowm['name']}','1999999999',75,'{$mtype}');");
			}
	
			//$_SESSION['sumrak']=$_SESSION['sumrak']+1;
			
			echo "<font color=red>Удачно использован свиток: <i>".$rowm['name']."</i>...</font>";
			if ($user[sex]==1) { $sexi='использовал'; } else { $sexi='использовала'; }
			addch("<img src=i/sh/".$rowm['img']."> {$user[login]} ".$sexi." <i>".$rowm['name']."</i>",$user['room'],$user['id_city']);
			
			$bet=1;
			$sbet = 1;
			$MAGIC_OK=1;
	   
	}	
	  
	
	

}
?>

