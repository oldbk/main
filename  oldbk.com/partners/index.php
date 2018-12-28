<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{$ip=$_SERVER['HTTP_CLIENT_IP'];}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{$ip.='|'.$_SERVER['HTTP_X_FORWARDED_FOR'];}
else
	{$ip.='|'.$_SERVER['REMOTE_ADDR'];}
session_set_cookie_params(25920000);
session_start();
include "/www/oldbk.com/connect.php";
require_once('../mailer/send-email2.php');




if (isset($_GET['logout']))	
	{
	 $_SESSION['partnerid']=0;
	 setcookie("amiloggedin");
	 setcookie("farsh");
	}

function send_mail($to,$tem,$text)
{
//$headers= "MIME-Version: 1.0\r\n";
//$headers .= "Content-type: text/html; charset=windows-1251\r\n";
//$headers .= "From: oldbk.com <admin@oldbk.com>\r\n";
//$headers .= "Reply-To: admin@oldbk.com\r\n";
//$headers .= "X-Mailer: PHP\r\n";
//mail($to, $tem, $text, $headers);
mailnew($to,$tem,$text, true);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

<title>ОлдБК - Партнерская программа</title>
<meta name="keywords" content="бойцовский клуб, бк, онлайн игра, rpg, магия бой, игра фэнтези, fantasy, маг " />
<meta name="description" content="Бойцовский клуб - rpg онлайн игра, он же БК, созданный в 2003 году. Борьба Тьмы и Света. Бои, магия, персонажи - всё это Бойцовский клуб ">
<meta name="robots" content="index, follow"/>
<meta name="author" content="oldbk.com">
<link rel="apple-touch-icon-precomposed" sizes="512x512" href="https://i.oldbk.com/i/icon/oldbk_512x512.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://i.oldbk.com/i/icon/oldbk_144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://i.oldbk.com/i/icon/oldbk_114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://i.oldbk.com/i/icon/oldbk_72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="58x58" href="https://i.oldbk.com/i/icon/oldbk_58x58.png" />
<link rel="apple-touch-icon-precomposed" sizes="48x48" href="https://i.oldbk.com/i/icon/oldbk_48x48.png" />
<link rel="apple-touch-icon-precomposed" sizes="29x29" href="https://i.oldbk.com/i/icon/oldbk_29x29.png" />
<link rel="apple-touch-icon-precomposed" href="https://i.oldbk.com/i/icon/oldbk_57x57.png" />
<link rel="stylesheet" href="styles.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="https://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="https://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="https://i.oldbk.com/i/jscal/css/gold/gold.css" />
<script type="text/javascript" src="https://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="https://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
</head>

<body leftmargin=0 rightmargin=0 bottommargin=0 topmargin=0 marginwidth=0 marginheight=0>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="1065" valign="top" class="leftY"><table width="100%" height="270" border="0" cellpadding="0" cellspacing="0" class="headLeft">
<tr>
<td height="270">&nbsp;</td>
</tr>
</table></td>
    <td width="1018" valign="top" background="i/main_bg.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td height="263" class="header">&nbsp;</td>
</tr>
<tr>
<td height="700" valign="top" class="cont_cracks" align=center>

<?php

if (isset($_REQUEST['login']) and $_REQUEST['psw']!='')
	 	{
		 AuthorizeToPartners($_REQUEST['login'],$_REQUEST['psw'],$_REQUEST['remember']);
			if (stripos($_REQUEST['login'],"Архитектор") !== FALSE) {
				header("Location: indexold-26-12-13.php");
				die();	
			}
		}


function Show_Auth_form()
{
	 echo "<br>";
	 $o='<center><b>Авторизируйтесь используя свой логин и пароль партнера.</b></center><br>
	 <form method="post" action="index.php">
	 
			 <table border=0 align="center" style="margin-left: auto; margin-right: auto;">
			 <tr>
			 <td><input type="text" name="login" class="enter1" value="Логин" onfocus="javascript:this.value=\'\';" /> <input type="password" name="psw" class="enter1" value="Пароль" onfocus="javascript:this.value=\'\';" /></td>
			 <td><input type="submit" id="enter" value="ВОЙТИ"  class="button2" />  </td></tr>
		    	 <tr><td>&nbsp</td>
		    	 	<td><input type=checkbox name=remember />Запомнить</td></tr>
			 </table>
			 </form><a href=?reg class="button2">РЕГИСТРАЦИЯ</a>';
	 render_text_block($o);
	 
}

function render_text_block($text)
{
?>
<table  width="950" border="0" cellspacing="0" cellpadding="0" class="menu">
<tr>
<td class="menu_headL" width=25 height=17 >&nbsp;</td>
<td class="menu_head" height=17>&nbsp;</td>
<td class="menu_headR" width=25 height=17>&nbsp;</td>
</tr>
<tr>
<td class="menu_cbgL" >&nbsp;</td>
<td class="menu_cbg"><?=$text;?>
</td>
<td class="menu_cbgR" >&nbsp;</td>
</tr>
<tr>
<td class="menu_footL" height=26 >&nbsp;</td>
<td class="menu_foot" height=26 >&nbsp;<br></td>
<td class="menu_footR" height=26 >&nbsp;</td>
</tr>
</table>
<?
}



if (isset($_COOKIE['amiloggedin']) and isset($_COOKIE['farsh']))
 	{
	 $data = mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = '{$_COOKIE['amiloggedin']}' AND `password`!='' AND  `password` = '".$_COOKIE['farsh']."' LIMIT 1;"));
	 if ($data['id']!='')	{$_SESSION['partnerid']=$data['id'];}
	}



if (!($_SESSION['partnerid'] >0))
	{
	 if (isset($_POST['registration'])) {RegisterPartner($_POST);}

	if (!isset($_GET['reg']))
	 {
	 
	 //блок авторизации
	Show_Auth_form();
	 ?>
	

	<?
	  ShowInfo();
	  }
	  else
	  {
	  ShowRegForm();
	  }
	 ShowFooter();
	 exit();
	}

//if (isset($_GET['showreg']))	{ShowRegistrations();}


echo '<br><center>
<table width="900" height="52"  style="background: url(i/hmenu_bg.jpg) center no-repeat;"><tr><td align="center" valign="top" >
    <table><tr>
	<td>&nbsp;

<a href=? class="hmenu">Сводная информация</a>
<a href=?showregs=1 class="hmenu">Регистрации</a>
<a href=?payments=1 class="hmenu">Платежи</a>
<a href=?showsites=1 class="hmenu">Сайты</a>
<a href=?stat=1 id="isstat" class="hmenu">Статистика</a>
<a href=?banners=1 class="hmenu">Баннеры</a>
<a href=?logout=1 class="hmenu">Выйти</a>
</div>
&nbsp;</td>
</tr></table>
</tr></table>
<br></center>';

$partner = mysql_fetch_array(mysql_query("SELECT id, status FROM `partners` WHERE `id` = '{$_SESSION['partnerid']}' LIMIT 1;"));
if (!($partner['status'] >0) and !($_SESSION['partnerid']==10 or $_SESSION['partnerid']==11 or $_SESSION['partnerid']==12 or $_SESSION['partnerid']==1))
	{echo "<font color=red>Ваша заявка на регистрацию ожидает подтверждения администрации!</font>"; ShowFooter(); exit;}

if ($_SESSION['partnerid']==10 or $_SESSION['partnerid']==11 or $_SESSION['partnerid']==12 or $_SESSION['partnerid']==1)
	{
	 echo "<h1>Партнерская программа: Админка</h1><script>document.getElementById('isstat').innerHTML='Выплаты';</script>";

	 if (isset($_REQUEST['delclaim']))
	 	{

		$pwhois=mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = {$_REQUEST['unapprove']};"));
		
$message = '
<html>
<head>
 <title>Партнерская программа oldbk.com</title>
</head>
<body>
<p>Ваша учетная запись в  <a href=https://oldbk.com/partners/index.php>партнерской программе oldbk.com</a>, была <b>удалена</b>.</p>
<p><b>Причина:</b> '.$_POST[txtblock].'</p>
</body>
</html>
';
send_mail($pwhois['email'], 'Партнерская программа oldbk.com', $message);		 	
	 	
	 	
	 	
	 	
	 	mysql_query("DELETE FROM `partners` WHERE `id` = {$_REQUEST['delclaim']};");
	 	
	 	
	 	}
	 if (isset($_REQUEST['approve']))
	 	{
		$pwhois=mysql_fetch_array(mysql_query("select  * FROM `partners` WHERE `id` = {$_REQUEST['approve']};"));
$message = '
<html>
<head>
 <title>Партнерская программа oldbk.com</title>
</head>
<body>
<p>Ваша заявка в <a href=https://oldbk.com/partners/index.php>партнерской программе oldbk.com</a>, была одобрена администрацией!</p>
</body>
</html>
';
send_mail($pwhois['email'], 'Партнерская программа oldbk.com', $message);	
		
		
	 	mysql_query("UPDATE `partners` SET `status`='1', `percent`=40 WHERE `id` = {$_REQUEST['approve']};");
	 	
	 	}
	 if (isset($_REQUEST['unapprove']))
	 	{
	 	//echo "БЛОКККК";
		$pwhois=mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = {$_REQUEST['unapprove']};"));
		
$message = '
<html>
<head>
 <title>Партнерская программа oldbk.com</title>
</head>
<body>
<p>Ваша учетная запись в  <a href=https://oldbk.com/partners/index.php>партнерской программе oldbk.com</a>, была заблокирована.</p>
<p><b>Причина блокировки:</b> '.$_POST[txtblock].'</p>
</body>
</html>
';
send_mail($pwhois['email'], 'Партнерская программа oldbk.com', $message);		 	
	 	
	 	
	 	mysql_query("UPDATE `partners` SET `status`='0', `percent`=0 WHERE `id` = {$_REQUEST['unapprove']};");
	 	
	 	}
	 if (isset($_REQUEST['pid']) and isset($_REQUEST['getted']))
	 	{
		 mysql_query("UPDATE `partners` SET `getted`=`getted`+'{$_REQUEST['getted']}', `money`=`money`-'{$_REQUEST['getted']}' WHERE `id` = {$_REQUEST['pid']};");
		 mysql_query("INSERT INTO `partners_delo` (`dealer_id`,`bank`,`ekr`, `partner_id`,`owner_id`,`transfer_time`) VALUES ('{$_SESSION['partnerid']}', '999999999', '{$_REQUEST['getted']}', '{$_REQUEST['pid']}', '{$_REQUEST['pid']}', '".time()."');");
		$_REQUEST['Админка']=$_REQUEST['pid'];
		}
	 if (isset($_REQUEST['banners']))
	 	{ShowBanners();}
	 elseif (isset($_REQUEST['showregs']))
	 	 {AdminRegs($_REQUEST['fraud'],$_REQUEST['unfraud'],$_REQUEST['f_date'],$_REQUEST['t_date'],$_REQUEST['sort'],$_REQUEST['pr']);}
	 elseif (isset($_REQUEST['payments']))
	 	 {AdminTransfers($_REQUEST['nick'],$_REQUEST['f_date'],$_REQUEST['t_date']);}
	 elseif (isset($_REQUEST['showsites']))
	 	 {AdminSites($_REQUEST['f_date'],$_REQUEST['t_date'],$_REQUEST['sort']);}
	 elseif (isset($_REQUEST['stat']))
	 	 {AdminPayments($_REQUEST['f_date'],$_REQUEST['t_date']);}
	 else
	 	 {AdminOwerview($_REQUEST['showpartner']);}
	 ShowFooter();
	 exit();
	}

if (isset($_REQUEST['showregs']))
	{
	 echo "<h1>Партнерская программа: Регистрации</h1>";
	 ShowRegistrations($_REQUEST['f_date'], $_REQUEST['t_date']);
	 ShowFooter();
	 exit();
	}
if (isset($_REQUEST['showsites']))
	{
	 echo "<h1>Партнерская программа: Сайты</h1>";
	 ShowSites($_REQUEST['f_date'], $_REQUEST['t_date']);
	 ShowFooter();
	 exit();
	}	
if (isset($_REQUEST['payments']))
	{
	 echo "<h1>Партнерская программа: Платежи</h1>";
	 ShowPayment($_REQUEST['f_date'], $_REQUEST['t_date'], $_REQUEST['mode'], $_REQUEST['what']);
	 ShowFooter();
	 exit();
	}
if (isset($_REQUEST['banners']))
	{
	 echo "<h1>Партнерская программа: Баннеры</h1>";
	 ShowBanners();
	 ShowFooter();
	 exit();
	}
if (isset($_REQUEST['stat']))
	{
	 echo "<h1>Партнерская программа: Статистика</h1>";
	 ShowStat();
	 ShowFooter();
	 exit();
	}
ShowAllInfo();
//echo "</td><td valign=top width=240>";

//echo "</td><td valign=top width=180>";
//ShowPayment("month");
//echo "</td><td valign=top width=180>";
//ShowPayment("day");
//echo "</td></tr></table>";
ShowFooter();

function AuthorizeToPartners($login,$pass,$remember)	{

	$ff=in_smdp($pass);
	$data = mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `login` = '{$login}' AND `password`!='' AND  `password` = '".$ff."' LIMIT 1;"));

	if($data['login'] == '') 
		{
		echo "<br><p class='menu'><font color=red><b>Произошла ошибка! Неверный пароль!</b></font></p>";
		Show_Auth_form();
		ShowInfo();		
		ShowFooter();
		 exit();
		}
	if ($remember!='')
		{
		 setcookie("amiloggedin", $data['id'], time() + 25920000, "/", "", false);
		 setcookie("farsh", $ff, time() + 25920000, "/", "", false);
		}

	$_SESSION['partnerid'] = $data['id'];
	$_SESSION['partnerlogin'] = $data['login'];
	
}

function ShowInfo()	{
?>
<br>
<table  width="900" border="0" cellspacing="0" cellpadding="0" class="menu">
<tr>
<td>
<b>Условия партнерской программы CPS (cost per sale)</b><br><br>
Вы размещаете у себя на сайте баннер или ссылку на игру "Бойцовский клуб - ОлдБК". 
Система фиксирует регистрации в игре пользователей, пришедших с Вашего сайта, произведенные ими оплаты игровых услуг. <br>
Вам перечисляется от 40% суммы платежей, выплаты осуществляются раз в неделю. 
Вы сможете просматривать информацию об оплатах, произведенных пользователями, пришедшими с Вашего сайта. <br>
Так же, мы следим за эффективностью рекламы через партнеров. 
Если эффективность будет высокой - мы индивидуально для Вас сможем повысить процент от платежей.<br><br>
</td>
</tr>
</table>
<?
$o='<b>Как стать участником</b><br><br>
1. Заполнить Заявку на участие в партнерской программе.<br>
2. Вы сразу получаете доступ к Вашему аккаунту, однако администрация оставляет за собой право при несоответствии сайта правилам партнерской программы отказать партнеру в продолжении партнерских отношений.<br>
3. Выбрать баннер(а) и разместить их у себя на сайте.<br><br>
<font color=red><b>Внимание!!!</b></font><br>
В Партнерской Программе могут принять участие web-сайты, отвечающие следующим общепринятым требованиям:<br>
- содержание должно соответствовать нормам Российского и международного законодательства.<br>
- не должен содержать оскорбления общественной морали (порнография, непристойности), национальных и религиозных чувств.<br>
Не принимаются к участию в Партнерской программе клановые сайты oldbk.com.<br>
По вопросам работы партнерской программы пишите на 
<a href = "&#109;&#097;&#105;&#108;&#116;&#111;:admin&#064;oldbk.com"><span style="unicode-bidi:bidi-override; direction: rtl;">moc.kbdlo@nimda</span></a>. <br>';
render_text_block($o);
}

function RegisterPartner($reg)	{
	$login=$reg['name'];
	$busy = mysql_fetch_array(mysql_query("SELECT count(*) FROM `partners` WHERE `login` = '{$login}';"));
	if ($busy[0]!=0)
		{echo "Извините, но логин $login уже занят!<br><a href='javascript:window.history.go(-1);'>Назад</a>"; exit();}
	if ($reg['pass']!=$reg['pass2'])
		{echo "Пароль и подтверждение пароля не совпадают!<br><a href='javascript:window.history.go(-1);'>Назад</a>"; exit();}
	if ($reg['email']=='' or $reg['fio']=='' or $reg['phone']=='' or $reg['webmoney']=='')
		{echo "Вы не заполнили одно из обязательных полей!<br><a href='javascript:window.history.go(-1);'>Назад</a>"; exit();}
	if (strlen($login)<4 || strlen($login)>20 || !preg_match("~^[a-zA-Zа-яА-Я0-9-][a-zA-Zа-яА-Я0-9_ -]+[a-zA-Zа-яА-Я0-9-]$~",$login) || preg_match("/__/",$login) || preg_match("/--/",$login) || preg_match("/  /",$login) || preg_match("/(.)\\1\\1\\1/",$login))
		{ echo "Логин может содержать от 4 до 20 символов, и состоять только из букв русского или английского алфавита, цифр, символов '_',  '-' и пробела. <br>Логин не может начинаться или заканчиваться пробелом или символом '_'.<br>Также в логине не должно присутствовать подряд более 1 символа '_' или '-' и более 1 пробела, а также более 3-х других одинаковых символов.!";exit();}
	if ($reg['contract']=='')
		{echo "Вы не согласились с условиями контракта!<br><a href='javascript:window.history.go(-1);'>Назад</a>"; exit();}
	$pass=$reg['pass'];
//	include "/www/capitalcity.oldbk.com/alg.php";
//include "../alg.php";
	$ff=in_smdp($pass);
	mysql_query("INSERT INTO `partners` 
				(`login`, `password`, `email`, `fio`, `phone`, `wm`, `site_name`, `site_link`, `site_desc`, `contract_type`, `show_b1`, `show_b2`, `show_b3`, `show_b4`, `show_b5`, `show_b6`, `show_b7`, `show_b8`, `show_b9`, `show_b10`, `show_b11`, `show_b12`, `show_b13`, `show_b14`, `click_b1`, `click_b2`, `click_b3`, `click_b4`, `click_b5`, `click_b6`, `click_b7`, `click_b8`, `click_b9`, `click_b10`, `click_b11`, `click_b12`, `click_b13`, `click_b14`,`status`,`all_ekr`,`money`,`getted`) 
	VALUES 		
				('".mysql_real_escape_string($login)."', '{$ff}', '".mysql_real_escape_string($reg[email])."', '".mysql_real_escape_string($reg[fio])."', '".mysql_real_escape_string($reg[phone])."', '".mysql_real_escape_string($reg[webmoney])."', '".mysql_real_escape_string($reg[site_name])."', '".mysql_real_escape_string($reg[site_url])."', '".mysql_real_escape_string($reg[site_desc])."', '".mysql_real_escape_string($reg[contract_type])."', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0','0','0','0','0','0','0','0','0');");
	echo "<font color=red>Вы успешно подали заявку! В скором времени она будет рассмотрена администрацией.</font>";
		
$message = '
<html>
<head>
 <title>Партнерская программа oldbk.com</title>
</head>
<body>
<p>Вы успешно подали заявку в <a href=https://oldbk.com/partners/index.php>партнерской программе oldbk.com</a>! В скором времени она будет рассмотрена администрацией.</p>
<p>После одобрения вашей заявки вы получите соответствующее сообщение на ваш e-mail.</p>
</body>
</html>
';
send_mail($reg['email'], 'Партнерская программа oldbk.com', $message);	
	

}

function ShowRegistrations($f_date,$t_date)	{
global $_REQUEST;
if ($f_date=='' or $t_date=='') // Выводим последние рег-ии если не указан диапазон.
	{
 	 $data=mysql_query("SELECT * FROM `partners_users` WHERE `partner` = '".$_SESSION['partnerid']."' ORDER BY `reg_time` DESC;");
	 $all_reg=mysql_num_rows($data);
	 $o= "<b>Последние регистрации:</b><br>Всего регистраций: $all_reg
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Дата&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Баннер&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Сайт&nbsp;</td></tr>";
	 while ($row=mysql_fetch_array($data))
		{	 
		 if ($i>20) // Количество выводимых последний рег-ий.
		 	{break;}
		 $bname='';
		  switch($row['banner']) {
			case "1": $bname="Легендарный (468х60)"; break;
			case "2": $bname="Тяжесть карающего топора (468х60)"; break;
			case "3": $bname="На работе и дома (468х60)"; break;
			case "4": $bname="Приключения ждут (468х60)"; break;
			case "5": $bname="Лучшая игра (468х60)"; break;
			case "6": $bname="Ты нам нужен (728х90)"; break;
			case "7": $bname="За кружкой эля (728х90)"; break;
			case "8": $bname="Тяжесть карающего топора (728х90)"; break;
			case "9": $bname="На работе и дома (728х90)"; break;
			case "10": $bname="Приключения ждут (728х90)"; break;
			case "11": $bname="Легендарный (120х300)"; break;
			case "12": $bname="Лучшая игра (120х300)"; break;
			case "13": $bname="Полоска 1 (350х19)"; break;
			case "14": $bname="Полоска 2 (350х19)"; break;

	case "15": $bname="oldbk_240_400_03.swf"; break;
	case "17": $bname="oldbk_240_400_01.jpg"; break;

	case "18": $bname="oldbk_240_400_01.swf"; break;
	case "19": $bname="oldbk_240_400_02.gif"; break;
	
	
	case "20": $bname="oldbk_240_400_03.gif"; break;
	case "21": $bname="oldbk_240_400_04.swf"; break;
	
	
		
	case "22": $bname="oldbk_240_400_05.gif"; break;	
	case "16": $bname="oldbk_120_240_01.gif"; break;
	

	case "23": $bname="oldbk_728_90_02.gif"; break;	
	case "24": $bname="oldbk_728_90_02.swf"; break;
	
	case "26": $bname="oldbk_120_240_02.gif"; break;
	case "28": $bname="oldbk_240_400_04.gif"; break;
	case "29": $bname="oldbk_728_90_03.gif"; break;		
	case "30": $bname="oldbk_468_60_01.gif"; break;		
	case "32": $bname="oldbk_468_60_02.gif"; break;					
	case "33": $bname="oldbk_468_60_03.gif"; break;


	case "25": $bname="oldbk_120_240_01.swf"; break;
	case "27": $bname="oldbk_240_400_02.swf"; break;
	case "31": $bname="oldbk_468_60_01.swf"; break;					
	
	case "34": $bname="300x250_04.swf"; break;
	
	case "35": $bname="600x90.swf"; break;
	case "36": $bname="728x90.swf"; break;
	case "37": $bname="728x90_n2.swf"; break;
	case "38": $bname="warrior_200x300.swf"; break;
	case "39": $bname="warrior_240x400.swf"; break;
	
	case "40": $bname="oldbk_468x60_fp8_fps60.swf"; break;
	case "41": $bname="oldbk_728x90_fp8_fps60_2.swf"; break;
	case "42": $bname="oldbk_240x400_fp8_fps60_2.swf"; break;

	case "43": $bname="240x400.swf"; break;


			
			}
		 
		 $i++;
		 $dat=date("d.m.y H:i",$row['reg_time']);
 		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;$dat&nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;{$row['banner']}. ".$bname." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=".$row['from_site']." target=_blank style='padding-left: 0px;'>".$row['from_site']."</a> </td></tr>";
		}
	 $o.= "</table>";
	}
else
	{
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$f_date,$mt);
	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$t_date,$mt);
	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3])+86399;
	 
////
$quant=mysql_fetch_array(mysql_query("SELECT count(*) FROM `partners_users` WHERE `partner` = '".$_SESSION['partnerid']."' AND `reg_time` > $start AND `reg_time` < $end ORDER BY `reg_time` DESC;"));

$all_reg=$quant[0];
if (is_int(intval($_REQUEST['page'])))
	{
	 $page_num=intval($_REQUEST['page']);
	 if ($page_num==0) {$page_num=1;}
	}
else	{$page_num=1;}
$start_id=($page_num-1)*50; // Кол-во на странице
////	 
	 
  	 $data=mysql_query("SELECT * FROM `partners_users` WHERE `partner` = '".$_SESSION['partnerid']."' AND `reg_time` > $start AND `reg_time` < $end ORDER BY `reg_time` DESC LIMIT {$start_id}, 50;");

	 $o.= "<b>Регистрации ($f_date - $t_date):</b><br>Всего регистраций: $all_reg<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>";
 	 $o.= "<tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Дата&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Баннер&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Сайт&nbsp;</td></tr>";
	 while ($row=mysql_fetch_array($data))
		{	 
		 $bname='';
		 switch($row['banner']) {
			case "1": $bname="Легендарный (468х60)"; break;
			case "2": $bname="Тяжесть карающего топора (468х60)"; break;
			case "3": $bname="На работе и дома (468х60)"; break;
			case "4": $bname="Приключения ждут (468х60)"; break;
			case "5": $bname="Лучшая игра (468х60)"; break;
			case "6": $bname="Ты нам нужен (728х90)"; break;
			case "7": $bname="За кружкой эля (728х90)"; break;
			case "8": $bname="Тяжесть карающего топора (728х90)"; break;
			case "9": $bname="На работе и дома (728х90)"; break;
			case "10": $bname="Приключения ждут (728х90)"; break;
			case "11": $bname="Легендарный (120х300)"; break;
			case "12": $bname="Лучшая игра (120х300)"; break;
			case "13": $bname="Полоска 1 (350х19)"; break;
			case "14": $bname="Полоска 2 (350х19)"; break;
			
	case "15": $bname="oldbk_240_400_03.swf"; break;
	case "17": $bname="oldbk_240_400_01.jpg"; break;

	case "18": $bname="oldbk_240_400_01.swf"; break;
	case "19": $bname="oldbk_240_400_02.gif"; break;
	
	
	case "20": $bname="oldbk_240_400_03.gif"; break;
	case "21": $bname="oldbk_240_400_04.swf"; break;
	
	
		
	case "22": $bname="oldbk_240_400_05.gif"; break;	
	case "16": $bname="oldbk_120_240_01.gif"; break;
	

	case "23": $bname="oldbk_728_90_02.gif"; break;	
	case "24": $bname="oldbk_728_90_02.swf"; break;
	
	case "26": $bname="oldbk_120_240_02.gif"; break;
	case "28": $bname="oldbk_240_400_04.gif"; break;
	case "29": $bname="oldbk_728_90_03.gif"; break;		
	case "30": $bname="oldbk_468_60_01.gif"; break;		
	case "32": $bname="oldbk_468_60_02.gif"; break;					
	case "33": $bname="oldbk_468_60_03.gif"; break;


	case "25": $bname="oldbk_120_240_01.swf"; break;
	case "27": $bname="oldbk_240_400_02.swf"; break;
	case "31": $bname="oldbk_468_60_01.swf"; break;					
	
	case "34": $bname="300x250_04.swf"; break;
	
	case "35": $bname="600x90.swf"; break;
	case "36": $bname="728x90.swf"; break;		
	case "37": $bname="728x90_n2.swf"; break;
	case "38": $bname="warrior_200x300.swf"; break;
	case "39": $bname="warrior_240x400.swf"; break;			

	case "40": $bname="oldbk_468x60_fp8_fps60.swf"; break;
	case "41": $bname="oldbk_728x90_fp8_fps60_2.swf"; break;
	case "42": $bname="oldbk_240x400_fp8_fps60_2.swf"; break;			

	case "43": $bname="240x400.swf"; break;
	
			
			}		 
		 $dat=date("d.m.Y H:i",$row['reg_time']);
 		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;$dat&nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;{$row['banner']}. ".$bname." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=http://".$row['from_site']." target=_blank  style='padding-left: 0px;'>".$row['from_site']."</a> </td></tr>";
		}
	 $o.= "</table>";
	}

