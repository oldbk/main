<?php
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

include "magic/magicconf.php";

if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif ($user['intel'] < 17) {
	echo "У вас недостаточно интеллекта...";
} elseif ($user['lab'] >0) {
	echo "Неподходящий момент...";
} else {
	if(isset($_REQUEST['clearstored'])) {
		$_SESSION['scroll'] = null;
		header("Location: main.php?edit=1");
	}
	$int = 80 + $user['intel'] - 17;
	if ($int > 100) { $int = 100; }

	if(!$_SESSION['scroll']) {
		$_SESSION['scroll'] = $_POST['target'];
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '".$_SESSION['scroll']."' and (cost>0 or ecost>0 or repcost > 0)  AND `owner` = '{$user['id']}' AND `dressed`=0 AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `setsale`=0 and labonly=0 and labflag=0 LIMIT 1;"));
		if ($svitok['getfrom'] == 43 && $svitok['repcost'] > 0 && $svitok['sowner'] > 0) {
			$sowner = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE id = '.$svitok['sowner']));
		?>
			<body onload="showitemschoice('Выберите предмет, в который встраивается свиток.<br><font color=red>Внимание! После встраивания этого свитка предмет станет привязан к персонажу <?=$sowner['login'];?></font>', 'items', 'main.php?edit=1&use=<?=$_GET['use']?>');">
		<?
		} else {
		?>
			<body onload="showitemschoice('Выберите предмет, в который встраивается свиток', 'items', 'main.php?edit=1&use=<?=$_GET['use']?>');">
		<?
		}
	} elseif (rand(1,100) <= $int) {
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '".$_SESSION['scroll']."' and (cost>0 or ecost>0 or repcost > 0)  AND `owner` = '{$user['id']}' AND `dressed`=0 AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `setsale`=0 and labonly=0 and labflag=0 LIMIT 1;"));
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE naem = 0 and `owner` = '{$user['id']}' AND present!='Арендная лавка'  AND `id` = '{$_POST['target']}' AND `includemagic` = 0 AND `dressed`=0 AND `setsale`=0 and labonly=0 and labflag=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND `prokat_idp`=0 and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) LIMIT 1;"));

		unset($_SESSION['scroll']);
		if(!$svitok) {
			echo "<font color=red><b>У вас нет такого свитка! [{$_SESSION['scroll']}]<b></font>";
		} elseif(!$dress OR $dress['type'] >= 12){
			echo "<font color=red><b>У вас нет такого предмета! [{$_POST['target']}]<b></font>";
		} elseif(($svitok['getfrom'] == 43 && $svitok['sowner'] > 0 && $dress['sowner'] > 0 && ($dress['sowner'] != $svitok['sowner']))) {
			echo "<font color=red><b>Владелец предмета не совпадает<b></font>";
		} 
		else {
			$incmagic = magicinf($svitok['magic']);
			if(!$incmagic['img'] || $svitok['dategoden'] > 0 || $svitok['duration'] > 0 ) {
				echo "<font color=red><b>Этот свиток нельзя встраивать в предметы!<b></font>";
			} 
			elseif(!in_array($svitok['prototype'],$can_inc_magic)) {
			echo "<font color=red><b>Этот свиток нельзя встраивать в предметы!<b></font>";
			}
			else {
				// встраиваем
				$perezar=$user['intel'] + 50;
				if ($perezar > 500) $perezar = 500; 

				if ($svitok['repcost'] > 0) {
					$rtype = 3;
				} elseif ($svitok['ecost'] > 0) {
					$rtype = 2;
				} else {
					$rtype = 1;
				}

				$addsql = "";
				
				if (($svitok['getfrom'] == 43 && $svitok['repcost'] > 0 && $svitok['sowner'] > 0) and ($dress['arsenal_klan'] != '') )
 				{
					echo "<font color=red><b>Этот свиток нельзя встроить в предмет арсенала клана!<b></font>";
				}
				else
				{
								if ($svitok['getfrom'] == 43 && $svitok['repcost'] > 0 && $svitok['sowner'] > 0) 
								{
									$addsql = ', sowner = '.$svitok['sowner'];
								}
//									".($dress['nlevel']<$svitok['nlevel']?"`nlevel`='".$svitok['nlevel']."',":"")."				
								$sql = "UPDATE oldbk.`inventory` SET ".
									($dress['nintel']<$svitok['nintel']?"`nintel`='".$svitok['nintel']."',":"")."
									".($dress['nmudra']<$svitok['nmudra']?"`nmudra`='".$svitok['nmudra']."',":"")."
									".($dress['ngray']<$svitok['ngray']?"`ngray`='".$svitok['ngray']."',":"")."
									".($dress['ndark']<$svitok['ndark']?"`ndark`='".$svitok['ndark']."',":"")."
									".($dress['nlight']<$svitok['nlight']?"`nlight`='".$svitok['nlight']."',":"")."
									`massa`=`massa`+1,`cost`=`cost`+'".$svitok['cost']."',
									`includemagic` = '".$svitok['magic']."',
									`includemagicdex` = '".$svitok['maxdur']."',
									`includemagicmax` = '".$svitok['maxdur']."',
									`includemagicname` = '".$svitok['name']."',
									`includemagicuses` = '".$perezar."',
									`includeprototype` = ".$svitok['prototype'].",
									`includerechargetype` = ".$rtype.",
									`sebescost` = `sebescost` + ".$incmagicprice."
									".$addsql."
									WHERE `id` = '{$dress['id']}' LIMIT 1;";
				
								if (mysql_query($sql)) {
									destructitem($svitok['id']);
				
									echo "<font color=red><b>Свиток \"".$svitok['name']."\" удачно встроен в \"".$dress['name']."\"<b></font>";
									$bet=1;
									$sbet = 1;
									if(!$_SESSION['beginer_quest'][none]) {				
										// квест
									        $last_q=check_last_quest(30);
									        if($last_q) {
											quest_check_type_30($last_q,$user[id],7,1);
										}
									      
									}
								}
				}
			}
		}
	} else {
		echo "<font color=red><b>Cвиток рассыпался в ваших руках...<b></font>";
		$bet = 1;
		$_SESSION['scroll'] = null;
	}
}
?>
