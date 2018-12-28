<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$targ=($_POST['target']);
$baff_name='Серый покров';
$baff_type=714;

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
	if($jert[level]>$user[level]) {      err('Вы не можете наложить это заклятие на персонажа старше Вас по уровню!');        }
	else
	{
	//1. проверить нету  ли на уже такого бафа
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert[id]}' and battle='{$jert[battle]}' and type='{$baff_type}' ; "));

		if (($get_test_baff[id] > 0) and ($get_test_baff[lastup]>0))
		{
		err('На "'.$jert[login].'" уже есть это заклятие!');
		}
		else
		{
		$get_var_baff= mysql_fetch_array(mysql_query("select * from battle_vars where battle='{$jert[battle]}' and owner='{$jert[id]}' ;"));
		if ($get_var_baff[baf_714_use]>0)
			{
			err("У \"".$jert[login]."\" иммунитет на использование этого заклятия до конца боя!");			
			}
			else
			{
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$jert[id]}',`lastup`=3,`battle`='{$jert[battle]}';");
			if (mysql_affected_rows()>0)
				{
				
				mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`baf_714_use`) values('".$jert['battle']."', '".$jert['id']."', '".time()."' , '1' ) ON DUPLICATE KEY UPDATE `baf_714_use` =`baf_714_use`+1;");
				
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>, на персонажа '.nick_in_battle($jert,$jert[battle_t]).'.<BR>');
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
