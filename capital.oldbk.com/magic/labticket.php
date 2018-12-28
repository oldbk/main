<?

// magic идентификацыя

//$mklan=mysql_fetch_array(mysql_query('select * from clans where short="'.$user[klan].'";'));
//$id_war=mysql_fetch_array(mysql_query('select * from clans_war where (defender='.$mklan[id].' or agressor='.$mklan[id].') and date>="'.time().'";'));

if (!($_SESSION['uid'] >0)) header("Location: index.php");
if (!$user['battle'] == 0) {	echo "Не в бою..."; }
//else
//if ($id_war[id]>0) { echo 'Не во время войны...';}

    /* Если будет шанс срабатывания, раскоментить!!! - для абилок
	elseif($klan_abil==1)    //если юзается как абилка (наследие от клана, шанс 100%)
	{
		$int=101;
	}*/

elseif ($user['lab'] !=0) {	echo "Вы уже в лабиринте..."; }
else
{
 mysql_query("DELETE FROM oldbk.`labirint_var` WHERE  `var`='labstarttime' and `owner`='".$user[id]."';");
//  if (mysql_affected_rows()>0)
if (true)
 	{
		 mysql_query("Insert into ristalka set `owner`='{$user[id]}', labp=1 on DUPLICATE key update labp=1"); //ставим флаг
		 echo "Вы использовали ".link_for_magic($rowm['img'],$rowm['name'])."...<i>посещение лабиринта открыто.</i>";
		 $mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
		 $a="";  if ($user[sex]==0)   {  $a="а";  }
		 addch($mag_gif." {$user[login]} использовал{$a} ".link_for_magic($rowm['img'],$rowm['name'])."...",$user['room'],$user['id_city']);
		    $bet=1;
		    $sbet = 1;
	}
	else
	{
		 echo "<i>Посещение лабиринта и так Вам открыто!</i>";	
	}
}



?>