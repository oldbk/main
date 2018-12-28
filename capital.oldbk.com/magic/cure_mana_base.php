<?php
if 	(!($_SESSION['uid'] >0)) 
	{
	header("Location: index.php");
	die("error");
	}

$us = mysqli_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '".$_SESSION['uid']."' LIMIT 1;")); 


if ($user[battle] > 0) { $need_dress=' AND dressed=1 '; } else { $need_dress=''; }

//шансы сработки -
if ($CHAOS!=true)
   {
	$rowm = mysqli_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}' ".$need_dress."  ;"));
	if((int)$rowm['magic'] > 0)
	{
	$magic = magicinf($rowm['magic']);

	}
	else
	{
	$magic = magicinf($rowm['includemagic']);
	}
	
         //8 0
	if ($user['intel'] >= $rowm['nintel'])
	{
		$int = $magic['chanse'] + ($user['intel'] - $rowm['nintel']) * 3;
		$int = $int * 0.85; //8/08/2012 - Ден+Алина правка процентов
		if ($int > 98){ $int = 99; }
	}
	else
	{
	$int = 0;
	}
	if ($FIX==1) {$int = 101;}
	if ($magic['chanse']>=100)  {$int = 101;}
   }
   else
   {
   $int = 101;
   }
   

if (!($NO_TEXT))  echo "<font color=red><B>";
if ((!$cure_value or $cure_value <= 0 or !$rowm['id']) AND ($CHAOS!=true) )
{
if (!($NO_TEXT)) echo "Произошла ошибка.";
}
else
{
if (!$us)  { if (!($NO_TEXT)) echo "Персонаж не онлайн!"; }
//elseif ($us['battle'] != $user['battle']) { if (!($NO_TEXT)) echo "Персонаж находится в поединке!"; }
//elseif ($user['room'] != $us['room'] && !$us['battle']) { if (!($NO_TEXT)) echo "Персонаж в другой комнате!"; }
//elseif ($us['battle'] && ($us['battle_t']!=$user[battle_t])) { if (!($NO_TEXT)) echo "Нельзя восстанавливать противников!"; }
elseif ($us['hp']<=0) { if (!($NO_TEXT)) echo "Нельзя пополнить ману трупу!"; }
elseif ($us['mana']>=$us['maxmana']) { if (!($NO_TEXT)) echo "У персонажа полный запас маны!"; }
//elseif (( ($us['level']-$user['level']) > 2 ) AND ($us[battle] > 0) AND $user['in_tower'] == 0)   { if (!($NO_TEXT)) echo "Вы не можете восстанавливать в бою этого персонажа. Ваш уровень слишком мал."; }
elseif ($user['room']==200 || $us['room'] == 200) { if (!($NO_TEXT)) echo "В турнире нельзя использовать магию!"; }
//elseif (($cure_value>180) AND (($user[room] > 240) AND ($user[room] < 254)) ) { if (!($NO_TEXT)) echo " Этот свиток нельзя тут использовать!"; }
//elseif ($self_only AND $us['id'] != $_SESSION['uid']) { if (!($NO_TEXT)) echo "Данную магию можно использовать только на себя!"; }
else
{
	if (mt_rand(1,100) < $int)
	{
		if ($us[id]==9) { $cure_value=10000; } // тыква
		
		if ($user['sex'] == 1) { $action = ""; }
		else { $action="а"; }



		$q = mysql_query('START TRANSACTION') or die("error 1");
		$q = mysql_query("SELECT * FROM  `users` WHERE `id` = ".$us['id']." and hp>0 FOR UPDATE") or die("error 2");
		if (mysql_num_rows($q) > 0) 
		{
		
				$us = mysqli_fetch_array($q); // для точности
				if(($us['mana'] + $cure_value) > $us['maxmana'])
				{
					$mana = $us['maxmana'];
					$add_mana=$us['maxmana']-$us['mana'];
				}
				else
				{
					$mana = $us['mana'] + $cure_value;
					$add_mana=$cure_value;
				}		
				
			mysql_query("UPDATE `users` SET `mana` =`mana`+ ".$add_mana."  WHERE `id` = ".$us['id']." and hp>0 ;") or die("errror 3");
			if (mysql_affected_rows()>0)
			{
			$q = mysql_query('COMMIT') or die("error 4");
			
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
		
					if ($user['battle'] > 0)
					{
					
						//hidden edit by Fred
						if (( $us['hidden'] > 0 ) and ( $us['hiddenlog'] =='' ) )
						{
						$us[sex]=1;
						addlog($user['battle'],'!:N:'.time().":".nick_new_in_battle($user).":".(($user[sex]+200)).":".(($user[sex]+200)).":".(($us['id']!=$user['id'])?nick_new_in_battle($us):"").":".(($user[sex]+200))."::::".$cure_value."|??:[??/??]\n");
						}
						else
						{
						$us = mysqli_fetch_array(mysql_query("SELECT * FROM `users` WHERE  `id` = '{$us['id']}' "));						
						addlog($user['battle'],'!:N:'.time().":".nick_new_in_battle($user).":".(($user[sex]+200)).":".(($user[sex]+200)).":".(($us['id']!=$user['id'])?nick_new_in_battle($us):"").":".(($user[sex]+200))."::::".$cure_value.":[{$us[mana]}/{$us[maxmana]}]\n");
						}
			
					// апаем мемори
					if ($us[battle_t]==1) {  $boec_t1[$user[id]][mana]=$mana  ;  }
							elseif ($us[battle_t]==2) {   $boec_t2[$user[id]][mana]=$mana  ;  }
							elseif ($us[battle_t]==3) {   $boec_t3[$user[id]][mana]=$mana  ;  }				
							
					if ($us[id]==$user[id]) { $user[mana]=$mana ;   }
					}
		
		
							


			if (!($NO_TEXT)) echo "Вы восстановили ".$cure_value." MР персонажу ".$us['login']."!";
			$MAGIC_OK=1;
			$bet=1;
			$sbet = 1;

			}
			else
			{
			$q = mysql_query('COMMIT') or die("error 5");
			if (!($NO_TEXT)) echo "Вы не смогли восстановить ману персонажу...";
			}
		
		}
			else
			{
			$q = mysql_query('COMMIT') or die("error 6");
			}

		/* отсвил на потом
		try {
			global $app;

			$UserObj = new \components\models\User($user);
			$Quest = $app->quest->setUser($UserObj)->get();

			$Checker = new \components\Component\Quests\check\CheckerHill();
			$Checker->value = $cure_value;
			$Checker->battle_id = $UserObj->battle;
			if (($Item = $Quest->isNeed($Checker)) !== false) {
				$Quest->taskUp($Item);
			}

			unset($UserObj);
			unset($Quest);
		} catch (Exception $ex) {
			\components\Helper\FileHelper::writeException($ex, 'cure_base');
		}
		*/

	}
	else
	{

		if (!($NO_TEXT)) echo "Свиток рассыпался в ваших руках...";
		$bet=1;
	}
}
}

if (!($NO_TEXT)) echo "</B></FONT>";
?>