$pages=ceil($quant[0]/50); // Кол-во на странице
$o.= "<br><center>...";
for($i=1; $i<$pages+1; $i++)
	{
	 if ($i>$page_num-10 and $i<$page_num+10)
		{
		 if ($i==$page_num)
			{$o.= " <b>$i</b>";}
		 else	{$o.= " <a href=?page=$i&showregs=1&f_date={$f_date}&t_date={$t_date}>$i</a>";}
		}
	}
$o.= "...</center>";


if ($f_date!='' and $t_date!='')
	{$from=$f_date; $now=$t_date;}
else
	{
	 $now=date("d.m.y");
	 $from=time()-2592000;
	 $from=date("d.m.y",$from);
	}
$o.= "<br><form method=POST action=index.php>
<input type=hidden name=showregs value=1>
<input type='text' name='f_date' class='enter1' value='$from' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id=\"calendar-inputField1\">
<button id=\"calendar-trigger1\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger1\",
        inputField : \"calendar-inputField1\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
</script>

<input type='text' name='t_date' class='enter1' value='$now' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id=\"calendar-inputField2\"/>
<button id=\"calendar-trigger2\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger2\",
        inputField : \"calendar-inputField2\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
</script>

<input type='submit' class='enter1' value='Смотреть' style='width: 70px; height:18px;'/>
</form>
";
	 render_text_block($o);
}

