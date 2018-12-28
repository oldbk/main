<?
if ($gen_map!=true) die();

$mobs=4; //% монстров
$lov=2;  //% ловушек
$sund=2;  //% сундуков
$hils=3;  //% хилок
$box=1;  //% ящиков пандоры
///////////////////
//Error_Reporting(0);



if ($mapid<0)
	{
	$mapid='1';
	}

///dir to save
$outmap='/www/capitalcity.oldbk.com/labmaps/'.$mapid.'.map';

function NettoieCellules($lab,$x2,$y2,$dim_x,$dim_y,$v1, $v2)
{
global $lab;

$lab[$x2][$y2]= $v1;
 if (($x2 > 0) and ($lab[$x2-1][$y2] == $v2))
	NettoieCellules($lab, $x2-1,$y2,$dim_x,$dim_y, $v1, $v2);

 if (($x2 < $dim_x-1) and ($lab[$x2+1][$y2] == $v2) )
  NettoieCellules($lab, $x2+1, $y2, $dim_x, $dim_y, $v1, $v2);

 if (($y2 > 0) and ($lab[$x2][$y2-1] == $v2))
  NettoieCellules($lab,$x2,$y2-1,$dim_x,$dim_y,$v1, $v2);


 if (($y2 < $dim_y-1) and ($lab[$x2][$y2+1] == $v2))
  NettoieCellules($lab, $x2, $y2+1, $dim_x, $dim_y, $v1,$v2);
}



/////////////////////////////////////////
$lab=array();
$MH=array();
$MV=array();

$dim=12; // размерность
$dim_x=$dim;
$dim_y=$dim;
$dim_Finale=2*$dim+1;

//mob and lov as count;
$k_mob=floor($dim_Finale*$dim_Finale/100*$mobs);
$k_lov=floor($dim_Finale*$dim_Finale/100*$lov);

$k_sund=floor($dim_Finale*$dim_Finale/100*$sund);
$k_hils=floor($dim_Finale*$dim_Finale/100*$hils);
$k_box=floor($dim_Finale*$dim_Finale/100*$box);


$inform="GMobs:$k_mob/Trap:$k_lov/Sund:$k_sund/Hils:$k_hils/Box:$k_box";

///randomize
srand();

$NbMurs=0;
$x1=0;$x2=0;$y1=0;$y2=0;


 for ($i=0;$i<=$dim_x-1;$i++) 
	for ($j=0;$j<=$dim_y-1;$j++)
	 $lab[$i][$j]=$i * $dim_y + $j;


 for ($i=0;$i<=$dim_x-1;$i++)
	for ($j=0;$j<=$dim_y-2;$j++)
	 $MH[$i][$j]=1;


	for ($i=0;$i<=$dim_x-2;$i++)
	 for ($j=0;$j<=$dim_y-1;$j++)
		$MV[$i][$j]= 1;


 while( $NbMurs <> ($dim_x*$dim_y)-1 )
{
	$continue=0;
	$rand= rand(0,2) + 1;
switch($rand)
{
case 1:
			$x1= rand(0,$dim_x);
			$y1= rand(0,$dim_y-1);
			if ($MH[$x1][$y1] == '1')
			{
			 $continue= 1;
			 $x2=$x1;
			 $y2=$y1+1;
			}
	 break;

case 2: 
			$x1= rand(0,$dim_x-1);
			$y1= rand(0,$dim_y);
			if ($MV[$x1][$y1] == '1')
			{
			 $continue=1;
			 $x2=$x1+1;
			 $y2=$y1;

			}
	break;

}


	if ($continue == 1)
	{
	 $v1= $lab[$x1][$y1];
	 $v2= $lab[$x2][$y2];
	 if ($v1 <> $v2) 
		{
		switch ($rand)
			{
			case 1: $MH[$x1][$y1]= 0; 	 break;
			case 2: $MV[$x1][$y1]= 0;	 break;
			}
 		NettoieCellules($lab, $x2, $y2, $dim_x, $dim_y, $v1, $v2);
		$NbMurs++;
		}
	}
} //while



 for ($i=1;$i<=$dim_Finale;$i++)
	for ($j=1;$j<=$dim_Finale;$j++)
	{
	$rendu[$i][$j]='O';
	}



 for ($i=1;$i<=$dim_Finale;$i++)
	$rendu[1][$i]= 'M';

 for ($i=1;$i<=$dim_Finale;$i++)
	$rendu[$dim_Finale][$i]='M';


 for ($i=1;$i<=$dim_Finale;$i++)
	{
	$rendu[$i][1]='M';
 	$rendu[$i][$dim_Finale]='M';
	}



 for ($i=0;$i<=$dim_x-2;$i++)
  for ($j=0;$j<=$dim_y-1;$j++)
   if ($MV[$i][$j] == 1) 
   {
    $rendu[2*($i+1)+1][$j*2+1]= 'M';
    $rendu[2*($i+1)+1][$j*2+2]= 'M';
    $rendu[2*($i+1)+1][$j*2+3]= 'M';
   }


 for ($i=0;$i<=$dim_x-1;$i++)
  for ($j=0;$j<=$dim_y-2;$j++)
    if ($MH[$i][$j] == 1)
	{
     $rendu[$i*2+1][($j+1)*2+1]= 'M';
     $rendu[$i*2+2][($j+1)*2+1]= 'M';
     $rendu[$i*2+3][($j+1)*2+1]= 'M';
	}


