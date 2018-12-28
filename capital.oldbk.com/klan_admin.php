<?php

	session_start();

	include "connect.php";	
	include "functions.php";
	if (!ADMIN)
	{
		die('Страница не найдена');
	}
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
	//print_r($_POST);
	
	if((isset($_POST[ok]) || isset($_POST[off])) && $_POST[id]>0 )
	{
		$kl=mysql_fetch_assoc(mysql_query('SELECT * FROM topsites.top WHERE ban=1 AND reg_flag=1 AND memberid="'.(int)$_POST[id].'" limit 1'));
		if($kl[memberid])
		{
		 //добавить главу!!!!!!!!!!
			$t_klan=mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.clans WHERE short="'.$kl[klan].'" limit 1'));
			
			$vozm=unserialize($t_klan[vozm]);
			if(isset($_POST[ok]))
			{
				mysql_query("UPDATE topsites.top set ban=0, reg_flag=0 WHERE memberid='".$kl[memberid]."'");	
				if(mysql_affected_rows()>0)
				{
					mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$t_klan[glava]."','','<font color=red><b></font> Ваш клан добавлен в рейтинг сайтов. Не забудьте установить на сайте ваш счетчик ОлдБК. В противном случае, ваш сайт не будет участвовать в рейтинге. Код счетчика можно посмотреть в панели управления кланом.</b>')");						
					foreach($vozm as $u=>$val)
				 	{
				 		if($val[0]==1)
				 		{
				 			mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$u."','','<font color=red><b></font> Ваш клан добавлен в рейтинг сайтов. Не забудьте установить на сайте ваш счетчик ОлдБК. В противном случае, ваш сайт не будет участвовать в рейтинге. Код счетчика можно посмотреть в панели управления кланом.</b>')");				
				 		}
				 	}
				}
							
			}
			else
			if(isset($_POST[off]))
			{
				mysql_query("DELETE FROM topsites.top WHERE memberid='".$kl[memberid]."' AND ban=1 AND reg_flag=1");
				if(mysql_affected_rows()>0)
				{
					if(isset($_POST[rison]))
		 			{
		 				$rison='Причина: '.mysql_escape_string($_POST[rison]);
		 			}
					mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$t_klan[glava]."','','<font color=red><b>Регистрация клана в рейтинге сайтов отклонена. </b></font>".$rison."  ')");	
					foreach($vozm as $u=>$val)
				 	{
				 		if($val[0]==1)
				 		{
				 			mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$u."','','<font color=red><b>Регистрация клана в рейтинге сайтов отклонена. </b></font>".$rison."  ')");				
				 		}
				 	}
				}
			}
		}
	}
	
	echo '<table>';
	$data=mysql_query('SELECT * FROM topsites.top WHERE ban=1 AND reg_flag=1 order by memberid desc');
	while($row=mysql_fetch_assoc($data))
	{
	 	echo ' <tr><td><a href="http://oldbk.com/encicl/klani/clans.php?clan='.$row[klan].'" target="_blank">'.$row[klan].'</a></td><td><a href="'.$row[url].'" target=_blank>'.$row[url].'</a></td><td>'.$row[sitename].  '</td><td>
	 	<form action=? method=post>
	 	<input type=submit value="Одобрить" name=ok> <input type=submit value="отклонить" name=off> причина:<input type=text name=rison><input type=hidden name=id value='.$row[memberid].'>
	 	</form>
	 	</td></tr>';
	}
	
	echo '</table>';


?>