function ShowPayment($f_date,$t_date,$mode,$what)	{
$now_month=date("Y-m");
//$partner = mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = '{$_SESSION['partnerid']}' LIMIT 1;"));
if (($f_date=='' or $t_date=='') and $mode=='')
	{
//	 $addon='LIMIT 20';
	 $end=time();
	 $start=0;
	}
else
	{
	 if ($mode=='day' and $what!='')
	 	{
		 preg_match("/^(.*?)\-(.*?)$/",$what,$mt);
		 $start=mktime(0,0,0,$mt[2],1,$mt[1]);
		 $m=$mt[2]+1;
		 $end=mktime(0,0,0,$m,1,$mt[1]);
		}
	 if ($what=='')
	 	{
		 $end=time(); $start=0;
		}
// 	 preg_match("/(.*?)\.(.*?)\.(.*?)/",$f_date,$mt);
//	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
//	 preg_match("/(.*?)\.(.*?)\.(.*?)/",$t_date,$mt);
//	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	}
if ($mode=='')	{$mode='month';}

$dlogs = mysql_query("SELECT * FROM `partners_delo`	WHERE `transfer_time` >= '{$start}' AND `transfer_time` <= '{$end}' AND `partner_id` = '".$_SESSION['partnerid']."'  AND bank!='999999999' ORDER by `transfer_time` DESC ".$addon.";");
while($row = @mysql_fetch_array($dlogs)) 
	{
	 $dat_month=date("Y-m",$row['transfer_time']);
	 $dat_day=date("Y-m-d",$row['transfer_time']);
	 $ekr_month[$dat_month]+=$row['ekr'];
	 $ekr_day[$dat_day]+=$row['ekr'];
	 $ekrsum+=$row['ekr'];
   	}
if ($mode=='month')
	{
	 $i=0;
	 $o="
	 <b>Платежи по месяцам:</b><br>
	 Всего платежей: $ekrsum екр.
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;' width=50%>&nbsp; Месяц &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;' width=50%>&nbsp; Сумма &nbsp;</td></tr>";
	 foreach($ekr_month as $month => $mekr)
			{
			 if ($i>20)	{break;} // Кол-во выводимых месяцов.
			 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=index.php?payments=1&mode=day&what={$month} style='padding-left: 0px;'>$month</a> &nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; $mekr &nbsp;</td></tr>";
			 $i++;
			} 
	$o.= "</table>";
	}
if ($mode=='day')
	{
	 if ($what=='')	{$what=$now_month;}
	 $o.= "
	 <b>Платежи (подробно):</b><br>
	 Всего платежей: {$ekr_month[$what]} екр.
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;' width=50%>&nbsp; Дата &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;' width=50%>&nbsp; Сумма &nbsp;</td></tr>";
		foreach($ekr_day as $day => $dekr)
			{
//			 echo "$day | $now_month |".substr($day,0,5)."<br>";
			 if ($what!=substr($day,0,7)) {continue;}
	 		 preg_match("/^(.*?)-(.*?)-(.*?)$/",$day,$mt);
			 $day=$mt[3].".".$mt[2].".".$mt[1];//." ".$mt[4].":".$mt[5];
			 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; $day &nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; $dekr &nbsp;</td></tr>";
			} 
	 $o.= "</table>";
	}

 render_text_block($o);
}



function ShowFooter()	{
?>
</td>
</tr>
</table></td>
    <td valign="top" class="rightY"><table width="100%" height="215" border="0" cellpadding="0" cellspacing="0" class="headRight">
<tr>
<td height="270">&nbsp;</td>
</tr>
</table></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="footLeft">&nbsp;</td>
    <td width="1018" height="138" valign="top" class="footer">
<br>
        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="down_menu">
	<tr>
	<td height="38" valign="bottom">
	<a href="https://oldbk.com/?about=yes" target=_blank class="down_menuL">ОБ ИГРЕ</a> | 	
	<a href="https://oldbk.com/news.php" target=_blank class="down_menuL">НОВОСТИ</a> | 
	<a href="https://oldbk.com/encicl/index.php" target=_blank class="down_menuL">БИБЛИОТЕКА</a> | 
	<a href="https://oldbk.com/forum.php" target=_blank class="down_menuL">ФОРУМ</a> | 
	<a href="https://top.oldbk.com/index.php" target=_blank class="down_menuL">РЕЙТИНГИ</a> | 
	<a href="https://oldbk.com/partners/index.php" target=_blank class="down_menuL">ПАРТНЕРАМ</a>
	</td>
	</tr>
   </table>

<div align=center>
                <br>
		<?=include('../counters/all.php');?><br>
		<a href="https://oldbk.com" class="down_link" >© 2010—<?=date("Y");?> «Бойцовский Клуб ОлдБК»</a>

			

		<br><a href="https://oldbk.com/" class="down_link" style="color:#808080;">Многопользовательская бесплатная онлайн фэнтези рпг - ОлдБК - Старый Бойцовский Клуб</a>
</div>
    
    
    </td>
    <td class="footRight">&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php } 

function ShowRegForm()	{
$o='
<form method="post" action="index.php"  style="padding-top: 10px; padding-bottom: 10px;">
<input type=hidden name=registration value="yes" />
<h1>Партнерская программа: Регистрация</h1>
<table width="100%">
	<tr><td colspan="2"><b>Персональные данные</b></td></tr>

	<tr><td>Логин<font color=red>*</font></td><td><input class="input"  type="text" value="" name="name" id="name" style="width: 261px"></td></tr>
	<tr><td>Пароль<font color=red>*</font></td><td><input class="input"  type="password" name="pass" id="pass" style="width: 261px"></td></tr>
	<tr><td>Подтверждение пароля<font color=red>*</font></td><td><input class="input"  type="password" name="pass2" id="pass2" style="width: 261px"></td></tr>
	<tr><td>E-mail<font color=red>*</font></td><td><input class="input"  type="text" value="" name="email" id="email" style="width: 261px"></td></tr>
	<tr><td>Ф.И.О<font color=red>*</font></td><td><input class="input"  type="text" value="" name="fio" id="fio" style="width: 261px"></td></tr>
	<tr><td>Контактный телефон<font color=red>*</font></td><td><input class="input"  type="text" value="" name="phone" id="phone" style="width: 261px"></td></tr>

	<tr><td>Кошелек <b>WMZ</b> <a href="https://www.webmoney.ru/" target="_blank">Webmoney</a><font color=red>*</font></td><td><input class="input"  type="text" value="" name="webmoney" id="webmoney" style="width: 261px"></td></tr>

	<tr><td colspan="2"><br><br><b>Информация о ресурсе</b></td></tr>
	<tr><td>Название сайта</td><td><input class="input"  type="text" value="" name="site_name" id="site_name" style="width: 261px"></td></tr>
	<tr><td>Ссылка на сайт</td><td><input class="input"  type="text" value="" name="site_url" id="site_url" style="width: 261px"></td></tr>
	<tr><td>Описание сайта</td><td><textarea class="input"  name="site_desc" id="site_descr" cols="40" rows="5" style="width: 261px; height: 50px;"></textarea></td></tr>

    
    <tr><td colspan="2" align="center"><br><br><a href="contract.html" target="_blank">С условиями договора согласен</a> <input type="checkbox" name="contract" value="1" class="input" style="width: 20px"></td></tr>
	
	<tr><td colspan="2" align="center" style="center; padding-left: auto; padding-right: auto;"><br><br><center><input type="submit" value="Готово" id="enter" class="button2"></center></td></tr>
</table>
</form>';
echo "<br>";
render_text_block($o);
} 

function ShowAllInfo()	{
	$partner = mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = '{$_SESSION['partnerid']}' LIMIT 1;"));
	 $ekrsum=$partner['money'];
//	 $to_pay=round(($ekrsum/100*$partner['percent']),2);
	 $to_pay=$partner['getted']+$partner['money'];
//	 if ($partner['status']==1)	{$status='Партнер';}
	 if ($partner['status']==1)	{$status='Silver';}
 	 if ($partner['status']==2)	{$status='Gold';}
	 if ($partner['status']==3)	{$status='Platinum';}
	 echo "<h1>Партнерская программа: Сводная информация</h1>";
	 $o= "<table cellspacing=10 align=center width=100%><tr><td valign=top>
	 <b>Сводная информация:</b><br>
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Название &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Количество</td></tr>
	 <tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; Ваш статус</td><td align=right>".$status." &nbsp;</td></tr>
	 <tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; Ваш процент</td><td align=right>".$partner['percent']."% &nbsp;</td></tr>
  	 <tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; Всего переведено</td><td align=right>".$partner['all_ekr']." &nbsp;</td></tr>
  	 <tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; Всего заработано</td><td align=right>".$to_pay." &nbsp;</td></tr>
  	 <tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; Получено</td><td align=right>".$partner['getted']." &nbsp;</td></tr>
 	 <tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; Остаток</td><td align=right>".$partner['money']." &nbsp;</td></tr>
	 </table></td><td valign=top>
	 <b>Ваши баннеры:</b><br>
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Название &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Регистраций</td></tr>";
	 
	for ($i=1; $i<=14; $i++)
	 	{
		 $mark="show_b".$i;
		 if ($partner[$mark]=='0')	{continue;}
		 $c = mysql_fetch_array(mysql_query("SELECT count(*) FROM `partners_users` WHERE `partner` = '{$_SESSION['partnerid']}' AND `banner`='{$i}';"));
		 $bname='';
		 switch($i) {
			case "1": $bname="Легендарный (468х60)"; break;
			case "2": $bname="Тяжесть карающего топора (468х60)"; break;
			case "3": $bname="На работе и дома (468х60)"; break;
			case "4": $bname="Приключения ждут (468х60)"; break;
			case "5": $bname="Лучшая игра (468х60)"; break;
			case "6": $bname="Ты нам нужен (728х90)"; break;
			case "7": $bname="За кружкой эля (728х90)"; break;
			case "8": $bname="Тяжесть карающего топора (728х90)"; break;
			case "9": $bname="На работе и дома (728х90)"; break;
			case "10": $bname="Приключения ждут (728х90)"; break;
			case "11": $bname="Легендарный (120х300)"; break;
			case "12": $bname="Лучшая игра (120х300)"; break;
			case "13": $bname="Полоска 1 (350х19)"; break;
			case "14": $bname="Полоска 2 (350х19)"; break;
			
	case "15": $bname="oldbk_240_400_03.swf"; break;
	case "17": $bname="oldbk_240_400_01.jpg"; break;

	case "18": $bname="oldbk_240_400_01.swf"; break;
	case "19": $bname="oldbk_240_400_02.gif"; break;
	
	
	case "20": $bname="oldbk_240_400_03.gif"; break;
	case "21": $bname="oldbk_240_400_04.swf"; break;
	
	
		
	case "22": $bname="oldbk_240_400_05.gif"; break;	
	case "16": $bname="oldbk_120_240_01.gif"; break;
	

	case "23": $bname="oldbk_728_90_02.gif"; break;	
	case "24": $bname="oldbk_728_90_02.swf"; break;
	
	case "26": $bname="oldbk_120_240_02.gif"; break;
	case "28": $bname="oldbk_240_400_04.gif"; break;
	case "29": $bname="oldbk_728_90_03.gif"; break;		
	case "30": $bname="oldbk_468_60_01.gif"; break;		
	case "32": $bname="oldbk_468_60_02.gif"; break;					
	case "33": $bname="oldbk_468_60_03.gif"; break;


	case "25": $bname="oldbk_120_240_01.swf"; break;
	case "27": $bname="oldbk_240_400_02.swf"; break;
	case "31": $bname="oldbk_468_60_01.swf"; break;					
	
	case "34": $bname="300x250_04.swf"; break;
	
	case "35": $bname="600x90.swf"; break;
	case "36": $bname="728x90.swf"; break;
	case "37": $bname="728x90_n2.swf"; break;
	case "38": $bname="warrior_200x300.swf"; break;		
	case "39": $bname="warrior_240x400.swf"; break;
		
	case "40": $bname="oldbk_468x60_fp8_fps60.swf"; break;
	case "41": $bname="oldbk_728x90_fp8_fps60_2.swf"; break;		
	case "42": $bname="oldbk_240x400_fp8_fps60_2.swf"; break;
	
	case "43": $bname="240x400.swf"; break;
				
			
			}
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; {$i}. $bname </td><td align=right>".$c[0]." &nbsp;</td></tr>";
		}

	 $o.= "</table></td><td valign=top>
	 <b>Ваши сайты:</b><br>
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Название &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Регистраций</td></tr>";
 	 $s = mysql_query("SELECT partner, from_site , count(id) as regs FROM `partners_users` WHERE `partner` = '{$_SESSION['partnerid']}' GROUP by from_site ORDER BY `regs` DESC ;");
	 $i=0;
	 while ($row=mysql_fetch_array($s))
		{
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; <a href='{$row[from_site]}' target=_blank style='padding-left: 2px;'>{$row[from_site]}</a> </td><td align=right>{$row[regs]} &nbsp;</td></tr>";	
		}


	 $o.= "</table></td>";
	 
	 $o.= "</tr></table>";
render_text_block($o);
}

function ShowBanners()	{
?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(function(){$(".banner-code").click(function(){$(this).select();});});
	</script>

	<style type="text/css">
		.title H3 {float:none!important;}
		.news_text {overflow:hidden;text-align:center;zoom:1;}
		.banner-code {margin-bottom:6em;}
		.left,.right{float:left;width:49%;}
	</style>
						<!--	<div class="title"><h1>Наши партнеры</h1></div>
							<div class="news_text">

								
								<script type="text/javascript">swfobject.registerObject("oldbk-banner_v03", "9.0.0");</script>

	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
									<a href="https://vivatours.co.il" target="_blank" style="position:absolute;top:0;left:0;width:100%;height:100%;"></a>
									<object id="oldbk-banner_v03" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="728" height="90">
										<param name="movie" value="https://i.oldbk.com/i/banners/banner_v03.swf" />
										<param name="wmode" value="opaque" />
										<param name="quality" value="high" />
										--><!--[if !IE]>--><!--<object type="application/x-shockwave-flash" data="https://i.oldbk.com/i/banners/banner_v03.swf" width="728" height="90" wmode="opaque"></object>--><!--<![endif]--><!--
									</object>
	                     </div>

							</div>
						</div>-->
							<i>Для размещения банера скопируйте код баннера и вставьте на страницы своего ресурса:</i>						
						<div class="item" style="background-position: center 100%;">
<!--							<div class="title">
								<h1>Банеры для сайтов партнеров</h1>
                        <p><i>Для размещения банера скопируйте код баннера и вставьте на страницы своего ресурса:</i></p>
							</div>-->

                            <div class="news_text" style="background-position: center 100%;">
<center>
<table align=center style="padding-left: auto; padding-right: auto; display:block;"><tr><td align=center>Партнерская ссылка:</td></tr>
<tr><td align=center style="border: 1px solid #85755d; padding: 5px;">
<b>https://oldbk.com/?pid=<?php echo $_SESSION['partnerid'] ?></b> - главная стр. сайта <br>
<b>https://oldbk.com/reg.php?pid=<?php echo $_SESSION['partnerid'] ?></b> - регистрация в игре 
</td></tr></table></center><br /><br />



<!--banner//--><center><b>oldbk_468x60_fp8_fps60.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_468x60_fp8_fps60.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=40&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb40" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb40').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_468x60_fp8_fps60.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=40%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>


<!--banner//--><center><b>oldbk_728x90_fp8_fps60_2.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_728x90_fp8_fps60_2.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=41&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb41" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb41').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_728x90_fp8_fps60_2.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=41%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>


<!--banner//--><center><b>oldbk_240x400_fp8_fps60_2.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_240x400_fp8_fps60_2.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=42&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb42" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb42').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_240x400_fp8_fps60_2.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=42%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>


								<!--banner//--><center><b>Легендарный (468х60)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/468x60_01.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=1&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb1" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb1').innerHTML='<embed src="https://i.oldbk.com/i/bp/468x60_01.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=1%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>

								<!--banner//--><center><b>Тяжесть карающего топора (468х60)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/468x60_02.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=2&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb2" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb2').innerHTML='<embed src="https://i.oldbk.com/i/bp/468x60_02.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=2%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
								
								
								<!--banner//--><center><b>600x90.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:600px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/600x90.swf" width="600" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=35&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb35" style="position:relative;margin:0 auto 1em;width:600px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb35').innerHTML='<embed src="https://i.oldbk.com/i/bp/600x90.swf" width="600" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=35%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>								
								
								<!--banner//--><center><b>728x90.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=36&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb36" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb36').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=36%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>								
								
								<!--banner//--><center><b>728x90_n2.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90_n2.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=37&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb37" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb37').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90_n2.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=37%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>								
								
								<!--banner//--><center><b>На работе и дома (468х60)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/468x60_03.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=3&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb3" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb3').innerHTML='<embed src="https://i.oldbk.com/i/bp/468x60_03.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=3%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
								
								
							<? /*	<!--banner//--><center><b> Приключения ждут (468х60)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/468x60_04.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>  
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=4&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb4" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb4').innerHTML='<embed src="https://i.oldbk.com/i/bp/468x60_04.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=4%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
							*/ ?>
								<!--banner//--><center><b>Лучшая игра (468х60)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/468x60_05.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=5&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb5" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb5').innerHTML='<embed src="https://i.oldbk.com/i/bp/468x60_05.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=5%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>


							<? /*	<!--banner//--><center><b>Ты нам нужен (728х90)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90_01.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>   
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=6&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb6" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb6').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90_01.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=6%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
								

								<!--banner//--><center><b>За кружкой эля (728х90)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90_02.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=7&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb7" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb7').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90_02.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=7%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
						*/ ?>
								<!--banner//--><center><b>Тяжесть карающего топора (728х90)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90_03.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=8&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb8" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb8').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90_03.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com/reg.php?reg=1%26b=8%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>

								<!--banner//--><center><b>На работе и дома (728х90)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90_04.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=9&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb9" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb9').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90_04.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com/reg.php?reg=1%26b=9%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>

								
							<? /*	<!--banner//--><center><b>Приключения ждут (728х90)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/728x90_05.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=10&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb10" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb10').innerHTML='<embed src="https://i.oldbk.com/i/bp/728x90_05.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=10%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
                                */ ?>
<table width=100%><tr><td align=center>
								<!--banner//--><center><b>Легендарный (120х300)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:120px;height:300px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/120_300_2.swf" width="120" height="300" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="10" cols="45"><img src="https://oldbk.com/partners/im.php?b=11&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb11" style="position:relative;margin:0 auto 1em;width:120px;height:300px;overflow:hidden;"></div><script>document.getElementById('bb11').innerHTML='<embed src="https://i.oldbk.com/i/bp/120_300_2.swf" width="120" height="300" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=11%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</td><td align=center>
								<!--banner//--><center><b>Лучшая игра (120х300)</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:120px;height:300px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/120_300_3.swf" width="120" height="300" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="10" cols="45"><img src="https://oldbk.com/partners/im.php?b=12&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb12" style="position:relative;margin:0 auto 1em;width:120px;height:300px;overflow:hidden;"></div><script>document.getElementById('bb12').innerHTML='<embed src="https://i.oldbk.com/i/bp/120_300_3.swf" width="120" height="300" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=12%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>

</td></tr></table>											

								<!--banner//--><center><b>300x250_04.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:300px;height:250px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/300x250_04.swf" width="300" height="250" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="10" cols="45"><img src="https://oldbk.com/partners/im.php?b=34&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb34" style="position:relative;margin:0 auto 1em;width:300px;height:250px;overflow:hidden;"></div><script>document.getElementById('bb34').innerHTML='<embed src="https://i.oldbk.com/i/bp/300x250_04.swf" width="300" height="250" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=34%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>



<table align=center width=100%><tr><td align=center>		<!--little-bpart//--><b>Полоска 1 (350х19)</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/baner_f-1.png" border="0"></a><br />
									<b>Код:</b><br />

									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=13&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb13'></div><script>document.getElementById('bb13').innerHTML='<a href="https://oldbk.com?b=13&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/baner_f-1.png" border=0></a>';</script></textarea>
</td><td align=center>
                                							<b>Полоска 2 (350х19)</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/baner_f-2.png" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=14&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb14'></div><script>document.getElementById('bb14').innerHTML='<a href="https://oldbk.com?b=14&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/baner_f-2.png" border=0></a>';</script></textarea>
</td></tr></table>



<table align=center width=100% border=0><tr><td align=center>
<!--banner//--><center><b>warrior_200x300.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:200px;height:300px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/warrior_200x300.swf" width="200" height="300" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="12" cols="60"><img src="https://oldbk.com/partners/im.php?b=38&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb38" style="position:relative;margin:0 auto 1em;width:200px;height:300px;overflow:hidden;"></div><script>document.getElementById('bb38').innerHTML='<embed src="https://i.oldbk.com/i/bp/warrior_200x300.swf" width="200" height="300" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=38%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</td><td align=center>                                
                                							<b>warrior_240x400.swf</b><br />
	                     <div style="position:relative;margin:0 auto 1em;width:200px;height:300px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/warrior_240x400.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="12" cols="60"><img src="https://oldbk.com/partners/im.php?b=39&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb39" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb39').innerHTML='<embed src="https://i.oldbk.com/i/bp/warrior_240x400.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="banner_href=https://oldbk.com?b=39%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</td></tr></table>




<table align=center width=100%><tr><td align=center>
<!--banner//--><center><b>oldbk_240_400_03.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_03.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=15&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb15" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb15').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_03.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=15%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</td><td align=center>                                
                                							<b>oldbk_240_400_01.jpg</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_01.jpg" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=17&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb17'></div><script>document.getElementById('bb17').innerHTML='<a href="https://oldbk.com?b=17&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_01.jpg" border=0></a>';</script></textarea>
</td></tr></table>

<table align=center width=100%><tr><td align=center>
<!--banner//--><center><b>oldbk_240_400_01.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_01.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=18&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb18" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb18').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_01.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=18%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</td><td align=center>                                
                                							<b>oldbk_240_400_02.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_02.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=19&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb19'></div><script>document.getElementById('bb19').innerHTML='<a href="https://oldbk.com?b=19&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_02.gif" border=0></a>';</script></textarea>
</td></tr></table>
<table align=center width=100%><tr><td align=center>
<!--banner//--><center><b>oldbk_240_400_04.swf</b></center>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_04.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=21&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb21" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb21').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_04.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=21%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</td><td align=center>                                
                                							<b>oldbk_240_400_03.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_03.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=20&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb20'></div><script>document.getElementById('bb20').innerHTML='<a href="https://oldbk.com?b=20&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_03.gif" border=0></a>';</script></textarea>
</td></tr></table>

<table align=center width=100%><tr><td align=center>
                                							<b>oldbk_240_400_05.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_05.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=22&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb22'></div><script>document.getElementById('bb22').innerHTML='<a href="https://oldbk.com?b=22&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_05.gif" border=0></a>';</script></textarea>
</td><td align=center>                                
                                							<b>oldbk_120_240_01.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_120_240_01.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="8" cols="45"><img src="https://oldbk.com/partners/im.php?b=16&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb16'></div><script>document.getElementById('bb16').innerHTML='<a href="https://oldbk.com?b=16&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_120_240_01.gif" border=0></a>';</script></textarea>
</td></tr></table>



<!--banner//--><center><b>oldbk_728_90_02.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_728_90_02.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=24&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb24" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb24').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_728_90_02.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=24%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center>                               
<center><b>oldbk_728_90_02.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_728_90_02.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=23&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb23'></div><script>document.getElementById('bb23').innerHTML='<a href="https://oldbk.com?b=23&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_728_90_02.gif" border=0></a>';</script></textarea>
</center>									

<center><b>oldbk_120_240_02.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_120_240_02.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=26&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb26'></div><script>document.getElementById('bb26').innerHTML='<a href="https://oldbk.com?b=26&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_120_240_02.gif" border=0></a>';</script></textarea>
</center>									
<center><b>oldbk_240_400_04.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_04.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=28&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb28'></div><script>document.getElementById('bb28').innerHTML='<a href="https://oldbk.com?b=28&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_240_400_04.gif" border=0></a>';</script></textarea>
</center>	
<center><b>oldbk_728_90_03.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_728_90_03.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=29&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb29'></div><script>document.getElementById('bb29').innerHTML='<a href="https://oldbk.com?b=29&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_728_90_03.gif" border=0></a>';</script></textarea>
</center>
<center><b>oldbk_468_60_01.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_468_60_01.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=30&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb30'></div><script>document.getElementById('bb30').innerHTML='<a href="https://oldbk.com?b=30&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_468_60_01.gif" border=0></a>';</script></textarea>
</center>
<center><b>oldbk_468_60_02.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_468_60_02.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=32&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb32'></div><script>document.getElementById('bb32').innerHTML='<a href="https://oldbk.com?b=32&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_468_60_02.gif" border=0></a>';</script></textarea>
</center>
<center><b>oldbk_468_60_03.gif</b><br />

									<a href="https://oldbk.com" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_468_60_03.gif" border="0"></a><br />
									<b>Код:</b><br />
									<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=33&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id='bb33'></div><script>document.getElementById('bb33').innerHTML='<a href="https://oldbk.com?b=33&pid=<?php echo $_SESSION['partnerid'] ?>' + '&ref=' + document.domain + '" target="_blank"><img src="https://i.oldbk.com/i/bp/oldbk_468_60_03.gif" border=0></a>';</script></textarea>
</center>

<!--banner//--><center><b>oldbk_120_240_01.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:120px;height:240px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_120_240_01.swf" width="120" height="240" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=25&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb25" style="position:relative;margin:0 auto 1em;width:120px;height:240px;overflow:hidden;"></div><script>document.getElementById('bb25').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_120_240_01.swf" width="120" height="240" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=25%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center>   
<!--banner//--><center><b>oldbk_240_400_02.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_02.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=27&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb27" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb27').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_240_400_02.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=27%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center>   
<!--banner//--><center><b>oldbk_468_60_01.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/oldbk_468_60_01.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=31&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb31" style="position:relative;margin:0 auto 1em;width:468px;height:60px;overflow:hidden;"></div><script>document.getElementById('bb31').innerHTML='<embed src="https://i.oldbk.com/i/bp/oldbk_468_60_01.swf" width="468" height="60" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=31%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center>   

<!--banner//--><center><b>Banner_728x90_1.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/Banner_728x90_1.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=32&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb32" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb32').innerHTML='<embed src="https://i.oldbk.com/i/bp/Banner_728x90_1.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=32%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center>   
<!--banner//--><center><b>Banner_728x90_2.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/Banner_728x90_2.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=33&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb33" style="position:relative;margin:0 auto 1em;width:728px;height:90px;overflow:hidden;"></div><script>document.getElementById('bb33').innerHTML='<embed src="https://i.oldbk.com/i/bp/Banner_728x90_2.swf" width="728" height="90" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=33%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center>   

<!--banner//--><center><b>Banner_240x400_2.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/Banner_240x400_2.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=34&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb34" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb34').innerHTML='<embed src="https://i.oldbk.com/i/bp/Banner_240x400_2.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="link1=https://oldbk.com?b=34%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center> 

<!--banner//--><center><b>240x400.swf</b>
	                     <div style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;">
<embed src="https://i.oldbk.com/i/bp/240x400.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="clickTAG=https://oldbk.com"></embed>
	                     </div>
								<strong>Код:</strong><br />
								<textarea class="banner-code" rows="7" cols="90"><img src="https://oldbk.com/partners/im.php?b=43&pid=<?php echo $_SESSION['partnerid'] ?>"/><div id="bb43" style="position:relative;margin:0 auto 1em;width:240px;height:400px;overflow:hidden;"></div><script>document.getElementById('bb43').innerHTML='<embed src="https://i.oldbk.com/i/bp/240x400.swf" width="240" height="400" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/ru/flashplayer/" flashvars="clickTAG=https://oldbk.com?b=43%26pid=<?php echo $_SESSION['partnerid'] ?>' + '%26ref=' + document.domain + '"></embed>';</script></textarea>
</center> 



								<i class="clear"></i>
								
							</div><!--.news_text//-->
						</div><!--.item//-->



<?php
}

function ShowStat()	{
$o= "
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%><tr>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Название &nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;' align=right>&nbsp; Показов</td>
<td style='background-color: #85755d; color: #f6e7c6;' align=right>&nbsp; Переходов</td>
<td style='background-color: #85755d; color: #f6e7c6;' align=right>&nbsp; CTR</td>
<td style='background-color: #85755d; color: #f6e7c6;' align=right>&nbsp; Регистраций</td>
</tr>";
$data = mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = '{$_SESSION['partnerid']}' LIMIT 1;"));
for ($i=1; $i<=14; $i++)
 	{
	 $mark="show_b".$i;
//	 if ($data[$mark]=='0')	{continue;}
	 $c = mysql_fetch_array(mysql_query("SELECT count(*) FROM `partners_users` WHERE `partner` = '{$_SESSION['partnerid']}' AND `banner`='{$i}';"));
	 $show_mark="show_b".$i;
	 $click_mark="click_b".$i;
	 if ($data[$show_mark]==0) {continue;}
	 $bname='';
	 switch($i) {
			case "1": $bname="Легендарный (468х60)"; break;
			case "2": $bname="Тяжесть карающего топора (468х60)"; break;
			case "3": $bname="На работе и дома (468х60)"; break;
			case "4": $bname="Приключения ждут (468х60)"; break;
			case "5": $bname="Лучшая игра (468х60)"; break;
			case "6": $bname="Ты нам нужен (728х90)"; break;
			case "7": $bname="За кружкой эля (728х90)"; break;
			case "8": $bname="Тяжесть карающего топора (728х90)"; break;
			case "9": $bname="На работе и дома (728х90)"; break;
			case "10": $bname="Приключения ждут (728х90)"; break;
			case "11": $bname="Легендарный (120х300)"; break;
			case "12": $bname="Лучшая игра (120х300)"; break;
			case "13": $bname="Полоска 1 (350х19)"; break;
			case "14": $bname="Полоска 2 (350х19)"; break;
			
	case "15": $bname="oldbk_240_400_03.swf"; break;
	case "17": $bname="oldbk_240_400_01.jpg"; break;

	case "18": $bname="oldbk_240_400_01.swf"; break;
	case "19": $bname="oldbk_240_400_02.gif"; break;
	
	
	case "20": $bname="oldbk_240_400_03.gif"; break;
	case "21": $bname="oldbk_240_400_04.swf"; break;
	
	
		
	case "22": $bname="oldbk_240_400_05.gif"; break;	
	case "16": $bname="oldbk_120_240_01.gif"; break;
	

	case "23": $bname="oldbk_728_90_02.gif"; break;	
	case "24": $bname="oldbk_728_90_02.swf"; break;
	
	case "26": $bname="oldbk_120_240_02.gif"; break;
	case "28": $bname="oldbk_240_400_04.gif"; break;
	case "29": $bname="oldbk_728_90_03.gif"; break;		
	case "30": $bname="oldbk_468_60_01.gif"; break;		
	case "32": $bname="oldbk_468_60_02.gif"; break;					
	case "33": $bname="oldbk_468_60_03.gif"; break;


	case "25": $bname="oldbk_120_240_01.swf"; break;
	case "27": $bname="oldbk_240_400_02.swf"; break;
	case "31": $bname="oldbk_468_60_01.swf"; break;					
	
	case "34": $bname="300x250_04.swf"; break;
		
	case "43": $bname="240x400.swf"; break;
	
			
			
			}
	 $ctr=round(($data[$click_mark]/$data[$show_mark])*100,2);
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td> &nbsp; {$i}. {$bname}</td><td align=right>{$data[$show_mark]} &nbsp;</td><td align=right>{$data[$click_mark]} &nbsp;</td><td align=right>{$ctr}% &nbsp;</td><td align=right>{$c[0]} &nbsp;</td></tr>";
	}	 
$o.=  "</table>";
render_text_block($o);
}

function AdminOwerview($who)	{
global $_REQUEST;
if ($who!='')
	{
	 if ($_REQUEST['setpercent']!='')
	 	{
		 mysql_query("UPDATE `partners` SET `percent`='{$_REQUEST['setpercent']}' WHERE `id` = '{$who}';");
		}
	 if ($_REQUEST['chstatus']!='')
	 	{
		 mysql_query("UPDATE `partners` SET `status`='{$_REQUEST['chstatus']}' WHERE `id` = '{$who}';");
		}

	
	 $user = mysql_fetch_array(mysql_query("SELECT * FROM `partners` WHERE `id` = '{$who}' LIMIT 1;"));
	 $regcount=mysql_fetch_array(mysql_query("SELECT count(*) FROM `partners_users` WHERE `partner` = '{$who}';"));
	 $alldone=$user['getted']+$user['money'];
	 if ($user['status']<1) {$user['status']='<font color=red>Неодобрен</font>'; $upst=1;}
//	 if ($user['status']==1) {$user['status']='Партнер';}
	 if ($user['status']==1) {$user['status']='Silver'; $upst=2;}
	 if ($user['status']==2) {$user['status']='Gold'; $upst=3;}
	 if ($user['status']==3) {$user['status']='Platinum'; $upst=0;}

	 $o= "<b>Логин:</b> {$user['login']}<br>
	 <b>Почта:</b> {$user['email']}<br>
	 <b>ФИО:</b> {$user['fio']}<br>
	 <b>Телефон:</b> {$user['phone']}<br>
	 <b>Кошелек Webmoney:</b> {$user['wm']}<br>
	 <b>Название сайта:</b> {$user['site_name']}<br>
	 <b>Ссылка на сайт:</b> {$user['site_link']}<br>
	 <b>Описание сайта:</b> {$user['site_desc']}<br>
	 <b>Статус:</b> <a href=index.php?showpartner={$who}&chstatus={$upst} style='padding-left: 0px; padding-right: 0px;'>{$user['status']}</a><br>
	 <b>Процент:</b> {$user['percent']}%<br>
 	 <b>Всего регистраций:</b> {$regcount[0]}<br>
	 <b>Всего переводов:</b> {$user['all_ekr']}<br>
	 <b>Всего заработано:</b> {$alldone}<br>
	 <b>Забрано:</b> {$user['getted']}<br>
	 <b>К выдаче:</b> {$user['money']}<br>
	 <br><form method=post action=index.php style='width: 300px; display: inline;'>
	 Отметить выплаченным:
	 <input type=hidden name='pid' value='{$who}'>
	 <input type=hidden name='pay' value='{$who}'>
	 <input type=text name='getted' size=4>
	 <input type=submit class='enter1' value='Выплатить'></form>

	 <form method=post action=index.php style='width: 300px; display: inline;'>
 	 <input type=hidden name='pid' value='{$who}'>
  	 <input type=hidden name='showpartner' value='{$who}'>
	 <input type=text name='setpercent' size=4>
	 <input type=submit class='enter1' value='Установить процент'>
	 </form>
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; ";

	 if ($user['status']=='<font color=red>Неодобрен</font>')
		{
		 $o.= "<a href=index.php?approve={$who}&showpartner={$who}  style='padding-right: 1px;'>Одобрить</a> ";
		//echo "<a href=index.php?delclaim={$who}  style='padding-left: 10px; padding-right: 1px;'>Удалить</a>";
	  	$o.= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;Причина<form method=POST style='width: 300px; display: inline;'><input type=text name='txtblock' size=24><input type=hidden name='delclaim' value=".$who."><input type=submit class='enter1' value='Удалить'></form>";		
	  	}
	 else   { 
	  	$o.= "Причина<form method=POST style='width: 300px; display: inline;'><input type=text name='txtblock' size=24><input type=hidden name='unapprove' value=".$who."><input type=submit class='enter1' value='Заблокировать'></form>";
	  	}
	 $o.= "<br><br>";
 	 render_text_block($o);
 	 $o='';

	  if ($_REQUEST['showbaner']!='')
	 {
		
		include("GeoIP/geoipcity.inc");
		include("GeoIP/geoipregionvars.php");
		$gi = geoip_open("/www/oldbk.com/partners/GeoIP/GeoLiteCity.dat",GEOIP_STANDARD);

	 $o.= "<br><br><b>GEO IP - Сайт:{$_REQUEST['showbaner']}</b><br>
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Страна &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Регистраций</td></tr>";
	  $s = mysql_query("SELECT * FROM `partners_users` WHERE `partner` = '{$who}' and from_site='".mysql_real_escape_string($_REQUEST['showbaner'])."' ORDER BY `reg_time` DESC ;");
	 $i=0;
	 $CNAME=array();
	 while ($row=mysql_fetch_array($s))
		{
		 $record = geoip_record_by_addr($gi,$row[ip]);
 		 $CNAME[$record->country_name]++;
		}

	arsort($CNAME);

		
	 foreach($CNAME as $cn => $co)
	 	{
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp;";
		 $o.= $cn;
		 $o.= "</td>";		 
		 $o.= "<td align=right>{$co} &nbsp;</td></tr>";	
	 	}		
		
	 $o.= "</table>";
	 
 		geoip_close($gi);
	 }
	 else
	 {
///
 $o.= "<br><br><b>Сайты:</b>  <br>
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Название &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Регистраций</td></tr>";
 	 $s = mysql_query("SELECT partner, from_site , count(id) as regs FROM `partners_users` WHERE `partner` = '{$who}' GROUP by from_site ORDER BY `regs` DESC ;");
	 $i=0;
	 while ($row=mysql_fetch_array($s))
		{
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp; <a href='{$row[from_site]}' target=_blank style='padding-left: 2px;'>{$row[from_site]}</a> </td><td align=right><a href=?showpartner={$who}&showbaner={$row[from_site]}>{$row[regs]} &nbsp;</a></td></tr>";	
		}
	 $o.= "</table>";
	 
/////////////////////////////////////	 
		
		include("GeoIP/geoipcity.inc");
		include("GeoIP/geoipregionvars.php");
		$gi = geoip_open("/www/oldbk.com/partners/GeoIP/GeoLiteCity.dat",GEOIP_STANDARD);

	 $o.= "<br><br><b>GEO IP -  Партнер ID:{$who}</b><br>
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Страна &nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Регистраций</td></tr>";
	  $s = mysql_query("SELECT * FROM `partners_users` WHERE `partner` = '{$who}' ORDER BY `reg_time` DESC ;");
	 $i=0;
	 $CNAME=array();
	 while ($row=mysql_fetch_array($s))
		{
		 $record = geoip_record_by_addr($gi,$row[ip]);
 		 $CNAME[$record->country_name]++;
		}

	arsort($CNAME);

		
	 foreach($CNAME as $cn => $co)
	 	{
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td>&nbsp;";
		 $o.= $cn;
		 $o.= "</td>";		 
		 $o.= "<td align=right>{$co} &nbsp;</td></tr>";	
	 	}		
		
	 $o.= "</table>";
	 
	geoip_close($gi);	  	 
	 
////////////////////////////////////////////	 
	 }
///	 


	 
	 
	 $o.= "<center><a href=index.php>Назад</a>";
	 $o.="</center>";
	 
	 render_text_block($o);	 
 	 ShowFooter();
	 exit();
	}

$o="
<table width=100%><tr><td width=48% valign=top>
<b>Топ 5 партнеров</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%><tr>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Логин &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Регистраций &nbsp;</td>
</tr>";

$data =mysql_query("SELECT partners_users.*, partners.id AS pid, partners.login AS plogin 
FROM `partners_users`, `partners` 
WHERE partners.id = partners_users.partner;");
while ($row=mysql_fetch_array($data))
	{
	 $rg[$row['plogin']]++;
	 $il[$row['plogin']]=$row['pid'];
	}
arsort($rg);
foreach ($rg as $key => $value)
	{
	 if ($j>5) {break;}
	 $o.="<tr  onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\">
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>
	 <a href=index.php?showpartner={$il[$key]} style='padding-left: 0px; padding-right: 0px'>$key</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$value}</td>
	 </tr>";
	 $j++;
	}
$o.= "</table><br><br></td><td width=4%>&nbsp;</td><td width=48% valign=top>";


$o.= "<b>Топ 5 сайтов</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%><tr>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Сайт &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Регистраций &nbsp;</td>
</tr>";
$j=0;
$data =mysql_query("SELECT * FROM `partners_users`;");
while ($row=mysql_fetch_array($data))
	{
	 $st[$row['from_site']]++;
	}
arsort($st);
foreach ($st as $key => $value)
	{
	 if ($j>5) {break;}
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\">
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>
	 <a href=http://{$key} style='padding-left: 0px; padding-right: 0px' target=_blank>$key</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$value}</td>
	 </tr>";
	 $j++;
	}
$o.= "</table><br><br></td></table>";
render_text_block($o);
/////////////////////////////////////
$o= "<b>Заявки на партнерство</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%><tr>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Логин &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Почта &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>ФИО &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Телефон &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Webmoney &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Название сайта &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Ссылка на сайт &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Описание сайта &nbsp;</td>
<td valign=top style='background-color: #85755d; color: #f6e7c6;'>Действия&nbsp;</td>
</tr>";
$data =mysql_query("SELECT * FROM `partners` WHERE `status` < 1;");
while ($row=mysql_fetch_array($data))
	{
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\">
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'><a href=index.php?showpartner={$row['id']} style='padding-left: 1px; padding-right: 1px;'>{$row['login']}</a></td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['email']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['fio']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['phone']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['wm']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['site_name']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['site_link']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['site_desc']}</td>
	 <td valign=top style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'><a href=index.php?approve={$row['id']}  style='padding-left: 1px; padding-right: 1px;'>Одобрить</a><a href=index.php?delclaim={$row['id']}  style='padding-left: 10px; padding-right: 1px;'>Удалить</a></td>
	 </tr>";
	}
$o.= "</table><br><br>";
render_text_block($o);	
////////////////////////////////////	

$o= "<b>Информация о партнерах</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%><tr>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;<a href='?orderby=login".((($_GET['orderby']=='login') and (!(isset($_GET['d']))))?"&d=1":"")."'>Логин</a>&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Почта</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; ФИО</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Дата рег.</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Телефон</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Webmoney</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp; Статус</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;<a href='?orderby=precent".((($_GET['orderby']=='precent') and (!(isset($_GET['d']))))?"&d=1":"")."'>Процент</a>&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;<a href='?orderby=reg".((($_GET['orderby']=='reg') and (!(isset($_GET['d']))))?"&d=1":"")."'>Регистраций</a>&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;<a href='?orderby=ekr".((($_GET['orderby']=='ekr') and (!(isset($_GET['d']))))?"&d=1":"")."'>Екр</a>&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;<a href='?orderby=pay".((($_GET['orderby']=='pay') and (!(isset($_GET['d']))))?"&d=1":"")."'>К выдаче</a>&nbsp;</td>
</tr>";
 if (isset($_GET['orderby']))
 	{
 	 if ($_GET['orderby']=='login')
 	 	{
 	 	 $orderby=' order by login ';
 	 	}
 	 elseif ($_GET['orderby']=='precent')
 	 	{
 	 	 $orderby=' order by percent ';
 	 	}
	elseif ($_GET['orderby']=='reg')
 	 	{
 	 	 $orderby=' order by 47 ';
 	 	} 	 	
	elseif ($_GET['orderby']=='ekr')
 	 	{
 	 	 $orderby=' order by all_ekr ';
 	 	} 	 	
	elseif ($_GET['orderby']=='pay')
 	 	{
 	 	 $orderby=' order by money ';
 	 	}
 	else
 	 	{
 	 	$orderby='';
 	 	} 
 	
 	if ((isset($_GET['d'])) and ($orderby!=''))
 		{
 		$orderby.=' DESC ';
 		}
 	 		 	
 	}
 	else
 	{
	 	$orderby='';
 	}

$data =mysql_query("SELECT *, (SELECT count(*) FROM `partners_users` WHERE `partner` = partners.id) as regcount FROM partners where status >0 ".$orderby."  ;");
 
while ($row=mysql_fetch_array($data))
	{
 	 $regcount=$row[regcount];
 	 $regdate=explode(" ",$row[regdate]);
  	 $regdate=explode("-",$regdate[0]);
	 if ($row['status']<1) {$row['status']='<font color=red>Неодобрен</font>';}
//	 if ($row['status']==1) {$row['status']='Партнер';}
	 if ($row['status']==1) {$row['status']='Silver';}
	 if ($row['status']==2) {$row['status']='Gold';}
	 if ($row['status']==3) {$row['status']='Platinum';}
	 if ($row['money']>50)	{$money='<font color=red>'.$row['money'].'</font>';} else {$money=$row['money'];}
	 $row['money']="<a href=index.php?pid={$row['id']}&getted={$row['money']} style='padding-left: 0px; padding-right: 0px;'>".$money."</a>";
	 	 
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\">
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'><a href=index.php?showpartner={$row['id']} style='padding-left: 1px; padding-right: 1px;'>{$row['login']}</a></td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['email']}</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['fio']}</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$regdate[2]}.{$regdate[1]}.{$regdate[0]}</td>	 
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['phone']}</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['wm']}</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['status']}</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['percent']}%</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=index.php?showregs=1&pr={$row['id']} style='padding-left: 0px; padding-right: 0px;'>{$regcount}</a></td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['all_ekr']}</td>
	 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; {$row['money']}</td>
	 </tr>";
	}
