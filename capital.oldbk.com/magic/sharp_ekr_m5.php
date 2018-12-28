<?php
if ($_POST['target']!='')
{
$need_str[1]='Заточка на ножи';
$need_str[11]='Заточка на топоры';
$need_str[12]='Заточка на  дубины,булавы';
$need_str[13]='Заточка на  мечи';

if ($user['battle'] > 0) {
	echo "Не в бою...";
} else {

		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE naem = 0 and present!='Арендная лавка' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}'  AND `owner` = '{$user['id']}' AND (arsenal_klan = '' OR arsenal_owner=1 ) AND `id` = '{$_POST['target']}' AND `sharped` = 0 and gmeshok = 0 LIMIT 1;"));
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `name` = 'Заточка на мечи +5' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND (arsenal_klan = '' OR arsenal_owner=1 ) AND `owner` = '{$user['id']}' LIMIT 1;"));


		if (($dress[otdel]==13) && $svitok)
		{
			if (mysql_query("UPDATE oldbk.`inventory` SET `sharped` = 1, `name` = CONCAT(`name`,'+5'), `minu` = `minu`+5, `maxu`=`maxu`+5, `nmech` = `nmech`+5, `cost` = `cost`+30, `nvinos` = `nvinos`+5 WHERE `id` = {$dress['id']} LIMIT 1;")) {
				echo "<font color=red><b>Предмет \"{$dress['name']}\" удачно заточен +5.<b></font> ";
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

	}
}
?>