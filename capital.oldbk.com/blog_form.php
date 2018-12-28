<?
include "connect.php";
if  (  ($_POST[login]!='') AND ($_POST[password]!='')  AND ($_POST[key]=='7XttXsFvpOmUQebCbgMGOpUXG0QI')  )
	{
	/*
	склонность
	уровень
	гейм ид
	клан
	*/
	$_POST[login]=urldecode($_POST[login]);
	$_POST[password]=urldecode($_POST[password]);
	include "alg.php";
	$ff=in_smdp_new($_POST['password']);
	$get_usr=mysql_fetch_assoc(mysql_query("SELECT id,login, align, level, sid, klan, block FROM `users` WHERE `login` = '".mysql_real_escape_string($_POST['login'])."' AND `pass`!='' AND  `pass` = '".$ff."' LIMIT 1;"));
	if ($get_usr[id]>0)
		{
			$get_usr[sid]='true';
			$get_usr[login]=urlencode($get_usr[login]);
			$get_usr[klan]=urlencode($get_usr[klan]);
			echo json_encode($get_usr);

		}
		else
		{
			$err[answ]='false';
			echo json_encode($err);
		}
	}
	else
	{
			$err[answ]='false';
			echo json_encode($err);
	}
?>
