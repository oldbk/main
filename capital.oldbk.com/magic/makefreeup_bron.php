<?php
$MAXUP=5;
$ADDMF[1]=2; $ADDCOST[1]=0.2;
$ADDMF[2]=3; $ADDCOST[2]=0.2;
$ADDMF[3]=4; $ADDCOST[3]=0.4;
$ADDMF[4]=6; $ADDCOST[4]=0.7;
$ADDMF[5]=10; $ADDCOST[5]=0.1;

if (!($_SESSION['uid'] >0)) header("Location: index.php");


if ($user['battle'] > 0) {
	echo "Не в бою...";
} else	{
	$_POST['target']=(int)($_POST['target']);
	$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}') AND arsenal_klan = '' AND `labonly`=0 AND `bs_owner`=0 AND `type`=4 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and id='{$_POST['target']}' ;"));
	$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `name` = 'Подогнать Бронь' AND `owner` = '{$user['id']}' LIMIT 1;"));
	if ($dress[id]>0)
	{
	if (($svitok[labflag]>0) AND ($svitok[id]>0))
		{
			echo "<font color=red>Этот свиток можно использовать только после выхода из лабиринта!<b></font>";		
		}
	else
	   if ($svitok[id]>0)
	   {
		if ($dress[ups] > ($MAXUP-1))
		{
			echo "<font color=red>Бронь уже подогнана до максимального уровня!<b></font>";
		}
		else
		{
		$cost_add=round($dress[cost] * $ADDCOST[$dress[ups]+1], 0);
		if (mysql_query("UPDATE oldbk.`inventory` SET `ups` =`ups`+1, `mfbonus`=`mfbonus`+'{$ADDMF[$dress[ups]+1]}' , `sowner`='{$user['id']}', `cost` = `cost` + '".$cost_add."'    WHERE `id` = {$dress['id']} LIMIT 1;"))
			{
				echo "<font color=red><b>Броня \"{$dress[name]}\" удачно подогнана.<b></font> ";
				$bet=1;
				$sbet = 1;
				        //new_delo
				  		    		$rec['owner']=$user[id];
								$rec['owner_login']=$user[login];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money'];
								$rec['target']=0;
								$rec['target_login']='Свиток подгона';
								$rec['type']=78;
								$rec['sum_kr']=0;
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								$rec['item_id']=get_item_fid($dress);
								$rec['item_name']=$dress['name'];
								$rec['item_count']=1;
								$rec['item_type']=$dress['type'];
								$rec['item_cost']=$dress['cost'];
								$rec['item_dur']=$dress['duration'];
								$rec['item_maxdur']=$dress['maxdur'];
								$rec['item_ups']=$dress['ups'];
								$rec['item_unic']=$dress['unic'];
								$rec['item_incmagic']=$dress['includemagicname'];
								$rec['item_incmagic_count']=$dress['includemagicuses'];
								$rec['item_arsenal']='';
								$rec['aitem_id']=get_item_fid($svitok);
								add_to_new_delo($rec); //юзеру

				if (olddelo==1)
				{
				mysql_query("INSERT INTO `delo` (`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$_SESSION['uid']}','Подогнана вещь \"".$dress['name']."\" id:(cap".$dress['id'].") [".$dress['duration']."/".$dress['maxdur']."] у \"".$user['login']."\" бесплатно свитком ',99,'".time()."');");
				}
			}
			else {
				echo "<font color=red><b>Произошла ошибка!<b></font>";
			}

		}
	   }
	   else
	   {
	   echo "<font color=red><b>У Вас нет нужного свитка<b></font>";
	   }
	}
	else
	{
	echo "<font color=red><b>Неправильное имя предмета <b></font>";
	}

}
?>