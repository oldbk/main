<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Иммунитет';
$baff_type=728;



if ($user['battle'] == 0) 
{	
	echo "Это боевая магия..."; 
}
if($user[hp]<=0) {      err('Для Вас бой окончен!');        }
else {
	

	//1. проверить нету  ли на целе уже такого бафа
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and type='{$baff_type}' ; "));

		if ($get_test_baff[id] > 0) 
		{
		err('На Вас  уже есть это заклятие!');
		}
		else
		{
		
		mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`='".(time()+1200)."',`owner`='{$user[id]}' ");
		if (mysql_affected_rows()>0)
			{
				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

	       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type.":\n");

			$bet=1;
			$sbet = 1;
			echo "Все прошло удачно!";
			$MAGIC_OK=1;
			}
		
		}
	 



	} 
	


?>
