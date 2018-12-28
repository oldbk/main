<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$q = mysql_query("SELECT * FROM `ruines_var` WHERE  `var`='cango' and `owner`='".$user[id]."';");
if (mysql_num_rows($q) > 0) {
	$pr = mysql_fetch_assoc($q);

	if ($pr['val']-time() > 8*3600) {
		echo "Что-то не сработало...";
	} else {
		mysql_query("DELETE FROM `ruines_var` WHERE  `var`='cango' and `owner`='".$user[id]."';");
		echo "Вы использовали Пропуск в Руины...<i>посещение руин открыто.</i>";
		$bet=1;
		$sbet = 1;
	}
} else {
	echo "Руины и так открыты для вас...</i>";
}
?>