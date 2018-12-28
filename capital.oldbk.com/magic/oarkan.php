<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");	

function CheckOpDayM() {
	$q = mysql_query('SELECT * FROM oldbk.variables WHERE var = "opposition_today"');
	if (mysql_num_rows($q) > 0) {
		$v = mysql_fetch_assoc($q);
		if ($v !== FALSE) {
			if (date("d/m/Y",time()+(24*3600*4)) == $v['value']) {
				if (date("H") >= 6) {
					return true;
				}
			}
		}
	}
	return false;
}


require "config_ko.php";

if (CheckOpDayM() || ((time()>$KO_start_time46) and (time()<$KO_fin_time46))) { 
	$OARKAN = true;
	require_once('opposition.php');
} else {
	echo 'Сейчас не время Противостояния...';
	return;
}
?>