$o.=  "</table>";
render_text_block($o);
}

function AdminRegs($fraud,$unfraud,$f_date,$t_date,$sort,$one_partner)	{
global $_REQUEST;
if ($one_partner!='')
	{
	 $p_login=mysql_fetch_array(mysql_query("SELECT id, login FROM `partners` WHERE `id`='{$one_partner}' LIMIT 1;"));
	 $o.= "<h3>Регистрации партнера {$p_login['login']}</h3>";
	}
if ($fraud!='')
	{mysql_query("UPDATE `partners_users` SET `fraud`='1' WHERE `id`='{$fraud}';");}
if ($unfraud!='')
	{mysql_query("UPDATE `partners_users` SET `fraud`='0' WHERE `id`='{$unfraud}';");}
$psort=$sort;
if ($sort=='') {$sort=' ORDER BY `reg_time` DESC '; $r1=' •';}
if ($sort=='date') {$sort=' ORDER BY `reg_time` DESC'; $r1=' •';}
if ($sort=='nick') {$sort=' ORDER BY login ASC'; $r3=' •';}
if ($sort=='lvl') {$sort=' ORDER BY lvl DESC'; $r4=' •';}
if ($sort=='ip') {$sort=' ORDER BY ip ASC'; $r5=' •';}
if ($sort=='partner') {$sort=' ORDER BY partners.login ASC'; $r6=' •';}
if ($sort=='banner') {$sort=' ORDER BY banner ASC'; $r7=' •';}
if ($sort=='site') {$sort=' ORDER BY from_site ASC'; $r8=' •';}

if ($f_date=='' or $t_date=='') // Выводим последние рег-ии если не указан диапазон.
	{
	 if ($one_partner!='')	{$add=" AND partners_users.partner='{$one_partner}'";}
 	 $data=mysql_query("SELECT partners_users.*,users.id AS uid,users.login,users.level AS lvl,partners.id AS pid,partners.login AS plogin 
	 FROM `partners_users`,oldbk.`users`,`partners` 
	 WHERE users.id=partners_users.id AND users.id_city=0 AND partners.id=partners_users.partner {$add}
	 UNION
	 SELECT partners_users.*,users.id AS uid,users.login,users.level AS lvl,partners.id AS pid,partners.login AS plogin 
	 FROM `partners_users`,avalon.`users`,`partners` 
	 WHERE users.id=partners_users.id AND users.id_city=1 AND partners.id=partners_users.partner {$add}
	 {$sort};");
	 
	 $all_reg=mysql_num_rows($data);
	 $o.= "<b>Последние регистрации:</b><br>Всего регистраций: $all_reg
	 <table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=date&pr={$one_partner}>Дата</a>
	 {$r1}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=nick&pr={$one_partner}>Ник</a>
	 {$r3}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=lvl&pr={$one_partner}>lvl</a>
	 {$r4}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=ip&pr={$one_partner}>IP</a>
	 {$r5}&nbsp;</td>
	  <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=partner&pr={$one_partner}>Партнер</a>
	  {$r6}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=banner&pr={$one_partner}>Баннер</a>
	 {$r7}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=site&pr={$one_partner}>Сайт</a>
	 {$r8}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Фрод&nbsp;</td>
	 </tr>";
	 while ($row=mysql_fetch_array($data))
		{	 
		 if ($i>20) // Количество выводимых последний рег-ий.
		 	{break;}
		 $i++;
		 $dat=date("d.m.y H:i",$row['reg_time']);
		 $bname='';
		 switch($row['banner']) {
			case "1": $bname="Легендарный (468х60)"; break;
			case "2": $bname="Тяжесть карающего топора (468х60)"; break;
			case "3": $bname="На работе и дома (468х60)"; break;
			case "4": $bname="Приключения ждут (468х60)"; break;
			case "5": $bname="Лучшая игра (468х60)"; break;
			case "6": $bname="Ты нам нужен (728х90)"; break;
			case "7": $bname="За кружкой эля (728х90)"; break;
			case "8": $bname="Тяжесть карающего топора (728х90)"; break;
			case "9": $bname="На работе и дома (728х90)"; break;
			case "10": $bname="Приключения ждут (728х90)"; break;
			case "11": $bname="Легендарный (120х300)"; break;
			case "12": $bname="Лучшая игра (120х300)"; break;
			case "13": $bname="Полоска 1 (350х19)"; break;
			case "14": $bname="Полоска 2 (350х19)"; break;
			
	case "15": $bname="oldbk_240_400_03.swf"; break;
	case "17": $bname="oldbk_240_400_01.jpg"; break;

	case "18": $bname="oldbk_240_400_01.swf"; break;
	case "19": $bname="oldbk_240_400_02.gif"; break;
	
	
	case "20": $bname="oldbk_240_400_03.gif"; break;
	case "21": $bname="oldbk_240_400_04.swf"; break;
	
	
		
	case "22": $bname="oldbk_240_400_05.gif"; break;	
	case "16": $bname="oldbk_120_240_01.gif"; break;
	

	case "23": $bname="oldbk_728_90_02.gif"; break;	
	case "24": $bname="oldbk_728_90_02.swf"; break;
	
	case "26": $bname="oldbk_120_240_02.gif"; break;
	case "28": $bname="oldbk_240_400_04.gif"; break;
	case "29": $bname="oldbk_728_90_03.gif"; break;		
	case "30": $bname="oldbk_468_60_01.gif"; break;		
	case "32": $bname="oldbk_468_60_02.gif"; break;					
	case "33": $bname="oldbk_468_60_03.gif"; break;


	case "25": $bname="oldbk_120_240_01.swf"; break;
	case "27": $bname="oldbk_240_400_02.swf"; break;
	case "31": $bname="oldbk_468_60_01.swf"; break;					
	
	case "34": $bname="300x250_04.swf"; break;			
		
	case "43": $bname="240x400.swf"; break;
	
			}
 		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;$dat&nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>".nick33($row['uid'])." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['lvl']." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['ip']." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;<a style='padding-right: 0px; padding-left: 0px' href=index.php?showpartner={$row[pid]}>".$row['plogin']."</a></td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['banner']." {$bname} </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=http://".$row['from_site']." target=_blank style='padding-left: 1px; padding-right: 1px;'>".$row['from_site']."</a> </td>";
		 if ($row['fraud']==0)
		 	{$o.= "<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=index.php?showregs=1&fraud=".$row['uid']." style='padding-left: 1px; padding-right: 1px;'>Не учитывать</a> </td>";}
		 else
		 	{$o.= "<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=index.php?showregs=1&unfraud=".$row['uid']." style='padding-left: 1px; padding-right: 1px;'>Учитывать</a> </td>";}
		 $o.= "</tr>";
		}
	 $o.= "</table>";
	}
else
	{
//echo $f_date."----".$t_date;
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$f_date,$mt);
	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$t_date,$mt);
	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3])+86399;

