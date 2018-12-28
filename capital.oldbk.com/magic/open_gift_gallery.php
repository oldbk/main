<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");
$expdate=time()+60*60*24*15;
$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));
if((int)$rowm['magic'] > 0)
{
	$magic = magicinf($rowm['magic']);
}
if($rowm[id]>0 && $magic[id]>0)
{
	echo "<font color=red><B>";
	if ($user['battle']>0) {
		echo "Не в бою!";
	}
	else
	{
 		if($diff==1)
 		{
	 		if($user[sex]==1)
			{
	            $s='dm_';
			}
			else
			{
				$s='sn_';
			}
		}
        else
        {        	$s='ny_';        }
		for($s1=1;$s1<=$imf_count;$s1++)
		{
   			$names[]=$img_name.$s.$s1;
		}
	$add_pic=$names[mt_rand(0,count($names)-1)];
		$insql='INSERT INTO oldbk.gellery
		(owner,img,exp_date,otdel,dressed)
		VALUES
		("'.$user[id].'","'.$add_pic.'.gif",'.$expdate.','.$otdel.',"0");';
		   mysql_query($insql);
		echo "Вы открыли подарок и получили " . $txt;
		$bet=1;
		$sbet = 1;
	}
	echo "</B></FONT>";
}
?>
