<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if($rowm['magic'] == 1030) {
	// екровая ёлка
	$q = mysql_query('SELECT * FROM eshop WHERE id = 55510350');
} elseif ($rowm['magic'] == 1031) {
	// артовая ёлка	
	$q = mysql_query('SELECT * FROM eshop WHERE id = 55510351');
} else {
	die();
}

$dress=mysql_fetch_array($q);
if (!$dress) die();

$dress['sowner'] = $user['id'];
$dress['goden'] = 7;
$dress['present'] = "Администрация";

$ny_events_cur_m = date("m");
$ny_events_cur_y = date("Y");

$elkagoden = $ny_events_cur_m == 12 ? mktime(23,59,59,2,29,$ny_events_cur_y+1) : mktime(23,59,59,2,29,$ny_events_cur_y);
$elkatime = time()+($dress['goden']*3600*24);
if ($elkatime > $elkagoden) {
	$elkatime = $elkagoden;
}

mysql_query("
	INSERT INTO oldbk.`inventory`
	(
	`prototype`,`owner`,`name`,`ghp`,`type`,
	`otdel`,`massa`,`cost`,`ecost`,`img`,`maxdur`,
	`isrep`,`nlevel`,`mfkrit`,`mfakrit`,`mfuvorot`,
	`mfauvorot`,`gsila`,`glovk`,`ginta`,`gintel`,`gnoj`,
	`gtopor`,`gdubina`,`gmech`,`bron1`,`bron2`,
	`bron3`,`bron4`,
	`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
	`nsila`,`nlovk`,`ninta`,
	`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,
	`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,
	`nearth`,`nlight`,`ngray`,`ndark`,
	`maxu`,`minu`,`dategoden`,`goden`,`includemagic`,
	`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,
	`idcity`,`gmeshok`,`present`,`stbonus`,`mfbonus`,`ab_mf`,`ab_bron`,`ab_uron`,`ekr_flag`,`unik`
	)
	VALUES
	(
	'{$dress['id']}','{$_SESSION['uid']}','{$dress['name']}','{$dress['ghp']}','3',
	'6',1,'{$dress['cost']}','{$dress['ecost']}','{$dress['img']}',{$dress['maxdur']},
	1,{$dress['nlevel']},'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}',
	'{$dress['mfauvorot']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['gnoj']}',
	'{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}',	'{$dress['bron1']}','{$dress['bron2']}',
	'{$dress['bron3']}','{$dress['bron4']}',
	'{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}',
	'{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}',
	'{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}',
	'{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}',
	'{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
	'{$dress['maxu']}','{$dress['minu']}','".($elkatime)."','{$dress['goden']}','{$dress['includemagic']}',
	'{$dress['includemagicdex']}','{$dress['includemagicmax']}','{$dress['includemagicname']}','{$dress['includemagicuses']}','{$dress['includemagiccost']}', '{$dress['includemagicekrcost']}',
	'{$user[id_city]}','{$dress[gmeshok]}','{$dress[present]}','{$dress[stbonus]}','{$dress[mfbonus]}','{$dress[ab_mf]}','{$dress[ab_bron]}','{$dress[ab_uron]}','{$dress[ekr_flag]}',2
)
") or die();

echo "Елка успешно доставлена Вам в рюкзак!";
$bet = 1;
$sbet = 1;

?>