<?php
	session_start();
	if (!($_SESSION['uid'] >0))
	{
	 header("Location: index.php");
	 die();
	}
	include "connect.php";
	include "functions.php";

if ($user['in_tower'] == 4) { header('Location: jail.php'); die(); }

$typet = "s";
$GOLD_GIVE_KURS=11;
$vauch_a = array(100000,100005,100015,100020,100025,100040,100100,100200,100300); //+ и бумажка КО
$tower_type=FALSE;
$ttype=0;

			if($user['in_tower']==1)
			{
				$tower=mysql_fetch_array(mysql_query('select * from deztow_turnir where active = TRUE'));
				if($tower[type]==12 || $tower['type'] == 13 || $tower['type'] == 14 || $tower['type'] == 15 || $tower['type'] == 16)
				{
					$ttype=1;
				}
			}
			
			if($user['in_tower']==15)
			{
				$tower=mysql_fetch_array(mysql_query('select * from dt_map where active = 1'));
				if($tower['greedtype'])
				{
					$ttype=2;
				}
			}


	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($_SESSION['boxisopen']!='open') { header('location: main.php?edit=1'); die(); }
	if (($user['room'] >= 197)AND($user['room'] <= 199))  { header('Location: armory.php'); die(); }
	if ($user['room'] == 76)  { header('Location: class_armory.php'); die(); }
	if (($user['room'] > 210)AND($user['room'] <= 239))  { header('Location: restal210.php'); die(); }


if($_POST['ssave']==1)
{
	save_gruppovuha();
}
elseif (isset($_GET['invload2']) && $user['battle'] == 0 && $user['battle_fin'] == 0 && isset($_GET['prototype'],$_GET['id'],$_GET['otdel'],$_GET['idkomu'])) {
	$idkomu=(int)$_GET['idkomu'];
	load_hidden_items_give();
}
elseif (isset($_GET['proto']) && $user['battle'] == 0 && $user['battle_fin'] == 0 && isset($_GET['to_id']) && isset($_GET['itemid'])  && isset($_GET['gift']) && isset($_GET['kol'])  )    {
	Show_item_to_give((int)$_GET['to_id'],(int)$_GET['proto'],(int)$_GET['itemid'],(int)($_GET['gift']),(int)($_GET['kol']),(int)($_GET['eflag']),round(floatval($_REQUEST['sprice']),2));
}



