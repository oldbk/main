<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

//print_r($_GET);

$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));

if((int)$rowm['magic'] > 0)
{
	$magic = magicinf($rowm['magic']);
}

$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' and type = 102');

if (mysql_num_rows($q) > 0) {
	echo "У вас уже есть увеличение опыта!";
	return;
}

if($rowm[id]>0 && $magic[id]>0)
{	echo "<font color=red><B>";


		if ($user['battle']>0)
		{
			echo "Не в бою!";
		}
		else
		 {
				if(mysql_query("INSERT INTO effects SET type = 102, name = '100 опыта' , time = '".(time()+60*60*24)."', add_info=1,
					owner='".$_SESSION['uid']."';"))
				{
					mysql_query('update users set expbonus=expbonus+1 where id='.$_SESSION['uid'].';');
				}

			echo "Вы открыли подарок! В течение суток вы будете получать дополнительно +100% опыта!";
			$bet=1;
			$sbet = 1;
		 }

	echo "</B></FONT>";
}


?>
