<?php
//19.07.11 08:49 Заплатил 25 кр. за подачу заявки на рекрутство.
$head = <<<HEADHEAD
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<link rel="stylesheet" href="/i/btn.css" type="text/css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
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
	<TABLE border=0 width=100% cellspacing="0" cellpadding="0">
	<td align=left>
		<div class="btn-control">
			<input class="button-big btn" type=button onclick="location.href='abilshop.php';" value="Купить Клановые реликты" />
		</div> 
	</td>
	<td align=right>
	<FORM action="city.php" method=GET>
		<div class="btn-control">
			<INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/klanedit.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
			<INPUT class="button-mid btn" TYPE="submit" onclick="location.href='city.php?cp=1';" value="Вернуться" name="strah">
		</div>
	</table>
	</form>
	<h3>Заявка на регистрацию клана</h3>
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


$claninfo = <<<CLANINFO
	Для регистрации клана необходимо иметь:
	<OL>
	<LI>сайт клана со счетчиком mail.ru в разделе игры.
	<LI>описание клана для энциклопедии ОлдБК.
	<LI>значки:
	<DL>
		<DD>- значок клана для чата (показывается рядом с ником персонажа), gif картинка с прозрачным фоном 24х15 не более чем 32 цвета (анимация запрещена!).
		<DD>- большой значок клана, для энциклопедии, в круге gif картинка с прозрачным фоном 100х99
	</DL>
	<LI>перед подачей заявки вы должны пройти проверку у паладинов
	</OL>
	Стоимость регистрации кланов:<BR>
	<img src="i/align_0.gif">серый - 12000 кр.<BR>
	<img src="i/align_3.gif">темный - 15000 кр.<BR>
	<img src="i/align_2.gif">нейтральный - 15000 кр.<BR>
	<img src="i/align_6.gif">светлый - 15000 кр.<BR>
	<BR>
	Заявку на регистрацию подает глава клана, у вас должна быть при себе необходимая сумма.
CLANINFO;

$baseform = <<<BASEFORM
	<form method="post" ENCTYPE="multipart/form-data">
	%CLANINFO%
	<br><br>
	<font color=red>%ERROR%</font>
	<br>
	<fieldset>
	<legend>Заявка</legend>
	<table>
	<tr>
	<td>Название клана</td><td><input type="text" name="klanname" size=60 value=""></td></tr>
	<tr><td>Английская аббревиатура <br>(только английские буквы, одно слово, не более 25 символов)</td><td><input type="text" name="klanabbr" size=25 value=""></td></tr>
	<tr><td>Ссылка на официальный сайт клана<br>
	<small>Принимается URL вида http://oldbk.com <br>(если сайта еще нет или он в разработке, оставьте поле пустым)</small></td><td><input type="text" size=30 name="http" value=""></td></tr>
	<tr><td>Маленький значок (не более 4кб)</td><td><input type="file" name="small"></td></tr>
	<tr><td>Большой значок (не более 10кб)</td><td><input type="file" name="big"></td></tr>
	<tr><td>Склонность клана</td><td><select name="klanalign"><option value="0">серый<option value="3">темный<option value="2">нейтральный<option value="6">светлый</select></td></tr>
	<!--
	<tr><td>Если рекрут клан, выберите клан основу:</td><td>
	<select size="1" name="base_klan">
		<option value=0>нет</option>
		%BASECLANS%
	</select></td></tr>
	-->
	<tr><td  valign='top'>Описание для библиотеки ОлдБК:</td>
	<td><textarea cols=80 rows=10 name="klandescr"></textarea></td>
	<tr><td></td><td>
		<div class="btn-control">
			<input class="button-mid btn" type="submit" name="regclan" value="Подать заявку">
		</div>
		</td></tr>
	</table>
	</fieldset>
	</form>
BASEFORM;

$recruitform = <<<RECRUITFORM
	Вы можете стать рекрутом следующих кланов (стоимость %UPDATEPRICE% кр.):
	<form name="rec" action="klanedit.php" method="POST">
		Хочу стать рекрутом клана:
		<select size="1" name="base_klan">
		%BASECLANS%
		</select>
		<div class="btn-control" style="display: inline-block">
			<input class="button-mid btn" type="submit" name="makerecruit" value="Записаться в рекруты">
		</div>
	</form>
RECRUITFORM;


$updateinfoform = <<<UPDATEINFOFORM
	<form name="updateinfo" action="klanedit.php" method="post">
		<fieldset>
		<legend>Обновление информации о клане (стоимость %PRICE% кр.)</legend>
		<table>
		<tr><td>Ссылка на официальный сайт клана<br><small><b>При удалении адреса сайта, сайт автоматически удаляется из рейтинга. <br>Данные о счетчике так же будут удалены. <br> При повторной регистрации клана в рейтинге сайтов, будет зарегистрирован НОВЫЙ счетчик.<br><br><b></small></td><td valign=top><input type="text" size=30 name="http" value="%HTTP%"></td></tr>
		<tr><td  valign='top'>Описание для библиотеки ОлдБК:</td>
		<td><textarea cols=80 rows=10 name="klandescr">%KLANDESCR%</textarea></td>
		<tr><td></td><td>
			<div class="btn-control">
				<input class="button-mid btn" type="submit" name="updateinfo" value="Обновить информацию">
			</div>
		</td></tr>
		</table>
		</fieldset>
	</form>