////
	 if ($one_partner!='')	{$add=" AND partners_users.partner='{$one_partner}'";}
$quant[0]=mysql_num_rows(mysql_query("SELECT partners_users.*,users.id AS uid,users.login,users.level as lvl,partners.id AS pid,partners.login AS plogin 
	 FROM `partners_users`,oldbk.`users`,`partners` 
	 WHERE users.id=partners_users.id AND users.id_city=0 AND partners.id=partners_users.partner 
	 AND partners_users.reg_time > {$start} AND partners_users.reg_time < {$end} {$add} 
	 UNION
	 SELECT partners_users.*,users.id AS uid,users.login,users.level as lvl,partners.id AS pid,partners.login AS plogin 
	 FROM `partners_users`,avalon.`users`,`partners` 
	 WHERE users.id=partners_users.id AND users.id_city=1 AND partners.id=partners_users.partner 
	 AND partners_users.reg_time > {$start} AND partners_users.reg_time < {$end} {$add} 
	 {$sort};"));

$all_reg=$quant[0];
if (is_int(intval($_REQUEST['page'])))
	{
	 $page_num=intval($_REQUEST['page']);
	 if ($page_num==0) {$page_num=1;}
	}
else	{$page_num=1;}
$start_id=($page_num-1)*50; // Кол-во на странице
////	 

$QQL="SELECT partners_users.*,users.id AS uid,users.login,users.level as lvl,partners.id AS pid,partners.login AS plogin 
	 FROM `partners_users`,oldbk.`users`,`partners` 
	 WHERE users.id=partners_users.id AND users.id_city=0 AND partners.id=partners_users.partner 
	 AND partners_users.reg_time > {$start} AND partners_users.reg_time < {$end} {$add} 
	 UNION
	 SELECT partners_users.*,users.id AS uid,users.login,users.level as lvl,partners.id AS pid,partners.login AS plogin 
	 FROM `partners_users`,avalon.`users`,`partners` 
	 WHERE users.id=partners_users.id AND users.id_city=1 AND partners.id=partners_users.partner 
	 AND partners_users.reg_time > {$start} AND partners_users.reg_time < {$end} {$add} 
	 {$sort} LIMIT {$start_id}, 50;";
  	 $data=mysql_query($QQL);
  	 //echo $QQL;
//	 $all_reg=mysql_num_rows($data);
	 $o.= "<b>Регистрации ($f_date - $t_date):</b><br>Всего регистраций: $all_reg
	<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
	 <tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=date&pr={$one_partner}>Дата</a>
	 {$r1}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=nick&pr={$one_partner}>Ник</a>
	 {$r3}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=lvl&pr={$one_partner}>lvl</a>
	 {$r4}&nbsp;</td>

	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=&pr={$one_partner}>SUM</a>
	 {$r44}&nbsp;</td>
	 
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
 	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=ip&pr={$one_partner}>IP</a>
	 {$r5}&nbsp;</td>
	  <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=partner&pr={$one_partner}>Партнер</a>
	  {$r6}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=banner&pr={$one_partner}>Баннер</a>
	 {$r7}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
	 <a style='padding-left: 0px; padding-right: 0px;' href=index.php?showregs=1&f_date={$f_date}&t_date={$t_date}&sort=site&pr={$one_partner}>Сайт</a>
	 {$r8}&nbsp;</td>
	 <td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Фрод&nbsp;</td>
	 </tr>";
	 while ($row=mysql_fetch_array($data))
		{	 
		 $bname='';
		 switch($row['banner']) {
			case "1": $bname="Легендарный (468х60)"; break;
			case "2": $bname="Тяжесть карающего топора (468х60)"; break;
			case "3": $bname="На работе и дома (468х60)"; break;
			case "4": $bname="Приключения ждут (468х60)"; break;
			case "5": $bname="Лучшая игра (468х60)"; break;
			case "6": $bname="Ты нам нужен (728х90)"; break;
			case "7": $bname="За кружкой эля (728х90)"; break;
			case "8": $bname="Тяжесть карающего топора (728х90)"; break;
			case "9": $bname="На работе и дома (728х90)"; break;
			case "10": $bname="Приключения ждут (728х90)"; break;
			case "11": $bname="Легендарный (120х300)"; break;
			case "12": $bname="Лучшая игра (120х300)"; break;
			case "13": $bname="Полоска 1 (350х19)"; break;
			case "14": $bname="Полоска 2 (350х19)"; break;
			
	case "15": $bname="oldbk_240_400_03.swf"; break;
	case "17": $bname="oldbk_240_400_01.jpg"; break;

	case "18": $bname="oldbk_240_400_01.swf"; break;
	case "19": $bname="oldbk_240_400_02.gif"; break;
	
	
	case "20": $bname="oldbk_240_400_03.gif"; break;
	case "21": $bname="oldbk_240_400_04.swf"; break;
	
	
		
	case "22": $bname="oldbk_240_400_05.gif"; break;	
	case "16": $bname="oldbk_120_240_01.gif"; break;
	

	case "23": $bname="oldbk_728_90_02.gif"; break;	
	case "24": $bname="oldbk_728_90_02.swf"; break;
	
	case "26": $bname="oldbk_120_240_02.gif"; break;
	case "28": $bname="oldbk_240_400_04.gif"; break;
	case "29": $bname="oldbk_728_90_03.gif"; break;		
	case "30": $bname="oldbk_468_60_01.gif"; break;		
	case "32": $bname="oldbk_468_60_02.gif"; break;					
	case "33": $bname="oldbk_468_60_03.gif"; break;


	case "25": $bname="oldbk_120_240_01.swf"; break;
	case "27": $bname="oldbk_240_400_02.swf"; break;
	case "31": $bname="oldbk_468_60_01.swf"; break;					
	
	case "34": $bname="300x250_04.swf"; break;
	case "43": $bname="240x400.swf"; break;
			
			}
		 $dat=date("d.m.Y H:i",$row['reg_time']);
 		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;$dat&nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>".nick33($row['uid'])." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['lvl']." </td>
 		 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ";
 		 $sm=mysql_fetch_array(mysql_query("select sum(ekr) payekr from dilerdelo where owner='{$row[login]}' and dilername NOT LIKE 'auto%' "));
 		 $o.= $sm[payekr];
 		 $o.= " </td>
 		 <td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['ip']." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a style='padding-right: 0px; padding-left: 0px' href=index.php?showpartner={$row[pid]}>".$row['plogin']."</a> </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['banner']." {$bname} </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=http://".$row['from_site']." target=_blank style='padding-left: 1px; padding-right: 1px;'>".$row['from_site']."</a> </td>";
		 if ($row['fraud']==0)
		 	{$o.= "<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=index.php?showregs=1&fraud=".$row['id']." style='padding-left: 1px; padding-right: 1px;'>Не учитывать</a> </td>";}
		 else
		 	{$o.= "<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a href=index.php?showregs=1&unfraud=".$row['id']." style='padding-left: 1px; padding-right: 1px;'>Учитывать</a> </td>";}
		 $o.= "</tr>";
		}
	 $o.= "</table>";
	}

$pages=ceil($quant[0]/50); // Кол-во на странице
$o.= "<br><center>...";
for($i=1; $i<$pages+1; $i++)
	{
	 if ($i>$page_num-10 and $i<$page_num+10)
		{
		 if ($i==$page_num)
			{$o.= " <b>$i</b>";}
		 else	{$o.= " <a href=?page=$i&pr={$one_partner}&showregs=1&f_date={$f_date}&t_date={$t_date}&sort={$psort}>$i</a>";}
		}
	}
$o.= "...</center>";



if ($f_date!='' and $t_date!='')
	{$now=$t_date; $before=$f_date;}
else
	{$now=date("d.m.y"); $before=date("d.m.y",(time()-2592000));}
$o.= "<br><form method=GET action=index.php>
<input type=hidden name=showregs value=1>
<input type=hidden name=pr value='{$one_partner}'>
<input type='text' name='f_date' class='enter1' value='$before' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id='calendar-inputField1'/>
<button id=\"calendar-trigger1\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger1\",
        inputField : \"calendar-inputField1\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='text' name='t_date' class='enter1' value='$now' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id='calendar-inputField2'/>
<button id=\"calendar-trigger2\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger2\",
        inputField : \"calendar-inputField2\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='submit' class='enter1' value='Смотреть' style='width: 70px; height:18px;'/>
</form>
";
	 render_text_block($o);
}

