<? 
header ("content-type: text/xml");

   include "/www/oldbk.com/connect.php";
   require_once('/www/oldbk.com/memcache.php');

		$query_curs=mysql_query_cache("select * from oldbk.variables where var='dollar' or var='euro' or var='grivna' or var='ekrkof' or var='ekrbonus'  or  var='grivna_wmu' ",false,360);

	while(list($k,$row) = each($query_curs)) 
	{
		if($row['var'] =='dollar') { $dollar = $row[value];}
		 else
		 	if($row['var'] =='euro')  { $euro = $row[value];} 
		 		else
		 			if($row['var'] =='grivna') { $grivna = $row[value];}
	 					 elseif($row['var'] =='ekrkof') { $ekrkof = $row[value];}
 					  		elseif($row['var'] =='ekrbonus') { $ekrbonus = $row[value];} 	
						 		elseif($row['var'] =='grivna_wmu') { $grivna_wmu = $row[value];} 					  					 							  					  		
 					  						 
	}
	
echo "<bank refresh=\"".time()."\">\n";
$EU=$euro;
echo "<cur name=\"EUR\">".(ceil($EU/0.01) * 0.01)."</cur>\n";
//$RU=(round($dollar,3)+round($dollar*0.085,3));
$RU=round($dollar,3);
echo "<cur name=\"RUR\">".(ceil($RU/0.01) * 0.01)."</cur>\n";
//echo "<cur name=\"UAH\">".$grivna_wmu."</cur>\n";
echo "<cur name=\"USD\">".$ekrkof."</cur>\n";
//echo "<cur name=\"BONUS\">".$ekrbonus."</cur>\n";
echo "<cur name=\"KR\">200</cur>\n";
echo "</bank>";
?>
				