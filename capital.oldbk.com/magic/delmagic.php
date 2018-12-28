<?php
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

include "magic/magicconf.php";

if ($user['battle'] > 0) {
	echo "Не в бою...";
}
elseif ($user['lab'] ==1) {
	echo "Неподходящий момент...";
}
else
{
	if(isset($_REQUEST['clearstored']))
	{
		$_SESSION['scroll'] = null;
		header("Location: main.php?edit=1");
	}
	$int = 99;
	if (rand(1,100) <= $int) {
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$user['id']}' AND present!='Арендная лавка'  AND `id` = '{$_POST['target']}' AND `includemagic` > 0 AND `dressed`=0 AND otdel!=6 AND `setsale`=0 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) LIMIT 1;"));
		$_SESSION['scroll'] = null;
		if(!$dress OR $dress['type'] >= 12)
		{
			echo "<font color=red><b>У вас нет такого предмета! [{$_POST['target']}]<b></font>";
		}
		else
		{
			$new_kr_cost = $dress['cost'] - $dress['includemagiccost'] * 2;
			if($new_kr_cost < 0) { $new_kr_cost = 1; }
			$new_ekr_cost = $dress['ecost'] - $dress['includemagicekrcost'] * 2;
			if($new_ekr_cost < 0) { $new_ekr_cost = 0; }
			$incmagic = magicinf($dress['includemagic']);
			$shop_base = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.shop WHERE id = \''.$dress['prototype'].'\' LIMIT 1;'));
		
			if($shop_base[nlevel]<$dress[up_level])
			{
              			    $shop_base[nlevel]=$dress[up_level];
			}
//`nlevel` = '{$shop_base['nlevel']}',
			if (mysql_query(
			"UPDATE oldbk.`inventory` SET
			`includemagic` = '',
			`includemagicdex` = '',
			`includemagicmax` = '',
			`includemagicname` = '',
			`includemagicuses` = '',
			`includemagiccost` = '',
			`includemagicekrcost` = '',
			`includerechargetype` = 0,
			`includeprototype` = 0,
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
				echo "<font color=red><b>Магия \"".$incmagic['name']."\" удачно убрана из \"".$dress['name']."\"<b></font>";
				$bet=1;
				$sbet = 1;
			}
		}

	}
	else
	{
		echo "<font color=red><b>Cвиток рассыпался в ваших руках...<b></font>";
		$bet=1;
		$_SESSION['scroll'] = null;
	}
}
?>
