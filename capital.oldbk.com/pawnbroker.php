<?php
$head = <<<HEADHEAD
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<link rel="stylesheet" href="/i/btn.css" type="text/css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<script>
		function MathRound(p) {
			return (Math.round(p*100))/100;
		}

		function CalcPawnBroker() {
			p = pawnbrokerprice;
			d = document;

			days = parseInt(d.getElementById("days").value);

			alertmsg = 'Введите количество дней для расчёта. От 1 до 30 дней.';

			if (isNaN(days) || days < 1 || days > 30) {
				alert(alertmsg);
				return;
			}

			res = "";
			res += "Вы получите: <font color=green>"+p+"</font> кр.<br>";
			perday = MathRound(p*%PERDAY%,2);
			maxperday = %MAXPERDAY%;
			if (perday > maxperday) perday = maxperday;
			res += "Суточный процент: <b>"+perday+"</b> кр.<br>";
			res += "Комиссия ломбарда составит: <b>"+MathRound((perday*days))+"</b> кр.<br>";
			res += "Сумма выкупа вещи составит: <b>"+MathRound((perday*days)+p)+"</b> кр.<br><br><br>";
			res += "<input OnClick=\"document.getElementById('agreeform').submit();\" type=button value='Согласен'>";

			d.getElementById("calcresult").innerHTML = res;
		}
	</script>
	<!-- Asynchronous Tracking GA top piece counter -->
<script type="text/javascript">
 
var _gaq = _gaq || [];

var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
    if(rsrc != null) {
        _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
    }

