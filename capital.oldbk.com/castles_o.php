<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("castles_pre.php");
	}


	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	require_once('connect.php');
	require_once('functions.php');
	require_once('castles_config.php');
	require_once('castles_functions.php');

	if (!($user['room'] > 70000 && $user['room'] < 71000)) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }
	if ($user['in_tower'] != 16) Redirect('castles_pre.php');

	$cid = $user['room']-70000;
	$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid) or die();
	$c = mysql_fetch_assoc($q) or die("no castle");

	$second = false;
	$selfclan = false;
	$mainclan = false;

	if (strlen($user['klan'])) {
		$selfclan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$user['klan'].'"');
		$selfclan = mysql_fetch_assoc($selfclan);
		if ($selfclan !== FALSE) {
			$second = CGetSecondClan($selfclan);
		}
	}

	require_once "clan_kazna.php"; 

	if ($selfclan !== FALSE) {
		if ($selfclan['base_klan'] > 0) {
			$mainclan = $second;
		} else {
			$mainclan = $selfclan['short'];
		}
	}
	
	/*
	8 серьг
	8 пушек
	8 маек
	8 броников
	24 колца
	8 шапок
	8 перчаток
	8 щитов
	8 сапог 	
	*/

	// оружие
	$item_arr[0] = array(1000267,1000268,1000269,1000270,1000271,1000272,1000273,1000274,1000275,1000276,1000277);
	// серьги
	$item_arr[1] = array(1000231,1000232,1000233,1000234,1000235,1000236,1000237,1000238,1000239,1000240,1000241,1000242,1000243,1000244,1000245,1000246,1000247,1000248);
	// амулеты
	$item_arr[2] = array(1000249,1000250,1000251,1000252,1000253,1000254,1000255,1000256,1000257,1000258,1000259,1000260,1000261,1000262,1000263,1000264,1000265,1000266);
	// Доспехи
	$item_arr[4] = array(1000278,1000279,1000280,1000281,1000282,1000283,1000284,1000285,1000286,1000287,1000288,1000289,1000290,1000291,1000292);
	// колцо
	$item_arr[5] = array(1000296,1000297,1000298,1000299,1000300,1000301,1000302,1000303,1000304,1000305,1000306,1000307,1000296,1000297,1000298,1000299,1000300,1000301,1000302,1000303,1000304,1000305,1000306,1000307);
	// шлем
	$item_arr[8] = array(1000201,1000202,1000203,1000204,1000205,1000206,1000201,1000202,1000203,1000204,1000205,1000206);
	// перчатки
	$item_arr[9] = array(1000207,1000208,1000209,1000210,1000211,1000212,1000213,1000214,1000215,1000216,1000217,1000218,1000219);
	// щиты  
	$item_arr[10]= array(1000220,1000221,1000222,1000223,1000224,1000225,1000226,1000220,1000221,1000222,1000223,1000224,1000225,1000226);
	// ботинки
	$item_arr[11]= array(1000227,1000228,1000229,1000230,1000227,1000228,1000229,1000230);
	// футболка
	$item_arr[28]= array(1000293,1000294,1000295,1000293,1000294,1000295,1000293,1000294,1000295);

	$myteam = 0;
	if ($c['clanashort1'] == $user['klan'] || $c['clanashort1'] == $second) $myteam = 1;
	if ($c['clanashort2'] == $user['klan'] || $c['clanashort2'] == $second) $myteam = 2;

	if (!$myteam) die("no team");

	$q = mysql_query('SELECT * FROM castles_inventory WHERE cid = '.$c['id'].' and team = '.$myteam);
	if (mysql_num_rows($q) === 0) {
		// нет вещей - ложим на склад
		while(list($k,$v) = each($item_arr)) {
                        shuffle($v);
			$i = 0;
			while(list($ka,$va) = each($v)) {
				mysql_query('INSERT INTO `castles_inventory` (cid,itemid,owner,team) VALUES("'.$c['id'].'","'.$va.'","0","'.$myteam.'")');
				$i++;
				if ($i >= 8 && $k != 5) break;
			}
		}
	}

	if (isset($_GET['itemid'])) {
		mysql_query('UPDATE castles_inventory SET owner = '.$user['id'].' WHERE id = '.intval($_GET['itemid']).' AND cid = '.$c['id'].' AND team = '.$myteam.' AND owner = 0');
		if (mysql_affected_rows() > 0) {
			// выдаём шмотку
			$q = mysql_query('SELECT * FROM castles_inventory WHERE id = '.intval($_GET['itemid']));
			$getitem = mysql_fetch_assoc($q);

	  		$item = mysql_fetch_array(mysql_query("select * from oldbk.inventory WHERE id = ".$getitem['itemid']));
	  		if ($item[id]>0) {	
	  			$mowner = $user['id'];
				$item['letter'] = $getitem['id'];
	  			
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
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='16',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity` = 0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}',`nclass` = 4, `img_big` = '{$item['img_big']}';") or mydie(mysql_error().":".__LINE__);
	  		}
		}
	}

	if (isset($_GET['ret'])) {
		$item = mysql_query('SELECT * FROM inventory WHERE id = '.intval($_GET['ret']).' and dressed = 0 and bs_owner = 16 and owner = '.$user['id']);
		if (mysql_num_rows($item) > 0) {
			$item = mysql_fetch_assoc($item);
			$ci = mysql_query('SELECT * FROM castles_inventory WHERE owner = '.$user['id'].' AND id = '.intval($item['letter']));
			if (mysql_num_rows($ci) > 0) {
				$ci = mysql_fetch_assoc($ci);
				
				// сносим вещь
				mysql_query('DELETE FROM inventory WHERE id = '.intval($_GET['ret']).' and dressed = 0 and bs_owner = 16 and owner = '.$user['id']);
				mysql_query('UPDATE castles_inventory SET owner = 0 WHERE id = '.intval($item['letter']));
			}
		}
	}
	if (isset($_GET['logoutbank'])) {
		unset($_SESSION['bankid']);
		Redirect('castles_o.php?razdel=99');
	}

	$noekrlist = array(119,120,249,1002223);

	$err = "";

	if (isset($_GET['buykr']) && in_array($_GET['buykr'],$noekrlist)) {
		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` = ".intval($_GET['buykr'])));
		$dress['cost'] = round($dress['cost'],2);

		if ($user['money'] >= $dress['cost']) {
			if($dress[nlevel]>6) {
				$str = ",`up_level` ";
				$sql = ",'".$dress[nlevel]."' ";
			}
	
			if(mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
				`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark` ".$str." , 
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`bs_owner`,`img_big`
				)
				VALUES
				('{$dress['id']}',0,'','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}' ".$sql." ,
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
				'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
				'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$user[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}',16,'{$dress['img_big']}'
				)"))
			{

				$insert_id = mysql_insert_id();

				mysql_query("UPDATE `users` set `money` = `money`- '".($dress['cost'])."' WHERE id = {$_SESSION['uid']} ;");
	
				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];

				$user['money'] -= $dress['cost'];

				$rec['owner_balans_posle']=$user[money];
				$rec['target']=0;
				$rec['target_login']='гос.маг.';
				$rec['type']=1;//покупка из госа
				$rec['sum_kr']=$dress['cost'];
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=$insert_id;
				$rec['item_name']=$dress['name'];
				$rec['item_count']=1;
				$rec['item_type']=$dress['type'];
				$rec['item_cost']=$dress['cost'];
				$rec['item_dur']=$dress['duration'];
				$rec['item_maxdur']=$dress['maxdur'];
				$rec['item_ups']=0;
				$rec['item_unic']=0;
				$rec['item_incmagic']='';
				$rec['item_incmagic_count']='';
				$rec['item_arsenal']='';
				add_to_new_delo($rec);

				$err = "Удачно куплено ".$dress['name'];
			} else {
				//$err = mysql_error();
			}
		} else {
			$err = "Недостаточно средств";
		}
	}

	if (isset($_GET['nullpers'])) {
		undressall($user['id']);

		if ($user['level'] == 9) {
			$stats = 120;
			$vinos = 13;
		}
		if ($user['level'] == 10) {
			$stats = 140;		
			$vinos = 16;
		}
		if ($user['level'] == 11) {
			$stats = 200;
			$vinos = 19;
		}
		if ($user['level'] == 12) {
			$stats = 230;	
			$vinos = 23;
		}
		if ($user['level'] == 13) {
			$stats = 250;	
			$vinos = 24;
		}
		if ($user['level'] == 14) {
			$stats = 300;	
			$vinos = 25;
		}


		$hp = $vinos*3;
		$master = 15;


		mysql_query('UPDATE `users` SET
			`sila` = "3",
			`lovk` = "3",
			`inta` = "3",
			`vinos` = "'.$vinos.'",
			`intel` = "3",
			`mudra` = "3",
			`stats` = "'.$stats.'",
			`noj` = 0,
			`mec` = 0,
			`topor` = 0,
			`dubina` = 0,
			`mfire` = 0,
			`mwater` = 0,
			`mair` = 0,
			`mearth` = 0,
			`mlight` = 0,
			`mgray` = 0,
			`mdark` = 0,
			`master` = "'.$master.'",
			`maxhp` = "'.$hp.'",
			`hp` = "'.$hp.'",
			`bpbonussila` = 0,
			`mana` = 0,
			`maxmana` = 0,
			`bpbonushp` = 0
			WHERE `id` = '.$user['id']
		);

	}
?>

<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body bgcolor=#D7D7D7 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">
<br><br>
<font color=red><b>Время начала боя: <?php echo date("d/m/Y H:i:s",$c['timeouta']); ?></b></font> <a href="castles_o.php?nullpers">Обнулить персонажа</a><br>
<br>
<?php
	if (!isset($_GET['razdel']) || !$_GET['razdel']) $_GET['razdel'] = 3;

	if (strlen($err)) echo '<b><font color=red>'.$err.'</font></b><br><br>';

	echo '
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0" bgcolor="#A5A5A5">
	<TR>
	<TD>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="3" bgcolor=#d4d2d2><TR>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 3) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=3">&nbsp;Оружие</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 1) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=1">&nbsp;Серьги</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 2) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=2">&nbsp;Амулеты</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 4) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=4">&nbsp;Доспехи</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 5) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=5">&nbsp;Кольца</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 8) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=8">&nbsp;Шлемы</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 9) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=9">&nbsp;Перчатки</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 10) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=10">&nbsp;Щиты</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 11) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=11">&nbsp;Ботинки</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 28) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=28">&nbsp;Футболки</A></TD>
		<TD align=center bgcolor="'.(($_GET['razdel'] == 99) ? "#A5A5A5":"#C7C7C7").'"><A HREF="?razdel=99">&nbsp;Заклятия</A></TD>
		</TR>
	</TABLE>
	</TD>
	</TR>
	<TR><TD align=center>
	<TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
	';

	if ($_GET['razdel'] != 99) {
		$q = mysql_query('SELECT *,castles_inventory.owner AS ccowner,castles_inventory.id AS ccid FROM castles_inventory LEFT JOIN inventory ON castles_inventory.itemid = inventory.id WHERE cid = '.$c['id'].' AND team = '.$myteam.' AND type = '.intval($_GET['razdel']).' ORDER BY castles_inventory.owner ASC');
		$ff = 0;
		while($row = mysql_fetch_assoc($q)) {
			if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }
			if ($row['ccowner'] > 0) {
				if($row['ccowner'] != $user['id']) {
					$so = mysql_query_cache('SELECT * from oldbk.users WHERE id = '.$row['ccowner'].' AND id_city = 0',false,600);
					if (!count($so)) {
			        		$so = mysql_query_cache('SELECT * from avalon.users WHERE id = '.$row['ccowner'].' AND id_city = 1',false,600);
					}
					if (!count($so)) {
			        		$so = mysql_query_cache('SELECT * from angels.users WHERE id = '.$row['ccowner'].' AND id_city = 2',false,600);
					}
					
					if (count($so)) {
						$so = $so[0];
			        		$sowner = s_nick($so['id'],$so['align'],$so['klan'],$so['login'],$so['level']);
					}
		        	} else {
			        	$sowner = s_nick($user['id'],$user['align'],$user['klan'],$user['login'],$user['level']);
				}
				$action = 'Данную вещь взял '.$sowner;
			} else {
				$action = '<a href="castles_o.php?razdel='.intval($_GET['razdel']).'&itemid='.$row['ccid'].'">Взять</a>';
			}
			echo showitem($row,0,false,$color,$action,0,0,1);
		}
	} else {
		$q = mysql_query('SELECT * FROM shop WHERE id IN ('.implode(",",$noekrlist).')');
		while($row = mysql_fetch_assoc($q)) {
			$row['cost'] = round($row['cost'],2);
			if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
			echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
			echo '<br><br><a href="castles_o.php?buykr='.$row['id'].'&razdel=99">Купить за '.$row['cost'].' кр. </a></td>';
			echo "<TD valign=top>";
			$row[GetShopCount()] = 999;
			echo showitem($row,0,false,$color,$action,0,0,1);
			echo "</TD></TR>";
		}

	}
	echo '</table>';
?>
</body>
</html>