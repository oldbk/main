<?php

if (!($_SESSION['uid'] >0)) header("Location: index.php");


if ($user['battle'] > 0) {
	echo "<font color=red>Не в бою...</font>";
} else	{

if ($user['room'] !=49 ) 
	{
	echo "<font color=red>В этой комнате свиток не прочитать...</font>";
	}
	else
	{
	// проверка на уже есть медаль
	if ((strpos($user[medals],"011;" ) !== FALSE))
		{
		echo "<font color=red>Вы уже изучили это знание...</font>";		
		}
		else
		{
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `magic`='111' AND `owner` = '{$user[id]}' LIMIT 1;"));
		 if ($svitok[0] > 0 )
		    {
			// гуд
			mysql_query("UPDATE `users` SET `medals` = CONCAT(`medals`, '011;') WHERE  `id` = '{$user[id]}' LIMIT 1; ");
			echo "<font color=red>Удачно использовано: <i>".$svitok[name]."</i>...</font>";
			$bet=1;
			$sbet = 1;
	   
		    }	
	    	else
		 	{
		 	 echo "<font color=red>У Вас нет такого свитка ...</font>";
			}
		}
	
	}

}
?>