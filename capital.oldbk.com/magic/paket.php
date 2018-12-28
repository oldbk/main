<?php

if (rand(1,100)!=1) {
	
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	$paket = mysql_fetch_array(mysql_query("SELECT `owner`,`id`,`name` FROM oldbk.`inventory` WHERE `id` = ".$_GET['use']." LIMIT 1;"));
	if ($paket[0] == $user['id']) {
		$inside = mysql_query("SELECT * FROM `paket` WHERE `id` = ".$paket[1].";");
		while ($row = mysql_fetch_array($inside)) {
			$ins = eval($row['eval']);
			mysql_query($ins);
			mysql_query("DELETE FROM `paket` WHERE `pid` = ".$row['pid'].";");
		}
		echo "<font color=red><b>Вы вскрыли \"".$paket[2]."\".<b></font> ";	
		destructitem($_GET['use']);
	} else {
		echo "<font color=red><b>Это не ваше...<b></font>";
	}
}
?>