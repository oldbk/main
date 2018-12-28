<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if ($user['battle'] > 0) {
	echo "<font color=red>Не в бою...</font>";
} else	{


if (($user['lab'] ==0 ) OR ($user['lab'] ==1 ))
	{
	echo "<font color=red>Можно использовать только в Героическом Лабиринте...</font>";
	}
	else
	{
	$id=(int)($id);
	$tested=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id='{$id}' and owner='{$user[id]}' and setsale=0 and prototype=50078  and duration < maxdur LIMIT 1;"));
	if ($tested[id]==$id)
		{
		mysql_query("UPDATE oldbk.`inventory` SET `present`='Лабиринт Хаоса',`duration`=1,`letter`='<a target=_blank href=lab2.php?lookmap={$id} >Просмотреть карту</a>',`labonly`=1,`labflag`=1 WHERE `id`={$id} and owner='{$user[id]} and setsale=0 ';");
		echo "<font color=red>Удачно использовано: <i>Познание Лабиринта</i>...</font>";
		if ($user[sex]==1) { $sexi='использовал'; } else { $sexi='использовала'; }
		addch("<img src=i/magic/lmap.gif> {$user[login]} ".$sexi." <i>Познание Лабиринта</i>",$user['room'],$user['id_city']);
		$sbet = 1;
		}
		else
		{
		echo "<font color=red>Свиток нельзя использовать...</font>";
		}
	 }	
	  
	
	

}
?>

