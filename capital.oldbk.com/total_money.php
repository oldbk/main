<?
include "connect.php";
include "new_delo.php";

if ($_GET[key]=='q3tyv57uwi4k5uiwk5juntgkswen54gkj34g')
{
$sd=(int)$_GET['sdate'];
$fd=(int)$_GET['fdate'];
$t=(int)($_GET['ekr']);

	if ($t>0)
	{
	//екры

		if (($sd>0) AND ($fd>0) )
		{

		 $get_data=mysql_query("select SUM(sum_ekr) as sum , type , owner from new_delo where (type=1107 OR type=1106) and sdate>='{$sd}' and sdate<='{$fd}'   group by type, owner ");		
		}
		else
		{

		 $get_data=mysql_query("select SUM(sum_ekr) as sum  , type , owner from new_delo where (type=1107 OR type=1106) group by type, owner");
		 }		
	}
	else
	{

	//креды
		if (($sd>0) AND ($fd>0) )
		{
		 $get_data=mysql_query("select SUM(sum_kr) as sum , type , owner from new_delo where (type=1105 OR type=1104) and sdate>='{$sd}' and sdate<='{$fd}'   group by type, owner ");		
		}
		else
		{
		 $get_data=mysql_query("select SUM(sum_kr) as sum  , type , owner from new_delo where (type=1105 OR type=1104) group by type, owner");
		 }
	}

		
		if (mysql_num_rows($get_data))
		{	
			while($row = mysql_fetch_assoc($get_data)) 
			{
				if (($row['type']==1104) OR ($row['type']==1106) )
					{
					$row['type']='input';
					}
				elseif (($row['type']==1105) OR ($row['type']==1107) )
					{
					$row['type']='output';
					}
				
				$array[] = $row;
			 }
	  
			echo json_encode($array);
		}
		else
		{
		$err[answ]='false';
		echo json_encode($err);			
		}


} else {
	$err[answ]='false';
	echo json_encode($err);
}	
?>