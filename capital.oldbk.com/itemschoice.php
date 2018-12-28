<?
if (!isset($_REQUEST['get'])) die();
session_start();

if (!($_SESSION['uid'] > 0)) die();

include "connect.php";
include "functions.php";

$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

$items = null;
if(isset($_REQUEST['scrolls']))
{
	// AND  m.id<>49  AND m.id<>212
	$items = mysql_query("SELECT inv.* FROM oldbk.`inventory` inv
								inner join magic m
								on m.id = inv.magic
						 WHERE inv.owner = '{$_SESSION['uid']}' AND m.img <> ''

						 	   AND inv.type = 12 AND (cost>0 or ecost>0) AND setsale = 0 AND inv.dressed = 0 and labonly=0 and labflag=0
						 	   AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and dategoden = 0
				");
	$action = "выбрать";
	$zero_count_message = "У вас нет свитков для встройки.";
}
elseif(isset($_REQUEST['rihtbron']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory` WHERE bs_owner=0 AND  prototype != 20000 AND prokat_idp = 0 AND present!='Арендная лавка'
	AND `owner` = '{$_SESSION['uid']}' AND (sowner = 0 or sowner = ".$user['id'].") AND `dressed` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND name like '%(мф)%'  AND `setsale`=0 and naem = 0 ORDER by `update` DESC; ");
	$action = "отрихтовать +1 броня";
	$zero_count_message = "У вас нет вещей для рихтовки.";
}
elseif(isset($_REQUEST['rihthp4204']) || isset($_REQUEST['rihthp4205']) || isset($_REQUEST['rihthp4206']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory` WHERE bs_owner=0 AND  prototype != 20000 AND prokat_idp = 0 AND present!='Арендная лавка'
	AND `owner` = '{$_SESSION['uid']}' AND (sowner = 0 or sowner = ".$user['id'].") AND `dressed` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND name like '%(мф)%'  AND `setsale`=0 and naem = 0 ORDER by `update` DESC; ");

	$zero_count_message = "У вас нет вещей для рихтовки.";
}
elseif(isset($_REQUEST['changemagic']))
{
	// AND  m.id<>49  AND m.id<>212
	$items = mysql_query("SELECT * FROM oldbk.inventory WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND includemagic > 0 AND present!='Арендная лавка' AND includemagicuses > 0 AND dressed = 0 AND setsale = 0 AND type < 12 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and naem = 0");
	$action = "заменить";
	$zero_count_message = "У вас нет вещей для перевстройки.";
}
elseif(isset($_REQUEST['add_runs_exp']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory WHERE owner = '{$_SESSION['uid']}' AND sowner = '{$_SESSION['uid']}' AND dressed = 0 AND present!='Арендная лавка' AND dressed = 0 AND setsale = 0 AND type =30 AND `prokat_idp`=0 and add_time>ups ORDER BY `UPDATE` DESC;");
	$action = "использовать";
	$zero_count_message = "У вас нет подходящих рун.";
}
elseif(isset($_REQUEST['moveemagic']))
{
	// AND  m.id<>49  AND m.id<>212
	$items = mysql_query("SELECT * FROM oldbk.inventory WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND ((prototype<55510301) OR (prototype>55510401) ) AND includemagic > 0 AND present!='Арендная лавка' AND includemagicuses > 0 AND dressed = 0 AND setsale = 0 AND type < 12 AND  `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and naem = 0");
	$action = "вынуть";
	$zero_count_message = "У вас нет вещей для перевстройки.";
}
elseif(isset($_REQUEST['makefreeup_bron']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory` WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0  AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}')  AND  arsenal_klan = '' AND `labonly`=0 AND `type`=4 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_boots']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory` WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}') AND arsenal_klan = '' AND `labonly`=0 AND `type`=11 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_kulon']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}') AND arsenal_klan = '' AND `labonly`=0 AND `type`=2 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_perchi']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}') AND arsenal_klan = '' AND `labonly`=0 AND `type`=9 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_ring']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}') AND arsenal_klan = '' AND `labonly`=0 AND `type`=5 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_sergi']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}') AND arsenal_klan = '' AND `labonly`=0 AND `type`=1 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_shit']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}')  AND arsenal_klan = '' AND `labonly`=0 AND `type`=10 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['makefreeup_shlem']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE (`mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0) AND present!='Арендная лавка' AND setsale = 0 AND `dressed`=0 AND `prokat_idp`=0 AND `bs_owner`=0 AND ( `sowner`=0 OR `sowner`='{$_SESSION['uid']}')  AND arsenal_klan = '' AND `labonly`=0 AND `type`=8 AND owner = '{$_SESSION['uid']}' AND `name` LIKE '%(мф)%' and naem = 0");
	$action = "Подогнать";
	$zero_count_message = "У вас нет подходящих вещей.";
}
elseif(isset($_REQUEST['items']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND includemagic = 0 AND present!='Арендная лавка' AND dressed = 0 AND setsale = 0 AND type < 12 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0");
	$action = "встроить";
	$zero_count_message = "У вас нет вещей для встройки.";
}
elseif(isset($_REQUEST['itemssowner']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND includemagic = 0 AND present!='Арендная лавка' AND dressed = 0 AND setsale = 0 AND type < 12 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and (sowner = 0 or sowner = ".$user['id'].") and naem = 0");
	$action = "встроить";
	$zero_count_message = "У вас нет вещей для встройки.";
}
elseif(isset($_REQUEST['reitems']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND includemagic > 0 AND present!='Арендная лавка' AND includemagicuses > 50 AND dressed = 0 AND setsale = 0 AND type < 12 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and naem = 0");
	$action = "перевстроить";
	$zero_count_message = "У вас нет вещей для перевстройки.";
}
elseif(isset($_REQUEST['delitems']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND includemagic > 0 AND present!='Арендная лавка' AND dressed = 0 AND otdel!=6 AND setsale = 0 AND type < 12 AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) and naem = 0");
	$action = "убрать";
	$zero_count_message = "У вас нет вещей со встроенной магией.";
}
elseif(isset($_REQUEST['del_time']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = ".$_SESSION['uid']."
		 AND dressed = 0 AND prototype!=10000 AND prototype!=0 AND setsale = 0 AND present!='Арендная лавка' AND present!='' and dategoden>0 AND type = 200 and otdel in (7,71,73) AND goden>0 order by  add_time desc;");
	$action = "сохранить";
	$zero_count_message = "У вас нет вещей, завязанных временем.";
}
elseif(isset($_REQUEST['identitems']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND dressed = 0 AND present!='Арендная лавка' AND needident = 1 AND setsale = 0 AND `bs_owner`='{$user[in_tower]}' AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 );");
	$action = "идентифицировать";
	$zero_count_message = "У вас нет вещей для идентификации.";
}
elseif(isset($_REQUEST['upgrade_7']))                                                                               //AND nlevel<7
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND (`type` < 12 or `type` = 28) AND `otdel` != 6 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0
														AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level < 7 AND present!='Арендная лавка'
													AND (`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет вещей для улучшения.";
}
elseif(isset($_REQUEST['upgrade_8']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND (`type` < 12 or `type` = 28) AND `otdel` != 6 AND `dressed`=0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0
														AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level >= 7 AND up_level < 8 AND present!='Арендная лавка' AND
														(`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет вещей для улучшения.";
}
elseif(isset($_REQUEST['upgrade_9']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND (`type` < 12 or `type` = 28) AND `otdel` != 6 AND `dressed`=0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0
														AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' AND up_level >= 8 AND up_level < 9 AND
														(`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет вещей для улучшения.";
}
elseif(isset($_REQUEST['upgrade_10']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND (`type` < 12 or `type` = 28) AND `otdel` != 6 AND `dressed`=0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0
														AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' AND up_level >= 9 AND up_level < 10 AND
														(`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет вещей для улучшения.";
}
elseif(isset($_REQUEST['upgrade_11']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND (`type` < 12 or `type` = 28) AND `otdel` != 6 AND `dressed`=0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0
														AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' AND up_level >= 10 AND up_level < 11 AND
														(`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет вещей для улучшения.";
}
elseif(isset($_REQUEST['upgrade_12']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND (`type` < 12 or `type` = 28) AND `otdel` != 6 AND `dressed`=0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0
														AND `prokat_idp`=0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' AND up_level >= 11 AND up_level < 12 AND
														(`minu`>0 or `gsila` > 0 OR `glovk` > 0 OR `ginta` > 0 OR `gintel` > 0 OR `mfkrit`> 0 OR `mfakrit` > 0 OR `mfuvorot` > 0 OR `mfauvorot` > 0 OR `bron1` > 0 OR `bron2` > 0 OR `bron3` > 0 OR `bron4` > 0 OR ghp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет вещей для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_7']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}')  AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND prototype not in (1006233,1006232,1006234) AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level < 7 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_8']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND prototype not in (1006233,1006232,1006234)  AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 7 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_9']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0  AND prototype not in (1006233,1006232,1006234) AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 8 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_10']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0  AND prototype not in (1006233,1006232,1006234) AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 9 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_11']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND prototype not in (1006233,1006232,1006234)  AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 10 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_12']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND prototype not in (1006233,1006232,1006234)  AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 11 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_13']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND prototype not in (1006233,1006232,1006234) AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 12 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upgradeart_14']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND prototype not in (1006233,1006232,1006234) AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 13 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "улучшить";
	$zero_count_message = "У вас нет артефактов для улучшения.";
}
elseif(isset($_REQUEST['upunik']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type in (1,2,3,4,5,8,9,10,11,27,28) AND unik=1  AND `dressed`= 0 AND nlevel>=10 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет уникальных предметов для улучшения.";
}
elseif(isset($_REQUEST['upitem']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type in (1,2,3,4,5,8,9,10,11,28) AND unik=0 AND `dressed`= 0 AND nlevel>=8  AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' and name LIKE '%(мф)%' and name NOT LIKE '%футболка%' and (gsila > 0 or glovk > 0 or ginta > 0 or gintel > 0 or gmp > 0) and naem = 0");
	$action = "улучшить";
	$zero_count_message = "У вас нет предметов для улучшения. Улучшению подлежат только предметы, имеющие модификацию.";
}
elseif(isset($_REQUEST['downgradeart_6']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND name like '%[%' AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level =7 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_7']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 8 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_8']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND   `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 9 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_9']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND  `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 10 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_10']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}'  and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND  `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 11 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_11']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND  `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 12 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_12']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND  `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 13 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif(isset($_REQUEST['downgradeart_13']))
{
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$_SESSION['uid']}') AND type!=30 AND   name like '%[%'  AND  `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND up_level = 14 AND present!='Арендная лавка'  AND (`ab_uron` > 0 OR `ab_bron` > 0 OR `ab_mf` > 0) AND ( isnull(`art_param`) OR `art_param`='') ;");
	$action = "понизить";
	$zero_count_message = "У вас нет артефактов для понижения уровня.";
}
elseif (isset($_REQUEST['bysshop']))
{
	$items = mysql_query("SELECT i.* FROM oldbk.`inventory` i  LEFT JOIN oldbk.skupka ON itemid=i.id  WHERE `owner` = '".$_SESSION['uid']."'  AND prototype not in (946,947,948,949,950,951,952,953,954,955,956,957,5101,5102,5103,7001,7002,7003,7005,7006,207,100028,100029,100030,100031)  AND (`type` < 12 OR `type`=27 OR `type`=28  OR `type`=555 OR `type`=556 OR  `type`=30 OR  `type`=33 )  AND dressed=0 AND `setsale` = 0 AND bs_owner =0 AND sowner =0 AND `prokat_idp` = 0 AND present!='Арендная лавка'  AND arsenal_klan = '' and ISNULL(stavka) and naem = 0 ORDER by `update` DESC");
	$action = "активировать";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif(  (isset($_REQUEST['sharp_m5'])) OR (isset($_REQUEST['sharp_m4'])) OR (isset($_REQUEST['sharp_m3'])) OR (isset($_REQUEST['sharp_m2'])) OR (isset($_REQUEST['sharp_m1']))  OR (isset($_REQUEST['sharp_ekr_m5'])) )
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE owner = '{$_SESSION['uid']}' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'  AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `sharped` = 0 AND otdel=13 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif(  (isset($_REQUEST['sharp_d5'])) OR (isset($_REQUEST['sharp_d4'])) OR (isset($_REQUEST['sharp_d3'])) OR (isset($_REQUEST['sharp_d2'])) OR (isset($_REQUEST['sharp_d1'])) OR (isset($_REQUEST['sharp_ekr_d5'])) )
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE owner = '{$_SESSION['uid']}' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'  AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `sharped` = 0 AND otdel=12 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif(  (isset($_REQUEST['sharp_t5'])) OR (isset($_REQUEST['sharp_t4'])) OR (isset($_REQUEST['sharp_t3'])) OR (isset($_REQUEST['sharp_t2'])) OR (isset($_REQUEST['sharp_t1'])) OR (isset($_REQUEST['sharp_ekr_t5'])) )
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE owner = '{$_SESSION['uid']}' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'  AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `sharped` = 0 AND otdel=11 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif(  (isset($_REQUEST['sharp_n5'])) OR (isset($_REQUEST['sharp_n4'])) OR (isset($_REQUEST['sharp_n3'])) OR (isset($_REQUEST['sharp_n2'])) OR (isset($_REQUEST['sharp_n1'])) OR (isset($_REQUEST['sharp_ekr_n5'])) )
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE owner = '{$_SESSION['uid']}' AND `setsale`=0 AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'  AND  (arsenal_klan = '' OR arsenal_owner=1 ) AND `sharped` = 0 AND otdel=1 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif (isset($_REQUEST['sharp_5']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE  `setsale`=0 AND name not REGEXP '\\\+5$|\\\+6$|\\\+7$|\\\+8$|\\\+9$|\\\+10$' AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) AND owner = '{$_SESSION['uid']}' AND (sowner=0 or sowner='{$_SESSION['uid']}')  AND type=3 AND otdel!=6 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif (isset($_REQUEST['sharp_6']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE  `setsale`=0 AND name not REGEXP '\\\+6$|\\\+7$|\\\+8$|\\\+9$|\\\+10$' AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) AND owner = '{$_SESSION['uid']}' AND (sowner=0 or sowner='{$_SESSION['uid']}')  AND type=3  AND otdel!=6 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif (isset($_REQUEST['sharp_7']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE  `setsale`=0 AND name not REGEXP '\\\+7$|\\\+8$|\\\+9$|\\\+10$' AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) AND owner = '{$_SESSION['uid']}' AND (sowner=0 or sowner='{$_SESSION['uid']}')  AND type=3 AND otdel!=6 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif (isset($_REQUEST['sharp_8']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory` WHERE  `setsale`=0 AND name not REGEXP '\\\+8$|\\\+9$|\\\+10$' AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) AND owner = '{$_SESSION['uid']}' AND (sowner=0 or sowner='{$_SESSION['uid']}')  AND type=3 AND otdel!=6 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif (isset($_REQUEST['sharp_9']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE  `setsale`=0 AND name not REGEXP '\\\+9$|\\\+10$' AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) AND owner = '{$_SESSION['uid']}' AND (sowner=0 or sowner='{$_SESSION['uid']}')  AND type=3 AND otdel!=6 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif (isset($_REQUEST['sharp_10']))
{
	$items = mysql_query("SELECT * FROM oldbk.`inventory`  WHERE  `setsale`=0 AND name not REGEXP '\\\+10$' AND `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) AND owner = '{$_SESSION['uid']}' AND (sowner=0 or sowner='{$_SESSION['uid']}')  AND type=3 AND otdel!=6 and dressed = 0 and gmeshok = 0 and naem = 0");
	$action = "заточить";
	$zero_count_message = "У вас нет подходящего оружия для этой заточки.";
}
elseif ( (isset($_REQUEST['art_bonus_1'])) or (isset($_REQUEST['art_bonus_1_big'])) or (isset($_REQUEST['art_bonus_1_big_step'])) )
{
		$filtitem='';
		if ((isset($_REQUEST['art_bonus_1_big_step'])) and (isset($_REQUEST['item'])) )
		{
		$itmid=(int)$_REQUEST['item'];
		$filtitem=" id='{$itmid}' and ";
		}

	$items = mysql_query("select i.*, IFNULL(b.blevel,1) as bonus_level , ifnull(b.info,'') as bonus_info from oldbk.inventory as i LEFT JOIN oldbk.art_bonus as b ON i.id=b.itemid where ".$filtitem." owner = '{$_SESSION['uid']}' and `sowner`='{$_SESSION['uid']}'  and art_param!='' and dressed=0  and (b.blevel>=0 or ISNULL(b.blevel) ) and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 )  ORDER by `update` DESC;");
	$action = "Улучшить";
	$zero_count_message = "У вас нет подходящих артефактов для улучшения.";
}
elseif ((isset($_REQUEST['art_bonus_2'])) or (isset($_REQUEST['art_bonus_2_big'])) or (isset($_REQUEST['art_bonus_2_big_step']))  )
{
		$filtitem='';
		if ((isset($_REQUEST['art_bonus_2_big_step'])) and (isset($_REQUEST['item'])) )
		{
		$itmid=(int)$_REQUEST['item'];
		$filtitem=" id='{$itmid}' and ";
		}

	$items = mysql_query("select i.*, IFNULL(b.blevel,1) as bonus_level , ifnull(b.info,'') as bonus_info from oldbk.inventory as i LEFT JOIN oldbk.art_bonus as b ON i.id=b.itemid where  ".$filtitem."  owner = '{$_SESSION['uid']}' and `sowner`='{$_SESSION['uid']}' and art_param!='' and dressed=0 and b.blevel>=1 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 )  ORDER by `update` DESC; ");
	$action = "Улучшить";
	$zero_count_message = "У вас нет подходящих артефактов для улучшения.";
}
elseif ((isset($_REQUEST['art_bonus_3'])) or (isset($_REQUEST['art_bonus_3_big'])) or (isset($_REQUEST['art_bonus_3_big_step']))  )
{
		$filtitem='';
		if ((isset($_REQUEST['art_bonus_3_big_step'])) and (isset($_REQUEST['item'])) )
		{
		$itmid=(int)$_REQUEST['item'];
		$filtitem=" id='{$itmid}' and ";
		}

	$items = mysql_query("select i.*, IFNULL(b.blevel,1) as bonus_level , ifnull(b.info,'') as bonus_info from oldbk.inventory as i LEFT JOIN oldbk.art_bonus as b ON i.id=b.itemid where  ".$filtitem."  owner = '{$_SESSION['uid']}' and `sowner`='{$_SESSION['uid']}' and art_param!='' and dressed=0 and b.blevel>=2 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 )  ORDER by `update` DESC; ");
	$action = "Улучшить";
	$zero_count_message = "У вас нет подходящих артефактов для улучшения.";
}
elseif ((isset($_REQUEST['art_bonus_4'])) or (isset($_REQUEST['art_bonus_4_big'])) or (isset($_REQUEST['art_bonus_4_big_step']))  )
{
		$filtitem='';
		if ((isset($_REQUEST['art_bonus_4_big_step'])) and (isset($_REQUEST['item'])) )
		{
		$itmid=(int)$_REQUEST['item'];
		$filtitem=" id='{$itmid}' and ";
		}

	$items = mysql_query("select i.*, IFNULL(b.blevel,1) as bonus_level , ifnull(b.info,'') as bonus_info from oldbk.inventory as i LEFT JOIN oldbk.art_bonus as b ON i.id=b.itemid where  ".$filtitem."  owner = '{$_SESSION['uid']}' and `sowner`='{$_SESSION['uid']}' and art_param!='' and dressed=0 and b.blevel>=3 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 )   ORDER by `update` DESC;");
	$action = "Улучшить";
	$zero_count_message = "У вас нет подходящих артефактов для улучшения.";
}
elseif ((isset($_REQUEST['art_bonus_5'])) or (isset($_REQUEST['art_bonus_5_big'])) or (isset($_REQUEST['art_bonus_5_big_step']))  )
{
		$filtitem='';
		if ((isset($_REQUEST['art_bonus_5_big_step'])) and (isset($_REQUEST['item'])) )
		{
		$itmid=(int)$_REQUEST['item'];
		$filtitem=" id='{$itmid}' and ";
		}

	$items = mysql_query("select i.*, IFNULL(b.blevel,1) as bonus_level , ifnull(b.info,'') as bonus_info from oldbk.inventory as i LEFT JOIN oldbk.art_bonus as b ON i.id=b.itemid where  ".$filtitem."  owner = '{$_SESSION['uid']}'  and `sowner`='{$_SESSION['uid']}' and art_param!='' and dressed=0 and b.blevel>=4 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 )   ORDER by `update` DESC;");
	$action = "Улучшить";
	$zero_count_message = "У вас нет подходящих артефактов для улучшения.";
}
elseif ((isset($_REQUEST['art_bonus_6'])) or (isset($_REQUEST['art_bonus_6_big']))  or (isset($_REQUEST['art_bonus_6_big_step']))  )
{
		$filtitem='';
		if ((isset($_REQUEST['art_bonus_6_big_step'])) and (isset($_REQUEST['item'])) )
		{
		$itmid=(int)$_REQUEST['item'];
		$filtitem=" id='{$itmid}' and ";
		}

	$items = mysql_query("select i.*, IFNULL(b.blevel,1) as bonus_level , ifnull(b.info,'') as bonus_info from oldbk.inventory as i LEFT JOIN oldbk.art_bonus as b ON i.id=b.itemid where  ".$filtitem."  owner = '{$_SESSION['uid']}'  and `sowner`='{$_SESSION['uid']}' and art_param!='' and dressed=0 and b.blevel>=5 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}' AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 )   ORDER by `update` DESC;");
	$action = "Улучшить";
	$zero_count_message = "У вас нет подходящих артефактов для улучшения.";
}
elseif (isset($_REQUEST['item_bonus_1']))
{
	$nlevel=(int)($_REQUEST['nlevel']);
//	$items = mysql_query("select * from oldbk.inventory where owner = '{$_SESSION['uid']}'  and dressed=0  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) and prototype not in (169,170,601,632) AND type in (1,2,3,4,5,8,9,10,11) and (isnull(charka) or LEFT(charka,1)>=0)  ");
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}' and (sowner=0 or sowner= '{$_SESSION['uid']}' )  and dressed=0 and nlevel>='{$nlevel}' and (name like '%(мф)%' ) and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0  ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_2']))
{
	$nlevel=(int)($_REQUEST['nlevel']);
//	$items = mysql_query("select * from oldbk.inventory where owner = '{$_SESSION['uid']}'  and dressed=0  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) and prototype not in (169,170,601,632) AND type in (1,2,3,4,5,8,9,10,11) and (LEFT(charka,1)>=1)  ");
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}' and (sowner=0 or sowner= '{$_SESSION['uid']}' )  and dressed=0 and nlevel>='{$nlevel}' and (name like '%(мф)%' ) and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'   AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0  ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_3']))
{
	$nlevel=(int)($_REQUEST['nlevel']);
//	$items = mysql_query("select * from oldbk.inventory where owner = '{$_SESSION['uid']}'  and dressed=0  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) and prototype not in (169,170,601,632) AND type in (1,2,3,4,5,8,9,10,11) and (LEFT(charka,1)>=2)  ");
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}' and (sowner=0 or sowner= '{$_SESSION['uid']}' )  and dressed=0 and nlevel>='{$nlevel}' and (name like '%(мф)%' ) and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0  ORDER by `update` DESC; ");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_3e']))
{
	$nlevel=(int)($_REQUEST['nlevel']);
//	$items = mysql_query("select * from oldbk.inventory where owner = '{$_SESSION['uid']}'  and dressed=0  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'   AND (arsenal_klan = '' OR arsenal_owner=1 ) and prototype not in (169,170,601,632) AND type in (1,2,3,4,5,8,9,10,11) and (LEFT(charka,1)>=2)  ");
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}'  and dressed=0 and nlevel>='{$nlevel}' and (name like '%(мф)%' )  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and (sowner = 0 or sowner = ".$user['id'].") and naem = 0 ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_4']))
{
	$nlevel=(int)($_REQUEST['nlevel']);
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}' and (sowner=0 or sowner= '{$_SESSION['uid']}' )  and dressed=0 and nlevel>='{$nlevel}' and (name like '%(мф)%' ) and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0 ORDER by `update` DESC; ");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_4e']))
{
//print_r($_REQUEST);
	$nlevel=(int)($_REQUEST['nlevel']);
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}'  and dressed=0 and nlevel>='{$nlevel}' and (name like '%(мф)%' )  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401))   and naem = 0 ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_big']))
{
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}'  and dressed=0 and nlevel>='{$nlevel}'  and (name like '%(мф)%' )  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401))  and (sowner = 0 or sowner = ".$user['id'].") and naem = 0 ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif (isset($_REQUEST['item_bonus_big_4']))
{
//print_r($_REQUEST);
	$nlevel=(int)($_REQUEST['nlevel']);
	$items = mysql_query("select * from oldbk.inventory  where owner = '{$_SESSION['uid']}'  and dressed=0 and nlevel>='{$nlevel}'  and (name like '%(мф)%' ) and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352,946,947,948,949,950,951,952,953,954,955,956,957) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0  ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";
}
elseif( (isset($_REQUEST['item_bonus_big_step_2'])) or (isset($_REQUEST['item_bonus_big_step_3'])) or (isset($_REQUEST['item_bonus_big_step_4'])) )
{
	$add_array=explode(",",$_REQUEST['item']);
	$itemid=(int)$add_array[0];

	if (($add_array[1]>=1) and ($add_array[1]<=4))
		{
		$confa=array(1=>'bonus_gsila',2=>'bonus_glovk',3=>'bonus_ginta',4=>'bonus_gintel');
		$step_stat=$add_array[1];
		$st=$confa[$step_stat];
		$add_bonus[$st]=3; //+3 стата
		}

	if (($add_array[2]>=1) and ($add_array[2]<=4))
		{
		$confam=array(1=>'bonus_mfkrit',2=>'bonus_mfakrit', 3=>'bonus_mfuvorot',4=>'bonus_mfauvorot');
		$step_mf=$add_array[2];
		$stm=$confam[$step_mf];
		$add_bonus[$stm]=30; //+30
		}

	$items = mysql_query("select * from oldbk.inventory  where id='{$itemid}' and owner = '{$_SESSION['uid']}'  and dressed=0  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0  ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";

}
elseif( (isset($_REQUEST['item_bonus_big_4_step_2'])) or (isset($_REQUEST['item_bonus_big_4_step_3'])) or (isset($_REQUEST['item_bonus_big_4_step_4'])) or (isset($_REQUEST['item_bonus_big_4_step_5'])) )
{
//print_r($_REQUEST);

	$add_array=explode(",",$_REQUEST['item']);
	$itemid=(int)$add_array[0];

	if (($add_array[1]>=1) and ($add_array[1]<=4))
		{
		$confa=array(1=>'bonus_gsila',2=>'bonus_glovk',3=>'bonus_ginta',4=>'bonus_gintel');
		$step_stat=$add_array[1];
		$st=$confa[$step_stat];
		$add_bonus[$st]=4; //+3 стата
		}

	if (($add_array[2]>=1) and ($add_array[2]<=4))
		{
		$confam=array(1=>'bonus_mfkrit',2=>'bonus_mfakrit', 3=>'bonus_mfuvorot',4=>'bonus_mfauvorot');
		$step_mf=$add_array[2];
		$stm=$confam[$step_mf];
		$add_bonus[$stm]=40; //+30
		}

	if (($add_array[3]>=1) and ($add_array[3]<=4))
		{
		$confam=array(1=>'bonus_gfire',2=>'bonus_gwater', 3=>'bonus_gair',4=>'bonus_gearth');
		$step_mag=$add_array[3];
		$stm=$confam[$step_mag];
		$add_bonus[$stm]=1; //+1
		}

	$items = mysql_query("select * from oldbk.inventory  where id='{$itemid}' and owner = '{$_SESSION['uid']}'  and dressed=0  and (art_param='' or  isnull(art_param) ) and ab_uron= 0 and ab_bron=0 and ab_mf = 0 and  `setsale`=0 AND  `bs_owner`='{$user[in_tower]}'  AND present!='Арендная лавка'  AND `prokat_idp`=0   AND arsenal_klan = ''  and prototype not in (169,170,601,632,55510350,55510351,55510352) AND type in (1,2,3,4,5,8,9,10,11) and gmeshok = 0 AND NOT ((`prototype` >= 410001  and `prototype` <= 410030) or (`prototype` >= 55510301  and `prototype` <= 55510401)) and naem = 0  ORDER by `update` DESC;");
	$action = "Зачаровать";
	$zero_count_message = "У вас нет подходящих предметов для чарования.";

}
elseif (isset($_REQUEST['usedays']))
{
	$items = mysql_query("SELECT * FROM oldbk.`effects` WHERE  owner = '{$_SESSION['uid']}'   AND type=171");
	$action = "Да";
	$action2 = "Нет";
}

elseif(isset($_REQUEST['lab_teleport']))
{
	$usr_map = mysql_fetch_array(mysql_query("select * from labirint_users where owner='{$user['id']}' "));
	$user_map=$usr_map['map'];


	$items = mysql_query("select * from labirint_items where map='{$user_map}' and item='T' and (owner='{$user['id']}' or owner=0) ;");
	$action = "Войти в портал";
	$zero_count_message = "Нет открытых порталов...";
}
elseif($_REQUEST['otdel']=='select_ring')
{
	$get_rings=mysql_query("select * from oldbk.gellery where owner='{$user['id']}' and otdel=42 and dressed>0");

	$rings=array();
	$rings[1]='w6.gif';
	$rings[2]='w6.gif';
	$rings[3]='w6.gif';
		while($r=mysql_fetch_array($get_rings))
		{
		$rings[$r['dressed']]='sh/'.$r['img'];
		}

echo "<br><br><div align=center><table cellspacing='0' cellpadding='0' border='0' bgcolor='#FFF6DD'>";
echo "<tr>";
echo "<td><a href='javascript:selecttarget(\"1\");'><img title=\"Выбрать первый слот\" alt=\"Выбрать первый слот\" src=\"http://i.oldbk.com/i/".$rings[1]."\" width=\"20\" height=\"20\"></a></td>
<td><a href='javascript:selecttarget(\"2\");'><img title=\"Выбрать второй слот\" alt=\"Пустой второй кольцо\" src=\"http://i.oldbk.com/i/".$rings[2]."\" width=\"20\" height=\"20\"></a></td>
<td><a href='javascript:selecttarget(\"3\");'><img title=\"Выбрать третьий слот\" alt=\"Пустой третьий кольцо\" src=\"http://i.oldbk.com/i/".$rings[3]."\" width=\"20\" height=\"20\"></a></td>";
echo "</tr>";
echo "</table></div><br><br>";

}
elseif(isset($_REQUEST['otdel']))
{
/*
	$sql=check_hollydays($_REQUEST,$hollyday);
  // Елки 55510312,55510313,55510314,,55510315,,55510316,55510317,55510318,55510319,55510320,55510321,55510322,55510323,55510324,55510325,55510326,
	$elka_prot=" (`prototype` < 55510301 OR `prototype`>55510327) and prototype not in (946,947,948,949,950,951,952,953,954,955,956,957)";
	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$_SESSION['uid']}' AND ".$elka_prot." AND (".$sql.") AND `dressed`=0 AND bs_owner = 0 AND labonly=0  AND `setsale`=0  ;");
	$action = "Надеть";
	$zero_count_message = "У вас нет вещей для этой картинки.";
*/
$get_rings=mysql_query("select * from oldbk.gellery where owner='{$user['id']}' and otdel=42 and dressed>0");

$rings=array();
$rings[1]='w6.gif';
$rings[2]='w6.gif';
$rings[3]='w6.gif';
	while($r=mysql_fetch_array($get_rings))
	{
	$rings[$r['dressed']]='sh/'.$r['img'];
	}

echo "<br><br><div align=center><table cellspacing='0' cellpadding='0' border='0' bgcolor='#FFF6DD'>";
echo "<tr>";
echo "<td><a href='javascript:selecttarget(\"1\");'><img title=\"Выбрать первый слот\" alt=\"Выбрать первый слот\" src=\"http://i.oldbk.com/i/".$rings[1]."\" width=\"20\" height=\"20\"></a></td>
<td><a href='javascript:selecttarget(\"2\");'><img title=\"Выбрать второй слот\" alt=\"Пустой второй кольцо\" src=\"http://i.oldbk.com/i/".$rings[2]."\" width=\"20\" height=\"20\"></a></td>
<td><a href='javascript:selecttarget(\"3\");'><img title=\"Выбрать третьий слот\" alt=\"Пустой третьий кольцо\" src=\"http://i.oldbk.com/i/".$rings[3]."\" width=\"20\" height=\"20\"></a></td>";
echo "</tr>";
echo "</table></div><br><br>";
}
elseif(isset($_REQUEST['svecha']))
{
	if($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com')
	{
		$zhert=array(2=>'2010042', 3=>'2010043',6=>'2010046',1=>'2010046');
		$err='огня';
	}
	else
	{
		$zhert=array(2=>'2010032', 3=>'2010033',6=>'2010036',1=>'2010036');
		$err='свечей';
	}

	if($user[align]==6 || (int)$user[align]==1 || (int)$user[align]==2 || $user[align]==3)
	{
		$id=$zhert[(int)$user[align]];
	}
	elseif($user[align]==0)
	{
		$id=$zhert[(int)$_GET[al]];
	}

	$items = mysql_query("SELECT * FROM oldbk.inventory  WHERE owner = '{$user['id']}' AND prototype in (".$id.") AND `dressed`=0
	AND bs_owner = 0 AND labonly=0  AND present!='Арендная лавка' AND `setsale`=0;");
	$action = "Зажечь";
	$zero_count_message = "У Вас нет ".$err.".";
}
else
if(isset($_REQUEST['item_change']))
{
	$new_item=array(
		5278=>array(222222230,222222231,222222232,222222233,222222234,222222235),
		5277=>array(222222236,222222237,222222238,222222239,222222240,222222241),
		272=>array(222222236,222222237,222222238,222222239,222222240,222222241),
		121121122=>array(222222242,222222243,222222244,222222245,222222246,222222247),
		121121123=>array(222222242,222222243,222222244,222222245,222222246,222222247),
		121121124=>array(222222242,222222243,222222244,222222245,222222246,222222247)
	);

	$sql='';
	if($user[klan]!='')
	{
		$klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans where short='".$user[klan]."';"));
		if($user[id]==$klan[glava])
		{
			$sql= "OR (arsenal_klan='".$user[klan]."' AND arsenal_owner=1)";
		}
	}

	$check_item=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.inventory where
			owner=".$user[id]."
			AND  (sowner=0 or sowner=".$user[id].") AND (type = 5 or type = 555)
			AND (arsenal_klan = '' ".$sql.") AND prokat_idp=0
			AND setsale=0 AND dressed=0 AND id = '".$_REQUEST[item_change]."';"));
	if($check_item[id]>0)
	{
		$items = mysql_query('SELECT * FROM oldbk.`shop` WHERE id in ('.(implode(',',$new_item[$check_item[prototype]])).')');
		$action = "Поменять";
	}
	$zero_count_message = "У Вас нет подходящих вещей.";




}
$i = 0;
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0' bgcolor='#FFF6DD'>";


if (isset($_REQUEST['usedays']))
{
		$row = mysql_fetch_array($items);
		if ($row['id']>0)
		{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td width='50' align='center'>";
		echo "</td>";
		echo "<td align='center'>";

		$H = floor(($row['time']-time())/60/60);
		$M = round((($row['time']-time())/60) - ($H*60));

		echo "Эффект 10% опыта с прошлого дня действителен еще <b>".$H." часов ".$M." минут</b>. Заменить на новый эффект от текущего дня?<br>";
		echo "<a href='javascript:selecttarget(\"{$row['id']}\");'>{$action}</a>";
		echo "&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;";
		echo "<a href='#' onclick='closehint3(true);'>{$action2}</a>";
		echo "</td>";
		echo "<td width='50' align='center'>";
		echo "</td></tr>";
		}
		else
		{
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td width='50' align='center'>";
		echo "</td>";
		echo "<td align='center'>";
		echo "Использовать сейчас?<br>";
		echo "<a href='javascript:selecttarget(\"0\");'>{$action}</a>";
		echo "&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;";
		echo "<a href='#' onclick='closehint3(true);'>{$action2}</a>";
		echo "</td>";
		echo "<td width='50' align='center'>";
		echo "</td></tr>";
		}

}
elseif (isset($_REQUEST['lab_teleport']))
{
//print teleports points
	while($row = mysql_fetch_array($items))
	{
		$i++;
		$strah="";
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td width='100' align='center'>";
		echo "<img src='http://i.oldbk.com/i/sh/openport.gif'>";
		echo "</td>";
		echo "<td align='top'>";

		if (($row['val']==1) and ($row['owner']==$user['id']) )
				{
				$strah='<small><font color=red><b>«Страховка Лабиринта»</b></font></small>';
				}
				elseif ($row['add_info']!='')
				{
				$strah='<small><b>'.$row['add_info'].'</b></small>';
				}

		echo "Координыты портала X:".$row[x]." / Y:".$row[y]." ".$strah."<br>";
		$tlink=$row[x].'-'.$row[y];
		echo "<a href='javascript:selecttarget(\"{$tlink}\");'>{$action}</a>";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}



}
elseif (isset($_REQUEST['usesalign']))
{

		$actions[3] = "<a href=# onclick='if(confirm(\"После использования текущая склонность будет заменена на выбранную, вы согласны?\")) { selecttarget(\"3\"); } else {closehint3(true);}'><IMG alt='Темная' title='Темная'  src=\"http://i.oldbk.com/i/align_3.gif\" > Темная склонность</a>";
		$actions[6] = "<a href=# onclick='if(confirm(\"После использования текущая склонность будет заменена на выбранную, вы согласны? \")) { selecttarget(\"6\"); } else {closehint3(true);}'><IMG alt='Светлая' title='Светлая'  src=\"http://i.oldbk.com/i/align_6.gif\" > Светлая склонность</a>" ;
		$actions[2] = "<a href=# onclick='if(confirm(\"После использования текущая склонность будет заменена на выбранную, вы согласны? \")) { selecttarget(\"2\"); } else {closehint3(true);}'><IMG alt='Нейтральная' title='Нейтральная'  src=\"http://i.oldbk.com/i/align_2.gif\" > Нейтральная склоность</a>";

		$i=0;

		$ual=(int)$user['align'];
		unset($actions[$ual]);


	foreach($actions as $k=>$v)
	{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td  align='center'>";
		echo " ";
		echo "</td>";
		echo "<td valign='top' align=center>";
		echo $v."<br>";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}
}
elseif (isset($_REQUEST['usesmagic']))
{
$effarray=array(
		1=>'Получить стихию огня',
		2=>'Получить стихию земли',
		3=>'Получить стихию воздуха',
		4=>'Получить стихию воды');

		$actions[1] = "<a href='javascript:selecttarget(\"1\");'><IMG alt='Знак стихии огня' title='Знак стихии огня' height=100 src=\"http://i.oldbk.com/i/infoicon_fire.png\" width=100></a>";
		$actions[2] = "<a href='javascript:selecttarget(\"2\");'><IMG align=center alt='Знак стихии земли' title='Знак стихии земли' height=100 src=\"http://i.oldbk.com/i/infoicon_ground.png\" width=100></a>" ;
		$actions[3] = "<a href='javascript:selecttarget(\"3\");'><IMG align=center alt='Знак стихии воздуха' title='Знак стихии воздуха' height=100 src=\"http://i.oldbk.com/i/infoicon_air.png\" width=100></a>";
		$actions[4] = "<a href='javascript:selecttarget(\"4\");'><IMG align=center alt='Знак стихии воды' title='Знак стихии воды'  height=100 src=\"http://i.oldbk.com/i/infoicon_water.png\" width=100></a>";
		$i=0;

unset($effarray[$user[smagic]]);


	foreach($effarray as $k=>$v)
	{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td  align='center'>";
		echo " ";
		echo "</td>";
		echo "<td valign='top' align=center>";
		echo "<b>".$i.".</b> ".$v."<br>";
		echo "{$actions[$k]}";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}
}
elseif (isset($_REQUEST['usesbaff']))
{
$effarray=array(
		1=>'Увеличение получаемой репутации +50%',
		2=>'Увеличение получаемого рунного опыта +50%');
		$i=0;

	foreach($effarray as $k=>$v)
	{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td  align='center'>";
		echo " ";
		echo "</td>";
		echo "<td valign='top' align=center>";
		echo "<a href='javascript:selecttarget(\"".$i."\");'>".$v."</a><br>";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}
}
elseif (isset($_REQUEST['euro2016']))
{
require_once('euro2016.php');




	foreach($euflags as $id=>$val)
	{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td  align='center' width=100>";
		echo " ";
		echo "</td>";
		echo "<td valign='top' align=left>";
		echo "<a href='javascript:selecttarget(\"{$id}\");'>";
		echo "<img src='http://i.oldbk.com/i/euro2016/".$euflags[$id]['flag']."' alt='{$euflags[$id]['name']}' title='{$euflags[$id]['name']}'> ";
		echo "</a>";
		echo "<b>".$euflags[$id]['name']."</b><br>";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}
}
elseif (isset($_REQUEST['chm2018']))
{
require_once('chm2018.php');


	foreach($euflags as $id=>$val)
	{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td  align='center' width=100>";
		echo " ";
		echo "</td>";
		echo "<td valign='top' align=left>";
		echo "<a href='javascript:selecttarget(\"{$id}\");'>";
		echo "<img style=\"width:60px;height:60px\" src='http://i.oldbk.com/i/chm2018/".$euflags[$id]['flag']."' alt='{$euflags[$id]['name']}' title='{$euflags[$id]['name']}'> ";
		echo "</a>";
		echo "<b>".$euflags[$id]['name']."</b><br>";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}
}
elseif (isset($_REQUEST['usev2015']))
{
	$effarray=array(
		9100=>'Получение репутации +30%',
		9102=>'Получение опыта +30%',
		9103=>'Получение рунного опыта +30%',
		9104=>'Таймаут в лабиринт -30%',
		9105=>'Таймаут в ристалище -30%',
		9106=>'Таймаут в руины -30%');

	$action = "использовать";
		$i=0;
	foreach($effarray as $k=>$v)
	{
		$i++;
		echo "<tr bgcolour='#c7c7c7'>";
		echo "<td width='100' align='center'>";
		echo " ";
		echo "</td>";
		echo "<td align='top'>";
		echo "<b>".$i.".</b> ".$v."<br>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:selecttarget(\"{$k}\");'>{$action}</a>";
		echo "</td>";
		echo "<tr><td colspan='2'><hr/></td></tr>";
	}
}
else
{

	if (isset($_SESSION['scroll']) && $_SESSION['scroll'] > 0) {
		$scroll = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '".$_SESSION['scroll']."' AND `owner` = '{$user['id']}' and (cost>0 or ecost>0 or repcost > 0)  AND `dressed`=0 and labonly=0 and labflag=0 LIMIT 1;"));
	} else {
		$scroll = false;
	}

	while($row = mysql_fetch_array($items))
	{

		if (isset($_REQUEST['rihtbron'])) {
		    	$rr = array('bron1','bron2','bron3','bron4','ghp','delta_stat');

			$newit = downgrade_item($row,$rr,1,1);

			if($newit['add_bron'] == 0) {
				continue;
			}
		}
		//http://tickets.oldbk.com/issue/oldbk-1505
		elseif (isset($_REQUEST['bysshop']))
		{
			if ( (($row['prototype']>=222222230) AND ($row['prototype']<=222222235)) AND ($row['massa']=='1.1') )
				{
				continue;
				}
				elseif ($row['cost']<=(EKR_TO_KR*1) )
				{
				continue;
				}
		}


		if(isset($_REQUEST['rihthp4204']) || isset($_REQUEST['rihthp4205']) || isset($_REQUEST['rihthp4206'])) {
		    	$rr = array('bron1','bron2','bron3','bron4','ghp','delta_stat');

			$newit = downgrade_item($row,$rr,1,1);

			if($newit['add_hp'] == 0) {
				continue;
			}

			if (isset($_REQUEST['rihthp4204'])) {
				$action = "отрихтовать +1 HP";
			}

			if (isset($_REQUEST['rihthp4205'])) {
				$how = 5;
				if ($newit['add_hp'] < $how) {
					$how = $newit['add_hp'];
				}
				$action = "отрихтовать +".$how." HP";
			}

			if (isset($_REQUEST['rihthp4206'])) {
				$how = 10;
				if ($newit['add_hp'] < $how) {
					$how = $newit['add_hp'];
				}
				$action = "отрихтовать +".$how." HP";
			}

		}

		if (!isset($_REQUEST['scrolls']) && $scroll !== false && $scroll['getfrom'] == 43 && $scroll['repcost'] > 0 && $scroll['sowner'] > 0) {
			// не показыем для встроек-перевстроек привязанные не нами вещи
			if ($row['sowner'] > 0 && ($row['sowner'] != $scroll['sowner'])) continue;
		}

		if(isset($_REQUEST['scrolls']))
		{
			if (!in_array($row['prototype'],$can_inc_magic) ) {
				// не показыем для встроек-перевстроек если прото нет в конфиге
				continue;
			}
		}



		$i++;

		if (isset($_REQUEST['art_bonus_1_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выберите Бонус',{$row['id']},'art_bonus_1_big_step');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['art_bonus_1_big_step']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},1');\">+40hp и 3 брони</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},2');\">+11 брони</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},3');\">+55hp</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['art_bonus_2_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выбрать модификатор',{$row['id']},'art_bonus_2_big_step');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['art_bonus_2_big_step']))
		{
		$act="<div align=left><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},1');\">Мф. крит. уд: +70%</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},2');\">Мф. против кр.: +70%</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},3');\">Мф. уверт.: +70%</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},4');\">Мф. против ув.: +70%</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['art_bonus_3_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выбрать параметр',{$row['id']},'art_bonus_3_big_step');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['art_bonus_3_big_step']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},1');\">Сила +7</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},2');\">Ловкость +7</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},3');\">Интуиция +7</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},4');\">Интеллект +7</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},5');\">Мудрость +7</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['art_bonus_4_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выбрать умение',{$row['id']},'art_bonus_4_big_step');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['art_bonus_4_big_step']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},1');\">Ножами и кастетами +2</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},2');\">Топорами и секирами +2</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},3');\">Дубинами и булавами +2</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},4');\">Мечами +2</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['art_bonus_5_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выбрать умение',{$row['id']},'art_bonus_5_big_step');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['art_bonus_5_big_step']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},1');\">Стихия огня: +2</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},2');\">Cтихия воды +2</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},3');\">Стихия воздуха +2</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},4');\">Стихия земли: +2</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['art_bonus_6_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выбрать бонус',{$row['id']},'art_bonus_6_big_step');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['art_bonus_6_big_step']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},1');\">+3% Макс. мф.</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},2');\">+6% брони</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},3');\">+1% Макс. мф. и +1% урона</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выберите Бонус параметров',{$row['id']},'item_bonus_big_step_2');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['item_bonus_big_step_2']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},1','item_bonus_big_step_3');\">Сила +3</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},2','item_bonus_big_step_3');\">Ловкость +3</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},3','item_bonus_big_step_3');\">Интуиция +3</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},4','item_bonus_big_step_3');\">Интеллект +3</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big_step_3']))
		{
		// добавка параметров в отображение
		$add_bonus['bonus_ghp']=25;
		$row =array_merge($row,$add_bonus);
		$act="<div align=left><small>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},3','item_bonus_big_step_4');\">Мф. уверт.: +30%</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},4','item_bonus_big_step_4');\">Мф. против ув.: +30%</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},1','item_bonus_big_step_4');\">Мф. крит. уд: +30%</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},2','item_bonus_big_step_4');\">Мф. против кр.: +30%</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big_step_4']))
		{
		// добавка параметров в отображение
		$add_bonus['bonus_ghp']=25;
		$row =array_merge($row,$add_bonus);
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},1');\">Стихия огня: +1</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},2');\">Стихия воды: +1</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},3');\">Стихия воздуха +1</a><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},4');\">Cтихия земли +1</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big_4']))
		{
		$act = "<a href=# onClick=\"getchoice_big('Выберите Бонус параметров',{$row['id']},'item_bonus_big_4_step_2');\">{$action}</a>";
		}
		elseif (isset($_REQUEST['item_bonus_big_4_step_2']))
		{
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},1','item_bonus_big_4_step_3');\">Сила +4</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},2','item_bonus_big_4_step_3');\">Ловкость +4</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},3','item_bonus_big_4_step_3');\">Интуиция +4</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите бонус модификаторов','{$row['id']},4','item_bonus_big_4_step_3');\">Интеллект +4</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big_4_step_3']))
		{
		// добавка параметров в отображение
		$add_bonus['bonus_ghp']=35;
		$row =array_merge($row,$add_bonus);
		$act="<div align=left><small>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},3','item_bonus_big_4_step_4');\">Мф. уверт.: +40%</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},4','item_bonus_big_4_step_4');\">Мф. против ув.: +40%</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},1','item_bonus_big_4_step_4');\">Мф. крит. уд: +40%</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите магическое мастерство','{$row['id']},{$step_stat},2','item_bonus_big_4_step_4');\">Мф. против кр.: +40%</a><br>";
		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big_4_step_4']))
		{
		// добавка параметров в отображение
		$add_bonus['bonus_ghp']=35;
		$row =array_merge($row,$add_bonus);
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите мастерство оружия','{$row['id']},{$step_stat},{$step_mf},1','item_bonus_big_4_step_5');\">Стихия огня: +1</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите мастерство оружия','{$row['id']},{$step_stat},{$step_mf},2','item_bonus_big_4_step_5');\">Стихия воды: +1</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите мастерство оружия','{$row['id']},{$step_stat},{$step_mf},3','item_bonus_big_4_step_5');\">Стихия воздуха +1</a><br>";
		$act.= "<a href=# onClick=\"getchoice_big('Выберите мастерство оружия','{$row['id']},{$step_stat},{$step_mf},4','item_bonus_big_4_step_5');\">Cтихия земли +1</a><br>";


		$act.="</small></div>";
		}
		elseif (isset($_REQUEST['item_bonus_big_4_step_5']))
		{
		// добавка параметров в отображение
		$add_bonus['bonus_ghp']=35;
		$row =array_merge($row,$add_bonus);
		$act="<div align=left style=\"padding:10px;\"><small>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},{$step_mag},2');\">Топорами и секирами:+1</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},{$step_mag},3');\">Дубинами и булавами:+1</a><br><br>";
		$act.= "<a href=\"javascript:selecttarget('{$row['id']},{$step_stat},{$step_mf},{$step_mag},4');\">Мечами:+1</a><br>";
		$act.="</small></div>";
		}
		else
		{
		$act = "<a href='javascript:selecttarget(\"{$row['id']}\");'>{$action}</a>";
		}

		if(isset($_REQUEST['item_change'])) {
			$row['count']=0;
			$row['avacount']=0;
		}

		showitem($row,0,false,'#fff6dd',$act);

		echo "<tr><td colspan='2'><hr/></td></tr>";
	}

}

if($i == 0) {
	echo "<tr><td>".$zero_count_message."</td></tr>";
}
echo "</table>";
?>
