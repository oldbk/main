<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

mysql_query("DELETE FROM `lord_var` WHERE  `var`='cango' and `owner`='".$user[id]."';");
echo "Вы использовали Пропуск к Лорду Разрушителю...<i>посещение Лорда открыто.</i>";

/* if (($rowm['maxdur'] <= ($rowm['duration']+1)) and ($rowm['magic'])) {
	mysql_query('UPDATE oldbk.inventory SET magic = 0 WHERE id = '.$rowm['id']);
} else 
*/
//{
	$bet=1;
	$sbet = 1;
//}
?>