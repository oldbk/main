<?php
///ini_set("display_errors",1);
//error_reporting(E_ALL);
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
	$miniBB_gzipper_encoding = 'gzip';
}
if (isset($miniBB_gzipper_encoding)) {
	ob_start();
}
function percent($a, $b) {
	$c = $b/$a*100;
	return $c;
}

session_start();

if (!($_SESSION['uid'] >0)) {
	echo "<script>top.window.location='http://capitalcity.oldbk.com/index.php?exit=0.560057875997465.{$_SESSION['uid']}.000.{$_COOKIE['battle']}'</script>";
	die();
}

header("Cache-Control: no-cache");
header('Content-Type: text/html; charset=windows-1251');
require_once 'connect.php';
require_once 'memcache.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
<title>Бойцовский клуб oldbk.com - Обновления</title>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<script>
document.domain = "oldbk.com";
</script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=e2e0e0>
<H3  style="margin-bottom: 0px;">Бойцовский клуб <a href="http://oldbk.com/">oldbk.com</a></H3>
<H4  style="margin-bottom: 0px;">Обновления:</H4>
<?		
		//$getdata=mysql_query("select * from oldbk.new_updates where hide=0 order by top desc , cdate limit 50; ");
		$getdata=mysql_query_cache("select * from oldbk.new_updates where hide=0 order by top desc , cdate DESC",false,120);
		if (count($getdata) > 0) 
		{
			while(list($k,$row) = each($getdata)) 
			{
				$phpdate = strtotime($row['cdate']);
					echo "<span class=date>".date( 'd-m-Y H:i:s', $phpdate )."</span> <span class=stext id=news".$row['id'].">".$row['message']."</span><br>";
			}			
		}
		

		

?>
</BODY>
</HTML>
<?
if (isset($miniBB_gzipper_encoding)) 
{
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
die();
}
?>