function load_hidden_items_give()
{
global  $idkomu, $user, $vauch_a, $ttype, $grrr;


		if ($_SESSION['razdel']==0)
		{
		// загружаем данные по прототипу
		//$_GET['id'];
		$etid=(int)($_GET['id']);
		$get_etalon=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id`='{$etid}' and `owner` = '{$_SESSION['uid']}' "));
		if ($get_etalon['id']>0)
			{
			$q = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' and prototype = '{$get_etalon['prototype']}' AND
			`name`= '{$get_etalon['name']}' ".($get_etalon['charka']!=""?"AND `charka`= '{$get_etalon['charka']}":"")." AND  `unik` = '{$get_etalon['unik']}' AND  `mfinfo` = '{$get_etalon['mfinfo']}' AND
			`ups` = '{$get_etalon['ups']}' AND `includemagic` = '{$get_etalon['includemagic']}' AND  `dressed` = 0 and sowner=0 AND `setsale` = 0  AND id != ".(int)$_GET['id']." AND `present` = '' AND `bs_owner` ='".$user['in_tower']."' ORDER by `update` DESC; ");			
			}
			else
			{
			$ret = 'Пусто!';
			}
		}
		else
		{
		$q = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' and prototype = ".(int)$_GET['prototype']." AND `dressed` = 0 AND `setsale` = 0 and sowner=0 AND id != ".(int)$_GET['id']." AND `present` = '' AND `bs_owner` ='".$user['in_tower']."' ORDER by `update` DESC; ");
		}

	$ret = "";

	if (mysql_num_rows($q) > 0) {
		$ret .= "<table border=2  WIDTH=100% CELLSPACING='0' CELLPADDING='2' BGCOLOR='#A5A5A5'>";
		while($row = mysql_fetch_assoc($q)) {
			if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }
			$act='';
			$money_out=($ttype==1?10:1);
			$money_out=($ttype==2?100:1);
			$row['itemscount']=0;
			$grrr=1;
			$act=show_item_to_give_link($row,$money_out); //линки на продажу
		
			$ret .= showitem($row,0,false,$color,$act,0,0,1);
		}
		$ret .= "</table>";
	} else {
		$ret = 'Пусто';
	}
	echo $ret;
	die();
}

function Show_item_to_give($idkomu,$proto,$itemid,$sale,$kol,$efalg,$sprice)
{
global $user;  

	if (($sale<0) OR ($sale>2)) $sale=0;

	$str_txt_a[0]='Передача';
	$str_txt_a[1]='Подарок';		
	$str_txt_a[2]='Продажа';

	
	$str_txt_b[0]='Передать';
	$str_txt_b[1]='Подарить';	
	$str_txt_b[2]='Продать';	



	$komu=mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` ='".$idkomu."';"));
	$item=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` ='".$itemid."';"));
	
	$item_img=($item['img_big']!=''?$item['img_big']:$item['img']);
	if ($item['repcost'] > 0) { $item_cost = "<b>Цена: ".$item['cost']." кр.</b> &nbsp;"; } elseif($item['ecost'] > 0)  { $item_cost = "<b>Цена: ".$item['ecost']." екр.</b> &nbsp;"; } else  { $item_cost = "<b>Цена: ".$item['cost']." кр.</b> &nbsp;"; }

	$str_kol=''; if ($kol>0) $str_kol='(Количество: <b>'.$kol.'</b>)';
	
	

	
echo ' <br><table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#CCC3AA" id="tlsize">';
echo ' <tr><td colspan=2 align=center><h4>'.$str_txt_a[$sale].' персонажу: '.s_nick($komu['id'],$komu['align'],$komu['klan'],$komu['login'],$komu['level']).'</b></font> </h4></td>';
echo ' <tr bgcolor="#FFF6DD"><td width=130px align=center><br><img src="http://i.oldbk.com/i/sh/'.$item_img.'"><br><small>('.get_item_fid($item).')</small></td><td align=left valign=top><br><b>'.link_for_item($item).'</b><br>'.$item_cost.'     '.$str_kol.'</td>';
echo ' <tr><td colspan=2 align=center bgcolor="#FFF6DD">';

echo '<form action="give22.php" method="GET" name="ftogive">';
echo '<INPUT TYPE=hidden name=to_id value="'.$komu['id'].'">';
echo '<INPUT TYPE=hidden name=s4i value="'.$user['sid'].'">';
echo '<INPUT TYPE=hidden name=sd4 value="'.$user['id'].'">';
echo '<INPUT TYPE=hidden name=tmp value="'.$item['duration'].'">';

if ($sale==1) {  echo '<INPUT TYPE=hidden name=gift value="1">'; }

echo ' <table border=0 width=100% cellspacing=0 cellpadding=0 >
	<tr><td colspan=2 align=center>'.$str_txt_b[$sale];
	
if ($kol>1) 
	{
	echo ' <input name="count" id="count" size="4" type="text" value="1" onChange=\'javascript: calccount(this.value)\';  onkeyup="this.value=this.value.replace(/[^\d]/,\'\'); calccount(this.value);"> шт. '; 
	}
	else
	{
	echo ' <input id="count" type="hidden" value="1">'; 
	}
	
	if ($sale==2) 
	{
		if ($kol>1) 
		{
			echo '<INPUT TYPE="hidden" name="set" value="'.$proto.'">';	
		} 

 	echo '<INPUT TYPE=hidden name=id_th value="'.$itemid.'">';
 	
 	if (($sprice>0) and ($efalg==1 || $efalg==2) )	 
 		{
		echo '	по цене <input id="dummy" name="dummy" type="text" size=4 value="'.$sprice.'" disabled="disabled" > кр. за <b>1 шт.</b> ';
		echo '<input id="cost" name="cost" type="hidden" size=4 value="'.$sprice.'">';
 		}
 		elseif (($sprice>0) and ($efalg==3) )	 
 		{
		echo '	по цене <input id="cost" name="cost" type="text" size=4 value="'.$sprice.'" onChange=\'javascript: calccost(this.value,\''.$sprice.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); calccost(this.value,'.$sprice.');"> кр. за <b>1 шт.</b> ';
 		}
 		else
 		{
 		$sprice=1;
		echo '	по цене <input id="cost" name="cost" type="text" size=4 value="1" onChange=\'javascript: calccost(this.value,\''.$sprice.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); calccost(this.value,'.$sprice.');"> кр. за <b>1 шт.</b> ';
 		} 		
	echo "<script> calccost('{$sprice}','{$sprice}'); </script>"; 		

	if ($efalg==1) { echo "<br><font color=red>Предмет будет передан как подарок!</font>"; }
	if ($efalg==3) { echo "<font color=red>Минимальная цена продажи <b>{$sprice}</b> кр.</font>"; }	
	echo ' <br> Итого товаров на сумму: <b><span id="totallkr">1</span> кр.</b>, комиссия за передачу: <b><span id="totall_kom">1</span> кр.</b>';
	}
	else
	{
		if ($kol>1) 
		{
		echo '<INPUT TYPE="hidden" name="set" value="'.$proto.'">';			
		}

	echo '<INPUT TYPE=hidden name=setobject value="'.$itemid.'">';		

	echo ' <br> Комиссия за передачу: <b><span id="totall_kom">1</span> кр.</b>';
	}
echo '<br><br></td></tr>';
echo ' <tr> <td align=center> <a href="javascript:void(0)" onClick="document.ftogive.submit();" title="'.$str_txt_b[$sale].'">'.$str_txt_b[$sale].'</a> </td><td align=center> <a href="javascript:void(0)" onClick="closeinfo();" title="Закрыть">Отмена</a> <br></td> </tr>';
echo ' </TABLE></td></tr></table>';

//height: ($('#tlsize').css('height')+150)+'px'

die();
}



function  show_item_to_give_link($row,$money_out)
{
global $idkomu, $user, $vauch_a, $ttype, $grrr;
										if ((($row['ekr_flag']==0) and ($row['otdel']!=72)) OR ($user['klan']=='radminion') OR ($user['klan']=='Adminion') OR ($user['id']==8325) )  // купленые в березе с этим флагом передавать / дарить нельзя , уники запрещаем передавать
										{
											if (!(in_array($row['prototype'],$vauch_a) && $idkomu != 102904 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325)) 
											{
												$act .= "<A HREF=\"give22.php?to_id=".$idkomu."&id_th=".$row['id']."&setobject=".$row['id']."
												&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$row['duration']."&gift=0\"".'
												onclick="return confirm(\'Передать предмет '.$row['name'].'?\')">передать&nbsp;за&nbsp;'.$money_out.'&nbsp;кр.</A>';
											}
				
											if($row['group']==1)
											{
					
												if (!(in_array($row['prototype'],$vauch_a) && $idkomu != 102904 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325)) 
												{
												 if ((isset($row['itemscount']) and ($row['itemscount'] >1)) AND ($grrr == 1))
												 	{
										        		$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Передать несколько штук\" style=\"cursor: pointer\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','0','{$idkomu}','{$row['itemscount']}',0,0,event)\">";
										        		}
												}
											}
										}
										
										// !=1 to disable in tower
										if($idkomu == 83 || $idkomu == 136 || $idkomubot == 84)
										{
											$ttype=0;
										}
									
										if ($row['ekr_flag']==0 || ADMIN) // купленые в березе с этим флагом передавать / дарить нельзя
										{
											if ( (($row['art_param'] !='') or ($row['ab_mf'] >0 )  or ($row['ab_bron'] >0 )  or ($row['ab_uron'] >0 ))     and ($row['sowner'] !=0)) 
											{
												if (ADMIN) {
													$act .= "<br><A HREF=\"give22.php?to_id=".$idkomu."&id_th=".$row['id']."&setobject=".$row['id']."
													&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$row['duration']."&gift=1\"".'
													onclick="return confirm(\'Подарить предмет '.$row['name'].'?\')">подарить</A>';
												}
											}
											elseif(ADMIN || ($user['in_tower'] == 0 || $user['in_tower']==2 || ($user['in_tower']==1 && $ttype!=1 ) || ($user['in_tower']==15 && $ttype != 2)))
											{
												if (ADMIN || (!(in_array($row['prototype'],$vauch_a) && $idkomu != 102904 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325))) {
													$act .= "<br><A HREF=\"give22.php?to_id=".$idkomu."&id_th=".$row['id']."&setobject=".$row['id']."
													&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$row['duration']."&gift=1\"".'
													onclick="return confirm(\'Подарить предмет '.$row['name'].'?\')">подарить</A>';
												        if($row['group']==1) 
													{
													 if ((isset($row['itemscount']) and ($row['itemscount'] >1)) AND ($grrr == 1) )
													 	{
												        	$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Подарить несколько штук\" style=\"cursor: pointer\" onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','1','{$idkomu}','{$row['itemscount']}',0,0,event)\">";
														}										        	
													}
												}
					
											}
										}
										
										if($user['in_tower'] == 0)
										{
											if ($row['sowner']!=0)
											{
											//не протаюутся sowner!=0
											}						
											elseif ($row['otdel']==72 || $row['labonly'] > 0)
											{
											//не протаюутся уники и вещи из лабы
											} 
											else
											if ( (($row['art_param'] !='') or ($row['ab_mf'] >0 )  or ($row['ab_bron'] >0 )  or ($row['ab_uron'] >0 ))     and ($row['sowner'] !=0)) 
											{
											

											
											}
											elseif ( ($row['ekr_flag']  == 1)) {
												if ($row['ecost']>0) {
													if ($row['prototype'] == 55510350 || $row['prototype'] == 55510352 || $row['prototype'] == 55510351 || $row['prototype'] == 410021 || $row['prototype'] == 410022 || $row['prototype'] == 410026) {													
														if (ceil(($row['dategoden'] - time())/(60*60*24)) >= 7) 
														{
															$act .= "<br><A href=\"javascript:void(0)\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','1','{$row['ekr_flag']}','".($row['ecost']*EKR_TO_KR)."',event)\">продать<br>(комиссия 1 кр.)</A>";
															if (($row['itemscount']>1) and ($grrr==1) )
																{
																$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Продать несколько штук\" style=\"cursor: pointer\" onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','{$row['itemscount']}','{$row['ekr_flag']}','".($row['ecost']*EKR_TO_KR)."',event)\">";
																}
														}
													} else {
														$tcost = $row['ecost']*EKR_TO_KR;
														if ($row['type'] == 50) {
															$tcost = $tcost - (($tcost / $row['maxdur']) * $row['duration']);
														}
														$act .= "<br><A href=\"javascript:void(0)\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','1','{$row['ekr_flag']}','{$tcost}',event)\">продать<br>(комиссия 1 кр.)</A>";
															if (($row['itemscount']>1) and ($grrr==1) )
															{
															$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Продать несколько штук\" style=\"cursor: pointer\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','{$row['itemscount']}','{$row['ekr_flag']}','{$tcost}',event)\">";
															}

													}
												}
											} elseif ( ($row['ekr_flag']  == 2)) {
												if ($row['ecost']>0) {
													$tcost = $row['ecost']*EKR_TO_KR;
													if ($row['type'] == 50) {
														$tcost = $tcost - (($tcost / $row['maxdur']) * $row['duration']);
													}
													$act .= "<br><A href=\"javascript:void(0)\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','1','{$row['ekr_flag']}','{$tcost}',event)\">продать<br>(комиссия 1 кр.)</A>";				

														if (($row['itemscount']>1) and ($grrr==1) )													
														{
														$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Продать несколько штук\" style=\"cursor: pointer\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','{$row['itemscount']}','{$row['ekr_flag']}','{$tcost}',event)\">";
														}													

												}
											} elseif ( ($row['ekr_flag']  == 3)) {
												if ($row['ecost']>0) {
													$tcost = $row['ecost']*EKR_TO_KR;
													if ($row['type'] == 50) {
														$tcost = $tcost - (($tcost / $row['maxdur']) * $row['duration']);
													}
				
													$act .= "<br><A href=\"javascript:void(0)\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','1','{$row['ekr_flag']}','{$tcost}',event)\">продать<br>(комиссия 1 кр.)</A>";				
														if (($row['itemscount']>1) and ($grrr==1) )
														{
														$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Продать несколько штук\" style=\"cursor: pointer\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','{$row['itemscount']}','{$row['ekr_flag']}','{$tcost}',event)\">";
														}
													
												}
											} 
											else {
												$act .= "<br><A href=\"javascript:void(0)\"  onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}',1,0,0,event)\">продать<br>(комиссия 1 кр.)</A>";
												
													 if ((isset($row['itemscount']) and ($row['itemscount'] >1)) and ($grrr == 1) )
													 	{
												        	$act .= "<IMG SRC=\"http://i.oldbk.com/i/up.gif\" WIDTH=11 HEIGHT=11 BORDER=0 ALT=\"Продать несколько штук\" style=\"cursor: pointer\" onclick=\"NewAddCount('{$row[prototype]}', '{$row[id]}','2','{$idkomu}','{$row['itemscount']}',0,0,event)\">";
														}
											}
										}
return $act;
}

$step=1;

	if ($step==1) {  $idkomu=0; }

	if (!$_REQUEST['razdel']) { $_REQUEST['razdel']=1; }

	if ($_REQUEST['FindLogin']) {
		$res=mysql_fetch_array(mysql_query("SELECT `id`, `id_grup`, `ruines`, `level`, `room`, `align`, `odate` as `online` FROM `users` WHERE `login` ='".mysql_escape_string($_REQUEST['FindLogin'])."';"));

		if (!$res['id'] && $user['in_tower'] == 15 && strpos($_REQUEST['FindLogin'],'pxива') !== FALSE) {
			$res=mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE bot_room = ".$user['room']." AND `login` ='".mysql_escape_string($_REQUEST['FindLogin'])."' and id_user = 84"));
			$res['room'] = $res['bot_room'];
		}
		$step=3;
	}
	
	if ($_REQUEST['to_id']) {
		$res=mysql_fetch_array(mysql_query("SELECT `id`, `id_grup`, `ruines`, `level`, `room`, `align`, `odate` as `online` FROM `users` WHERE `id` ='".(int)($_REQUEST['to_id'])."';"));
		if (!$res['id'] && $user['in_tower'] == 15) {
			$res=mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `id` ='".intval($_REQUEST['to_id'])."' and id_user = 84"));
			$res['room'] = $res['bot_room'];
		}
		$step=3;
	}

	if (@$step==3)
	{
		$step=0;
		$del_trade=false;
		
		$id_person_x=$res['id'];
		if (@!$id_person_x) { $mess='Персонаж не найден'; $typet = "e"; $del_trade=true; }
		elseif ($id_person_x==$user['id']) { $mess='Незачем передавать самому себе'; $typet = "e"; }
		elseif ($user['align']==4 && $user['id']!='188') { $mess='Со склонностью хаос передачи предметов запрещены'; $typet = "e"; $del_trade=true; }
		elseif ( (($res['online'] < (time()-120) && !($user['klan'] == 'radminion') && !($user['klan'] == 'Adminion') && !($user['id'] == 8325) && $user['in_tower'] != 15)) AND !(isset($_REQUEST['cancel'])) )  { $mess='Персонаж не онлайн'; $typet = "e"; $del_trade=true; }
		elseif ( ($res['room']!=$user['room'] && !($user['klan'] == 'radminion') && !($user['klan'] == 'Adminion') && !($user['id'] == 8325))  AND !(isset($_REQUEST['cancel'])) ) { $mess='Вы должны находиться в одной комнате с тем, кому хотите передать вещи'; $typet = "e"; $del_trade=true; }
		elseif ($user['ruines'] > 0 && $user['id_grup'] != $res['id_grup']) { $mess='Нельзя передавать врагам!'; $typet = "e"; $del_trade=true; }
		elseif ($res['level']<4 && !($user['klan'] == 'radminion') && !($user['klan'] == 'Adminion') && !($user['id'] == 8325)) { $mess='К персонажам до 4-го уровня передачи предметов запрещены'; $typet = "e"; $del_trade=true; }
		elseif ($user['level']<4 AND !($user['klan'] == 'radminion') AND !($user['klan'] == 'Adminion') && !($user['id'] == 8325)) { $mess='Персонажам до 4-го уровня передачи предметов запрещены'; $typet = "e"; $del_trade=true; }
		else{
			$idkomu=$id_person_x;
			if (!isset($res['id_user']) || $res['id_user'] != 84) {
				$komu=mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` ='".$idkomu."';"));
			} else {
				$komu=mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `id` ='".$idkomu."';"));			
				$idkomubot = 84;
			}


			$VAUCHER='and prototype not in (900,901,902,903,904,905,906,907,908,200001,200002,200005,200010,200025,200050,200100,200250,200500,2013005) ';   

			$mess=$_REQUEST['FindLogin'];
			$step=3;
		}
		
		if ($del_trade==true)
			{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;");
			}
	}


?><HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<SCRIPT src='i/commoninf.js'></SCRIPT>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<script src="i/jquery.drag.js" type="text/javascript"></script>	

<style>
.noty_message { padding: 5px !important;}
</style>

<SCRIPT>

			function NewAddCount(proto, itemid, sale, idkomu, kol, eflag, sprice ,event)
			{

				if (window.event) 
				{
					event = window.event;
				}
				if (event ) 
				{

				       $.get('give22.php?rnd='+Math.random()+'&itemid='+itemid+'&proto='+proto+'&to_id='+idkomu+'&eflag='+eflag+'&sprice='+sprice+'&kol='+kol+'&gift='+sale, function(data) {
					  $('#pl').html(data);
					  $('#pl').show(200, function() {
						var hh = $('#tlsize').css('height');
						 $('#pl').css({ height: (parseInt(hh)+30)+'px'  });	
						
						});
					});
				


				
				 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: (event.pageY-200)+'px'  });	

				}
				
			}
			
			function closeinfo()
			{
			  	$('#pl').hide(200);
			}
			

	

function showhide(id)
	{
	 if (document.getElementById(id).style.display=="none")
	 	{document.getElementById(id).style.display="block";}
	 else
	 	{document.getElementById(id).style.display="none";}
	}

var Hint3Name = '';
// Заголовок, название скрипта, имя поля с логином
function findlogin(title, script, name)
{
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}


