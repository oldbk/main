<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if ($user['battle'] > 0) {
	err("Не в бою...");
} else	
{
	if (($user['lab'] ==0 ) OR ($user['lab'] ==1 ))
	{
	err("Можно использовать только в Героическом Лабиринте...");
	}
	else
	{
		$eff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='7777' ; "));	
				
		if ($eff['id']>0)
		{
		err("Уже есть страховка...");
		}
		else
			{
				mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) values ('".$user['id']."','{$rowm['name']}',1999999999,7777,'{$rowm['img']}:{$rowm['letter']}' );");
				  if (mysql_affected_rows()>0)
				  	{
					if ($user[sex]==1) { $sexi='использовал'; } else { $sexi='использовала'; }
					$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
					addch($mag_gif." {$user[login]} ".$sexi." ".link_for_magic($rowm['img'],$rowm['name']),$user['room'],$user['id_city']);
					$bet=1;
					$sbet = 1;
					err('Вы удачно застраховались, портал откроется автоматически перед местом вашей гибели!');
					}
			}
	 }	


}
?>

