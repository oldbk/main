<?php
if ($_POST['target']!='')
{
$need_str[1]='Заточка на ножи';
$need_str[11]='Заточка на топоры';
$need_str[12]='Заточка на  дубины,булавы';
$need_str[13]='Заточка на  мечи';

if ($user['battle'] > 0) {
	echo "Не в бою...";
} else	{
	if ($user['intel'] >= 4) {
		$int=71 + $user['intel'] - 4;
		if ($int>100){$int=100;}
	}
	else {$int=0;}
	if (rand(1,100) < $int) {

		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE naem = 0 and present!='Арендная лавка'  AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND (arsenal_klan = '' OR arsenal_owner=1 ) AND `owner` = '{$user['id']}' AND `id` = '{$_POST['target']}' AND `sharped` = 0 and gmeshok = 0 LIMIT 1;"));
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `name` = 'Заточка на ножи +3' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}'  AND (arsenal_klan = '' OR arsenal_owner=1 )  AND `owner` = '{$user['id']}' LIMIT 1;"));


		if (($dress[otdel]==1) && $svitok) 
		{
			if (mysql_query("UPDATE oldbk.`inventory` SET `sharped` = 1, `name` = CONCAT(`name`,'+3'), `minu` = `minu`+3, `maxu`=`maxu`+3, `cost` = `cost`+18, `nnoj` = `nnoj`+3, `ninta` = `ninta`+3 WHERE `id` = {$dress['id']} LIMIT 1;")) {
				echo "<font color=red><b>Предмет \"{$dress['name']}\" удачно заточен +3.<b></font> ";
				$bet=1;
				$sbet = 1;
				if(!$_SESSION['beginer_quest'][none]) 
				{				
					// квест
				        $last_q=check_last_quest(30);
				        if($last_q) 
					{
						quest_check_type_30($last_q,$user[id],6,1);
					}
				      
				}
			}
			else {
				echo "<font color=red><b>Произошла ошибка!<b></font>";
			}
		} 
		else if ($need_str[$dress[otdel]]!='')
			{
			//пишем ошибка пушки 
			echo "<font color=red><b>Для этого оружия необходим свиток \"{$need_str[$dress[otdel]]}\"<b></font>";			
			}
		else {
			echo "<font color=red><b>Неправильное имя предмета или неправильный свиток<b></font>";
		}
	} else
	{
		echo "<font color=red><b>Неудачно...<b></font>";
		$bet=1;
	}
}
}
?>