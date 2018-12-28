<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat1='maxhp';
$addvalue1=$user['maxhp'];
                   
$addtxt='Уровень жизни +100%';

$gf=mysql_fetch_array(mysql_query("select * from users_bonus where owner='{$user[id]}' ;"));
if ($gf['maxhp'] == 0) {
	include "food_base_obed.php";
	if ($bet==1)
		{
		$baff_type=3055;
		$baff_name="Большой ужин викинга";
		mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}', `time`='1999999999', `owner`='{$user[id]}'  ;");
		}	
} else {
	 echo "<font color=red>Вы уже подкрепились этим...можно попробовать после боя...</font>";	
}

?>