UPDATEINFOFORM;

	function Redirect($path) {
		header("Location: ".$path);
		die();
	}

	function GetStatus($user,$klan,$effects,$clan_reg,$eff_pal) {
		// админы
		if (strtolower($user['klan']) === "adminion" || strtolower($user['klan']) === "radminion") {
			return "isadmin";
		}

		// 4ый уровень
		if ($user['level'] < 4) return "lowlevel";

		// склонка без клана
		if ($user['align'] != '0' && empty($user['klan'])) return "lonegunmen";


		// проверяем есть ли заявка от этого перса
		if (count($clan_reg)) {
			if (!empty($user['klan'])) {
				// перс в клане, значит заявка или на обновление инфы или на рекрутство
				return "waitupdate";
			} else {
				// перс не в клане, значит заявка на регистрацию
				return "waitapprove";
			}
		}

		// есть клан
		if (!empty($user['klan'])) {
			if (count($klan) && $klan["base_klan"] == 0 && $klan["rekrut_klan"] == 0) {
				// return "inclan";
				// перс - глава, клан является основным и не имеет рекуртов

				// проверяем подал ли заявку какой-нибудь клан на рекрутство к этому клану
				$q = mysql_query('SELECT * FROM clans_reg WHERE base_klan = '.$klan["id"]) or die();
				if (mysql_num_rows($q) > 0) return "waitrecruit";

				// никто не подал заявку на рекрутство, можем сами подать сами
				return "canberecruit";
			}

			if (count($klan) && $klan["base_klan"] != 0) {
				// перс - глава и это рекрут клан
				return "recruitclan";
			}
			if (count($klan) && $klan["rekrut_klan"] != 0) {
				// перс - глава и это базовый клан и этот клан уже имеет рекрута
				return "basehaverecruit";
			}
			return "inclan";
		}

		// нету клана

		// проверяем эффект пал проверки
		reset($effects);
		$foundcheck = false;
		while(list($k,$v) = each($effects)) {
			if ($v['type'] == $eff_pal) {
				$foundcheck = true;
				break;
			}
		}
		if (!$foundcheck) return "nochecked";

		return "canreg";
	}

	function ShowRecruitForm($user,$form,$updateprice) {
		$baseclans = "";
                $data = mysql_query('SELECT id, short FROM clans where short <> "'.$user['klan'].'" AND rekrut_klan = 0 AND base_klan = 0 AND align = "'.$user['align'].'" ORDER BY short') or die();
		while($base_klan = mysql_fetch_assoc($data)) {
			$baseclans .= '<option value='.$base_klan['id'].'>'.$base_klan['short'].'</option>';
		}

		$ret = str_replace("%BASECLANS%",$baseclans,$form);
		$ret = str_replace("%UPDATEPRICE%",$updateprice,$ret);
		return $ret;
	}

	function ShowBaseForm($user,$form,$error,$regprice) {
		$baseclans = "";
                $data = mysql_query('SELECT id, short FROM clans where short <> "'.$user['klan'].'" AND rekrut_klan = 0 AND base_klan = 0 ORDER BY short') or die();
		while($base_klan = mysql_fetch_assoc($data)) {
			$baseclans .= '<option value='.$base_klan['id'].'>'.$base_klan['short'].'</option>';
		}

		$ret = str_replace("%BASECLANS%",$baseclans,$form);
		reset($regprice);
		while(list($k,$v) = each($regprice)) {
			$ret = str_replace('%ALIGN'.$k.'%',$v,$ret);
		}
		if (!count($error)) {
			$ret = str_replace("%ERROR%","",$ret);
		} else {
			$errtxt = "";
			reset($error);
			while(list($k,$v) = each($error)) {
				$errtxt .= $v.'<BR>';
			}
			$ret = str_replace("%ERROR%",$errtxt,$ret);
		}

		return $ret;
	}

	function ShowUpdateInfoForm($user,$klan,$form,$price) {
		$ret = $form;
		$ret = str_replace("%PRICE%",$price,$ret);
		$ret = str_replace("%HTTP%",htmlspecialchars($klan["homepage"],ENT_QUOTES),$ret);
		$ret = str_replace("%KLANDESCR%",htmlspecialchars($klan["descr"],ENT_QUOTES),$ret);
		return $ret;
	}

	function ShowApproveForm() {
		$ret = '<form method=POST action="klanedit.php"><table width=100%>';
		$q = mysql_query('
			SELECT rcl.*, DATE_FORMAT(rcl.date,"%d/%m/%Y %H:%i") as date , cl.short as clshort, cl.homepage as clhomepage, cl.rekrut_klan AS clrekrutklan FROM clans_reg as rcl
			LEFT JOIN
			clans as cl
			ON cl.id = rcl.base_klan
		') or die();
		while($clan = mysql_fetch_assoc($q)) {
			if (empty($clan['clshort']) && $clan['base_klan'] != 0) {
				// в заявке есть базовый клан, но самого базового клана нет. правим заявку
				$q2 = mysql_query('UPDATE `clan_reg` SET base_klan = 0 WHERE id = '.$clan['id']);
				Redirect("klanedit.php");
			}
			$owner = mysql_query('SELECT * FROM `users` WHERE id = '.$clan['owner']) or die();
			$owner = mysql_fetch_assoc($owner) or die();

			$ret .= '<tr><td>'.$clan['date'].'</td><td>
			 <a href="http://oldbk.com/encicl/klani/clans.php?clan='.$clan['name'].'" target=_blank>'.$clan['name'].'</a>
			 </td><td>&nbsp;
			 <a href="http://oldbk.com/encicl/klani/clans.php?clan='.$clan['abr'].'" target=_blank>'.$clan['abr'].'</a> 
			 </td><td>'.nick_hist($owner).'</td>';
			
			if (!empty($clan['sznak']) && !empty($clan['bznak'])) 
			{
				// заявка является регистрацией на клан
				$ret .= '<td><img src="i/nklan/'.$clan['sznak'].'"></td><td><img src="i/nklan/'.$clan['bznak'].'"></td>';
				$ret .= '<td><img src="i/align_'.$clan['align'].'.gif"></td>';
				$ret .= '<td><input type=text size=30 name="http_'.$clan['id'].'" value="'.htmlspecialchars($clan['http']).'"></td>';
				$ret .= '<td><input type=hidden size=30 id="reject_'.$clan['id'].'" name="reject_'.$clan['id'].'" value=""></td>';
				$ret .= '<td><textarea cols=80 rows=10 name="klandescr_'.$clan['id'].'">'.$clan['descr'].'</textarea></tD>';
																				
				$ret .= '<td>
					<div class="btn-control">
						<input class="button-mid btn" type=submit name="reg_'.$clan['id'].'" value="Зарегистрировать"> 
						<input class="button-mid btn" type="submit" OnClick="var t = prompt(\'Причина отказа\'); if (!t) return false; document.getElementById(\'reject_'.$clan['id'].'\').value = t;" name="delete_'.$clan['id'].'" value="Отказать">
					</div>';
				if ($clan['base_klan'])
					$ret .= '<br>Базовый: '.$clan['clshort'].', Рекрут: '.$clan['abr'];
				$ret .= '</td></tr>';
			} 
			else 
			{
				$ret .= '<td></td><td></td>';
				$ret .= '<td><img src="i/align_'.$clan['align'].'.gif"></td>';

				$connected = false;
				if ($clan['base_klan'] && !empty($clan['clrekrutklan'])) 
				{
					// есть базовый клан у заявки и есть рекрут клан и у базового есть рекрут clrekrutklan
					// надо выбрать рекрут и проверить его базовый и если совпадают - значит соеденены
					$q2 = mysql_query('SELECT `base_klan` FROM clans WHERE id = '.$clan['clrekrutklan']) or die();
					$data = mysql_fetch_assoc($q2);
					if ($data !== FALSE) {
						if ($data['base_klan'] == $clan['base_klan']) {
							$connected = true;
						}
					}
				}
				if ((empty($clan['clshort']) && $clan['base_klan'] == 0) || $connected == true) 
				{
					$reit=mysql_query("SELECT * FROM topsites.top WHERE klan='".$clan['abr']."' LIMIT 1");
					
					if(mysql_num_rows($reit)>0)
					{
						$ret.='<td><font color=red>Клан в рейтинге<font></td>';
					}
					else
					{
						$ret.='<td><font color=green>Клан НЕ в рейтинге</font></td>';
					}
					
					// если нет базового клана в заявке или кланы уже связаны
					$ret .= '<td><input type=hidden size=30 name="http_'.$clan['id'].'" value="'.htmlspecialchars($clan['http']).'"><a href="'.htmlspecialchars($clan['http']).'" target=_blank>'.htmlspecialchars($clan['http']).'</a></td>';
					$ret .= '<td><textarea cols=80 rows=10 name="klandescr_'.$clan['id'].'">'.$clan['descr'].'</textarea></tD>';
					$ret .= '<td>
						<div class="btn-control">
							<input class="button-mid btn" type=submit name="update_'.$clan['id'].'" value="Обновить информацию"> 
							<input class="button-mid btn" type=submit OnClick="if (!confirm(\'Вы уверены?\')) return false;" name="delete_'.$clan['id'].'" value="Удалить">
						</div>';
					$ret .= '<td>Причина удаления: <input type=text name="reason_'.$clan['id'].'" >';
					
					$ret .= '</td></tr>';
				} 
				else 
				{
					// кланы не связаны - заявка это прицепление рекрутом к базовому
					$ret .= '<td></td>';
					$ret .= '<td>Базовый: '.$clan['clshort'].', Рекрут: '.$clan['abr'].'</td>';

					// проверка на войны
					$iswar = false;
					$idlist = array();

					$q2 = mysql_query('SELECT id FROM oldbk.clans WHERE short = "'.$clan['clshort'].'" or short = "'.$clan['abr'].'"');
					while($idl = mysql_fetch_assoc($q2)) {
						$idlist[] = $idl['id'];
					}
					if (count($idlist)) {
						$q2 = mysql_query('select * from oldbk.`clans_war_2` where (agressor IN ('.implode(",",$idlist).') OR defender IN ('.implode(",",$idlist).')) AND date > '.time());
						if (mysql_num_rows($q2) > 0) {
							$iswar = true;
						}
					}

					if ($iswar) {
						$ret .= '<td><b>Один из кланов находится в войне</b> 
							<div class="btn-control" style="display: inline-block;">
								<input class="button-mid btn" type=submit OnClick="if (!confirm(\'Вы уверены?\')) return false;" name="delete_'.$clan['id'].'" value="Удалить">
							</div>
							</td></tr>';
					} else {
						$ret .= '<td>
							<div class="btn-control">
								<input class="button-big btn" type=submit name="recruit_'.$clan['id'].'" value="Сделать рекрутом"> 
								<input class="button-mid btn" type=submit OnClick="if (!confirm(\'Вы уверены?\')) return false;" name="delete_'.$clan['id'].'" value="Удалить">
							</div>
							</td></tr>';
					}
				}
			}
		}
		$ret .= '</form></table>';
		return $ret;
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	require_once('connect.php');
	require_once('functions.php');
	require_once('./clouddndtrack/cloud_api.php');


	$eff_pal = "20";

	$regimagepath = './i/nklan/';

	$updateprice = 25; // стоимость обновления информации о клане
	$regprice = array(0 => 12000, 2 => 15000, 3 => 15000, 6 => 15000); // стоимость регистрации клана
	$regname = array(0 => "серый", 2 => "нейтральный", 3 => "тёмный", 6 => "светлый");

	// получаем инфу перса
	$q = mysql_query('SELECT * FROM `users` WHERE `id` = '.$_SESSION["uid"]) or die();
	$user = mysql_fetch_assoc($q) or die();

	if ($user['room'] != 28) Redirect("main.php");
	if ($user['battle'] != 0) Redirect("fbattle.php");

	// клан
	$klan = array();
	if (!empty($user['klan'])) {
		$q = mysql_query('SELECT * FROM `clans` WHERE `glava` = '.$_SESSION["uid"].' and time_to_del=0 LIMIT 1') or die(); // если клан живой
		if (mysql_num_rows($q) > 0) $klan = mysql_fetch_assoc($q);
	}

	// регистрация
	$clan_reg = array();
	$q = mysql_query('SELECT * FROM `clans_reg` WHERE `owner` = '.$_SESSION["uid"]) or die();
	if (mysql_num_rows($q) > 0) $clan_reg = mysql_fetch_assoc($q);

	// эффекты
	$effects = array();
	$q = mysql_query('SELECT `time`,`type`,`add_info` FROM `effects` WHERE `owner` = '.$_SESSION["uid"].' AND (type = '.$eff_pal.' OR type = '.$eff_align_type.') ') or die();
	if (mysql_num_rows($q) > 0) {
		while($res = mysql_fetch_assoc($q)) {
			$effects[] = $res;
		}
	}


	$status = GetStatus($user,$klan,$effects,$clan_reg,$eff_pal);

	$error = array();

	// если отправлена форма
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if ($status == 'isadmin' && !isset($_POST['updateinfo'])) {
			reset($_POST);
			while(list($k,$v) = each($_POST)) {
				$t = explode("_",$k);
				if (count($t) == 2) {
					if ($t[0] == "delete") {
						// удаляем заявку
						$id = intval($t[1]);
						$q = mysql_query('SELECT * FROM `clans_reg` WHERE `id` = '.$id) or die();
						if (mysql_num_rows($q) > 0) {
							$data = mysql_fetch_assoc($q);

							// отсылаем сообщение
							$q = mysql_query('SELECT * FROM `users` WHERE id = '.$data['owner']) or die();
							$data2 = mysql_fetch_assoc($q) or die();

							if (!empty($data['sznak']) && !empty($data['bznak'])) {
								@unlink($regimagepath.$data['sznak']);
								@unlink($regimagepath.$data['bznak']);

								$rec = array();
					    			$rec['owner']=$data2[id]; 
								$rec['owner_login']=$data2[login];
								$rec['owner_balans_do']=$data2['money'];
								$rec['owner_balans_posle']=$data2['money']+(floor($regprice[$data["align"]]*0.9));
								$rec['target']=0;
								$rec['target_login']="регистратура";
								$rec['type']=238; // регистрация клана
								$rec['sum_kr']=floor($regprice[$data["align"]]*0.9);
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								add_to_new_delo($rec); //юзеру
	
								// добавляем бабки
								mysql_query('UPDATE `users` set money = money + '.floor($regprice[$data["align"]]*0.9).' WHERE id = '.$data2['id']) or die();
	
								telepost($data2['login'],'<font color=red>Внимание!</font> Ваша заявка в регистратуре кланов была отклонена. Причина '. $_POST['reject_'.$id]);
							} else {
								$rec = array();
					    			$rec['owner']=$data2[id]; 
								$rec['owner_login']=$data2[login];
								$rec['owner_balans_do']=$data2['money'];
								$rec['owner_balans_posle']=$data2['money']+floor($updateprice*0.9);
								$rec['target']=0;
								$rec['target_login']="регистратура";
								$rec['type']=238; // регистрация клана
								$rec['sum_kr']=floor($updateprice*0.9);
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								add_to_new_delo($rec); //юзеру
	
								// добавляем бабки
								mysql_query('UPDATE `users` set money = money + '.floor($updateprice*0.9).' WHERE id = '.$data2['id']) or die();
	
								telepost($data2['login'],'<font color=red>Внимание!</font> Ваша заявка на обновление информации была отклонена. Причина '. $_POST['reject_'.$id]);
							}


							mysql_query('DELETE FROM `clans_reg` WHERE `id` = '.$id) or die();
						}
						break;
					}
					if ($t[0] == "update") {
						// обновляем инфу о клане
						$id = intval($t[1]);
						$q = mysql_query('SELECT `abr`,`owner` FROM `clans_reg` WHERE `id` = '.$id) or die();
						if (mysql_num_rows($q) > 0) {
							$data = mysql_fetch_assoc($q);

							// обновляем
							mysql_query('UPDATE `clans` SET `homepage` = "'.$_POST['http_'.$id].'", `descr` = "'.mysql_escape_string($_POST['klandescr_'.$id]).'" WHERE `short` = "'.mysql_escape_string($data['abr']).'"') or die();
							echo $_POST['http_'.$id];
							
							//обновляем URL в рейтинге
							if($_POST['http_'.$id]!='')
							{
								mysql_query("UPDATE topsites.top set url='".$_POST['http_'.$id]."' WHERE klan='".mysql_escape_string($data['abr'])."'");
							}
							else
							if($_POST['http_'.$id]=='')
							{//если юрл пустой - удаляем из рейтинга сайт
								mysql_query("DELETE FROM topsites.top WHERE klan='".mysql_escape_string($data['abr'])."' limit 1");	
							}
							
							echo mysql_error();
							// отсылаем сообщение
							$q = mysql_query('SELECT `login` FROM `users` WHERE id = '.$data['owner']) or die();
							$data = mysql_fetch_assoc($q) or die();
							telepost($data['login'],'<font color=red>Внимание!</font> Ваша заявка на обновление информации о клане была утверждена.');

							mysql_query('DELETE FROM `clans_reg` WHERE `id` = '.$id) or die();
						}
						break;
					}
					if ($t[0] == "recruit") {
						// делаем клан рекрутом
						$id = intval($t[1]);
						$q = mysql_query('SELECT * FROM `clans_reg` WHERE `id` = '.$id) or die();
						if (mysql_num_rows($q) > 0) {
							$clan = mysql_fetch_assoc($q);
							$q2 = mysql_query('SELECT * FROM `clans` WHERE `rekrut_klan` = 0 AND `base_klan` = 0 AND `id` = "'.$clan["base_klan"].'"') or die();
							if (mysql_num_rows($q2) > 0) {
								$klan = mysql_fetch_assoc($q2);

								// clan - клан подавший заявку на рекруство
								// klan - клан основа для рекрута
								if ($clan['align'] == $klan['align']) {
									// узнаём id рекрута
									$q2 = mysql_query('SELECT `id` FROM `clans` WHERE `short` = "'.mysql_escape_string($clan['abr']).'"') or die();
									$idr = mysql_fetch_assoc($q2) or die();

									// обновление рекрута, таймер на удаление ставим от основы
									mysql_query('UPDATE `clans` set base_klan = '.$clan['base_klan'].', `tax_date` ='.$klan['tax_date'].' WHERE `short` = "'.mysql_escape_string($clan['abr']).'"') or die();

									// обновление базового
								        mysql_query('UPDATE `clans` set rekrut_klan = '.$idr['id'].' WHERE id = '.$clan['base_klan']) or die();
										
									mysql_query('DELETE FROM topsites.top WHERE klan="'.$clan['short'].'" AND ban=1');
										
									// отсылаем сообщение
									$q = mysql_query('SELECT `login` FROM `users` WHERE id = '.$clan['owner']) or die();
									$data = mysql_fetch_assoc($q) or die();
									telepost($data['login'],'<font color=red>Внимание!</font> Ваша заявка на обновление информации о клане была утверждена.');

									mysql_query('DELETE FROM `clans_reg` WHERE `id` = '.$id) or die();
								}
							}
						}
					}
					if ($t[0] == "reg") {
					
						// регистрируем клан
						$id = intval($t[1]);
						$q = mysql_query('SELECT * FROM `clans_reg` WHERE `id` = '.$id) or die();
						if (mysql_num_rows($q) > 0) {
							
							$clan = mysql_fetch_assoc($q);
							//print_r($clan);
							// права на клан
							$rights[$clan['owner']][0]=1;
							$rights[$clan['owner']][1]=1;


							// добавляем клан
							mysql_query('
									INSERT INTO `clans` SET
									`short` = "'.mysql_escape_string($clan['abr']).'",
									`name` = "'.mysql_escape_string($clan['name']).'",
									`glava`= "'.$clan['owner'].'",
									`descr`= "'.mysql_escape_string($_POST['klandescr_'.$id]).'",
									`align`= "'.$clan['align'].'",
									`homepage`= "'.$_POST['http_'.$id].'",
									`vozm` = "'.mysql_escape_string(serialize($rights)).'",
									`mshadow` = "",
									`wshadow` = "",
									`base_klan` = '.$clan['base_klan'].',
									`tax_date` ='.(time()+60*60*24*30).';') or die();
							
					                $newclanid = mysql_insert_id();
							
							// штраф на смену склонки
							if($clan['align'] != 0) {
								mysql_query(
									'INSERT INTO `effects`
									(`type`, `name`, `owner`, `time`, `add_info`, `lastup`)
									VALUES
									(
										"'.$eff_align_type.'",
										"Штраф склонки",
										"'.$clan['owner'].'",
										"'.$eff_align_time.'",
										"'.$clan['align'].'",
										0
									)
								') or die();
				                        }

							// обновляем базовый клан, если регистрируемый клан - рекруты
							/*
							if ($clan['base_klan'] != 0) {
								mysql_query('UPDATE `clans` SET `rekrut_klan` = '.$newclanid.' WHERE id = '.$clan['base_klan']) or die();
							}
							*/

							// отсылаем сообщение
							$q = mysql_query('SELECT * FROM `users` WHERE id = '.$clan['owner']) or die();
							$data = mysql_fetch_assoc($q) or die();
							telepost($data['login'],'<font color=red>Внимание!</font> Ваша заявка на регистрацию клана была утверждена.');

							$prefix = "oldbk";
							if ($data['id_city'] == 1) {
								$prefix = "avalon";
							}

							// делаем главу
							$status = '<font color=#008080><b>Глава клана</b></font>';
							mysql_query('UPDATE '.$prefix.'.`users` SET
								`klan` = "'.mysql_escape_string($clan['abr']).'",
								`align` = "'.$clan['align'].'",
								`status` = "'.mysql_escape_string($status).'"
								WHERE `id` = '.$clan['owner']
							) or die();

							mysql_query('DELETE FROM `clans_reg` WHERE `id` = '.$id) or die();

							// отсылаем картинки на клауд
		 					CloudPut($regimagepath.$clan['sznak'],'oldbkstatic','i/klan/');
		 					CloudSetACL('oldbkstatic','i/klan/',$clan['sznak'],"public");

		 					CloudPut($regimagepath.$clan['bznak'],'oldbkstatic','i/klan/');
		 					CloudSetACL('oldbkstatic','i/klan/',$clan['bznak'],"public");
						}
					}
				}
			}
			Redirect("klanedit.php");
		}
		if (isset($_POST["regclan"]) && $status == 'canreg') {
			// заявка на регистрацию

			// проверяем склонку и эффект
			if (!isset($_POST['klanalign']) || !isset($regprice[$_POST["klanalign"]])) die();
			reset($effects);
			while(list($k,$v) = each($effects)) {
				if ($v["type"] == $eff_align_type) {
					if ($v["add_info"] != $_POST["klanalign"]) {
						$error[] = 'У вас штраф склонности, вы можете зарегестировать только '.$regname[$v['add_info']].' клан.';
						break;
					}
				}
			}

			// проверяем деньги
			if ($user["money"] < $regprice[$_POST["klanalign"]]) {
				$error[] = "У вас недостаточно денег для регистрации клана.";
			}
			
		
			
			// проверяем английскую аббревиатуру и название клана
			if (!isset($_POST['klanname']) || !isset($_POST['klanabbr'])) die();
			if (!preg_match('~^[a-z]{1,25}$~iU',$_POST['klanabbr'])) {
				$error[] = 'Английская аббревиатура должна содержать только английские буквы без пробела и не более 25 символов.';
			}
			if (!preg_match('~^[a-z\-_ ]{1,60}$~iU',$_POST['klanname']) && !preg_match('~^[А-Яа-я\-_ ]{1,60}$~iU',$_POST['klanname'])) {
				$error[] = 'Название клана может содержать только русские ИЛИ анлийские буквы, знак минуса, подчёркивание и пробел.';
			}
			

			// ссылка, описание
			if (!isset($_POST['klandescr'])) die();
			
			if(!isset($_POST["http"]))
			{
				$_POST["http"]=' ';
			}
			else
			if(isset($_POST["http"]))
			{
					
			}
			


			$exist = mysql_query("SELECT * FROM oldbk.clans WHERE short='".$_POST['klanabbr']."'");
			if(mysql_num_rows($exist) >0) {
				$error[] = 'Клан с такой аббривиатурой уже существует.';	
			}

			$_POST['base_klan'] = 0;
			// проверка на рекрутство
			/*
			if (isset($_POST['base_klan'])) {
				$_POST['base_klan'] = intval($_POST['base_klan']);
				if ($_POST['base_klan'] != 0) {
					// регестрируется рекрут клан, проверяем совпадение склонок
					$q = mysql_query('SELECT * FROM clans WHERE id = '.$_POST["base_klan"]) or die();
					$base = mysql_fetch_assoc($q);
					if ($base !== FALSE) {
						if ($base["align"] != $_POST['klanalign']) {
							$error[] = "У выбранного основного клана не совпадает склонность с вашим.";
						}
						if ($base["rekrut_klan"] != 0) {
							$error[] = "У выбранного основного клана уже есть рекрут.";
						}
					} else {
						// базовый клан ненайден
						$_POST['base_klan'] = 0;
					}
				}
			}
			*/

			// картинки
			if (!isset($_FILES['small']) || !isset($_FILES['big'])) die();
			if ($_FILES['small']['error'] == UPLOAD_ERR_OK && $_FILES['big']['error'] == UPLOAD_ERR_OK) {
				$imageinfo1 = @getimagesize($_FILES['small']['tmp_name']);
				$imageinfo2 = @getimagesize($_FILES['big']['tmp_name']);
				if ($imageinfo1 !== FALSE && $imageinfo2 !== FALSE) {
					if($imageinfo1[2] != IMAGETYPE_GIF) {
						$error[] = 'Маленький значок не является gif файлом.';
					}
					if($imageinfo2[2] != IMAGETYPE_GIF) {
						$error[] = 'Большой значок не является gif файлом.';
					}

					if ($imageinfo1[0] != 24 || $imageinfo1[1] != 15) {
						$error[] = 'Маленький значок должен быть размером 24х15.';
					}

					if ($imageinfo2[0] != 100 || $imageinfo2[1] != 99) {
						$error[] = 'Большой значок должен быть размером 100х99.';
					}

					if($_FILES['small']['size'] > 1024*4) {
						$error[] = 'Файл с маленьким значком слишком большой (более 4кб).';
					}
					if($_FILES['big']['size'] > 1024*10) {
						$error[] = 'Файл с большим значком слишком большой (более 10кб).';
					}
				} else {
					$error[] = "Ошибка загрузки значков для клана.";
				}
			} else {
				$error[] = "Ошибка загрузки значков для клана.";
			}

			if (!count($error)) {
				// ошибок нет - подаём на премодерацию

				$rec = array();
	    			$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']-$regprice[$_POST["klanalign"]];
				$rec['target']=0;
				$rec['target_login']="регистратура";
				$rec['type']=233; // регистрация клана
				$rec['sum_kr']=$regprice[$_POST["klanalign"]];
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				add_to_new_delo($rec); //юзеру

				// снимаем бабки за регу
				mysql_query('UPDATE `users` set money = money - '.$regprice[$_POST["klanalign"]].' WHERE id = '.$user['id']) or die();

				$image1 = $_POST['klanabbr'].".gif";
				$image2 = $_POST['klanabbr']."_big.gif";

				// доблавяем в таблицу премодерации
				$q = mysql_query('
					INSERT `clans_reg` (`name`,`owner`,`abr`,`http`,`sznak`,`bznak`,`align`,`descr`,`base_klan`)
					VALUES
					(
						"'.$_POST['klanname'].'",
						"'.$user['id'].'",
						"'.$_POST['klanabbr'].'",
						"'.$_POST['http'].'",
						"'.$image1.'",
						"'.$image2.'",
						"'.$_POST['klanalign'].'",
						"'.mysql_escape_string($_POST['klandescr']).'",
						"'.$_POST['base_klan'].'"
					)
				') or die();

				//Пишим тринити
				telepost("Тринити",'<font color=red>Внимание!</font> Новая заявка в Регистратуре кланов.');

				// копируем картинки
				move_uploaded_file($_FILES['small']['tmp_name'], $regimagepath.$image1);
				move_uploaded_file($_FILES['big']['tmp_name'], $regimagepath.$image2);

				Redirect("klanedit.php");
			}
		}
		if (isset($_POST["updateinfo"]) && ($status == 'canberecruit' || $status == 'recruitclan' || $status == 'basehaverecruit' || ($status == 'isadmin' && count($klan)))) {
			// обновление инфу на клан

			// проверяем деньги
			if ($user["money"] < $updateprice) {
				$error[] = "У вас недостаточно денег для обновления информации о клане.";
			}

			// ссылка, описание
			if (!isset($_POST["http"]) || !isset($_POST['klandescr'])) die();

			if (!count($error)) {
				// ошибок нет - подаём на премодерацию

				// пишем в дело
				$rec = array();
	    			$rec['owner']=$user[id]; 
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$rec['owner_balans_posle']=$user['money']-$updateprice;
				$rec['target']=0;
				$rec['target_login']="регистратура";
				$rec['type']=234; // обновление информации о клане
				$rec['sum_kr']=$updateprice;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				add_to_new_delo($rec); //юзеру


				// снимаем бабки за регу
				mysql_query('UPDATE `users` set money = money - '.$updateprice.' WHERE id = '.$user['id']) or die();

				// доблавяем в таблицу премодерации
				$q = mysql_query('
					INSERT `clans_reg` (`name`,`owner`,`abr`,`http`,`sznak`,`bznak`,`align`,`descr`,`base_klan`)
					VALUES
					(
						"'.$klan['name'].'",
						"'.$user['id'].'",
						"'.$klan['short'].'",
						"'.$_POST['http'].'",
						"",
						"",
						"'.$klan['align'].'",
						"'.mysql_escape_string($_POST['klandescr']).'",
						"'.$klan['base_klan'].'"
					)
				') or die();
				telepost("Мастер",'<font color=red>Внимание!</font> Новая заявка в Регистратуре кланов.');
				telepost("Архитектор",'<font color=red>Внимание!</font> Новая заявка в Регистратуре кланов.');
				Redirect("klanedit.php");
			}
		}

		/*
		if (isset($_POST["makerecruit"]) && $status == 'canberecruit') {
			// клан хочет стать рекрутом
			if (!isset($_POST['base_klan'])) die();
			$id = intval($_POST['base_klan']);
			if ($id == 0) Redirect("klan_edit.php");

			$q = mysql_query('SELECT * FROM clans WHERE id = '.$id) or die();
			$clan = mysql_fetch_assoc($q) or die();

			if ($clan['base_klan'] == 0 && $clan['rekrut_klan'] == 0 && $klan['align'] == $clan['align']) {
				// доблавяем в таблицу премодерации
				if($user[money] >= $updateprice) {
					$q = mysql_query('
						INSERT `clans_reg` (`name`,`owner`,`abr`,`http`,`sznak`,`bznak`,`align`,`descr`,`base_klan`)
						VALUES
						(
							"'.$klan['name'].'",
							"'.$user['id'].'",
							"'.$klan['short'].'",
							"",
							"",
							"",
							"'.$klan['align'].'",
							"",
							"'.$id.'"
						)
					') or die();
					telepost("Тринити",'<font color=red>Внимание!</font> Новая заявка в Регистратуре кланов.');
					// снимаем бабки за регу
					mysql_query('UPDATE `users` set money = money - '.$updateprice.' WHERE id = '.$user['id']) or die();

					// пишем в дело
					if (olddelo == 1) {
					mysql_query('
						INSERT INTO `delo` (`author` ,`pers`, `text`,`text_ext`, `type`, `date`)
						VALUES
						(
							0,
							"'.$user['id'].'",
							"Заплатил '.$updateprice.' кр. за подачу заявки на рекрутство.",
							"",
							0,
							"'.time().'"
						)
					') or die();
					}

					$rec = array();
		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money']-$updateprice;
					$rec['target']=0;
					$rec['target_login']="регистратура";
					$rec['type']=235; // заявка на рекрутство
					$rec['sum_kr']=$updateprice;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					add_to_new_delo($rec); //юзеру
     				} else {
     					$status = 'nomoney';
     				}
			}
			Redirect("klanedit.php");
		}
		*/
	}

	$text = "";
	$isform = false;
	switch($status) {
		case "lowlevel":
			$text = "Вход в регистратуру кланов только с 4го уровня";
		break;
		case "nomoney":
			$text = "У вас недостаточно денег";
		break;
		case "lonegunmen":
			$text = "Вы уже имеете личную склонность";
		break;
		case "waitupdate":
			$text = "Вы подали заявку на изменение вашего клана. Ожидайте решения.";
		break;
		case "waitapprove":
			$text = "Вы подали заявку на регистрацию клана. Ожидайте решения.";
		break;
		case "waitrecruit":
			$text = "Ваш клан готовится стать основным для рекрута. Ожидайте решения по рекруту.";
		break;
		/*
		case "canberecruit":
			$text = ShowRecruitForm($user,$recruitform,$updateprice);
			$isform = true;
		break;
		*/
		case "recruitclan":
			$text = "Вы уже состоите в клане и ваш клан является рекрутом.";
		break;
		case "basehaverecruit":
			$text = "Вы уже состоите в клане и являетесь основой для рекрут-клана.";
		break;
		case "inclan":
			$text = "Вы уже состоите в клане.";
		break;
		case "nochecked":
			$text = "Для регистрации клана вам необходимо пройти проверку у паладинов.";
		break;
		case "canreg":
			$baseform = str_replace('%CLANINFO%',$claninfo,$baseform);
			$text = ShowBaseForm($user,$baseform,$error,$regprice);
			$isform = true;
		break;
		case "isadmin";
			$text = ShowApproveForm();
			$isform = true;
		break;
	}

	echo $head;
	if ($isform) {
		echo $text;
	} else {
		echo '<center><b>'.$text.'</b></center>';
	}

	if ($status == 'canberecruit' || $status == 'recruitclan' || $status == 'basehaverecruit' || ($status == 'isadmin' && count($klan))) echo ShowUpdateInfoForm($user,$klan,$updateinfoform,$updateprice);
	if ($status == 'nochecked') {
		reset($regprice);
		while(list($k,$v) = each($regprice)) {
			$claninfo = str_replace('%ALIGN'.$k.'%',$v,$claninfo);
		}
		echo $claninfo;
	}

	if(isset($_SESSION['vk']) && is_array($_SESSION["vk"])) {
		echo str_replace("%MAILRU%","",$foot);
	} else {
		echo str_replace("%MAILRU%",$mailrucounter,$foot);
	}
?>