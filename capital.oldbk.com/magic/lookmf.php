<?php
// магия "опознание мф"
if ($user['battle'] > 0) {
	echo "Не в бою...";
} else if ($user['ruines'] > 0) {
	if (!($_SESSION['uid'] >0)) header("Location: index.php");

	$rr = "";
	$q = mysql_query('SELECT * FROM users WHERE in_tower = 2 AND ruines = '.$user['ruines'].' AND `login` = "'.$_POST['target'].'"');
	if (mysql_num_rows($q) > 0) {
		$u = mysql_fetch_assoc($q);
		$user_dressed = mysql_fetch_array(mysql_query('SELECT sum(minu),sum(maxu),sum(mfkrit),sum(mfakrit),sum(mfuvorot),sum(mfauvorot),sum(bron1),sum(bron2),sum(bron3),sum(bron4),sum(ab_mf), sum(ab_bron), sum(ab_uron) FROM oldbk.`inventory` WHERE `dressed`=1 AND `owner` = \''.$u['id'].'\' LIMIT 1;'));	

		$arrmf[uvorota]=$user_dressed[4] + $u['lovk'] * 5;
		$arrmf[auvorota]=$user_dressed[5] + $u['lovk'] * 5 + $u['inta'] * 2;
		$arrmf[krita]=$user_dressed[2] + $u['inta'] * 5;
		$arrmf[akrita]=$user_dressed[3] + $u['inta'] * 5 + $u['lovk'] * 2;

		if ($user_dressed[11]>0) {
			$user_dressed[6]+=(int)($user_dressed[6]*($user_dressed[11]/100));
			$user_dressed[7]+=(int)($user_dressed[7]*($user_dressed[11]/100));
			$user_dressed[8]+=(int)($user_dressed[8]*($user_dressed[11]/100));
			$user_dressed[9]+=(int)($user_dressed[9]*($user_dressed[11]/100));	
		}


		$rr = '<b>'.$_POST['target'].'</b><br>Модификаторы:<br>уворот: '.$arrmf[uvorota].'% <br>антиуворот: '.$arrmf[auvorota].'%<br>крит: '.$arrmf[krita].'% <br>антикрит: '.$arrmf[akrita].'% ';

		$rr .= '<br>Броня<br>головы: '.$user_dressed[6].'<br>';
		$rr .= 'корпуса: '.$user_dressed[7].'<br>';
		$rr .= 'пояса: '.$user_dressed[8].'<br>';
		$rr .= 'ног: '.$user_dressed[9].'<br>';
	
		echo "<font color=red><b>Отчет об опознании у вас в рюкзаке в разделе Подарки<b></font>";

		mysql_query("INSERT INTO oldbk.`inventory` (`bs`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`letter`,`maxdur`,`isrep`,`bs_owner`)
		VALUES
		('1','{$_SESSION['uid']}','Отчет об опознании','200',1,0,'paper100.gif','{$rr}',1,0,2) ;");
		$bet = 1;
		$sbet = 1;
	} else {
		echo "<font color=red><b>Вы не можете опознать этого персонажа<b></font>";		
	}
} else {
	echo "<font color=red><b>Только для руин<b></font>";		
}
?>