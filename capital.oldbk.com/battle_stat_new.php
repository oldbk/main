<?

$data = mysql_fetch_row(mysql_query ("SELECT win FROM `battle` WHERE `id` = ".(int)($_GET['log'])." LIMIT 1"));
if ((int)$data[0]!=3)
//if (1==1)
	{



$dir=(int)($_GET['log']/1000);



$dir="/www/data/combat_logs/".$dir."000";	
$statfile=$dir."/battle".(int)$_GET['log'].".txt";

$statfile = file($statfile);	


$udtp=array("U"=>"<font color=gray>•</font>",'B'=>"<font color=black>•</font>","K"=>"<font color=red>x</font>","P"=>"<font color=#B02701>x</font>","R"=>"x");

if (is_array($statfile)) {
	
	if (isset($_GET[bl]))	
	{
	 	$array_data=array();
		$array_hiddens=array();
		
		foreach ($statfile as $line) 	
    			{
    			if ($FINISHLOG!=true)
    			{
    			if(trim($line)!='')
    			{
			$det=explode(':',$line);
			if (($det[0]=='!') AND ( ($det[1]=='U') OR ($det[1]=='B') OR ($det[1]=='K') OR ($det[1]=='P') OR ($det[1]=='R') )  )
			{
			//обработка строк только : уворот,блок,крит,крит через блок,простой удар
			//организовываем масив с указателем по команде, 
			$login_data=explode("|",$det[6]); // покому били что он блочил
			$team=$login_data[1]; //команда
			
			//запоминаем ники для перевоплотов и невидимок
			if ($login_data[2]!='') { $array_hiddens[$login_data[0]]=$login_data[2]; }
			
		
			//все блоки  по зонам
			if ($det[10]==1)
					{
					$array_data[$team][$login_data[0]][block][1].="<td>".$udtp[$det[1]]."</td>";
					$array_data[$team][$login_data[0]][block][2].="<td>".$udtp[$det[1]]."</td>";
					$array_data[$team][$login_data[0]][block][3].="<td>&nbsp;</td>";
					$array_data[$team][$login_data[0]][block][4].="<td>&nbsp;</td>";
					}
			elseif ($det[10]==2)
					{
					$array_data[$team][$login_data[0]][block][1].="<td>&nbsp;</td>";
					$array_data[$team][$login_data[0]][block][2].="<td>".$udtp[$det[1]]."</td>";
					$array_data[$team][$login_data[0]][block][3].="<td>".$udtp[$det[1]]."</td>";
					$array_data[$team][$login_data[0]][block][4].="<td>&nbsp;</td>";
					}
			elseif ($det[10]==3)
					{
					$array_data[$team][$login_data[0]][block][1].="<td>&nbsp;</td>";
					$array_data[$team][$login_data[0]][block][2].="<td>&nbsp;</td>";
					$array_data[$team][$login_data[0]][block][3].="<td>".$udtp[$det[1]]."</td>";
					$array_data[$team][$login_data[0]][block][4].="<td>".$udtp[$det[1]]."</td>";	
					}					
			elseif ($det[10]==4)
					{
					$array_data[$team][$login_data[0]][block][1].="<td>".$udtp[$det[1]]."</td>";
					$array_data[$team][$login_data[0]][block][2].="<td>&nbsp;</td>";
					$array_data[$team][$login_data[0]][block][3].="<td>&nbsp;</td>";
					$array_data[$team][$login_data[0]][block][4].="<td>".$udtp[$det[1]]."</td>";
					}			
			
				if ((($det[10]>=1)AND($det[10]<=4)) AND ($det[1]=='B') )
					{
					//успешный блок (блок не пробит критом)
					$array_data[$team][$login_data[0]][z_good]+=1;
						
						$array_data[$team][$login_data[0]][z_good_temp]+=1;
						
						if ($array_data[$team][$login_data[0]][z_good_temp]>=3)
							{
							$array_data[$team][$login_data[0]][z_ptgood]+=1;
							
							$array_data[$team][$login_data[0]][z_good_temp]=0;
							}
						
						
					}
					else
					{
					//обнуляем линейку удачных блоков
						$array_data[$team][$login_data[0]][z_good_temp]=0;					
					}
					
					if ($det[1]=='U')
					{
					//увернулся от удара
					$array_data[$team][$login_data[0]][z_uver]+=1;
						
						$array_data[$team][$login_data[0]][z_uver_temp]+=1;
						
						if ($array_data[$team][$login_data[0]][z_uver_temp]>=3)
							{
							$array_data[$team][$login_data[0]][z_pt_uver]+=1;
							
							$array_data[$team][$login_data[0]][z_uver_temp]=0;
							}
						
						
					}
					else
					{
					//обнуляем линейку удачных блоков
						$array_data[$team][$login_data[0]][z_uver_temp]=0;					
					}					
			
     			}
     			}
     			}
     			
     					if (strpos($line,'!:F:') !== FALSE) // пришло окончание боя - закрываем вывод
					{
					$FINISHLOG=true;			
					}
     			
     			
    			}
    }
	else
	{
	 	$array_data=array();
		$array_hiddens=array();
		$line_count=0;
		foreach ($statfile as $line) 	
    			{
    			$line_count++;
    			
    			if ($FINISHLOG!=true)
    			{
    			
    			if(trim($line)!='')
    			{
			$det=explode(':',$line);
			if (($det[0]=='!') AND ( ($det[1]=='U') OR ($det[1]=='B') OR ($det[1]=='K') OR ($det[1]=='P') OR ($det[1]=='R') )  )
			{
			//обработка строк только : уворот,блок,крит,крит через блок,простой удар
			//организовываем масив с указателем по команде, 
			$login_data=explode("|",$det[3]); // кто
			$team=$login_data[1]; //команда
			
			//запоминаем ники для перевоплотов и невидимок
			if ($login_data[2]!='') { $array_hiddens[$login_data[0]]=$login_data[2]; }
			
			//урон
			$uron=explode("|",$det[11]);
			$uron=$uron[0];
			
			//куда били
			$kuda=$det[9]/100; //113/100=1.13
			$kuda=(int)(($kuda-(int)($kuda))*10) ;//1.13-1=0.13*10=1.3=1 - зона попадания 1-2-3-4
			
			
			//криты
			if (($det[1]=='K' || $det[1]=='P')) { $array_data[$team][$login_data[0]][krit]+=$uron; $array_data[$team][$login_data[0]][krit_c]++; }
			 
			//общий урон
			if ($uron>0) 	{
						$array_data[$team][$login_data[0]][alldmg]+=$uron; 
						//считаем попадания по зонам
						$array_data[$team][$login_data[0]][udars_pop]++;						
	
						if ($det[1]!='P')  //пробив блок не считаем
							{
							$array_data[$team][$login_data[0]][ata_nobl]+=($kuda>=1&&$kuda<=4?1:0); //попадание в противника без блока (нанесен урон >0)						
							}
						
						if ($det[1]!='P')  //пробив блок не считаем
							{
							$array_data[$team][$login_data[0]][ata_ptnobl_temp]+=($kuda>=1&&$kuda<=4?1:0);
							
							if ($array_data[$team][$login_data[0]][ata_ptnobl_temp]>=3)
								{
								$array_data[$team][$login_data[0]][ata_ptnobl]+=1; //попадание в противника без блока (нанесен урон >0)						
								$array_data[$team][$login_data[0]][ata_ptnobl_temp]=0;
								}
							}
							else
							{
							//если  попали в блок , серия три подряд обнуляется
							$array_data[$team][$login_data[0]][ata_ptnobl_temp]=0;							
							
							//пробил блок противника критом
							$array_data[$team][$login_data[0]][ata_bl_krit]+=($kuda>=1&&$kuda<=4?1:0);
							
							}
						
							$test_ded=explode("/",$det[12]);
							$test_ded=$test_ded[0];
					
							if ($test_ded=='[0')
								{

								//убил противника
								$array_data[$team][$login_data[0]][ata_dead]+=1;
								}
								elseif ($test_ded=='[??')
								{
								//удар по неведу был, смотрим  лог дальше на пару строк
									$vrag_neved=explode('|',$det[6]); $vrag_neved=$vrag_neved[0]; //ник неведа по которому били
									
									$temp_det_1=explode(':',$statfile[$line_count+1]); $temp_det_login_1=explode('|',$temp_det_1[3]);$temp_det_login_1=$temp_det_login_1[0];
									$temp_det_2=explode(':',$statfile[$line_count+2]);$temp_det_login_2=explode('|',$temp_det_2[3]);$temp_det_login_2=$temp_det_login_2[0];
									
									if (($temp_det_1[1]=='D' and $temp_det_login_1==$vrag_neved) OR ($temp_det_2[1]=='D' and $temp_det_login_2==$vrag_neved) )
									{
									//неведимка после размена умер
									$array_data[$team][$login_data[0]][ata_dead]+=1;
									}
								
								}
							
						
						}
						else
						{
						//если нет урона серия три подряд обнуляется
						$array_data[$team][$login_data[0]][ata_ptnobl_temp]=0;
						}
						
			
			//все ходы по зонам
			$array_data[$team][$login_data[0]][udars][1].="<td>".($kuda==1?$udtp[$det[1]]:"&nbsp;")."</td>";
			$array_data[$team][$login_data[0]][udars][2].="<td>".($kuda==2?$udtp[$det[1]]:"&nbsp;")."</td>";
			$array_data[$team][$login_data[0]][udars][3].="<td>".($kuda==3?$udtp[$det[1]]:"&nbsp;")."</td>";
			$array_data[$team][$login_data[0]][udars][4].="<td>".($kuda==4?$udtp[$det[1]]:"&nbsp;")."</td>";
			
			$array_data[$team][$login_data[0]][udars_c][1]+=($kuda==1?1:0);
			$array_data[$team][$login_data[0]][udars_c][2]+=($kuda==2?1:0);
			$array_data[$team][$login_data[0]][udars_c][3]+=($kuda==3?1:0);	
			$array_data[$team][$login_data[0]][udars_c][4]+=($kuda==4?1:0);
			
					
					
					
			
			
     			}
     			elseif (($det[0]=='!') AND (($det[1]=='Y')OR($det[1]=='Z')OR($det[1]=='J')OR($det[1]=='G')OR($det[1]=='L')OR($det[1]=='O')OR($det[1]=='1') ) )
     			{
				$login_data=explode("|",$det[6]); // кто
				$team=$login_data[1]; //команда

				//запоминаем ники для перевоплотов и невидимок
				if ($login_data[2]!='') { $array_hiddens[$login_data[0]]=$login_data[2]; }
				
				if ($login_data[0]!='')
				{
				//урон
				$uron=explode("|",$det[11]);
				$uron=$uron[0];
					
					if (($det[1]=='Z') and ($det[7]!='') and ($det[8]!='') )
					{
					//свиток молнии делим на троих
					$uron=(int)($uron/3);
					}
					
				$array_data[$team][$login_data[0]][mag_uron]+=$uron; 
				}
				
				if (($det[1]=='Z') and ($det[7]!='')  )
				{
				//доп. ники которые получили урон свиток молнии
				$array_data[$team][$det[7]][mag_uron]+=$uron; 
				}

				if (($det[1]=='Z') and ($det[8]!='')  )
				{
				//доп. ники которые получили урон свиток молнии
				$array_data[$team][$det[8]][mag_uron]+=$uron; 
				}				
				
     			}
     			elseif (($det[0]=='!') AND ($det[1]=='H')  )
     			{
     			// использование юза свитков лечения любой хил
     			$array_data[$team][$login_data[0]][mag_hill]+=1; 
     			}
     			elseif (($det[0]=='!') AND ($det[1]=='X') AND ($det[4]==1020 ||$det[4]==1021||$det[4]==1010 ||$det[4]==1011) )   //1020-1021-призыв 1010-1011- захват
     			{
     			// использование юза свитков призыва/захвата
     			$array_data[$team][$login_data[0]][mag_pzah]+=1; 
     			}
     			elseif (($det[0]=='!') AND ($det[1]=='X') AND ($det[4]==300||$det[4]==301||$det[4]==1000 ||$det[4]==1001) )   //300-301-клонирование 1000-1001- переман
     			{
     			// использование юза свитков призыва/захвата
     			$array_data[$team][$login_data[0]][mag_clons]+=1; 
     			}
     			elseif (($det[0]=='!') AND ($det[1]=='X') AND ($det[4]==800||$det[4]==801) )   //800-801-использовала аптечку
     			{
     			// использование юза свитков призыва/захвата
     			$array_data[$team][$login_data[0]][mag_apt]+=1; 
     			}
     			
     			}
     			}
     			
     			if (strpos($line,'!:F:') !== FALSE) // пришло окончание боя - закрываем вывод
					{
					$FINISHLOG=true;			
					}
    }
    }
asort($array_data);

$ADMIN_INFO=false;
if ($user['klan']=='radminion')
	{
	$ADMIN_INFO=true;
	}

if (isset($_GET[bl]))
{




echo "<h4><a href='?log=".$_GET['log']."&stat=1'>Последовательность ударов</a>&nbsp;&nbsp;&nbsp;&nbsp;Последовательность блоков</h4>";
echo "<table border=1 style='font-family:monospace; font-size:13px;font-weight:400;'><tr><td>Логин</td><td>Зона блока<td>Последовательность блоков</td>";

if ($ADMIN_INFO)
	{
		echo "<td> Защита </td>";	
	}


foreach ($array_data as $tm => $valdat) 
	{
		foreach ($valdat as $lgn => $infdat) 
			{
			if ($array_hiddens[$lgn] !='')  { $nick=$array_hiddens[$lgn]; } else 
			{ 
			$nick=$lgn."<a target=\"_blank\" href=\"inf.php?login=".$lgn."\"><img width=\"12\" height=\"11\" alt=\"Инф. о ".$lgn." \" src=\"http://i.oldbk.com/i/inf.gif\"></img></a>";
			 } //для вывода ников неведов
			$nick='<span class="B'.$tm.'">'.$nick.'</span>';
			
			echo "<tr><td>".$nick."<td>голова<br>корпус<br>пояс(пах)<br>ноги.<td style='padding:0px; margin:0px; font-weight:bold; text-decoration:none; font-family:monospace;' cellspacing=0 cellpadding=0>";
					
					echo "<table border=0 cellspacing=0 cellpadding=0 >";
					echo "<tr>";
					echo $infdat[block][1];
					echo "</tr>";
					echo "<tr>";
					echo $infdat[block][2];
					echo "</tr>";
					echo "<tr>";					
					echo $infdat[block][3];
					echo "</tr>";
					echo "<tr>";					
					echo $infdat[block][4];
					echo "</tr>";					
					echo "</table>";
				
				if ($ADMIN_INFO)
					{
						
						echo "<td>".(int)$infdat[z_good]."+";
						echo (int)$infdat[z_ptgood]."+";
						echo (int)$infdat[z_uver]."+";
						echo (int)$infdat[z_pt_uver]."=<b>";
						
						echo ((int)$infdat[z_good]+(int)$infdat[z_ptgood]+(int)$infdat[z_uver]+(int)$infdat[z_pt_uver]);
						echo "</b>";

						
					}
	
					
			}
	}
echo "</table>";

if ($ADMIN_INFO)
	{
		echo "<small>Защита:<br>
	&nbsp;&nbsp;&nbsp;-  успешный блок (блок не пробит критом)<br>
	&nbsp;&nbsp;&nbsp;- три раза подряд успешный блок (блок не пробит критом) как и с атакой серии считаются по три, любое действие кроме удачного блока сбивает серию<br>
	&nbsp;&nbsp;&nbsp;- увернулся от удара<br>
	&nbsp;&nbsp;&nbsp;- три раза подряд увернулся от удара, все так же считается серия <br>
		 </small>";	
	}

}
else
//удары
{
echo "<h4>Последовательность ударов&nbsp;&nbsp;&nbsp;&nbsp;<a href='?log=".$_GET['log']."&stat=1&bl=1'>Последовательность блоков</a></h4>";

echo "<table border=1 style='font-family:monospace; font-size:13px;font-weight:400;'><tr><td>Логин<td>Удар в<td>Последовательность ударов";

if ($ADMIN_INFO)
	{
		echo "<td> Атака </td><td> Магия </td>";	
	}

foreach ($array_data as $tm => $valdat) 
	{
	//деление по командам		
		foreach ($valdat as $lgn => $infdat) 
			{
			//делание по никам					
			if ($array_hiddens[$lgn] !='')  { $nick=$array_hiddens[$lgn]; } 
			else { 
			$nick=$lgn."<a target=\"_blank\" href=\"inf.php?login=".$lgn."\"><img width=\"12\" height=\"11\" alt=\"Инф. о ".$lgn." \" src=\"http://i.oldbk.com/i/inf.gif\"></img></a>";
			 } //для вывода ников неведов
			$nick='<span class="B'.$tm.'">'.$nick.'</span>';
			
			echo "<tr valign=top><td>".$nick."<td>голова<br>корпус<br>пояс(пах)<br>ноги<td style='padding:0px; margin:0px; font-weight:bold; text-decoration:none; font-family:monospace;' cellspacing=0 cellpadding=0>";
		
				//echo $infdat[udars][1]."<br>".$infdat[udars][2]."<br>".$infdat[udars][3]."<br>".$infdat[udars][4];
					echo "<table border=0 cellspacing=0 cellpadding=0 >";
					echo "<tr>";
					echo $infdat[udars][1];
					echo "</tr>";
					echo "<tr>";
					echo $infdat[udars][2];
					echo "</tr>";
					echo "<tr>";
					echo $infdat[udars][3];
					echo "</tr>";
					echo "<tr>";
					echo $infdat[udars][4];
					echo "</tr>";					
					echo "</table>";
					
					if ($ADMIN_INFO)
					{
						echo "<td>".(int)$infdat[ata_nobl]."+";
						echo (int)$infdat[ata_ptnobl]."+";
						echo (int)$infdat[ata_bl_krit]."+";
						echo (int)$infdat[ata_dead]."=<b>";
						echo ((int)$infdat[ata_nobl]+(int)$infdat[ata_ptnobl]+(int)$infdat[ata_bl_krit]+(int)$infdat[ata_dead]);
						echo "</b>";

						
						echo "<td>".(int)$infdat[mag_hill]."+";
						echo (int)$infdat[mag_pzah]."+";
						echo (int)$infdat[mag_clons]."+";
						echo (int)$infdat[mag_apt]."=<b>";						
						
						echo ((int)$infdat[mag_hill]+(int)$infdat[mag_pzah]+(int)$infdat[mag_clons]+(int)$infdat[mag_apt])."</b>";						
						
					}				
					
			}
	
	}
echo "</table><br>";



echo "<font color=gray><b>•</b></font> - увернулся, <font color=black><b>•</b></font> - блок, <font color=red><b>X</b></font> - критический удар, <font color=#B02701><b>X</b></font> - критический удар пробил блок, <b>X</b> - удачный удар";

if ($ADMIN_INFO)
	{
		echo "<br><small><b>Атака:</b><br>
	&nbsp;&nbsp;&nbsp;- попадание в противника без блока (нанесен урон >0) - пробив блок не считаем! магия не считается<br>
	&nbsp;&nbsp;&nbsp;- три попадания подряд без блока (нанесен урон >0) - пробив блок не считается и обрывает серию, серия считается по три удара подряд, если было к примеру 6 ударов подрядят , то будет защитно две серии магия не считается<br>
	&nbsp;&nbsp;&nbsp;-  пробил блок противника критом только пробитые блоки критами<br>
	&nbsp;&nbsp;&nbsp;-  смертельный удар (добил противника) только физ, от магии не считается<br>
	<br>
	<b>Магия:</b><br>
	&nbsp;&nbsp;&nbsp;- использование юза свитков лечения = любой ХИЛ!!!<br>
	&nbsp;&nbsp;&nbsp;- использование юза свитков призыва/захвата<br>
	&nbsp;&nbsp;&nbsp;- использование юза свитков клонирования/переманивания<br>
	&nbsp;&nbsp;&nbsp;- использование аптечки<br>
		 </small>";	
	}


echo "<h4>Суммарно</h4>";
echo "<table border=1 style='font-family:monospace; font-size:13px;font-weight:400;'>";
echo "<tr><td>Логин<td>Удары<td>Попадания<td>Маг. урон<td>Суммарно урон";

foreach ($array_data as $tm => $valdat) 
{
	foreach ($valdat as $lgn => $infdat) 
	{
	if ($array_hiddens[$lgn] !='')  { $nick=$array_hiddens[$lgn]; } else 
	{ 
	$nick=$lgn."<a target=\"_blank\" href=\"inf.php?login=".$lgn."\"><img width=\"12\" height=\"11\" alt=\"Инф. о ".$lgn." \" src=\"http://i.oldbk.com/i/inf.gif\"></img></a>";
	 } //для вывода ников неведов	
	$nick='<span class="B'.$tm.'">'.$nick.'</span>';
	
	echo "<tr><td>".$nick;
	echo "<td>".(int)$infdat[udars_c][1]."/".(int)$infdat[udars_c][2]."/".(int)$infdat[udars_c][3]."/".(int)$infdat[udars_c][4]."<td>".(int)($infdat[udars_pop]-$infdat[krit_c])."<font color=red>(".(int)$infdat[krit_c].")</font>/".(int)$infdat[udars_pop]."<td><font color=red>(".(int)$infdat[mag_uron].")</font><td>".(int)($infdat[alldmg]+$infdat[mag_uron])."<font color=red>(".(int)$infdat[krit].")</font>";
	}
}
echo "</table><br>";
echo "<br>
Удары: голова/корпус/пояс(пах)/ноги<br>
Попадания: обычных(критов)/всего<br>
Урон: всего(критом)<br>
";
}
}
}
else echo "Нельзя смотреть статистику активного поединка!";
?>