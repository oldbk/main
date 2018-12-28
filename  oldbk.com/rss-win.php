<?
ob_start();
include "/www/oldbk.com/connect.php";
$linkrss="http://capitalcity.oldbk.com/news.php?topic=";

echo("<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\r\n");
echo("<rss version=\"2.0\">\r\n");
echo("<channel>\r\n\r\n");

echo("<title>Бойцовский Клуб - OLDBK.COM - новости RSS</title>\r\n");
echo("<link>http://oldbk.com</link>\r\n");
echo("<description>Новостная лента игры «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!</description>r\n");
echo("<copyright>© 2010—".date("Y")." «Бойцовский Клуб ОлдБК»</copyright>\r\n\r\n");

$query = "SELECT n. * FROM `news` AS n WHERE n.parent = 1 and (ISNULL(n.print_time) OR n.print_time<=NOW() )   ORDER BY id DESC limit 20";
$res = mysql_query($query);
while($item = mysql_fetch_array($res))
{
	$descr = "";
	$descr = stripslashes($item["text"]);
	$item["title"] = htmlspecialchars($item["topic"]);

	echo("\r\n<item>\r\n");
	echo("<title>".$item["title"]."</title>\r\n");
	echo("<link>".$linkrss.$item["id"]."</link>\r\n");
	echo("<description><![CDATA[".$descr."]]></description>\r\n");
	
	$pdate=explode(" ",$item["date"]);
	$ptime=explode(":",$pdate[1]);
	$pddate=explode(".",$pdate[0]);
	$out_date=date(DATE_RFC822, mktime($ptime[0], $ptime[1], 0, $pddate[1], $pddate[0], $pddate[2]));
	
	
	echo("<pubDate>".$out_date."</pubDate>\r\n");
	echo("</item>\r\n\r\n");
}
echo("</channel>\r\n");
echo("</rss>\r\n");
include "endhttps.php";
?>