function AdminTransfers($nick,$f_date,$t_date)	{
if ($f_date=='' or $t_date=='')
	{$end=time(); $start=$end-2592000; $t_date=date("d.m.y"); $f_date=date("d.m.y",$start);}
else
	{
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$f_date,$mt);
	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$t_date,$mt);
	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3])+86399;	
	}
if ($nick!='')	{$addon=" AND partners.login='{$nick}' ";}
$data=mysql_query("SELECT partners_delo.*,users.id AS uid,users.login,partners.id AS pid,partners.login AS plogin, partners.percent 
FROM `partners_delo`,oldbk.`users`,`partners` 
WHERE users.id=partners_delo.owner_id AND partners.id=partners_delo.partner_id {$addon}
AND partners_delo.transfer_time > {$start} AND partners_delo.transfer_time < {$end} AND partners_delo.bank!='999999999' 

ORDER BY 1 DESC;");



$o= "<b>Переводы ($f_date - $t_date):</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
<tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Дата&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Покупатель&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Сумма&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Партнер&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Получено&nbsp;</td>
</tr>";
while ($row=mysql_fetch_array($data))
	{	 
	 $getted=round($row['ekr']/100*$row['percent'],2);
	 $dat=date("d.m.Y H:i",$row['transfer_time']);
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;$dat&nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".nick33($row['uid'])." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$row['ekr']." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; <a style='padding-right: 0px; padding-left: 0px' href=index.php?showpartner={$row[pid]}>".$row['plogin']."</a> </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$getted." </td></tr>";
	}
$now=date("d.m.y");
$before=date("d.m.y",(time()-2592000));
if ($f_date=='') {$f_date=$before;}
if ($t_date=='') {$t_date=$now;}
$o.= "</table><br><form method=post action=index.php>
<input type=hidden name=payments value=1>
<input type='text' name='nick' class='enter1' value='$nick' style='width: 100px; padding-left: 2px; height:18px; padding-bottom: 0px;'/>

<input type='text' name='f_date' class='enter1' value='$f_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id='calendar-inputField1'/>
<button id=\"calendar-trigger1\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger1\",
        inputField : \"calendar-inputField1\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='text' name='t_date' class='enter1' value='$t_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id=calendar-inputField2/>
<button id=\"calendar-trigger2\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger2\",
        inputField : \"calendar-inputField2\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='submit' class='enter1' value='Смотреть' style='width: 70px; height:18px;'/>
</form>
";

 render_text_block($o);
}
function ShowSites($f_date,$t_date)	{

if ($f_date=='' or $t_date=='')
	{$end=time(); $start=0;}
else
	{
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$f_date,$mt);
	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$t_date,$mt);
	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3])+86399;	
	}
