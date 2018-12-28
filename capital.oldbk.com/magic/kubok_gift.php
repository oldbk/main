<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

//print_r($_GET);

$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = '".$_GET['use']."';"));

if((int)$rowm['magic'] > 0)
{
	$magic = magicinf($rowm['magic']);
}


if($rowm[id]>0 && $magic[id]>0)
{	echo "<font color=red><B>";


		if ($user['battle']>0)
		{
			echo "Не в бою!";
		}
		else
		{
			$sql="insert into oldbk.inventory
			(name,				maxdur,cost,         owner,    img,       isrep, type,massa,magic,prototype,otdel,    add_time,           present_text,   present, nlevel)
			values
			('Подарок -Благословение Ангелов-',1,	  0,'".$user[id]."','gift_hb_126.gif', 0,  12,    5,  2000,    10000,    6,'".time()."','Благословение Ангелов','Искушение',7),
			('Телепорт',                      10,     0,'".$user[id]."','teleport_e.gif',  0,   12,  0.2,10002,   110002,    5,'".time()."','','Искушение',			7),
			('Невидимость',			   5,     0,'".$user[id]."','hidden_e.gif',    0,   12,  1.0,   97,     301,     5,'".time()."','','Искушение',			7),
			('Заступиться',			   5,	  0,'".$user[id]."','helpbattle_e.gif',0,   12,  1.0, 5353,     352,     5,'".time()."','','Искушение',			7);";
			
			mysql_query($sql);
		        mysql_query("UPDATE oldbk.inventory SET magic='' WHERE id='".$rowm[id]."' AND owner=".$user[id].";");
		      
		       echo 'Внимание!</font> Вы получили свитки "-Благословение Ангелов-", "Телепорт", "Невидимость", "Заступиться"';
		}

	echo "</B></FONT>";
}


?>
