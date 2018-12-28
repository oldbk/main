<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("station.php");
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");	

	require_once('connect.php');
	require_once('functions.php');

	if ($user['room'] != 61000) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }
	
	if (isset($_GET['exit'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '26' WHERE `users`.`id` = '{$_SESSION['uid']}' ;") or die();
		Redirect('city.php');
	}
	if (isset($_POST['buyticket'])) {
		reset($_POST);
		while(list($k,$v) = each($_POST)) {
			if ($k[0] == 't' && strlen($k) > 1) {
				$tid = intval(substr($k,1));
				if ($tid > 0) {
					$q = mysql_query('START TRANSACTION') or mydie();
					$q = mysql_query('SELECT * FROM `station` WHERE id = '.$tid.' FOR UPDATE') or mydie();

					if (mysql_num_rows($q) > 0) {
						$t = mysql_fetch_assoc($q);
						if ($t['count'] == 0) {
							$q = mysql_query('COMMIT') or mydie();
							Redirect("station.php?error=1");
						}
						if ($user['money'] < $t['price']) {
							$q = mysql_query('COMMIT') or mydie();
							Redirect("station.php?error=3");
						}

						$h = date("H");
						$m = date("i");		

						$tt = explode(":",$t['starttime']);
						$ht = $tt[0];
						$mt = $tt[1];
	
						if ($h > $ht) {
							$q = mysql_query('COMMIT') or mydie();
							Redirect("station.php?error=4");
						} elseif ($h == $ht) {
							if ($m > ($mt-2)) {
								$q = mysql_query('COMMIT') or mydie();
								Redirect("station.php?error=4");
							}
						}


						// всё хорошо, продаём билет

						// минус один в колве
						mysql_query('UPDATE station SET count = count - 1 WHERE id = '.$tid) or mydie();

						// ложим билет в инвентарь
						$cities = array(
							0 => "Capital city",
							1 => "Avalon city",
						);
						$letter = "Дата отправления кареты в ".$cities[$t['tocity']].": ".date("d.m.Y")." в ".$t['starttime'];

						$dress = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.$t['protoid']) or mydie();
						$dress = mysql_fetch_assoc($dress) or mydie();


						mysql_query('INSERT INTO oldbk.`inventory`
								(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`duration`,`maxdur`,`isrep`,
									`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
									`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
									,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
									`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
									`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`idcity`
								)
								VALUES	(
									"",
									'.$dress['id'].',
									'.$user['id'].',
									"'.mysql_real_escape_string($dress['name']).'",
									'.$dress['type'].',
									'.$dress['massa'].',
									'.$t['price'].',
									"'.mysql_real_escape_string($dress['img']).'",
									'.$dress['duration'].',
									'.$dress['maxdur'].',
									'.$dress['isrep'].',
									'.$dress['gsila'].',
									'.$dress['glovk'].',
									'.$dress['ginta'].',
									'.$dress['gintel'].',
									'.$dress['ghp'].',
									'.$dress['gnoj'].',
									'.$dress['gtopor'].',
									'.$dress['gdubina'].',
									'.$dress['gmech'].',
									'.$dress['gfire'].',
									'.$dress['gwater'].',
									'.$dress['gair'].',
									'.$dress['gearth'].',
									'.$dress['glight'].',
									'.$dress['ggray'].',
									'.$dress['gdark'].',
									'.$dress['needident'].',
									'.$dress['nsila'].',
									'.$dress['nlovk'].',
									'.$dress['ninta'].',
									'.$dress['nintel'].',
									'.$dress['nmudra'].',
									'.$dress['nvinos'].',
									'.$dress['nnoj'].',
									'.$dress['ntopor'].',
									'.$dress['ndubina'].',
									'.$dress['nmech'].',
									'.$dress['nfire'].',
									'.$dress['nwater'].',
									'.$dress['nair'].',
									'.$dress['nearth'].',
									'.$dress['nlight'].',
									'.$dress['ngray'].',
									'.$dress['ndark'].',
									'.$dress['mfkrit'].',
									'.$dress['mfakrit'].',
									'.$dress['mfuvorot'].',
									'.$dress['mfauvorot'].',
									'.$dress['bron1'].',
									'.$dress['bron2'].',
									'.$dress['bron3'].',
									'.$dress['bron4'].',
									'.$dress['maxu'].',
									'.$dress['minu'].',
									'.$dress['magic'].',
									'.$dress['nlevel'].',
									'.$dress['nalign'].',
									"'.(time()+(3600*24*2)).'",
									"2",
									'.$dress['razdel'].',
									'.$dress['group'].',"'.mysql_real_escape_string($letter).'",0,0,0,0,"",0,0,0,
									"'.$user['id_city'].'"
							)
						') or mydie();

						$tid = mysql_insert_id();

	                    			$dressid = get_item_fid(array('id' => $tid, 'idcity' => $user['id_city'])); //принудительно указываем
	                    			//Отнимаем бабки :)))))
	                    			mysql_query("UPDATE users set money=money-'{$t['price']}' where id='{$user[id]}' ;");
	                    			
						$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user[money];
						$rec['owner_balans_posle']=$user[money]-$t['price'];
						$rec['target']=0;
						$rec['target_login']='вокзал';
						$rec['type']=272;//покупка из госа
						$rec['sum_kr']=$t['price'];
						$rec['item_id']=$dressid;
						$rec['item_name']=$dress['name'];
						$rec['item_count']=1;
						$rec['item_type']=$dress['type'];
						$rec['item_cost']=$t['price'];
						$rec['item_dur']=$dress['duration'];
						$rec['item_maxdur']=$dress['maxdur'];
						if (add_to_new_delo($rec) === FALSE) Redirect("station.php?error=2");

					} else {
						$q = mysql_query('COMMIT') or mydie();
						Redirect("station.php?error=2");
					}

					$q = mysql_query('COMMIT') or mydie();
					Redirect("station.php?error=99");
				}
			}
		}
	}

?>
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<script>
			var timerID;
			function refreshPeriodic() {
				location.href='station.php?'+Math.random();
			}
			timerID = setTimeout("refreshPeriodic()",30000);
	</script>
	</HEAD>
	<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0">
	<tr><td width=100% valign=top>
	<center>
		<font color="#a4a4a4">Расписание движения карет на сегодня.</font>
		<table width="80%" cellpadding=0 cellspacing=0>
		<tr>
			<td background="http://i.oldbk.com/i/station/up_bg_left.jpg" width=10></td>
			<td height=20 background="http://i.oldbk.com/i/station/up_bg_new.jpg"><center><img src="http://i.oldbk.com/i/station/v_<?php if ($_SERVER['SERVER_NAME'] == "avaloncity.oldbk.com") echo "av"; else echo "cap"; ?>.png"></center></td>
			<td background="http://i.oldbk.com/i/station/up_bg_right.jpg"width=10></td>
		</tr>

		<tr>
			<td background="http://i.oldbk.com/i/station/up_bg_left.jpg" width=10></td>

			<td><form method="POST"><input type="hidden" name="buyticket" value="1">
			<table width="100%" cellpadding=2 cellspacing=1>
			<tr><td bgcolor="#8f959c"><font color="#dedede">время отправления</font></td><td bgcolor="#8f959c"><font color="#dedede">пункт назначения</font></td><td bgcolor="#8f959c"><font color="#dedede">время в пути</font></td><td bgcolor="#8f959c"><font color="#dedede">цена билета</font></td><td bgcolor="#8f959c"><font color="#dedede">осталось билетов</font></td><td bgcolor="#8f959c"><font color="#dedede">приобрести билет</font></td></tr>
			<tr><td colspan=6 height=5 background="http://i.oldbk.com/i/station/gorizontal_line.jpg"></td></tr>
			<?php

				$q = mysql_query('SELECT * FROM `station` ORDER BY starttime');
				while($s = mysql_fetch_assoc($q)) {
					$h = date("H");
					$m = date("i");		

					$t = explode(":",$s['starttime']);
					$ht = $t[0];
					$mt = $t[1];

					if ($h > $ht) {
						continue;
					} elseif ($h == $ht) {
						if ($m > ($mt-2)) {
							continue;
						}
					}
				
					if ($s['tocity'] == 0) $s['tocity'] = "Capital city";
					if ($s['tocity'] == 1) $s['tocity'] = "Avalon city";
					if ($s['count']) {
						$bt = '<input type="submit" value="купить билет" name="t'.$s['id'].'">';
					} else {
						$bt = "нет в наличии";
					}
					echo '<tr><td bgcolor="#cecece">'.$s['starttime'].'</td><td bgcolor="#cecece">'.$s['tocity'].'</td><td bgcolor="#cecece">'.$s['time'].' мин.</td><td bgcolor="#cecece">'.$s['price'].' <b>кр.</b></td><td bgcolor="#cecece">'.$s['count'].'</td><td bgcolor="#cecece">'.$bt.'</td></tr>';
				}
			?>	
			</table>
			</form>
		</td>
		<td background="http://i.oldbk.com/i/station/up_bg_right.jpg"width=10></td>
		</tr>

		<tr><td colspan=3 height=5 background="http://i.oldbk.com/i/station/gorizontal_line.jpg"></td></tr>
		</table>
	<?php
		if (isset($_GET['error'])) {
			echo '<br><font color=red>';
			switch($_GET['error']) {
				case 1:
					echo 'Билетов нет в наличии';
				break;
				case 2:
					echo 'Такого билета не существует';
				break;
				case 3:
					echo 'У вас недостаточно денег для покупки';
				break;
				case 4:
					echo 'Вы не можете купить билет на это время';
				break;
				case 99:
					echo 'Вы удачно купили билет';
				break;
			}
			echo '</font>';
		}
	?>

	</center>
	</td>
	<td align=right width=250 nowrap valign=top><center>
		<input type=button value='Обновить' onClick="location.href='station.php?'+Math.random();">
		<INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/station.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
		<INPUT TYPE="submit" onclick="location.href='station.php?exit=1';" value="Вернуться" name="exit"><br><br>
		<script>
			<?php
				if ($_SERVER['SERVER_NAME'] == "avaloncity.oldbk.com") {
					$swfname = "clock6.swf?hours=".date("H")."&minutes=".date("i")."&seconds=".date("s");
				} else {
					$swfname = "clock5.swf?hours=".date("H")."&minutes=".date("i")."&seconds=".date("s");
				}                                                  
			?>
			var html='';
			if (navigator.userAgent.match(/MSIE/)) {
				// IE gets an OBJECT tag
				html += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="150" height="130" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"><param name="movie" value="http://i.oldbk.com/i/station/<?php echo $swfname; ?>" /><param name="quality" value="high" /><param name="bgcolor" value="#e0e0e0" /></object>';
			} else {
				// all other browsers get an EMBED tag
				html += '<embed bgcolor=#e0e0e0 src="http://i.oldbk.com/i/station/<?php echo $swfname; ?>" width="150" height="130" quality="best" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
			}
			document.write(html);
		</script><br><br>
		<img src="http://i.oldbk.com/i/station/<?php if ($_SERVER['SERVER_NAME'] == "avaloncity.oldbk.com") echo "avalon_gerb.png"; else echo "cap_gerb.png"; ?>">
		</center>
	</td>
	</tr>
	</table>
	</body>
	</html>