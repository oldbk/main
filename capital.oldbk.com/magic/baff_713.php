<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Аура Хаоса';
$baff_type=713;

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
		$get_var_baff= mysql_fetch_array(mysql_query("select * from battle_vars where battle='{$user[battle]}' and owner='{$user[id]}' ;"));
		if ($get_var_baff[baf_713_use]>0)
			{
			err('Можно использовать только раз в бою!');			
			}
			else
			{
			// делаем рандом на мф
			//первым идет значение которое будет + вторым идет минус
			$strmf[1]='Мф. крит.^';//1-mfkrit
			$strmf[2]='Мф. против крит.^';//2-mfakrit
			$strmf[3]='Мф. уверт.^';//3-mfuvorot
			$strmf[4]='Мф. против уверт.^';	//mfauvorot
			$bffg=mt_rand(1,4);
			$bffn=mt_rand(1,4);
			$ad_bff=$bffg.$bffn;
			
			
			
			
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$user[id]}',`lastup`=1, `add_info`='{$ad_bff}' ,`battle`='{$user[battle]}';");// не обрабатывается в разменах
			if (mysql_affected_rows()>0)
				{
				mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`baf_713_use`) values('".$user['battle']."', '".$user['id']."', '".time()."' , '1' ) ON DUPLICATE KEY UPDATE `baf_713_use` =`baf_713_use`+1;");

					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }


//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>('.$strmf[$bffg].'+20% и '.$strmf[$bffn].'-20% )</i> <BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(".$strmf[$bffg]."+20% и ".$strmf[$bffn]."-20% )</i>\n");

				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			}
		
		}


	} 
	


?>
