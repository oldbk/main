<?php

if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$targ=($_POST['target']);

$baff_name='Печать инквизиции';
$baff_type=805;

if ($user['battle'] == 0) 
{	
	echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
else {
	
	//if ($user[battle_t]==1)  {  $enem_t=2; 	}  else {  $enem_t=1; }
	
	$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$targ}' and battle='{$user[battle]}' and battle_t!='{$user[battle_t]}' and hp>0   LIMIT 1;"));
	
	if ($jert[id] >0)
	{
	//1. проверить нету  ли на мне уже такого бафа
	
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert[id]}'  and type='{$baff_type}' ; "));

		if ($get_test_baff[id] > 0) 
		{
			err('На "'.$jert[login].'" уже есть это заклятие!');
		}
		else
		{
	
		$get_imun_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert[id]}'  and type=806 ; "));
		if ($get_imun_baff[id]>0)
			{
			err("У \"".$jert[login]."\" иммунитет на использование этого заклятия, еще ".floor(($get_imun_baff['time']-time())/60/60)." ч. ".round((($get_imun_baff['time']-time())/60)-(floor(($get_imun_baff['time']-time())/3600)*60))." мин.");
			}
			else
			{
			$get_var_baff= mysql_fetch_array(mysql_query("select * from battle_vars where battle='{$jert[battle]}' and owner='{$jert[id]}' ;"));
			if ($get_var_baff[baf_805_use]>0)
				{
				err("У \"".$jert[login]."\" иммунитет на использование этого заклятия до конца боя!");			
				}
				else
				{
				mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=".(time()+1200).",`owner`='{$jert[id]}' , `battle`='{$user[battle]}'  ;");//20 мин запрет использовать абилки
				if (mysql_affected_rows()>0)
					{
					mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`baf_805_use`) values('".$jert['battle']."', '".$jert['id']."', '".time()."' , '1' ) ON DUPLICATE KEY UPDATE `baf_805_use` =`baf_805_use`+1;");
					
					$get_mol= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert[id]}'  and type=2 ; "));
					if (!($get_mol[id]>0))
					{
					mysql_query("INSERT INTO `effects` SET `type`='2',`name`='Заклятие молчания',`time`=".(time()+180).",`owner`='{$jert[id]}' ;");//3 мин молчанка
					mysql_query("UPDATE users set slp=1 where id={$jert[id]} ;");
					}

					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

//					addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>, на персонажа '.nick_in_battle($jert,$jert[battle_t]).'.<BR>');				
			       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type.":".nick_new_in_battle($jert)."\n");

					$bet=1;
					$sbet = 1;
					echo "Все прошло удачно!";
					$MAGIC_OK=1;
					}
				}
			}
		
		}
		
	   }	
	else
	     {
	     err('Среди живых врагов "'.$targ.'"  не найден!');
	     }

	} 


?>
