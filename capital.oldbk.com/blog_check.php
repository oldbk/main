<?
include "connect.php";
if  (  ($_POST[game_id]!='')  AND ($_POST[key]=='7XttXsFvpOmUQebCbgMGOpUXG0QI')  )
	{
	/*
	склонность
	уровень
	гейм ид
	клан
	*/

	$gid=(int)($_POST[game_id]);

	$get_usr=mysql_fetch_assoc(mysql_query("SELECT id,login, align, level, klan, block, odate  FROM `users` WHERE `id` = '".$gid."' AND `pass`!='' AND bot=0 LIMIT 1;"));
	if ($get_usr[id]>0)
		{
			if ($get_usr['odate'] > (time()-60))
			{
			$get_usr['odate']='Online';
			}
			else
			{
			$get_usr['odate']='Offline';
			}


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
