<?php
// магия "Илюзая для хелоуна"
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
    	$ok=0;


	if($ng==1)
	{	        $rowm[id]=26;
	        $rowm[prototype]=2010024;
	        $magic[id]=1; // просто фикс, так как вызывается не через инвентарь	}
	else
	{
		$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));
	}


	if((int)$rowm['magic'] > 0)
	{
		$magic = magicinf($rowm['magic']);
	}
//if($user[klan]!='radminion')
if($user[klan]=='rn')
{
	echo 'Не доступно 10-15 минут...';
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
				if($rowm[prototype]==10000)  //
				{
	   				$helloween=1;
				}

				$suit=mysql_fetch_array(mysql_query('select * from oldbk.shop_suit_prototype where id_prototype ='.$rowm[prototype].';'));

		 		$ill=mysql_fetch_array(mysql_query("SELECT * FROM effects WHERE type = 300 AND owner='".$_SESSION['uid']."' LIMIT 1"));

                if($suit[exp_time]!=1999999999)
			 	{
			 		$suit[exp_time]=time()+$suit[exp_time];
			 	}

                if($helloween==1)
                {
                   $sex=($user[sex]==1?'men':'women');                   $rowm[prototype]=mt_rand(1,13);
		   $suit[img_name]=$suit[img_name].$sex.'_'.$rowm[prototype].'.gif';
                   $ok=2;
                   $bet=1;
		   $sbet = 1;                }
                else
                if($ng==1)
                {
			$sex=($user[sex]==1?'m':'f');
                    	//obraz_ny_m4.gif
			$p=(mt_rand(1,20));
			
			/*  исключения на покупки
			if($sex<1 && ($p==1 || $p==17))
			{
				$p=14;			}
			*/
                    	$suit[img_name]='obraz_ny_'.$sex.$p.'.gif';//19 картинок, на все один прототип
                    	$ok=2;
                }
                else
		 		if($suit[sex]<2 && $user[sex]==$suit[sex])
			 	{
                    			$sex=($user[sex]==1?'m':'w');
					//свадебные костюмы
					$sv_s=array(2010020,2010021,2010022,2010023);
					$ng_s=array();

					if(in_array($rowm[prototype], $sv_s))
					{
					    if($ill[id])
						{
		                  			if (strpos($ill[add_info],$suit[img_name])!== FALSE)
						  	{
		                    			$nmb_s=1;
								for($i=1;$i<9;$i++)
								{
									 $nm= $sex.'1_'.$i;
									 $nm1=$sex.'2_'.$i;
	
									if (strpos($ill[add_info],$nm) || strpos($ill[add_info],$nm1))
									{
			                            				$nmb_s=$i+1;
									}
								}
							    $suit[img_name]=$suit[img_name].($nmb_s==9?1:$nmb_s).'.gif';
							    $ok=1;
							    $change=1;
							  }
							  else
							  {
							  	$change=2;
							  }
						}

						if(!$ill[id] || $change==2)
						{
                            				$rowm[dategoden]=(!$rowm[dategoden]?$suit[exp_time]:$rowm[dategoden]);
							$suit[img_name]=$suit[img_name].'1.gif';
							$ok=1;
						}
		            }
	            }
				else
				{
					echo 'Это '.($suit[sex]==1?'мужской':'женский').' образ.';
				}

				$ill[id]=(!$ill[id]?'NULL':$ill[id]);

					if($ok>0)
					{
					    if($ok==1)
					    {
							if($change==2 || !$change)
							{
								mysql_query("UPDATE oldbk.inventory set goden =3, present='\"Свадебное агентство \"ООО Березка\"' ,dategoden=".($suit[exp_time]-20)." WHERE owner='".$_SESSION['uid']."' AND id =".$_GET['use'].";");
							}
                        }

						mysql_query("INSERT INTO effects SET id=".$ill[id].", type = 300, name = 'Иллюзия' , time = '".$suit[exp_time]."', owner='".$_SESSION['uid']."', add_info='".$suit[img_name]."'
                        	ON DUPLICATE KEY UPDATE add_info='".$suit[img_name]."', time = '".$suit[exp_time]."';");
					    $ok=10;
					}
					if($ok==10)
					{
						echo  "Вы подверглись иллюзии...";
	                }
	     }
		echo "</B></FONT>";
	}
}
?>
