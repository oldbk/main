<?php
		if (!($_SESSION['uid'] >0))
		{
			header("Location: index.php");
			die();
		}

if ($user['battle'] > 0) {
	echo "Не в бою...";
}
else if ($user['trv'] > 0) {
	echo "Вы уже защищены...";
} else {
		if ($user[hidden]>0 and $user[hiddenlog]=='') 
		{
		addch("<img src=i/magic/no_cure2.gif>Персонаж &quot;<i>Невидимка</i>&quot; защитился от травм на бой..",$user['room'],$user['id_city']);
		}
		elseif ($user[hidden]>0 and $user[hiddenlog]!='') 
		{ 
		 $fuser=load_perevopl($user); 
 		addch("<img src=i/magic/no_cure2.gif>Персонаж &quot;{$fuser['login']}&quot; защитился от травм на бой..",$user['room'],$user['id_city']);
		 }
		 else
		 {
		 addch("<img src=i/magic/no_cure2.gif>Персонаж &quot;{$user['login']}&quot; защитился от травм на бой..",$user['room'],$user['id_city']);
		}

		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$user['id']."','Защита от травм на бой',1999999999,556);");
		mysql_query("UPDATE `users` SET `trv`='1' where `id`='{$user['id']}';");
		echo "<font color=red><b>Вы защитились от травм на бой...</b></font>";

		$bet=1;
		$sbet = 1;
	}

?>
