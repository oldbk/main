<?


$grant_continue = false;
if(($_SERVER['PHP_SELF'] != '/_repair.php') && ($_SERVER['PHP_SELF'] != '/repair.php') && ($_SERVER['PHP_SELF'] !='/lab2.php') && ($_SERVER['PHP_SELF'] !='/new_rings.php') && ($_SERVER['PHP_SELF'] !='/fillmf.php'))
{
	if ($user['battle'] > 0) {
		echo "Не в бою...";
	}
	elseif ($user['lab'] == 1)
	{
		echo "Неподходящий момент...";
	}
	elseif(!$upgrade)
	{
		echo "<font color=red><b>Произошла ошибка!<b></font>";
	}
	elseif ($user['intel'] < $upgrade['nintel'])
	{
		echo "У вас недостаточно интеллекта...";
	}
	elseif(isset($_REQUEST['clearstored']))
	{
		//
	}
	else
	{
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$user['id']}' and (sowner=0 or sowner = '{$user['id']}')   AND `id` = '{$_POST['target']}' AND (`type` < 12 or `type` = 28) AND `dressed`=0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND name like '%[%'  AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 )
		AND (`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) LIMIT 1;"));
		if(!$dress OR (($dress['type'] >= 12) and ($dress['type']!=28) ))
		{
			echo "<font color=red><b>У вас нет такого предмета!<b></font>";
		}
		elseif($dress['up_level'] > 0 AND ($dress['up_level'] != $upgrade['level']))
		{
		echo $dress['up_level'] ;
		echo " !=  ";
		echo $upgrade['level'];
			echo "<font color=red><b>Эту вещь нельзя понизить до этого уровня!<b></font>";
		}
		elseif (($dress['stbonus'] < $upgrade['stat']) AND ($dress['mfbonus'] < $upgrade['mf']) AND  ($dress['type'] !=3)  )
		{
			echo "<font color=red><b>На предмете нет достаточно свободных статов или модификаторов, для понижения необходимо сбросить статы и модификаторы в ремотной мастерской!<b></font>";
		}
		else
		{
		$stop=false;
		if  ($dress['arsenal_klan'] != '')
			{
			if  ($dress['arsenal_klan'] != $user['klan'])			
				{
				$stop=true;				
				echo "<font color=red><b>Эта вещь не вашего клана!<b></font>";
				}
				else
				{
				$kln=mysql_fetch_array(mysql_query("select * from clans where short='{$user['klan']}' "));
					if ($kln['id']>0)
					{
						if ($kln['glava']!=$user['id'])						
						{
						$stop=true;				
						echo "<font color=red><b>Поднять или понизить уровень артефакта из арсенала, может только глава клана!<b></font>";					
						}
					}
					else
					{
					$stop=true;				
					echo "<font color=red><b>Нет такого клана!<b></font>";					
					}
				}
			} 
		if ($stop!=true)
		{
		// Внимание - дальше в строчке баг - нет поля -cost!!!- Мы знаем - и не трогаем потому что пиздец :) с) Fred
			$shop_base = mysql_fetch_array(mysql_query('SELECT name, maxdur FROM oldbk.shop WHERE id = \''.$dress['prototype'].'\' LIMIT 1;'));
			$is_mf = !(strpos($dress['name'], '(мф)') === false);

			//fix for sharped
			$sharp=explode("+",$dress['name']);
			if ((int)($sharp[1])>0) {$is_sharp="+".$sharp[1]; } else {$is_sharp='';}

			$upgrade['level']-=1;

//только для храмартов
			if (($upgrade['level']<=7) OR (($upgrade['level']<=8) AND ($dress['prototype']==283)||($dress['prototype']==284)||($dress['prototype']==18527) ) ) 
				{
				$newname = $shop_base['name']." ".(($is_mf) ? " (мф)" : "");				
				}
				else
				{
				$newname = $shop_base['name']." [".$upgrade['level']."]".(($is_mf) ? " (мф)" : "");
				}
		 	$newname=$newname.$is_sharp;
			
		$nlevel = $upgrade['level'];

		$query = "UPDATE oldbk.inventory SET
		`up_level` = '{$upgrade['level']}',
		"."`maxdur` = `maxdur` - '{$upgrade['duration']}',"."
		".(($dress['type'] == 3)?(($dress['minu'] > 0) ? "`minu` = `minu` - '{$upgrade['udar']}', " : "")."
		".(($dress['maxu'] > 0) ? "`maxu` = `maxu` - '{$upgrade['udar']}', " : "")
		:
	  	(($dress['ghp'] > 0) ? "`ghp` = `ghp` - '{$upgrade['hp']}', " : "")."
		".(($dress['bron1'] > 0) ? "`bron1` = `bron1` - '{$upgrade['bron']}', " : "")."
		".(($dress['bron2'] > 0) ? "`bron2` = `bron2` - '{$upgrade['bron']}', " : "")."
		".(($dress['bron3'] > 0) ? "`bron3` = `bron3` - '{$upgrade['bron']}', " : "")."
		".(($dress['bron4'] > 0) ? "`bron4` = `bron4` - '{$upgrade['bron']}', " : "")."
		".(($dress['gsila'] > 0 OR $dress['glovk'] OR $dress['ginta'] OR $dress['gintel'] OR $dress['stbonus']) ? "`stbonus` = `stbonus` - '{$upgrade['stat']}', " : "")."
		".(($dress['mfkrit'] > 0 OR $dress['mfakrit'] OR $dress['mfuvorot'] OR $dress['mfauvorot'] OR $dress['mfbonus'] ) ? "`mfbonus` = `mfbonus` - '{$upgrade['mf']}', " : "")
		)."
		".(($dress['nsila'] > 0) ? "`nsila` = `nsila` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['nlovk'] > 0) ? "`nlovk` = `nlovk` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['ninta'] > 0) ? "`ninta` = `ninta` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['nvinos'] > 0) ? "`nvinos` = `nvinos` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['nnoj'] > 0) ? "`nnoj` = `nnoj` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['ntopor'] > 0) ? "`ntopor` = `ntopor` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['ndubina'] > 0) ? "`ndubina` = `ndubina` - '{$upgrade['nparam']}', " : "" )."
		".(($dress['nmech'] > 0) ? "`nmech` = `nmech` - '{$upgrade['nparam']}', " : "" )."
		".(($upgrade['destiny']) ? "`present` = 'Храм Древних', " : "" )."
		`nlevel` = '{$nlevel}',
		`name` = '{$newname}',
		`cost` = `cost` - '".round($shop_base['cost']/2, 0)."'
		WHERE id = '{$dress['id']}' AND `owner` = '{$user['id']}';";

			if(mysql_query($query))
			{
				$bet=1;
				$sbet = 1;
				echo "<font color=red><b>Вещь \"{$dress['name']}\" удачно понижена до уровня {$upgrade['level']}<b></font>";
				/*
				if(!$_SESSION['beginer_quest'][none]) 
				{				
					// квест
				        $last_q=check_last_quest(30);
				        if($last_q) 
					{
					//	quest_check_type_30($last_q,$user[id],4,1);
					}
				      
				}
				*/
				
			}
			else
			{
				echo "<font color=red><b>Произошла ошибка!<b></font>";
			}
		   }
		}
	}
}
?>