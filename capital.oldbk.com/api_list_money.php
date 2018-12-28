<?
include "connect.php";


$owner=(int)($_GET['owner']);

if  ( ( $_GET[key]=='q3tyv57uwi4k5uiwk5juntgkswen54gkj34g')  AND ($owner>0)  )
	{
	$get_usr=mysql_query("select id, money from oldbk.users where id='{$owner}'");
	if (mysql_num_rows($get_usr))
		{
			while($row = mysql_fetch_assoc($get_usr)) 
			{
				$array[] = $row;
			 }
	  
			echo json_encode($array);
		}
		else
		{
			$err[answ]='false';
			$err[txt]='owner not found';			
			echo json_encode($err);		
		}
	
	}
	else
	{
			$err[answ]='false';
			echo json_encode($err);
	}	
?>