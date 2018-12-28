<?
include "connect.php";


$owner=(int)($_GET['owner']);
$bankid=(int)($_GET['bankid']);
$pass=($_GET['pass']);

if  ( ( $_GET[key]=='q3tyv57uwi4k5uiwk5juntgkswen54gkj34g')  AND ($owner>0) AND ($bankid>0) AND ($pass!='') )
	{
	$get_usr=mysql_query("select  id, ekr, cr from oldbk.bank where owner='{$owner}' AND `id`= '{$bankid}' AND `pass` = '".md5($pass)."';");
	
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
			$err[txt]='error login';
			echo json_encode($err);
		}
	}
	else
	{
			$err[answ]='false';
			echo json_encode($err);
	}	
?>