function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
	Hint3Name='';
}

function calccost(c,p)
{
var kol = document.getElementById("count").value;
document.getElementById("totallkr").innerHTML = (kol*c);
document.getElementById("totall_kom").innerHTML = (kol*1);
}

function calccount(c)
{
if (document.getElementById("cost")) { var cost = document.getElementById("cost").value; }
if (document.getElementById("totallkr")) { document.getElementById("totallkr").innerHTML = (cost*c); }
document.getElementById("totall_kom").innerHTML = (c*1);
}

var transfersale = true;
var tologin = '<? echo @($step==3?$komu['login']:''); ?>';


function transfer(to_id, login, txt, kredit, id, destiny,proto,ekrfalg,kol){
	var warn = "";
	if (proto == 100005 || proto == 100015 || proto == 100020 || proto == 100025 || proto == 100040 || proto == 100100 || proto == 100200 || proto == 100300) {
		warn = "</tr><tr><td colspan=3><font color=red>Ваучер будет привязан к покупателю после продажи</font>";
	}
	else
	if (ekrfalg == 1) {
		warn = "</tr><tr><td colspan=3><font color=red>Внимание! После покупки этот предмет нельзя будет передать или продать!</font>";
	}
	else
	if (ekrfalg == 2) {
		warn = "</tr><tr><td colspan=3>";
	}
	else
	if (ekrfalg == 3) {
		warn = "</tr><tr><td colspan=3>";
	}
	
	var msell='<BR>За <font color=red><b>'+kredit+' кр.</b>';
	if (kol>1) { 
		msell='<BR>За <b>'+kredit+'</b> кр./шт. в количестве <b>'+kol+'</b> шт. <br> Итого сумма сделки: <b>'+(kredit*kol)+'</b> кр.'; 
	}

	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Продажа предмета</td></tr><tr><td>'+
	'<form action="give22.php" method=get><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=FindLogin value=0><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=transfersale value="'+id+'">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> предлагает Вам купить предмет:<BR>'+
	txt+'<font color=red>'+msell+'</font><BR>Проводим сделку?</TD></TR><TR><TD align=center><INPUT TYPE=submit '+(destiny?" onclick='return confirm(\"Этот предмет может использовать только "+destiny+" Вы уверены, что хотите его купить?\")'":"")+' value="  ДА  " name="confirm"> &nbsp;&nbsp; <INPUT TYPE="submit" name="cancel" value=" НЕТ "">'+warn+'</TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}


function transfergold(to_id, login, id ,txt, gold, kr, mylim, err){

	if (err==0) {  mmsg='<BR>Проводим сделку?</center></TD></TR><TR><TD align=center><INPUT TYPE=submit name="confim" value="  ДА  "> &nbsp;&nbsp; <INPUT TYPE=submit name="cancel" value=" НЕТ " >' ; }
	else if (err==1) {  mmsg='<BR><b>Предложение превышает остаточный лимит покупки!</b></center></TD></TR><TR><TD align=center>' ; }
	else if (err==2) {  mmsg='<BR><b>У вас недостаточно кредитов для этой покупки!</b></center></TD></TR><TR><TD align=center>' ; }

	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Продажа монет</td></tr><tr><td>'+
	'<form action="give22.php" method=post><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=tcodeid value="'+id+'"><INPUT TYPE=hidden name=transfergoldconf value="gold">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> предлагает Вам купить:<BR><BR><center> <b>'+gold+'</b> <img src="http://i.oldbk.com/i/icon/coin_icon.png" alt="Монеты" title="Монеты" style="margin-bottom: -2px;">  за <b>'+kr+' кр.</b><BR><BR><BR>Доступно для покупки еще:<b> '+mylim+' монет</b><BR>(лимит обновляется в полночь)<br>'+mmsg+'</TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}


function save1(id){

	var gg;
	var f = document.f1;
	id='rzd'+id;
	gg=document.getElementById(id).value;
	if(gg==0)
	{document.getElementById(id).value=1;}
	else
	{document.getElementById(id).value=0;}
	f.submit();
	return true;
}

function closehiddeninv(id) {
	// free
	$("#id_"+id).html('<img src="http://i.oldbk.com/i/ajax-loader.gif" border=0>');

	document.getElementById('id_'+id).style.display = 'none';
	document.getElementById('txt_'+id).style.display = 'block';
	document.getElementById('txt1_'+id).style.display = 'none';
}

function showhiddeninv(proto,id,otdel,idkomu) {
	document.getElementById('id_'+proto).style.display = 'block';
	document.getElementById('txt_'+proto).style.display = 'none';
	document.getElementById('txt1_'+proto).style.display = 'block';

	// ajax load
	$.ajax({
		url: "give22.php?invload2=3&prototype="+proto+"&id="+id+"&otdel="+otdel+"&idkomu="+idkomu,
		cache: false,
		async: true,
		success: function(data){
			$("#id_"+proto).html(data);
		}
	});
}

</SCRIPT>
<?

if (isset($_POST['tcodeid']) AND isset($_POST['transfergoldconf']) )
	{
	$step=1;
	}


if ($step==3) {
        $item=array();
       	$it_id='';
        $chk_massa=0;
        $ff=0;
        $okk=1;
   //перевод кредов
	if ($_REQUEST['setkredit']>0 && $_REQUEST['to_id'] && $_REQUEST['sd4']==$user['id'] && $idbotkomu != 84) {
		$_REQUEST['setkredit'] = round($_REQUEST['setkredit'],2);
		if (($user['money']<$_REQUEST['setkredit']) OR ($_REQUEST['setkredit']<=0) ) { $mess="Недостаточно денег или неверная сумма"; $typet = "e"; }
		else {
		
				//подсчет и если ок то дальше - TEST
				/*
				if (($okk==1) )
				{
				if ( ($user['in_tower']!=15)  AND //не бс, не руины
					($user['ruines']==0) AND //не в руинах
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],1);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],1) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],1) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
									$okk=0;															 	
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
								$okk=0;							 	
							 	}
							}
							else
							{
							$tlo[$user['id']]=$user['login'];
							$tlo[$komu['id']]=$komu['login'];							
							
							$mess='';
							 foreach ($tco as $k => $l)
							 	{
								$mess.='У Персонажа "'.$tlo[$l].'" недостаточно лимита передач на сегодня! <br> ' ;
								}
							$okk=0;
							}
					
					}
				}////////////////////////////////////////////////////////////////////////////////////////////////////////			
				*/
		if ($okk==1)
		{
			if ((mysql_query("UPDATE `users` set money=money-".strval($_REQUEST[setkredit])." where id='".$user['id']."'")) &&
			    (mysql_query("UPDATE `users` set money=money+".strval($_REQUEST[setkredit])." where id='".$idkomu."'")))

				{
					if($_POST[settext])
					{
						$text1=$_POST['settext'];
						$text1 = preg_replace("~&amp;~i","&",$text1);
						$text1 = preg_replace("~&lt;B&gt;~i","<B>",$text1);
						$text1 = preg_replace("~&lt;/B&gt;~i","</B>",$text1);
						$text1 = preg_replace("~&lt;U&gt;~i","<U>",$text1);
						$text1 = preg_replace("~&lt;/U&gt;~i","</U>",$text1);
						$text1 = preg_replace("~&lt;I&gt;~i","<I>",$text1);
						$text1 = preg_replace("~&lt;/I&gt;~i","</I>",$text1);
						$text1 = preg_replace("~&lt;CODE&gt;~i","<CODE>",$text1);
						$text1 = preg_replace("~&lt;/CODE&gt;~i","</CODE>",$text1);
						$text1 = preg_replace("~&lt;b&gt;~i","<b>",$text1);
						$text1 = preg_replace("~&lt;/b&gt;~i","</b>",$text1);
						$text1 = preg_replace("~&lt;u&gt;~i","<u>",$text1);
						$text1 = preg_replace("~&lt;/u&gt;~i","</u>",$text1);
						$text1 = preg_replace("~&lt;i&gt;~i","<i>",$text1);
						$text1 = preg_replace("~&lt;/i&gt;~i","</i>",$text1);
						$text1 = preg_replace("~&lt;code&gt;~i","<code>",$text1);
						$text1 = preg_replace("~&lt;/code&gt;~i","</code>",$text1);
						$text1 = preg_replace("~&lt;br&gt;~i","<br>",$text1);
						if(strlen($text1)>70)
						{
							$text1=substr($text1,0,70);
						}
						
					}
					
					
					$mess='Удачно переданы '.strval($_REQUEST[setkredit]).' кр персонажу '.$komu['login']. ($text1!=''?'. Детали платежа: '.$text1:'');
					addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" передал вам <B>'.strval($_REQUEST[setkredit]).' кр</B>.'.($text1!=''?' Детали платежа: '.$text1:''),'{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
						//new_delo
	  		    		$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$user['money']-=$_REQUEST[setkredit];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$komu['id'];
					$rec['target_login']=$komu['login'];
					$rec['type']=36;//передача кредитов
					$rec['sum_kr']=strval($_REQUEST[setkredit]);
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']='';
					$rec['item_name']='';
					$rec['item_count']=0;
					$rec['item_type']=0;
					$rec['item_cost']=0;
					$rec['item_dur']=0;
					$rec['item_maxdur']=0;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['add_info']=$text1;
					add_to_new_delo($rec); //юзеру
					$rec['type']=37;//получение кредитов
  		    			$rec['owner']=$komu[id];
					$rec['owner_login']=$komu[login];
					$rec['owner_balans_do']=$komu['money'];
					$komu['money']+=$_REQUEST[setkredit];
					$rec['owner_balans_posle']=$komu['money'];
					$rec['target']=$user['id'];
					$rec['target_login']=$user['login'];
					add_to_new_delo($rec); //кому

					
			}
			else {
				$mess='Произошла ошибка!';
				$typet = "e";
			}
		
		}
		
		}
	}
	else if (($_REQUEST['setgold']) and ($user['level']>=10) )
	{
		$testrow = mysql_fetch_array(mysql_query("SELECT * FROM `trade` WHERE to_id='{$user['id']}' and `baer` ='{$komu['id']}'  LIMIT 1;"));
		
		if ($testrow['id']>0)
		{
				$mess="С этим персонажем есть незаконченная сделка!";
				$typet = "e";
		}
		elseif ($komu['level']>=10)
		{
		$send_gold=(int)$_REQUEST['setgold'];
		if (($send_gold>0) AND ($send_gold<=$user['gold']) )
						{
								$vkr=round($send_gold*$GOLD_GIVE_KURS);
								mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer` ,`zalog`) VALUES 	('{$_SESSION['uid']}','{$user['login']}','Продажа монет','$vkr','".mt_rand(111111,999999)."',{$_REQUEST['to_id']} ,4);") or die(mysql_error()."!!!");
								$mess = 'Предложение персонажу '.$komu['login'].' сделано.';
								addchp('<font color=red>Внимание!</font> <B>'.$user[login].'</B> предлагает Вам купить <b>'.$send_gold.'</b> монет за <b>'.$vkr.'</b> кр. <BR>\'; top.frames[\'main\'].location=\'http://capitalcity.oldbk.com/give22.php\'; var z = \'   ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
						}
						else
						{
						$mess="Недостаточно денег или неверная сумма";
						$typet = "e";
						}
		}
		else
		{
						$mess="Продажа монет доступна чарам 10 уровня и выше!";
						$typet = "e";
		}
	
	}


	//передача предмета за 1 кр и подарок

    $_REQUEST['gift']== '1'? '1' : '0';
    $gift=$_REQUEST['gift'];

	if ($_REQUEST['setobject'] && $_REQUEST['to_id'] && $gift>=0 && $_REQUEST['sd4']==$user['id'] && $_GET['s4i']==$user['sid']) 
	{

        if(!$_GET['count'])
        {
        	$count=1;
        	$sql=' AND id='.mysql_escape_string($_REQUEST['setobject']);
        }
        else
        {
        	$count=(int)$_GET['count'];
        	$sql=' AND prototype='.mysql_escape_string((int)$_GET['set']).' AND `group`=1 ';
        }
        //делаем доп проверку веса лдя архивариусов, так как их шмот висит в инвентаре олдбк, с разделением по месту производства шмотки (сити)
        if($idkomu==83 || $idkomu==136)
        {
        	//Fix на переполнение мешка арха в разных городах
        	$sql1="SELECT sum(`massa`) as massa FROM oldbk.`inventory` USE INDEX (owner_3) WHERE `owner` = '".$idkomu."' AND duration=".(int)$_GET[tmp]." AND `dressed` = 0  AND `setsale` = 0 AND  bs_owner='".$user[in_tower]."' AND idcity='".$user[id_city]."'; ";        	
        	$mto = mysql_fetch_array(mysql_query($sql1));
        	
        	$d = mysql_fetch_array(mysql_query("SELECT sum(`gmeshok`) FROM oldbk.`inventory` WHERE `owner` = '{$idkomu}' AND bs_owner='".$user['in_tower']."' AND `setsale` = 0 AND `gmeshok`>0 AND idcity='".$user[id_city]."'; "));
		$s = mysql_fetch_array(mysql_query("SELECT sila FROM `users` WHERE `id` = '{$idkomu}' LIMIT 1 ; "));
	//	return('30000');
		$allmass=($s['sila']*4+$d[0]);
		$ttype=0;
        }
        else
        {
		if ($idkomubot != 84) {
	        	$sql1="SELECT sum(`massa`) as massa FROM oldbk.`inventory` USE INDEX (owner_3) WHERE `owner` = ".$idkomu." AND duration=".(int)$_GET[tmp]." AND `dressed` = 0 AND `setsale` = 0 AND  bs_owner='".$user[in_tower]."'; ";
        		$mto = mysql_fetch_array(mysql_query($sql1));
        		$allmass=get_meshok_to($idkomu);
		}
	}
	//2123456804 нельзя передавать, упакованный подарок. ekr_flag=0 нельзя передавать предметы только что купленные в березе
	
	if ($user['klan']=='radminion' || $user['klan']=='Adminion' || $user['id'] == 8325) //AND prototype!=2123456804 and ekr_flag=0 and otdel!=72 and type!=77
	{
	        $sql="SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' ".$sql."	        
		AND dressed=0 AND bs_owner='".$user[in_tower]."' AND `setsale` = 0
		AND duration=".(int)$_GET[tmp]."  ".$VAUCHER." AND `present` = '' and type!=99  LIMIT ".$count.";";
	}
	else
	{
        	$sql="SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' ".$sql."
		AND dressed=0 AND bs_owner='".$user[in_tower]."' AND `setsale` = 0 AND prototype!=40000001 AND prototype!=2123456804 and ekr_flag=0 and otdel!=72 and type!=77 and sowner=0
		AND duration=".(int)$_GET[tmp]."  ".$VAUCHER." AND `present` = '' and type!=99  LIMIT ".$count.";";
	}
	  
	$data = mysql_query($sql);
	
	  	if (mysql_error())
	  		{
	  			$fp = fopen ("/www/other/giveerror.txt","a");
				flock ($fp,LOCK_EX); 
				fputs($fp , $sql."\n"); 
				fflush ($fp); 
				flock ($fp,LOCK_UN); 
				fclose ($fp); 
				die();
	  		}
		
 	if(mysql_num_rows($data)>0)
	{
      		while($res=mysql_fetch_array($data))
      		{
			if (in_array($res['prototype'],$vauch_a) && $komu['id'] != 102904 && $komu['id'] != 8540 && $komu['id'] !=182783 && $komu['id']!=457757 && $komu['id'] !=8325) continue;

			  if($gift==1) { 
			  			if ( (($res['art_param'] !='') or ($res['ab_mf'] >0 )  or ($res['ab_bron'] >0 )  or ($res['ab_uron'] >0 ))     and ($res['sowner'] !=0)) continue;
			                    }

                	$chk_massa+=$res[massa];
                	$item[$ff]=$res;
			$ff++;
		}
	}
	else
	{
		$mess=" Предмет не найден в рюкзаке";
		$typet = "e";
	}
        //тут делаем все расчеты:
        if(count($item)>0){
		
		        $newmass=$mto[massa]+$chk_massa;
		        if (($newmass<=$allmass) OR ($user['klan']=='radminion') OR ($user['klan']=='Adminion') OR ($user['id']==8325) or $idkomubot == 84)
		        {
		          $prez='';
		          $per=0;
			        for($jj=0;$jj<count($item);$jj++)
			        {
		                   if($per==10)
		                   {
		                   	$per=0;
		                   	$pp='<br>';
		                   }
		                   else{
		                   	$pp='';
		                   }
			               	       /*  if($dem[type]=='200' && ($dem[otdel]=='7' || $dem[otdel]=='77') && $dem[dategoden]==0)
		                                {
		                                	$sql=' `goden`="180", `dategoden`="'.(time()+60*60*24*30*3).'", ';
		                                }
		                                 if($dem[type]=='200' && ($dem[otdel]=='71' || $dem[otdel]=='73') && $dem[dategoden]==0)
		                                {
		                                	$sql=' `goden`="90", `dategoden`="'.(time()+60*60*24*30*6).'", ';
		                                }
			                        */
			                   if($gift==1 && $item[$jj][type]==200 && ($item[$jj][otdel]=='7' || $item[$jj][otdel]=='77') && $item[$jj][dategoden]==0 )
		                       {
		                          $prezs=' `goden`="90", `dategoden`="'.(time()+60*60*24*30*3).'", ';
		                       }
		
			                   if($gift==1 && $item[$jj][type]==200 && ($item[$jj][otdel]=='71' || $item[$jj][otdel]=='73') && $item[$jj][dategoden]==0 )
		                       {
		                          $prezs=' `goden`="180", `dategoden`="'.(time()+60*60*24*30*6).'", ';
		                       }
		
			                   $sql_it_id.= $item[$jj][id].',';
			                   $sql_delo.=get_item_fid($item[$jj]).','.$pp;
			                   $per++;
				}

			        if($ttype==1)
			        {
			        	$jj=$jj*10;
			        	
		                        if($idkomu==83 || $idkomu==136 || $idkomubot == 84)
		                        {
						$jj = 1;
		                        	$gift=1;
		                        }
		                        else
		                        {
					        $gift=0;
		                        }
			        }

			        if($ttype==2)
			        {
			        	$jj=$jj*100;
					//if ($idkomubot != 84) $gift = 0;
		                        if($idkomu==83 || $idkomu==136 || $idkomubot == 84)
		                        {
						$jj = 1;
		                        	$gift=1;
		                        }
		                        else
		                        {
					        $gift=0;
		                        }

			        }


			        $sql_it_id=substr($sql_it_id,0,-1);
			        $sql_delo=substr($sql_delo,0,-1);
			        $ook=0;

			if($gift==0)
			{
				if($user[money]>=$jj)
				{
					$money_sql="update `users` set `money`=`money`-".$jj." where `id`='".$user['id']."'";
					$prez='';
					$txt='Передан';
					$txt1='передано';
					$txt2='передал';
					$ook=1;
				}
				else
				{
					$mess='Недостаточно денег на оплату передачи!';
					$typet = "e";
				}
			}
			else
			{
				if($user[money]>=0)
				{
					$money_sql="";
					
					$prez=', present = "'.$user['login'].($item[otdel]==72?':|:'.$user[id]:'').'"';
					$txt='Подарен';
					$txt1='подарено';
					$txt2='подарил';
					$ook=1;
				}
			}
                    if($gift==1)
                       {
                       	 $gsql=',add_time='.time();
                       }
                       else
                       {
                       	 $gsql='';
                       }

				//подсчет и если ок то дальше - TEST
				if (($ook==1) )
				{
				if ( ($user['in_tower']!=15)  AND //не бс, не руины
					($user['ruines']==0) AND //не в руинах
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],$jj);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],$jj) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],$jj) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
									$ook=0;															 	
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
								$ook=0;							 	
								
							 	}
							}
							else
							{
							$tlo[$user['id']]=$user['login'];
							$tlo[$komu['id']]=$komu['login'];							
							
							 foreach ($tco as $k => $l)
							 	{
								$mess.='У Персонажа "'.$tlo[$l].'" недостаточно лимита передач на сегодня! <br> ' ;
								$typet = "e";
								}
							$ook=0;
							}
					
					}
				}////////////////////////////////////////////////////////////////////////////////////////////////////////
					




		           if($ook==1){
		           	    $counter=0;
                         		while($counter<100)
			            {
			                if($item[0][add_pick]!='')
			                {
			                	undress_img($item[0]);
			                	$ok1=1;
			                }
			                else
			                {
			                	$ok1=1;
			                }

			                $sql="update oldbk.`inventory` set ".$prezs." `owner` = ".$komu['id']." ".$prez." ".$gsql." where `id` in (".$sql_it_id.") AND prototype!=40000001 AND prototype!=2123456804 and `owner`= '".$user['id']."';";
                            //echo $sql;
			                if(mysql_query($sql) && $ok1==1)
			                {
				               	 	if($money_sql){
				               	 	mysql_query($money_sql);
					               	 }
					               	if (($user['in_tower']==0 || $ttype==1 || $ttype==2) && $item[0][labonly]==0 && ($item[0][bs_owner]==0 || ($item[0][bs_owner]==15 && $user['in_tower'] == 15)))
					               	{
				        		//new_delo
				        			if(($ttype==1 || $ttype==2)  && $gift<1)
				        			{
                                        					$rec['owner']=$user[id];
										$rec['owner_login']=$user[login];
										$rec['owner_balans_do']=$user['money'];
										$rec['owner_balans_posle']=$user['money']-$jj;;
										$rec['target']=$komu['id'];
										$rec['target_login']=$komu['login'];
										$rec['type']=237;
										$rec['sum_kr']=0;
										$rec['sum_kom']=$jj;
					                                        $rec['add_info']='Заплатил за передачу в БС';
					                                        add_to_new_delo($rec);
				        			}
				        			else
				        			{
					  		    			$rec['owner']=$user[id];
										$rec['owner_login']=$user[login];
										$rec['owner_balans_do']=$user['money'];
										$rec['owner_balans_posle']=($gift==1?$user[money]:$user[money]-$jj);
										$rec['target']=$komu['id'];
										$rec['target_login']=$komu['login'];
										$rec['type']=($gift==1?38:39);//дарю/передаю предмет
										$rec['sum_kr']=0;
										$rec['sum_ekr']=0;
										$rec['sum_kom']=($gift==1?0:1);
										$rec['item_id']=$sql_delo;
										$rec['item_name']=$item[0]['name'];
										$rec['item_count']=$jj;
										$rec['item_type']=$item[0]['type'];
										$rec['item_cost']=$item[0]['cost'];
										$rec['item_dur']=$item[0]['duration'];
										$rec['item_maxdur']=$item[0]['maxdur'];
										$rec['item_ups']=$item[0]['ups'];
										$rec['item_unic']=$item[0]['unik'];

										$rec['item_incmagic_id']=$item[0]['includemagic'];
	                                    					$rec['item_ecost']=$item[0]['ecost'];
										$rec['item_proto']=$item[0]['prototype'];
                                        					$rec['item_sowner']=($item[0]['sowner']>0?1:0);
										$rec['item_incmagic']=$item[0]['includemagicname'];
										$rec['item_incmagic_count']=$item[0]['includemagicuses'];
										$rec['item_arsenal']='';
										$rec['item_mfinfo']=$item[0]['mfinfo'];
										$rec['item_level']=$item[0]['nlevel'];

										add_to_new_delo($rec); //юзеру
										$rec['owner']=$komu[id];
										$rec['owner_login']=$komu[login];
										$rec['owner_balans_do']=$komu['money'];
										$rec['owner_balans_posle']=$komu['money'];
										$rec['target']=$user['id'];
										$rec['target_login']=$user['login'];
										$rec['type']=($gift==1?98:99);//получаю/в подарок предмет
										add_to_new_delo($rec); //кому

										//region gift checker
										if($gift == 1) {
											try {
												$UserObj = new \components\models\User($user);
												$Quest = $app->quest->setUser($UserObj)->get();

												$Checker = new \components\Component\Quests\check\CheckerGift();
												$Checker->shop_id = \components\Helper\ShopHelper::TYPE_ALL;
												$Checker->item_id = $item[0]['prototype'];
												$Checker->user_to = new \components\models\User($komu);
												$Checker->operation_type = \components\Component\Quests\pocket\questTask\GiftTask::OPERATION_TYPE_GIVE;
												if (($Item = $Quest->isNeed($Checker)) !== false) {
													$Quest->taskUp($Item);
												}

												unset($UserObj);
												unset($Quest);
											} catch (Exception $ex) {
												\components\Helper\FileHelper::writeException($ex, 'fshop');
											}
										}
										//endregion

			                                    }
			
							}

								if ($ttype == 2) {
				                			$mess='Удачно '.$txt1.' "'.$item[0]['name'].'" (x'.($jj/100).'), ('.$sql_delo.') персонажу '.$komu['login'];
									addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" '.$txt2.' вам "'.$item[0]['name'].'" (x'.($jj/100).')  ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
								} else {
				                			$mess='Удачно '.$txt1.' "'.$item[0]['name'].'" (x'.($jj).'), ('.$sql_delo.') персонажу '.$komu['login'];
									addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" '.$txt2.' вам "'.$item[0]['name'].'" (x'.$jj.')  ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
								}
								if($gift==0){
									$user['money']-=$jj;
									$mess.=' за '.$jj.'кр.';
								}

								$counter=100;
							}
							else
							{
							    	if($counter==0||$counter==10||$counter==50||$counter==99)
								{
									$mess = 'Произошла ошибка 1. попробуйте еще раз.';
									$typet = "e";
									///telepost('A-Tech','<font color=red>Внимание! give22.php str 571</font> Ошибка передачи: каунтер='.$counter.' Персонаж '.$user['login'].' '.$txt.'/'.$txt2. ' ' . $item[0]['name']. ' '.$item[0]['id'].' кол-во'.$jj . ' Кому:'.$komu['login']);
		                            				
		                            				telepost('Bred','<font color=red>Внимание! give22.php str 571</font> Ошибка передачи: каунтер='.$counter.' Персонаж '.$user['login'].' '.$txt.'/'.$txt2. ' ' . $item[0]['name']. ' '.$item[0]['id'].' кол-во'.$jj . ' Кому:'.$komu['login']);
							    	}
							    	$counter++;
							}
						}
					}
		        }
		        else {
					$mess='У персонажа "'.$komu['login'].'" переполнен рюкзак!';
					$typet = "e";
				}
	    }
	}
	
	   //продажа
	if ($_REQUEST['cost'] >= 1 && $_REQUEST['to_id'])
		{
		    	if($user['room']>500&&$user['room']<=560){
		    	//Тут продажи фигни за нал апрещены
		    	//думаю сюда же лабу надо добавить =)
		    	}
			else
	    		{
	    		$_REQUEST['cost']=round(floatval($_REQUEST['cost']),2);
	    		$sellcount=(int)($_REQUEST['count']);
	    		
	    				if (($sellcount>1) and isset($_GET['set']) and ((int)($_GET['set'])>0) )
	    				{
	    					$sproto=(int)($_GET['set']);
						$res = mysql_fetch_array(mysql_query("SELECT *, count(*) as salecount  FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed`=0 AND `prototype` = '{$sproto}' ".$VAUCHER." AND `bs_owner` ='0' AND prototype!=40000001 AND prototype!=2123456804 and type!=99 AND setsale=0 and labonly = 0 group by prototype;"));	    				
	    				}
					else
					{
					$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed`=0 and sowner=0 AND `id` = '{$_REQUEST['id_th']}' ".$VAUCHER." AND `bs_owner` ='0' AND prototype!=40000001 AND prototype!=2123456804 and type!=99 AND setsale=0 and labonly = 0 LIMIT 1;"));
					$sellcount=1;
					$res['salecount']=1;
					}
					
					if (!$res['id']) { $mess="Предмет не найден в рюкзаке"; $typet = "e";}
					elseif ($res['dressed']!=0) { $mess="Сначала необходимо снять предмет."; $typet = "e";}
					elseif ($komu['align']==4) { $mess="С хаосниками торговые сделки запрещены."; $typet = "e";}
					elseif ($res['sowner'] !=0) { $mess="Этот предмет нельзя продать!"; $typet = "e";}
					elseif ($res['otdel'] ==72) { $mess="Этот предмет нельзя продать!"; $typet = "e";}
					elseif ($res['type'] ==77) { $mess="Этот предмет нельзя продать!"; $typet = "e";}
					elseif ($sellcount>$res['salecount']) { $mess="Ошибка количества предметов"; $typet = "e";}					
					elseif (in_array($res['prototype'],$vauch_a) && $res['sowner'] > 0) { $mess="Предмет не найден в рюкзаке"; $typet = "e";}
					elseif ($user['money']<(1*$sellcount)) { $mess="Недостаточно денег, чтобы оплатить налог на продажу!"; $typet = "e"; }
					//elseif ($user['in_tower'] == 1) {$mess = "Не в Башне Смерти.......";}
					else {
						$value=$res;
						if (@$value['present']) { $mess='Нельзя передавать подарки'; $typet = "e";}
						else{
							#KOMOK_LOG
							$row = $res;
								function calb ($b) {
									global $re;
										$re .= $b;
								}
								$row[GetShopCount()] = $sellcount;

								$re .= "<table width=100%><TR ><TD align=center ><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><BR></TD>";
								$re .= "<TD valign=top>";

								if ($res['prototype'] == 100005) $_REQUEST['cost'] = 5*18;
								if ($res['prototype'] == 100015) $_REQUEST['cost'] = 15*18;
								if ($res['prototype'] == 100020) $_REQUEST['cost'] = 20*18;
								if ($res['prototype'] == 100025) $_REQUEST['cost'] = 25*18;
								if ($res['prototype'] == 100040) $_REQUEST['cost'] = 40*18;
								if ($res['prototype'] == 100100) $_REQUEST['cost'] = 100*18;
								if ($res['prototype'] == 100200) $_REQUEST['cost'] = 200*18;
								if ($res['prototype'] == 100300) $_REQUEST['cost'] = 300*18;
								
								
								if ($res['ekr_flag'] == 3) {
									if ($res['ecost'] > 0) {
										$testcost = ($res['ecost']*EKR_TO_KR); // для предметов из березы цена жесткая екры * курс
										if ($res['type'] == 50) {
											$testcost = $testcost - (($testcost / $res['maxdur']) * $res['duration']);
										}
	
										if ($_REQUEST['cost'] < $testcost) $_REQUEST['cost'] = $testcost;

										if ($_REQUEST['cost']<=0) { die(); }
									} else {
										die();
									}
								} elseif ($res['ekr_flag'] > 0 && $res['ekr_flag'] != 3) {
									$_REQUEST['cost'] = ($res['ecost']*EKR_TO_KR); // для предметов из березы цена жесткая екры * курс
									if ($res['type'] == 50) {
										$_REQUEST['cost'] = $_REQUEST['cost'] - (($_REQUEST['cost'] / $res['maxdur']) * $res['duration']);
									}

									if ($_REQUEST['cost']<=0) { die(); }
									if ($res['prototype'] == 55510350 || $res['prototype'] == 55510352 || $res['prototype'] == 55510351 || $res['prototype'] == 410021 || $res['prototype'] == 410022 || $res['prototype'] == 410026 ) {
										if (ceil(($res['dategoden'] - time())/(60*60*24)) <= 6) {
											die();
										}
									}
									
								}
								

								//function calb($t) {
								//    global $re;
								//    $re .= $t;
								//}

								ob_start();
									showitem ($row);
								//ob_end_flush();
								$re .= ob_get_clean();
								$re .= "</TD></TR></table>";
								$re = str_replace("\r\n", "", $re);
								$re = str_replace("\n", "", $re);
								$re = str_replace("'", "\'", $re);
								$mess = 'Предложение персонажу '.$komu['login'].' сделано.';
								
								mysql_query("update oldbk.`inventory` set `tradesale` = '".$_REQUEST['cost']."' where `id`='".$res['id']."' AND prototype!=40000001 AND prototype!=2123456804 and `owner`= '".$res['owner']."';") or die(mysql_error()."!!");
								mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer`, `proto` , `kol` ) VALUES
										('{$_SESSION['uid']}','{$user['login']}','".mysql_escape_string($re)."','{$_REQUEST['cost']}','{$_REQUEST['id_th']}','{$_REQUEST['to_id']}','{$res['prototype']}' ,'{$sellcount}' );") or die(mysql_error()."!!!");

						}
					}

				}
			}
	else
	if ($_REQUEST['transfersale'] && $_REQUEST['to_id']) 
	{
	$komsa=1;
	
	    $_transfersale = (int)$_REQUEST['transfersale'];
	    $_trade_row = mysql_fetch_array(mysql_query("SELECT * FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale} LIMIT 1;"));
		
		if (!($_trade_row && $_trade_row['id'] > 0))
		{
			$_trade_row = false;
        	}

		if (($_trade_row==true) AND ($_trade_row['kol']>1) AND ($_trade_row['proto']>0) )
			{
			//проверка наличия нужного количества
			$res = mysql_fetch_array(mysql_query("SELECT *, count(*) as salecount  FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `dressed`=0 and setsale=0 and type!=99 and labonly = 0 AND `owner` = '{$_REQUEST['to_id']}' AND `prototype` = '{$_trade_row['proto']}' ".$VAUCHER." AND `bs_owner` = 0 AND prototype!=40000001 AND prototype!=2123456804 group by prototype;"));			
			}
			elseif ($_trade_row==true)
			{
			$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `dressed`=0 and setsale=0 and sowner=0 and type!=99 and labonly = 0 AND `owner` = '{$_REQUEST['to_id']}' AND `id` = '{$_transfersale}' ".$VAUCHER." AND `bs_owner` = 0 AND prototype!=40000001 AND prototype!=2123456804 LIMIT 1;"));			
			if ($res) {$res['salecount']=1; }
			}


		if(!$_trade_row) {
			$mess = '<b>Сделка не найдена</b>';
			$typet = "e";
		}  elseif(isset($_REQUEST['cancel']) && $_trade_row) 
		{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
			$mess = '<b>Вы отказались от сделки</b>';
			$typet = "e";
		} elseif (($_trade_row['kol']>($res['salecount'])) and ($_trade_row['kol']>1) )
		{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
			$mess ='<b>У продающей стороны недостаточное количество предметов.</b>';
			$typet = "e";
		}
		elseif (($user['money'] < ($res['tradesale']*$_trade_row['kol'])) and ($_trade_row['kol']>1) ) 
		{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
			$mess ='<b>Не хватает денег для совершения операции</b>';
			$typet = "e";
		} elseif ($komu['money']<($komsa*$_trade_row['kol']) ) 
		{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
			$mess ='<b>У продающей стороны недостаточно средств для оплаты комиссии.</b>';
			$typet = "e";
		} elseif (in_array($res['prototype'],$vauch_a) && $res['sowner'] > 0) 
		{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");		
			$mess ='<b>Нельзя передавать привязанный ваучер.</b>';
			$typet = "e";
		} elseif($res[id]>0)
		{
		    if($res[add_pick]!='')
		              {
		              	undress_img($res);
		              	$ok=1;
		              }
			  else
			  {
			      $ok=1;
			  }
			  
			  
				//подсчет и если ок то дальше - TEST
				if (($ok==1)  )
				{
				if ( ($user['in_tower']!=15)  AND //не бс, не руины
					($user['ruines']==0) AND //не в руинах
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],$_trade_row['kol']);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],$_trade_row['kol']) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],$_trade_row['kol']) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
								 	$typet = "e";
									$ok=0;															 	
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
							 	$typet = "e";
								$ok=0;							 	
							 	}
							}
							else
							{
							$tlo[$user['id']]=$user['login'];
							$tlo[$komu['id']]=$komu['login'];							
							
							$mess='';
							 foreach ($tco as $k => $l)
							 	{
								$mess.='У Персонажа "'.$tlo[$l].'" недостаточно лимита передач на сегодня! <br> ' ;
								$typet = "e";
								}
							$ok=0;
							}
					
					}
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////			  
			  
			  
			  
			  
			  
			  
			  
			  if($ok==1)
			  {
				if ($res['ekr_flag'] == 1) 
				{
				  	$add_present=",  present='".$komu['login']."'  "; //  после продажи покупателю падает подарком
				  	
				  	
			  	}

				if ($res['ekr_flag'] == 2) {
				  	//$add_present=",  sowner='".$komu['id']."'  "; //  после продажи перевязываем вещь
				}

			$items_tosale_id=array();
			$items_tosale_id_str=array();
			  if ($_trade_row['kol']>1)
			  	{
			  	
   					$prep_items = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `dressed`=0 and setsale=0 and sowner=0 and type!=99 and labonly = 0 AND `owner` = '{$_REQUEST['to_id']}' AND `prototype` = '{$_trade_row['proto']}' ".$VAUCHER." AND `bs_owner` = 0 LIMIT {$_trade_row['kol']}");			
   					while($row = mysql_fetch_assoc($prep_items)) 
   					{
   					$items_tosale_id[]=$row[id];
   					$items_tosale_id_str[]=get_item_fid($row);
   						
								if ($row['prototype']==33333)
							  	{
							  	//если билет лото, то правим индекс для всех предметов
							  	mysql_query("UPDATE `oldbk`.`item_loto` SET `owner`='{$user['id']}' WHERE `id`='{$row['mffree']}' ");
							  	}
   					}
   					
   					mysql_query_100("update oldbk.`inventory` set `owner` = ".$user['id']." ".$add_present."  where `id` in (".implode(",",$items_tosale_id).") and `owner`= '".$res['owner']."' ");
   					
			  	}
			  	else
			  	{
			  		if ($res['prototype']==33333)
				  	{
				  	//если билет лото, то правим индекс если продается 1 шт.
				  	mysql_query("UPDATE `oldbk`.`item_loto` SET `owner`='{$user['id']}' WHERE `id`='{$res['mffree']}' ");
				  	}
			  	
			    	mysql_query_100("update oldbk.`inventory` set `owner` = ".$user['id']." ".$add_present."  where `id`='".$res['id']."' and `owner`= '".$res['owner']."' AND prototype!=2123456804 AND prototype!=40000001");
			    	}

			    	
			    	mysql_query("update `users` set `money`=`money`-".($res['tradesale']*$_trade_row['kol'])." where `id`='".$user['id']."'");
			    	mysql_query("update `users` set `money`=`money`+".(($res['tradesale']*$_trade_row['kol'])-($komsa*$_trade_row['kol']))." where `id`='{$_REQUEST['to_id']}'");
			    	
				mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
				
				    if ($user['in_tower']==0 && $res[labonly]==0 && $res[bs_owner]==0)
				    {
				    		//new_delo
	  		    			$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money']-($res['tradesale']*$_trade_row['kol']);
						$rec['target']=$komu['id'];
						$rec['target_login']=$komu['login'];
						$rec['type']=40;//купил предмет
						$rec['sum_kr']=($res['tradesale']*$_trade_row['kol']);
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						if (count($items_tosale_id_str)>0) { $rec['aitem_id']=implode(",",$items_tosale_id_str); } else { $rec['item_id']=get_item_fid($res); }
						$rec['item_name']=$res['name'];
						$rec['item_count']=$_trade_row['kol'];
						$rec['item_type']=$res['type'];
						$rec['item_cost']=$res['cost'];
						$rec['item_dur']=$res['duration'];
						$rec['item_maxdur']=$res['maxdur'];
						$rec['item_ups']=$res['ups'];
						$rec['item_unic']=$res['unik'];
						$rec['item_incmagic_id']=$res['includemagic'];
	                    			$rec['item_ecost']=$res['ecost'];
						$rec['item_proto']=$res['prototype'];
                        			$rec['item_sowner']=($res['sowner']>0?1:0);
						$rec['item_incmagic']=$res['includemagicname'];
						$rec['item_incmagic_count']=$res['includemagicuses'];
						$rec['item_arsenal']='';
						$rec['item_mfinfo']=$rec['mfinfo'];
						$rec['item_level']=$rec['nlevel'];

						add_to_new_delo($rec); //юзеру
						$rec['sum_kom']=($komsa*$_trade_row['kol']);
						$rec['owner']=$komu[id];
						$rec['owner_login']=$komu[login];
						$rec['owner_balans_do']=$komu['money'];
						$rec['owner_balans_posle']=$komu['money']+(($res['tradesale']*$_trade_row['kol'])-($komsa*$_trade_row['kol']));
						$rec['target']=$user['id'];
						$rec['target_login']=$user['login'];
						$rec['type']=41;//продал предмет
					    	add_to_new_delo($rec); //кому

				    }
				    else
				    {
	                    			$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money']-($res['tradesale']*$_trade_row['kol']);
						$rec['target']=$komu['id'];
						$rec['target_login']=$komu['login'];
						$rec['type']=237;
						$rec['sum_kr']=($res['tradesale']*$_trade_row['kol']);
						$rec['item_count']=$_trade_row['kol'];
						$rec['sum_kom']=0;
						$rec['add_info']='Купил не выносимый предмет в БС/лабе';
						add_to_new_delo($rec);
				    }


			    	$mess='Удачно куплено "'.$res['name'].'" (x'.$_trade_row['kol'].')  у персонажа '.$komu['login'];
			    	$mess2='Удачно куплено "'.$res['name'].'" (x'.$_trade_row['kol'].')  персонажем '.$user['login'];
			    	addchp ('<font color=red>Внимание!</font>  '.$mess2,'{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
			    	addchp ('<font color=red>Внимание!</font>  '.$mess,'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
			    				    	
			    	$user['money']-=($res['tradesale']*$_trade_row['kol']);
       		  }
       		  else
       		  {
       		  	if ($mess=='')
       		  	{
       		  	$mess='Ошибка продажи. попробуйте еще раз';
			$typet = "e";
       		  	}
       		  }
		}
	}

}
?>
</HEAD>
<body bgcolor=e2e0e0>
<script type='text/javascript'>
RecoverScroll.start();
</script>
<div id="pl" style="z-index: 1; position: absolute; left: 155px; top: 120px;
				width: 480px; height:230px; background-color: #CCC3AA; 
				border: 1px solid black; display: none;">
