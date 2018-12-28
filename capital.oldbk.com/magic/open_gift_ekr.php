<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

//$expdate=mktime(0,0,0,01,15,2011); //время окончания работы всякой вскрываемой фигни дающейся в подарках.
//надо будет объединить с таймером включения и выклчения праздников.
$expdate=time()+60*60*24*15;
$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));

if((int)$rowm['magic'] > 0)
{
	$magic =magicinf($rowm['magic']);
}

//mt_rand(0,3); - это для простых подарков.

//для екровых 100% нужные шмотки.

if($rowm[id]>0 && $magic[id]>0)
{	echo "<font color=red><B>";

	if ($user['battle']>0) { echo "Не в бою!"; }
	else
	 {
      //  екровые костюмы шмотки
 		if($user[sex]==1)
		{
            $s='dm';
			$txt='костюм Деда мороза';
		}
		else
		{
			$s='sn';
			$txt='костюм Снегурочки';

		}
			$img=array(
				4=>'clip_ny_'.$s,
				41=>'scarf_ny_'.$s,
				1=>'armor_ny_'.$s,
				22=>'overclothes_ny_'.$s,
				42=>'ring_ny_'.$s,
				24=>'cap_ny_'.$s,
				21=>'gloves_ny_'.$s,
				3=>'shield_ny_'.$s,
				2=>'boots_ny_'.$s
			);

		$insql='INSERT INTO oldbk.gellery
		(owner,img,exp_date,otdel,dressed)
		VALUES
		';
		foreach($img as $k=>$v)
		{
		 	if($k==42)
		 	{
		 		for($jj=0;$jj<2;$jj++)
		 		{
		 			$insql.='("'.$user[id].'","'.$v.'.gif",'.$expdate.','.$k.',"0"),';
		 		}
		 	}
		 	$insql.='("'.$user[id].'","'.$v.'.gif",'.$expdate.','.$k.',"0"),';
		}
		   $insql=substr($insql,0,-1).';';
		   mysql_query($insql);

		echo "Вы открыли подарок и получили " . $txt;
		$bet=1;
		$sbet = 1;
	 }
	echo "</B></FONT>";
}
?>
