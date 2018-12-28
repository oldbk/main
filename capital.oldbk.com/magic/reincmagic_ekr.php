<?php
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

include "magic/magicconf.php";

if ($user['battle'] > 0) {
	echo "Не в бою...";
} elseif ($user['lab'] >0) {
	echo "Неподходящий момент...";
} else {
	if(isset($_REQUEST['clearstored'])) {
		$_SESSION['scroll'] = null;
		header("Location: main.php?edit=1");
	}

	if(!$_SESSION['scroll']) {
		$_SESSION['scroll'] = $_POST['target'];

		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '".$_SESSION['scroll']."' and (cost>0 or ecost>0 or repcost > 0)  AND `owner` = '{$user['id']}' AND `dressed`=0 AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `setsale`=0 and labonly=0 and labflag=0 LIMIT 1;"));
		if ($svitok['getfrom'] == 43 && $svitok['repcost'] > 0 && $svitok['sowner'] > 0) {
			$sowner = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE id = '.$svitok['sowner']));
		?>
			<body onload="showitemschoice('Выберите предмет, в который перевстраивается свиток.<br><font color=red>Внимание! После встраивания этого свитка предмет станет привязан к персонажу <?=$sowner['login'];?></font>', 'reitems', 'main.php?edit=1&use=<?=$_GET['use']?>');">
		<?
		} else {
		?>
			<body onload="showitemschoice('Выберите предмет, в который перевстраивается свиток', 'reitems', 'main.php?edit=1&use=<?=$_GET['use']?>');">
		<?
		}
	} else {
		$svitok = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '".$_SESSION['scroll']."' and (cost>0 or ecost>0)  AND `owner` = '{$user['id']}' AND `dressed`=0 and labonly=0 and labflag=0 LIMIT 1;"));
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$user['id']}' AND present!='Арендная лавка' AND `id` = '{$_POST['target']}' AND `includemagic` > 0 AND includemagicuses > 50 AND `dressed`=0 AND `setsale`=0 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and labonly=0 and labflag=0  LIMIT 1;"));
		unset($_SESSION['scroll']);

		if(!$svitok) {
			echo "<font color=red><b>У вас нет такого свитка! [{$_SESSION['scroll']}]<b></font>";
		} elseif(!$dress OR $dress['type'] >= 12) {
			echo "<font color=red><b>У вас нет такого предмета! [{$_POST['target']}]<b></font>";
		} elseif(($svitok['getfrom'] == 43 && $svitok['sowner'] > 0 && $dress['sowner'] > 0 && ($dress['sowner'] != $svitok['sowner']))) {
			echo "<font color=red><b>Владелец предмета не совпадает<b></font>";
		} else {
			$incmagic = magicinf($svitok['magic']);
			if(!$incmagic['img'] || $svitok['dategoden'] > 0) {
				echo "<font color=red><b>Этот свиток нельзя перевстраивать в предметы!<b></font>";
			}
			elseif(!in_array($svitok['prototype'],$can_inc_magic)) {
			echo "<font color=red><b>Этот свиток нельзя встраивать в предметы!<b></font>";
			}
			 else {
				$new_kr_cost = $dress['cost'] - $dress['includemagiccost'] * 2;
				if($new_kr_cost < 0) { $new_kr_cost = 1; }
				$new_ekr_cost = $dress['ecost'] - $dress['includemagicekrcost'] * 2;
				if($new_ekr_cost < 0) { $new_ekr_cost = 0; }
				$shop_base = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.shop WHERE id = \''.$dress['prototype'].'\' LIMIT 1;'));

				if($shop_base[nlevel]<$dress[up_level]) {
					$shop_base[nlevel]=$dress[up_level];
				}
				//					`nlevel` = '{$shop_base['nlevel']}',
				if (mysql_query(
					"UPDATE oldbk.`inventory` SET
					`includemagic` = '',
					`includemagicdex` = '',
					`includemagicmax` = '',
					`includemagicname` = '',
					`includemagicuses` = '',
					`includemagiccost` = '',
					`includemagicekrcost` = '',
					`nintel` = '{$shop_base['nintel']}',
					`nmudra` = '{$shop_base['nmudra']}',
					`ngray` = '{$shop_base['ngray']}',
					`ndark` = '{$shop_base['ndark']}',
					`nlight` = '{$shop_base['nlight']}',
					`cost` = '{$new_kr_cost}',
					`ecost` = '{$new_ekr_cost}',
					`massa` = `massa` - 1,
					`sebescost` = `sebescost` - ".$incmagicprice."
					WHERE `id` = '{$dress['id']}' LIMIT 1;"))
				{
					// перевстраиваем
					$perezar=200;

					if ($svitok['repcost'] > 0) {
						$rtype = 3;
					} elseif ($svitok['ecost'] > 0) {
						$rtype = 2;
					} else {
						$rtype = 1;
					}

					$addsql = "";
					if ($svitok['getfrom'] == 43 && $svitok['repcost'] > 0 && $svitok['sowner'] > 0) {
						$addsql = ', sowner = '.$svitok['sowner'];
					}

//					".($dress['nlevel']<=$svitok['nlevel']?"`nlevel`='".$svitok['nlevel']."',":"")."
					$sql = "UPDATE oldbk.`inventory` SET
					".($dress['nintel']<=$svitok['nintel']?"`nintel`='".$svitok['nintel']."',":"")."
					".($dress['nmudra']<=$svitok['nmudra']?"`nmudra`='".$svitok['nmudra']."',":"")."
					".($dress['ngray']<=$svitok['ngray']?"`ngray`='".$svitok['ngray']."',":"")."
					".($dress['ndark']<=$svitok['ndark']?"`ndark`='".$svitok['ndark']."',":"")."
					".($dress['nlight']<=$svitok['nlight']?"`nlight`='".$svitok['nlight']."',":"")."
					`massa`=`massa`+1,`cost`=`cost`+'".$svitok['cost']."', `includemagic` = '".$svitok['magic']."', `includemagicdex` = '".$svitok['maxdur']."',
					`includemagicmax` = '".$svitok['maxdur']."', `includemagicname` = '".$svitok['name']."', `includemagicuses` = '".$perezar."',
					`includeprototype` = ".$svitok['prototype'].",
					`includerechargetype` = ".$rtype.",
					`sebescost` = `sebescost` + ".$incmagicprice."
					".$addsql."
					WHERE `id` = '{$dress['id']}' LIMIT 1;";

					if (mysql_query($sql)) {
						destructitem($svitok['id']);
						echo "<font color=red><b>Свиток \"".$svitok['name']."\" удачно перевстроен в \"".$dress['name']."\"<b></font>";
						$bet=1;
						$sbet = 1;
					}
				}
			}
		}
	}
}
?>