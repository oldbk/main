<?php
if (!($_SESSION['uid'] > 0)) header("Location: index.php");
// magic
// встраивание магии
//+add fix no incmagic type 52 (Fred)
//+ NG meshok fix
$dont_del_time="AND prototype not in (20002,2014001,2014002,2014003,2014004,2014005,2014006,2014007,2014008,600660) ";


if ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif ($user['lab'] ==1) {
	echo "Неподходящий момент...";
}
else
{
	if(isset($_REQUEST['clearstored']))
	{
		$_SESSION['scroll'] = null;
		header("Location: main.php?edit=1");
	}
	$int = 100;
	if (rand(1,100) <= $int) {		$sql="SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$user['id']}'
		AND `id` = '{$_POST['target']}' ".$dont_del_time."
		AND dressed = 0 AND setsale = 0 AND type = 200 and prototype!=0 and prototype!=10000 and prototype!=9999 and otdel in (7,71,73,77) AND goden>0 AND present!='' AND
		`prokat_idp`=0 AND arsenal_klan = '' and dategoden>0 LIMIT 1;";
		//echo $sql;
		$dress = mysql_fetch_array(mysql_query($sql));

		$_SESSION['scroll'] = null;
		if(!$dress OR $dress['type'] != 200)
		{
			echo "<font color=red><b>У вас нет такого предмета! [{$_POST['target']}]<b></font>";
		}
		else
		{
			if (mysql_query(
			"UPDATE oldbk.`inventory` SET
			goden = 0, dategoden=0
			WHERE `id` = '{$dress['id']}' LIMIT 1;"))
			{
				echo "<font color=red><b>Время стерто с \"".$dress['name']."\"<b></font>";
				$bet=1;
				$sbet = 1;
				if(!$_SESSION['beginer_quest'][none]) 
				{				
					// квест
				        $last_q=check_last_quest(30);
				        if($last_q) 
					{
						quest_check_type_30($last_q,$user[id],5,1);
					}
				      
				}
				
			}
		}

	}
	else
	{
		echo "<font color=red><b>Cвиток рассыпался в ваших руках...<b></font>";
		$bet=1;
		$_SESSION['scroll'] = null;
	}
}
?>