_gaq.push(['_setAccount', 'UA-17715832-1']);
_gaq.push(['_addOrganic', 'm.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'images.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'video.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push(['_addOrganic', 'm.go.mail.ru', 'q', true]);
_gaq.push(['_addOrganic', 'mail.ru', 'q']);
_gaq.push(['_addOrganic', 'google.com.ua', 'q']);
_gaq.push(['_addOrganic', 'images.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'maps.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push(['_addOrganic', 'm.rambler.ru', 'query', true]);
_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
_gaq.push(['_addOrganic', 'nigma.ru', 's']);
_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
_gaq.push(['_addOrganic', 'sm.aport.ru', 'r']);
_gaq.push(['_addOrganic', 'akavita.by', 'z']);
_gaq.push(['_addOrganic', 'meta.ua', 'q']);
_gaq.push(['_addOrganic', 'search.bigmir.net', 'z']);
_gaq.push(['_addOrganic', 'search.tut.by', 'query']);
_gaq.push(['_addOrganic', 'all.by', 'query']);
_gaq.push(['_addOrganic', 'search.i.ua', 'q']);
_gaq.push(['_addOrganic', 'index.online.ua', 'q']);
_gaq.push(['_addOrganic', 'web20.a.ua', 'query']);
_gaq.push(['_addOrganic', 'search.ukr.net', 'search_query']);
_gaq.push(['_addOrganic', 'search.com.ua', 'q']);
_gaq.push(['_addOrganic', 'search.ua', 'q']);
_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
_gaq.push(['_addOrganic', 'go.km.ru', 'sq']);
_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push(['_addOrganic', 'affiliates.quintura.com', 'request']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
</script>
<!-- Asynchronous Tracking GA top piece end -->
	</HEAD>
	<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0"><tr><td><h3 style="text-align:right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ломбард</h3></td>
	<td align=right>
	<FORM action="pawnbroker.php?exit=1" method=GET>
		<div class="btn-control">
			<INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/pawnbroker.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
			<INPUT class="button-mid btn" TYPE="submit" onclick="location.href='pawnbroker.php?exit=1';" value="Вернуться" name="exit">
		</div>
	</table>
	</form>

	<table width="100%" CELLSPACING="0" CELLPADDING="0" BGCOLOR="#D5D5D5" border=0>
	<tr><td width=64% align=right>
		<table CELLSPACING="0" CELLPADDING="7" border=0><tr><td %main%><a href="?view=main">Ломбард</a></td><td nowrap %sellitems%><a href="?view=sellitems">Сдать вещи</td><td nowrap %viewitems%><a href="?view=viewitems">Забрать вещи</td></tr></table>
	</td><td width="36%" nowrap align="right">У вас в наличии <font color=green>%MONEY% </font>кр.</td></tr>
	</table><br>
HEADHEAD;

$mailrucounter = <<<MAILRUCOUNTER
	<div align=left>
	<!--Rating@Mail.ru counter-->
	<script language="javascript" type="text/javascript"><!--
	d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
	<script language="javascript1.1" type="text/javascript"><!--
	a+=';j='+navigator.javaEnabled();js=11;//--></script>
	<script language="javascript1.2" type="text/javascript"><!--
	s=screen;a+=';s='+s.width+'*'+s.height;
	a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
	<script language="javascript1.3" type="text/javascript"><!--
	js=13;//--></script><script language="javascript" type="text/javascript"><!--
	d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_top">'+
	'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
	a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
	'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
	<noscript><a target="_top" href="http://top.mail.ru/jump?from=1765367">
	<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
	height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
	<script language="javascript" type="text/javascript"><!--
	if(11<js)d.write('--'+'>');//--></script>
	<!--// Rating@Mail.ru counter-->
	</div>
MAILRUCOUNTER;

$foot = <<<FOOTFOOT
	<br><br>
	%MAILRU%
	<br>
	<!-- Asynchronous Tracking GA bottom piece counter-->
<script type="text/javascript">
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>
 
<!-- Asynchronous Tracking GA bottom piece end -->
	</BODY>
	</HTML>
FOOTFOOT;

$main = <<<MAINPAGE
	<table width=720 align=center><tr><td>
	<center><font color=#003388><b>В Ломбарде вы можете получить деньги под залог своих вещей.</b></font></center><br>
	Срок залога вещей <u><b>от 1 до 30 дней под 1% в день</b></u> от получаемой суммы.<br>
	Если расчетный суточный процент превышает 3.33 кр., то процент считается равным 3.33 кр.<br>
	Сумма, выдаваемая на руки, равняется половине цены вещи, которая написана в инвентаре.<br> <br>
	<b>В день выкупа вещи Ломбард возьмет с вас:</b><br>
	сумма, полученная на руки + проценты за прошедшие дни.<br> <br>
	<b>В случае досрочного выкупа вещи, Ломбард возьмет с вас:</b><br>
	сумма, полученная на руки + проценты за прошедшие дни + половина процентов за оставшиеся дни.<br><br>

	В момент сдачи вещи в Ломбард, вы получите квитанцию о залоге вещи, с датой выкупа.<br>
	За три дня до истечения срока залога вам придет телеграмма с напоминанием о дате выкупа вещи. <br> <br>
	<center><font color=#003388><b>Если вещь <u>не выкупается</u> в указанный при залоге срок,<br> она считается проданной в Ломбард и <u>возврату не подлежит</u>.</b></font></center><br><br>
	</td></tr></table>
MAINPAGE;

	function GetPawnBrokerPrice($price) {
		return $price * 0.5;
	}

	function Redirect($path) {
		header("Location: ".$path);
		die();
	}

	// получаем количество дней между двумя таймстампами, округляем
	function GetDiffDates($t1,$t2, $type) {
		$diff = ($t2 - $t1) / (60*60*24);
		return $type($diff);
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	include('connect.php');
	include('functions.php');
	

	$pawnbrokerid = 444;
	$maxperday = 3.33; // максимум 3.33кр в сутки
	$perday = 0.01; // 1% от полученной суммы

	// получаем инфу перса
	$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$_SESSION["uid"]) or die();
	$user = mysql_fetch_assoc($q) or die();

	// поправить рум намбер
	if ($user['room'] != 70) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	if (isset($_GET['exit'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '66' WHERE `users`.`id` = '{$_SESSION['uid']}' ;") or die();
		Redirect('city.php');
	}

	$view = isset($_GET['view']) ? $_GET['view'] : "main";
	$center = "";

	// проверка на уровень
	if ($user['level'] < 4) {
		$view = "main";
	}

	if ($user['align'] == 4) {
		if ($view != "main" && $view != "viewitems") $view = "main";
	}


	switch($view) {
		default:
			$view = "main";
		case "main":
			if ($user['level'] < 4) {
				$center .= '<center><font color=red>Вход в ломбард только с 4го уровня.</font></center>';
				break;
			}
			if ($user['align'] == 4) {
				$center .= '<center><font color=red>Вход со склонностью хаоса запрещен, вы можете только забрать вещи, ранее проданные в ломбард.</font></center>';
				break;
			}
			$center = $main;
		break;
		case "viewitems":
			if (isset($_GET['id'])) {
				$_GET['id'] = intval($_GET['id']);
				// выбираем вещь, проверяем овнера и время
				$q = mysql_query('
					SELECT * FROM pawnbroker AS pawn
					LEFT JOIN oldbk.`inventory` AS inv
					ON pawn.itemid = inv.id
					WHERE pawn.owner = '.$user['id'].' AND (inv.prototype < 55510301 OR inv.prototype > 55510401) AND inv.owner = "'.$pawnbrokerid.'" AND endtime > '.time().' AND pawn.itemid = '.$_GET['id']
				) or die();

				if (mysql_num_rows($q) == 0) {
					Redirect('pawnbroker.php?view=viewitems');
				}
				$item = mysql_fetch_assoc($q);

				$pawnprice = round(GetPawnBrokerPrice($item['cost']),2);
				$perdaytotal = round(($pawnprice*$perday),2);
				if ($perdaytotal > $maxperday) $perdaytotal = $maxperday;

				// цена складывается из выданных денег на руки
				$brokerprice = round($pawnprice,2);

				// из дней пользования, округляем в большую сторону
				$useddays = round($perdaytotal * GetDiffDates($item['starttime'],time(),"ceil"),2);

				// из дней неиспользования, т.е. штрафов за неиспользованные дни
				$notuseddays = round(($perdaytotal * GetDiffDates(time(),$item['endtime'],"floor")) / 2,2);

				$total = round($brokerprice+$useddays+$notuseddays,2);

				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					if ($user['money'] < $total) {
						Redirect('pawnbroker.php?view=viewitems&id='.$_GET['id'].'&error=1');
					}

					// выкупаем вещь из ломбарда

				        //new_delo
					$rec = array();
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'] - $total;
					$rec['target']=$pawnbrokerid;
					$rec['target_login']="ломбард";
					$rec['type']=210; //забираю из ломбарда
					$rec['sum_kr']=$total;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unik'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_incmagic_id']=$item['includemagic'];
					$rec['item_proto']=$item['prototype'];
					$rec['item_sowner']=($item['sowner']>0?1:0);
					$rec['item_arsenal']='';
					$rec['add_info'] = '['.$brokerprice.'/'.$useddays.'/'.$notuseddays.']';
					add_to_new_delo($rec); //юзеру


					// забираем деньги
					mysql_query('UPDATE `users` set money = money - '.$total.' WHERE id = '.$user['id']) or die();

					// возвращаем вещь владельцу
					mysql_query('UPDATE oldbk.`inventory` SET owner = "'.$user['id'].'" WHERE id = '.$_GET['id']) or die();

					// удаляем из ломбардовского списка
					mysql_query('DELETE FROM `pawnbroker` WHERE itemid = '.$_GET['id']) or die();

					Redirect('pawnbroker.php?view=viewitems&error=0');
				}


				$center .= '<form method="POST" id="agreeform"><center><input OnClick="location.href=\'pawnbroker.php?view=viewitems\'" type="button" value="Вернуться"><br>';
				if (isset($_GET['error'])) {
					$text = "";
					switch($_GET['error']) {
						case 0:
							$text = 'Вы удачно выкупили вещь из ломбарда.';
						break;
						case 1:
							$text = 'У вас недостаточно денег.';
						break;
					}
					$center .= '<br><font color=red>'.$text.'<br>';
				}
				$center .= '<br><TABLE BORDER=0 CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5"><tr bgcolor=#A5A5A5><td colspan=2 align=center>Вы хотите забрать из ломбарда</td></tr>';
				// output buffering для того чтобы перехватить вывод showitem()
				ob_start();
				showitem($item,0,false,'#C7C7C7','&nbsp;');
				$center .= ob_get_contents();
				ob_end_clean();
				$center .= '<tr><td bgcolor=#C7C7C7 colspan=2>';
				$center .= 'Выкупить вещь из ломабарда за <b>'.$total.'</b> кр.<br> <u>Сумма выкупа содержит:</u> <br>';
				$center .= 'Сумма выданная на руки: <b>'.$brokerprice.'</b><br>';
				$center .= 'Суточные проценты: <b>'.$useddays.'</b><br>';
				if ($notuseddays) $center .= 'Штраф за досрочное изьятие: <b>'.$notuseddays.'</b><br>';
				$center .= '<br><input type=button value="Забрать" OnClick="document.getElementById(\'agreeform\').submit();">';
				$center .= '</td></tr>';
				$center .= '</table></center></form>';
			} else {
				$center = '<center>';

				if (isset($_GET['error'])) {
					$text = "";
					switch($_GET['error']) {
						case 0:
							$text = 'Вы удачно выкупили вещь из ломбарда.';
						break;
					}
					$center .= '<font color=red>'.$text.'<br><br>';
				}

				$q = mysql_query('
					SELECT * FROM pawnbroker AS pawn
					LEFT JOIN oldbk.`inventory` AS inv
					ON pawn.itemid = inv.id
					WHERE pawn.owner = '.$user['id'].' AND (inv.prototype < 55510301 OR inv.prototype > 55510401) AND inv.owner = "'.$pawnbrokerid.'" AND endtime > '.time().' ORDER BY endtime DESC
				') or die();

				if (mysql_num_rows($q) == 0 && !isset($_GET['error'])) {
					$center .= '<center><font color=red>Вы ничего не сдали в ломбард.</font></center>';
				} else {
					$center .= '<TABLE BORDER=0 CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';
					$i = 0;

					// output buffering для того чтобы перехватить вывод showitem()
					ob_start();
					while($item = mysql_fetch_assoc($q)) {
						$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
						$pawnprice = round(GetPawnBrokerPrice($item['cost']),2);
						$perdaytotal = round(($pawnprice*$perday),2);
						if ($perdaytotal > $maxperday) $perdaytotal = $maxperday;
						$action = 'Вы&nbsp;должны&nbsp;выкупить&nbsp;до:&nbsp;'.date("d/m/Y\&\\n\b\s\p\;H:i:s",$item['endtime']).'<br>';

						// цена складывается из выданных денег на руки
						$buynow = $pawnprice;

						// из дней пользования, округляем в большую сторону
						$buynow += $perdaytotal * GetDiffDates($item['starttime'],time(),"ceil");

						// из дней неиспользования, т.е. штрафов за неиспользованные дни
						$buynow += ($perdaytotal * GetDiffDates(time(),$item['endtime'],"floor")) / 2;

						$buynow = round($buynow,2);

						$action .= '<a href="?view=viewitems&id='.$item['itemid'].'">Выкупить сейчас</a> за: <b>'.$buynow.'</b> кр.<br><br>';
						showitem($item,0,false,$color,$action);
						$center .= ob_get_contents();
						ob_clean ();
						$i++;
					}
					ob_end_clean();
					$center .= '</table>';
					$center .= '</center>';
				}
			}
		break;
		case "sellitems":
			if (isset($_GET['id'])) {
				$_GET['id'] = intval($_GET['id']);

				// выбираем шмотку с проверкой возможности сдатия в ломбард
				$q = mysql_query('SELECT * FROM oldbk.`inventory` AS inv WHERE ((`type` > 0 AND `type` < 12) OR `type` = 27 OR `type` = 28 ) AND (inv.prototype < 55510301 OR inv.prototype > 55510401) AND arsenal_klan = "" AND present != "Арендная лавка" AND id = '.$_GET['id'].' AND dressed=0 AND setsale=0 AND dategoden=0 AND bs_owner=0  AND prokat_idp=0 AND owner = '.$user['id']) or die();
				if (mysql_num_rows($q) == 0) Redirect('pawnbroker.php?view=sellitems');
				$item = mysql_fetch_assoc($q) or die();
				$pawnprice = round(GetPawnBrokerPrice($item['cost']),2);

				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					if (!isset($_POST['days'])) die();
					$_POST['days'] = intval($_POST['days']);
					if ($_POST['days'] < 1 || $_POST['days'] > 30) die();

					// время когда вещь пропадёт
					$endtime = time()+($_POST['days']*60*60*24);

					// сдаём вещь в ломард
					if($item['add_pick']!='') {
	    					undress_img($item);
					}


				        //new_delo
					$rec = array();
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'] + $pawnprice;
					$rec['target']=$pawnbrokerid;
					$rec['target_login']="ломбард";
					$rec['type']=211; //сдаю в ломбарда
					$rec['sum_kr']=$pawnprice;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unik'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['add_info'] = $_POST['days'];
					$rec['item_incmagic_id']=$item['includemagic'];
					$rec['item_proto']=$item['prototype'];
					$rec['item_sowner']=($item['sowner']>0?1:0);
					add_to_new_delo($rec); //юзеру


					// добавляем денег
					mysql_query('UPDATE `users` set money = money + '.$pawnprice.' WHERE id = '.$user['id']) or die();

					// отбираем вещь в ломбард
					mysql_query('UPDATE oldbk.`inventory` AS `inv` SET owner = "'.$pawnbrokerid.'" WHERE (inv.prototype < 55510301 OR inv.prototype > 55510401) AND id = '.$_GET['id']) or die();

					// добавляем вещь в ломбардовский список
					mysql_query('
						INSERT INTO `pawnbroker` (`itemid` ,`owner`, `starttime`, `endtime`)
						VALUES
						(
							"'.$_GET['id'].'",
							"'.$user['id'].'",
							"'.time().'",
							"'.$endtime.'"
						)
					') or die();

					// добавляем бумагу в инвентарь юзеру
					$perdaytotal = round($pawnprice*$perday,2);
					if ($perdaytotal > $maxperday) $perdaytotal = $maxperday;
					$text = 'Вы сдали в ломбард "'.$item['name'].'"<br> Вам необходимо выкупить вещь до '.date("d/m/Y H:i:s",$endtime).' за '.(($perdaytotal*$_POST['days'])+$pawnprice).' кр. иначе она пропадёт';
					mysql_query('
						INSERT INTO oldbk.`inventory` (`owner`,`name`,`type`,`massa`,`cost`,`img`,`letter`,`maxdur`,`isrep`,`gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`)
						VALUES(
							"'.$user['id'].'",
							"Бумага",
							"50",
							1,
							0,
							"paper100.gif",
							"'.mysql_escape_string($text).'",
							1,
							0,0,0,0,0,"",0,0,0
						)
					') or die();

					Redirect('pawnbroker.php?view=sellitems&ok');
				}

				$center .= '<form method="POST" id="agreeform"><center><input OnClick="location.href=\'pawnbroker.php?view=sellitems\'" type="button" value="Вернуться"><br><br><TABLE BORDER=0 CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5"><tr bgcolor=#A5A5A5><td colspan=2 align=center>Вы хотите сдать в ломбард</td></tr>';
				// output buffering для того чтобы перехватить вывод showitem()
				ob_start();
				showitem($item,0,false,'#C7C7C7','&nbsp;');
				$center .= ob_get_contents();
				ob_end_clean();
				$center .= '<tr><td colspan=2 >На <input type=text id=days maxlength=2 size=2 name=days> дней (от 1 до 30 дней) <input type=button OnClick="CalcPawnBroker();" value="Расcчитать"></td></tr>';
				$center .= '<tr><td bgcolor=#C7C7C7 colspan=2><div id=calcresult></div></td></tr>';
				$center .= '</table></center></form>';
				$center .= '<script>var pawnbrokerprice = parseFloat("'.$pawnprice.'");</script>';
			} else {
				// выбираем вещи из категории "обмундирование", тип 1-11 и 27 28 и не арсенальную
				$q = mysql_query('SELECT * FROM oldbk.`inventory` AS `inv` WHERE ((`type` > 0 AND `type` < 12) OR `type` = 27 OR `type` = 28) AND (inv.prototype < 55510301 OR inv.prototype > 55510401) AND arsenal_klan = ""  AND present != "Арендная лавка" AND dressed=0 AND setsale=0 AND dategoden=0 AND bs_owner=0 AND prokat_idp=0  AND owner = '.$user['id']) or die();
				$center = '<center>';
				if (isset($_GET['ok'])) $center .= '<font color=red>Вещь удачно сдана в ломбард, не забудьте вовремя её забрать. Квитанция о сдаче вещи лежит у вас в рюкзаке.</font><br><br>';
				$center .= '<TABLE BORDER=0 CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';
				$i = 0;

				// output buffering для того чтобы перехватить вывод showitem()
				ob_start();
				while($item = mysql_fetch_assoc($q)) {
					$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
					$pawnprice = round(GetPawnBrokerPrice($item['cost']),2);
					$perdaytotal = round(($pawnprice*$perday),2);
					if ($perdaytotal > $maxperday) $perdaytotal = $maxperday;
					$action = '<a href="?view=sellitems&id='.$item['id'].'">Сдать&nbsp;вещь&nbsp;в&nbsp;ломбард</a><br>За&nbsp;<b>'.$pawnprice.'</b>&nbsp;кр.&nbsp;(суточный&nbsp;%&nbsp;&minus;&nbsp;<b>'.$perdaytotal.'</b>&nbsp;кр.)';
					showitem($item,0,false,$color,$action);
					$center .= ob_get_contents();
					ob_clean ();
					$i++;
				}
				ob_end_clean();
				$center .= '</table></center>';
			}
		break;
	}

	$head = str_replace('%MONEY%',$user['money'],$head);
	$head = str_replace('%'.$view.'%','BGCOLOR="#A5A5A5"',$head);
	$head = str_replace('%PERDAY%',$perday,$head);
	$head = str_replace('%MAXPERDAY%',$maxperday,$head);
	echo $head;
	echo $center;
	if(isset($_SESSION['vk']) && is_array($_SESSION["vk"])) {
		echo str_replace("%MAILRU%","",$foot);
	} else {
		echo str_replace("%MAILRU%",$mailrucounter,$foot);
	}
?>