//in and out
$k1=(rand(0,$dim-1)+1)*2;
$k2=(rand(0,$dim-1)+1)*2;

$inlabx=$k1;
$inlaby=1;

//echo "Вход : $k1 / 1 <br>";
//echo "Выход: $k2 / $dim_Finale <br>";

 $rendu[$k1][1]= 'I';
 $rendu[$k2][$dim_Finale]= 'F';

/// rand mobs
//echo "Монстров: $k_mob  - RED <br>";
 while ($k_mob > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='R'; 
		$k_mob--;

		}
	}
	
/// rand lovs
//echo "Ловушек: $k_lov  - Blue <br>";
 while ($k_lov > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='B'; 
		$k_lov--;

		}
	}
//////////////////
//echo "Сундуки $k_sund  <br>";
 while ($k_sund > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='S'; 
		$k_sund--;

		}
	}

//////////////////
//echo "Хилки $k_hils  <br>";
 while ($k_hils > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='H'; 
		$k_hils--;

		}
	}

/////////////////////////


//////////////////
//echo "Хилки $k_box  <br>";
 while ($k_box > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='P'; 
		$k_box--;

		}
	}

/////////////////////////


$savemap = fopen($outmap,"w");
fwrite($savemap,"//map lab id:".$mapid." gen date:".date("d-m-Y H:m:s")."/".$inform."  \n"); // clear line

//echo "<pre>";
 for ($i=1;$i<=$dim_Finale;$i++)
	{
 	$linte=''; // for visualimage
	$fline=' ';
	for ($j=1;$j<=$dim_Finale;$j++)
		{
		//first write to file
		$fline=$fline.$rendu[$i][$j];

		if ($rendu[$i][$j]=='I') { $rendu[$i][$j]='<img src=o.gif>'; } // вход
		if ($rendu[$i][$j]=='F') { $rendu[$i][$j]='<img src=o.gif>'; } // Выход
		if ($rendu[$i][$j]=='O') { $rendu[$i][$j]='<img src=o.gif>'; } // проход 
		if ($rendu[$i][$j]=='M') { $rendu[$i][$j]='<img src=m.gif>'; } // стенка
		if ($rendu[$i][$j]=='R') { $rendu[$i][$j]='<img src=r.gif>'; } // mob
		if ($rendu[$i][$j]=='B') { $rendu[$i][$j]='<img src=b.gif>'; } // ловушка
		if ($rendu[$i][$j]=='S') { $rendu[$i][$j]='<img src=s.gif>'; } // сундук
		if ($rendu[$i][$j]=='H') { $rendu[$i][$j]='<img src=h.gif>'; } // хилка
		if ($rendu[$i][$j]=='P') { $rendu[$i][$j]='<img src=p.gif>'; } // пандоры
            $linte=$linte.$rendu[$i][$j];
            }
	fwrite($savemap, $fline."\n"); // save line
	//echo  $linte."<br>\n";
	}

//echo "</pre>";
fclose($savemap);

?>