</div>
<div id=hint3 class=ahint></div><div id=hint4 class=ahint></div>
<H3>Передача предметов/кредитов другому игроку</H3>
<TABLE width=100% cellspacing=0 cellpadding=0>
<TR><TD>
<? if ($step==3) 
{
	echo 'К кому передавать: <font color=red><SCRIPT>drwfl("'.@$komu['login'].'",'.@$komu['id'].',"'.@$komu['level'].'","'.@$komu['align'].'","'.@$komu['klan'].'")</SCRIPT></font>';
	echo "<INPUT TYPE=button value='Сменить' onClick=\"findlogin('Передача предметов','give22.php','FindLogin')\"><BR>";
}
 else
{
	$roww = mysql_fetch_array(mysql_query("SELECT * FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;"));
	$del_trade=true;

		if (($roww[zalog]==4) AND ($roww['id'])) //если =4 то это продажа золота
		{
		//echo "start.....";
		$send_gold=round($roww['kr']/$GOLD_GIVE_KURS);
	
		$gold_lim=800; //общий лимит для покупок на день http://tickets.oldbk.com/issue/oldbk-2618
		
		$get_my_lim=mysql_fetch_array(mysql_query("select sum(gold) as gold  from users_gold_log where baer_owner='{$user['id']}' and `tdate`=CURDATE() "));

		$my_lim=(int)$get_my_lim['gold'];
		$gold_lim-=$my_lim;
		
				if (($_REQUEST['cancel']) and  ($_REQUEST['tcodeid']==$roww['id']) )
				{
				//отказ удаляем заявку
				$mess='Вы отказались от сделки!'; $typet = "e"; 				
				$del_trade=true;		
				}
				elseif (($_REQUEST['confim']) and  ($_REQUEST['tcodeid']==$roww['id']) )
				{
				//echo "проводим операцию...";

					 if ($gold_lim>=$send_gold)  	//1. проверка лимита на возможность покупки
					 	{
					 		
					 		if ($user['money']>=$roww['kr']) //2. проверка КР на покупателе
					 			{
									$ftelo = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$roww['to_id']}' ")); // продавец
					 			
	 								//3. проверка доступности золота на продавце
									$q = mysql_query('START TRANSACTION') or die();
									$q = mysql_query('SELECT * FROM users WHERE id = '.$roww['to_id'].' OR id = '.$user['id'].' FOR UPDATE') or die("stop:1");

									if ($ftelo['gold']>=$send_gold)
											{
												//4. все ок снимаем там золото и добавляем кр.
												//пишем в дело продавану
												//5. добавляем золото и отнимаем кр
												mysql_query('UPDATE users  SET `gold` = `gold` - '.$send_gold.' , `money`= `money` + '.$roww['kr'].' WHERE id = '.$roww['to_id']) or die("stop:3");
												mysql_query('UPDATE users  SET `gold` = `gold` + '.$send_gold.' , `money`= `money` - '.$roww['kr'].' WHERE id = '.$user['id']) or die("stop:4");	
																							
												//new_delo
												$rec=array();
								  		    		$rec['owner']=$user[id];
												$rec['owner_login']=$user[login];
												$rec['owner_balans_do']=$user['money'];
												
												$user['money']-=$roww['kr'];
												$user['gold']+=$send_gold;
												
												$rec['owner_balans_posle']=$user['money'];
												$rec['target']=$ftelo['id'];
												$rec['target_login']=$ftelo['login'];
												$rec['type']=3636;//передача монет
												$rec['sum_kr']=$roww['kr'];
												$rec['add_info'] = $send_gold."/".$user['gold'];																	
												if (add_to_new_delo($rec) === FALSE) die();

												$rec=array();												
							  		    			$rec['owner']=$ftelo['id'];
												$rec['owner_login']=$ftelo['login'];
												$rec['owner_balans_do']=$ftelo['money'];
												
												$ftelo['money']+=$roww['kr'];
												$ftelo['gold']-=$send_gold;												
												
												$rec['owner_balans_posle']=$ftelo['money'];
												$rec['type']=3737;//получение кредитов												
												$rec['sum_kr']=$roww['kr'];												

												$rec['target']=$user['id'];
												$rec['target_login']=$user['login'];
												$rec['sum_kr']=$roww['kr'];
												$rec['add_info'] = $send_gold."/".$ftelo['gold'];												

												if (add_to_new_delo($rec) === FALSE) die();
												
												//лог лимитов
												mysql_query("INSERT INTO `oldbk`.`users_gold_log` SET `trade_owner`='{$ftelo['id']}',`gold`='{$send_gold}',`baer_owner`='{$user['id']}',`kr`='{$roww['kr']}',`tdate`=NOW();") or die();
												
												$mess='Сделка прошла удачно, вы купили <b>'.$send_gold.'</b> монет за <b>'.$roww['kr'].'</b> кр. у персонажа <b>'.$ftelo['login'].'</b>'; $typet = "e"; 
				 								$del_trade=true;
				 								addchp('<font color=red>Внимание!</font> Удачно продано <b>'.$send_gold.'</b> монет за <b>'.$roww['kr'].'</b> кр.  персонажу <B>'.$user[login].'</B> ','{[]}'.$ftelo['login'].'{[]}',$ftelo['room'],$ftelo['id_city']);
												addchp('<font color=red>Внимание!</font> Удачно куплено <b>'.$send_gold.'</b> монет за <b>'.$roww['kr'].'</b> кр.  у персонажа <B>'.$ftelo[login].'</B> ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);				 								
				 								//1. продавцу монет чтобы приходило уведомление что сделка прошла (сейчас оно ему не приходит)
				 										
											}
											else
											{
								 			$mess='У Продавца уже нет этих монет! Сделка отменена!'; $typet = "e"; 
			 								$del_trade=true;		
											}
									$q = mysql_query('COMMIT') or die();
					 			}
					 			else
					 			{
					 			$mess='У Вас не достаточно кредитов для покупки этих монет!'; $typet = "e"; 
					 			}
					 	}
					 	else
					 	{
					 	$mess='У Вас исчерпан лимит покупки монет на сегодня!'; $typet = "e"; 
					 	}

				}
				else
				{
				$err=0;
					
					 if (!($gold_lim>=$send_gold))  	//1. проверка лимита на возможность покупки
					 	{
					 	$err=1;
					 	}
					 	elseif (!($user['money']>=$roww['kr'])) 
					 	{
					 	$err=2;
					 	}

				echo "<SCRIPT>transfergold({$roww['to_id']}, '{$roww['login']}', '{$roww['id']}',  '".str_replace("\r\n","",$roww['txt'])."', {$send_gold} , '{$roww['kr']}' , '{$gold_lim}', '{$err}' );</SCRIPT>";
				if ($err==0) { $del_trade=false; }
				}
		}
		else
		{
	  	    $rwx = mysql_fetch_array(mysql_query("SELECT `id` FROM oldbk.`inventory` WHERE `bs_owner` ='".$user['in_tower']."' AND `owner` = '".$roww['to_id']."' AND `tradesale` > 0 AND `id` = '".$roww['id']."' AND prototype!=2123456804 AND prototype!=40000001 and type!=99 AND setsale=0 LIMIT 1;"));
			if (!$roww['id'] OR !$rwx['id'])
			{
			//проверим рефреш?
			if ($_REQUEST['refresh'] )
			   {
			   echo "Ожидаем ответа... <a href=give22.php?refresh=".$_REQUEST['refresh'].">Обновить</a>";
			   }
			   else
			   {
			    ?> <SCRIPT>findlogin('Передача предметов','give22.php','FindLogin');</SCRIPT><?
			   }
			}
			else 
			{
			$tmp = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$roww['id']));
			$roww['prototype'] = $tmp['prototype'];
			$roww['ekr_flag'] = $tmp['ekr_flag']; // подсказка о том что Внимание! После покупки этот предмет нельзя будет передать или продать! 


			echo "<SCRIPT>transfer('{$roww['to_id']}', '{$roww['login']}', '".str_replace("\r\n","",$roww['txt'])."', '{$roww['kr']}', '{$roww['id']}', '','{$roww['prototype']}','{$roww['ekr_flag']}','{$roww['kol']}');</SCRIPT>";
		         $del_trade = false;
			}
		}
