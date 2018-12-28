<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$baff_name='Темный ритуал';
$baff_type=707;//старт каста 807

if ($user['battle'] == 0) 
{	
	echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
else {
	
	//1. проверить нету  ли на мне уже такого бафа
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and battle='{$user[battle]}' and ( type='{$baff_type}' or type=807) ; "));
		if ($get_test_baff[id] > 0) 
		{
		err('На Вас уже есть это заклятие!');
		}
		else
		{
		//2. проверяем  сколько есть уже в этом бою закастовавших это заклинание
		$get_count_baff=mysql_query("select  * from effects where battle='{$user[battle]}' and type=807 and owner in (select id from users where battle='{$user[battle]}' and battle_t='{$user[battle_t]}' and hp >0); ");
		$kow=0;
		while ($baff_owners = mysql_fetch_array($get_count_baff))
			   	{
				$kow++;
				$remem_own[$kow]=$baff_owners[owner];
				$remem_time[$kow]=$baff_owners[time];
				$remem_baff_id[$kow]=$baff_owners[id];
			   	}
		
			if ($kow==0)
			{
			//никого я начинаю
				mysql_query("INSERT INTO `effects` SET `type`='807',`name`='Начало темного ритуала',`time`='".(time()+60)."', `owner`='{$user[id]}',`battle`='{$user[battle]}';");//1 минута
				if (mysql_affected_rows()>0)
				{
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
				
//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>(Начал цепь...)</i> <BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(Начал цепь...)</i>\n");

				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			}
			else if ($kow==1)
			{
			//есть 1 я продолжаю
				mysql_query("INSERT INTO `effects` SET `type`='807',`name`='Продолжение темного ритуала',`time`='".$remem_time[1]."',`owner`='{$user[id]}',`battle`='{$user[battle]}';");//время дублируем с эфекта начала
				if (mysql_affected_rows()>0)
				{
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>(Продолжил цепь...)</i> <BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(Продолжил цепь...)</i>\n");
				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
				}
			}
			else
			{
			//есть двое я замыкаю и включаю баф
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$user[id]}',`lastup`=5,`battle`='{$user[battle]}';");//ставим себе баф
			foreach($remem_own as $ic=>$owner)
				{
			mysql_query("DELETE from effects where `type`='807' and owner='{$owner}' and battle='{$user[battle]}' ;"); 	
			mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$owner}',`lastup`=5,`battle`='{$user[battle]}';");//ставим  остальным баф				
				}
				
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

//				addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' использовал'.$action.' заклятие <b>"'.$baff_name.'"</b>. <i>(Закрыл цепь...)</i> <BR>');
		       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":".$baff_type."::<i>(Закрыл цепь...)</i>\n");

				$bet=1;
				$sbet = 1;
				echo "Все прошло удачно!";
				$MAGIC_OK=1;
			}
		}


	} 
	



?>
