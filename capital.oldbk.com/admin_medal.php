<?
session_start();
/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<title>Вручение медалек</title>
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<style>
	.row {
		cursor:pointer;
	}
</style>
<SCRIPT>
function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}
</SCRIPT>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 ><br>
<h2> Вручение медалек</h2><br><br>
<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
	if (ADMIN)
	{
		echo "<form method=post action=\"?\">Вручить орден за защиту Проекта <br>
		<table><tr><td>Введите логин </td><td><input type='text' name='ordnick' value=''></td><td><input type='hidden' name='orden' value='ordadd'><input type=submit value='Вручить'></td></tr>";
		echo "</table></form>";

		if ($_POST['orden']) {
			if ($_POST['ordnick']) {
				$tar = mysql_fetch_array(mysql_query("SELECT `id`,`align` FROM `users` WHERE `login` = '{$_POST['ordnick']}' LIMIT 1;"));
				if ($tar['id']) {
					mysql_query("UPDATE oldbk.`users` SET `medals` = CONCAT('006;',`medals`) WHERE  `login` = '{$_POST['ordnick']}' ");
					mysql_query("UPDATE avalon.`users` SET `medals` = CONCAT('006;',`medals`) WHERE  `login` = '{$_POST['ordnick']}' ");					
					mysql_query("UPDATE angels.`users` SET `medals` = CONCAT('006;',`medals`) WHERE  `login` = '{$_POST['ordnick']}' ");					
					if ($user['sex'] == 1) {$action="вручил";}
					else {$action="вручила";}
					$mess="Ангел &quot;{$user['login']}&quot; $action орден &quot;За защиту Проекта!&quot; персонажу &quot;{$_POST['ordnick']}&quot; ";
					mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$tar['id']."','$mess','".time()."');");
					addch("<img src=i/magic/006.gif> $mess");
					print "<font color=red> Орден вручен!</font>";
				}
				else {
					print "<font color=red> Персонаж с таким ником не существует!</font>";
				}
			}
		}
	echo "<hr>";
	echo "<form method=post action=\"?\">Вручить орден/медаль <br>
		
	<a  onclick=\"showhide('ordens');\" style=\"cursor:pointer;\" >Паладинские</a>
		<div id=\"ordens\" style=\"display: none;\">
			<b>069</b><img src=\"http://i.oldbk.com/i/pal4.png\" > - За стойкость и верность Ордену Света! <br>
			<b>063</b><img src=\"http://i.oldbk.com/i/063.png\" > - За верность Ордену Света III степени! <br>
			<b>064</b><img src=\"http://i.oldbk.com/i/064.png\"> - За верность Ордену Света II степени! <br>
			<b>065</b><img src=\"http://i.oldbk.com/i/065.png\"> - За верность Ордену Света I степени! <br>
			<b>066</b><img src=\"http://i.oldbk.com/i/066.png\"> - За отличную работу в Ордене Света III степени! <br>
			<b>067</b><img src=\"http://i.oldbk.com/i/067.png\"> - За отличную работу в Ордене Света II степени!<br>
			<b>068</b><img src=\"http://i.oldbk.com/i/068.png\"> - За отличную работу в Ордене Света I степени!<br>
		</div>
		
		<table><tr><td>Введите логин </td><td><input type='text' name='ordnick1' value=''></td><td>Введите номер ордена </td><td><input type='text' name='ordnum1' value=''></td><td><input type='hidden' name='orden1' value='ordadd1'><input type=submit value='Вручить'></td></tr>";
		echo "</table></form>";

		if ($_POST['orden1']) {
			if ($_POST['ordnick1'] && $_POST['ordnum1']) {
				$tar = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `login` = '{$_POST['ordnick1']}' LIMIT 1;"));
				if ($tar['id']) {
					$tar=check_users_city_data($tar[id]);
					$med=explode('|',$tar[medals]);
					$mm=$med[0].$_POST['ordnum1'].';|'.$med[1];
					echo $mm.'<br>';
					
					$db_city[0]='oldbk.';
					$db_city[1]='avalon.';
					$db_city[2]='angels.';
					mysql_query("UPDATE ".$db_city[$tar[id_city]]."`users` SET `medals` = '".$mm."' WHERE  `login` = '{$_POST['ordnick1']}' ");
					print "<font color=red> Орден вручен!</font>";
				}
				else {
					print "<font color=red> Персонаж с таким ником не существует!</font>";
				}
			}
		}
	}
	else
	{
	echo "Доступ закрыт!";
	}
?>	
</body>
</html>
<?
/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////
?>