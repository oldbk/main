<?
if ($_GET[key]=='w5vm948ygu894w5uyvm84wr') //защита
{
include "/www/oldbk.com/connect.php";

	$_GET[teloid]=(int)$_GET[teloid];


	$get_all_items=mysql_query("select * from oldbk.effects where owner='{$_GET[teloid]}' and type in (11,12,13,14)");
	
	$tt[11]='легка€';
	$tt[12]='средн€€';
	$tt[13]='т€жела€';		
	$tt[14]='неизлечима€';	
	

	if (mysql_num_rows($get_all_items) >0 ) 
	{
 	echo "<effects>\n";	
	while($row = mysql_fetch_array($get_all_items)) 
	{
	echo "<travma type=\"$row[type]\" finishtime=\"$row[time]\" ";
	
	if ($row[sila]>0)
		{
		echo "  sila=\"-$row[sila]\" ";
		}

	if ($row[lovk]>0)
		{
		echo "  lovk=\"-$row[lovk]\" ";
		}
		
	if ($row[inta]>0)
		{
		echo "  inta=\"-$row[inta]\" ";
		}				
	
	if ($row[battle]>0)
		{
		echo "  battle=\"$row[battle]\" ";
		}	
	
	echo ">".htmlspecialchars(strip_tags($row['name']))."</travma>\n";
	}
 	echo "</effects>\n"; 	
 	}

}
?>