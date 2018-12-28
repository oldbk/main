<?
include "connect.php";
if  ( ((int)($_POST[uid])>0) AND ($_POST[hash])  )
	{
	$uid=(int)($_POST[uid]);
	/*
	склонность
	уровень
	гейм ид
	клан
	*/
	$get_usr=mysql_fetch_assoc(mysql_query("SELECT id,login, align, level, sid, klan, block FROM `users` WHERE `id` = '{$uid}' LIMIT 1;"));
	if ($get_usr[id]>0)
		{
		$key='7XttXsFvpOmUQebCbgMGOpUXG0QI';
		$hash=md5($get_usr[id]."/".$get_usr[sid]."/".$key);
			if ($hash===$_POST[hash])
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
	}
	else
	{
			$err[answ]='false';
			echo json_encode($err);
	}
?>
