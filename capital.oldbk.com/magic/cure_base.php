<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");


if ($self_only) 
 {
 $_POST['target']=$user[login];
 }



$us = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '".mysql_real_escape_string($_POST['target'])."' LIMIT 1;"));

if ($user[battle] > 0) { $need_dress=' AND dressed=1 '; } else { $need_dress=''; }

//шансы сработки -
if ($CHAOS!=true)
   {
	$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}' ".$need_dress."  ;"));
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
   
   /*
   
	if (($us['battle']>0) AND ($us[id]!=$user[id])) {
		$q = mysql_query('SELECT * FROM effects WHERE owner = '.$us['id'].' and type = 795');
		if (mysql_num_rows($q) > 0) {
			$hps=($us[level]*500)-$us[stamina]+1000;
		} else {
			$hps=($us[level]*500)-$us[stamina];
		}
		if ($hps<$cure_value) {
			$cure_value=$hps;		
		}
	}
*/

if (!($NO_TEXT))  echo "<font color=red><B>";
if ((!$cure_value or $cure_value <= 0 or !$rowm['id']) AND ($CHAOS!=true) )
{
if (!($NO_TEXT)) echo "Произошла ошибка.";
}
else
{
if (!$us)  { if (!($NO_TEXT)) echo "Персонаж не онлайн!"; }
elseif ($us['battle'] != $user['battle']) { if (!($NO_TEXT)) echo "Персонаж находится в поединке!"; }
elseif ($user['room'] != $us['room'] && !$us['battle']) { if (!($NO_TEXT)) echo "Персонаж в другой комнате!"; }
elseif ($us['battle'] && ($us['battle_t']!=$user[battle_t])) { if (!($NO_TEXT)) echo "Нельзя лечить противников!"; }
elseif ($us['hp']<=0) { if (!($NO_TEXT)) echo "Нельзя лечить трупов!"; }
elseif ($us['hp']>=$us['maxhp']) { if (!($NO_TEXT)) echo "Персонаж здоров!"; }
elseif (can_hill($us))	{ if (!($NO_TEXT))  echo 'Вы временно не можете использовать восстановление жизни для '.$us[login]; }
elseif (( ($us['level']-$user['level']) > 2 ) AND ($us[battle] > 0) AND $user['in_tower'] == 0)   { if (!($NO_TEXT)) echo "Вы не можете вылечить в бою этого персонажа. Ваш уровень слишком мал."; }
elseif ($user['room']==200 || $us['room'] == 200) { if (!($NO_TEXT)) echo "В турнире нельзя использовать магию!"; }
elseif (($cure_value>180) AND (($user[room] > 240) AND ($user[room] < 254)) ) { if (!($NO_TEXT)) echo " Этот свиток нельзя тут использовать!"; }
elseif ($self_only AND $us['id'] != $_SESSION['uid']) { if (!($NO_TEXT)) echo "Данную магию можно использовать только на себя!"; }
else
{
	if (mt_rand(1,100) < $int)
	{
		if ($us[id]==9) { $cure_value=10000; } // тыква
		
		if ($user['sex'] == 1) { $action = ""; }
		else { $action="а"; }



		if ( ($us['battle']>0) AND ($us[id]!=$user[id]) )
			{$stm=", `stamina`=`stamina`+".$cure_value ; }else{$stm='';}
			
			
		$q = mysql_query('START TRANSACTION') or die();
		$q = mysql_query("SELECT * FROM  `users` WHERE `id` = ".$us['id']." and hp>0 FOR UPDATE") or die();
		if (mysql_num_rows($q) > 0) 
		{
		
				$us = mysql_fetch_array($q); // для точности
				if(($us['hp'] + $cure_value) > $us['maxhp'])
				{
					$hp = $us['maxhp'];
					$add_hp=$us['maxhp']-$us['hp'];
				}
				else
				{
					$hp = $us['hp'] + $cure_value;
					$add_hp=$cure_value;
				}		
		
			mysql_query("UPDATE `users` SET `hp` =`hp`+ ".$add_hp." ".$stm." WHERE `id` = ".$us['id']." and hp>0 ;") or die();
			if (mysql_affected_rows()>0)
			{
			$q = mysql_query('COMMIT') or die();
			
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
		
					if ($user['battle'] > 0)
					{
					
						//hidden edit by Fred
						if (( $us['hidden'] > 0 ) and ( $us['hiddenlog'] =='' ) )
						{
						$us[sex]=1;
							addlog($user['battle'],'!:H:'.time().":".nick_new_in_battle($user).":".(($user[sex]*100)+1).":".(($user[sex]*100)+1).":".(($us['id']!=$user['id'])?nick_new_in_battle($us):"").":".(($user[sex]*100)+1)."::::".$cure_value."|??:[??/??]\n");
						}
						else
						{
						$us = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE  `id` = '{$us['id']}' "));						
						addlog($user['battle'],'!:H:'.time().":".nick_new_in_battle($user).":".(($user[sex]*100)+1).":".(($user[sex]*100)+1).":".(($us['id']!=$user['id'])?nick_new_in_battle($us):"").":".(($user[sex]*100)+1)."::::".$cure_value.":[{$us[hp]}/{$us[maxhp]}]\n");
						}
			
					// апаем мемори
					if ($us[battle_t]==1) {  $boec_t1[$user[id]][hp]=$hp  ;  }
							elseif ($us[battle_t]==2) {   $boec_t2[$user[id]][hp]=$hp  ;  }
							elseif ($us[battle_t]==3) {   $boec_t3[$user[id]][hp]=$hp  ;  }				
							
					if ($us[id]==$user[id]) { $user[hp]=$hp ;   }
					}
		
		
							if (  ( ($cure_value==60) OR ($cure_value==90) ) AND ($user[id]!=$us['id'] ) AND ($user['battle'] > 0) )
							{
							 $baseexp = array( "0" => "5",	"1" => "10", "2" => "20", "3" => "30", "4" => "60", "5" => "120", "6" => "180",								"7" => "300",
									"8" => "450","9" => "600","10" => "1200","11" => "2400","12" => "4800",	"13" => "4800",	"14" => "4800",	"15" => "4800",
									"16" => "4800",	"17" => "4800",	"18" => "4800",	"19" => "4800",	"20" => "4800",	"21" => "4800",	); // Массив базового опыта
							//
							//setup koef
							$koeff[60]=0.7; //70%
							$koeff[90]=1; //100%
							$usr_lvl=$user[level];
							$tar_lvl=$us[level];
							$lk=(int)(($baseexp[$usr_lvl]*100/$baseexp[$tar_lvl] ) * $koeff[$cure_value] ) ;
							if ($lk>100) { $lk=100; }
							if ($lk<1)   { $lk=1;   }
							//ставим в очередь - остальное сделает триггер если надо
							mysql_query("INSERT INTO  `users_hill` SET `owner`='{$user[id]}',`target`='{$us[id]}',`khill`='{$lk}',`battle`='{$user[battle]}';") ;
				
							}


			if (!($NO_TEXT)) echo "Вы восстановили ".$cure_value." НР персонажу ".$us['login']."!";
			$MAGIC_OK=1;
			$bet=1;
			$sbet = 1;

			}
			else
			{
			$q = mysql_query('COMMIT') or die();
			if (!($NO_TEXT)) echo "Вы не смогли восстановить здоровье персонажу...";
			}
		
		}
			else
			{
			$q = mysql_query('COMMIT') or die();
			}

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