if ($del_trade)		
	{
	mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;");
	}
}
?>

</td><TD align=right>
	<INPUT TYPE=button value="Подсказка" style="background-color:#A9AFC0" onClick="window.open('help/transfer.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
	<form action=main.php><INPUT TYPE=submit value="Вернуться"></form>
</td></tr><tr><td colspan=2 align=right><? 
			if ($step!=4) 
				{
				// <FONT COLOR=red><B><? echo $mess; </B></FONT> 
				}
				?></td></tr></table>

<TABLE width=100% cellspacing=0 cellpadding=0>
<FORM ACTION="?" METHOD=POST NAME="KR">
<TR>
	<TD valign=top align=left width=40%>
<?
	if ($step==3) 
	{ ?>
	<INPUT TYPE=hidden name=to_id value="<? echo $idkomu; ?>">
	<INPUT TYPE=hidden name=sd4 value="<? echo $user['id']; ?>">
	<BR>У вас на счету: <FONT COLOR=339900><B><? echo $user['money']; ?></B></FONT> кр.<BR>
	Передать кредиты, минимально 0.01кр.<BR>
	Укажите передаваемую сумму: <INPUT TYPE=text NAME=setkredit maxlength=8 size=6>
	<br>
	Детали платежа: <br><INPUT TYPE=text NAME=settext maxlength=70 size=30><br> &nbsp <INPUT TYPE=submit VALUE="Передать">
	</FORM>	
	<BR>
	<?
		if  ($user['level']>=10)
		 {
		?>
		<FORM ACTION="?" METHOD=POST NAME="GOLD">	
		<INPUT TYPE=hidden name=to_id value="<? echo $idkomu; ?>">
		<INPUT TYPE=hidden name=sd4 value="<? echo $user['id']; ?>">
		<BR>У вас на счету: <FONT COLOR=339900><B><? echo $user['gold']; ?></B></FONT><img src="http://i.oldbk.com/i/icon/coin_icon.png" alt="Монеты" title="Монеты" style="margin-bottom: -2px;"><BR>
		Продать монеты: <INPUT TYPE=text NAME=setgold maxlength=8 size=6> <INPUT TYPE=submit VALUE="Продать">
		<br>
		Курс продажи: 1 монета = <?=$GOLD_GIVE_KURS;?> кр.	
		<?
		 }
	
	}