$data=mysql_query("SELECT * from `partners_users` WHERE `partner`='{$_SESSION['partnerid']}' AND `reg_time` > {$start} AND `reg_time` < {$end};");
while ($row=mysql_fetch_array($data))
	{
	 $sites[$row['from_site']]+=1;
	 $regs[$row['id']]=$row['from_site'];
	}
foreach ($regs as $id => $st)
	{
	 $data=mysql_query("SELECT * from `partners_delo` WHERE `owner_id`='{$id}' AND `transfer_time` > {$start} AND `transfer_time` < {$end};");
	 while ($row=mysql_fetch_array($data))
		{
		 $ekr[$st]=$ekr[$st]+$row['ekr'];
		}
	}
$o= "<b>Сайты ($f_date - $t_date):</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
<tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Сайт&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Регистраций&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Сумма екр &nbsp;</td>
</tr>";
foreach ($sites as $site => $rgstr)
	{
	 if ($ekr[$site]=='') {$ekr[$site]=0;}
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;$site&nbsp;</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$rgstr." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$ekr[$site]." </td></tr>";
	}
$now=date("d.m.y"); $before=date("d.m.y",(time()-2592000));
if ($f_date=='') {$f_date=$before;}
if ($t_date=='') {$t_date=$now;}
$o.= "</table><br><form method=post action=index.php>
<input type=hidden name=showsites value=1>
<input type='text' name='f_date' class='enter1' value='$f_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id='calendar-inputField1'/>
<button id=\"calendar-trigger1\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger1\",
        inputField : \"calendar-inputField1\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='text' name='t_date' class='enter1' value='$t_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id=calendar-inputField2/>
