<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if ($user['hp']==$user['maxhp'])
	{
	echo "Вы и так полны сил...";
	} 
	else if ($user['in_tower'] > 0) 
	{
	echo "Не работает в БС...";
	}
else if (($user['hp']==0) and ($user['battle'] > 0))
	{
	echo "Вы метрвы...";
	}
elseif (($user['room'] >=210)AND($user['room'] <299)) {
	echo "Тут это не работает...";
}
elseif (can_hill($user))	
{
	err('Вы временно не можете использовать восстановление жизни!');
}
else
{

//get calc
//echo $user['maxhp'] ;
//echo "<br>";
//echo $user['hp'];
//echo "<br>";

$need=($user['maxhp']-$user['hp']);

if ($klan['glava']==$user['id'])
		{
		// for glava
		$ihave=$tabil[maxcount]-$tabil[count];
		}
 else 	{
		// for other
		$allhave=$tabil[maxcount]-$tabil[count];
		$ihave=$dostup[$user['id']][$tabil[magic]]-$ucount[$user['id']][$tabil[magic]];
			if ($ihave>$allhave) {$ihave=$allhave;} 
		}
//echo "$need <br>";
//echo "$ihave <br>";

if ($need > $ihave )
	{
	$need=$ihave;
	}
//echo "$need <br>";





if ($user['battle'] > 0) {

$typebattle=mysql_fetch_array(mysql_query("SELECT `nomagic`, `type` from `battle` where `id`='".$user['battle']."' ; "));
if ( ($typebattle['nomagic']!=0) OR ($typebattle['type']==62) )
	{
	echo "В этом бою нельзя использовать эту магию...";
	}
	else
   {
	//check
	$count_use= mysql_fetch_array(mysql_query("SELECT sum(istok_use) as istok from `battle_vars` where  `owner`='".$user[id]."' and `battle`='".$user[battle]."' ;"));
	if ( $count_use[0] > 1)
	{
	echo "Вы исчерпали источник в этом бою...";
	}
		else 
		{
	//add count_use istok
	mysql_query("INSERT battle_vars (`battle`, `owner`, `istok_use`) values ('{$user[battle]}', '{$user[id]}' , '1' ) ON DUPLICATE KEY UPDATE `istok_use`=`istok_use`+1 ; ");

	if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
	elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
		
//use
	mysql_query("UPDATE `users` SET `hp`=`hp`+'".$need."' WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
	$user['hp']=$user['hp']+$need;

        addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+900).":<b><font color=red>+".((($user[hidden]>0)and($user[hiddenlog]==''))?"??</font></b> [??/??]":"{$need}</font></b> [{$user[hp]}/{$user[maxhp]}]")."\n");									



	// апаем мемори
		if ($user[battle_t]==1) {  $boec_t1[$user[id]][hp]=$user['hp']  ;  } 
				elseif ($user[battle_t]==2) {   $boec_t2[$user[id]][hp]=$user['hp']  ;  }
				elseif ($user[battle_t]==3) {   $boec_t3[$user[id]][hp]=$user['hp']  ;  }				


//
//to abil log
		$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+$need;
		mysql_query("update clans_abil set `count`=`count`+'".$need."', `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and maxcount!=count and maxcount!=0 and klan='".$user['klan']."'  ; ");
		mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '".$need." HP' ) ; ");
////
	echo "<font color=red><b>Вы пополнили здоровье из источника...</b></font>";
	$bet=1;
	$sbet = 1;
	}
  }
}
else
{
//no in battle
//use
	mysql_query("UPDATE `users` SET `hp`=`hp`+'".$need."' WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
	$user['hp']=$user['hp']+$need;

//
//to abil log
		$ucount[$user['id']][$tabil[magic]]=$ucount[$user['id']][$tabil[magic]]+$need;
		mysql_query("update clans_abil set `count`=`count`+'".$need."', `userscount`='".serialize($ucount)."' where magic='".(int)($_POST['use'])."' and maxcount!=count and maxcount!=0 and klan='".$user['klan']."'  ; ");
		mysql_query("INSERT clans_abil_log (`owner`, `klan` , `magic`, `date`, `msg`) values ('".$user['id']."', '".$user['klan']."', '".(int)($_POST['use'])."' , NOW() , '".$need." HP' ) ; ");
////
	echo "<font color=red><b>Вы пополнили здоровье из источника...</b></font>";
	$bet=1;
	$sbet = 1;

}



}
echo "<br>";
?>