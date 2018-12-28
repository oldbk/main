<? 
//header ("content-type: text/xml");

   include "/www/oldbk.com/connect.php";
   require_once('/www/oldbk.com/memcache.php');

	$query=mysql_query_cache("select id, homepage from clans where homepage!=''",false,3600);

/*
	while(list($k,$row) = each($query)) 
	{
echo $row['homepage'];
echo "<br>";
	}
*/
echo json_encode($query);

	
?>
				