?>
	</TD>
</FORM>

<FORM ACTION="give22.php" METHOD=POST name=f1>
<INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>">
<TD valign=top align=right>

<?
if ($step==3) {

					if ($_SESSION['gruppovuha']!='')
					{
					$gruppovuha = $_SESSION['gruppovuha'];
					}
					else
					{
					$gruppovuha = unserialize($user['gruppovuha']);
					}

	if (isset($_GET['all'])) { $_SESSION['allp']=(int)($_GET['all']);}
					
	if (isset($_GET['razdel']))
		{
		$_GET['razdel']=(int)$_GET['razdel'];
		$_SESSION['razdel']=$_GET['razdel'];
		}
		else
		{
		$_SESSION['razdel']=(int)$_SESSION['razdel'];
		}

	if (($_SESSION['razdel'] < 0) OR ($_SESSION['razdel'] >2)) { $_SESSION['razdel'] = 0; }

	echo '
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0" bgcolor="#A5A5A5">
	<TR><TD>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="3" bgcolor=#d4d2d2><TR>';

	echo '<TD align=center bgcolor="'.(($_SESSION['razdel'] === 0) ? "#A5A5A5":"#C7C7C7").'"><INPUT TYPE=hidden name=to_id value="'.$idkomu.'"><input name="ssave" type="hidden" value=1><input type="hidden" id="rzd0" name="rzd0" value="'.($gruppovuha[0]=='1'?'1':'0').'" ><img src="http://i.oldbk.com/i/g'.($gruppovuha[0]=='1'?'1':'0').'.gif" onClick="save1(0);" style="cursor: pointer;">';
	echo '<A HREF="?to_id='.$idkomu.'&razdel=0&sd4='.$user['id'].'">Обмундирование</A></TD>';
	echo '<TD align=center bgcolor="'.(($_SESSION['razdel'] === 1) ? "#A5A5A5":"#C7C7C7").'"><input type="hidden" id="rzd1" name="rzd1" value="'.($gruppovuha[1]=='1'?'1':'0').'" ><img src="http://i.oldbk.com/i/g'.($gruppovuha[1]=='1'?'1':'0').'.gif" onClick="save1(1);" style="cursor: pointer;">';
	echo '<A HREF="?to_id='.$idkomu.'&razdel=1&sd4='.$user['id'].'">Заклятия</A></TD>';
	echo '<TD align=center bgcolor="'.(($_SESSION['razdel'] === 2) ? "#A5A5A5":"#C7C7C7").'"><input type="hidden" id="rzd2" name="rzd2" value="'.($gruppovuha[2]=='1'?'1':'0').'" ><img src="http://i.oldbk.com/i/g'.($gruppovuha[2]=='1'?'1':'0').'.gif" onClick="save1(2);" style="cursor: pointer;">';
	echo '<A HREF="?to_id='.$idkomu.'&razdel=2&sd4='.$user['id'].'">Прочее</A></TD>';

	
	echo '</TR></TABLE>
	</TD></TR>
	<TR>
	<TD align=center>';
	echo '<B>Рюкзак (масса: ';
	$d = getmymassa($user);
	echo $d[0];
	echo get_meshok();
	echo '</B></TD>';
	
	echo "</TR>
	<TR><TD align=center>
	<TABLE BORDER=0 WIDTH=100% CELLSPACING=\"1\" CELLPADDING=\"2\" BGCOLOR=\"#A5A5A5\">";

	if ($_SESSION['razdel']==1) 
	{
		$where = 'AND (`type` = 12)';
	}
	elseif ($_SESSION['razdel']==2) 
	{
		$where = 'AND (`type` > 12 AND `type`!=27 AND `type`!=28 AND `type`!=34 AND `type`!=35)';
	}
	else
	{
		$where = 'AND (`type` < 12 OR `type`=27 OR `type`=28 OR `type`=34 OR `type`=35 )';
	}

	$grrr=$gruppovuha[$_SESSION['razdel']]=='1'?'1':'0';

	if ($user['klan']=='radminion' || $user['klan']=='Adminion' || $user['id'] == 8325) {
		$addsql = "";
	} else {
		$addsql = " and sowner=0 ";
	}


	$count = 0;
	if($grrr == 1) {
		
				if  ($_SESSION['razdel']==0)
				{
				$query = mysql_query("SELECT *, count(*) as `itemscount` FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 ".$where." AND `setsale` = 0 and sowner=0 AND prototype!=40000001 AND prototype!=2123456804 and type!=99 and otdel!=72 and type!=77  AND `present` = '' ".$addsql." ".$VAUCHER." AND `bs_owner` ='".$user['in_tower']."'  GROUP BY `prototype`, `name`, `charka`, `unik`, `mfinfo`, `ups`, `includemagic`  ORDER by `update` DESC; ");
				}
				else
				{
				$query = mysql_query("SELECT *, count(*) as `itemscount` FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 ".$where." AND `setsale` = 0 and sowner=0 AND prototype!=40000001 AND prototype!=2123456804 and type!=99 and otdel!=72 and type!=77  AND `present` = '' ".$addsql." ".$VAUCHER." AND `bs_owner` ='".$user['in_tower']."'  GROUP BY `prototype` ORDER by `update` DESC; ");
				}

			}
	else
			{
			$query = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 ".$where." AND `setsale` = 0 and sowner=0 AND prototype!=40000001 AND prototype!=2123456804 and type!=99 and otdel!=72 and type!=77  AND `present` = '' ".$addsql." ".$VAUCHER." AND `bs_owner` ='".$user['in_tower']."' ORDER by `update` DESC; ");
			}

	$displc = 0;
	$displcn = 0;
	$_SESSION['lim'] = 10;
	$_GET['page'] = (int)$_GET['page'];
	$razdel=$_SESSION['razdel'];
	if ($_GET['page'] < 0) {$_GET['page']=0;}

	$lastpresentid = -1;

	$ret = "";

	$count = mysql_num_rows($query);
	$count_all = $count;

	$art_items_ids=array();

	while($row = mysql_fetch_assoc($query)) 
	{
	
			if (in_array($row['prototype'],$vauch_a) && $row['sowner'] > 0 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325) continue;
			if (($row['ekr_flag']>0) and ($row['ecost']<=0)) continue;
			if (in_array($row['prototype'],$vauch_a)) $row['group'] = 0;
			//////////////////////////////////////////////////////////////						

		if ($row['art_param']!='')
		{
			$art_items_ids[]=$row['id'];// запоминаем ид артов
		}

		if($grrr == 1) 
		{
			// если не групповая, то будут показаны подкатом
			// если подарки и открытки - то их отдельно группируем по отделу
			if($row['otdel']==7 || $row['otdel']==71 || $row['otdel']==72|| $row['otdel']==73) 
			{
				if ($lastpresentid == -1) {
					$inv_shmot[] = $row;
					end($inv_shmot);
					$lastpresentid = key($inv_shmot);
				} else {
					$inv_shmot[$lastpresentid]['itemscount'] += $row['itemscount'];
					$count--;
				}
			} 
			
			
			else {
				// если все остальное - то их по прототипу
				$inv_shmot[] = $row;
			}
			
		} else {

			$inv_shmot[]=$row;

			if ($_SESSION['allp'] != 1) {
				if ($displcn >= $_GET['page']*$_SESSION['lim']+$_SESSION['lim']) break;
			}
			$displcn++;
		}
	}

	if (!empty($ret)) {
		$displc = $count;
	}


	// делаем запрос на бонусы артов
	if ((is_array($art_items_ids)) and (count($art_items_ids)>0) )
	{
		$bonus_data=mysql_query("select * from oldbk.art_bonus where itemid in (".implode(",",$art_items_ids).")");
		if (mysql_num_rows($bonus_data)>0)
		{
			while($art_row = mysql_fetch_assoc($bonus_data))
			{
				$art_bonus_array[$art_row['itemid']]=$art_row;
			}
		}
	}


						if($grrr == 1) {
								$ret = "";
								reset($inv_shmot);

								foreach ($inv_shmot as $key => $row) 
								{
									if ((($_SESSION['allp'] != 1) and ($displc >= $_GET['page']*$_SESSION['lim']) AND ($displc < $_GET['page']*$_SESSION['lim']+$_SESSION['lim'])) || $_SESSION['allp'] == 1) 
									{
										if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }

										$act='';
										$money_out=($ttype==1?10:1);
										$money_out=($ttype==2?100:1);
										$act=show_item_to_give_link($row,$money_out); //линки на продажу


										if ($row['itemscount'] == 1) 
										{
											$ret .= "<table border=1  WIDTH=100% CELLSPACING='0' CELLPADDING='2' BGCOLOR='#A5A5A5'>";

											if ($row['art_param']!='')
											{

												if (is_array($art_bonus_array[$row['id']]))
												{
													$row['bonus_info']=$art_bonus_array[$row['id']]['info'];
													$row['art_proto_id']=$art_bonus_array[$row['id']]['art_proto'];
													$row['art_proto_name']=$art_bonus_array[$row['id']]['art_proto_name'];
													$row['art_proto_img']=$art_bonus_array[$row['id']]['art_proto_img'];													
												}
											}

											$ret .= showitem($row,0,false,$color,$act,0,0,1);
											$ret .= "</table>";
										} else 
										{
											$ret .= "<table border=1  WIDTH=100% CELLSPACING='0' CELLPADDING='2' BGCOLOR='#A5A5A5'>";
											$ret .= showitem($row,0,false,$color,$act,0,0,1);
											$ret .= "<tr BGCOLOR='".$color."' ><td colspan=2>";
											$ret .= '<div  id=txt_'.$row['prototype'].' style="display: block;">';
											if ($row['otdel'] == "") $row['otdel'] = 0;
											$ret .= "<a href=\"#".$row['prototype']."\" Onclick=\"showhiddeninv(".$row['prototype'].",".$row['id'].",".$row['otdel'].", ".$idkomu." );\"> показать еще ".($row['itemscount']-1)."шт.</a></div>";
											$ret .= '<div  id="txt1_'.$row['prototype'].'" style="display: none;">';
											$ret .= "<a href=\"#".$row['prototype']."\" Onclick=\"closehiddeninv(".$row['prototype'].");\">скрыть</a></div></td></tr>";
											$ret .= '</table><div style="display: none;" id="id_'.$row['prototype'].'"><img src="http://i.oldbk.com/i/ajax-loader.gif" border=0></div>';
										}
										
										
									}
									$displc++;

									if ($_SESSION['allp'] != 1) {
										if ($displc >= $_GET['page']*$_SESSION['lim']+$_SESSION['lim']) break;
									}
								}
								$displc = $count;
							}
							else
							{
								$displcno=0;
								foreach ($inv_shmot as $key => $row)
								{
									if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }
									$act='';
									$money_out=($ttype==1?10:1);
									$money_out=($ttype==2?100:1);
									$act=show_item_to_give_link($row,$money_out); //линки на продажу

									if ((($_SESSION['allp'] != 1) and ($displcno >= $_GET['page']*$_SESSION['lim']) AND ($displcno < $_GET['page']*$_SESSION['lim']+$_SESSION['lim'])) || $_SESSION['allp'] == 1) {

										if ($row['art_param']!='')
										{

											if (is_array($art_bonus_array[$row['id']]))
											{
												$row['bonus_info']=$art_bonus_array[$row['id']]['info'];
												$row['art_proto_id']=$art_bonus_array[$row['id']]['art_proto'];
												$row['art_proto_name']=$art_bonus_array[$row['id']]['art_proto_name'];
												$row['art_proto_img']=$art_bonus_array[$row['id']]['art_proto_img'];													
											}
										}
										$ret .= showitem($row,0,false,$color,$act,0,0,1);
									}

									$displcno++;
								}
								$displc = $count_all;
							}

							if ($_SESSION['allp']==1) {
								echo "[<a href='?to_id=".$idkomu."&razdel=".$razdel."&all=0'>страницы</a>]";
							} else {
								$pgs[0]=$displc;

								$_GET['page']=(int)$_GET['page'];
								if (($_GET['page']*$_SESSION['lim']) >= $pgs[0]) {
									$_GET['page']=0;
								}

								$pgs = $pgs[0]/$_SESSION['lim'];
								if ($pgs>1) {
									echo "Страницы: ";
								}
								$pages_str='';

								$page = (int)$_GET['page']>0 ? (((int)$_GET['page']+1)>$pgs ? ($pgs-1):(int)$_GET['page']):0;
								$page=ceil($page);

								if ($pgs>1) {
									for ($i=0;$i<ceil($pgs);$i++)
										if (($i>($page-5))&&($i<=($page+4)))
											$pages_str.=($i==$page ? "&nbsp;<b>".($i+1)."</b>&nbsp;":"&nbsp;<a href='?to_id=".$idkomu."&razdel=".$razdel."&page=".($i)."'>".($i+1)."</a>&nbsp;");
									$pages_str.=($page<$pgs-5 ? "...":"");
									$pages_str=($page>4 ? "<a href='?to_id=".$idkomu."&razdel=".$razdel."&page=".($page-1)."'> < </a> ... ":"").$pages_str.(($page<($pgs-1) ? "<a href='?to_id=".$idkomu."&razdel=".$razdel."&page=".($page+1)."' > ></a> ":""));
								}

								$FirstPage=(ceil($pgs)>4 ? $_GET['page']>0 ? "<a href='?to_id=".$idkomu."&razdel=".$razdel."&page=0'>   << </a>":"":"");
								$LastPage=(ceil($pgs)>4 ? (ceil($pgs)-1)!=$_GET['page'] ? "<a href='?to_id=".$idkomu."&razdel=".$razdel."&page=".(ceil($pgs)-1)."'>   >> </a>":"":"");
								$pages_str=$FirstPage.$pages_str.$LastPage;
								echo $pages_str; echo " [<a href='?to_id=".$idkomu."&razdel=".$razdel."&all=1'>все</a>]";
							}


							if ($count === 0) {
								echo "<tr><td align=center bgcolor=#C7C7C7>Пусто</td></tr>";
							} else {
								echo $ret;
							}

							if ($pgs>1) 
							{
								echo "<TR><TD colspan=2 align=center>";
								echo "Страницы: ";
								echo $pages_str;
								echo "</TD></TR>";
							}
	


	echo "
	</TABLE>
	</TD></TR>
	</TABLE>";
}

echo "
</TD></TR>
</FORM>
</TABLE>";

		if (strlen($mess)) {
			echo '	
				<script>
					var n = noty({
						text: "'.addslashes($mess).'",
					        layout: "topLeft2",
					        theme: "relax2",
						type: "'.($typet == "e" ? "error" : "success").'",
					});
				</script>
			';
		}

echo "</BODY></HTML>";
