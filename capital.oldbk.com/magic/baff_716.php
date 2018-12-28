<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Щит Хаоса';
$baff_type=716;//816

if ($user['battle'] == 0) 
{	
	echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
else {
	
	//1. проверить нету  ли на мне уже такого бафа
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and battle='{$user[battle]}' and type='{$baff_type}' ; "));

		if (($get_test_baff[id] > 0) and ($get_test_baff[lastup]>0))
		{
		err('На Вас уже есть это заклятие!');
		}
		else
		{
		$get_imun_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and type='816' ; "));
		if (($get_imun_baff[id] > 0) )
			{
			err("Вы сможете использовать это заклятие через ".floor(($get_imun_baff['time']-time())/60/60)." ч. ".round((($get_imun_baff['time']-time())/60)-(floor(($get_imun_baff['time']-time())/3600)*60))." мин.");
			}
			else
			{
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$user[id]}',`lastup`=1,`battle`='{$user[battle]}';");
			if (mysql_affected_rows()>0)
				{
				mysql_query("INSERT INTO `effects` SET `type`='816',`name`='Иммунитет от заклятия Щит Хаоса',`time`=".(time()+12*60*60).",`owner`='{$user[id]}',`battle`='{$user[battle]}';");//12 часов

				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>.<BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."\n");				
				
				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			}
		
		}


	} 
	


?>
