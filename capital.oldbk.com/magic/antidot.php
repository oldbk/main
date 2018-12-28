<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");
if (!$user['battle'] == 0) {	echo "Не в бою..."; }
elseif ($user['lab'] == 0) {	echo "Можно использовать только в лабиринте..."; }
else
{

      $i_have_st=mysql_fetch_array(mysql_query("SELECT * FROM `labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user[id]."' ;"));
		if ($i_have_st[owner]==$user[id])
		{
		// есть
		mysql_query("UPDATE users set sila=sila+1, lovk=lovk+1, inta=inta+1  where id='{$user[id]}';");	
		}


//mysql_query("DELETE FROM `labirint_var` WHERE  (`var`='timer_trap'  or `var`='poison_trap'  ) and `owner`='".$user[id]."';");
  mysql_query("DELETE FROM `labirint_var` WHERE  (`var`='stat_trap'  or `var`='timer_trap'  or `var`='poison_trap'  ) and `owner`='".$user[id]."';");
  
  mysql_query("UPDATE users set `podarokAD`=0 where id='{$user[id]}';");
  
  
  
  
  
  echo "Вы выпили антидот...";
  $_SESSION['time_trap']=0;
  $bet=1;
  $sbet = 1;
}



?>