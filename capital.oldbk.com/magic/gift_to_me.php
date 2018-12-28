<?
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}


	if($rowm['present']=='')
		{
							$rowm['duration']=-1; // заглушка чтоб не удалилась
							mysql_query("UPDATE oldbk.`inventory` SET duration=-1, maxdur=1, `present`='јноним', magic=0 , add_time='".time()."'  WHERE `id` = {$rowm['id']} LIMIT 1;");
							if (mysql_affected_rows() > 0) 	
								{
								echo "¬се прошло удачно! ѕодарок будет доставлен вам в течение минуты.";
								$sbet = 1;
								$bet = 1;
								}
		}
		else
			{
				echo "Ётот подарок уже вам подарен!";
			}

?>