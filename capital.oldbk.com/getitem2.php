<?php
	session_start();
	include "connect.php";
	include "functions.php";

if ($user['id'] != 8540 && $user['id'] != 102904 && $user['id'] != 546433) die();

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
?>
<html>
<head>
<link rel=stylesheet type="text/css" href="i/main.css">
</head>
<body>
<script>
function closehint3()
{
	document.getElementById("hint3").style.visibility="hidden";
}


function SetNick(mid) {
    var el = document.getElementById("hint3");
	sale_txt = "";
	txt = "Введите ник";
	el.innerHTML = '<form method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B>'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="mid" value="'+mid+'"><td colspan=2 align=center><B><I>'+txt+'</td></tr><tr><td width=80% align=right>'+
	'<input type="text" name="nick" value=""></td><td width=20%><INPUT TYPE="submit" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	el.style.top = (document.body.scrollTop + 5) + 100 + 'px';
	document.getElementById("count").focus();
}
</script>
<?php
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['nick'])) {
		$owner = check_users_city_datal($_POST['nick']);
		if ($owner !== FALSE) {
			$item = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.eshop WHERE id = '.intval($_POST['mid'])));
			echo 'Подарить с небес <b>'.$owner['login'].'</b>';
			if ($owner['id_city'] == 0) {
				echo ' (cap)';
			} elseif ($owner['id_city'] == 1) {
				echo ' (ava)';
			}
			echo ' вещь '.$item['name'].' ?';
			echo '<form method="post"><input type="hidden" name="nickok" value="'.htmlspecialchars($_POST['nick'],ENT_QUOTES).'"><input type="hidden" name="mid" value="'.$_POST['mid'].'"><input value="ДА" type="submit"></form>';

			$start_datetime = new DateTime();
			$start_datetime->setTime(0,0);
			$q = sprintf('select id from `oldbk`.new_delo where sdate > %d and owner = %d and type = 98 AND target = 0 and target_login = "Небеса"', $start_datetime->getTimestamp(), $owner['id']);
			if (mysql_num_rows($q)) {
				echo '<font color=red>Тело уже получало подарок с небес</font>';
			}
		}
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['nickok'])) {
		try {
			$dress = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.eshop WHERE id = '.intval($_POST['mid'])));
			if(($owner = check_users_city_datal($_POST['nickok'])) === false) {
				throw new Exception(sprintf('Пользователь не найден. %s', $_POST['nickok']));
			}

			$as_present_list = array(
				//'',
				'Мироздатель',
				'Мусорщик'
			);
			$as_present = $as_present_list[rand(0, count($as_present_list) - 1)];

			$fraze_list = array(
				//'Зимний Дух посетил Capitalcity и подарил',
				sprintf('Внезапно %s подарил', $as_present),
			);
			$fraze = $fraze_list[rand(0, count($fraze_list) - 1)];

			$as_sowner = $owner['id'];
			$sql = "";
			$str = "";
			$gos_cost=$dress['cost'];
			if($dress[nlevel]>6) {
				$str=",`up_level` ";
				$sql=",'".$dress[nlevel]."' ";
			}

			if(!mysql_query("INSERT INTO oldbk.`inventory`
					(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`idcity`,
						`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`mfbonus` ".$str."
					)
					VALUES
					('{$dress['id']}',{$as_sowner},'{$as_present}','{$owner['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$gos_cost},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
					'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$owner[id_city]}'
					,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['mfbonus']}' ".$sql."
				) ;"))
			{
				throw new Exception(mysql_error());
			}

			echo "Всё ок";

			$rec = array();
			$rec['owner']=$owner[id];
			$rec['owner_login']=$owner[login];
			$rec['owner_balans_do']=$owner['money'];
			$rec['owner_balans_posle']=$owner['money'];
			$rec['target']=0;
			$rec['target_login']="Небеса";
			$rec['type']=98;
			$rec['sum_kr']=0;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=mysql_insert_id();
			$rec['item_name']=$dress['name'];
			$rec['item_count']=1;
			$rec['item_type']=$dress['type'];
			$rec['item_cost']=$dress['cost'];
			$rec['item_dur']=$dress['duration'];
			$rec['item_maxdur']=$dress['maxdur'];
			$rec['item_ups']=$dress['ups'];
			$rec['item_unic']=$dress['unik'];
			$rec['item_incmagic_id']=$dress['includemagic'];
			$rec['item_ecost']=$dress['ecost'];
			$rec['item_proto']=$dress['prototype'];
			$rec['item_incmagic']=$dress['includemagicname'];
			$rec['item_incmagic_count']=$dress['includemagicuses'];
			$rec['item_arsenal']='';
			add_to_new_delo($rec); //кому

			$razdel=array(
				1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",
				24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun", 61=>'eda' , 72 =>''
			);

			$dress['otdel'] ? $xx = $dress['otdel'] : $xx = $dress['razdel'];
			$ehtml = str_replace('.gif','',$dress['img']);

			if ($dress['type']==30) {
				$razdel[$xx]="runs/".$ehtml;
			} else {
				if ($razdel[$xx] == '') {
					$dola = array(5001, 5002, 5003, 5005, 5010, 5015, 5020, 5025);

					if (in_array($dress['prototype'], $vau4)) {
						$razdel[$xx] = 'vaucher';
					} elseif (in_array($dress['prototype'], $dola)) {
						$razdel[$xx] = 'earning';
					} else {
						$oskol = array(15551, 15552, 15553, 15554, 15555, 15556, 15557, 15558, 15561, 15562, 15568, 15563, 15564, 15565, 15566, 15567);
						if (in_array($dress['prototype'], $oskol)) {
							$razdel[$xx] = "amun/" . $ehtml;
						} else {
							$razdel[$xx] = 'predmeti';
						}
					}
				} else {
					$razdel[$xx] = $razdel[$xx] . "/" . $ehtml;
				}
			}
			$retname = "<a href=http://oldbk.com/encicl/".$razdel[$xx].".html target=_blank>\"".$dress['name']."\"</a>";

			addchp ('<font color=red>Внимание!</font> Вам с Небес упал подарок <b>"'.$dress['name'].'"</b>','{[]}'.$owner['login'].'{[]}',-1,$owner['id_city']) or mydie();
			addch2all('Внимание! '.$fraze.' '.$retname.' персонажу <b>'.$owner['login'].'</b> <a href="http://capitalcity.oldbk.com/inf.php?'.$owner['id'].'"  target=_blank><img src="http://i.oldbk.com/i/inf.gif"></a>!');
		} catch (Exception $ex) {
			echo '<font color=red>'.$ex->getMessage().'</font>';
		}
	}
?>

	<div style="background-color:#d2d0d0;padding:1"><center><font color="#oooo"><B>Отделы магазина</B></center></div>
	<A HREF="?otdel=5&sid=&0.648834385828923">Заклинания: нейтральные</A><BR>
	<A HREF="?otdel=51&sid=&0.722009624500359">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;боевые и защитные</A><BR>
	<A HREF="?otdel=52&sid=&0.722009624500359">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;сервисные</A><BR>
	<A HREF="?otdel=6&sid=&0.925798340638547">Амуниция</A><BR>
	<?
	if ($clan_kazna)
	{
	?>
	<A HREF="?otdel=106&sid=&0.925798340638547">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Амуниция Клановая</A><BR>	
	<?
	}
	?>
	<A HREF="?otdel=61&sid=&0.925798340638547">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Еда</A><BR>
	<A HREF="#">Прилавок Великих</A><BR>
	<A HREF="?otdel=100&sid=&0.925798340638547">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;100 побед</A><BR>
	<?
	 echo '<A HREF="?otdel=300&sid=&0.925798340638547">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;300 побед</A><BR>';
	 echo '<A HREF="?otdel=500&sid=&0.925798340638547">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;500 побед</A><BR>'; 
	// if ($user[winstbat]>=500) echo '<A HREF="eshop.php?otdel=700&sid=&0.925798340638547">Прилавок Великих (700 побед) </A><BR>'; 
	?>

<?php
	if (isset($_GET['otdel'])) {
		echo '<TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';
		$data = mysql_query('SELECT * FROM oldbk.`eshop` WHERE `razdel` = '.$_GET['otdel'].' AND name NOT LIKE "%прокат%" ORDER by `cost` ASC');
		while($row = mysql_fetch_array($data)) {
			if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
			echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
			?>
			<BR><A HREF="javascript:void(0);" OnClick="SetNick(<?=$row['id']?>); return false;">подарить с небес</A>
			</TD>
			<?php
			echo "<TD valign=top>";
			$row['count'] = 9999;
			showitem ($row);
			echo "</TD></TR>";
		}
		echo '</table>';
   	}
?>
<div id="hint3" class="ahint"></div>
</body>
</html>