<button id=\"calendar-trigger2\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger2\",
        inputField : \"calendar-inputField2\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='submit' class='enter1' value='Смотреть' style='width: 70px; height:18px;'/>
</form>
";

 render_text_block($o);
}

function nick33 ($id) {

	$user = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$id}' LIMIT 1;"));
	if ($user[id_city]==1)
	{
	$user = mysql_fetch_array(mysql_query("SELECT * FROM avalon.`users` WHERE `id` = '{$id}' LIMIT 1;"));
	}


	if (($user[0]) )
		 {

		$mm .= "<img src=\"https://i.oldbk.com/i/align_".($user['align']>0 ? $user['align']:"0").".gif\">";
		if ($user['klan'] <> '') {
			$mm .= '<img title="'.$user['klan'].'" src="https://i.oldbk.com/i/klan/'.$user['klan'].'.gif">'; }
			$mm .= "<B>{$user['login']}</B> [{$user['level']}]<a href=http://capitalcity.oldbk.com/inf.php?{$user['id']} style='padding-left: 0px; padding-right: 0px;' target=_blank><IMG SRC=http://i.oldbk.com/i/inf.gif WIDTH=12 HEIGHT=11 ALT=\"Инф. о {$user['login']}\"></a>";
	}
	return $mm;
}

function AdminSites($f_date,$t_date, $sort)	{

if ($sort=='') {$r1=' •';}
if ($sort=='regs') {$r2=' •';}
if ($sort=='ekr') {$r3=' •';}


if ($f_date=='' or $t_date=='')
	{$end=time(); $start=$end-2592000; $t_date=date("d.m.y"); $f_date=date("d.m.y",$start);}
else
	{
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$f_date,$mt);
	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$t_date,$mt);
	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3])+86399;	
	}
$data=mysql_query("SELECT partners_users.*,partners.id AS pid,partners.login AS plogin
from `partners_users`, `partners`
WHERE partners_users.reg_time > {$start} AND partners_users.reg_time < {$end} AND partners_users.partner=partners.id;");

$o="<b>Сайты ($f_date - $t_date):</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
<tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
<a href=index.php?showsites=1&f_date={$f_date}&t_date={$t_date} style='padding-left: 0px; padding-right: 0px;'>Сайт</a>
&nbsp;{$r1}</td>
<!--<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Партнер&nbsp;</td>-->
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
<a href=index.php?showsites=1&f_date={$f_date}&t_date={$t_date}&sort=regs style='padding-left: 0px; padding-right: 0px;'>Регистраций</a>
&nbsp;{$r2}</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;
<a href=index.php?showsites=1&f_date={$f_date}&t_date={$t_date}&sort=ekr style='padding-left: 0px; padding-right: 0px;'>Сумма екр</a>
&nbsp;{$r3}</td>
</tr>";

while ($row=mysql_fetch_array($data))
	{
	 $sites[$row['from_site']]+=1;
	 $regs[$row['id']]=$row['from_site'];
	 $part[$row['from_site']]['plogin']=$row['plogin'];
 	 $part[$row['from_site']]['pid']=$row['pid'];
	}
foreach ($regs as $id => $st)
	{
	 $data=mysql_query("SELECT * from `partners_delo` WHERE `owner_id`='{$id}' AND `transfer_time` > {$start} AND `transfer_time` < {$end};");
	 while ($row=mysql_fetch_array($data))
		{
		 $ekr[$st]=$ekr[$st]+$row['ekr'];
		}
	}
if ($sort!='ekr')	
	{
	 if ($sort=='regs')	{arsort($sites);}
	 foreach ($sites as $site => $rgstr)
		{
		 if ($ekr[$site]=='') {$ekr[$site]=0;}
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;<a style='padding-right: 0px; padding-left: 0px' href=http://{$site}>$site</a>&nbsp;</td><!--<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'> <a style='padding-right: 0px; padding-left: 0px' href=index.php?showpartner={$part[$site]['pid']}>{$part[$site]['plogin']}</a>&nbsp;</td>--><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$rgstr." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$ekr[$site]." </td></tr>";
		}
	}
else
	{
	 arsort($ekr);
	 foreach ($ekr as $site => $money)
		{
		 if ($sites[$site]=='') {$sites[$site]=0;}
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;<a style='padding-right: 0px; padding-left: 0px' href=http://{$site}>$site</a>&nbsp;</td><!--<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'> <a style='padding-right: 0px; padding-left: 0px' href=index.php?showpartner={$part[$site]['pid']}>{$part[$site]['plogin']}</a>&nbsp;</td>--><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$sites[$site]." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$money." </td></tr>";	
		 unset($sites[$site]);	 
		}
	 arsort($sites);
	 foreach ($sites as $site => $rgstr)
		{
		 if ($ekr[$site]=='') {$ekr[$site]=0;}
		 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp;<a style='padding-right: 0px; padding-left: 0px' href=http://{$site}>$site</a>&nbsp;</td><!--<td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'> <a style='padding-right: 0px; padding-left: 0px' href=index.php?showpartner={$part[$site]['pid']}>{$part[$site]['plogin']}</a>&nbsp;</td>--><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$rgstr." </td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>&nbsp; ".$ekr[$site]." </td></tr>";
		}	
	}
$now=date("d.m.y"); $before=date("d.m.y",(time()-2592000));
if ($f_date=='') {$f_date=$before;}
if ($t_date=='') {$t_date=$now;}
$o.= "</table><br><form method=post action=index.php>
<input type=hidden name=showsites value=1>
<input type='text' name='f_date' class='enter1' value='$f_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id='calendar-inputField1'/>
<button id=\"calendar-trigger1\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger1\",
        inputField : \"calendar-inputField1\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='text' name='t_date' class='enter1' value='$t_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id=calendar-inputField2/>
<button id=\"calendar-trigger2\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger2\",
        inputField : \"calendar-inputField2\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='submit' class='enter1' value='Смотреть' style='width: 70px; height:18px;'/>
</form>
";
	 render_text_block($o);
}

function AdminPayments($f_date,$t_date)	{
if ($f_date=='' or $t_date=='')
	{$end=time(); $start=$end-2592000; $t_date=date("d.m.y"); $f_date=date("d.m.y",$start);}
else
	{
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$f_date,$mt);
	 $start=mktime(0,0,0,$mt[2],$mt[1],$mt[3]);
	 preg_match("/^(.*?)\.(.*?)\.(.*?)$/",$t_date,$mt);
	 $end=mktime(0,0,0,$mt[2],$mt[1],$mt[3])+86399;	
	}
//$data=mysql_query("SELECT partners_delo.*,partners.id AS pid,partners.login AS plogin
//from `partners_delo`, `partners`
//WHERE partners_delo.transfer_time > {$start} AND partners_delo.transfer_time < {$end} AND partners_delo.partner_id=partners.id AND partners_delo.bank='999999999';");
$data=mysql_query("select partners_delo.*, u1.login as fromLogin, u2.login as toLogin 
from partners_delo join partners u1 on (u1.id=partners_delo.dealer_id) join partners u2 on (u2.id=partners_delo.partner_id)
WHERE partners_delo.transfer_time > {$start} AND partners_delo.transfer_time < {$end} AND partners_delo.partner_id=partners.id AND partners_delo.bank='999999999';");

$o= "<b>Выплаты ($f_date - $t_date):</b>
<table cellspacing=0 cellpadding=0 style='border: 1px solid #85755d;' width=100%>
<tr><td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Дата&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Кто выплатил&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Получатель&nbsp;</td>
<td style='background-color: #85755d; color: #f6e7c6;'>&nbsp;Сумма</td>
</tr>";
while ($row=mysql_fetch_array($data))
	{
	 $dat=date("d.m.Y H:i",$row['transfer_time']);
//	 $who=mysql_fetch_array(mysql_query("SELECT id, login FROM partners WHERE id='{$row['dealer_id']}' LIMIT 1;"));
	 $o.= "<tr onmouseover=\"this.className='selected';\" onmouseout=\"this.className='';\"><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>$dat</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['fromLogin']}</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['toLogin']}</td><td style='border-style: solid; border-color: #A5957d; border-width: 1px 0px 0px 0px;'>{$row['ekr']}$</td>";
	}
$now=date("d.m.y"); $before=date("d.m.y",(time()-2592000));
if ($f_date=='') {$f_date=$before;}
if ($t_date=='') {$t_date=$now;}
$o.= "</table><br><form method=post action=index.php>
<input type=hidden name=stat value=1>
<input type='text' name='f_date' class='enter1' value='$f_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id='calendar-inputField1'/>
<button id=\"calendar-trigger1\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger1\",
        inputField : \"calendar-inputField1\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='text' name='t_date' class='enter1' value='$t_date' style='width: 50px; padding-left: 2px; height:18px; padding-bottom: 0px;' id=calendar-inputField2/>
<button id=\"calendar-trigger2\" class='enter1'>...</button>
<script>
    Calendar.setup({
        trigger    : \"calendar-trigger2\",
        inputField : \"calendar-inputField2\",
		dateFormat : \"%d.%m.%y\",
		onSelect   : function() { this.hide() }
    });
	document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
</script>
<input type='submit' class='enter1' value='Смотреть' style='width: 70px; height:18px;'/>
</form>
";

 render_text_block($o);
}

// alg.php от Бреда. Юзаю локально ибо почему-то не дает поставить куку...

function in_smdp($input)
{
// не менять никогда
	$c = xxtea_encrypt($input, 'B5cHAfdsFz14x55');
	return mysql_real_escape_string($c);

}
// пока не используется
function out_smdp($input)
{

$c= xxtea_decrypt($input, 'A2b3AGdHjx00x11');
return $c;
}





function long2str($v, $w) {
    $len = count($v);
    $n = ($len - 1) << 2;
    if ($w) {
        $m = $v[$len - 1];
        if (($m < $n - 3) || ($m > $n)) return false;
        $n = $m;
    }
    $s = array();
    for ($i = 0; $i < $len; $i++) {
        $s[$i] = pack("V", $v[$i]);
    }
    if ($w) {
        return substr(join('', $s), 0, $n);
    } else {
        return join('', $s);
    }
}

function str2long($s, $w) {
    $v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
    $v = array_values($v);
    if ($w) {
        $v[count($v)] = strlen($s);
    }
    return $v;
}

function int32($n) {
    while ($n >= 2147483648) $n -= 4294967296;
    while ($n <= -2147483649) $n += 4294967296;
    return (int)$n;
}

function xxtea_encrypt($str, $key) {
    if ($str == "") {
        return "";
    }
    $v = str2long($str, true);
    $k = str2long($key, false);
    if (count($k) < 4) {
        for ($i = count($k); $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;

    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = 0;
    while (0 < $q--) {
        $sum = int32($sum + $delta);
        $e = $sum >> 2 & 3;
        for ($p = 0; $p < $n; $p++) {
            $y = $v[$p + 1];
            $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$p] = int32($v[$p] + $mx);
        }
        $y = $v[0];
        $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $z = $v[$n] = int32($v[$n] + $mx);
    }
    return long2str($v, false);
}

function xxtea_decrypt($str, $key) {
    if ($str == "") {
        return "";
    }
    $v = str2long($str, false);
    $k = str2long($key, false);
    if (count($k) < 4) {
        for ($i = count($k); $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;

    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = int32($q * $delta);
    while ($sum != 0) {
        $e = $sum >> 2 & 3;
        for ($p = $n; $p > 0; $p--) {
            $z = $v[$p - 1];
            $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[$p] = int32($v[$p] - $mx);
        }
        $z = $v[$n];
        $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $y = $v[0] = int32($v[0] - $mx);
        $sum = int32($sum - $delta);
    }
    return long2str($v, true);
}


?>