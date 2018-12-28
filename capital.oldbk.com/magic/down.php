<?php
// магия "шаг назад"
if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif (rand(1,100)!=1) {

	undressall($user['id']);
	mysql_query('DELETE from users_bonus where owner='.$user['id'].';');

	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	if (!($_SESSION['uid'] >0)) header("Location: index.php");

	try {
		if(\components\models\effect\AddZnaharStatEffect::isHave($user['id'])) {
			throw new Exception('Нельзя использовать вместе со статовым эффектом', 1);
		}


		if($user['sila']>3+$user['bpbonussila']){
			mysql_query("UPDATE `users` SET `stats`=`stats`+1,`sila` = `sila`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
		}
		if($user['inta']>3){
			mysql_query("UPDATE `users` SET `stats`=`stats`+1,`inta` = `inta`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
		}
		if($user['lovk']>3){
			mysql_query("UPDATE `users` SET `stats`=`stats`+1,`lovk` = `lovk`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
		}
		$add=0;
		if($user[level]==9){$add =1; }
		if($user[level]==10){$add =3; }

		if($user['vinos']>3+$user['level']+$add) {

			if($user['hp']<= ($user['maxhp']-6)) {
				mysql_query("UPDATE `users` SET `stats`=`stats`+1, `maxhp`=`maxhp`-'6',`vinos` = `vinos`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			}
			else {
				mysql_query("UPDATE `users` SET `stats`=`stats`+1, `maxhp`=`maxhp`-'6', `hp`=`hp`-'6',`vinos` = `vinos`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			}
		}
		if($user['intel'] >0) {

			$get_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and type='826' ; "));
			if ($get_baff[id]>0)
			{
				$user['intel']-=$get_baff['intel'];
				if($user['intel'] >0) {
					mysql_query("UPDATE `users` SET `stats`=`stats`+1,`intel` = `intel`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
				}
			}
			else
			{
				mysql_query("UPDATE `users` SET `stats`=`stats`+1,`intel` = `intel`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			}
		}
		if($user['mudra'] >0) {
			mysql_query("UPDATE `users` SET `stats`=`stats`+1,`mudra` = `mudra`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
		}
		check_hp($user[id],1);

		echo "<font color=red><b>Удачно использована магия \"Шаг назад\"<b></font>";
		$bet=1;
		$sbet = 1;
	} catch (Exception $ex) {
		if($ex->getCode() == 1) {
			echo "<font color=red><b>".$ex->getMessage()."<b></font>";
		}
	}
}
?>