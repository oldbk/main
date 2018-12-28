<?
include "connect.php";
if  ( $_GET[key]=='7XttXsFvpOmUQebCbgMGOpUXG0QI')
	{

	$get_usr=mysql_query("select u.id, u.login, klan, align, level, dj.r1_access, dj.r2_access, dj.icq, dj.skype , nn.id_radio,nn.efir_type from r_djsn dj LEFT JOIN users u ON dj.id_dj=u.id LEFT JOIN r_djse nn ON dj.id_dj=nn.id_dj");

	while($row = mysql_fetch_assoc($get_usr))
		{
			$row['login']=urlencode($row['login']);
			$row['klan']=urlencode($row['klan']);
			$array[] = $row;
		 }

		echo json_encode($array);
	}
	else
	{
			$err[answ]='false';
			echo json_encode($err);
	}
?>
