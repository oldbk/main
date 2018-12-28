<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

//$expdate=mktime(0,0,0,01,15,2011); //время окончания работы всякой вскрываемой фигни дающейся в подарках.
//надо будет объединить с таймером включения и выклчения праздников.
$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));

if((int)$rowm['magic'] > 0)
{
	$magic = magicinf($rowm['magic']);
}
//if($user[klan]!='radminion')
if($user[klan]=='rn')
{
	echo 'Не доступно';
}
else
{
	if($rowm[id]>0 && $magic[id]>0)
	{	echo "<font color=red><B>";

		if ($user['battle']>0)
		{
			echo "Не в бою!";
		}
		else
		 {
		 	if($user[id]==28453)
		 	{
		 		echo $rowm[prototype];
		 	}
		 	$suit=mysql_fetch_array(mysql_query('select * from oldbk.shop_suit_prototype where id_prototype ='.$rowm[prototype].';'));

		 	$suit[sex]=($suit[sex]==2?$user[sex]:$suit[sex]); //есть костюмы с полом 2 - это для М и Ж  один прототип, что открывать определяем по полу

		 	$sex=($suit[sex]==1?'m':($suit[sex]==0?'w':''));

		 	if($suit[exp_time]!=1999999999)
		 	{
		 		$suit[exp_time]=time()+$suit[exp_time];
		 	}

		 	if(($user[sex]==$suit[sex])) //есть разнополые костюмы, дабы не надевали
		 	{
		                $s='';
		                //ny_suit_scarf_sn.gif
		                if($suit[id_prototype]==1006218)   //дед мороз и снегурка
		                {
		                    if($user[sex]==1)
							{   $s='_dm';
								$suit[name]='костюм Деда мороза';}
							else
							{
								$s='_sn';
								$suit[name]='костюм Снегурочки';
							}
		                }
		                //wedding_w1_bron.gif
		             	$img=array(
		             	4=> $suit[img_name].'sergi'.$s,
						41=>$suit[img_name].'kulon'.$s,
						1=> $suit[img_name].'pushka'.$s,
						11=> $suit[img_name].'pushka'.$s,
						12=> $suit[img_name].'pushka'.$s,
						13=> $suit[img_name].'pushka'.$s,
						22=>$suit[img_name].'bron'.$s,
						23=>$suit[img_name].'bron'.$s,
						6=>$suit[img_name].'cloack'.$s,
						42=>$suit[img_name].'ring'.$s,
						24=>$suit[img_name].'shlem'.$s,
						21=>$suit[img_name].'perchi'.$s,
						3=> $suit[img_name].'shield'.$s,
						2=> $suit[img_name].'boots'.$s
						);
		
			               // print_r($img);
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
						 			$insql.='("'.$user[id].'","'.$v.'.gif",'.$suit[exp_time].','.$k.',"0"),';
						 		}
						 	}
						 	$insql.='("'.$user[id].'","'.$v.'.gif",'.$suit[exp_time].','.$k.',"0"),';
						}
					   $insql=substr($insql,0,-1).';';
					   mysql_query($insql);
		               			//echo mysql_error();
					   echo "Вы открыли подарок и получили " . $suit[name];
					   $bet=1;
					   $sbet = 1;
			}
			else
			{
				echo 'Это '.($suit[sex]==0?'женский':'мужской').' костюм.';
			}
		 }
		echo "</B></FONT>";
	}
}
?>
