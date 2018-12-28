<?
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "/www/oldbk.com/connect.php";
include "/www/oldbk.com/ny_events.php";

include "/www/oldbk.com/commerce/cloud_api.php";	
include "/www/oldbk.com/commerce/price.php";
//include "/www/oldbk.com/commerce/new_delo.php";
include "/www/oldbk.com/commerce/func.php";
include "/www/oldbk.com/config_ko.php";


mysql_query("update art_prototype set art_status=1  where art_status=-1");

$get_ut_arts=mysql_query("select * from oldbk.art_prototype where art_status=1 order by battle DESC, art_zakdate DESC");
$user='Авто-Утверждение';
if (mysql_num_rows($get_ut_arts) > 0)
	{	 
	echo date("D M j G:i:s T Y"); 
	echo "\n";	
	
		while ($row=mysql_fetch_array($get_ut_arts))
		{
		echo "одобряеем {$row[id]} \n";
		mk_yes_forart($row[id]);
		 }
	
	echo "\n";
	}
	else
	{
	//echo "нет заказов";
	}
	   

?>