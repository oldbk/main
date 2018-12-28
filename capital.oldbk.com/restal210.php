<?
//error_reporting(E_ALL); 
//NEW_LOG
//ini_set('display_errors','On');

		session_start();
		
		if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}
		include ("connect.php");
		include "functions.php";
		if (($user['battle']>0) OR ($user['battle_fin'] >0))  { header("Location: fbattle.php"); die(); }
	require_once('/www/capitalcity.oldbk.com/functions.zayavka.php'); // функи заявок
	

function insert_items($owners_by_team,$wc,$oc) // ид тел по командам , кол- оружия, кол. предметов, тип турнира - раскидывает нужное кол. шмота - сразу на два тела
{
$ty=8;
$item_wep[8]=array(1000267,1000268,1000269,1000270,1000271,1000272,1000273,1000274,1000275,1000276,1000277);
$slots_arr=array(1,2,4,5,5,5,8,9,10,11,28); // масив слотов - кроме пушек
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////------------//////-----------//////////------------///////////////
$item_arr[8][1]=array(1000231,1000232,1000233,1000234,1000235,1000236,1000237,1000238,1000239,1000240,1000241,1000242,1000243,1000244,1000245,1000246,1000247,1000248);
$item_arr[8][2]=array(1000249,1000250,1000251,1000252,1000253,1000254,1000255,1000256,1000257,1000258,1000259,1000260,1000261,1000262,1000263,1000264,1000265,1000266);
$item_arr[8][4]=array(1000278,1000279,1000280,1000281,1000282,1000283,1000284,1000285,1000286,1000287,1000288,1000289,1000290,1000291,1000292);
$item_arr[8][5]=array(1000296,1000297,1000298,1000299,1000300,1000301,1000302,1000303,1000304,1000305,1000306,1000307);
$item_arr[8][8]=array(1000201,1000202,1000203,1000204,1000205,1000206);
$item_arr[8][9]=array(1000207,1000208,1000209,1000210,1000211,1000212,1000213,1000214,1000215,1000216,1000217,1000218,1000219);
$item_arr[8][10]=array(1000220,1000221,1000222,1000223,1000224,1000225,1000226);
$item_arr[8][11]=array(1000227,1000228,1000229,1000230);
$item_arr[8][28]=array(1000293,1000294,1000295);
///-----------------------------------------------------------------------------------------------
$item_weps=$item_wep[$ty]; // назначаем масив по типу  турнира
$item_arrs=$item_arr[$ty]; // назначаем масив по типу  турнира


	// 1. раздаем пушки
	  for($i=1;$i <=$wc;$i++) //цикл - кол. пушек
	  {

	  	shuffle($item_weps); $rand_wep=mt_rand(0,(count($item_weps)-1) );
	  	$rand_wep=$item_weps[$rand_wep];
	  	if ($rand_wep>0)
	  		{

	  		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id={$rand_wep}")) or mydie(mysql_error().":".__LINE__);
	  		if ($item[id]>0)
	  		    {	
	  			for($tt=1;$tt <=2;$tt++) 	// кидаем зеркально каждой команде
	  			{  
	  			$mowner=$owners_by_team[$tt];
	  			
	  			mysql_query("INSERT INTO `oldbk`.`inventory` SET `name`='{$item[name]}',`duration`='{$item[duration]}',`maxdur`='{$item[maxdur]}',`cost`='{$item[cost]}',`owner`='{$mowner}',
	  			`nlevel`='{$item[nlevel]}',`nsila`='{$item[nsila]}',`nlovk`='{$item[nlovk]}',`ninta`='{$item[ninta]}',`nvinos`='{$item[nvinos]}',`nintel`='{$item[nintel]}',`nmudra`='{$item[nmudra]}',
	  			`nnoj`='{$item[nnoj]}',`ntopor`='{$item[ntopor]}',`ndubina`='{$item[ndubina]}',`nmech`='{$item[nmech]}',`nalign`='{$item[nalign]}',`minu`='{$item[minu]}',`maxu`='{$item[maxu]}',
	  			`gsila`='{$item[gsila]}',`glovk`='{$item[glovk]}',`ginta`='{$item[ginta]}',`gintel`='{$item[gintel]}',`ghp`='{$item[ghp]}',`mfkrit`='{$item[mfkrit]}',`mfakrit`='{$item[mfakrit]}',`mfuvorot`='{$item[mfuvorot]}',`mfauvorot`='{$item[mfauvorot]}',
	  			`gnoj`='{$item[gnoj]}',`gtopor`='{$item[gtopor]}',`gdubina`='{$item[gdubina]}',`gmech`='{$item[gmech]}',`img`='{$item[img]}',
	  			`text`='{$item[text]}',`dressed`='0',`bron1`='{$item[bron1]}',`bron2`='{$item[bron2]}',`bron3`='{$item[bron3]}',`bron4`='{$item[bron4]}',`dategoden`='{$item[dategoden]}',`magic`='{$item[magic]}',`type`='{$item[type]}',
	  			`present`='{$item[present]}',`sharped`='{$item[sharped]}',`massa`='{$item[massa]}',`goden`='{$item[goden]}',`needident`='{$item[needident]}',`nfire`='{$item[nfire]}',`nwater`='{$item[nwater]}',`nair`='{$item[nair]}',`nearth`='{$item[nearth]}',`nlight`='{$item[nlight]}',
	  			`ngray`='{$item[ngray]}',`ndark`='{$item[ndark]}',`gfire`='{$item[gfire]}',`gwater`='{$item[gwater]}',`gair`='{$item[gair]}',`gearth`='{$item[gearth]}',`glight`='{$item[glight]}',
	  			`ggray`='{$item[ggray]}',`gdark`='{$item[gdark]}',`letter`='{$item[letter]}',`isrep`='{$item[isrep]}',`prototype`='{$item[prototype]}',`otdel`='{$item[otdel]}',`bs`='{$item[bs]}',`gmp`='{$item[gmp]}',
	  			`includemagic`='{$item[includemagic]}',`includemagicdex`='{$item[includemagicdex]}',`includemagicmax`='{$item[includemagicmax]}',`includemagicname`='{$item[includemagicname]}',`includemagicuses`='{$item[includemagicuses]}',
	  			`includemagiccost`='{$item[includemagiccost]}',`includemagicekrcost`='{$item[includemagicekrcost]}',
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='3',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity`=0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}';") or mydie(mysql_error().":".__LINE__);
	  			}
	  		    }
	  		}
	  }
	  
	  //перемешиваем слоты
	  shuffle($slots_arr);
	  
	  //указатель на слот
	  $uk_slot=0;
	  
	  //2, раздаем остальное
	  for ($i=1;$i<=$oc;$i++) // цикл кол. остальных предметов
	  {
	 
	 		if (!($slots_arr[$uk_slot] > 0))
		   	{
		   	//если  добрались до конца скидываем указатель в 0 и заново мешаем масив слотов
	   		  $uk_slot=0;
		   	  shuffle($slots_arr);
		   	}
		   	
		   //спрашиваем шмотку
		$mas_items=$item_arrs[$slots_arr[$uk_slot]]; //масив для нужного слота
		shuffle($mas_items); // перемешиваем
		$get_itm_id=$mas_items[0];

		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id={$get_itm_id}"  )) or mydie(mysql_error().":".__LINE__);
		   	
		if ($item[id]>0)
	  	 {	
	  		for($tt=1;$tt <=2;$tt++) 	// кидаем зеркально каждой команде
	  		{  
				$mowner=$owners_by_team[$tt];
	  			mysql_query("INSERT INTO `oldbk`.`inventory` SET `name`='{$item[name]}',`duration`='{$item[duration]}',`maxdur`='{$item[maxdur]}',`cost`='{$item[cost]}',`owner`='{$mowner}',
	  			`nlevel`='{$item[nlevel]}',`nsila`='{$item[nsila]}',`nlovk`='{$item[nlovk]}',`ninta`='{$item[ninta]}',`nvinos`='{$item[nvinos]}',`nintel`='{$item[nintel]}',`nmudra`='{$item[nmudra]}',
	  			`nnoj`='{$item[nnoj]}',`ntopor`='{$item[ntopor]}',`ndubina`='{$item[ndubina]}',`nmech`='{$item[nmech]}',`nalign`='{$item[nalign]}',`minu`='{$item[minu]}',`maxu`='{$item[maxu]}',
	  			`gsila`='{$item[gsila]}',`glovk`='{$item[glovk]}',`ginta`='{$item[ginta]}',`gintel`='{$item[gintel]}',`ghp`='{$item[ghp]}',`mfkrit`='{$item[mfkrit]}',`mfakrit`='{$item[mfakrit]}',`mfuvorot`='{$item[mfuvorot]}',`mfauvorot`='{$item[mfauvorot]}',
	  			`gnoj`='{$item[gnoj]}',`gtopor`='{$item[gtopor]}',`gdubina`='{$item[gdubina]}',`gmech`='{$item[gmech]}',`img`='{$item[img]}',
	  			`text`='{$item[text]}',`dressed`='0',`bron1`='{$item[bron1]}',`bron2`='{$item[bron2]}',`bron3`='{$item[bron3]}',`bron4`='{$item[bron4]}',`dategoden`='{$item[dategoden]}',`magic`='{$item[magic]}',`type`='{$item[type]}',
	  			`present`='{$item[present]}',`sharped`='{$item[sharped]}',`massa`='{$item[massa]}',`goden`='{$item[goden]}',`needident`='{$item[needident]}',`nfire`='{$item[nfire]}',`nwater`='{$item[nwater]}',`nair`='{$item[nair]}',`nearth`='{$item[nearth]}',`nlight`='{$item[nlight]}',
	  			`ngray`='{$item[ngray]}',`ndark`='{$item[ndark]}',`gfire`='{$item[gfire]}',`gwater`='{$item[gwater]}',`gair`='{$item[gair]}',`gearth`='{$item[gearth]}',`glight`='{$item[glight]}',
	  			`ggray`='{$item[ggray]}',`gdark`='{$item[gdark]}',`letter`='{$item[letter]}',`isrep`='{$item[isrep]}',`prototype`='{$item[prototype]}',`otdel`='{$item[otdel]}',`bs`='{$item[bs]}',`gmp`='{$item[gmp]}',
	  			`includemagic`='{$item[includemagic]}',`includemagicdex`='{$item[includemagicdex]}',`includemagicmax`='{$item[includemagicmax]}',`includemagicname`='{$item[includemagicname]}',`includemagicuses`='{$item[includemagicuses]}',
	  			`includemagiccost`='{$item[includemagiccost]}',`includemagicekrcost`='{$item[includemagicekrcost]}',
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='3',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity`=0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}';") or mydie(mysql_error().":".__LINE__);	  		
	  		}
	  	}
	  $uk_slot++; 
	  }

}


		$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND ((`type` >=11 AND `type` <= 14))  ;"));
		
		$owner_prof=mysql_fetch_array(mysql_query("SELECT * FROM `ntur_profile` WHERE `owner` = '{$user['id']}'   AND `def` = 1 limit 1;" ));
		
		$gerb=mysql_fetch_array(mysql_query("select * from oldbk.inventory where owner='{$user['id']}' and prototype=5111 and setsale=0 limit 1"));


		
		$start_room=80000;
		$mast=9;

		if (($_GET['got'] && $_GET['level']==200) and  ($user[battle]==0) and ($user[room]==210))
		{
				if ($user['in_tower'] >0 )
				{
					$begin_error='<font color=red>Вы в заявке на турнир!</font>';
				}
				else
				{
				//выход из локи  если не в турнире
				mysql_query("UPDATE `users` SET `users`.`room` = '200' WHERE  `users`.`id`  = '{$user[id]}' ;");
				header('location: city.php?strah=1&tmp='.mt_rand(1111,9999));
				die();
				}
		}
		elseif ($user[align]==4)
		{
			$begin_error="<font color=red>Хаос не ходит в ристалище...<br></font>";
		}		
		elseif ($user[level]<=5)
		{
		 	$begin_error='<font color=red>Вы не можете принять участие в турнире, уровень маловат!</font><br>';
		 }
		 else if ($user[hidden]>0)
		{
			$begin_error="<font color=red><b>Невидимки могут только наблюдать...</b></font>";
		}		 
		elseif ($eff[id]>0)
		{
			$begin_error="<font color=red>Травмированных сюда не пускают....</font>";
		}
		elseif (!($owner_prof['id']>0))
		{
			$begin_error="<font color=red>У Вас нет профиля  по умолчанию!</font>";
		}
		elseif (!($gerb['id'] >0))
		{
			$begin_error="<font color=red>У Вас нет <b>Личного Герба</b> - для участия в турнире!</font>";
		}
		elseif (($user['room']==210) and ($user['in_tower']==0) and ((int)$_POST['turid'] > 0) and ($_POST['starttur']))
		{
			$idtur=(int)$_POST['turid'];
	
			$get_turs=mysql_fetch_array(mysql_query("select * from sturnir  where stat=0 and id='{$idtur}' and allc>inc"));				
			
			if ($get_turs['id']>0)
				{
					
					mysql_query("UPDATE `sturnir` SET `inc`=`inc`+1  WHERE `id`='{$idtur}' ");
					
					if (mysql_affected_rows()>0)
						{
							
						//удаляем герб
						mysql_query("DELETE from oldbk.inventory where id='{$gerb['id']}' and owner='{$user['id']}' and prototype=5111 and setsale=0 limit 1");

							//ищем если есть ждущая заявка
							$get_opp_test=mysql_fetch_array(mysql_query("select * from stur_users where stur='{$idtur}' and t2_owner=0 and stat=0 and krug='{$get_turs['krug']}' "));
								if ($get_opp_test['stur']>0)
									{
									//добавляем в нее как 2й чар
									// делаем статус 1 - и говорим что можно разкинуть вещи
									// ставим время начала боя
									mysql_query("UPDATE `stur_users` SET `t2_owner`='{$user['id']}' , stat=1, sttime=now()+interval 300 second  WHERE stur='{$idtur}' and t2_owner=0 and stat=0 and krug='{$get_turs['krug']}' ");
									$uroom=$get_opp_test['room'];
									
										$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b> Новый участник,  1/".$get_turs['krug']." :</b>".make_html_login_battle($user)."<BR>";
										
										$en=mysql_fetch_array(mysql_query("select * from  users where id='{$get_opp_test['t1_owner']}' "));				
										
										$logtext.="<span class=date2>".date("d.m.y H:i")."</span> <b> В   1/".$get_turs['krug']." :</b> Участвуют: ".make_html_login_battle($en)."  против  ".make_html_login_battle($user)."<BR>";										
										
										mysql_query_100("UPDATE `stur_logs` SET  `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id='{$idtur}' "); 	
										
										
										//и кидаем вещи!!
										$owners[1]=$user['id'];
										$owners[2]=$en['id'];							
										
										insert_items($owners,5,31);
										
									
									}
									else
									{
									//создаем
									mysql_query("INSERT INTO `stur_users` SET `stur`='{$idtur}' ,`t1_owner`='{$user['id']}' ,`t2_owner`=0,`stat`=0,`krug`='{$get_turs['krug']}' ");
									$uroom=mysql_insert_id();
									
											
												if ($get_turs['inc']==0)
												{
												//первое тело
												$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>Начало турнира, 1/".$get_turs['krug']." Участник </b>:".make_html_login_battle($user)."<BR>";
											 	mysql_query_100("INSERT INTO  `stur_logs` SET id={$idtur} , active=1, start_time='".time()."' , `logs`= '{$logtext}' , `type`='1210' ;");
												}
												else
												{
												$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b> Новый участник,  1/".$get_turs['krug']." :</b>".make_html_login_battle($user)."<BR>";
												mysql_query_100("UPDATE `stur_logs` SET  `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id='{$idtur}' "); 					
												}
									
									}
	
								if (mysql_affected_rows()>0)
									{
;
									
									//раздеваем
									undressall($user['id']);
									mysql_query("UPDATE `users` SET  `room` =  ".($start_room+$uroom)." , in_tower=3 , id_grup='{$idtur}'  WHERE  `id`  = '{$user[id]}' ;");
									
									//убиваем всем бонусы 
							 		mysql_query_100("DELETE FROM users_bonus where owner='{$user['id']}' ") ;	
							 		
							 		//берем данные их эффектов - на интелект или еще какие другие эффекты которые дают статы
									 $get_drop_eff=mysql_fetch_array(mysql_query("select * from effects where type=826 and owner='{$user['id']}' ")) ;
									 if ($get_drop_eff['id']>0)
									 	{
										 mysql_query("UPDATE `users` SET  `intel` =`intel` - '{$get_drop_eff['intel']}'  WHERE  `id`  = '{$user[id]}' ;");
										 }
							 		
							 		//- удаляем эти эффекты - 791,792,793,794,795 - бафы книг
									mysql_query_100("DELETE FROM  effects where type in (826,791,792,793,794,795) and owner='{$user['id']}' ") ;
									
									// dug fix hp
									if ($user['hp'] > $user['maxhp'])  { $user['hp']=$user['maxhp']; mysql_query("UPDATE `users` SET `hp`=`maxhp` where id='".$user['id']."' ;"); }

									//reload
									$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$user['id']}' ;"));
									//3. запоминаем профиль
									// Сохраняем реальные статы в голом виде
									mysql_query_100('REPLACE INTO `ntur_realchars` (`owner`,`sila`,`lovk`,`inta`,`vinos`,`intel`,`mudra`,`stats`,`master`,`bpbonussila`,`bpbonushp`,`noj`,`mec`,`topor`,`dubina`,`mfire`,`mwater`,`mair`,`mearth`,`mlight`,`mgray`,`mdark`,`mana`)  VALUES	("'.$user['id'].'", "'.$user['sila'].'", "'.$user['lovk'].'","'.$user['inta'].'","'.$user['vinos'].'","'.$user['intel'].'","'.$user['mudra'].'","'.$user['stats'].'","'.$user['master'].'","'.$user['bpbonussila'].'","'.$user['bpbonushp'].'","'.$user['noj'].'","'.$user['mec'].'","'.$user['topor'].'","'.$user['dubina'].'","'.$user['mfire'].'","'.$user['mwater'].'","'.$user['mair'].'","'.$user['mearth'].'","'.$user['mlight'].'","'.$user['mgray'].'","'.$user['mdark'].'","'.$user['maxmana'].'")') ;
									
									//3. накидываем профиль
									mysql_query_100('UPDATE `users` SET `sila` = "'.$owner_prof['sila'].'",  `lovk` = "'.$owner_prof['lovk'].'", `inta` = "'.$owner_prof['inta'].'",`vinos` = "'.$owner_prof['vinos'].'",`intel` = "'.$owner_prof['intel'].'",`mudra` = "'.$owner_prof['mudra'].'",`master` = "'.$mast.'",`maxhp` = "'.($owner_prof['vinos']*6).'",`hp` = "'.($owner_prof['vinos']*6).'",`sergi`=0,`kulon`=0,`perchi`=0,`weap`=0,`bron`=0,`r1`=0,`r2`=0,`r3`=0,`runa1`=0,`runa2`=0,`runa3`=0,`helm`=0,`shit`=0,`boots`=0,`m1`=0,`m2`=0,`m3`=0,`m4`=0,`m5`=0,`m6`=0,`m7`=0,`m8`=0,`m9`=0,`m10`=0,`m11`=0,`m12`=0,`m13`=0,`m14`=0,`m15`=0,`m16`=0,`m17`=0,`m18`=0,`m19`=0,`m20`=0,`nakidka`=0,`rubashka`=0,`stats` = 0,`noj` = 0,`mec` = 0,`topor` = 0,`dubina` = 0,`mfire` = 0,`mwater` = 0,`mair` = 0,`mearth` = 0,`mlight` = 0,`mgray` = 0,`mdark` = 0,`bpbonussila` = 0,`mana` = 0,`maxmana` = 0,`bpbonushp` = 0   WHERE `id` = '.$user['id'] ) ;	
									
									//4. рефреш
									}
						}
					
				
				}
				else
				{
				$begin_error="<font color=red>Турнир уже набран...<br></font>";				
				}
			}

