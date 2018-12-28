<?php
	session_start();


	include "connect.php";
	include "functions.php";

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';

	if ($user['id'] != "7937" && $user['id'] != "648" && $user['klan'] != "radminion" && $user['klan'] != "Adminion") die("");

	$exists = false;
	
	$q = mysql_query('SELECT owner,tempowner FROM rentalshop WHERE tempowner != 0');
	while($u = mysql_fetch_assoc($q)) {
		$q2 = mysql_query('SELECT * FROM oldbk.users WHERE (id = '.$u['owner'].' or id = '.$u['tempowner'].') and block != 1 and align != 4');
		if (mysql_num_rows($q2) == 2) {
			$q2 = mysql_query('SELECT * FROM avalon.users WHERE (id = '.$u['owner'].' or id = '.$u['tempowner'].') and block != 1 and align != 4');

			if (mysql_num_rows($q2) == 2) {		
				$u1 = mysql_fetch_assoc($q2);
				$u2 = mysql_fetch_assoc($q2);
		
				if ($u1['pass'] == $u2['pass'] || $u1['email'] == $u2['email'] || $u1['borndate'] == $u2['borndate']) {
					$reason = "";
					if ($u1['pass'] == $u2['pass']) {
						$reason = "пароль";
					}
					if ($u1['email'] == $u2['email']) {
						$reason = "емейл";
					}
					if ($u1['borndate'] == $u2['borndate']) {
						$reason = "дата рождения";
					}
		
					$exists = true;
					echo $u1['login'].' => '.$u2['login'].' Причина: '.$reason.'<br>';
				}
			}
		}
	}


	if (!$exists) {
		echo "Нет нахождений<br>";
	}
?>