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
	$get_usr=mysql_fetch_assoc(mysql_query("SELECT id,login, align, level, sid, klan, block, odate FROM `users` WHERE `id` = '{$uid}' LIMIT 1;"));
	if ($get_usr[id]>0)
		{
		$key='aYHlGLIxCG1lI1aryrjNzI1YVHLbyMTI';
		$hash=md5($get_usr[id]."/".$get_usr[sid]."/".$key);
			if ($hash===$_POST[hash])
			{
			
			if ($get_usr['odate'] > (time()-60))
			{
			$get_usr['odate']='Online';
			}
			else
			{
			$get_usr['odate']='Offline';
			}
			
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