///////////////////////////////////////////////////////////////////////////////
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<script type="text/javascript" src="/i/globaljs.js"></script>
<style>
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }

    body {
			background-image: url('http://capitalcity.oldbk.com/i/restal/r210_1.jpg');
			background-repeat: no-repeat;
			background-position: top right;
	   }
</style>
<SCRIPT LANGUAGE="JavaScript">
function solo(n)
{

		<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>changeroom=n;
		window.location.href='restal210.php?got=1&level'+n+'=1';

}

function imover(im)
{
	im.filters.Glow.Enabled=true;
//	im.style.visibility="hidden";
}

function imout(im)
{
	im.filters.Glow.Enabled=false;
//	im.style.visibility="visible";
}

		function returned2(s){
			location.href='restal210.php?'+s+'tmp='+Math.random();
		}


function Down() {<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>CtrlPress = window.event.ctrlKey}

	document.onmousedown = Down;




function refreshPeriodic()
			{
			location.href='restal210.php?onlvl=<?=$onlvl;?>';//reload();
			timerID=setTimeout("refreshPeriodic()",30000);
			}
			timerID=setTimeout("refreshPeriodic()",30000);

</SCRIPT>
</HEAD>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0>

 <?
  if ($user[room]==210) 
  {
	  // вход 

	
	echo '
	<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
	<TR valign=top>
	<TD width=3% align=center>&nbsp;</TD>
	<TD >
	<div align=center><h3>Турнир</h3></div>';

	if ($begin_error) echo $begin_error."<br>";

	$get_turs=mysql_query("select * from sturnir  where stat=0");
	 if (mysql_num_rows($get_turs)  > 0)	
	 	{
	 		while ($row = mysql_fetch_array($get_turs))
	 			{
		 		$viewform=false;
				
				 	if ($row['inc']<$row['allc'])
				 		{
				 		$viewform=true;
				 		}
	 			
	 			if ($viewform)
	 				{
	 				echo "<form method=POST>";
	 				echo "<input type=hidden name=turid value='{$row['id']}' >";	 				
	 				}
	 			

				 echo "<FONT class=date>".($row['mktime'])."</FONT> Текущий турнир <b>«".$row['nazv']."» </b> макc. участников: ".$row['allc']."  " ;
				 

	 			if ($viewform)
	 				{
	 				echo " <input type=submit name=starttur  value=\"Принять &nbsp;участие \" style=\"font-weight:bold;\" >";
	 				}

				if ($row['inc']>0)  echo " ,  лог турнира <a href=/sturlog.php?id={$row['id']} target=\"_blank\"> »» </a><br>";

	 			if ($viewform)
	 				{						 
					echo "</form>";	 		
					}
					 
	 			}
	 		
	 	}
	 	else
	 	{
	 	echo "<b>Пока нет открытых турниров!</b>";
	 	}

	echo "<div align=left><P>&nbsp;<H4>История турниров</H4></div>";
	
	$get_turs=mysql_query("select * from sturnir t LEFT JOIN stur_logs l on t.id=l.id  where stat=1");
	 if (mysql_num_rows($get_turs)  > 0)	
	 	{
	 		while ($row = mysql_fetch_array($get_turs))
	 			{
				echo "<FONT class=date>".($row['mktime'])."</FONT>Турнир <b>«".$row['nazv']."» </b> макc. участников: ".$row['allc']."  " ;
				echo ", победитель ".$row['winer'];
				echo " ,  лог турнира <a href=/sturlog.php?id={$row['id']} target=\"_blank\"> »» </a><br>";
	 			}
	 		
	 	}
	 	else
	 	{
	 	echo "<b>Пока нет открытых турниров!</b>";
	 	}
	
		

 }
 else	if ($user[in_tower]==3)
 	{
 		//я в турнире
 		//данные о турнире моем
 		//данные о положении чара
 		$lroom=$user['room']-$start_room;
		$turdata=mysql_fetch_array(mysql_query("select s.*,UNIX_TIMESTAMP(sttime) as usttime, turn.nazv from stur_users as s LEFT JOIN sturnir as turn on stur=id where room='{$lroom}' and (t1_owner='{$user['id']}' or t2_owner='{$user['id']}')"));


	  	if ($turdata['krug']<=0)
	  		{
	  		$strtur=' финал  ';	  		
	  		}
	  		else
	  		{
	  		$strtur=' 1/'.$turdata['krug'];
	  		}
	  
		   echo "<div align=center><h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Турнир: <b>".$turdata['nazv']."</b>  ".$strtur."</h3></div>";
				   
				   	echo "<div align=right>
					<form method=GET action='restal270.php'>";
					echo "<input value=\"Профили характеристик\" style=\"background-color:#A9AFC0\" onclick=\"window.open('restal_profile.php', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')\" type=\"button\"> ";
					echo "<INPUT TYPE=\"button\" value=\"Подсказка\" style=\"background-color:#A9AFC0\" onclick=\"window.open('help/r210.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')\">
					<input type=button value='Обновить' onClick=\"returned2('refresh=3.14');\">
					</form></div>";
				   
				
				   echo "
				   <TABLE width=100% height=100% border=0 cellspacing=0 cellpadding=0>
					<TR valign=top>
					<TD width=5% align=center>&nbsp;</TD>
					<TD width=85%>";
				   echo "<font color=red>";

				if ($turdata[stat]==10)
				{
					if ($_POST['ready'])
					{
						mysql_query("UPDATE `stur_users` SET  stat=0  WHERE room='{$lroom}' and (t1_owner='{$user['id']}' or t2_owner='{$user['id']}') and stat=10 and stur='{$turdata[stur]}' ");
						if (mysql_affected_rows()>0)
							{
							$turdata[stat]=0;
							}
					}
					else
					{
					echo "<form method=post><input type=submit name='ready' value='Готов продолжить турнир'></form>";
					}
				}				   				
				
				
				if ($turdata[stat]==0)
				{
				echo "Ожидаем входа противника....";
				}
				//рисуем таймер када начнется бой
				elseif ($turdata[stat]==1)
				{
				   $de_time=$turdata['usttime']-time();
			   

				   
				   if ($turdata['t1_owner']==$user['id'])
				   	{
				   	$enemyid=$turdata['t2_owner'];
				   	}
				   	else
				   	{
				   	$enemyid=$turdata['t1_owner'];
				   	}
				   	
				   	$enem=mysql_fetch_array(mysql_query("select * from users where id='{$enemyid}' ;"));
				   	
					   	if ($enem['id']>0)
					   	{
				   		echo "Ваш противник:".nick_align_klan($enem);
				   		echo "<br>";
				   		}
				   	
				   
				    if ($de_time>0)
				   	{
				  	   echo "Ваш бой начнется в <span class=date>".$turdata['sttime']."</span> через:<b>".$de_time." сек.</b> ";
				   	}
				   	else
				   	{
				   	   echo "Ваш бой начнется через:<b>несколько секунд.</b> ";
				   	}
				   	
				   	
				   echo "<br>Ваше турнирное обмундирование находится в инвентаре. Вам необходимо одеться и распределить статы до начала боя.";
				}
				

			   echo "</font><br><br>";
					
						
						///лог
						$get_tur_log=mysql_fetch_array(mysql_query("select * from stur_logs where id='{$turdata[id]}' ;"));
						if ($get_tur_log[logs]!='')		
						{
						$log_txt=str_replace("<BR>","<BR><HR>",$get_tur_log[logs]);
						echo $log_txt; 
						 }
						 //////
						 
				
				echo "</TD><TD width=10% align=center><div>";
					
				//Хилка
				$hilled=$user[maxhp]-$user[hp];
				if (($_GET[hill]==1)and($hilled>0))
						{
							 $ss[1]='';
							 $ss[0]='a';
				  			mysql_query("UPDATE `users` set hp=maxhp where id='{$user[id]}' ");
						  	echo "<font color=red>Вы пополнили здоровье:<b>+".$hilled."HP</b></font>";
				  	 		echo "<br><img src=http://i.oldbk.com/llabb/use_heal_off.gif alt='Пустая бутылка' title='Пустая бутылка' border=0>";
						}
						else
						if ($hilled>0)
						{
						echo "<br><br><a href=?hill=1><img src=http://i.oldbk.com/llabb/use_heal_on.gif alt='Востановление жизни' title='Востановление жизни' border=0></a>";
						}
						else
						{
						echo "<br><br><img src=http://i.oldbk.com/llabb/use_heal_off.gif alt='Пустая бутылка' title='Пустая бутылка' border=0>";
						}
				echo "</div></TD></TR></TABLE>";

 	
 	}
	echo "</TD><TD align=right ><br><br>";
	echo "<div align=right> <form method=GET>";
	echo "<input value=\"Профили характеристик\" style=\"background-color:#A9AFC0\" onclick=\"window.open('restal_profile.php', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')\" type=\"button\"> ";
	echo "<input type=button value='Обновить' onClick=\"returned2('refresh=".mt_rand(1111,9999)."&');\"><INPUT TYPE=button value=\"Вернуться\" onClick=\"returned2('got=1&level=200&');\"><br></form></div>";
	echo "</TD><TD  align=center>&nbsp;</TD></TR><TR><TD align=center colspan=2>	</TD></table>";
 	echo "</body></html>";
